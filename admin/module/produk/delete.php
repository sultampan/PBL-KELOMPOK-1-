<?php
// admin/module/produk/delete.php

// --- FUNGSI RESPON JSON ---
function sendJson($status, $message)
{
    header('Content-Type: application/json');
    echo json_encode(['status' => $status, 'message' => $message]);
    exit;
}
// --- AKHIR FUNGSI RESPON JSON ---

if (session_status() === PHP_SESSION_NONE) session_start();

ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL & ~E_NOTICE); // Menangkap semua error kecuali notice

require_once __DIR__ . '/../../../config/koneksi.php';
require_once "model.php";

if (!isset($pdo) || !function_exists('deleteProduk')) {
    sendJson('error', 'FATAL ERROR: Koneksi atau Model gagal dimuat. Cek jalur require.');
}

$id = $_POST['id'] ?? null;

if ($id) {
    try {
        $id_int = (int)$id;
        $data = getProdukById($pdo, $id_int);

        // 1. Hapus file foto (jika ada)
        if ($data && $data['gambar']) {
            $uploadDir = __DIR__ . '/../../../public/uploads/produk/';
            if (is_file($uploadDir . $data['gambar'])) {
                @unlink($uploadDir . $data['gambar']);
            }
        }

        // 2. Hapus data dari DB
        deleteProduk($pdo, $id_int);

        // Kirim JSON Sukses
        sendJson('success', "Produk berhasil **dihapus**.");

    } catch (\PDOException $e) {
        // MENANGKAP ERROR DATABASE SECARA SPESIFIK

        $errorMessage = "Database Error: " . $e->getMessage();

        // Kode 23503 adalah standar PostgreSQL untuk Foreign Key Violation
        if ($e->getCode() === '23503') {
            $errorMessage = "Gagal menghapus: Produk ini tidak dapat dihapus karena masih terikat dengan data lain di sistem (misal: digunakan oleh Member atau Activity).";
        } else {
            $errorMessage = "Database Error: " . $e->getMessage();
        }
        sendJson('error', $errorMessage);

    } catch (Exception $e) {
        // Menangkap error PHP umum lainnya (misal file not found)
        sendJson('error', "Internal Error: " . $e->getMessage());
    }
} else {
    sendJson('error', "ID produk tidak valid.");
}
