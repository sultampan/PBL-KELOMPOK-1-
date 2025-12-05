<div id="member-list-container">
    <div class="card">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:15px;">
            <h3>Daftar Member</h3>
            
            <div style="font-size: 13px;">
                Urutkan: 
                <?php
                global $webUploadDir, $webThumbDir, $serverUploadDir, $serverThumbDir;
                if (isset($paginationData) && is_array($paginationData)) extract($paginationData);
                else {
                    $currentPage = 1; $totalPages = 1; $searchKeyword = null;
                    $limit = 6; $currentSortBy = 'id_member'; $currentSortOrder = 'ASC';
                }

                function getSortLink($column, $label, $currentSortBy, $currentSortOrder, $searchKeyword, $currentPage) {
                    $newOrder = ($currentSortBy === $column && $currentSortOrder === 'ASC') ? 'DESC' : 'ASC';
                    $style = ($currentSortBy === $column) ? 'font-weight:bold; color:#3498db;' : 'color:#555;';
                    $icon = ($currentSortBy === $column) ? ($currentSortOrder === 'ASC' ? '▲' : '▼') : '';
                    $qs = '?page=member&sort=' . $column . '&order=' . $newOrder . '&p=' . $currentPage;
                    if ($searchKeyword) $qs .= '&keyword=' . urlencode($searchKeyword);
                    return '<a href="' . $qs . '" style="text-decoration: none; margin-left:10px; ' . $style . '">' . $label . ' ' . $icon . '</a>';
                }

                if (!function_exists('fixUrl')) {
                    function fixUrl($url) {
                        $url = trim($url);
                        if (empty($url)) return '';
                        if (strpos($url, 'http://') === false && strpos($url, 'https://') === false) return 'https://' . $url;
                        return $url;
                    }
                }

                echo getSortLink('nama_member', 'Nama', $currentSortBy, $currentSortOrder, $searchKeyword, $currentPage);
                echo getSortLink('nidn', 'NIDN', $currentSortBy, $currentSortOrder, $searchKeyword, $currentPage);
                ?>
            </div>
        </div>

        <div class="member-grid">
            <?php if ($list): ?>
                <?php foreach ($list as $row): 
                     $dataJson = htmlspecialchars(json_encode($row), ENT_QUOTES, 'UTF-8');

                     // --- LOGIKA PEMBEDA HEAD OF LAB ---
                     $isHead = ($row['jabatan'] === 'Head of Laboratory'); // Cek jabatan

                     // 1. Tentukan Teks Badge
                     $badgeLabel = $isHead ? 'HEAD LAB' : 'MEMBER'; 

                     // 2. Tentukan Class CSS Badge (Ini yang bikin warnanya beda)
                     // Kalau Head -> pakai .role-badge-head
                     // Kalau Member -> pakai .role-badge biasa
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
                                    $defaultImg = 'https://ui-avatars.com/api/?name=' . urlencode($row['nama_member']) . '&background=random&color=fff&size=128';
                                    $imgSrc = $defaultImg; 
                                    if ($row['gambar']) {
                                        $thumb = pathinfo($row['gambar'], PATHINFO_FILENAME) . '-thumb.' . pathinfo($row['gambar'], PATHINFO_EXTENSION);
                                        if (is_file($serverThumbDir . $thumb)) {
                                            $imgSrc = $webThumbDir . $thumb;
                                        } elseif (is_file($serverUploadDir . $row['gambar'])) {
                                            $imgSrc = $webUploadDir . $row['gambar'];
                                        }
                                        if ($imgSrc !== $defaultImg) $imgSrc .= '?' . time();
                                    }
                                ?>
                                <img src="<?= $imgSrc ?>" alt="<?= htmlspecialchars($row['nama_member']) ?>">
                            </div>

                            <div class="mit-contact">
                                <?php if($row['google_scholar']): ?>
                                    <div class="mit-email-row">
                                        <span class="mit-icon">GS</span>
                                        <a href="<?= fixUrl($row['google_scholar']) ?>" target="_blank">Google Scholar</a>
                                    </div>
                                <?php endif; ?>
                                <?php if($row['sinta']): ?>
                                    <div class="mit-email-row">
                                        <span class="mit-icon">ST</span>
                                        <a href="<?= fixUrl($row['sinta']) ?>" target="_blank">Sinta ID</a>
                                    </div>
                                <?php endif; ?>
                                <?php if($row['orcid']): ?>
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
                            <a type="button" onclick="deleteMember(<?= (int)$row['id_member'] ?>)" class="btn-card btn-card-delete">Hapus</a>
                        </div>

                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div style="width: 100%; text-align:center; padding: 20px; color:#777;">Belum ada data member.</div>
            <?php endif; ?>
        </div>
        
        <?php if ($totalPages > 1): ?>
            <div class="pagination" style="margin-top: 20px; text-align: center;">
                <?php for ($i = 1; $i <= $totalPages; $i++): 
                    $qs = '?page=member&p=' . $i;
                    if ($searchKeyword) $qs .= '&keyword=' . urlencode($searchKeyword);
                    if ($currentSortBy) $qs .= '&sort=' . $currentSortBy . '&order=' . $currentSortOrder;
                ?>
                    <a href="<?= $qs ?>" class="page-link <?= ($i == $currentPage ? 'active' : '') ?>"><?= $i ?></a>
                <?php endfor; ?>
            </div>
        <?php endif; ?>
    </div>
</div>