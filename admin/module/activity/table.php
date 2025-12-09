<div id="activity-list-container">
    <div class="card">
        <h3>Daftar Activity</h3>

        <?php
        global $webUploadDir, $webThumbDir, $serverUploadDir, $serverThumbDir;
        if (isset($paginationData) && is_array($paginationData)) extract($paginationData);
        else {
            $currentPage = 1; $totalPages = 1; $searchKeyword = null;
            $limit = 10; $currentSortBy = 'id_activity'; $currentSortOrder = 'ASC';
        }

        function getSortLink($column, $currentSortBy, $currentSortOrder, $searchKeyword, $currentPage) {
            $newOrder = ($currentSortBy === $column && $currentSortOrder === 'ASC') ? 'DESC' : 'ASC';
            $icon = ($currentSortBy === $column) ? ($currentSortOrder === 'ASC' ? ' ▲' : ' ▼') : '';
            $qs = '?page=activity&sort=' . $column . '&order=' . $newOrder . '&p=' . $currentPage;
            if ($searchKeyword) $qs .= '&keyword=' . urlencode($searchKeyword);
            return '<a href="' . $qs . '" style="text-decoration: none; color: inherit;">' . ucfirst(str_replace('_', ' ', $column)) . $icon . '</a>';
        }
        ?>

        <div style="overflow-x: auto;">
            <table class="table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th><?= getSortLink('judul', $currentSortBy, $currentSortOrder, $searchKeyword, $currentPage) ?></th>
                        <th><?= getSortLink('tanggal_kegiatan', $currentSortBy, $currentSortOrder, $searchKeyword, $currentPage) ?></th>
                        <th><?= getSortLink('deskripsi', $currentSortBy, $currentSortOrder, $searchKeyword, $currentPage) ?></th>
                        <th>Gambar</th>
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
                                <td><?= htmlspecialchars($row['judul']) ?></td>
                                
                                <td><?= date('d M Y', strtotime($row['tanggal_kegiatan'])) ?></td>

                                <td>
                                    <?php
                                    $desc = htmlspecialchars($row['deskripsi']);
                                    echo (strlen($desc) > 90) ? substr($desc, 0, 90) . '...' : $desc;
                                    ?>
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
                                        <img src="<?= $imgPath ?>" style="max-width: 120px; height: auto;" alt="Thumb">
                                    <?php else: ?> - <?php endif; ?>
                                </td>
                                <td>
                                    <a href="?page=activity&edit=<?= $row['id_activity'] ?>">Edit</a>
                                    <a href="javascript:void(0)" onclick="deleteActivity(<?= (int)$row['id_activity'] ?>)" class="del" title="Hapus"> Hapus</a>
                                </td>
                            </tr>
                        <?php endforeach;
                    else: ?>
                        <tr><td colspan="6" class="text-center">Belum ada activity.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if ($totalPages > 1): ?>
            <div class="pagination" style="margin-top: 20px; text-align: center;">
                <?php for ($i = 1; $i <= $totalPages; $i++): 
                    $qs = '?page=activity&p=' . $i;
                    if ($searchKeyword) $qs .= '&keyword=' . urlencode($searchKeyword);
                    if ($currentSortBy) $qs .= '&sort=' . $currentSortBy . '&order=' . $currentSortOrder;
                ?>
                    <a href="<?= $qs ?>" style="padding:8px 12px; margin:0 4px; border:1px solid <?= ($i == $currentPage ? '#3498db' : '#ccc'); ?>; background-color:<?= ($i == $currentPage ? '#3498db' : '#fff'); ?>; color:<?= ($i == $currentPage ? '#fff' : '#3498db'); ?>; border-radius:4px; text-decoration:none;"><?= $i ?></a>
                <?php endfor; ?>
            </div>
        <?php endif; ?>
    </div>
</div>