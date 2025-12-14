<?php
// admin/module/activity/save.php
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) session_start();

require_once __DIR__ . '/../../../config/koneksi.php';
require_once "model.php";
require_once "upload.php"; 

function sendJson($status, $message) {
    header('Content-Type: application/json');
    echo json_encode(['status' => $status, 'message' => $message]);
    exit;
}

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception("Invalid request");
    }

    $judul       = trim($_POST['judul'] ?? '');
    $deskripsi   = trim($_POST['deskripsi'] ?? '');
    $tanggal     = $_POST['tanggal_kegiatan'] ?? null;
    $kategori    = $_POST['kategori'] ?? ''; // <-- MENANGKAP KATEGORI
    $id          = $_POST['id_activity'] ?? null;
    $member_ids  = $_POST['member_ids'] ?? [];
    $id_admin    = $_SESSION['id_admin'] ?? 1;
    $gambarLama  = $_POST['gambar_lama'] ?? null;

    if ($judul === '') throw new Exception("Judul wajib diisi");
    if (!$tanggal) throw new Exception("Tanggal wajib diisi");
    if ($kategori === '') throw new Exception("Kategori wajib dipilih"); // Validasi Kategori

    // PROSES UPLOAD GAMBAR
    $serverAdminBase = dirname(__DIR__, 3) . '/';
    $uploadDir = $serverAdminBase . 'public/uploads/activity/';
    
    if (!is_dir($uploadDir)) { mkdir($uploadDir, 0755, true); }

    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $judul), '-'));
    $allowedExt = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $maxSize = 5 * 1024 * 1024; // 5MB
    
    $gambar = handleUpload('gambar', $gambarLama, $uploadDir, $allowedExt, $maxSize, $slug);
    $removeFlag = $_POST['remove_existing_image'] ?? '0';

    if ($removeFlag === '1' && empty($_FILES['gambar']['name'])) {
        $gambar = null; // Paksa jadi NULL agar di database terhapus
        
        // Opsional: Hapus file fisik lama di sini jika mau hemat storage
        if ($gambarLama && file_exists($uploadDir . $gambarLama)) {
            @unlink($uploadDir . $gambarLama);
        }
    }

    if ($id) {
        // UPDATE ACTIVITY (Ada parameter kategori)
        updateActivity($pdo, $id, $judul, $deskripsi, $tanggal, $kategori, $gambar);

        // HAPUS MEMBER LAMA
        $stmt = $pdo->prepare("DELETE FROM activity_member WHERE id_activity = ?");
        $stmt->execute([$id]);

        // INSERT MEMBER BARU
        if (!empty($member_ids)) {
            $stmt = $pdo->prepare("INSERT INTO activity_member (id_activity, id_member) VALUES (?, ?)");
            foreach ($member_ids as $mid) {
                if ($mid) { $stmt->execute([$id, $mid]); }
            }
        }
        sendJson('success', 'Activity berhasil diperbarui');

    } else {
        // INSERT ACTIVITY (Ada parameter kategori)
        insertActivity($pdo, $judul, $deskripsi, $tanggal, $kategori, $gambar, $id_admin);
        
        // Ambil ID terakhir (karena insertActivity tidak return ID, kita ambil manual atau modif fungsi model)
        // Agar aman, kita modifikasi insertActivity di model agar mengembalikan ID, 
        // TAPI karena Anda pakai PDO lastInsertId, kita ambil dari sana di model.
        // Cek file model di bawah untuk implementasi return ID.
        
        $newId = $pdo->lastInsertId(); // Mengambil ID dari proses insertActivity

        // INSERT MEMBER
        if (!empty($member_ids)) {
            $stmt = $pdo->prepare("INSERT INTO activity_member (id_activity, id_member) VALUES (?, ?)");
            foreach ($member_ids as $mid) {
                if ($mid) { $stmt->execute([$newId, $mid]); }
            }
        }
        sendJson('success', 'Activity berhasil disimpan', ['action' => 'insert']);
    }

} catch (Exception $e) {
    sendJson('error', $e->getMessage());
}
?>