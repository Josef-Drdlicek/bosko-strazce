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

            results = data.get("Results", [])
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

            total = data.get("Total", 0)
            page_size = data.get("PageSize", 50) or 50
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
                params={"dotaz": f"ico:{ico}", "strana": page, "razeni": "1"},
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
    external_id = item.get("Id")
    if not external_id:
        return None

    subject = item.get("predmet", "") or ""
    if not subject:
        subject = "Bez předmětu"

    amount = item.get("hodnotaBezDph") or item.get("hodnotaVcetneDph")
    currency = item.get("cipiSmloupiodMena", "CZK") or "CZK"

    date_signed = _parse_date(item.get("datumUzavreni"))
    date_published = _parse_date(item.get("casZverejneni"))

    publisher = _extract_party(item, "Platce")
    counterparty = _extract_party(item, "Prijemce")

    source_url = f"https://www.hlidacstatu.cz/Detail/{external_id}"

    texts = []
    for attachment in item.get("Prilohy", []) or []:
        text = attachment.get("PlainTextContent")
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
        publisher_ico=publisher[1],
        publisher_name=publisher[0],
        counterparty_ico=counterparty[1],
        counterparty_name=counterparty[0],
        source_url=source_url,
        fulltext=fulltext,
    )


def _extract_party(item: dict, role_key: str) -> tuple[Optional[str], Optional[str]]:
    smluvni_strany = item.get("Subjekt") if role_key == "Platce" else None
    if smluvni_strany:
        return smluvni_strany.get("nazev"), smluvni_strany.get("ico")

    for strana in item.get("SmluvniStrany", []) or []:
        if role_key == "Platce" and strana.get("prijemce") is False:
            return strana.get("nazev"), strana.get("ico")
        if role_key == "Prijemce" and strana.get("prijemce") is True:
            return strana.get("nazev"), strana.get("ico")

    parties = item.get("SmluvniStrany", []) or []
    if parties:
        idx = 0 if role_key == "Platce" else -1
        party = parties[idx] if parties else {}
        return party.get("nazev"), party.get("ico")

    return None, None


def _parse_date(value) -> Optional[date]:
    if not value:
        return None
    try:
        return date.fromisoformat(str(value)[:10])
    except (ValueError, TypeError):
        return None
