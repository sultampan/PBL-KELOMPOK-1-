<?php
// admin/module/produk/upload.php

// Tentukan path server untuk folder thumbnail (admin/uploads/produk-thumb/)
// Dari admin/module/produk/ naik 2 tingkat (ke admin/) lalu masuk ke uploads/produk-thumb/
$serverAdminBase = dirname(__DIR__, 3) . '/'; 
$thumbDir = $serverAdminBase . 'public/uploads/thumb/produk-thumb/';;

// Pastikan folder thumbnail ada (Harus dipanggil saat inisialisasi pertama kali)
if (!is_dir($thumbDir)) {
    @mkdir($thumbDir, 0755, true);
}

function handleUpload($fieldName, $oldFile, $uploadDir, $allowedExt, $maxSize, $newSlug) {
    global $thumbDir;
    // --- 1. KASUS: TIDAK ADA FILE BARU DI-UPLOAD (MODE RENAME/UPDATE SLUG) ---
    // ... (Logika ini tetap sama) ...
    if (!isset($_FILES[$fieldName]) || $_FILES[$fieldName]['error'] === UPLOAD_ERR_NO_FILE) {
        // ... (Logika rename file lama) ...
        // ... (Kode rename yang sudah ada) ...

        // Cek apakah ada file lama dan apakah perlu diganti namanya
        if (!empty($oldFile) && is_file($uploadDir . $oldFile)) {
            
            $filename_no_ext = pathinfo($oldFile, PATHINFO_FILENAME);
            $ext = pathinfo($oldFile, PATHINFO_EXTENSION);
            $lastHyphenPos = strrpos($filename_no_ext, '-'); 
            
            // Periksa apakah format nama file memiliki bagian unik (uniqid)
            if ($lastHyphenPos !== false) {
                // Ekstrak slug lama dan bagian unik
                $oldSlug = substr($filename_no_ext, 0, $lastHyphenPos);
                $unique_part = substr($filename_no_ext, $lastHyphenPos);

                // Hanya ganti nama jika slug lama TIDAK SAMA dengan slug baru
                if ($oldSlug !== $newSlug) {
                    $new_filename_for_rename = $newSlug . $unique_part . '.' . $ext;
                    
                    // TAMBAHAN: Handle rename file thumbnail di lokasi baru
                    $old_thumb_name = pathinfo($oldFile, PATHINFO_FILENAME) . '-thumb.' . $ext;
                    $new_thumb_name = str_replace(".$ext", "-thumb.$ext", $new_filename_for_rename);

                    if (is_file($thumbDir . $old_thumb_name)) {
                        @rename($thumbDir . $old_thumb_name, $thumbDir . $new_thumb_name);
                    }

                    if (@rename($uploadDir . $oldFile, $uploadDir . $new_filename_for_rename)) {
                        return $new_filename_for_rename;
                    }
                    return $oldFile; 
                }
            }
        }
        
        return $oldFile ?? ''; 
    }

    // --- 2. KASUS: ADA FILE BARU DI-UPLOAD (MODE INSERT ATAU REPLACE) ---
    
    $file = $_FILES[$fieldName];

    // LANGKAH 1: Cek Kesalahan Upload Internal (Sistem)
    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new Exception("Gagal mengunggah file. Kode error: " . $file['error']);
    }

    // LANGKAH 2: Cek Ukuran
    if ($file['size'] > $maxSize) {
        $maxMB = round($maxSize / 1024 / 1024, 1);
        throw new Exception("File terlalu besar. Maksimal " . $maxMB . "MB.");
    }

    // LANGKAH 3: Cek Tipe MIME Sejati (File Signature)
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime_type = finfo_file($finfo, $file['tmp_name']);
    // finfo_close($finfo);

    // Daftar MIME Tipe yang diizinkan (HARUS SAMA dengan $allowedExt di save.php)
    $allowedMime = [
        'image/jpeg',   // untuk .jpg, .jpeg
        'image/png',    // untuk .png
        'image/gif',    // untuk .gif
        'image/webp',   // untuk .webp
    ];
    
    if (!in_array($mime_type, $allowedMime)) {
        // Hapus file sementara jika tipe MIME tidak sesuai
        @unlink($file['tmp_name']); 
        throw new Exception("Tipe file (`" . $mime_type . "`) tidak diizinkan.");
    }

    // LANGKAH 4: Cek Ekstensi
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    if (!in_array($ext, $allowedExt)) {
        // Hapus file sementara (meskipun tipe MIME sudah benar, kita tetap tolak jika ekstensi aneh)
        @unlink($file['tmp_name']); 
        throw new Exception("Ekstensi file `." . $ext . "` tidak diizinkan. Hanya: " . implode(', ', $allowedExt));
    }
    
    // LANGKAH 5: Proses Penyimpanan

    // 🚨 TAMBAHAN: Hapus file thumbnail LAMA (jika ada, sebelum upload baru)
    if ($oldFile) {
        $old_thumb_name = pathinfo($oldFile, PATHINFO_FILENAME) . '-thumb.' . pathinfo($oldFile, PATHINFO_EXTENSION);
        if (is_file($thumbDir . $old_thumb_name)) {
            @unlink($thumbDir . $old_thumb_name); // Hapus thumbnail dari admin/uploads/produk-thumb/
        }
    }

    // hapus file lama (karena sudah diganti yang baru)
    if ($oldFile && file_exists($uploadDir . $oldFile)) {
        @unlink($uploadDir . $oldFile);
    }
    
    // Buat nama file baru menggunakan $newSlug dan waktu unik
    $filename = $newSlug . '-' . time() . '.' . $ext;
    $target = $uploadDir . $filename;

    if (!move_uploaded_file($file['tmp_name'], $target)) {
        throw new Exception("Gagal memindahkan file yang diunggah. Cek permission folder upload.");
    }

    // 🚨 TAMBAHAN PENTING: BUAT THUMBNAIL STATIS
    $ext_lower = strtolower($ext);
    
        $filename_thumb = str_replace(".$ext", "-thumb.$ext", $filename);
        $target_thumb = $thumbDir . $filename_thumb;

        // Buat thumbnail (lebar 300px, kualitas 80)
        createStaticThumbnail($target, $target_thumb, 300, 80);
    return $filename;
}

/**
 * Membuat thumbnail dari gambar sumber menggunakan GD.
 * Hanya mendukung JPG, PNG, dan WEBP.
 */
function createStaticThumbnail(string $sourcePath, string $targetPath, int $targetWidth = 300, int $quality = 90): bool
{
    if (!extension_loaded('gd')) {
        error_log("GD extension not available. Cannot create thumbnail.");
        return false;
    }
    
    $ext = strtolower(pathinfo($sourcePath, PATHINFO_EXTENSION));

    if ($ext === 'gif') {
        return copy($sourcePath, $targetPath); 
    }

    // Jika bukan GIF, lanjutkan dengan proses GD kompresi dan resizing
    if (!extension_loaded('gd')) {
        error_log("GD extension not available. Cannot create thumbnail.");
        return false;
    }
    
    try {
        // Tentukan fungsi pembaca berdasarkan ekstensi
        switch ($ext) {
            case 'jpg':
            case 'jpeg':
                $img = imagecreatefromjpeg($sourcePath);
                break;
            case 'png':
                $img = imagecreatefrompng($sourcePath);
                break;
            case 'webp':
                // imagecreatefromwebp membutuhkan PHP 5.4+
                if (function_exists('imagecreatefromwebp')) {
                    $img = imagecreatefromwebp($sourcePath);
                } else {
                    return false; // WebP not supported
                }
                break;
            default:
                return false; // Tidak support atau GIF/jenis lain
        }
        
        if (!$img) return false;
        
        $width = imagesx($img);
        $height = imagesy($img);

        // Hitung tinggi baru berdasarkan lebar target (menjaga rasio aspek)
        $targetHeight = floor($height * ($targetWidth / $width));
        
        // Buat gambar baru yang di-resize
        $tmp = imagecreatetruecolor($targetWidth, $targetHeight);
        
        // Untuk PNG, pertahankan transparansi
        if ($ext === 'png') {
            imagealphablending($tmp, false);
            imagesavealpha($tmp, true);
        }

        // Resampling
        imagecopyresampled($tmp, $img, 0, 0, 0, 0, $targetWidth, $targetHeight, $width, $height);

        // Tentukan fungsi penyimpanan
        if ($ext === 'png') {
            imagepng($tmp, $targetPath, 9); // Kualitas PNG (0-9)
        } else {
            imagejpeg($tmp, $targetPath, $quality); // Kualitas JPEG (0-100)
        }
        return true;
        
    } catch (Exception $e) {
        error_log("Thumbnail creation failed: " . $e->getMessage());
        return false;
    }
}
?>