<?php
// admin/module/produk/save.php
if (session_status() === PHP_SESSION_NONE) session_start();

require_once __DIR__ . '/../../../config/koneksi.php';

require_once "model.php";
require_once "upload.php";

function sendJson($status, $message)
{
    header('Content-Type: application/json'); // Penting: memberitahu browser bahwa ini adalah JSON
    echo json_encode(['status' => $status, 'message' => $message]);
    exit;
}

$uploadDir = __DIR__ . '/../../../public/uploads/produk/';
@mkdir($uploadDir, 0755, true);

$allowedExt = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
$maxSize = 5 * 1024 * 1024;

$nama = trim($_POST['nama'] ?? '');
$deskripsi = trim($_POST['deskripsi'] ?? '');
$link = trim($_POST['link_produk'] ?? '');
$id = $_POST['id_produk'] ?? null;
$oldImg = $_POST['gambar_lama'] ?? null;

// Fungsi createSlug ada di model.php
$newSlug = createSlug($nama);
$new_uploaded_filename = null; // Variabel untuk menyimpan nama file yang baru di-upload

// Ambil status penghapusan
$should_remove_old_image = ($_POST['remove_existing_image'] ?? '0') === '1';

try {

    // 🚨 Logika 1: Hapus Gambar Lama Jika Tombol X Ditekan
    if ($should_remove_old_image && !empty($oldImg)) {
        $file = $uploadDir . $oldImg;
        if (is_file($file)) @unlink($file);
        
        // Atur $oldImg dan $gambar menjadi kosong agar DB juga bersih
        $oldImg = null;
        $gambar = null; 
    }

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
        sendJson('success', "Data produk berhasil **diperbarui**.");
    } else {
        $id_admin = $_SESSION['id_admin'] ?? 1;
        insertProduk($pdo, $nama, $deskripsi, $gambar, $link, $id_admin);
        sendJson('success', "Data produk berhasil **ditambahkan**.");
    }
    // MODE DEBUG: Redirect PHP Standar
    header("Location: ../../index.php?page=produk");
    exit;
} catch (Exception $e) {
    // LANGKAH 3: ROLLBACK FILE FISIK JIKA DB GAGAL
    if ($new_uploaded_filename && is_file($uploadDir . $new_uploaded_filename)) {
        // MODE DEBUG: Simpan error ke Session dan Redirect
    $_SESSION['error'] = "Gagal menyimpan: " . $e->getMessage();
    header("Location: ../../index.php?page=produk"); 
    exit;
    }

    // 6. SIMPAN PESAN ERROR
    sendJson('error', "Gagal menyimpan: " . $e->getMessage());
    exit;
}
