import json
import logging

from bs4 import BeautifulSoup

from src.config import BOSKOVICE_ICO
from src.database import Database
from src.downloader import Downloader
from src.models import Entity, EntityLink

logger = logging.getLogger(__name__)

VOLBY_BASE = "https://www.volby.cz/pls"
BOSKOVICE_OBEC_CODE = "581372"
BOSKOVICE_NUTS = "6201"

ELECTED_MEMBERS_URLS = {
    2022: (
        f"{VOLBY_BASE}/kv2022/kv21111?xjazyk=CZ&xid=1&xv=23&xdz=2"
        f"&xnumnuts={BOSKOVICE_NUTS}&xobec={BOSKOVICE_OBEC_CODE}"
        f"&xstrana=0&xstat=0&xodkaz=1"
    ),
    2018: (
        f"{VOLBY_BASE}/kv2018/kv21111?xjazyk=CZ&xid=1&xv=23&xdz=2"
        f"&xnumnuts={BOSKOVICE_NUTS}&xobec={BOSKOVICE_OBEC_CODE}"
        f"&xstrana=0&xstat=0&xodkaz=1"
    ),
    2014: (
        f"{VOLBY_BASE}/kv2014/kv21111?xjazyk=CZ&xid=1&xv=23&xdz=2"
        f"&xnumnuts={BOSKOVICE_NUTS}&xobec={BOSKOVICE_OBEC_CODE}"
        f"&xstrana=0&xstat=0&xodkaz=1"
    ),
}


class VolbyCollector:
    """Collects elected council members from volby.cz."""

    def __init__(self, database: Database, downloader: Downloader):
        self._db = database
        self._dl = downloader

    def collect(self) -> int:
        city_body_id = self._ensure_city_body()
        total_persons = 0

        for year, url in ELECTED_MEMBERS_URLS.items():
            logger.info("Fetching elected members for year %d...", year)
            persons = self._process_election(url, year, city_body_id)
            total_persons += persons

        logger.info("Volby collector: created %d person-link pairs", total_persons)
        return total_persons

    def _ensure_city_body(self) -> int:
        existing = self._db.get_entity_by_name_and_type(
            "Zastupitelstvo města Boskovice", "city_body",
        )
        if existing:
            return existing["id"]

        return self._db.save_person_entity(
            name="Zastupitelstvo města Boskovice",
            source="volby_cz",
            metadata_json=json.dumps({
                "city": "Boskovice",
                "city_ico": BOSKOVICE_ICO,
                "type": "zastupitelstvo",
            }, ensure_ascii=False),
            entity_type="city_body",
        )

    def _process_election(self, url: str, year: int, city_body_id: int) -> int:
        html = self._fetch_page(url)
        if not html:
            return 0

        members = self._parse_elected_members(html)
        logger.info("Year %d: found %d elected members", year, len(members))

        created = 0
        for member in members:
            person_id = self._db.save_person_entity(
                name=member["name"],
                source="volby_cz",
                metadata_json=json.dumps({
                    "party": member["party"],
                    "votes": member["votes"],
                    "election_year": year,
                }, ensure_ascii=False),
            )

            self._db.save_entity_link(EntityLink(
                entity_id=person_id,
                linked_type="entity",
                linked_id=city_body_id,
                role="council_member",
            ))
            created += 1

        return created

    def _parse_elected_members(self, html: str) -> list[dict]:
        soup = BeautifulSoup(html, "lxml")
        table = soup.find("table")
        if not table:
            return []

        members = []
        current_party = ""

        for row in table.find_all("tr"):
            cells = row.find_all("td")
            if len(cells) < 6:
                continue

            party_cell = cells[1].get_text(strip=True)
            if party_cell:
                current_party = party_cell

            name_raw = cells[3].get_text(strip=True)
            if not name_raw:
                continue

            name = self._normalize_name(name_raw)
            if not name:
                continue

            votes = self._parse_votes(cells)

            members.append({
                "name": name,
                "party": current_party,
                "votes": votes,
            })

        return members

    def _normalize_name(self, raw: str) -> str:
        parts = raw.split()
        if len(parts) < 2:
            return ""

        title_suffixes = {"DiS.", "Ph.D.", "CSc.", "MBA", "MPA", "LL.M."}
        title_prefixes = {
            "Ing.", "Mgr.", "Bc.", "JUDr.", "MUDr.", "RNDr.", "PhDr.",
            "Doc.", "Prof.", "PaedDr.", "MVDr.", "Ing.arch.", "RSDr.",
        }

        name_parts = []
        for part in parts:
            if part in title_prefixes or part in title_suffixes:
                continue
            name_parts.append(part)

        if len(name_parts) < 2:
            return ""

        surname = name_parts[0]
        first_names = name_parts[1:]
        return f"{' '.join(first_names)} {surname}"

    def _parse_votes(self, cells) -> int | None:
        for cell in reversed(cells):
            text = cell.get_text(strip=True).replace("\xa0", "").replace(" ", "")
            if text.isdigit() and int(text) > 10:
                return int(text)
        return None

    def _fetch_page(self, url: str) -> str | None:
        try:
            response = self._dl._session.get(url, timeout=30)
            response.raise_for_status()
            response.encoding = "utf-8"
            return response.text
        except Exception as exc:
            logger.warning("Volby.cz fetch error: %s", exc)
            return None
