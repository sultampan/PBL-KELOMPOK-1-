<?php
// admin/index.php

// (Opsional) Tambahkan ini jika masih blank putih, hapus jika sudah normal
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) session_start();

// proteksi
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header("Location: login.php");
    exit;
}

// logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit;
}

require_once __DIR__ . '/config/koneksi.php';

// sanitize page param
$page = isset($_GET['page']) ? preg_replace('/[^a-z0-9_]/', '', $_GET['page']) : 'home';
?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Admin Dashboard - LAB AI</title>
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:Segoe UI, Tahoma, sans-serif;background:#f0f2f5}
.container{display:flex;min-height:100vh}
.sidebar{width:250px;background:#2c3e50;color:#fff;padding:20px}
.sidebar a{color:#fff;text-decoration:none;display:block;padding:10px;border-radius:6px;margin-bottom:8px}
.sidebar a.active, .sidebar a:hover{background:#34495e}
.main{flex:1;padding:28px}
.header{display:flex;justify-content:space-between;align-items:center;margin-bottom:18px}
.content-box{background:#fff;padding:20px;border-radius:10px;box-shadow:0 6px 18px rgba(0,0,0,0.06)}
.stats-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:14px;margin-top:12px}
.stat-card{padding:18px;border-radius:8px;color:#fff}
.error-box{background:#fee;color:#900;padding:10px;border-radius:8px;margin-bottom:15px}
</style>
</head>
<body>
<div class="container">
    <div class="sidebar">
        <h2 style="color:#3498db;margin-bottom:14px"> LAB AI</h2>
        <a href="index.php" class="<?= $page === 'home' ? 'active' : '' ?>"> Dashboard</a>
        <a href="?page=activity" class="<?= $page === 'activity' ? 'active' : '' ?>"> Activity</a>
        <a href="?page=fasilitas" class="<?= $page === 'fasilitas' ? 'active' : '' ?>"> Fasilitas</a>
        <a href="?page=member" class="<?= $page === 'member' ? 'active' : '' ?>"> Member</a>
        <a href="?page=produk" class="<?= $page === 'produk' ? 'active' : '' ?>"> Produk</a>
        <a href="?logout" onclick="return confirm('Yakin ingin logout?')"> Logout</a>
    </div>

    <div class="main">
        <div class="header">
            <div>
                <h3>Dashboard Admin</h3>
                <div style="color:#7f8c8d">Selamat datang, <?= htmlspecialchars($_SESSION['username'] ?? 'Admin') ?></div>
            </div>
        </div>

        <div class="content-box">
            <?php
            // router sederhana: include module berdasarkan $page
            switch ($page) {
                case 'activity':
                    include __DIR__ . '/module/activity/index.php';
                    break;
                case 'fasilitas':
                    include __DIR__ . '/module/fasilitas/index.php';
                    break;
                case 'member':
                    include __DIR__ . '/module/member/index.php';
                    break;
                case 'produk':
                    include __DIR__ . '/module/produk/index.php';
                    break;
                
                // INI BAGIAN YANG DIPERBAIKI
                default:
                    // Dashboard statistik (Versi PDO)
                    try {
                        $countActivity = (int) $pdo->query("SELECT COUNT(*) FROM activity")->fetchColumn();
                        $countFasilitas = (int) $pdo->query("SELECT COUNT(*) FROM fasilitas")->fetchColumn();
                        $countMember = (int) $pdo->query("SELECT COUNT(*) FROM member")->fetchColumn();
                        $countProduk = (int) $pdo->query("SELECT COUNT(*) FROM produk")->fetchColumn();
                    
                    } catch (PDOException $e) {
                        // Jika tabel belum ada atau error, tampilkan 0
                        $countActivity = $countFasilitas = $countMember = $countProduk = 0;
                        echo "<div class='error-box'><strong>Error Database:</strong> " . $e->getMessage() . "</div>";
                    }

                    // Tampilkan HTML
                    echo "<div style='text-align:center;padding:30px 10px;'>
                            <h1> Selamat Datang di Admin Panel</h1>
                            <p style='color:#7f8c8d'>Kelola data Laboratory of Applied Informatics</p>
                          </div>";
                          
                    echo "<div class='stats-grid'>
                            <div class='stat-card' style='background:linear-gradient(135deg,#667eea,#764ba2)'><h3>{$countActivity}</h3><p> Total Activity</p></div>
                            <div class='stat-card' style='background:linear-gradient(135deg,#f093fb,#f5576c)'><h3>{$countFasilitas}</h3><p> Total Fasilitas</p></div>
                            <div class='stat-card' style='background:linear-gradient(135deg,#4facfe,#00f2fe)'><h3>{$countMember}</h3><p> Total Member</p></div>
                            <div class='stat-card' style='background:linear-gradient(135deg,#43e97b,#38f9d7)'><h3>{$countProduk}</h3><p> Total Produk</p></div>
                          </div>";
                    break;
            }
            ?>
        </div>
    </div>
</div>
</body>
</html>