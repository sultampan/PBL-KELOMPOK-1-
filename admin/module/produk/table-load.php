<?php
// admin/module/produk/table-load.php

if (session_status() === PHP_SESSION_NONE) session_start();

// Ambil semua dependensi yang diperlukan untuk query dan view
require_once __DIR__ . '/../../../config/koneksi.php'; 
require_once "model.php";

// --- DEFINISI PATH UNIVERSAL (REPLIKA DARI produk/index.php) ---
$scriptName = $_SERVER['SCRIPT_NAME'];
$basePath = substr($scriptName, 0, strpos($scriptName, '/admin/'));
$basePath = rtrim($basePath, '/'); 
$projectRoot = dirname(__DIR__, 3) . '/'; 

$serverUploadDir = $projectRoot . 'public/uploads/produk/'; 
$serverThumbDir = $projectRoot . 'public/uploads/thumb/produk-thumb/';
$webUploadDir = $basePath . '/public/uploads/produk/'; 
$webThumbDir = $basePath . '/public/uploads/thumb/produk-thumb/';
// --- AKHIR DEFINISI PATH UNIVERSAL ---

// --- Konfigurasi dan Pengambilan Parameter ---
$limit = (int)($_GET['limit'] ?? 10);
$page = (int)($_GET['p'] ?? 1);
$offset = ($page - 1) * $limit;
$searchKeyword = $_GET['keyword'] ?? null;
$currentSortBy = $_GET['sort'] ?? 'id_produk';
$currentSortOrder = $_GET['order'] ?? 'ASC';

// // --- DEFINISI PATH BARU ---

// // Path Web untuk file ASLI (public/uploads/produk/)
// $webUploadDir = '../public/uploads/produk/'; 
// // Path Web untuk file THUMBNAIL (admin/uploads/produk-thumb/)
// $webThumbDir = '../public/uploads/thumb/produk-thumb/'; 

// // Path Server untuk pengecekan is_file()
// $serverUploadDir = __DIR__ . '/../../../public/uploads/produk/'; 
// $serverThumbDir = __DIR__ . '/../../../public/uploads/thumb/produk-thumb/';

// --- Ambil Data Terbaru ---
$totalRecords = getTotalProdukCount($pdo, $searchKeyword);
$totalPages = ceil($totalRecords / $limit);
$list = getProdukAll($pdo, $limit, $offset, $searchKeyword, $currentSortBy, $currentSortOrder) ?: [];

// --- Siapkan data paginasi untuk table.php ---
$paginationData = [
    'currentPage' => $page,
    'totalPages' => $totalPages,
    'searchKeyword' => $searchKeyword,
    'limit' => $limit,
    'currentSortBy' => $currentSortBy,
    'currentSortOrder' => $currentSortOrder,
];

// Output HANYA HTML TABEL
require_once __DIR__ . "/table.php"; 
?>