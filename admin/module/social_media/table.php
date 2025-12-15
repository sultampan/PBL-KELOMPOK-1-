<?php $no = 1; ?>

<div class="card-header">
    <h3>Data Social Media</h3>
</div>

<div class="card-body">
    <div style="overflow-x:auto;">
        <table class="table">
            <thead>
                <tr>
                    <th style="width: 60px;">NO</th>
                    <th>LINK</th>
                    <th style="width: 120px; text-align: center;">AKSI</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($list)): ?>
                <tr>
                    <td colspan="3" style="text-align: center; padding: 20px; color: #95a5a6;">
                        Tidak ada data
                    </td>
                </tr>
                <?php else: ?>
                    <?php foreach ($list as $row): ?>
                    <tr>
                        <td><?= $no++; ?></td>
                        <td>
                            <a href="<?= htmlspecialchars($row['link']) ?>" 
                               target="_blank" 
                               style="color: #3498db; text-decoration: none;">
                                <?= htmlspecialchars($row['link']) ?>
                            </a>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <a href="index.php?page=social_media&edit=<?= $row['id_social_media'] ?>"
                                   class="btn-action btn-edit" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>

                                <a href="module/social_media/delete.php?id=<?= $row['id_social_media'] ?>"
                                   onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')"
                                   class="btn-action btn-delete" title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<style>
.table {
    width: 100%;
    border-collapse: collapse;
    background: white;
}

.table thead {
    background-color: #34495e;
    color: white;
}

.table th {
    padding: 12px;
    text-align: left;
    font-weight: 600;
    font-size: 13px;
    letter-spacing: 0.5px;
}

.table td {
    padding: 12px;
    border-bottom: 1px solid #ecf0f1;
}

.table tbody tr:hover {
    background-color: #f8f9fa;
}

.action-buttons {
    display: flex;
    gap: 8px;
}

.btn-action {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 35px;
    height: 35px;
    border-radius: 4px;
    text-decoration: none;
    transition: all 0.3s ease;
}

.btn-edit {
    background-color: #f39c12;
    color: white;
}

.btn-edit:hover {
    background-color: #e67e22;
    transform: translateY(-2px);
}

.btn-delete {
    background-color: #e74c3c;
    color: white;
}

.btn-delete:hover {
    background-color: #c0392b;
    transform: translateY(-2px);
}
</style>