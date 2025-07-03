import asyncio
import logging
import requests
from database.matches import insert_match
from database.users import get_telegramid_by_faceitid
from telegram.main import sendmessage
import os
from dotenv import load_dotenv

load_dotenv()
api_admin_key = os.getenv("API_ADMIN_KEY")

def user_finished_match(
    subserver_id,
    userid,
    elo_before,
    elo_after,
    elo_difference,
    map,
    win,
    nickname,
    gameid,
):
    try:
        message = (
            "\n"
            "============================================================\n"
            f"User {nickname} [{userid}] just finished his match on {map}\n"
            f"ELO Before: {elo_before} | Elo After: {elo_after}"
            f" | Difference: {elo_difference}\n"
            f"Win: {'True' if win == True else 'False'}\n"
            f"GameID: {gameid}\n"
            f"Subserver ID: {subserver_id}\n"
            "============================================================\n"
        )

        success_bd, success_tg = insert_match(
            userid,
            elo_before,
            elo_after,
            elo_difference,
            map,
            win,
            nickname,
            gameid,
        )

        url = 'http://faceit_webanim/send_update.php'
        data = {
            'faceit_id': userid,
            'elo': elo_before,
            'elo_diff': int(elo_after-elo_before),
            'api_key': api_admin_key
        }
        success_animation = False

        try:
            response = requests.post(url, data=data, timeout=10)

            if response.status_code == 200:
                content = response.text.strip().lower()
                if 'authorized' in content or 'success' in content:
                    success_animation = True
                else:
                    success_animation = False
            else:
                success_animation = False
        except requests.RequestException as e:
            logging.error(f"Request error: {e}")
            success_animation = False

        telegram_id = asyncio.run(get_telegramid_by_faceitid(userid))
        asyncio.run(sendmessage(telegram_id, message))
        return success_bd, success_tg, success_animation

    except Exception as e:
        logging.error(
            f"❌ Ошибка при в user_finished_match: {e}", exc_info=True
        )

    return False, False, False
