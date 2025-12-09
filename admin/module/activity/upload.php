<?php
// admin/module/activity/upload.php

$serverAdminBase = dirname(__DIR__, 3) . '/'; 
$thumbDir = $serverAdminBase . 'public/uploads/thumb/activity-thumb/';

if (!is_dir($thumbDir)) { @mkdir($thumbDir, 0755, true); }

function handleUpload($fieldName, $oldFile, $uploadDir, $allowedExt, $maxSize, $newSlug) {
    global $thumbDir;

    if (!isset($_FILES[$fieldName]) || $_FILES[$fieldName]['error'] === UPLOAD_ERR_NO_FILE) {
        if (!empty($oldFile) && is_file($uploadDir . $oldFile)) {
            // Logika Rename (sama seperti fasilitas/produk)
            $filename_no_ext = pathinfo($oldFile, PATHINFO_FILENAME);
            $ext = pathinfo($oldFile, PATHINFO_EXTENSION);
            $lastHyphenPos = strrpos($filename_no_ext, '-'); 
            
            if ($lastHyphenPos !== false) {
                $oldSlug = substr($filename_no_ext, 0, $lastHyphenPos);
                $unique_part = substr($filename_no_ext, $lastHyphenPos);

                if ($oldSlug !== $newSlug) {
                    $new_filename_for_rename = $newSlug . $unique_part . '.' . $ext;
                    $old_thumb_name = pathinfo($oldFile, PATHINFO_FILENAME) . '-thumb.' . $ext;
                    $new_thumb_name = str_replace(".$ext", "-thumb.$ext", $new_filename_for_rename);

                    if (is_file($thumbDir . $old_thumb_name)) @rename($thumbDir . $old_thumb_name, $thumbDir . $new_thumb_name);
                    if (@rename($uploadDir . $oldFile, $uploadDir . $new_filename_for_rename)) return $new_filename_for_rename;
                    return $oldFile; 
                }
            }
        }
        return $oldFile ?? ''; 
    }

    $file = $_FILES[$fieldName];
    if ($file['error'] !== UPLOAD_ERR_OK) throw new Exception("Gagal upload. Kode: " . $file['error']);
    if ($file['size'] > $maxSize) throw new Exception("File terlalu besar. Maksimal 5MB.");

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime_type = finfo_file($finfo, $file['tmp_name']);
    $allowedMime = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    if (!in_array($mime_type, $allowedMime)) throw new Exception("Tipe file tidak valid.");

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowedExt)) throw new Exception("Ekstensi tidak diizinkan.");
    
    if ($oldFile) {
        $old_thumb_name = pathinfo($oldFile, PATHINFO_FILENAME) . '-thumb.' . pathinfo($oldFile, PATHINFO_EXTENSION);
        if (is_file($thumbDir . $old_thumb_name)) @unlink($thumbDir . $old_thumb_name);
    }
    if ($oldFile && file_exists($uploadDir . $oldFile)) @unlink($uploadDir . $oldFile);
    
    $filename = $newSlug . '-' . time() . '.' . $ext;
    $target = $uploadDir . $filename;

    if (!move_uploaded_file($file['tmp_name'], $target)) throw new Exception("Gagal memindahkan file upload.");

    $filename_thumb = str_replace(".$ext", "-thumb.$ext", $filename);
    $target_thumb = $thumbDir . $filename_thumb;
    createStaticThumbnail($target, $target_thumb, 300, 80);

    return $filename;
}

function createStaticThumbnail(string $sourcePath, string $targetPath, int $targetWidth = 300, int $quality = 90): bool
{
    if (!extension_loaded('gd')) return false;
    $ext = strtolower(pathinfo($sourcePath, PATHINFO_EXTENSION));
    if ($ext === 'gif') return copy($sourcePath, $targetPath); 

    try {
        switch ($ext) {
            case 'jpg': case 'jpeg': $img = imagecreatefromjpeg($sourcePath); break;
            case 'png': $img = imagecreatefrompng($sourcePath); break;
            case 'webp': if (function_exists('imagecreatefromwebp')) $img = imagecreatefromwebp($sourcePath); else return false; break;
            default: return false;
        }
        if (!$img) return false;
        $width = imagesx($img); $height = imagesy($img);
        $targetHeight = floor($height * ($targetWidth / $width));
        $tmp = imagecreatetruecolor($targetWidth, $targetHeight);
        if ($ext === 'png') { imagealphablending($tmp, false); imagesavealpha($tmp, true); }
        imagecopyresampled($tmp, $img, 0, 0, 0, 0, $targetWidth, $targetHeight, $width, $height);
        if ($ext === 'png') imagepng($tmp, $targetPath, 9); else imagejpeg($tmp, $targetPath, $quality);
        return true;
    } catch (Exception $e) { return false; }
}
?>