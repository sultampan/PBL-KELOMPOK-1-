<?php
<<<<<<< HEAD
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
=======
session_start();
require_once __DIR__ . '/../config/koneksi.php';

$timeout_error = null;
if (isset($_SESSION['login_error'])) {
    $timeout_error = $_SESSION['login_error'];
    unset($_SESSION['login_error']); // Hapus setelah ditampilkan
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = trim($_POST["username"]);
    $password = $_POST["password"];

    // Ambil user berdasarkan username
    $sql = "SELECT * FROM admin WHERE username = ? LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Hash ulang password login menggunakan salt dari DB
        $rehashed = hash_hmac("sha256", $user['salt'] . $password, "key-rahasia-opsional");

        // Cocokkan hash DB
        if (hash_equals($user["password"], $rehashed)) {

            $_SESSION["admin"] = true;
            $_SESSION["username"] = $user["username"];
            $_SESSION["id_admin"] = $user["id"];
            $_SESSION['last_activity'] = time();

>>>>>>> 4a06ff5f31c1902e91f194fec1a2f6811699fd3e
            header("Location: index.php");
            exit;
        }
    }
<<<<<<< HEAD
    $error = "⚠ Username atau password salah!";
}
?>
=======

    // Jika gagal
    $error = "Username atau password salah!";
}
?>

>>>>>>> 4a06ff5f31c1902e91f194fec1a2f6811699fd3e
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Login - LAB AI Admin</title>
<<<<<<< HEAD
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
=======
<link rel="stylesheet" href="assets/css/login.css">
</head>
<body>

<div class="login-container">
    <h1 style="margin-top: auto";>LAB AI Admin</h1>

    <?php if (!empty($timeout_error)): ?>
    <div class="alert alert-warning" style="background:#fff3cd; color:#856404; padding:10px; border-radius:5px; margin-bottom:15px; border: 1px solid #ffeeba;">
        <?= htmlspecialchars($timeout_error) ?>
    </div>
<?php endif; ?>

    <?php if (!empty($error)): ?>
>>>>>>> 4a06ff5f31c1902e91f194fec1a2f6811699fd3e
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="post" autocomplete="off">
        <div class="form-group">
            <label>Username</label>
            <input type="text" name="username" required autofocus>
        </div>
<<<<<<< HEAD
=======

>>>>>>> 4a06ff5f31c1902e91f194fec1a2f6811699fd3e
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" required>
        </div>
<<<<<<< HEAD
        <button class="btn" type="submit">🚀 Login</button>
    </form>
</div>
</body>
</html>
=======

        <button class="btn-login" type="submit">Login</button>
    </form>
</div>

</body>
</html>
>>>>>>> 4a06ff5f31c1902e91f194fec1a2f6811699fd3e
