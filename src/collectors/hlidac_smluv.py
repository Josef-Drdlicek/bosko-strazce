import logging
from datetime import date
from typing import Optional

from src.config import BOSKOVICE_ICO, HLIDAC_STATU_API_URL, HLIDAC_STATU_TOKEN
from src.database import Database
from src.downloader import Downloader
from src.models import Contract, Entity, EntityLink

logger = logging.getLogger(__name__)


class HlidacSmluvCollector:
    """Collects contracts from Hlidac statu API (Registr smluv data)."""

    def __init__(self, database: Database, downloader: Downloader):
        self._db = database
        self._dl = downloader

    def collect(self, ico: str = BOSKOVICE_ICO) -> int:
        if not HLIDAC_STATU_TOKEN:
            logger.error(
                "HLIDAC_STATU_TOKEN not set. "
                "Register at https://www.hlidacstatu.cz/api and set the token in .env"
            )
            return 0

        logger.info("Fetching contracts for ICO %s from Hlidac statu...", ico)
        saved = 0
        page = 1

        while True:
            data = self._fetch_page(ico, page)
            if data is None:
                break

            results = data.get("results", [])
            if not results:
                break

            for item in results:
                contract = _parse_contract(item)
                if contract is None:
                    continue

                if self._db.contract_exists(contract.external_id):
                    continue

                contract_id = self._db.save_contract(contract)
                self._link_entities(contract, contract_id)
                saved += 1

            total = data.get("total", 0)
            page_size = len(results)
            logger.info(
                "Page %d: fetched %d contracts (total available: %d)",
                page, len(results), total,
            )

            if page * page_size >= total:
                break
            page += 1

        logger.info("Hlidac smluv: saved %d new contracts", saved)
        return saved

    def _fetch_page(self, ico: str, page: int) -> Optional[dict]:
        url = f"{HLIDAC_STATU_API_URL}/Smlouvy/Hledat"
        try:
            response = self._dl._session.get(
                url,
                params={"dotaz": f"ico:{ico}", "strana": page, "razeni": "0"},
                headers={
                    "Authorization": f"Token {HLIDAC_STATU_TOKEN}",
                    "User-Agent": self._dl._session.headers.get("User-Agent", ""),
                },
                timeout=30,
            )
            response.raise_for_status()
            return response.json()
        except Exception as exc:
            logger.warning("Hlidac statu API error (page %d): %s", page, exc)
            return None

    def _link_entities(self, contract: Contract, contract_id: int):
        if contract.publisher_ico:
            entity_id = self._ensure_entity(
                contract.publisher_name or contract.publisher_ico,
                contract.publisher_ico,
                "organization",
            )
            self._db.save_entity_link(EntityLink(
                entity_id=entity_id,
                linked_type="contract",
                linked_id=contract_id,
                role="publisher",
            ))

        if contract.counterparty_ico:
            entity_id = self._ensure_entity(
                contract.counterparty_name or contract.counterparty_ico,
                contract.counterparty_ico,
                "organization",
            )
            self._db.save_entity_link(EntityLink(
                entity_id=entity_id,
                linked_type="contract",
                linked_id=contract_id,
                role="counterparty",
            ))

    def _ensure_entity(self, name: str, ico: str, entity_type: str) -> int:
        existing = self._db.get_entity_by_ico(ico)
        if existing:
            return existing["id"]
        entity = Entity(name=name, entity_type=entity_type, ico=ico, source="hlidac_statu")
        return self._db.save_entity(entity)


def _parse_contract(item: dict) -> Optional[Contract]:
    external_id = item.get("id")
    if not external_id:
        return None

    subject = item.get("predmet", "") or ""
    if not subject:
        subject = "Bez předmětu"

    raw_amount = item.get("hodnotaBezDph") or item.get("hodnotaVcetneDph")
    amount = float(raw_amount) if isinstance(raw_amount, (int, float)) else None

    raw_currency = item.get("ciziMena")
    currency = str(raw_currency) if isinstance(raw_currency, str) and raw_currency else "CZK"

    date_signed = _safe_parse_date(item.get("datumUzavreni"))
    date_published = _safe_parse_date(item.get("casZverejneni"))

    publisher_name, publisher_ico = _extract_platce(item)
    counterparty_name, counterparty_ico = _extract_prijemce(item)

    source_url = f"https://www.hlidacstatu.cz/Detail/{external_id}"

    texts = []
    for attachment in item.get("prilohy", []) or []:
        text = attachment.get("plainTextContent") or attachment.get("PlainTextContent")
        if text:
            texts.append(text)
    fulltext = "\n\n".join(texts) if texts else None

    return Contract(
        external_id=str(external_id),
        subject=subject,
        amount=amount,
        currency=currency,
        date_signed=date_signed,
        date_published=date_published,
        publisher_ico=publisher_ico,
        publisher_name=publisher_name,
        counterparty_ico=counterparty_ico,
        counterparty_name=counterparty_name,
        source_url=source_url,
        fulltext=fulltext,
    )


def _extract_platce(item: dict) -> tuple[Optional[str], Optional[str]]:
    platce = item.get("platce")
    if isinstance(platce, dict):
        return platce.get("nazev"), platce.get("ico")
    return None, None


def _extract_prijemce(item: dict) -> tuple[Optional[str], Optional[str]]:
    prijemce = item.get("prijemce")
    if isinstance(prijemce, list) and prijemce:
        first = prijemce[0]
        return first.get("nazev"), first.get("ico")
    if isinstance(prijemce, dict):
        return prijemce.get("nazev"), prijemce.get("ico")
    return None, None


def _safe_parse_date(value) -> Optional[date]:
    if not value or not isinstance(value, str):
        return None
    try:
        return date.fromisoformat(value[:10])
    except (ValueError, TypeError):
        return None
