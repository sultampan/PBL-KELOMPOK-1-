<?php
// ==========================================
// 1. CONNECTION & PREPARATION (POSTGRESQL)
// ==========================================
$rootPath = dirname(dirname(__DIR__)); 
$koneksiPath = $rootPath . '/config/koneksi.php';

if (file_exists($koneksiPath)) {
    require_once $koneksiPath;
}

// --- CONFIG PAGINATION ---
// Note: Menggunakan 'p' bukan 'page' untuk menghindari konflik dengan routing halaman utama
$limit = 6; // Jumlah item per halaman
$currPage = isset($_GET['p']) ? (int)$_GET['p'] : 1;
if ($currPage < 1) $currPage = 1;
$offset = ($currPage - 1) * $limit;

// --- SEARCH LOGIC ---
$searchKeyword = '';
if (isset($_GET['search'])) {
    $searchKeyword = trim($_GET['search']);
}

// --- CONFIG PATH ---
$webThumbPath = 'uploads/thumb/produk-thumb/';
$webImgPath   = 'uploads/produk/';
$serverBase = $rootPath . '/public'; 
$serverThumbPath = $serverBase . '/uploads/thumb/produk-thumb/';
$serverImgPath   = $serverBase . '/uploads/produk/';

// --- FUNCTION 1: HITUNG TOTAL DATA (Untuk Pagination) ---
function getProdukCount($pdo, $keyword) {
    try {
        $sql = "SELECT COUNT(DISTINCT p.id_produk) FROM produk p";
        if (!empty($keyword)) {
            $sql .= " WHERE p.nama ILIKE :keyword OR p.deskripsi ILIKE :keyword";
        }
        $stmt = $pdo->prepare($sql);
        if (!empty($keyword)) {
            $stmt->bindValue(':keyword', "%$keyword%", PDO::PARAM_STR);
        }
        $stmt->execute();
        return $stmt->fetchColumn();
    } catch (Exception $e) { return 0; }
}

// --- FUNCTION 2: AMBIL DATA DENGAN LIMIT/OFFSET ---
function getProdukData($pdo, $keyword, $limit, $offset) {
    try {
        $sql = "
          SELECT 
            p.id_produk,
            p.nama,
            p.deskripsi,
            p.gambar,
            p.link_produk
          FROM produk p
        ";

        if (!empty($keyword)) {
            $sql .= " WHERE p.nama ILIKE :keyword OR p.deskripsi ILIKE :keyword";
        }

        $sql .= " GROUP BY p.id_produk, p.nama, p.deskripsi, p.gambar, p.link_produk 
                  ORDER BY p.id_produk DESC 
                  LIMIT :limit OFFSET :offset";
        
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

// --- GET DATA UTAMA ---
$totalData = getProdukCount($pdo, $searchKeyword);
$totalPages = ceil($totalData / $limit);
$produkList = getProdukData($pdo, $searchKeyword, $limit, $offset);

// ==========================================
// 2. AJAX HANDLER (LIVE SEARCH & PAGINATION)
// ==========================================
if (isset($_GET['ajax']) && $_GET['ajax'] == '1') {
    while (ob_get_level()) { ob_end_clean(); }
    
    echo '<div id="ajax-content-wrapper">';

    // --- A. OUTPUT GRID ---
    if (!empty($produkList)) {
        echo '<div class="activity-grid">';
        foreach ($produkList as $row) {
            $gambar = $row['gambar'];
            $srcDisplay = ''; 
            $hasImage = false; 
            
            if (!empty($gambar)) {
                $ext    = pathinfo($gambar, PATHINFO_EXTENSION);
                $filename = pathinfo($gambar, PATHINFO_FILENAME);
                $thumbName = $filename . '-thumb.' . $ext;
                
                if (file_exists($serverThumbPath . $thumbName)) {
                    $srcDisplay = $webThumbPath . $thumbName;
                    $hasImage = true;
                } elseif (file_exists($serverImgPath . $gambar)) {
                    $srcDisplay = $webImgPath . $gambar;
                    $hasImage = true;
                }
            }
            ?>
            <div class="card-link-wrapper">
              <div class="activity-card">
                <div class="activity-image-wrapper">
                  <?php if ($hasImage): ?>
                    <img src="<?= htmlspecialchars($srcDisplay); ?>" alt="<?= htmlspecialchars($row['nama']); ?>" class="activity-image">
                  <?php else: ?>
                    <div style="display: flex; flex-direction: column; justify-content: center; align-items: center; height: 100%; color: #ccc;">
                          <i class="fas fa-box-open" style="font-size: 48px; margin-bottom: 10px;"></i>
                          <span style="font-size: 14px;">No Image</span>
                    </div>
                  <?php endif; ?>
                </div>
                <div class="activity-content">
                  <span class="card-badge">PRODUCT</span>
                  <h4 class="activity-title"><?= htmlspecialchars($row['nama']); ?></h4>
                  <div class="activity-description"><?= nl2br(htmlspecialchars($row['deskripsi'])); ?></div>
                  <div class="activity-footer">
                    <a href="index.php?page=product-detail&id=<?= $row['id_produk']; ?>" class="view-btn">
                        Details <i class="fas fa-arrow-right"></i>
                    </a>
                  </div>
                </div>
              </div>
            </div> 
            <?php
        }
        echo '</div>'; 
    } else {
        echo '<div class="no-activity">
                <i class="fas fa-search fa-3x text-muted mb-3"></i>
                <p>No results found.</p>
              </div>';
    }

    // --- B. OUTPUT PAGINATION ---
    if ($totalPages > 1) {
        echo '<div class="pagination-wrapper">';
        
        // Tombol Previous
        $prevDisabled = ($currPage <= 1) ? 'disabled' : '';
        $prevPage = $currPage - 1;
        echo '<button class="page-btn" onclick="changePage('.$prevPage.')" '.$prevDisabled.'><i class="fas fa-chevron-left"></i></button>';

        // Tombol Angka Halaman
        for ($i = 1; $i <= $totalPages; $i++) {
            $active = ($i == $currPage) ? 'active' : '';
            echo '<button class="page-btn '.$active.'" onclick="changePage('.$i.')">'.$i.'</button>';
        }

        // Tombol Next
        $nextDisabled = ($currPage >= $totalPages) ? 'disabled' : '';
        $nextPage = $currPage + 1;
        echo '<button class="page-btn" onclick="changePage('.$nextPage.')" '.$nextDisabled.'><i class="fas fa-chevron-right"></i></button>';
        
        echo '</div>';
    }

    echo '</div>'; // End #ajax-content-wrapper
    exit; 
}
?>

<style>
    /* CSS SEARCH BAR */
    .search-wrapper-center {
        display: flex; justify-content: center; width: 100%;
        margin-top: 30px; margin-bottom: 40px; padding: 0 15px;
        position: relative; z-index: 2;
    }
    .search-facility-box {
        width: 100%; max-width: 600px; position: relative;
        background: white; border-radius: 50px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    }
    .search-facility-box input {
        width: 100%; border: 1px solid #ddd; border-radius: 50px;
        padding: 15px 60px 15px 30px; background-color: #fff;
        color: #333; font-family: sans-serif; font-size: 16px; outline: none;
        transition: all 0.3s ease;
    }
    .search-facility-box input:focus {
        border-color: #01B5B8; box-shadow: 0 4px 10px rgba(1, 181, 184, 0.15);
    }
    .search-icon-static {
        position: absolute; right: 25px; top: 50%;
        transform: translateY(-50%); color: #aaa;
        font-size: 20px; pointer-events: none;
    }
    .search-loading {
        position: absolute; right: 25px; top: 50%;
        transform: translateY(-50%); color: #01B5B8;
        font-size: 20px; display: none;
    }

    /* HEADER BANNER */
    .inner-banner.product-banner {
        background: url('assets/images/header-facility.jpeg') no-repeat center center;
        background-size: cover; position: relative; z-index: 0;
        min-height: 350px; display: grid; align-items: center; padding-top: 80px; 
    }
    .inner-banner.product-banner:before {
        content: ""; background: rgba(0,0,0,0.6);
        position: absolute; inset: 0; z-index: -1;
    }
    .inner-w3-title { font-size: 3rem; font-weight: 700; color: #fff; margin: 0; }

    /* GRID & CARD */
    .activity-grid {
        display: grid !important; 
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)) !important; 
        gap: 30px; margin-top: 1rem;
    }
    
    .card-link-wrapper { 
        display: block; 
        height: 100%; 
    }

    .activity-card {
        background: white; border-radius: 8px; overflow: hidden;
        box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        display: flex; flex-direction: column; height: 100%; border: 1px solid #eee;
    }

    /* Efek hover pada card (Visual saja, tidak bisa diklik) */
    .card-link-wrapper:hover .activity-card {
        transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        border-color: #01B5B8;
    }

    .activity-image-wrapper {
        position: relative; width: 100%; height: 250px; overflow: hidden; 
        background-color: #fff; display: flex; justify-content: center; 
        align-items: center; border-bottom: 1px solid #eee; padding: 15px;
    }
    .activity-image {
        width: 100%; height: 100%; object-fit: contain; transition: transform 0.5s ease;
    }
    .card-link-wrapper:hover .activity-image { transform: scale(1.05); }
    .activity-content { padding: 20px; flex-grow: 1; display: flex; flex-direction: column; }
    .card-badge {
        font-size: 10px; text-transform: uppercase; color: #999;
        font-weight: 700; margin-bottom: 5px; letter-spacing: 1px;
    }
    .activity-title { 
        color: #02406C; font-size: 1.2rem; font-weight: 700;
        line-height: 1.3; margin-bottom: 10px;
        display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
    }
    .activity-description { 
        color: #666; font-size: 0.95rem; line-height: 1.6; margin-bottom: 20px;
        flex-grow: 1; display: -webkit-box; -webkit-line-clamp: 3; 
        -webkit-box-orient: vertical; overflow: hidden; text-overflow: ellipsis;
        word-wrap: break-word; overflow-wrap: anywhere; 
    }
    .activity-footer { 
        font-size: 0.85rem; color: #555; border-top: 1px solid #f0f0f0;
        padding-top: 15px; margin-top: auto;
        display: flex; align-items: center; justify-content: space-between;
    }

    /* --- STYLE TOMBOL DETAILS --- */
    .view-btn { 
        color: #01B5B8; 
        font-weight: 600; 
        font-size: 12px; 
        display: inline-flex; 
        align-items: center; 
        gap: 5px;
        text-decoration: none; 
        cursor: pointer;
        transition: color 0.3s ease; 
    }
    
    /* Efek Hover Orange pada tombol Details */
    .view-btn:hover {
        color: #FE7C11; 
    }
    
    .no-activity { text-align: center; color: #888; grid-column: 1 / -1; padding: 40px; width: 100%; }

    /* CSS PAGINATION */
    .pagination-wrapper {
        display: flex; justify-content: center; align-items: center; gap: 8px;
        margin-top: 40px; margin-bottom: 20px;
    }
    .page-btn {
        min-width: 40px; height: 40px; border: 1px solid #ddd;
        background: #fff; color: #555; border-radius: 5px;
        font-weight: 600; cursor: pointer; transition: all 0.3s;
        display: flex; justify-content: center; align-items: center;
    }
    .page-btn:hover:not(:disabled) {
        background-color: #f0f0f0; border-color: #ccc;
    }
    .page-btn.active {
        background-color: #01B5B8; color: #fff; border-color: #01B5B8;
    }
    .page-btn:disabled {
        opacity: 0.5; cursor: not-allowed;
    }
</style>

<div class="inner-banner product-banner">
  <section class="w3l-breadcrumb text-center">
    <div class="container">
      <h2 class="inner-w3-title">Our Products</h2>
      <ul class="breadcrumbs-custom-path">
        <li><a href="index.php?page=home">Home</a></li>
        <li class="active"><span class="fas fa-angle-double-right mx-2"></span> Product</li>
      </ul>
    </div>
  </section>
</div>

<div class="container pb-md-5">

    <div class="search-wrapper-center">
        <div class="search-facility-box">
            <form action="#" method="GET" onsubmit="return false;">
                <input type="text" id="searchInput" 
                       placeholder="Search Products..." 
                       value="<?= htmlspecialchars($searchKeyword) ?>"
                       oninput="performLiveSearch(this.value, 1)"> 
                
                <i id="staticSearchIcon" class="fas fa-search search-icon-static"></i>
                <div id="searchSpinner" class="search-loading">
                    <i class="fas fa-spinner fa-spin"></i>
                </div>
            </form>
        </div>
    </div>

    <div id="product-results-container">
        <div id="ajax-content-wrapper">
            <?php if (!empty($produkList)): ?>
              <div class="activity-grid"> 
                <?php foreach ($produkList as $row): ?>
                  <?php
                    $gambar = $row['gambar'];
                    $srcDisplay = ''; 
                    $hasImage = false; 
                    if (!empty($gambar)) {
                        $ext    = pathinfo($gambar, PATHINFO_EXTENSION);
                        $filename = pathinfo($gambar, PATHINFO_FILENAME);
                        $thumbName = $filename . '-thumb.' . $ext;
                        if (file_exists($serverThumbPath . $thumbName)) {
                            $srcDisplay = $webThumbPath . $thumbName;
                            $hasImage = true;
                        } elseif (file_exists($serverImgPath . $gambar)) {
                            $srcDisplay = $webImgPath . $gambar;
                            $hasImage = true;
                        }
                    }
                  ?>
                  
                  <div class="card-link-wrapper">
                      <div class="activity-card">
                        <div class="activity-image-wrapper">
                          <?php if ($hasImage): ?>
                            <img src="<?= htmlspecialchars($srcDisplay); ?>" alt="<?= htmlspecialchars($row['nama']); ?>" class="activity-image">
                          <?php else: ?>
                            <div style="display: flex; flex-direction: column; justify-content: center; align-items: center; height: 100%; color: #ccc;">
                                  <i class="fas fa-box-open" style="font-size: 48px; margin-bottom: 10px;"></i>
                                  <span style="font-size: 14px;">No Image</span>
                            </div>
                          <?php endif; ?>
                        </div>
                        <div class="activity-content">
                          <span class="card-badge">PRODUCT</span>
                          <h4 class="activity-title"><?= htmlspecialchars($row['nama']); ?></h4>
                          <div class="activity-description"><?= nl2br(htmlspecialchars($row['deskripsi'])); ?></div>
                          <div class="activity-footer">
                            <a href="index.php?page=product-detail&id=<?= $row['id_produk']; ?>" class="view-btn">
                                Details <i class="fas fa-arrow-right"></i>
                            </a>
                          </div>
                        </div>
                      </div>
                  </div> 
                <?php endforeach; ?>
              </div>
            <?php else: ?>
              <div class="no-activity">
                <i class="fas fa-search fa-3x text-muted mb-3"></i>
                <p>No featured products available at the moment.</p>
              </div>
            <?php endif; ?>

            <?php if ($totalPages > 1): ?>
            <div class="pagination-wrapper">
                <?php 
                $prevDisabled = ($currPage <= 1) ? 'disabled' : '';
                $prevPage = $currPage - 1;
                ?>
                <button class="page-btn" onclick="changePage(<?= $prevPage ?>)" <?= $prevDisabled ?>><i class="fas fa-chevron-left"></i></button>

                <?php 
                for ($i = 1; $i <= $totalPages; $i++) {
                    $active = ($i == $currPage) ? 'active' : '';
                    echo '<button class="page-btn '.$active.'" onclick="changePage('.$i.')">'.$i.'</button>';
                }
                
                $nextDisabled = ($currPage >= $totalPages) ? 'disabled' : '';
                $nextPage = $currPage + 1;
                ?>
                <button class="page-btn" onclick="changePage(<?= $nextPage ?>)" <?= $nextDisabled ?>><i class="fas fa-chevron-right"></i></button>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    let searchTimeout;
    
    // Fungsi ganti halaman
    function changePage(pageNum) {
        const keyword = document.getElementById('searchInput').value;
        performLiveSearch(keyword, pageNum);
        
        // Scroll ke atas grid
        const container = document.getElementById('product-results-container');
        window.scrollTo({
            top: container.offsetTop - 150, 
            behavior: 'smooth'
        });
    }

    // Fungsi Utama AJAX Search & Pagination
    function performLiveSearch(keyword, page = 1) {
        const spinner = document.getElementById('searchSpinner');
        const staticIcon = document.getElementById('staticSearchIcon');
        const container = document.getElementById('product-results-container');

        spinner.style.display = 'block';
        staticIcon.style.opacity = '0';
        container.style.opacity = '0.5';

        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            fetch(`index.php?page=product&ajax=1&search=${encodeURIComponent(keyword)}&p=${page}`)
                .then(res => res.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    
                    const newContent = doc.getElementById('ajax-content-wrapper');

                    if (newContent) {
                        container.innerHTML = '';
                        container.appendChild(newContent);
                    } else {
                        container.innerHTML = '<div class="no-activity"><p>No results found.</p></div>';
                    }

                    spinner.style.display = 'none';
                    staticIcon.style.opacity = '1';
                    container.style.opacity = '1';
                })
                .catch(err => {
                    console.error('Search error:', err);
                    spinner.style.display = 'none';
                    staticIcon.style.opacity = '1';
                    container.style.opacity = '1';
                });
        }, 300);
    }
</script>