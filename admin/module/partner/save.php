<?php
// admin/module/partner/save.php

// 1. Matikan tampilan error text agar tidak merusak JSON
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

// 2. Mulai Output Buffering (Menahan output apa pun)
ob_start();

if (session_status() === PHP_SESSION_NONE) session_start();

// Helper untuk respons JSON bersih
function sendJson($status, $message, $debug = null) {
    // Bersihkan semua output (warning/html) yang tersimpan di buffer sebelumnya
    ob_clean(); 
    
    header('Content-Type: application/json');
    echo json_encode([
        'status' => $status, 
        'message' => $message,
        'debug' => $debug
    ]);
    exit;
}

try {
    require_once __DIR__ . '/../../../config/koneksi.php';
    require_once "model.php";
    require_once "upload.php";

    // 1. Definisi Path
    $uploadDir = __DIR__ . '/../../../public/uploads/partner/';
    $thumbDir  = __DIR__ . '/../../../public/uploads/thumb/partner-thumb/';
    
    // Pastikan folder ada
    if (!is_dir($uploadDir)) @mkdir($uploadDir, 0755, true);
    if (!is_dir($thumbDir)) @mkdir($thumbDir, 0755, true);

    $allowedExt = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $maxSize = 5 * 1024 * 1024; // 5MB

    // 2. Ambil Data
    $nama = trim($_POST['nama'] ?? '');
    $kategori = trim($_POST['kategori'] ?? '');
    $id = $_POST['id_partner'] ?? null;
    $oldImg = $_POST['gambar_lama'] ?? null;
    $shouldRemove = ($_POST['remove_existing_image'] ?? '0') === '1';

    if (!$nama || !$kategori) {
        sendJson('error', "Nama dan Kategori wajib diisi.");
    }

    $newSlug = createSlug($nama);
    $gambar = $oldImg; // Default gambar lama

    // 3. Logic Hapus Gambar Lama (Jika diminta user)
    if ($shouldRemove && !empty($oldImg)) {
        if (is_file($uploadDir . $oldImg)) @unlink($uploadDir . $oldImg);
        
        $thumbName = pathinfo($oldImg, PATHINFO_FILENAME) . '-thumb.' . pathinfo($oldImg, PATHINFO_EXTENSION);
        if (is_file($thumbDir . $thumbName)) @unlink($thumbDir . $thumbName);
        
        $gambar = null; // Gambar jadi kosong
    }

    // 4. Logic Upload Gambar Baru
    // Cek apakah ada file yang diupload?
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] !== UPLOAD_ERR_NO_FILE) {
        $gambar = handleUpload("gambar", $oldImg, $uploadDir, $allowedExt, $maxSize, $newSlug);
    }

    // 5. Simpan ke Database
    if ($id) {
        updatePartner($pdo, $id, $nama, $gambar, $kategori);
        sendJson('success', "Data partner berhasil diperbarui.");
    } else {
        insertPartner($pdo, $nama, $gambar, $kategori);
        sendJson('success', "Partner baru berhasil ditambahkan.");
    }

} catch (Exception $e) {
    // Tangkap error logic
    sendJson('error', "Gagal menyimpan: " . $e->getMessage());
} catch (Throwable $t) {
    // Tangkap error fatal coding (typo dll)
    sendJson('error', "Server Error: " . $t->getMessage());
}

// Jika script tembus sampai sini tanpa exit, bersihkan buffer juga
ob_end_clean();
?>