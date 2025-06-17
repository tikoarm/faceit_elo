import logging
import time
from datetime import datetime
from flask import jsonify
from cache import sub_servers

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


def json_default_datetime(obj):
    if isinstance(obj, datetime):
        return obj.strftime("%Y-%m-%d %H:%M:%S")
    return str(obj)


def is_token_valid(received_token: int, window=20):
    now = int(time.time())
    for offset in range(-window, window + 1):
        if dynamic_hash(now + offset) == received_token:
            return True
    return False


def dynamic_hash(ts: int) -> int:
    return int(((ts * 3.14) + 42) * 1.7) % 1000000007


def send_apikey_to_subserver(ip: str, api_key: str, port: str):
    token = dynamic_hash(int(time.time()))

    params = {"token": token, "api_key": api_key}

    try:
        response = requests.post(
            f"http://{ip}:{port}/install", params=params, timeout=5
        )
        data = response.json()

        if data.get("success") == "api_key is installed":
            return True
        else:
            logging.warning(
                "⚠️ Unexpected response from subserver %s: %s", ip, data
            )
            return False

    except requests.exceptions.HTTPError as e:
        logging.error("❌ HTTP error: %s", e)
    except ValueError as e:
        logging.error("❌ Failed to parse JSON: %s", e)
        logging.error("❌ Response text was: %s", response.text)
        return False


# Helper function for subserver API key and IP validation
def validate_subserver_access(request):
    apikey_param = request.args.get("api_key")
    if not apikey_param:
        return None, jsonify({"error": "API Key is required"}), 400

    api_key = apikey_param.strip()
    #client_ip = request.remote_addr
    client_ip = request.headers.get("X-Forwarded-For", request.remote_addr)

    validation_result = sub_servers.is_valid_subserver(client_ip, api_key)
    if validation_result is False:
        return None, jsonify({"error": "API Key is invalid"}), 403
    elif validation_result == "wrong_ip":
        error_text = f"API Key is bound to another IP. Your is: {client_ip}"
        return None, jsonify({"error": error_text}), 403

    subserver_id = sub_servers.get_subserver_id_by_ip_key(client_ip, api_key)
    if not subserver_id:
        return None, jsonify({"error": "There is an error with your api access"}), 403

    return subserver_id, None, None