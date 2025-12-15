<?php
// admin/module/produk/form-fields.php
?>

<h2><?= $editData ? "Edit Produk" : "Tambah Produk Baru" ?></h2>

<form id="productForm" method="POST" class="form-grid" enctype="multipart/form-data"> 
    
    <?php if ($editData): ?>
        <input type="hidden" name="id_produk" value="<?= $editData['id_produk'] ?>">
        <input type="hidden" name="gambar_lama" value="<?= htmlspecialchars($editData['gambar']) ?>">
    <?php endif; ?>

    <div class="mb-3">
        <label class="form-label">Nama Produk <span style="color: red;">*</span></label>
        <input type="text" name="judul" id="judulInput" class="form-control input-judul"
               value="<?= $formData['judul'] ?? '' ?>" required placeholder="Masukkan nama produk...">
    </div>
    
    <div class="mb-3">
        <label class="form-label">Deskripsi <span style="color: red;">*</span></label>
        <textarea name="deskripsi" id="deskripsiInput" rows="5" class="form-control" required placeholder="Tulis deskripsi produk..."><?= $formData['deskripsi'] ?? '' ?></textarea>
    </div>

    <div class="mb-3">
        <label class="form-label">Gambar Produk</label>
        
        <div class="custom-file-upload">
            <input type="file" name="gambar" class="form-control" 
                   accept="image/*" id="inputGambar" 
                   onchange="previewProductImage(event); updateProductFileName(this);"> 
            
            <label for="inputGambar" class="file-label" id="fileLabel">
                <span class="file-button">Browse</span> 
                <span id="fileNameText" class="placeholder-text">
                    <?= !empty($initialSrc) ? ($editData['gambar'] ?? 'Gambar terpilih') : 'Tidak ada file yang dipilih...' ?>
                </span>
            </label>
            
            <button type="button" 
                    id="removeImageBtn" 
                    class="remove-image-btn" 
                    onclick="removeProductImage();"
                    style="<?= empty($initialSrc) ? 'display: none;' : '' ?>"
                    title="Hapus gambar yang dipilih">
                &times;
            </button>
        </div>
        
        <div id="fileError" style="margin-top: 10px; display: none; color: red;"></div>
        
        <div class="form-preview-box" 
             id="previewContainer" 
             style="<?= empty($initialSrc) ? 'display: none;' : '' ?>">
            
            <img src="<?= $initialSrc ?>"
                 alt="Preview Gambar"
                 id="imgPreview">
        </div>

        <input type="hidden" name="remove_existing_image" id="removeExistingImage" value="0">
    </div>
    <div class="link-section-box">
        <label class="form-label link-section-title">Tim Pengembang</label>
        
        <div class="team-input-row">
            <select id="memberSelect" class="form-control">
                <option value="">-- Pilih Member --</option>
                <?php foreach ($memberOptions as $m): ?>
                    <option value="<?= $m['id_member'] ?>"><?= htmlspecialchars($m['nama_member']) ?></option>
                <?php endforeach; ?>
            </select>
            
            <input type="text" id="roleInput" class="form-control" placeholder="Peran (misal: Frontend)">
            
            <button type="button" class="btn btn-success" onclick="addTeamToTable()">
                Tambah
            </button>
        </div>

        <table class="table table-bordered" id="teamTable" style="margin-top: 10px; background: #fff;">
            <thead>
                <tr>
                    <th>Nama Member</th>
                    <th>Peran</th>
                    <th style="width: 50px; text-align:center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($productMembers)): ?>
                    <?php foreach ($productMembers as $pm): ?>
                        <tr>
                            <td>
                                <?= htmlspecialchars($pm['nama_member']) ?>
                                <input type="hidden" name="member_ids[]" value="<?= $pm['id_member'] ?>">
                            </td>
                            <td>
                                <?= htmlspecialchars($pm['role']) ?>
                                <input type="hidden" name="member_roles[]" value="<?= htmlspecialchars($pm['role']) ?>">
                            </td>
                            <td style="text-align:center;">
                                <button type="button" class="btn btn-danger btn-sm" onclick="removeTeamRowTable(this)">✕</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="mb-3 button-group">
        <button type="button" 
                id="btnCancel" 
                class="btn btn-secondary" 
                onclick="cancelProductForm()"
                <?= !$editData ? 'disabled' : '' ?>> 
            Batal
        </button>
        
        <button type="submit" id="submitBtn" class="btn btn-primary">
            <?= $editData ? "Update" : "Simpan" ?>
        </button>
    </div>
</form>

<script>
    // Memastikan listener dipasang saat form diload via AJAX
    if(typeof attachSearchListener === 'function') { attachSearchListener(); }
</script>