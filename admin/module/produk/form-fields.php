<?php
// admin/module/produk/form-fields.php (KODE BERSIH)

// Catatan: Variabel $formData, $editData, $initialSrc, $initialStyle sudah tersedia.
?>

<h2><?= $editData ? "Edit Produk" : "Tambah Produk Baru" ?></h2>

<form id="productForm" method="POST" class="form-grid">

    <?php if ($editData): ?>
        <input type="hidden" name="id_produk" value="<?= $editData['id_produk'] ?>">
        <input type="hidden" name="gambar_lama" value="<?= htmlspecialchars($editData['gambar']) ?>">
    <?php endif; ?>

    <div class="mb-3">
        <label class="form-label">Nama Produk</label>
        <input type="text" name="nama" class="form-control"
            value="<?= $formData['nama'] ?? '' ?>" required>
    </div>

    <div class="mb-3">
        <label class="form-label">Deskripsi</label>
        <textarea name="deskripsi" rows="4" class="form-control" required><?= $formData['deskripsi'] ?? '' ?></textarea>
    </div>

    <div class="mb-3">
        <label class="form-label">Link Produk</label>
        <input type="text" name="link_produk" class="form-control"
            value="<?= $formData['link_produk'] ?? '' ?>">
    </div>

    <div class="mb-3">
        <label class="form-label">Gambar Produk</label>

        <div class="custom-file-upload">
            <input type="file" name="gambar" class="form-control"
                accept="image/*" id="inputGambar" onchange="previewImage(event); updateFileName(this);">

            <label for="inputGambar" class="file-label" id="fileLabel">
                <span class="file-button">Browse</span>
                <span id="fileNameText" class="placeholder-text">Tidak ada file yang dipilih...</span>
            </label>

            <button type="button"
                id="removeImageBtn"
                class="remove-image-btn"
                onclick="removeImage();"
                style="<?= empty($initialSrc) ? 'display: none;' : '' ?>"
                title="Hapus gambar yang dipilih">
                &times;
            </button>
        </div>

        <div id="fileError" style="margin-top: 10px;"></div>

        <div class="preview mt-2">
            <img src="<?= $initialSrc ?>"
                class="img-thumbnail" alt="Preview Gambar" width="auto"
                id="imgPreview" style="<?= $initialStyle ?>">
        </div>
        <input type="hidden" name="remove_existing_image" id="removeExistingImage" value="0">

    </div>
    <div class="mb-3 button-group">
        <button type="submit" id="submitBtn" class="btn btn-primary">
            <?= $editData ? "Update" : "Simpan" ?>
        </button>

        <button type="button" class="btn btn-secondary" onclick="cancelProductForm()">
            Batal
        </button>
    </div>
</form>