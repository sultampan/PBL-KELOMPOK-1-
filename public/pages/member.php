<?php
// ==========================================
// 1. KONEKSI & LOGIKA DATA
// ==========================================

// Naik 2 level untuk mencari config (sesuaikan jika struktur folder berbeda)
$rootPath = dirname(dirname(__DIR__)); 
$koneksiPath = $rootPath . '/config/koneksi.php';

// Cek file koneksi
if (file_exists($koneksiPath)) {
    require_once $koneksiPath;
}

// Helper Path
$serverBase = $_SERVER['DOCUMENT_ROOT'] . '/public'; 
// Path untuk browser (src)
$webThumbPathMember = 'uploads/thumb/member-thumb/';
$webImgPathMember   = 'uploads/member/';
// Path untuk server (file_exists)
$serverThumbPathMember = $rootPath . '/public/uploads/thumb/member-thumb/';
$serverImgPathMember   = $rootPath . '/public/uploads/member/';

$memberList = [];
if (isset($pdo)) {
    try {
        // 1. Ambil Member (Urutkan Head Lab paling atas)
        $stmt = $pdo->prepare("
            SELECT * FROM member 
            ORDER BY 
            CASE WHEN jabatan = 'Head of Laboratory' THEN 0 ELSE 1 END,
            id_member ASC
        ");
        $stmt->execute();
        $memberList = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 2. Ambil Link untuk setiap member
        foreach ($memberList as &$m) {
            $stmtLink = $pdo->prepare("SELECT * FROM member_link WHERE id_member = ?");
            $stmtLink->execute([$m['id_member']]);
            $m['links'] = $stmtLink->fetchAll(PDO::FETCH_ASSOC);
        }
        unset($m); 

    } catch (Exception $e) { }
}
?>

<style>
    /* --- 1. BANNER HEADER (Tetap Sama) --- */
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
        position: absolute; top: 0; bottom: 0; left: 0; right: 0; z-index: -1;
    }
    
    /* --- 2. CSS MEMBER STYLE (Diambil dari member.css Admin) --- */
    
    /* Grid Container */
    .member-grid {
        display: grid;
        /* Grid responsif: minimal lebar kartu 350px, sisanya flexible */
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 25px; 
        margin-top: 20px;
    }

    /* Kartu Utama */
    .mit-card {
        background: #fff; 
        border: 1px solid #e0e0e0;
        padding: 20px; 
        border-radius: 6px;
        transition: transform 0.2s, box-shadow 0.2s;
        display: flex; 
        flex-direction: column; 
        gap: 0px;
        position: relative;
        overflow: hidden;
    }
    
    .mit-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.08);
        border-color: #FE7C11; /* Highlight warna oranye saat hover */
    }

    /* Badge Role (Head/Member) */
    .mit-card-role {
        display: flex; align-items: center; gap: 10px;
        font-size: 11px; text-transform: uppercase; color: #666; font-weight: 600;
        margin-bottom: 5px;
    }
    .role-badge { background-color: #eee; padding: 3px 8px; border-radius: 4px; }
    .role-badge-head { background-color: #FE7C11; color: #fff; padding: 3px 8px; border-radius: 4px; }

    /* Nama Member */
    .mit-card-name {
        margin: 5px 0 10px 0 !important; 
        font-size: 20px; 
        font-weight: 700;
        line-height: 1.3;
        color: #02406C;
    }
    .mit-card-name a { text-decoration: none; color: inherit; }

    /* Konten Tengah (Avatar + Link) */
    .mit-card-content {
        display: flex; gap: 15px; align-items: flex-start; margin-top: 5px;
    }
    
    /* Avatar */
    .mit-avatar img {
        width: 80px; height: 80px; 
        object-fit: cover;
        border-radius: 50%; 
        border: 3px solid #f8f9fa;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1); 
        background-color: #eee;
    }

    /* List Link Contact */
    .mit-contact { 
        margin-top: 5px; 
        display: flex; 
        flex-direction: column; 
        font-size: 13px; 
        gap: 8px; 
    }
    .mit-email-row { display: flex; align-items: center; gap: 8px; }
    
    /* Styling Link agar menarik */
    .mit-contact a { 
        color: #01B5B8; 
        text-decoration: none; 
        font-weight: 600; 
        transition: color 0.2s;
    }
    .mit-contact a:hover { color: #008c8e; text-decoration: underline; }

    /* Bio / Deskripsi */
    .mit-bio {
        /* Spacing & Border */
        margin-top: 15px; 
        padding-top: 15px;
        border-top: 1px solid #f0f0f0;
        
        /* Typography */
        font-size: 14px; 
        color: #555; 
        line-height: 1.6;

        /* --- SETTING FIX 2 BARIS --- */
        height: 42px;       /* Tinggi pas untuk 2 baris (14px * 1.6 * 2) */
        overflow: hidden;   /* Sembunyikan sisa teks */
        
        /* Efek titik-titik (...) di akhir baris ke-2 */
        display: -webkit-box;
        line-clamp: 2; /* Batas maksimal 2 baris */
        -webkit-box-orient: vertical;
        text-overflow: ellipsis;
    }
</style>

<div class="inner-banner facility-banner">
    <section class="w3l-breadcrumb text-center">
        <div class="container">
            <div class="w3breadcrumb-gids">
                <div class="w3breadcrumb-left text-center">
                    <h2 class="inner-w3-title">Member</h2>
                </div>
                <div class="w3breadcrumb-right">
                    <ul class="breadcrumbs-custom-path">
                        <li><a href="index.php?page=home">Home</a></li>
                        <li class="active"><span class="fas fa-angle-double-right mx-2"></span> Member</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>
</div>

<section class="w3l-gallery pb-5 pt-4" style="background-color: #f9f9f9;">
    <div class="container pb-md-5 pt-3">
        
        <div class="title-content text-center mb-5">
            <h6 class="title-subw3hny">Meet The Team</h6>
            <h3 class="title-w3l mb-4"> Our Laboratory Members </h3>
        </div>

        <?php if (!empty($memberList)): ?>
            <div class="member-grid">
                <?php foreach ($memberList as $row): ?>
                    <?php 
                        // Persiapan Data
                        $nama      = $row['nama_member'];
                        $nidn      = $row['nidn'];
                        $jabatan   = $row['jabatan'];
                        $deskripsi = $row['deskripsi'];
                        $links     = $row['links'] ?? [];
                        
                        // Cek Role untuk Badge Warna
                        $isHead = ($jabatan === 'Head of Laboratory');
                        $badgeLabel = $isHead ? 'HEAD LAB' : 'MEMBER';
                        $badgeClass = $isHead ? 'role-badge-head' : 'role-badge';

                        // Logic Gambar (Persis Admin)
                        $defaultImg = 'https://ui-avatars.com/api/?name=' . urlencode($nama) . '&background=random&color=fff&size=128&length=1';
                        $imgSrc = $defaultImg;
                        $gambar = $row['gambar'];

                        // Cek Gambar Fisik
                        if (!empty($gambar)) {
                            $ext = pathinfo($gambar, PATHINFO_EXTENSION);
                            $filename = pathinfo($gambar, PATHINFO_FILENAME);
                            $thumbName = $filename . '-thumb.' . $ext;
                            
                            // Prioritas 1: Thumbnail
                            if (file_exists($serverThumbPathMember . $thumbName)) {
                                $imgSrc = $webThumbPathMember . $thumbName;
                            } 
                            // Prioritas 2: Gambar Asli
                            elseif (file_exists($serverImgPathMember . $gambar)) {
                                $imgSrc = $webImgPathMember . $gambar;
                            }
                            // Tambahkan timestamp agar refresh cache
                            if ($imgSrc !== $defaultImg) $imgSrc .= '?' . time();
                        }
                    ?>

                    <div class="mit-card">
                        
                        <div class="mit-card-role">
                            <span class="<?= $badgeClass ?>"><?= $badgeLabel ?></span>
                            <?php if(!empty($nidn)): ?>
                                <span><?= htmlspecialchars($nidn) ?></span>
                            <?php endif; ?>
                        </div>

                        <h2 class="mit-card-name">
                            <?= htmlspecialchars($nama) ?>
                        </h2>

                        <div class="mit-card-content">
                            <div class="mit-avatar">
                                <img src="<?= $imgSrc ?>" alt="<?= htmlspecialchars($nama) ?>">
                            </div>

                            <div class="mit-contact">
                                <?php if (!empty($links)): ?>
                                    <?php foreach ($links as $link): 
                                        $url = $link['url_link'];
                                        // Fix URL
                                        if (strpos($url, 'http') === false) $url = 'https://' . $url;
                                    ?>
                                        <div class="mit-email-row">
                                            <i class="fas fa-link" style="color:#ccc; font-size:12px;"></i>
                                            <a href="<?= $url ?>" target="_blank" title="<?= htmlspecialchars($link['judul_link']) ?>">
                                                <?= htmlspecialchars($link['judul_link']) ?>
                                            </a>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="mit-bio">
                            <?= nl2br(htmlspecialchars($deskripsi)) ?>
                        </div>

                    </div>
                    <?php endforeach; ?>
            </div>

        <?php else: ?>
            <div class="text-center py-5">
                <h4 class="text-muted">Belum ada data member.</h4>
            </div>
        <?php endif; ?>
        
    </div>
</section>