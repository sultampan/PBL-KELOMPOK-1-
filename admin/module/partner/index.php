<?php
// admin/module/partner/index.php

$serverUploadDir = __DIR__ . '/../../../public/uploads/partner/'; 
$serverThumbDir = __DIR__ . '/../../../public/uploads/thumb/partner-thumb/';
$webUploadDir = '../public/uploads/partner/'; 
$webThumbDir = '../public/uploads/thumb/partner-thumb/';

require_once "model.php";

// Parameter Filter
$searchKeyword = $_GET['keyword'] ?? null;
$currentSortBy = $_GET['sort'] ?? 'kategori'; // Default sort kategori biar rapi
$currentSortOrder = $_GET['order'] ?? 'ASC';

// [FIX] Panggil fungsi dengan parameter yang benar (Tanpa Limit & Offset)
// Agar Client-Side Pagination bisa bekerja membagi data per kategori
$list = getPartnerAll($pdo, $searchKeyword, $currentSortBy, $currentSortOrder) ?: [];

// Data untuk Edit Form
$editData = null;
if (isset($_GET['edit'])) {
    $id_edit = (int)$_GET['edit'];
    $editData = getPartnerById($pdo, $id_edit);
}
?>

<link rel="stylesheet" href="assets/css/partner.css">
<link rel="stylesheet" href="assets/css/forms.css">
<link rel="stylesheet" href="assets/css/components.css">

<div class="header-title" style="margin-bottom: 20px;"></div>

<?php include __DIR__ . '/form.php'; ?>

<?php include __DIR__ . '/table.php'; ?>

<script src="assets/js/partner.js"></script>