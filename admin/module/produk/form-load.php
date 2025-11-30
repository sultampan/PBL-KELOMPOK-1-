<?php
// admin/module/produk/form-load.php (ENDPOINT AJAX)

if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../../../config/koneksi.php'; 
require_once "model.php";

// --- REPLIKA DEFINISI PATH DINAMIS ---
// Ambil base path URL proyek secara dinamis
$scriptName = $_SERVER['SCRIPT_NAME'];
$basePath = substr($scriptName, 0, strpos($scriptName, '/admin/'));
$basePath = rtrim($basePath, '/'); 

// Path Web File ASLI (URL)
$webUploadDir = $basePath . '/public/uploads/produk/'; 
// --- AKHIR DEFINISI PATH ---


// 1. Tentukan variabel yang dibutuhkan oleh form-fields.php (Mode Tambah Baru/Kosong)
$editData = null; // Tidak ada data edit
$oldInput = [];  // Tidak ada input lama
$error = null;   // Tidak ada error pada form ini

// 2. Ambil pesan sukses dari URL yang dikirim oleh JS
$success = $_GET['success_msg'] ?? null; 

// 3. Hitung variabel turunan yang digunakan form-fields.php
$initialSrc = ''; 
$initialStyle = 'display: none;';
$formData = null; 

// OUTPUT HANYA FORM FIELDS BARU
require_once __DIR__ . "/form-fields.php"; 
?>