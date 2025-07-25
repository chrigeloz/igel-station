<?php
$host = '127.0.0.1';
$port = '3306';
$dbname = 'chribhtl_animalrescue';
$user = 'chribhtl_animalrescue';
$pass = 'V$KQza5^Bhp3';

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    error_log('Database connection failed: ' . $e->getMessage());
    die('Database connection failed. Please try again later.');
}
