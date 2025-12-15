<?php
// admin/module/fasilitas/table.php

// [FIX PENTING] Bongkar array paginationData agar variabel $currentPage terbaca
if (isset($paginationData) && is_array($paginationData)) {
    extract($paginationData);
}

// Fallback jika variabel belum ada (misal dipanggil manual tanpa array)
if (!isset($list)) $list = [];
if (!isset($currentPage)) $currentPage = 1;
if (!isset($totalPages)) $totalPages = 1;
if (!isset($searchKeyword)) $searchKeyword = '';

// Helper URL
if (!function_exists('buildFasilitasUrl')) {
    function buildFasilitasUrl($p, $keyword) {
        $qs = '?page=fasilitas&p=' . $p;
        if ($keyword) $qs .= '&keyword=' . urlencode($keyword);
        return $qs;
    }
}
?>

<div class="fasilitas-grid">
    <?php if (!empty($list)): ?>
        <?php foreach ($list as $row): 
            $imgSrc = 'assets/img/no-image.png'; 
            $uploadPath = '../public/uploads/fasilitas/';
            
            if (!empty($row['gambar'])) {
                $imgSrc = $uploadPath . $row['gambar'];
            }
        ?>
        <div class="fasilitas-card">
            <div class="fasilitas-img-wrapper">
                <?php if(!empty($row['gambar'])): ?>
                    <img src="<?= $imgSrc ?>" alt="<?= htmlspecialchars($row['judul']) ?>" loading="lazy">
                <?php else: ?>
                    <div class="no-image-placeholder">No Image</div>
                <?php endif; ?>
            </div>
            
            <div class="fasilitas-content">
                <h4 class="fasilitas-title"><?= htmlspecialchars($row['judul']) ?></h4>
                <div class="fasilitas-desc">
                    <?= nl2br(htmlspecialchars($row['deskripsi'])) ?>
                </div>
            </div>
            
            <div class="card-action-buttons">
                <a href="?page=fasilitas&edit=<?= $row['id_fasilitas'] ?>" class="btn-card btn-card-edit">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <a href="javascript:void(0)" onclick="deleteFasilitas(<?= $row['id_fasilitas'] ?>)" class="btn-card btn-card-delete">
                    <i class="fas fa-trash"></i> Hapus
                </a>
            </div>
        </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div style="grid-column: 1 / -1; text-align: center; padding: 40px; color: #777;">
            <i class="fas fa-box-open" style="font-size: 40px; margin-bottom: 10px; opacity: 0.5;"></i><br>
            Data fasilitas tidak ditemukan.
        </div>
    <?php endif; ?>
</div>

<?php if ($totalPages > 1): ?>
    <div class="pagination">

        <?php if ($currentPage > 1): ?>
            <a href="<?= buildFasilitasUrl($currentPage - 1, $searchKeyword) ?>" class="page-link page-arrow" title="Sebelumnya">&lsaquo;</a>
        <?php else: ?>
            <span class="page-link page-arrow disabled">&lsaquo;</span>
        <?php endif; ?>

        <?php if ($currentPage > 1): ?>
            <a href="<?= buildFasilitasUrl(1, $searchKeyword) ?>" class="page-link page-arrow" title="Ke Awal">&laquo;</a>
        <?php else: ?>
            <span class="page-link page-arrow disabled">&laquo;</span>
        <?php endif; ?>

        <?php
        $max_buttons = 5;
        $half = floor($max_buttons / 2);
        $start_page = $currentPage - $half;
        $end_page   = $currentPage + $half;

        if ($start_page < 1) { 
            $start_page = 1; 
            $end_page = $start_page + $max_buttons - 1; 
        }
        if ($end_page > $totalPages) { 
            $end_page = $totalPages; 
            $start_page = $end_page - $max_buttons + 1; 
            if ($start_page < 1) $start_page = 1; 
        }

        for ($i = $start_page; $i <= $end_page; $i++):
            if ($i <= 0 || $i > $totalPages) continue;
            // Di sini kuncinya: $i == $currentPage (sekarang $currentPage sudah benar isinya 2)
            $isActive = ($i == $currentPage) ? 'active' : '';
        ?>
            <a href="<?= buildFasilitasUrl($i, $searchKeyword) ?>" class="page-link page-num <?= $isActive ?>"><?= $i ?></a>
        <?php endfor; ?>

        <?php if ($currentPage < $totalPages): ?>
            <a href="<?= buildFasilitasUrl($totalPages, $searchKeyword) ?>" class="page-link page-arrow" title="Ke Akhir">&raquo;</a>
        <?php else: ?>
            <span class="page-link page-arrow disabled">&raquo;</span>
        <?php endif; ?>

        <?php if ($currentPage < $totalPages): ?>
            <a href="<?= buildFasilitasUrl($currentPage + 1, $searchKeyword) ?>" class="page-link page-arrow" title="Berikutnya">&rsaquo;</a>
        <?php else: ?>
            <span class="page-link page-arrow disabled">&rsaquo;</span>
        <?php endif; ?>

    </div>
<?php endif; ?>