<?php
// admin/module/produk/table-load.php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../../../config/koneksi.php'; 
require_once "model.php";

$scriptName = $_SERVER['SCRIPT_NAME'];
$basePath = substr($scriptName, 0, strpos($scriptName, '/admin/'));
$basePath = rtrim($basePath, '/'); 
$projectRoot = dirname(__DIR__, 3) . '/'; 

$serverUploadDir = $projectRoot . 'public/uploads/produk/'; 
$serverThumbDir = $projectRoot . 'public/uploads/thumb/produk-thumb/';
$webUploadDir = $basePath . '/public/uploads/produk/'; 
$webThumbDir = $basePath . '/public/uploads/thumb/produk-thumb/';

$limit = 6; $page = (int)($_GET['p'] ?? 1); $offset = ($page - 1) * $limit;
$searchKeyword = $_GET['keyword'] ?? null;
$currentSortBy = $_GET['sort'] ?? 'id_produk'; $currentSortOrder = $_GET['order'] ?? 'DESC';

$totalRecords = getTotalProdukCount($pdo, $searchKeyword);
$totalPages = ceil($totalRecords / $limit);
$list = getProdukAll($pdo, $limit, $offset, $searchKeyword, $currentSortBy, $currentSortOrder) ?: [];

$paginationData = [
    'currentPage' => $page, 'totalPages' => $totalPages, 'searchKeyword' => $searchKeyword,
    'limit' => $limit, 'currentSortBy' => $currentSortBy, 'currentSortOrder' => $currentSortOrder, 'list' => $list
];
require_once __DIR__ . "/table.php"; 
?>