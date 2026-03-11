import logging
import re

from src.database import Database
from src.models import Entity, EntityLink

logger = logging.getLogger(__name__)

_ICO_PATTERNS = [
    re.compile(r"(?:IČO?|IČ|ICO)\s*[:.]?\s*(\d{8})\b", re.IGNORECASE),
    re.compile(r"(?:identifikační\s+číslo)\s*[:.]?\s*(\d{8})\b", re.IGNORECASE),
]

_BOSKOVICE_ICO = "00279978"


def extract_entities_from_documents(database: Database) -> int:
    linked = 0

    docs = database.get_documents_by_section("", limit=0, offset=0)
    if not docs:
        docs = _get_all_documents_with_fulltext(database)

    for doc in docs:
        if not doc.get("fulltext"):
            continue
        icos = _extract_icos(doc["fulltext"])
        for ico in icos:
            entity_id = _ensure_entity(database, ico)
            if entity_id:
                database.save_entity_link(EntityLink(
                    entity_id=entity_id,
                    linked_type="document",
                    linked_id=doc["id"],
                    role="mentioned",
                ))
                linked += 1

    logger.info("Entity extraction: created %d document-entity links", linked)
    return linked


def _get_all_documents_with_fulltext(database: Database) -> list[dict]:
    with database._connection() as conn:
        rows = conn.execute(
            """SELECT id, fulltext FROM documents
               WHERE fulltext IS NOT NULL AND fulltext != ''"""
        ).fetchall()
        return [dict(row) for row in rows]


def _extract_icos(text: str) -> set[str]:
    found = set()
    for pattern in _ICO_PATTERNS:
        for match in pattern.finditer(text):
            ico = match.group(1)
            if _is_valid_ico(ico) and ico != _BOSKOVICE_ICO:
                found.add(ico)
    return found


def _is_valid_ico(ico: str) -> bool:
    if len(ico) != 8 or not ico.isdigit():
        return False
    if ico == "00000000":
        return False
    weights = [8, 7, 6, 5, 4, 3, 2]
    total = sum(int(ico[i]) * weights[i] for i in range(7))
    remainder = total % 11
    if remainder == 0:
        check = 1
    elif remainder == 1:
        check = 0
    else:
        check = 11 - remainder
    return int(ico[7]) == check


def _ensure_entity(database: Database, ico: str) -> int:
    existing = database.get_entity_by_ico(ico)
    if existing:
        return existing["id"]

    entity = Entity(
        name=f"ICO {ico}",
        entity_type="organization",
        ico=ico,
        source="document_extraction",
    )
    return database.save_entity(entity)
