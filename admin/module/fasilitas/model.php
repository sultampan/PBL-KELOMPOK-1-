<?php
// admin/module/fasilitas/model.php
if (session_status() === PHP_SESSION_NONE) session_start();

function checkPdo($pdo) {
    if (!$pdo instanceof PDO) {
        throw new Exception("Error: Koneksi database (\$pdo) tidak tersedia.");
    }
}

/**
 * Ambil semua fasilitas
 */
function getFasilitasAll($pdo, $limit, $offset, $keyword = null, $sortBy = 'id_galery', $sortOrder = 'ASC') { 
    checkPdo($pdo);
    
    // Validasi kolom sorting (sesuai field tabel fasilitas)
    $allowedColumns = ['id_galery', 'judul', 'deskripsi'];
    if (!in_array($sortBy, $allowedColumns)) {
        $sortBy = 'id_galery';
    }

    $sortOrder = strtoupper($sortOrder) === 'DESC' ? 'DESC' : 'ASC';

    $sql = "SELECT * FROM fasilitas ";
    $params = [];
    
    if ($keyword) {
        $sql .= "WHERE judul ILIKE :keyword OR deskripsi ILIKE :keyword ";
        $params[':keyword'] = '%' . $keyword . '%'; 
    }
    
    $sql .= "ORDER BY " . $sortBy . " " . $sortOrder; 
    $sql .= " LIMIT :limit OFFSET :offset";

    $stmt = $pdo->prepare($sql);
    
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    
    if ($keyword) {
        $stmt->bindValue(':keyword', $params[':keyword'], PDO::PARAM_STR);
    }
    
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Hitung total fasilitas
 */
function getTotalFasilitasCount($pdo, $keyword = null) {
    checkPdo($pdo);
    
    $sql = "SELECT COUNT(id_galery) FROM fasilitas ";
    $params = [];
    
    if ($keyword) {
        $sql .= "WHERE judul ILIKE :keyword OR deskripsi ILIKE :keyword ";
        $params[':keyword'] = '%' . $keyword . '%'; 
    }
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return (int) $stmt->fetchColumn();
}

/**
 * Ambil 1 fasilitas by ID
 */
function getFasilitasById($pdo, $id) {
    checkPdo($pdo); 
    // Perhatikan: primary key adalah id_galery
    $stmt = $pdo->prepare("SELECT * FROM fasilitas WHERE id_galery = :id");
    $stmt->execute([':id' => $id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

/**
 * Insert fasilitas baru
 */
function insertFasilitas($pdo, $judul, $deskripsi, $gambar, $id_admin) {
    checkPdo($pdo);
    $stmt = $pdo->prepare("
        INSERT INTO fasilitas (judul, deskripsi, gambar, created_by)
        VALUES (:judul, :deskripsi, :gambar, :created_by)
    ");
    $stmt->execute([
        ':judul' => $judul,
        ':deskripsi' => $deskripsi,
        ':gambar' => $gambar,
        ':created_by' => $id_admin
    ]);
}

/**
 * Update fasilitas
 */
function updateFasilitas($pdo, $id, $judul, $deskripsi, $gambar) {
    checkPdo($pdo);
    $stmt = $pdo->prepare("
        UPDATE fasilitas
        SET judul = :judul, deskripsi = :deskripsi, gambar = :gambar
        WHERE id_galery = :id
    ");
    $stmt->execute([
        ':judul' => $judul,
        ':deskripsi' => $deskripsi,
        ':gambar' => $gambar,
        ':id' => $id
    ]);
}

/**
 * Delete fasilitas
 */
function deleteFasilitas($pdo, $id) {
    checkPdo($pdo);
    $stmt = $pdo->prepare("DELETE FROM fasilitas WHERE id_galery = :id");
    $stmt->execute([':id' => $id]);
}

/**
 * Slug generator (untuk nama file)
 */
function createSlug($text) {
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    $text = preg_replace('~[^-\w]+~', '', $text);
    $text = trim($text, '-');
    $text = preg_replace('~-+~', '-', $text);
    $text = strtolower($text);
    return $text ?: 'file';
}
?>