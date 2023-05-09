import threading
import time
from api import *
from bot import *

threading.Thread(target=run_flask).start()
run_discord()
