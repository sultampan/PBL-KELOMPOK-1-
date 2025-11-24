<div class="card">
    <h3>Daftar Produk</h3>
    <div style="overflow-x: auto;">
    <table class="table">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Gambar</th>
                <th>Deskripsi</th>  
                <th>Link</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($list): $no = 1;
                foreach ($list as $row): ?>
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
                                // Potong teks jika lebih dari 90 karakter
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
</div>