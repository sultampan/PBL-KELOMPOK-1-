<div id="product-list-container">
    
    <div class="card">
        
        <div class="toolbar-header">
            
            <h3 class="header-title">Daftar Produk</h3>

            <div class="search-form">
                <div class="search-group">
                    <input type="text" 
                           id="searchProductInput" 
                           class="search-input-fasilitas" 
                           placeholder="Cari data produk..." 
                           value="<?= htmlspecialchars($searchKeyword ?? '') ?>"
                           autocomplete="off">
                </div>
            </div>

        </div>

        <?php
        // ... (KODE PHP KE BAWAH TETAP SAMA, JANGAN DIUBAH) ...
        global $webUploadDir, $webThumbDir, $serverUploadDir, $serverThumbDir;
        
        if (isset($paginationData) && is_array($paginationData)) extract($paginationData);
        else {
            $currentPage = 1; $totalPages = 1; $searchKeyword = null;
            $limit = 6; $currentSortBy = 'id_produk'; $currentSortOrder = 'DESC';
        }

        if (!function_exists('buildProductUrl')) {
            function buildProductUrl($p, $keyword, $sort, $order) {
                $qs = '?page=produk&p=' . $p;
                if ($keyword) $qs .= '&keyword=' . urlencode($keyword);
                if ($sort) $qs .= '&sort=' . $sort . '&order=' . $order;
                return $qs;
            }
        }
        ?>

        <div class="member-grid">
            <?php if (!empty($list)): ?>
                <?php foreach ($list as $row): ?>
                    <div class="mit-card">
                        
                        <div class="mit-card-role">
                            <span class="role-badge">PRODUK</span> 
                            <span style="flex-grow: 1; text-align: right;">
                                <?php if (!empty($row['link_produk'])): ?>
                                    <?php 
                                    $rawUrl = htmlspecialchars($row['link_produk']);
                                    $finalUrl = preg_match('#^https?://#i', $rawUrl) ? $rawUrl : 'https://' . $rawUrl;
                                    ?>
                                    <a href="<?= $finalUrl ?>" target="_blank" class="btn-link-produk">
                                        <i class="fas fa-external-link-alt" style="margin-right: 4px;"></i> LIHAT
                                    </a>
                                <?php else: ?>
                                    <span style="color:#aaa; font-style:italic; font-size:10px;">No Link</span>
                                <?php endif; ?>
                            </span>
                        </div>

                        <h2 class="mit-card-name"><?= htmlspecialchars($row['nama']) ?></h2>

                        <div class="mit-card-content">
                            <div class="mit-activity-image">
                                <?php
                                $imgSrc = '';
                                $hasImage = false;
                                
                                if (!empty($row['gambar'])) {
                                    $thumb = pathinfo($row['gambar'], PATHINFO_FILENAME) . '-thumb.' . pathinfo($row['gambar'], PATHINFO_EXTENSION);
                                    
                                    if (is_file($serverThumbDir . $thumb)) {
                                        $imgSrc = $webThumbDir . $thumb;
                                        $hasImage = true;
                                    } elseif (is_file($serverUploadDir . $row['gambar'])) {
                                        $imgSrc = $webUploadDir . $row['gambar'];
                                        $hasImage = true;
                                    }
                                    if ($hasImage) $imgSrc .= '?' . time();
                                }
                                ?>
                                <?php if ($hasImage): ?>
                                    <img src="<?= $imgSrc ?>" alt="<?= htmlspecialchars($row['nama']) ?>">
                                <?php else: ?>
                                    <div class="no-image">
                                        <i class="fas fa-box-open"></i>
                                        <span>No Image</span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="mit-bio">
                            <?= nl2br(htmlspecialchars($row['deskripsi'])) ?>
                        </div>

                        <div class="activity-members">
                            <div style="font-weight: 600; font-size: 12px; color: #7f8c8d; margin-bottom: 8px;">
                                <i class="fas fa-code-branch" style="margin-right: 4px;"></i>Tim:
                            </div>
                            
                            <div class="member-scroll-box"> 
                                <?php if (!empty($row['team']) && is_array($row['team'])): ?>
                                    <?php foreach ($row['team'] as $member): ?>
                                        <span class="member-tag">
                                            <?= htmlspecialchars($member['nama_member']) ?>
                                            <small style="opacity:0.7; margin-left:3px;">(<?= htmlspecialchars($member['role']) ?>)</small>
                                        </span>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <span style="font-size: 11px; color: #ccc;">-</span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="card-action-buttons">
                            <a href="?page=produk&edit=<?= $row['id_produk'] ?>" class="btn-card btn-card-edit">Edit</a>
                            <a href="javascript:void(0)" onclick="deleteProduct(<?= (int)$row['id_produk'] ?>)" class="btn-card btn-card-delete">Hapus</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div style="grid-column: 1 / -1; text-align: center; padding: 40px; color: #777;">
                    <i class="fas fa-search" style="font-size: 40px; margin-bottom: 10px; opacity: 0.3;"></i><br>
                    Produk "<b><?= htmlspecialchars($searchKeyword) ?></b>" tidak ditemukan.
                </div>
            <?php endif; ?>
        </div>

        <?php if ($totalPages > 1): ?>
            <div class="pagination">
                <?php if ($currentPage > 1): ?>
                    <a href="<?= buildProductUrl($currentPage - 1, $searchKeyword, $currentSortBy, $currentSortOrder) ?>" class="page-link page-arrow">&lsaquo;</a>
                <?php else: ?>
                    <span class="page-link page-arrow disabled">&lsaquo;</span>
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
                    <a href="<?= buildProductUrl($i, $searchKeyword, $currentSortBy, $currentSortOrder) ?>" class="page-link page-num <?= $isActive ?>"><?= $i ?></a>
                <?php endfor; ?>

                <?php if ($currentPage < $totalPages): ?>
                    <a href="<?= buildProductUrl($currentPage + 1, $searchKeyword, $currentSortBy, $currentSortOrder) ?>" class="page-link page-arrow">&rsaquo;</a>
                <?php else: ?>
                    <span class="page-link page-arrow disabled">&rsaquo;</span>
                <?php endif; ?>
            </div>
        <?php endif; ?>

    </div>
</div>