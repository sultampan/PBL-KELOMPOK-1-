<?php
// admin/module/partner/table-load.php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../../../config/koneksi.php'; 
require_once "model.php"; 

// Setup Path
$scriptName = $_SERVER['SCRIPT_NAME'];
$basePath = substr($scriptName, 0, strpos($scriptName, '/admin/'));
$basePath = rtrim($basePath, '/'); 
$projectRoot = dirname(__DIR__, 3) . '/'; 

$serverUploadDir = $projectRoot . 'public/uploads/partner/'; 
$serverThumbDir = $projectRoot . 'public/uploads/thumb/partner-thumb/';
$webUploadDir = $basePath . '/public/uploads/partner/'; 
$webThumbDir = $basePath . '/public/uploads/thumb/partner-thumb/';

// Ambil Parameter (Limit diabaikan karena kita pakai JS Pagination per Kategori)
$searchKeyword = $_GET['keyword'] ?? null;
$currentSortBy = $_GET['sort'] ?? 'nama'; 
$currentSortOrder = $_GET['order'] ?? 'ASC';

// Ambil SEMUA data
$list = getPartnerAll($pdo, $searchKeyword, $currentSortBy, $currentSortOrder);

require_once "table.php"; 
?>