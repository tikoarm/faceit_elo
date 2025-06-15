import logging

from database.connection import get_connection


def insert_match(faceit_id, elo_before, elo_after, elo_difference, map_name, win, nickname, gameid):
    try:
        conn = get_connection()
        cursor = conn.cursor()

        query = """
            INSERT INTO matches (userid, elo_before, elo_after, elo_difference, map, win, nickname, gameid)
            VALUES (
                (SELECT id FROM users WHERE faceit_id = %s LIMIT 1),
                %s, %s, %s, %s, %s, %s, %s
            )
        """
        values = (faceit_id, elo_before, elo_after, elo_difference, map_name, win, nickname, gameid)

        cursor.execute(query, values)
        conn.commit()
        return True, True #бд, уведомление
    
    except Exception as e:
        logging.error(f"❌ Ошибка при вставке матча: {e}", exc_info=True)
    finally:
        if cursor:
            cursor.close()
        if conn:
            conn.close()

    return False, False #бд, уведомление