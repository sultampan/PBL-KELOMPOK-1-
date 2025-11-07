<!-- ========== module/member/index.php ========== -->
<?php
require_once 'config/koneksi.php';

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    pg_query($koneksi, "DELETE FROM member WHERE id_member = $id");
    header("Location: ?page=member");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = pg_escape_string($koneksi, $_POST['nama_member']);
    $jabatan = pg_escape_string($koneksi, $_POST['jabatan']);
    $deskripsi = pg_escape_string($koneksi, $_POST['deskripsi']);
    $gs = $_POST['google_scholar'];
    $rg = $_POST['research_gate'];
    $orcid = $_POST['orcid'];
    
    if (isset($_POST['id_member']) && !empty($_POST['id_member'])) {
        $id = $_POST['id_member'];
        pg_query($koneksi, "UPDATE member SET nama_member='$nama', jabatan='$jabatan', deskripsi='$deskripsi', google_scholar='$gs', research_gate='$rg', orcid='$orcid' WHERE id_member=$id");
    } else {
        pg_query($koneksi, "INSERT INTO member (nama_member, jabatan, deskripsi, google_scholar, research_gate, orcid) VALUES ('$nama', '$jabatan', '$deskripsi', '$gs', '$rg', '$orcid')");
    }
    header("Location: ?page=member");
    exit;
}

$editData = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $result = pg_query($koneksi, "SELECT * FROM member WHERE id_member = $id");
    $editData = pg_fetch_assoc($result);
}

$query = pg_query($koneksi, "SELECT * FROM member ORDER BY id_member DESC");
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

<h2>👥 Kelola Member</h2>

<div class="form-section">
    <h3><?= $editData ? 'Edit' : 'Tambah' ?> Member</h3>
    <form method="POST">
        <input type="hidden" name="id_member" value="<?= $editData['id_member'] ?? '' ?>">
        <div class="form-group">
            <label>Nama Member:</label>
            <input type="text" name="nama_member" value="<?= $editData['nama_member'] ?? '' ?>" required>
        </div>
        <div class="form-group">
            <label>Jabatan:</label>
            <input type="text" name="jabatan" value="<?= $editData['jabatan'] ?? '' ?>">
        </div>
        <div class="form-group">
            <label>Deskripsi:</label>
            <textarea name="deskripsi"><?= $editData['deskripsi'] ?? '' ?></textarea>
        </div>
        <div class="form-group">
            <label>Google Scholar:</label>
            <input type="text" name="google_scholar" value="<?= $editData['google_scholar'] ?? '' ?>">
        </div>
        <div class="form-group">
            <label>Research Gate:</label>
            <input type="text" name="research_gate" value="<?= $editData['research_gate'] ?? '' ?>">
        </div>
        <div class="form-group">
            <label>ORCID:</label>
            <input type="text" name="orcid" value="<?= $editData['orcid'] ?? '' ?>">
        </div>
        <button type="submit" class="btn btn-success">💾 Simpan</button>
        <?php if ($editData): ?>
            <a href="?page=member" class="btn btn-primary">Batal</a>
        <?php endif; ?>
    </form>
</div>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Nama</th>
            <th>Jabatan</th>
            <th>Deskripsi</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = pg_fetch_assoc($query)): ?>
        <tr>
            <td><?= $row['id_member'] ?></td>
            <td><?= $row['nama_member'] ?></td>
            <td><?= $row['jabatan'] ?></td>
            <td><?= substr($row['deskripsi'], 0, 40) ?>...</td>
            <td>
                <a href="?page=member&edit=<?= $row['id_member'] ?>" class="btn btn-warning">✏️ Edit</a>
                <a href="?page=member&delete=<?= $row['id_member'] ?>" class="btn btn-danger" onclick="return confirm('Yakin hapus?')">🗑️ Hapus</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>