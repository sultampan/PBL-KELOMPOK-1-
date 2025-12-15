<?php
// admin/module/activity/index.php

$serverUploadDir = __DIR__ . '/../../../public/uploads/activity/'; 
$serverThumbDir = __DIR__ . '/../../../public/uploads/thumb/activity-thumb/';
$webUploadDir = '../public/uploads/activity/'; 
$webThumbDir = '../public/uploads/thumb/activity-thumb/';

require_once "model.php";

// [PERBAIKAN] Hapus limit & offset agar sesuai strategi Client-Side Pagination
$searchKeyword = $_GET['keyword'] ?? null;

// Ambil SEMUA data (tanpa parameter limit/offset)
// Fungsi getActivityAll di model.php kamu sudah benar (hanya terima $pdo dan $keyword)
$list = getActivityAll($pdo, $searchKeyword) ?: [];

// Cek mode edit
$editData = null; 
if (isset($_GET['edit'])) {
    $editData = getActivityById($pdo, (int)$_GET['edit']);
}
?>

<link rel="stylesheet" href="assets/css/components.css">
<link rel="stylesheet" href="assets/css/forms.css">
<link rel="stylesheet" href="assets/css/activity.css">
<link rel="stylesheet" href="assets/css/activity-simple.css">

<div class="header-title" style="margin-bottom: 20px;">
</div>

<div id="form-content-wrapper">
    <?php include __DIR__ . '/form.php'; ?>
</div>

<?php include __DIR__ . '/table.php'; ?>

<script src="assets/js/activity.js"></script>