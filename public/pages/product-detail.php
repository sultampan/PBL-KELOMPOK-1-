<?php
// ==========================================
// 1. KONEKSI & LOGIKA DATA
// ==========================================
$rootPath = dirname(dirname(__DIR__)); 
$koneksiPath = $rootPath . '/config/koneksi.php';
if (file_exists($koneksiPath)) require_once $koneksiPath;

// Helper Path (Sesuaikan dengan struktur folder Anda)
$webImgPathProd   = 'uploads/produk/';
$serverImgPathProd = $rootPath . '/public/uploads/produk/';

$webImgPathMember = 'uploads/member/';
$serverImgPathMember = $rootPath . '/public/uploads/member/';

$id_produk = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$product = null;
$teamMembers = [];

if ($id_produk > 0 && isset($pdo)) {
    try {
        // A. Ambil Data Produk
        $stmt = $pdo->prepare("SELECT * FROM produk WHERE id_produk = ?");
        $stmt->execute([$id_produk]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($product) {
            // B. Ambil Tim Pengembang (Join Member & Produk_Member)
            // Kita ambil Foto, Nama, dan Role
            $stmtTeam = $pdo->prepare("
                SELECT m.nama_member, m.gambar, pm.role 
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

// Redirect jika produk tidak ditemukan
if (!$product) {
    echo "<script>window.location='index.php?page=produk';</script>";
    exit;
}
?>

<style>
    /* --- REUSING HEADER STYLE (Sama seperti Member) --- */
    .inner-banner.facility-banner {
        background: url('assets/images/header-facility.jpeg') no-repeat center;
        background-size: cover;
        position: relative;
        z-index: 0;
        min-height: 350px;
        display: grid;
        align-items: center;
    }
    .inner-banner.facility-banner:before {
        content: ""; background: rgba(0, 0, 0, 0.6);
        position: absolute; inset: 0; z-index: -1;
    }

    /* --- LAYOUT UTAMA --- */
    .profile-header {
        background: white; border-radius: 12px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        padding: 40px; margin-top: -50px;
        position: relative; z-index: 2;
        border: 1px solid #eee;
        
        /* [FIX] Mencegah container utama melar keluar layar */
        max-width: 100%;
        overflow: hidden; 
    }
    
    .profile-layout {
        display: grid; 
        grid-template-columns: 300px 1fr; 
        gap: 40px;
        /* [FIX] Memastikan grid tidak memaksakan lebar jika konten terlalu besar */
        max-width: 100%;
    }

    /* --- SIDEBAR (GAMBAR PRODUK) --- */
    .profile-sidebar { 
        text-align: center; 
        min-width: 0; /* [FIX] Mencegah sidebar melar flex item */
    }
    
    .product-main-image {
        width: 100%; 
        height: auto; 
        max-height: 250px;
        border-radius: 10px; 
        object-fit: cover;
        border: 1px solid #eee; 
        box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        margin-bottom: 20px; 
        background: #fff;
    }
    
    .link-btn {
        display: flex; align-items: center; justify-content: center; gap: 8px;
        padding: 12px; background: #01B5B8; border-radius: 8px;
        color: #fff; text-decoration: none; font-weight: 600; font-size: 14px;
        transition: 0.2s; border: 1px solid #01B5B8;
        box-shadow: 0 4px 10px rgba(1, 181, 184, 0.2);
    }
    .link-btn:hover { background: #008c8e; border-color: #008c8e; color: white; transform: translateY(-2px); }

    /* --- KONTEN KANAN --- */
    .profile-content {
        /* [FIX PENTING] min-width: 0 memaksa grid item untuk shrink jika teks kepanjangan */
        min-width: 0; 
    }

    .profile-content h2 { 
        font-size: 2.2rem; color: #02406C; font-weight: 700; margin-bottom: 20px; line-height: 1.2;
        /* [FIX] Judul juga harus dipotong jika terlalu panjang */
        word-wrap: break-word;
    }
    
    .content-section { margin-bottom: 35px; }
    .section-title {
        font-size: 16px; font-weight: 700; color: #333; text-transform: uppercase;
        border-bottom: 2px solid #f0f0f0; padding-bottom: 10px; margin-bottom: 15px;
        letter-spacing: 0.5px;
    }

    /* [FIX UTAMA] Style untuk Deskripsi Panjang */
    .description-text {
        line-height: 1.8; 
        color: #555; 
        font-size: 1rem;
        
        /* Properti Ajaib untuk memotong teks panjang tanpa spasi */
        word-wrap: break-word;      /* Standar lama */
        overflow-wrap: break-word;  /* Standar baru */
        word-break: break-word;     /* Memastikan kata dipotong jika perlu */
        
        /* Tambahan untuk teks yang benar-benar tanpa spasi (seperti "AAAAA...") */
        overflow-wrap: anywhere;   
    }

    /* --- TEAM LIST STYLING (Roles) --- */
    .team-list {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 15px;
    }

    .team-card {
        display: flex; align-items: center; gap: 12px;
        background: #fff; border: 1px solid #eee; 
        padding: 12px; border-radius: 8px;
        transition: transform 0.2s;
        /* [FIX] Agar kartu tim tidak melar */
        max-width: 100%;
        overflow: hidden;
    }
    .team-card:hover { transform: translateY(-3px); border-color: #ddd; box-shadow: 0 5px 15px rgba(0,0,0,0.05); }

    .team-avatar {
        width: 50px; height: 50px; border-radius: 50%; object-fit: cover;
        background: #f0f0f0; border: 2px solid #fff; box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        flex-shrink: 0; /* Mencegah foto gepeng */
    }

    .team-info { 
        display: flex; flex-direction: column; gap: 3px; 
        min-width: 0; /* [FIX] Agar teks nama panjang terpotong rapi */
    }
    .team-name { 
        font-weight: 700; color: #333; font-size: 14px; 
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis; /* Nama panjang jadi ... */
    }
    
    .team-role { 
        display: inline-block;
        background-color: #fce4ec; 
        color: #ad1457; 
        font-size: 11px; 
        font-weight: 600; 
        padding: 2px 8px; 
        border-radius: 12px;
        align-self: flex-start;
    }

    @media (max-width: 768px) {
        .profile-layout { grid-template-columns: 1fr; }
        .product-main-image { max-height: 300px; }
        .profile-content h2 { text-align: center; font-size: 1.8rem; }
    }
</style>

<div class="inner-banner facility-banner" style="min-height: 250px;">
    <section class="w3l-breadcrumb text-center">
        <div class="container">
            <div class="w3breadcrumb-gids">
                <div class="w3breadcrumb-left text-center">
                    <h2 class="inner-w3-title" style="font-size: 2rem;">Product Detail</h2>
                </div>
                <div class="w3breadcrumb-right">
                    <ul class="breadcrumbs-custom-path">
                        <li><a href="index.php?page=produk">Product</a></li>
                        <li class="active"><span class="fas fa-angle-double-right mx-2"></span> Detail</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>
</div>

<section class="w3l-gallery pb-5" style="background-color: #f9f9f9;">
    <div class="container">
        
        <?php
            // Logic Gambar Produk
            $imgSrc = 'https://via.placeholder.com/400x300?text=No+Image'; // Fallback
            if (!empty($product['gambar'])) {
                if (file_exists($serverImgPathProd . $product['gambar'])) {
                    $imgSrc = $webImgPathProd . $product['gambar'];
                }
            }

            // Logic Link
            $linkProduk = $product['link_produk'];
            $hasLink = !empty($linkProduk);
            if ($hasLink) {
                // Pastikan ada http/https
                if (!preg_match("~^(?:f|ht)tps?://~i", $linkProduk)) {
                    $linkProduk = "https://" . $linkProduk;
                }
            }
        ?>

        <div class="profile-header">
            <div class="profile-layout">
                
                <div class="profile-sidebar">
                    <img src="<?= $imgSrc ?>" alt="<?= htmlspecialchars($product['nama']) ?>" class="product-main-image">
                    
                    <?php if ($hasLink): ?>
                        <a href="<?= htmlspecialchars($linkProduk) ?>" target="_blank" class="link-btn">
                            <i class="fas fa-external-link-alt"></i> Visit Product
                        </a>
                    <?php else: ?>
                        <button class="link-btn" style="background:#ccc; border-color:#ccc; cursor:not-allowed;">
                            <i class="fas fa-ban"></i> No Link Available
                        </button>
                    <?php endif; ?>
                </div>

                <div class="profile-content">
                    <h2><?= htmlspecialchars($product['nama']) ?></h2>

                    <div class="content-section">
                        <div class="section-title">About This Product</div>
                        <div class="description-text">
                            <?= $product['deskripsi'] ? nl2br(htmlspecialchars($product['deskripsi'])) : 'Belum ada deskripsi untuk produk ini.' ?>
                        </div>
                    </div>

                    <div class="content-section">
                        <div class="section-title">Development Team</div>
                        
                        <?php if (!empty($teamMembers)): ?>
                            <div class="team-list">
                                <?php foreach ($teamMembers as $tm): 
                                    // Logic Gambar Member
                                    $memberImg = 'https://ui-avatars.com/api/?name=' . urlencode($tm['nama_member']) . '&background=random&color=fff&size=64';
                                    if (!empty($tm['gambar']) && file_exists($serverImgPathMember . $tm['gambar'])) {
                                        $memberImg = $webImgPathMember . $tm['gambar'];
                                    }
                                ?>
                                <div class="team-card">
                                    <img src="<?= $memberImg ?>" alt="<?= htmlspecialchars($tm['nama_member']) ?>" class="team-avatar">
                                    <div class="team-info">
                                        <span class="team-name"><?= htmlspecialchars($tm['nama_member']) ?></span>
                                        <span class="team-role">
                                            <?= htmlspecialchars($tm['role'] ?? 'Contributor') ?>
                                        </span>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p style="color:#999; font-style:italic;">Data tim pengembang belum ditambahkan.</p>
                        <?php endif; ?>
                    </div>
                    
                    <div style="margin-top: 40px;">
                        <a href="index.php?page=product" class="btn btn-style btn-primary" style="padding: 10px 20px; font-size: 14px;">
                            <i class="fas fa-arrow-left"></i> Back to Products
                        </a>
                    </div>

                </div>
            </div>
        </div>
        
    </div>
</section>