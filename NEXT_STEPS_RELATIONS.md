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

- 11 web routes (dashboard, dokumenty, smlouvy, dotace, subjekty, ARES, vyhledávání)
- 4 API endpointy (stats, entities, relations)
- Moderní design (Tailwind CSS 4 + Alpine.js)
- Service layer (7 services, SOLID architektura)

---

## 1. ~~Aktivovat sběr dat~~ ✅ HOTOVO

- [x] Zaregistrovat se na https://www.hlidacstatu.cz/api
- [x] Nastavit `HLIDAC_STATU_TOKEN` v `.env`
- [x] Data importována: 2 462 smluv, 443 dotací, 535 subjektů, 5 234 vazeb
- [x] Import do Laravelu: `php artisan bosko:import`
- [x] Ověřeno v UI i API

---

## 2. Rozšířit doménový model (nové typy entit)

Chybějící typy entit, které je třeba přidat do DB + Eloquent modelů:

| Entita | Popis | Zdroj dat |
|--------|-------|-----------|
| **Osoba (fyzická)** | Zastupitelé, radní, členové komisí, statutáři firem | Volby.cz, Justice.cz, zápisy ZM/RM |
| **Orgán města** | Zastupitelstvo, rada, komise, výbory | Web města, zápisy ZM/RM |
| **Projekt / akce** | Investiční akce, projekty města | Dokumenty, rozpočty, dotace |
| **Nemovitost** | Parcely, budovy dotčené projekty | RÚIAN/ČÚZK |
| **Událost** | Hlasování, schválení, zahájení stavby | Zápisy ZM/RM |

Nové typy vztahů:

| Vztah | Popis |
|-------|-------|
| osoba → firma | statutár, společník, jednatel |
| osoba → orgán města | zastupitel, radní, člen komise |
| projekt → smlouva / dotace | financován, realizován |
| projekt → nemovitost | realizován na parcele/adrese |
| firma/osoba → nemovitost | vlastník, nájemce |
| událost → subjekt / dokument | hlasoval, rozhodl, podal žádost |

Implementace:
- [ ] Přidat `entity_type` hodnoty: `person`, `city_body`, `project`, `property`, `event`
- [ ] Přidat `role` hodnoty do `entity_links`
- [ ] Přidat Eloquent scope/filtr pro nové typy
- [ ] Aktualizovat UI: detail entity zobrazí role specificky (statutár vs. dodavatel vs. zastupitel)

---

## 3. Integrace dalších registrů

### Justice.cz / obchodní rejstřík

- Cíl: navázat osoby (statutáři, vlastníci) na firmy
- Vztahy: osoba –[statutár]→ firma, osoba –[společník]→ firma
- [ ] Implementovat `JusticeCollector` (HTML scraping nebo API)
- [ ] Vytvořit entity typu `person` pro statutáry
- [ ] Propojit s existujícími firmami v `entities`

### Volby.cz + Evidence veřejných funkcionářů

- Cíl: přehled veřejných funkcí osob, které se objevují ve firmách či smlouvách
- Vztahy: osoba –[zastupitel/radní]→ orgán města
- [ ] Implementovat `VolbyCollector`
- [ ] Vytvořit entity typu `person` a `city_body`
- [ ] Propojit osoby s firmami (cross-reference IČO, jména)

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

### Sekvence „rozhodnutí → smlouva → dotace"

- Hledat časové řetězce: dokument města → smlouva s firmou → dotace
- Evidovat jako „case" s časovou osou a odkazy
- [ ] Implementovat detekci sekvencí
- [ ] Přidat „case view" s timeline vizualizací

### Možné střety zájmů

- Osoba je na straně města (zastupitel/radní) A zároveň má roli ve firmě s zakázkami
- Systém označí „možný střet zájmů – zkontroluj" s odkazy na zdroje
- [ ] Implementovat cross-referencing osob: veřejná funkce vs. role ve firmě
- [ ] Vizualizace na detailu osoby

---

## 5. UX a pokročilé zobrazení

### ~~Detail entity — vylepšení~~ ✅ HOTOVO

- [x] Zobrazení rolí u každé vazby (dodavatel, objednatel, příjemce, zmíněn...)
- [x] Agregované statistiky (celková/průměrná částka smluv, počet dotací)
- [x] Timeline: chronologická osa smluv a dotací entity
- [ ] Filtrování smluv/dokumentů/dotací dle období (TODO)

### Case view

- [ ] Vizualizace konkrétního signálu (časová osa, entity, částky, zdroje)
- [ ] Export jako PDF report nebo JSON snapshot
- [ ] Sdílitelný permalink

### ~~Grafová vizualizace~~ ✅ HOTOVO

- [x] D3.js force-directed graf vztahů (`/graph/{id}`)
- [x] Interaktivní: klik na uzel = detail entity, drag, zoom, pan
- [x] Velikost uzlů dle objemu smluv, barva dle typu entity, barva hran dle typu vztahu
- [x] API endpoint `GET /api/graph/{id}` s nodes + edges
- [ ] Filtrování dle typu vztahu, období, částky (TODO)

### Rozšíření API

- [x] `GET /api/signals` — seznam detekovaných signálů ✅
- [ ] `GET /api/entities/{id}/timeline` — časová osa entity (TODO)
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
