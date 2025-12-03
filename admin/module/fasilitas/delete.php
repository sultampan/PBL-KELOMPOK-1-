<?php
// admin/module/fasilitas/delete.php

function sendJson($status, $message) {
    header('Content-Type: application/json');
    echo json_encode(['status' => $status, 'message' => $message]);
    exit;
}

if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../../../config/koneksi.php';
require_once "model.php";

$id = $_POST['id'] ?? null;

if ($id) {
    try {
        $id_int = (int)$id;
        
        // Ambil data dulu untuk tahu nama filenya
        $data = getFasilitasById($pdo, $id_int);
        
        if (!$data) {
             sendJson('error', "Data tidak ditemukan.");
        }

        $original_filename = $data['gambar'] ?? null;

        // --- Definisi Path ---
        $uploadDir = __DIR__ . '/../../../public/uploads/fasilitas/'; 
        $thumbDir  = __DIR__ . '/../../../public/uploads/thumb/fasilitas-thumb/';

        // 1. Hapus File Fisik
        if ($original_filename) {
            // Hapus File Asli
            $original_file_path = $uploadDir . $original_filename;
            if (is_file($original_file_path)) {
                @unlink($original_file_path);
            }

            // Hapus Thumbnail
            $ext = pathinfo($original_filename, PATHINFO_EXTENSION);
            $base_name = pathinfo($original_filename, PATHINFO_FILENAME);
            $thumbnail_filename = $base_name . '-thumb.' . $ext;

            $thumb_file_path = $thumbDir . $thumbnail_filename;
            if (is_file($thumb_file_path)) {
                @unlink($thumb_file_path); 
            }
        }

        // 2. Hapus Database
        deleteFasilitas($pdo, $id_int);

        sendJson('success', "Fasilitas berhasil dihapus.");

    } catch (Exception $e) {
        sendJson('error', "Gagal menghapus: " . $e->getMessage());
    }
} else {
    sendJson('error', "ID tidak valid.");
}
?>