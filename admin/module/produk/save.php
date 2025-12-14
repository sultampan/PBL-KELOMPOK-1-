<?php
// admin/module/produk/save.php

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

// -------------------------------------------
// PATH
// -------------------------------------------
$uploadDir = __DIR__ . '/../../../public/uploads/produk/';
$thumbDir  = __DIR__ . '/../../../public/uploads/thumb/produk-thumb/';
@mkdir($uploadDir, 0755, true);

$allowedExt = ['jpg','jpeg','png','gif','webp'];
$maxSize = 5 * 1024 * 1024;

// -------------------------------------------
// INPUT
// -------------------------------------------
$id          = $_POST['id_produk'] ?? null;
$nama        = trim($_POST['nama'] ?? '');
$deskripsi   = trim($_POST['deskripsi'] ?? '');
$link        = trim($_POST['link_produk'] ?? '');

$member_ids   = $_POST['member_ids'] ?? [];
$member_roles = $_POST['member_roles'] ?? [];

$oldImg = $_POST['gambar_lama'] ?? null;
$removeOld = ($_POST['remove_existing_image'] ?? '0') === '1';

$newSlug = createSlug($nama);
$gambar = $oldImg;

// -------------------------------------------
// VALIDASI AWAL
// -------------------------------------------
if ($nama === '' || $deskripsi === '') {
    sendJson('error', 'Nama produk dan deskripsi wajib diisi!');
}

try {

    // ========================================================================
    // MULAI TRANSAKSI
    // ========================================================================
    $pdo->beginTransaction();

    // ---------------------------------------------------------
    // HANDLE REMOVE GAMBAR LAMA
    // ---------------------------------------------------------
    if ($removeOld && !empty($oldImg)) {

        // Hapus gambar utama
        $file = $uploadDir . $oldImg;
        if (is_file($file)) @unlink($file);

        // Hapus thumbnail
        $ext = pathinfo($oldImg, PATHINFO_EXTENSION);
        $baseName = pathinfo($oldImg, PATHINFO_FILENAME);
        $thumbName = $baseName . '-thumb.' . $ext;

        $thumbPath = $thumbDir . $thumbName;
        if (is_file($thumbPath)) @unlink($thumbPath);

        $gambar = null; // reset ke database
    }

    // ---------------------------------------------------------
    // HANDLE UPLOAD GAMBAR BARU
    // ---------------------------------------------------------
    $uploaded = handleUpload("gambar", $gambar, $uploadDir, $allowedExt, $maxSize, $newSlug);
    if ($uploaded !== $gambar) {
        $gambar = $uploaded;
    }

    // ---------------------------------------------------------
    // INSERT / UPDATE PRODUK
    // ---------------------------------------------------------
    if ($id) {
        updateProduk($pdo, $id, $nama, $deskripsi, $gambar, $link);
        $id_produk_target = $id;
        $msg = "Produk berhasil diperbarui.";
    } else {
        $id_admin = $_SESSION['id_admin'] ?? 1;

        $stmt = $pdo->prepare("
            INSERT INTO produk (nama, deskripsi, gambar, link_produk, created_by)
            VALUES (?, ?, ?, ?, ?)
            RETURNING id_produk
        ");
        $stmt->execute([$nama, $deskripsi, $gambar, $link, $id_admin]);
        $id_produk_target = $stmt->fetchColumn();

        $msg = "Produk berhasil ditambahkan.";
    }

    // ---------------------------------------------------------
    // RESET PRODUK MEMBER
    // ---------------------------------------------------------
    $stmtDel = $pdo->prepare("DELETE FROM produk_member WHERE id_produk = ?");
    $stmtDel->execute([$id_produk_target]);

    // INSERT TIM BARU
    if (!empty($member_ids)) {

        $stmtInsert = $pdo->prepare("
            INSERT INTO produk_member (id_produk, id_member, role)
            VALUES (?, ?, ?)
        ");

        for ($i = 0; $i < count($member_ids); $i++) {

            $mid = $member_ids[$i];
            $role = trim($member_roles[$i]);

            if (!empty($mid) && !empty($role)) {
                $stmtInsert->execute([$id_produk_target, $mid, $role]);
            }
        }
    }

// ========================================================================
// COMMIT & RESPON JSON (SAMA DENGAN ACTIVITY)
// ========================================================================
$pdo->commit();

// 🔥 Perubahan: Hapus logika $extra['redirect'] 🔥
// Kirim respons JSON TANPA instruksi redirect
sendJson('success', $msg); 


} catch (Exception $e) {
    // ... (Logika error tetap sama, tapi HAPUS SEMUA LOGIKA REDIRECT DI SINI) ...
    
    if ($pdo->inTransaction()) $pdo->rollBack();
    sendJson('error', "Terjadi kesalahan: " . $e->getMessage());
}