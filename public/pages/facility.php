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
$limit = 5; 
$page = isset($_GET['halaman']) ? (int)$_GET['halaman'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;
$searchKeyword = isset($_GET['search']) ? trim($_GET['search']) : '';

// --- PATH DEFINITIONS ---
$webThumbPath = 'uploads/thumb/fasilitas-thumb/';
$webImgPath   = 'uploads/fasilitas/'; // Path gambar asli (HD)
$serverBase   = $rootPath . '/public'; 
$serverThumbPath = $serverBase . '/uploads/thumb/fasilitas-thumb/';
$serverImgPath   = $serverBase . '/uploads/fasilitas/';

// ==========================================
// 2. FUNGSI GENERATOR HTML
// ==========================================

function renderFacilityGrid($dataList, $serverThumbPath, $webThumbPath, $serverImgPath, $webImgPath) {
    if (empty($dataList)) {
        return '<div class="text-center py-5">
                    <i class="fas fa-search fa-3x text-muted mb-3"></i>
                    <h4 class="text-muted">Fasilitas tidak ditemukan.</h4>
                </div>';
    }

    $html = '<div class="facility-list-container">';
    
    foreach ($dataList as $row) {
        $gambar = $row['gambar'];
        $hasImage = false; 
        $displayImage = ''; // Variabel untuk menampung gambar yang akan ditampilkan

        if (!empty($gambar)) {
            $ext = pathinfo($gambar, PATHINFO_EXTENSION);
            $filename = pathinfo($gambar, PATHINFO_FILENAME);
            $thumbName = $filename . '-thumb.' . $ext;
            
            // LOGIKA PERBAIKAN HD:
            // Cek gambar asli (Full Size) dulu agar tajam/HD
            if (file_exists($serverImgPath . $gambar)) {
                $displayImage = $webImgPath . $gambar;
                $hasImage = true;
            } 
            // Jika gambar asli rusak/hilang, baru pakai thumbnail sebagai cadangan
            elseif (file_exists($serverThumbPath . $thumbName)) {
                $displayImage = $webThumbPath . $thumbName;
                $hasImage = true;
            }
        }

        // ROW WRAPPER
        $html .= '<div class="facility-row">';
        
        // BAGIAN GAMBAR (KIRI)
        $html .= '<div class="facility-img-col">';
        if ($hasImage) {
            // Tampilkan Gambar HD, Tanpa Link, Tanpa Zoom
            $html .= '<img src="'.$displayImage.'" alt="'.htmlspecialchars($row['judul']).'" />';
        } else {
            $html .= '<div class="no-image-box">
                        <i class="fa fa-image"></i>
                        <span>No Image</span>
                      </div>';
        }
        $html .= '</div>'; 

        // BAGIAN TEXT (KANAN)
        $html .= '<div class="facility-text-col">
                    <h3 class="facility-list-title">'.htmlspecialchars($row['judul']).'</h3>
                    <div class="title-line"></div>
                    <div class="facility-list-desc">'.nl2br(htmlspecialchars($row['deskripsi'])).'</div>
                  </div>';
        
        $html .= '</div>'; 
    }
    
    $html .= '</div>'; 
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

    /* ======================================================== */
    /* 3. SEARCH BAR (STYLE ASLI - UKURAN DIPERKECIL)           */
    /* ======================================================== */
    .search-wrapper-center { 
        display: flex; justify-content: center; width: 100%; 
        margin-top: 20px; margin-bottom: 50px; 
        padding: 0 15px; 
    }
    .search-facility-box { 
        width: 100%; 
        max-width: 450px; /* Ukuran search diperkecil */ 
        position: relative; 
    }
    .search-facility-box input {
        width: 100%; 
        border: 1px solid #ccc; 
        border-radius: 50px; 
        padding: 10px 45px 10px 20px; 
        background-color: #fff; 
        color: #333; 
        font-size: 15px;
        outline: none; 
        transition: all 0.3s ease;
        box-shadow: 0 3px 6px rgba(0,0,0,0.05); 
        height: 45px;
    }
    .search-facility-box input:focus { 
        border-color: var(--primary-color, #007bff); 
        box-shadow: 0 4px 10px rgba(0,0,0,0.1); 
    }
    .search-icon-static { 
        position: absolute; right: 18px; top: 50%; transform: translateY(-50%); 
        color: #aaa; font-size: 16px; pointer-events: none; 
    }
    .search-loading { 
        position: absolute; right: 18px; top: 50%; transform: translateY(-50%); 
        color: #aaa; font-size: 16px; display: none; 
    }

    /* ======================================================== */
    /* 4. MODERN LIST LAYOUT (STATIC IMAGE - NO ZOOM)           */
    /* ======================================================== */
    .facility-section-bg { background-color: #fff; } 
    
    .facility-list-container {
        display: flex;
        flex-direction: column;
        gap: 50px;
        max-width: 960px;
        margin: 0 auto;
    }

    .facility-row {
        display: flex;
        align-items: flex-start;
        gap: 40px;
        padding-bottom: 40px;
        border-bottom: 1px solid #eee;
    }
    
    .facility-row:last-child {
        border-bottom: none;
    }

    /* BAGIAN GAMBAR (KIRI) */
    .facility-img-col {
        width: 380px; /* Lebar fix */
        height: 250px; /* Tinggi fix */
        flex-shrink: 0;
        border-radius: 8px;
        overflow: hidden;
        position: relative;
        background-color: #f4f4f4;
        box-shadow: 0 4px 10px rgba(0,0,0,0.08);
        cursor: default; /* Cursor biasa, bukan pointer link */
    }

    .facility-img-col img {
        width: 100%; height: 100%;
        object-fit: cover;
        /* TRANSISI & TRANSFORM DIHAPUS agar tidak ada efek zoom */
        display: block;
    }

    /* No Image Placeholder */
    .no-image-box {
        width: 100%; height: 100%;
        display: flex; flex-direction: column; justify-content: center; align-items: center;
        background-color: #f8f9fa; color: #ced4da;
    }
    .no-image-box i { font-size: 40px; margin-bottom: 5px; }

    /* BAGIAN TEKS (KANAN) */
    .facility-text-col {
        flex-grow: 1;
        padding-top: 5px;
        text-align: left;
    }

    .facility-list-title {
        font-size: 26px;
        font-weight: 800;
        text-transform: uppercase;
        color: #222;
        margin-bottom: 12px;
        line-height: 1.3;
        letter-spacing: -0.5px;
    }

    .title-line {
        width: 60px;
        height: 4px;
        background-color: #ff9800;
        margin-bottom: 20px;
        border-radius: 2px;
    }

    .facility-list-desc {
        font-size: 16px;
        line-height: 1.8;
        color: #666;
        font-family: 'Source Sans Pro', sans-serif;
    }

    /* ======================================================== */
    /* 5. PAGINATION (STYLE ASLI)                               */
    /* ======================================================== */
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
        background-color: #ff9800; 
        color: #ffffff; border-color: #ff9800; cursor: default; 
    }
    
    .page-btn.disabled { 
        background-color: #fafafa; color: #e0e0e0; border-color: #f0f0f0; cursor: not-allowed; 
    }
    
    .page-btn i { font-size: 12px; }

    /* RESPONSIVE */
    @media (max-width: 992px) {
        .facility-img-col { width: 320px; height: 220px; }
        .facility-list-title { font-size: 22px; }
    }

    @media (max-width: 768px) {
        .facility-row { flex-direction: column; gap: 20px; text-align: left; }
        .facility-img-col { width: 100%; height: 240px; }
        .facility-text-col { width: 100%; padding-top: 0; }
        .title-line { margin-bottom: 15px; }
        
        .search-facility-box input { padding: 10px 40px 10px 20px; font-size: 14px; height: 40px; }
        .page-btn { min-width: 35px; height: 35px; font-size: 14px; border-radius: 6px; }
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
    // FUNGSI CHOCOLAT (POPUP) HAPUS SAJA KARENA TIDAK DIPAKAI
    // TAPI TETAP SAYA BIARKAN KOSONG AGAR TIDAK ERROR JIKA ADA SISA JS LAIN
    function initChocolat() { }

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