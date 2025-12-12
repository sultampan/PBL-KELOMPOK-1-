<?php
// admin/module/partner/model.php
if (session_status() === PHP_SESSION_NONE) session_start();

function checkPdo($pdo) {
    if (!$pdo instanceof PDO) throw new Exception("Koneksi database bermasalah.");
}

function getPartnerAll($pdo, $limit, $offset, $keyword = null, $sortBy = 'id_partner', $sortOrder = 'ASC') { 
    checkPdo($pdo);
    $allowedColumns = ['id_partner', 'nama', 'kategori'];
    if (!in_array($sortBy, $allowedColumns)) $sortBy = 'id_partner';
    $sortOrder = strtoupper($sortOrder) === 'DESC' ? 'DESC' : 'ASC';

    $sql = "SELECT * FROM partner ";
    $params = [];
    
    if ($keyword) {
        $sql .= "WHERE nama ILIKE :keyword OR kategori::text ILIKE :keyword ";
        $params[':keyword'] = '%' . $keyword . '%'; 
    }
    
    $sql .= "ORDER BY " . $sortBy . " " . $sortOrder; 
    $sql .= " LIMIT :limit OFFSET :offset";

    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    if ($keyword) $stmt->bindValue(':keyword', $params[':keyword'], PDO::PARAM_STR);
    
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getTotalPartnerCount($pdo, $keyword = null) {
    checkPdo($pdo);
    $sql = "SELECT COUNT(id_partner) FROM partner ";
    $params = [];
    if ($keyword) {
        $sql .= "WHERE nama ILIKE :keyword OR kategori::text ILIKE :keyword ";
        $params[':keyword'] = '%' . $keyword . '%'; 
    }
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return (int) $stmt->fetchColumn();
}

function getPartnerById($pdo, $id) {
    checkPdo($pdo); 
    $stmt = $pdo->prepare("SELECT * FROM partner WHERE id_partner = :id");
    $stmt->execute([':id' => $id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function insertPartner($pdo, $nama, $gambar, $kategori, $created_by) { // <-- Tambah parameter ini
    checkPdo($pdo);
    $stmt = $pdo->prepare("INSERT INTO partner (nama, gambar, kategori, created_by) VALUES (:nama, :gambar, :kategori, :created_by)");
    $stmt->execute([
        ':nama' => $nama, 
        ':gambar' => $gambar, 
        ':kategori' => $kategori,
        ':created_by' => $created_by // <-- Binding data
    ]);
}

function updatePartner($pdo, $id, $nama, $gambar, $kategori) {
    checkPdo($pdo);
    $stmt = $pdo->prepare("UPDATE partner SET nama = :nama, gambar = :gambar, kategori = :kategori WHERE id_partner = :id");
    $stmt->execute([':nama' => $nama, ':gambar' => $gambar, ':kategori' => $kategori, ':id' => $id]);
}

function deletePartner($pdo, $id) {
    checkPdo($pdo);
    $stmt = $pdo->prepare("DELETE FROM partner WHERE id_partner = :id");
    $stmt->execute([':id' => $id]);
}

function createSlug($text) {
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    $text = preg_replace('~[^-\w]+~', '', $text);
    $text = strtolower(trim($text, '-'));
    return $text ?: 'partner';
}
?>