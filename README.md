# Bosko Strážce

Systém pro automatický sběr, ukládání a analýzu veřejných dat města Boskovice.

## Instalace

```bash
pip install -r requirements.txt
```

Pro sběr archivu úřední desky (Fáze 4) je potřeba Playwright:

```bash
playwright install chromium
```

## Použití

### Sběr dat

```bash
# Vše najednou
python main.py collect-all

# Jednotlivé zdroje
python main.py collect-uredni-deska   # Úřední deska (Open Data API)
python main.py collect-zapisy         # Zápisy ZM a RM
python main.py collect-documents      # Vyhlášky, rozpočty, poskytnuté informace
python main.py collect-archive        # Archiv úřední desky (Playwright)
```

### Stahování souborů a extrakce textu

```bash
python main.py download-files         # Stáhne PDF přílohy
python main.py extract-text           # Extrahuje text z PDF/DOCX/RTF
```

### Vyhledávání a statistiky

```bash
python main.py stats                  # Přehled sbíraných dat
python main.py search "sportovní hala" # Fulltextové vyhledávání
```

## Zdroje dat

| Zdroj | Typ | Metoda |
|-------|-----|--------|
| Úřední deska | JSON-LD (OFN) | HTTP GET |
| Zápisy ZM | HTML + PDF | HTTP GET |
| Zápisy RM | HTML + PDF | HTTP GET |
| Vyhlášky | HTML + PDF | HTTP GET |
| Rozpočty | HTML + PDF | HTTP GET |
| Poskytnuté informace | HTML + PDF | HTTP GET |
| Archiv úřední desky | HTML (JS) | Playwright |

## Struktura

```
src/
├── config.py           # Konfigurace
├── models.py           # Doménové entity
├── database.py         # SQLite úložiště
├── downloader.py       # HTTP klient + stahování souborů
├── collectors/
│   ├── uredni_deska.py # Fáze 1: Open Data API
│   ├── zapisy.py       # Fáze 2: Zápisy ZM/RM
│   ├── documents.py    # Fáze 3: Vyhlášky, rozpočty, info
│   └── archive.py      # Fáze 4: Archiv (Playwright)
└── extractors/
    └── pdf.py          # Fáze 5: Extrakce textu
```
