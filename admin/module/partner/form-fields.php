<?php
// admin/module/partner/form-fields.php
?>

<h2><?= $editData ? "Edit Partner" : "Tambah Partner Baru" ?></h2>

<form id="partnerForm" method="POST" class="form-grid"> 
    
    <?php if ($editData): ?>
        <input type="hidden" name="id_partner" value="<?= $editData['id_partner'] ?>">
        <input type="hidden" name="gambar_lama" value="<?= htmlspecialchars($editData['gambar']) ?>">
    <?php endif; ?>

    <div class="mb-3">
        <label class="form-label">Nama Partner <span style="color: red;">*</span></label>
        <input type="text" name="nama" id="namaInput" class="form-control"
               value="<?= htmlspecialchars($formData['nama'] ?? '') ?>" required>
    </div>
    
    <div class="mb-3">
        <label class="form-label">Kategori <span style="color: red;">*</span></label>
        <select name="kategori" id="kategoriInput" class="form-control" required>
            <option value="" disabled <?= empty($formData['kategori']) ? 'selected' : '' ?>>-- Pilih Kategori --</option>
            <?php
            $options = [
                'Industry Partner', 
                'Educational Institutions', 
                'Government Institutions', 
                'International Institutions'
            ];
            foreach($options as $opt) {
                $selected = ($formData['kategori'] ?? '') === $opt ? 'selected' : '';
                echo "<option value=\"$opt\" $selected>$opt</option>";
            }
            ?>
        </select>
    </div>

    <div class="mb-3">
        <label class="form-label">Logo Partner</label>
        
        <div class="custom-file-upload">
            <input type="file" name="gambar" class="form-control" 
                   accept="image/*" id="inputGambar" 
                   onchange="previewPartnerImage(event); updatePartnerFileName();"> 
            
            <label for="inputGambar" class="file-label" id="fileLabel">
                <span class="file-button">Browse</span> 
                <span id="fileNameText" class="placeholder-text">
                    <?= !empty($initialSrc) ? ($editData['gambar'] ?? 'Gambar terpilih') : 'Tidak ada file yang dipilih...' ?>
                </span>
            </label>
            
            <button type="button" id="removeImageBtn" class="remove-image-btn" 
                    onclick="removePartnerImage();"
                    style="<?= empty($initialSrc) ? 'display: none;' : '' ?>"
                    title="Hapus gambar">&times;</button>
        </div>
        
        <div id="fileError" style="margin-top: 10px;"></div>
        
        <div class="form-preview-box" id="previewContainer" style="<?= empty($initialSrc) ? 'display: none;' : '' ?>">
            <img src="<?= $initialSrc ?>" alt="Preview" id="imgPreview">
        </div>

        <input type="hidden" name="remove_existing_image" id="removeExistingImage" value="0">
    </div>
    
    <div class="mb-3 button-group">
        <button type="button" 
                id="btnCancel" 
                class="btn btn-secondary" 
                onclick="cancelPartnerForm()"
                <?= !$editData ? 'disabled' : '' ?>> 
            Batal
        </button>
        
        <button type="submit" id="submitBtn" class="btn btn-primary"><?= $editData ? "Update" : "Simpan" ?></button>
    </div>
</form>

<script>
    if(typeof initPartnerForm === 'function') { initPartnerForm(); }
</script>