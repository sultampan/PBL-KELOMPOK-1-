<?php
if (session_status() === PHP_SESSION_NONE) session_start();

// Memuat koneksi DB
require_once __DIR__ . '/../../../config/koneksi.php'; 

require_once "model.php";

// --- DEFINISI PATH UNIVERSAL YANG STABIL ---

// 1. Tentukan Base Path Web Dinamis (URL)
// Output: /Semester3/pbl/PBL-KELOMPOK-1-
$scriptName = $_SERVER['SCRIPT_NAME'];
$basePath = substr($scriptName, 0, strpos($scriptName, '/admin/'));
$basePath = rtrim($basePath, '/'); 

// 2. Tentukan Server Paths
$projectRoot = dirname(__DIR__, 3) . '/'; // Naik 3 tingkat ke C:\...\PBL-KELOMPOK-1-\

// Path Server File ASLI
$uploadDir = $projectRoot . 'public/uploads/produk/'; 

// Path Server File THUMBNAIL (LOKASI BARU: public/uploads/thumb/produk-thumb/)
$serverThumbDir = $projectRoot . 'public/uploads/thumb/produk-thumb/';

// Path Web File ASLI (URL)
$webUploadDir = $basePath . '/public/uploads/produk/'; 

// Path Web File THUMBNAIL (URL)
$webThumbDir = $basePath . '/public/uploads/thumb/produk-thumb/'; 

// Pastikan folder thumbnail ada
if (!is_dir($serverThumbDir)) {
    @mkdir($serverThumbDir, 0755, true);
}
// --- AKHIR DEFINISI PATH UNIVERSAL ---

$uploadDir = __DIR__ . '/../../../public/uploads/produk/';
$webUploadDir = '../public/uploads/produk/'; 

// Session error
$error = $_SESSION['error'] ?? null;
unset($_SESSION['error']);

// Session success
$success = $_SESSION['success'] ?? null;
unset($_SESSION['success']);

// AMBIL DATA INPUT LAMA DARI SESSION
$oldInput = $_SESSION['old_input'] ?? [];
unset($_SESSION['old_input']);

$editIdSession = $_SESSION['edit_id'] ?? null;
unset($_SESSION['edit_id']); // Bersihkan ID dari Session

// DELETE
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    try {
        $data = getProdukById($pdo, $id);
        if ($data && $data['gambar']) @unlink($uploadDir.$data['gambar']);
        deleteProduk($pdo, $id);
        $_SESSION['success'] = "Produk berhasil **dihapus**.";
    } catch (Exception $e) {
        // Tangani jika delete gagal
        $_SESSION['error'] = "Gagal menghapus produk: " . $e->getMessage();
    }
    // Redirect ke admin/index.php?page=produk
    header("Location: index.php?page=produk"); 
    exit;
}

// LOGIKA EDIT DATA (PRIORITAS: 1. ID dari Session, 2. ID dari GET)
$idToEdit = $_GET['edit'] ?? $editIdSession; // Ambil ID dari GET atau dari Session setelah error
$editData = $idToEdit ? getProdukById($pdo, (int)$idToEdit) : null;

// PENGURUTAN (SORTING)
$currentSortBy = $_GET['sort'] ?? 'id_produk';
$currentSortOrder = $_GET['order'] ?? 'ASC';

// TANGKAP KATA KUNCI PENCARIAN
$searchKeyword = $_GET['keyword'] ?? null;

// 1. PENGATURAN PAGINASI
$limit = 10; // Jumlah item per halaman (BISA DIUBAH)
$page = (int) ($_GET['p'] ?? 1); // Halaman saat ini, default 1
$page = max(1, $page); // Pastikan halaman tidak kurang dari 1

// Hitung offset (misal Halaman 2: offset = (2-1)*10 = 10)
$offset = ($page - 1) * $limit;

// 3. HITUNG TOTAL DATA & PAGINASI
$totalRecords = getTotalProdukCount($pdo, $searchKeyword); // Gunakan fungsi baru
$totalPages = ceil($totalRecords / $limit); // Total halaman (dibulatkan ke atas)

// 4. LIST PRODUK (Meneruskan LIMIT dan OFFSET)
$list = getProdukAll($pdo, $limit, $offset, $searchKeyword, $currentSortBy, $currentSortOrder) ?: [];


// SIMPAN VARIABEL PAGINASI UNTUK DIGUNAKAN DI table.php
$paginationData = [
    'currentPage' => $page,
    'totalPages' => $totalPages,
    'searchKeyword' => $searchKeyword,
    'limit' => $limit,
    'currentSortBy' => $currentSortBy,      
    'currentSortOrder' => $currentSortOrder,
];

// TAMPILKAN VIEW & TABLE
require_once __DIR__ . "/form.php";
require_once __DIR__ . "/table.php";
?>