<?php
// admin/module/member/model.php
if (session_status() === PHP_SESSION_NONE) session_start();

function checkPdo($pdo) {
    if (!$pdo instanceof PDO) throw new Exception("Koneksi database gagal.");
}

// Ambil semua member (Table View)
function getMemberAll($pdo, $limit, $offset, $keyword = null, $sortBy = 'id_member', $sortOrder = 'ASC') { 
    checkPdo($pdo);
    $allowedColumns = ['id_member', 'nama_member', 'nidn', 'jabatan'];
    if (!in_array($sortBy, $allowedColumns)) $sortBy = 'id_member';
    $sortOrder = strtoupper($sortOrder) === 'DESC' ? 'DESC' : 'ASC';

    // Kita join dengan member_link agar bisa ditampilkan di JSON (opsional, via subquery array)
    // Tapi untuk performa list, kita ambil data member intinya saja dulu.
    // Kolom google_scholar, orcid, sinta SUDAH DIHAPUS, jadi SELECT * aman.
    
    $sql = "SELECT m.* FROM member m ";
    $params = [];
    
    if ($keyword) {
        $sql .= "WHERE m.nama_member ILIKE :keyword 
                 OR m.nidn ILIKE :keyword 
                 OR m.jabatan ILIKE :keyword 
                 OR m.deskripsi ILIKE :keyword "; 
        $params[':keyword'] = '%' . $keyword . '%'; 
    }
    
    $sql .= "ORDER BY " . $sortBy . " " . $sortOrder . " LIMIT :limit OFFSET :offset";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    if ($keyword) $stmt->bindValue(':keyword', $params[':keyword'], PDO::PARAM_STR);
    
    $stmt->execute();
    $members = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // OPTIONAL: Ambil Links untuk setiap member agar icon di tabel tetap muncul
    // Ini teknik "Eager Loading" manual biar query ga berat
    foreach ($members as &$m) {
        $m['links'] = getLinksByMemberId($pdo, $m['id_member']);
    }
    return $members;
}

function getTotalMemberCount($pdo, $keyword = null) {
    checkPdo($pdo);
    $sql = "SELECT COUNT(id_member) FROM member ";
    $params = [];
    if ($keyword) {
        $sql .= "WHERE nama_member ILIKE :keyword OR nidn ILIKE :keyword OR jabatan ILIKE :keyword OR deskripsi ILIKE :keyword "; 
        $params[':keyword'] = '%' . $keyword . '%'; 
    }
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return (int) $stmt->fetchColumn();
}

// Ambil 1 member beserta link-nya (Untuk Edit Form)
function getMemberById($pdo, $id) {
    checkPdo($pdo); 
    // 1. Ambil data induk
    $stmt = $pdo->prepare("SELECT * FROM member WHERE id_member = :id");
    $stmt->execute([':id' => $id]);
    $member = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($member) {
        // 2. Ambil data anak (links)
        $member['links'] = getLinksByMemberId($pdo, $id);
    }

    return $member;
}

// Fungsi helper ambil link
function getLinksByMemberId($pdo, $id_member) {
    $stmt = $pdo->prepare("SELECT * FROM member_link WHERE id_member = ?");
    $stmt->execute([$id_member]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Cek NIDN
function isNidnExist($pdo, $nidn, $excludeId = null) {
    checkPdo($pdo);
    $sql = "SELECT COUNT(*) FROM member WHERE nidn = :nidn";
    if ($excludeId) $sql .= " AND id_member != :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':nidn', $nidn);
    if ($excludeId) $stmt->bindValue(':id', $excludeId, PDO::PARAM_INT);
    $stmt->execute();
    return (int) $stmt->fetchColumn() > 0;
}

// Cek Head Lab
function isHeadLabExist($pdo, $excludeId = null) {
    checkPdo($pdo);
    $sql = "SELECT COUNT(*) FROM member WHERE jabatan = 'Head of Laboratory'";
    if ($excludeId) $sql .= " AND id_member != :id";
    $stmt = $pdo->prepare($sql);
    if ($excludeId) $stmt->bindValue(':id', $excludeId, PDO::PARAM_INT);
    $stmt->execute();
    return (int) $stmt->fetchColumn() > 0;
}

// Hapus Member (Cascade akan otomatis menghapus link di Postgres jika di-set ON DELETE CASCADE)
// Tapi kita hapus manual gambarnya di controller.
function deleteMember($pdo, $id) {
    checkPdo($pdo);
    $stmt = $pdo->prepare("DELETE FROM member WHERE id_member = :id");
    $stmt->execute([':id' => $id]);
}
?>