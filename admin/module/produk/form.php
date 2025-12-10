<?php
// admin/module/produk/form.php

// Pastikan fungsi-fungsi model tersedia
require_once __DIR__ . '/model.php';

// Ambil data members untuk dropdown
$memberOptions = getAllMembersOption($pdo);

// Ambil tim produk jika mode edit
$existingTeam = [];
if (!empty($editData['id_produk'])) {
    $existingTeam = getTeamByProduk($pdo, $editData['id_produk']); // <-- Tambahkan fungsi ini di model
    $editData['team'] = $existingTeam;
}

// Inisialisasi gambar
$initialSrc = !empty($editData['gambar']) ? $webUploadDir . $editData['gambar'] : '';
$initialStyle = empty($editData['gambar']) ? 'display: none;' : '';

// Data lama atau data edit
$formData = $oldInput ?: $editData;
?>

<div class="card">

    <div id="form-content-wrapper"> 

        <?php 
        // Kirim variabel ke form-fields.php
        $memberOptions = $memberOptions;
        $existingTeam  = $editData['team'] ?? [];
        $formData      = $formData;
        $initialSrc    = $initialSrc;
        $initialStyle  = $initialStyle;

        require __DIR__ . '/form-fields.php'; 
        ?>

    </div>

</div>
