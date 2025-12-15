<?php
// ==========================================
// 1. KONFIGURASI PATH DAN DATABASE
// ==========================================
$rootPath = dirname(dirname(__DIR__)); 
$koneksiPath = $rootPath . '/config/koneksi.php';

if (file_exists($koneksiPath)) {
    require_once $koneksiPath;
} else {
    $altKoneksiPath = $_SERVER['DOCUMENT_ROOT'] . '/config/koneksi.php';
    if (file_exists($altKoneksiPath)) {
        require_once $altKoneksiPath;
    }
}
?>

<style>
    /* =======================================================
       1. HEADER BANNER - STYLE PERSIS FACILITY/ACTIVITY
       ======================================================= */
    .inner-banner.partner-banner {
        background: url('assets/images/header-facility.jpeg') no-repeat center;
        background-size: cover;
        position: relative;
        z-index: 0;
        min-height: 350px;
        display: grid;
        align-items: center;
    }

    /* Overlay Gelap */
    .inner-banner.partner-banner:before {
        content: "";
        background: rgba(0, 0, 0, 0.6);
        position: absolute;
        top: 0; bottom: 0; left: 0; right: 0;
        z-index: -1;
    }

    /* JUDUL BESAR "Partner" (Tebal & Putih) */
    h2.inner-w3-title {
        font-family: 'Source Sans Pro', sans-serif;
        font-size: 56px !important;
        line-height: 1.1;
        font-weight: 800 !important; /* Extra Bold */
        text-transform: capitalize;
        color: #fff;
        margin-bottom: 0;
        text-align: center;
    }

    /* BREADCRUMB CONTAINER */
    .w3breadcrumb-gids {
        text-align: center;
        width: 100%;
        display: block;
    }

    .w3breadcrumb-right {
        margin-top: 10px;
        display: flex;
        justify-content: center;
    }

    /* LIST BREADCRUMB */
    ul.breadcrumbs-custom-path {
        padding: 0;
        margin: 0;
        list-style: none;
        display: flex;
        align-items: center;
        justify-content: center;
        font-family: 'Source Sans Pro', sans-serif;
    }

    ul.breadcrumbs-custom-path li {
        display: inline-block;
        font-size: 16px;
        font-weight: 700; /* Bold */
        line-height: 1.5;
    }

    /* LINK "Home" (Kuning Emas) */
    ul.breadcrumbs-custom-path li a {
        color: #ecb41c !important; /* Warna Kuning Emas */
        text-decoration: none;
        opacity: 0.9;
        transition: 0.3s ease;
    }

    ul.breadcrumbs-custom-path li a:hover {
        color: #fff !important;
        opacity: 1;
    }

    /* ICON PANAH & TEKS "Partner" */
    ul.breadcrumbs-custom-path li span.fa-angle-double-right {
        font-size: 12px;
        margin: 0 12px;
        color: #fff;
        font-weight: 700;
        opacity: 0.8;
    }

    ul.breadcrumbs-custom-path li.active {
        color: #fff;
        font-weight: 700;
        text-transform: capitalize;
    }

    /* Responsive */
    @media (max-width: 992px) {
        h2.inner-w3-title {
            font-size: 40px !important;
            line-height: 50px;
        }
    }

    @media (max-width: 600px) {
        h2.inner-w3-title {
            font-size: 32px !important;
        }
    }

    /* =======================================================
       2. PARTNER CONTENT STYLING (TIDAK DIUBAH)
       ======================================================= */
    .partner-section-bg {
        background-color: #f8f9fa;
    }

    .category-section {
        margin-bottom: 4rem;
        position: relative;
    }

    .category-title {
        font-size: 2rem;
        font-weight: 700;
        color: #02406C;
        margin-bottom: 1.5rem;
        padding-left: 15px;
        border-left: 5px solid #ff9800;
        display: inline-block;
    }

    /* Carousel Wrapper */
    .partner-carousel-wrapper {
        position: relative;
        width: 100%;
        display: flex;
        align-items: center;
    }

    /* Track Scroll */
    .partner-track {
        display: flex;
        gap: 25px;
        overflow-x: auto;
        scroll-behavior: smooth;
        padding: 20px 5px;
        width: 100%;
        -ms-overflow-style: none;  
        scrollbar-width: none;  
    }
    .partner-track::-webkit-scrollbar { display: none; }

    /* CARD STYLE */
    .partner-card {
        background: white;
        min-width: 260px;
        max-width: 260px;
        height: 280px;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        display: flex;
        flex-direction: column;
        border: 1px solid #f0f0f0;
    }

    .partner-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 12px 25px rgba(0, 0, 0, 0.15);
        border-color: #ff9800;
    }

    /* Image Wrapper */
    .partner-logo-wrapper {
        width: 100%;
        height: 180px;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 25px;
        background-color: #fff;
        border-bottom: 1px solid #f9f9f9;
    }

    .partner-logo {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
        transition: transform 0.5s ease;
        filter: grayscale(100%);
        opacity: 0.8;
    }

    .partner-card:hover .partner-logo { 
        transform: scale(1.1); 
        filter: grayscale(0%);
        opacity: 1;
    }

    .no-logo-placeholder {
        color: #ddd;
        text-align: center;
    }
    .no-logo-placeholder i { font-size: 40px; margin-bottom: 5px; }

    .partner-content {
        padding: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        flex-grow: 1;
        background-color: #fff;
    }

    .partner-name {
        font-size: 1rem;
        font-weight: 700;
        color: #333;
        line-height: 1.3;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    /* Nav Buttons */
    .nav-btn {
        position: absolute; top: 50%; transform: translateY(-50%);
        width: 50px; height: 50px;
        background-color: #fff; border: 1px solid #eee; border-radius: 50%;
        color: #333; font-size: 18px;
        display: flex; align-items: center; justify-content: center;
        cursor: pointer; z-index: 10;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        transition: all 0.3s ease; opacity: 1;
    }
    .nav-btn:hover { background-color: #02406C; color: white; border-color: #02406C; }
    .nav-btn.hidden { opacity: 0; pointer-events: none; }
    
    .prev-btn { left: -25px; }
    .next-btn { right: -25px; }

    @media (max-width: 768px) {
        .nav-btn { display: none !important; }
        .partner-track { padding-right: 20px; }
    }

    .no-data { text-align: center; color: #888; padding: 40px; width: 100%; font-size: 1.1rem; }
</style>

<div class="inner-banner partner-banner">
    <section class="w3l-breadcrumb text-center">
        <div class="container">
            <div class="w3breadcrumb-gids">
                
                <div class="w3breadcrumb-left text-center">
                    <h2 class="inner-w3-title">Partner</h2>
                </div>
                
                <div class="w3breadcrumb-right">
                    <ul class="breadcrumbs-custom-path">
                        <li><a href="index.php?page=home">Home</a></li>
                        <li class="active">
                            <span class="fas fa-angle-double-right" aria-hidden="true"></span> Partner
                        </li>
                    </ul>
                </div>

            </div>
        </div>
    </section>
</div>

<section class="py-5 partner-section-bg">
    <div class="container py-md-5 py-3">

        <?php
        if (!isset($pdo)) {
            echo '<p class="no-data">Koneksi database belum tersedia.</p>';
        } else {
            try {
                // Query Database
                $stmt = $pdo->prepare("SELECT * FROM partner ORDER BY id_partner DESC");
                $stmt->execute();
                $allPartners = $stmt->fetchAll(PDO::FETCH_ASSOC);

                // Grouping Data
                $grouped = [
                    'Industry Partner'           => [],
                    'Educational Institutions'   => [],
                    'Government Institutions'    => [],
                    'International Institutions' => [],
                    'Other'                      => [] 
                ];

                if (count($allPartners) > 0) {
                    foreach ($allPartners as $row) {
                        $cat = $row['kategori'];
                        if (array_key_exists($cat, $grouped)) {
                            $grouped[$cat][] = $row;
                        } else {
                            $grouped['Other'][] = $row;
                        }
                    }
                }

                // Render Data
                $hasData = false;
                foreach ($grouped as $kategoriName => $listPartner) {
                    if (!empty($listPartner)) {
                        $hasData = true;
                        ?>
                        
                        <div class="category-section">
                            <h4 class="category-title"><?= htmlspecialchars($kategoriName); ?></h4>
                            
                            <div class="partner-carousel-wrapper">
                                <button class="nav-btn prev-btn hidden"><i class="fas fa-chevron-left"></i></button>

                                <div class="partner-track">
                                    <?php foreach ($listPartner as $row): 
                                        $rawImg = $row['gambar'];
                                        $hasImage = false;
                                        $thumbPath = '';
                                        $originalPath = '';

                                        if (!empty($rawImg)) {
                                            $hasImage = true;
                                            $originalPath = 'uploads/partner/' . $rawImg;
                                            $info = pathinfo($rawImg);
                                            $ext = isset($info['extension']) ? '.' . $info['extension'] : '';
                                            $filename = $info['filename'];
                                            $thumbName = $filename . '-thumb' . $ext;
                                            $thumbPath = 'uploads/thumb/partner-thumb/' . $thumbName;
                                        }
                                    ?>
                                        <div class="partner-card">
                                            
                                            <div class="partner-logo-wrapper">
                                                <?php if ($hasImage): ?>
                                                    <img src="<?= htmlspecialchars($thumbPath); ?>" 
                                                         alt="<?= htmlspecialchars($row['nama']); ?>" 
                                                         class="partner-logo"
                                                         loading="lazy"
                                                         onerror="this.onerror=null; this.src='<?= htmlspecialchars($originalPath); ?>';">
                                                <?php else: ?>
                                                    <div class="no-logo-placeholder">
                                                        <i class="fas fa-image"></i>
                                                        <span>No Logo</span>
                                                    </div>
                                                <?php endif; ?>
                                            </div>

                                            <div class="partner-content">
                                                <div class="partner-name">
                                                    <?= htmlspecialchars($row['nama']); ?>
                                                </div>
                                            </div>

                                        </div>
                                    <?php endforeach; ?>
                                </div>

                                <button class="nav-btn next-btn hidden"><i class="fas fa-chevron-right"></i></button>
                            </div>
                        </div>
                        <?php
                    }
                }

                if (!$hasData) {
                    echo '<div class="no-data">Belum ada partner yang tersedia saat ini.</div>';
                }

            } catch (PDOException $e) {
                echo '<div class="no-data">Terjadi kesalahan sistem database.</div>';
            }
        }
        ?>

    </div>
</section>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const carousels = document.querySelectorAll('.partner-carousel-wrapper');

    carousels.forEach(wrapper => {
        const track = wrapper.querySelector('.partner-track');
        const prevBtn = wrapper.querySelector('.prev-btn');
        const nextBtn = wrapper.querySelector('.next-btn');
        const scrollAmount = 280; 

        const checkArrows = () => {
            const maxScrollLeft = track.scrollWidth - track.clientWidth - 1;
            
            if (track.scrollLeft <= 0) {
                prevBtn.classList.add('hidden');
            } else {
                prevBtn.classList.remove('hidden');
            }

            if (track.scrollWidth <= track.clientWidth || track.scrollLeft >= maxScrollLeft) {
                nextBtn.classList.add('hidden');
            } else {
                nextBtn.classList.remove('hidden');
            }
        };

        if (prevBtn && nextBtn) {
            prevBtn.addEventListener('click', () => {
                track.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
            });

            nextBtn.addEventListener('click', () => {
                track.scrollBy({ left: scrollAmount, behavior: 'smooth' });
            });

            track.addEventListener('scroll', checkArrows);
            window.addEventListener('resize', checkArrows);
            
            setTimeout(checkArrows, 100);
        }
    });
});
</script>