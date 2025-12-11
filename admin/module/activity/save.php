<?php
// admin/module/activity/save.php
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) session_start();

require_once __DIR__ . '/../../../config/koneksi.php';
require_once "model.php";
require_once "upload.php"; // ← TAMBAHKAN INI

function sendJson($status, $message) {
    header('Content-Type: application/json');
    echo json_encode(['status' => $status, 'message' => $message]);
    exit;
}

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception("Invalid request");
    }

    $judul       = trim($_POST['judul'] ?? '');
    $deskripsi   = trim($_POST['deskripsi'] ?? '');
    $tanggal     = $_POST['tanggal_kegiatan'] ?? null;
    $id          = $_POST['id_activity'] ?? null;
    $member_ids  = $_POST['member_ids'] ?? [];
    $id_admin    = $_SESSION['id_admin'] ?? 1;
    $gambarLama  = $_POST['gambar_lama'] ?? null;

    if ($judul === '') throw new Exception("Judul wajib diisi");
    if (!$tanggal) throw new Exception("Tanggal wajib diisi");

    // PROSES UPLOAD GAMBAR menggunakan handleUpload()
    $serverAdminBase = dirname(__DIR__, 3) . '/';
    $uploadDir = $serverAdminBase . 'public/uploads/activity/';
    
    // Buat folder jika belum ada
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    // Buat slug dari judul untuk nama file
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $judul), '-'));
    
    // Handle upload (akan return nama file baru, atau nama file lama jika tidak ada upload)
    $allowedExt = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $maxSize = 5 * 1024 * 1024; // 5MB
    
    $gambar = handleUpload('gambar', $gambarLama, $uploadDir, $allowedExt, $maxSize, $slug);

    if ($id) {
        // UPDATE ACTIVITY
        updateActivity($pdo, $id, $judul, $deskripsi, $tanggal, $gambar);

        // HAPUS MEMBER LAMA
        $stmt = $pdo->prepare("DELETE FROM activity_member WHERE id_activity = ?");
        $stmt->execute([$id]);

        // INSERT MEMBER BARU
        if (!empty($member_ids)) {
            $stmt = $pdo->prepare("
                INSERT INTO activity_member (id_activity, id_member)
                VALUES (?, ?)
            ");

            foreach ($member_ids as $mid) {
                if ($mid) {
                    $stmt->execute([$id, $mid]);
                }
            }
        }

        sendJson('success', 'Activity berhasil diperbarui');

    } else {
        // INSERT ACTIVITY
        $stmt = $pdo->prepare("
            INSERT INTO activity (judul, deskripsi, tanggal_kegiatan, gambar, created_by)
            VALUES (?, ?, ?, ?, ?)
            RETURNING id_activity
        ");
        $stmt->execute([$judul, $deskripsi, $tanggal, $gambar, $id_admin]);
        $newId = $stmt->fetchColumn();

        // INSERT MEMBER
        if (!empty($member_ids)) {
            $stmt = $pdo->prepare("
                INSERT INTO activity_member (id_activity, id_member)
                VALUES (?, ?)
            ");

            foreach ($member_ids as $mid) {
                if ($mid) {
                    $stmt->execute([$newId, $mid]);
                }
            }
        }

        sendJson('success', 'Activity berhasil disimpan', ['action' => 'insert']);
    }

} catch (Exception $e) {
    sendJson('error', $e->getMessage());
}