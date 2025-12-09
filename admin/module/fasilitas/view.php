<!-- admin/module/fasilitas/view.php -->

<style>
.card {
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    margin-bottom: 20px;
}
.card-body {
    padding: 20px;
}
.form-group {
    margin-bottom: 15px;
}
.form-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: 600;
    color: #333;
}
.form-control {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
}
.form-control:focus {
    outline: none;
    border-color: #007bff;
}
.btn {
    padding: 10px 20px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 14px;
    text-decoration: none;
    display: inline-block;
}
.btn-primary {
    background: #007bff;
    color: #fff;
}
.btn-primary:hover {
    background: #0056b3;
}
.btn-secondary {
    background: #6c757d;
    color: #fff;
}
.btn-warning {
    background: #ffc107;
    color: #000;
}
.btn-danger {
    background: #dc3545;
    color: #fff;
}
.btn-info {
    background: #17a2b8;
    color: #fff;
}
.table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 10px;
}
.table thead {
    background: #343a40;
    color: #fff;
}
.table th,
.table td {
    padding: 12px;
    text-align: left;
    border-bottom: 1px solid #dee2e6;
}
.table tbody tr:hover {
    background: #f8f9fa;
}
.preview-img {
    max-width: 150px;
    border-radius: 4px;
    margin-top: 10px;
    display: none;
}
.preview-img.show {
    display: block;
}
.alert {
    padding: 12px 20px;
    border-radius: 4px;
    margin-bottom: 15px;
}
.alert-danger {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}
</style>

<!-- Form Tambah/Edit -->
<div class="card">
    <div class="card-body">
        <h3><?= isset($editData) ? 'Edit Fasilitas' : 'Tambah Fasilitas Baru' ?></h3>
        
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="post" action="?page=fasilitas" enctype="multipart/form-data">
            <input type="hidden" name="id_galery" value="<?= htmlspecialchars($editData['id_galery'] ?? '') ?>">
            <input type="hidden" name="gambar_lama" value="<?= htmlspecialchars($editData['gambar'] ?? '') ?>">

            <div class="form-group">
                <label for="judul">Nama Fasilitas</label>
                <input type="text" class="form-control" id="judul" name="judul" required 
                       value="<?= htmlspecialchars($editData['judul'] ?? '') ?>"
                       placeholder="Masukkan nama fasilitas">
            </div>

            <div class="form-group">
                <label for="deskripsi">Deskripsi</label>
                <textarea class="form-control" id="deskripsi" name="deskripsi" rows="4"
                          placeholder="Masukkan deskripsi fasilitas"><?= htmlspecialchars($editData['deskripsi'] ?? '') ?></textarea>
            </div>

            <div class="form-group">
                <label for="gambar">Gambar Fasilitas (Opsional)</label>
                <input type="file" class="form-control" id="gambar" name="gambar" 
                       accept="image/*" onchange="previewImage(event)">
                <small style="color: #666;">Format: JPG, JPEG, PNG, GIF, WEBP. Maksimal 2MB.</small>
                
                <?php if (!empty($editData['gambar'])): ?>
                    <img id="imgPreview" class="preview-img show" 
                         src="<?= htmlspecialchars($webUploadDir . $editData['gambar']) ?>" alt="Preview">
                <?php else: ?>
                    <img id="imgPreview" class="preview-img" src="" alt="Preview">
                <?php endif; ?>
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Simpan
                </button>
                <?php if (isset($editData)): ?>
                    <a href="?page=fasilitas" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Batal
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<!-- Daftar Fasilitas (HANYA 1 SECTION INI) -->
<div class="card">
    <div class="card-body">
        <h3>Daftar Fasilitas</h3>
        
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th style="width: 50px;">NO</th>
                        <th>JUDUL</th>
                        <th>GAMBAR</th>
                        <th>DESKRIPSI</th>
                        <th style="width: 150px;">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                <?php 
                if (isset($stmt_list) && $stmt_list && $stmt_list->rowCount() > 0):
                    $no = 1;
                    while ($row = $stmt_list->fetch(PDO::FETCH_ASSOC)): 
                ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= htmlspecialchars($row['judul']) ?></td>
                        <td>
                            <?php if (!empty($row['gambar'])): ?>
                                <button type="button" class="btn btn-info btn-sm" 
                                        onclick="showImage('<?= htmlspecialchars($webUploadDir . $row['gambar']) ?>')">
                                    <i class="fas fa-eye"></i> Lihat
                                </button>
                            <?php else: ?>
                                <span style="color: #999;">-</span>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars(strlen($row['deskripsi']) > 100 ? substr($row['deskripsi'], 0, 100) . '...' : $row['deskripsi']) ?></td>
                        <td>
                            <a href="?page=fasilitas&edit=<?= (int)$row['id_galery'] ?>" 
                               class="btn btn-warning btn-sm" title="Edit">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <a href="?page=fasilitas&delete=<?= (int)$row['id_galery'] ?>" 
                               class="btn btn-danger btn-sm" 
                               onclick="return confirm('Yakin ingin menghapus fasilitas ini?')" 
                               title="Hapus">
                                <i class="fas fa-trash"></i> Hapus
                            </a>
                        </td>
                    </tr>
                <?php 
                    endwhile; 
                else:
                ?>
                    <tr>
                        <td colspan="5" style="text-align: center; color: #999; padding: 30px;">
                            <i class="fas fa-inbox"></i> Tidak ada data fasilitas
                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Preview Gambar -->
<div id="imageModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.8); z-index:9999; align-items:center; justify-content:center;">
    <div style="position:relative; max-width:90%; max-height:90%; background:#fff; padding:20px; border-radius:8px;">
        <button onclick="closeImage()" style="position:absolute; top:10px; right:10px; background:#000; color:#fff; border:none; width:30px; height:30px; border-radius:50%; cursor:pointer; font-size:20px;">×</button>
        <img id="modalImg" src="" style="max-width:100%; max-height:80vh; border-radius:4px;">
    </div>
</div>

<script>
function previewImage(event) {
    const img = document.getElementById('imgPreview');
    if (!event.target.files || !event.target.files[0]) return;
    img.src = URL.createObjectURL(event.target.files[0]);
    img.classList.add('show');
}

function showImage(src) {
    document.getElementById('modalImg').src = src;
    document.getElementById('imageModal').style.display = 'flex';
}

function closeImage() {
    document.getElementById('imageModal').style.display = 'none';
    document.getElementById('modalImg').src = '';
}

document.getElementById('imageModal').addEventListener('click', function(e) {
    if (e.target === this) closeImage();
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeImage();
});
</script>