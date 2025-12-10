<?php
// ==========================================
// 1. KONEKSI & PERSIAPAN DATA
// ==========================================

// --- PERBAIKAN PATH DI SINI ---
// __DIR__ saat ini = .../public/pages
// Kita perlu mundur 2 langkah ke belakang untuk sampai ke ROOT project
$rootPath = dirname(__DIR__, 2); 
$koneksiPath = $rootPath . '/config/koneksi.php';

// Debugging: Jika masih error, ini akan memberi tahu dimana dia mencari file
if (!file_exists($koneksiPath)) {
    die("<h3>ERROR PATH:</h3> File koneksi tidak ditemukan.<br>Mencari di: <b>" . $koneksiPath . "</b><br>Pastikan struktur foldermu benar.");
}

require_once $koneksiPath;

$activityList = [];
if (isset($pdo)) {
    try {
        // PERBAIKAN QUERY (PostgreSQL Group By)
        // PostgreSQL mewajibkan semua kolom non-agregat masuk ke GROUP BY
        $query = "
            SELECT 
                a.id_activity,
                a.judul,
                a.deskripsi,
                a.tanggal_kegiatan,
                a.gambar,
                STRING_AGG(m.nama_member, ', ') AS members
            FROM activity a
            LEFT JOIN activity_member am ON a.id_activity = am.id_activity
            LEFT JOIN member m ON am.id_member = m.id_member
            GROUP BY a.id_activity, a.judul, a.deskripsi, a.tanggal_kegiatan, a.gambar
            ORDER BY a.tanggal_kegiatan DESC
        ";
        
        $stmt = $pdo->prepare($query);
        $stmt->execute();
        $activityList = $stmt->fetchAll(PDO::FETCH_ASSOC);

    } catch (PDOException $e) { 
        die("ERROR QUERY DATABASE: " . $e->getMessage());
    }
} else {
    die("Koneksi Database Gagal ($pdo belum terdefinisi).");
}

// ==========================================
// 2. PENGATURAN PATH GAMBAR
// ==========================================
// Karena file ini ada di 'public/pages/', kita perlu keluar folder ('../') untuk ke 'uploads'
$webThumbPath    = 'uploads/thumb/activity-thumb/';
$webImgPath      = 'uploads/activity/';

// Path server (Absolute path) untuk pengecekan file_exists
$serverBase      = $rootPath . '/public'; 
$serverThumbPath = $serverBase . '/uploads/thumb/activity-thumb/';
$serverImgPath   = $serverBase . '/uploads/activity/';
?>

<style>
    /* --- BANNER ACTIVITY --- */
    .inner-banner.activity-banner {
        background: url('assets/images/header-facility.jpeg') no-repeat center;
        background-size: cover;
        position: relative;
        z-index: 0;
        min-height: 350px;
        display: grid;
        align-items: center;
        justify-content: center;
        text-align: center;
    }

    /* Overlay Gelap */
    .inner-banner.activity-banner:before {
        content: "";
        background: rgba(0, 0, 0, 0.6);
        position: absolute;
        inset: 0;
        z-index: -1;
    }

    .inner-banner.activity-banner h2 {
        font-size: 3rem;
        font-weight: 700;
        color: white;
        margin-bottom: 10px;
    }

    /* Breadcrumb */
    .breadcrumb-activity {
        color: white;
        font-size: 1rem;
    }

    .breadcrumb-activity a {
        color: #ffb400;
        text-decoration: none;
    }

    .breadcrumb-activity span {
        margin: 0 5px;
    }

    /* Grid Layout */
    .activity-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 30px;
        margin-top: 2rem;
    }

    /* Card Style */
    .activity-card {
        background: white;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        display: flex;
        flex-direction: column;
    }

    .activity-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
    }

    .activity-image-wrapper {
        position: relative;
        width: 100%;
        height: 250px;
        overflow: hidden;
        background-color: #e0e0e0;
    }

    .activity-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }

    .activity-card:hover .activity-image {
        transform: scale(1.1);
    }

    .activity-title-overlay {
        position: absolute;
        inset: 0;
        background: rgba(0, 0, 0, 0.7);
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.3s ease;
        padding: 20px;
    }

    .activity-card:hover .activity-title-overlay {
        opacity: 1;
    }

    .activity-title {
        color: white;
        font-size: 1.2rem;
        font-weight: 600;
        text-align: center;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
        line-height: 1.3;
    }

    .activity-content {
        padding: 20px;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
    }

    .activity-date {
        color: #888;
        font-size: 0.85rem;
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .activity-description {
        color: #555;
        font-size: 0.95rem;
        line-height: 1.6;
        margin-bottom: 15px;
        flex-grow: 1;
    }

    .activity-members {
        font-size: 0.9rem;
        color: #333;
        font-weight: 500;
        border-top: 1px solid #eee;
        padding-top: 10px;
    }

    .activity-members span {
        font-weight: normal;
        color: #555;
        display: block;
        margin-top: 3px;
    }

    .no-activity {
        text-align: center;
        color: #888;
        grid-column: 1 / -1;
        padding: 40px;
        font-size: 1.1rem;
    }
</style>

<section class="inner-banner activity-banner">
    <div>
        <h2>Activity</h2>

        <div class="breadcrumb-activity">
            <a href="index.php">Home</a>
            <span>›</span>
            <span>Activity</span>
        </div>
    </div>
</section>

<section class="py-5 activity-section-bg">
    <div class="container py-md-5 py-3">
        <h3 class="title-w3l mb-4 text-center">Our Activities</h3>

        <div class="activity-grid">
            <?php if (!empty($activityList)): ?>
                <?php foreach ($activityList as $row): ?>
                    <?php 
                        // --- LOGIKA CEK GAMBAR ---
                        $gambar = $row['gambar'];
                        $srcDisplay = ''; 
                        $hasImage = false; // Default anggap tidak ada gambar

                        if (!empty($gambar)) {
                            $ext       = pathinfo($gambar, PATHINFO_EXTENSION);
                            $filename  = pathinfo($gambar, PATHINFO_FILENAME);
                            $thumbName = $filename . '-thumb.' . $ext;
                            
                            // 1. Cek Thumbnail di Server
                            if (file_exists($serverThumbPath . $thumbName)) {
                                $srcDisplay = $webThumbPath . $thumbName;
                                $hasImage = true;
                            } 
                            // 2. Cek Gambar Asli di Server
                            elseif (file_exists($serverImgPath . $gambar)) {
                                $srcDisplay = $webImgPath . $gambar;
                                $hasImage = true;
                            }
                        }
                    ?>

                    <div class="activity-card">
                        <div class="activity-image-wrapper">
                            
                            <?php if ($hasImage): ?>
                                <img src="<?= htmlspecialchars($srcDisplay); ?>"
                                     alt="<?= htmlspecialchars($row['judul']); ?>"
                                     class="activity-image">
                            <?php else: ?>
                                <div style="display: flex; flex-direction: column; justify-content: center; align-items: center; height: 100%; background-color: #f0f0f0; color: #999;">
                                    <i class="fas fa-image" style="font-size: 48px; margin-bottom: 10px;"></i>
                                    <span style="font-size: 14px;">Tidak ada gambar</span>
                                </div>
                            <?php endif; ?>

                            <div class="activity-title-overlay">
                                <h4 class="activity-title"><?= htmlspecialchars($row['judul']); ?></h4>
                            </div>
                        </div>

                        <div class="activity-content">
                            <div class="activity-date">
                                <?php
                                    $date = date_create($row['tanggal_kegiatan']);
                                    echo date_format($date, 'd F Y');
                                ?>
                            </div>
                            <div class="activity-description">
                                <?= nl2br(htmlspecialchars($row['deskripsi'])); ?>
                            </div>
                            <div class="activity-members">
                                <strong>Member Berpartisipasi:</strong>
                                <span>
                                    <?= !empty($row['members']) ? htmlspecialchars($row['members']) : '-'; ?>
                                </span>
                            </div>
                        </div>
                    </div>

                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-activity">
                    <p>Belum ada aktivitas yang tersedia saat ini.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>