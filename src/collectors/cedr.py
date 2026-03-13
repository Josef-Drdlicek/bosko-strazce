import logging
from typing import Optional

from src.config import BOSKOVICE_ICO, HLIDAC_STATU_API_URL, HLIDAC_STATU_TOKEN
from src.database import Database
from src.downloader import Downloader
from src.models import Entity, EntityLink, Subsidy

logger = logging.getLogger(__name__)


class CedrCollector:
    """Collects subsidies data via Hlidac statu API (aggregated CEDR/IsRed/Eufondy)."""

    def __init__(self, database: Database, downloader: Downloader):
        self._db = database
        self._dl = downloader

    def collect(self, ico: str = BOSKOVICE_ICO) -> int:
        if not HLIDAC_STATU_TOKEN:
            logger.error(
                "HLIDAC_STATU_TOKEN not set. "
                "Register at https://www.hlidacstatu.cz/api and set the token in .env"
            )
            return 0

        logger.info("Fetching subsidies for ICO %s from Hlidac statu...", ico)
        saved = 0
        page = 1

        while True:
            data = self._fetch_page(ico, page)
            if data is None:
                break

            results = data.get("results", [])
            if not results:
                break

            for item in results:
                subsidy = _parse_subsidy(item)
                if subsidy is None:
                    continue

                if self._db.subsidy_exists(subsidy.external_id):
                    continue

                subsidy_id = self._db.save_subsidy(subsidy)
                self._link_recipient(subsidy, subsidy_id)
                saved += 1

            total = data.get("total", 0)
            page_size = len(results)
            logger.info(
                "Page %d: fetched %d subsidies (total available: %d)",
                page, len(results), total,
            )

            if page * page_size >= total:
                break
            page += 1

        logger.info("CEDR: saved %d new subsidies", saved)
        return saved

    def _fetch_page(self, ico: str, page: int) -> Optional[dict]:
        url = f"{HLIDAC_STATU_API_URL}/Dotace/Hledat"
        try:
            response = self._dl._session.get(
                url,
                params={"dotaz": f"ico:{ico}", "strana": page},
                headers={
                    "Authorization": f"Token {HLIDAC_STATU_TOKEN}",
                    "User-Agent": self._dl._session.headers.get("User-Agent", ""),
                },
                timeout=30,
            )
            response.raise_for_status()
            return response.json()
        except Exception as exc:
            logger.warning("Hlidac statu Dotace API error (page %d): %s", page, exc)
            return None

    def _link_recipient(self, subsidy: Subsidy, subsidy_id: int):
        if not subsidy.recipient_ico:
            return

        existing = self._db.get_entity_by_ico(subsidy.recipient_ico)
        if existing:
            entity_id = existing["id"]
        else:
            entity = Entity(
                name=subsidy.recipient_name or subsidy.recipient_ico,
                entity_type="organization",
                ico=subsidy.recipient_ico,
                source="cedr",
            )
            entity_id = self._db.save_entity(entity)

        self._db.save_entity_link(EntityLink(
            entity_id=entity_id,
            linked_type="subsidy",
            linked_id=subsidy_id,
            role="recipient",
        ))


def _parse_subsidy(item: dict) -> Optional[Subsidy]:
    external_id = item.get("id")
    if not external_id:
        return None

    title = item.get("projectName") or item.get("projectDescription") or "Bez názvu"

    recipient = item.get("recipient", {}) or {}
    recipient_ico = recipient.get("ico") if isinstance(recipient, dict) else None
    recipient_name = (
        recipient.get("name") or recipient.get("obchodniJmeno")
        if isinstance(recipient, dict) else None
    )

    program_name = item.get("programName")
    provider = item.get("subsidyProvider")

    raw_amount = item.get("subsidyAmount") or item.get("assumedAmount")
    amount = float(raw_amount) if isinstance(raw_amount, (int, float)) else None

    year = item.get("approvedYear")

    source_url = f"https://www.hlidacstatu.cz/dotace/detail/{external_id}"

    return Subsidy(
        external_id=str(external_id),
        title=title,
        provider=provider,
        recipient_ico=recipient_ico,
        recipient_name=recipient_name,
        program=program_name,
        amount=amount,
        year=year,
        source_url=source_url,
    )
