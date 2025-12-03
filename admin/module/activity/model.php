<?php
// admin/module/activity/model.php
if (session_status() === PHP_SESSION_NONE) session_start();

function checkPdo($pdo) {
    if (!$pdo instanceof PDO) throw new Exception("Koneksi database gagal.");
}

function getActivityAll($pdo, $limit, $offset, $keyword = null, $sortBy = 'id_activity', $sortOrder = 'ASC') { 
    checkPdo($pdo);
    $allowedColumns = ['id_activity', 'judul', 'deskripsi', 'tanggal_kegiatan'];
    if (!in_array($sortBy, $allowedColumns)) $sortBy = 'id_activity';
    $sortOrder = strtoupper($sortOrder) === 'DESC' ? 'DESC' : 'ASC';

    $sql = "SELECT * FROM activity ";
    $params = [];
    
    if ($keyword) {
        $sql .= "WHERE judul ILIKE :keyword OR deskripsi ILIKE :keyword ";
        $params[':keyword'] = '%' . $keyword . '%'; 
    }
    
    $sql .= "ORDER BY " . $sortBy . " " . $sortOrder . " LIMIT :limit OFFSET :offset";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    if ($keyword) $stmt->bindValue(':keyword', $params[':keyword'], PDO::PARAM_STR);
    
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getTotalActivityCount($pdo, $keyword = null) {
    checkPdo($pdo);
    $sql = "SELECT COUNT(id_activity) FROM activity ";
    $params = [];
    if ($keyword) {
        $sql .= "WHERE judul ILIKE :keyword OR deskripsi ILIKE :keyword ";
        $params[':keyword'] = '%' . $keyword . '%'; 
    }
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return (int) $stmt->fetchColumn();
}

function getActivityById($pdo, $id) {
    checkPdo($pdo); 
    $stmt = $pdo->prepare("SELECT * FROM activity WHERE id_activity = :id");
    $stmt->execute([':id' => $id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function insertActivity($pdo, $judul, $deskripsi, $tanggal, $gambar, $id_admin) {
    checkPdo($pdo);
    $stmt = $pdo->prepare("
        INSERT INTO activity (judul, deskripsi, tanggal_kegiatan, gambar, created_by)
        VALUES (:judul, :deskripsi, :tanggal, :gambar, :created_by)
    ");
    $stmt->execute([
        ':judul' => $judul,
        ':deskripsi' => $deskripsi,
        ':tanggal' => $tanggal,
        ':gambar' => $gambar,
        ':created_by' => $id_admin
    ]);
}

function updateActivity($pdo, $id, $judul, $deskripsi, $tanggal, $gambar) {
    checkPdo($pdo);
    $stmt = $pdo->prepare("
        UPDATE activity
        SET judul = :judul, deskripsi = :deskripsi, tanggal_kegiatan = :tanggal, gambar = :gambar
        WHERE id_activity = :id
    ");
    $stmt->execute([
        ':judul' => $judul,
        ':deskripsi' => $deskripsi,
        ':tanggal' => $tanggal,
        ':gambar' => $gambar,
        ':id' => $id
    ]);
}

function deleteActivity($pdo, $id) {
    checkPdo($pdo);
    $stmt = $pdo->prepare("DELETE FROM activity WHERE id_activity = :id");
    $stmt->execute([':id' => $id]);
}

function createSlug($text) {
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    $text = preg_replace('~[^-\w]+~', '', $text);
    $text = trim($text, '-');
    $text = strtolower($text);
    return $text ?: 'activity';
}
?>