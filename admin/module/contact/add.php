<?php
include '../config/koneksi.php';

if(isset($_POST['submit'])){
    $email = $_POST['email'];
    $pesan = $_POST['pesan'];
    $tgl = date("Y-m-d H:i:s");

    $sql = "INSERT INTO contact (email, pesan, tgl_pesan_diterima, status)
            VALUES ('$email', '$pesan', '$tgl', 'pending')";
    if($conn->query($sql)){
        header("Location: index.php");
    }
}
?>

<h2>Tambah Contact</h2>
<form method="post">
    Email: <input type="email" name="email" required><br>
    Pesan:<br><textarea name="pesan" required></textarea><br>
    <button type="submit" name="submit">Simpan</button>
</form>
