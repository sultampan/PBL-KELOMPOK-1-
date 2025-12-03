<?php 
$start_time = microtime(true);
require_once __DIR__ . '/../config/koneksi.php';

if (session_status() === PHP_SESSION_NONE) session_start();

// proteksi
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header("Location: login.php");
    exit;
}

// 1. Set waktu timeout 
$inactive_timeout = 1800; // 30 menit 

// 2. Cek waktu terakhir aktivitas
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $inactive_timeout)) {
    $timeout_message = "Sesi Anda telah berakhir karena tidak aktif. Silakan login kembali.";

    session_unset();
    session_destroy();
    session_start();

    $_SESSION['login_error'] = $timeout_message;

    header("Location: login.php");
    exit;
}

// 3. Jika sesi masih aktif, perbarui waktu aktivitas terakhir
$_SESSION['last_activity'] = time();

// logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit;
}

// halaman default
$page = $_GET['page'] ?? 'dashboard';

// ============================
//  LAYOUT START
// ============================

include __DIR__ . '/layout/header.php';      // <html><body><div class="container">
include __DIR__ . '/layout/sidebar.php';     // <div class="sidebar">

// ============================
//  MAIN WRAPPER DIMULAI
// ============================
?>
<div class="main" id="main">

    <!-- ====================== HEADER ADMIN ADA DI DALAM MAIN ====================== -->
    <div class="header-top" style="
        display:flex;
        justify-content:space-between;
        align-items:center;
        margin-bottom:20px;
    ">
        <h3 style="margin:0;">Dashboard Admin</h3>

        <div style="color:#7f8c8d;">
            Selamat datang, <?= htmlspecialchars($_SESSION['username'] ?? 'Admin') ?>
        </div>
    </div>

    <!-- ========================= ISI HALAMAN ========================== -->
    <?php
    switch ($page) {
        case 'dashboard':
            try {
                $countActivity = (int) $pdo->query("SELECT COUNT(*) FROM activity")->fetchColumn();
                $countFasilitas = (int) $pdo->query("SELECT COUNT(*) FROM fasilitas")->fetchColumn();
                $countMember = (int) $pdo->query("SELECT COUNT(*) FROM member")->fetchColumn();
                $countProduk = (int) $pdo->query("SELECT COUNT(*) FROM produk")->fetchColumn();
            } catch (PDOException $e) {
                $countActivity = $countFasilitas = $countMember = $countProduk = 0;
                echo "<div class='error-box'>Error Database: " . $e->getMessage() . "</div>";
            }
            include __DIR__ . '/module/dashboard/index.php';
            break;

        case 'produk':
            include __DIR__ . '/module/produk/index.php';
            break;

        case 'member':
            include __DIR__ . '/module/member/view.php';
            include __DIR__ . '/module/member/table.php';
            break;

        case 'fasilitas':
            include __DIR__ . '/module/fasilitas/view.php';
            include __DIR__ . '/module/fasilitas/table.php';
            break;

        case 'activity':
            include __DIR__ . '/module/activity/view.php';
            include __DIR__ . '/module/activity/table.php';
            break;

        default:
            echo "<h3>Halaman tidak ditemukan.</h3>";
            break;
    }
    ?>

</div> <!-- END MAIN -->

<?php 
include __DIR__ . '/layout/footer.php'; 
?>
