<?php
// admin/module/produk/model.php
if (session_status() === PHP_SESSION_NONE) session_start();

// --- Helper pengecekan PDO ---
function checkPdo($pdo) {
    if (!$pdo instanceof PDO) {
        throw new Exception("Koneksi database \$pdo tidak valid.");
    }
}

/* ============================================================
   AMBIL OPSI MEMBER UNTUK DROPDOWN
   ============================================================ */
function getAllMembersOption($pdo) {
    checkPdo($pdo);
    $stmt = $pdo->query("SELECT id_member, nama_member FROM member ORDER BY nama_member ASC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/* ============================================================
   GET 1 PRODUK BY ID (UNTUK EDIT & DELETE)
   ============================================================ */
function getProdukById($pdo, $id) {
    checkPdo($pdo);

    // 1. Ambil produk utama
    $stmt = $pdo->prepare("SELECT * FROM produk WHERE id_produk = :id");
    $stmt->execute([':id' => $id]);
    $produk = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$produk) {
        return null;
    }

    // 2. Ambil tim produk
    $sqlTeam = "
        SELECT pm.id_member, pm.role, m.nama_member
        FROM produk_member pm
        JOIN member m ON pm.id_member = m.id_member
        WHERE pm.id_produk = :id
        ORDER BY pm.id_produk_member ASC
    ";
    $stmtTeam = $pdo->prepare($sqlTeam);
    $stmtTeam->execute([':id' => $id]);
    $produk['team'] = $stmtTeam->fetchAll(PDO::FETCH_ASSOC);

    return $produk;
}

/* ============================================================
   GET LIST PRODUK UNTUK TABEL
   ============================================================ */
function getProdukAll($pdo, $limit, $offset, $keyword = null, $sortBy = 'id_produk', $sortOrder = 'ASC') {
    checkPdo($pdo);

    $allowed = ['id_produk', 'nama', 'deskripsi'];
    if (!in_array($sortBy, $allowed)) {
        $sortBy = 'id_produk';
    }

    $sortOrder = strtoupper($sortOrder) === 'DESC' ? 'DESC' : 'ASC';

    $sql = "SELECT * FROM produk";
    $params = [];

    if ($keyword) {
        $sql .= " WHERE nama ILIKE :kw OR deskripsi ILIKE :kw";
        $params[':kw'] = "%$keyword%";
    }

    $sql .= " ORDER BY $sortBy $sortOrder LIMIT :limit OFFSET :offset";

    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

    if ($keyword) {
        $stmt->bindValue(':kw', "%$keyword%", PDO::PARAM_STR);
    }

    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Tambahkan tim setiap produk
    foreach ($rows as &$r) {
        $r['team'] = getTeamByProduk($pdo, $r['id_produk']);
    }

    return $rows;
}

/* ============================================================
   HITUNG TOTAL PRODUK (UNTUK PAGINASI)
   ============================================================ */
function getTotalProdukCount($pdo, $keyword = null) {
    checkPdo($pdo);

    $sql = "SELECT COUNT(*) FROM produk";
    $params = [];

    if ($keyword) {
        $sql .= " WHERE nama ILIKE :kw OR deskripsi ILIKE :kw";
        $params[':kw'] = "%$keyword%";
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    return (int) $stmt->fetchColumn();
}

/* ============================================================
   CRUD PRODUK
   ============================================================ */
function insertProduk($pdo, $nama, $deskripsi, $gambar, $link, $id_admin) {
    checkPdo($pdo);
    $sql = "INSERT INTO produk (nama, deskripsi, gambar, link_produk, created_by)
            VALUES (:n, :d, :g, :l, :c)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':n' => $nama,
        ':d' => $deskripsi,
        ':g' => $gambar,
        ':l' => $link,
        ':c' => $id_admin
    ]);
}

function updateProduk($pdo, $id, $nama, $deskripsi, $gambar, $link) {
    checkPdo($pdo);
    $sql = "UPDATE produk SET nama = :n, deskripsi = :d, gambar = :g, link_produk = :l
            WHERE id_produk = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':n' => $nama,
        ':d' => $deskripsi,
        ':g' => $gambar,
        ':l' => $link,
        ':id' => $id
    ]);
}

function deleteProduk($pdo, $id) {
    checkPdo($pdo);
    $stmt = $pdo->prepare("DELETE FROM produk WHERE id_produk = :id");
    $stmt->execute([':id' => $id]);
}

/* ============================================================
   SLUG
   ============================================================ */
function createSlug($text) {
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    $text = preg_replace('~[^-\w]+~', '', $text);
    $text = trim($text, '-');
    $text = preg_replace('~-+~', '-', $text);
    return strtolower($text ?: 'file');
}

/* ============================================================
   PRODUK_MEMBER FUNCTIONS
   ============================================================ */
function insertProdukMember($pdo, $id_produk, $id_member, $role) {
    checkPdo($pdo);
    $sql = "INSERT INTO produk_member (id_produk, id_member, role)
            VALUES (:p, :m, :r)";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([
        ':p' => $id_produk,
        ':m' => $id_member,
        ':r' => $role
    ]);
}

function getTeamByProduk($pdo, $id_produk) {
    $sql = "SELECT 
                pm.id_member, 
                pm.role,
                m.nama_member
            FROM produk_member pm
            JOIN member m ON pm.id_member = m.id_member
            WHERE pm.id_produk = :id_produk
            ORDER BY m.nama_member ASC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id_produk' => $id_produk]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

?>
