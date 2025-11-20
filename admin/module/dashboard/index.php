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

<div class="header">
    <h3>Dashboard Admin</h3>
    <div style="color:#7f8c8d;">Selamat datang, <?= htmlspecialchars($_SESSION['username'] ?? 'Admin') ?></div>
</div>

<div class="content-box">
    <?php if (isset($dbError)): ?>
        <div class="error-box"><?= $dbError ?></div>
    <?php endif; ?>

    <div style="text-align:center; padding:30px 10px;">
        <h1>Selamat Datang di Admin Panel</h1>
        <p style="color:#7f8c8d;">Kelola data Laboratory of Applied Informatics</p>
    </div>

    <div class="stats-grid">
        <div class="stat-card" style="background:linear-gradient(135deg,#667eea,#764ba2)">
            <h3><?= $countActivity ?></h3><p>Total Activity</p>
        </div>
        <div class="stat-card" style="background:linear-gradient(135deg,#f093fb,#f5576c)">
            <h3><?= $countFasilitas ?></h3><p>Total Fasilitas</p>
        </div>
        <div class="stat-card" style="background:linear-gradient(135deg,#4facfe,#00f2fe)">
            <h3><?= $countMember ?></h3><p>Total Member</p>
        </div>
        <div class="stat-card" style="background:linear-gradient(135deg,#43e97b,#38f9d7)">
            <h3><?= $countProduk ?></h3><p>Total Produk</p>
        </div>
    </div>
</div>
