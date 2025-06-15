import logging
from database.matches import insert_match
from database.users import get_telegramid_by_faceitid
from telegram.main import sendmessage
import asyncio

def user_finished_match(userid, elo_before, elo_after, elo_difference, map, win, nickname, gameid):
    try:
        message = (
            "\n"
            "============================================================\n"
            f"User {nickname} [{userid}] just finished his match on {map}\n"
            f"ELO Before: {elo_before} | Elo After: {elo_after} | Difference: {elo_difference}\n"
            f"Win: {'True' if win == True else 'False'}\n"
            f"GameID: {gameid}\n"
            "============================================================\n"
        )

        success_bd, success_tg = insert_match(userid, elo_before, elo_after, elo_difference, map, win, nickname, gameid)

        telegram_id = asyncio.run(get_telegramid_by_faceitid(userid))
        asyncio.run(sendmessage(telegram_id, message))
        return success_bd, success_tg, True
    
    except Exception as e:
        logging.error(f"❌ Ошибка при вuser_finished_match: {e}", exc_info=True)

    return False, False, False