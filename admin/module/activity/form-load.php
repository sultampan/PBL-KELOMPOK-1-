<?php
// admin/module/activity/form-load.php
if (session_status() === PHP_SESSION_NONE) session_start();

require_once __DIR__ . '/../../../config/koneksi.php'; 
require_once "model.php";

$scriptName = $_SERVER['SCRIPT_NAME'];
$basePath = substr($scriptName, 0, strpos($scriptName, '/admin/'));
$basePath = rtrim($basePath, '/'); 
$webUploadDir = $basePath . '/public/uploads/activity/'; 

// Reset variabel untuk mode tambah
$editData = null; 
$error = null;   
$initialSrc = ''; 
$labelTeks = "Tidak ada file yang dipilih...";
$boxStyle = 'display: none;';

// Panggil form.php yang utama (Bukan form-fields.php)
require_once __DIR__ . "/form.php"; 
?>