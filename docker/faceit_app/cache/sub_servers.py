import logging
import secrets

from database.connection import get_connection

subservers_cache = set()  # stores (ip, api_key) tuples


def generate_api_key():
    while True:
        new_key = secrets.token_hex(16)
        if all(new_key != key for _, key in subservers_cache):
            return new_key

def subservers_cache_add(entry):
    subservers_cache.add(entry)
    return True

def is_valid_api_key(ip, api_key):
    return (ip, api_key) in subservers_cache

def get_cached_subservers():
    return [{"ip": ip, "api_key": api_key} for ip, api_key in subservers_cache]