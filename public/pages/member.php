<?php
// ==========================================
// 1. KONEKSI & LOGIKA DATA
// ==========================================
$rootPath = dirname(dirname(__DIR__)); 
$koneksiPath = $rootPath . '/config/koneksi.php';

if (file_exists($koneksiPath)) require_once $koneksiPath;

// Helper Path
$webThumbPathMember = 'uploads/thumb/member-thumb/';
$webImgPathMember   = 'uploads/member/';
$serverThumbPathMember = $rootPath . '/public/uploads/thumb/member-thumb/';
$serverImgPathMember   = $rootPath . '/public/uploads/member/';

$memberList = [];
if (isset($pdo)) {
    try {
        $stmt = $pdo->prepare("
            SELECT * FROM member 
            ORDER BY 
            CASE WHEN jabatan = 'Head of Laboratory' THEN 0 ELSE 1 END,
            id_member ASC
        ");
        $stmt->execute();
        $memberList = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Ambil Link
        foreach ($memberList as &$m) {
            $id = $m['id_member'];
            $stmtLink = $pdo->prepare("SELECT * FROM member_link WHERE id_member = ?");
            $stmtLink->execute([$id]);
            $m['links'] = $stmtLink->fetchAll(PDO::FETCH_ASSOC);
        }
        unset($m); 

    } catch (Exception $e) { }
}
?>

<style>
    /* --- CSS UTAMA --- */
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
    
    .member-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 25px; margin-top: 20px;
    }

    /* LINK WRAPPER */
    .mit-card-link {
        text-decoration: none; color: inherit; display: block; height: 100%;
    }

    /* CARD STYLE */
    .mit-card {
        background: #fff; border: 1px solid #e0e0e0; padding: 20px; 
        border-radius: 8px; transition: transform 0.2s, box-shadow 0.2s;
        display: flex; flex-direction: column; position: relative; overflow: hidden;
        height: 100%; 
    }
    
    .mit-card-link:hover .mit-card {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.08); border-color: #FE7C11; 
    }

    .mit-card-role {
        display: flex; justify-content: space-between; align-items: center;
        font-size: 11px; text-transform: uppercase; color: #666; font-weight: 600; margin-bottom: 5px;
    }
    .role-badge { background-color: #eee; padding: 3px 8px; border-radius: 4px; }
    .role-badge-head { background-color: #FE7C11; color: #fff; padding: 3px 8px; border-radius: 4px; }

    .mit-card-name { 
        margin: 5px 0 5px 0 !important; 
        font-size: 20px; font-weight: 700; color: #02406C; 
        line-height: 1.3;
        display: -webkit-box; -webkit-line-clamp: 2; line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
    }
    
    .mit-card-content { 
        display: flex; gap: 15px; align-items: flex-start; margin-bottom: 15px;
        min-height: 85px; 
    }
    .mit-avatar img {
        width: 80px; height: 80px; object-fit: cover; border-radius: 50%; 
        border: 3px solid #f8f9fa; background-color: #eee; flex-shrink: 0;
    }

    .mit-contact { 
        margin-top: 5px; display: flex; flex-direction: column; 
        font-size: 13px; gap: 6px; 
    }
    .mit-email-row { display: flex; align-items: center; gap: 8px; }
    .mit-contact a { 
        color: #01B5B8; text-decoration: none; font-weight: 600; transition: color 0.2s;
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 160px; display: inline-block;
    }
    .mit-contact a:hover { color: #008c8e; text-decoration: underline; }

    /* --- [UPDATE] AREA EXPERTISE (BADGE STYLE) --- */
    .mit-expertise-box {
        margin-top: auto; 
        padding-top: 15px;
        border-top: 1px solid #f0f0f0;
    }
    
    .mit-expertise-label {
        font-size: 10px; color: #999; text-transform: uppercase; font-weight: 700; 
        margin-bottom: 8px; display: block; letter-spacing: 0.5px;
    }

    .mit-expertise-items {
        /* Fix height agar kartu seragam */
        height: 55px; 
        overflow: hidden; /* Sembunyikan jika ada badge yang bablas */
        display: flex; 
        flex-wrap: wrap; 
        gap: 5px;
        align-content: flex-start;
    }

    /* Style Badge (Mirip Detail tapi lebih kecil) */
    .skill-tag {
        display: inline-block; 
        padding: 4px 10px; 
        background: #e0f7fa; 
        color: #006064;
        border-radius: 15px; 
        font-size: 11px; 
        font-weight: 600; 
        border: 1px solid #b2ebf2;
        white-space: nowrap;
    }
    
    .skill-more {
        font-size: 11px; color: #777; font-weight: 600; padding: 4px 5px;
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
                <?php foreach ($memberList as $row): 
                    // Variabel Tampilan
                    $id        = $row['id_member'];
                    $nama      = $row['nama_member'];
                    $nidn      = $row['nidn'];
                    $jabatan   = $row['jabatan'];
                    $keahlian  = $row['keahlian'] ?? ''; 
                    
                    $isHead = ($jabatan === 'Head of Laboratory');
                    $badgeLabel = $isHead ? 'HEAD LAB' : 'MEMBER';
                    $badgeClass = $isHead ? 'role-badge-head' : 'role-badge';

                    // Logic Gambar
                    $defaultImg = 'https://ui-avatars.com/api/?name=' . urlencode($nama) . '&background=random&color=fff&size=128&length=1';

                    $imgSrc = $defaultImg;
                    if (!empty($row['gambar'])) {
                        $ext = pathinfo($row['gambar'], PATHINFO_EXTENSION);
                        $thumbName = pathinfo($row['gambar'], PATHINFO_FILENAME) . '-thumb.' . $ext;
                        if (file_exists($serverThumbPathMember . $thumbName)) $imgSrc = $webThumbPathMember . $thumbName;
                        elseif (file_exists($serverImgPathMember . $row['gambar'])) $imgSrc = $webImgPathMember . $row['gambar'];
                        if ($imgSrc !== $defaultImg) $imgSrc .= '?' . time();
                    }
                ?>

                <a href="index.php?page=member-detail&id=<?= $id ?>" class="mit-card-link">
                    <div class="mit-card">
                        
                        <div class="mit-card-role">
                            <span class="<?= $badgeClass ?>"><?= $badgeLabel ?></span>
                            <?php if($nidn): ?><span><?= htmlspecialchars($nidn) ?></span><?php endif; ?>
                        </div>

                        <div class="mit-card-name"><?= htmlspecialchars($nama) ?></div>

                        <div class="mit-card-content">
                            <div class="mit-avatar">
                                <img src="<?= $imgSrc ?>" alt="<?= htmlspecialchars($nama) ?>">
                            </div>
                            
                            <div class="mit-contact" style="flex:1;">
                                <?php 
                                if (!empty($row['links']) && is_array($row['links'])): 
                                    $showLinks = array_slice($row['links'], 0, 3);
                                    foreach ($showLinks as $link):
                                        $judulLink = $link['judul_link'];
                                        $urlLink   = $link['url_link'];
                                        if (strpos($urlLink, 'http') === false) $urlLink = 'https://' . $urlLink;
                                ?>
                                    <div class="mit-email-row">
                                        <i class="fas fa-link" style="color:#ccc; font-size:12px; margin-right:6px;"></i>
                                        <span style="color: #01B5B8; font-weight: 600; font-size: 13px;">
                                            <?= htmlspecialchars($judulLink) ?>
                                        </span>
                                    </div>
                                <?php 
                                    endforeach;
                                else:
                                ?>
                                    <span style="color:#ccc; font-style:italic; font-size:12px;">No contact info</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="mit-expertise-box">
                            <span class="mit-expertise-label">Expertise</span>
                            <div class="mit-expertise-items">
                                <?php 
                                    if($keahlian) {
                                        $skillsArr = explode(',', $keahlian);
                                        // Filter yg kosong & trim spasi
                                        $skillsArr = array_filter(array_map('trim', $skillsArr));
                                        
                                        // Ambil 3 pertama saja
                                        $limit = 3;
                                        $showSkills = array_slice($skillsArr, 0, $limit);
                                        $sisa = count($skillsArr) - $limit;

                                        foreach ($showSkills as $sk) {
                                            echo "<span class='skill-tag'>".htmlspecialchars($sk)."</span>";
                                        }

                                        // Jika ada sisa, tampilkan +X
                                        if($sisa > 0) {
                                            echo "<span class='skill-more'>+$sisa</span>";
                                        }
                                    } else {
                                        echo '<span style="color:#999; font-size:12px;">-</span>';
                                    }
                                ?>
                            </div>
                        </div>
                        
                        <div style="margin-top:10px; padding-top:10px; font-size:12px; color:#01B5B8; font-weight:600; text-align:right;">
                            View Full Profile <i class="fas fa-arrow-right" style="font-size:10px;"></i>
                        </div>
                    </div>
                </a>

                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="text-center py-5"><h4 class="text-muted">Belum ada data member.</h4></div>
        <?php endif; ?>
        
    </div>
</section>