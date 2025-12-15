<?php
// admin/module/activity/table-load.php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../../../config/koneksi.php';
require_once "model.php";

// Setup Path
$scriptName = $_SERVER['SCRIPT_NAME'];
$basePath = substr($scriptName, 0, strpos($scriptName, '/admin/'));
$basePath = rtrim($basePath, '/');
$projectRoot = dirname(__DIR__, 3) . '/';

$serverUploadDir = $projectRoot . 'public/uploads/activity/';
$webUploadDir = $basePath . '/public/uploads/activity/';

// Ambil Keyword
$searchKeyword = $_GET['keyword'] ?? null;

// [PENTING] Panggil fungsi tanpa Limit
$list = getActivityAll($pdo, $searchKeyword);

require_once "table.php";
?>