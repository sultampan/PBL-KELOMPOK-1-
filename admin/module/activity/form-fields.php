<?php
// admin/module/activity/form-fields.php
?>
<h2><?= $editData ? "Edit Activity" : "Tambah Activity Baru" ?></h2>

<form id="activityForm" method="POST" class="form-grid"> 
    
    <?php if ($editData): ?>
        <input type="hidden" name="id_activity" value="<?= $editData['id_activity'] ?>">
        <input type="hidden" name="gambar_lama" value="<?= htmlspecialchars($editData['gambar']) ?>">
    <?php endif; ?>

    <div class="mb-3">
        <label class="form-label">Judul Activity</label>
        <input type="text" name="judul" class="form-control"
               value="<?= $formData['judul'] ?? '' ?>" required>
    </div>

    <div class="mb-3">
        <label class="form-label">Tanggal Kegiatan</label>
        <input type="date" name="tanggal_kegiatan" class="form-control"
               value="<?= $formData['tanggal_kegiatan'] ?? '' ?>" required>
    </div>
    
    <div class="mb-3">
        <label class="form-label">Deskripsi</label>
        <textarea name="deskripsi" rows="4" class="form-control" required><?= $formData['deskripsi'] ?? '' ?></textarea>
    </div>

    <div class="mb-3">
        <label class="form-label">Gambar Activity</label>
        <div class="custom-file-upload">
            <input type="file" name="gambar" class="form-control" 
                   accept="image/*" id="inputGambar" 
                   onchange="previewActivityImage(event); updateActivityFileName(this);"> 
            
            <label for="inputGambar" class="file-label" id="fileLabel">
                <span class="file-button">Browse</span> 
                <span id="fileNameText" class="placeholder-text">Tidak ada file yang dipilih...</span>
            </label>
            
            <button type="button" 
                    id="removeImageBtn" 
                    class="remove-image-btn" 
                    onclick="removeActivityImage();"
                    style="<?= empty($initialSrc) ? 'display: none;' : '' ?>"
                    title="Hapus gambar">
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
        <button type="button" class="btn btn-secondary" onclick="cancelActivityForm()">
            Batal
        </button>
    </div>
</form>