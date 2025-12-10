<?php
// Ambil opsi member
$memberOptions = getAllMembersOption($pdo);

// Data tim ketika edit
$existingTeam = $editData['team'] ?? [];
?>

<h2><?= $editData ? "Edit Produk" : "Tambah Produk Baru" ?></h2>

<form id="productForm" method="POST" class="form-grid" enctype="multipart/form-data">

    <?php if ($editData): ?>
        <input type="hidden" name="id_produk" value="<?= $editData['id_produk'] ?>">
        <input type="hidden" name="gambar_lama" value="<?= htmlspecialchars($editData['gambar'] ?? '') ?>">
    <?php endif; ?>


    <!-- Nama Produk -->
    <div class="mb-3">
        <label class="form-label">Nama Produk</label>
        <input type="text" name="nama" class="form-control"
               value="<?= htmlspecialchars($formData['nama'] ?? '') ?>" required>
    </div>

    <!-- Link Produk -->
    <div class="mb-3">
        <label class="form-label">Link Produk</label>
        <input type="text" name="link_produk" class="form-control"
               value="<?= htmlspecialchars($formData['link_produk'] ?? '') ?>"
               placeholder="https://...">
    </div>

    <!-- Deskripsi -->
    <div class="mb-3">
        <label class="form-label">Deskripsi</label>
        <textarea name="deskripsi" rows="4" class="form-control" required><?= htmlspecialchars($formData['deskripsi'] ?? '') ?></textarea>
    </div>



    <!-- =============================== -->
    <!--         TIM PENGEMBANG          -->
    <!-- =============================== -->
    <div class="mb-3 link-section-box">
        <label class="form-label link-section-title">Tim Pengembang (Member & Role)</label>
        <small style="display:block; margin-bottom:10px; color:#666;">Tambah anggota & role untuk produk ini.</small>

        <div id="team-container">
            <!-- JIKA MODE EDIT: TAMPILKAN DATA TIM YANG ADA -->
            <?php if (!empty($existingTeam)): ?>
                <?php foreach ($existingTeam as $tm): ?>
                    <div class="link-row">
                        
                        <!-- Dropdown Member -->
                        <select name="member_ids[]" class="form-control" style="flex:1;" required>
                            <option value="">-- Pilih Member --</option>
                            <?php foreach ($memberOptions as $opt): ?>
                                <option value="<?= $opt['id_member'] ?>"
                                    <?= ($tm['id_member'] == $opt['id_member']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($opt['nama_member']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>

                        <!-- Role -->
                        <input type="text"
                               name="member_roles[]"
                               class="form-control"
                               placeholder="Role (ex: Frontend Dev)"
                               value="<?= htmlspecialchars($tm['role']) ?>"
                               style="flex:1;" required>

                        <button type="button" class="btn-remove-link" onclick="removeTeamRow(this)">&times;</button>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Tombol Tambah Baris -->
        <button type="button" class="btn-add-link" onclick="addTeamRow()">+ Tambah Anggota Tim</button>

        <!-- TEMPLATE ROW -->
        <template id="teamRowTemplate">
            <div class="link-row">

                <select name="member_ids[]" class="form-control" style="flex:1;" required>
                    <option value="">-- Pilih Member --</option>
                    <?php foreach ($memberOptions as $opt): ?>
                        <option value="<?= $opt['id_member'] ?>">
                            <?= htmlspecialchars($opt['nama_member']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <input type="text" 
                       name="member_roles[]" 
                       class="form-control" 
                       placeholder="Role (ex: Backend Dev)" 
                       style="flex:1;" 
                       required>

                <button type="button" class="btn-remove-link" onclick="removeTeamRow(this)">&times;</button>
            </div>
        </template>
    </div>



    <!-- =============================== -->
    <!--            GAMBAR               -->
    <!-- =============================== -->
    <div class="mb-3">
        <label class="form-label">Gambar Produk</label>

        <div class="custom-file-upload">
            <input type="file" name="gambar" class="form-control"
                   accept="image/*" id="inputGambar"
                   onchange="previewImage(event); updateFileName(this);">

            <label for="inputGambar" class="file-label" id="fileLabel">
                <span class="file-button">Browse</span>
                <span id="fileNameText" class="placeholder-text">Tidak ada file yang dipilih...</span>
            </label>

            <button type="button"
                    id="removeImageBtn"
                    class="remove-image-btn"
                    onclick="removeImage();"
                    style="<?= empty($initialSrc) ? 'display:none;' : '' ?>">
                &times;
            </button>
        </div>

        <div class="preview mt-2">
            <img src="<?= $initialSrc ?>" class="img-thumbnail"
                 id="imgPreview" style="<?= $initialStyle ?>">
        </div>

        <input type="hidden" name="remove_existing_image" id="removeExistingImage" value="0">
    </div>


    <!-- Buttons -->
    <div class="mb-3 button-group">
        <button type="submit" id="submitBtn" class="btn btn-primary">
            <?= $editData ? "Update" : "Simpan" ?>
        </button>

        <button type="button" class="btn btn-secondary" onclick="cancelProductForm()">
            Batal
        </button>
    </div>

</form>
