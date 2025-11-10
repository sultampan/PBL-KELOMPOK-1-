<?php
/*
 * File: koneksi.php (Versi manual bersih)
 */
date_default_timezone_set("Asia/Jakarta");

// Path ke file .env (naik 2 level)
$envPath = __DIR__ . '/../../.env';

if (!file_exists($envPath)) {
    // Paksa tampilkan error
    ini_set('display_errors', 1); error_reporting(E_ALL);
    die("Koneksi Gagal: File .env tidak ditemukan. Path dicari di: " . htmlspecialchars($envPath));
}

$lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
if ($lines === false) {
    // Paksa tampilkan error
    ini_set('display_errors', 1); error_reporting(E_ALL);
    die("Koneksi Gagal: Gagal membaca file .env. Cek file permissions.");
}

$config = [];
foreach ($lines as $line) {
    // Lewati komentar
    if (strpos(trim($line), '#') === 0) {
        continue;
    }
    
    // Pecah di tanda =
    if (strpos($line, '=') !== false) {
        list($key, $value) = explode('=', $line, 2);
        $key = trim($key);
        // Hapus spasi dan tanda petik di awal/akhir nilai
        $value = trim($value, " \t\n\r\0\x0B'\"");
        
        $config[$key] = $value;
    }
}

// Ambil data
$host = $config['DB_HOST'] ?? null;
$port = $config['DB_PORT'] ?? null;
$dbname = $config['DB_DATABASE'] ?? null;
$user = $config['DB_USERNAME'] ?? null;
$password = $config['DB_PASSWORD'] ?? null;

if (!$host || !$dbname || !$user) {
    // Paksa tampilkan error
    ini_set('display_errors', 1); error_reporting(E_ALL);
    die("Koneksi Gagal: Pastikan DB_HOST, DB_DATABASE, dan DB_USERNAME ada di file .env.");
}

try {
    $dsn_pg = "pgsql:host=$host;port=$port;dbname=$dbname";
    $pdo = new PDO($dsn_pg, $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    // Paksa tampilkan error
    ini_set('display_errors', 1); error_reporting(E_ALL);
    die("Koneksi Gagal ke Database: " . $e->getMessage());
}
?>