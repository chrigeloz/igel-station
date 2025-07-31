<?php
$host = '127.0.0.1';
$port = '3306';
$dbname = 'chribhtl_igel';
$user = 'chribhtl_igel';
$pass = 'XfsF0$BhAVYB';

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    error_log('Database connection failed: ' . $e->getMessage());
    die('Database connection failed. Please try again later.');
}
