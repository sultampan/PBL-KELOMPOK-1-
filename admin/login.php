<?php
session_start();
require_once __DIR__ . '/../config/koneksi.php';

// Cek jika user sudah login, langsung lempar ke index
if (isset($_SESSION["admin"]) && $_SESSION["admin"] === true) {
    header("Location: index.php");
    exit;
}

$timeout_error = null;
if (isset($_SESSION['login_error'])) {
    $timeout_error = $_SESSION['login_error'];
    unset($_SESSION['login_error']); // Hapus setelah ditampilkan
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = trim($_POST["username"]);
    $password = $_POST["password"];

    try {
        // 1. Ambil user berdasarkan username
        // Kita tidak perlu mengambil salt lagi, cukup ambil hash passwordnya
        $sql = "SELECT * FROM admin WHERE username = ? LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // 2. Cek apakah user ada
        if ($user) {
            // 3. Verifikasi Password
            // password_verify otomatis mencocokkan inputan dengan hash di database
            if (password_verify($password, $user["password"])) {

                // Login Sukses
                $_SESSION["admin"] = true;
                $_SESSION["username"] = $user["username"];
                
                // Pastikan nama kolom ID sesuai tabel (id_admin)
                $_SESSION["id_admin"] = $user["id_admin"]; 
                
                $_SESSION['last_activity'] = time();

                header("Location: index.php");
                exit;
            }
        }

        // Jika user tidak ditemukan ATAU password salah
        $error = "Username atau password salah!";
        
    } catch (PDOException $e) {
        $error = "Terjadi kesalahan sistem: " . $e->getMessage();
    }
}
?>

<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Login - LAB AI Admin</title>
<link rel="stylesheet" href="assets/css/login.css">
</head>
<body>

<div class="login-container">
    <h1 style="margin-top: auto;">LAB AI Admin</h1>

    <?php if (!empty($timeout_error)): ?>
    <div class="alert alert-warning" style="background:#fff3cd; color:#856404; padding:10px; border-radius:5px; margin-bottom:15px; border: 1px solid #ffeeba;">
        <?= htmlspecialchars($timeout_error) ?>
    </div>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
        <div class="error" style="background:#f8d7da; color:#721c24; padding:10px; border-radius:5px; margin-bottom:15px; border: 1px solid #f5c6cb;">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form method="post" autocomplete="off">
        <div class="form-group">
            <label>Username</label>
            <input type="text" name="username" required autofocus>
        </div>

        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" required>
        </div>

        <button class="btn-login" type="submit">Login</button>
    </form>
</div>

</body>
</html>