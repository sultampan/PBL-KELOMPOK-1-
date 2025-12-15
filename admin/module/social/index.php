<?php
// admin/module/social/index.php
require_once "model.php";
$socials = getAllSocials($pdo);
?>

<link rel="stylesheet" href="assets/css/forms.css">
<link rel="stylesheet" href="assets/css/components.css">
<link rel="stylesheet" href="assets/css/social.css"> <div class="header-title" style="margin-bottom: 20px;"></div>

<div id="social-content-wrapper">
    <?php include __DIR__ . '/form.php'; ?>
</div>

<script src="assets/js/social.js"></script>