<div class="sidebar">
    <h2>LAB AI</h2>
    <?php $page = $_GET['page'] ?? 'dashboard'; ?>
    <a href="index.php?page=dashboard" class="<?= $page==='dashboard' ? 'active' : '' ?>">Dashboard</a>
    <a href="index.php?page=activity" class="<?= $page==='activity' ? 'active' : '' ?>">Activity</a>
    <a href="index.php?page=fasilitas" class="<?= $page==='fasilitas' ? 'active' : '' ?>">Fasilitas</a>
    <a href="index.php?page=member" class="<?= $page==='member' ? 'active' : '' ?>">Member</a>
    <a href="index.php?page=produk" class="<?= $page==='produk' ? 'active' : '' ?>">Produk</a>
    <a href="logout.php" onclick="return confirm('Yakin logout?')">Logout</a>
</div>
<div class="main">
    <div class="header">
        <h3>Dashboard Admin</h3>
        <div style="color:#7f8c8d;">Selamat datang, <?= htmlspecialchars($_SESSION['username'] ?? 'Admin') ?></div>
    </div>
    <div class="content-box">
