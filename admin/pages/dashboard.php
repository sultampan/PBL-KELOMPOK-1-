<?php
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . '/../../config/koneksi.php';

// include modul dashboard
require_once __DIR__ . '/../../admin/module/dashboard/index.php';
