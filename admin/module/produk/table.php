<!-- admin/module/produk/table.php -->
<div id="product-list-container">
    <div class="card">
        <h3>Daftar Produk</h3>

        <?php
        // FIX WAJIB: agar variabel path dikenali table-load.php
        global $serverUploadDir, $serverThumbDir, $webUploadDir, $webThumbDir;

        // Pagination data
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

        // Sorting helper
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

        <!-- ================================ -->
        <!--  GRID CARD PRODUK DIMULAI DI SINI -->
        <!-- ================================ -->

        <div class="product-grid">

        <?php foreach ($list as $row): ?>

            <div class="product-card">

                <!-- Gambar Produk -->
                <?php 
                    $image_path = "-";
                    if (!empty($row['gambar'])) {

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
                    }
                ?>

                <img src="<?= $image_path ?>" class="product-thumb" alt="Gambar Produk">

                <!-- Nama Produk -->
                <div class="product-title">
                    <?= htmlspecialchars($row['nama']) ?>
                </div>

                <!-- Deskripsi Produk -->
                <div class="product-desc">
                    <?php
                    $desc = htmlspecialchars($row['deskripsi']);
                    echo strlen($desc) > 120 ? substr($desc, 0, 120) . '...' : $desc;
                    ?>
                </div>

                <hr>

                <!-- TIM PRODUK (STYLE C PREMIUM) -->
                <?php if (!empty($row['team'])): ?>
                    <?php foreach ($row['team'] as $tm): ?>
                        <div class="team-card-item">
                            <div class="team-name">
                                <i class="fas fa-user"></i>
                                <?= htmlspecialchars($tm['nama_member']) ?>
                            </div>
                            <div class="team-role">
                                Role: <?= htmlspecialchars($tm['role']) ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="team-card-item">
                        <div class="team-name"><i class="fas fa-user"></i> Belum ada member</div>
                    </div>
                <?php endif; ?>

                <hr>

                <!-- Link Produk -->
                <div style="margin-bottom:8px;">
                    <?php if ($row['link_produk']): ?>
                        <?php
                        $url = htmlspecialchars($row['link_produk']);
                        $url = (!preg_match('/^https?:\/\//', $url)) ? "https://$url" : $url;
                        ?>
                        <a href="<?= $url ?>" target="_blank">🔗 Lihat Produk</a>
                    <?php else: ?>
                        <span style="color:#888;">Tidak ada link produk</span>
                    <?php endif; ?>
                </div>

                <!-- Actions -->
                <div class="product-actions">
                    <a href="?page=produk&edit=<?= $row['id_produk'] ?>" class="btn-edit">
                        Edit
                    </a>

                    <a onclick="deleteProduct(<?= $row['id_produk'] ?>)" class="btn-delete">
                        Hapus
                    </a>
                </div>

            </div>

        <?php endforeach; ?>

        </div>

        <!-- ================================ -->
        <!--  PAGINATION TETAP BERFUNGSI      -->
        <!-- ================================ -->
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
                            border-radius:4px;">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>
            </div>
        <?php endif; ?>

    </div>
</div>
