import secrets
import ipaddress

subservers_cache = set()  # stores (id, ip, port, api_key) tuples


def generate_api_key():
    while True:
        new_key = secrets.token_hex(16)
        if all(new_key != key for _, _, _, key in subservers_cache):
            return new_key


def subservers_cache_add(entry):
    # entry is expected to be a tuple (id, ip, port, api_key)
    subservers_cache.add(entry)
    return True


def is_valid_subserver(ip, api_key):
    for _, cached_ip, _, cached_key in subservers_cache:
        if cached_key == api_key:
            if cached_ip == ip:
                return True
            elif is_docker_internal_ip(ip):
                # If the IP is a Docker internal IP, we allow it to match any cached IP
                return True
            else:
                return "wrong_ip"
    return False


def get_cached_subservers():
    return [
        {"id": sid, "ip": ip, "port": port, "api_key": api_key}
        for sid, ip, port, api_key in subservers_cache
    ]


def get_subserver_id_by_ip_key(ip, api_key):
    for sid, cached_ip, _, cached_key in subservers_cache:
        if cached_ip == ip and cached_key == api_key:
            return sid
    return None

def is_docker_internal_ip(ip: str) -> bool:
    try:
        ip_obj = ipaddress.ip_address(ip)
        docker_networks = [
            ipaddress.ip_network('172.17.0.0/16'),
            ipaddress.ip_network('172.18.0.0/16'),
            ipaddress.ip_network('172.19.0.0/16'),
            ipaddress.ip_network('172.20.0.0/16'),
            ipaddress.ip_network('172.21.0.0/16'),
            ipaddress.ip_network('172.22.0.0/16'),
            ipaddress.ip_network('172.23.0.0/16'),
            ipaddress.ip_network('172.24.0.0/16'),
            ipaddress.ip_network('172.25.0.0/16'),
            ipaddress.ip_network('172.26.0.0/16'),
            ipaddress.ip_network('172.27.0.0/16'),
            ipaddress.ip_network('172.28.0.0/16'),
            ipaddress.ip_network('172.29.0.0/16'),
            ipaddress.ip_network('172.30.0.0/16'),
            ipaddress.ip_network('172.31.0.0/16'),
        ]
        return any(ip_obj in net for net in docker_networks)
    except ValueError:
        return False  # Невалидный IP
    