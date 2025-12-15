<?php
// admin/module/produk/form-fields.php
$memberOptions = getAllMembersOption($pdo);
$existingTeam = $editData['team'] ?? [];
?>
<h2><?= $editData ? "Edit Produk" : "Tambah Produk Baru" ?></h2>

<form id="productForm" method="POST" enctype="multipart/form-data" class="form-grid"> 
    
    <?php if ($editData): ?>
        <input type="hidden" name="id_produk" value="<?= $editData['id_produk'] ?>">
        <input type="hidden" name="gambar_lama" value="<?= htmlspecialchars($editData['gambar']) ?>">
    <?php endif; ?>

    <div class="mb-3">
        <label class="form-label">Nama Produk <span style="color: red;">*</span></label>
        <input type="text" name="nama" class="form-control"
               value="<?= $formData['nama'] ?? '' ?>" required>
    </div>

    <div class="mb-3">
        <label class="form-label">Link Produk <span style="color: red;">*</span></label>
        <input type="text" name="link_produk" class="form-control"
               value="<?= $formData['link_produk'] ?? '' ?>" placeholder="https://...">
    </div>
    
    <div class="mb-3">
        <label class="form-label">Deskripsi <span style="color: red;">*</span></label>
        <textarea name="deskripsi" rows="4" class="form-control" required><?= $formData['deskripsi'] ?? '' ?></textarea>
    </div>

    <div class="mb-3 link-section-box">
        <label class="form-label link-section-title">Tim Pengembang</label>
        <div class="team-input-row">
            <select id="memberSelect" class="form-control">
                <option value="">-- Pilih Member --</option>
                <?php foreach ($memberOptions as $m): ?>
                    <option value="<?= $m['id_member'] ?>"><?= htmlspecialchars($m['nama_member']) ?></option>
                <?php endforeach; ?>
            </select>
            <input id="roleInput" type="text" class="form-control" placeholder="Role (ex: Backend)">
            <button type="button" class="btn btn-success" onclick="addTeamToTable()">Tambah</button>
        </div>

        <table class="table table-bordered mt-3" id="teamTable">
            <thead>
                <tr><th width="40%">Member</th><th width="40%">Role</th><th width="20%">Aksi</th></tr>
            </thead>
            <tbody>
                <?php if (!empty($existingTeam)): ?>
                    <?php foreach ($existingTeam as $tm): ?>
                        <tr>
                            <td>
                                <?= htmlspecialchars($tm['nama_member']) ?>
                                <input type="hidden" name="member_ids[]" value="<?= $tm['id_member'] ?>">
                            </td>
                            <td>
                                <?= htmlspecialchars($tm['role']) ?>
                                <input type="hidden" name="member_roles[]" value="<?= $tm['role'] ?>">
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
        
        <div id="fileError" style="color: red; margin-top: 5px; font-size: 13px; display: none;"></div>
        
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
        <button type="button" id="cancelBtn" class="btn btn-secondary" onclick="cancelProductForm()" disabled>Batal</button>
        
        <button type="submit" id="submitBtn" class="btn btn-primary" disabled><?= $editData ? "Update" : "Simpan" ?></button>
    </div>
</form>