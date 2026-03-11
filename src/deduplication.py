import logging
import re
import unicodedata
from collections import defaultdict

from src.database import Database

logger = logging.getLogger(__name__)

_FILE_ID_PATTERN = re.compile(r"/file/(\d+)")


def run_deduplication(database: Database) -> int:
    marked = 0
    marked += _deduplicate_by_file_url(database)
    marked += _deduplicate_by_title_date(database)
    logger.info("Total duplicates marked: %d", marked)
    return marked


def _deduplicate_by_file_url(database: Database) -> int:
    all_attachments = database.get_attachment_file_ids()

    file_id_to_documents: dict[str, list[dict]] = defaultdict(list)
    for att in all_attachments:
        match = _FILE_ID_PATTERN.search(att["url"])
        if match:
            file_id_to_documents[match.group(1)].append(att)

    marked = 0
    seen_documents: set[int] = set()

    for file_id, entries in file_id_to_documents.items():
        if len(entries) < 2:
            continue

        doc_ids = list({e["document_id"] for e in entries})
        if len(doc_ids) < 2:
            continue

        primary_id = min(doc_ids)
        for doc_id in doc_ids:
            if doc_id == primary_id:
                continue
            if doc_id in seen_documents:
                continue
            seen_documents.add(doc_id)
            database.mark_duplicate(doc_id, primary_id)
            marked += 1
            logger.info(
                "File %s: document %d is duplicate of %d",
                file_id, doc_id, primary_id,
            )

    logger.info("Duplicates by file URL: %d", marked)
    return marked


def _deduplicate_by_title_date(database: Database) -> int:
    from src.database import Database

    marked = 0
    title_date_groups: dict[str, list[dict]] = defaultdict(list)

    sections = [
        "uredni_deska", "zapisy_zm", "zapisy_rm",
        "uredni_deska_archiv", "vyhlasky",
    ]

    for section in sections:
        docs = database.get_documents_by_section(section, limit=10000, offset=0)
        for doc in docs:
            if doc.get("duplicate_of"):
                continue
            key = _normalize_title(doc["title"]) + "|" + (doc.get("published_date") or "")
            title_date_groups[key].append(doc)

    for key, docs in title_date_groups.items():
        if len(docs) < 2:
            continue

        primary = min(docs, key=lambda d: d["id"])
        for doc in docs:
            if doc["id"] == primary["id"]:
                continue
            if doc.get("duplicate_of"):
                continue
            database.mark_duplicate(doc["id"], primary["id"])
            marked += 1
            logger.info(
                "Title match: '%s' (doc %d) is duplicate of (doc %d)",
                doc["title"][:60], doc["id"], primary["id"],
            )

    logger.info("Duplicates by title+date: %d", marked)
    return marked


def _normalize_title(title: str) -> str:
    title = unicodedata.normalize("NFKD", title.lower().strip())
    title = re.sub(r"[^\w\s]", "", title)
    title = re.sub(r"\s+", " ", title)
    return title.strip()
