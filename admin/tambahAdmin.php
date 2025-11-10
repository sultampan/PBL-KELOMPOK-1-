<?php
// 1. Panggil koneksi (pathnya dari folder 'admin' ke 'config')
require_once __DIR__ . '/config/koneksi.php';
echo "<pre>";

// 2. Data admin yang mau di buat
$username_baru = "";
$password_mentah = ""; // Ganti ini nanti

// 3. Hash password
$password_hash = password_hash($password_mentah, PASSWORD_DEFAULT);

// 4. Cek dulu, jangan sampai username-nya duplikat
try {
    $sql_cek = "SELECT COUNT(*) FROM users WHERE username = ?";
    $stmt_cek = $pdo->prepare($sql_cek);
    $stmt_cek->execute([$username_baru]);
    
    if ($stmt_cek->fetchColumn() > 0) {
        die("ERROR: Username '$username_baru' sudah ada di database.\n");
    }

    // 5. Masukkan ke database
    $sql_insert = "INSERT INTO users (username, password) VALUES (?, ?)";
    $stmt_insert = $pdo->prepare($sql_insert);
    
    // Eksekusi
    if ($stmt_insert->execute([$username_baru, $password_hash])) {
        echo "SUKSES!\n";
        echo "Admin baru berhasil dibuat.\n";
        echo "Username: $username_baru\n";
        echo "Password: $password_mentah\n\n";
        echo "Silakan login di halaman login.php";
    } else {
        echo "ERROR: Gagal memasukkan data.";
    }

} catch (PDOException $e) {
    die("KONEKSI DATABASE GAGAL: " . $e->getMessage());
}

echo "</pre>";
?>