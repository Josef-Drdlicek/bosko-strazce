# Bosko Strážce -- Stav projektu

> Poslední aktualizace: 2026-03-14

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

### Sběr externích dat (aktivní)

Implementovány kolektory pro:
- **Hlídač státu API** -- smlouvy z Registru smluv (Boskovice IČO 00279978) -- **2 462 smluv**
- **Hlídač státu API** -- dotace z CEDR/IsRed/Eufondy -- **443 dotací**
- **ARES REST API** -- detaily firem dle IČO (adresa, právní forma, datum vzniku)
- **Entity extractor** -- regex extrakce IČO z fulltextu dokumentů -- **535 subjektů, 5 234 vazeb**

`HLIDAC_STATU_TOKEN` je nastaven v `.env`.

### Signály (nové)

Automatická detekce anomálií v datech (`SignalService`):
- **Koncentrace zakázek** -- dodavatelé s objemem smluv výrazně nad mediánem (poměr k mediánu, závažnost)
- **Koncentrace dotací** -- příjemci s nejvyššími částkami
- **Nejvyšší smlouvy** -- top smlouvy dle částky
- Webová stránka `/signals` s přehledovou tabulkou
- API endpoint `GET /api/signals`
- Informační disclaimer o interpretaci signálů

### Grafová vizualizace vztahů (nové)

Interaktivní D3.js force-directed graf zobrazující propojení mezi subjekty:
- **Stránka `/graph/{id}`** — vizualizace vztahů entity a jejích sousedů
- **API endpoint `GET /api/graph/{id}`** — JSON data (nodes + edges) pro vizualizéry
- Uzly: subjekty (velikost dle celkového objemu smluv, barva dle typu entity)
- Hrany: společné smlouvy/dokumenty/dotace (barva dle typu, tloušťka dle počtu)
- Interakce: zoom, pan, drag uzlů, klik = přechod na detail entity
- Odkaz „Graf vztahů" na detailu každého subjektu

### Integrace ARES (kompletní)

Dvouvrstvá integrace — Python kolektor (batch enrichment) + Laravel service (live lookup):

- **Python `AresCollector`** (`src/collectors/ares.py`):
  - Batch obohacení všech entit s IČO
  - Uložení metadat do `metadata_json`
  - Rate limiting 0.15s per request
- **Laravel `AresService`** (`app/Services/AresService.php`):
  - Nativní HTTP klient pro ARES REST API
  - Vyhledávání firem podle názvu (POST `/vyhledat`)
  - Lookup podle IČO (GET `/{ico}`)
  - 24h cache výsledků přes Laravel Cache
  - Automatické obohacení entit při zobrazení detailu
- **Webová stránka `/ares`** — interaktivní vyhledávání v registru ARES (dle názvu i IČO)
- **Detail subjektu** — automaticky zobrazuje strukturovaná data z ARES (sídlo, právní forma, datum vzniku, CZ-NACE, finanční úřad)

### Webové rozhraní (Laravel) — moderní design

Laravel 12 + Blade + Tailwind CSS 4 + Alpine.js:

| Route | Popis |
|-------|-------|
| `/` | Dashboard — statistiky, poslední dokumenty, smlouvy, top dodavatelé |
| `/documents` | Dokumenty — filtry dle sekce, fulltext, paginace |
| `/documents/{id}` | Detail dokumentu s přílohami a propojenými subjekty |
| `/contracts` | Smlouvy — částka, datum, protistrana, paginace |
| `/contracts/{id}` | Detail smlouvy |
| `/subsidies` | Dotace — filtry dle roku, vyhledávání, paginace |
| `/subsidies/{id}` | Detail dotace |
| `/entities` | Subjekty — filtry dle typu, vyhledávání, počet vazeb |
| `/entities/{id}` | Detail subjektu — agregované statistiky, časová osa, role u vazeb, ARES data |
| `/signals` | Signály — koncentrace zakázek, nejvyšší smlouvy, příjemci dotací |
| `/graph/{id}` | Interaktivní graf vztahů subjektu (D3.js force-directed) |
| `/ares` | ARES vyhledávání — hledání firem podle názvu nebo IČO |
| `/search` | Globální vyhledávání — dokumenty, smlouvy, subjekty, dotace |

Design:
- Indigo/slate profesionální paleta
- Gradient hero banner na dashboardu
- Heroicons SVG ikony v navigaci a sekcích
- Responzivní mobilní menu (Alpine.js)
- Inter font, zaoblené karty se stíny, barevné badge pro částky a sekce

### REST API

| Endpoint | Popis |
|----------|-------|
| `GET /api/stats` | Celkové statistiky (počty, součty částek, sekce) |
| `GET /api/entities` | Seznam subjektů (filtry, paginace) |
| `GET /api/entities/{id}` | Detail subjektu |
| `GET /api/entities/{id}/relations` | Všechny vztahy subjektu s napojenými záznamy |
| `GET /api/signals` | Detekované signály (koncentrace, vysoké smlouvy, dotace) |
| `GET /api/graph/{id}` | Graf vztahů entity (nodes + edges pro vizualizéry) |

---

## Architektura (SOLID)

### Service Layer

Business logika je oddělena od controllerů do servisních tříd:

| Service | Odpovědnost |
|---------|-------------|
| `StatsService` | Dashboard statistiky, agregace dat |
| `DocumentService` | Filtrování, paginace a relace dokumentů |
| `ContractService` | Filtrování, paginace a relace smluv |
| `EntityService` | Filtrování, paginace, resolving relací subjektů |
| `SubsidyService` | Filtrování, paginace a relace dotací |
| `SearchService` | Globální vyhledávání napříč všemi typy |
| `AresService` | HTTP klient pro ARES REST API s cachováním |
| `SignalService` | Detekce anomálií: koncentrace zakázek, dotací, vysoké smlouvy |
| `GraphService` | Sestavení dat grafu vztahů (nodes, edges) pro D3.js vizualizaci |

### Controllery

Tenké controllery delegují na services — žádná business logika v controllerech:

| Controller | Routes |
|------------|--------|
| `DashboardController` | `/` |
| `DocumentController` | `/documents`, `/documents/{id}` |
| `ContractController` | `/contracts`, `/contracts/{id}` |
| `SubsidyController` | `/subsidies`, `/subsidies/{id}` |
| `EntityController` | `/entities`, `/entities/{id}` |
| `AresController` | `/ares`, `/ares/search` |
| `SearchController` | `/search` |
| `SignalController` | `/signals` |
| `GraphController` | `/graph/{entity}` |
| `StatsApiController` | `/api/stats` |
| `EntityApiController` | `/api/entities/*` |
| `SignalApiController` | `/api/signals` |
| `GraphApiController` | `/api/graph/{entity}` |

---

## Struktura projektu

```
bosko-strazce/
├── main.py                        # Python CLI (16 příkazů pro sběr dat)
├── requirements.txt               # Python závislosti
├── .env.example                   # Šablona pro API klíče
├── .gitignore
│
├── src/                           # Python sběr dat (stále aktivní)
│   ├── config.py                  # URL konstanty, env proměnné, nastavení
│   ├── models.py                  # Doménové entity (dataclasses)
│   ├── database.py                # SQLite repository (6 tabulek)
│   ├── downloader.py              # HTTP klient s rate limiting
│   ├── deduplication.py           # Detekce duplicit
│   ├── collectors/                # Kolektory dat
│   │   ├── uredni_deska.py        # Úřední deska (JSON-LD)
│   │   ├── zapisy.py              # Zápisy ZM/RM
│   │   ├── documents.py           # Vyhlášky, rozpočty, poskytnuté info
│   │   ├── archive.py             # Archiv úřední desky
│   │   ├── hlidac_smluv.py        # Smlouvy (Hlídač státu API)
│   │   ├── cedr.py                # Dotace (Hlídač státu API)
│   │   └── ares.py                # ARES batch enrichment
│   └── extractors/
│       ├── pdf.py                 # Text z PDF/DOCX/RTF/HTML
│       └── entity_extractor.py    # Extrakce IČO z fulltextu
│
├── laravel/                       # Laravel webová aplikace (hlavní stack)
│   ├── app/
│   │   ├── Models/                # Eloquent modely (6)
│   │   ├── Services/              # Business logika (7 services)
│   │   ├── Http/Controllers/      # Web controllery (7) + API (2)
│   │   └── Console/Commands/      # Artisan příkazy (import, collect)
│   ├── database/
│   │   ├── migrations/            # Migrace (6 tabulek)
│   │   └── database.sqlite        # Laravel SQLite databáze
│   ├── resources/
│   │   ├── views/                 # Blade šablony (13 šablon, 4 komponenty)
│   │   ├── css/app.css            # Tailwind 4 + custom theme
│   │   └── js/app.js              # Alpine.js
│   ├── routes/
│   │   ├── web.php                # 14 web routes
│   │   └── api.php                # 4 API routes
│   └── public/                    # Veřejný adresář (Vite build)
│
└── data/
    ├── db/boskovice.db            # Legacy Python SQLite databáze
    └── pdf/                       # Stažené soubory (~1 874 souborů)
```

---

## Jak to spustit

### Předpoklady

```bash
# Python kolektory
python -m pip install -r requirements.txt

# Laravel aplikace
cd laravel
composer install
npm install
```

### Nastavení API klíčů

```bash
cp .env.example .env
# Editovat .env a nastavit HLIDAC_STATU_TOKEN
```

### Sběr dat (Python)

```bash
python main.py collect-all            # Vše najednou + stažení + extrakce
python main.py collect-contracts      # Smlouvy z Registru smluv (vyžaduje token)
python main.py collect-subsidies      # Dotace z CEDR (vyžaduje token)
python main.py download-files         # Stáhnout PDF přílohy
python main.py extract-text           # Extrahovat text ze souborů
python main.py extract-entities       # Najít IČO v textech dokumentů
python main.py enrich-entities        # Doplnit info z ARES
python main.py deduplicate            # Označit duplicity
```

### Laravel webová aplikace

```bash
cd laravel

# Import dat z Python databáze do Laravelu
php artisan bosko:import

# Spuštění kolektorů přes Laravel
php artisan bosko:collect collect-all
php artisan bosko:collect collect-contracts

# Lokální vývoj (dva terminály)
php artisan serve --port=8000         # Terminál 1: PHP server
npm run dev                           # Terminál 2: Vite dev server (CSS/JS + HMR)

# Otevřít http://localhost:8000
```

### Produkční build

```bash
cd laravel
npm run build                         # Jednorázový build CSS/JS do public/build/
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
- **ARES**: `https://ares.gov.cz/ekonomicke-subjekty-v-be/rest/ekonomicke-subjekty/` (bez auth, 500 req/min)
  - GET `/{ico}` — detail firmy
  - POST `/vyhledat` — vyhledávání dle názvu (parametry: `obchodniJmeno`, `pocet`, `start`)

### Technologický stack

#### Sběr dat (Python)

- Python 3.14
- requests + BeautifulSoup4 + lxml
- pymupdf (PDF text)
- python-dotenv (env proměnné)
- SQLite (data/db/boskovice.db)
- CLI přes argparse v `main.py`

#### Webová aplikace (Laravel)

- PHP 8.3 + Laravel 12
- Eloquent ORM (6 modelů: Document, Attachment, Entity, Contract, Subsidy, EntityLink)
- Service Layer (9 služeb: Stats, Document, Contract, Entity, Subsidy, Search, Ares, Signal, Graph)
- Blade šablony + Tailwind CSS 4 + Alpine.js (Vite build)
- SQLite (laravel/database/database.sqlite)
- REST API (4 endpointy)
- Artisan příkazy:
  - `php artisan bosko:import` — import z legacy Python databáze
  - `php artisan bosko:collect {command}` — spouštění Python kolektorů

### Konvence kódu

#### Python

- Kolektory v `src/collectors/`, extraktory v `src/extractors/`
- Každý kolektor má metodu `collect() -> int`
- Repository pattern v `Database` třídě
- HTTP throttling v `Downloader` (1 req/s pro web města, 0.15s pro ARES)
- Doménové entity jako dataclasses v `src/models.py`

#### Laravel

- **SOLID principy**:
  - Single Responsibility: každý service má jednu zodpovědnost
  - Dependency Injection: controllery přijímají services přes constructor
  - Tenké controllery: žádná business logika, pouze HTTP concerns
- Eloquent modely s relacemi v `app/Models/`
- Services v `app/Services/` (7 servisních tříd)
- Controllers v `app/Http/Controllers/` (7 web + 2 API)
- Blade šablony v `resources/views/` s Tailwind CSS 4 + Alpine.js
- Migrační soubory v `database/migrations/`
- Artisan commands v `app/Console/Commands/`
