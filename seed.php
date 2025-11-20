<?php
require_once __DIR__ . '/config/koneksi.php';

// Data admin
$username = "admin2";
$password = "admin2";

// Generate salt (32 bytes, aman)
$salt = bin2hex(random_bytes(32)); 

// Hash manual
$hashed = hash_hmac("sha256", $salt . $password, "key-rahasia-opsional");

// Insert ke database
$sql = "INSERT INTO admin (username, password, salt) VALUES (?, ?, ?)";
$stmt = $pdo->prepare($sql);
$stmt->execute([$username, $hashed, $salt]);

echo "Admin berhasil dibuat!";
