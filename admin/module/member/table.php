<div id="member-list-container">
    <div class="card">
        <h3>Daftar Member</h3>

        <?php
        global $webUploadDir, $webThumbDir, $serverUploadDir, $serverThumbDir;
        if (isset($paginationData) && is_array($paginationData)) extract($paginationData);
        else {
            $currentPage = 1;
            $totalPages = 1;
            $searchKeyword = null;
            $limit = 10;
            $currentSortBy = 'id_member';
            $currentSortOrder = 'ASC';
        }

        // --- FUNGSI 1: SORTING HEADER ---
        function getSortLink($column, $currentSortBy, $currentSortOrder, $searchKeyword, $currentPage)
        {
            $newOrder = ($currentSortBy === $column && $currentSortOrder === 'ASC') ? 'DESC' : 'ASC';
            $icon = ($currentSortBy === $column) ? ($currentSortOrder === 'ASC' ? ' ▲' : ' ▼') : '';
            $qs = '?page=member&sort=' . $column . '&order=' . $newOrder . '&p=' . $currentPage;
            if ($searchKeyword) $qs .= '&keyword=' . urlencode($searchKeyword);
            return '<a href="' . $qs . '" style="text-decoration: none; color: inherit;">' . ucfirst(str_replace('_', ' ', $column)) . $icon . '</a>';
        }

        // --- FUNGSI 2: PERBAIKI LINK (Agar jadi Absolute URL) ---
        function fixUrl($url)
        {
            $url = trim($url);
            if (empty($url)) return '';

            // Jika tidak ada http:// atau https://, tambahkan https://
            if (strpos($url, 'http://') === false && strpos($url, 'https://') === false) {
                return 'https://' . $url;
            }
            return $url;
        }
        ?>

        <div style="overflow-x: auto;">
            <table class="table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th><?= getSortLink('nama_member', $currentSortBy, $currentSortOrder, $searchKeyword, $currentPage) ?></th>
                        <th>NIDN</th>
                        <th>Jabatan</th>

                        <th>Deskripsi</th>
                        <th>Tautan</th>
                        <th>Foto</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = (($currentPage - 1) * $limit) + 1;
                    if ($list):
                        foreach ($list as $row):
                    ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= htmlspecialchars($row['nama_member']) ?></td>
                                <td><?= htmlspecialchars($row['nidn']) ?></td>
                                <td><?= htmlspecialchars($row['jabatan']) ?></td>
                                <td>
                                    <?php
                                    // Kita potong biar tabel tidak kepanjangan (maksimal 50 huruf)
                                    $deskripsi = htmlspecialchars($row['deskripsi']);
                                    if (strlen($deskripsi) > 50) {
                                        echo substr($deskripsi, 0, 50) . '...';
                                    } else {
                                        echo $deskripsi;
                                    }
                                    ?>
                                </td>

                                <td style="white-space: nowrap;">
                                    <?php if ($row['google_scholar']): ?>
                                        <a href="<?= fixUrl($row['google_scholar']) ?>" target="_blank" class="btn-link-icon" title="Google Scholar">GS</a>
                                        <?= ($row['orcid'] || $row['sinta']) ? '|' : '' ?>
                                    <?php endif; ?>

                                    <?php if ($row['orcid']): ?>
                                        <a href="<?= fixUrl($row['orcid']) ?>" target="_blank" class="btn-link-icon" title="ORCID">OR</a>
                                        <?= ($row['sinta']) ? '|' : '' ?>
                                    <?php endif; ?>

                                    <?php if ($row['sinta']): ?>
                                        <a href="<?= fixUrl($row['sinta']) ?>" target="_blank" class="btn-link-icon" title="Sinta">ST</a>
                                    <?php endif; ?>

                                    <?php if (!$row['google_scholar'] && !$row['orcid'] && !$row['sinta']) echo "-"; ?>
                                </td>

                                <td>
                                    <?php if ($row['gambar']):
                                        $orig = $row['gambar'];
                                        $ext = pathinfo($orig, PATHINFO_EXTENSION);
                                        $thumb = pathinfo($orig, PATHINFO_FILENAME) . '-thumb.' . $ext;
                                        $srvThumb = $serverThumbDir . $thumb;
                                        $srvOrig = $serverUploadDir . $orig;

                                        $imgPath = (is_file($srvThumb)) ? $webThumbDir . $thumb : $webUploadDir . $orig;
                                        if (is_file(is_file($srvThumb) ? $srvThumb : $srvOrig)) $imgPath .= '?' . filemtime(is_file($srvThumb) ? $srvThumb : $srvOrig);
                                    ?>
                                        <img src="<?= $imgPath ?>" style="max-width: 80px; height: auto; border-radius: 4px;" alt="Thumb">
                                    <?php else: ?> - <?php endif; ?>
                                </td>
                                <td>
                                    <a href="?page=member&edit=<?= $row['id_member'] ?>">Edit</a>
                                    <a href="javascript:void(0)" onclick="deleteMember(<?= (int)$row['id_member'] ?>)" class="del" title="Hapus"> Hapus</a>
                                </td>
                            </tr>
                        <?php endforeach;
                    else: ?>
                        <tr>
                            <td colspan="7" class="text-center">Belum ada member.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if ($totalPages > 1): ?>
            <div class="pagination" style="margin-top: 20px; text-align: center;">
                <?php for ($i = 1; $i <= $totalPages; $i++):
                    $qs = '?page=member&p=' . $i;
                    if ($searchKeyword) $qs .= '&keyword=' . urlencode($searchKeyword);
                    if ($currentSortBy) $qs .= '&sort=' . $currentSortBy . '&order=' . $currentSortOrder;
                ?>
                    <a href="<?= $qs ?>" style="padding:8px 12px; margin:0 4px; border:1px solid <?= ($i == $currentPage ? '#3498db' : '#ccc'); ?>; background-color:<?= ($i == $currentPage ? '#3498db' : '#fff'); ?>; color:<?= ($i == $currentPage ? '#fff' : '#3498db'); ?>; border-radius:4px; text-decoration:none;"><?= $i ?></a>
                <?php endfor; ?>
            </div>
        <?php endif; ?>
    </div>
</div>