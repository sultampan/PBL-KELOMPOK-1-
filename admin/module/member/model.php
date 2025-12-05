<?php
// admin/module/member/model.php
if (session_status() === PHP_SESSION_NONE) session_start();

function checkPdo($pdo) {
    if (!$pdo instanceof PDO) throw new Exception("Koneksi database gagal.");
}

function getMemberAll($pdo, $limit, $offset, $keyword = null, $sortBy = 'id_member', $sortOrder = 'ASC') { 
    checkPdo($pdo);
    $allowedColumns = ['id_member', 'nama_member', 'nidn', 'jabatan'];
    if (!in_array($sortBy, $allowedColumns)) $sortBy = 'id_member';
    $sortOrder = strtoupper($sortOrder) === 'DESC' ? 'DESC' : 'ASC';

    $sql = "SELECT * FROM member ";
    $params = [];
    
    if ($keyword) {
        $sql .= "WHERE nama_member ILIKE :keyword OR nidn ILIKE :keyword OR jabatan ILIKE :keyword ";
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

function getTotalMemberCount($pdo, $keyword = null) {
    checkPdo($pdo);
    $sql = "SELECT COUNT(id_member) FROM member ";
    $params = [];
    if ($keyword) {
        $sql .= "WHERE nama_member ILIKE :keyword OR nidn ILIKE :keyword OR jabatan ILIKE :keyword ";
        $params[':keyword'] = '%' . $keyword . '%'; 
    }
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return (int) $stmt->fetchColumn();
}

function getMemberById($pdo, $id) {
    checkPdo($pdo); 
    $stmt = $pdo->prepare("SELECT * FROM member WHERE id_member = :id");
    $stmt->execute([':id' => $id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function insertMember($pdo, $data) {
    checkPdo($pdo);
    $stmt = $pdo->prepare("
        INSERT INTO member (nama_member, nidn, jabatan, deskripsi, google_scholar, orcid, sinta, gambar, created_by)
        VALUES (:nama, :nidn, :jabatan, :deskripsi, :scholar, :orcid, :sinta, :gambar, :created_by)
    ");
    $stmt->execute($data);
}

function updateMember($pdo, $data) {
    checkPdo($pdo);
    $stmt = $pdo->prepare("
        UPDATE member
        SET nama_member = :nama, nidn = :nidn, jabatan = :jabatan, deskripsi = :deskripsi,
            google_scholar = :scholar, orcid = :orcid, sinta = :sinta, gambar = :gambar
        WHERE id_member = :id
    ");
    $stmt->execute($data);
}

function deleteMember($pdo, $id) {
    checkPdo($pdo);
    $stmt = $pdo->prepare("DELETE FROM member WHERE id_member = :id");
    $stmt->execute([':id' => $id]);
}

function createSlug($text) {
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    $text = preg_replace('~[^-\w]+~', '', $text);
    $text = trim($text, '-');
    $text = strtolower($text);
    return $text ?: 'member';
}

/**
 * Cek apakah Head of Laboratory sudah ada.
 * @param PDO $pdo
 * @param int|null $excludeId (Opsional) ID member yang sedang diedit agar tidak menghitung dirinya sendiri.
 * @return bool True jika sudah ada, False jika belum.
 */
function isHeadLabExist($pdo, $excludeId = null) {
    checkPdo($pdo);
    
    // Cari member yang jabatannya 'Head of Laboratory'
    $sql = "SELECT COUNT(*) FROM member WHERE jabatan = 'Head of Laboratory'";
    
    // Jika sedang edit, jangan hitung diri sendiri (supaya bisa update profil sendiri)
    if ($excludeId) {
        $sql .= " AND id_member != :id";
    }
    
    $stmt = $pdo->prepare($sql);
    
    if ($excludeId) {
        $stmt->bindValue(':id', $excludeId, PDO::PARAM_INT);
    }
    
    $stmt->execute();
    return (int) $stmt->fetchColumn() > 0;
}

/**
 * Cek apakah NIDN sudah terdaftar.
 * @param PDO $pdo
 * @param string $nidn NIDN yang akan dicek
 * @param int|null $excludeId ID member saat edit (agar tidak bentrok dengan diri sendiri)
 * @return bool
 */
function isNidnExist($pdo, $nidn, $excludeId = null) {
    checkPdo($pdo);
    
    $sql = "SELECT COUNT(*) FROM member WHERE nidn = :nidn";
    
    // Jika mode edit, abaikan ID sendiri
    if ($excludeId) {
        $sql .= " AND id_member != :id";
    }
    
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':nidn', $nidn);
    
    if ($excludeId) {
        $stmt->bindValue(':id', $excludeId, PDO::PARAM_INT);
    }
    
    $stmt->execute();
    return (int) $stmt->fetchColumn() > 0;
}
?>