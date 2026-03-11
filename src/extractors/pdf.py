import logging
from pathlib import Path
from typing import Optional

logger = logging.getLogger(__name__)


class PdfExtractor:
    """Extracts text from PDF, DOCX, RTF, and DOC files."""

    def extract(self, file_path: str) -> Optional[str]:
        path = Path(file_path)

        if not path.exists():
            logger.warning("File not found: %s", file_path)
            return None

        suffix = path.suffix.lower()
        extractors = {
            ".pdf": self._extract_from_pdf,
            ".docx": self._extract_from_docx,
            ".doc": self._extract_from_doc,
            ".rtf": self._extract_from_rtf,
            ".html": self._extract_from_html,
            ".htm": self._extract_from_html,
        }

        extractor = extractors.get(suffix)
        if extractor is None:
            logger.debug("Unsupported format: %s", suffix)
            return None

        try:
            return extractor(path)
        except Exception as exc:
            logger.warning("Extraction failed for %s: %s", path.name, exc)
            return None

    def _extract_from_pdf(self, path: Path) -> Optional[str]:
        import fitz  # pymupdf

        text_parts = []
        with fitz.open(path) as doc:
            for page in doc:
                text_parts.append(page.get_text())

        text = "\n".join(text_parts).strip()
        return text if text else None

    def _extract_from_docx(self, path: Path) -> Optional[str]:
        import zipfile
        from xml.etree import ElementTree

        WORD_NS = "{http://schemas.openxmlformats.org/wordprocessingml/2006/main}"

        with zipfile.ZipFile(path) as zf:
            with zf.open("word/document.xml") as f:
                tree = ElementTree.parse(f)

        paragraphs = []
        for paragraph in tree.iter(f"{WORD_NS}p"):
            texts = [node.text for node in paragraph.iter(f"{WORD_NS}t") if node.text]
            if texts:
                paragraphs.append("".join(texts))

        text = "\n".join(paragraphs).strip()
        return text if text else None

    def _extract_from_doc(self, path: Path) -> Optional[str]:
        logger.debug("DOC format requires external tools; skipping: %s", path.name)
        return None

    def _extract_from_rtf(self, path: Path) -> Optional[str]:
        raw = path.read_bytes()
        text = _strip_rtf_markup(raw.decode("latin-1", errors="replace"))
        return text.strip() if text.strip() else None

    def _extract_from_html(self, path: Path) -> Optional[str]:
        from bs4 import BeautifulSoup

        html = path.read_text(encoding="utf-8", errors="replace")
        soup = BeautifulSoup(html, "lxml")

        for tag in soup(["script", "style", "nav", "footer", "header"]):
            tag.decompose()

        text = soup.get_text(separator="\n", strip=True)
        return text if text else None


def _strip_rtf_markup(rtf_text: str) -> str:
    """Minimal RTF-to-text: strips control words and groups."""
    import re

    rtf_text = re.sub(r"\\[a-z]{1,32}-?\d*\s?", " ", rtf_text)
    rtf_text = re.sub(r"[{}]", "", rtf_text)
    rtf_text = re.sub(r"\\'([0-9a-f]{2})", lambda m: chr(int(m.group(1), 16)), rtf_text)
    rtf_text = re.sub(r"\s+", " ", rtf_text)
    return rtf_text.strip()
