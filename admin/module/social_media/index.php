<?php
require_once __DIR__ . '/../../../config/koneksi.php';
require_once __DIR__ . '/model.php';

$searchKeyword = $_GET['keyword'] ?? null;

/* ambil data */
$list = getSocialMediaAll($pdo, $searchKeyword);

/* edit mode */
$editData = null;
if (isset($_GET['edit'])) {
    $editData = getSocialMediaById($pdo, (int)$_GET['edit']);
}
?>

<!-- ✅ PAKAI CSS YANG BENAR -->
<link rel="stylesheet" href="assets/css/contact.css">
<link rel="stylesheet" href="assets/css/forms.css">
<link rel="stylesheet" href="assets/css/components.css">

<!-- ===== FORM ===== -->
<div class="card">
    <?php include __DIR__ . '/form.php'; ?>
</div>

<!-- ===== TABLE ===== -->
<div class="card" style="margin-top:20px;">
    <?php include __DIR__ . '/table.php'; ?>
</div>
