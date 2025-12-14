<?php
// admin/module/partner/upload.php
$serverAdminBase = dirname(__DIR__, 3) . '/'; 
$thumbDir = $serverAdminBase . 'public/uploads/thumb/partner-thumb/';
if (!is_dir($thumbDir)) @mkdir($thumbDir, 0755, true);

function handleUpload($fieldName, $oldFile, $uploadDir, $allowedExt, $maxSize, $newSlug) {
    global $thumbDir;
    
    // Logic rename file lama jika nama berubah (Sama seperti fasilitas)
    if (!isset($_FILES[$fieldName]) || $_FILES[$fieldName]['error'] === UPLOAD_ERR_NO_FILE) {
        if ($oldFile && is_file($uploadDir . $oldFile)) {
            $info = pathinfo($oldFile);
            $parts = explode('-', $info['filename']);
            $oldSlug = $parts[0]; // Simplifikasi logic slug
            // (Disini bisa ditambahkan logic rename canggih kayak fasilitas jika perlu)
            // Untuk ringkas, kita return file lama saja jika tidak ada upload baru
        }
        return $oldFile ?? '';
    }

    $file = $_FILES[$fieldName];
    if ($file['size'] > $maxSize) throw new Exception("Max file 5MB.");
    
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowedExt)) throw new Exception("Format tidak valid.");

    // Hapus file lama
    if ($oldFile) {
        @unlink($uploadDir . $oldFile);
        $oldThumb = pathinfo($oldFile, PATHINFO_FILENAME) . '-thumb.' . pathinfo($oldFile, PATHINFO_EXTENSION);
        @unlink($thumbDir . $oldThumb);
    }

    $filename = $newSlug . '-' . time() . '.' . $ext;
    if (!move_uploaded_file($file['tmp_name'], $uploadDir . $filename)) throw new Exception("Gagal upload.");

    // Buat Thumbnail
    $thumbName = str_replace(".$ext", "-thumb.$ext", $filename);
    createStaticThumbnail($uploadDir . $filename, $thumbDir . $thumbName, 300, 80);

    return $filename;
}

function createStaticThumbnail($src, $dest, $w, $q) {
    if (!extension_loaded('gd')) return copy($src, $dest);
    $ext = strtolower(pathinfo($src, PATHINFO_EXTENSION));
    if ($ext == 'gif') return copy($src, $dest);
    
    switch($ext){
        case 'jpg': case 'jpeg': $img = @imagecreatefromjpeg($src); break;
        case 'png': $img = @imagecreatefrompng($src); break;
        case 'webp': $img = @imagecreatefromwebp($src); break;
        default: return false;
    }
    if(!$img) return false;

    $ow = imagesx($img); $oh = imagesy($img);
    $th = floor($oh * ($w / $ow));
    $tmp = imagecreatetruecolor($w, $th);
    
    if($ext=='png'){
        imagealphablending($tmp, false); imagesavealpha($tmp, true);
    }
    imagecopyresampled($tmp, $img, 0, 0, 0, 0, $w, $th, $ow, $oh);
    
    if($ext=='png') imagepng($tmp, $dest, 9);
    else imagejpeg($tmp, $dest, $q);
    return true;
}
?>