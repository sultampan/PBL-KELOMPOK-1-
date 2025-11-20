<?php
// src/module/member/index.php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header("Location: ../../login.php");
    exit;
}

// config upload
$uploadDir = __DIR__ . '/../../../uploads/member/';
$webUploadDir = '../../uploads/member/';
@mkdir($uploadDir, 0755, true);
$maxFileSize = 5 * 1024 * 1024; // 5MB
$allowedExt = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
$error = null; // variabel error

// DELETE (PDO)
if (isset($_GET['delete'])) {
    try {
        $id = (int) $_GET['delete'];
        $stmt = $pdo->prepare("SELECT gambar FROM member WHERE id_member = ? LIMIT 1");
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row && !empty($row['gambar'])) {
            $file = $uploadDir . $row['gambar'];
            if (is_file($file)) @unlink($file);
        }
        $stmt = $pdo->prepare("DELETE FROM member WHERE id_member = ?");
        $stmt->execute([$id]);
    } catch (PDOException $e) {
        $error = "Gagal menghapus data: " . $e->getMessage();
    }
    if (!$error) {
        header("Location: ?page=member");
        exit;
    }
}

// EDIT (PDO)
$editData = null;
if (isset($_GET['edit'])) {
    try {
        $id = (int) $_GET['edit'];
        $stmt = $pdo->prepare("SELECT * FROM member WHERE id_member = ? LIMIT 1");
        $stmt->execute([$id]);
        $editData = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($editData === false) $editData = null;
    } catch (PDOException $e) {
        $error = "Gagal mengambil data edit: " . $e->getMessage();
    }
}

// INSERT / UPDATE (PDO)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Ambil semua data form member
    $nama_member = trim($_POST['nama_member'] ?? '');
    $jabatan = trim($_POST['jabatan'] ?? '');
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    $google_scholar = trim($_POST['google_scholar'] ?? '');
    $sinta = trim($_POST['sinta'] ?? '');
    $orcid = trim($_POST['orcid'] ?? '');
    $created_by_id = $_SESSION['id_admin'] ?? null;

    // 2. Validasi Teks
    if (!isset($error)) {
        if (strlen($nama_member) > 100) {
            $error = "Nama member terlalu panjang (maks 100 karakter).";
        } else if (empty($nama_member)) {
            $error = "Nama member wajib diisi.";
        }
    }

    // 3. Logika File (sama persis)
    $filename = $_POST['gambar_lama'] ?? ($editData['gambar'] ?? '');
    $is_update = !empty($_POST['id_member']);

    // --- Fungsi untuk membuat slug (nama file aman) ---
    function createSlug($name)
    {
        $slug = strtolower($name);
        $slug = preg_replace('/[^a-z0-9\s-]/', '', $slug);
        $slug = preg_replace('/[\s_]+/', '-', $slug);
        $slug = preg_replace('/-+/', '-', $slug);
        $slug = trim($slug, '-');
        $slug = substr($slug, 0, 100);
        $slug = trim($slug, '-');
        if (empty($slug)) {
            $slug = 'member'; // ganti default
        }
        return $slug;
    }

    $new_slug = createSlug($nama_member); // Buat slug dari NAMA MEMBER BARU

    // --- Handle jika ADA FILE BARU DI-UPLOAD ---
    if (!empty($_FILES['gambar']['name']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK && !isset($error)) {
        if ($_FILES['gambar']['size'] > $maxFileSize) {
            $error = "File terlalu besar. Maks 5MB.";
        } else {
            $info = @getimagesize($_FILES['gambar']['tmp_name']);
            if ($info === false) { $error = "File bukan gambar valid."; }
            else {
                $ext = strtolower(pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION));
                if (!in_array($ext, $allowedExt)) { $error = "Format gambar tidak diperbolehkan."; }
                else {
                    if (!empty($filename) && is_file($uploadDir . $filename)) {
                        @unlink($uploadDir . $filename);
                    }
                    // Ganti prefix jadi 'mem_'
                    $newName = $new_slug . '-' . uniqid() . '.' . $ext; 
                    if (move_uploaded_file($_FILES['gambar']['tmp_name'], $uploadDir . $newName)) {
                        $filename = $newName; 
                    } else {
                        $error = "Gagal menyimpan file. Cek permission folder upload.";
                    }
                }
            }
        }
    } 
    // --- Handle jika NAMA MEMBER diedit ---
    else if ($is_update && !empty($filename)) {
        $filename_no_ext = pathinfo($filename, PATHINFO_FILENAME);
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        $lastHyphenPos = strrpos($filename_no_ext, '-');
        if ($lastHyphenPos !== false) {
            $old_slug = substr($filename_no_ext, 0, $lastHyphenPos);
            $unique_part = substr($filename_no_ext, $lastHyphenPos);
            if ($old_slug !== $new_slug) {
                $new_filename_for_rename = $new_slug . $unique_part . '.' . $ext;
                if (@rename($uploadDir . $filename, $uploadDir . $new_filename_for_rename)) {
                    $filename = $new_filename_for_rename;
                }
            }
        }
    }

    // --- Eksekusi DB ---
    if (!isset($error)) {
        try {
            if ($is_update) {
                // UPDATE
                $id = (int) $_POST['id_member'];
                $sql = "UPDATE member SET 
                            nama_member = ?, 
                            jabatan = ?, 
                            deskripsi = ?, 
                            google_scholar = ?, 
                            sinta = ?, 
                            orcid = ?, 
                            gambar = ? 
                        WHERE id_member = ?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$nama_member, $jabatan, $deskripsi, $google_scholar, $sinta, $orcid, $filename, $id]);
            } else {
                // INSERT
                $sql = "INSERT INTO member (nama_member, jabatan, deskripsi, google_scholar, sinta, orcid, gambar, created_by) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$nama_member, $jabatan, $deskripsi, $google_scholar, $sinta, $orcid, $filename, $created_by_id]);
            }
            header("Location: ?page=member"); // Sukses, redirect
            exit;
        } catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
            if (isset($newName) && is_file($uploadDir . $newName)) {
                @unlink($uploadDir . $newName); // Hapus file yg gagal
            }
        }
    }
}

// FETCH SEMUA (PDO)
$stmt_list = null; // Inisialisasi
try {
    // Urutkan berdasarkan ID terlama (ASC)
    $query_sql = "SELECT * FROM member ORDER BY id_member ASC";
    $stmt_list = $pdo->query($query_sql);
} catch (PDOException $e) {
    if (!isset($error)) $error = "Gagal mengambil daftar member: " . $e->getMessage();
}
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Kelola Member</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <style>
        /* (Salin-tempel semua CSS dari produk.php ke sini) */
        /* ... */
        .card { background: #fff; padding: 18px; border-radius: 12px; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.06); margin-bottom: 18px }
        .form-grid { display: grid; gap: 12px }
        label { font-weight: 600; color: #2c3e50; margin-bottom: 6px; display: block }
        input[type="text"], textarea, input[type="file"] { width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #e6eef6 }
        textarea { min-height: 100px; resize: vertical }
        .btn { background: #27ae60; color: #fff; padding: 10px 14px; border-radius: 10px; border: none; cursor: pointer }
        .btn.cancel { background: #95a5a6; margin-left: 8px; text-decoration: none; color: #fff; padding: 10px 14px; border-radius: 10px }
        .preview img { max-width: 100%; max-height: 200px; width: auto; height: auto; object-fit: cover; border-radius: 8px; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08); margin-top: 8px; }
        .table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        .table thead { background: #2f3b46; color: #fff }
        .table td { word-wrap: break-word; overflow-wrap: break-word; }
        .table th, .table td { padding: 12px; border-bottom: 1px solid #eee; text-align: left; vertical-align: top }
        .actions a { display: inline-block; padding: 8px 12px; border-radius: 8px; color: #fff; text-decoration: none; margin-right: 6px }
        .edit { background: #f39c12 } .del { background: #e74c3c }
        .modal-backdrop { position: fixed; inset: 0; display: none; align-items: center; justify-content: center; background: rgba(10, 15, 25, 0.6); backdrop-filter: blur(3px); z-index: 9999; animation: fadeIn .18s ease }
        .modal-card { max-width: 90%; max-height: 90%; border-radius: 12px; display: flex; flex-direction: column; align-items: center; animation: popUp .18s ease }
        .modal-card img { max-width: 100%; max-height: 80vh; border-radius: 8px; }
        .modal-close { position: absolute; right: 18px; top: 18px; background: #111; color: #fff; width: 36px; height: 36px; border-radius: 999px; border: none; cursor: pointer; font-size: 18px; display: flex; align-items: center; justify-content: center }
        @keyframes popUp { from { transform: translateY(8px) scale(0.98); opacity: 0 } to { transform: translateY(0) scale(1); opacity: 1 } }
        @keyframes fadeIn { from { opacity: 0 } to { opacity: 1 } }
        .error { background: #fff0f0; color: #800; padding: 10px; border-radius: 8px; margin-bottom: 12px; border: 1px solid #f5c2c2 }
        .table .preview-btn-img { height: 60px; width: 60px; object-fit: cover; cursor: pointer; border-radius: 6px; border: none; background: transparent; }
    </style>
</head>
<body>
    <div class="card">
        <h2>Kelola Member</h2>

        <?php if (!empty($error)): ?><div class="error"><?= htmlspecialchars($error) ?></div><?php endif; ?>

        <form method="post" enctype="multipart/form-data" class="form-grid">
            <input type="hidden" name="id_member" value="<?= htmlspecialchars($editData['id_member'] ?? '') ?>">
            <input type="hidden" name="gambar_lama" value="<?= htmlspecialchars($editData['gambar'] ?? '') ?>">

            <div>
                <label>Nama Member</label>
                <input type="text" name="nama_member" required value="<?= htmlspecialchars($_POST['nama_member'] ?? $editData['nama_member'] ?? '') ?>">
            </div>
            
            <div>
                <label>Jabatan</label>
                <input type="text" name="jabatan" value="<?= htmlspecialchars($_POST['jabatan'] ?? $editData['jabatan'] ?? '') ?>">
            </div>

            <div>
                <label>Google Scholar (Link)</label>
                <input type="text" name="google_scholar" value="<?= htmlspecialchars($_POST['google_scholar'] ?? $editData['google_scholar'] ?? '') ?>">
            </div>

            <div>
                <label>Sinta (Link)</label>
                <input type="text" name="sinta" value="<?= htmlspecialchars($_POST['sinta'] ?? $editData['sinta'] ?? '') ?>">
            </div>
            
            <div>
                <label>ORCID (Link)</label>
                <input type="text" name="orcid" value="<?= htmlspecialchars($_POST['orcid'] ?? $editData['orcid'] ?? '') ?>">
            </div>

            <div>
                <label>Deskripsi</label>
                <textarea name="deskripsi"><?= htmlspecialchars($_POST['deskripsi'] ?? $editData['deskri/psi'] ?? '') ?></textarea>
            </div>

            <div>
                <label>Upload Gambar (maks 5MB)</label>
                <input type="file" name="gambar" accept="image/*" onchange="previewImage(event)">
                <div id="jsFileError" class="error" style="display:none; margin-top:8px;"></div>
                <div class="preview">
                    <?php if (!empty($editData['gambar'])): ?>
                        <img id="imgPreview" src="<?= htmlspecialchars($webUploadDir . $editData['gambar']) ?>" alt="Preview">
                    <?php else: ?>
                        <img id="imgPreview" src="" alt="Preview" style="display:none">
                    <?php endif; ?>
                </div>
            </div>

            <div style="display:flex;align-items:center">
                <button class="btn" type="submit"> Simpan</button>
                <?php if ($editData): ?>
                    <a class="btn cancel" href="?page=member">Batal</a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <div class="card">
        <h3>Daftar Member</h3>
        <table class="table">
            <thead>
                <tr style="text-align: left;">
                    <th style="width: 5%;">No.</th>
                    <th style="width: 10%;">Nama</th>
                    <th style="width: 20%;">Gambar</th>
                    <th style="width: 15%;">Jabatan</th>
                    <th style="width: 30%;">Link (Scholar, Sinta, Orcid)</th>
                    <th style="width: 15%;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $nomor = 1; 
                if ($stmt_list): 
                    while ($row = $stmt_list->fetch(PDO::FETCH_ASSOC)):
                ?>
                    <tr>
                        <td><?= $nomor ?></td>
                        <td><?= htmlspecialchars($row['nama_member']) ?></td>
                        <td>
                            <?php if (!empty($row['gambar'])): ?>
                                <?php $imgPath = htmlspecialchars($webUploadDir . $row['gambar']); ?>
                                <img src="<?= $imgPath ?>" 
                                     alt="Preview <?= htmlspecialchars($row['nama_member']) ?>" 
                                     class="preview-btn preview-btn-img" 
                                     data-src="<?= $imgPath ?>">
                            <?php else: ?>-<?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($row['jabatan']) ?></td>
                        <td>
                            <?php
                            $links = [
                                'Scholar' => $row['google_scholar'],
                                'Sinta' => $row['sinta'],
                                'ORCID' => $row['orcid']
                            ];
                            foreach ($links as $label => $link_url) {
                                if (!empty($link_url)) {
                                    if (strpos($link_url, '://') === false) {
                                        $link_url = 'https://' . $link_url;
                                    }
                                    echo "<a href='" . htmlspecialchars($link_url) . "' target='_blank'>$label</a><br>";
                                }
                            }
                            ?>
                        </td>
                        <td class="actions">
                            <a class="edit" href="?page=member&edit=<?= (int)$row['id_member'] ?>"> Edit</a>
                            <a class="del" href="?page=member&delete=<?= (int)$row['id_member'] ?>" onclick="return confirm('Yakin hapus?')"> Hapus</a>
                        </td>
                    </tr>
                <?php
                    $nomor++; 
                    endwhile;
                endif; 
                ?>
            </tbody>
        </table>
    </div>

    <div id="modalBackdrop" class="modal-backdrop" role="dialog" aria-hidden="true">
        <div class="modal-card" role="document">
            <button id="modalClose" class="modal-close" aria-label="Tutup">×</button>
            <img id="modalImage" src="" alt="Preview Gambar">
        </div>
    </div>

    <script>
    // Ambil batas ukuran file dari PHP
    const MAX_FILE_SIZE_BYTES = <?= $maxFileSize ?>;
    const MAX_FILE_SIZE_MB = MAX_FILE_SIZE_BYTES / 1024 / 1024;
    const submitButton = document.querySelector('form .btn[type="submit"]');
    const fileErrorDiv = document.getElementById('jsFileError');

    function previewImage(event) {
        const img = document.getElementById('imgPreview');
        const fileInput = event.target;
        
        fileErrorDiv.style.display = 'none';
        submitButton.disabled = false;

        if (!fileInput.files || !fileInput.files[0]) {
            img.style.display = 'none';
            return;
        }
        const file = fileInput.files[0];

        // Cek Ukuran File
        if (file.size > MAX_FILE_SIZE_BYTES) {
            fileErrorDiv.textContent = 'File terlalu besar. Maks ' + MAX_FILE_SIZE_MB + 'MB.';
            fileErrorDiv.style.display = 'block';
            img.style.display = 'none';
            fileInput.value = ''; 
            submitButton.disabled = true;
        } else {
            img.src = URL.createObjectURL(file);
            img.style.display = 'block';
            submitButton.disabled = false;
        }
    }

    // Modal JS (sama persis)
    const modal = document.getElementById('modalBackdrop');
    const modalImg = document.getElementById('modalImage');
    const closeBtn = document.getElementById('modalClose');
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.preview-btn');
        if (btn) {
            const src = btn.getAttribute('data-src');
            if (!src) return;
            modalImg.src = src;
            modal.style.display = 'flex';
            modal.setAttribute('aria-hidden', 'false');
        }
    });
    closeBtn.addEventListener('click', closeModal);
    modal.addEventListener('click', function(e) {
        if (e.target === modal) closeModal();
    });
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeModal();
    });
    function closeModal() {
        modal.style.display = 'none';
        modal.setAttribute('aria-hidden', 'true');
        modalImg.src = '';
    }
    </script>
</body>
</html>