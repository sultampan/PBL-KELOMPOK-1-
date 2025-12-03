<?php
// admin/module/fasilitas/form-fields.php

// Variabel $formData, $editData, $initialSrc, $initialStyle sudah tersedia dari form-load.php atau index.php
?>

<h2><?= $editData ? "Edit Fasilitas (TEST)" : "Tambah Fasilitas Baru (TEST)" ?></h2>

<form id="fasilitasForm" method="POST" class="form-grid"> 
    
    <?php if ($editData): ?>
        <input type="hidden" name="id_galery" value="<?= $editData['id_galery'] ?>">
        <input type="hidden" name="gambar_lama" value="<?= htmlspecialchars($editData['gambar']) ?>">
    <?php endif; ?>

    <div class="mb-3">
        <label class="form-label">Nama Fasilitas</label>
        <input type="text" name="judul" class="form-control"
               value="<?= $formData['judul'] ?? '' ?>" required>
    </div>
    
    <div class="mb-3">
        <label class="form-label">Deskripsi</label>
        <textarea name="deskripsi" rows="4" class="form-control" required><?= $formData['deskripsi'] ?? '' ?></textarea>
    </div>

    <div class="mb-3">
        <label class="form-label">Gambar Fasilitas</label>
        
        <div class="custom-file-upload">
            <input type="file" name="gambar" class="form-control" 
                   accept="image/*" id="inputGambar" 
                   onchange="previewFasilitasImage(event); updateFasilitasFileName(this);"> 
            
            <label for="inputGambar" class="file-label" id="fileLabel">
                <span class="file-button">Browse</span> 
                <span id="fileNameText" class="placeholder-text">Tidak ada file yang dipilih...</span>
            </label>
            
            <button type="button" 
                    id="removeImageBtn" 
                    class="remove-image-btn" 
                    onclick="removeFasilitasImage();"
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
        
        <button type="button" class="btn btn-secondary" onclick="cancelFasilitasForm()">
            Batal
        </button>
    </div>
</form>