<?php
// admin/module/contact/fetch_history.php

// 1. Koneksi Database
require_once '../../../config/koneksi.php'; 

// 2. Cek Parameter ID
if (!isset($_GET['id'])) {
    echo json_encode([]);
    exit;
}

$id = (int)$_GET['id'];

try {
    // 3. Query Data Balasan
    $stmt = $pdo->prepare("
        SELECT r.*, a.username as nama_admin 
        FROM contact_reply r 
        LEFT JOIN admin a ON r.id_admin = a.id_admin 
        WHERE r.id_contact = ? 
        ORDER BY r.tanggal_balasan DESC
    ");
    $stmt->execute([$id]);
    $history = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // 4. Return JSON Murni
    header('Content-Type: application/json');
    echo json_encode($history);

} catch (Exception $e) {
    // Jika error, kirim status 500
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>