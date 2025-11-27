<?php
// admin/module/produk/table-load.php

if (session_status() === PHP_SESSION_NONE) session_start();

// Ambil semua dependensi yang diperlukan untuk query dan view
require_once __DIR__ . '/../../../config/koneksi.php'; 
require_once "model.php";

// Pastikan Anda mendapatkan variabel dari URL/GET yang dikirim oleh AJAX

// --- Konfigurasi dan Pengambilan Parameter ---
$limit = (int)($_GET['limit'] ?? 10);
$page = (int)($_GET['p'] ?? 1);
$offset = ($page - 1) * $limit;
$searchKeyword = $_GET['keyword'] ?? null;
$currentSortBy = $_GET['sort'] ?? 'id_produk';
$currentSortOrder = $_GET['order'] ?? 'ASC';

// Pastikan $webUploadDir terdefinisi agar gambar muncul
$webUploadDir = '../public/uploads/produk/'; 
// Pastikan path ini sama dengan yang digunakan di index.php

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
// File table.php sudah berisi <div class="card">...</table>
require_once __DIR__ . "/table.php"; 
?>