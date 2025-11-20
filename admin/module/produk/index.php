<?php
if (session_status() === PHP_SESSION_NONE) session_start();

// Memuat koneksi DB
require __DIR__ . '/../../../config/koneksi.php'; 

require "model.php";

$uploadDir = __DIR__ . '/../../../public/uploads/produk/';
$webUploadDir = '../public/uploads/produk/'; 

$error = $_SESSION['error'] ?? null;
unset($_SESSION['error']);

// DELETE
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    try {
        $data = getProdukById($pdo, $id);
        if ($data && $data['gambar']) @unlink($uploadDir.$data['gambar']);
        deleteProduk($pdo, $id);
    } catch (Exception $e) {
        // Tangani jika delete gagal
        $_SESSION['error'] = "Gagal menghapus produk: " . $e->getMessage();
    }
    // Redirect ke admin/index.php?page=produk
    header("Location: index.php?page=produk"); 
    exit;
}

// EDIT DATA
$editData = isset($_GET['edit']) ? getProdukById($pdo, (int)$_GET['edit']) : null;

// LIST PRODUK
$list = getProdukAll($pdo) ?: [];

// TAMPILKAN VIEW & TABLE
require_once "form.php";
require_once "table.php";
?>