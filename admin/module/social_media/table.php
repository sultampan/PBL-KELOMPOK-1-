<?php
$keyword = $_GET['keyword'] ?? '';
$list = getSocialMediaAll($pdo, $keyword);
?>

<div class="toolbar-header">
    <h3 class="header-title">Daftar Social Media</h3>

    <form method="GET" class="search-box">
        <input type="hidden" name="page" value="social_media">
        <input type="text" name="keyword" class="search-input"
               placeholder="Cari social media..."
               value="<?= htmlspecialchars($_GET['keyword'] ?? '') ?>">
        <button class="btn-search">
            <i class="fas fa-search"></i> Cari
        </button>
    </form>
</div>

<div class="card-table-wrapper">
    <div class="table-responsive">
        <table class="contact-table">
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th>Nama</th>
                    <th width="10%">Icon</th>
                    <th>Link</th>
                    <th width="12%">Status</th>
                    <th width="15%" style="text-align:center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($list)) : ?>
                    <tr>
                        <td colspan="6" style="text-align:center;color:#777;">
                            Tidak ada social media
                        </td>
                    </tr>
                <?php endif; ?>

                <?php foreach ($list as $i => $row): ?>
                <tr>
                    <td><?= $i + 1 ?></td>
                    <td><?= htmlspecialchars($row['nama']) ?></td>

                    <td style="text-align:center;">
                        <i class="fa-brands <?= htmlspecialchars($row['icon']) ?>"></i>
                    </td>

                    <td>
                        <a href="<?= htmlspecialchars($row['link']) ?>" target="_blank">
                            <?= htmlspecialchars($row['link']) ?>
                        </a>
                    </td>

                    <td>
                        <span class="badge <?= $row['is_active'] ? 'badge-replied' : 'badge-pending' ?>">
                            <?= $row['is_active'] ? 'Aktif' : 'Nonaktif' ?>
                        </span>
                    </td>

                    <td style="text-align:center;">
                        <a href="index.php?page=social_media&edit=<?= $row['id_social_media'] ?>"
                           class="btn-reply" title="Edit">
                            <i class="fas fa-edit"></i>
                        </a>

                        <a href="index.php?page=social_media&delete=<?= $row['id_social_media'] ?>"
                           class="btn-delete-msg"
                           onclick="return confirm('Hapus data ini?')">
                            <i class="fas fa-trash"></i>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
