<div id="activity-list-container">
    <div class="activity-main-card">
        <!-- HEADER SECTION -->
        <div class="card-header-section">
            <h2 class="header-title">Daftar Activity</h2>

            <div class="search-section">
                <?php
                global $webUploadDir, $webThumbDir, $serverUploadDir, $serverThumbDir;
                
                if (isset($paginationData) && is_array($paginationData)) extract($paginationData);
                else {
                    $currentPage = 1; $totalPages = 1; $searchKeyword = null;
                    $limit = 6; $currentSortBy = 'id_activity'; $currentSortOrder = 'DESC';
                }

                if (!function_exists('buildPageUrl')) {
                    function buildPageUrl($p, $searchKeyword, $currentSortBy, $currentSortOrder) {
                        $qs = '?page=activity&p=' . $p;
                        if ($searchKeyword) $qs .= '&keyword=' . urlencode($searchKeyword);
                        if ($currentSortBy) $qs .= '&sort=' . $currentSortBy . '&order=' . $currentSortOrder;
                        return $qs;
                    }
                }
                ?>

                <input type="text" id="searchActivityInput" class="search-input"
                    placeholder="Cari Judul atau Deskripsi..."
                    value="<?= htmlspecialchars($searchKeyword ?? '') ?>">

                <button type="button" onclick="searchActivity()" class="btn-search">
                    <i class="fas fa-search"></i> <span>Cari</span>
                </button>

                <?php if($searchKeyword): ?>
                    <button type="button" onclick="resetSearchActivity()" class="btn-search" title="Hapus Pencarian" style="background-color: #e74c3c;">
                        <i class="fas fa-times"></i>
                    </button>
                <?php endif; ?>
            </div>
        </div>

        <!-- CONTENT AREA -->
        <div class="activity-content">
            <?php if ($list): ?>
                <div class="activity-grid">
                    <?php foreach ($list as $row): ?>
                        <div class="activity-item">
                            <!-- HEADER -->
                            <div class="activity-item-header">
                                <span class="badge-activity">ACTIVITY</span>
                                <span class="activity-date-small"><?= date('d M Y', strtotime($row['tanggal_kegiatan'])) ?></span>
                            </div>

                            <!-- TITLE -->
                            <h3 class="activity-item-title">
                                <?= htmlspecialchars($row['judul']) ?>
                            </h3>

                            <!-- IMAGE -->
                            <div class="activity-item-image">
                                <?php
                                $defaultImg = '';
                                $imgSrc = $defaultImg;
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
                                    <img src="<?= $imgSrc ?>" alt="<?= htmlspecialchars($row['judul']) ?>">
                                <?php else: ?>
                                    <div class="no-image">
                                        <i class="fas fa-image"></i>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- DESCRIPTION -->
                            <div class="activity-item-description">
                                <?= htmlspecialchars($row['deskripsi']) ?>
                            </div>

                            <!-- MEMBERS -->
                            <div class="activity-item-members">
                                <div class="members-label">
                                    <i class="fas fa-users"></i>
                                    Member yang Berpartisipasi:
                                </div>
                                <div class="members-tags">
                                    <?php 
                                    if (!empty($row['members']) && is_array($row['members'])) {
                                        foreach ($row['members'] as $member): 
                                            $memberName = is_array($member) ? ($member['nama_member'] ?? 'Unknown') : $member;
                                    ?>
                                        <span class="member-tag">
                                            <i class="fas fa-user"></i>
                                            <?= htmlspecialchars($memberName) ?>
                                        </span>
                                    <?php 
                                        endforeach;
                                    } else {
                                        echo '<span class="no-members-text">Tidak ada member.</span>';
                                    }
                                    ?>
                                </div>
                            </div>

                            <!-- ACTIONS -->
                            <div class="activity-item-actions">
                                <a href="?page=activity&edit=<?= $row['id_activity'] ?>" class="btn-action btn-edit">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <button type="button" onclick="deleteActivity(<?= (int)$row['id_activity'] ?>)" class="btn-action btn-delete">
                                    <i class="fas fa-trash"></i> Hapus
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-inbox"></i>
                    <p><?= $searchKeyword ? 'Tidak ada hasil untuk pencarian "' . htmlspecialchars($searchKeyword) . '"' : 'Belum ada activity.' ?></p>
                </div>
            <?php endif; ?>

            <!-- PAGINATION -->
            <?php if ($totalPages > 1): ?>
                <div class="pagination-container">
                    <!-- First Page -->
                    <button onclick="window.location.href='<?= buildPageUrl(1, $searchKeyword, $currentSortBy, $currentSortOrder) ?>'" 
                            <?= ($currentPage <= 1) ? 'disabled' : '' ?>>
                        &laquo;
                    </button>

                    <!-- Previous Page -->
                    <button onclick="window.location.href='<?= buildPageUrl($currentPage - 1, $searchKeyword, $currentSortBy, $currentSortOrder) ?>'" 
                            <?= ($currentPage <= 1) ? 'disabled' : '' ?>>
                        &lsaquo;
                    </button>

                    <?php
                    $max_buttons = 5;
                    $half = floor($max_buttons / 2);
                    $start_page = $currentPage - $half;
                    $end_page   = $currentPage + $half;

                    if ($start_page < 1) { 
                        $start_page = 1; 
                        $end_page = min($totalPages, $start_page + $max_buttons - 1); 
                    }
                    if ($end_page > $totalPages) { 
                        $end_page = $totalPages; 
                        $start_page = max(1, $end_page - $max_buttons + 1); 
                    }

                    for ($i = $start_page; $i <= $end_page; $i++):
                        $isActive = ($i == $currentPage) ? 'active' : '';
                    ?>
                        <button onclick="window.location.href='<?= buildPageUrl($i, $searchKeyword, $currentSortBy, $currentSortOrder) ?>'" 
                                class="<?= $isActive ?>">
                            <?= $i ?>
                        </button>
                    <?php endfor; ?>

                    <!-- Next Page -->
                    <button onclick="window.location.href='<?= buildPageUrl($currentPage + 1, $searchKeyword, $currentSortBy, $currentSortOrder) ?>'" 
                            <?= ($currentPage >= $totalPages) ? 'disabled' : '' ?>>
                        &rsaquo;
                    </button>

                    <!-- Last Page -->
                    <button onclick="window.location.href='<?= buildPageUrl($totalPages, $searchKeyword, $currentSortBy, $currentSortOrder) ?>'" 
                            <?= ($currentPage >= $totalPages) ? 'disabled' : '' ?>>
                        &raquo;
                    </button>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>