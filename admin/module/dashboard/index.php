<?php
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: ../../login.php");
    exit;
}

// ambil statistik
try {
    $countActivity = (int) $pdo->query("SELECT COUNT(*) FROM activity")->fetchColumn();
    $countFasilitas = (int) $pdo->query("SELECT COUNT(*) FROM fasilitas")->fetchColumn();
    $countMember = (int) $pdo->query("SELECT COUNT(*) FROM member")->fetchColumn();
    $countProduk = (int) $pdo->query("SELECT COUNT(*) FROM produk")->fetchColumn();
} catch (PDOException $e) {
    $countActivity = $countFasilitas = $countMember = $countProduk = 0;
    $dbError = $e->getMessage();
}
?>

<div class="dashboard-row">

    <div class="sb-card border-blue">
        <div class="sb-card-info">
            <span class="sb-card-title text-blue">Total Activity</span>
            <span class="sb-card-value"><?= $countActivity ?></span>
        </div>
        <i class="fas fa-calendar sb-card-icon icon-blue"></i>
    </div>

    <div class="sb-card border-green">
        <div class="sb-card-info">
            <span class="sb-card-title text-green">Total Fasilitas</span>
            <span class="sb-card-value"><?= $countFasilitas ?></span>
        </div>
        <i class="fas fa-building sb-card-icon icon-green"></i>
    </div>

    <div class="sb-card border-cyan">
        <div class="sb-card-info">
            <span class="sb-card-title text-cyan">Total Member</span>
            <span class="sb-card-value"><?= $countMember ?></span>
        </div>
        <i class="fas fa-users sb-card-icon icon-cyan"></i>
    </div>

    <div class="sb-card border-yellow">
        <div class="sb-card-info">
            <span class="sb-card-title text-yellow">Total Produk</span>
            <span class="sb-card-value"><?= $countProduk ?></span>
        </div>
        <i class="fas fa-box sb-card-icon icon-yellow"></i>
    </div>


</div>
