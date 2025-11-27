<?php
// admin/module/produk/form.php (Hanya Wrapper dan Search)
$initialSrc = !empty($editData['gambar']) ? $webUploadDir . $editData['gambar'] : '';
$initialStyle = empty($editData['gambar']) ? 'display: none;' : '';
$formData = $oldInput ?: $editData;
?>

<div class="card">
    <h2><?= $editData ? "Edit Produk" : "Tambah Produk Baru" ?></h2>
    
    <div id="form-content-wrapper"> 
        
        <?php if (!empty($success)): ?>
            <div class="alert success">
                <?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>
        <?php if (!empty($error)): ?>
            <div class="alert error">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>
        
        <!-- <form method="GET" class="search-form" style="margin-bottom: 20px;">
            <input type="hidden" name="page" value="produk">
            <div style="display: flex; gap: 10px;">
                <input type="text" name="keyword"
                       placeholder="Cari berdasarkan Nama atau Deskripsi..."
                       value="<?= htmlspecialchars($_GET['keyword'] ?? '') ?>"
                       style="flex-grow: 1; padding: 10px; border-radius: 5px; border: 1px solid #ccc;">
                <button type="submit" class="btn btn-primary" style="padding: 10px 15px;">Cari</button>
                <?php if (isset($_GET['keyword'])): ?>
                    <a href="?page=produk" class="btn btn-secondary" style="padding: 10px 15px; text-decoration: none;">Reset</a>
                <?php endif; ?>
            </div>
        </form> -->

        <?php 
        // Ini adalah area di mana form fields lama dimuat.
        // Saat halaman pertama kali dimuat, Anda harus memuat fields
        require_once __DIR__ . '/form-fields.php'; 
        ?>
        
    </div>
</div>