<?php
// admin/module/fasilitas/save.php

ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) session_start();

require_once __DIR__ . '/../../../config/koneksi.php';
require_once "model.php";
require_once "upload.php";

function sendJson($status, $message) {
    header('Content-Type: application/json');
    echo json_encode(['status' => $status, 'message' => $message]);
    exit;
}

// 1. Definisi Path
$uploadDir = __DIR__ . '/../../../public/uploads/fasilitas/';
$thumbDir  = __DIR__ . '/../../../public/uploads/thumb/fasilitas-thumb/'; // <--- TAMBAHAN PATH THUMBNAIL
@mkdir($uploadDir, 0755, true);

$allowedExt = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
$maxSize = 5 * 1024 * 1024; 

$judul = trim($_POST['judul'] ?? '');
$deskripsi = trim($_POST['deskripsi'] ?? '');
$id = $_POST['id_galery'] ?? null;
$oldImg = $_POST['gambar_lama'] ?? null;

$newSlug = createSlug($judul);
$new_uploaded_filename = null; 

$should_remove_old_image = ($_POST['remove_existing_image'] ?? '0') === '1';

try {
    // --- LOGIKA HAPUS GAMBAR LAMA + THUMBNAIL ---
    if ($should_remove_old_image && !empty($oldImg)) {
        // A. Hapus File Utama
        $file = $uploadDir . $oldImg;
        if (is_file($file)) @unlink($file);
        
        // B. Hapus Thumbnail (REVISI BUG)
        $ext = pathinfo($oldImg, PATHINFO_EXTENSION);
        $base_name = pathinfo($oldImg, PATHINFO_FILENAME);
        $thumb_name = $base_name . '-thumb.' . $ext;
        $thumb_path = $thumbDir . $thumb_name;
        
        if (is_file($thumb_path)) @unlink($thumb_path);
        
        $oldImg = null;
        $gambar = null; 
    }

    $gambar = handleUpload("gambar", $oldImg, $uploadDir, $allowedExt, $maxSize, $newSlug);

    if ($gambar !== $oldImg) {
        $new_uploaded_filename = $gambar;
    }

    if ($id) {
        updateFasilitas($pdo, $id, $judul, $deskripsi, $gambar);
        sendJson('success', "Data fasilitas berhasil diperbarui.");
    } else {
        $id_admin = $_SESSION['id_admin'] ?? 1;
        insertFasilitas($pdo, $judul, $deskripsi, $gambar, $id_admin);
        sendJson('success', "Fasilitas baru berhasil ditambahkan.");
    }

} catch (Exception $e) {
    if ($new_uploaded_filename && is_file($uploadDir . $new_uploaded_filename)) {
        @unlink($uploadDir . $new_uploaded_filename);
    }
    sendJson('error', "Gagal menyimpan: " . $e->getMessage());
}
?>