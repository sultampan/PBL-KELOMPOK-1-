<!-- ========== module/fasilitas/index.php ========== -->
<?php
require_once 'config/koneksi.php';

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    pg_query($koneksi, "DELETE FROM fasilitas WHERE id_galery = $id");
    header("Location: ?page=fasilitas");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $judul = pg_escape_string($koneksi, $_POST['judul']);
    $deskripsi = pg_escape_string($koneksi, $_POST['deskripsi']);
    $gambar = $_POST['gambar'];
    
    if (isset($_POST['id_galery']) && !empty($_POST['id_galery'])) {
        $id = $_POST['id_galery'];
        pg_query($koneksi, "UPDATE fasilitas SET judul='$judul', deskripsi='$deskripsi', gambar='$gambar' WHERE id_galery=$id");
    } else {
        pg_query($koneksi, "INSERT INTO fasilitas (judul, gambar, deskripsi) VALUES ('$judul', '$gambar', '$deskripsi')");
    }
    header("Location: ?page=fasilitas");
    exit;
}

$editData = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $result = pg_query($koneksi, "SELECT * FROM fasilitas WHERE id_galery = $id");
    $editData = pg_fetch_assoc($result);
}

$query = pg_query($koneksi, "SELECT * FROM fasilitas ORDER BY id_galery DESC");
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

<h2>🏢 Kelola Fasilitas</h2>

<div class="form-section">
    <h3><?= $editData ? 'Edit' : 'Tambah' ?> Fasilitas</h3>
    <form method="POST">
        <input type="hidden" name="id_galery" value="<?= $editData['id_galery'] ?? '' ?>">
        <div class="form-group">
            <label>Judul:</label>
            <input type="text" name="judul" value="<?= $editData['judul'] ?? '' ?>" required>
        </div>
        <div class="form-group">
            <label>Gambar (URL/Path):</label>
            <input type="text" name="gambar" value="<?= $editData['gambar'] ?? '' ?>" required>
        </div>
        <div class="form-group">
            <label>Deskripsi:</label>
            <textarea name="deskripsi"><?= $editData['deskripsi'] ?? '' ?></textarea>
        </div>
        <button type="submit" class="btn btn-success">💾 Simpan</button>
        <?php if ($editData): ?>
            <a href="?page=fasilitas" class="btn btn-primary">Batal</a>
        <?php endif; ?>
    </form>
</div>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Judul</th>
            <th>Gambar</th>
            <th>Deskripsi</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = pg_fetch_assoc($query)): ?>
        <tr>
            <td><?= $row['id_galery'] ?></td>
            <td><?= $row['judul'] ?></td>
            <td><?= $row['gambar'] ?></td>
            <td><?= substr($row['deskripsi'], 0, 50) ?>...</td>
            <td>
                <a href="?page=fasilitas&edit=<?= $row['id_galery'] ?>" class="btn btn-warning">✏️ Edit</a>
                <a href="?page=fasilitas&delete=<?= $row['id_galery'] ?>" class="btn btn-danger" onclick="return confirm('Yakin hapus?')">🗑️ Hapus</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>