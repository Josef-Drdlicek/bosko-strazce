import json
import sqlite3
from contextlib import contextmanager
from datetime import date, datetime
from pathlib import Path
from typing import Optional

from src.models import Attachment, Contract, Document, Entity, EntityLink, Subsidy


class Database:
    def __init__(self, db_path: Path):
        db_path.parent.mkdir(parents=True, exist_ok=True)
        self._db_path = db_path
        self._initialize_schema()

    @contextmanager
    def _connection(self):
        conn = sqlite3.connect(self._db_path)
        conn.row_factory = sqlite3.Row
        conn.execute("PRAGMA journal_mode=WAL")
        conn.execute("PRAGMA foreign_keys=ON")
        try:
            yield conn
            conn.commit()
        except Exception:
            conn.rollback()
            raise
        finally:
            conn.close()

    def _initialize_schema(self):
        with self._connection() as conn:
            self._migrate_documents_table(conn)
            conn.executescript("""
                CREATE TABLE IF NOT EXISTS documents (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    source_url TEXT NOT NULL UNIQUE,
                    title TEXT NOT NULL,
                    section TEXT NOT NULL,
                    published_date TEXT,
                    valid_until TEXT,
                    department TEXT,
                    fulltext TEXT,
                    collected_at TEXT NOT NULL,
                    duplicate_of INTEGER REFERENCES documents(id)
                );

                CREATE TABLE IF NOT EXISTS attachments (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    document_id INTEGER NOT NULL,
                    url TEXT NOT NULL,
                    filename TEXT NOT NULL,
                    local_path TEXT,
                    size_bytes INTEGER,
                    content_type TEXT,
                    FOREIGN KEY (document_id) REFERENCES documents(id) ON DELETE CASCADE
                );

                CREATE TABLE IF NOT EXISTS entities (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    name TEXT NOT NULL,
                    entity_type TEXT NOT NULL,
                    ico TEXT UNIQUE,
                    source TEXT,
                    metadata_json TEXT,
                    created_at TEXT NOT NULL
                );

                CREATE TABLE IF NOT EXISTS contracts (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    external_id TEXT NOT NULL UNIQUE,
                    subject TEXT NOT NULL,
                    amount REAL,
                    currency TEXT DEFAULT 'CZK',
                    date_signed TEXT,
                    date_published TEXT,
                    publisher_ico TEXT,
                    publisher_name TEXT,
                    counterparty_ico TEXT,
                    counterparty_name TEXT,
                    source_url TEXT,
                    fulltext TEXT,
                    collected_at TEXT NOT NULL
                );

                CREATE TABLE IF NOT EXISTS subsidies (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    external_id TEXT NOT NULL UNIQUE,
                    title TEXT NOT NULL,
                    provider TEXT,
                    recipient_ico TEXT,
                    recipient_name TEXT,
                    program TEXT,
                    amount REAL,
                    year INTEGER,
                    source_url TEXT,
                    collected_at TEXT NOT NULL
                );

                CREATE TABLE IF NOT EXISTS entity_links (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    entity_id INTEGER NOT NULL,
                    linked_type TEXT NOT NULL,
                    linked_id INTEGER NOT NULL,
                    role TEXT NOT NULL,
                    FOREIGN KEY (entity_id) REFERENCES entities(id) ON DELETE CASCADE,
                    UNIQUE(entity_id, linked_type, linked_id, role)
                );

                CREATE INDEX IF NOT EXISTS idx_documents_section
                    ON documents(section);
                CREATE INDEX IF NOT EXISTS idx_documents_published
                    ON documents(published_date);
                CREATE INDEX IF NOT EXISTS idx_documents_department
                    ON documents(department);
                CREATE INDEX IF NOT EXISTS idx_documents_duplicate
                    ON documents(duplicate_of);
                CREATE INDEX IF NOT EXISTS idx_attachments_document
                    ON attachments(document_id);
                CREATE INDEX IF NOT EXISTS idx_entities_ico
                    ON entities(ico);
                CREATE INDEX IF NOT EXISTS idx_entities_type
                    ON entities(entity_type);
                CREATE INDEX IF NOT EXISTS idx_contracts_publisher
                    ON contracts(publisher_ico);
                CREATE INDEX IF NOT EXISTS idx_contracts_counterparty
                    ON contracts(counterparty_ico);
                CREATE INDEX IF NOT EXISTS idx_contracts_date
                    ON contracts(date_signed);
                CREATE INDEX IF NOT EXISTS idx_subsidies_recipient
                    ON subsidies(recipient_ico);
                CREATE INDEX IF NOT EXISTS idx_entity_links_entity
                    ON entity_links(entity_id);
                CREATE INDEX IF NOT EXISTS idx_entity_links_target
                    ON entity_links(linked_type, linked_id);
            """)

    def _migrate_documents_table(self, conn):
        columns = {row[1] for row in conn.execute("PRAGMA table_info(documents)").fetchall()}
        if columns and "duplicate_of" not in columns:
            conn.execute("ALTER TABLE documents ADD COLUMN duplicate_of INTEGER REFERENCES documents(id)")

    def document_exists(self, source_url: str) -> bool:
        with self._connection() as conn:
            row = conn.execute(
                "SELECT 1 FROM documents WHERE source_url = ?",
                (source_url,),
            ).fetchone()
            return row is not None

    def save_document(self, document: Document) -> int:
        with self._connection() as conn:
            cursor = conn.execute(
                """INSERT OR REPLACE INTO documents
                   (source_url, title, section, published_date,
                    valid_until, department, fulltext, collected_at)
                   VALUES (?, ?, ?, ?, ?, ?, ?, ?)""",
                (
                    document.source_url,
                    document.title,
                    document.section,
                    _date_to_str(document.published_date),
                    _date_to_str(document.valid_until),
                    document.department,
                    document.fulltext,
                    datetime.now().isoformat(),
                ),
            )
            document_id = cursor.lastrowid

            conn.execute(
                "DELETE FROM attachments WHERE document_id = ?",
                (document_id,),
            )
            for attachment in document.attachments:
                conn.execute(
                    """INSERT INTO attachments
                       (document_id, url, filename, local_path,
                        size_bytes, content_type)
                       VALUES (?, ?, ?, ?, ?, ?)""",
                    (
                        document_id,
                        attachment.url,
                        attachment.filename,
                        attachment.local_path,
                        attachment.size_bytes,
                        attachment.content_type,
                    ),
                )
            return document_id

    def update_fulltext(self, source_url: str, fulltext: str):
        with self._connection() as conn:
            conn.execute(
                "UPDATE documents SET fulltext = ? WHERE source_url = ?",
                (fulltext, source_url),
            )

    def get_documents_without_fulltext(self) -> list[dict]:
        with self._connection() as conn:
            rows = conn.execute(
                """SELECT d.id, d.source_url, a.local_path, a.filename
                   FROM documents d
                   JOIN attachments a ON a.document_id = d.id
                   WHERE d.fulltext IS NULL
                     AND a.local_path IS NOT NULL
                     AND (a.filename LIKE '%.pdf'
                          OR a.filename LIKE '%.docx'
                          OR a.filename LIKE '%.doc'
                          OR a.filename LIKE '%.rtf')
                   ORDER BY d.id""",
            ).fetchall()
            return [dict(row) for row in rows]

    def get_all_attachments_without_file(self) -> list[dict]:
        with self._connection() as conn:
            rows = conn.execute(
                """SELECT a.id, a.url, a.filename, d.section
                   FROM attachments a
                   JOIN documents d ON d.id = a.document_id
                   WHERE a.local_path IS NULL
                   ORDER BY a.id""",
            ).fetchall()
            return [dict(row) for row in rows]

    def update_attachment_path(self, attachment_id: int, local_path: str, size_bytes: int):
        with self._connection() as conn:
            conn.execute(
                """UPDATE attachments
                   SET local_path = ?, size_bytes = ?
                   WHERE id = ?""",
                (local_path, size_bytes, attachment_id),
            )

    def count_documents(self, section: Optional[str] = None) -> int:
        with self._connection() as conn:
            if section:
                row = conn.execute(
                    "SELECT COUNT(*) as cnt FROM documents WHERE section = ?",
                    (section,),
                ).fetchone()
            else:
                row = conn.execute("SELECT COUNT(*) as cnt FROM documents").fetchone()
            return row["cnt"]

    def search_fulltext(self, query: str) -> list[dict]:
        with self._connection() as conn:
            rows = conn.execute(
                """SELECT id, source_url, title, section,
                          published_date, department
                   FROM documents
                   WHERE fulltext LIKE ?
                   ORDER BY published_date DESC""",
                (f"%{query}%",),
            ).fetchall()
            return [dict(row) for row in rows]

    def mark_duplicate(self, document_id: int, duplicate_of: int):
        with self._connection() as conn:
            conn.execute(
                "UPDATE documents SET duplicate_of = ? WHERE id = ?",
                (duplicate_of, document_id),
            )

    def get_attachment_file_ids(self) -> list[dict]:
        with self._connection() as conn:
            rows = conn.execute(
                """SELECT a.id, a.url, a.document_id, d.title, d.section
                   FROM attachments a
                   JOIN documents d ON d.id = a.document_id
                   ORDER BY a.url""",
            ).fetchall()
            return [dict(row) for row in rows]

    def get_documents_by_section(
        self, section: str, limit: int = 50, offset: int = 0
    ) -> list[dict]:
        with self._connection() as conn:
            rows = conn.execute(
                """SELECT id, source_url, title, section, published_date,
                          department, duplicate_of
                   FROM documents
                   WHERE section = ?
                   ORDER BY published_date DESC
                   LIMIT ? OFFSET ?""",
                (section, limit, offset),
            ).fetchall()
            return [dict(row) for row in rows]

    def get_document_by_id(self, document_id: int) -> Optional[dict]:
        with self._connection() as conn:
            row = conn.execute(
                "SELECT * FROM documents WHERE id = ?", (document_id,)
            ).fetchone()
            if row is None:
                return None
            doc = dict(row)
            attachments = conn.execute(
                "SELECT * FROM attachments WHERE document_id = ?",
                (document_id,),
            ).fetchall()
            doc["attachments"] = [dict(a) for a in attachments]
            links = conn.execute(
                """SELECT el.role, e.id as entity_id, e.name, e.ico, e.entity_type
                   FROM entity_links el
                   JOIN entities e ON e.id = el.entity_id
                   WHERE el.linked_type = 'document' AND el.linked_id = ?""",
                (document_id,),
            ).fetchall()
            doc["linked_entities"] = [dict(l) for l in links]
            return doc

    def get_recent_documents(self, limit: int = 20) -> list[dict]:
        with self._connection() as conn:
            rows = conn.execute(
                """SELECT id, source_url, title, section, published_date, department
                   FROM documents
                   WHERE duplicate_of IS NULL
                   ORDER BY collected_at DESC
                   LIMIT ?""",
                (limit,),
            ).fetchall()
            return [dict(row) for row in rows]

    # --- Entity CRUD ---

    def entity_exists_by_ico(self, ico: str) -> bool:
        with self._connection() as conn:
            row = conn.execute(
                "SELECT 1 FROM entities WHERE ico = ?", (ico,)
            ).fetchone()
            return row is not None

    def get_entity_by_ico(self, ico: str) -> Optional[dict]:
        with self._connection() as conn:
            row = conn.execute(
                "SELECT * FROM entities WHERE ico = ?", (ico,)
            ).fetchone()
            return dict(row) if row else None

    def save_entity(self, entity: Entity) -> int:
        with self._connection() as conn:
            cursor = conn.execute(
                """INSERT INTO entities (name, entity_type, ico, source, metadata_json, created_at)
                   VALUES (?, ?, ?, ?, ?, ?)
                   ON CONFLICT(ico) DO UPDATE SET
                       name = excluded.name,
                       metadata_json = excluded.metadata_json,
                       source = excluded.source""",
                (
                    entity.name,
                    entity.entity_type,
                    entity.ico,
                    entity.source,
                    entity.metadata_json,
                    datetime.now().isoformat(),
                ),
            )
            if entity.ico:
                row = conn.execute(
                    "SELECT id FROM entities WHERE ico = ?", (entity.ico,)
                ).fetchone()
                return row["id"]
            return cursor.lastrowid

    def get_all_entities(self, limit: int = 100, offset: int = 0) -> list[dict]:
        with self._connection() as conn:
            rows = conn.execute(
                """SELECT e.*, COUNT(el.id) as link_count
                   FROM entities e
                   LEFT JOIN entity_links el ON el.entity_id = e.id
                   GROUP BY e.id
                   ORDER BY link_count DESC
                   LIMIT ? OFFSET ?""",
                (limit, offset),
            ).fetchall()
            return [dict(row) for row in rows]

    def get_entity_by_id(self, entity_id: int) -> Optional[dict]:
        with self._connection() as conn:
            row = conn.execute(
                "SELECT * FROM entities WHERE id = ?", (entity_id,)
            ).fetchone()
            if row is None:
                return None
            entity = dict(row)
            links = conn.execute(
                "SELECT * FROM entity_links WHERE entity_id = ?",
                (entity_id,),
            ).fetchall()
            entity["links"] = [dict(l) for l in links]
            return entity

    def count_entities(self) -> int:
        with self._connection() as conn:
            row = conn.execute("SELECT COUNT(*) as cnt FROM entities").fetchone()
            return row["cnt"]

    def get_all_entity_icos(self) -> set[str]:
        with self._connection() as conn:
            rows = conn.execute(
                "SELECT ico FROM entities WHERE ico IS NOT NULL"
            ).fetchall()
            return {row["ico"] for row in rows}

    # --- Entity Link CRUD ---

    def save_entity_link(self, link: EntityLink):
        with self._connection() as conn:
            conn.execute(
                """INSERT OR IGNORE INTO entity_links
                   (entity_id, linked_type, linked_id, role)
                   VALUES (?, ?, ?, ?)""",
                (link.entity_id, link.linked_type, link.linked_id, link.role),
            )

    def get_links_for_entity(self, entity_id: int) -> list[dict]:
        with self._connection() as conn:
            rows = conn.execute(
                "SELECT * FROM entity_links WHERE entity_id = ?",
                (entity_id,),
            ).fetchall()
            return [dict(row) for row in rows]

    # --- Contract CRUD ---

    def contract_exists(self, external_id: str) -> bool:
        with self._connection() as conn:
            row = conn.execute(
                "SELECT 1 FROM contracts WHERE external_id = ?",
                (external_id,),
            ).fetchone()
            return row is not None

    def save_contract(self, contract: Contract) -> int:
        with self._connection() as conn:
            cursor = conn.execute(
                """INSERT OR REPLACE INTO contracts
                   (external_id, subject, amount, currency, date_signed,
                    date_published, publisher_ico, publisher_name,
                    counterparty_ico, counterparty_name, source_url,
                    fulltext, collected_at)
                   VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)""",
                (
                    contract.external_id,
                    contract.subject,
                    contract.amount,
                    contract.currency,
                    _date_to_str(contract.date_signed),
                    _date_to_str(contract.date_published),
                    contract.publisher_ico,
                    contract.publisher_name,
                    contract.counterparty_ico,
                    contract.counterparty_name,
                    contract.source_url,
                    contract.fulltext,
                    datetime.now().isoformat(),
                ),
            )
            return cursor.lastrowid

    def count_contracts(self) -> int:
        with self._connection() as conn:
            row = conn.execute("SELECT COUNT(*) as cnt FROM contracts").fetchone()
            return row["cnt"]

    def get_all_contracts(self, limit: int = 50, offset: int = 0) -> list[dict]:
        with self._connection() as conn:
            rows = conn.execute(
                """SELECT * FROM contracts
                   ORDER BY date_signed DESC
                   LIMIT ? OFFSET ?""",
                (limit, offset),
            ).fetchall()
            return [dict(row) for row in rows]

    def get_contract_by_id(self, contract_id: int) -> Optional[dict]:
        with self._connection() as conn:
            row = conn.execute(
                "SELECT * FROM contracts WHERE id = ?", (contract_id,)
            ).fetchone()
            if row is None:
                return None
            contract = dict(row)
            links = conn.execute(
                """SELECT el.role, e.id as entity_id, e.name, e.ico, e.entity_type
                   FROM entity_links el
                   JOIN entities e ON e.id = el.entity_id
                   WHERE el.linked_type = 'contract' AND el.linked_id = ?""",
                (contract_id,),
            ).fetchall()
            contract["linked_entities"] = [dict(l) for l in links]
            return contract

    def get_recent_contracts(self, limit: int = 20) -> list[dict]:
        with self._connection() as conn:
            rows = conn.execute(
                """SELECT id, external_id, subject, amount, currency,
                          date_signed, publisher_name, counterparty_name
                   FROM contracts
                   ORDER BY date_signed DESC
                   LIMIT ?""",
                (limit,),
            ).fetchall()
            return [dict(row) for row in rows]

    def get_contracts_by_ico(self, ico: str) -> list[dict]:
        with self._connection() as conn:
            rows = conn.execute(
                """SELECT * FROM contracts
                   WHERE publisher_ico = ? OR counterparty_ico = ?
                   ORDER BY date_signed DESC""",
                (ico, ico),
            ).fetchall()
            return [dict(row) for row in rows]

    def search_contracts(self, query: str) -> list[dict]:
        with self._connection() as conn:
            rows = conn.execute(
                """SELECT id, external_id, subject, amount, currency,
                          date_signed, publisher_name, counterparty_name
                   FROM contracts
                   WHERE subject LIKE ? OR fulltext LIKE ?
                   ORDER BY date_signed DESC""",
                (f"%{query}%", f"%{query}%"),
            ).fetchall()
            return [dict(row) for row in rows]

    # --- Subsidy CRUD ---

    def subsidy_exists(self, external_id: str) -> bool:
        with self._connection() as conn:
            row = conn.execute(
                "SELECT 1 FROM subsidies WHERE external_id = ?",
                (external_id,),
            ).fetchone()
            return row is not None

    def save_subsidy(self, subsidy: Subsidy) -> int:
        with self._connection() as conn:
            cursor = conn.execute(
                """INSERT OR REPLACE INTO subsidies
                   (external_id, title, provider, recipient_ico,
                    recipient_name, program, amount, year,
                    source_url, collected_at)
                   VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)""",
                (
                    subsidy.external_id,
                    subsidy.title,
                    subsidy.provider,
                    subsidy.recipient_ico,
                    subsidy.recipient_name,
                    subsidy.program,
                    subsidy.amount,
                    subsidy.year,
                    subsidy.source_url,
                    datetime.now().isoformat(),
                ),
            )
            return cursor.lastrowid

    def count_subsidies(self) -> int:
        with self._connection() as conn:
            row = conn.execute("SELECT COUNT(*) as cnt FROM subsidies").fetchone()
            return row["cnt"]

    def get_all_subsidies(self, limit: int = 50, offset: int = 0) -> list[dict]:
        with self._connection() as conn:
            rows = conn.execute(
                """SELECT * FROM subsidies
                   ORDER BY year DESC
                   LIMIT ? OFFSET ?""",
                (limit, offset),
            ).fetchall()
            return [dict(row) for row in rows]

    # --- Global search ---

    def search_all(self, query: str) -> dict:
        pattern = f"%{query}%"
        with self._connection() as conn:
            docs = conn.execute(
                """SELECT id, source_url, title, section, published_date
                   FROM documents
                   WHERE (fulltext LIKE ? OR title LIKE ?)
                     AND duplicate_of IS NULL
                   ORDER BY published_date DESC LIMIT 50""",
                (pattern, pattern),
            ).fetchall()
            contracts = conn.execute(
                """SELECT id, external_id, subject, amount, date_signed,
                          counterparty_name
                   FROM contracts
                   WHERE subject LIKE ? OR fulltext LIKE ?
                   ORDER BY date_signed DESC LIMIT 50""",
                (pattern, pattern),
            ).fetchall()
            entities = conn.execute(
                """SELECT id, name, entity_type, ico
                   FROM entities
                   WHERE name LIKE ? OR ico LIKE ?
                   ORDER BY name LIMIT 50""",
                (pattern, pattern),
            ).fetchall()
            return {
                "documents": [dict(r) for r in docs],
                "contracts": [dict(r) for r in contracts],
                "entities": [dict(r) for r in entities],
            }

    # --- Dashboard stats ---

    def get_dashboard_stats(self) -> dict:
        with self._connection() as conn:
            doc_count = conn.execute(
                "SELECT COUNT(*) as cnt FROM documents"
            ).fetchone()["cnt"]
            contract_count = conn.execute(
                "SELECT COUNT(*) as cnt FROM contracts"
            ).fetchone()["cnt"]
            entity_count = conn.execute(
                "SELECT COUNT(*) as cnt FROM entities"
            ).fetchone()["cnt"]
            subsidy_count = conn.execute(
                "SELECT COUNT(*) as cnt FROM subsidies"
            ).fetchone()["cnt"]
            contract_sum = conn.execute(
                "SELECT COALESCE(SUM(amount), 0) as total FROM contracts"
            ).fetchone()["total"]

            sections = conn.execute(
                """SELECT section, COUNT(*) as cnt
                   FROM documents
                   GROUP BY section
                   ORDER BY cnt DESC""",
            ).fetchall()

            return {
                "documents": doc_count,
                "contracts": contract_count,
                "entities": entity_count,
                "subsidies": subsidy_count,
                "contract_total_czk": contract_sum,
                "sections": [dict(s) for s in sections],
            }


def _date_to_str(value: Optional[date]) -> Optional[str]:
    if value is None:
        return None
    return value.isoformat()
