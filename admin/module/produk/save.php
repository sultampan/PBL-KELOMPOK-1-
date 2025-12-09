<?php
// admin/module/produk/save.php

// Matikan error display agar JSON aman
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) session_start();

require_once __DIR__ . '/../../../config/koneksi.php';
require_once "model.php";
require_once "upload.php";

function sendJson($status, $message) {
    header('Content-Type: application/json');
    echo json_encode(['status' => $status, 'message' => $message]);
    exit;
}

// 1. Definisi Path
$uploadDir = __DIR__ . '/../../../public/uploads/produk/';
$thumbDir  = __DIR__ . '/../../../public/uploads/thumb/produk-thumb/'; // <--- TAMBAHAN PATH THUMBNAIL
@mkdir($uploadDir, 0755, true);

$allowedExt = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
$maxSize = 5 * 1024 * 1024;

$nama = trim($_POST['nama'] ?? '');
$deskripsi = trim($_POST['deskripsi'] ?? '');
$link = trim($_POST['link_produk'] ?? '');
$id = $_POST['id_produk'] ?? null;
$oldImg = $_POST['gambar_lama'] ?? null;

$newSlug = createSlug($nama);
$new_uploaded_filename = null; 

$should_remove_old_image = ($_POST['remove_existing_image'] ?? '0') === '1';

try {
    // --- LOGIKA HAPUS GAMBAR LAMA + THUMBNAIL ---
    if ($should_remove_old_image && !empty($oldImg)) {
        // A. Hapus File Utama
        $file = $uploadDir . $oldImg;
        if (is_file($file)) @unlink($file);
        
        // B. Hapus Thumbnail (REVISI BUG)
        $ext = pathinfo($oldImg, PATHINFO_EXTENSION);
        $base_name = pathinfo($oldImg, PATHINFO_FILENAME);
        $thumb_name = $base_name . '-thumb.' . $ext;
        $thumb_path = $thumbDir . $thumb_name;
        
        if (is_file($thumb_path)) @unlink($thumb_path);
        
        // Reset variabel DB
        $oldImg = null;
        $gambar = null; 
    }

    $gambar = handleUpload("gambar", $oldImg, $uploadDir, $allowedExt, $maxSize, $newSlug);

    if ($gambar !== $oldImg) {
        $new_uploaded_filename = $gambar;
    }

    if ($id) {
        updateProduk($pdo, $id, $nama, $deskripsi, $gambar, $link);
        sendJson('success', "Data produk berhasil diperbarui.");
    } else {
        $id_admin = $_SESSION['id_admin'] ?? 1;
        insertProduk($pdo, $nama, $deskripsi, $gambar, $link, $id_admin);
        sendJson('success', "Data produk berhasil ditambahkan.");
    }

    // === MULAI TRANSAKSI ===
    $pdo->beginTransaction();

    // 1. SIMPAN DATA PRODUK (TABEL INDUK)
    if ($id) {
        updateProduk($pdo, $id, $nama, $deskripsi, $gambar, $link);
        $id_produk_target = $id; // ID yang sedang diedit
        $msg = "Data produk berhasil diperbarui.";
    } else {
        $id_admin = $_SESSION['id_admin'] ?? 1;
        // insertProduk harus kita ubah sedikit biar mengembalikan ID, 
        // ATAU kita pakai lastInsertId() manual di sini.
        // Asumsi: insertProduk pakai RETURNING id atau kita panggil lastInsertId
        
        // Agar aman, lebih baik query insert manual disini biar dapat ID-nya langsung:
        $stmt = $pdo->prepare("INSERT INTO produk (nama, deskripsi, gambar, link_produk, created_by) 
                               VALUES (?, ?, ?, ?, ?) RETURNING id_produk");
        $stmt->execute([$nama, $deskripsi, $gambar, $link, $id_admin]);
        $id_produk_target = $stmt->fetchColumn(); 
        
        $msg = "Data produk berhasil ditambahkan.";
    }

    // 2. SIMPAN DATA MEMBER (TABEL ANAK)
    
    // A. Hapus data lama (Reset member untuk produk ini)
    $stmtDel = $pdo->prepare("DELETE FROM produk_member WHERE id_produk = ?");
    $stmtDel->execute([$id_produk_target]);

    // B. Insert data baru dari form
    $member_ids   = $_POST['member_ids'] ?? [];
    $member_roles = $_POST['member_roles'] ?? [];

    if (!empty($member_ids) && is_array($member_ids)) {
        $sqlInsert = "INSERT INTO produk_member (id_produk, id_member, role) VALUES (?, ?, ?)";
        $stmtInsert = $pdo->prepare($sqlInsert);

        for ($i = 0; $i < count($member_ids); $i++) {
            $m_id = $member_ids[$i];
            $role = trim($member_roles[$i]);

            // Hanya simpan jika Member dipilih dan Role diisi
            if (!empty($m_id) && !empty($role)) {
                $stmtInsert->execute([$id_produk_target, $m_id, $role]);
            }
        }
    }

    // === COMMIT TRANSAKSI ===
    $pdo->commit();
    sendJson('success', $msg);
} catch (Exception $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    // ... (Error handling file upload SAMA) ...
    sendJson('error', "Gagal menyimpan: " . $e->getMessage());
}
?>