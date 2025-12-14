<?php
// ==========================================
// 1. KONFIGURASI & FUNGSI BANTUAN
// ==========================================
$rootPath = dirname(dirname(__DIR__)); 
$koneksiPath = $rootPath . '/config/koneksi.php';

if (file_exists($koneksiPath)) {
    require_once $koneksiPath;
}

// --- CONFIG ---
$limit = 6; 
$page = isset($_GET['halaman']) ? (int)$_GET['halaman'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;
$searchKeyword = isset($_GET['search']) ? trim($_GET['search']) : '';

// --- PATH DEFINITIONS ---
$webThumbPath = 'uploads/thumb/fasilitas-thumb/';
$webImgPath   = 'uploads/fasilitas/';
$serverBase   = $rootPath . '/public'; 
$serverThumbPath = $serverBase . '/uploads/thumb/fasilitas-thumb/';
$serverImgPath   = $serverBase . '/uploads/fasilitas/';

// ==========================================
// 2. FUNGSI GENERATOR HTML (AGAR KONSISTEN)
// ==========================================

function renderFacilityGrid($dataList, $serverThumbPath, $webThumbPath, $serverImgPath, $webImgPath) {
    if (empty($dataList)) {
        return '<div class="text-center py-5">
                    <i class="fas fa-search fa-3x text-muted mb-3"></i>
                    <h4 class="text-muted">Fasilitas tidak ditemukan.</h4>
                </div>';
    }

    $html = '<ul class="gallery_agile">';
    
    foreach ($dataList as $row) {
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
            
            $srcFull = (file_exists($serverImgPath . $gambar)) ? $webImgPath . $gambar : $srcThumb;
        }

        $html .= '<li class="facility-card">';
        
        // WRAPPER GAMBAR (Height 250px Fixed)
        $html .= '<div class="facility-img-wrap">';
        
        if ($hasImage) {
            // JIKA ADA GAMBAR
            $html .= '<a href="'.$srcFull.'" class="chocolat-image" title="'.htmlspecialchars($row['judul']).'">
                        <img src="'.$srcThumb.'" alt="'.htmlspecialchars($row['judul']).'" />
                      </a>';
        } else {
            // JIKA TIDAK ADA GAMBAR (PLACEHOLDER)
            $html .= '<div class="no-image-box">
                        <i class="fa fa-image"></i>
                        <span>Tidak ada gambar</span>
                      </div>';
        }
        $html .= '</div>'; 

        // TEXT (JUDUL & DESKRIPSI)
        $html .= '<div class="facility-text">
                    <div class="facility-title">'.htmlspecialchars($row['judul']).'</div>
                    <div class="facility-desc">'.nl2br(htmlspecialchars($row['deskripsi'])).'</div>
                  </div>';
        
        $html .= '</li>';
    }
    
    $html .= '</ul>';
    return $html;
}

function renderPagination($currentPage, $totalPages) {
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

// --- QUERY DB ---
function getFasilitasData($pdo, $keyword, $limit, $offset) {
    try {
        $sql = "SELECT * FROM fasilitas";
        if (!empty($keyword)) {
            $sql .= " WHERE judul LIKE :k1 OR deskripsi LIKE :k2";
        }
        $sql .= " ORDER BY id_fasilitas DESC LIMIT :limit OFFSET :offset";
        $stmt = $pdo->prepare($sql);
        if (!empty($keyword)) {
            $stmt->bindValue(':k1', "%$keyword%", PDO::PARAM_STR);
            $stmt->bindValue(':k2', "%$keyword%", PDO::PARAM_STR);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) { return []; }
}

function countTotalFasilitas($pdo, $keyword) {
    try {
        $sql = "SELECT COUNT(*) FROM fasilitas";
        if (!empty($keyword)) {
            $sql .= " WHERE judul LIKE :k1 OR deskripsi LIKE :k2";
        }
        $stmt = $pdo->prepare($sql);
        if (!empty($keyword)) {
            $stmt->bindValue(':k1', "%$keyword%", PDO::PARAM_STR);
            $stmt->bindValue(':k2', "%$keyword%", PDO::PARAM_STR);
        }
        $stmt->execute();
        return $stmt->fetchColumn();
    } catch (Exception $e) { return 0; }
}

// ==========================================
// 3. HANDLER AJAX
// ==========================================
if (isset($_GET['ajax']) && $_GET['ajax'] == '1') {
    while (ob_get_level()) { ob_end_clean(); } 
    
    $fasilitasList = getFasilitasData($pdo, $searchKeyword, $limit, $offset);
    $totalData = countTotalFasilitas($pdo, $searchKeyword);
    $totalPages = ceil($totalData / $limit);
    
    echo renderFacilityGrid($fasilitasList, $serverThumbPath, $webThumbPath, $serverImgPath, $webImgPath);
    echo renderPagination($page, $totalPages);
    exit;
}

// --- INITIAL DATA ---
$fasilitasList = getFasilitasData($pdo, $searchKeyword, $limit, $offset);
$totalData = countTotalFasilitas($pdo, $searchKeyword);
$totalPages = ceil($totalData / $limit);
?>

<style>
    /* 1. LAYOUT & Z-INDEX */
    header, .w3l-header, .w3l-header-fixed, .main-header, .navbar {
        z-index: 9999 !important; position: relative;
    }
    .inner-banner, .w3l-gallery, .search-wrapper-center {
        z-index: 1; position: relative;
    }

    /* 2. HEADER BANNER */
    .inner-banner.facility-banner {
        background: url('assets/images/header-facility.jpeg') no-repeat center;
        background-size: cover; position: relative;
        min-height: 350px; display: grid; align-items: center;
    }
    .inner-banner.facility-banner:before {
        content: ""; background: rgba(0, 0, 0, 0.6); position: absolute; top: 0; bottom: 0; left: 0; right: 0; z-index: -1;
    }

    /* 3. SEARCH BAR */
    .search-wrapper-center { display: flex; justify-content: center; width: 100%; margin-top: 15px; margin-bottom: 40px; padding: 0 15px; }
    .search-facility-box { width: 100%; max-width: 600px; position: relative; }
    .search-facility-box input {
        width: 100%; border: 1px solid #ccc; border-radius: 50px; padding: 15px 60px 15px 30px;
        background-color: #fff; color: #333; font-size: 18px; outline: none; transition: all 0.3s ease;
        box-shadow: 0 4px 8px rgba(0,0,0,0.05); 
    }
    .search-facility-box input:focus { border-color: var(--primary-color, #007bff); box-shadow: 0 6px 15px rgba(0,0,0,0.1); }
    .search-icon-static { position: absolute; right: 25px; top: 50%; transform: translateY(-50%); color: #aaa; font-size: 22px; pointer-events: none; }
    .search-loading { position: absolute; right: 25px; top: 50%; transform: translateY(-50%); color: #aaa; font-size: 22px; display: none; }

    /* 4. GRID SYSTEM (STRICT ANTI-GESER) */
    .facility-section-bg { background-color: var(--bg-color); }
    
    ul.gallery_agile {
        display: grid; 
        grid-template-columns: repeat(3, minmax(0, 1fr)); 
        gap: 30px; 
        padding: 0 !important; margin: 0 !important;
        list-style: none !important; width: 100%; 
        align-items: stretch; /* MEMAKSA TINGGI KARTU SAMA */
    }
    
    .facility-card {
        width: 100%; margin-bottom: 30px; box-sizing: border-box; overflow: hidden;
        display: flex; flex-direction: column; height: 100%;
        text-align: left !important; align-items: flex-start !important; justify-content: flex-start !important;
    }

    /* WRAPPER GAMBAR (TINGGI DIKUNCI) */
    .facility-img-wrap {
        width: 100%; 
        height: 250px !important; /* TINGGI FIXED */
        border-radius: var(--border-radius); 
        overflow: hidden; 
        margin-bottom: 20px; 
        position: relative;
        background-color: var(--bg-grey); 
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        flex-shrink: 0;
    }
    
    .facility-img-wrap img {
        width: 100% !important; height: 100% !important; 
        object-fit: cover; object-position: center; display: block;
        transition: transform 0.5s ease;
    }
    .facility-img-wrap:hover img { transform: scale(1.05); }

    /* PLACEHOLDER NO IMAGE */
    .no-image-box {
        width: 100%; height: 100%; 
        background-color: var(--bg-lightgrey);
        display: flex; flex-direction: column; justify-content: center; align-items: center;
        color: var(--font-color); text-align: center !important;
        border: 1px solid #dee2e6; /* Border tipis biar tegas */
    }
    .no-image-box i { font-size: 42px; margin-bottom: 8px; color: #ced4da; }
    .no-image-box span { font-size: 16px; font-weight: 600; font-family: sans-serif; color: #adb5bd; }
    
    /* WRAPPER TEKS */
    .facility-text { padding: 0 5px; width: 100%; text-align: left !important; flex-grow: 1; }
    
   /* JUDUL (Maksimal 2 Baris) */
   /* JUDUL (Maksimal 2 Baris) */
   .facility-title {
        font-size: 22px; 
        font-weight: 800; 
        text-transform: uppercase; 
        color: var(--heading-color); 
        margin-bottom: 10px; 
        line-height: 1.4; 
        
        /* CSS Line Clamp (Untuk Ellipsis) */
        display: -webkit-box; 
        -webkit-line-clamp: 2; /* Batas 2 baris */
        -webkit-box-orient: vertical; 
        overflow: hidden; 
        text-overflow: ellipsis; /* Tambahkan titik-titik */
        
        min-height: 62px; /* Menjaga tinggi agar sejajar */
        text-align: left !important;
    }
    
    /* DESKRIPSI (Maksimal 2 Baris) */
    .facility-desc {
        font-size: 16px; 
        color: var(--font-color); 
        line-height: 1.6; 
        
        /* CSS Line Clamp (Untuk Ellipsis) */
        display: -webkit-box; 
        -webkit-line-clamp: 2; /* Batas 2 baris */
        -webkit-box-orient: vertical; 
        overflow: hidden; 
        text-overflow: ellipsis; /* Tambahkan titik-titik */
        
        min-height: 52px; /* Menjaga tinggi agar sejajar (sekitar 16px * 1.6 * 2) */
        text-align: left !important;
    }

    /* 5. PAGINASI STYLE (KOTAK ROUNDED) */
    .pagination-wrapper { margin-top: 50px; display: flex; justify-content: center; gap: 8px; flex-wrap: wrap; }
    
    .page-btn {
        min-width: 42px; height: 42px; padding: 0 12px; 
        display: flex; align-items: center; justify-content: center;
        background-color: #fff; border: 1px solid #e1e1e1; border-radius: 8px;
        color: #333; font-weight: 600; font-size: 15px; font-family: sans-serif;
        cursor: pointer; transition: all 0.2s ease-in-out;
    }
    
    .page-btn:hover:not(.disabled):not(.active) { background-color: #f1f1f1; border-color: #ccc; }
    
    .page-btn.active { 
        background-color: #ff9800; /* WARNA BIRU TUA */
        color: #ffffff; border-color: #ff9800; cursor: default; 
    }
    
    .page-btn.disabled { 
        background-color: #fafafa; color: #e0e0e0; border-color: #f0f0f0; cursor: not-allowed; 
    }
    
    .page-btn i { font-size: 12px; }

    /* RESPONSIVE */
    @media (max-width: 992px) { ul.gallery_agile { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 768px) { 
        ul.gallery_agile { grid-template-columns: 1fr; } 
        .search-facility-box input { padding: 12px 50px 12px 20px; font-size: 16px; }
        .page-btn { min-width: 35px; height: 35px; font-size: 14px; border-radius: 6px; }
    }
    
    /* CLOSE BUTTON POPUP */
    #Choco_close, .chocolat-close {
        position: fixed !important; top: 25px !important; right: 25px !important;
        z-index: 2147483647 !important; width: 44px !important; height: 44px !important;
        background: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%23fff'%3e%3cpath d='M.293.293a1 1 0 011.414 0L8 6.586 14.293.293a1 1 0 111.414 1.414L9.414 8l6.293 6.293a1 1 0 01-1.414 1.414L8 9.414l-6.293 6.293a1 1 0 01-1.414-1.414L6.586 8 .293 1.707a1 1 0 010-1.414z'/%3e%3c/svg%3e") no-repeat center center !important;
        background-color: rgba(0, 0, 0, 0.6) !important; background-size: 24px 24px !important;
        border: 2px solid #fff !important; border-radius: 50% !important;
        cursor: pointer !important; opacity: 1 !important; transition: transform 0.2s ease;
    }
    #Choco_close:hover, .chocolat-close:hover { background-color: #dc3545 !important; transform: scale(1.1); }
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
        <div class="title-content text-center mb-2">
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
            <?= renderFacilityGrid($fasilitasList, $serverThumbPath, $webThumbPath, $serverImgPath, $webImgPath) ?>
            <?= renderPagination($page, $totalPages) ?>
        </div>
    </div>
</section>

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
        
        if (typeof $ !== 'undefined' && $(".facility-section-bg").length) {
            $('html, body').animate({ scrollTop: $(".facility-section-bg").offset().top - 100 }, 500);
        } else {
            window.scrollTo({ top: document.querySelector(".facility-section-bg").offsetTop - 100, behavior: 'smooth' });
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

        if (window.history.pushState) {
            const newUrl = new URL(window.location.href);
            newUrl.searchParams.set('search', keyword);
            newUrl.searchParams.set('halaman', pageNum);
            window.history.pushState({}, '', newUrl);
        }

        searchTimeout = setTimeout(() => {
            const timestamp = new Date().getTime();
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