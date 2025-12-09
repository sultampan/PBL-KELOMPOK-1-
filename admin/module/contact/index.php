<?php
require_once __DIR__ . '/../../../config/koneksi.php';

try {
    $query = $pdo->query("
        SELECT contact.*, admin.username
        FROM contact
        LEFT JOIN admin ON contact.handled_by = admin.id
        ORDER BY contact.id DESC
    ");
} catch (PDOException $e) {
    echo "<pre style='background:#ffdddd;padding:10px;'>SQL ERROR: " . $e->getMessage() . "</pre>";
    exit;
}
?>

<h2 style="margin-bottom: 20px;">Daftar Pesan Contact</h2>

<table class="table table-bordered" style="width:100%; background:white; border-radius:6px; overflow:hidden;">
    <thead style="background:#f2f2f2;">
        <tr>
            <th>No</th>
            <th>Email</th>
            <th>Pesan</th>
            <th>Status</th>
            <th>Diterima</th>
            <th>Dibalas</th>
            <th>Ditangani Oleh</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php 
        $no = 1;
        while ($row = $query->fetch(PDO::FETCH_ASSOC)): ?>
        <tr>
            <td><?= $no++; ?></td>
            <td><?= htmlspecialchars($row['email']); ?></td>
            <td><?= htmlspecialchars(substr($row['pesan'], 0, 40)); ?>...</td>
            <td style="font-weight:bold; color: <?= $row['status']=='pending'?'red':'green'; ?>">
                <?= ucfirst($row['status']); ?>
            </td>
            <td><?= $row['tgl_pesan_diterima']; ?></td>
            <td><?= $row['tgl_pesan_dikirim'] ?: '-'; ?></td>
            <td><?= $row['username'] ?: '-'; ?></td>
            <td>
                <a href="index.php?page=contact_edit&id=<?= $row['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                <a href="index.php?page=contact_reply&id=<?= $row['id'] ?>" class="btn btn-sm btn-info">Reply</a>
                <a href="index.php?page=contact_delete&id=<?= $row['id'] ?>" onclick="return confirm('Yakin hapus?')" class="btn btn-sm btn-danger">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>
