<?php
session_start();
require_once __DIR__ . '/../../../config/koneksi.php';
require_once __DIR__ . '/model.php';

$data = [
    'nama'       => $_POST['nama'],
    'icon'       => $_POST['icon'] ?? null,
    'link'       => $_POST['link'],
    'tipe_icon'  => 'font',
    'is_active'  => ($_POST['is_active'] == '1'), // ✅ BOOLEAN
    'urutan'     => (int)($_POST['urutan'] ?? 0),
    'created_by' => $_SESSION['id_admin'] ?? null
];

if (!empty($_POST['id'])) {
    updateSocialMedia($pdo, (int)$_POST['id'], $data);
} else {
    insertSocialMedia($pdo, $data);
}

header("Location: ../../index.php?page=social_media");
exit;
