<?php
// admin/module/fasilitas/save.php
if (session_status() === PHP_SESSION_NONE) session_start();

require_once __DIR__ . '/../../../config/koneksi.php';
require_once "model.php";
require_once "upload.php";

function sendJson($status, $message) {
    header('Content-Type: application/json');
    echo json_encode(['status' => $status, 'message' => $message]);
    exit;
}

// Path folder upload (naik 3 level ke root admin, lalu ke public)
$uploadDir = __DIR__ . '/../../../public/uploads/fasilitas/';
@mkdir($uploadDir, 0755, true);

$allowedExt = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
$maxSize = 5 * 1024 * 1024; // 5MB

// 1. Ambil Input (sesuai tabel fasilitas)
$judul = trim($_POST['judul'] ?? '');
$deskripsi = trim($_POST['deskripsi'] ?? '');
// id_galery adalah Primary Key
$id = $_POST['id_galery'] ?? null; 
$oldImg = $_POST['gambar_lama'] ?? null;

// Buat slug dari JUDUL
$newSlug = createSlug($judul);
$new_uploaded_filename = null; 

$should_remove_old_image = ($_POST['remove_existing_image'] ?? '0') === '1';

try {
    // 2. Hapus Gambar Lama Jika Diminta
    if ($should_remove_old_image && !empty($oldImg)) {
        $file = $uploadDir . $oldImg;
        if (is_file($file)) @unlink($file);
        
        $oldImg = null;
        $gambar = null; 
    }

    // 3. Proses Upload
    // Parameter pertama "gambar" harus sesuai name di <input type="file" name="gambar">
    $gambar = handleUpload("gambar", $oldImg, $uploadDir, $allowedExt, $maxSize, $newSlug);

    if ($gambar !== $oldImg) {
        $new_uploaded_filename = $gambar;
    }

    // 4. Simpan ke Database
    if ($id) {
        // UPDATE
        updateFasilitas($pdo, $id, $judul, $deskripsi, $gambar);
        sendJson('success', "Data fasilitas berhasil diperbarui.");
    } else {
        // INSERT
        $id_admin = $_SESSION['id_admin'] ?? 1; // Default 1 jika session kosong (dev mode)
        insertFasilitas($pdo, $judul, $deskripsi, $gambar, $id_admin);
        sendJson('success', "Fasilitas baru berhasil ditambahkan.");
    }

} catch (Exception $e) {
    // Rollback: Hapus file fisik jika DB gagal
    if ($new_uploaded_filename && is_file($uploadDir . $new_uploaded_filename)) {
        @unlink($uploadDir . $new_uploaded_filename);
    }
    sendJson('error', "Gagal menyimpan: " . $e->getMessage());
}
?>