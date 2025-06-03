# Получить player_id по nickname
async def get_player_id_by_nickname(nickname):
    url = f"{BASE_URL}/players"
    params = {"nickname": nickname}
    async with httpx.AsyncClient() as client:
        resp = await client.get(url, headers=HEADERS, params=params)
        if resp.status_code != 200:
            logging.error(f"Ошибка получения player_id по nickname: {resp.status_code} {resp.text}")
            return None
        data = resp.json()
        player_id = data.get("player_id")
        if not player_id:
            logging.info(f"Не найден player_id для nickname: {nickname}")
            return None
        return player_id