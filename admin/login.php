<?php
// admin/login.php
if (session_status() === PHP_SESSION_NONE) session_start();

// jika sudah login langsung ke index
if (isset($_SESSION['admin']) && $_SESSION['admin'] === true) {
    header("Location: index.php");
    exit;
}

require_once __DIR__ . '/config/koneksi.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    // ambil user berdasarkan username (gunakan pg_query_params untuk keamanan)
    $res = pg_query_params($koneksi, "SELECT * FROM users WHERE username = $1 LIMIT 1", array($username));
    if ($res && pg_num_rows($res) > 0) {
        $user = pg_fetch_assoc($res);

        // jika kolom salt ada di DB
        $salt = $user['salt'] ?? '';
        if ($password === $user['password']) {
            // sukses login
            $_SESSION['admin'] = true;
            $_SESSION['username'] = $user['username'];
            $_SESSION['id_admin'] = $user['id'];
            header("Location: index.php");
            exit;
        }
    }
    $error = "⚠ Username atau password salah!";
}
?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Login - LAB AI Admin</title>
<style>
/* minimal styling */
body{font-family:Segoe UI, Tahoma, sans-serif;background:linear-gradient(135deg,#667eea,#764ba2);min-height:100vh;display:flex;align-items:center;justify-content:center}
.login-container{background:#fff;padding:32px;border-radius:12px;max-width:420px;width:100%;box-shadow:0 10px 30px rgba(0,0,0,0.15)}
h1{margin-bottom:6px}
.form-group{margin-bottom:14px}
input{width:100%;padding:10px;border-radius:8px;border:1px solid #ddd}
.btn{width:100%;padding:12px;border-radius:8px;border:none;background:#667eea;color:#fff;font-weight:600}
.error{background:#fee;color:#900;padding:10px;border-radius:8px;margin-bottom:12px}
</style>
</head>
<body>
<div class="login-container">
    <h1>LAB AI Admin</h1>
    <p style="color:#666;margin-bottom:16px">Laboratory of Applied Informatics</p>

    <?php if ($error): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
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
        <button class="btn" type="submit">🚀 Login</button>
    </form>
</div>
</body>
</html>