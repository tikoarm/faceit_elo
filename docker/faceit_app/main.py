import asyncio
import logging
from multiprocessing import Process

from cache.api_keys import load_api_keys
from logic.logger import setup_logger
from web.api import start_api

setup_logger()
logging.info("⏳ Please wait...")


if __name__ == "__main__":
    try:
        asyncio.run(load_api_keys())

        logging.info("⏳ Webserver is starting...")
        api_process = Process(target=start_api)
        api_process.start()

    except KeyboardInterrupt:
        print("Bot stopped.")
