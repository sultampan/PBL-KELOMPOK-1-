<?php
// admin/module/produk/form-fields.php (INI ADALAH KONTEN YANG AKAN DI-RELOAD)

// Catatan: Variabel $formData, $editData, $initialSrc, $initialStyle sudah tersedia dari form-load.php

// PERBAIKAN: Form Utama (ID productForm) sekarang membungkus semua input fields
?>

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
        <label class="form-label">Link Produk (opsional)</label>
        <input type="text" name="link_produk" class="form-control"
               value="<?= $formData['link_produk'] ?? '' ?>">
    </div>

    <div class="mb-3">
        <label class="form-label">Gambar Produk</label>
        <div id="fileError"></div>
        <input type="file" name="gambar" class="form-control" accept="image/*" id="inputGambar" onchange="previewImage(event)">

        <div class="preview mt-2">
            <img src="<?= $initialSrc ?>"
                 class="img-thumbnail" alt="Preview Gambar" width="auto"
                 id="imgPreview" style="<?= $initialStyle ?>">
        </div>
    </div>
    
    <div class="mb-3 button-group">
        <button type="submit" id="submitBtn" class="btn btn-primary">
            <?= $editData ? "Update" : "Simpan" ?>
        </button>
        
        <a href="index.php?page=produk" class="btn btn-secondary">
            Batal
        </a>
    </div>
</form>