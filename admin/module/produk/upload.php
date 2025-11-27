<?php
// admin/module/produk/upload.php

function handleUpload($fieldName, $oldFile, $uploadDir, $allowedExt, $maxSize, $newSlug) { // UBAH PARAMETER KE $newSlug
    
    // --- 1. KASUS: TIDAK ADA FILE BARU DI-UPLOAD (MODE RENAME/UPDATE SLUG) ---
    if (!isset($_FILES[$fieldName]) || $_FILES[$fieldName]['error'] === UPLOAD_ERR_NO_FILE) {
        
        // Cek apakah ada file lama dan apakah perlu diganti namanya
        if (!empty($oldFile) && is_file($uploadDir . $oldFile)) {
            
            $filename_no_ext = pathinfo($oldFile, PATHINFO_FILENAME);
            $ext = pathinfo($oldFile, PATHINFO_EXTENSION);
            $lastHyphenPos = strrpos($filename_no_ext, '-'); 
            
            // Periksa apakah format nama file memiliki bagian unik (uniqid)
            if ($lastHyphenPos !== false) {
                // Ekstrak slug lama dan bagian unik
                $oldSlug = substr($filename_no_ext, 0, $lastHyphenPos);
                $unique_part = substr($filename_no_ext, $lastHyphenPos); // misal: "-123456abc"

                // Hanya ganti nama jika slug lama TIDAK SAMA dengan slug baru
                if ($oldSlug !== $newSlug) {
                    // Slug BERUBAH. Lakukan rename.
                    $new_filename_for_rename = $newSlug . $unique_part . '.' . $ext;

                    // Coba ganti nama file di server
                    if (@rename($uploadDir . $oldFile, $uploadDir . $new_filename_for_rename)) {
                        // Rename berhasil, kembalikan nama file baru
                        return $new_filename_for_rename;
                    }
                    // Jika rename gagal, kita biarkan nama lama yang tersimpan di DB
                    return $oldFile; 
                }
            }
        }
        
        return $oldFile ?? ''; // Tidak ada file baru, tidak ada perubahan slug, atau tidak ada file lama.
    }

    // --- 2. KASUS: ADA FILE BARU DI-UPLOAD (MODE INSERT ATAU REPLACE) ---
    
    $file = $_FILES[$fieldName];
    $ext  = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    if (!in_array($ext, $allowedExt)) {
        throw new Exception("File tidak diperbolehkan, hanya: " . implode(', ', $allowedExt));
    }

    // Hitung ukuran maksimal dalam MB untuk pesan error
    if ($file['size'] > $maxSize) {
        $maxMB = round($maxSize / 1024 / 1024, 1);
        throw new Exception("File terlalu besar. Maksimal " . $maxMB . "MB.");
    }
    
    // Buat nama file baru menggunakan $newSlug dan waktu unik
    $filename = $newSlug . '-' . time() . '.' . $ext;
    $target = $uploadDir . $filename;

    if (!move_uploaded_file($file['tmp_name'], $target)) {
        throw new Exception("Gagal mengunggah file. Cek permission folder upload.");
    }

    // hapus file lama (karena sudah diganti yang baru)
    if ($oldFile && file_exists($uploadDir . $oldFile)) {
        @unlink($uploadDir . $oldFile);
    }

    return $filename;
}
?>