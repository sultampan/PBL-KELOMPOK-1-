<?php
if (session_status() === PHP_SESSION_NONE) session_start();

require_once __DIR__ . '/../../../config/koneksi.php';
require_once __DIR__ . '/model.php';

$searchKeyword = $_GET['keyword'] ?? null;
$list = insertSocialMedia($pdo, $searchKeyword);

require __DIR__ . '/table.php';
