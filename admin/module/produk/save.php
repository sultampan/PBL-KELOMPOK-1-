<?php
// admin/module/produk/save.php
if (session_status() === PHP_SESSION_NONE) session_start();

require __DIR__ . '/../../../config/koneksi.php'; 

require "model.php";
require "upload.php";

$uploadDir = __DIR__ . '/../../../public/uploads/produk/';
@mkdir($uploadDir, 0755, true);

$allowedExt = ['jpg','jpeg','png','gif','webp'];
$maxSize = 5 * 1024 * 1024;

$nama = trim($_POST['nama'] ?? '');
$deskripsi = trim($_POST['deskripsi'] ?? '');
$link = trim($_POST['link_produk'] ?? '');
$id = $_POST['id_produk'] ?? null;
$oldImg = $_POST['gambar_lama'] ?? null;

// Fungsi createSlug ada di model.php
$slug = createSlug($nama);

try {
    $gambar = handleUpload("gambar", $oldImg, $uploadDir, $allowedExt, $maxSize, $slug);

    if ($id) {
        updateProduk($pdo, $id, $nama, $deskripsi, $gambar, $link);
    } else {
        $id_admin = $_SESSION['id_admin'] ?? 1;
        insertProduk($pdo, $nama, $deskripsi, $gambar, $link, $id_admin);
    }
    
    header("Location: ../../index.php?page=produk"); 
    exit;
} catch (Exception $e) {
    // Tampilkan error
    $_SESSION['error'] = "Gagal menyimpan: " . $e->getMessage();
    header("Location: ../../index.php?page=produk"); 
    exit;
}
?>