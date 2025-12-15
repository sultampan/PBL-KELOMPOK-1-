<?php
// admin/module/social/model.php
if (session_status() === PHP_SESSION_NONE) session_start();

function checkPdo($pdo) {
    if (!$pdo instanceof PDO) throw new Exception("Koneksi database gagal.");
}

// Ambil semua data sosmed (Instagram, FB, X, GitHub)
function getAllSocials($pdo) {
    checkPdo($pdo);
    $stmt = $pdo->query("SELECT * FROM social_media ORDER BY id_social ASC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Update satu baris sosmed
function updateSocialLink($pdo, $id, $link, $isActive) {
    checkPdo($pdo);
    $stmt = $pdo->prepare("UPDATE social_media SET link_url = :link, is_active = :active WHERE id_social = :id");
    $stmt->execute([
        ':link'   => $link,
        ':active' => $isActive, // String 'true'/'false' atau Boolean
        ':id'     => $id
    ]);
}
?>