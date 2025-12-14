<?php
// ==========================================
// 1. AMBIL DATA DETAIL MEMBER
// ==========================================
$rootPath = dirname(dirname(__DIR__)); 
$koneksiPath = $rootPath . '/config/koneksi.php';
if (file_exists($koneksiPath)) require_once $koneksiPath;

// Helper Path
$webThumbPathMember = 'uploads/thumb/member-thumb/';
$webImgPathMember   = 'uploads/member/';
$serverThumbPathMember = $rootPath . '/public/uploads/thumb/member-thumb/';
$serverImgPathMember   = $rootPath . '/public/uploads/member/';

$id_member = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$member = null;

if ($id_member > 0 && isset($pdo)) {
    try {
        // A. Data Utama
        $stmt = $pdo->prepare("SELECT * FROM member WHERE id_member = ?");
        $stmt->execute([$id_member]);
        $member = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($member) {
            // B. Links
            $stmtLink = $pdo->prepare("SELECT * FROM member_link WHERE id_member = ?");
            $stmtLink->execute([$id_member]);
            $member['links'] = $stmtLink->fetchAll(PDO::FETCH_ASSOC);

            // C. Produk/Project
            $stmtProd = $pdo->prepare("
                SELECT p.nama, p.link_produk, p.gambar FROM produk p 
                JOIN produk_member pm ON p.id_produk = pm.id_produk 
                WHERE pm.id_member = ?
            ");
            $stmtProd->execute([$id_member]);
            $member['products'] = $stmtProd->fetchAll(PDO::FETCH_ASSOC);

            // D. Activity
            $stmtAct = $pdo->prepare("
                SELECT a.judul, a.tanggal_kegiatan FROM activity a 
                JOIN activity_member am ON a.id_activity = am.id_activity 
                WHERE am.id_member = ?
                ORDER BY a.tanggal_kegiatan DESC
            ");
            $stmtAct->execute([$id_member]);
            $member['activities'] = $stmtAct->fetchAll(PDO::FETCH_ASSOC);
        }
    } catch (Exception $e) { }
}

// Redirect jika member tidak ditemukan
if (!$member) {
    echo "<script>window.location='index.php?page=member';</script>";
    exit;
}
?>

<style>
    /* --- CSS HEADER --- */
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

    /* CSS KHUSUS HALAMAN DETAIL */
    .profile-header {
        background: white; border-radius: 12px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        padding: 40px; margin-top: -50px; /* Posisi agak naik */
        position: relative; z-index: 2;
        border: 1px solid #eee;
    }
    
    .profile-layout {
        display: grid; grid-template-columns: 280px 1fr; gap: 40px;
    }

    /* Sidebar Kiri */
    .profile-sidebar { text-align: center; }
    .profile-avatar {
        width: 180px; height: 180px; border-radius: 50%; object-fit: cover;
        border: 5px solid #fff; box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        margin-bottom: 20px; background: #eee;
    }
    .profile-role {
        display: inline-block; padding: 5px 12px; border-radius: 20px;
        font-size: 12px; font-weight: 700; text-transform: uppercase; margin-bottom: 10px;
    }
    .role-head { background: #FE7C11; color: white; }
    .role-member { background: #eee; color: #555; }
    
    .profile-links { display: flex; flex-direction: column; gap: 10px; margin-top: 20px; }
    .link-btn {
        display: flex; align-items: center; justify-content: center; gap: 8px;
        padding: 10px; background: #f8f9fa; border-radius: 8px;
        color: #333; text-decoration: none; font-weight: 600; font-size: 14px;
        transition: 0.2s; border: 1px solid #eee;
    }
    .link-btn:hover { background: #02406C; color: white; border-color: #02406C; }

    /* Content Kanan */
    .profile-content h2 { font-size: 2.5rem; color: #02406C; font-weight: 700; margin-bottom: 5px; }
    .profile-nidn { color: #888; font-size: 14px; margin-bottom: 20px; display: block; }
    
    .content-section { margin-bottom: 30px; }
    .section-title {
        font-size: 16px; font-weight: 700; color: #333; text-transform: uppercase;
        border-bottom: 2px solid #f0f0f0; padding-bottom: 10px; margin-bottom: 15px;
        letter-spacing: 0.5px;
    }
    
    /* Expertise Tags */
    .skill-tag {
        display: inline-block; padding: 6px 14px; background: #e0f7fa; color: #006064;
        border-radius: 20px; font-size: 13px; font-weight: 600; margin-right: 8px; margin-bottom: 8px;
        border: 1px solid #b2ebf2;
    }

    /* List Project & Activity */
    .involve-list { list-style: none; padding: 0; }
    .involve-item {
        display: flex; align-items: center; gap: 12px; padding: 10px;
        background: #fff; border: 1px solid #eee; border-radius: 8px; margin-bottom: 10px;
        transition: 0.2s;
    }
    .involve-item:hover { transform: translateX(5px); border-color: #01B5B8; }
    .involve-icon {
        width: 36px; height: 36px; background: #f0f0f0; border-radius: 50%;
        display: flex; align-items: center; justify-content: center; color: #555;
    }
    .involve-text { font-size: 14px; color: #333; font-weight: 500; }
    .involve-date { font-size: 12px; color: #999; margin-left: auto; }

    @media (max-width: 768px) {
        .profile-layout { grid-template-columns: 1fr; text-align: center; }
        .profile-content h2, .profile-nidn { text-align: center; }
        .involve-item { text-align: left; }
    }
</style>

<div class="inner-banner facility-banner" style="min-height: 250px;">
    <section class="w3l-breadcrumb text-center">
        <div class="container">
            <div class="w3breadcrumb-gids">
                <div class="w3breadcrumb-left text-center">
                    <h2 class="inner-w3-title" style="font-size: 2rem;">Member Profile</h2>
                </div>
                <div class="w3breadcrumb-right">
                    <ul class="breadcrumbs-custom-path">
                        <li><a href="index.php?page=member">Members</a></li>
                        <li class="active"><span class="fas fa-angle-double-right mx-2"></span> Profile</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>
</div>

<section class="w3l-gallery pb-5" style="background-color: #f9f9f9;">
    <div class="container">
        
        <?php
            // Setup Gambar Profile
            $nama = $member['nama_member'];
            
            // [FIX] Mengembalikan ke RANDOM & SIZE 128 agar warna sama dengan depan
            $defaultImg = 'https://ui-avatars.com/api/?name=' . urlencode($nama) . '&background=random&color=fff&size=128&length=1';
            
            $imgSrc = $defaultImg;
            if (!empty($member['gambar'])) {
                if (file_exists($serverImgPathMember . $member['gambar'])) {
                    $imgSrc = $webImgPathMember . $member['gambar'];
                }
            }
            
            $isHead = ($member['jabatan'] === 'Head of Laboratory');
            $roleClass = $isHead ? 'role-head' : 'role-member';
            $roleLabel = $isHead ? 'Head of Laboratory' : 'Member Lab';
        ?>

        <div class="profile-header">
            <div class="profile-layout">
                
                <div class="profile-sidebar">
                    <img src="<?= $imgSrc ?>" alt="<?= htmlspecialchars($nama) ?>" class="profile-avatar">
                    <br>
                    <span class="profile-role <?= $roleClass ?>"><?= $roleLabel ?></span>
                    
                    <div class="profile-links">
                        <?php if (!empty($member['links'])): ?>
                            <?php foreach ($member['links'] as $link): 
                                $url = $link['url_link'];
                                if (!preg_match("~^(?:f|ht)tps?://~i", $url)) $url = "https://" . $url;
                            ?>
                                <a href="<?= $url ?>" target="_blank" class="link-btn">
                                    <i class="fas fa-link"></i> <?= htmlspecialchars($link['judul_link']) ?>
                                </a>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <span style="color:#999; font-size:13px; font-style:italic;">No contact links</span>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="profile-content">
                    <h2><?= htmlspecialchars($nama) ?></h2>
                    <?php if($member['nidn']): ?>
                        <span class="profile-nidn">NIDN: <?= htmlspecialchars($member['nidn']) ?></span>
                    <?php endif; ?>

                    <div class="content-section">
                        <div class="section-title">Expertise Area</div>
                        <div>
                            <?php 
                            $keahlian = $member['keahlian'] ?? '';
                            if ($keahlian) {
                                $skills = explode(',', $keahlian);
                                foreach ($skills as $sk) {
                                    $sk = trim($sk);
                                    if($sk) echo "<span class='skill-tag'>$sk</span>";
                                }
                            } else {
                                echo "<span style='color:#999;'>-</span>";
                            }
                            ?>
                        </div>
                    </div>

                    <div class="content-section">
                        <div class="section-title">Biography</div>
                        
                        <p style="line-height: 1.8; color: #555; word-wrap: break-word; overflow-wrap: break-word; word-break: break-word;">
                            <?= $member['deskripsi'] ? nl2br(htmlspecialchars($member['deskripsi'])) : 'Belum ada deskripsi.' ?>
                        </p>
                    </div>

                    <?php if (!empty($member['products']) || !empty($member['activities'])): ?>
                    <div class="content-section">
                        <div class="section-title">Laboratory Involvement</div>
                        
                        <ul class="involve-list">
                            <?php foreach ($member['products'] as $prod): ?>
                                <li class="involve-item">
                                    <div class="involve-icon" style="background:#e3f2fd; color:#1565c0;">
                                        <i class="fas fa-box-open"></i>
                                    </div>
                                    <div class="involve-text">
                                        Project: <strong><?= htmlspecialchars($prod['nama']) ?></strong>
                                    </div>
                                </li>
                            <?php endforeach; ?>

                            <?php foreach ($member['activities'] as $act): 
                                $date = date('d M Y', strtotime($act['tanggal_kegiatan']));
                            ?>
                                <li class="involve-item">
                                    <div class="involve-icon" style="background:#e8f5e9; color:#2e7d32;">
                                        <i class="fas fa-calendar-check"></i>
                                    </div>
                                    <div class="involve-text">
                                        Activity: <strong><?= htmlspecialchars($act['judul']) ?></strong>
                                    </div>
                                    <div class="involve-date"><?= $date ?></div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php endif; ?>
                    
                    <div style="margin-top: 40px;">
                        <a href="index.php?page=member" class="btn btn-style btn-primary">
                            <i class="fas fa-arrow-left"></i> Back to Members
                        </a>
                    </div>

                </div>
            </div>
        </div>
        
    </div>
</section>