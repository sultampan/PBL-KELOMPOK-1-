<?php
// admin/module/member/save.php
ini_set('display_errors', 0); error_reporting(E_ALL);
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../../../config/koneksi.php';
require_once "model.php";
require_once "upload.php";

function sendJson($status, $message) {
    header('Content-Type: application/json');
    echo json_encode(['status' => $status, 'message' => $message]);
    exit;
}

// Helper Slug
function createSlug($text) {
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    $text = preg_replace('~[^-\w]+~', '', $text);
    $text = trim($text, '-');
    $text = strtolower($text);
    return $text ?: 'member';
}

$uploadDir = __DIR__ . '/../../../public/uploads/member/';
$thumbDir  = __DIR__ . '/../../../public/uploads/thumb/member-thumb/';
@mkdir($uploadDir, 0755, true);

$allowedExt = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
$maxSize = 5 * 1024 * 1024;

// AMBIL DATA UTAMA
$id        = $_POST['id_member'] ?? null;
$nama      = trim($_POST['nama_member'] ?? '');
$nidn      = trim($_POST['nidn'] ?? '');
$jabatan   = trim($_POST['jabatan'] ?? '');
$deskripsi = trim($_POST['deskripsi'] ?? '');
$oldImg    = $_POST['gambar_lama'] ?? null;

// AMBIL DATA LINK (ARRAY)
$judul_links = $_POST['judul_link'] ?? [];
$url_links   = $_POST['url_link'] ?? [];

$newSlug = substr(createSlug($nama), 0, 50);
$new_uploaded_filename = null; 
$should_remove_old_image = ($_POST['remove_existing_image'] ?? '0') === '1';

try {
    if (empty($nama)) throw new Exception("Nama Member wajib diisi.");

    // VALIDASI
    if (!empty($nidn)) {
        if (!ctype_digit($nidn)) throw new Exception("NIDN hanya boleh angka.");
        if (isNidnExist($pdo, $nidn, $id)) throw new Exception("NIDN '$nidn' sudah terdaftar.");
    }
    if ($jabatan === 'Head of Laboratory') {
        if (isHeadLabExist($pdo, $id)) throw new Exception("Jabatan 'Head of Laboratory' sudah terisi.");
    }

    // UPLOAD LOGIC
    if ($should_remove_old_image && !empty($oldImg)) {
        if (is_file($uploadDir . $oldImg)) @unlink($uploadDir . $oldImg);
        $base = pathinfo($oldImg, PATHINFO_FILENAME);
        $ext = pathinfo($oldImg, PATHINFO_EXTENSION);
        if (is_file($thumbDir . $base . '-thumb.' . $ext)) @unlink($thumbDir . $base . '-thumb.' . $ext);
        $oldImg = null; 
    }
    $gambar = handleUpload("gambar", $oldImg, $uploadDir, $allowedExt, $maxSize, $newSlug);
    if ($gambar !== $oldImg) $new_uploaded_filename = $gambar;


    // === MULAI TRANSAKSI DATABASE ===
    $pdo->beginTransaction();

    if ($id) {
        // --- MODE UPDATE ---
        
        // 1. Update Tabel Induk
        $stmt = $pdo->prepare("
            UPDATE member
            SET nama_member = ?, nidn = ?, jabatan = ?, deskripsi = ?, gambar = ?
            WHERE id_member = ?
        ");
        $stmt->execute([$nama, $nidn, $jabatan, $deskripsi, $gambar, $id]);
        
        // 2. Update Link (Strategi: Hapus Semua Link Lama, Insert yang Baru)
        $stmtDel = $pdo->prepare("DELETE FROM member_link WHERE id_member = ?");
        $stmtDel->execute([$id]);

        $id_target = $id; // ID untuk insert link
        $msg = "Data member berhasil diperbarui.";

    } else {
        // --- MODE INSERT ---
        
        $id_admin = $_SESSION['id_admin'] ?? 1;
        
        // 1. Insert Induk (Pakai RETURNING id_member untuk PostgreSQL)
        $stmt = $pdo->prepare("
            INSERT INTO member (nama_member, nidn, jabatan, deskripsi, gambar, created_by)
            VALUES (?, ?, ?, ?, ?, ?)
            RETURNING id_member
        ");
        $stmt->execute([$nama, $nidn, $jabatan, $deskripsi, $gambar, $id_admin]);
        
        // Ambil ID yang baru dibuat
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $id_target = $row['id_member'];
        
        $msg = "Member baru berhasil ditambahkan.";
    }

    // 3. PROSES INSERT LINK (Dipakai baik Insert maupun Update)
    if (!empty($judul_links) && is_array($judul_links)) {
        $sqlLink = "INSERT INTO member_link (id_member, judul_link, url_link) VALUES (?, ?, ?)";
        $stmtLink = $pdo->prepare($sqlLink);

        for ($i = 0; $i < count($judul_links); $i++) {
            $j = trim($judul_links[$i]);
            $u = trim($url_links[$i]);

            // Hanya simpan jika Judul DAN URL ada
            if (!empty($j) && !empty($u)) {
                $stmtLink->execute([$id_target, $j, $u]);
            }
        }
    }

    // === COMMIT TRANSAKSI (SIMPAN PERMANEN) ===
    $pdo->commit();
    
    sendJson('success', $msg);

} catch (Exception $e) {
    // === ROLLBACK (BATALKAN SEMUA JIKA ERROR) ===
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    // Hapus file upload jika gagal DB
    if ($new_uploaded_filename) {
        @unlink($uploadDir . $new_uploaded_filename);
        $ext = pathinfo($new_uploaded_filename, PATHINFO_EXTENSION);
        $base = pathinfo($new_uploaded_filename, PATHINFO_FILENAME);
        @unlink($thumbDir . $base . '-thumb.' . $ext);
    }

    sendJson('error', "Gagal menyimpan: " . $e->getMessage());
}
?>