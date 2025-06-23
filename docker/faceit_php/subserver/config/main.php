<?php

function getDatabaseConnection(): ?mysqli {
    $host = $_ENV['MYSQL_HOST'] ?? 'localhost';
    $port = $_ENV['MYSQL_PORT'] ?? '3306';
    $dbname = $_ENV['MYSQL_DATABASE'] ?? '';
    $username = $_ENV['MYSQL_USER'] ?? 'root';
    $password_db = $_ENV['MYSQL_PASSWORD'] ?? '';
    
    $conn = new mysqli($host, $username, $password_db, $dbname, (int)$port);
    if ($conn->connect_error) {
        return null;
    }
    return $conn;
}