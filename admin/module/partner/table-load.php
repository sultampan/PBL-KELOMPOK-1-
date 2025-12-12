<?php
// admin/module/partner/table-load.php

if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../../../config/koneksi.php'; 
require_once "model.php";

// --- REPLIKA PATH (Agar table.php bisa merender gambar) ---
// Kita harus definisikan ulang karena file ini dipanggil terpisah oleh AJAX
$scriptName = $_SERVER['SCRIPT_NAME'];
$basePath = substr($scriptName, 0, strpos($scriptName, '/admin/'));
$basePath = rtrim($basePath, '/'); 
$projectRoot = dirname(__DIR__, 3) . '/'; 

$serverUploadDir = $projectRoot . 'public/uploads/partner/'; 
$serverThumbDir = $projectRoot . 'public/uploads/thumb/partner-thumb/';
$webUploadDir = $basePath . '/public/uploads/partner/'; 
$webThumbDir = $basePath . '/public/uploads/thumb/partner-thumb/';
// --- END PATH ---

$limit = 10;
$page = (int)($_GET['p'] ?? 1);
$offset = ($page - 1) * $limit;
$searchKeyword = $_GET['keyword'] ?? null;
$currentSortBy = $_GET['sort'] ?? 'id_partner';
$currentSortOrder = $_GET['order'] ?? 'ASC';

$totalRecords = getTotalPartnerCount($pdo, $searchKeyword);
$totalPages = ceil($totalRecords / $limit);
$list = getPartnerAll($pdo, $limit, $offset, $searchKeyword, $currentSortBy, $currentSortOrder) ?: [];

$paginationData = [
    'currentPage' => $page,
    'totalPages' => $totalPages,
    'searchKeyword' => $searchKeyword,
    'limit' => $limit,
    'currentSortBy' => $currentSortBy,
    'currentSortOrder' => $currentSortOrder,
    'list' => $list
];

require_once __DIR__ . "/table.php"; 
?>