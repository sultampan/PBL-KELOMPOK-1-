<?php
// admin/module/produk/table-load.php

if (session_status() === PHP_SESSION_NONE) session_start();

// Ambil semua dependensi
require_once __DIR__ . '/../../../config/koneksi.php'; 
require_once "model.php";

// --- PATH BERDASARKAN SISTEM LAMA ---
$scriptName = $_SERVER['SCRIPT_NAME'];
$basePath = substr($scriptName, 0, strpos($scriptName, '/admin/'));
$basePath = rtrim($basePath, '/'); 
$projectRoot = dirname(__DIR__, 3) . '/'; 

$serverUploadDir = $projectRoot . 'public/uploads/produk/'; 
$serverThumbDir  = $projectRoot . 'public/uploads/thumb/produk-thumb/';

$webUploadDir  = $basePath . '/public/uploads/produk/'; 
$webThumbDir   = $basePath . '/public/uploads/thumb/produk-thumb/';

// --- GET PARAMETER ---
$limit  = (int)($_GET['limit'] ?? 10);
$page   = (int)($_GET['p'] ?? 1);
$offset = ($page - 1) * $limit;

$searchKeyword    = $_GET['keyword'] ?? null;
$currentSortBy    = $_GET['sort'] ?? 'id_produk';
$currentSortOrder = $_GET['order'] ?? 'ASC';

// --- GET DATA PRODUK ---
$totalRecords = getTotalProdukCount($pdo, $searchKeyword);
$totalPages   = ceil($totalRecords / $limit);

$list = getProdukAll($pdo, $limit, $offset, $searchKeyword, $currentSortBy, $currentSortOrder) ?: [];

// ====================================================================
// 🔥 AMBIL TEAM / ANGGOTA PRODUK SETIAP PRODUK
// ====================================================================
foreach ($list as &$row) {
    $row['team'] = getTeamByProduk($pdo, $row['id_produk']); 
}
// ====================================================================

// --- Siapkan data paginasi untuk table.php ---
$paginationData = [
    'currentPage' => $page,
    'totalPages'  => $totalPages,
    'searchKeyword' => $searchKeyword,
    'limit' => $limit,
    'currentSortBy' => $currentSortBy,
    'currentSortOrder' => $currentSortOrder,
];

// Output HTML tabel
require_once __DIR__ . "/table.php";
