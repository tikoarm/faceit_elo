import secrets

subservers_cache = set()  # stores (id, ip, api_key) tuples


def generate_api_key():
    while True:
        new_key = secrets.token_hex(16)
        if all(new_key != key for _, _, key in subservers_cache):
            return new_key


def subservers_cache_add(entry):
    # entry is expected to be a tuple (id, ip, api_key)
    subservers_cache.add(entry)
    return True


def is_valid_subserver(ip, api_key):
    for _, cached_ip, cached_key in subservers_cache:
        if cached_key == api_key:
            if cached_ip == ip:
                return True
            else:
                return "wrong_ip"
    return False


def get_cached_subservers():
    return [
        {"id": sid, "ip": ip, "api_key": api_key}
        for sid, ip, api_key in subservers_cache
    ]


def get_subserver_id_by_ip_key(ip, api_key):
    for sid, cached_ip, cached_key in subservers_cache:
        if cached_ip == ip and cached_key == api_key:
            return sid
    return None
