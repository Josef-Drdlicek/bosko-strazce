## Dlouhodobý cíl

Vytvořit otevřenou „mapu vztahů" veřejného prostoru města Boskovice, která:

- propojuje dokumenty, smlouvy, dotace a registry do jednoho grafu vztahů,
- umožňuje sledovat vazby mezi subjekty (osoby, firmy, orgány města, projekty, nemovitosti),
- identifikuje vzory a nesrovnalosti (koncentrace zakázek, střety zájmů, časové souvislosti),
- nikdy sama „neobviňuje" – pouze ukazuje data a vztahy, interpretaci nechává na člověku.

Boskovice slouží jako laboratoř, architektura musí být přenositelná na další města.

---

## Aktuální stav (co je hotové)

### ✅ Doménový model: základní entity a vztahy

Implementováno v DB (SQLite) + Eloquent modelech:

| Entita | Tabulka | Identifikátor | Zdroj |
|--------|---------|----------------|-------|
| Firma / organizace | `entities` | IČO | Hlídač státu, ARES, extrakce z fulltextu |
| Smlouva | `contracts` | `external_id` (Hlídač státu) | Hlídač státu API |
| Dotace | `subsidies` | `external_id` (Hlídač státu) | Hlídač státu API |
| Dokument | `documents` | `source_url` | Web města Boskovice |
| Příloha | `attachments` | `url` | Web města Boskovice |

Implementované vztahy (`entity_links`):

| Vztah | Role | Zdroj |
|-------|------|-------|
| subjekt → smlouva | `publisher`, `counterparty` | Hlídač státu |
| subjekt → dotace | `recipient` | Hlídač státu |
| subjekt → dokument | `mentioned` | Extrakce IČO z fulltextu |

### ✅ ARES integrace

- Python batch enrichment (`src/collectors/ares.py`)
- Laravel live lookup + cache (`app/Services/AresService.php`)
- Webová stránka `/ares` pro vyhledávání
- Automatické obohacení entit při zobrazení detailu

### ✅ Webové rozhraní a API

- 16 web routes (dashboard, dokumenty, smlouvy, dotace, subjekty, politici, signály, graf, ARES, vyhledávání)
- 4 API endpointy (stats, entities, relations, signals, graph)
- Moderní design (Tailwind CSS 4 + Alpine.js)
- Service layer (10 services, SOLID architektura)

---

## 1. ~~Aktivovat sběr dat~~ ✅ HOTOVO

- [x] Zaregistrovat se na https://www.hlidacstatu.cz/api
- [x] Nastavit `HLIDAC_STATU_TOKEN` v `.env`
- [x] Data importována: 2 462 smluv, 443 dotací, 535 subjektů, 5 234 vazeb
- [x] Import do Laravelu: `php artisan bosko:import`
- [x] Ověřeno v UI i API

---

## 2. ~~Rozšířit doménový model (nové typy entit)~~ ✅ ČÁSTEČNĚ HOTOVO

Implementované entity:

| Entita | Typ | Stav | Zdroj |
|--------|-----|------|-------|
| **Osoba (fyzická)** | `person` | ✅ 1 117 osob | ARES VR (statutáři), Volby.cz (zastupitelé) |
| **Orgán města** | `city_body` | ✅ 1 entita | Volby.cz (Zastupitelstvo města Boskovice) |
| **Projekt / akce** | `project` | ❌ TODO | Dokumenty, rozpočty, dotace |
| **Nemovitost** | `property` | ❌ TODO | RÚIAN/ČÚZK |
| **Událost** | `event` | ❌ TODO | Zápisy ZM/RM |

Implementované vztahy (`entity_links` s `linked_type='entity'`):

| Vztah | Role | Stav |
|-------|------|------|
| osoba → firma | `statutory`, `chairman`, `vice_chairman`, `supervisory_member` | ✅ 884 vazeb |
| osoba → orgán města | `council_member` | ✅ 51 vazeb |
| projekt → smlouva / dotace | `implementor`, `funded_by` | ❌ TODO |
| firma/osoba → nemovitost | `owner`, `tenant` | ❌ TODO |

- [x] Přidat `entity_type` hodnoty: `person`, `city_body`
- [x] Přidat `role` hodnoty do `entity_links`
- [x] Přidat Eloquent scope/filtr pro nové typy
- [x] Aktualizovat UI: detail entity zobrazí role specificky + propojené subjekty
- [ ] Přidat `entity_type` hodnoty: `project`, `property`, `event` (TODO)

---

## 3. ~~Integrace dalších registrů~~ ✅ ČÁSTEČNĚ HOTOVO

### ~~Justice.cz / obchodní rejstřík~~ ✅ HOTOVO (via ARES VR)

- Cíl: navázat osoby (statutáři, vlastníci) na firmy
- Vztahy: osoba –[statutár]→ firma, osoba –[společník]→ firma
- [x] Implementován `JusticeCollector` (`src/collectors/justice.py`) — data z ARES VR API
- [x] Vytvořeno 1 126 osob typu `person` jako aktuální statutáři firem
- [x] Propojeno s existujícími firmami v `entities` (884 vazeb statutory/chairman/vice_chairman/supervisory)
- [x] CLI příkaz: `python main.py collect-persons`

### ~~Volby.cz + Evidence veřejných funkcionářů~~ ✅ HOTOVO

- Cíl: přehled veřejných funkcí osob, které se objevují ve firmách či smlouvách
- Vztahy: osoba –[zastupitel]→ orgán města
- [x] Implementován `VolbyCollector` (`src/collectors/volby.py`) — scraping volby.cz
- [x] 27 zastupitelů × 3 volební období (2014, 2018, 2022) = 81 vazeb
- [x] Entita `Zastupitelstvo města Boskovice` typu `city_body`
- [x] CLI příkaz: `python main.py collect-volby`

### RÚIAN / ČÚZK

- Cíl: nemovitosti dotčené rozhodnutími města
- Vztahy: firma/osoba –[vlastní]→ nemovitost, projekt –[realizován_na]→ nemovitost
- [ ] Implementovat `RuianCollector` (RÚIAN API / ČÚZK Nahlížení do KN)
- [ ] Vytvořit entity typu `property`

---

## 4. „Signály" – heuristiky nesrovnalostí

> Signály nikdy nejsou „verdikt". Jsou to jen upozornění s vysvětlitelným výpočtem a trasovatelnými zdroji.

### ~~Koncentrace zakázek / dotací~~ ✅ HOTOVO

- [x] Implementován `SignalService` v Laravelu
- [x] `/signals` stránka s přehledem (koncentrace, nejvyšší smlouvy, dotace)
- [x] API endpoint `GET /api/signals`
- [x] Poměr k mediánu, závažnost (high/medium/low), odkaz na detail entity

### ~~Sekvence „rozhodnutí → smlouva → dotace"~~ ✅ ČÁSTEČNĚ HOTOVO

- [x] Detekce časových sekvencí smlouva–dotace (±1 rok) v `SignalService::detectTemporalSequences`
- [x] Zobrazení na stránce `/signals` s prolinky na detail smlouvy i dotace
- [x] Severity: high (5M+), medium (1M+), low
- [ ] Přidat „case view" s timeline vizualizací (TODO)
- [ ] Rozšířit o detekci sekvencí zahrnující dokumenty města (TODO)

### ~~Možné střety zájmů~~ ✅ HOTOVO

- [x] Cross-referencing zastupitelů × statutářů firem se zakázkami města
- [x] Detekce na základě case-insensitive name matching (osoby z volby.cz vs ARES VR)
- [x] 16 detekovaných možných střetů zájmů
- [x] Zobrazení na stránce `/signals` s odkazem na osobu i firmu
- [x] Závažnost: high = firma má smlouvy s městem, low = bez smluv
- [x] Propojené subjekty zobrazeny na detailu entity

---

## 5. UX a pokročilé zobrazení

### ~~Detail entity — vylepšení~~ ✅ HOTOVO

- [x] Zobrazení rolí u každé vazby (dodavatel, objednatel, příjemce, zmíněn...)
- [x] Agregované statistiky (celková/průměrná částka smluv, počet dotací)
- [x] Timeline: chronologická osa smluv a dotací entity
- [x] Filtrování smluv/dotací/timeline dle období (Alpine.js date range picker)

### ~~Zastupitelé (Politici)~~ ✅ HOTOVO

- [x] Samostatná stránka `/politicians` — card grid s filtry (všichni / s vazbou / bez vazby)
- [x] Detail zastupitele `/politicians/{id}` — profil, volební historie, firmy, top smlouvy
- [x] `PoliticianService` — přehled zastupitelů, vazby na firmy, volební data
- [x] `PoliticianController` s route model binding na `Entity`
- [x] Navigační položka „Politici" v hlavním menu
- [x] Redesign střetů zájmů na `/signals` — vizuální karty seskupené dle osoby s prolinky na profil

### Case view

- [ ] Vizualizace konkrétního signálu (časová osa, entity, částky, zdroje)
- [ ] Export jako PDF report nebo JSON snapshot
- [ ] Sdílitelný permalink

### ~~Grafová vizualizace~~ ✅ HOTOVO

- [x] D3.js force-directed graf vztahů (`/graph/{id}`)
- [x] Interaktivní: klik na uzel = detail entity, drag, zoom, pan
- [x] Velikost uzlů dle objemu smluv, barva dle typu entity, barva hran dle typu vztahu
- [x] API endpoint `GET /api/graph/{id}` s nodes + edges
- [x] Filtrování dle typu vztahu (Alpine.js checkboxy: smlouvy/dokumenty/dotace)
- [ ] Filtrování dle období a částky (TODO)

### ~~Rozšíření API~~ ✅ HOTOVO

- [x] `GET /api/signals` — seznam detekovaných signálů ✅
- [x] `GET /api/entities/{id}/timeline` — časová osa entity ✅
- [x] `GET /api/graph/{id}` — graf vztahů kolem entity (pro vizualizéry) ✅

---

## 6. Multi-city strategie

- [ ] Oddělit konfiguraci města (IČO, doména, URL patterny, specifické collectory)
- [ ] Přidat `city_id` / `city_code` ke všem tabulkám
- [ ] Web UI: přepínání města + globální pohled přes více měst
- [ ] PostgreSQL místo SQLite (pro produkční nasazení)
- [ ] Laravel multi-tenancy pattern

---

## 7. Produkce a bezpečnost

- [ ] Autentizace a autorizace (Laravel Sanctum)
- [ ] Rate limiting na API
- [ ] Nasazení (Laravel Forge / Docker)
- [ ] Monitoring a alerting (Sentry, health checks)
- [ ] Automatický scheduling sběru dat (Laravel Scheduler + Cron)
- [ ] Tesseract OCR pro skenované PDF
- [ ] NER (Named Entity Recognition) pro extrakci jmen osob z fulltextu

---

## Praktický postup pro další agenty (shrnutí)

1. Držet krok se `STAV_PROJEKTU.md` – tam je technický stav a co už běží.
2. **Nejdříve aktivovat data** (sekce 1) – bez smluv a dotací nelze pracovat na vztazích.
3. Rozšiřovat ontologii (sekce 2) a přidávat registry (sekce 3) po vrstvách.
4. Implementovat signály (sekce 4) — vždy vysvětlitelně, s odkazy na zdroje.
5. Všechny nové vztahy a signály musí být:
   - dohledatelné ke zdrojovým dokumentům,
   - pochopitelné pro člověka (žádné „černé skříňky"),
   - znovu vypočitatelné (deterministické).
6. Nové services přidávat do `app/Services/`, nové collectory do `src/collectors/`.
7. Dodržovat SOLID principy — tenké controllery, DI, single responsibility.
