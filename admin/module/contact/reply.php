<?php
require_once __DIR__ . '/../../../config/koneksi.php';

if (!isset($_GET['id'])) exit("ID tidak ditemukan.");
$id = $_GET['id'];

// Ambil data contact
$data = $pdo->prepare("SELECT * FROM contact WHERE id = ?");
$data->execute([$id]);
$row = $data->fetch(PDO::FETCH_ASSOC);

if (!$row) exit("Data tidak ditemukan.");

if (isset($_POST['balasan'])) {
    $balasan = $_POST['balasan'];

    $query = $pdo->prepare("
        UPDATE contact SET
            balasan = ?,
            status = 'done',
            handled_by = ?,
            tgl_pesan_dikirim = NOW()
        WHERE id = ?
    ");
    $query->execute([$balasan, $_SESSION['id_admin'], $id]);

    echo "<script>alert('Balasan berhasil disimpan!');window.location='index.php?page=contact';</script>";
}
?>

<h2>Balas Pesan</h2>
<p><b>Email:</b> <?= $row['email'] ?></p>
<p><b>Pesan:</b> <?= $row['pesan'] ?></p>

<form method="post">
    <label>Tulis Balasan</label><br>
    <textarea name="balasan" required style="width:100%;height:150px;"></textarea>
    <br><br>
    <button class="btn btn-success">Kirim Balasan</button>
</form>
