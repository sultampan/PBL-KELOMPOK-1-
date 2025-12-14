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

<style>
    /* Style untuk tombol Reply Lagi (Oranye) */
    .btn-reply-again {
        background-color: #f39c12; color: #fff;
        padding: 8px 10px; border-radius: 6px; text-decoration: none;
        font-size: 12px; display: inline-flex; align-items: center; justify-content: center;
        transition: all 0.2s; border: none; cursor: pointer; margin-right: 5px;
    }
    .btn-reply-again:hover { background-color: #e67e22; transform: translateY(-2px); }

    /* [BARU] Style untuk tombol Hapus Manual (Merah) 
       Kita ganti nama classnya jadi 'btn-delete-red' supaya tidak bentrok dengan JS lama 
    */
    .btn-delete-red {
        background-color: #e74c3c;
        color: #fff;
        padding: 8px 10px; /* Padding disamakan dengan tombol lain */
        border-radius: 6px;
        text-decoration: none;
        font-size: 12px;
        display: inline-flex; /* Agar ikon pas di tengah */
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
        border: none;
        cursor: pointer;
        margin-left: 0;
    }
    .btn-delete-red:hover {
        background-color: #c0392b;
        transform: translateY(-2px);
    }
</style>

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
                    <th width="15%">Tanggal</th>
                    <th width="20%">Pengirim</th>
                    <th width="25%">Subjek</th>
                    <th width="15%" style="text-align: center;">Status</th>
                    <th width="22%" style="text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $no = 1;
                $sql = "SELECT * FROM contact ORDER BY tanggal_kirim DESC";
                $stmt = $pdo->query($sql);

                while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    if ($row['status'] == 'replied') { 
                        $badgeClass = 'badge-replied'; $badgeText = 'Sudah Dibalas'; 
                    } elseif ($row['status'] == 'read') { 
                        $badgeClass = 'badge-read'; $badgeText = 'Dibaca'; 
                    } else { 
                        $badgeClass = 'badge-pending'; $badgeText = 'Baru'; 
                    }

                    $jsonData = htmlspecialchars(json_encode($row), ENT_QUOTES, 'UTF-8');
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
                    <td data-label="Aksi" style="text-align: center; white-space: nowrap;">
                        
                        <?php if ($row['status'] == 'replied'): ?>
                            <button type="button" class="btn-view-reply" onclick="openViewModal(<?= $jsonData ?>)" title="Lihat History Balasan">
                                <i class="fas fa-eye"></i>
                            </button>
                            <a href="index.php?page=contact&action=reply&id=<?= $row['id_contact'] ?>" class="btn-reply-again" title="Kirim Balasan Lagi">
                                <i class="fas fa-redo"></i>
                            </a>
                        <?php else: ?>
                            <a href="index.php?page=contact&action=reply&id=<?= $row['id_contact'] ?>" class="btn-reply" title="Balas Pesan">
                                <i class="fas fa-reply"></i>
                            </a>
                        <?php endif; ?>

                        <a href="index.php?page=contact&action=delete&id=<?= $row['id_contact'] ?>" 
                           class="btn-delete-red" 
                           onclick="return confirm('Yakin ingin menghapus pesan ini beserta balasannya?')" 
                           title="Hapus Pesan">
                            <i class="fas fa-trash"></i>
                        </a>

                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<div id="viewModal" class="modal-overlay">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Detail Percakapan</h3>
            <span class="close-modal" onclick="closeViewModal()">&times;</span>
        </div>
        <div class="modal-body">
            
            <div class="view-box" style="background: #fff; border-left: 4px solid #34495e;">
                <strong><i class="fas fa-user"></i> Pesan Masuk</strong>
                <div style="margin-bottom: 5px; font-weight:bold; color:#555;" id="modalSubject"></div>
                <p id="modalMessage"></p>
                <div class="view-meta">
                    Dari: <span id="modalSender"></span> | <span id="modalDate"></span>
                </div>
            </div>

            <div class="reply-history-container">
                <div class="history-label"><i class="fas fa-history"></i> Riwayat Balasan Admin</div>
                <div id="replyHistoryList">
                    <div class="loading-text">Memuat data balasan...</div>
                </div>
            </div>

            <div style="text-align: right; margin-top:20px;">
                <button type="button" class="btn-secondary" onclick="closeViewModal()" style="padding: 8px 15px; border-radius: 4px; border:1px solid #ccc; cursor:pointer;">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script src="assets/js/contact.js"></script>
<script>
    const modal = document.getElementById("viewModal");
    const historyList = document.getElementById("replyHistoryList");

    function openViewModal(data) {
        document.getElementById("modalSubject").textContent = data.subjek;
        document.getElementById("modalMessage").textContent = data.pesan;
        document.getElementById("modalSender").textContent = data.nama_pengirim + " (" + data.email_pengirim + ")";
        document.getElementById("modalDate").textContent = data.tanggal_kirim;

        historyList.innerHTML = '<div class="loading-text"><i class="fas fa-spinner fa-spin"></i> Memuat riwayat...</div>';
        modal.style.display = "block";

        fetch(`module/contact/fetch_history.php?id=${data.id_contact}`)
            .then(response => {
                if (!response.ok) { throw new Error("HTTP Status " + response.status); }
                return response.json();
            })
            .then(replies => {
                if (replies.length > 0) {
                    let html = '';
                    replies.forEach(r => {
                        let adminName = r.nama_admin ? r.nama_admin : 'Admin';
                        html += `
                            <div class="reply-item">
                                <div class="reply-meta">
                                    <span style="color:#02406C;">${adminName}</span>
                                    <span style="color:#999; font-weight:normal; font-size:12px; margin-left:5px;">
                                        ${r.tanggal_balasan}
                                    </span>
                                </div>
                                <div class="reply-content">
                                    ${r.balasan.replace(/\n/g, '<br>')}
                                </div>
                            </div>
                        `;
                    });
                    historyList.innerHTML = html;
                } else {
                    historyList.innerHTML = '<div class="loading-text">Belum ada balasan.</div>';
                }
            })
            .catch(err => {
                console.error("Fetch Error:", err);
                historyList.innerHTML = '<div class="loading-text" style="color:red;">Gagal memuat data.</div>';
            });
    }

    function closeViewModal() {
        modal.style.display = "none";
    }

    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
</script>

<?php
    break;
}
?>