<?php
// ==========================================
// 1. KONEKSI & PERSIAPAN DATA
// ==========================================
$rootPath = dirname(dirname(__DIR__)); 
$koneksiPath = $rootPath . '/config/koneksi.php';

if (file_exists($koneksiPath)) {
    require_once $koneksiPath;
}

$fasilitasList = [];
if (isset($pdo)) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM fasilitas ORDER BY id_fasilitas DESC");
        $stmt->execute();
        $fasilitasList = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) { }
}

// Path Helper
$webThumbPath = 'uploads/thumb/fasilitas-thumb/';
$webImgPath   = 'uploads/fasilitas/';
$serverBase = $rootPath . '/public'; 
$serverThumbPath = $serverBase . '/uploads/thumb/fasilitas-thumb/';
$serverImgPath   = $serverBase . '/uploads/fasilitas/';
?>

<style>
    /* --- CSS HEADER BANNER --- */
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
        content: "";
        background: rgba(0, 0, 0, 0.6); 
        position: absolute;
        top: 0;
        bottom: 0;
        left: 0;
        right: 0;
        z-index: -1;
    }

    /* --- CSS GRID FASILITAS --- */
    .facility-section-bg {
        background-color: var(--bg-color); 
        transition: background-color 0.3s ease;
    }
    ul.gallery_agile {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr)); 
        gap: 30px; 
        padding: 0 !important;
        margin: 0 !important;
        list-style: none !important;
        width: 100%;
        align-items: start;
    }
    .facility-card {
        width: 100%;
        margin-bottom: 30px;
        max-width: 100%; 
        box-sizing: border-box;
        overflow: hidden;
    }
    .facility-img-wrap {
        width: 100%;
        height: 250px; 
        border-radius: var(--border-radius);
        overflow: hidden;
        margin-bottom: 20px;
        position: relative;
        background-color: var(--bg-grey); 
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    }
    .facility-img-wrap a {
        display: block;
        width: 100%;
        height: 100%;
    }
    .facility-img-wrap img {
        width: 100% !important;  
        height: 100% !important; 
        object-fit: cover;
        object-position: center; 
        transition: transform 0.5s ease;
        display: block;
    }
    .facility-img-wrap:hover img {
        transform: scale(1.05);
    }

    /* Placeholder No Image */
    .no-image-box {
        width: 100%;
        height: 100%;
        background-color: var(--bg-lightgrey);
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        color: var(--font-color);
        text-align: center;
        user-select: none;
    }
    .no-image-box i { font-size: 48px; margin-bottom: 10px; }
    .no-image-box span { font-size: 18px; font-weight: 600; font-family: 'Source Sans Pro', sans-serif; }

    .facility-text { padding: 0 5px; }

    /* Typography */
    .facility-title {
        font-size: 22px; 
        font-weight: 800;
        text-transform: uppercase;
        color: var(--heading-color); 
        margin-bottom: 10px;
        line-height: 1.4;
        cursor: text; 
        display: -webkit-box;
        -webkit-line-clamp: 2; 
        -webkit-box-orient: vertical;
        overflow: hidden;
        word-break: break-word; 
    }
    .facility-desc {
        font-size: 16px;
        color: var(--font-color);
        line-height: 1.6;
        margin: 0;
        text-align: left; 
        display: -webkit-box;
        -webkit-line-clamp: 3; 
        -webkit-box-orient: vertical;
        overflow: hidden;
        word-break: break-word; 
    }

    /* --- TOMBOL X (KITA TETAP PERTAHANKAN BIAR ADA DUA OPSI CLOSE) --- */
    #Choco_close {
        position: fixed !important;
        top: 25px !important;
        right: 25px !important;
        z-index: 2147483647 !important;
        width: 44px !important;
        height: 44px !important;
        background: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%23fff'%3e%3cpath d='M.293.293a1 1 0 011.414 0L8 6.586 14.293.293a1 1 0 111.414 1.414L9.414 8l6.293 6.293a1 1 0 01-1.414 1.414L8 9.414l-6.293 6.293a1 1 0 01-1.414-1.414L6.586 8 .293 1.707a1 1 0 010-1.414z'/%3e%3c/svg%3e") no-repeat center center !important;
        background-color: rgba(0, 0, 0, 0.6) !important;
        background-size: 24px 24px !important;
        border: 2px solid rgba(255,255,255,0.8) !important;
        border-radius: 50% !important;
        cursor: pointer !important;
        display: block !important; 
        opacity: 1 !important;     
        box-shadow: 0 4px 10px rgba(0,0,0,0.5) !important;
        transition: transform 0.2s ease !important;
    }
    #Choco_close:hover {
        background-color: #dc3545 !important;
        transform: scale(1.1) !important;
        border-color: #fff !important;
    }

    /* RESPONSIVE */
    @media (max-width: 992px) {
        ul.gallery_agile { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }
    @media (max-width: 768px) {
        ul.gallery_agile { grid-template-columns: repeat(1, minmax(0, 1fr)); }
        .facility-img-wrap { height: 250px; }
        .facility-title { font-size: 20px; }
        #Choco_close {
            top: 15px !important;
            right: 15px !important;
            width: 36px !important;
            height: 36px !important;
            background-size: 18px 18px !important;
        }
    }
</style>

<div class="inner-banner facility-banner">
    <section class="w3l-breadcrumb text-center">
        <div class="container">
            <div class="w3breadcrumb-gids">
                <div class="w3breadcrumb-left text-center">
                    <h2 class="inner-w3-title">Facility</h2>
                </div>
                <div class="w3breadcrumb-right">
                    <ul class="breadcrumbs-custom-path">
                        <li><a href="index.php?page=home">Home</a></li>
                        <li class="active"><span class="fas fa-angle-double-right mx-2"></span> Facility</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>
</div>

<section class="w3l-gallery facility-section-bg pb-5 pt-4">
    <div class="container pb-md-5 pt-3">
        <div class="title-content text-center mb-5">
            <h6 class="title-subw3hny">Explore Our Labs</h6>
            <h3 class="title-w3l mb-4">Laboratory Facilities and Infrastructure</h3>
        </div>

        <?php if (!empty($fasilitasList)): ?>
            <ul class="gallery_agile">
                <?php foreach ($fasilitasList as $row): ?>
                    <?php 
                        $gambar = $row['gambar'];
                        $hasImage = false;
                        $srcThumb = ''; 
                        $srcFull  = '';

                        if (!empty($gambar)) {
                            $ext = pathinfo($gambar, PATHINFO_EXTENSION);
                            $filename = pathinfo($gambar, PATHINFO_FILENAME);
                            $thumbName = $filename . '-thumb.' . $ext;
                            
                            if (file_exists($serverThumbPath . $thumbName)) {
                                $srcThumb = $webThumbPath . $thumbName;
                                $hasImage = true;
                            } elseif (file_exists($serverImgPath . $gambar)) {
                                $srcThumb = $webImgPath . $gambar;
                                $hasImage = true;
                            }

                            if (file_exists($serverImgPath . $gambar)) {
                                $srcFull = $webImgPath . $gambar;
                            } else {
                                $srcFull = $srcThumb;
                            }
                        }
                    ?>

                    <li class="facility-card">
                        <div class="facility-img-wrap">
                            <?php if ($hasImage): ?>
                                <a href="<?= $srcFull ?>" class="chocolat-image" title="<?= htmlspecialchars($row['judul']) ?>">
                                    <img src="<?= $srcThumb ?>" alt="<?= htmlspecialchars($row['judul']) ?>" />
                                </a>
                            <?php else: ?>
                                <div class="no-image-box">
                                    <i class="fa fa-image"></i>
                                    <span>Tidak ada gambar</span>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="facility-text">
                            <div class="facility-title">
                                <?= htmlspecialchars($row['judul']) ?>
                            </div>
                            <div class="facility-desc">
                                <?= nl2br(htmlspecialchars($row['deskripsi'])) ?>
                            </div>
                        </div>
                    </li>

                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <div class="text-center py-5">
                <h4 class="text-muted">Belum ada data fasilitas.</h4>
            </div>
        <?php endif; ?>
    </div>
</section>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        if (typeof $ !== 'undefined' && $.fn.Chocolat) {
            
            // 1. Inisialisasi Plugin
            $('.chocolat-image').Chocolat({
                imageSize: 'contain', 
                loop: true,           
                overlayOpacity: 0.9,  
                closeImg: '', // Kosongkan biar pake SVG kita
                leftImg: '',
                rightImg: ''
            });

            // 2. LOGIKA TAMBAHAN: KLIK BACKGROUND = TUTUP
            // Kita pasang event listener ke 'body' karena overlay dibuat dinamis
            $('body').on('click', '#Choco_overlay', function() {
                // Cari tombol close dan klik secara otomatis
                $('#Choco_close').trigger('click');
            });
        }
    });
</script>