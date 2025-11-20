<?php
// admin/module/produkform.php
$initialSrc = !empty($editData['gambar']) ? $webUploadDir . $editData['gambar'] : '';

$initialStyle = empty($editData['gambar']) ? 'display: none;' : '';
?>

<div class="card">
    <h2><?= $editData ? "Edit Produk" : "Tambah Produk Baru" ?></h2>

    <?php 
    if(isset($_SESSION['error'])): ?>
        <div class="error-message" style="background:#fdd; color:#c00; padding:10px; border-radius:5px; margin-bottom:15px;">
            <?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <form action="module/produk/save.php" method="POST" enctype="multipart/form-data" class="form-grid">

        <?php if ($editData): ?>
            <input type="hidden" name="id_produk" value="<?= $editData['id_produk'] ?>">
            <input type="hidden" name="gambar_lama" value="<?= htmlspecialchars($editData['gambar']) ?>">
        <?php endif; ?>

        <div class="mb-3">
            <label class="form-label">Nama Produk</label>
            <input type="text" name="nama" class="form-control"
                   value="<?= $editData['nama'] ?? '' ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Deskripsi</label>
            <textarea name="deskripsi" rows="4" class="form-control" required><?= $editData['deskripsi'] ?? '' ?></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Link Produk (opsional)</label>
            <input type="text" name="link_produk" class="form-control"
                   value="<?= $editData['link_produk'] ?? '' ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">Gambar Produk</label>
            <input type="file" name="gambar" class="form-control" accept="image/*" id="inputGambar" onchange="previewImage(event)">

            <div class="preview mt-2">
                <img src="<?= $initialSrc ?>" 
                     class="img-thumbnail" alt="Preview Gambar" width="120" 
                     id="imgPreview" style="<?= $initialStyle ?>">
            </div>
        </div>
        
        <div class="mb-3 button-group" style="display: flex; gap: 10px;">
            
            <button type="submit" class="btn btn-primary" style="flex-grow: 1;">
                <?= $editData ? "Update" : "Simpan" ?>
            </button>
            
            <a href="index.php?page=produk" class="btn btn-secondary" style="flex-grow: 1; text-align: center;">
                Batal
            </a>
            
        </div>
        </form>
</div>