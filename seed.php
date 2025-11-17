<?php
// seed.php
// (PENTING: Jalankan file ini HANYA dari terminal, BUKAN browser)

// 1. Panggil koneksi
require_once __DIR__ . '/config/koneksi.php';

echo "Memulai script seeder...\n"; // \n = baris baru di terminal

try {
    // 2. Tentukan data admin
    $admin_user = 'admin3'; // username
    $admin_pass = 'admin3'; // password

    // 3. HASH passwordnya (WAJIB)
    $hashed_pass = password_hash($admin_pass, PASSWORD_DEFAULT);

    // 4. Masukkan ke database (TANPA CEK)
    $sql = "INSERT INTO users (username, password) VALUES (?, ?)";
    $stmt = $pdo->prepare($sql);
    
    if ($stmt->execute([$admin_user, $hashed_pass])) {
        echo "SUKSES! Admin baru berhasil dibuat.\n";
        echo "   Username: $admin_user\n";
        echo "   Password: $admin_pass (Ganti ini di production!)\n";
    } else {
        echo "Gagal memasukkan data ke database.\n";
    }

} catch (PDOException $e) {
    echo "ERROR DATABASE: " . $e->getMessage() . "\n";
}
?>