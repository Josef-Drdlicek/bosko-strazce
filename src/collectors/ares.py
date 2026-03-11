import json
import logging
import time
from typing import Optional

from src.config import ARES_API_URL, ARES_REQUEST_DELAY
from src.database import Database
from src.downloader import Downloader
from src.models import Entity

logger = logging.getLogger(__name__)


class AresCollector:
    """Enriches entities with company data from ARES REST API."""

    def __init__(self, database: Database, downloader: Downloader):
        self._db = database
        self._dl = downloader

    def enrich_all_entities(self) -> int:
        known_icos = self._db.get_all_entity_icos()
        logger.info("Found %d entities with ICO to enrich", len(known_icos))

        enriched = 0
        for ico in known_icos:
            existing = self._db.get_entity_by_ico(ico)
            if existing and existing.get("metadata_json"):
                continue

            data = self._fetch_company(ico)
            if data is None:
                continue

            entity = _parse_ares_response(ico, data)
            if entity:
                self._db.save_entity(entity)
                enriched += 1
                logger.info("Enriched: %s (%s)", entity.name, ico)

            time.sleep(ARES_REQUEST_DELAY)

        logger.info("ARES: enriched %d entities", enriched)
        return enriched

    def lookup_single(self, ico: str) -> Optional[Entity]:
        data = self._fetch_company(ico)
        if data is None:
            return None

        entity = _parse_ares_response(ico, data)
        if entity:
            self._db.save_entity(entity)
        return entity

    def _fetch_company(self, ico: str) -> Optional[dict]:
        url = f"{ARES_API_URL}/{ico}"
        try:
            response = self._dl._session.get(url, timeout=15)
            if response.status_code == 404:
                logger.debug("ARES: ICO %s not found", ico)
                return None
            response.raise_for_status()
            return response.json()
        except Exception as exc:
            logger.warning("ARES API error for ICO %s: %s", ico, exc)
            return None


def _parse_ares_response(ico: str, data: dict) -> Optional[Entity]:
    name = data.get("obchodniJmeno") or data.get("nazev")
    if not name:
        return None

    address = data.get("sidlo", {})
    legal_form = data.get("pravniForma")
    date_created = data.get("datumVzniku")

    metadata = {
        "address": _format_address(address),
        "legal_form": legal_form,
        "date_created": date_created,
        "czNace": data.get("czNace", []),
        "financniUrad": data.get("financniUrad"),
    }

    return Entity(
        name=name,
        entity_type="organization",
        ico=ico,
        source="ares",
        metadata_json=json.dumps(metadata, ensure_ascii=False),
    )


def _format_address(address: dict) -> str:
    parts = []
    street = address.get("nazevUlice", "")
    cp = address.get("cisloDomovni", "")
    co = address.get("cisloOrientacni", "")

    if street:
        number = str(cp)
        if co:
            number += f"/{co}"
        parts.append(f"{street} {number}".strip())
    elif cp:
        parts.append(f"č.p. {cp}")

    city = address.get("nazevObce", "")
    postal = address.get("psc", "")
    if city:
        parts.append(f"{postal} {city}".strip() if postal else city)

    return ", ".join(parts)
