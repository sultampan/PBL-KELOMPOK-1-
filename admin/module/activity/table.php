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
                // 1. Grouping Data by kategori
                $grouped = [];
                foreach ($list as $item) {
                    $cat = $item['kategori'] ?: 'Uncategorized';
                    $grouped[$cat][] = $item;
                }

                // 2. Render Per Kategori
                foreach ($grouped as $categoryName => $items): 
                ?>
                    <div class="activity-category-section">
                        
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
                                    <?php if (!empty($row['deskripsi'])): ?>
                                        <div class="activity-item-description">
                                            <?= htmlspecialchars($row['deskripsi']) ?>
                                        </div>
                                    <?php endif; ?>

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