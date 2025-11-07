<!-- ========== module/produk/index.php ========== -->
<?php
require_once 'config/koneksi.php';

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    pg_query($koneksi, "DELETE FROM produk WHERE id_produk = $id");
    header("Location: ?page=produk");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = pg_escape_string($koneksi, $_POST['nama']);
    $deskripsi = pg_escape_string($koneksi, $_POST['deskripsi']);
    $gambar = $_POST['gambar'];
    $link = $_POST['link_produk'];
    
    if (isset($_POST['id_produk']) && !empty($_POST['id_produk'])) {
        $id = $_POST['id_produk'];
        pg_query($koneksi, "UPDATE produk SET nama='$nama', deskripsi='$deskripsi', gambar='$gambar', link_produk='$link' WHERE id_produk=$id");
    } else {
        pg_query($koneksi, "INSERT INTO produk (nama, deskripsi, gambar, link_produk) VALUES ('$nama', '$deskripsi', '$gambar', '$link')");
    }
    header("Location: ?page=produk");
    exit;
}

$editData = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $result = pg_query($koneksi, "SELECT * FROM produk WHERE id_produk = $id");
    $editData = pg_fetch_assoc($result);
}

$query = pg_query($koneksi, "SELECT * FROM produk ORDER BY id_produk DESC");
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

<h2>📦 Kelola Produk</h2>

<div class="form-section">
    <h3><?= $editData ? 'Edit' : 'Tambah' ?> Produk</h3>
    <form method="POST">
        <input type="hidden" name="id_produk" value="<?= $editData['id_produk'] ?? '' ?>">
        <div class="form-group">
            <label>Nama Produk:</label>
            <input type="text" name="nama" value="<?= $editData['nama'] ?? '' ?>" required>
        </div>
        <div class="form-group">
            <label>Deskripsi:</label>
            <textarea name="deskripsi"><?= $editData['deskripsi'] ?? '' ?></textarea>
        </div>
        <div class="form-group">
            <label>Gambar (URL/Path):</label>
            <input type="text" name="gambar" value="<?= $editData['gambar'] ?? '' ?>">
        </div>
        <div class="form-group">
            <label>Link Produk:</label>
            <input type="text" name="link_produk" value="<?= $editData['link_produk'] ?? '' ?>">
        </div>
        <button type="submit" class="btn btn-success">💾 Simpan</button>
        <?php if ($editData): ?>
            <a href="?page=produk" class="btn btn-primary">Batal</a>
        <?php endif; ?>
    </form>
</div>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Nama</th>
            <th>Deskripsi</th>
            <th>Link</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = pg_fetch_assoc($query)): ?>
        <tr>
            <td><?= $row['id_produk'] ?></td>
            <td><?= $row['nama'] ?></td>
            <td><?= substr($row['deskripsi'], 0, 40) ?>...</td>
            <td><a href="<?= $row['link_produk'] ?>" target="_blank">🔗 Link</a></td>
            <td>
                <a href="?page=produk&edit=<?= $row['id_produk'] ?>" class="btn btn-warning">✏️ Edit</a>
                <a href="?page=produk&delete=<?= $row['id_produk'] ?>" class="btn btn-danger" onclick="return confirm('Yakin hapus?')">🗑️ Hapus</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>