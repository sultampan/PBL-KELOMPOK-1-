<?php
// admin/module/produk/form-load.php (ENDPOINT AJAX)

if (session_status() === PHP_SESSION_NONE) session_start();

require_once __DIR__ . '/../../../config/koneksi.php'; 
require_once "model.php";
require_once "upload.php"; // Mungkin diperlukan untuk createSlug, dll.

// Set variabel yang dibutuhkan oleh form-fields.php ke nilai default 'Tambah Baru'
$editData = null; 
$oldInput = [];
$error = null;
$success = $_GET['success_msg'] ?? null; // Ambil pesan sukses dari URL jika ada
$webUploadDir = '../public/uploads/produk/'; 

// Hitung variabel turunan
$initialSrc = ''; // Form baru, tidak ada gambar lama
$initialStyle = 'display: none;';
$formData = null; // Form baru, datanya kosong

// OUTPUT HANYA FORM FIELDS BARU
require_once __DIR__ . "/form-fields.php"; 
?>