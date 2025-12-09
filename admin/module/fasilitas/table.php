<div id="fasilitas-list-container">
    <div class="card">
        <h3>Daftar Fasilitas</h3>

        <?php
        global $webUploadDir, $webThumbDir, $serverUploadDir, $serverThumbDir;
        
        if (isset($paginationData) && is_array($paginationData)) {
            extract($paginationData);
        } else {
            $currentPage = 1;
            $totalPages = 1;
            $searchKeyword = null;
            $limit = 10;
            $currentSortBy = 'id_galery';
            $currentSortOrder = 'ASC';
        }

        /**
         * Fungsi pembantu sorting (diadaptasi untuk fasilitas)
         */
        function getSortLink($column, $currentSortBy, $currentSortOrder, $searchKeyword, $currentPage)
        {
            $newOrder = 'ASC';
            if ($currentSortBy === $column) {
                $newOrder = $currentSortOrder === 'ASC' ? 'DESC' : 'ASC';
            }

            $icon = '';
            if ($currentSortBy === $column) {
                $icon = $currentSortOrder === 'ASC' ? ' ▲' : ' ▼';
            }

            $queryString = '?page=fasilitas&sort=' . $column . '&order=' . $newOrder;
            if ($searchKeyword) {
                $queryString .= '&keyword=' . urlencode($searchKeyword);
            }
            $queryString .= '&p=' . $currentPage;

            return '<a href="' . $queryString . '" style="text-decoration: none; color: inherit;">' . ucfirst($column) . $icon . '</a>';
        }
        ?>

        <div style="overflow-x: auto;">
            <table class="table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th><?= getSortLink('judul', $currentSortBy, $currentSortOrder, $searchKeyword, $currentPage) ?></th>
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

                                <td>
                                    <?php
                                    $deskripsi_lengkap = htmlspecialchars($row['deskripsi']);
                                    if (strlen($deskripsi_lengkap) > 90) {
                                        echo substr($deskripsi_lengkap, 0, 90) . '...';
                                    } else {
                                        echo $deskripsi_lengkap;
                                    }
                                    ?>
                                </td>

                                <td>
                                    <?php if ($row['gambar']):
                                        $original_filename = $row['gambar'];
                                        $ext = pathinfo($original_filename, PATHINFO_EXTENSION);
                                        $base_name = pathinfo($original_filename, PATHINFO_FILENAME);
                                        $thumbnail_filename = $base_name . '-thumb.' . $ext;

                                        $server_thumb_path = $serverThumbDir . $thumbnail_filename;
                                        $server_original_path = $serverUploadDir . $original_filename;

                                        if (is_file($server_thumb_path)) {
                                            $image_path = $webThumbDir . $thumbnail_filename;
                                            $path_for_mtime = $server_thumb_path;
                                        } else {
                                            $image_path = $webUploadDir . $original_filename;
                                            $path_for_mtime = $server_original_path;
                                        }

                                        if (is_file($path_for_mtime)) {
                                            $timestamp = filemtime($path_for_mtime);
                                            $image_path .= '?' . $timestamp; 
                                        }
                                    ?>
                                        <img src="<?= $image_path ?>" style="max-width: 120px; height: auto;" alt="Thumb">
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <a href="?page=fasilitas&edit=<?= $row['id_galery'] ?>">Edit</a>
                                    <a href="javascript:void(0)" onclick="deleteFasilitas(<?= (int)$row['id_galery'] ?>)" class="del" title="Hapus"> Hapus</a>
                                </td>
                            </tr>
                        <?php endforeach;
                    else: ?>
                        <tr>
                            <td colspan="5" class="text-center">Belum ada fasilitas.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if ($totalPages > 1): ?>
            <div class="pagination" style="margin-top: 20px; text-align: center;">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <?php
                    $queryString = '?page=fasilitas&p=' . $i;
                    if ($searchKeyword) $queryString .= '&keyword=' . urlencode($searchKeyword);
                    if ($currentSortBy) $queryString .= '&sort=' . $currentSortBy . '&order=' . $currentSortOrder;
                    ?>
                    <a href="<?= $queryString ?>"
                        style="padding: 8px 12px; margin: 0 4px; border: 1px solid <?= ($i == $currentPage ? '#3498db' : '#ccc'); ?>; background-color: <?= ($i == $currentPage ? '#3498db' : '#fff'); ?>; color: <?= ($i == $currentPage ? '#fff' : '#3498db'); ?>; text-decoration: none; border-radius: 4px;">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>
            </div>
        <?php endif; ?>
    </div>
</div>