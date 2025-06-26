import os

from aiogram import Bot, Dispatcher, types
from dotenv import load_dotenv

load_dotenv()
tg_token = os.getenv("TG_TOKEN")
if not tg_token:
    raise ValueError("Missing Telegram credential in environment variables.")

bot = Bot(token=tg_token)
dp = Dispatcher(bot)


@dp.message_handler(commands=["start"])
async def cmd_start(message: types.Message):
    await bot.send_message(message.from_user.id, "Hello World!")


async def sendmessage(userid, text):
    await bot.send_message(userid, text)


async def start():
    await dp.start_polling()
