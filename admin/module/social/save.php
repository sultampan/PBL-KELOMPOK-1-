<?php
// admin/module/social/save.php
ini_set('display_errors', 0); error_reporting(E_ALL);
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../../../config/koneksi.php';
require_once "model.php";

// Helper Return JSON
function sendJson($status, $message) {
    header('Content-Type: application/json');
    echo json_encode(['status' => $status, 'message' => $message]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo->beginTransaction();

        $links = $_POST['links'] ?? [];
        $actives = $_POST['active'] ?? [];

        foreach ($links as $id => $url) {
            // Cek checkbox
            $isActive = isset($actives[$id]) ? 'true' : 'false';
            
            // Validasi sederhana (opsional)
            $url = trim($url);
            
            updateSocialLink($pdo, $id, $url, $isActive);
        }

        $pdo->commit();
        sendJson('success', "Link social media berhasil diperbarui!");

    } catch (Exception $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        sendJson('error', "Gagal menyimpan: " . $e->getMessage());
    }
} else {
    sendJson('error', "Metode request tidak valid.");
}
?>