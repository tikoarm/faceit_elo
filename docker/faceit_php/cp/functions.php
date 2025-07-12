<?php

function obfuscate_text(string $text): string
{
    $len = strlen($text);

    if ($len > 6) {
        return substr($text, 0, 3) . '...' . substr($text, -3);
    } elseif ($len > 2) {
        return substr($text, 0, 2) . str_repeat('*', $len - 2);
    } else {
        return $text;
    }
}
function convert_timestamp(string $timestamp): string
{
    try {
        $dt = new DateTime($timestamp);
        return $dt->format('d.m.Y H:i:s');
    } catch (Exception $e) {
        return $timestamp;
    }
}
function convert_unix_timestamp(int|string $timestamp): string
{
    try {
        $timestamp = (int)$timestamp;
        $dt = (new DateTime('@' . $timestamp))->setTimezone(new DateTimeZone('Europe/Berlin'));
        return $dt->format('d.m.Y H:i:s') . ' (Berlin)';
    } catch (Exception $e) {
        return (string)$timestamp;
    }
}