<?php
// admin/module/fasilitas/index.php

// 1. LOGIKA UTAMA (PHP)
require_once "model.php";

// A. Paginasi Awal
$page = (int)($_GET['p'] ?? 1);
$limit = 6;
$offset = ($page - 1) * $limit;
$searchKeyword = $_GET['keyword'] ?? null;
$currentSortBy = $_GET['sort'] ?? 'id_fasilitas';
$currentSortOrder = $_GET['order'] ?? 'ASC';

// B. Ambil Data
$totalRecords = getTotalFasilitasCount($pdo, $searchKeyword);
$totalPages = ceil($totalRecords / $limit);
$list = getFasilitasAll($pdo, $limit, $offset, $searchKeyword, $currentSortBy, $currentSortOrder) ?: [];

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
    $editData = getFasilitasById($pdo, $id_edit);
}
?>

<link rel="stylesheet" href="assets/css/fasilitas.css">
<link rel="stylesheet" href="assets/css/forms.css">
<link rel="stylesheet" href="assets/css/components.css">

<div class="header-title" style="margin-bottom: 20px;">
    </div>

<?php include __DIR__ . '/form.php'; ?>


<div class="fasilitas-grid-container">
    
    <div class="toolbar-header">
        <h3 class="header-title">Daftar Fasilitas</h3>

        <div class="search-form">
            <div class="search-group">
                <input type="text" id="searchFasilitasInput" class="search-input" 
                       placeholder="Cari fasilitas..." 
                       value="<?= htmlspecialchars($searchKeyword ?? '') ?>"
                       autocomplete="off">
            </div>
        </div>
    </div>

    <div id="fasilitas-data-content">
        <?php include __DIR__ . '/table.php'; ?>
    </div>

</div>

<script src="assets/js/fasilitas.js"></script>