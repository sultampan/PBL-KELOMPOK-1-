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

function getActivityAll($pdo, $keyword = null) {
    checkPdo($pdo);

    $sql = "SELECT * FROM activity ";
    $params = [];

    if ($keyword) {
        $sql .= "WHERE judul ILIKE :keyword OR deskripsi ILIKE :keyword ";
        $params[':keyword'] = '%' . $keyword . '%';
    }

    // Urutkan Kategori dulu (A-Z), baru ID terbaru
    $sql .= "ORDER BY kategori ASC, id_activity DESC";

    $stmt = $pdo->prepare($sql);
    if ($keyword) {
        $stmt->bindValue(':keyword', $params[':keyword']);
    }

    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC); // Return semua data
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

function insertActivity($pdo, $judul, $deskripsi, $tanggal, $kategori, $gambar, $id_admin) {
    checkPdo($pdo);
    $stmt = $pdo->prepare("
        INSERT INTO activity (judul, deskripsi, tanggal_kegiatan, kategori, gambar, created_by)
        VALUES (:judul, :deskripsi, :tanggal, :kategori, :gambar, :created_by)
    ");
    $stmt->execute([
        ':judul' => $judul,
        ':deskripsi' => $deskripsi,
        ':tanggal' => $tanggal,
        ':kategori' => $kategori, // <-- Kolom Kategori
        ':gambar' => $gambar,
        ':created_by' => $id_admin
    ]);
    // Tidak perlu return di sini, kita pakai $pdo->lastInsertId() di save.php
}

// UPDATE FUNGSI UPDATE (Tambah parameter Kategori)
function updateActivity($pdo, $id, $judul, $deskripsi, $tanggal, $kategori, $gambar) {
    checkPdo($pdo);
    
    // [PERBAIKAN]
    // Hapus logika if/else. Kita SELALU update kolom gambar.
    // Karena save.php sudah mengatur isinya:
    // 1. File Baru -> $gambar = 'nama_baru.jpg'
    // 2. Tidak Berubah -> $gambar = 'nama_lama.jpg'
    // 3. Dihapus -> $gambar = NULL
    
    $sql = "UPDATE activity 
            SET judul = :judul, 
                deskripsi = :deskripsi, 
                tanggal_kegiatan = :tanggal, 
                kategori = :kategori, 
                gambar = :gambar 
            WHERE id_activity = :id";
            
    $stmt = $pdo->prepare($sql);
    
    $stmt->execute([
        ':judul'     => $judul,
        ':deskripsi' => $deskripsi,
        ':tanggal'   => $tanggal,
        ':kategori'  => $kategori,
        ':gambar'    => $gambar, // Ini bisa string atau NULL, PDO akan menanganinya
        ':id'        => $id
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