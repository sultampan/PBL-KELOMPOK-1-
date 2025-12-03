<?php
// admin/module/activity/form.php
$initialSrc = !empty($editData['gambar']) ? $webUploadDir . $editData['gambar'] : '';
$initialStyle = empty($editData['gambar']) ? 'display: none;' : '';
$formData = $oldInput ?: $editData;
?>
<div class="card">
    <div id="form-content-wrapper"> 
        <?php require_once __DIR__ . '/form-fields.php'; ?>
    </div>
</div>