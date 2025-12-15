<?php
// ==========================================
// 1. AMBIL DATA DETAIL ACTIVITY
// ==========================================
$rootPath = dirname(dirname(__DIR__)); 
$koneksiPath = $rootPath . '/config/koneksi.php';
if (file_exists($koneksiPath)) require_once $koneksiPath;

// Helper Path
$webImgPathActivity = 'uploads/activity/';
$serverImgPathActivity = $rootPath . '/public/uploads/activity/';

$id_activity = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$activity = null;

if ($id_activity > 0 && isset($pdo)) {
    try {
        // A. Data Utama Activity
        $stmt = $pdo->prepare("SELECT * FROM activity WHERE id_activity = ?");
        $stmt->execute([$id_activity]);
        $activity = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($activity) {
            // B. Ambil Members yang terlibat
            $stmtMembers = $pdo->prepare("
                SELECT m.id_member, m.nama_member, m.gambar, m.jabatan 
                FROM member m
                JOIN activity_member am ON m.id_member = am.id_member
                WHERE am.id_activity = ?
                ORDER BY 
                CASE WHEN m.jabatan = 'Head of Laboratory' THEN 0 ELSE 1 END,
                m.id_member ASC
            ");
            $stmtMembers->execute([$id_activity]);
            $activity['members'] = $stmtMembers->fetchAll(PDO::FETCH_ASSOC);
        }
    } catch (Exception $e) { }
}

// Redirect jika activity tidak ditemukan
if (!$activity) {
    echo "<script>window.location='index.php?page=activity';</script>";
    exit;
}
?>

<style>
    /* --- CSS HEADER --- */
    .inner-banner.activity-detail-banner {
        background: url('assets/images/header-facility.jpeg') no-repeat center;
        background-size: cover;
        position: relative;
        z-index: 0;
        min-height: 350px;
        display: grid;
        align-items: center;
    }
    .inner-banner.activity-detail-banner:before {
        content: ""; background: rgba(0, 0, 0, 0.6);
        position: absolute; inset: 0; z-index: -1;
    }

    /* CSS KHUSUS HALAMAN DETAIL ACTIVITY */
    .activity-detail-header {
        background: white; border-radius: 12px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        padding: 0; margin-top: -50px;
        position: relative; z-index: 2;
        border: 1px solid #eee;
        overflow: hidden;
    }
    
    /* Header Image Section */
    .activity-header-image {
        position: relative;
        width: 100%;
        height: 350px;
        overflow: hidden;
        background: #9e9e9e;
    }

    .activity-header-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        opacity: 0.9;
    }

    .activity-header-overlay {
        position: absolute;
        inset: 0;
        background: linear-gradient(to bottom, transparent 0%, rgba(0, 0, 0, 0.8) 100%);
        display: flex;
        flex-direction: column;
        justify-content: flex-end;
        padding: 30px;
    }

    /* Overlay for no image - completely transparent, no gradient */
    .activity-header-image.no-image-bg .activity-header-overlay {
        background: transparent;
    }

    .activity-header-image.no-image-bg .activity-header-title {
        color: white;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
    }

    .activity-header-image.no-image-bg .activity-header-meta {
        color: white;
    }

    .activity-header-image.no-image-bg .activity-meta-item i {
        color: #ffb400;
    }

    .activity-header-title {
        color: white;
        font-size: 2.2rem;
        font-weight: 700;
        margin-bottom: 15px;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
        line-height: 1.2;
    }

    .activity-header-meta {
        display: flex;
        gap: 30px;
        color: white;
        font-size: 1rem;
        flex-wrap: wrap;
    }

    .activity-meta-item {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .activity-meta-item i {
        color: #ffb400;
        font-size: 1.1rem;
    }

    /* Content Body */
    .activity-detail-body {
        padding: 40px;
    }

    .detail-section {
        margin-bottom: 40px;
    }

    .section-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: #02406C;
        margin-bottom: 15px;
        padding-bottom: 10px;
        border-bottom: 3px solid #ffb400;
        display: inline-block;
    }

    .activity-description {
        color: #555;
        font-size: 1.05rem;
        line-height: 1.9;
        text-align: justify;
        word-wrap: break-word;
        overflow-wrap: break-word;
    }

    /* Members Grid */
    .members-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        gap: 20px;
        margin-top: 20px;
    }

    .member-card {
        background: #fff;
        border: 1px solid #e0e0e0;
        border-radius: 10px;
        padding: 15px;
        text-align: center;
        transition: all 0.3s ease;
        cursor: pointer;
        text-decoration: none;
        color: inherit;
        display: block;
    }

    .member-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        border-color: #02406C;
    }

    .member-card-avatar {
        width: 70px;
        height: 70px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid #f8f9fa;
        margin: 0 auto 12px;
        background: #eee;
    }

    .member-card-name {
        font-weight: 700;
        color: #333;
        font-size: 1rem;
        margin-bottom: 5px;
    }

    .member-card-role {
        font-size: 0.85rem;
        color: #666;
        font-weight: 600;
        text-transform: uppercase;
    }

    .member-card-badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        margin-top: 10px;
    }

    .badge-head {
        background: #FE7C11;
        color: white;
    }

    .badge-member {
        background: #e0e0e0;
        color: #555;
    }

    .no-members {
        color: #888;
        font-style: italic;
        padding: 30px;
        text-align: center;
        background: #f8f9fa;
        border-radius: 10px;
    }

    /* Category Badge */
    .category-badge {
        display: inline-block;
        padding: 8px 16px;
        background: #e3f2fd;
        color: #1565c0;
        border-radius: 20px;
        font-size: 0.9rem;
        font-weight: 600;
        border: 1px solid #bbdefb;
        margin-left: 10px;
    }

    @media (max-width: 768px) {
        .activity-header-image {
            height: 300px;
        }

        .activity-header-title {
            font-size: 1.8rem;
        }

        .activity-detail-body {
            padding: 25px;
        }

        .activity-header-meta {
            flex-direction: column;
            gap: 10px;
        }

        .members-grid {
            grid-template-columns: 1fr;
        }
    }

    /* Image Lightbox */
    .activity-image-clickable {
        cursor: pointer;
        position: relative;
    }

    .activity-image-clickable::after {
        content: '\f002'; /* FontAwesome search icon */
        font-family: 'Font Awesome 5 Free';
        font-weight: 900;
        position: absolute;
        top: 20px;
        right: 20px;
        background: rgba(255, 255, 255, 0.9);
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .activity-image-clickable:hover::after {
        opacity: 1;
    }

    .lightbox-modal {
        display: none;
        position: fixed;
        z-index: 9999;
        inset: 0;
        background-color: rgba(0, 0, 0, 0.95);
        align-items: center;
        justify-content: center;
    }

    .lightbox-modal.active {
        display: flex;
    }

    .lightbox-content {
        position: relative;
        max-width: 90%;
        max-height: 90vh;
    }

    .lightbox-image {
        max-width: 100%;
        max-height: 90vh;
        object-fit: contain;
        border-radius: 8px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.5);
    }

    .lightbox-close {
        position: absolute;
        top: -40px;
        right: 0;
        color: white;
        font-size: 40px;
        font-weight: bold;
        cursor: pointer;
        background: none;
        border: none;
        padding: 0;
        line-height: 1;
    }

    .lightbox-close:hover {
        color: #ffb400;
    }
</style>

<div class="inner-banner activity-detail-banner" style="min-height: 250px;">
    <section class="w3l-breadcrumb text-center">
        <div class="container">
            <div class="w3breadcrumb-gids">
                <div class="w3breadcrumb-left text-center">
                    <h2 class="inner-w3-title" style="font-size: 2rem;">Activity Detail</h2>
                </div>
                <div class="w3breadcrumb-right">
                    <ul class="breadcrumbs-custom-path">
                        <li><a href="index.php?page=activity">Activities</a></li>
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
            // Setup Data
            $judul = $activity['judul'];
            $deskripsi = $activity['deskripsi'];
            $tanggal = date('d F Y', strtotime($activity['tanggal_kegiatan']));
            $kategori = $activity['kategori'];
            
            // Setup Gambar
            $image_path = $activity['gambar'];
            $hasImage = !empty($image_path);
            if ($hasImage && strpos($image_path, '/') === false) {
                $image_path = $webImgPathActivity . $image_path;
            }
        ?>

        <div class="activity-detail-header">
            
            <!-- Header Image Section -->
            <div class="activity-header-image <?php if(!$hasImage): ?>no-image-bg<?php else: ?>activity-image-clickable<?php endif; ?>" 
                 <?php if($hasImage): ?>onclick="openLightbox('<?= htmlspecialchars($image_path); ?>', '<?= htmlspecialchars($judul); ?>')"<?php endif; ?>>
                <?php if ($hasImage): ?>
                    <img src="<?= htmlspecialchars($image_path); ?>" 
                         alt="<?= htmlspecialchars($judul); ?>"
                         onerror="this.parentElement.style.background='#9e9e9e'; this.parentElement.classList.add('no-image-bg'); this.style.display='none';">
                <?php else: ?>
                    <!-- No Image Placeholder -->
                    <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100%; gap: 12px;">
                        <i class="fas fa-image" style="font-size: 60px; color: rgba(0, 0, 0, 0.2);"></i>
                        <span style="font-size: 1.1rem; color: rgba(0, 0, 0, 0.4); font-weight: 600;">No Image Available</span>
                    </div>
                <?php endif; ?>
                
                <div class="activity-header-overlay">
                    <h1 class="activity-header-title"><?= htmlspecialchars($judul); ?></h1>
                    <div class="activity-header-meta">
                        <div class="activity-meta-item">
                            <i class="far fa-calendar-alt"></i>
                            <span><?= $tanggal; ?></span>
                        </div>
                        <div class="activity-meta-item">
                            <i class="fas fa-tag"></i>
                            <span><?= htmlspecialchars($kategori); ?></span>
                        </div>
                        <?php if (!empty($activity['members'])): ?>
                        <div class="activity-meta-item">
                            <i class="fas fa-users"></i>
                            <span><?= count($activity['members']); ?> Member<?= count($activity['members']) > 1 ? 's' : ''; ?></span>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Content Body -->
            <div class="activity-detail-body">
                
                <!-- Description Section -->
                <div class="detail-section">
                    <h3 class="section-title">Description</h3>
                    <div class="activity-description">
                        <?= $deskripsi ? nl2br(htmlspecialchars($deskripsi)) : 'There is no description for this activity.'; ?>
                    </div>
                </div>

                <!-- Members Section -->
                <div class="detail-section">
                    <h3 class="section-title">Participating Members</h3>
                    
                    <?php if (!empty($activity['members'])): ?>
                        <div class="members-grid">
                            <?php foreach ($activity['members'] as $member): 
                                $member_name = $member['nama_member'];
                                $member_jabatan = $member['jabatan'];
                                $isHead = ($member_jabatan === 'Head of Laboratory');
                                
                                // Setup Avatar
                                $defaultImg = 'https://ui-avatars.com/api/?name=' . urlencode($member_name) . '&background=02406C&color=fff&size=128&length=1';
                                $imgSrc = $defaultImg;
                                
                                if (!empty($member['gambar'])) {
                                    $member_img_path = 'uploads/member/' . $member['gambar'];
                                    if (file_exists($rootPath . '/public/' . $member_img_path)) {
                                        $imgSrc = $member_img_path;
                                    }
                                }
                            ?>
                                <a href="index.php?page=member-detail&id=<?= $member['id_member']; ?>" class="member-card">
                                    <img src="<?= $imgSrc; ?>" alt="<?= htmlspecialchars($member_name); ?>" class="member-card-avatar">
                                    <div class="member-card-name"><?= htmlspecialchars($member_name); ?></div>
                                    <span class="member-card-badge <?= $isHead ? 'badge-head' : 'badge-member'; ?>">
                                        <?= $isHead ? 'Head Lab' : 'Member'; ?>
                                    </span>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="no-members">
                            <i class="fas fa-users" style="font-size: 2rem; margin-bottom: 10px; opacity: 0.3;"></i>
                            <p>No members</p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Back Button -->
                <div style="margin-top: 40px; text-align: center;">
                    <a href="index.php?page=activity" class="btn btn-style btn-primary">
                        <i class="fas fa-arrow-left"></i> Back to Activities
                    </a>
                </div>

            </div>
        </div>
        
    </div>
</section>

<!-- Lightbox Modal -->
<div class="lightbox-modal" id="lightboxModal">
    <div class="lightbox-content">
        <button class="lightbox-close" id="lightboxClose">&times;</button>
        <img src="" alt="" class="lightbox-image" id="lightboxImage">
    </div>
</div>

<script>
// Lightbox functionality
function openLightbox(imageSrc, title) {
    const modal = document.getElementById('lightboxModal');
    const image = document.getElementById('lightboxImage');
    
    image.src = imageSrc;
    image.alt = title;
    
    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeLightbox() {
    const modal = document.getElementById('lightboxModal');
    modal.classList.remove('active');
    document.body.style.overflow = '';
}

document.getElementById('lightboxClose').addEventListener('click', closeLightbox);
document.getElementById('lightboxModal').addEventListener('click', function(e) {
    if (e.target.id === 'lightboxModal') closeLightbox();
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeLightbox();
});
</script>