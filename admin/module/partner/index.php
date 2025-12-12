<?php
// admin/module/partner/index.php

$serverUploadDir = __DIR__ . '/../../../public/uploads/partner/'; 
$serverThumbDir = __DIR__ . '/../../../public/uploads/thumb/partner-thumb/';
$webUploadDir = '../public/uploads/partner/'; 
$webThumbDir = '../public/uploads/thumb/partner-thumb/';

require_once "model.php";

$page = (int)($_GET['p'] ?? 1);
$limit = 10;
$offset = ($page - 1) * $limit;
$searchKeyword = $_GET['keyword'] ?? null;
$currentSortBy = $_GET['sort'] ?? 'id_partner';
$currentSortOrder = $_GET['order'] ?? 'ASC';

$totalRecords = getTotalPartnerCount($pdo, $searchKeyword);
$totalPages = ceil($totalRecords / $limit);
$list = getPartnerAll($pdo, $limit, $offset, $searchKeyword, $currentSortBy, $currentSortOrder) ?: [];

$paginationData = [
    'currentPage' => $page, 'totalPages' => $totalPages,
    'searchKeyword' => $searchKeyword, 'limit' => $limit,
    'list' => $list
];

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