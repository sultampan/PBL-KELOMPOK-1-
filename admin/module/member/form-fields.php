<?php
// admin/module/member/form-fields.php

// 1. SIAPKAN DATA LINK (Untuk Mode Edit)
// Kita ambil link ke-1, ke-2, dan ke-3 secara manual dari array
$links = $editData['links'] ?? [];

$link1 = $links[0] ?? null; // Slot 1
$link2 = $links[1] ?? null; // Slot 2
$link3 = $links[2] ?? null; // Slot 3
?>

<h2><?= $editData ? "Edit Member" : "Tambah Member Baru" ?></h2>

<form id="memberForm" method="POST" class="form-grid">

    <?php if ($editData): ?>
        <input type="hidden" name="id_member" value="<?= $editData['id_member'] ?>">
        <input type="hidden" name="gambar_lama" value="<?= htmlspecialchars($editData['gambar'] ?? '') ?>">
    <?php endif; ?>

    <div class="mb-3">
        <label class="form-label">Nama Member <span style="color: red">*</span></label>
        <input type="text" name="nama_member" class="form-control"
               value="<?= $formData['nama_member'] ?? '' ?>" maxlength="100" required>
    </div>

    <div class="mb-3">
        <label class="form-label">NIDN/NIM <span style="color: red">*</span></label>
        <input type="text" name="nidn" class="form-control"
               value="<?= $formData['nidn'] ?? '' ?>" inputmode="numeric" 
               oninput="this.value = this.value.replace(/[^0-9]/g, '')">
    </div>

    <div class="mb-3">
        <label class="form-label">Jabatan <span style="color: red">*</span></label>
        <select name="jabatan" class="form-control" required>
            <option value="">-- Pilih Jabatan --</option>
            <option value="Head of Laboratory" <?= ($formData['jabatan'] ?? '') == 'Head of Laboratory' ? 'selected' : '' ?>>Head of Laboratory</option>
            <option value="Member Lab" <?= ($formData['jabatan'] ?? '') == 'Member Lab' ? 'selected' : '' ?>>Member Lab</option>
        </select>
    </div>

    <div class="mb-3">
        <label class="form-label">Keahlian / Expertise <span style="color: red">*</span></label>
        <input type="text" name="keahlian" class="form-control" 
               placeholder="Contoh: Web Development, AI, Data Mining"
               value="<?= htmlspecialchars($formData['keahlian'] ?? '') ?>" required>
        <small style="color: #888; font-size: 12px;">Pisahkan dengan koma jika lebih dari satu.</small>
    </div>

    <div class="mb-3">
        <label class="form-label">Deskripsi</label>
        <textarea name="deskripsi" rows="4" class="form-control"><?= $formData['deskripsi'] ?? '' ?></textarea>
    </div>

    <div class="mb-3 link-section-box">
        <label class="form-label link-section-title">
            Link Profil (Maksimal 3, Opsional)
        </label>
        
        <div class="link-row-static">
            <label class="link-helper-label">Link 1 (Contoh: Google Scholar)</label>
            <div class="link-inputs-wrapper">
                <input type="text" name="judul_link[]" class="form-control input-judul" 
                       placeholder="Judul Link" 
                       value="<?= htmlspecialchars($link1['judul_link'] ?? '') ?>">
                
                <input type="text" name="url_link[]" class="form-control input-url" 
                       placeholder="URL (https://...)" 
                       value="<?= htmlspecialchars($link1['url_link'] ?? '') ?>">
            </div>
        </div>

        <div class="link-row-static">
            <label class="link-helper-label">Link 2 (Contoh: Sinta)</label>
            <div class="link-inputs-wrapper">
                <input type="text" name="judul_link[]" class="form-control input-judul" 
                       placeholder="Judul Link" 
                       value="<?= htmlspecialchars($link2['judul_link'] ?? '') ?>">
                
                <input type="text" name="url_link[]" class="form-control input-url" 
                       placeholder="URL (https://...)" 
                       value="<?= htmlspecialchars($link2['url_link'] ?? '') ?>">
            </div>
        </div>

        <div class="link-row-static">
            <label class="link-helper-label">Link 3 (Contoh: ORCID/LinkedIn)</label>
            <div class="link-inputs-wrapper">
                <input type="text" name="judul_link[]" class="form-control input-judul" 
                       placeholder="Judul Link" 
                       value="<?= htmlspecialchars($link3['judul_link'] ?? '') ?>">
                
                <input type="text" name="url_link[]" class="form-control input-url" 
                       placeholder="URL (https://...)" 
                       value="<?= htmlspecialchars($link3['url_link'] ?? '') ?>">
            </div>
        </div>
    </div>

    <div class="mb-3">
        <label class="form-label">Foto Member</label>
        <?php 
            $labelTeks = "Tidak ada file yang dipilih...";
            if (!empty($editData['gambar'])) {
                $labelTeks = htmlspecialchars($editData['gambar']);
            }

            // LOGIKA BARU: Tentukan nasib kotak preview
            // Kalau tidak ada gambar awal ($initialSrc kosong), kotak disembunyikan (display: none)
            // Kalau ada gambar, kotak dimunculkan (display: flex)
            $boxStyle = empty($initialSrc) ? 'display: none;' : 'display: flex;';
        ?>
        <div class="custom-file-upload">
            <input type="file" name="gambar" class="form-control" accept="image/*" id="inputGambar" 
                   onchange="previewMemberImage(event); updateMemberFileName(this);"> 
            
            <label for="inputGambar" class="file-label" id="fileLabel">
                <span class="file-button">Browse</span> 
                <span id="fileNameText" class="placeholder-text"><?= $labelTeks ?></span>
            </label>
            
            <button type="button" id="removeImageBtn" class="remove-image-btn" 
                    onclick="removeMemberImage();"
                    style="<?= empty($initialSrc) ? 'display: none;' : '' ?>">&times;</button>
        </div>
        
        <div id="fileError" style="margin-top: 10px;"></div>

        <div class="form-preview-box" id="previewBox" style="<?= $boxStyle ?>">
            <img src="<?= $initialSrc ?>" class="img-thumbnail" id="imgPreview">
        </div>

        <input type="hidden" name="remove_existing_image" id="removeExistingImage" value="0">
    </div>

    <div class="mb-3 button-group">
        <button type="button" class="btn btn-secondary" onclick="cancelMemberForm()">Batal</button>
        <button type="submit" id="submitBtn" class="btn btn-primary"><?= $editData ? "Update" : "Simpan" ?></button>
    </div>

</form>