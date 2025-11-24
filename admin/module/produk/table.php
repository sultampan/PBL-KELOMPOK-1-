<div class="card">
    <h3>Daftar Produk</h3>
    
    <?php 
    // LANGKAH 1: EKSTRAK VARIABEL DARI $paginationData
    // Ini membuat variabel $currentPage, $totalPages, $searchKeyword, dll. tersedia.
    if (isset($paginationData) && is_array($paginationData)) {
        extract($paginationData); 
    } else {
        // Fallback jika terjadi error pada index.php
        $currentPage = 1; 
        $totalPages = 1;
        $searchKeyword = null;
    }

    /**
     * Fungsi pembantu untuk membuat link pengurutan
     * @param string $column Nama kolom (misal 'nama')
     * @param string $currentSortBy Kolom yang sedang diurutkan
     * @param string $currentSortOrder Arah urutan saat ini
     * @param string $searchKeyword Kata kunci pencarian saat ini
     */

    function getSortLink($column, $currentSortBy, $currentSortOrder, $searchKeyword, $currentPage) {
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

    // Bangun query string dengan mempertahankan page dan keyword
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
                <th>No </th>
                
                <th><?= getSortLink('nama', $currentSortBy, $currentSortOrder, $searchKeyword, $currentPage) ?></th>
                
                <th>Gambar</th> 

                <th><?= getSortLink('deskripsi', $currentSortBy, $currentSortOrder, $searchKeyword, $currentPage) ?></th>
                
                <th>Link</th>
                
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            // Penyesuaian nomor urut untuk paginasi
            $no = ($currentPage - 1) * $limit + 1; 
            
            if ($list): 
                foreach ($list as $row): 
            ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= htmlspecialchars($row['nama']) ?></td>
                        <td>
                            <?php if ($row['gambar']): ?>
                                <img src="<?= $webUploadDir . $row['gambar'] ?>" width="120"> 
                            <?php else: ?>-<?php endif; ?>
                        </td>
                        
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
                            <?php if ($row['link_produk']): ?>
                                <?php
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
                            <a href="?page=produk&delete=<?= $row['id_produk'] ?>" onclick="return confirm('Yakin hapus?')">Hapus</a>
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
    // LOGIKA PAGINASI
    if ($totalPages > 1): 
    ?>
    <div class="pagination" style="margin-top: 20px; text-align: center;">
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <?php
            // Membangun URL dengan mempertahankan keyword pencarian
            $queryString = '?page=produk&p=' . $i;
            if ($searchKeyword) {
                $queryString .= '&keyword=' . urlencode($searchKeyword);
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