<?php
// admin/module/fasilitas/form-fields.php
?>

<h2><?= $editData ? "Edit Fasilitas" : "Tambah Fasilitas Baru" ?></h2>

<form id="fasilitasForm" method="POST" class="form-grid"> 
    
    <?php if ($editData): ?>
        <input type="hidden" name="id_fasilitas" value="<?= $editData['id_fasilitas'] ?>">
        <input type="hidden" name="gambar_lama" value="<?= htmlspecialchars($editData['gambar']) ?>">
    <?php endif; ?>

    <div class="mb-3">
        <label class="form-label">Nama Fasilitas <span style="color: red;">*</span></label>
        <input type="text" name="judul" id="judulInput" class="form-control"
               value="<?= $formData['judul'] ?? '' ?>" required>
    </div>
    
    <div class="mb-3">
        <label class="form-label">Deskripsi <span style="color: red;">*</span></label>
        <textarea name="deskripsi" id="deskripsiInput" rows="4" class="form-control" required><?= $formData['deskripsi'] ?? '' ?></textarea>
    </div>

    <div class="mb-3">
        <label class="form-label">Gambar Fasilitas</label>
        
        <div class="custom-file-upload">
            <input type="file" name="gambar" class="form-control" 
                   accept="image/*" id="inputGambar" 
                   onchange="previewFasilitasImage(event); updateFasilitasFileName(this);"> 
            
            <label for="inputGambar" class="file-label" id="fileLabel">
                <span class="file-button">Browse</span> 
                <span id="fileNameText" class="placeholder-text">
                    <?= !empty($initialSrc) ? ($editData['gambar'] ?? 'Gambar terpilih') : 'Tidak ada file yang dipilih...' ?>
                </span>
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
        
        <div class="form-preview-box" 
             id="previewContainer" 
             style="<?= empty($initialSrc) ? 'display: none;' : '' ?>">
            
            <img src="<?= $initialSrc ?>"
                 alt="Preview Gambar"
                 id="imgPreview">
        </div>

        <input type="hidden" name="remove_existing_image" id="removeExistingImage" value="0">
    </div>
    
    <div class="mb-3 button-group">
        <button type="button" 
                id="btnCancel" 
                class="btn btn-secondary" 
                onclick="cancelFasilitasForm()"
                <?= !$editData ? 'disabled' : '' ?>> 
            Batal
        </button>
        
        <button type="submit" id="submitBtn" class="btn btn-primary">
            <?= $editData ? "Update" : "Simpan" ?>
        </button>
    </div>
</form>

<script>
    if(typeof initFormListener === 'function') { initFormListener(); }
</script>