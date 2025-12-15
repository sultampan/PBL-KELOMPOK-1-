<?php
// admin/module/activity/table.php
?>
<div id="activity-list-container">
    <div class="activity-main-card">
        <!-- Header dengan Search dalam satu kotak -->
        <div class="card-header-section">
            <div class="header-title">Daftar Activity</div>
            <div class="search-section">
                <input type="text" id="searchActivityInput" class="search-input" 
                       placeholder="Cari Nama Activity atau Deskripsi..." 
                       value="<?= htmlspecialchars($searchKeyword ?? '') ?>">
                <button type="button" onclick="searchActivity()" class="btn-search">
                    <i class="fas fa-search"></i> Cari
                </button>
            </div>
        </div>

        <!-- Content Area -->
        <div class="activity-content">
            <?php if ($list && count($list) > 0): ?>
                <?php 
                // 1. Grouping Data by kategori (normalize ke lowercase)
                $grouped = [];
                $categoryMap = []; // Map lowercase ke original case
                foreach ($list as $item) {
                    $cat = $item['kategori'] ?: 'Uncategorized';
                    $catLower = strtolower($cat);
                    $grouped[$catLower][] = $item;
                    if (!isset($categoryMap[$catLower])) {
                        $categoryMap[$catLower] = $cat; // Simpan case asli
                    }
                }

                // 2. Urutan kategori yang tetap (lowercase untuk matching)
                $categoryOrder = ['research', 'projects', 'activity'];
                
                // 2a. Konfigurasi pagination per kategori
                $itemsPerPage = 3; // Jumlah item per halaman (ubah sesuai kebutuhan)
                
                // 3. Render Per Kategori sesuai urutan yang ditentukan
                foreach ($categoryOrder as $categoryNameLower): 
                    // Skip jika kategori tidak ada data
                    if (!isset($grouped[$categoryNameLower]) || empty($grouped[$categoryNameLower])) {
                        continue;
                    }
                    
                    $allItems = $grouped[$categoryNameLower];
                    $categoryName = $categoryMap[$categoryNameLower]; // Gunakan case asli untuk display
                    
                    // Ambil halaman saat ini untuk kategori ini
                    $pageParam = 'page_' . $categoryNameLower;
                    $currentPage = isset($_GET[$pageParam]) ? max(1, intval($_GET[$pageParam])) : 1;
                    
                    // Hitung total halaman
                    $totalItems = count($allItems);
                    $totalPages = ceil($totalItems / $itemsPerPage);
                    
                    // Ambil data untuk halaman saat ini
                    $offset = ($currentPage - 1) * $itemsPerPage;
                    $items = array_slice($allItems, $offset, $itemsPerPage);
                ?>
                    <div class="activity-category-section" data-category="<?= $pageParam ?>">
                        
                        <h3 class="category-title"><?= htmlspecialchars($categoryName) ?></h3>
                        
                        <div class="activity-grid" id="grid-<?= md5($categoryName) ?>">
                            
                            <?php foreach ($items as $row): ?>
                                <div class="activity-item">
                                    <!-- Badge dan Tanggal -->
                                    <div class="activity-item-header">
                                        <span class="badge-activity">ACTIVITY</span>
                                        <span class="activity-date-small"><?= date('d M Y', strtotime($row['tanggal_kegiatan'])) ?></span>
                                    </div>

                                    <!-- Judul -->
                                    <h3 class="activity-item-title"><?= htmlspecialchars($row['judul']) ?></h3>

                                    <!-- Gambar -->
                                    <div class="activity-item-image">
                                        <?php
                                        $imgSrc = '';
                                        $hasImage = false;
                                        
                                        if (!empty($row['gambar'])) {
                                            $thumb = pathinfo($row['gambar'], PATHINFO_FILENAME) . '-thumb.' . pathinfo($row['gambar'], PATHINFO_EXTENSION);
                                            if (is_file($serverThumbDir . $thumb)) {
                                                $imgSrc = $webThumbDir . $thumb . '?' . time();
                                                $hasImage = true;
                                            } elseif (is_file($serverUploadDir . $row['gambar'])) {
                                                $imgSrc = $webUploadDir . $row['gambar'] . '?' . time();
                                                $hasImage = true;
                                            }
                                        }
                                        ?>
                                        <?php if ($hasImage): ?>
                                            <img src="<?= $imgSrc ?>" alt="<?= htmlspecialchars($row['judul']) ?>">
                                        <?php else: ?>
                                            <div class="no-image">
                                                <i class="fas fa-image"></i>
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Deskripsi -->
                                    <div class="activity-item-description <?= empty($row['deskripsi']) ? 'empty-description' : '' ?>">
                                        <?php if (!empty($row['deskripsi'])): ?>
                                            <?= htmlspecialchars($row['deskripsi']) ?>
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </div>

                                    <!-- Members Section -->
                                    <div class="activity-item-members">
                                        <div class="members-label">
                                            <i class="fas fa-users"></i> Member Berpartisipasi:
                                        </div>
                                        <div class="members-tags">
                                            <?php 
                                            if (!empty($row['members']) && is_array($row['members']) && count($row['members']) > 0) {
                                                foreach ($row['members'] as $member): 
                                                    $memberName = is_array($member) ? ($member['nama_member'] ?? 'Unknown') : $member;
                                            ?>
                                                <span class="member-tag">
                                                    <i class="fas fa-user"></i>
                                                    <?= htmlspecialchars($memberName) ?>
                                                </span>
                                            <?php 
                                                endforeach;
                                            } else {
                                                echo '<span class="no-members-text">Tidak ada</span>';
                                            }
                                            ?>
                                        </div>
                                    </div>

                                    <!-- Action Buttons -->
                                    <div class="activity-item-actions">
                                        <a href="?page=activity&edit=<?= $row['id_activity'] ?>" class="btn-action btn-edit">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <button type="button" onclick="deleteActivity(<?= (int)$row['id_activity'] ?>)" class="btn-action btn-delete">
                                            <i class="fas fa-trash"></i> Hapus
                                        </button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            
                        </div>

                        <!-- Pagination untuk kategori ini -->
                        <?php if ($totalPages > 1): ?>
                        <div class="pagination-container">
                            <?php
                            // Tombol First (ke halaman pertama)
                            if ($currentPage > 1) {
                                echo '<button onclick="navigatePage(\'' . $pageParam . '\', 1)" title="First Page">«</button>';
                            } else {
                                echo '<button disabled title="First Page">«</button>';
                            }
                            
                            // Tombol Previous
                            if ($currentPage > 1) {
                                $prevPage = $currentPage - 1;
                                echo '<button onclick="navigatePage(\'' . $pageParam . '\', ' . $prevPage . ')" title="Previous Page">‹</button>';
                            } else {
                                echo '<button disabled title="Previous Page">‹</button>';
                            }
                            
                            // Tombol Nomor Halaman
                            for ($i = 1; $i <= $totalPages; $i++) {
                                $activeClass = ($i == $currentPage) ? 'active' : '';
                                echo '<button class="' . $activeClass . '" onclick="navigatePage(\'' . $pageParam . '\', ' . $i . ')">' . $i . '</button>';
                            }
                            
                            // Tombol Next
                            if ($currentPage < $totalPages) {
                                $nextPage = $currentPage + 1;
                                echo '<button onclick="navigatePage(\'' . $pageParam . '\', ' . $nextPage . ')" title="Next Page">›</button>';
                            } else {
                                echo '<button disabled title="Next Page">›</button>';
                            }
                            
                            // Tombol Last (ke halaman terakhir)
                            if ($currentPage < $totalPages) {
                                echo '<button onclick="navigatePage(\'' . $pageParam . '\', ' . $totalPages . ')" title="Last Page">»</button>';
                            } else {
                                echo '<button disabled title="Last Page">»</button>';
                            }
                            ?>
                        </div>
                        <?php endif; ?>

                    </div>
                <?php 
                endforeach; 
                
                // 4. Tampilkan kategori lain yang tidak ada di urutan tetap (jika ada)
                foreach ($grouped as $categoryNameLower => $allItems):
                    if (in_array($categoryNameLower, $categoryOrder)) {
                        continue; // Skip kategori yang sudah ditampilkan
                    }
                    $categoryName = $categoryMap[$categoryNameLower];
                    
                    // Pagination untuk kategori lain
                    $pageParam = 'page_' . $categoryNameLower;
                    $currentPage = isset($_GET[$pageParam]) ? max(1, intval($_GET[$pageParam])) : 1;
                    
                    $totalItems = count($allItems);
                    $totalPages = ceil($totalItems / $itemsPerPage);
                    
                    $offset = ($currentPage - 1) * $itemsPerPage;
                    $items = array_slice($allItems, $offset, $itemsPerPage);
                ?>
                    <div class="activity-category-section" data-category="<?= $pageParam ?>">
                        
                        <h3 class="category-title"><?= htmlspecialchars($categoryName) ?></h3>
                        
                        <div class="activity-grid" id="grid-<?= md5($categoryName) ?>">
                            
                            <?php foreach ($items as $row): ?>
                                <div class="activity-item">
                                    <!-- Badge dan Tanggal -->
                                    <div class="activity-item-header">
                                        <span class="badge-activity">ACTIVITY</span>
                                        <span class="activity-date-small"><?= date('d M Y', strtotime($row['tanggal_kegiatan'])) ?></span>
                                    </div>

                                    <!-- Judul -->
                                    <h3 class="activity-item-title"><?= htmlspecialchars($row['judul']) ?></h3>

                                    <!-- Gambar -->
                                    <div class="activity-item-image">
                                        <?php
                                        $imgSrc = '';
                                        $hasImage = false;
                                        
                                        if (!empty($row['gambar'])) {
                                            $thumb = pathinfo($row['gambar'], PATHINFO_FILENAME) . '-thumb.' . pathinfo($row['gambar'], PATHINFO_EXTENSION);
                                            if (is_file($serverThumbDir . $thumb)) {
                                                $imgSrc = $webThumbDir . $thumb . '?' . time();
                                                $hasImage = true;
                                            } elseif (is_file($serverUploadDir . $row['gambar'])) {
                                                $imgSrc = $webUploadDir . $row['gambar'] . '?' . time();
                                                $hasImage = true;
                                            }
                                        }
                                        ?>
                                        <?php if ($hasImage): ?>
                                            <img src="<?= $imgSrc ?>" alt="<?= htmlspecialchars($row['judul']) ?>">
                                        <?php else: ?>
                                            <div class="no-image">
                                                <i class="fas fa-image"></i>
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Deskripsi -->
                                    <div class="activity-item-description <?= empty($row['deskripsi']) ? 'empty-description' : '' ?>">
                                        <?php if (!empty($row['deskripsi'])): ?>
                                            <?= htmlspecialchars($row['deskripsi']) ?>
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </div>

                                    <!-- Members Section -->
                                    <div class="activity-item-members">
                                        <div class="members-label">
                                            <i class="fas fa-users"></i> Member Berpartisipasi:
                                        </div>
                                        <div class="members-tags">
                                            <?php 
                                            if (!empty($row['members']) && is_array($row['members']) && count($row['members']) > 0) {
                                                foreach ($row['members'] as $member): 
                                                    $memberName = is_array($member) ? ($member['nama_member'] ?? 'Unknown') : $member;
                                            ?>
                                                <span class="member-tag">
                                                    <i class="fas fa-user"></i>
                                                    <?= htmlspecialchars($memberName) ?>
                                                </span>
                                            <?php 
                                                endforeach;
                                            } else {
                                                echo '<span class="no-members-text">Tidak ada</span>';
                                            }
                                            ?>
                                        </div>
                                    </div>

                                    <!-- Action Buttons -->
                                    <div class="activity-item-actions">
                                        <a href="?page=activity&edit=<?= $row['id_activity'] ?>" class="btn-action btn-edit">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <button type="button" onclick="deleteActivity(<?= (int)$row['id_activity'] ?>)" class="btn-action btn-delete">
                                            <i class="fas fa-trash"></i> Hapus
                                        </button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            
                        </div>

                        <!-- Pagination untuk kategori lain -->
                        <?php if ($totalPages > 1): ?>
                        <div class="pagination-container">
                            <?php
                            // Tombol First (ke halaman pertama)
                            if ($currentPage > 1) {
                                echo '<button onclick="navigatePage(\'' . $pageParam . '\', 1)" title="First Page">«</button>';
                            } else {
                                echo '<button disabled title="First Page">«</button>';
                            }
                            
                            // Tombol Previous
                            if ($currentPage > 1) {
                                $prevPage = $currentPage - 1;
                                echo '<button onclick="navigatePage(\'' . $pageParam . '\', ' . $prevPage . ')" title="Previous Page">‹</button>';
                            } else {
                                echo '<button disabled title="Previous Page">‹</button>';
                            }
                            
                            // Tombol Nomor Halaman
                            for ($i = 1; $i <= $totalPages; $i++) {
                                $activeClass = ($i == $currentPage) ? 'active' : '';
                                echo '<button class="' . $activeClass . '" onclick="navigatePage(\'' . $pageParam . '\', ' . $i . ')">' . $i . '</button>';
                            }
                            
                            // Tombol Next
                            if ($currentPage < $totalPages) {
                                $nextPage = $currentPage + 1;
                                echo '<button onclick="navigatePage(\'' . $pageParam . '\', ' . $nextPage . ')" title="Next Page">›</button>';
                            } else {
                                echo '<button disabled title="Next Page">›</button>';
                            }
                            
                            // Tombol Last (ke halaman terakhir)
                            if ($currentPage < $totalPages) {
                                echo '<button onclick="navigatePage(\'' . $pageParam . '\', ' . $totalPages . ')" title="Last Page">»</button>';
                            } else {
                                echo '<button disabled title="Last Page">»</button>';
                            }
                            ?>
                        </div>
                        <?php endif; ?>

                    </div>
                <?php endforeach; ?>

            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-calendar-alt"></i>
                    <p>Belum ada data activity yang ditemukan.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>