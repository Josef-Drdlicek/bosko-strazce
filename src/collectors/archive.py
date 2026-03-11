import logging
import re
from datetime import date
from typing import Optional

from bs4 import BeautifulSoup

from src.config import BASE_URL
from src.database import Database
from src.downloader import Downloader
from src.models import Attachment, Document

logger = logging.getLogger(__name__)

SECTION = "uredni_deska_archiv"
ARCHIVE_URL = f"{BASE_URL}/archiv-uredni-desky"

_FILE_LINK_PATTERN = re.compile(r"/file/(\d+)")
_DATE_PATTERN = re.compile(r"(\d{1,2})\.\s*(\d{1,2})\.\s*(\d{4})")
_PAGE_COUNT_PATTERN = re.compile(r"z\s+(\d+)")
_TAG_PATTERN = re.compile(r"ÚD\s*-\s*(\w+)")

# vismo CMS uses elparam-{element_id}-page=N for pagination
_PAGINATION_PARAM = "elparam-11-page"


class ArchiveCollector:
    """Collects historical documents from úřední deska archive via HTTP pagination."""

    def __init__(self, database: Database, downloader: Downloader):
        self._db = database
        self._dl = downloader

    def collect(self) -> int:
        logger.info("Starting archive collection...")

        total_pages = self._detect_page_count()
        logger.info("Detected %d pages in archive", total_pages)

        total_saved = 0
        for page_num in range(1, total_pages + 1):
            url = f"{ARCHIVE_URL}?{_PAGINATION_PARAM}={page_num}"
            logger.info("Processing archive page %d/%d...", page_num, total_pages)

            try:
                html = self._dl.fetch_html(url)
            except Exception as exc:
                logger.warning("Failed to fetch page %d: %s", page_num, exc)
                continue

            saved = self._process_page(html)
            total_saved += saved

        logger.info("Archive: %d new documents saved", total_saved)
        return total_saved

    def _detect_page_count(self) -> int:
        html = self._dl.fetch_html(ARCHIVE_URL)
        soup = BeautifulSoup(html, "lxml")

        current_page = soup.find(class_="current-page")
        if current_page:
            match = _PAGE_COUNT_PATTERN.search(current_page.get_text())
            if match:
                return int(match.group(1))

        pgn = soup.find("nav", class_="pgn")
        if pgn:
            for link in pgn.find_all("a", href=True):
                text = link.get_text(strip=True)
                if text == "Na konec":
                    href = link["href"]
                    match = re.search(r"page=(\d+)", href)
                    if match:
                        return int(match.group(1))

        return 1

    def _process_page(self, html: str) -> int:
        soup = BeautifulSoup(html, "lxml")
        saved = 0

        main = soup.find("main")
        if not main:
            main = soup

        doc_links = main.select("a.document__link[href]")
        for link in doc_links:
            document = self._parse_document_link(link)
            if document is None:
                continue

            if self._db.document_exists(document.source_url):
                continue

            self._db.save_document(document)
            saved += 1

        return saved

    def _parse_document_link(self, link_element) -> Optional[Document]:
        href = link_element.get("href", "")
        if not href or href.startswith("#"):
            return None

        title_el = link_element.find("h3", class_="document__title")
        title = title_el.get_text(strip=True) if title_el else link_element.get_text(strip=True)
        if not title or len(title) < 3:
            return None

        source_url = href if href.startswith("http") else f"{BASE_URL}{href}"

        document_body = link_element.parent
        published = None
        department = None

        if document_body:
            body_text = document_body.get_text()
            published = _extract_date_from_text(body_text)

            tag_match = _TAG_PATTERN.search(body_text)
            if tag_match:
                department = tag_match.group(1)

        attachments = self._collect_detail_attachments(source_url)

        return Document(
            source_url=source_url,
            title=title,
            section=SECTION,
            published_date=published,
            department=department,
            attachments=attachments,
        )

    def _collect_detail_attachments(self, url: str) -> list[Attachment]:
        try:
            html = self._dl.fetch_html(url)
        except Exception:
            return []

        soup = BeautifulSoup(html, "lxml")
        attachments = []
        seen = set()

        for link in soup.find_all("a", href=True):
            href = link["href"]
            if not _FILE_LINK_PATTERN.search(href):
                continue

            file_url = href if href.startswith("http") else f"{BASE_URL}{href}"
            if file_url in seen:
                continue
            seen.add(file_url)

            filename = link.get_text(strip=True) or f"file_{href.split('/')[-1]}"
            attachments.append(Attachment(url=file_url, filename=filename))

        return attachments


def _extract_date_from_text(text: str) -> Optional[date]:
    match = _DATE_PATTERN.search(text)
    if match:
        try:
            return date(int(match.group(3)), int(match.group(2)), int(match.group(1)))
        except ValueError:
            return None
    return None
