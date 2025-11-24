<?php
// admin/module/produk/delete.php
if (session_status() === PHP_SESSION_NONE) session_start();

require_once __DIR__ . '/../../config/koneksi.php';

require_once "model.php";

$id = $_GET['id'] ?? null;
if ($id) {
    try {
        $data = getProdukById($pdo, $id);
        if ($data && $data['gambar']) {
            $uploadDir = __DIR__ . '/../../../public/uploads/produk/';
            @unlink($uploadDir . $data['gambar']);
        }

        deleteProduk($pdo, $id);
    } catch (Exception $e) {
        $_SESSION['error'] = "Gagal menghapus: " . $e->getMessage();
    }
}

// Redirect ke admin/index.php?page=produk
header("Location: ../../index.php?page=produk"); 
exit;
?>