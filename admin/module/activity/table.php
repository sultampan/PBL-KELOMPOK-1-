<div id="activity-list-container">
    <div class="card-header-section" style="padding: 0 0 20px 0; border-bottom: none;">
        <div class="header-title">Daftar Activity</div>
        <div class="search-section">
            <input type="text" id="searchActivityInput" class="search-input" 
                   placeholder="Cari Activity..." 
                   value="<?= htmlspecialchars($searchKeyword ?? '') ?>">
            <button type="button" onclick="searchActivity()" class="btn-search">
                <i class="fas fa-search"></i> Cari
            </button>
        </div>
    </div>

    <div class="activity-wrapper">
        <?php if ($list): ?>
            <?php 
            // 1. Grouping Data
            $grouped = [];
            foreach ($list as $item) {
                $cat = $item['kategori'] ?: 'Uncategorized';
                $grouped[$cat][] = $item;
            }

            // 2. Loop Per Kategori
            foreach ($grouped as $categoryName => $items): 
                $catId = md5($categoryName);
            ?>
                <div class="activity-category-section mb-5" data-category="<?= htmlspecialchars($categoryName) ?>">
                    
                    <h3 class="category-title"><?= htmlspecialchars($categoryName) ?></h3>
                    
                    <div class="activity-grid paginated-grid" id="grid-<?= $catId ?>" data-current-page="1" data-items-per-page="6">
                        
                        <?php foreach ($items as $row): 
                             // A. Setup Gambar
                             $imgSrc = '';
                             if (!empty($row['gambar']) && file_exists($serverUploadDir . $row['gambar'])) {
                                 $imgSrc = $webUploadDir . $row['gambar'];
                             }

                             // B. Setup Tanggal
                             $displayDate = '-';
                             if (!empty($row['tanggal_kegiatan'])) {
                                 $timestamp = strtotime($row['tanggal_kegiatan']);
                                 if ($timestamp) $displayDate = date('d M Y', $timestamp);
                             }

                             // C. [BARU] AMBIL DATA MEMBER
                             // Pastikan fungsi ini ada di model.php
                             $members = [];
                             if (function_exists('getActivityMembers')) {
                                 $members = getActivityMembers($pdo, $row['id_activity']);
                             }
                        ?>
                            <div class="activity-item grid-item"> 
                                
                                <div class="activity-item-header">
                                    <span class="badge-activity"><?= htmlspecialchars($row['kategori']) ?></span>
                                    <span class="activity-date-small"><?= $displayDate ?></span>
                                </div>

                                <h4 class="activity-item-title" title="<?= htmlspecialchars($row['judul']) ?>">
                                    <?= htmlspecialchars($row['judul']) ?>
                                </h4>

                                <div class="activity-item-image">
                                    <?php if ($imgSrc): ?>
                                        <img src="<?= $imgSrc ?>" alt="Activity Image">
                                    <?php else: ?>
                                        <div class="no-image"><i class="fas fa-image"></i></div>
                                    <?php endif; ?>
                                </div>

                                <div class="activity-item-description">
                                    <?= strip_tags(html_entity_decode($row['deskripsi'])) ?>
                                </div>

                                <div class="activity-item-members">
                                    <div class="members-label">
                                        <i class="fas fa-users"></i> Partisipan:
                                    </div>
                                    <div class="members-tags">
                                        <?php if (!empty($members)): ?>
                                            <?php foreach ($members as $m): ?>
                                                <span class="member-tag">
                                                    <i class="fas fa-user-circle"></i> 
                                                    <?= htmlspecialchars($m['nama_member']) ?>
                                                </span>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <span class="no-members-text">- Tidak ada member -</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="activity-item-actions">
                                    <a href="?page=activity&edit=<?= $row['id_activity'] ?>" class="btn-action btn-edit">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <button type="button" onclick="deleteActivity(<?= $row['id_activity'] ?>)" class="btn-action btn-delete">
                                        <i class="fas fa-trash"></i> Hapus
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        
                    </div>

                    <div class="pagination-container" id="pagination-<?= $catId ?>"></div>

                </div>
            <?php endforeach; ?>

        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-folder-open"></i>
                <p>Belum ada data activity.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
    if(typeof initActivityPagination === 'function') {
        initActivityPagination();
    }
</script>