<?php
// admin/index.php 
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
include __DIR__ . '/layout/header.php';
include __DIR__ . '/layout/sidebar.php';
?>

<div class="main" id="main">

    <!-- ====================== HEADER TITLE DINAMIS ====================== -->
    <?php
    $judul = ucfirst(str_replace("_", " ", $page));
    if ($judul == 'Dashboard') $judul = 'Dashboard Admin';
    ?>
    <div class="header-top" style="
        display:flex;
        justify-content:space-between;
        align-items:center;
        margin-bottom:10px;
    ">
        <h3><?= $judul ?></h3>

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
            include __DIR__ . '/module/member/index.php';
            break;

        case 'fasilitas':
            include __DIR__ . '/module/fasilitas/index.php';
            break;

        case 'activity':
            include __DIR__ . '/module/activity/index.php';
            break;

        /* 🔥 CONTACT CRUD */
        case 'contact':
            include __DIR__ . '/module/contact/index.php';
            break;

        case 'contact_edit':
            include __DIR__ . '/module/contact/edit.php';
            break;

        case 'contact_reply':
            include __DIR__ . '/module/contact/reply.php';
            break;

        case 'contact_delete':
            include __DIR__ . '/module/contact/delete.php';
            break;
        /* ===================================================== */

        default:
            echo "<h3>Halaman tidak ditemukan.</h3>";
            break;
    }
    ?>

</div> <!-- END MAIN -->

<?php include __DIR__ . '/layout/footer.php'; ?>
