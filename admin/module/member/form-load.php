<?php
// admin/module/member/form-load.php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../../../config/koneksi.php'; require_once "model.php";

$scriptName = $_SERVER['SCRIPT_NAME'];
$basePath = substr($scriptName, 0, strpos($scriptName, '/admin/'));
$basePath = rtrim($basePath, '/'); 
$webUploadDir = $basePath . '/public/uploads/member/'; 

$editData = null; $oldInput = []; $error = null;   
$initialSrc = ''; $initialStyle = 'display: none;'; $formData = null; 
require_once __DIR__ . "/form-fields.php"; 
?>