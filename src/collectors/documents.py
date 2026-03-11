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

SECTION_VYHLASKY = "vyhlasky"
SECTION_ROZPOCET = "rozpocet"
SECTION_POSKYTNUTE_INFO = "poskytnute_informace"

_FILE_LINK_PATTERN = re.compile(r"/file/\d+")
_DATE_PATTERN = re.compile(r"(\d{1,2})\.\s*(\d{1,2})\.\s*(\d{4})")
_ROZPOCET_YEAR_PATTERN = re.compile(r"/rozpocet[^/]*-(\d{4})")

_VYHLASKY_URL = f"{BASE_URL}/vyhlasky-a-narizeni-mesta-boskovice"
_ROZPOCET_URL = f"{BASE_URL}/rozpocet-a-rozklikavaci-rozpocet-mesta"
_POSKYTNUTE_INFO_URL = f"{BASE_URL}/poskytnute-informace"


class DocumentsCollector:
    """Collects vyhlášky, rozpočty, and poskytnuté informace."""

    def __init__(self, database: Database, downloader: Downloader):
        self._db = database
        self._dl = downloader

    def collect(self) -> int:
        total = 0
        total += self._collect_vyhlasky()
        total += self._collect_rozpocty()
        total += self._collect_poskytnute_informace()
        return total

    def _collect_vyhlasky(self) -> int:
        logger.info("Collecting vyhlášky a nařízení...")
        html = self._dl.fetch_html(_VYHLASKY_URL)
        soup = BeautifulSoup(html, "lxml")

        items = _parse_vyhlasky_items(soup)
        if not items:
            return self._collect_single_page(_VYHLASKY_URL, SECTION_VYHLASKY)

        saved = 0
        for title, attachments in items:
            source_url = f"{_VYHLASKY_URL}#vyhlaska-{saved}"
            if attachments:
                source_url = attachments[0].url

            if self._db.document_exists(source_url):
                continue

            document = Document(
                source_url=source_url,
                title=title,
                section=SECTION_VYHLASKY,
                attachments=attachments,
            )
            self._db.save_document(document)
            saved += 1
            logger.info("Saved vyhláška: %s", title)

        logger.info("Vyhlášky: %d new documents", saved)
        return saved

    def _collect_rozpocty(self) -> int:
        logger.info("Collecting rozpočty...")
        saved = 0

        html = self._dl.fetch_html(_ROZPOCET_URL)
        detail_links = _extract_internal_links(html)
        logger.info("Found %d rozpočet links", len(detail_links))

        for link in detail_links:
            absolute_url = f"{BASE_URL}{link}"

            if self._db.document_exists(absolute_url):
                continue

            try:
                detail_html = self._dl.fetch_html(absolute_url)
            except Exception as exc:
                logger.warning("Failed: %s - %s", absolute_url, exc)
                continue

            soup = BeautifulSoup(detail_html, "lxml")
            title = _build_rozpocet_title(link, soup)
            attachments = _extract_file_attachments(soup)

            if not attachments:
                continue

            document = Document(
                source_url=absolute_url,
                title=title,
                section=SECTION_ROZPOCET,
                attachments=attachments,
            )
            self._db.save_document(document)
            saved += 1
            logger.info("Saved rozpočet: %s (%d files)", title, len(attachments))

        logger.info("Rozpočty: %d new documents", saved)
        return saved

    def _collect_poskytnute_informace(self) -> int:
        logger.info("Collecting poskytnuté informace...")
        saved = 0

        html = self._dl.fetch_html(_POSKYTNUTE_INFO_URL)
        year_links = _extract_internal_links(html)
        logger.info("Found %d year links for poskytnuté informace", len(year_links))

        for link in year_links:
            absolute_url = f"{BASE_URL}{link}"

            if self._db.document_exists(absolute_url):
                continue

            try:
                detail_html = self._dl.fetch_html(absolute_url)
            except Exception as exc:
                logger.warning("Failed: %s - %s", absolute_url, exc)
                continue

            soup = BeautifulSoup(detail_html, "lxml")
            title = _extract_title(soup)
            attachments = _extract_file_attachments(soup)

            sub_links = _extract_internal_links(detail_html)
            for sub_link in sub_links:
                sub_url = f"{BASE_URL}{sub_link}"
                if self._db.document_exists(sub_url):
                    continue

                try:
                    sub_html = self._dl.fetch_html(sub_url)
                except Exception:
                    continue

                sub_soup = BeautifulSoup(sub_html, "lxml")
                sub_title = _extract_title(sub_soup)
                sub_attachments = _extract_file_attachments(sub_soup)

                if sub_attachments:
                    sub_doc = Document(
                        source_url=sub_url,
                        title=sub_title,
                        section=SECTION_POSKYTNUTE_INFO,
                        attachments=sub_attachments,
                    )
                    self._db.save_document(sub_doc)
                    saved += 1

            if attachments:
                document = Document(
                    source_url=absolute_url,
                    title=title,
                    section=SECTION_POSKYTNUTE_INFO,
                    attachments=attachments,
                )
                self._db.save_document(document)
                saved += 1
                logger.info("Saved: %s", title)

        logger.info("Poskytnuté informace: %d new documents", saved)
        return saved

    def _collect_single_page(self, url: str, section: str) -> int:
        if self._db.document_exists(url):
            logger.info("Already collected: %s", section)
            return 0

        html = self._dl.fetch_html(url)
        soup = BeautifulSoup(html, "lxml")
        title = _extract_title(soup)
        attachments = _extract_file_attachments(soup)

        document = Document(
            source_url=url,
            title=title,
            section=section,
            attachments=attachments,
        )
        self._db.save_document(document)
        logger.info("Saved %s: %s (%d files)", section, title, len(attachments))
        return 1


def _parse_vyhlasky_items(soup: BeautifulSoup) -> list[tuple[str, list[Attachment]]]:
    items = []
    current_title = None
    current_attachments = []

    content = soup.select_one(".board__content, .document-content, .content, article")
    if not content:
        content = soup

    for element in content.find_all(["li", "p", "h2", "h3", "strong"]):
        text = element.get_text(strip=True)
        links = element.find_all("a", href=_FILE_LINK_PATTERN)

        if links:
            title_text = text
            for link in links:
                link_text = link.get_text(strip=True)
                title_text = title_text.replace(link_text, "").strip(" -–,")

            if not title_text and current_title:
                title_text = current_title

            if not title_text:
                title_text = links[0].get_text(strip=True)

            attachments = []
            for link in links:
                href = link["href"]
                url = href if href.startswith("http") else f"{BASE_URL}{href}"
                filename = link.get_text(strip=True) or _filename_from_url(href)
                attachments.append(Attachment(url=url, filename=filename))

            items.append((title_text or "Vyhláška", attachments))
        elif text and not element.find("a", href=_FILE_LINK_PATTERN):
            current_title = text

    return items


def _build_rozpocet_title(url_path: str, soup: BeautifulSoup) -> str:
    year_match = _ROZPOCET_YEAR_PATTERN.search(url_path)
    if year_match:
        return f"Rozpočet města Boskovice {year_match.group(1)}"

    breadcrumbs = soup.select(".breadcrumbs a, .breadcrumb a, nav.breadcrumb a")
    if breadcrumbs:
        last_crumb = breadcrumbs[-1].get_text(strip=True)
        if last_crumb and last_crumb != "Boskovice":
            return last_crumb

    return _extract_title(soup)


def _extract_title(soup: BeautifulSoup) -> str:
    h1 = soup.find("h1")
    if h1:
        return h1.get_text(strip=True)
    title_tag = soup.find("title")
    if title_tag:
        return title_tag.get_text(strip=True).split(":")[0].strip()
    return "Untitled"


def _extract_file_attachments(soup: BeautifulSoup) -> list[Attachment]:
    attachments = []
    seen_urls = set()

    for anchor in soup.select("a[href]"):
        href = anchor["href"]
        if not _FILE_LINK_PATTERN.search(href):
            continue

        url = href if href.startswith("http") else f"{BASE_URL}{href}"
        if url in seen_urls:
            continue
        seen_urls.add(url)

        filename = anchor.get_text(strip=True) or _filename_from_url(href)
        attachments.append(Attachment(url=url, filename=filename))

    return attachments


def _extract_internal_links(html: str) -> list[str]:
    soup = BeautifulSoup(html, "lxml")
    links = []
    seen = set()

    for anchor in soup.select("a[href]"):
        href = anchor["href"]
        if not href.startswith("/"):
            continue
        if href in seen:
            continue
        if _FILE_LINK_PATTERN.search(href):
            continue
        if any(skip in href for skip in ["/kontakty", "/boskovize", "/webkamera", "#"]):
            continue

        seen.add(href)
        links.append(href)

    return links


def _extract_date_from_text(text: str) -> Optional[date]:
    match = _DATE_PATTERN.search(text)
    if match:
        try:
            return date(int(match.group(3)), int(match.group(2)), int(match.group(1)))
        except ValueError:
            return None
    return None


def _filename_from_url(url: str) -> str:
    parts = url.rstrip("/").split("/")
    return parts[-1] if parts else "unknown"
