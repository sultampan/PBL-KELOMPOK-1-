<?php
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

            header("Location: index.php");
            exit;
        }
    }

    // Jika gagal
    $error = "Username atau password salah!";
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
    <h1 style="margin-top: auto";>LAB AI Admin</h1>

    <?php if (!empty($timeout_error)): ?>
    <div class="alert alert-warning" style="background:#fff3cd; color:#856404; padding:10px; border-radius:5px; margin-bottom:15px; border: 1px solid #ffeeba;">
        <?= htmlspecialchars($timeout_error) ?>
    </div>
<?php endif; ?>

    <?php if (!empty($error)): ?>
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

        <button class="btn-login" type="submit">Login</button>
    </form>
</div>

</body>
</html>
