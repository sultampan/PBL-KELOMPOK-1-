<?php
// admin/module/member/save.php
ini_set('display_errors', 0); ini_set('display_startup_errors', 0); error_reporting(E_ALL);
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../../../config/koneksi.php';
require_once "model.php";
require_once "upload.php";

function sendJson($status, $message) {
    header('Content-Type: application/json');
    echo json_encode(['status' => $status, 'message' => $message]);
    exit;
}

$uploadDir = __DIR__ . '/../../../public/uploads/member/';
$thumbDir  = __DIR__ . '/../../../public/uploads/thumb/member-thumb/';
@mkdir($uploadDir, 0755, true);

$allowedExt = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
$maxSize = 5 * 1024 * 1024;

// Ambil Input
$id = $_POST['id_member'] ?? null;
$nama = trim($_POST['nama_member'] ?? '');
$nidn = trim($_POST['nidn'] ?? '');
$jabatan = trim($_POST['jabatan'] ?? ''); // Pastikan ini sesuai value di dropdown/text
$deskripsi = trim($_POST['deskripsi'] ?? '');
$scholar = trim($_POST['google_scholar'] ?? '');
$orcid = trim($_POST['orcid'] ?? '');
$sinta = trim($_POST['sinta'] ?? '');
$oldImg = $_POST['gambar_lama'] ?? null;

$newSlug = createSlug($nama);
$new_uploaded_filename = null; 
$should_remove_old_image = ($_POST['remove_existing_image'] ?? '0') === '1';

try {
    if (empty($nama)) throw new Exception("Nama Member wajib diisi.");

    // --- LOGIKA BARU: VALIDASI HEAD OF LABORATORY ---
    // Jika user memilih jabatan 'Head of Laboratory', cek apakah sudah ada orang lain yg menjabat
    if ($jabatan === 'Head of Laboratory') {
        if (isHeadLabExist($pdo, $id)) {
            throw new Exception("Gagal: Jabatan 'Head of Laboratory' sudah terisi. Hanya boleh ada 1 orang.");
        }
    }
    // ------------------------------------------------

    // Logika Hapus Gambar Lama + Thumbnail
    if ($should_remove_old_image && !empty($oldImg)) {
        $file = $uploadDir . $oldImg;
        if (is_file($file)) @unlink($file);
        
        $ext = pathinfo($oldImg, PATHINFO_EXTENSION);
        $base_name = pathinfo($oldImg, PATHINFO_FILENAME);
        $thumb_name = $base_name . '-thumb.' . $ext;
        $thumb_path = $thumbDir . $thumb_name;
        
        if (is_file($thumb_path)) @unlink($thumb_path);

        $oldImg = null; $gambar = null; 
    }

    $gambar = handleUpload("gambar", $oldImg, $uploadDir, $allowedExt, $maxSize, $newSlug);
    if ($gambar !== $oldImg) $new_uploaded_filename = $gambar;

    if ($id) {
        $data = [
            ':id' => $id, ':nama' => $nama, ':nidn' => $nidn, ':jabatan' => $jabatan,
            ':deskripsi' => $deskripsi, ':scholar' => $scholar, ':orcid' => $orcid,
            ':sinta' => $sinta, ':gambar' => $gambar
        ];
        updateMember($pdo, $data);
        sendJson('success', "Data member berhasil diperbarui.");
    } else {
        $id_admin = $_SESSION['id_admin'] ?? 1;
        $data = [
            ':nama' => $nama, ':nidn' => $nidn, ':jabatan' => $jabatan,
            ':deskripsi' => $deskripsi, ':scholar' => $scholar, ':orcid' => $orcid,
            ':sinta' => $sinta, ':gambar' => $gambar, ':created_by' => $id_admin
        ];
        insertMember($pdo, $data);
        sendJson('success', "Member baru berhasil ditambahkan.");
    }
} catch (Exception $e) {
    if ($new_uploaded_filename && is_file($uploadDir . $new_uploaded_filename)) @unlink($uploadDir . $new_uploaded_filename);
    sendJson('error', "Gagal menyimpan: " . $e->getMessage());
}
?>