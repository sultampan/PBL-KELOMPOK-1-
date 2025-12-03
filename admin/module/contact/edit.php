<?php
require_once __DIR__ . '/../../../config/koneksi.php';

if (!isset($_GET['id'])) {
    echo "<div style='color:red;'>ID tidak ditemukan.</div>";
    exit;
}

$id = $_GET['id'];

// Ambil data contact
$data = $pdo->prepare("SELECT * FROM contact WHERE id = ?");
$data->execute([$id]);
$row = $data->fetch(PDO::FETCH_ASSOC);

if (!$row) {
    echo "<div style='color:red;'>Data tidak ditemukan.</div>";
    exit;
}

// Proses update form
if (isset($_POST['submit'])) {
    $status = $_POST['status'];

    $update = $pdo->prepare("
        UPDATE contact SET status = ?, handled_by = ?, tgl_pesan_dikirim = NOW()
        WHERE id = ?
    ");
    $update->execute([$status, $_SESSION['id_admin'], $id]);

    echo "<script>alert('Data berhasil diperbarui!');window.location='index.php?page=contact';</script>";
}
?>

<h2>Edit Contact</h2>
<form method="post">
    <label>Status</label>
    <select name="status" required>
        <option value="pending" <?= $row['status']=='pending'?'selected':'' ?>>Pending</option>
        <option value="done" <?= $row['status']=='done'?'selected':'' ?>>Done</option>
    </select>
    <br><br>
    <button class="btn btn-primary" name="submit">Simpan</button>
</form>
