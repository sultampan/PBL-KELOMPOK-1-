<?php
// admin/module/produk/table.php

// --- 1. DEFINISI VARIABEL & FUNGSI HELPER (Ala Member) ---
global $webUploadDir, $webThumbDir, $serverUploadDir, $serverThumbDir;

// Extract data dari controller (index.php)
if (isset($paginationData) && is_array($paginationData)) {
    extract($paginationData);
} else {
    // Default values jika tidak ada data
    $currentPage = 1; $totalPages = 1; $searchKeyword = null;
    $limit = 6; $currentSortBy = 'id_produk'; $currentSortOrder = 'DESC';
    $list = []; $totalRecords = 0;
}

// Helper: Build URL Paginasi
if (!function_exists('buildPageUrl')) {
    function buildPageUrl($p, $searchKeyword) {
        $qs = '?page=produk&p=' . $p;
        if ($searchKeyword) $qs .= '&keyword=' . urlencode($searchKeyword);
        return $qs;
    }
}

// Helper: Fix URL (Menambahkan https:// jika tidak ada)
if (!function_exists('fixUrl')) {
    function fixUrl($url) {
        $url = trim($url);
        if (empty($url)) return '';
        if (strpos($url, 'http://') === false && strpos($url, 'https://') === false) {
            return 'https://' . $url;
        }
        return $url;
    }
}
?>

<div id="product-list-container">
    <div class="product-main-card">

        <div class="card-header-section">
            <div class="header-title">Daftar Produk</div>

            <div class="search-section">
                <input type="text" id="searchProductInput" class="search-input"
                    placeholder="Cari Nama Produk / Deskripsi..."
                    value="<?= htmlspecialchars($searchKeyword ?? '') ?>">

                <button type="button" onclick="searchProduct()" class="btn-search">
                    <i class="fas fa-search"></i> Cari
                </button>

                <?php if ($searchKeyword): ?>
                    <button type="button" onclick="resetSearchProduct()" class="btn-search" style="background:#666;" title="Hapus Pencarian">
                        Reset
                    </button>
                <?php endif; ?>
            </div>
        </div>

        <div class="product-content">
            
            <?php if ($list && count($list) > 0): ?>
                
                <div style="font-size:13px; color:#777; margin-bottom:15px; text-align:right;">
                    Page <?= $currentPage ?> of <?= $totalPages ?> (Total <?= $totalRecords ?> items)
                </div>

                <div class="product-grid">
                    <?php foreach ($list as $row): 
                        // Encode data untuk JS (jika nanti butuh modal detail)
                        $dataJson = htmlspecialchars(json_encode($row), ENT_QUOTES, 'UTF-8');
                    ?>
                        <div class="product-item">
                            
                            <div class="product-item-header">
                                <span class="badge-product">PRODUK</span>
                                
                                <?php if (!empty($row['link_produk'])): 
                                    $link = fixUrl($row['link_produk']);
                                ?>
                                    <a href="<?= $link ?>" target="_blank" class="link-product-btn" title="Kunjungi Website">
                                        <i class="fas fa-external-link-alt"></i> Link
                                    </a>
                                <?php else: ?>
                                    <span style="font-size:11px; color:#aaa;">No Link</span>
                                <?php endif; ?>
                            </div>

                            <h3 class="product-item-title" title="<?= htmlspecialchars($row['nama']) ?>">
                                <?= htmlspecialchars($row['nama']) ?>
                            </h3>

                            <div class="product-item-image">
                                <?php
                                $imgSrc = ''; 
                                $hasImage = false;
                                
                                if (!empty($row['gambar'])) {
                                    // Cek Thumbnail dulu
                                    $thumb = pathinfo($row['gambar'], PATHINFO_FILENAME) . '-thumb.' . pathinfo($row['gambar'], PATHINFO_EXTENSION);
                                    
                                    if (file_exists($serverThumbDir . $thumb)) {
                                        $imgSrc = $webThumbDir . $thumb; 
                                        $hasImage = true;
                                    } 
                                    // Cek Gambar Asli jika thumb tidak ada
                                    elseif (file_exists($serverUploadDir . $row['gambar'])) {
                                        $imgSrc = $webUploadDir . $row['gambar']; 
                                        $hasImage = true;
                                    }
                                    
                                    // Anti-cache param
                                    if ($hasImage) $imgSrc .= '?' . time();
                                }
                                ?>
                                
                                <?php if ($hasImage): ?>
                                    <img src="<?= $imgSrc ?>" alt="<?= htmlspecialchars($row['nama']) ?>">
                                <?php else: ?>
                                    <div class="no-image">
                                        <i class="fas fa-box-open"></i>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="product-item-description">
                                <?= !empty($row['deskripsi']) ? nl2br(htmlspecialchars($row['deskripsi'])) : '-' ?>
                            </div>

                            <div class="product-item-members">
                                <div class="members-label">
                                    <i class="fas fa-users"></i> Tim Pengembang:
                                </div>
                                <div class="members-tags">
                                    <?php 
                                    if (!empty($row['team']) && is_array($row['team'])) {
                                        foreach ($row['team'] as $member):
                                            $mName = $member['nama_member'] ?? 'Unknown';
                                            $mRole = $member['role'] ?? 'Dev';
                                            // Tampilkan Role dalam kurung kecil
                                            echo "<span class='member-tag'><i class='fas fa-user'></i> $mName <small style='opacity:0.7; margin-left:3px;'>($mRole)</small></span>";
                                        endforeach;
                                    } else {
                                        echo "<span style='font-size:12px; color:#999; font-style:italic;'>Tidak ada data tim.</span>";
                                    }
                                    ?>
                                </div>
                            </div>

                            <div class="product-item-actions">
                                <a href="?page=produk&edit=<?= $row['id_produk'] ?>" class="btn-action btn-edit">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <button type="button" onclick="deleteProduct(<?= (int)$row['id_produk'] ?>)" class="btn-action btn-delete">
                                    <i class="fas fa-trash"></i> Hapus
                                </button>
                            </div>

                        </div>
                    <?php endforeach; ?>
                </div>

                <?php if ($totalPages > 1): ?>
                <div class="pagination-container">
                    
                    <?php if ($currentPage > 1): ?>
                        <a href="<?= buildPageUrl($currentPage - 1, $searchKeyword) ?>" class="page-btn" title="Sebelumnya">‹</a>
                    <?php else: ?>
                        <span class="page-btn disabled">‹</span>
                    <?php endif; ?>

                    <?php if ($currentPage > 1): ?>
                        <a href="<?= buildPageUrl(1, $searchKeyword) ?>" class="page-btn" title="Ke Awal">«</a>
                    <?php else: ?>
                        <span class="page-btn disabled">«</span>
                    <?php endif; ?>

                    <?php
                    $max_buttons = 5;
                    $half = floor($max_buttons / 2);
                    $start_page = $currentPage - $half;
                    $end_page   = $currentPage + $half;

                    if ($start_page < 1) { 
                        $start_page = 1; 
                        $end_page = $start_page + $max_buttons - 1; 
                    }
                    if ($end_page > $totalPages) { 
                        $end_page = $totalPages; 
                        $start_page = $end_page - $max_buttons + 1; 
                        if ($start_page < 1) $start_page = 1; 
                    }

                    for ($i = $start_page; $i <= $end_page; $i++):
                        $isActive = ($i == $currentPage) ? 'active' : '';
                    ?>
                        <a href="<?= buildPageUrl($i, $searchKeyword) ?>" class="page-btn <?= $isActive ?>"><?= $i ?></a>
                    <?php endfor; ?>

                    <?php if ($currentPage < $totalPages): ?>
                        <a href="<?= buildPageUrl($totalPages, $searchKeyword) ?>" class="page-btn" title="Ke Akhir">»</a>
                    <?php else: ?>
                        <span class="page-btn disabled">»</span>
                    <?php endif; ?>

                    <?php if ($currentPage < $totalPages): ?>
                        <a href="<?= buildPageUrl($currentPage + 1, $searchKeyword) ?>" class="page-btn" title="Berikutnya">›</a>
                    <?php else: ?>
                        <span class="page-btn disabled">›</span>
                    <?php endif; ?>

                </div>
                <?php endif; ?>

            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-box-open"></i>
                    <p>Belum ada data produk yang ditemukan.</p>
                </div>
            <?php endif; ?>
        </div>

    </div>
</div>