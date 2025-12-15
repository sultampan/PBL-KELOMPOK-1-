<?php
// admin/module/activity/save.php
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) session_start();

require_once __DIR__ . '/../../../config/koneksi.php';
require_once "model.php";
require_once "upload.php"; 

function sendJson($status, $message, $data = []) {
    // Clear any output buffers
    while (ob_get_level()) {
        ob_end_clean();
    }
    
    header('Content-Type: application/json');
    echo json_encode([
        'status' => $status, 
        'message' => $message,
        'data' => $data
    ]);
    exit;
}

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception("Invalid request");
    }

    // Ambil data dari POST
    $judul       = trim($_POST['judul'] ?? '');
    $deskripsi   = trim($_POST['deskripsi'] ?? '');
    $tanggal     = $_POST['tanggal_kegiatan'] ?? null;
    $kategori    = $_POST['kategori'] ?? '';
    $id          = $_POST['id_activity'] ?? null;
    $member_ids  = $_POST['member_ids'] ?? [];
    $id_admin    = $_SESSION['id_admin'] ?? 1;
    $gambarLama  = $_POST['gambar_lama'] ?? null;

    // Validasi input
    if ($judul === '') {
        throw new Exception("Judul wajib diisi");
    }
    
    if (strlen($judul) > 255) {
    throw new Exception("Judul maksimal 255 karakter");
    }
    
    if (!$tanggal) {
        throw new Exception("Tanggal wajib diisi");
    }
    
    if ($kategori === '') {
        throw new Exception("Kategori wajib dipilih");
    }
    
    // Validasi kategori sesuai dengan enum/yang diizinkan
    $validKategori = ['Research', 'Projects', 'Activity'];
    if (!in_array($kategori, $validKategori)) {
        throw new Exception("Kategori tidak valid");
    }

    // PROSES UPLOAD GAMBAR
    $serverAdminBase = dirname(__DIR__, 3) . '/';
    $uploadDir = $serverAdminBase . 'public/uploads/activity/';
    
    if (!is_dir($uploadDir)) { 
        if (!mkdir($uploadDir, 0755, true)) {
            throw new Exception("Gagal membuat direktori upload");
        }
    }

    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $judul), '-'));
    $allowedExt = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $maxSize = 5 * 1024 * 1024; // 5MB
    
    $gambar = null;
    $removeFlag = $_POST['remove_existing_image'] ?? '0';
    
    // Cek apakah ada file baru yang diupload
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
        // Ada file baru
        $gambar = handleUpload('gambar', $gambarLama, $uploadDir, $allowedExt, $maxSize, $slug);
        
        // Hapus gambar lama jika ada
        if ($gambarLama && file_exists($uploadDir . $gambarLama)) {
            @unlink($uploadDir . $gambarLama);
        }
    } elseif ($removeFlag === '1') {
        // User ingin hapus gambar existing
        $gambar = null;
        
        if ($gambarLama && file_exists($uploadDir . $gambarLama)) {
            @unlink($uploadDir . $gambarLama);
        }
    } else {
        // Pertahankan gambar lama
        $gambar = $gambarLama;
    }

    // Mulai database transaction
    $pdo->beginTransaction();
    
    try {
        if ($id) {
            // MODE UPDATE
            error_log("Updating activity ID: $id");
            
            updateActivity($pdo, $id, $judul, $deskripsi, $tanggal, $kategori, $gambar);

            // Hapus member lama
            $stmt = $pdo->prepare("DELETE FROM activity_member WHERE id_activity = ?");
            $stmt->execute([$id]);

            // Insert member baru
            if (!empty($member_ids)) {
                $stmt = $pdo->prepare("INSERT INTO activity_member (id_activity, id_member) VALUES (?, ?)");
                foreach ($member_ids as $mid) {
                    $mid = trim($mid);
                    if (!empty($mid) && is_numeric($mid)) {
                        $stmt->execute([$id, $mid]);
                    }
                }
            }
            
            $pdo->commit();
            error_log("Activity updated successfully: $id");
            
            sendJson('success', 'Activity berhasil diperbarui', [
                'id' => $id,
                'action' => 'update'
            ]);

        } else {
            // MODE INSERT
            error_log("Inserting new activity: $judul");
            
            insertActivity($pdo, $judul, $deskripsi, $tanggal, $kategori, $gambar, $id_admin);
            
            $newId = $pdo->lastInsertId();
            
            if (!$newId) {
                throw new Exception("Gagal mendapatkan ID activity yang baru dibuat");
            }
            
            error_log("New activity ID: $newId");

            // Insert member
            if (!empty($member_ids)) {
                $stmt = $pdo->prepare("INSERT INTO activity_member (id_activity, id_member) VALUES (?, ?)");
                foreach ($member_ids as $mid) {
                    $mid = trim($mid);
                    if (!empty($mid) && is_numeric($mid)) {
                        $stmt->execute([$newId, $mid]);
                    }
                }
            }
            
            $pdo->commit();
            error_log("Activity saved successfully: $newId");
            
            sendJson('success', 'Activity berhasil disimpan', [
                'id' => $newId,
                'action' => 'insert'
            ]);
        }
        
    } catch (Exception $e) {
        $pdo->rollBack();
        error_log("Database transaction error: " . $e->getMessage());
        throw new Exception("Gagal menyimpan ke database: " . $e->getMessage());
    }

} catch (Exception $e) {
    error_log("Activity save error: " . $e->getMessage());
    error_log("POST data: " . print_r($_POST, true));
    
    sendJson('error', $e->getMessage());
}
?>