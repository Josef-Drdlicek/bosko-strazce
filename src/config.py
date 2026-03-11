import os
from pathlib import Path

from dotenv import load_dotenv

PROJECT_ROOT = Path(__file__).resolve().parent.parent
load_dotenv(PROJECT_ROOT / ".env")

BASE_URL = "https://www.boskovice.cz"
OPEN_DATA_UREDNI_DESKA = f"{BASE_URL}/data/308"
FILE_URL_TEMPLATE = f"{BASE_URL}/file/{{file_id}}"

DATA_DIR = PROJECT_ROOT / "data"
PDF_DIR = DATA_DIR / "pdf"
DB_PATH = DATA_DIR / "db" / "boskovice.db"

REQUEST_DELAY_SECONDS = 1.0
REQUEST_TIMEOUT_SECONDS = 30
USER_AGENT = "BoskoStrazce/1.0 (open-data-archiver; +https://github.com/bosko-strazce)"

ZAPISY_ZM_YEARS = range(2006, 2027)
ZAPISY_RM_YEARS = range(2012, 2027)

BOSKOVICE_ICO = "00279978"

HLIDAC_STATU_API_URL = "https://api.hlidacstatu.cz/Api/v2"
HLIDAC_STATU_TOKEN = os.environ.get("HLIDAC_STATU_TOKEN", "")

ARES_API_URL = "https://ares.gov.cz/ekonomicke-subjekty-v-be/rest/ekonomicke-subjekty"
ARES_REQUEST_DELAY = 0.15

CEDR_API_URL = "https://data.mf.gov.cz/api/v1"
