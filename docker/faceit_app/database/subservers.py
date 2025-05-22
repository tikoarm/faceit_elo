import logging
from database.connection import get_connection
from cache.sub_servers import subservers_cache_add


def get_all_subservers_from_db():
    conn = get_connection()
    cursor = conn.cursor()
    try:
        cursor.execute("SELECT id, ip, api_key, current_user_load, creation_date, location FROM subservers")
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


def add_subserver_to_db(ip: str, api_key: str, location: str):
    conn = get_connection()
    cursor = conn.cursor()
    try:
        cursor.execute(
            (
                "INSERT INTO subservers (ip, api_key, location)"
                " VALUES (%s, %s, %s)"
            ),
            (
                ip,
                api_key,
                location,
            ),
        )
        conn.commit()

        subservers_cache_add((ip, api_key))
        logging.info(
            f"✅ Subserver with IP {ip} created successfully."
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
        cursor.execute("SELECT ip, api_key FROM subservers")
        rows = cursor.fetchall()
        entries = [(row[0], row[1]) for row in rows]
        for entry in entries:
            subservers_cache_add(entry)

    except Exception as e:
        logging.error(f"Failed to load subservers: {e}")
    finally:
        cursor.close()
        conn.close()
        logging.info(f"Successfully loaded {len(entries)} subserver credentials")