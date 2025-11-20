<?php
require_once __DIR__ . '/../config/koneksi.php';

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

// halaman default
$page = $_GET['page'] ?? 'dashboard';

// include layout
include __DIR__ . '/layout/header.php';
include __DIR__ . '/layout/sidebar.php';

// echo '<div class="main"><div class="content-box">';

switch ($page) {
    case 'dashboard':
        // ambil statistik
        try {
            $countActivity = (int) $pdo->query("SELECT COUNT(*) FROM activity")->fetchColumn();
            $countFasilitas = (int) $pdo->query("SELECT COUNT(*) FROM fasilitas")->fetchColumn();
            $countMember = (int) $pdo->query("SELECT COUNT(*) FROM member")->fetchColumn();
            $countProduk = (int) $pdo->query("SELECT COUNT(*) FROM produk")->fetchColumn();
        } catch (PDOException $e) {
            $countActivity = $countFasilitas = $countMember = $countProduk = 0;
            echo "<div class='error-box'>Error Database: " . $e->getMessage() . "</div>";
        }
        include __DIR__ . '/pages/dashboard.php';
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

// echo '</div>';

// footer
include __DIR__ . '/layout/footer.php';
