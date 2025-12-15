<?php
// admin/module/produk/index.php

require_once "model.php";

$scriptName = $_SERVER['SCRIPT_NAME'];
$basePath = substr($scriptName, 0, strpos($scriptName, '/admin/'));
$basePath = rtrim($basePath, '/'); 
$projectRoot = dirname(__DIR__, 3) . '/'; 

$serverUploadDir = $projectRoot . 'public/uploads/produk/'; 
$serverThumbDir = $projectRoot . 'public/uploads/thumb/produk-thumb/';
$webUploadDir = $basePath . '/public/uploads/produk/'; 
$webThumbDir = $basePath . '/public/uploads/thumb/produk-thumb/';

// A. Logika Utama
$page = (int)($_GET['p'] ?? 1);
$limit = 6;
$offset = ($page - 1) * $limit;
$searchKeyword = $_GET['keyword'] ?? null;
$currentSortBy = $_GET['sort'] ?? 'id_produk';
$currentSortOrder = $_GET['order'] ?? 'DESC'; 

// B. Ambil Data
$totalRecords = getTotalProdukCount($pdo, $searchKeyword);
$totalPages = ceil($totalRecords / $limit);
$list = getProdukAll($pdo, $limit, $offset, $searchKeyword, $currentSortBy, $currentSortOrder) ?: [];

// Data untuk table.php
$paginationData = [
    'currentPage' => $page,
    'totalPages' => $totalPages,
    'searchKeyword' => $searchKeyword,
    'limit' => $limit,
    'currentSortBy' => $currentSortBy,
    'currentSortOrder' => $currentSortOrder,
    'list' => $list
];

// C. Mode Edit
$editData = null;
$oldInput = [];
if (isset($_GET['edit'])) {
    $id_edit = (int)$_GET['edit'];
    $editData = getProdukById($pdo, $id_edit);
}
?>

<link rel="stylesheet" href="assets/css/produk.css">
<link rel="stylesheet" href="assets/css/forms.css">
<link rel="stylesheet" href="assets/css/components.css">

<div class="header-title" style="margin-bottom: 20px;">
</div>

<?php include __DIR__ . '/form.php'; ?>

<div class="produk-grid-container">
    
    <div id="product-list-container">
        <?php include __DIR__ . '/table.php'; ?>
    </div>

</div>

<script src="assets/js/produk.js"></script>