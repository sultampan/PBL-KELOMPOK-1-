<?php
// admin/module/activity/form-fields.php

// 1. Ambil Opsi Member untuk Dropdown
$memberOptions = getAllMembersOption($pdo);

// 2. Ambil Data Tim yang sudah ada (Kalau lagi Edit)
$existingTeam = $editData['team'] ?? [];

// 3. DAFTAR KATEGORI SESUAI ENUM DATABASE
$kategoriList = ['Research', 'Projects', 'Activity'];
?>
<h2><?= $editData ? "Edit Activity" : "Tambah Activity Baru" ?></h2>

<form id="activityForm" method="POST" enctype="multipart/form-data" class="form-grid"> 
    
    <?php if ($editData): ?>
        <input type="hidden" name="id_activity" value="<?= $editData['id_activity'] ?>">
        <input type="hidden" name="gambar_lama" value="<?= htmlspecialchars($editData['gambar']) ?>">
    <?php endif; ?>

    <div class="mb-3">
        <label class="form-label">Judul Activity</label>
        <input type="text" name="judul" class="form-control"
               value="<?= $formData['judul'] ?? '' ?>" maxlength="50" required>
        <small class="form-text text-muted">Maksimal 50 karakter</small>
    </div>

    <div class="mb-3">
        <label class="form-label">Kategori <span style="color:red">*</span></label>
        <select name="kategori" class="form-control" required>
            <option value="">-- Pilih Kategori --</option>
            <?php foreach ($kategoriList as $cat): ?>
                <option value="<?= $cat ?>" 
                    <?= (isset($formData['kategori']) && $formData['kategori'] == $cat) ? 'selected' : '' ?>>
                    <?= $cat ?>
                </option>
            <?php endforeach; ?>
        </select>
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

    <div class="mb-3 link-section-box">
        <label class="form-label link-section-title">Partisipasi Member</label>
        <small style="display:block; margin-bottom:10px; color:#666;">Member yang terlibat dalam proyek ini.</small>
        
        <div id="team-container">
            <?php 
            // A. JIKA MODE EDIT: Tampilkan baris yang sudah ada
            if (!empty($existingTeam)) {
                foreach ($existingTeam as $tm) {
                    ?>
                    <div class="link-row">
                        <select name="member_ids[]" class="form-control" style="flex: 1;" required>
                            <option value="">-- Pilih Member --</option>
                            <?php foreach ($memberOptions as $opt): ?>
                                <option value="<?= $opt['id_member'] ?>" 
                                    <?= ($tm['id_member'] == $opt['id_member']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($opt['nama_member']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>

                        <button type="button" class="btn-remove-link" onclick="removeTeamRow(this)" title="Hapus Member">&times;</button>
                    </div>
                    <?php
                }
            }
            ?>
        </div>

        <button type="button" class="btn-add-link" onclick="addTeamRow()">
            + Tambah Member
        </button>

        <template id="teamRowTemplate">
            <div class="link-row">
                <select name="member_ids[]" class="form-control" style="flex: 1;" required>
                    <option value="">-- Pilih Member --</option>
                    <?php foreach ($memberOptions as $opt): ?>
                        <option value="<?= $opt['id_member'] ?>">
                            <?= htmlspecialchars($opt['nama_member']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="button" class="btn-remove-link" onclick="removeTeamRow(this)" title="Hapus Member">&times;</button>
            </div>
        </template>
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
        <button type="button" class="btn btn-secondary" onclick="cancelMemberForm()">Batal</button>
        <button type="submit" id="submitBtn" class="btn btn-primary"><?= $editData ? "Update" : "Simpan" ?></button>
    </div>
</form>