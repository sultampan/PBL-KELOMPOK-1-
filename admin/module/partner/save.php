<?php
// admin/module/partner/save.php

ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

ob_start();

if (session_status() === PHP_SESSION_NONE) session_start();

function sendJson($status, $message, $debug = null) {
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

    $uploadDir = __DIR__ . '/../../../public/uploads/partner/';
    $thumbDir  = __DIR__ . '/../../../public/uploads/thumb/partner-thumb/';
    
    if (!is_dir($uploadDir)) @mkdir($uploadDir, 0755, true);
    if (!is_dir($thumbDir)) @mkdir($thumbDir, 0755, true);

    $allowedExt = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $maxSize = 5 * 1024 * 1024; 

    // --- AMBIL DATA FORM ---
    $nama = trim($_POST['nama'] ?? '');
    $kategori = trim($_POST['kategori'] ?? '');
    $id = $_POST['id_partner'] ?? null;
    $oldImg = $_POST['gambar_lama'] ?? null;
    $shouldRemove = ($_POST['remove_existing_image'] ?? '0') === '1';

    // --- AMBIL ID ADMIN DARI SESSION (PENTING) ---
    // Pastikan saat login kamu menyimpan id_admin ke session. 
    // Sesuaikan kuncinya, misal $_SESSION['id_admin'] atau $_SESSION['admin_id']
    $created_by = $_SESSION['id_admin'] ?? $_SESSION['admin_id'] ?? null; 

    if (!$nama || !$kategori) {
        sendJson('error', "Nama dan Kategori wajib diisi.");
    }

    $newSlug = createSlug($nama);
    $gambar = $oldImg; 

    if ($shouldRemove && !empty($oldImg)) {
        if (is_file($uploadDir . $oldImg)) @unlink($uploadDir . $oldImg);
        $thumbName = pathinfo($oldImg, PATHINFO_FILENAME) . '-thumb.' . pathinfo($oldImg, PATHINFO_EXTENSION);
        if (is_file($thumbDir . $thumbName)) @unlink($thumbDir . $thumbName);
        $gambar = null; 
    }

    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] !== UPLOAD_ERR_NO_FILE) {
        $gambar = handleUpload("gambar", $oldImg, $uploadDir, $allowedExt, $maxSize, $newSlug);
    }

    // --- SIMPAN DATA ---
    if ($id) {
        // Update (Biasanya created_by tidak diubah saat update)
        updatePartner($pdo, $id, $nama, $gambar, $kategori);
        sendJson('success', "Data partner berhasil diperbarui.");
    } else {
        // Insert (Masukkan $created_by ke fungsi)
        insertPartner($pdo, $nama, $gambar, $kategori, $created_by);
        sendJson('success', "Partner baru berhasil ditambahkan.");
    }

} catch (Exception $e) {
    sendJson('error', "Gagal menyimpan: " . $e->getMessage());
} catch (Throwable $t) {
    sendJson('error', "Server Error: " . $t->getMessage());
}

ob_end_clean();
?>