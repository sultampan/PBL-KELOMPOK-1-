<!-- layout/sidebar.php -->
<div class="sidebar" id="sidebar">
    <h2 class="sidebar-title"><span>LAB AI</span></h2>

    <?php $page = $_GET['page'] ?? 'dashboard'; ?>

    <a href="index.php?page=dashboard" class="<?= $page==='dashboard' ? 'active' : '' ?>">
        <i class="fas fa-home"></i>
        <span>Dashboard</span>
    </a>

    <a href="index.php?page=activity" class="<?= $page==='activity' ? 'active' : '' ?>">
        <i class="fas fa-tasks"></i>
        <span>Activity</span>
    </a>

    <a href="index.php?page=fasilitas" class="<?= $page==='fasilitas' ? 'active' : '' ?>">
        <i class="fas fa-building"></i>
        <span>Fasilitas</span>
    </a>

    <a href="index.php?page=member" class="<?= $page==='member' ? 'active' : '' ?>">
        <i class="fas fa-users"></i>
        <span>Member</span>
    </a>

    <a href="index.php?page=produk" class="<?= $page==='produk' ? 'active' : '' ?>">
        <i class="fas fa-box"></i>
        <span>Produk</span>
    </a>

    <a href="logout.php" onclick="return confirm('Yakin logout?')">
        <i class="fas fa-sign-out-alt"></i>
        <span>Logout</span>
    </a>

    <div class="sidebar-toggle-wrapper">
        <button id="toggleSidebar" class="sidebar-toggle">
            <i class="fas fa-angle-left"></i>
        </button>
    </div>

</div>

<!-- main -->
<div class="main" id="main">
