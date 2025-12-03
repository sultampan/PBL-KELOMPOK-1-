<?php
// admin/module/member/index.php

$serverUploadDir = __DIR__ . '/../../../public/uploads/member/'; 
$serverThumbDir = __DIR__ . '/../../../public/uploads/thumb/member-thumb/';
$webUploadDir = '../public/uploads/member/'; 
$webThumbDir = '../public/uploads/thumb/member-thumb/';

require_once "model.php";

$page = (int)($_GET['p'] ?? 1);
$limit = 10;
$offset = ($page - 1) * $limit;
$searchKeyword = $_GET['keyword'] ?? null;
$currentSortBy = $_GET['sort'] ?? 'id_member'; 
$currentSortOrder = $_GET['order'] ?? 'ASC';

$totalRecords = getTotalMemberCount($pdo, $searchKeyword);
$totalPages = ceil($totalRecords / $limit);
$list = getMemberAll($pdo, $limit, $offset, $searchKeyword, $currentSortBy, $currentSortOrder) ?: [];

$paginationData = [
    'currentPage' => $page, 'totalPages' => $totalPages, 'searchKeyword' => $searchKeyword,
    'limit' => $limit, 'currentSortBy' => $currentSortBy, 'currentSortOrder' => $currentSortOrder, 'list' => $list
];

$editData = null; $oldInput = [];
if (isset($_GET['edit'])) {
    $editData = getMemberById($pdo, (int)$_GET['edit']);
}
?>

<link rel="stylesheet" href="assets/css/produk.css">

<div class="header-title" style="margin-bottom: 20px;">
</div>

<?php include __DIR__ . '/form.php'; ?>
<?php include __DIR__ . '/table.php'; ?>

<script src="assets/js/member.js"></script>