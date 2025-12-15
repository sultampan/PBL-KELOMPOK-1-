<?php
// admin/module/activity/form.php

// Ambil data untuk member dropdown
$members = getAllMembersOption($pdo);

// Ambil ID Member yang sudah terpilih (untuk mode edit)
$selectedMembers = [];
if ($editData) {
    // Ambil dari database relation
    $currentMembers = getActivityMembers($pdo, $editData['id_activity']);
    // Kita butuh array ID-nya saja untuk mencocokkan di select option
    // Namun karena getActivityById di model kamu mengembalikan struktur 'team' berisi id_member, kita pakai itu.
    if (!empty($editData['team'])) {
        $selectedMembers = array_column($editData['team'], 'id_member');
    }
}

// LOGIKA GAMBAR & PREVIEW BOX
$initialSrc = '';
$labelTeks = "Tidak ada file yang dipilih...";
$boxStyle = 'display: none;'; // Default hidden jika tidak ada gambar

if (!empty($editData['gambar'])) {
    $imgUrl = $webUploadDir . $editData['gambar'];
    // Jika file ada, set source dan tampilkan kotak
    $initialSrc = $imgUrl;
    $labelTeks = htmlspecialchars($editData['gambar']);
    $boxStyle = 'display: flex;'; // Flex agar gambar di tengah
}
?>

<div class="card">
    <div class="card-header-flex">
        <h2><?= $editData ? "Edit Activity" : "Tambah Activity Baru" ?></h2>
    </div>

    <form id="activityForm" method="POST" enctype="multipart/form-data" class="form-grid">
        
        <?php if ($editData): ?>
            <input type="hidden" name="id_activity" value="<?= $editData['id_activity'] ?>">
            <input type="hidden" name="gambar_lama" value="<?= htmlspecialchars($editData['gambar']) ?>">
        <?php endif; ?>

        <div class="mb-3">
            <label class="form-label">Judul Activity <span style="color: red">*</span></label>
            <input type="text" name="judul" class="form-control" 
                   value="<?= htmlspecialchars($editData['judul'] ?? '') ?>" maxlength="50" required>
            <small class="form-text text-muted">Maksimal 50 karakter</small>
        </div>

        <div class="mb-3">
            <label class="form-label">Kategori <span style="color: red">*</span></label>
            <select name="kategori" class="form-control" required>
                <option value="">-- Pilih Kategori --</option>
                <?php
                $cats = ['Research', 'Projects', 'Activity'];
                $currentCat = $editData['kategori'] ?? '';
                foreach ($cats as $c) {
                    $selected = ($c == $currentCat) ? 'selected' : '';
                    echo "<option value=\"$c\" $selected>$c</option>";
                }
                ?>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Tanggal Kegiatan <span style="color: red">*</span></label>
            <input type="date" name="tanggal_kegiatan" class="form-control" 
                   value="<?= htmlspecialchars($editData['tanggal_kegiatan'] ?? '') ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Deskripsi</label>
            <textarea name="deskripsi" rows="5" class="form-control"><?= htmlspecialchars($editData['deskripsi'] ?? '') ?></textarea>
        </div>

        <div class="mb-3 link-section-box">
            <label class="form-label link-section-title">Partisipan Member (Opsional)</label>
            
            <input type="text" id="searchMemberInput" class="form-control" 
                   placeholder="Cari nama member..." 
                   onkeyup="filterMemberSelection()" 
                   style="margin-bottom: 10px; border-color: #01B5B8;">

            <div id="memberListContainer" style="max-height: 200px; overflow-y: auto; border: 1px solid #ccc; padding: 10px; background: #fff; border-radius: 5px;">
                <?php foreach ($members as $m): 
                    $isChecked = in_array($m['id_member'], $selectedMembers) ? 'checked' : '';
                ?>
                    <div class="member-item" style="margin-bottom: 5px;">
                        <label style="font-weight: normal; cursor: pointer; display: flex; align-items: center; gap: 8px;">
                            <input type="checkbox" name="member_ids[]" value="<?= $m['id_member'] ?>" <?= $isChecked ?>>
                            <span class="member-name"><?= htmlspecialchars($m['nama_member']) ?></span>
                        </label>
                    </div>
                <?php endforeach; ?>
                
                <div id="noMemberFound" style="display:none; color:#999; text-align:center; padding:10px;">
                    Member tidak ditemukan.
                </div>
            </div>
            <small style="color: #888;">Centang member yang terlibat dalam aktivitas ini.</small>
        </div>

        <div class="mb-3">
            <label class="form-label">Gambar Activity</label>
            
            <div class="custom-file-upload">
                <input type="file" name="gambar" class="form-control" accept="image/*" id="inputGambar" 
                       onchange="previewActivityImage(event); updateActivityFileName(this);"> 
                
                <label for="inputGambar" class="file-label" id="fileLabel">
                    <span class="file-button">Browse</span> 
                    <span id="fileNameText" class="placeholder-text"><?= $labelTeks ?></span>
                </label>
                
                <button type="button" id="removeImageBtn" class="remove-image-btn" 
                        onclick="removeActivityImage();"
                        style="<?= empty($initialSrc) ? 'display: none;' : '' ?>">&times;</button>
            </div>
            
            <div id="fileError" style="margin-top: 10px; display: none; color: red;"></div>

            <div class="form-preview-box" id="previewBox" style="<?= $boxStyle ?>">
                <img src="<?= $initialSrc ?>" class="img-thumbnail" id="imgPreview">
            </div>

            <input type="hidden" name="remove_existing_image" id="removeExistingImage" value="0">
        </div>

        <div class="mb-3 button-group">
            <button type="button" class="btn btn-secondary" onclick="cancelMemberForm()">Batal</button>
            <button type="submit" id="submitBtn" class="btn btn-primary">
                <?= $editData ? "Update" : "Simpan" ?>
            </button>
        </div>

    </form>
</div>