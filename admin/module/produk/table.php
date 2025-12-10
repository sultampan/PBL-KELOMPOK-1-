<!-- admin/module/produk/table.php -->
<div id="product-list-container">
    <div class="card">
        <h3>Daftar Produk</h3>

        <?php
        global $webUploadDir, $webThumbDir, $serverUploadDir, $serverThumbDir;

        if (isset($paginationData) && is_array($paginationData)) {
            extract($paginationData);
        } else {
            $currentPage = 1;
            $totalPages = 1;
            $searchKeyword = null;
            $limit = 10;
            $currentSortBy = 'id_produk';
            $currentSortOrder = 'ASC';
        }

        function getSortLink($column, $currentSortBy, $currentSortOrder, $searchKeyword, $currentPage)
        {
            $newOrder = ($currentSortBy === $column && $currentSortOrder === 'ASC') ? 'DESC' : 'ASC';
            $icon = ($currentSortBy === $column) ? ($currentSortOrder === 'ASC' ? ' ▲' : ' ▼') : '';

            $query = '?page=produk&sort=' . $column . '&order=' . $newOrder;

            if ($searchKeyword) {
                $query .= '&keyword=' . urlencode($searchKeyword);
            }

            $query .= '&p=' . $currentPage;

            return '<a href="' . $query . '" style="text-decoration:none;color:inherit;">' . ucfirst($column) . $icon . '</a>';
        }
        ?>

        <div style="overflow-x:auto;">
            <table class="table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th><?= getSortLink('nama', $currentSortBy, $currentSortOrder, $searchKeyword, $currentPage) ?></th>
                        <th><?= getSortLink('deskripsi', $currentSortBy, $currentSortOrder, $searchKeyword, $currentPage) ?></th>

                        <!-- 🟩 KOLOM TIM BARU -->
                        <th>Tim</th>

                        <th>Gambar</th>
                        <th>Link</th>
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

                                <td><?= htmlspecialchars($row['nama']) ?></td>

                                <td>
                                    <?php
                                    $desc = htmlspecialchars($row['deskripsi']);
                                    echo strlen($desc) > 90 ? substr($desc, 0, 90) . '...' : $desc;
                                    ?>
                                </td>

                                <!--  KOLOM TIM PRODUK -->
                                <td>
                                    <?php if (!empty($row['team'])): ?>

                                        <?php foreach ($row['team'] as $tm): ?>
                                            <div class="team-card-item">
                                                <div class="team-name"><i class="fas fa-user"></i> <?= htmlspecialchars($tm['nama_member']) ?></div>
                                                <div class="team-role">Role: <?= htmlspecialchars($tm['role']) ?></div>
                                            </div>
                                        <?php endforeach; ?>

                                    <?php else: ?>
                                        <span style="color:#888">Belum ada member</span>
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <?php if ($row['gambar']):

                                        $original = $row['gambar'];
                                        $ext = pathinfo($original, PATHINFO_EXTENSION);
                                        $base = pathinfo($original, PATHINFO_FILENAME);
                                        $thumb = $base . '-thumb.' . $ext;

                                        $serverThumb = $serverThumbDir . $thumb;
                                        $serverOriginal = $serverUploadDir . $original;

                                        if (is_file($serverThumb)) {
                                            $image_path = $webThumbDir . $thumb;
                                            $mtimePath = $serverThumb;
                                        } else {
                                            $image_path = $webUploadDir . $original;
                                            $mtimePath = $serverOriginal;
                                        }

                                        if (is_file($mtimePath)) {
                                            $image_path .= '?' . filemtime($mtimePath);
                                        }
                                    ?>
                                        <img src="<?= $image_path ?>" style="max-width:120px;height:auto;">
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <?php if ($row['link_produk']): ?>
                                        <?php
                                        $url = htmlspecialchars($row['link_produk']);
                                        $url = (!preg_match('/^https?:\/\//', $url)) ? "https://$url" : $url;
                                        ?>
                                        <a href="<?= $url ?>" target="_blank">Lihat</a>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <a href="?page=produk&edit=<?= $row['id_produk'] ?>">Edit</a>
                                    <a href="javascript:void(0)"
                                       onclick="deleteProduct(<?= (int)$row['id_produk'] ?>)"
                                       class="del">
                                       Hapus
                                    </a>
                                </td>
                            </tr>

                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center">Belum ada produk.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if ($totalPages > 1): ?>
            <div class="pagination" style="margin-top:20px;text-align:center;">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <?php
                    $query = '?page=produk&p=' . $i;

                    if ($searchKeyword) {
                        $query .= '&keyword=' . urlencode($searchKeyword);
                    }

                    if ($currentSortBy) {
                        $query .= '&sort=' . $currentSortBy . '&order=' . $currentSortOrder;
                    }
                    ?>
                    <a href="<?= $query ?>"
                        style="
                            padding:8px 12px;
                            margin:0 4px;
                            border:1px solid <?= ($i == $currentPage ? '#3498db' : '#ccc'); ?>;
                            background-color: <?= ($i == $currentPage ? '#3498db' : '#fff'); ?>;
                            color: <?= ($i == $currentPage ? '#fff' : '#3498db'); ?>;
                            text-decoration:none;
                            border-radius:4px;
                        ">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>
            </div>
        <?php endif; ?>

    </div>
</div>
