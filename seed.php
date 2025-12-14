<?php
// seed.php
require_once __DIR__ . '/config/koneksi.php';

// Fungsi Load Env Manual
function loadEnv($path) {
    if (!file_exists($path)) throw new Exception("File .env tidak ditemukan di: $path");
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        list($name, $value) = explode('=', $line, 2);
        putenv(sprintf('%s=%s', trim($name), trim($value, '"\' ')));
        $_ENV[trim($name)] = trim($value, '"\' ');
    }
}

try {
    loadEnv(__DIR__ . '/config/.env'); 

    // === KONFIGURASI DARI .ENV ===
    $username       = getenv('ADMIN_USERNAME');
    $email          = getenv('ADMIN_EMAIL');
    $password_login = getenv('ADMIN_LOGIN_PASS');
    $password_email = getenv('ADMIN_EMAIL_PASS'); // Password App Gmail (Plaintext)
    
    // Validasi input
    if (!$username || !$email || !$password_login || !$password_email) {
        throw new Exception("Data di file .env belum lengkap (Username, Email, Pass Login, atau Pass Email kosong).");
    }

    // 1. Hash Password Login (TETAP DI-HASH BIAR AMAN)
    // Ini untuk login ke dashboard admin
    $hash_login = password_hash($password_login, PASSWORD_DEFAULT);

    // 2. Password Email (LANGSUNG PLAINTEXT)
    // Disimpan apa adanya agar bisa langsung dipakai PHPMailer tanpa dekripsi
    $plain_email_pass = $password_email;

    // 3. Simpan ke Database
    $check = $pdo->prepare("SELECT id_admin FROM admin WHERE username = ?");
    $check->execute([$username]);
    
    if ($check->rowCount() > 0) {
        // UPDATE: Timpa data lama dengan data baru (termasuk pass email plaintext)
        $sql = "UPDATE admin SET email = ?, password = ?, email_password = ? WHERE username = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$email, $hash_login, $plain_email_pass, $username]);
        
        echo "<h3>UPDATE SUKSES!</h3>";
        echo "Data Admin <b>$username</b> berhasil diperbarui.<br>";
    } else {
        // INSERT: Buat admin baru
        $sql = "INSERT INTO admin (username, email, password, email_password) VALUES (?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$username, $email, $hash_login, $plain_email_pass]);
        
        echo "<h3>INSERT SUKSES!</h3>";
        echo "Admin Baru <b>$username</b> berhasil dibuat.<br>";
    }

    echo "<ul>";
    echo "<li>Email Pengirim: $email</li>";
    echo "<li>Password Login: (Telah di-Hash demi keamanan)</li>";
    echo "<li>Password Email: (Disimpan sebagai teks biasa untuk SMTP)</li>";
    echo "</ul>";

} catch (Exception $e) {
    echo "<h3>Error:</h3> " . $e->getMessage();
}
?>