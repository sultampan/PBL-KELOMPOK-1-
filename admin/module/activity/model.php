<?php
// admin/module/activity/model.php
if (session_status() === PHP_SESSION_NONE) session_start();

function checkPdo($pdo) {
    if (!$pdo instanceof PDO) throw new Exception("Koneksi database gagal.");
}
function getAllMembersOption($pdo) {
    checkPdo($pdo);
    $stmt = $pdo->query("SELECT id_member, nama_member FROM member ORDER BY nama_member ASC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getActivityAll(
    $pdo,
    $limit,
    $offset,
    $keyword = null,
    $sortBy = 'id_activity',
    $sortOrder = 'ASC'
) {
    checkPdo($pdo);

    $allowedColumns = ['id_activity', 'judul', 'deskripsi', 'tanggal_kegiatan'];
    if (!in_array($sortBy, $allowedColumns)) $sortBy = 'id_activity';
    $sortOrder = strtoupper($sortOrder) === 'DESC' ? 'DESC' : 'ASC';

    $params = [];

    $sql = "
        SELECT 
            a.*,
            COALESCE(
                json_agg(
                    DISTINCT jsonb_build_object(
                        'id_member', m.id_member,
                        'nama_member', m.nama_member
                    )
                ) FILTER (WHERE m.id_member IS NOT NULL),
                '[]'
            ) AS members
        FROM activity a
        LEFT JOIN activity_member am ON a.id_activity = am.id_activity
        LEFT JOIN member m ON am.id_member = m.id_member
    ";

    if ($keyword) {
        $sql .= " WHERE a.judul ILIKE :keyword OR a.deskripsi ILIKE :keyword ";
        $params[':keyword'] = '%' . $keyword . '%';
    }

    $sql .= "
        GROUP BY a.id_activity
        ORDER BY a.$sortBy $sortOrder
        LIMIT :limit OFFSET :offset
    ";

    $stmt = $pdo->prepare($sql);

    foreach ($params as $k => $v) {
        $stmt->bindValue($k, $v, PDO::PARAM_STR);
    }

    $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);

    $stmt->execute();

    // json_agg dikembalikan sebagai string → decode ke array
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($data as &$row) {
        $row['members'] = json_decode($row['members'], true);
    }
    unset($row);

    return $data;
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
    
    // 1. Ambil Data Induk (Activity)
    $stmt = $pdo->prepare("SELECT * FROM activity WHERE id_activity = :id");
    $stmt->execute([':id' => $id]);
    $activity = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($activity) {
        // 2. Ambil Data Anak (Member) dari tabel activity_member
        // JOIN ke tabel member biar dapat namanya
        $sqlMem = "SELECT am.id_member, m.nama_member 
                   FROM activity_member am 
                   JOIN member m ON am.id_member = m.id_member 
                   WHERE am.id_activity = ? 
                   ORDER BY am.id_activity_member ASC";
        
        $stmtMem = $pdo->prepare($sqlMem);
        $stmtMem->execute([$id]);
        $activity['team'] = $stmtMem->fetchAll(PDO::FETCH_ASSOC);
    }
    
    return $activity; // ← RETURN DI AKHIR
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
function getActivityMembers($pdo, $id_activity) {
    checkPdo($pdo);

    $stmt = $pdo->prepare("
        SELECT m.nama_member
        FROM activity_member am
        JOIN member m ON am.id_member = m.id_member
        WHERE am.id_activity = :id
        ORDER BY m.nama_member
    ");

    $stmt->execute([':id' => $id_activity]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

?>