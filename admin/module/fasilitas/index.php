<?php
// admin/module/fasilitas/index.php

// 1. DEFINISI PATH (Sama seperti produk, tapi folder tujuannya beda)
// Path Server (untuk pengecekan file_exists di PHP)
$serverUploadDir = __DIR__ . '/../../../public/uploads/fasilitas/'; 
$serverThumbDir = __DIR__ . '/../../../public/uploads/thumb/fasilitas-thumb/';

// Path Web (untuk tag <img src="...">)
// Kita gunakan path relatif dari root admin
$webUploadDir = '../public/uploads/fasilitas/'; 
$webThumbDir = '../public/uploads/thumb/fasilitas-thumb/';

// 2. LOGIKA UTAMA
require_once "model.php";

// A. Paginasi & Pencarian
$page = (int)($_GET['p'] ?? 1);
$limit = 10;
$offset = ($page - 1) * $limit;
$searchKeyword = $_GET['keyword'] ?? null;
$currentSortBy = $_GET['sort'] ?? 'id_galery'; // Default sort ID
$currentSortOrder = $_GET['order'] ?? 'ASC';

// B. Ambil Data
$totalRecords = getTotalFasilitasCount($pdo, $searchKeyword);
$totalPages = ceil($totalRecords / $limit);
$list = getFasilitasAll($pdo, $limit, $offset, $searchKeyword, $currentSortBy, $currentSortOrder) ?: [];

// Data untuk dikirim ke table.php
$paginationData = [
    'currentPage' => $page,
    'totalPages' => $totalPages,
    'searchKeyword' => $searchKeyword,
    'limit' => $limit,
    'currentSortBy' => $currentSortBy,
    'currentSortOrder' => $currentSortOrder,
    'list' => $list
];

// C. Logika Mode Edit
$editData = null;
$oldInput = [];
if (isset($_GET['edit'])) {
    $id_edit = (int)$_GET['edit'];
    $editData = getFasilitasById($pdo, $id_edit);
}
?>

<link rel="stylesheet" href="assets/css/produk.css">

<div class="header-title" style="margin-bottom: 20px;">
    <!-- <h2>Manajemen Fasilitas</h2> -->
</div>

<?php include __DIR__ . '/form.php'; ?>

<?php include __DIR__ . '/table.php'; ?>

<script src="assets/js/fasilitas.js"></script>