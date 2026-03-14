import json
import logging
import time

from src.config import ARES_API_URL, ARES_REQUEST_DELAY
from src.database import Database
from src.downloader import Downloader
from src.models import Entity, EntityLink

logger = logging.getLogger(__name__)

ARES_VR_URL = "https://ares.gov.cz/ekonomicke-subjekty-v-be/rest/ekonomicke-subjekty-vr"


class JusticeCollector:
    """Collects statutory representatives from ARES VR (veřejný rejstřík) API."""

    def __init__(self, database: Database, downloader: Downloader):
        self._db = database
        self._dl = downloader

    def collect(self) -> int:
        entity_icos = self._db.get_all_entity_icos()
        logger.info("Fetching statutory reps for %d entities with ICO...", len(entity_icos))

        total_persons = 0
        for ico in entity_icos:
            persons = self._process_company(ico)
            total_persons += persons
            time.sleep(ARES_REQUEST_DELAY)

        logger.info("Justice collector: created %d person entities", total_persons)
        return total_persons

    def _process_company(self, ico: str) -> int:
        data = self._fetch_vr(ico)
        if data is None:
            return 0

        records = data.get("zaznamy", [])
        if not records:
            return 0

        record = records[0]
        company_entity = self._db.get_entity_by_ico(ico)
        if not company_entity:
            return 0

        company_id = company_entity["id"]
        persons_created = 0

        for organ in record.get("statutarniOrgany", []):
            for member in organ.get("clenoveOrganu", []):
                if self._process_member(member, company_id, "statutory"):
                    persons_created += 1

        for organ in record.get("ostatniOrgany", []):
            for member in organ.get("clenoveOrganu", []):
                if self._process_member(member, company_id, "supervisory"):
                    persons_created += 1

        return persons_created

    def _process_member(self, member: dict, company_id: int, organ_type: str) -> bool:
        if member.get("datumVymazu"):
            return False

        person_data = member.get("fyzickaOsoba", {})
        name = self._build_person_name(person_data)
        if not name:
            return False

        role = self._determine_role(member, organ_type)

        metadata = {
            "funkce": member.get("clenstvi", {}).get("funkce", {}).get("nazev"),
            "nazevAngazma": member.get("nazevAngazma"),
            "datumZapisu": member.get("datumZapisu"),
        }

        person_id = self._db.save_person_entity(
            name=name,
            source="ares_vr",
            metadata_json=json.dumps(metadata, ensure_ascii=False),
        )

        self._db.save_entity_link(EntityLink(
            entity_id=person_id,
            linked_type="entity",
            linked_id=company_id,
            role=role,
        ))

        return True

    def _build_person_name(self, person: dict) -> str:
        parts = []
        if person.get("titulPred"):
            parts.append(person["titulPred"])
        if person.get("jmeno"):
            parts.append(person["jmeno"])
        if person.get("prijmeni"):
            parts.append(person["prijmeni"])
        if person.get("titulZa"):
            parts.append(person["titulZa"])
        return " ".join(parts).strip()

    def _determine_role(self, member: dict, organ_type: str) -> str:
        funkce = (member.get("clenstvi", {}).get("funkce", {}).get("nazev") or "").lower()

        if "předseda" in funkce and "místopředseda" not in funkce:
            return "chairman"
        if "místopředseda" in funkce:
            return "vice_chairman"
        if organ_type == "supervisory":
            return "supervisory_member"
        return "statutory"

    def _fetch_vr(self, ico: str) -> dict | None:
        url = f"{ARES_VR_URL}/{ico}"
        try:
            response = self._dl._session.get(url, timeout=15)
            if response.status_code == 404:
                return None
            response.raise_for_status()
            return response.json()
        except Exception as exc:
            logger.debug("ARES VR error for ICO %s: %s", ico, exc)
            return None
