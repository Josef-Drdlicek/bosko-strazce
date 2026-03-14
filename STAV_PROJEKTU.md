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

### Sběr externích dat (připraveno)

Implementovány kolektory pro:
- **Hlídač státu API** -- smlouvy z Registru smluv (Boskovice IČO 00279978)
- **Hlídač státu API** -- dotace z CEDR/IsRed/Eufondy
- **ARES REST API** -- detaily firem dle IČO (adresa, právní forma, datum vzniku)
- **Entity extractor** -- regex extrakce IČO z fulltextu dokumentů

> Ke spuštění sběru smluv a dotací je potřeba nastavit `HLIDAC_STATU_TOKEN` v `.env`.

### Integrace ARES (nové)

- **Laravel AresService** — nativní HTTP klient pro ARES REST API
  - Vyhledávání firem podle názvu (POST `/vyhledat`)
  - Lookup podle IČO (GET `/{ico}`)
  - 24h cache výsledků
  - Automatické obohacení entit při zobrazení detailu
- **Webová stránka `/ares`** — interaktivní vyhledávání v registru ARES
- **Detail subjektu** — automaticky zobrazuje data z ARES (sídlo, právní forma, datum vzniku, CZ-NACE)

### Webové rozhraní (Laravel) — nový design

Laravel 12 + Blade + Tailwind CSS 4 + Alpine.js:
- `/` -- Dashboard (statistiky, poslední dokumenty, smlouvy, top dodavatelé)
- `/documents` -- Procházení dokumentů (filtry dle sekce, fulltext, paginace)
- `/documents/{id}` -- Detail dokumentu s přílohami a propojenými subjekty
- `/contracts` -- Procházení smluv (částka, datum, protistrana, paginace)
- `/contracts/{id}` -- Detail smlouvy
- `/subsidies` -- Procházení dotací (filtry dle roku, vyhledávání, paginace)
- `/subsidies/{id}` -- Detail dotace
- `/entities` -- Procházení subjektů (filtry dle typu, vyhledávání)
- `/entities/{id}` -- Detail subjektu se všemi propojeními + ARES data
- `/ares` -- Vyhledávání v registru ARES (podle názvu nebo IČO)
- `/search` -- Globální vyhledávání přes dokumenty, smlouvy, subjekty i dotace

### REST API

- `/api/stats` -- Celkové statistiky (počty, součty částek, sekce)
- `/api/entities` -- Seznam subjektů (filtry, paginace)
- `/api/entities/{id}` -- Detail subjektu
- `/api/entities/{id}/relations` -- Všechny vztahy subjektu s napojenými záznamy

---

## Architektura (SOLID refactoring)

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
| `StatsApiController` | `/api/stats` |
| `EntityApiController` | `/api/entities/*` |

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
│   └── extractors/                # Extraktory textu a entit
│
├── laravel/                       # Laravel webová aplikace (hlavní stack)
│   ├── app/
│   │   ├── Models/                # Eloquent modely (6 modelů)
│   │   ├── Services/              # Business logika (7 services)
│   │   ├── Http/Controllers/      # Web controllery (7) + API (2)
│   │   └── Console/Commands/      # Artisan příkazy (import, collect)
│   ├── database/
│   │   ├── migrations/            # Migrace (6 tabulek)
│   │   └── database.sqlite        # Laravel SQLite databáze
│   ├── resources/
│   │   ├── views/                 # Blade šablony + Tailwind CSS
│   │   ├── css/app.css            # Tailwind 4 konfigurace
│   │   └── js/app.js              # Alpine.js inicializace
│   ├── routes/
│   │   ├── web.php                # Webové routes
│   │   └── api.php                # API routes
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
npm install && npm run build
```

### Nastavení API klíčů

```bash
cp .env.example .env
# Editovat .env a nastavit HLIDAC_STATU_TOKEN
```

### Sběr dat (Python)

```bash
# Sběr dat z webu města
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
```

### Laravel webová aplikace

```bash
cd laravel

# Import dat z Python databáze do Laravelu
php artisan bosko:import

# Spuštění kolektorů přes Laravel
php artisan bosko:collect collect-all
php artisan bosko:collect collect-contracts

# Webové rozhraní
php artisan serve --port=8000         # http://localhost:8000
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
  - GET `/{ico}` — detail firmy
  - POST `/vyhledat` — vyhledávání dle názvu

### Technologický stack

#### Sběr dat (Python — stále aktivní)

- Python 3.14
- requests + BeautifulSoup4 + lxml
- pymupdf (PDF text)
- python-dotenv (env proměnné)
- SQLite (data/db/boskovice.db)
- CLI přes argparse v `main.py`

#### Webová aplikace (Laravel — hlavní stack)

- PHP 8.3 + Laravel 12
- Eloquent ORM (6 modelů: Document, Attachment, Entity, Contract, Subsidy, EntityLink)
- Service Layer (7 služeb: Stats, Document, Contract, Entity, Subsidy, Search, Ares)
- Blade šablony + Tailwind CSS 4 + Alpine.js (Vite build)
- SQLite (laravel/database/database.sqlite)
- REST API (`/api/stats`, `/api/entities`, `/api/entities/{id}/relations`)
- Artisan příkazy:
  - `php artisan bosko:import` — import z legacy Python databáze
  - `php artisan bosko:collect {command}` — spouštění Python kolektorů

### Konvence kódu

#### Python (sběr dat)

- Čistá architektura: kolektory v `src/collectors/`, extraktory v `src/extractors/`
- Každý kolektor má metodu `collect() -> int`
- Repository pattern v `Database` třídě
- HTTP throttling v `Downloader` (1 req/s pro web města, 0.15s pro ARES)
- Doménové entity jako dataclasses v `src/models.py`

#### Laravel (web + API)

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

---

## Doporučené další kroky

### Priorita 1: Aktivovat externí zdroje

1. Zaregistrovat se na https://www.hlidacstatu.cz/api
2. Nastavit token v `.env`
3. Spustit `php artisan bosko:collect collect-contracts` a `php artisan bosko:collect collect-subsidies`
4. Spustit `php artisan bosko:import` pro synchronizaci dat do Laravelu

### Priorita 2: Rozšířit doménový model (NEXT_STEPS_RELATIONS.md)

- Přidat Eloquent modely: Person, CityDepartment, Project, Property, Event
- Přidat typy vztahů: statutár firmy, vlastník nemovitosti, zastupitel, člen komise
- Integrace Justice.cz, Volby.cz, ČÚZK
- Implementovat signály (koncentrace zakázek, střety zájmů, časové sekvence)

### Priorita 3: Zlepšit pokrytí fulltextu

- Integrace Tesseract OCR pro skenované PDF
- Rozšířit entity extrakci o jména osob (NER)

### Priorita 4: Pokročilá analýza a UX

- Timeline view: chronologické zobrazení rozhodnutí a smluv
- Case view: vizualizace konkrétního signálu
- Grafová vizualizace vztahů (např. D3.js, Sigma.js)
- Export reportů (PDF, JSON)

### Priorita 5: Multi-city a produkce

- Abstrakce konfigurace pro jiná města (IČO, URL patterny)
- PostgreSQL místo SQLite
- Laravel multi-tenancy
- Autentizace a autorizace (Laravel Sanctum)
- Nasazení (Laravel Forge / Docker)
