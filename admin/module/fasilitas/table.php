<div id="fasilitas-list-container">
    
    <?php
    global $webUploadDir, $webThumbDir, $serverUploadDir, $serverThumbDir;
    
    if (isset($paginationData) && is_array($paginationData)) {
        extract($paginationData);
    } else {
        $currentPage = 1; $totalPages = 1; $searchKeyword = null; $limit = 10;
        $list = [];
    }
    ?>

    <div class="fasilitas-wrapper">

        <div class="toolbar-header">
            <div class="header-title">
                Daftar Fasilitas
            </div>
            
            <div class="search-form">
                <div class="search-group">
                    <input type="text" id="searchFasilitasInput" class="search-input" 
                           placeholder="Cari Nama atau Deskripsi..." 
                           value="<?= htmlspecialchars($searchKeyword ?? '') ?>">
                    
                    <button type="button" onclick="searchFasilitas()" class="btn-cari">
                        <i class="fa fa-search"></i> Cari
                    </button>
                </div>
            </div>
        </div>

        <div class="fasilitas-grid-container">
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

                                        if (is_file($server_thumb_path)) {
                                            $image_path = $webThumbDir . $thumbnail_filename;
                                            $path_for_mtime = $server_thumb_path;
                                        } elseif (is_file($server_original_path)) {
                                            $image_path = $webUploadDir . $original_filename;
                                            $path_for_mtime = $server_original_path;
                                        }

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
                    <div style="grid-column: 1 / -1; text-align: center; padding: 50px; background: #f9f9f9; border-radius: 8px; border: 1px dashed #ccc;">
                        <h4 style="color: #999;">Belum ada data fasilitas.</h4>
                        <p style="color: #aaa;">Silakan tambahkan data baru melalui form di atas.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div> 
        </div> 
    <?php if ($totalPages > 1): ?>
        <div class="pagination">
            <?php 
                function getPageLink($pageNum, $keyword) {
                    $link = "?page=fasilitas&p=" . $pageNum;
                    if ($keyword) $link .= "&keyword=" . urlencode($keyword);
                    return $link;
                }
            ?>

            <?php if ($currentPage > 1): ?>
                <a href="<?= getPageLink($currentPage - 1, $searchKeyword) ?>" class="page-link page-arrow">&laquo;</a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="<?= getPageLink($i, $searchKeyword) ?>" 
                   class="page-link page-num <?= ($i == $currentPage) ? 'active' : '' ?>">
                    <?= $i ?>
                </a>
            <?php endfor; ?>

            <?php if ($currentPage < $totalPages): ?>
                <a href="<?= getPageLink($currentPage + 1, $searchKeyword) ?>" class="page-link page-arrow">&raquo;</a>
            <?php endif; ?>
        </div>
    <?php endif; ?>

</div>