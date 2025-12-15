<?php
require_once __DIR__ . '/../../../config/koneksi.php';
require_once __DIR__ . '/model.php';

if (isset($_GET['id'])) {
    deleteSocialMedia($pdo, (int)$_GET['id']);
}

header("Location: ../../index.php?page=social_media");
exit;
