<div id="product-list-container">
    <div class="card">
        <h3>Daftar Produk</h3>

        <?php
        // 🚨 KOREKSI: Tambahkan deklarasi global di sini
        global $webUploadDir, $webThumbDir, $serverUploadDir, $serverThumbDir;
        // LOGIKA INITIALISASI DAN EKSTRAKSI PAGINASI
        // Ini membuat variabel $currentPage, $totalPages, $searchKeyword, $limit, $currentSortBy, $currentSortOrder tersedia.
        if (isset($paginationData) && is_array($paginationData)) {
            extract($paginationData);
        } else {
            // Fallback default jika variabel belum diset di controller
            $currentPage = 1;
            $totalPages = 1;
            $searchKeyword = null;
            $limit = 10;
            $currentSortBy = 'id_produk';
            $currentSortOrder = 'ASC';
        }

        /**
         * Fungsi pembantu untuk membuat link pengurutan
         */
        function getSortLink($column, $currentSortBy, $currentSortOrder, $searchKeyword, $currentPage)
        {
            // Tentukan arah urutan baru
            $newOrder = 'ASC';
            if ($currentSortBy === $column) {
                $newOrder = $currentSortOrder === 'ASC' ? 'DESC' : 'ASC';
            }

            // Ikon untuk menampilkan status
            $icon = '';
            if ($currentSortBy === $column) {
                $icon = $currentSortOrder === 'ASC' ? ' ▲' : ' ▼';
            }

            // Bangun query string (mempertahankan page dan keyword)
            $queryString = '?page=produk&sort=' . $column . '&order=' . $newOrder;
            if ($searchKeyword) {
                $queryString .= '&keyword=' . urlencode($searchKeyword);
            }
            // Pertahankan halaman saat ini
            $queryString .= '&p=' . $currentPage;

            return '<a href="' . $queryString . '" style="text-decoration: none; color: inherit;">' . ucfirst($column) . $icon . '</a>';
        }
        ?>

        <div style="overflow-x: auto;">
            <table class="table">
                <thead>
                    <tr>
                        <th>No</th>

                        <th><?= getSortLink('nama', $currentSortBy, $currentSortOrder, $searchKeyword, $currentPage) ?></th>

                        <th><?= getSortLink('deskripsi', $currentSortBy, $currentSortOrder, $searchKeyword, $currentPage) ?></th>

                        <th>Gambar</th>
                        <th>Link</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Penyesuaian nomor urut untuk paginasi (dimulai dari offset + 1)
                    $no = (($currentPage - 1) * $limit) + 1;

                    if ($list):
                        foreach ($list as $row):
                    ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= htmlspecialchars($row['nama']) ?></td>

                                <td>
                                    <?php
                                    // Logika Pemotongan Deskripsi
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

                                        // --- 1. Tentukan Path File Server dan Web ---
                                        $server_thumb_path = $serverThumbDir . $thumbnail_filename; // Path server THUMBNAIL
                                        $server_original_path = $serverUploadDir . $original_filename; // Path server ASLI

                                        // --- 2. LOGIKA PENENTUAN TAMPILAN ---
                                        if (is_file($server_thumb_path)) {
                                            // A. TAMPILKAN THUMBNAIL (JPG/PNG/WEBP)
                                            $image_path = $webThumbDir . $thumbnail_filename;
                                            $path_for_mtime = $server_thumb_path;
                                        } else {
                                            // B. TAMPILKAN FILE ASLI (GIF atau Fallback)
                                            $image_path = $webUploadDir . $original_filename;
                                            $path_for_mtime = $server_original_path;
                                        }

                                        // 3. Cache Busting
                                        if (is_file($path_for_mtime)) {
                                            $timestamp = filemtime($path_for_mtime);
                                            $image_path .= '?' . $timestamp; // Memaksa refresh
                                        }

                                    ?>
                                        <img
                                            src="<?= $image_path ?>"
                                            style="max-width: 120px; height: auto;"
                                            alt="<?= htmlspecialchars($row['nama']) ?> Thumbnail">
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($row['link_produk']): ?>
                                        <?php
                                        // Logika Perbaikan Link URL
                                        $link_url = htmlspecialchars($row['link_produk']);
                                        if (strpos($link_url, 'http://') === false && strpos($link_url, 'https://') === false) {
                                            $link_url_fixed = 'https://' . $link_url;
                                        } else {
                                            $link_url_fixed = $link_url;
                                        }
                                        ?>
                                        <a href="<?= $link_url_fixed ?>" target="_blank">Lihat</a>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <a href="?page=produk&edit=<?= $row['id_produk'] ?>">Edit</a>
                                    <a href="javascript:void(0)" onclick="deleteProduct(<?= (int)$row['id_produk'] ?>)" class="del" title="Hapus Produk"> Hapus</a>
                                </td>
                            </tr>
                        <?php endforeach;
                    else: ?>
                        <tr>
                            <td colspan="6" class="text-center">Belum ada produk.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php
        // LOGIKA PAGINASI DISPLAY
        if ($totalPages > 1):
        ?>
            <div class="pagination" style="margin-top: 20px; text-align: center;">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <?php
                    // Membangun URL dengan mempertahankan sorting dan keyword
                    $queryString = '?page=produk&p=' . $i;
                    if ($searchKeyword) {
                        $queryString .= '&keyword=' . urlencode($searchKeyword);
                    }
                    if ($currentSortBy) {
                        $queryString .= '&sort=' . $currentSortBy . '&order=' . $currentSortOrder;
                    }
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