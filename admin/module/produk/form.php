<?php
// admin/module/produk/form.php
$initialSrc = !empty($editData['gambar']) ? $webUploadDir . $editData['gambar'] : '';
$initialStyle = empty($editData['gambar']) ? 'display: none;' : '';
$formData = $oldInput ?: $editData;
?>

<div class="card">
    <h2><?= $editData ? "Edit Produk" : "Tambah Produk Baru" ?></h2>

    <?php 
    if(!empty($success)): ?>
        <div class="alert success">
            <?= htmlspecialchars($success) ?>
        </div>
    <?php endif; ?>

    <?php 
    if(!empty($error)): ?>
        <div class="alert error">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>
    
    <form method="GET" class="search-form" style="margin-bottom: 20px;">
        <input type="hidden" name="page" value="produk">
        <div style="display: flex; gap: 10px;">
            <input type="text" name="keyword" 
                   placeholder="Cari berdasarkan Nama atau Deskripsi..." 
                   value="<?= htmlspecialchars($_GET['keyword'] ?? '') ?>" 
                   style="flex-grow: 1; padding: 10px; border-radius: 5px; border: 1px solid #ccc;">
            <button type="submit" class="btn btn-primary" style="padding: 10px 15px;">Cari</button>
            <?php if (isset($_GET['keyword'])): ?>
                <a href="?page=produk" class="btn btn-secondary" style="padding: 10px 15px; text-decoration: none;">Reset</a>
            <?php endif; ?>
        </div>
    </form>


    <form action="module/produk/save.php" method="POST" enctype="multipart/form-data" class="form-grid">

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
            
            <button type="submit" class="btn btn-primary">
                <?= $editData ? "Update" : "Simpan" ?>
            </button>
            
            <a href="index.php?page=produk" class="btn btn-secondary">
                Batal
            </a>
            
        </div>
    </form>
</div>