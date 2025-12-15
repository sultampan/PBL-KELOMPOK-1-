<?php
// VERSI SIMPLE - Tanpa cek session (diasumsikan sudah dicek di index.php utama)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../../config/koneksi.php';
require_once __DIR__ . '/model.php';

$link = trim($_POST['link'] ?? '');
$id   = (int)($_POST['id'] ?? 0);
$adminId = $_SESSION['id_admin'] ?? 1;


// Validasi input
if (empty($link)) {
    $_SESSION['error'] = 'Link tidak boleh kosong!';
    header("Location: ../../index.php?page=social_media");
    exit;
}

// Validasi format URL
if (!filter_var($link, FILTER_VALIDATE_URL)) {
    $_SESSION['error'] = 'Format link tidak valid!';
    header("Location: ../../index.php?page=social_media" . ($id ? "&edit=$id" : ""));
    exit;
}

try {
    if ($id > 0) {
        // Update
        updateSocialMedia($pdo, $id, $link);
        $_SESSION['success'] = 'Data berhasil diupdate!';
    } else {
        // Insert
        insertSocialMedia($pdo, $link, $adminId);
        $_SESSION['success'] = 'Data berhasil disimpan!';
    }
} catch (PDOException $e) {
    $_SESSION['error'] = 'Gagal menyimpan data: ' . $e->getMessage();
}

header("Location: ../../index.php?page=social_media");
exit;