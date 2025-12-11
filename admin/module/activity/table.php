<div id="activity-list-container">
    <div class="card">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:15px;">
            <h3>Daftar Activity</h3>

            <div class="search-box">
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

                <input type="text" id="searchActivityInput" class="form-control"
                    placeholder="Cari Judul atau Deskripsi..."
                    value="<?= htmlspecialchars($searchKeyword ?? '') ?>"
                    style="font-size: 13px; padding: 6px 10px; width: 250px;">

                <button type="button" onclick="searchActivity()" class="btn-search">
                    <i class="fas fa-search"></i> <span>Cari</span>
                </button>

                <?php if($searchKeyword): ?>
                    <button type="button" onclick="resetSearchActivity()" class="btn-reset" title="Hapus Pencarian">
                        <i class="fas fa-times"></i>
                    </button>
                <?php endif; ?>
            </div>
        </div>

        <div class="member-grid">
            <?php if ($list): ?>
                <?php foreach ($list as $row): ?>
                    <div class="mit-card">
                        <div class="mit-card-role">
                            <span class="role-badge">ACTIVITY</span>
                            <span><?= date('d M Y', strtotime($row['tanggal_kegiatan'])) ?></span>
                        </div>

                        <h2 class="mit-card-name">
                            <?= htmlspecialchars($row['judul']) ?>
                        </h2>

                        <div class="mit-card-content">
                            <div class="mit-activity-image">
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
                                    <div style="display: flex; flex-direction: column; justify-content: center; align-items: center; height: 100%; background-color: #f0f0f0; color: #999;">
                                        <i class="fas fa-image" style="font-size: 48px; margin-bottom: 10px;"></i>
                                        <span style="font-size: 14px;">Tidak ada gambar</span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="mit-bio">
                            <?= htmlspecialchars($row['deskripsi']) ?>
                        </div>

                        <div class="activity-members" style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #e0e0e0;">
                            <div style="font-weight: 600; font-size: 13px; color: #666; margin-bottom: 10px;">
                                <i class="fas fa-users" style="margin-right: 5px;"></i>Member yang Berpartisipasi:
                            </div>
                            <div style="display: flex; flex-wrap: wrap; gap: 8px;">
                                <?php 
                                if (!empty($row['members']) && is_array($row['members'])) {
                                    foreach ($row['members'] as $member): 
                                        $memberName = is_array($member) ? ($member['nama_member'] ?? 'Unknown') : $member;
                                ?>
                                    <span class="member-tag" style="display: inline-flex; align-items: center; padding: 6px 12px; background-color: #f0f7f8; color: #01B5B8; border-radius: 20px; font-size: 12px; font-weight: 500;">
                                        <i class="fas fa-user" style="font-size: 10px; margin-right: 5px;"></i>
                                        <?= htmlspecialchars($memberName) ?>
                                    </span>
                                <?php 
                                    endforeach;
                                } else {
                                    echo '<span style="font-size: 12px; color: #999; font-style: italic;">Tidak ada.</span>';
                                }
                                ?>
                            </div>
                        </div>

                        <div class="card-action-buttons">
                            <a href="?page=activity&edit=<?= $row['id_activity'] ?>" class="btn-card btn-card-edit">Edit</a>
                            <a onclick="deleteActivity(<?= (int)$row['id_activity'] ?>)" class="btn-card btn-card-delete">Hapus</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div style="width:100%; text-align:center; padding:20px; color:#777;">
                    Belum ada activity.
                </div>
            <?php endif; ?>
        </div>

        <?php if ($totalPages > 1): ?>
            <div class="pagination" style="margin-top: 20px; text-align: center;">

                <?php if ($currentPage > 1): ?>
                    <a href="<?= buildPageUrl($currentPage - 1, $searchKeyword, $currentSortBy, $currentSortOrder) ?>" class="page-link page-arrow" title="Sebelumnya">&lsaquo;</a>
                <?php else: ?>
                    <span class="page-link page-arrow disabled">&lsaquo;</span>
                <?php endif; ?>

                <?php if ($currentPage > 1): ?>
                    <a href="<?= buildPageUrl(1, $searchKeyword, $currentSortBy, $currentSortOrder) ?>" class="page-link page-arrow" title="Ke Awal">&laquo;</a>
                <?php else: ?>
                    <span class="page-link page-arrow disabled">&laquo;</span>
                <?php endif; ?>

                <?php
                $max_buttons = 5;
                $half = floor($max_buttons / 2);
                $start_page = $currentPage - $half;
                $end_page   = $currentPage + $half;

                if ($start_page < 1) { $start_page = 1; $end_page = $start_page + $max_buttons - 1; }
                if ($end_page > $totalPages) { $end_page = $totalPages; $start_page = $end_page - $max_buttons + 1; if ($start_page < 1) $start_page = 1; }

                for ($i = $start_page; $i <= $end_page; $i++):
                    $isActive = ($i == $currentPage) ? 'active' : '';
                ?>
                    <a href="<?= buildPageUrl($i, $searchKeyword, $currentSortBy, $currentSortOrder) ?>" class="page-link page-num <?= $isActive ?>"><?= $i ?></a>
                <?php endfor; ?>

                <?php if ($currentPage < $totalPages): ?>
                    <a href="<?= buildPageUrl($totalPages, $searchKeyword, $currentSortBy, $currentSortOrder) ?>" class="page-link page-arrow" title="Ke Akhir">&raquo;</a>
                <?php else: ?>
                    <span class="page-link page-arrow disabled">&raquo;</span>
                <?php endif; ?>

                <?php if ($currentPage < $totalPages): ?>
                    <a href="<?= buildPageUrl($currentPage + 1, $searchKeyword, $currentSortBy, $currentSortOrder) ?>" class="page-link page-arrow" title="Berikutnya">&rsaquo;</a>
                <?php else: ?>
                    <span class="page-link page-arrow disabled">&rsaquo;</span>
                <?php endif; ?>

            </div>
        <?php endif; ?>

    </div>
</div>