import logging

from database.connection import get_connection


async def add_user_to_db(telegram_id: int, name: str, source: str):
    prefix = "source_"
    if source and source.startswith(prefix):
        source = source[len(prefix) :]
        source = source.replace("_", ".")
    else:
        source = "Unknown"

    conn = get_connection()
    cursor = conn.cursor()
    try:
        cursor.execute(
            (
                "INSERT INTO users (telegram_id, name, source)"
                " VALUES (%s, %s, %s)"
            ),
            (
                telegram_id,
                name,
                source,
            ),
        )
        conn.commit()
        logging.info(
            f"✅ User {telegram_id} ({name}) registered successfully."
        )
        return True

    except Exception as e:
        logging.error(f"❌ Failed to register user {telegram_id}: {e}")
        return False

    finally:
        cursor.close()
        conn.close()


async def get_internal_user_id(telegram_id: int) -> int | None:
    conn = get_connection()
    cursor = conn.cursor()
    try:
        cursor.execute(
            "SELECT id FROM users WHERE telegram_id = %s", (telegram_id,)
        )
        row = cursor.fetchone()
        return row[0] if row else None
    finally:
        cursor.close()
        conn.close()


async def get_user_name_by_telegramid(telegram_id: int) -> str | None:
    conn = get_connection()
    cursor = conn.cursor()
    try:
        cursor.execute(
            "SELECT name FROM users WHERE telegram_id = %s", (telegram_id,)
        )
        row = cursor.fetchone()
        return row[0] if row else None
    finally:
        cursor.close()
        conn.close()

async def get_telegramid_by_faceitid(faceit_id: int) -> str | None:
    conn = get_connection()
    cursor = conn.cursor()
    try:
        cursor.execute(
            "SELECT telegram_id FROM users WHERE faceit_id = %s", (faceit_id,)
        )
        row = cursor.fetchone()
        return row[0] if row else None
    finally:
        cursor.close()
        conn.close()