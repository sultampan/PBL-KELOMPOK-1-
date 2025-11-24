<?php
if (session_status() === PHP_SESSION_NONE) session_start();

// Memuat koneksi DB
require_once __DIR__ . '/../../../config/koneksi.php'; 

require_once "model.php";

$uploadDir = __DIR__ . '/../../../public/uploads/produk/';
$webUploadDir = '../public/uploads/produk/'; 

// Session error
$error = $_SESSION['error'] ?? null;
unset($_SESSION['error']);

// Session success
$success = $_SESSION['success'] ?? null;
unset($_SESSION['success']);

// AMBIL DATA INPUT LAMA DARI SESSION
$oldInput = $_SESSION['old_input'] ?? [];
unset($_SESSION['old_input']);

$editIdSession = $_SESSION['edit_id'] ?? null;
unset($_SESSION['edit_id']); // Bersihkan ID dari Session

// DELETE
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    try {
        $data = getProdukById($pdo, $id);
        if ($data && $data['gambar']) @unlink($uploadDir.$data['gambar']);
        deleteProduk($pdo, $id);
        $_SESSION['success'] = "Produk berhasil **dihapus**.";
    } catch (Exception $e) {
        // Tangani jika delete gagal
        $_SESSION['error'] = "Gagal menghapus produk: " . $e->getMessage();
    }
    // Redirect ke admin/index.php?page=produk
    header("Location: index.php?page=produk"); 
    exit;
}

// LOGIKA EDIT DATA (PRIORITAS: 1. ID dari Session, 2. ID dari GET)
$idToEdit = $_GET['edit'] ?? $editIdSession; // Ambil ID dari GET atau dari Session setelah error
$editData = $idToEdit ? getProdukById($pdo, (int)$idToEdit) : null;

// LIST PRODUK
$list = getProdukAll($pdo) ?: [];

// TAMPILKAN VIEW & TABLE
require_once "form.php";
require_once "table.php";
?>