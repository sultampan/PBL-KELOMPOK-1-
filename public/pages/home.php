<?php
// ==========================================
// 1. KONEKSI & LOGIKA DATA
// ==========================================
require_once __DIR__ . "/../../config/koneksi.php";

// --- A. PRODUK (Limit 3) ---
try {
    $stmtProd = $pdo->prepare("SELECT * FROM produk ORDER BY id_produk ASC LIMIT 3");
    $stmtProd->execute();
    $produk = $stmtProd->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) { $produk = []; }

// --- B. FACILITY (Limit 6) ---
$limitFac = 6; 
$pageFac  = isset($_GET['halaman']) ? (int)$_GET['halaman'] : 1; // Ubah parameter jadi 'halaman' agar sinkron
if ($pageFac < 1) $pageFac = 1;
$offsetFac = ($pageFac - 1) * $limitFac;
$searchKeyword = isset($_GET['search']) ? trim($_GET['search']) : '';

// Function untuk Render Pagination (Sama dengan Facility.php)
function renderPaginationHome($currentPage, $totalPages) {
    if ($totalPages <= 1) return '';
    
    $maxVisible = 5;
    $half = floor($maxVisible / 2);
    $startPage = $currentPage - $half;
    if ($startPage < 1) $startPage = 1;
    $endPage = $startPage + $maxVisible - 1;
    if ($endPage > $totalPages) {
        $endPage = $totalPages;
        $startPage = max(1, $endPage - $maxVisible + 1);
    }

    $html = '<div class="pagination-wrapper">';
    $disabledFirst = ($currentPage == 1) ? 'disabled' : '';
    $disabledPrev = ($currentPage == 1) ? 'disabled' : '';
    $prevPage = max(1, $currentPage - 1);

    $html .= '<button class="page-btn first" onclick="goToPage(1)" '.$disabledFirst.'><i class="fas fa-angle-double-left"></i></button>';
    $html .= '<button class="page-btn prev" onclick="goToPage('.$prevPage.')" '.$disabledPrev.'><i class="fas fa-angle-left"></i></button>';

    for ($i = $startPage; $i <= $endPage; $i++) {
        $activeClass = ($i == $currentPage) ? 'active' : '';
        $html .= '<button class="page-btn '.$activeClass.'" onclick="goToPage('.$i.')">'.$i.'</button>';
    }

    $disabledNext = ($currentPage == $totalPages) ? 'disabled' : '';
    $disabledLast = ($currentPage == $totalPages) ? 'disabled' : '';
    $nextPage = min($totalPages, $currentPage + 1);

    $html .= '<button class="page-btn next" onclick="goToPage('.$nextPage.')" '.$disabledNext.'><i class="fas fa-angle-right"></i></button>';
    $html .= '<button class="page-btn last" onclick="goToPage('.$totalPages.')" '.$disabledLast.'><i class="fas fa-angle-double-right"></i></button>';

    $html .= '</div>';
    return $html;
}

// Logic Query Facility
$fasilitasHome = [];
$totalPagesFac = 1;
try {
    // 1. Hitung Total (Dengan Search)
    $sqlCount = "SELECT COUNT(*) FROM fasilitas";
    if(!empty($searchKeyword)) { $sqlCount .= " WHERE judul LIKE :k OR deskripsi LIKE :k"; }
    $stmtCount = $pdo->prepare($sqlCount);
    if(!empty($searchKeyword)) { $stmtCount->bindValue(':k', "%$searchKeyword%"); }
    $stmtCount->execute();
    $totalDataFac = $stmtCount->fetchColumn();
    $totalPagesFac = ceil($totalDataFac / $limitFac);

    // 2. Ambil Data
    $sqlFac = "SELECT * FROM fasilitas";
    if(!empty($searchKeyword)) { $sqlFac .= " WHERE judul LIKE :k OR deskripsi LIKE :k"; }
    $sqlFac .= " ORDER BY id_fasilitas DESC LIMIT :limit OFFSET :offset";
    
    $stmtFac = $pdo->prepare($sqlFac);
    if(!empty($searchKeyword)) { $stmtFac->bindValue(':k', "%$searchKeyword%"); }
    $stmtFac->bindValue(':limit', $limitFac, PDO::PARAM_INT);
    $stmtFac->bindValue(':offset', $offsetFac, PDO::PARAM_INT);
    $stmtFac->execute();
    $fasilitasHome = $stmtFac->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) { $fasilitasHome = []; }

// Path Helper Facility
$webThumbPathF = 'uploads/thumb/fasilitas-thumb/';
$webImgPathF   = 'uploads/fasilitas/';
$serverThumbPathF = dirname(dirname(__DIR__)) . '/public/uploads/thumb/fasilitas-thumb/';
$serverImgPathF   = dirname(dirname(__DIR__)) . '/public/uploads/fasilitas/';

// --- C. ACTIVITY (Limit 9) ---
try {
    $stmtAct = $pdo->prepare("SELECT id_activity, judul, deskripsi, gambar FROM activity ORDER BY tanggal_kegiatan DESC LIMIT 9");
    $stmtAct->execute();
    $activities = $stmtAct->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) { $activities = []; }
?>

<section class="w3l-main-slider banner-slider" id="home">
    <div class="owl-one owl-carousel owl-theme">
        <div class="item">
            <div class="slider-info banner-view banner-top1">
                <div class="container">
                    <div class="banner-info header-hero-19">
                        <h3 class="title-hero-19">Laboratory for Applied Informatics</h3>
                        <p class="w3ban-para">The Applied Informatics Laboratory at Malang State Polytechnic is an innovation center focused on developing information technology-based solutions.</p>
                        <a href="index.php?page=about" class="btn btn-style btn-primary mt-sm-5 mt-4">
                            Read More <i class="fas fa-angle-double-right ms-2"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
.box-wrap { height: 460px; display: flex; flex-direction: column; justify-content: flex-start; padding: 30px; border-radius: 12px; background: #f7f7f7; }
</style>
<section class="w3l-features py-5" id="work">
    <div class="container py-lg-5 py-md-4 py-2">
        <div class="title-content text-center mb-lg-3 mb-4">
            <h6 class="title-subw3hny mb-1">Our Products</h6>
            <h3 class="title-w3l">Innovative Solutions Developed by the Applied Informatics Laboratory</h3>
        </div>
        <div class="main-cont-wthree-2">
            <div class="row justify-content-center">
                <?php foreach ($produk as $p): ?>
                    <div class="col-lg-4 col-md-6 mt-lg-5 mt-4">
                        <div class="grids-1 box-wrap">
                            <div class="icon">
                                <?php 
                                    $gambar = $p['gambar']; 
                                    $ext = pathinfo($gambar, PATHINFO_EXTENSION);
                                    $base = pathinfo($gambar, PATHINFO_FILENAME);
                                    $thumbName = $base . '-thumb.' . $ext; 
                                    $srcThumb = "uploads/thumb/produk-thumb/" . $thumbName;
                                    $srcAsli  = "uploads/produk/" . $gambar;
                                ?>
                                <img src="<?php echo htmlspecialchars($srcThumb); ?>"
                                     alt="<?php echo htmlspecialchars($p['nama']); ?>"
                                     style="width: 180px; height: 120px; object-fit: contain; border-radius: 8px;"
                                     onerror="this.onerror=null; this.src='<?php echo htmlspecialchars($srcAsli); ?>';">
                            </div>
                            <h4><a href="<?php echo htmlspecialchars($p['link_produk']); ?>" class="title-head mb-3" target="_blank"><?php echo htmlspecialchars($p['nama']); ?></a></h4>
                            <p class="text-para"><?php echo htmlspecialchars($p['deskripsi']); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="text-center mt-5">
                <a href="index.php?page=product" class="btn btn-style btn-primary mt-lg-5 mt-4 me-2">Read More <i class="fas fa-angle-double-right ms-2"></i></a>
            </div>
        </div>
    </div>
</section>

<section class="w3l-gallery facility-section-home" id="facility-home">
    <div class="container pb-md-5 pt-3">
        
        <div class="title-content text-center mb-2">
            <h6 class="title-subw3hny">Explore Our Labs</h6>
            <h3 class="title-w3l">Laboratory Facilities and Infrastructure</h3>
        </div>

        <div class="search-wrapper-center">
            <div class="search-facility-box">
                <form action="#" method="GET" onsubmit="return false;">
                    <input type="text" id="searchInput" placeholder="Search..." value="<?= htmlspecialchars($searchKeyword) ?>"
                           oninput="performLiveSearch(this.value, 1)"> 
                    <i id="staticSearchIcon" class="fas fa-search search-icon-static"></i>
                    <div id="searchSpinner" class="search-loading"><i class="fas fa-spinner fa-spin"></i></div>
                </form>
            </div>
        </div>

        <div id="facility-results-container">
            <?php if (!empty($fasilitasHome)): ?>
                <ul class="gallery_agile">
                    <?php foreach ($fasilitasHome as $row): ?>
                        <?php 
                            $gambar = $row['gambar']; $hasImage = false; $srcThumb = ''; $srcFull = '';
                            if (!empty($gambar)) {
                                $ext = pathinfo($gambar, PATHINFO_EXTENSION);
                                $filename = pathinfo($gambar, PATHINFO_FILENAME);
                                $thumbName = $filename . '-thumb.' . $ext;
                                if (file_exists($serverThumbPathF . $thumbName)) {
                                    $srcThumb = $webThumbPathF . $thumbName; $hasImage = true;
                                } elseif (file_exists($serverImgPathF . $gambar)) {
                                    $srcThumb = $webImgPathF . $gambar; $hasImage = true;
                                }
                                $srcFull = (file_exists($serverImgPathF . $gambar)) ? $webImgPathF . $gambar : $srcThumb;
                            }
                        ?>
                        <li class="facility-card">
                            <div class="facility-img-wrap">
                                <?php if ($hasImage): ?>
                                    <a href="<?= $srcFull ?>" class="chocolat-image" title="<?= htmlspecialchars($row['judul']) ?>">
                                        <img src="<?= $srcThumb ?>" alt="<?= htmlspecialchars($row['judul']) ?>" />
                                    </a>
                                <?php else: ?>
                                    <div class="no-image-box"><i class="fa fa-image"></i><span>Tidak ada gambar</span></div>
                                <?php endif; ?>
                            </div>
                            <div class="facility-text">
                                <div class="facility-title"><?= htmlspecialchars($row['judul']) ?></div>
                                <div class="facility-desc"><?= nl2br(htmlspecialchars($row['deskripsi'])) ?></div>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <div class="text-center py-5"><h4 class="text-muted">Belum ada data fasilitas.</h4></div>
            <?php endif; ?>

            <?= renderPaginationHome($pageFac, $totalPagesFac) ?>
        </div>

    </div>
</section>

<section class="w3l-gallery" id="gallery">
    <div class="destionation-innf py-5">
        <div class="container py-lg-5 py-md-4 py-2 HomePageGallery">
            <div class="title-content text-center">
                <h6 class="title-subw3hny text-center">Laboratory Activities</h6>
                <h3 class="title-w3l mb-5 text-center">Latest Research</h3>
            </div>
            <ul class="gallery_agile">
                <?php
                if (count($activities) > 0) {
                    foreach ($activities as $activity) {
                        $imagePath = "uploads/activity/" . htmlspecialchars($activity['gambar']);
                        $title = htmlspecialchars($activity['judul']);
                        ?>
                        <li>
                            <div class="w3_agile_portfolio_grid">
                                <a href="javascript:void(0);" class="gallery-item" data-image="<?php echo $imagePath; ?>" data-title="<?php echo $title; ?>">
                                    <img src="<?php echo $imagePath; ?>" alt="<?php echo $title; ?>" class="img-fluid radius-image" />
                                    <div class="w3layouts_news_grid_pos">
                                        <div class="wthree_text">
                                            <h3><?php echo $title; ?></h3>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </li>
                        <?php
                    }
                } else {
                    echo '<li><p class="text-center">Belum ada kegiatan.</p></li>';
                }
                ?>
            </ul>
        </div>
    </div>
</section>

<div id="imageModal" class="modal-gallery" style="display:none;">
    <span class="close-modal">&times;</span>
    <img class="modal-content-gallery" id="modalImage">
    <div id="modalCaption"></div>
</div>

<style>
.modal-gallery { display: none; position: fixed; z-index: 9999; padding-top: 50px; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.9); }
.modal-content-gallery { margin: auto; display: block; max-width: 90%; max-height: 80%; animation: zoom 0.3s; }
@keyframes zoom { from {transform: scale(0.8)} to {transform: scale(1)} }
.close-modal { position: absolute; top: 15px; right: 35px; color: #f1f1f1; font-size: 40px; font-weight: bold; transition: 0.3s; cursor: pointer; z-index: 10000; }
.close-modal:hover { color: #bbb; text-decoration: none; cursor: pointer; }
#modalCaption { margin: auto; display: block; width: 80%; max-width: 700px; text-align: center; color: #ccc; padding: 10px 0; font-size: 18px; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var modal = document.getElementById('imageModal');
    var modalImg = document.getElementById('modalImage');
    var captionText = document.getElementById('modalCaption');
    var closeBtn = document.getElementsByClassName('close-modal')[0];
    var galleryItems = document.querySelectorAll('.gallery-item');
    
    galleryItems.forEach(function(item) {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            modal.style.display = 'block';
            modalImg.src = this.getAttribute('data-image');
            captionText.innerHTML = this.getAttribute('data-title');
        });
    });
    
    if(closeBtn) closeBtn.addEventListener('click', function() { modal.style.display = 'none'; });
    if(modal) modal.addEventListener('click', function(e) { if (e.target === modal) modal.style.display = 'none'; });
});
</script>

<section class="w3l-passion-mid-sec py-5">
    <div class="container py-md-5 py-3">
        <div class="container">
            <div class="row w3l-passion-mid-grids">
                <div class="col-lg-6 passion-grid-item-info pe-lg-5 mb-lg-0 mb-5">
                    <h6 class="title-subw3hny mb-1">What We Offer</h6>
                    <h3 class="title-w3l mb-4">Our Project</h3>
                    <p class="mt-3 pe-lg-5">Our lab showcases a diverse range of applied AI projects designed to solve real-world problems and support innovation across multiple domains.</p>
                    <div class="w3banner-content-btns">
                        <a href="index.php?page=activity" class="btn btn-style btn-primary mt-lg-5 mt-4 me-2">Read More <i class="fas fa-angle-double-right ms-2"></i></a>
                    </div>
                </div>
                <div class="col-lg-6 passion-grid-item-info">
                    <img src="assets/images/g3.jpg" alt="" class="img-fluid radius-image">
                </div>
            </div>
        </div>
    </div>
</section>

<section class="w3l-testimonials" id="testimonials">
    <div class="cusrtomer-layout py-5">
        <div class="container py-lg-4 py-md-3 py-2 pb-lg-0">
            <div class="title-content text-center">
                <h6 class="title-subw3hny">Our Partners</h6>
                <h3 class="title-w3l mb-5">Collaborative Partners</h3>
            </div>
            <div class="partners-carousel-wrapper pt-lg-4">
                <div class="carousel-partners-container">
                    <button class="carousel-btn-partner btn-prev" id="prevBtnPartner">‹</button>
                    <div class="carousel-track-wrapper">
                        <div class="carousel-track-partner" id="carouselTrackPartner">
                            <div class="logo-card-partner"><img src="assets/images/team1.jpg" alt="Partner"></div>
                            <div class="logo-card-partner"><img src="assets/images/team2.jpg" alt="Partner"></div>
                            <div class="logo-card-partner"><img src="assets/images/team3.jpg" alt="Partner"></div>
                            <div class="logo-card-partner"><img src="assets/images/team1.jpg" alt="Partner"></div>
                            <div class="logo-card-partner"><img src="assets/images/team2.jpg" alt="Partner"></div>
                        </div>
                    </div>
                    <button class="carousel-btn-partner btn-next" id="nextBtnPartner">›</button>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
.partners-carousel-wrapper { position: relative; max-width: 1200px; margin: 0 auto; }
.carousel-partners-container { position: relative; display: flex; align-items: center; justify-content: center; gap: 20px; background: white; padding: 40px 20px; border-radius: 10px; }
.carousel-track-wrapper { overflow: hidden; width: 100%; max-width: 900px; }
.carousel-track-partner { display: flex; transition: transform 0.5s ease-in-out; gap: 20px; }
.logo-card-partner { flex: 0 0 auto; width: 180px; height: 140px; background: white; border-radius: 10px; display: flex; align-items: center; justify-content: center; padding: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); }
.logo-card-partner img { max-width: 100%; max-height: 100%; object-fit: contain; }
.carousel-btn-partner { background: white; border: 2px solid #e0e0e0; width: 50px; height: 50px; border-radius: 50%; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 24px; color: #333; }
.carousel-btn-partner:hover { background: #f8f9fa; border-color: #007bff; color: #007bff; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const trackPartner = document.getElementById('carouselTrackPartner');
    const prevBtnPartner = document.getElementById('prevBtnPartner');
    const nextBtnPartner = document.getElementById('nextBtnPartner');
    if(!trackPartner) return;
    
    let currentIndexPartner = 0;
    let visibleCardsPartner = 4;

    function updateCarouselPartner() {
        const cards = trackPartner.querySelectorAll('.logo-card-partner');
        if(cards.length === 0) return;
        const cardWidth = cards[0].offsetWidth;
        const gap = 20;
        const moveDistance = (cardWidth + gap) * currentIndexPartner;
        trackPartner.style.transform = `translateX(-${moveDistance}px)`;
    }

    if(nextBtnPartner) {
        nextBtnPartner.addEventListener('click', function() {
            const cards = trackPartner.querySelectorAll('.logo-card-partner');
            if (currentIndexPartner < cards.length - visibleCardsPartner) currentIndexPartner++;
            else currentIndexPartner = 0;
            updateCarouselPartner();
        });
    }
    if(prevBtnPartner) {
        prevBtnPartner.addEventListener('click', function() {
            if (currentIndexPartner > 0) currentIndexPartner--;
            updateCarouselPartner();
        });
    }
});
</script>

<script>
    function initChocolat() {
        if (typeof $ !== 'undefined' && $.fn.Chocolat) {
            if ($('.chocolat-image').data('chocolat')) {
                $('.chocolat-image').data('chocolat').destroy();
            }
            $('.chocolat-image').Chocolat({
                imageSize: 'contain', loop: true, overlayOpacity: 0.9, overlayClose: true, closeImg: '',
            });
        }
    }

    document.addEventListener("DOMContentLoaded", function () {
        initChocolat();
        $('body').off('click.chocoOverlay').on('click.chocoOverlay', '#Choco_overlay, .chocolat-overlay', function (e) {
            e.preventDefault(); e.stopPropagation();
            if ($('#Choco_close').length) $('#Choco_close').trigger('click');
            else if ($('.chocolat-close').length) $('.chocolat-close').trigger('click');
        });
    });

    let searchTimeout;
    function goToPage(pageNum) {
        const keyword = document.getElementById('searchInput').value;
        performLiveSearch(keyword, pageNum);
        
        // Scroll halus ke fasilitas
        const section = document.getElementById("facility-home");
        if(section) {
            window.scrollTo({ top: section.offsetTop - 100, behavior: 'smooth' });
        }
    }

    function performLiveSearch(keyword, pageNum = 1) {
        const spinner = document.getElementById('searchSpinner');
        const staticIcon = document.getElementById('staticSearchIcon');
        const container = document.getElementById('facility-results-container');

        if(spinner) spinner.style.display = 'block'; 
        if(staticIcon) staticIcon.style.opacity = '0'; 
        if(container) container.style.opacity = '0.5';
        
        clearTimeout(searchTimeout);

        // Jangan ubah URL di Home agar tidak merusak navigasi, cukup AJAX saja
        // if (window.history.pushState) { ... } // (Opsional: Matikan di Home jika mau)

        searchTimeout = setTimeout(() => {
            const timestamp = new Date().getTime();
            // PENTING: AJAX TETAP KE FACILITY.PHP karena disana ada fungsi render yang konsisten
            fetch(`index.php?page=facility&ajax=1&search=${encodeURIComponent(keyword)}&halaman=${pageNum}&_t=${timestamp}`)
                .then(res => res.text())
                .then(html => {
                    if(container) {
                        container.innerHTML = html;
                        initChocolat();
                        container.style.opacity = '1';
                    }
                    if(spinner) spinner.style.display = 'none';
                    if(staticIcon) staticIcon.style.opacity = '1';
                })
                .catch(err => {
                    console.error(err);
                    if(spinner) spinner.style.display = 'none';
                    if(staticIcon) staticIcon.style.opacity = '1';
                    if(container) container.style.opacity = '1';
                });
        }, 300);
    }
</script>