<?php
// ==========================================
// 1. KONEKSI & PERSIAPAN DATA
// ==========================================
$rootPath = dirname(dirname(__DIR__)); 
$koneksiPath = $rootPath . '/config/koneksi.php';

if (file_exists($koneksiPath)) {
    require_once $koneksiPath;
}

// --- KONFIGURASI PAGINASI ---
$limit = 9; // Batas item per halaman (9 fasilitas)
$page = isset($_GET['halaman']) ? (int)$_GET['halaman'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

// --- LOGIKA PENCARIAN (PHP) ---
$searchKeyword = '';
if (isset($_GET['search'])) {
    $searchKeyword = trim($_GET['search']);
}

// --- FUNGSI QUERY DATA (DENGAN LIMIT & OFFSET) ---
function getFasilitasData($pdo, $keyword, $limit, $offset) {
    try {
        $sql = "SELECT * FROM fasilitas";
        $params = [];

        if (!empty($keyword)) {
            $sql .= " WHERE judul LIKE :keyword OR deskripsi LIKE :keyword";
            $params['keyword'] = "%$keyword%";
        }

        $sql .= " ORDER BY id_fasilitas DESC LIMIT :limit OFFSET :offset";
        
        $stmt = $pdo->prepare($sql);
        
        if (!empty($keyword)) {
            $stmt->bindValue(':keyword', "%$keyword%", PDO::PARAM_STR);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) { return []; }
}

// --- FUNGSI HITUNG TOTAL DATA (UNTUK PAGINASI) ---
function countTotalFasilitas($pdo, $keyword) {
    try {
        $sql = "SELECT COUNT(*) FROM fasilitas";
        $params = [];
        if (!empty($keyword)) {
            $sql .= " WHERE judul LIKE :keyword OR deskripsi LIKE :keyword";
            $params['keyword'] = "%$keyword%";
        }
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn();
    } catch (Exception $e) { return 0; }
}

// Definisi Path
$webThumbPath = 'uploads/thumb/fasilitas-thumb/';
$webImgPath   = 'uploads/fasilitas/';
$serverBase = $rootPath . '/public'; 
$serverThumbPath = $serverBase . '/uploads/thumb/fasilitas-thumb/';
$serverImgPath   = $serverBase . '/uploads/fasilitas/';

// ==========================================
// 2. HANDLER AJAX (LIVE SEARCH + PAGINASI)
// ==========================================
if (isset($_GET['ajax']) && $_GET['ajax'] == '1') {
    while (ob_get_level()) { ob_end_clean(); }
    
    // Ambil data terbaru sesuai halaman & search
    $fasilitasList = getFasilitasData($pdo, $searchKeyword, $limit, $offset);
    $totalData = countTotalFasilitas($pdo, $searchKeyword);
    $totalPages = ceil($totalData / $limit);
    
    // --- OUTPUT GRID GAMBAR ---
    if (!empty($fasilitasList)) {
        echo '<ul class="gallery_agile">';
        foreach ($fasilitasList as $row) {
            $gambar = $row['gambar'];
            $hasImage = false;
            $srcThumb = ''; $srcFull  = '';

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
                if (file_exists($serverImgPath . $gambar)) $srcFull = $webImgPath . $gambar;
                else $srcFull = $srcThumb;
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
                    <div class="facility-title"><?= htmlspecialchars($row['judul']) ?></div>
                    <div class="facility-desc"><?= nl2br(htmlspecialchars($row['deskripsi'])) ?></div>
                </div>
            </li>
            <?php
        }
        echo '</ul>';
    } else {
        echo '<div class="text-center py-5">
                <i class="fas fa-search fa-3x text-muted mb-3"></i>
                <h4 class="text-muted">Fasilitas tidak ditemukan.</h4>
              </div>';
    }

    // --- OUTPUT TOMBOL PAGINASI (AJAX) ---
    if ($totalPages > 1) {
        echo '<div class="pagination-wrapper">';
        
        // Prev
        if ($page > 1) {
            echo '<button class="page-btn" onclick="goToPage('.($page - 1).')">&laquo; Prev</button>';
        } else {
            echo '<button class="page-btn disabled" disabled>&laquo; Prev</button>';
        }

        // Numbers
        for ($i = 1; $i <= $totalPages; $i++) {
            $activeClass = ($i == $page) ? 'active' : '';
            echo '<button class="page-btn '.$activeClass.'" onclick="goToPage('.$i.')">'.$i.'</button>';
        }

        // Next
        if ($page < $totalPages) {
            echo '<button class="page-btn" onclick="goToPage('.($page + 1).')">Next &raquo;</button>';
        } else {
            echo '<button class="page-btn disabled" disabled>Next &raquo;</button>';
        }
        echo '</div>';
    }

    exit;
}

// --- LOAD DATA AWAL ---
$fasilitasList = getFasilitasData($pdo, $searchKeyword, $limit, $offset);
$totalData = countTotalFasilitas($pdo, $searchKeyword);
$totalPages = ceil($totalData / $limit);
?>

<style>
    /* --- [FIX NAVBAR KETIMPA] --- */
    /* Memaksa Header/Navbar memiliki Z-Index paling tinggi */
    header, .w3l-header, .w3l-header-fixed, .main-header, .navbar {
        z-index: 9999 !important;
        position: relative; /* Pastikan position diset agar z-index jalan */
    }

    /* Memastikan konten fasilitas ada di bawah header */
    .inner-banner, .w3l-gallery, .search-wrapper-center {
        z-index: 1;
        position: relative;
    }

    /* --- CSS HEADER BANNER --- */
    .inner-banner.facility-banner {
        background: url('assets/images/header-facility.jpeg') no-repeat center;
        background-size: cover;
        position: relative;
        /* z-index: 0; Hapus atau biarkan default agar tidak konflik */
        min-height: 350px; 
        display: grid;
        align-items: center;
    }
    .inner-banner.facility-banner:before {
        content: "";
        background: rgba(0, 0, 0, 0.6); 
        position: absolute;
        top: 0; bottom: 0; left: 0; right: 0;
        z-index: -1;
    }

    /* --- LAYOUT POSISI SEARCH BAR (TENGAH & BESAR) --- */
    .search-wrapper-center {
        display: flex;
        justify-content: center;
        width: 100%;
        margin-top: 15px;
        margin-bottom: 40px;
        padding: 0 15px;
    }

    /* CSS KOTAK PENCARIAN */
    .search-facility-box {
        width: 100%;
        max-width: 600px; 
        position: relative;
    }

    /* Input Style Pill - Versi Besar */
    .search-facility-box input {
        width: 100%;
        border: 1px solid #ccc;
        border-radius: 50px;
        padding: 15px 60px 15px 30px; 
        background-color: #fff;
        color: #333;
        font-family: sans-serif;
        font-weight: 400;
        font-size: 18px; 
        outline: none;
        transition: all 0.3s ease;
        box-shadow: 0 4px 8px rgba(0,0,0,0.05); 
    }
    .search-facility-box input:focus {
        border-color: var(--primary-color, #007bff);
        box-shadow: 0 6px 15px rgba(0,0,0,0.1);
    }
    .search-facility-box input::placeholder { color: #aaa; }

    /* Ikon Search & Loading */
    .search-icon-static {
        position: absolute; right: 25px; top: 50%;
        transform: translateY(-50%); color: #aaa;
        font-size: 22px; pointer-events: none; transition: opacity 0.2s;
    }
    .search-loading {
        position: absolute; right: 25px; top: 50%;
        transform: translateY(-50%); color: #aaa;
        font-size: 22px; display: none;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .search-facility-box input { padding: 12px 50px 12px 20px; font-size: 16px; }
    }

    /* --- CSS GRID & CARD --- */
    .facility-section-bg {
        background-color: var(--bg-color); 
        transition: background-color 0.3s ease;
    }
    ul.gallery_agile {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr)); 
        gap: 30px; 
        padding: 0 !important; margin: 0 !important;
        list-style: none !important; width: 100%; align-items: start;
    }
    .facility-card {
        width: 100%; margin-bottom: 30px; max-width: 100%; 
        box-sizing: border-box; overflow: hidden;
        animation: fadeIn 0.5s ease-in-out;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .facility-img-wrap {
        width: 100%; height: 250px; 
        border-radius: var(--border-radius); overflow: hidden;
        margin-bottom: 20px; position: relative;
        background-color: var(--bg-grey); 
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    }
    .facility-img-wrap img {
        width: 100% !important; height: 100% !important; 
        object-fit: cover; object-position: center; 
        transition: transform 0.5s ease; display: block;
    }
    .facility-img-wrap:hover img { transform: scale(1.05); }

    .no-image-box {
        width: 100%; height: 100%;
        background-color: var(--bg-lightgrey);
        display: flex; flex-direction: column; justify-content: center; align-items: center;
        color: var(--font-color); text-align: center; user-select: none;
    }
    .no-image-box i { font-size: 48px; margin-bottom: 10px; }
    .no-image-box span { font-size: 18px; font-weight: 600; font-family: 'Source Sans Pro', sans-serif; }

    .facility-text { padding: 0 5px; }
    .facility-title {
        font-size: 22px; font-weight: 800; text-transform: uppercase;
        color: var(--heading-color); margin-bottom: 10px; line-height: 1.4;
        cursor: text; display: -webkit-box; -webkit-line-clamp: 2; 
        -webkit-box-orient: vertical; overflow: hidden; word-break: break-word; 
    }
    .facility-desc {
        font-size: 16px; color: var(--font-color); line-height: 1.6;
        margin: 0; text-align: left; display: -webkit-box;
        -webkit-line-clamp: 3; -webkit-box-orient: vertical;
        overflow: hidden; word-break: break-word; 
    }

    /* --- CSS TOMBOL CLOSE (Agar pasti muncul di atas segalanya) --- */
    #Choco_close, .chocolat-close {
        position: fixed !important; top: 25px !important; right: 25px !important;
        z-index: 2147483647 !important; width: 44px !important; height: 44px !important;
        background: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%23fff'%3e%3cpath d='M.293.293a1 1 0 011.414 0L8 6.586 14.293.293a1 1 0 111.414 1.414L9.414 8l6.293 6.293a1 1 0 01-1.414 1.414L8 9.414l-6.293 6.293a1 1 0 01-1.414-1.414L6.586 8 .293 1.707a1 1 0 010-1.414z'/%3e%3c/svg%3e") no-repeat center center !important;
        background-color: rgba(0, 0, 0, 0.6) !important; background-size: 24px 24px !important;
        border: 2px solid rgba(255,255,255,0.8) !important; border-radius: 50% !important;
        cursor: pointer !important; display: block !important; opacity: 1 !important;     
        box-shadow: 0 4px 10px rgba(0,0,0,0.5) !important; transition: transform 0.2s ease !important;
    }
    #Choco_close:hover, .chocolat-close:hover {
        background-color: #dc3545 !important; transform: scale(1.1) !important; border-color: #fff !important;
    }

    /* --- CSS PAGINASI BARU --- */
    .pagination-wrapper {
        margin-top: 40px;
        display: flex;
        justify-content: center;
        gap: 5px;
        flex-wrap: wrap;
    }
    .page-btn {
        padding: 8px 16px;
        border: 1px solid #ddd;
        background-color: #fff;
        color: #333;
        cursor: pointer;
        border-radius: 5px;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    .page-btn:hover:not(.disabled) {
        background-color: var(--primary-color, #007bff);
        color: #fff;
        border-color: var(--primary-color, #007bff);
    }
    .page-btn.active {
        background-color: var(--primary-color, #007bff);
        color: #fff;
        border-color: var(--primary-color, #007bff);
        cursor: default;
    }
    .page-btn.disabled {
        color: #ccc;
        cursor: not-allowed;
        background-color: #f9f9f9;
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
                    <input type="text" id="searchInput" 
                           placeholder="Search..." 
                           value="<?= htmlspecialchars($searchKeyword) ?>"
                           oninput="performLiveSearch(this.value, 1)"> <i id="staticSearchIcon" class="fas fa-search search-icon-static"></i>
                    <div id="searchSpinner" class="search-loading">
                        <i class="fas fa-spinner fa-spin"></i>
                    </div>
                </form>
            </div>
        </div>

        <div id="facility-results-container">
            <?php if (!empty($fasilitasList)): ?>
                <ul class="gallery_agile">
                    <?php foreach ($fasilitasList as $row): ?>
                        <?php 
                            $gambar = $row['gambar'];
                            $hasImage = false;
                            $srcThumb = ''; $srcFull  = '';
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
                                if (file_exists($serverImgPath . $gambar)) $srcFull = $webImgPath . $gambar;
                                else $srcFull = $srcThumb;
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
                                <div class="facility-title"><?= htmlspecialchars($row['judul']) ?></div>
                                <div class="facility-desc"><?= nl2br(htmlspecialchars($row['deskripsi'])) ?></div>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <div class="text-center py-5">
                    <h4 class="text-muted">Belum ada data fasilitas.</h4>
                </div>
            <?php endif; ?>

            <?php if ($totalPages > 1): ?>
                <div class="pagination-wrapper">
                    <?php if ($page > 1): ?>
                        <button class="page-btn" onclick="goToPage(<?= $page - 1 ?>)">&laquo; Prev</button>
                    <?php else: ?>
                        <button class="page-btn disabled" disabled>&laquo; Prev</button>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <button class="page-btn <?= ($i == $page) ? 'active' : '' ?>" onclick="goToPage(<?= $i ?>)"><?= $i ?></button>
                    <?php endfor; ?>

                    <?php if ($page < $totalPages): ?>
                        <button class="page-btn" onclick="goToPage(<?= $page + 1 ?>)">Next &raquo;</button>
                    <?php else: ?>
                        <button class="page-btn disabled" disabled>Next &raquo;</button>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>

    </div>
</section>

<script>
    function initChocolat() {
        if (typeof $ !== 'undefined' && $.fn.Chocolat) {
            $('.chocolat-image').Chocolat({
                imageSize: 'contain',
                loop: true,
                overlayOpacity: 0.9,
                overlayClose: true, 
                closeImg: '',
            });
        }
    }

    document.addEventListener("DOMContentLoaded", function () {
        initChocolat();

        // LOGIKA KLIK OVERLAY UNTUK TUTUP POPUP
        $('body')
            .off('click.chocoOverlay')
            .on('click.chocoOverlay', '#Choco_overlay, .chocolat-overlay', function (e) {
                e.preventDefault();
                e.stopPropagation();
                if ($('#Choco_close').length) {
                    $('#Choco_close').trigger('click');
                } else if ($('.chocolat-close').length) {
                    $('.chocolat-close').trigger('click');
                }
            });
    });

    let searchTimeout;
    
    // Fungsi pindah halaman
    function goToPage(pageNum) {
        const keyword = document.getElementById('searchInput').value;
        performLiveSearch(keyword, pageNum);
        
        // Scroll halus ke atas grid
        $('html, body').animate({
            scrollTop: $(".facility-section-bg").offset().top
        }, 500);
    }

    // Fungsi Pencarian Utama + Paginasi AJAX
    function performLiveSearch(keyword, pageNum = 1) {
        const spinner = document.getElementById('searchSpinner');
        const staticIcon = document.getElementById('staticSearchIcon');
        const container = document.getElementById('facility-results-container');

        spinner.style.display = 'block';
        staticIcon.style.opacity = '0';
        container.style.opacity = '0.5';

        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            fetch(`index.php?page=facility&ajax=1&search=${encodeURIComponent(keyword)}&halaman=${pageNum}`)
                .then(res => res.text())
                .then(html => {
                    container.innerHTML = html;
                    initChocolat(); 

                    spinner.style.display = 'none';
                    staticIcon.style.opacity = '1';
                    container.style.opacity = '1';
                })
                .catch(() => {
                    spinner.style.display = 'none';
                    staticIcon.style.opacity = '1';
                    container.style.opacity = '1';
                });
        }, 300);
    }
</script>