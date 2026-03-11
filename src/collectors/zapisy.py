import logging
import re
from datetime import date
from typing import Optional

from bs4 import BeautifulSoup

from src.config import BASE_URL, ZAPISY_RM_YEARS, ZAPISY_ZM_YEARS
from src.database import Database
from src.downloader import Downloader
from src.models import Attachment, Document

logger = logging.getLogger(__name__)

SECTION_ZM = "zapisy_zm"
SECTION_RM = "zapisy_rm"

_FILE_LINK_PATTERN = re.compile(r"/file/\d+")
_DATE_PATTERN = re.compile(r"(\d{1,2})\.\s*(\d{1,2})\.\s*(\d{4})")
_MEETING_NUMBER_PATTERN = re.compile(r"(\d+)\.\s*(?:zasedání|schůze|jednání)", re.IGNORECASE)


class ZapisyCollector:
    """Collects ZM and RM meeting minutes from yearly archive pages."""

    def __init__(self, database: Database, downloader: Downloader):
        self._db = database
        self._dl = downloader

    def collect(self) -> int:
        total = 0
        total += self._collect_body("zapisy-zm", SECTION_ZM, ZAPISY_ZM_YEARS)
        total += self._collect_body("zapisy-rm", SECTION_RM, ZAPISY_RM_YEARS)
        return total

    def _collect_body(self, url_prefix: str, section: str, years: range) -> int:
        saved = 0
        for year in years:
            url = f"{BASE_URL}/{url_prefix}-{year}"
            logger.info("Fetching %s year %d...", section, year)

            try:
                html = self._dl.fetch_html(url)
            except Exception as exc:
                logger.warning("Failed to fetch %s: %s", url, exc)
                continue

            detail_urls = _extract_detail_links(html)
            logger.info("Found %d items for %s/%d", len(detail_urls), section, year)

            for detail_url in detail_urls:
                saved += self._collect_detail(detail_url, section)

        logger.info("%s: total new documents saved: %d", section, saved)
        return saved

    def _collect_detail(self, relative_url: str, section: str) -> int:
        absolute_url = f"{BASE_URL}{relative_url}"

        if self._db.document_exists(absolute_url):
            logger.debug("Skipping existing: %s", absolute_url)
            return 0

        try:
            html = self._dl.fetch_html(absolute_url)
        except Exception as exc:
            logger.warning("Failed to fetch detail %s: %s", absolute_url, exc)
            return 0

        soup = BeautifulSoup(html, "lxml")
        raw_title = _extract_title(soup)
        published = _extract_date_from_text(raw_title) or _extract_date_from_page(soup)
        meeting_number = _extract_meeting_number(raw_title)
        title = _build_zapisy_title(section, meeting_number, published, raw_title)
        attachments = _extract_attachments(soup)

        document = Document(
            source_url=absolute_url,
            title=title,
            section=section,
            published_date=published,
            attachments=attachments,
        )

        self._db.save_document(document)
        logger.info("Saved: %s (%d attachments)", title, len(attachments))
        return 1


def _extract_detail_links(html: str) -> list[str]:
    soup = BeautifulSoup(html, "lxml")
    links = []
    for anchor in soup.select("a[href]"):
        href = anchor["href"]
        if href.startswith("/") and ("zapis" in href.lower() or "zápis" in href.lower()):
            if href not in links:
                links.append(href)
    return links


def _extract_title(soup: BeautifulSoup) -> str:
    h1 = soup.find("h1")
    if h1:
        return h1.get_text(strip=True)

    title_tag = soup.find("title")
    if title_tag:
        text = title_tag.get_text(strip=True)
        return text.split(":")[0].strip()

    return "Untitled"


def _extract_attachments(soup: BeautifulSoup) -> list[Attachment]:
    attachments = []
    for anchor in soup.select("a[href]"):
        href = anchor["href"]
        if _FILE_LINK_PATTERN.search(href):
            filename = anchor.get_text(strip=True) or _filename_from_url(href)
            url = href if href.startswith("http") else f"{BASE_URL}{href}"
            attachments.append(Attachment(url=url, filename=filename))
    return attachments


def _extract_meeting_number(text: str) -> Optional[int]:
    match = _MEETING_NUMBER_PATTERN.search(text)
    if match:
        return int(match.group(1))
    return None


def _build_zapisy_title(
    section: str,
    meeting_number: Optional[int],
    meeting_date: Optional[date],
    fallback: str,
) -> str:
    body_label = "ZM" if section == SECTION_ZM else "RM"
    parts = [f"Zápis z"]

    if meeting_number:
        parts.append(f"{meeting_number}.")
        parts.append("zasedání" if section == SECTION_ZM else "schůze")
    else:
        parts.append("zasedání" if section == SECTION_ZM else "schůze")

    parts.append(body_label)

    if meeting_date:
        parts.append(f"dne {meeting_date.day}. {meeting_date.month}. {meeting_date.year}")

    result = " ".join(parts)
    if result == f"Zápis z zasedání {body_label}" or result == f"Zápis z schůze {body_label}":
        return fallback

    return result


def _extract_date_from_text(text: str) -> Optional[date]:
    match = _DATE_PATTERN.search(text)
    if match:
        try:
            return date(int(match.group(3)), int(match.group(2)), int(match.group(1)))
        except ValueError:
            return None
    return None


def _extract_date_from_page(soup: BeautifulSoup) -> Optional[date]:
    for text_node in soup.stripped_strings:
        result = _extract_date_from_text(text_node)
        if result:
            return result
    return None


def _filename_from_url(url: str) -> str:
    parts = url.rstrip("/").split("/")
    return parts[-1] if parts else "unknown"
