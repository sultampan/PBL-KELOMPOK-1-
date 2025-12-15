<?php
// ==========================================
// 1. KONEKSI & PERSIAPAN DATA (POSTGRESQL)
// ==========================================
$rootPath = dirname(dirname(__DIR__)); 
$koneksiPath = $rootPath . '/config/koneksi.php';

if (file_exists($koneksiPath)) {
    require_once $koneksiPath;
}

// --- LOGIKA PENCARIAN ---
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

// --- FUNGSI QUERY DATA (KHUSUS POSTGRESQL) ---
function getProdukData($pdo, $keyword) {
    try {
        // [POSTGRESQL] Menggunakan STRING_AGG & ILIKE
        $sql = "
          SELECT 
            p.id_produk,
            p.nama,
            p.deskripsi,
            p.gambar,
            p.link_produk, 
            STRING_AGG(DISTINCT m.nama_member || ' (' || pm.role || ')', ', ') AS members
          FROM produk p
          LEFT JOIN produk_member pm ON p.id_produk = pm.id_produk
          LEFT JOIN member m ON pm.id_member = m.id_member
        ";

        // Tambahkan Filter
        if (!empty($keyword)) {
            $sql .= " WHERE p.nama ILIKE :keyword OR p.deskripsi ILIKE :keyword";
        }

        $sql .= " GROUP BY p.id_produk, p.nama, p.deskripsi, p.gambar, p.link_produk ORDER BY p.id_produk DESC";
        
        $stmt = $pdo->prepare($sql);
        
        if (!empty($keyword)) {
            $stmt->bindValue(':keyword', "%$keyword%", PDO::PARAM_STR);
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) { return []; }
}

// ==========================================
// 2. HANDLER AJAX (LIVE SEARCH)
// ==========================================
if (isset($_GET['ajax']) && $_GET['ajax'] == '1') {
    while (ob_get_level()) { ob_end_clean(); }
    
    $produkList = getProdukData($pdo, $searchKeyword);
    
    // --- OUTPUT GRID SAJA ---
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
            <a href="index.php?page=product-detail&id=<?= $row['id_produk']; ?>" class="card-link-wrapper">
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
                    <span class="view-btn">Details <i class="fas fa-arrow-right"></i></span>
                  </div>
                </div>
              </div>
            </a> 
            <?php
        }
        echo '</div>'; 
    } else {
        echo '<div class="no-activity">
                <i class="fas fa-search fa-3x text-muted mb-3"></i>
                <p>Produk tidak ditemukan.</p>
              </div>';
    }
    exit; 
}

// --- LOAD DATA AWAL ---
$produkList = getProdukData($pdo, $searchKeyword);
?>

<style>
    /* CSS SEARCH BAR */
    .search-wrapper-center {
        display: flex;
        justify-content: center;
        width: 100%;
        margin-top: 30px; 
        margin-bottom: 40px;
        padding: 0 15px;
        position: relative;
        z-index: 2;
    }

    .search-facility-box {
        width: 100%;
        max-width: 600px;
        position: relative;
        background: white;
        border-radius: 50px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    }

    .search-facility-box input {
        width: 100%;
        border: 1px solid #ddd;
        border-radius: 50px;
        padding: 15px 60px 15px 30px;
        background-color: #fff;
        color: #333;
        font-family: sans-serif;
        font-weight: 400;
        font-size: 16px;
        outline: none;
        transition: all 0.3s ease;
    }
    
    .search-facility-box input:focus {
        border-color: #01B5B8;
        box-shadow: 0 4px 10px rgba(1, 181, 184, 0.15);
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
        background-size: cover;
        position: relative;
        z-index: 0;
        min-height: 350px; 
        display: grid;
        align-items: center;
        padding-top: 80px; 
    }
    .inner-banner.product-banner:before {
        content: ""; background: rgba(0,0,0,0.6);
        position: absolute; inset: 0; z-index: -1;
    }
    .inner-w3-title {
        font-size: 3rem; font-weight: 700; color: #fff; margin: 0;
    }

    /* CARD STYLE */
    .activity-grid {
        display: grid !important; 
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)) !important; 
        gap: 30px; margin-top: 1rem;
    }

    .card-link-wrapper {
        display: block; text-decoration: none; color: inherit; height: 100%; 
    }

    .activity-card {
        background: white; border-radius: 8px; overflow: hidden;
        box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        display: flex; flex-direction: column; height: 100%; border: 1px solid #eee;
    }

    .card-link-wrapper:hover .activity-card {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        border-color: #01B5B8;
    }

    /* --- [FIX BAGIAN INI] AGAR GAMBAR TIDAK ZOOM --- */
    .activity-image-wrapper {
        position: relative; 
        width: 100%; 
        height: 250px; /* Tinggi ditambah sedikit biar lega */
        overflow: hidden; 
        background-color: #fff; /* Background putih bersih */
        display: flex; 
        justify-content: center; 
        align-items: center; 
        border-bottom: 1px solid #eee;
        padding: 15px; /* Tambah padding agar gambar tidak mepet pinggir */
    }

    .activity-image {
        width: 100%; 
        height: 100%; 
        object-fit: contain; /* KUNCI UTAMA: Agar gambar tampil utuh (tidak dicrop) */
        transition: transform 0.5s ease;
    }
    
    .card-link-wrapper:hover .activity-image { 
        transform: scale(1.05); /* Zoom sedikit saat hover tetap ada biar keren */
    }
    /* ----------------------------------------------- */

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

    .view-btn {
        color: #01B5B8; font-weight: 600; font-size: 12px;
        display: flex; align-items: center; gap: 5px;
    }

    .no-activity { 
        text-align: center; color: #888; grid-column: 1 / -1;
        padding: 40px; width: 100%;
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
                       placeholder="Cari Produk..." 
                       value="<?= htmlspecialchars($searchKeyword) ?>"
                       oninput="performLiveSearch(this.value)"> 
                
                <i id="staticSearchIcon" class="fas fa-search search-icon-static"></i>
                <div id="searchSpinner" class="search-loading">
                    <i class="fas fa-spinner fa-spin"></i>
                </div>
            </form>
        </div>
    </div>

    <div id="product-results-container">
        <?php if (!empty($produkList)): ?>
          <div class="activity-grid"> 
            <?php foreach ($produkList as $row): ?>
              <?php
                // Setup Gambar
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
              <a href="index.php?page=product-detail&id=<?= $row['id_produk']; ?>" class="card-link-wrapper">
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
                        <span class="view-btn">Lihat Detail <i class="fas fa-arrow-right"></i></span>
                      </div>
                    </div>
                  </div>
              </a> 
            <?php endforeach; ?>
          </div>
        <?php else: ?>
          <div class="no-activity">
            <i class="fas fa-search fa-3x text-muted mb-3"></i>
            <p>Belum ada produk unggulan yang tersedia saat ini.</p>
          </div>
        <?php endif; ?>
    </div>

  </div>

<script>
    let searchTimeout;

    function performLiveSearch(keyword) {
        const spinner = document.getElementById('searchSpinner');
        const staticIcon = document.getElementById('staticSearchIcon');
        const container = document.getElementById('product-results-container');

        spinner.style.display = 'block';
        staticIcon.style.opacity = '0';
        container.style.opacity = '0.5';

        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            fetch(`index.php?page=product&ajax=1&search=${encodeURIComponent(keyword)}`)
                .then(res => res.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    
                    const newGrid = doc.querySelector('.activity-grid');
                    const noData = doc.querySelector('.no-activity');

                    if (newGrid) {
                        container.innerHTML = newGrid.outerHTML;
                    } else if (noData) {
                        container.innerHTML = noData.outerHTML;
                    } else {
                        container.innerHTML = '<div class="no-activity"><p>Tidak ada hasil ditemukan.</p></div>';
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