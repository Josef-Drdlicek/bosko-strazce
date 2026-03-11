# Bosko Strážce -- Stav projektu

> Poslední aktualizace: 2026-03-11

## Co je to za projekt

Antikorupční monitorovací platforma pro město Boskovice. Systém automaticky sbírá veřejná data
z webu města, Registru smluv, ARES a CEDR, propojuje je do znalostního grafu (dokumenty, smlouvy,
subjekty, dotace) a poskytuje webové rozhraní pro vyhledávání a analýzu.

Boskovice jsou "laboratoř" -- architektura je navržena pro budoucí rozšíření na další města.

---

## Co je hotové

### Sběr metadat z webu města (kompletní)

1 039 dokumentů ze 7 datových zdrojů:

| Sekce | Dokumentů | Metoda |
|-------|-----------|--------|
| Úřední deska (aktuální) | 56 | JSON-LD API (OFN standard) |
| Zápisy zastupitelstva (ZM) | 127 | HTML scraping |
| Zápisy rady města (RM) | 336 | HTML scraping |
| Vyhlášky a nařízení | 1 (66 PDF) | HTML scraping |
| Rozpočty | 20 | HTML scraping |
| Poskytnuté informace (106) | 47 | HTML scraping |
| Archiv úřední desky | 452 | HTML scraping (21 stránek) |

### Stažené soubory a fulltext

- **1 874 příloh** staženo (PDF, DOCX, RTF, HTML, XLS)
- **45 dokumentů** s extrahovaným fulltextem (pymupdf + docx + rtf parser)
- Fulltextové vyhledávání funkční přes CLI i web UI

### Deduplikace

- 136 duplicit identifikováno a označeno (1 dle URL souboru, 135 dle titulku+data)
- Deduplikované dokumenty jsou skryty z výchozího zobrazení

### Rozšířený datový model

Databáze obsahuje nové tabulky pro:
- **entities** (firmy, organizace, osoby s IČO)
- **contracts** (smlouvy z Registru smluv)
- **subsidies** (dotace z CEDR)
- **entity_links** (propojení subjektů s dokumenty, smlouvami a dotacemi)

### Sběr externích dat (připraveno)

Implementovány kolektory pro:
- **Hlídač státu API** -- smlouvy z Registru smluv (Boskovice IČO 00279978)
- **Hlídač státu API** -- dotace z CEDR/IsRed/Eufondy
- **ARES REST API** -- detaily firem dle IČO (adresa, právní forma, datum vzniku)
- **Entity extractor** -- regex extrakce IČO z fulltextu dokumentů

> Ke spuštění sběru smluv a dotací je potřeba nastavit `HLIDAC_STATU_TOKEN` v `.env`.

### Webové rozhraní

FastAPI + Jinja2 SSR webové UI:
- `/` -- Dashboard (statistiky, poslední dokumenty a smlouvy)
- `/documents` -- Procházení dokumentů (filtry dle sekce, fulltext)
- `/document/{id}` -- Detail dokumentu s přílohami a propojenými subjekty
- `/contracts` -- Procházení smluv (částka, datum, protistrana)
- `/contract/{id}` -- Detail smlouvy
- `/entities` -- Procházení subjektů (firmy, organizace)
- `/entity/{id}` -- Detail subjektu se všemi propojeními
- `/search` -- Globální vyhledávání přes dokumenty, smlouvy i subjekty

---

## Struktura projektu

```
bosko-strazce/
├── main.py                        # CLI (16 příkazů)
├── requirements.txt               # Python závislosti
├── .env.example                   # Šablona pro API klíče
├── .gitignore
│
├── src/
│   ├── config.py                  # URL konstanty, env proměnné, nastavení
│   ├── models.py                  # Doménové entity (Document, Attachment, Entity, Contract, Subsidy, EntityLink)
│   ├── database.py                # SQLite repository (6 tabulek, CRUD, search, stats, migrace)
│   ├── downloader.py              # HTTP klient s rate limiting
│   ├── deduplication.py           # Detekce duplicit (dle file URL + title+date)
│   │
│   ├── collectors/
│   │   ├── uredni_deska.py        # JSON-LD API
│   │   ├── zapisy.py              # Zápisy ZM a RM (vylepšená metadata)
│   │   ├── documents.py           # Vyhlášky, rozpočty, poskytnuté informace (vylepšené titulky)
│   │   ├── archive.py             # Archiv úřední desky
│   │   ├── hlidac_smluv.py        # Registr smluv via Hlídač státu API
│   │   ├── ares.py                # ARES REST API (detail firem dle IČO)
│   │   └── cedr.py                # Dotace via Hlídač státu API
│   │
│   ├── extractors/
│   │   ├── pdf.py                 # Extrakce textu z PDF/DOCX/RTF/HTML
│   │   └── entity_extractor.py    # Regex extrakce IČO z fulltextu
│   │
│   └── web/
│       ├── app.py                 # FastAPI aplikace (7 stránek)
│       ├── templates/             # Jinja2 HTML šablony
│       └── static/                # CSS (responsive, mobile-first)
│
└── data/
    ├── db/boskovice.db            # SQLite databáze
    └── pdf/                       # Stažené soubory (~1 874 souborů)
```

---

## Jak to spustit

### Předpoklady

```bash
python -m pip install -r requirements.txt
```

### Nastavení API klíčů

```bash
cp .env.example .env
# Editovat .env a nastavit HLIDAC_STATU_TOKEN
```

### Příkazy

```bash
# Sběr dat z webu města
python main.py collect-uredni-deska
python main.py collect-zapisy
python main.py collect-documents
python main.py collect-archive
python main.py collect-all            # Vše najednou + stažení + extrakce

# Sběr externích dat (vyžaduje HLIDAC_STATU_TOKEN)
python main.py collect-contracts      # Smlouvy z Registru smluv
python main.py collect-subsidies      # Dotace z CEDR

# Obohacení dat
python main.py download-files         # Stáhnout PDF přílohy
python main.py extract-text           # Extrahovat text ze souborů
python main.py extract-entities       # Najít IČO v textech dokumentů
python main.py enrich-entities        # Doplnit info z ARES
python main.py deduplicate            # Označit duplicity

# Zobrazení a vyhledávání
python main.py stats
python main.py search "klíčové slovo"

# Webové rozhraní
python main.py serve --port 8000      # http://localhost:8000
```

---

## Databázové schéma

### Tabulka `documents`

| Sloupec | Typ | Popis |
|---------|-----|-------|
| id | INTEGER PK | Auto ID |
| source_url | TEXT UNIQUE | URL dokumentu na webu města |
| title | TEXT | Název dokumentu |
| section | TEXT | Sekce (uredni_deska, zapisy_zm, zapisy_rm, ...) |
| published_date | TEXT | Datum zveřejnění |
| valid_until | TEXT | Relevantní do |
| department | TEXT | Odbor města |
| fulltext | TEXT | Extrahovaný text |
| collected_at | TEXT | Datum sběru |
| duplicate_of | INTEGER FK | Odkaz na originální dokument (NULL = originál) |

### Tabulka `attachments`

| Sloupec | Typ | Popis |
|---------|-----|-------|
| id | INTEGER PK | Auto ID |
| document_id | INTEGER FK | Odkaz na documents.id |
| url | TEXT | URL souboru |
| filename | TEXT | Název souboru |
| local_path | TEXT | Cesta ke staženému souboru |
| size_bytes | INTEGER | Velikost |

### Tabulka `entities`

| Sloupec | Typ | Popis |
|---------|-----|-------|
| id | INTEGER PK | Auto ID |
| name | TEXT | Název subjektu |
| entity_type | TEXT | Typ (organization, person, city_department) |
| ico | TEXT UNIQUE | IČO (nullable) |
| source | TEXT | Zdroj dat (hlidac_statu, ares, document_extraction) |
| metadata_json | TEXT | Strukturovaná data z ARES (JSON) |

### Tabulka `contracts`

| Sloupec | Typ | Popis |
|---------|-----|-------|
| id | INTEGER PK | Auto ID |
| external_id | TEXT UNIQUE | ID z Hlídače státu |
| subject | TEXT | Předmět smlouvy |
| amount | REAL | Částka |
| currency | TEXT | Měna (default CZK) |
| date_signed | TEXT | Datum podpisu |
| publisher_ico / publisher_name | TEXT | Objednatel |
| counterparty_ico / counterparty_name | TEXT | Dodavatel |
| source_url | TEXT | URL na Hlídači státu |
| fulltext | TEXT | Text příloh smlouvy |

### Tabulka `subsidies`

| Sloupec | Typ | Popis |
|---------|-----|-------|
| id | INTEGER PK | Auto ID |
| external_id | TEXT UNIQUE | ID dotace |
| title | TEXT | Název projektu |
| provider | TEXT | Poskytovatel |
| recipient_ico / recipient_name | TEXT | Příjemce |
| program | TEXT | Dotační program |
| amount | REAL | Částka |
| year | INTEGER | Rok rozhodnutí |

### Tabulka `entity_links`

| Sloupec | Typ | Popis |
|---------|-----|-------|
| entity_id | INTEGER FK | Odkaz na entities.id |
| linked_type | TEXT | Typ propojení (document, contract, subsidy) |
| linked_id | INTEGER | ID propojené entity |
| role | TEXT | Role (publisher, counterparty, mentioned, recipient) |

---

## Technický kontext pro AI agenty

### Web města Boskovice

- CMS: **vismo** (WEBHOUSE), verze 6
- IČO města: **00279978**
- Rendering: server-side HTML
- Soubory: `https://www.boskovice.cz/file/{NUMERIC_ID}`
- Paginace: `?elparam-{ELEMENT_ID}-page={PAGE_NUM}`
- Open Data: `https://www.boskovice.cz/data/308` (JSON-LD)

### Externí API

- **Hlídač státu**: `https://api.hlidacstatu.cz/Api/v2/` (token v .env)
  - `/Smlouvy/Hledat?dotaz=ico:00279978` -- smlouvy
  - `/Dotace/Hledat?dotaz=ico:00279978` -- dotace
- **ARES**: `https://ares.gov.cz/ekonomicke-subjekty-v-be/rest/ekonomicke-subjekty/{ICO}` (bez auth, 500 req/min)

### Technologický stack

- Python 3.14
- requests + BeautifulSoup4 + lxml
- pymupdf (PDF text)
- FastAPI + Jinja2 + uvicorn (web UI)
- python-dotenv (env proměnné)
- SQLite (data/db/boskovice.db)

### Konvence kódu

- Čistá architektura: kolektory v `src/collectors/`, extraktory v `src/extractors/`, web v `src/web/`
- Každý kolektor má metodu `collect() -> int`
- Repository pattern v `Database` třídě
- HTTP throttling v `Downloader` (1 req/s pro web města, 0.15s pro ARES)
- Doménové entity jako dataclasses v `src/models.py`
- CLI přes argparse v `main.py`

---

## Doporučené další kroky

### Priorita 1: Aktivovat externí zdroje

1. Zaregistrovat se na https://www.hlidacstatu.cz/api
2. Nastavit token v `.env`
3. Spustit `python main.py collect-contracts` a `python main.py collect-subsidies`
4. Spustit `python main.py enrich-entities`

### Priorita 2: Zlepšit pokrytí fulltextu

- Integrace Tesseract OCR pro skenované PDF (aktuálně ~23 z 45 nelze extrahovat)
- Rozšířit `get_documents_without_fulltext` o více souborových typů

### Priorita 3: Pokročilá analýza

- Detekce vzorců: opakovaní dodavatelé, neobvyklé částky, časové korelace
- Cross-referencing: hlasování zastupitelů vs. smlouvy s propojenými firmami
- Timeline view: chronologické zobrazení rozhodnutí a smluv

### Priorita 4: Další datové zdroje

- Starý archiv úřední desky (2014-2025)
- Zápisy komisí a výborů
- Věstník veřejných zakázek (vvz.nipez.cz)
- Katastr nemovitostí (ČÚZK)
- Volby.cz (složení zastupitelstva)

### Priorita 5: Multi-city

- Abstrakce konfigurace pro jiná města (IČO, URL patterny)
- Sdílený web UI s výběrem města
- PostgreSQL místo SQLite pro škálování
