<?php
// admin/module/contact/index.php

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'reply':
        include __DIR__ . '/reply.php';
        break;
    case 'delete':
        include __DIR__ . '/delete.php';
        break;
    default:
?>

<link rel="stylesheet" href="assets/css/contact.css">
<link rel="stylesheet" href="assets/css/components.css">


<div class="toolbar-header">
    <h3 class="header-title">Daftar Pesan Masuk</h3>
    <div class="search-box">
        <input type="text" id="searchInput" class="search-input" placeholder="Cari pengirim atau subjek...">
        <button class="btn-search"><i class="fas fa-search"></i> Cari</button>
    </div>
</div>

<div class="card-table-wrapper">
    <div class="table-responsive">
        <table class="contact-table" id="contactTable">
            <thead>
                <tr>
                    <th width="3%">No</th>
                    <th width="17%">Tanggal</th>
                    <th width="20%">Pengirim</th>
                    <th width="23%">Subjek</th>
                    <th width="21%">Status</th>
                    <th width="16%" style="text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $no = 1;
                $stmt = $pdo->query("SELECT * FROM contact ORDER BY tanggal_kirim DESC");
                while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    if ($row['status'] == 'replied') { $badgeClass = 'badge-replied'; $badgeText = 'Sudah Dibalas'; }
                    elseif ($row['status'] == 'read') { $badgeClass = 'badge-read'; $badgeText = 'Dibaca'; }
                    else { $badgeClass = 'badge-pending'; $badgeText = 'Baru'; }
                ?>
                <tr>
                    <td data-label="No"><?= $no++ ?></td>
                    <td data-label="Tanggal" style="font-size: 13px; color: #777;">
                        <?= date('d M Y H:i', strtotime($row['tanggal_kirim'])) ?>
                    </td>
                    <td data-label="Pengirim" class="col-sender">
                        <b><?= htmlspecialchars($row['nama_pengirim']) ?></b>
                        <small><?= htmlspecialchars($row['email_pengirim']) ?></small>
                    </td>
                    <td data-label="Subjek" class="col-subject">
                        <?= htmlspecialchars($row['subjek']) ?>
                    </td>
                    <td data-label="Status">
                        <span class="badge <?= $badgeClass ?>"><?= $badgeText ?></span>
                    </td>
                    <td data-label="Aksi" style="text-align: center;">
                        <a href="index.php?page=contact&action=reply&id=<?= $row['id_contact'] ?>" class="btn-reply"><i class="fas fa-reply"></i></a>
                        <a href="index.php?page=contact&action=delete&id=<?= $row['id_contact'] ?>" class="btn-delete-msg" data-name="<?= htmlspecialchars($row['nama_pengirim']) ?>"><i class="fas fa-trash"></i></a>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<script src="assets/js/contact.js"></script>

<?php
    break;
}
?>