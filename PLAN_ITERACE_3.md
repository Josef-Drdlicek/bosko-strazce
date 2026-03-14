# Bosko Strážce — Plán iterace 3

> Cíl: Proměnit technicky funkční platformu v profesionální, srozumitelný nástroj,
> který každý návštěvník okamžitě pochopí — od babičky po investigativního novináře.

---

## Analýza aktuálního stavu

### Co funguje dobře

- **Datová vrstva** je silná: 1 039 dokumentů, 2 462 smluv, 443 dotací, 1 653 entit, 6 404 vazeb.
- **Architektura** je čistá: 10 servisních tříd, SOLID principy, tenké controllery.
- **Vizuál** má solidní základ: Tailwind 4, indigo/slate paleta, Inter font, responzivní layout.
- **Signály** detekují reálné anomálie: koncentrace zakázek, střety zájmů, časové sekvence.

### Co chybí

| Problém | Dopad |
|---------|-------|
| Popisy sekcí jsou strohé a technické | Běžný občan neví, co má hledat a proč |
| Chybí „průvodce" — co se z dat dozvím? | Uživatel se ztratí v číslech bez kontextu |
| Vizuál je funkční, ale ne prémiový | Chybí wow-efekt, který budí důvěru a zájem médií |
| Prázdné stavy a chybové stavy nejsou řešeny | Aplikace vypadá „rozbitě" když nemá data |
| Signály nemají plain-language vysvětlení | Čtenář nerozumí, co „koncentrace zakázek 12.4× mediánu" znamená |
| Chybí Case view pro detailní průzkum signálu | Není způsob, jak signál prozkoumat do hloubky |
| Graf nemá filtrování dle období a částky | Omezená analytická hodnota |
| Entity typu `project` nejsou implementovány | Nelze sledovat konkrétní městské projekty |

---

## Fáze 1 — Srozumitelnost pro každého (UX copywriting + onboarding)

> Princip: Každá stránka musí odpovědět na tři otázky:
> 1. **Co tady najdu?** — jasný nadpis a popis
> 2. **Proč je to důležité?** — kontext, co to znamená pro občana
> 3. **Co mám udělat dál?** — jasné výzvy k akci (CTA)

### 1.1 — Přepsat všechny popisy sekcí (hero texty)

Každá stránka dostane dvouřádkový popis srozumitelný pro kohokoliv:

| Stránka | Současný popis | Nový popis |
|---------|---------------|------------|
| Dashboard | „Antikorupční monitorovací platforma…" | **„Bosko Strážce sleduje veřejná data města Boskovice — smlouvy, dotace, dokumenty a firmy — a hledá v nich zajímavé souvislosti. Všechna data pocházejí z veřejných zdrojů."** |
| Dokumenty | „Úřední dokumenty města Boskovice" | **„Všechny úřední dokumenty, které město zveřejňuje — od zápisů ze zastupitelstva po vyhlášky a rozpočty. Můžete v nich hledat fulltextem."** |
| Smlouvy | „Registr smluv města Boskovice" | **„Každá smlouva, kterou město Boskovice uzavřelo a která je evidována v celostátním Registru smluv. Vidíte s kým, za kolik a kdy."** |
| Dotace | „Přehled dotací spojených s městem Boskovice" | **„Peníze, které město Boskovice dostalo nebo rozdalo — z ministerstev, EU fondů i vlastních programů. U každé dotace vidíte částku, poskytovatele i příjemce."** |
| Subjekty | „Firmy, organizace a osoby propojené s městem" | **„Všechny firmy, organizace a osoby, které se ve veřejných datech města objevují — jako dodavatelé, příjemci dotací nebo účastníci rozhodování."** |
| Politici | „Přehled všech zvolených zastupitelů…" | **„Kdo rozhoduje o městě? Zastupitelé zvolení v komunálních volbách a jejich případné vazby na firmy, které s městem obchodují."** |
| Signály | „Automaticky detekované vzory…" | **„Počítač prošel tisíce dokumentů a smluv a našel věci, které stojí za pozornost — neobvykle velké zakázky, opakované dodavatele nebo zastupitele ve vedení firem. Nejde o obvinění, ale o vodítka pro bližší zkoumání."** |
| Graf | (žádný popis) | **„Vizuální mapa propojení — kdo s kým obchoduje, kdo kde sedí ve vedení, kdo pobírá dotace. Klikněte na libovolný bod pro detail."** |
| ARES | „Administrativní registr ekonomických subjektů…" | **„Vyhledejte jakoukoliv firmu nebo organizaci v oficiálním státním registru. Dozvíte se adresu, vedení, obor činnosti a další údaje."** |
| Vyhledávání | „Hledejte napříč dokumenty…" | **„Zadejte cokoliv — jméno firmy, číslo smlouvy, klíčové slovo — a prohledáme všechna data najednou."** |

### 1.2 — „Co se z toho dozvím?" boxy na každé stránce

Pod hero sekci každé stránky přidat řadu karet „Co zde najdete":

**Dashboard:**
- 🔍 „Kolik smluv město uzavřelo a za kolik peněz celkem"
- 📊 „Kdo jsou největší dodavatelé města"
- 📋 „Nejnovější dokumenty a smlouvy"

**Smlouvy:**
- 💰 „Kolik město platí jednotlivým firmám"
- 📅 „Kdy byly smlouvy uzavřeny"
- 🔗 „S jakými firmami a organizacemi město spolupracuje"

**Politici:**
- 👥 „Kteří zastupitelé jsou ve vedení firem"
- 🏢 „Které firmy zastupitelů mají zakázky od města"
- 📈 „Kolik peněz prochází přes firmy propojené se zastupiteli"

**Signály:**
- ⚠️ „Firmy, které dostávají neúměrně velké zakázky"
- 🔄 „Podezřelé časové návaznosti smluv a dotací"
- 🤝 „Zastupitelé, kteří sedí ve vedení firem dodávajících městu"

### 1.3 — Plain-language vysvětlení signálů

Ke každému signálu přidat lidsky čitelnou větu:

| Signál | Technický popis | Nový lidský popis |
|--------|----------------|-------------------|
| Koncentrace zakázek | „12.4× mediánu" | **„Tato firma dostala od města 12× více peněz než je běžné. To samo o sobě není špatné — možná je v oboru nejlepší — ale stojí za to se podívat proč."** |
| Střet zájmů | „high severity" | **„Tento zastupitel je zároveň ve vedení firmy, která má zakázky od města. To může být v pořádku, ale veřejnost by o tom měla vědět."** |
| Časová sekvence | „±1 rok, combined 5M+" | **„Tato firma získala smlouvu a krátce nato i dotaci, dohromady za více než 5 milionů. Časová blízkost může být náhoda, ale i záměr."** |
| Vysoká smlouva | „top by amount" | **„Jedna z nejdražších smluv, které město uzavřelo. U takto velkých částek se vyplatí zkontrolovat, zda proběhla soutěž."** |

### 1.4 — Kontextové nápovědy (tooltips a info boxy)

- **U každé částky**: tooltip „Z Registru smluv, ověřte na hlidacstatu.cz"
- **U IČO**: tooltip „Identifikační číslo organizace, klikněte pro ARES detail"
- **U rolí** (dodavatel, objednatel, příjemce): krátké vysvětlení v tooltipech
- **U severity badges**: tooltip s vysvětlením jak se počítá závažnost
- **Info box na dashboardu**: „Odkud data pocházejí" — přehled zdrojů s logy (Registr smluv, ARES, Volby.cz, web města)

### 1.5 — Empty states a onboarding

- Každá stránka s prázdným výsledkem: ilustrace + vysvětlení + CTA
- Dashboard: pokud nejsou data, zobrazit průvodce importem
- Vyhledávání bez výsledků: návrhy co zkusit

---

## Fáze 2 — Profesionální vizuální design

> Princip: Vizuál musí vzbuzovat důvěru na úrovni profesionální investigativní platformy
> (reference: Hlídač státu, ICIJ Offshore Leaks, Follow the Money).

### 2.1 — Landing page / Dashboard redesign

**Hero sekce:**
- Výrazný gradient s profesionální typografií
- Animovaný counter: „Sledujeme X smluv za Y Kč" (CountUp.js nebo Alpine)
- Tři KPI karty s ikonami: Smlouvy / Dotace / Signály
- CTA tlačítka: „Prohlédnout signály" + „Hledat firmu"

**Sekce „Co monitorujeme":**
- 4 karty s ikonami v gridu (Dokumenty, Smlouvy, Dotace, Subjekty)
- Každá s počtem položek a jednovětým popisem
- Hover efekt: zvětšení + stín

**Sekce „Nejzajímavější zjištění":**
- Top 3 signály s lidským popisem (viz fáze 1.3)
- Vizuální karty s gradient border a severity ikonou
- Link na `/signals` pro více

**Sekce „Zdroje dat":**
- Řada log/ikon: Registr smluv, ARES, Volby.cz, Web města, CEDR
- Pod každým: jednořádkový popis co daný zdroj poskytuje

### 2.2 — Design system upgrade

**Typografie:**
- H1: `text-3xl sm:text-4xl font-extrabold tracking-tight`
- H2: `text-2xl font-bold`
- H3: `text-lg font-semibold`
- Body: `text-base text-slate-600 leading-relaxed`
- Captions: `text-sm text-slate-500`

**Karty — tři úrovně:**
1. **Feature card** (dashboard): velká, s ikonou, gradient border-left, hover elevace
2. **Data card** (seznamy): kompaktní, s metadaty v řádku, subtle border
3. **Alert card** (signály): barevný left-border dle severity, ikona varování

**Barevné kódování (konzistentní napříč celou aplikací):**
- Smlouvy = `emerald-600` (zelená = peníze ven)
- Dotace = `amber-500` (žlutá = peníze dovnitř)
- Dokumenty = `sky-500` (modrá = informace)
- Subjekty = `violet-600` (fialová = aktéři)
- Signály high = `rose-600` (červená = pozor)
- Signály medium = `amber-500`
- Signály low = `slate-400`

**Nové komponenty Blade:**
- `<x-info-box>` — kontextová nápověda (ikona + text, varianta: info/warning/tip)
- `<x-feature-card>` — velká karta s ikonou a popisem
- `<x-signal-card>` — karta signálu s severity, popisem a prolinky
- `<x-amount-badge>` — formátovaná částka s měnou a barevným kódováním
- `<x-source-badge>` — odkaz na zdroj dat (Registr smluv, ARES apod.)
- `<x-empty-state>` — prázdný stav s ilustrací a CTA
- `<x-tooltip>` — kontextová nápověda při hoveru
- `<x-kpi-card>` — KPI karta s číslem, labelem, trendem a ikonou

### 2.3 — Vylepšení existujících stránek

**Dokumenty (`/documents`):**
- Lepší section badge s ikonou (ne jen barva)
- Preview prvních 200 znaků fulltextu přímo v listu
- Počet příloh jako badge

**Smlouvy (`/contracts`):**
- Vizuální částková lišta (bar) ukazující relativní velikost smlouvy
- Barevné označení: „velká" (nad 1M), „střední" (100k–1M), „malá" (pod 100k)
- Quick-view: kliknutí na řádek rozbalí detail (Alpine.js)

**Dotace (`/subsidies`):**
- Rok jako vizuální timeline na boku
- Seskupení dle poskytovatele (accordion)

**Subjekty (`/entities`):**
- Avatar/ikona dle typu (firma=budova, osoba=silueta, orgán=radnice)
- Mini-stats v kartě: počet smluv, celková částka, počet dotací
- „Důležitost" indikátor (na základě počtu vazeb)

**Politici (`/politicians`):**
- Foto placeholder s iniciálami (již existuje) → lépe vizuálně
- Barevné kódování dle strany (CSS třídy pro známé strany)
- Timeline volebních období jako vizuální pruh
- „Má vazby na firmy" badge prominentněji

**Signály (`/signals`):**
- Kompletní redesign: každý signál jako storytelling karta
- „Co to znamená?" expandable sekce pod každým signálem
- Vizuální souhrnné statistiky nahoře: „X signálů, z toho Y vysoké závažnosti"
- Filtrování dle typu a severity

**Detail entity (`/entities/{id}`):**
- Hero karta s KPI (celkový objem, počet smluv, počet dotací, počet vazeb)
- Tab layout: Smlouvy | Dotace | Dokumenty | Vztahy | Timeline
- Timeline jako vizuální osa (ne jen tabulka)
- „Odkud data pocházejí" footer s logy zdrojů

**Detail zastupitele (`/politicians/{id}`):**
- Vylepšený hero s gradient dle strany
- Sekce „Co o tomto zastupiteli víme" — shrnutí v 2–3 větách
- Firmy zobrazené jako propojené karty s mini-grafem

**Graf (`/graph/{id}`):**
- Legenda s vysvětlením barev a velikostí
- Minimap pro orientaci ve velkém grafu
- Filtrování dle období a minimální částky (TODO z NEXT_STEPS)
- Panel s detailem vybraného uzlu (sidebar)

### 2.4 — Animace a mikrointerakce

- Page transitions: fade-in obsahu při načtení
- Karty: subtle hover scale + shadow transition
- Countery na dashboardu: animovaný count-up
- Grafy: smooth enter/exit animace uzlů
- Skeleton loading states místo prázdné stránky
- Scroll-triggered fade-in pro sekce pod foldem

### 2.5 — Footer redesign

- Tři sloupce: Navigace | O projektu | Zdroje dat
- „O projektu": 2–3 věty o účelu platformy
- „Zdroje dat": seznam s logy a linky na původní zdroje
- Disclaimer: „Data z veřejných zdrojů. Platforma neobviňuje — pouze zobrazuje veřejně dostupné informace."
- Verze + datum poslední aktualizace dat

---

## Fáze 3 — Guided Exploration (Průvodce daty)

> Princip: Uživatel nikdy nesmí tápat „a co teď?".
> Každý prvek musí nabízet další logický krok.

### 3.1 — Kontextové CTA na každé stránce

| Stránka | CTA |
|---------|-----|
| Dashboard | „Podívejte se na signály →" / „Kdo jsou největší dodavatelé? →" |
| Smlouvy (seznam) | „Která firma dostává nejvíce? → Subjekty" |
| Detail smlouvy | „Zobrazit všechny smlouvy tohoto dodavatele →" / „Zobrazit graf vztahů →" |
| Detail entity | „Zobrazit graf vztahů →" / „Má tato firma signály? →" |
| Detail politika | „Zobrazit firmy tohoto zastupitele →" / „Zobrazit v grafu →" |
| Signály | „Prozkoumat tento signál →" (→ case view) |

### 3.2 — „Průvodce pro nováčky" (onboarding banner)

Na dashboardu pro prvního návštěvníka (cookie-based):
1. „Vítejte v Bosko Strážce"
2. „Začněte zde: podívejte se na signály — věci, které stojí za pozornost"
3. „Nebo vyhledejte konkrétní firmu či osobu"
4. „Chcete porozumět vztahům? Zkuste graf propojení"
5. Dismiss tlačítko (uložení do localStorage)

### 3.3 — Breadcrumbs a navigační kontext

- Breadcrumby na každé podstránce: `Přehled > Subjekty > Firma XYZ`
- „Zpět na seznam" odkaz na detail stránkách
- Aktuální pozice zvýrazněna v navigaci

### 3.4 — Prolinky mezi sekcemi (cross-linking)

- Na detailu smlouvy: odkaz na dodavatele, objednatele, graf
- Na detailu entity: odkazy na smlouvy, dotace, dokumenty, signály, graf
- Na detailu politika: odkazy na firmy, smlouvy, graf
- Na signálech: odkazy na entity, smlouvy, dotace, politiky

---

## Fáze 4 — Chybějící funkce z NEXT_STEPS

### 4.1 — Case view (vizualizace signálu)

Nová stránka `/signals/{id}` nebo `/cases/{id}`:
- Timeline vizualizace konkrétního signálu
- Všechny zúčastněné entity na jednom místě
- Chronologický přehled: co se kdy stalo
- Lidský popis: „Co tato situace znamená"
- Zdroje: odkaz na každý originální dokument/smlouvu
- (Budoucí: export PDF, sdílitelný permalink)

### 4.2 — Graf: filtrování dle období a částky

- Date range picker (Alpine.js) pro filtrování hran
- Slider pro minimální částku
- Dynamické přepočítání velikosti uzlů

### 4.3 — Entity typu `project`

- Nový `entity_type = 'project'`
- Extrakce projektů z názvů smluv a dotací (regex patterns)
- Propojení: projekt ↔ smlouvy, projekt ↔ dotace
- Stránka `/projects` s přehledem městských projektů
- Detail projektu: celkové náklady, zdroje financování, dodavatelé, timeline

### 4.4 — Temporální sekvence s dokumenty

- Rozšířit `SignalService::detectTemporalSequences` o dokumenty města
- Detekce: usnesení ZM/RM → smlouva → dotace (časová řada)

---

## Fáze 5 — Technické vylepšení

### 5.1 — Performance

- Eager loading relací (eliminace N+1 queries)
- Cache pro dashboard statistiky a signály (5 min TTL)
- Lazy loading obrázků a komponent pod foldem
- Skeleton loading states

### 5.2 — SEO a meta

- Unikátní `<title>` a `<meta description>` pro každou stránku
- Open Graph meta tagy pro sdílení na sociálních sítích
- Strukturovaná data (JSON-LD): Organization, WebSite, BreadcrumbList
- Sitemap XML
- Canonical URLs

### 5.3 — Accessibility

- Správné ARIA labels na všech interaktivních prvcích
- Keyboard navigace v grafu
- Dostatečný kontrast (WCAG AA)
- Skip to content link
- Focus visible styles

---

## Pořadí implementace

| # | Co | Odhad | Priorita | Závislosti |
|---|-----|-------|----------|------------|
| 1 | Nové Blade komponenty (info-box, feature-card, signal-card, amount-badge, empty-state, kpi-card, tooltip, source-badge) | M | P0 | — |
| 2 | Přepis hero textů a popisů všech sekcí | S | P0 | — |
| 3 | „Co se dozvím" boxy na každé stránce | S | P0 | #1 |
| 4 | Plain-language vysvětlení signálů | S | P0 | #1 |
| 5 | Dashboard redesign (hero, KPI, zjištění, zdroje dat) | L | P0 | #1, #2 |
| 6 | Kontextové CTA a cross-linking mezi sekcemi | M | P0 | #2 |
| 7 | Breadcrumbs komponenta | S | P1 | — |
| 8 | Footer redesign | S | P1 | — |
| 9 | Signály page redesign (storytelling karty, filtry) | L | P1 | #1, #4 |
| 10 | Detail entity redesign (tabs, KPI hero, timeline vizuální) | L | P1 | #1 |
| 11 | Detail politika vylepšení (shrnutí, strana barvy) | M | P1 | #1 |
| 12 | Politici page vylepšení (strana barvy, prominence vazeb) | M | P1 | — |
| 13 | Smlouvy vylepšení (částková lišta, quick-view) | M | P1 | — |
| 14 | Subjekty vylepšení (ikony dle typu, mini-stats) | M | P1 | — |
| 15 | Kontextové tooltips (částky, IČO, role, severity) | M | P2 | #1 |
| 16 | Empty states pro všechny stránky | S | P2 | #1 |
| 17 | Animace a mikrointerakce (hover, fade-in, count-up) | M | P2 | — |
| 18 | Graf: legenda, filtrování dle období/částky, sidebar | L | P2 | — |
| 19 | Onboarding banner pro nováčky | S | P2 | — |
| 20 | Case view (`/signals/{id}` nebo `/cases/{id}`) | L | P2 | #9 |
| 21 | Entity typu `project` + stránka `/projects` | L | P3 | — |
| 22 | Temporální sekvence s dokumenty | M | P3 | — |
| 23 | SEO: meta tagy, OG, JSON-LD, sitemap | M | P3 | — |
| 24 | Accessibility audit + opravy | M | P3 | — |
| 25 | Performance: cache, eager loading, skeletons | M | P3 | — |

**Velikosti:** S = pár hodin, M = den, L = 2–3 dny

**Priority:** P0 = tato iterace nutně, P1 = tato iterace ideálně, P2 = pokud zbyde čas, P3 = další iterace

---

## Definice „hotovo" pro iteraci 3

- [ ] Každá stránka má srozumitelný popis, který pochopí kdokoliv
- [ ] Každá stránka říká „co se z toho dozvím"
- [ ] Signály mají lidsky čitelná vysvětlení
- [ ] Dashboard vypadá jako profesionální platforma (ne jako admin panel)
- [ ] Existuje jasná navigační cesta: Dashboard → Signály → Detail → Zdroje
- [ ] Cross-linky mezi sekcemi fungují konzistentně
- [ ] Nové Blade komponenty zajišťují vizuální konzistenci
- [ ] Footer obsahuje informace o zdrojích a disclaimery
- [ ] Prázdné stavy mají smysluplný obsah
