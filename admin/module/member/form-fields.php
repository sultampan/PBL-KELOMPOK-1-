<?php
// admin/module/member/form-fields.php
?>
<h2><?= $editData ? "Edit Member" : "Tambah Member Baru" ?></h2>

<form id="memberForm" method="POST" class="form-grid">

    <?php if ($editData): ?>
        <input type="hidden" name="id_member" value="<?= $editData['id_member'] ?>">
        <input type="hidden" name="gambar_lama" value="<?= htmlspecialchars($editData['gambar']) ?>">
    <?php endif; ?>

    <div class="mb-3">
        <label class="form-label">Nama Member</label>
        <input type="text" name="nama_member" class="form-control"
            value="<?= $formData['nama_member'] ?? '' ?>" required>
    </div>

    <div class="mb-3">
        <label class="form-label">NIDN</label>
        <input type="text" name="nidn" class="form-control"
               value="<?= $formData['nidn'] ?? '' ?>" 
               inputmode="numeric" 
               oninput="this.value = this.value.replace(/[^0-9]/g, '')"
               placeholder="Contoh: 0011223344">
    </div>

    <div class="mb-3">
        <label class="form-label">Jabatan</label>
        <select name="jabatan" class="form-control" required>
            <option value="">-- Pilih Jabatan --</option>
            <option value="Head of Laboratory" <?= ($formData['jabatan'] ?? '') == 'Head of Laboratory' ? 'selected' : '' ?>>Head of Laboratory</option>
            <option value="Member Lab" <?= ($formData['jabatan'] ?? '') == 'Member Lab' ? 'selected' : '' ?>>Member Lab</option>
        </select>
    </div>

    <div class="mb-3">
        <label class="form-label">Link Google Scholar</label>
        <input type="text" name="google_scholar" class="form-control" placeholder="https://scholar.google.com/..."
            value="<?= $formData['google_scholar'] ?? '' ?>">
    </div>

    <div class="mb-3">
        <label class="form-label">Link ORCID</label>
        <input type="text" name="orcid" class="form-control" placeholder="https://orcid.org/..."
            value="<?= $formData['orcid'] ?? '' ?>">
    </div>

    <div class="mb-3">
        <label class="form-label">Link Sinta</label>
        <input type="text" name="sinta" class="form-control" placeholder="https://sinta.kemdikbud.go.id/..."
            value="<?= $formData['sinta'] ?? '' ?>">
    </div>

    <div class="mb-3">
        <label class="form-label">Deskripsi</label>
        <textarea name="deskripsi" rows="4" class="form-control"><?= $formData['deskripsi'] ?? '' ?></textarea>
    </div>

    <div class="mb-3">
        <label class="form-label">Foto Member</label>
        <div class="custom-file-upload">
            <input type="file" name="gambar" class="form-control"
                accept="image/*" id="inputGambar"
                onchange="previewMemberImage(event); updateMemberFileName(this);">

            <label for="inputGambar" class="file-label" id="fileLabel">
                <span class="file-button">Browse</span>
                <span id="fileNameText" class="placeholder-text">Tidak ada file yang dipilih...</span>
            </label>

            <button type="button"
                id="removeImageBtn"
                class="remove-image-btn"
                onclick="removeMemberImage();"
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
        <button type="button" class="btn btn-secondary" onclick="cancelMemberForm()">
            Batal
        </button>
    </div>
</form>