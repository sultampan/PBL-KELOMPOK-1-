<!-- ========== module/activity/index.php ========== -->
<?php
require_once 'config/koneksi.php';

// Handle Delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    pg_query($koneksi, "DELETE FROM activity WHERE id_activity = $id");
    header("Location: ?page=activity");
    exit;
}

// Handle Insert/Update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $judul = pg_escape_string($koneksi, $_POST['judul']);
    $deskripsi = pg_escape_string($koneksi, $_POST['deskripsi']);
    $tanggal = $_POST['tanggal_kegiatan'];
    $gambar = $_POST['gambar'];
    
    if (isset($_POST['id_activity']) && !empty($_POST['id_activity'])) {
        $id = $_POST['id_activity'];
        pg_query($koneksi, "UPDATE activity SET judul='$judul', deskripsi='$deskripsi', tanggal_kegiatan='$tanggal', gambar='$gambar' WHERE id_activity=$id");
    } else {
        pg_query($koneksi, "INSERT INTO activity (judul, deskripsi, tanggal_kegiatan, gambar) VALUES ('$judul', '$deskripsi', '$tanggal', '$gambar')");
    }
    header("Location: ?page=activity");
    exit;
}

// Get data for edit
$editData = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $result = pg_query($koneksi, "SELECT * FROM activity WHERE id_activity = $id");
    $editData = pg_fetch_assoc($result);
}

// Get all data
$query = pg_query($koneksi, "SELECT * FROM activity ORDER BY tanggal_kegiatan DESC");
?>

<style>
    .btn { padding: 8px 15px; border: none; border-radius: 5px; cursor: pointer; text-decoration: none; display: inline-block; margin: 2px; }
    .btn-primary { background: #3498db; color: white; }
    .btn-success { background: #27ae60; color: white; }
    .btn-warning { background: #f39c12; color: white; }
    .btn-danger { background: #e74c3c; color: white; }
    .btn:hover { opacity: 0.8; }
    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
    th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
    th { background: #34495e; color: white; }
    tr:hover { background: #f5f5f5; }
    .form-group { margin-bottom: 15px; }
    .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
    .form-group input, .form-group textarea { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; }
    .form-group textarea { min-height: 100px; }
    .form-section { background: #ecf0f1; padding: 20px; border-radius: 8px; margin-bottom: 20px; }
</style>

<h2>📅 Kelola Activity</h2>

<div class="form-section">
    <h3><?= $editData ? 'Edit' : 'Tambah' ?> Activity</h3>
    <form method="POST">
        <input type="hidden" name="id_activity" value="<?= $editData['id_activity'] ?? '' ?>">
        <div class="form-group">
            <label>Judul:</label>
            <input type="text" name="judul" value="<?= $editData['judul'] ?? '' ?>" required>
        </div>
        <div class="form-group">
            <label>Deskripsi:</label>
            <textarea name="deskripsi"><?= $editData['deskripsi'] ?? '' ?></textarea>
        </div>
        <div class="form-group">
            <label>Tanggal Kegiatan:</label>
            <input type="date" name="tanggal_kegiatan" value="<?= $editData['tanggal_kegiatan'] ?? '' ?>" required>
        </div>
        <div class="form-group">
            <label>Gambar (URL/Path):</label>
            <input type="text" name="gambar" value="<?= $editData['gambar'] ?? '' ?>">
        </div>
        <button type="submit" class="btn btn-success">💾 Simpan</button>
        <?php if ($editData): ?>
            <a href="?page=activity" class="btn btn-primary">Batal</a>
        <?php endif; ?>
    </form>
</div>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Judul</th>
            <th>Tanggal</th>
            <th>Deskripsi</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = pg_fetch_assoc($query)): ?>
        <tr>
            <td><?= $row['id_activity'] ?></td>
            <td><?= $row['judul'] ?></td>
            <td><?= date('d/m/Y', strtotime($row['tanggal_kegiatan'])) ?></td>
            <td><?= substr($row['deskripsi'], 0, 50) ?>...</td>
            <td>
                <a href="?page=activity&edit=<?= $row['id_activity'] ?>" class="btn btn-warning">✏️ Edit</a>
                <a href="?page=activity&delete=<?= $row['id_activity'] ?>" class="btn btn-danger" onclick="return confirm('Yakin hapus?')">🗑️ Hapus</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>