<?php
$isEdit = isset($editData);
?>

<div class="card-body">
<form action="module/social_media/save.php" method="POST">

<?php if ($isEdit): ?>
    <input type="hidden" name="id" value="<?= $editData['id']; ?>">
<?php endif; ?>

<div class="form-grid">

    <div>
        <label>Nama Sosmed *</label>
        <input type="text" name="name" class="form-control"
               value="<?= $editData['name'] ?? ''; ?>" required>
    </div>

    <div>
        <label>Icon (FontAwesome)</label>
        <input type="text" name="icon" class="form-control"
               value="<?= $editData['icon'] ?? ''; ?>">
    </div>

    <div>
        <label>Link *</label>
        <input type="url" name="link" class="form-control"
               value="<?= $editData['link'] ?? ''; ?>" required>
    </div>

    <div>
        <label>Urutan</label>
        <input type="number" name="sort_order" class="form-control"
               value="<?= $editData['sort_order'] ?? 0; ?>">
    </div>

    <div>
        <label>Status</label>
        <select name="is_active" class="form-control">
            <option value="1" <?= (!isset($editData) || $editData['is_active']) ? 'selected' : ''; ?>>
                Aktif
            </option>
            <option value="0" <?= (isset($editData) && !$editData['is_active']) ? 'selected' : ''; ?>>
                Nonaktif
            </option>
        </select>
    </div>

    <!-- 🔥 BUTTON GROUP (INI YANG BIKIN MIRIP PARTNER) -->
    <div class="button-group">
        <button type="button"
                onclick="window.location.href='index.php?page=social_media'"
                class="btn-secondary">
            Batal
        </button>

        <button type="submit" class="btn-primary">
            <?= $isEdit ? 'Update' : 'Simpan'; ?>
        </button>
    </div>

</div>
</form>
</div>


    </form>
</div>
