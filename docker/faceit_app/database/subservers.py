import logging

from cache.sub_servers import subservers_cache_add
from database.connection import get_connection


def get_all_subservers_from_db():
    conn = get_connection()
    cursor = conn.cursor()
    try:
        cursor.execute(
            (
                "SELECT "
                "id, ip, api_key, current_user_load, creation_date, location"
                " FROM subservers"
            )
        )
        rows = cursor.fetchall()
        return [
            {
                "id": row[0],
                "ip": row[1],
                "api_key": row[2],
                "current_user_load": row[3],
                "creation_date": row[4],
                "location": row[5],
            }
            for row in rows
        ]
    except Exception as e:
        logging.error(f"Error fetching subservers: {e}")
        return []
    finally:
        cursor.close()
        conn.close()


def add_subserver_to_db(ip: str, port: str, api_key: str, location: str):
    conn = get_connection()
    cursor = conn.cursor()
    try:
        cursor.execute(
            (
                "INSERT INTO subservers (ip, port, api_key, location)"
                " VALUES (%s, %s, %s, %s)"
            ),
            (
                ip,
                port,
                api_key,
                location,
            ),
        )
        conn.commit()
        newsub_id = cursor.lastrowid

        subservers_cache_add((newsub_id, ip, port, api_key))
        logging.info(
            f"✅ Subserver with IP {ip} (ID: {newsub_id}) created successfully."
        )
        return True

    except Exception as e:
        logging.error(f"❌ Failed to create a subserver with ip {ip}: {e}")
        return False

    finally:
        cursor.close()
        conn.close()


async def load_subservers():
    conn = get_connection()
    cursor = conn.cursor()
    entries = []
    try:
        cursor.execute("SELECT id, ip, api_key FROM subservers")
        rows = cursor.fetchall()
        entries = [(row[0], row[1], row[2]) for row in rows]
        for entry in entries:
            subservers_cache_add(entry)

    except Exception as e:
        logging.error(f"Failed to load subservers: {e}")
    finally:
        cursor.close()
        conn.close()
        logging.info(
            f"Successfully loaded {len(entries)} subserver credentials"
        )


def get_all_subserver_users(subserver_id: int):
    conn = get_connection()
    cursor = conn.cursor()
    try:
        cursor.execute(
            (
                "SELECT id, sub_end_day, faceit_id, faceit_username FROM users"
                " WHERE status = 1 AND subserver_id = %s"
            ),
            (subserver_id,),
        )
        rows = cursor.fetchall()
        return [
            {
                "id": row[0],
                "sub_end_day": row[1],
                "faceit_id": row[2],
                "faceit_username": row[3],
            }
            for row in rows
        ]
    except Exception as e:
        logging.error(
            f"Error fetching users for subserver {subserver_id}: {e}"
        )
        return []
    finally:
        cursor.close()
        conn.close()
