<?php
// admin/module/partner/delete.php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../../../config/koneksi.php';
require_once "model.php";

header('Content-Type: application/json');
$id = $_POST['id'] ?? null;

if ($id) {
    try {
        $data = getPartnerById($pdo, (int)$id);
        if ($data && $data['gambar']) {
            $path = __DIR__ . '/../../../public/uploads/partner/' . $data['gambar'];
            if (is_file($path)) @unlink($path);
            
            $thumb = __DIR__ . '/../../../public/uploads/thumb/partner-thumb/' . pathinfo($data['gambar'], PATHINFO_FILENAME) . '-thumb.' . pathinfo($data['gambar'], PATHINFO_EXTENSION);
            if (is_file($thumb)) @unlink($thumb);
        }
        deletePartner($pdo, (int)$id);
        echo json_encode(['status' => 'success', 'message' => 'Partner berhasil dihapus.']);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'ID Invalid']);
}
?>