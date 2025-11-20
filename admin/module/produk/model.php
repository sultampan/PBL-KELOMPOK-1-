<?php
// admin/module/produk/model.php
if (session_status() === PHP_SESSION_NONE) session_start();

// Tambahkan pengaman untuk fungsi yang membutuhkan $pdo
function checkPdo($pdo) {
    if (!$pdo instanceof PDO) {
        throw new Exception("Error: Koneksi database (\$pdo) tidak tersedia.");
    }
}

/**
 * Ambil semua produk
 */
function getProdukAll($pdo) {
    checkPdo($pdo);
    $stmt = $pdo->query("SELECT * FROM produk ORDER BY id_produk DESC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Ambil 1 produk berdasarkan id
 */
function getProdukById($pdo, $id) {
    checkPdo($pdo); 
    $stmt = $pdo->prepare("SELECT * FROM produk WHERE id_produk = :id");
    $stmt->execute([':id' => $id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

/**
 * Insert produk baru
 */
function insertProduk($pdo, $nama, $deskripsi, $gambar, $link, $id_admin) {
    checkPdo($pdo);
    $stmt = $pdo->prepare("
        INSERT INTO produk (nama, deskripsi, gambar, link_produk, created_by)
        VALUES (:nama, :deskripsi, :gambar, :link, :created_by)
    ");
    $stmt->execute([
        ':nama' => $nama,
        ':deskripsi' => $deskripsi,
        ':gambar' => $gambar,
        ':link' => $link,
        ':created_by' => $id_admin
    ]);
}

/**
 * Update produk
 */
function updateProduk($pdo, $id, $nama, $deskripsi, $gambar, $link) {
    checkPdo($pdo);
    $stmt = $pdo->prepare("
        UPDATE produk
        SET nama = :nama, deskripsi = :deskripsi, gambar = :gambar, link_produk = :link
        WHERE id_produk = :id
    ");
    $stmt->execute([
        ':nama' => $nama,
        ':deskripsi' => $deskripsi,
        ':gambar' => $gambar,
        ':link' => $link,
        ':id' => $id
    ]);
}

/**
 * Delete produk
 */
function deleteProduk($pdo, $id) {
    checkPdo($pdo);
    $stmt = $pdo->prepare("DELETE FROM produk WHERE id_produk = :id");
    $stmt->execute([':id' => $id]);
}

/**
 * Slug generator (untuk nama file gambar)
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