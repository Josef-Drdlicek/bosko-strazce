import logging
from datetime import date
from typing import Optional

from src.config import OPEN_DATA_UREDNI_DESKA
from src.database import Database
from src.downloader import Downloader
from src.models import Attachment, Document

logger = logging.getLogger(__name__)

SECTION = "uredni_deska"


class UredniDeskaCollector:
    """Collects documents from the Open Data JSON-LD API (OFN standard)."""

    def __init__(self, database: Database, downloader: Downloader):
        self._db = database
        self._dl = downloader

    def collect(self) -> int:
        logger.info("Fetching úřední deska from Open Data API...")
        data = self._dl.fetch_json(OPEN_DATA_UREDNI_DESKA)
        items = data.get("informace", [])
        logger.info("Found %d items in Open Data feed", len(items))

        saved_count = 0
        for item in items:
            document = _parse_item(item)
            if document is None:
                continue

            if self._db.document_exists(document.source_url):
                logger.debug("Skipping existing: %s", document.title)
                continue

            self._db.save_document(document)
            saved_count += 1
            logger.info("Saved: %s", document.title)

        logger.info(
            "Úřední deska: %d new documents (total in DB: %d)",
            saved_count,
            self._db.count_documents(SECTION),
        )
        return saved_count


def _parse_item(item: dict) -> Optional[Document]:
    title_obj = item.get("název", {})
    title = title_obj.get("cs", "")
    source_url = item.get("url", "")

    if not title or not source_url:
        logger.warning("Skipping item without title or URL: %s", item.get("iri"))
        return None

    published = _parse_date_field(item.get("vyvěšení", {}))
    valid_until = _parse_date_field(item.get("relevantní_do", {}))
    attachments = _parse_attachments(item.get("dokument", []))

    return Document(
        source_url=source_url,
        title=title,
        section=SECTION,
        published_date=published,
        valid_until=valid_until,
        attachments=attachments,
    )


def _parse_date_field(field: dict) -> Optional[date]:
    date_str = field.get("datum")
    if not date_str:
        return None
    try:
        return date.fromisoformat(date_str)
    except ValueError:
        logger.warning("Invalid date: %s", date_str)
        return None


def _parse_attachments(documents: list) -> list[Attachment]:
    attachments = []
    for doc in documents:
        url = doc.get("url", "")
        name_obj = doc.get("název", {})
        filename = name_obj.get("cs", "unknown")
        if url:
            attachments.append(Attachment(url=url, filename=filename))
    return attachments
