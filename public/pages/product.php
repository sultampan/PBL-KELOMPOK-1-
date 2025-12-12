<?php
// ==========================================
// 1. KONEKSI & AMBIL DATA PRODUK
// ==========================================
$rootPath = dirname(dirname(__DIR__)); 
$koneksiPath = $rootPath . '/config/koneksi.php';

if (file_exists($koneksiPath)) {
    require_once $koneksiPath;
}

// ==========================================
// 2. PENGATURAN PATH GAMBAR & LINK
// ==========================================
$webThumbPath = 'uploads/thumb/produk-thumb/';
$webImgPath  = 'uploads/produk/';
$serverBase = $rootPath . '/public'; 
$serverThumbPath = $serverBase . '/uploads/thumb/produk-thumb/';
$serverImgPath  = $serverBase . '/uploads/produk/';


$produkList = [];
if (isset($pdo)) {
  try {
    $query = "
      SELECT 
        p.id_produk,
        p.nama,
        p.deskripsi,
        p.gambar,
                p.link_produk, 
        STRING_AGG(m.nama_member || ' (' || pm.role || ')', ', ') AS members
      FROM produk p
            LEFT JOIN produk_member pm ON p.id_produk = pm.id_produk
            LEFT JOIN member m ON pm.id_member = m.id_member
            GROUP BY p.id_produk, p.nama, p.deskripsi, p.gambar, p.link_produk 
      ORDER BY p.id_produk DESC
    ";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $produkList = $stmt->fetchAll(PDO::FETCH_ASSOC);
  } catch (Exception $e) { }
}
?>

<style>
/* ==================================================== */
/* CSS LAMA AGAR CARD TAMPIL RAPI (TIDAK ADA PERUBAHAN) */
/* ==================================================== */

/* Grid Layout */
.activity-grid {
  display: grid !important; 
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)) !important; 
  gap: 30px;
  margin-top: 2rem;
}

/* Card Style */
.activity-card {
  background: white;
  border-radius: 10px;
  overflow: hidden;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  display: flex;
  flex-direction: column;
}

.activity-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
}

.activity-image-wrapper {
  position: relative;
  width: 100%;
  height: 250px;
  overflow: hidden;
  background-color: #e0e0e0;
    /* CENTERING GAMBAR */
    display: flex;
    justify-content: center; 
    align-items: center; 
}

.activity-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.3s ease;
}

.activity-card:hover .activity-image {
  transform: scale(1.1);
}

.activity-content {
  padding: 20px;
  flex-grow: 1;
  display: flex;
  flex-direction: column;
}

.activity-title { 
  color: #333;
  font-size: 1.1rem;
  font-weight: 600;
  line-height: 1.4;
  margin-bottom: 10px;
}

.activity-description { 
  color: #555;
  font-size: 0.95rem;
  line-height: 1.6; 
  margin-bottom: 15px;
  flex-grow: 1;
  display: -webkit-box;
  -webkit-box-orient: vertical;
  -webkit-line-clamp: 3; 
  overflow: hidden;
  text-overflow: ellipsis;
}

.activity-members { 
  font-size: 0.9rem;
  color: #333;
  font-weight: 500;
  border-top: 1px solid #eee;
  padding-top: 10px;
}

.activity-members span {
  font-weight: normal;
  color: #555;
  display: block;
  margin-top: 3px;
}

.no-activity { 
  text-align: center;
  color: #888;
  grid-column: 1 / -1;
  padding: 40px;
  font-size: 1.1rem;
}

/* Tambahan CSS untuk Link Wrapper */
.card-link-wrapper {
    display: block; 
    text-decoration: none; 
    color: inherit; 
    height: 100%; 
}

/* Styling Banner */
.inner-banner.product-banner {
    background: url('assets/images/header-facility.jpeg') no-repeat center;
    background-size: cover;
    position: relative;
    z-index: 0;
    min-height: 350px;
    display: grid;
    align-items: center;
}
.inner-banner.product-banner:before {
    content: "";
    background: rgba(0,0,0,0.6);
    position: absolute;
    inset: 0;
    z-index: -1;
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

<section class="w3l-gallery pb-5 pt-4">
  <div class="container pb-md-5 pt-3">

    <div class="title-content text-center mb-5">
      <h6 class="title-subw3hny">Explore Our Works</h6>
      <h3 class="title-w3l mb-4"> Produk Unggulan Kami </h3>
    </div>

    <?php if (!empty($produkList)): ?>
      <div class="activity-grid"> 

        <?php foreach ($produkList as $row): ?>
          <?php
                        // ==========================================
                        // LOGIKA LINK ABSOLUT (DIPERKUAT DENGAN HTTPS DEFAULT)
                        // ==========================================
                        $rawLink = $row['link_produk'];
                        $finalLink = '#'; // Default ke '#' jika link kosong

                        if (!empty($rawLink)) {
                            // 1. Membersihkan link dari protokol lama (jika ada)
                            $cleanLink = preg_replace('#^https?://#i', '', $rawLink);

                            // 2. Memastikan protokol HTTPS yang merupakan standar web modern
                            $finalLink = 'https://' . $cleanLink; 
                        }


            // LOGIKA CEK GAMBAR (tetap sama) 
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

          <a href="<?= htmlspecialchars($finalLink); ?>" class="card-link-wrapper" target="_blank">
              <div class="activity-card">
                <div class="activity-image-wrapper">
                                
                  <?php if ($hasImage): ?>
                    <img src="<?= htmlspecialchars($srcDisplay); ?>"
                      alt="<?= htmlspecialchars($row['nama']); ?>"
                      class="activity-image">
                  <?php else: ?>
                    <div style="display: flex; flex-direction: column; justify-content: center; align-items: center; height: 100%; background-color: #f0f0f0; color: #999;">
                          <i class="fa fa-image" style="font-size: 48px; margin-bottom: 10px;"></i>
                          <span style="font-size: 14px;">Tidak ada gambar</span>
                    </div>
                  <?php endif; ?>
                </div>

                <div class="activity-content">
                                
                  <h4 class="activity-title">
                    <?= htmlspecialchars($row['nama']); ?>
                  </h4>

                  <div class="activity-description">
                    <?= nl2br(htmlspecialchars($row['deskripsi'])); ?>
                  </div>
              
                  <div class="activity-members">
                    <strong>Pengembang :</strong>
                    <span>
                      <?= !empty($row['members']) ? htmlspecialchars($row['members']) : 'Tim LabAI'; ?>
                    </span>
                  </div>
                </div>
              </div>
                    </a> 

        <?php endforeach; ?>

      </div>
    <?php else: ?>
      <div class="no-activity">
        <p>Belum ada produk unggulan yang tersedia saat ini.</p>
      </div>
    <?php endif; ?>

  </div>
</section>