<?php
// public/pages/facility/index.php

// 1. LOGIC PHP (Tetap di sini)
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

<link rel="stylesheet" href="assets/css/facility.css">

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
            <h3 class="title-w3l mb-4"> Sarana & Prasarana Laboratorium </h3>
        </div>

        <?php if (!empty($fasilitasList)): ?>
            <ul class="gallery_agile">
                <?php foreach ($fasilitasList as $row): ?>
                    <?php 
                        // Logika cek gambar
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

<script src="assets/js/facility.js"></script>