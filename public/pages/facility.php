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
    /* --- CSS UNTUK BANNER (HERO SECTION) --- */
    .inner-banner.facility-banner {
        /* Ganti path ini sesuai lokasi kamu menyimpan gambar */
        background: url('assets/images/header-facility.jpeg') no-repeat center;
        background-size: cover;
        position: relative;
        z-index: 0;
        min-height: 350px; /* Tinggi banner */
        display: grid;
        align-items: center;
    }

    /* Lapisan Gelap (Overlay) agar tulisan putih terbaca */
    .inner-banner.facility-banner:before {
        content: "";
        background: rgba(0, 0, 0, 0.6); /* Hitam transparan 60% */
        position: absolute;
        top: 0;
        bottom: 0;
        left: 0;
        right: 0;
        z-index: -1;
    }

    /* --- CSS UNTUK KONTEN FASILITAS (DARK MODE SUPPORT) --- */
    .facility-section-bg {
        background-color: var(--bg-color); 
        transition: background-color 0.3s ease;
    }

    /* Grid Layout: 3 Kolom */
    ul.gallery_agile {
        display: grid;
        /* UBAH DISINI: repeat(3, ...) artinya 3 kolom */
        grid-template-columns: repeat(3, minmax(0, 1fr)); 
        gap: 30px; /* Gap sedikit diperkecil agar muat 3 kolom */
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
    }

    .facility-img-wrap {
        width: 100%;
        height: 250px; /* Tinggi disesuaikan sedikit agar proporsional dengan lebar 3 kolom */
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

    .facility-title {
        font-size: 22px; /* Font sedikit diperkecil agar pas di 3 kolom */
        font-weight: 800;
        text-transform: uppercase;
        color: var(--heading-color); 
        display: block;
        margin-bottom: 10px;
        line-height: 1.4;
        word-wrap: break-word;
        cursor: text; 
    }

    .facility-desc {
        font-size: 16px;
        color: var(--font-color);
        line-height: 1.6;
        margin: 0;
        text-align: justify;
        word-wrap: break-word;
    }

    /* RESPONSIVE BREAKPOINTS */
    
    /* Tablet (Layar sedang): Jadi 2 Kolom */
    @media (max-width: 992px) {
        ul.gallery_agile {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    /* Mobile (Layar kecil): Jadi 1 Kolom */
    @media (max-width: 768px) {
        ul.gallery_agile { 
            grid-template-columns: repeat(1, minmax(0, 1fr)); 
        }
        .facility-img-wrap { height: 250px; }
        .facility-title { font-size: 20px; }
    }
</style>

<div class="inner-banner facility-banner">
    <section class="w3l-breadcrumb text-center">
        <div class="container">
            <div class="w3breadcrumb-gids">
                <div class="w3breadcrumb-left text-center">
                    <h2 class="inner-w3-title">
                        Facility
                    </h2>
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
            <h3 class="title-w3l mb-4"> Sarana & Prasarana Laboratorium </h3>
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
            $('.chocolat-image').Chocolat();
        }
    });
</script>