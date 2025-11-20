<?php
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

// koneksi
require_once __DIR__ . '/../../config/koneksi.php';

// arahkan ke modul produk
require_once __DIR__ . '/../../admin/module/produk/index.php';