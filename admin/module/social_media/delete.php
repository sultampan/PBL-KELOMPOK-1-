<?php
session_start();

require_once __DIR__ . '/../../../config/koneksi.php';
require_once __DIR__ . '/model.php';

// ===== VALIDASI LOGIN ADMIN =====
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header("Location: ../../login.php");
    exit;
}


// ===== AMBIL ID =====
$id = (int)($_GET['id'] ?? 0);

if ($id <= 0) {
    $_SESSION['error'] = 'ID tidak valid!';
    header("Location: ../../index.php?page=social_media");
    exit;
}

try {
    deleteSocialMedia($pdo, $id);
    $_SESSION['success'] = 'Data berhasil dihapus!';
} catch (PDOException $e) {
    $_SESSION['error'] = 'Gagal menghapus data!';
}

// 🔥 PENTING: BALIK KE ADMIN INDEX
header("Location: ../../index.php?page=social_media");
exit;
