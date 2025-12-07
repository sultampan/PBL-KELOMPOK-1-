<div id="member-list-container">
    <div class="card">

        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:15px;">
            <h3>Daftar Member</h3>

            <div class="search-box">
                <?php
                // --- DEFINISI VARIABEL & FUNGSI HELPER (JANGAN DIHAPUS) ---
                global $webUploadDir, $webThumbDir, $serverUploadDir, $serverThumbDir;
                if (isset($paginationData) && is_array($paginationData)) extract($paginationData);
                else {
                    $currentPage = 1;
                    $totalPages = 1;
                    $searchKeyword = null;
                    $limit = 6;
                    $currentSortBy = 'id_member';
                    $currentSortOrder = 'ASC';
                }

                // Fungsi Build URL Paginasi
                if (!function_exists('buildPageUrl')) {
                    function buildPageUrl($p, $searchKeyword, $currentSortBy, $currentSortOrder)
                    {
                        $qs = '?page=member&p=' . $p;
                        if ($searchKeyword) $qs .= '&keyword=' . urlencode($searchKeyword);
                        if ($currentSortBy) $qs .= '&sort=' . $currentSortBy . '&order=' . $currentSortOrder;
                        return $qs;
                    }
                }

                // Fungsi Fix URL (PENTING! JANGAN HILANG)
                if (!function_exists('fixUrl')) {
                    function fixUrl($url)
                    {
                        $url = trim($url);
                        if (empty($url)) return '';
                        if (strpos($url, 'http://') === false && strpos($url, 'https://') === false) return 'https://' . $url;
                        return $url;
                    }
                }
                ?>

                <input type="text" id="searchMemberInput" class="form-control"
                    placeholder="Cari Nama, NIDN, atau Deskripsi..."
                    value="<?= htmlspecialchars($searchKeyword ?? '') ?>"
                    style="font-size: 13px; padding: 6px 10px; width: 250px;">

                <button type="button" onclick="searchMember()" class="btn-search">
                    <i class="fas fa-search"></i> <span>Cari</span>
                </button>

                <?php if($searchKeyword): ?>
                    <button type="button" onclick="resetSearchMember()" class="btn-reset" title="Hapus Pencarian">
                        <i class="fas fa-times"></i>
                    </button>
                <?php endif; ?>
            </div>
        </div>

        <div class="member-grid">
            <?php if ($list): ?>
                <?php foreach ($list as $row):
                    $dataJson = htmlspecialchars(json_encode($row), ENT_QUOTES, 'UTF-8');
                    $isHead = ($row['jabatan'] === 'Head of Laboratory');
                    $badgeLabel = $isHead ? 'HEAD LAB' : 'MEMBER';
                    $badgeClass = $isHead ? 'role-badge-head' : 'role-badge';
                ?>
                    <div class="mit-card">
                        <div class="mit-card-role">
                            <span class="<?= $badgeClass ?>"><?= $badgeLabel ?></span>
                            <span><?= htmlspecialchars($row['nidn']) ?></span>
                        </div>

                        <h2 class="mit-card-name">
                            <a href="javascript:void(0)" onclick="showMemberDetail(<?= $dataJson ?>)">
                                <?= htmlspecialchars($row['nama_member']) ?>
                            </a>
                        </h2>

                        <div class="mit-card-content">
                            <div class="mit-avatar">
                                <?php
                                $defaultImg = 'https://ui-avatars.com/api/?name=' . urlencode($row['nama_member']) . '&background=random&color=fff&size=128&length=1';
                                $imgSrc = $defaultImg;
                                if ($row['gambar']) {
                                    $thumb = pathinfo($row['gambar'], PATHINFO_FILENAME) . '-thumb.' . pathinfo($row['gambar'], PATHINFO_EXTENSION);
                                    if (is_file($serverThumbDir . $thumb)) $imgSrc = $webThumbDir . $thumb;
                                    elseif (is_file($serverUploadDir . $row['gambar'])) $imgSrc = $webUploadDir . $row['gambar'];

                                    if ($imgSrc !== $defaultImg) $imgSrc .= '?' . time();
                                }
                                ?>
                                <img src="<?= $imgSrc ?>" alt="<?= htmlspecialchars($row['nama_member']) ?>">
                            </div>

                            <div class="mit-contact">
                                <?php if ($row['google_scholar']): ?>
                                    <div class="mit-email-row">
                                        <span class="mit-icon">GS</span>
                                        <a href="<?= fixUrl($row['google_scholar']) ?>" target="_blank">Google Scholar</a>
                                    </div>
                                <?php endif; ?>
                                <?php if ($row['sinta']): ?>
                                    <div class="mit-email-row">
                                        <span class="mit-icon">ST</span>
                                        <a href="<?= fixUrl($row['sinta']) ?>" target="_blank">Sinta ID</a>
                                    </div>
                                <?php endif; ?>
                                <?php if ($row['orcid']): ?>
                                    <div class="mit-email-row">
                                        <span class="mit-icon">OR</span>
                                        <a href="<?= fixUrl($row['orcid']) ?>" target="_blank">ORCID</a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="mit-bio">
                            <?= htmlspecialchars($row['deskripsi']) ?>
                        </div>

                        <div class="card-action-buttons">
                            <a href="?page=member&edit=<?= $row['id_member'] ?>" class="btn-card btn-card-edit">Edit</a>
                            <a onclick="deleteMember(<?= (int)$row['id_member'] ?>)" class="btn-card btn-card-delete">Hapus</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div style="width:100%; text-align:center; padding:20px; color:#777;">
                    Belum ada data member.
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