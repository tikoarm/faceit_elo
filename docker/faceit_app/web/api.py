import json
import os
import time
from collections import OrderedDict
from datetime import datetime
from zoneinfo import ZoneInfo

from cache import sub_servers
from database.subservers import (
    add_subserver_to_db,
    get_all_subserver_users,
    get_all_subservers_from_db,
)
from dotenv import dotenv_values, load_dotenv
from flask import Flask, Response, jsonify, request
from logic.functions import (
    format_seconds,
    get_location_by_ip,
    json_default_datetime,
    send_apikey_to_subserver,
    validate_subserver_access,
)
from logic.subserver_api.matches import user_finished_match

app_start_time = time.time()

load_dotenv()
admin_key = os.getenv("API_ADMIN_KEY")
if not admin_key:
    raise ValueError(
        "Missing API Admin Key credential in environment variables."
    )

app = Flask(__name__)


@app.route("/subservers/add/match", methods=["GET"])
def add_match_to_user():
    #subserver_id, error_response, status_code = validate_subserver_access(request)
    #if error_response or not subserver_id:
    #    return error_response, status_code

    userid = request.args.get("userid")
    elo_before = request.args.get("elo_before")
    elo_after = request.args.get("elo_after")
    win = request.args.get("win")
    map_name = request.args.get("map")
    nickname = request.args.get("nickname")
    gameid = request.args.get("gameid")

    missing_params = []
    if not userid:
        missing_params.append("User ID")
    if not elo_before:
        missing_params.append("Players ELO Before")
    if not elo_after:
        missing_params.append("Players ELO After")
    if win is None:
        missing_params.append("Win?")
    else:
        win = win.lower() == "true"
    if not map_name:
        missing_params.append("Game Map")
    if not nickname:
        missing_params.append("Players NickName")
    if not gameid:
        missing_params.append("Game ID")

    if missing_params:
        return {
            "error": "Missing required parameters",
            "missing": missing_params
        }, 400
    
    try:
        elo_before = int(elo_before)
        elo_after = int(elo_after)
    except (TypeError, ValueError):
        return {
            "error": "ELO values must be valid integers"
        }, 400

    elo_difference = elo_after - elo_before
    if elo_difference < 1:
        elo_difference *= -1;
    
    success_bd, success_tg, success_mainfunc = user_finished_match(userid, elo_before, elo_after, elo_difference, map_name, win, nickname, gameid)

    response_data = OrderedDict(
        [
            ("status", "added"),
            ("success_bd", "yes" if success_bd else "no"),
            ("success_tg", "yes" if success_tg else "no"),
            ("success_mainfunc", "yes" if success_mainfunc else "no"),
        ]
    )
    return Response(
        response=json.dumps(response_data, default=json_default_datetime),
        status=200,
        mimetype="application/json",
    )


@app.route("/subservers/get/settings/users", methods=["GET"])
def get_subservers_users():
    subserver_id, error_response, status_code = validate_subserver_access(request)
    if error_response or not subserver_id:
        return error_response, status_code

    userslist = get_all_subserver_users(subserver_id)
    response_data = OrderedDict(
        [
            ("status", "success"),
            ("users_count", len(userslist)),
            ("users", userslist),
        ]
    )

    return Response(
        response=json.dumps(response_data, default=json_default_datetime),
        status=200,
        mimetype="application/json",
    )


@app.route("/subservers/get/cache", methods=["GET"])
def get_subservers_cache():
    admkey_param = request.args.get("admin_key")
    if not admkey_param:
        return jsonify({"error": "Admin Key is required"}), 400

    adm_key = admkey_param.strip()
    if adm_key != admin_key:
        return jsonify({"error": f"Admin Key '{adm_key}' is Invalid!"}), 400

    cache_list = sub_servers.get_cached_subservers()

    return Response(
        response=json.dumps(
            {
                "status": "success",
                "count": len(cache_list),
                "cached_subservers": cache_list,
            },
            indent=2,
        ),
        status=200,
        mimetype="application/json",
    )


@app.route("/subservers/get/all", methods=["GET"])
def get_all_subservers():
    admkey_param = request.args.get("admin_key")
    if not admkey_param:
        return jsonify({"error": "Admin Key is required"}), 400

    adm_key = admkey_param.strip()
    if adm_key != admin_key:
        return jsonify({"error": f"Admin Key '{adm_key}' is Invalid!"}), 400

    subservers = get_all_subservers_from_db()
    response_data = OrderedDict(
        [
            ("status", "success"),
            ("subserver_count", len(subservers)),
            ("subservers", subservers),
        ]
    )

    return Response(
        response=json.dumps(response_data, default=json_default_datetime),
        status=200,
        mimetype="application/json",
    )


@app.route("/subservers/add", methods=["POST"])
def add_subserver():
    admkey_param = request.args.get("admin_key")
    if not admkey_param:
        return jsonify({"error": "Admin Key is required"}), 400

    adm_key = admkey_param.strip()
    if adm_key != admin_key:
        return jsonify({"error": f"Admin Key '{adm_key}' is Invalid!"}), 400

    ip_param = request.args.get("ip")
    if not ip_param:
        return jsonify({"error": "Subserver IP is required"}), 400
    ip = ip_param.strip()

    start_time = time.time()
    client_ip = request.remote_addr

    api_key = sub_servers.generate_api_key()
    location = get_location_by_ip(ip)
    result = add_subserver_to_db(ip, api_key, location)
    installation = send_apikey_to_subserver(ip, api_key)

    response_data = OrderedDict(
        [
            ("status", "processed"),
            ("vps_ip", ip),
            ("vps_api_key", api_key),
            ("db_result", "done" if result else "failed"),
            ("request_origin_ip", client_ip),
            ("response_time_sec", round(time.time() - start_time, 4)),
            ("vps_ip_location", location),
            ("subserver_installation", "done" if installation else "failed"),
        ]
    )

    return Response(
        response=json.dumps(response_data),
        status=200,
        mimetype="application/json",
    )


@app.route("/logs/view", methods=["GET"])
def logs_view_process():
    admkey_param = request.args.get("admin_key")
    if not admkey_param:
        return jsonify({"error": "Admin Key is required"}), 400

    adm_key = admkey_param.strip()
    if adm_key != admin_key:
        error_message = f"Admin Key '{adm_key}' is Invalid!"
        return jsonify({"error": error_message}), 400

    logtype_param = request.args.get("log_type")
    if not logtype_param:
        return jsonify({"error": "Log Type is required"}), 400

    try:
        env_values = dotenv_values()
    except Exception:
        env_values = {}

    sensitive_values = list(
        {
            v
            for v in list(env_values.values()) + list(os.environ.values())
            if v and len(v) >= 6
        }
    )

    # Mask sensitive values in log lines
    def mask_sensitive(line, sensitive_values):
        for value in sensitive_values:
            if value and value in line:
                line = line.replace(value, "--hidden--")
        return line

    log_files = {
        "error": "logs/error.log",
        "warning": "logs/warning.log",
        "info": "logs/info.log",
    }

    if logtype_param not in log_files:
        return jsonify({"error": "Log Type is incorrect"}), 400

    file_path = log_files[logtype_param]
    try:
        with open(file_path, "r") as f:
            raw_lines = f.readlines()
            masked_lines = [
                mask_sensitive(line, sensitive_values) for line in raw_lines
            ]
            lines = masked_lines[-25:]

    except FileNotFoundError:
        return (
            jsonify({"error": f"Log file for '{logtype_param}' not found"}),
            404,
        )

    return jsonify({"log_type": logtype_param, "lines": lines})


@app.route("/health", methods=["GET"])
def health():
    """
    Health check endpoint returning status of key components and version.
    """

    berlin_time = datetime.now(ZoneInfo("Europe/Berlin"))
    result = {
        "status": "ok",
        "timestamp": berlin_time.isoformat(),
        "timezone": "Europe/Berlin",
        "uptime": format_seconds(int(time.time() - app_start_time)),
    }

    # 1) Version
    version_path = os.path.join(os.getcwd(), "VERSION")
    try:
        with open(version_path, "r") as f:
            result["version"] = f.read().strip()
    except Exception:
        result["version"] = "unknown"
        result["status"] = "degraded"

    # 2) Database
    try:
        from database.connection import get_connection

        conn = get_connection()
        conn.close()
        result["database"] = "ok"
    except Exception as e:
        result["database"] = f"error: {e}"
        result["status"] = "degraded"

    return jsonify(result)


def start_api():
    app.run(host="0.0.0.0", port=5050, debug=False)  # nosec
