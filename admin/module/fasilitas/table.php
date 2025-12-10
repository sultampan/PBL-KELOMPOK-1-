<div id="fasilitas-list-container">
    
    <?php
    global $webUploadDir, $webThumbDir, $serverUploadDir, $serverThumbDir;
    
    // Fallback variable jika tidak ada data dari controller
    if (isset($paginationData) && is_array($paginationData)) {
        extract($paginationData);
    } else {
        $currentPage = 1; $totalPages = 1; $searchKeyword = null; $limit = 10;
        $currentSortBy = 'id_fasilitas'; $currentSortOrder = 'ASC';
        $list = [];
    }

    // Helper function untuk sorting link
    function getSortLink($column, $label, $currentSortBy, $currentSortOrder, $searchKeyword, $currentPage) {
        $newOrder = 'ASC';
        $activeStyle = '';
        $icon = '';

        if ($currentSortBy === $column) {
            $newOrder = $currentSortOrder === 'ASC' ? 'DESC' : 'ASC';
            $activeStyle = 'style="color: #F28C28; font-weight:bold; text-decoration:underline;"'; 
            $icon = $currentSortOrder === 'ASC' ? ' &#9650;' : ' &#9660;'; // Panah atas/bawah
        }
        
        $queryString = '?page=fasilitas&sort=' . $column . '&order=' . $newOrder;
        if ($searchKeyword) $queryString .= '&keyword=' . urlencode($searchKeyword);
        $queryString .= '&p=' . $currentPage;

        return '<a href="' . $queryString . '" ' . $activeStyle . '>' . $label . $icon . '</a>';
    }
    ?>

    <div class="toolbar-header">
        <div>
            <strong>Total:</strong> <?= $totalRecords ?? 0 ?> Fasilitas
        </div>
        <div class="sort-links">
            Urutkan: 
            <?= getSortLink('judul', 'Nama', $currentSortBy, $currentSortOrder, $searchKeyword, $currentPage) ?> 
            <span style="color:#ccc; margin:0 5px;">|</span>
            <?= getSortLink('id_fasilitas', 'Terbaru', $currentSortBy, $currentSortOrder, $searchKeyword, $currentPage) ?>
        </div>
    </div>

    <div class="fasilitas-grid">
        
        <?php if ($list): ?>
            <?php foreach ($list as $row): ?>
                
                <div class="fasilitas-card">
                    <div class="fasilitas-img-wrapper">
                        <?php 
                            $image_path = '';
                            if ($row['gambar']) {
                                $original_filename = $row['gambar'];
                                $ext = pathinfo($original_filename, PATHINFO_EXTENSION);
                                $base_name = pathinfo($original_filename, PATHINFO_FILENAME);
                                $thumbnail_filename = $base_name . '-thumb.' . $ext;

                                $server_thumb_path = $serverThumbDir . $thumbnail_filename;
                                $server_original_path = $serverUploadDir . $original_filename;

                                // Prioritas: Thumbnail -> Asli
                                if (is_file($server_thumb_path)) {
                                    $image_path = $webThumbDir . $thumbnail_filename;
                                    $path_for_mtime = $server_thumb_path;
                                } elseif (is_file($server_original_path)) {
                                    $image_path = $webUploadDir . $original_filename;
                                    $path_for_mtime = $server_original_path;
                                }

                                // Cache busting
                                if (!empty($image_path) && is_file($path_for_mtime)) {
                                    $image_path .= '?' . filemtime($path_for_mtime); 
                                }
                            }
                        ?>

                        <?php if (!empty($image_path)): ?>
                            <img src="<?= $image_path ?>" alt="<?= htmlspecialchars($row['judul']) ?>" loading="lazy">
                        <?php else: ?>
                            <div class="no-image-placeholder">
                                <i class="fa fa-image" style="font-size:24px; margin-right:5px;"></i> Tidak ada gambar
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="fasilitas-content">
                        <div class="fasilitas-title">
                            <?= htmlspecialchars($row['judul']) ?>
                        </div>
                        <div class="fasilitas-desc" title="<?= htmlspecialchars($row['deskripsi']) ?>">
                            <?= htmlspecialchars($row['deskripsi']) ?>
                        </div>
                    </div>

                    <div class="card-action-buttons">
                        <a href="?page=fasilitas&edit=<?= $row['id_fasilitas'] ?>" class="btn-card btn-card-edit">
                            Edit
                        </a>
                        <button type="button" onclick="deleteFasilitas(<?= (int)$row['id_fasilitas'] ?>)" class="btn-card btn-card-delete">
                            Hapus
                        </button>
                    </div>
                </div>

            <?php endforeach; ?>
        <?php else: ?>
            <div style="grid-column: 1 / -1; text-align: center; padding: 50px; background: #fff; border-radius: 8px; border: 1px dashed #ccc;">
                <h4 style="color: #999;">Belum ada data fasilitas.</h4>
                <p style="color: #aaa;">Silakan tambahkan data baru melalui form di atas.</p>
            </div>
        <?php endif; ?>

    </div>
    <?php if ($totalPages > 1): ?>
        <div class="pagination">
            <?php if ($currentPage > 1): ?>
                <a href="?page=fasilitas&p=<?= ($currentPage - 1) . ($searchKeyword ? '&keyword='.urlencode($searchKeyword) : '') . ($currentSortBy ? '&sort='.$currentSortBy.'&order='.$currentSortOrder : '') ?>" class="page-link page-arrow">&laquo;</a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="?page=fasilitas&p=<?= $i . ($searchKeyword ? '&keyword='.urlencode($searchKeyword) : '') . ($currentSortBy ? '&sort='.$currentSortBy.'&order='.$currentSortOrder : '') ?>" 
                   class="page-link page-num <?= ($i == $currentPage) ? 'active' : '' ?>">
                    <?= $i ?>
                </a>
            <?php endfor; ?>

            <?php if ($currentPage < $totalPages): ?>
                <a href="?page=fasilitas&p=<?= ($currentPage + 1) . ($searchKeyword ? '&keyword='.urlencode($searchKeyword) : '') . ($currentSortBy ? '&sort='.$currentSortBy.'&order='.$currentSortOrder : '') ?>" class="page-link page-arrow">&raquo;</a>
            <?php endif; ?>
        </div>
    <?php endif; ?>

</div>