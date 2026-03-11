## Dlouhodobý cíl

Vytvořit otevřenou „mapu vztahů“ veřejného prostoru města Boskovice, která:

- propojuje dokumenty, smlouvy, dotace a registry do jednoho grafu vztahů,
- umožňuje sledovat vazby mezi subjekty (osoby, firmy, orgány města, projekty, nemovitosti),
- identifikuje vzory a nesrovnalosti (koncentrace zakázek, střety zájmů, časové souvislosti),
- nikdy sama „neobviňuje“ – pouze ukazuje data a vztahy, interpretaci nechává na člověku.

Boskovice slouží jako laboratoř, architektura musí být přenositelná na další města.

---

## 1. Doménový model: entity a vztahy

- Definovat základní typy **entit**:
  - osoba (fyzická),
  - firma / organizace,
  - orgán města (zastupitelstvo, rada, komise, výbory),
  - projekt / akce,
  - smlouva,
  - dotace,
  - nemovitost (adresní bod / parcela),
  - dokument (už existuje),
  - událost (např. schválení, hlasování, zahájení stavby).
- U každého typu jasně popsat:
  - jaký má primární identifikátor (IČO, ID smlouvy, ID dotace, URL, kombinace data a názvu),
  - z jakých zdrojů se plní (Boskovice web, Hlídač státu, ARES, ČÚZK, Volby.cz, atd.).
- Definovat typy **vztahů (relations)**:
  - osoba ↔ firma: člen orgánu, vlastník, jednatel,
  - subjekt ↔ smlouva: objednatel, dodavatel,
  - subjekt ↔ dotace: příjemce, poskytovatel,
  - subjekt ↔ dokument: zmíněn, autor, adresát,
  - subjekt ↔ nemovitost: vlastník, nájemce,
  - projekt ↔ smlouva / dotace / dokument: financován, schválen, realizován,
  - událost ↔ subjekt / dokument: hlasoval, rozhodl, podal žádost.
- Vytvořit krátký technický popis (ontologii) v podobě:
  - seznam typů entit,
  - seznam typů vztahů,
  - povinné atributy (čas, zdroj, role, identifikátory).

> Cíl: jeden zdroj pravdy pro další agenty, jaké entity a vztahy mají vytvářet.

---

## 2. Naplnění grafu z existujících dat

- **Smlouvy z Registru smluv (Hlídač státu)**:
  - pro každou smlouvu vytvořit entity pro objednatele (město Boskovice) a dodavatele (firma),
  - vytvořit vztahy:
    - město Boskovice –[objednatel]→ smlouva,
    - firma –[dodavatel]→ smlouva.
- **Dotace (Hlídač státu – Dotace/Hledat)**:
  - pro každou dotaci vytvořit entity pro příjemce a poskytovatele,
  - vytvořit vztahy:
    - poskytovatel –[poskytovatel_dotace]→ dotace,
    - příjemce –[příjemce_dotace]→ dotace.
- **Dokumenty města Boskovice**:
  - z fulltextu a metadat doplnit vztahy:
    - subjekt –[zmíněn_v_dokumentu]→ dokument (z IČO, názvů firem, jmen osob),
    - dokument –[souvisí_se_smlouvou/dotací]→ smlouva / dotace (pozdější krok, přes ID, spisovou značku nebo název).
- Zajistit, že každý vztah má:
  - odkaz na **zdrojový dokument** / API odpověď,
  - pokud možno **krátký úryvek textu** nebo ID přílohy jako důkaz.

---

## 3. Integrace dalších registrů (po vrstvách)

Postupné rozšíření grafu o další otevřené registry:

- **Justice.cz / obchodní rejstřík**:
  - cíl: navázat osoby (statutáři, vlastníci, společníci) na firmy, které vystupují ve smlouvách a dotacích,
  - vztahy typu:
    - osoba –[statutár_firmy]→ firma,
    - osoba –[společník/akcionář]→ firma.
- **RÚIAN / ISKN / ČÚZK**:
  - cíl: nemovitosti, na které se vztahují rozhodnutí města, projekty, dotace a smlouvy,
  - vztahy typu:
    - firma/osoba –[vlastní]→ nemovitost,
    - projekt –[realizován_na]→ nemovitost.
- **Volby.cz + Evidence veřejných funkcionářů**:
  - cíl: přehled veřejných funkcí a mandátů osob, které se objevují ve firmách či smlouvách,
  - vztahy typu:
    - osoba –[zastupitel/radní/člen_komise]→ orgán města,
    - osoba –[veřejný_funkcionář]→ funkce.

> Každý nový registr = nový collector, nové typy entit/vztahů, zápis do `entities` a `entity_links`.

---

## 4. „Signály“ – první heuristiky nesrovnalostí

Z nasbíraného grafu počítat jednoduché, vysvětlitelné signály:

- **Koncentrace zakázek / dotací u subjektu**:
  - pro každou firmu spočítat:
    - počet smluv s městem Boskovice,
    - součet částek,
    - období (rolling okno např. 2–3 roky),
  - subjekt, který výrazně vyčnívá nad medián / průměr, označit jako „zajímavý“ pro ruční analýzu.
- **Sekvence „rozhodnutí → smlouva → dotace“**:
  - hledat časové řetězce toho typu:
    - dokument/rozhodnutí města o projektu nebo záměru,
    - krátce poté smlouva s konkrétní firmou,
    - navázaná dotace (národní/EU).
  - tyto sekvence evidovat jako „case“ s časovou osou a odkazy na dokumenty.
- **Možné střety zájmů (role osoby)**:
  - osoba je:
    - na straně města (zastupitel, radní, člen komise),
    - zároveň má významnou roli ve firmě, která získává zakázky nebo dotace,
  - systém jen označí „možný střet zájmů – zkontroluj“, s přímými odkazy na všechny zdrojové dokumenty.

> Signály nikdy nejsou „verdikt“. Jsou to jen upozornění s vysvětlitelným výpočtem a trasovatelnými zdroji.

---

## 5. UX a API pro analytiky

- Vylepšit **detail entity**:
  - agregovat všechny smlouvy, dotace, dokumenty a nemovitosti,
  - ukázat role (`dodavatel`, `objednatel`, `příjemce_dotace`, `zmíněn_v_dokumentu`, …),
  - umožnit rychlé filtrování podle období a typu vztahu.
- Přidat **pohled na „case“**:
  - vizualizace konkrétního signálu (časová osa, zapojené entity, částky, zdroje),
  - snadný export / sdílení (např. PDF report, JSON snapshot).
- Připravit **interní API**:
  - endpointy typu:
    - `/api/entities/{id}`,
    - `/api/entities/{id}/relations`,
    - `/api/signals`,
  - aby další nástroje (např. tvoje skripty, jiné agenty, grafové vizualizéry) mohly data snadno využít.

---

## 6. Multi-city strategie

- Oddělit konfiguraci **města**:
  - IČO, doména, základní URL patterny, specifické collectory,
  - tak aby bylo možné přidat další město bez zásahu do jádra.
- Rozšířit datový model:
  - přidat `city_id` / `city_code` ke všem tabulkám (`documents`, `entities`, `contracts`, `subsidies`, `entity_links`),
  - nebo zvolit samostatné databáze per město (podle budoucího objemu dat).
- Ujistit se, že web UI:
  - umí přepínat město,
  - zároveň zachovává možnost „globálního pohledu“ přes více měst (dlouhodobě).

---

## Praktický postup pro další agenty (shrnutí)

1. Držet krok se `STAV_PROJEKTU.md` – tam je technický stav a co už běží v Boskovicích.
2. Podle tohoto dokumentu:
   - udržovat a rozšiřovat ontologii (typy entit a vztahů),
   - přidávat nové integrace na veřejné registry,
   - naplňovat `entity_links` konkrétními vazbami,
   - implementovat a ladit signály (vždy vysvětlitelně, s odkazy na zdroje).
3. Všechny nové vztahy a signály musí být:
   - dohledatelné ke zdrojovým dokumentům,
   - pochopitelné pro člověka (žádné „černé skříňky“),
   - znovu vypočitatelné (deterministické).

