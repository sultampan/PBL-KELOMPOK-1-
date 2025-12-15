<div id="fasilitas-list-container">
    
    <div class="fasilitas-grid-container">
        
        <div class="toolbar-header">
            <h3 class="header-title">Daftar Fasilitas</h3>

            <div class="search-form">
                <?php
                    // --- DEFINISI VARIABEL PAGINATION ---
                    // Pastikan variabel ini tersedia dari table-load.php
                    if (isset($paginationData) && is_array($paginationData)) extract($paginationData);
                    else {
                        $currentPage = 1; $totalPages = 1; $searchKeyword = null; $limit = 6;
                    }

                    // FUNGSI HELPER PEMBUAT URL (Khusus Fasilitas)
                    if (!function_exists('buildFasilitasUrl')) {
                        function buildFasilitasUrl($p, $keyword) {
                            $qs = '?page=fasilitas&p=' . $p;
                            if ($keyword) $qs .= '&keyword=' . urlencode($keyword);
                            return $qs;
                        }
                    }
                ?>
                
                <div class="search-group">
                    <input type="text" id="searchFasilitasInput" class="search-input" 
                           placeholder="Cari fasilitas..." 
                           value="<?= htmlspecialchars($searchKeyword ?? '') ?>">
                    <button type="button" onclick="searchFasilitas()" class="btn-cari">
                        <i class="fas fa-search"></i> Cari
                    </button>
                    <?php if($searchKeyword): ?>
                        <button type="button" onclick="window.location.href='?page=fasilitas'" 
                                class="btn-cari" style="background-color:#95a5a6; padding:0 15px;">
                            <i class="fas fa-times"></i>
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div class="fasilitas-grid">
            <?php if (!empty($list)): ?>
                <?php foreach ($list as $row): 
                    $imgSrc = 'assets/img/no-image.png'; 
                    // Path gambar (pastikan path ini sesuai struktur folder Anda)
                    // Menggunakan logic yang sama dengan Member untuk thumbnail
                    $uploadPath = '../public/uploads/fasilitas/';
                    $thumbPath  = '../public/uploads/thumb/fasilitas-thumb/';
                    
                    if (!empty($row['gambar'])) {
                        $thumbName = pathinfo($row['gambar'], PATHINFO_FILENAME) . '-thumb.' . pathinfo($row['gambar'], PATHINFO_EXTENSION);
                        
                        // Cek fisik file (PHP side check - optional, or just direct link)
                        // Disini kita asumsi file ada agar performa cepat
                        $imgSrc = $uploadPath . $row['gambar'];
                        
                        // Jika ingin pakai thumb (jika file thumb pasti ada)
                        // $imgSrc = $thumbPath . $thumbName; 
                    }
                ?>
                <div class="fasilitas-card">
                    <div class="fasilitas-img-wrapper">
                        <?php if(!empty($row['gambar'])): ?>
                            <img src="<?= $imgSrc ?>" alt="<?= htmlspecialchars($row['judul']) ?>" loading="lazy">
                        <?php else: ?>
                            <div class="no-image-placeholder">No Image</div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="fasilitas-content">
                        <h4 class="fasilitas-title"><?= htmlspecialchars($row['judul']) ?></h4>
                        <div class="fasilitas-desc">
                            <?= nl2br(htmlspecialchars($row['deskripsi'])) ?>
                        </div>
                    </div>
                    
                    <div class="card-action-buttons">
                        <a href="?page=fasilitas&edit=<?= $row['id_fasilitas'] ?>" class="btn-card btn-card-edit">Edit</a>
                        <a href="javascript:void(0)" onclick="deleteFasilitas(<?= $row['id_fasilitas'] ?>)" class="btn-card btn-card-delete">Hapus</a>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div style="grid-column: 1 / -1; text-align: center; padding: 40px; color: #777;">
                    <i class="fas fa-box-open" style="font-size: 40px; margin-bottom: 10px; opacity: 0.5;"></i><br>
                    Data fasilitas tidak ditemukan.
                </div>
            <?php endif; ?>
        </div>

        <?php if ($totalPages > 1): ?>
            <div class="pagination">

                <?php if ($currentPage > 1): ?>
                    <a href="<?= buildFasilitasUrl($currentPage - 1, $searchKeyword) ?>" class="page-link page-arrow" title="Sebelumnya">&lsaquo;</a>
                <?php else: ?>
                    <span class="page-link page-arrow disabled">&lsaquo;</span>
                <?php endif; ?>

                <?php if ($currentPage > 1): ?>
                    <a href="<?= buildFasilitasUrl(1, $searchKeyword) ?>" class="page-link page-arrow" title="Ke Awal">&laquo;</a>
                <?php else: ?>
                    <span class="page-link page-arrow disabled">&laquo;</span>
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
                    <a href="<?= buildFasilitasUrl($i, $searchKeyword) ?>" class="page-link page-num <?= $isActive ?>"><?= $i ?></a>
                <?php endfor; ?>

                <?php if ($currentPage < $totalPages): ?>
                    <a href="<?= buildFasilitasUrl($totalPages, $searchKeyword) ?>" class="page-link page-arrow" title="Ke Akhir">&raquo;</a>
                <?php else: ?>
                    <span class="page-link page-arrow disabled">&raquo;</span>
                <?php endif; ?>

                <?php if ($currentPage < $totalPages): ?>
                    <a href="<?= buildFasilitasUrl($currentPage + 1, $searchKeyword) ?>" class="page-link page-arrow" title="Berikutnya">&rsaquo;</a>
                <?php else: ?>
                    <span class="page-link page-arrow disabled">&rsaquo;</span>
                <?php endif; ?>

            </div>
        <?php endif; ?>

    </div>
</div>