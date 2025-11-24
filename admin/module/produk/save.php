<?php
// admin/module/produk/save.php
if (session_status() === PHP_SESSION_NONE) session_start();

require_once __DIR__ . '/../../../config/koneksi.php'; 

require_once "model.php";
require_once "upload.php";

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
$newSlug = createSlug($nama);
$new_uploaded_filename = null; // Variabel untuk menyimpan nama file yang baru di-upload

try {
    // LANGKAH 1: Ambil nama file hasil upload
    $gambar = handleUpload("gambar", $oldImg, $uploadDir, $allowedExt, $maxSize, $newSlug);
    
    // Jika upload berhasil (yaitu ada file baru atau rename berhasil), simpan namanya.
    // Jika $gambar berbeda dari $oldImg, maka ada aksi file baru.
    if ($gambar !== $oldImg) {
        $new_uploaded_filename = $gambar;
    }

    // LANGKAH 2: EKSEKUSI DATABASE
    if ($id) {
        updateProduk($pdo, $id, $nama, $deskripsi, $gambar, $link);
        $_SESSION['success'] = "Data produk berhasil **diperbarui**.";
    } else {
        $id_admin = $_SESSION['id_admin'] ?? 1;
        insertProduk($pdo, $nama, $deskripsi, $gambar, $link, $id_admin);
        $_SESSION['success'] = "Data produk berhasil **ditambahkan**.";
    }
    
    // Jika sukses, file tidak perlu dihapus. Redirect.
    header("Location: ../../index.php?page=produk"); 
    exit;
} catch (Exception $e) {
    // LANGKAH 3: ROLLBACK FILE FISIK JIKA DB GAGAL
    if ($new_uploaded_filename && is_file($uploadDir . $new_uploaded_filename)) {
        @unlink($uploadDir . $new_uploaded_filename);
    }
    
    // 4. SIMPAN DATA INPUT & PESAN ERROR
    $_SESSION['old_input'] = [
        'nama' => $nama,
        'deskripsi' => $deskripsi,
        'link_produk' => $link,
    ];

    // 5. SIMPAN ID PRODUK JIKA ADA
    if ($id) {
        $_SESSION['edit_id'] = $id; 
    }
    
    // 6. SIMPAN PESAN ERROR
    $_SESSION['error'] = "Gagal menyimpan: " . $e->getMessage();

    // 7. REDIRECT
    header("Location: ../../index.php?page=produk"); 
    exit;
}
?>