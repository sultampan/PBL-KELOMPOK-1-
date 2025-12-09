<?php
require_once __DIR__ . '/config/koneksi.php';

try {
    // === KONFIGURASI ===
    $username       = "admin";
    $email          = "tes@example.com";
    $password_login = "admin";      // Password Login Admin
    $password_email = "tes"; // Password App Gmail/SMTP

    // Key Enkripsi (SIMPAN INI DI FILE CONFIG, JANGAN HILANG!)
    // Ini kuncinya. Kalau hilang, password email tidak bisa dibuka lagi.
    $kunci_rahasia  = "KunciRahasiaDapur1234567890"; 
    $cipher_method  = "AES-256-CBC";

    // === 1. PROSES DATA ===
    
    // A. Password Login -> Pakai HASH (Satu Arah)
    $hash_login = password_hash($password_login, PASSWORD_DEFAULT);

    // B. Password Email -> Pakai ENKRIPSI (Dua Arah)
    $iv_length = openssl_cipher_iv_length($cipher_method);
    $iv        = openssl_random_pseudo_bytes($iv_length); // Buat pengacak
    $encrypted = openssl_encrypt($password_email, $cipher_method, $kunci_rahasia, 0, $iv);
    
    // Gabungkan IV dan Hasil Enkripsi dengan pemisah "::" lalu encode ke base64
    // Format simpan: Base64(IV::EncryptedData)
    $token_email_aman = base64_encode($iv . "::" . $encrypted);

    // === 2. INSERT KE DATABASE ===
    $sql = "INSERT INTO admin (username, email, password, email_password) VALUES (?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$username, $email, $hash_login, $token_email_aman]);

    echo "Sukses!";
    echo "Password Admin: Di-Hash (Aman, tidak bisa dibaca)";
    echo "Password Email: Di-Enkripsi (Aman, tapi bisa dikembalikan saat kirim email)";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
