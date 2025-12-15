<div id="activity-list-container">
    <div class="card-header-section" style="padding: 0 0 20px 0; border-bottom: none;">
        <div class="header-title">Daftar Activity</div>
        <div class="search-section">
            <input type="text" id="searchActivityInput" class="search-input" 
                   placeholder="Cari Activity..." 
                   value="<?= htmlspecialchars($searchKeyword ?? '') ?>">
            <button type="button" onclick="searchActivity()" class="btn-search">
                <i class="fas fa-search"></i> Cari
            </button>
        </div>
    </div>

    <div class="activity-wrapper">
        <?php if ($list): ?>
            <?php 
            // 1. Grouping Data
            $grouped = [];
            foreach ($list as $item) {
                $cat = $item['kategori'] ?: 'Uncategorized';
                $grouped[$cat][] = $item;
            }

            // 2. Loop Per Kategori
            foreach ($grouped as $categoryName => $items): 
                $catId = md5($categoryName);
            ?>
                <div class="activity-category-section mb-5" data-category="<?= htmlspecialchars($categoryName) ?>">
                    
                    <h3 class="category-title"><?= htmlspecialchars($categoryName) ?></h3>
                    
                    <div class="activity-grid paginated-grid" id="grid-<?= $catId ?>" data-current-page="1" data-items-per-page="6">
                        
                        <?php foreach ($items as $row): 
                             // A. Setup Gambar
                             $imgSrc = '';
                             if (!empty($row['gambar']) && file_exists($serverUploadDir . $row['gambar'])) {
                                 $imgSrc = $webUploadDir . $row['gambar'];
                             }

                             // B. Setup Tanggal
                             $displayDate = '-';
                             if (!empty($row['tanggal_kegiatan'])) {
                                 $timestamp = strtotime($row['tanggal_kegiatan']);
                                 if ($timestamp) $displayDate = date('d M Y', $timestamp);
                             }

                             // C. AMBIL DATA MEMBER dengan error handling
                             $members = [];
                             try {
                                 if (function_exists('getActivityMembers')) {
                                     $members = getActivityMembers($pdo, $row['id_activity']);
                                 }
                             } catch (Exception $e) {
                                 error_log("Error getting members for activity {$row['id_activity']}: " . $e->getMessage());
                             }
                             
                             // D. Setup untuk display member (max 5)
                             $totalMembers = count($members);
                             $maxDisplay = 5;
                             $displayMembers = array_slice($members, 0, $maxDisplay);
                             $remainingCount = $totalMembers - $maxDisplay;
                        ?>
                            <div class="activity-item grid-item"> 
                                
                                <div class="activity-item-header">
                                    <span class="badge-activity"><?= htmlspecialchars($row['kategori']) ?></span>
                                    <span class="activity-date-small"><?= $displayDate ?></span>
                                </div>

                                <h4 class="activity-item-title" title="<?= htmlspecialchars($row['judul']) ?>">
                                    <?= htmlspecialchars($row['judul']) ?>
                                </h4>

                                <div class="activity-item-image">
                                    <?php if ($imgSrc): ?>
                                        <img src="<?= $imgSrc ?>" alt="Activity Image">
                                    <?php else: ?>
                                        <div class="no-image"><i class="fas fa-image"></i></div>
                                    <?php endif; ?>
                                </div>

                                <div class="activity-item-description">
                                    <?= strip_tags(html_entity_decode($row['deskripsi'])) ?>
                                </div>

                                <div class="activity-item-members">
                                    <div class="members-label">
                                        <i class="fas fa-users"></i> Partisipan:
                                    </div>
                                    <div class="members-tags-scroll">
                                        <?php if (!empty($members)): ?>
                                            <?php 
                                            // Tampilkan maksimal 5 member pertama
                                            foreach ($displayMembers as $m): 
                                            ?>
                                                <span class="member-tag">
                                                    <?= htmlspecialchars($m['nama_member']) ?>
                                                </span>
                                            <?php endforeach; ?>
                                            
                                            <?php 
                                            // Jika ada lebih dari 5 member, tampilkan +X
                                            if ($remainingCount > 0): 
                                            ?>
                                                <span class="member-tag member-tag-more" 
                                                      onclick="showAllMembers(<?= $row['id_activity'] ?>, '<?= htmlspecialchars($row['judul'], ENT_QUOTES) ?>')"
                                                      title="Klik untuk melihat semua partisipan">
                                                    +<?= $remainingCount ?>
                                                </span>
                                            <?php endif; ?>
                                            
                                        <?php else: ?>
                                            <span class="no-members-text">- Tidak ada member -</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <div class="activity-item-actions">
                                    <a href="?page=activity&edit=<?= $row['id_activity'] ?>" class="btn-action btn-edit">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <button type="button" onclick="deleteActivity(<?= $row['id_activity'] ?>)" class="btn-action btn-delete">
                                        <i class="fas fa-trash"></i> Hapus
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        
                    </div>

                    <div class="pagination-container" id="pagination-<?= $catId ?>"></div>

                </div>
            <?php endforeach; ?>

        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-folder-open"></i>
                <p>Belum ada data activity.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal untuk menampilkan semua members -->
<div id="membersModal" class="modal-members" style="display: none;">
    <div class="modal-members-overlay" onclick="closeAllMembersModal()"></div>
    <div class="modal-members-content">
        <div class="modal-members-header">
            <h3 id="modalMembersTitle">Semua Partisipan</h3>
            <button class="modal-close-btn" onclick="closeAllMembersModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-members-body" id="modalMembersBody">
            <!-- Will be filled by JavaScript -->
        </div>
    </div>
</div>

<script>
// Initialize pagination
if(typeof initActivityPagination === 'function') {
    initActivityPagination();
}

// Function to show all members in modal
function showAllMembers(activityId, activityTitle) {
    // Show loading
    const modalBody = document.getElementById('modalMembersBody');
    const modalTitle = document.getElementById('modalMembersTitle');
    
    modalTitle.textContent = 'Semua Partisipan - ' + activityTitle;
    modalBody.innerHTML = '<div class="loading-members"><i class="fas fa-spinner fa-spin"></i> Memuat data...</div>';
    
    // Show modal
    document.getElementById('membersModal').style.display = 'flex';
    
    // Fetch members via AJAX
    fetch('?page=activity&action=get_members&id=' + activityId)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            let html = '';
            
            if (data.success && data.members && data.members.length > 0) {
                html = '<div class="all-members-grid">';
                data.members.forEach((member, index) => {
                    html += `
                        <div class="member-item-modal">
                            <div class="member-number">${index + 1}</div>
                            <div class="member-info">
                                <i class="fas fa-user-circle"></i>
                                <span class="member-name">${escapeHtml(member.nama_member)}</span>
                            </div>
                        </div>
                    `;
                });
                html += '</div>';
            } else {
                html = '<div class="no-members-modal"><i class="fas fa-users-slash"></i><p>Tidak ada member</p></div>';
            }
            
            modalBody.innerHTML = html;
        })
        .catch(error => {
            console.error('Error:', error);
            modalBody.innerHTML = '<div class="error-members"><i class="fas fa-exclamation-triangle"></i><p>Gagal memuat data partisipan</p></div>';
        });
}

function closeAllMembersModal() {
    document.getElementById('membersModal').style.display = 'none';
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Close modal when pressing ESC
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeAllMembersModal();
    }
});
</script>

<style>
/* ===========================
   MEMBER TAG STYLING
   =========================== */

/* Container untuk scroll horizontal */
.members-tags-scroll {
    display: flex;
    gap: 8px;
    overflow-x: auto;
    overflow-y: hidden;
    padding: 4px 0;
    scrollbar-width: thin;
    scrollbar-color: #cbd5e0 transparent;
}

/* Hide scrollbar untuk Webkit browsers */
.members-tags-scroll::-webkit-scrollbar {
    height: 4px;
}

.members-tags-scroll::-webkit-scrollbar-track {
    background: transparent;
}

.members-tags-scroll::-webkit-scrollbar-thumb {
    background: #cbd5e0;
    border-radius: 10px;
}

.members-tags-scroll::-webkit-scrollbar-thumb:hover {
    background: #a0aec0;
}

.member-tag {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 8px 16px;
    background: #d1ecf1;
    color: #0c5460;
    border-radius: 20px;
    font-size: 13px;
    font-weight: 500;
    white-space: nowrap;
    flex-shrink: 0;
    transition: all 0.3s ease;
}

.member-tag:hover {
    background: #bee5eb;
    transform: translateY(-1px);
}

/* Member tag untuk +X */
.member-tag-more {
    background: #e0e0e0 !important;
    color: #666666 !important;
    cursor: pointer;
    font-weight: 500;
}

.member-tag-more:hover {
    background: #d0d0d0 !important;
    color: #444444 !important;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
}

/* ===========================
   MODAL STYLING
   =========================== */
.modal-members {
    display: none;
    position: fixed;
    z-index: 99999;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    align-items: center;
    justify-content: center;
    animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

.modal-members-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.6);
    backdrop-filter: blur(5px);
}

.modal-members-content {
    position: relative;
    background: white;
    border-radius: 16px;
    width: 90%;
    max-width: 700px;
    max-height: 80vh;
    display: flex;
    flex-direction: column;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    animation: slideUp 0.3s ease;
}

@keyframes slideUp {
    from {
        transform: translateY(50px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

.modal-members-header {
    padding: 24px 28px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 16px 16px 0 0;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.modal-members-header h3 {
    margin: 0;
    font-size: 20px;
    font-weight: 600;
}

.modal-close-btn {
    background: rgba(255, 255, 255, 0.2);
    border: none;
    color: white;
    width: 36px;
    height: 36px;
    border-radius: 50%;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
    font-size: 18px;
}

.modal-close-btn:hover {
    background: rgba(255, 255, 255, 0.3);
    transform: rotate(90deg);
}

.modal-members-body {
    padding: 24px;
    overflow-y: auto;
    max-height: calc(80vh - 100px);
}

/* Scrollbar styling */
.modal-members-body::-webkit-scrollbar {
    width: 8px;
}

.modal-members-body::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

.modal-members-body::-webkit-scrollbar-thumb {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 10px;
}

.modal-members-body::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
}

/* ===========================
   MEMBERS GRID IN MODAL
   =========================== */
.all-members-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 12px;
}

.member-item-modal {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 14px 16px;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: 10px;
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.member-item-modal:hover {
    background: linear-gradient(135deg, #fff 0%, #f8f9fa 100%);
    transform: translateX(5px);
    border-color: #667eea;
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.2);
}

.member-number {
    width: 32px;
    height: 32px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 13px;
    flex-shrink: 0;
}

.member-info {
    display: flex;
    align-items: center;
    gap: 10px;
    flex: 1;
}

.member-info i {
    color: #667eea;
    font-size: 20px;
}

.member-name {
    font-weight: 500;
    color: #333;
    font-size: 14px;
}

/* ===========================
   LOADING & ERROR STATES
   =========================== */
.loading-members,
.error-members,
.no-members-modal {
    text-align: center;
    padding: 40px 20px;
    color: #6c757d;
}

.loading-members i {
    font-size: 36px;
    color: #667eea;
    margin-bottom: 12px;
}

.error-members i {
    font-size: 36px;
    color: #dc3545;
    margin-bottom: 12px;
}

.no-members-modal i {
    font-size: 48px;
    color: #adb5bd;
    margin-bottom: 16px;
}

.no-members-modal p {
    font-size: 16px;
    color: #6c757d;
    margin: 0;
}

/* ===========================
   RESPONSIVE
   =========================== */
@media (max-width: 768px) {
    .modal-members-content {
        width: 95%;
        max-height: 90vh;
    }
    
    .all-members-grid {
        grid-template-columns: 1fr;
    }
    
    .modal-members-header {
        padding: 20px;
    }
    
    .modal-members-header h3 {
        font-size: 18px;
    }
    
    .modal-members-body {
        padding: 16px;
    }
}
</style>