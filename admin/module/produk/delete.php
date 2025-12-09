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
error_reporting(E_ALL & ~E_NOTICE); 

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
        $original_filename = $data['gambar'] ?? null;

        // --- DEFINISI PATH ---
        $uploadDir = __DIR__ . '/../../../public/uploads/produk/'; // Lokasi File ASLI
        // Dari admin/module/produk/ naik 2 tingkat (ke admin/) lalu masuk ke uploads/produk-thumb/
        $thumbDir = __DIR__ . '/../../../public/uploads/thumb/produk-thumb/'; // lokasi thumbnail
        // --- END DEFINISI PATH ---


        // 1. Hapus file foto ASLI (jika ada)
        if ($data && $original_filename) {
            $original_file_path = $uploadDir . $original_filename;

            if (is_file($original_file_path)) {
                @unlink($original_file_path);
            }

            // 2. Hapus file THUMBNAIL terkait (jika ada)
            $ext = pathinfo($original_filename, PATHINFO_EXTENSION);
            $base_name = pathinfo($original_filename, PATHINFO_FILENAME);
            $thumbnail_filename = $base_name . '-thumb.' . $ext;

            $thumb_file_path = $thumbDir . $thumbnail_filename;
            
            if (is_file($thumb_file_path)) {
                @unlink($thumb_file_path); // 🚨 Menghapus thumbnail dari admin/uploads/produk-thumb/
            }
        }

        // 3. Hapus data dari DB
        deleteProduk($pdo, $id_int);

        // Kirim JSON Sukses
        sendJson('success', "Produk berhasil dihapus.");

    } catch (\PDOException $e) {
        // MENANGKAP ERROR DATABASE SECARA SPESIFIK
        $errorMessage = "Database Error: " . $e->getMessage();

        // Kode 23503 adalah standar PostgreSQL untuk Foreign Key Violation
        if ($e->getCode() === '23503') {
            $errorMessage = "Gagal menghapus: Produk ini tidak dapat dihapus karena masih terikat dengan data lain di sistem.";
        } else {
            $errorMessage = "Database Error: " . $e->getMessage();
        }
        sendJson('error', $errorMessage);

    } catch (Exception $e) {
        // Menangkap error PHP umum lainnya
        sendJson('error', "Internal Error: " . $e->getMessage());
    }
} else {
    sendJson('error', "ID produk tidak valid.");
}