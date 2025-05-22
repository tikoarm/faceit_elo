import logging
from datetime import datetime
import requests


def format_seconds(seconds: int) -> str:
    days = seconds // 86400
    hours = (seconds % 86400) // 3600
    minutes = (seconds % 3600) // 60
    secs = seconds % 60

    parts = []
    if days > 0:
        parts.append(f"{days} day{'s' if days != 1 else ''}")
    if hours > 0:
        parts.append(f"{hours} hour{'s' if hours != 1 else ''}")
    if minutes > 0:
        parts.append(f"{minutes} minute{'s' if minutes != 1 else ''}")
    if secs > 0 or not parts:
        parts.append(f"{secs} second{'s' if secs != 1 else ''}")

    return ", ".join(parts)


def get_location_by_ip(ip: str) -> str:
    try:
        response = requests.get(f"https://ipwho.is/{ip}", timeout=3)
        data = response.json()
        if data.get("success", False):
            country = data.get("country", "")
            city = data.get("city", "")
            return f"{country}, {city}" if city else country
    except Exception as e:
        logging.warning(f"IP location lookup failed for {ip}: {e}")

    return "Unknown"