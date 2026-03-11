from pathlib import Path
from typing import Optional

from fastapi import FastAPI, Query, Request
from fastapi.responses import HTMLResponse
from fastapi.staticfiles import StaticFiles
from fastapi.templating import Jinja2Templates

from src.config import DB_PATH
from src.database import Database

_WEB_DIR = Path(__file__).resolve().parent

app = FastAPI(title="Bosko Strážce", docs_url=None, redoc_url=None)
app.mount("/static", StaticFiles(directory=_WEB_DIR / "static"), name="static")

templates = Jinja2Templates(directory=_WEB_DIR / "templates")


def _db() -> Database:
    return Database(DB_PATH)


@app.get("/", response_class=HTMLResponse)
async def dashboard(request: Request):
    db = _db()
    stats = db.get_dashboard_stats()
    recent_docs = db.get_recent_documents(limit=10)
    recent_contracts = db.get_recent_contracts(limit=10)
    return templates.TemplateResponse("dashboard.html", {
        "request": request,
        "stats": stats,
        "recent_docs": recent_docs,
        "recent_contracts": recent_contracts,
    })


@app.get("/documents", response_class=HTMLResponse)
async def documents_list(
    request: Request,
    section: Optional[str] = None,
    q: Optional[str] = None,
    page: int = Query(1, ge=1),
):
    db = _db()
    per_page = 50
    offset = (page - 1) * per_page

    if q:
        results = db.search_fulltext(q)
        total = len(results)
        docs = results[offset:offset + per_page]
    elif section:
        docs = db.get_documents_by_section(section, limit=per_page, offset=offset)
        total = db.count_documents(section)
    else:
        docs = db.get_recent_documents(limit=per_page)
        total = db.count_documents()

    sections = db.get_dashboard_stats()["sections"]

    return templates.TemplateResponse("documents.html", {
        "request": request,
        "documents": docs,
        "sections": sections,
        "current_section": section,
        "query": q or "",
        "page": page,
        "total": total,
        "per_page": per_page,
    })


@app.get("/document/{document_id}", response_class=HTMLResponse)
async def document_detail(request: Request, document_id: int):
    db = _db()
    doc = db.get_document_by_id(document_id)
    if doc is None:
        return HTMLResponse("<h1>Dokument nenalezen</h1>", status_code=404)
    return templates.TemplateResponse("document_detail.html", {
        "request": request,
        "doc": doc,
    })


@app.get("/contracts", response_class=HTMLResponse)
async def contracts_list(
    request: Request,
    q: Optional[str] = None,
    page: int = Query(1, ge=1),
):
    db = _db()
    per_page = 50
    offset = (page - 1) * per_page

    if q:
        contracts = db.search_contracts(q)
        total = len(contracts)
        contracts = contracts[offset:offset + per_page]
    else:
        contracts = db.get_all_contracts(limit=per_page, offset=offset)
        total = db.count_contracts()

    return templates.TemplateResponse("contracts.html", {
        "request": request,
        "contracts": contracts,
        "query": q or "",
        "page": page,
        "total": total,
        "per_page": per_page,
    })


@app.get("/contract/{contract_id}", response_class=HTMLResponse)
async def contract_detail(request: Request, contract_id: int):
    db = _db()
    contract = db.get_contract_by_id(contract_id)
    if contract is None:
        return HTMLResponse("<h1>Smlouva nenalezena</h1>", status_code=404)
    return templates.TemplateResponse("contract_detail.html", {
        "request": request,
        "contract": contract,
    })


@app.get("/entities", response_class=HTMLResponse)
async def entities_list(
    request: Request,
    page: int = Query(1, ge=1),
):
    db = _db()
    per_page = 50
    offset = (page - 1) * per_page
    entities = db.get_all_entities(limit=per_page, offset=offset)
    total = db.count_entities()

    return templates.TemplateResponse("entities.html", {
        "request": request,
        "entities": entities,
        "page": page,
        "total": total,
        "per_page": per_page,
    })


@app.get("/entity/{entity_id}", response_class=HTMLResponse)
async def entity_detail(request: Request, entity_id: int):
    db = _db()
    entity = db.get_entity_by_id(entity_id)
    if entity is None:
        return HTMLResponse("<h1>Entita nenalezena</h1>", status_code=404)

    contracts = []
    documents = []
    for link in entity.get("links", []):
        if link["linked_type"] == "contract":
            c = db.get_contract_by_id(link["linked_id"])
            if c:
                c["role"] = link["role"]
                contracts.append(c)
        elif link["linked_type"] == "document":
            d = db.get_document_by_id(link["linked_id"])
            if d:
                d["role"] = link["role"]
                documents.append(d)

    return templates.TemplateResponse("entity_detail.html", {
        "request": request,
        "entity": entity,
        "contracts": contracts,
        "documents": documents,
    })


@app.get("/search", response_class=HTMLResponse)
async def global_search(
    request: Request,
    q: str = Query(""),
):
    db = _db()
    results = db.search_all(q) if q else {"documents": [], "contracts": [], "entities": []}

    return templates.TemplateResponse("search.html", {
        "request": request,
        "query": q,
        "results": results,
    })
