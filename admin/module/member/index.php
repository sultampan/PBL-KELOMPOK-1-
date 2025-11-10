<?php
// admin/module/member/index.php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header("Location: ../../login.php");
    exit;
}
// Panggil koneksi (sudah ada $pdo)
// require_once __DIR__ . '/../../config/koneksi.php'; 
// Tidak perlu require_once lagi, karena sudah dipanggil di admin/index.php

// config upload
$uploadDir = __DIR__ . '/../../uploads/member/';
$webUploadDir = 'uploads/member/';;
@mkdir($uploadDir, 0755, true);
$maxFileSize = 2 * 1024 * 1024;
$allowedExt = ['jpg','jpeg','png','gif','webp'];
$error = null; // Definisikan variabel error

// DELETE (Versi PDO)
if (isset($_GET['delete'])) {
    try {
        $id = (int) $_GET['delete'];
        
        // 1. Ambil nama gambar dulu
        $stmt = $pdo->prepare("SELECT gambar FROM member WHERE id_member = ? LIMIT 1");
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // 2. Hapus file jika ada
        if ($row && !empty($row['gambar'])) {
            $file = $uploadDir . $row['gambar'];
            if (is_file($file)) @unlink($file);
        }

        // 3. Hapus data dari DB
        $stmt = $pdo->prepare("DELETE FROM member WHERE id_member = ?");
        $stmt->execute([$id]);

    } catch (PDOException $e) {
        $error = "Gagal menghapus data: " . $e->getMessage();
        // Sebaiknya jangan langsung redirect agar error terlihat
    }
    
    // Redirect jika tidak ada error
    if (!$error) {
        header("Location: ?page=member");
        exit;
    }
}

// EDIT (Versi PDO)
$editData = null;
if (isset($_GET['edit'])) {
    try {
        $id = (int) $_GET['edit'];
        $stmt = $pdo->prepare("SELECT * FROM member WHERE id_member = ? LIMIT 1");
        $stmt->execute([$id]);
        $editData = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($editData === false) $editData = null; 
    } catch (PDOException $e) {
        $error = "Gagal mengambil data edit: " . $e->getMessage();
    }
}

// INSERT/UPDATE (Versi PDO)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama_member'] ?? '');
    $jabatan = trim($_POST['jabatan'] ?? '');
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    $gs = trim($_POST['google_scholar'] ?? '');
    $st = trim($_POST['sinta'] ?? ''); // <-- Ini ada di form Anda
    $orcid = trim($_POST['orcid'] ?? '');

    $filename = $_POST['gambar_lama'] ?? ($editData['gambar'] ?? ''); // Ambil nama file lama

    // Logika upload file (sudah benar, tidak perlu diubah)
    if (!empty($_FILES['gambar']['name']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
        if ($_FILES['gambar']['size'] > $maxFileSize) {
            $error = "File terlalu besar. Maks 2MB.";
        } else {
            $info = @getimagesize($_FILES['gambar']['tmp_name']);
            if ($info === false) $error = "File bukan gambar valid.";
            else {
                $ext = strtolower(pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION));
                if (!in_array($ext, $allowedExt)) $error = "Format gambar tidak diperbolehkan.";
                else {
                    // Hapus file lama jika ada
                    if (!empty($filename) && is_file($uploadDir . $filename)) {
                        @unlink($uploadDir . $filename);
                    }
                    $newName = uniqid('mem_') . '.' . $ext;
                    if (move_uploaded_file($_FILES['gambar']['tmp_name'], $uploadDir . $newName)) {
                        $filename = $newName; // Gunakan nama file baru
                    } else {
                        $error = "Gagal menyimpan file. Cek permission folder upload.";
                    }
                }
            }
        }
    }

    // Eksekusi database jika tidak ada error upload
    if (!isset($error)) {
        try {
            if (!empty($_POST['id_member'])) {
                // UPDATE
                $id = (int) $_POST['id_member'];
                $sql = "UPDATE member SET 
                            nama_member = :nama, 
                            jabatan = :jabatan, 
                            deskripsi = :deskripsi, 
                            google_scholar = :gs, 
                            sinta = :st, 
                            orcid = :orcid, 
                            gambar = :gambar 
                        WHERE id_member = :id";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':nama' => $nama,
                    ':jabatan' => $jabatan,
                    ':deskripsi' => $deskripsi,
                    ':gs' => $gs,
                    ':st' => $st, // <-- Saya perbaiki, data $rg sekarang disimpan
                    ':orcid' => $orcid,
                    ':gambar' => $filename,
                    ':id' => $id
                ]);
            } else {
                // INSERT
                $sql = "INSERT INTO member (nama_member, jabatan, deskripsi, google_scholar, sinta, orcid, gambar) 
                        VALUES (:nama, :jabatan, :deskripsi, :gs, :st, :orcid, :gambar)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':nama' => $nama,
                    ':jabatan' => $jabatan,
                    ':deskripsi' => $deskripsi,
                    ':gs' => $gs,
                    ':st' => $st, // <-- Saya perbaiki, data $st sekarang disimpan
                    ':orcid' => $orcid,
                    ':gambar' => $filename
                ]);
            }
            header("Location: ?page=member"); // Redirect setelah sukses
            exit;
        } catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    }
}

// FETCH (Versi PDO)
$stmt_list = null; // Inisialisasi
try {
    $query_sql = "SELECT * FROM member ORDER BY id_member DESC";
    $stmt_list = $pdo->query($query_sql); // $pdo->query() mengembalikan PDOStatement
} catch (PDOException $e) {
    // Tampilkan error di dalam card
    if (!isset($error)) $error = "Gagal mengambil daftar member: " . $e->getMessage();
}
?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<title>Kelola Member</title>
<meta name="viewport" content="width=device-width,initial-scale=1">
<style>
/* ... (CSS Anda sama, tidak perlu diubah) ... */
.card{background:#fff;padding:18px;border-radius:12px;box-shadow:0 10px 30px rgba(0,0,0,0.06);margin-bottom:18px}
label{font-weight:600;color:#2c3e50;margin-bottom:6px;display:block}
input[type="text"], textarea, input[type="file"]{width:100%;padding:10px;border-radius:8px;border:1px solid #e6eef6}
textarea{min-height:90px}
.btn{background:#27ae60;color:#fff;padding:10px 14px;border-radius:10px;border:none;cursor:pointer}
.btn.secondary{background:#95a5a6}
.preview img{width:140px;border-radius:8px;box-shadow:0 10px 30px rgba(0,0,0,0.08);margin-top:8px}
.table{width:100%;border-collapse:collapse}
.table thead{background:#2f3b46;color:#fff}
.table th, .table td{padding:12px;border-bottom:1px solid #eee;text-align:left;vertical-align:top}
.actions a{display:inline-block;padding:8px 12px;border-radius:8px;color:#fff;text-decoration:none;margin-right:6px}
.edit{background:#f39c12} .del{background:#e74c3c}
.preview-btn{background:#34495e;padding:8px 12px;border-radius:8px;color:#fff;text-decoration:none;cursor:pointer;border:none}
.modal-backdrop{position:fixed;inset:0;display:none;align-items:center;justify-content:center;background:rgba(10,15,25,0.6);backdrop-filter:blur(3px);z-index:9999;animation:fadeIn .18s ease}
.modal-card{max-width:90%;max-height:90%;background:linear-gradient(180deg,#ffffff,#fbfcff);padding:12px;border-radius:12px;box-shadow:0 20px 60px rgba(7,12,22,0.45);display:flex;flex-direction:column;align-items:center;animation:popUp .18s ease}
.modal-card img{max-width:100%;max-height:80vh;border-radius:8px;box-shadow:0 12px 40px rgba(0,0,0,0.25)}
.modal-close{position:absolute;right:18px;top:18px;background:#111;color:#fff;width:36px;height:36px;border-radius:999px;border:none;cursor:pointer;font-size:18px;display:flex;align-items:center;justify-content:center}
@keyframes popUp { from { transform: translateY(8px) scale(0.98); opacity:0 } to { transform: translateY(0) scale(1); opacity:1 } }
@keyframes fadeIn { from { opacity:0 } to { opacity:1 } }
.error{background:#fff0f0;color:#800;padding:10px;border-radius:8px;margin-bottom:12px;border:1px solid #f5c2c2}
</style>
</head>
<body>
<div class="card">
    <h2> Kelola Member</h2>

    <?php if (!empty($error)): ?><div class="error"><?= htmlspecialchars($error) ?></div><?php endif; ?>

    <form method="post" enctype="multipart/form-data">
        <input type="hidden" name="id_member" value="<?= htmlspecialchars($editData['id_member'] ?? '') ?>">
        <input type="hidden" name="gambar_lama" value="<?= htmlspecialchars($editData['gambar'] ?? '') ?>">


        <div style="margin-bottom:10px">
            <label>Nama Member</label>
            <input type="text" name="nama_member" required value="<?= htmlspecialchars($editData['nama_member'] ?? '') ?>">
        </div>

        <div style="margin-bottom:10px">
            <label>Jabatan</label>
            <input type="text" name="jabatan" value="<?= htmlspecialchars($editData['jabatan'] ?? '') ?>">
        </div>

        <div style="margin-bottom:10px">
            <label>Deskripsi</label>
            <textarea name="deskripsi"><?= htmlspecialchars($editData['deskripsi'] ?? '') ?></textarea>
        </div>

        <div style="margin-bottom:10px">
            <label>Google Scholar</label>
            <input type="text" name="google_scholar" value="<?= htmlspecialchars($editData['google_scholar'] ?? '') ?>">
        </div>

        <div style="margin-bottom:10px">
            <label>sinta</label>
            <input type="text" name="sinta" value="<?= htmlspecialchars($editData['SINTA'] ?? '') ?>">
        </div>

        <div style="margin-bottom:10px">
            <label>ORCID</label>
            <input type="text" name="orcid" value="<?= htmlspecialchars($editData['orcid'] ?? '') ?>">
        </div>

        <div style="margin-bottom:10px">
            <label>Foto Member (opsional, maks 2MB)</label>
            <input type="file" name="gambar" accept="image/*" onchange="previewImage(event)">
            <div class="preview">
                <?php if (!empty($editData['gambar'])): ?>
                    <img id="imgPreview" src="<?= htmlspecialchars($webUploadDir . $editData['gambar']) ?>" alt="Preview">
                <?php else: ?>
                    <img id="imgPreview" src="" alt="Preview" style="display:none">
                <?php endif; ?>
            </div>
        </div>

        <div style="margin-top:8px">
            <button class="btn" type="submit"> Simpan</button>
            <?php if ($editData): ?><a class="btn secondary" href="?page=member" style="text-decoration:none;color:#fff;margin-left:8px">Batal</a><?php endif; ?>
        </div>
    </form>
</div>

<div class="card">
    <h3>Daftar Member</h3>
    <table class="table">
        <thead><tr><th style="width:60px">ID</th><th>Nama</th><th>Jabatan</th><th>Gambar</th><th>Aksi</th></tr></thead>
        <tbody>
        <?php 
        // LOOPING (Versi PDO)
        if ($stmt_list): // Cek jika $stmt_list berhasil dibuat
            while ($row = $stmt_list->fetch(PDO::FETCH_ASSOC)): 
        ?>
            <tr>
                <td><?= (int)$row['id_member'] ?></td>
                <td><?= htmlspecialchars($row['nama_member']) ?></td>
                <td><?= htmlspecialchars($row['jabatan']) ?></td>
                <td>
                    <?php if (!empty($row['gambar'])): ?>
                        <button class="preview-btn" data-src="<?= htmlspecialchars($webUploadDir . $row['gambar']) ?>">👁 Preview</button>
                    <?php else: ?>-<?php endif; ?>
                </td>
                <td class="actions">
                    <a class="edit" href="?page=member&edit=<?= (int)$row['id_member'] ?>">✏ Edit</a>
                    <a class="del" href="?page=member&delete=<?= (int)$row['id_member'] ?>" onclick="return confirm('Yakin hapus?')">🗑 Hapus</a>
                </td>
            </tr>
        <?php 
            endwhile; 
        endif; // Akhir dari 'if ($stmt_list)'
        ?>
        </tbody>
    </table>
</div>

<div id="modalBackdrop" class="modal-backdrop" role="dialog" aria-hidden="true">
    <div class="modal-card" role="document">
        <button id="modalClose" class="modal-close" aria-label="Tutup">×</button>
        <img id="modalImage" src="" alt="Preview Gambar">
    </div>
</div>

<script>
function previewImage(event) {
    const img = document.getElementById('imgPreview');
    if (!event.target.files || !event.target.files[0]) return;
    img.src = URL.createObjectURL(event.target.files[0]);
    img.style.display = 'block';
}

const modal = document.getElementById('modalBackdrop');
const modalImg = document.getElementById('modalImage');
const closeBtn = document.getElementById('modalClose');

document.addEventListener('click', function(e){
    const btn = e.target.closest('.preview-btn');
    if (btn) {
        const src = btn.getAttribute('data-src');
        if (!src) return;
        modalImg.src = src;
        modal.style.display = 'flex';
        modal.setAttribute('aria-hidden','false');
    }
});

closeBtn.addEventListener('click', closeModal);
modal.addEventListener('click', function(e){ if (e.target === modal) closeModal(); });
document.addEventListener('keydown', function(e){ if (e.key === 'Escape') closeModal(); });
function closeModal(){ modal.style.display = 'none'; modal.setAttribute('aria-hidden','true'); modalImg.src = ''; }
</script>
</body>
</html>