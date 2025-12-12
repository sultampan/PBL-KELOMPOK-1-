<div id="partner-list-container">
    <?php
    global $webUploadDir, $webThumbDir, $serverUploadDir, $serverThumbDir;
    if (isset($paginationData)) extract($paginationData);
    else { $currentPage = 1; $totalPages = 1; $searchKeyword = null; $list = []; }
    ?>

    <div class="toolbar-header">
        <div class="header-title">Daftar Partner</div>
        <div class="search-form">
            <div class="search-group">
                <input type="text" id="searchPartnerInput" class="search-input" 
                       placeholder="Cari Partner atau Kategori..." 
                       value="<?= htmlspecialchars($searchKeyword ?? '') ?>">
                <button type="button" onclick="searchPartner()" class="btn-cari">
                    <i class="fa fa-search"></i> Cari
                </button>
            </div>
        </div>
    </div>

    <div class="partner-grid">
        <?php if ($list): ?>
            <?php foreach ($list as $row): ?>
                <div class="partner-card">
                    
                    <div class="partner-img-wrapper">
                        <?php 
                            $image_path = '';
                            if ($row['gambar']) {
                                // Cek Thumbnail dulu
                                $thumbName = pathinfo($row['gambar'], PATHINFO_FILENAME) . '-thumb.' . pathinfo($row['gambar'], PATHINFO_EXTENSION);
                                if (is_file($serverThumbDir . $thumbName)) {
                                    $image_path = $webThumbDir . $thumbName;
                                } elseif (is_file($serverUploadDir . $row['gambar'])) {
                                    $image_path = $webUploadDir . $row['gambar'];
                                }
                                if ($image_path) $image_path .= '?' . time(); // Cache buster
                            }
                        ?>
                        <?php if ($image_path): ?>
                            <img src="<?= $image_path ?>" alt="<?= htmlspecialchars($row['nama']) ?>" loading="lazy">
                        <?php else: ?>
                            <div class="no-image-placeholder">
                                <i class="fas fa-image" style="font-size: 24px; margin-bottom: 5px;"></i>
                                <span>Tidak ada gambar</span>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="partner-content">
                        <span class="partner-category-badge">
                            <?= htmlspecialchars($row['kategori']) ?>
                        </span>
                        
                        <div class="partner-title" title="<?= htmlspecialchars($row['nama']) ?>">
                            <?= htmlspecialchars($row['nama']) ?>
                        </div>
                    </div>

                    <div class="card-action-buttons">
                        <a href="?page=partner&edit=<?= $row['id_partner'] ?>" class="btn-card btn-card-edit">
                            <i></i> Edit
                        </a>
                        
                        <button type="button" onclick="deletePartner(<?= (int)$row['id_partner'] ?>)" class="btn-card btn-card-delete">
                            <i></i> Hapus
                        </button>
                    </div>

                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-handshake" style="font-size: 40px; color: #ccc; margin-bottom: 10px;"></i>
                <p>Belum ada data partner yang ditemukan.</p>
            </div>
        <?php endif; ?>
    </div>

    <?php if ($totalPages > 1): ?>
        <div class="pagination">
            <?php 
                function getLink($p, $k) { return "?page=partner&p=$p" . ($k ? "&keyword=".urlencode($k) : ""); } 
            ?>
            <?php if ($currentPage > 1): ?>
                <a href="<?= getLink($currentPage - 1, $searchKeyword) ?>" class="page-link page-arrow">&laquo;</a>
            <?php endif; ?>
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="<?= getLink($i, $searchKeyword) ?>" class="page-link page-num <?= ($i == $currentPage) ? 'active' : '' ?>"><?= $i ?></a>
            <?php endfor; ?>
            <?php if ($currentPage < $totalPages): ?>
                <a href="<?= getLink($currentPage + 1, $searchKeyword) ?>" class="page-link page-arrow">&raquo;</a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>