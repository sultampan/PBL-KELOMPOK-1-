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
                                $defaultImg = 'https://via.placeholder.com/400x250/e0e0e0/666666?text=No+Image';
                                $imgSrc = $defaultImg;
                                
                                if (!empty($row['gambar'])) {
                                    $thumb = pathinfo($row['gambar'], PATHINFO_FILENAME) . '-thumb.' . pathinfo($row['gambar'], PATHINFO_EXTENSION);
                                    if (is_file($serverThumbDir . $thumb)) $imgSrc = $webThumbDir . $thumb;
                                    elseif (is_file($serverUploadDir . $row['gambar'])) $imgSrc = $webUploadDir . $row['gambar'];

                                    if ($imgSrc !== $defaultImg) $imgSrc .= '?' . time();
                                }
                                ?>
                                <img src="<?= $imgSrc ?>" alt="<?= htmlspecialchars($row['judul']) ?>">
                            </div>
                        </div>

                        <div class="mit-bio">
                            <?= htmlspecialchars($row['deskripsi']) ?>
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

<style>
/* Khusus untuk gambar activity - bentuk persegi panjang */
.mit-activity-image {
    width: 100%;
    height: 180px;
    overflow: hidden;
    border-radius: 8px;
    background-color: #f5f5f5;
    margin-bottom: 12px;
}

.mit-activity-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    object-position: center;
}

/* Pastikan card tidak overflow dan lebih besar */
#activity-list-container .mit-card {
    overflow: visible;
    padding: 20px !important;
    min-height: 400px;
    display: block;
    position: relative;
}

#activity-list-container .mit-card-role {
    margin-bottom: 0 !important;
    padding-bottom: 10px !important;
    display: flex;
    justify-content: space-between;
    align-items: center;
    width: 100%;
    height: auto;
    min-height: 30px;
}

#activity-list-container .mit-card-name {
    word-wrap: break-word;
    word-break: break-word;
    overflow-wrap: break-word;
    hyphens: auto;
    width: 100%;
    max-width: 100%;
    font-size: 1.2em !important;
    padding: 0 !important;
    margin: 20px 0 15px 0 !important;
    line-height: 1.5 !important;
    display: block;
    clear: both;
}

#activity-list-container .mit-card-content {
    display: block;
    width: 100%;
    clear: both;
}

#activity-list-container .mit-bio {
    word-wrap: break-word;
    word-break: break-word;
    overflow-wrap: break-word;
    max-width: 100%;
    padding: 15px 0 !important;
    min-height: 60px;
    display: block;
    clear: both;
}

#activity-list-container .card-action-buttons {
    margin-top: 15px !important;
    display: block;
    clear: both;
}
</style>

<script>
function searchActivity() {
    const keyword = document.getElementById('searchActivityInput').value;
    window.location.href = '?page=activity&keyword=' + encodeURIComponent(keyword);
}

function resetSearchActivity() {
    window.location.href = '?page=activity';
}

document.getElementById('searchActivityInput')?.addEventListener('keypress', function(e) {
    if (e.key === 'Enter') searchActivity();
});

function deleteActivity(id) {
    if (confirm('Yakin ingin menghapus activity ini?')) {
        window.location.href = '?page=activity&delete=' + id;
    }
}
</script>