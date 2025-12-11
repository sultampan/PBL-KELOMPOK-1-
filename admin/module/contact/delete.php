<?php
// admin/module/contact/delete.php

$id = $_GET['id'] ?? 0;

if ($id) {
    try {
        $stmt = $pdo->prepare("DELETE FROM contact WHERE id_contact = :id");
        $stmt->execute([':id' => $id]);

        // Redirect Sukses Hapus
        header("Location: index.php?page=contact&status=deleted");
        exit;

    } catch (PDOException $e) {
        header("Location: index.php?page=contact&status=error&msg=" . urlencode($e->getMessage()));
        exit;
    }
} else {
    header("Location: index.php?page=contact");
    exit;
}
?>