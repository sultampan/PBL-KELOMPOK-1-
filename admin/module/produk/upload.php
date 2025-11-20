<?php
// admin/module/produk/upload.php

function handleUpload($fieldName, $oldFile, $uploadDir, $allowedExt, $maxSize, $slug) {
    if (!isset($_FILES[$fieldName]) || $_FILES[$fieldName]['error'] === UPLOAD_ERR_NO_FILE) {
        return $oldFile ?? '';
    }

    $file = $_FILES[$fieldName];
    $ext  = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    if (!in_array($ext, $allowedExt)) {
        throw new Exception("File tidak diperbolehkan, hanya: " . implode(', ', $allowedExt));
    }

    if ($file['size'] > $maxSize) {
        throw new Exception("File terlalu besar. Maksimal 5MB");
    }

    $filename = $slug . '-' . time() . '.' . $ext;
    $target = $uploadDir . $filename;

    if (!move_uploaded_file($file['tmp_name'], $target)) {
        throw new Exception("Gagal mengunggah file");
    }

    // hapus file lama kalau ada
    if ($oldFile && file_exists($uploadDir . $oldFile)) {
        @unlink($uploadDir . $oldFile);
    }

    return $filename;
}
