<?php
$isEdit = !empty($editData);
?>

<h2><?= $isEdit ? 'Edit Social Media' : 'Tambah Social Media' ?></h2>

<form method="POST" action="module/social_media/save.php" class="form-grid">

    <?php if ($isEdit): ?>
        <input type="hidden" name="id_social_media" value="<?= $editData['id_social_media'] ?>">
    <?php endif; ?>

    <div class="mb-3">
        <label>Nama Sosmed</label>
        <input type="text" name="nama" class="form-control"
               value="<?= htmlspecialchars($editData['nama'] ?? '') ?>" required>
    </div>

    <div class="mb-3">
        <label>Icon (FontAwesome)</label>
        <input type="text" name="icon" class="form-control"
               placeholder="fa-instagram"
               value="<?= htmlspecialchars($editData['icon'] ?? '') ?>">
        <small>Contoh: fa-instagram, fa-twitter, fa-youtube</small>
    </div>

    <div class="mb-3">
        <label>Link</label>
        <input type="url" name="link" class="form-control"
               value="<?= htmlspecialchars($editData['link'] ?? '') ?>" required>
    </div>

    <div class="mb-3">
        <label>Urutan</label>
        <input type="number" name="urutan" class="form-control"
               value="<?= $editData['urutan'] ?? 0 ?>">
    </div>

    <div class="mb-3">
        <label>Status</label>
        <select name="status" class="form-control">
            <option value="1" <?= ($editData['status'] ?? 1) == 1 ? 'selected' : '' ?>>Aktif</option>
            <option value="0" <?= ($editData['status'] ?? 1) == 0 ? 'selected' : '' ?>>Nonaktif</option>
        </select>
    </div>

    <div class="button-group">
        <button type="submit" class="btn btn-primary">
            <?= $isEdit ? 'Update' : 'Simpan' ?>
        </button>
    </div>

</form>
