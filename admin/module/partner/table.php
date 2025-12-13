<div id="partner-list-container">
    <div class="toolbar-header">
        <div class="header-title">Daftar Partner</div>
        <div class="search-form">
            <div class="search-group">
                <input type="text" id="searchPartnerInput" class="search-input" 
                       placeholder="Cari Partner..." 
                       value="<?= htmlspecialchars($searchKeyword ?? '') ?>">
                <button type="button" onclick="searchPartner()" class="btn-cari">
                    <i class="fas fa-search"></i> Cari
                </button>
            </div>
        </div>
    </div>

    <div class="partner-wrapper">
        <?php if ($list): ?>
            <?php 
            // 1. Grouping Data by PHP Array dulu biar rapi
            $grouped = [];
            foreach ($list as $item) {
                $cat = $item['kategori'] ?: 'Uncategorized';
                $grouped[$cat][] = $item;
            }

            // 2. Render Per Kategori
            foreach ($grouped as $categoryName => $items): 
            ?>
                <div class="partner-category-group mb-5">
                    
                    <h3 class="category-separator"><?= htmlspecialchars($categoryName) ?></h3>
                    
                    <div class="partner-grid paginated-grid" id="grid-<?= md5($categoryName) ?>" data-current-page="1" data-items-per-page="6">
                        
                        <?php foreach ($items as $row): 
                            // Logika Gambar
                            $image_path = '';
                            if ($row['gambar']) {
                                $thumbName = pathinfo($row['gambar'], PATHINFO_FILENAME) . '-thumb.' . pathinfo($row['gambar'], PATHINFO_EXTENSION);
                                // Cek file fisik
                                if (is_file($serverThumbDir . $thumbName)) {
                                    $image_path = $webThumbDir . $thumbName;
                                } elseif (is_file($serverUploadDir . $row['gambar'])) {
                                    $image_path = $webUploadDir . $row['gambar'];
                                }
                                if ($image_path) $image_path .= '?' . time();
                            }
                        ?>
                            <div class="partner-card grid-item">
                                <div class="partner-img-wrapper">
                                    <?php if ($image_path): ?>
                                        <img src="<?= $image_path ?>" alt="Logo">
                                    <?php else: ?>
                                        <div class="no-image-placeholder">
                                            <i class="fas fa-image" style="font-size: 24px; margin-bottom: 5px;"></i>
                                            <span>No Logo</span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="partner-content">
                                    <div class="partner-title" title="<?= htmlspecialchars($row['nama']) ?>">
                                        <?= htmlspecialchars($row['nama']) ?>
                                    </div>
                                </div>
                                <div class="card-action-buttons">
                                    <a href="?page=partner&edit=<?= $row['id_partner'] ?>" class="btn-card btn-card-edit">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <button type="button" onclick="deletePartner(<?= (int)$row['id_partner'] ?>)" class="btn-card btn-card-delete">
                                        <i class="fas fa-trash"></i> Hapus
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        
                    </div>

                    <div class="pagination-container" id="pagination-<?= md5($categoryName) ?>"></div>

                </div>
            <?php endforeach; ?>

        <?php else: ?>
            <div class="partner-grid">
                <div class="empty-state">
                    <i class="fas fa-handshake" style="font-size: 40px; color: #ccc; margin-bottom: 10px;"></i>
                    <p>Belum ada data partner yang ditemukan.</p>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
    // Jalankan fungsi paginasi setiap tabel ini dimuat
    if(typeof initPartnerPagination === 'function') {
        initPartnerPagination();
    }
</script>