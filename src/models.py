from dataclasses import dataclass, field
from datetime import date, datetime
from typing import Optional


@dataclass
class Attachment:
    url: str
    filename: str
    local_path: Optional[str] = None
    size_bytes: Optional[int] = None
    content_type: Optional[str] = None


@dataclass
class Document:
    source_url: str
    title: str
    section: str
    published_date: Optional[date] = None
    valid_until: Optional[date] = None
    department: Optional[str] = None
    attachments: list[Attachment] = field(default_factory=list)
    fulltext: Optional[str] = None
    collected_at: Optional[datetime] = None
    document_id: Optional[int] = None
    duplicate_of: Optional[int] = None


@dataclass
class Entity:
    name: str
    entity_type: str
    ico: Optional[str] = None
    source: Optional[str] = None
    metadata_json: Optional[str] = None
    entity_id: Optional[int] = None


@dataclass
class Contract:
    external_id: str
    subject: str
    amount: Optional[float] = None
    currency: str = "CZK"
    date_signed: Optional[date] = None
    date_published: Optional[date] = None
    publisher_ico: Optional[str] = None
    publisher_name: Optional[str] = None
    counterparty_ico: Optional[str] = None
    counterparty_name: Optional[str] = None
    source_url: Optional[str] = None
    fulltext: Optional[str] = None
    contract_id: Optional[int] = None


@dataclass
class Subsidy:
    external_id: str
    title: str
    provider: Optional[str] = None
    recipient_ico: Optional[str] = None
    recipient_name: Optional[str] = None
    program: Optional[str] = None
    amount: Optional[float] = None
    year: Optional[int] = None
    source_url: Optional[str] = None
    subsidy_id: Optional[int] = None


@dataclass
class EntityLink:
    entity_id: int
    linked_type: str
    linked_id: int
    role: str
