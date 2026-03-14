import argparse
import logging
import sys

from src.config import DB_PATH
from src.database import Database
from src.downloader import Downloader


def setup_logging(verbose: bool):
    level = logging.DEBUG if verbose else logging.INFO
    logging.basicConfig(
        level=level,
        format="%(asctime)s [%(levelname)s] %(name)s: %(message)s",
        datefmt="%Y-%m-%d %H:%M:%S",
    )


def run_collect_uredni_deska(database: Database, downloader: Downloader):
    from src.collectors.uredni_deska import UredniDeskaCollector

    collector = UredniDeskaCollector(database, downloader)
    return collector.collect()


def run_collect_zapisy(database: Database, downloader: Downloader):
    from src.collectors.zapisy import ZapisyCollector

    collector = ZapisyCollector(database, downloader)
    return collector.collect()


def run_collect_documents(database: Database, downloader: Downloader):
    from src.collectors.documents import DocumentsCollector

    collector = DocumentsCollector(database, downloader)
    return collector.collect()


def run_collect_archive(database: Database, downloader: Downloader):
    from src.collectors.archive import ArchiveCollector

    collector = ArchiveCollector(database, downloader)
    return collector.collect()


def run_collect_contracts(database: Database, downloader: Downloader):
    from src.collectors.hlidac_smluv import HlidacSmluvCollector

    collector = HlidacSmluvCollector(database, downloader)
    return collector.collect()


def run_collect_subsidies(database: Database, downloader: Downloader):
    from src.collectors.cedr import CedrCollector

    collector = CedrCollector(database, downloader)
    return collector.collect()


def run_enrich_entities(database: Database, downloader: Downloader):
    from src.collectors.ares import AresCollector

    collector = AresCollector(database, downloader)
    return collector.enrich_all_entities()


def run_collect_persons(database: Database, downloader: Downloader):
    from src.collectors.justice import JusticeCollector

    collector = JusticeCollector(database, downloader)
    return collector.collect()


def run_collect_volby(database: Database, downloader: Downloader):
    from src.collectors.volby import VolbyCollector

    collector = VolbyCollector(database, downloader)
    return collector.collect()


def run_extract_entities(database: Database):
    from src.extractors.entity_extractor import extract_entities_from_documents

    return extract_entities_from_documents(database)


def run_deduplicate(database: Database):
    from src.deduplication import run_deduplication

    return run_deduplication(database)


def run_download_files(database: Database, downloader: Downloader):
    pending = database.get_all_attachments_without_file()
    logging.info("Downloading %d pending files...", len(pending))

    downloaded = 0
    for attachment in pending:
        try:
            path = downloader.download_file(attachment["url"], attachment["section"])
            if path:
                database.update_attachment_path(
                    attachment["id"],
                    str(path),
                    path.stat().st_size,
                )
                downloaded += 1
        except Exception as exc:
            logging.warning("Download failed for %s: %s", attachment["url"], exc)

    logging.info("Downloaded %d files", downloaded)
    return downloaded


def run_extract_text(database: Database):
    from src.extractors.pdf import PdfExtractor

    extractor = PdfExtractor()
    pending = database.get_documents_without_fulltext()
    logging.info("Extracting text from %d files...", len(pending))

    extracted = 0
    for record in pending:
        text = extractor.extract(record["local_path"])
        if text:
            database.update_fulltext(record["source_url"], text)
            extracted += 1

    logging.info("Extracted text from %d files", extracted)
    return extracted


def run_stats(database: Database):
    stats = database.get_dashboard_stats()
    print(f"\nTotal documents: {stats['documents']}")
    print(f"Contracts: {stats['contracts']}")
    print(f"Entities: {stats['entities']}")
    print(f"Subsidies: {stats['subsidies']}")
    if stats['contract_total_czk']:
        print(f"Contract total: {stats['contract_total_czk']:,.0f} CZK")
    print("\nDocuments by section:")
    for section in stats["sections"]:
        print(f"  {section['section']}: {section['cnt']}")
    print()


def run_search(database: Database, query: str):
    results = database.search_all(query)

    if results["documents"]:
        print(f"\nDocuments ({len(results['documents'])}):")
        for row in results["documents"]:
            print(f"  [{row['published_date'] or '?'}] {row['title']}")
            print(f"    Section: {row['section']} | URL: {row.get('source_url', '')}")

    if results["contracts"]:
        print(f"\nContracts ({len(results['contracts'])}):")
        for row in results["contracts"]:
            amount = f"{row['amount']:,.0f} CZK" if row.get("amount") else "N/A"
            print(f"  [{row['date_signed'] or '?'}] {row['subject'][:60]}")
            print(f"    Counterparty: {row.get('counterparty_name', '?')} | Amount: {amount}")

    if results["entities"]:
        print(f"\nEntities ({len(results['entities'])}):")
        for row in results["entities"]:
            print(f"  {row['name']} (ICO: {row.get('ico', '?')}, type: {row['entity_type']})")

    if not any(results.values()):
        print(f"No results for: {query}")

    print()


def run_serve(port: int):
    import uvicorn
    from src.web.app import app

    uvicorn.run(app, host="0.0.0.0", port=port)


def build_parser() -> argparse.ArgumentParser:
    parser = argparse.ArgumentParser(
        description="Bosko Strážce - Monitoring veřejné správy města Boskovice",
    )
    parser.add_argument("-v", "--verbose", action="store_true", help="Verbose output")

    subparsers = parser.add_subparsers(dest="command", help="Available commands")

    subparsers.add_parser("collect-all", help="Run all collectors")
    subparsers.add_parser("collect-uredni-deska", help="Collect úřední deska (Open Data API)")
    subparsers.add_parser("collect-zapisy", help="Collect ZM and RM minutes")
    subparsers.add_parser("collect-documents", help="Collect vyhlášky, rozpočty, info")
    subparsers.add_parser("collect-archive", help="Collect úřední deska archive")
    subparsers.add_parser("collect-contracts", help="Collect contracts from Hlídač státu")
    subparsers.add_parser("collect-subsidies", help="Collect subsidies from CEDR via Hlídač státu")
    subparsers.add_parser("enrich-entities", help="Enrich entities with ARES data")
    subparsers.add_parser("collect-persons", help="Collect statutory reps from ARES VR (Justice.cz data)")
    subparsers.add_parser("collect-volby", help="Collect elected council members from Volby.cz")
    subparsers.add_parser("extract-entities", help="Extract entity ICOs from document text")
    subparsers.add_parser("deduplicate", help="Mark duplicate documents")
    subparsers.add_parser("download-files", help="Download pending PDF attachments")
    subparsers.add_parser("extract-text", help="Extract text from downloaded files")
    subparsers.add_parser("stats", help="Show collection statistics")

    search_parser = subparsers.add_parser("search", help="Fulltext search")
    search_parser.add_argument("query", help="Search query")

    serve_parser = subparsers.add_parser("serve", help="Start web UI")
    serve_parser.add_argument("--port", type=int, default=8000, help="Port number")

    return parser


def main():
    parser = build_parser()
    args = parser.parse_args()

    if not args.command:
        parser.print_help()
        sys.exit(1)

    setup_logging(args.verbose)

    if args.command == "serve":
        run_serve(args.port)
        return

    database = Database(DB_PATH)

    if args.command == "stats":
        run_stats(database)
        return

    if args.command == "search":
        run_search(database, args.query)
        return

    if args.command == "deduplicate":
        run_deduplicate(database)
        return

    if args.command == "extract-entities":
        run_extract_entities(database)
        return

    if args.command == "extract-text":
        run_extract_text(database)
        return

    downloader = Downloader()

    commands = {
        "collect-uredni-deska": lambda: run_collect_uredni_deska(database, downloader),
        "collect-zapisy": lambda: run_collect_zapisy(database, downloader),
        "collect-documents": lambda: run_collect_documents(database, downloader),
        "collect-archive": lambda: run_collect_archive(database, downloader),
        "collect-contracts": lambda: run_collect_contracts(database, downloader),
        "collect-subsidies": lambda: run_collect_subsidies(database, downloader),
        "enrich-entities": lambda: run_enrich_entities(database, downloader),
        "collect-persons": lambda: run_collect_persons(database, downloader),
        "collect-volby": lambda: run_collect_volby(database, downloader),
        "download-files": lambda: run_download_files(database, downloader),
        "collect-all": lambda: _collect_all(database, downloader),
    }

    handler = commands.get(args.command)
    if handler:
        handler()


def _collect_all(database: Database, downloader: Downloader):
    logging.info("=== Running all collectors ===")

    run_collect_uredni_deska(database, downloader)
    run_collect_zapisy(database, downloader)
    run_collect_documents(database, downloader)

    try:
        run_collect_archive(database, downloader)
    except Exception as exc:
        logging.warning("Archive collection failed: %s", exc)

    try:
        run_collect_contracts(database, downloader)
    except Exception as exc:
        logging.warning("Contract collection failed: %s", exc)

    try:
        run_collect_subsidies(database, downloader)
    except Exception as exc:
        logging.warning("Subsidy collection failed: %s", exc)

    run_download_files(database, downloader)
    run_extract_text(database)
    run_extract_entities(database)
    run_deduplicate(database)

    try:
        run_enrich_entities(database, downloader)
    except Exception as exc:
        logging.warning("ARES enrichment failed: %s", exc)

    try:
        run_collect_persons(database, downloader)
    except Exception as exc:
        logging.warning("Justice/person collection failed: %s", exc)

    try:
        run_collect_volby(database, downloader)
    except Exception as exc:
        logging.warning("Volby collection failed: %s", exc)

    logging.info("=== Collection complete ===")
    run_stats(database)


if __name__ == "__main__":
    main()
