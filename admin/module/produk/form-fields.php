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
    <div class="card-section">

        <h4>Tim Pengembang Produk</h4>
        <p class="text-muted">Tambahkan member dan role untuk produk ini.</p>

        <!-- Input Tambah Tim -->
        <div class="team-input-row">
            <select id="memberSelect" class="form-control">
                <option value="">-- Pilih Member --</option>
                <?php foreach ($memberOptions as $m): ?>
                    <option value="<?= $m['id_member'] ?>">
                        <?= htmlspecialchars($m['nama_member']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <input id="roleInput" type="text" class="form-control" placeholder="Role (ex: Backend Dev)">

            <button type="button" class="btn btn-success" onclick="addTeamToTable()">Tambah</button>
        </div>

        <!-- Tabel Tim -->
        <table class="table table-bordered mt-3" id="teamTable">
            <thead>
                <tr>
                    <th style="width:35%;">Member</th>
                    <th style="width:45%;">Role</th>
                    <th style="width:20%; text-align:center;">Aksi</th>
                </tr>
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
</div>

<!-- TEMPLATE UNTUK ROW BARU (TIDAK ADA DIV NGESOT) -->
<template id="teamRowTemplate">
    <tr>
        <td>
            <select name="member_ids[]" class="form-control" required>
                <option value="">-- Pilih Member --</option>
                <?php foreach ($memberOptions as $opt): ?>
                    <option value="<?= $opt['id_member'] ?>">
                        <?= htmlspecialchars($opt['nama_member']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </td>

        <td>
            <input type="text" name="member_roles[]" class="form-control"
                   placeholder="Role (ex: Backend Dev)" required>
        </td>

        <td style="text-align:center;">
            <button type="button" class="btn btn-danger btn-sm" onclick="removeTeamRowTable(this)">✕</button>
        </td>
    </tr>
</template>

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
