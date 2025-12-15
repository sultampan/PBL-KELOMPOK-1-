<?php $isEdit = isset($editData); ?>

<div class="card-header">
    <h3><?= $isEdit ? 'Edit Social Media' : 'Tambah Social Media'; ?></h3>
</div>

<div class="card-body">
    <form method="POST" action="module/social_media/save.php">

        <?php if ($isEdit): ?>
            <input type="hidden" name="id" value="<?= $editData['id_social_media']; ?>">
        <?php endif; ?>

        <div class="mb-3">
            <label class="form-label">Link Social Media *</label>
            <input
                type="url"
                name="link"
                id="linkInput"
                class="form-control"
                placeholder="https://instagram.com/username"
                value="<?= htmlspecialchars($editData['link'] ?? '') ?>"
                required
            >
        </div>

        <div class="form-buttons">
            <button
                type="button"
                id="btnBatal"
                class="btn btn-batal"
                disabled
                onclick="window.location.href='index.php?page=social_media'"
            >
                Batal
            </button>

            <button
                type="submit"
                id="btnSimpan"
                class="btn btn-simpan"
                disabled
            >
                <?= $isEdit ? 'Update' : 'Simpan'; ?>
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const linkInput = document.getElementById('linkInput');
    const btnSimpan = document.getElementById('btnSimpan');
    const btnBatal  = document.getElementById('btnBatal');

    function toggleButtons() {
        const filled = linkInput.value.trim() !== '';
        btnSimpan.disabled = !filled;
        btnBatal.disabled  = !filled;
    }

    // cek saat load (edit mode)
    toggleButtons();

    // cek saat ngetik
    linkInput.addEventListener('input', toggleButtons);
});
</script>

<style>
.form-buttons {
    display: flex;
    gap: 10px;
    margin-top: 20px;
}

.btn {
    padding: 12px;
    border: none;
    border-radius: 4px;
    font-weight: 500;
    cursor: pointer;
}

.btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.btn-batal {
    background: #95a5a6;
    color: white;
    flex: 1;
}

.btn-simpan {
    background: #1abc9c;
    color: white;
    flex: 1;
}
</style>
