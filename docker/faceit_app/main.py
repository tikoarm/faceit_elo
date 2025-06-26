import asyncio
import logging
import time
from multiprocessing import Process

from database.subservers import load_subservers
from logic.logger import setup_logger
from telegram import main as telegram
from web.api import start_api

setup_logger()
logging.info("⏳ Please wait...")


async def main():
    await asyncio.gather(telegram.start())


if __name__ == "__main__":
    try:
        logging.info("⏳ Waiting for MySQL...")
        time.sleep(5)

        asyncio.run(load_subservers())

        logging.info("⏳ Webserver is starting...")
        api_process = Process(target=start_api)
        api_process.start()

        asyncio.run(main())

    except KeyboardInterrupt:
        print("Bot stopped.")
