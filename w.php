<?php
// src/module/produk/index.php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header("Location: ../../login.php");
    exit;
}

// config upload
$uploadDir = __DIR__ . '/../../../uploads/produk/';
$webUploadDir = '../../uploads/produk/';
@mkdir($uploadDir, 0755, true);
$maxFileSize = 5 * 1024 * 1024; // 5MB
$allowedExt = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
$error = null; // variabel error

// DELETE (PDO)
if (isset($_GET['delete'])) {
    try {
        $id = (int) $_GET['delete'];

        // 1. Ambil nama gambar
        $stmt = $pdo->prepare("SELECT gambar FROM produk WHERE id_produk = ? LIMIT 1");
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // 2. Hapus file jika ada
        if ($row && !empty($row['gambar'])) {
            $file = $uploadDir . $row['gambar'];
            if (is_file($file)) @unlink($file);
        }

        // 3. Hapus data dari DB
        $stmt = $pdo->prepare("DELETE FROM produk WHERE id_produk = ?");
        $stmt->execute([$id]);
    } catch (PDOException $e) {
        $error = "Gagal menghapus data: " . $e->getMessage();
    }

    if (!$error) {
        header("Location: ?page=produk");
        exit;
    }
}

// EDIT (PDO)
$editData = null;
if (isset($_GET['edit'])) {
    try {
        $id = (int) $_GET['edit'];
        $stmt = $pdo->prepare("SELECT * FROM produk WHERE id_produk = ? LIMIT 1");
        $stmt->execute([$id]);
        $editData = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($editData === false) $editData = null;
    } catch (PDOException $e) {
        $error = "Gagal mengambil data edit: " . $e->getMessage();
    }
}

// INSERT / UPDATE (PDO)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama'] ?? '');
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    $link = trim($_POST['link_produk'] ?? '');
    $created_by_id = $_SESSION['id_admin'] ?? null;

    if (!isset($error)) {
        if (strlen($nama) > 100) { // batas 100 karakter
            $error = "Nama produk terlalu panjang (maks 100 karakter).";
        } else if (empty($nama)) {
            $error = "Nama produk wajib diisi.";
        }
        // bisa juga tambahkan validasi lain di sini (misal: deskripsi)
    }

    // default filename
    $filename = $_POST['gambar_lama'] ?? ($editData['gambar'] ?? '');
    $is_update = !empty($_POST['id_produk']); // Cek apakah ini mode UPDATE

    // --- Fungsi untuk membuat slug (nama file aman) ---
    function createSlug($name)
    {
        $slug = strtolower($name);
        $slug = preg_replace('/[^a-z0-9\s-]/', '', $slug); // hapus karakter aneh
        $slug = preg_replace('/[\s_]+/', '-', $slug); // ganti spasi jadi strip
        $slug = preg_replace('/-+/', '-', $slug); // hapus strip ganda
        $slug = trim($slug, '-'); // hapus strip di awal/akhir
        $slug = substr($slug, 0, 100); // mengambil 100 karakter pertama saja
        $slug = trim($slug, '-'); // membersihkan lagi untuk jaga-jaga kepotong di strip
        if (empty($slug)) {
            $slug = 'produk';
        }
        return $slug;
    }

    $new_slug = createSlug($nama); // Buat slug dari NAMA PRODUK BARU

    // --- 1. Handle jika ADA FILE BARU DI-UPLOAD ---//
    if (!empty($_FILES['gambar']['name']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK && !isset($error)) {
        // ... (Validasi file: size, type, ext - ini sudah benar) ...
        if ($_FILES['gambar']['size'] > $maxFileSize) {
            $error = "File terlalu besar. Maks 5MB.";
        } else {
            $info = @getimagesize($_FILES['gambar']['tmp_name']);
            if ($info === false) {
                $error = "File bukan gambar valid.";
            } else {
                $ext = strtolower(pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION));
                if (!in_array($ext, $allowedExt)) {
                    $error = "Format gambar tidak diperbolehkan.";
                } else {
                    // hapus file lama jika ada
                    if (!empty($filename) && is_file($uploadDir . $filename)) {
                        @unlink($uploadDir . $filename);
                    }

                    // Buat nama baru: slug BARU + uniqid BARU + ekstensi
                    $newName = $new_slug . '-' . uniqid() . '.' . $ext;

                    if (move_uploaded_file($_FILES['gambar']['tmp_name'], $uploadDir . $newName)) {
                        $filename = $newName; // Sukses! $filename diisi nama baru
                    } else {
                        $error = "Gagal menyimpan file. Cek permission folder upload.";
                    }
                }
            }
        }
    }
    // --- 2. Handle jika TIDAK ADA FILE BARU (tapi NAMA PRODUK diedit) ---
    else if ($is_update && !empty($filename)) {
        // Ini mode UPDATE, tidak ada file baru, dan file lama ADA.
        // Kita cek apakah nama produknya berubah?

        $filename_no_ext = pathinfo($filename, PATHINFO_FILENAME);
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        $lastHyphenPos = strrpos($filename_no_ext, '-'); // Cari strip terakhir (pemisah uniqid)

        if ($lastHyphenPos !== false) {
            $old_slug = substr($filename_no_ext, 0, $lastHyphenPos);
            $unique_part = substr($filename_no_ext, $lastHyphenPos); // misal: "-654c123"

            if ($old_slug !== $new_slug) {
                // NAMA PRODUK BERUBAH!
                $new_filename_for_rename = $new_slug . $unique_part . '.' . $ext; // misal: "produk-baru-654c123.jpg"

                // Coba rename file di server
                if (@rename($uploadDir . $filename, $uploadDir . $new_filename_for_rename)) {
                    // Rename berhasil, update $filename untuk disimpan ke DB
                    $filename = $new_filename_for_rename;
                } else {
                    // Rename gagal (mungkin permission), biarkan $filename tetap yang lama.
                    // Jangan set $error agar data teks tetap ter-update.
                }
            }
        }
    }

    // --- 3. Eksekusi DB ---
    if (!isset($error)) {
        try {
            if ($is_update) {
                // UPDATE
                $id = (int) $_POST['id_produk'];
                $sql = "UPDATE produk SET nama = ?, deskripsi = ?, gambar = ?, link_produk = ? WHERE id_produk = ?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$nama, $deskripsi, $filename, $link, $id]);
            } else {
                // INSERT
                $sql = "INSERT INTO produk (nama, deskripsi, gambar, link_produk, created_by) VALUES (?, ?, ?, ?, ?)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$nama, $deskripsi, $filename, $link, $created_by_id]);
            }
            header("Location: ?page=produk"); // Sukses, redirect
            exit;
        } catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
            // Cek apakah kita BARU SAJA upload file di proses ini
            if (isset($newName) && is_file($uploadDir . $newName)) {
                @unlink($uploadDir . $newName); // Hapus file yg gagal
            }
        }
    }
}

// FETCH SEMUA (PDO)
$stmt_list = null; // Inisialisasi
try {
    $query_sql = "SELECT * FROM produk ORDER BY id_produk ASC";
    $stmt_list = $pdo->query($query_sql);
} catch (PDOException $e) {
    if (!isset($error)) $error = "Gagal mengambil daftar produk: " . $e->getMessage();
}
?>
<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Kelola Produk</title>
    <meta name="viewport" content="width=device-width,initial-scale-1">
    <style>
        /* css */
        .card {
            background: #fff;
            padding: 18px;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.06);
            margin-bottom: 18px
        }

        .form-grid {
            display: grid;
            gap: 12px
        }

        label {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 6px;
            display: block
        }

        input[type="text"],
        textarea,
        input[type="file"] {
            width: 100%;
            padding: 10px;
            border-radius: 8px;
            border: 1px solid #e6eef6
        }

        textarea {
            min-height: 100px;
            resize: vertical
        }

        .btn {
            background: #27ae60;
            color: #fff;
            padding: 10px 14px;
            border-radius: 10px;
            border: none;
            cursor: pointer
        }

        .btn.cancel {
            background: #95a5a6;
            margin-left: 8px;
            text-decoration: none;
            color: #fff;
            padding: 10px 14px;
            border-radius: 10px
        }

        .preview img {
            max-width: 100%;
            max-height: 500px;
            width: auto;
            height: auto;
            object-fit: cover;
            /* Biar fotonya pas */
            border-radius: 8px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            margin-top: 8px;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .table thead {
            background: #2f3b46;
            color: #fff
        }

        .table td {
            word-wrap: break-word;
            overflow-wrap: break-word;
            /* max-width: 200px; */
        }

        .table th,
        .table td {
            padding: 12px;
            border-bottom: 1px solid #eee;
            text-align: left;
            vertical-align: top
        }

        .actions a {
            display: inline-block;
            padding: 8px 12px;
            border-radius: 8px;
            color: #fff;
            text-decoration: none;
            margin-right: 6px
        }

        .edit {
            background: #f39c12
        }

        .del {
            background: #e74c3c
        }

        .preview-btn {
            background: #34495e;
            padding: 8px 12px;
            border-radius: 8px;
            color: #fff;
            text-decoration: none;
            cursor: pointer;
            border: none
        }

        .modal-backdrop {
            position: fixed;
            inset: 0;
            display: none;
            align-items: center;
            justify-content: center;
            background: rgba(10, 15, 25, 0.6);
            backdrop-filter: blur(3px);
            z-index: 9999;
            animation: fadeIn .18s ease
        }

        .modal-card {
            max-width: 90%;
            max-height: 90%;
            border-radius: 12px;
            display: flex;
            flex-direction: column;
            align-items: center;
            animation: popUp .18s ease
        }

        .modal-card img {
            max-width: 100%;
            max-height: 80vh;
            border-radius: 8px;
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.25)
        }

        .modal-close {
            position: absolute;
            right: 18px;
            top: 18px;
            background: #111;
            color: #fff;
            width: 36px;
            height: 36px;
            border-radius: 999px;
            border: none;
            cursor: pointer;
            font-size: 18px;
            display: flex;
            align-items: center;
            justify-content: center
        }

        @keyframes popUp {
            from {
                transform: translateY(8px) scale(0.98);
                opacity: 0
            }

            to {
                transform: translateY(0) scale(1);
                opacity: 1
            }
        }

        @keyframes fadeIn {
            from {
                opacity: 0
            }

            to {
                opacity: 1
            }
        }

        .error {
            background: #fff0f0;
            color: #800;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 12px;
            border: 1px solid #f5c2c2
        }

        .table .preview-btn-img {
            height: 90px;
            width: 130px;
            object-fit: contain;
            cursor: pointer;
            border-radius: 6px;
            border: none;
            background: transparent;
        }
    </style>
</head>

<body>
    <div class="card">
        <h2> Kelola Produk</h2>

        <?php if (!empty($error)): ?><div class="error"><?= htmlspecialchars($error) ?></div><?php endif; ?>

        <form method="post" enctype="multipart/form-data" class="form-grid">
            <input type="hidden" name="id_produk" value="<?= htmlspecialchars($editData['id_produk'] ?? '') ?>">
            <input type="hidden" name="gambar_lama" value="<?= htmlspecialchars($editData['gambar'] ?? '') ?>">

            <div>
                <label>Nama Produk</label>
                <input type="text" name="nama" required value="<?= htmlspecialchars($_POST['nama'] ?? $editData['nama'] ?? '') ?>">
            </div>

            <div>
                <label>Deskripsi</label>
                <textarea name="deskripsi"><?= htmlspecialchars($_POST['deskripsi'] ?? $editData['deskripsi'] ?? '') ?></textarea>
            </div>

            <div>
                <label>Upload Gambar (maks 5MB)</label>
                <input type="file" name="gambar" accept="image/*" onchange="previewImage(event)">
                <div class="preview">
                    <?php if (!empty($editData['gambar'])): ?>
                        <img id="imgPreview" src="<?= htmlspecialchars($webUploadDir . $editData['gambar']) ?>" alt="Preview">
                    <?php else: ?>
                        <img id="imgPreview" src="" alt="Preview" style="display:none">
                    <?php endif; ?>
                </div>
            </div>

            <div>
                <label>Link Produk</label>
                <input type="text" name="link_produk" value="<?= htmlspecialchars($_POST['link_produk'] ?? $editData['link_produk'] ?? '') ?>">
            </div>

            <div style="display:flex;align-items:center">
                <button class="btn" type="submit"> Simpan</button>
                <?php if ($editData): ?>
                    <a class="btn cancel" href="?page=produk">Batal</a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <div class="card">
        <h3>Daftar Produk</h3>
        <table class="table">
            <thead>
                <tr style="text-align: left;">
                    <th style="width: 10%;">No.</th>
                    <th style="width: 15%;">Nama</th>
                    <th style="width: 25%;">Gambar</th>
                    <th style="width: 30%;">Deskripsi</th>
                    <th style="width: 15%;">Link</th>
                    <th style="width: 15%;">Aksi</th>
                </tr>
            </thead>

            <tbody>
                <?php
                $nomor = 1;
                // LOOPING (PDO)
                if ($stmt_list): // Cek jika $stmt_list berhasil dibuat
                    while ($row = $stmt_list->fetch(PDO::FETCH_ASSOC)):
                ?>
                        <tr>
                            <td><?= $nomor ?></td>
                            <td><?= htmlspecialchars($row['nama']) ?></td>
                            <td>
                                <?php if (!empty($row['gambar'])): ?>
                                    <?php
                                    // Bikin variabel biar rapi
                                    $imgPath = htmlspecialchars($webUploadDir . $row['gambar']);
                                    ?>
                                    <img src="<?= $imgPath ?>"
                                        alt="Preview <?= htmlspecialchars($row['nama']) ?>"
                                        class="preview-btn preview-btn-img"
                                        data-src="<?= $imgPath ?>">
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars(strlen($row['deskripsi']) > 120 ? substr($row['deskripsi'], 0, 120) . '...' : $row['deskripsi']) ?></td>
                            <td>
                                <?php
                                if (!empty($row['link_produk'])):

                                    // 1. Ambil & perbaiki link (sama seperti sebelumnya)
                                    $link_url = $row['link_produk'];
                                    if (strpos($link_url, '://') === false) {
                                        $link_url = 'https://' . $link_url;
                                    }

                                    // 2. Buat teks tampilan (potong jika > 30 karakter)
                                    $link_display = $link_url;
                                    if (strlen($link_display) > 30) {
                                        $link_display = substr($link_display, 0, 30) . '...';
                                    }

                                ?>
                                    <a href="<?= htmlspecialchars($link_url) ?>"
                                        title="<?= htmlspecialchars($link_url) ?>"
                                        target="_blank">
                                        <?= htmlspecialchars($link_display) ?>
                                    </a>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                            <td class="actions">
                                <a class="edit" href="?page=produk&edit=<?= (int)$row['id_produk'] ?>"> Edit</a>
                                <a class="del" href="?page=produk&delete=<?= (int)$row['id_produk'] ?>" onclick="return confirm('Yakin hapus?')"> Hapus</a>
                            </td>
                        </tr>
                <?php
                    $nomor++;
                    endwhile;
                endif; // Akhir dari 'if ($stmt_list)'
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
        function previewImage(event) {
            const img = document.getElementById('imgPreview');
            if (!event.target.files || !event.target.files[0]) return;
            img.src = URL.createObjectURL(event.target.files[0]);
            img.style.display = 'block';
        }
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

<thead>
    