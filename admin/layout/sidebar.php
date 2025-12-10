<!-- layout/sidebar.php -->
<div class="sidebar" id="sidebar">
    <h2 class="sidebar-title"><span>LAB AI</span></h2>

    <?php $activePage = $_GET['page'] ?? 'dashboard'; ?>

    <a href="index.php?page=dashboard" class="menu-item <?= $activePage==='dashboard' ? 'active' : '' ?>">
        <i class="fas fa-home"></i>
        <span>Dashboard</span>
    </a>

    <a href="index.php?page=activity" class="menu-item <?= $activePage==='activity' ? 'active' : '' ?>">
        <i class="fas fa-tasks"></i>
        <span>Activity</span>
    </a>

    <a href="index.php?page=fasilitas" class="menu-item <?= $activePage==='fasilitas' ? 'active' : '' ?>">
        <i class="fas fa-building"></i>
        <span>Fasilitas</span>
    </a>

    <a href="index.php?page=member" class="menu-item <?= $activePage==='member' ? 'active' : '' ?>">
        <i="fas fa-users"></i>
        <span>Member</span>
    </a>

    <a href="index.php?page=produk" class="menu-item <?= $activePage==='produk' ? 'active' : '' ?>">
        <i class="fas fa-box"></i>
        <span>Produk</span>
    </a>

    <a href="index.php?page=contact" class="menu-item <?= $activePage==='contact' ? 'active' : '' ?>">
        <i class="fas fa-envelope"></i>
        <span>Contact</span>
    </a>

    <a href="logout.php" class="menu-item" onclick="return confirm('Yakin logout?')">
        <i class="fas fa-sign-out-alt"></i>
        <span>Logout</span>
    </a>

    <div class="sidebar-toggle-wrapper">
        <button id="toggleSidebar" class="sidebar-toggle">
            <i class="fas fa-angle-left toggle-icon"></i>
        </button>
    </div>
</div>
