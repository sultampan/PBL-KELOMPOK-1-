<?php
// 1. Panggil autoload.php dari Composer
require_once __DIR__ . '/../vendor/autoload.php';

// 2. Muat .env
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// 3. Ambil data dari .env
$host = $_ENV['DB_HOST'];
$port = $_ENV['DB_PORT'];
$dbname = $_ENV['DB_DATABASE'];
$user = $_ENV['DB_USERNAME'];
$password = $_ENV['DB_PASSWORD'];

// 4. Koneksi PDO
try {
    $dsn_pg = "pgsql:host=$host;port=$port;dbname=$dbname";
    $pdo = new PDO($dsn_pg, $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Koneksi gagal: " . $e->getMessage());
}
?>