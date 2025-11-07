<?php
// admin/module/fasilitas/index.php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header("Location: ../../login.php");
    exit;
}
require_once __DIR__ . '/../../config/koneksi.php';

// config upload
$uploadDir = __DIR__ . '/../../uploads/fasilitas/';
$webUploadDir = '../../uploads/fasilitas/';
@mkdir($uploadDir, 0755, true);
$maxFileSize = 2 * 1024 * 1024;
$allowedExt = ['jpg','jpeg','png','gif','webp'];

// DELETE
if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    $res = pg_query_params($koneksi, "SELECT gambar FROM fasilitas WHERE id_galery = $1 LIMIT 1", array($id));
    if ($res && pg_num_rows($res) > 0) {
        $row = pg_fetch_assoc($res);
        if (!empty($row['gambar'])) {
            $file = $uploadDir . $row['gambar'];
            if (is_file($file)) @unlink($file);
        }
    }
    pg_query_params($koneksi, "DELETE FROM fasilitas WHERE id_galery = $1", array($id));
    header("Location: ?page=fasilitas");
    exit;
}

// edit
$editData = null;
if (isset($_GET['edit'])) {
    $id = (int) $_GET['edit'];
    $res = pg_query_params($koneksi, "SELECT * FROM fasilitas WHERE id_galery = $1 LIMIT 1", array($id));
    if ($res && pg_num_rows($res) > 0) $editData = pg_fetch_assoc($res);
}

// insert/update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul = trim($_POST['judul'] ?? '');
    $deskripsi = trim($_POST['deskripsi'] ?? '');

    $filename = $editData['gambar'] ?? '';

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
                    if (!empty($filename) && is_file($uploadDir . $filename)) @unlink($uploadDir . $filename);
                    $newName = uniqid('fsl_') . '.' . $ext;
                    if (move_uploaded_file($_FILES['gambar']['tmp_name'], $uploadDir . $newName)) $filename = $newName;
                    else $error = "Gagal menyimpan file. Cek permission folder upload.";
                }
            }
        }
    }

    if (!isset($error)) {
        if (!empty($_POST['id_galery'])) {
            $id = (int) $_POST['id_galery'];
            pg_query_params($koneksi, "UPDATE fasilitas SET judul=$1, deskripsi=$2, gambar=$3 WHERE id_galery=$4",
                array($judul, $deskripsi, $filename, $id));
        } else {
            pg_query_params($koneksi, "INSERT INTO fasilitas (judul, gambar, deskripsi) VALUES ($1,$2,$3)",
                array($judul, $filename, $deskripsi));
        }
        header("Location: ?page=fasilitas");
        exit;
    }
}

// fetch
$query = pg_query($koneksi, "SELECT * FROM fasilitas ORDER BY id_galery DESC");
?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<title>Kelola Fasilitas</title>
<meta name="viewport" content="width=device-width,initial-scale=1">
<style>
/* reuse same styling as activity for consistency */
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
    <h2>🏢 Kelola Fasilitas</h2>

    <?php if (!empty($error)): ?><div class="error"><?= htmlspecialchars($error) ?></div><?php endif; ?>

    <form method="post" enctype="multipart/form-data">
        <input type="hidden" name="id_galery" value="<?= htmlspecialchars($editData['id_galery'] ?? '') ?>">

        <div style="margin-bottom:10px">
            <label>Judul</label>
            <input type="text" name="judul" required value="<?= htmlspecialchars($editData['judul'] ?? '') ?>">
        </div>

        <div style="margin-bottom:10px">
            <label>Gambar (upload lokal)</label>
            <input type="file" name="gambar" accept="image/*" onchange="previewImage(event)">
            <div class="preview">
                <?php if (!empty($editData['gambar'])): ?>
                    <img id="imgPreview" src="<?= htmlspecialchars($webUploadDir . $editData['gambar']) ?>" alt="Preview">
                <?php else: ?>
                    <img id="imgPreview" src="" alt="Preview" style="display:none">
                <?php endif; ?>
            </div>
        </div>

        <div style="margin-bottom:10px">
            <label>Deskripsi</label>
            <textarea name="deskripsi"><?= htmlspecialchars($editData['deskripsi'] ?? '') ?></textarea>
        </div>

        <div style="margin-top:8px">
            <button class="btn" type="submit">💾 Simpan</button>
            <?php if ($editData): ?><a class="btn secondary" href="?page=fasilitas" style="text-decoration:none;color:#fff;margin-left:8px">Batal</a><?php endif; ?>
        </div>
    </form>
</div>

<div class="card">
    <h3>Daftar Fasilitas</h3>
    <table class="table">
        <thead><tr><th style="width:60px">ID</th><th>Judul</th><th>Gambar</th><th>Deskripsi</th><th>Aksi</th></tr></thead>
        <tbody>
        <?php while ($row = pg_fetch_assoc($query)): ?>
            <tr>
                <td><?= (int)$row['id_galery'] ?></td>
                <td><?= htmlspecialchars($row['judul']) ?></td>
                <td>
                    <?php if (!empty($row['gambar'])): ?>
                        <button class="preview-btn" data-src="<?= htmlspecialchars($webUploadDir . $row['gambar']) ?>">👁 Preview</button>
                    <?php else: ?>-<?php endif; ?>
                </td>
                <td><?= htmlspecialchars(strlen($row['deskripsi'])>120 ? substr($row['deskripsi'],0,120).'...' : $row['deskripsi']) ?></td>
                <td class="actions">
                    <a class="edit" href="?page=fasilitas&edit=<?= (int)$row['id_galery'] ?>">✏ Edit</a>
                    <a class="del" href="?page=fasilitas&delete=<?= (int)$row['id_galery'] ?>" onclick="return confirm('Yakin hapus?')">🗑 Hapus</a>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>

<!-- Modal -->
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
