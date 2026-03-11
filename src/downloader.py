import logging
import re
import time
from pathlib import Path
from typing import Optional
from urllib.parse import urljoin

import requests

from src.config import (
    BASE_URL,
    PDF_DIR,
    REQUEST_DELAY_SECONDS,
    REQUEST_TIMEOUT_SECONDS,
    USER_AGENT,
)

logger = logging.getLogger(__name__)

_FILE_URL_PATTERN = re.compile(r"/file/(\d+)")


class Downloader:
    def __init__(self):
        self._session = requests.Session()
        self._session.headers.update({"User-Agent": USER_AGENT})
        self._last_request_time = 0.0

    def fetch_json(self, url: str) -> dict:
        return self._get(url).json()

    def fetch_html(self, url: str) -> str:
        return self._get(url).text

    def download_file(self, url: str, section: str) -> Optional[Path]:
        target_dir = PDF_DIR / _sanitize_dirname(section)
        target_dir.mkdir(parents=True, exist_ok=True)

        filename = _extract_filename(url)
        target_path = target_dir / filename

        if target_path.exists():
            logger.debug("File already exists: %s", target_path)
            return target_path

        response = self._get(url, stream=True)
        content_disposition = response.headers.get("Content-Disposition", "")
        if "filename=" in content_disposition:
            server_filename = _parse_content_disposition(content_disposition)
            if server_filename:
                target_path = target_dir / server_filename

        with open(target_path, "wb") as f:
            for chunk in response.iter_content(chunk_size=8192):
                f.write(chunk)

        logger.info("Downloaded: %s (%d bytes)", target_path.name, target_path.stat().st_size)
        return target_path

    def _get(self, url: str, stream: bool = False) -> requests.Response:
        self._throttle()
        absolute_url = urljoin(BASE_URL, url) if not url.startswith("http") else url

        logger.debug("GET %s", absolute_url)
        response = self._session.get(
            absolute_url,
            timeout=REQUEST_TIMEOUT_SECONDS,
            stream=stream,
        )
        response.raise_for_status()
        return response

    def _throttle(self):
        elapsed = time.time() - self._last_request_time
        if elapsed < REQUEST_DELAY_SECONDS:
            time.sleep(REQUEST_DELAY_SECONDS - elapsed)
        self._last_request_time = time.time()


def _sanitize_dirname(name: str) -> str:
    return re.sub(r"[^\w\-]", "_", name).strip("_")


def _extract_filename(url: str) -> str:
    match = _FILE_URL_PATTERN.search(url)
    if match:
        return f"file_{match.group(1)}"
    parts = url.rstrip("/").split("/")
    return parts[-1] if parts else "unknown"


def _parse_content_disposition(header: str) -> Optional[str]:
    match = re.search(r'filename\*?=["\']?(?:UTF-8\'\')?([^"\';]+)', header, re.IGNORECASE)
    if match:
        name = match.group(1).strip()
        return re.sub(r'[<>:"/\\|?*]', "_", name)
    return None
