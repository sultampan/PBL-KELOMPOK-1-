<?php
// ==========================================
// 1. CONNECTION & DATA LOGIC
// ==========================================
$rootPath = dirname(dirname(__DIR__)); 
$koneksiPath = $rootPath . '/config/koneksi.php';
if (file_exists($koneksiPath)) require_once $koneksiPath;

// Helper Path
$webImgPathProd     = 'uploads/produk/';
$serverImgPathProd  = $rootPath . '/public/uploads/produk/';

$webImgPathMember   = 'uploads/member/';
$serverImgPathMember = $rootPath . '/public/uploads/member/';

$id_produk = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$product = null;
$teamMembers = [];

if ($id_produk > 0 && isset($pdo)) {
    try {
        // A. Fetch Product Data
        $stmt = $pdo->prepare("SELECT * FROM produk WHERE id_produk = ?");
        $stmt->execute([$id_produk]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($product) {
            // B. Fetch Development Team
            // [UPDATE] Menambahkan m.id_member agar bisa dilink
            $stmtTeam = $pdo->prepare("
                SELECT m.id_member, m.nama_member, m.gambar, pm.role 
                FROM produk_member pm
                JOIN member m ON pm.id_member = m.id_member
                WHERE pm.id_produk = ?
                ORDER BY m.nama_member ASC
            ");
            $stmtTeam->execute([$id_produk]);
            $teamMembers = $stmtTeam->fetchAll(PDO::FETCH_ASSOC);
        }
    } catch (Exception $e) { }
}

// Redirect if not found
if (!$product) {
    echo "<script>window.location='index.php?page=product';</script>";
    exit;
}
?>

<style>
    /* --- [FIX] NAVBAR AGAR SELALU DI ATAS --- */
    #site-header, .fixed-top {
        z-index: 9999 !important;
        position: fixed;
    }

    /* --- HEADER BANNER --- */
    .inner-banner.product-banner {
        background: url('assets/images/header-facility.jpeg') no-repeat center;
        background-size: cover;
        position: relative;
        z-index: 0;
        min-height: 350px;
        display: grid;
        align-items: center;
    }
    .inner-banner.product-banner:before {
        content: ""; background: rgba(0, 0, 0, 0.6);
        position: absolute; inset: 0; z-index: -1;
    }
    .inner-w3-title { font-size: 3rem; font-weight: 700; color: #fff; margin: 0; }

    /* --- PRODUCT DETAIL LAYOUT --- */
    .pd-container {
        max-width: 1100px;
        margin: 50px auto; 
        position: relative;
        z-index: 1; 
        padding-bottom: 50px;
    }

    .pd-card {
        background: #fff;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        padding: 40px;
        display: grid;
        grid-template-columns: 350px 1fr;
        gap: 50px;
        border: 1px solid #eee;
    }

    /* --- LEFT SIDE (IMAGE & LINK) --- */
    .pd-sidebar {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .pd-image-wrapper {
        width: 100%;
        border-radius: 12px;
        overflow: hidden;
        border: 1px solid #eee;
        background: #fafafa;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    }

    .pd-image {
        width: 100%;
        height: auto;
        display: block;
        object-fit: contain;
    }

    /* Styles for NO IMAGE state */
    .pd-no-image {
        width: 100%;
        height: 250px;
        background-color: #f0f2f5;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        color: #999;
        text-align: center;
    }
    .pd-no-image i { font-size: 48px; margin-bottom: 10px; color: #ccc; }
    .pd-no-image span { font-size: 14px; font-weight: 600; }

    .pd-link-btn {
        display: flex; align-items: center; justify-content: center; gap: 10px;
        padding: 15px; background: #01B5B8; color: #fff;
        border-radius: 8px; font-weight: 600; text-decoration: none;
        transition: 0.3s; box-shadow: 0 4px 10px rgba(1, 181, 184, 0.25);
    }
    .pd-link-btn:hover {
        background: #008c8e; transform: translateY(-3px); color: #fff;
    }
    .pd-link-btn.disabled {
        background: #e0e0e0; color: #999; cursor: not-allowed; box-shadow: none;
    }

    /* --- RIGHT SIDE (CONTENT) --- */
    .pd-content { display: flex; flex-direction: column; }

    .pd-title {
        font-size: 2.5rem; font-weight: 800; color: #02406C;
        margin-bottom: 20px; line-height: 1.2;
    }

    .pd-section-label {
        font-size: 14px; font-weight: 700; color: #999; text-transform: uppercase;
        letter-spacing: 1px; margin-bottom: 10px; border-bottom: 1px solid #eee;
        padding-bottom: 5px; margin-top: 10px;
    }

    .pd-description {
        font-size: 16px; line-height: 1.8; color: #555; margin-bottom: 30px;
        white-space: pre-line; /* Keeps paragraphs neat */
    }

    /* --- TEAM GRID --- */
    .pd-team-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        gap: 15px; margin-bottom: 30px;
    }

    /* [UPDATE] Ubah style agar terlihat bisa diklik */
    .pd-team-card {
        display: flex; align-items: center; gap: 12px;
        background: #fff; border: 1px solid #eee;
        padding: 12px; border-radius: 10px;
        transition: 0.2s;
        text-decoration: none; /* Hilangkan garis bawah link */
        color: inherit; /* Warisi warna teks */
        cursor: pointer;
    }
    .pd-team-card:hover {
        border-color: #01B5B8; 
        box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        transform: translateY(-3px); /* Efek naik dikit */
    }

    .pd-team-img {
        width: 45px; height: 45px; border-radius: 50%;
        object-fit: cover; border: 2px solid #f9f9f9;
        flex-shrink: 0;
    }

    .pd-team-info { display: flex; flex-direction: column; overflow: hidden; }
    .pd-team-name {
        font-size: 14px; font-weight: 700; color: #333;
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        transition: color 0.2s;
    }
    /* Saat card dihover, nama jadi hijau teal */
    .pd-team-card:hover .pd-team-name {
        color: #01B5B8;
    }

    .pd-team-role {
        font-size: 11px; color: #01B5B8; font-weight: 600;
        background: #e0f7fa; align-self: flex-start;
        padding: 2px 8px; border-radius: 20px; margin-top: 2px;
    }

    .back-btn-wrapper { margin-top: auto; padding-top: 20px; border-top: 1px solid #f0f0f0; }
    
    .btn-back {
        background: transparent; color: #666; font-weight: 600;
        padding: 8px 0; display: inline-flex; align-items: center; gap: 8px;
        transition: 0.2s; text-decoration: none;
    }
    .btn-back:hover { 
        color: #FE7C11; 
        transform: translateX(-5px); 
    }

    /* --- RESPONSIVE --- */
    @media (max-width: 991px) {
        .pd-card { grid-template-columns: 1fr; gap: 30px; padding: 30px; }
        .pd-image-wrapper { max-width: 400px; margin: 0 auto; }
        .pd-title { text-align: center; font-size: 2rem; }
    }
</style>

<div class="inner-banner product-banner">
    <section class="w3l-breadcrumb text-center">
        <div class="container">
            <h2 class="inner-w3-title">Product Details</h2>
            <ul class="breadcrumbs-custom-path">
                <li><a href="index.php?page=home">Home</a></li>
                <li><a href="index.php?page=product"><span class="fas fa-angle-right mx-2"></span> Product</a></li>
                <li class="active"><span class="fas fa-angle-right mx-2"></span> Detail</li>
            </ul>
        </div>
    </section>
</div>

<div class="container pd-container">
    <div class="pd-card">
        
        <div class="pd-sidebar">
            <?php
                $hasImage = false;
                $imgDisplay = '';

                if (!empty($product['gambar']) && file_exists($serverImgPathProd . $product['gambar'])) {
                    $hasImage = true;
                    $imgDisplay = $webImgPathProd . $product['gambar'];
                }
            ?>
            
            <div class="pd-image-wrapper">
                <?php if ($hasImage): ?>
                    <img src="<?= htmlspecialchars($imgDisplay) ?>" alt="<?= htmlspecialchars($product['nama']) ?>" class="pd-image">
                <?php else: ?>
                    <div class="pd-no-image">
                        <i class="fas fa-image"></i>
                        <span>No Image Available</span>
                    </div>
                <?php endif; ?>
            </div>

            <?php 
                $linkUrl = $product['link_produk'];
                if ($linkUrl) {
                    if (!preg_match("~^(?:f|ht)tps?://~i", $linkUrl)) {
                        $linkUrl = "https://" . $linkUrl;
                    }
                ?>
                    <a href="<?= htmlspecialchars($linkUrl) ?>" target="_blank" class="pd-link-btn">
                        Visit Product <i class="fas fa-external-link-alt"></i>
                    </a>
                <?php } else { ?>
                    <div class="pd-link-btn disabled">
                        Link Unavailable <i class="fas fa-ban"></i>
                    </div>
                <?php } ?>
        </div>

        <div class="pd-content">
            <h1 class="pd-title"><?= htmlspecialchars($product['nama']) ?></h1>
            
            <div class="pd-section-label">Description</div>
            <div class="pd-description">
                <?= $product['deskripsi'] ? nl2br(htmlspecialchars($product['deskripsi'])) : 'No description available for this product.' ?>
            </div>

            <div class="pd-section-label">Development Team</div>
            <div class="pd-team-list-wrapper">
                <?php if (!empty($teamMembers)): ?>
                    <div class="pd-team-grid">
                        <?php foreach ($teamMembers as $tm): 
                            // Member Image Logic
                            $memImg = 'https://ui-avatars.com/api/?name=' . urlencode($tm['nama_member']) . '&background=random&color=fff&size=128&length=1';
                            if (!empty($tm['gambar']) && file_exists($serverImgPathMember . $tm['gambar'])) {
                                $memImg = $webImgPathMember . $tm['gambar'];
                            }
                        ?>
                        <a href="index.php?page=member-detail&id=<?= $tm['id_member']; ?>" class="pd-team-card">
                            <img src="<?= $memImg ?>" alt="Member" class="pd-team-img">
                            <div class="pd-team-info">
                                <span class="pd-team-name"><?= htmlspecialchars($tm['nama_member']) ?></span>
                                <span class="pd-team-role"><?= htmlspecialchars($tm['role'] ?? 'Contributor') ?></span>
                            </div>
                        </a>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="text-muted"><i class="fas fa-info-circle"></i> No development team assigned.</p>
                <?php endif; ?>
            </div>

            <div class="back-btn-wrapper">
                <a href="index.php?page=product" class="btn-back">
                    <i class="fas fa-long-arrow-alt-left"></i> Back to Products
                </a>
            </div>
        </div>

    </div>
</div>