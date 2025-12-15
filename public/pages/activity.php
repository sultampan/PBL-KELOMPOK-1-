<style>
    /* --- BANNER ACTIVITY --- */
    .inner-banner.activity-banner {
        background: url('assets/images/header-facility.jpeg') no-repeat center;
        background-size: cover;
        position: relative;
        z-index: 0;
        min-height: 350px;
        display: grid;
        align-items: center;
        justify-content: center;
        text-align: center;
    }

    .inner-banner.activity-banner:before {
        content: "";
        background: rgba(0, 0, 0, 0.6);
        position: absolute;
        inset: 0;
        z-index: -1;
    }

    .inner-banner.activity-banner h2 {
        font-size: 3rem;
        font-weight: 700;
        color: white;
        margin-bottom: 10px;
    }

    .breadcrumb-activity { color: white; font-size: 1rem; }
    .breadcrumb-activity a { color: #ffb400; text-decoration: none; }
    .breadcrumb-activity span { margin: 0 5px; }

    /* --- LAYOUT PER KATEGORI --- */
    .category-section {
        margin-bottom: 3rem;
        position: relative;
    }

    .category-title {
        font-size: 1.8rem;
        font-weight: 700;
        color: #02406C;
        margin-bottom: 1rem;
        padding-left: 10px;
        border-left: 5px solid #ffb400;
        display: inline-block;
    }

    /* --- CAROUSEL WRAPPER --- */
    .activity-carousel-wrapper {
        position: relative;
        width: 100%;
        display: flex;
        align-items: center;
    }

    .activity-track {
        display: flex;
        gap: 25px;
        overflow-x: auto;
        scroll-behavior: smooth;
        padding: 15px 5px;
        width: 100%;
        -ms-overflow-style: none;  
        scrollbar-width: none;  
    }
    
    .activity-track::-webkit-scrollbar { display: none; }

    /* --- CARD STYLE --- */
    .activity-card {
        background: white;
        min-width: 320px;
        max-width: 320px;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        display: flex;
        flex-direction: column;
        border: 1px solid #f0f0f0;
    }

    .activity-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
    }

    /* Image Wrapper */
    .activity-image-wrapper {
        position: relative;
        width: 100%;
        height: 200px;
        overflow: hidden;
        background-color: #e0e0e0;
    }

    .activity-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }

    .activity-card:hover .activity-image { transform: scale(1.1); }

    /* No Image Placeholder */
    .no-image-placeholder {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        width: 100%;
        height: 100%;
        background-color: #f4f4f4;
        color: #888;
    }
    .no-image-placeholder i { font-size: 40px; margin-bottom: 10px; opacity: 0.5; }
    .no-image-placeholder span { font-size: 14px; font-weight: 500; }

    /* Content Area */
    .activity-content {
        padding: 20px;
        display: flex;
        flex-direction: column;
        flex-grow: 1;
    }

    .activity-title-main {
        font-size: 1.1rem;
        font-weight: 700;
        color: #333;
        margin-bottom: 10px;
        line-height: 1.4;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .activity-date {
        color: #888; font-size: 0.85rem; margin-bottom: 10px;
        display: flex; align-items: center; gap: 5px;
    }

    .activity-description {
        color: #666; font-size: 0.9rem; line-height: 1.6; margin-bottom: 15px;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    /* Members Section */
    .activity-members {
        font-size: 0.85rem; color: #333; font-weight: 600;
        margin-top: auto; padding-top: 10px; border-top: 1px solid #eee;
    }
    .activity-members span { font-weight: normal; color: #555; }

    /* View More Button (Style mirip member) */
    .activity-footer {
        display: flex;
        justify-content: flex-end;
        padding: 0 20px 20px;
        margin-top: auto;
    }

    .view-more-btn {
        background: none;
        color: #01B5B8;
        border: none;
        padding: 0;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 5px;
        text-decoration: none;
    }

    .view-more-btn:hover {
        color: #008c8e;
        transform: translateX(3px);
    }

    .view-more-btn i {
        font-size: 10px;
        transition: transform 0.3s ease;
    }

    .view-more-btn:hover i {
        transform: translateX(3px);
    }

    /* --- NAVIGATION BUTTONS (ARROWS) --- */
    .nav-btn {
        position: absolute; top: 50%; transform: translateY(-50%);
        width: 45px; height: 45px;
        background-color: #fff; border: 1px solid #ddd; border-radius: 50%;
        color: #333; font-size: 20px;
        display: flex; align-items: center; justify-content: center;
        cursor: pointer; z-index: 5;
        box-shadow: 0 4px 10px rgba(0,0,0,0.15);
        transition: all 0.3s ease; opacity: 1;
    }
    .nav-btn:hover { background-color: #02406C; color: white; border-color: #02406C; }
    .nav-btn.hidden { opacity: 0; pointer-events: none; }
    .prev-btn { left: -20px; }
    .next-btn { right: -20px; }

    @media (max-width: 768px) {
        .nav-btn { display: none !important; }
        .activity-track { padding-right: 20px; }
    }

    .no-data { text-align: center; color: #888; padding: 20px; width: 100%; }
</style>

<section class="inner-banner activity-banner">
    <div>
        <h2>Activity</h2>
        <div class="breadcrumb-activity">
            <a href="index.php">Home</a>
            <span>›</span>
            <span>Activity</span>
        </div>
    </div>
</section>

<section class="py-5 activity-section-bg">
    <div class="container py-md-5 py-3">
        <h3 class="title-w3l mb-5 text-center">Our Activities</h3>

        <?php
        /* === 1. KONEKSI DATABASE === */
        $koneksiPath = dirname(dirname(__DIR__)) . '/config/koneksi.php';
        if (file_exists($koneksiPath)) {
            require_once $koneksiPath;
        } else {
            echo '<p class="no-data">Config DB tidak ditemukan.</p>';
            exit;
        }

        if (!isset($pdo)) {
            echo '<p class="no-data">Koneksi database gagal.</p>';
            exit;
        }

        try {
            /* === 2. QUERY SEMUA ACTIVITY === */
            $query = "
                SELECT 
                    a.id_activity, a.judul, a.deskripsi, a.tanggal_kegiatan, a.gambar, a.kategori,
                    STRING_AGG(m.nama_member, ', ') AS members
                FROM activity a
                LEFT JOIN activity_member am ON a.id_activity = am.id_activity
                LEFT JOIN member m ON am.id_member = m.id_member
                GROUP BY a.id_activity
                ORDER BY a.tanggal_kegiatan DESC
            ";

            $stmt = $pdo->prepare($query);
            $stmt->execute();
            $allActivities = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // === 3. PENGELOMPOKAN DATA BERDASARKAN KATEGORI ===
            $grouped = [
                'Research' => [],
                'Projects' => [],
                'Activity' => []
            ];

            if (count($allActivities) > 0) {
                foreach ($allActivities as $row) {
                    $cat = $row['kategori'];
                    if (array_key_exists($cat, $grouped)) {
                        $grouped[$cat][] = $row;
                    }
                }
            }

            // === 4. LOOPING TAMPILKAN PER KATEGORI ===
            $hasData = false;
            foreach ($grouped as $kategoriName => $listActivity) {
                if (!empty($listActivity)) {
                    $hasData = true;
                    ?>
                    
                    <div class="category-section">
                        <h4 class="category-title"><?= htmlspecialchars($kategoriName); ?></h4>
                        
                        <div class="activity-carousel-wrapper">
                            <button class="nav-btn prev-btn hidden"><i class="fas fa-chevron-left"></i></button>

                            <div class="activity-track">
                                <?php foreach ($listActivity as $row): 
                                    // Proses Gambar
                                    $image_path = $row['gambar'];
                                    $hasImage = !empty($image_path);
                                    if ($hasImage && strpos($image_path, '/') === false) {
                                        $image_path = 'uploads/activity/' . $image_path;
                                    }

                                    // Format Data
                                    $date = date_create($row['tanggal_kegiatan']);
                                    $formatted_date = date_format($date, 'd F Y');
                                    
                                    $safe_image = $hasImage ? htmlspecialchars($image_path, ENT_QUOTES) : '';
                                    $safe_title = htmlspecialchars($row['judul'], ENT_QUOTES);
                                    $safe_desc  = htmlspecialchars($row['deskripsi'], ENT_QUOTES);
                                    $safe_date  = htmlspecialchars($formatted_date, ENT_QUOTES);
                                ?>
                                    <div class="activity-card">
                                        <div class="activity-image-wrapper">
                                            <?php if ($hasImage): ?>
                                                <img src="<?= htmlspecialchars($image_path); ?>" 
                                                     alt="<?= htmlspecialchars($row['judul']); ?>" 
                                                     class="activity-image"
                                                     loading="lazy"
                                                     onerror="this.parentElement.innerHTML='<div class=\'no-image-placeholder\'><i class=\'fas fa-image\'></i><span>Error Image</span></div>';">
                                            <?php else: ?>
                                                <div class="no-image-placeholder">
                                                    <i class="fas fa-image"></i>
                                                    <span>No Image</span>
                                                </div>
                                            <?php endif; ?>
                                        </div>

                                        <div class="activity-content">
                                                <div class="activity-title-main">
                                                    <?= htmlspecialchars($row['judul']); ?>
                                                </div>

                                                <div class="activity-date">
                                                    <i class="far fa-calendar-alt"></i> <?= $formatted_date; ?>
                                                </div>

                                                <div class="activity-description">
                                                    <?= mb_strimwidth(htmlspecialchars($row['deskripsi']), 0, 100, "..."); ?>
                                                </div>

                                                <div class="activity-members">
                                                    Member: 
                                                    <?php 
                                                    if (!empty($row['members'])) {
                                                        $members_array = explode(',', $row['members']);
                                                        $display_members = array_slice($members_array, 0, 2);
                                                        echo '<span>' . htmlspecialchars(implode(', ', $display_members));
                                                        if(count($members_array) > 2) echo ', ...';
                                                        echo '</span>';
                                                    } else {
                                                        echo '<span>-</span>';
                                                    }
                                                    ?>
                                                </div>
                                            </div>
                                        
                                        <div class="activity-footer">
                                            <a href="index.php?page=activity-detail&id=<?= $row['id_activity']; ?>" class="view-more-btn">
                                                View More <i class="fas fa-arrow-right"></i>
                                            </a>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <button class="nav-btn next-btn hidden"><i class="fas fa-chevron-right"></i></button>
                        </div>
                    </div>
                    <?php
                }
            }

            if (!$hasData) {
                echo '<div class="no-data">Belum ada aktivitas yang tersedia saat ini.</div>';
            }

        } catch (PDOException $e) {
            echo '<div class="no-data">Terjadi kesalahan sistem.</div>';
        }
        ?>

    </div>
</section>

<script>
// === LOGIKA CAROUSEL SCROLL ===
document.addEventListener("DOMContentLoaded", function() {
    const carousels = document.querySelectorAll('.activity-carousel-wrapper');

    carousels.forEach(wrapper => {
        const track = wrapper.querySelector('.activity-track');
        const prevBtn = wrapper.querySelector('.prev-btn');
        const nextBtn = wrapper.querySelector('.next-btn');
        const scrollAmount = 340;

        const checkArrows = () => {
            const maxScrollLeft = track.scrollWidth - track.clientWidth - 1;
            
            if (track.scrollLeft <= 0) {
                prevBtn.classList.add('hidden');
            } else {
                prevBtn.classList.remove('hidden');
            }

            if (track.scrollWidth <= track.clientWidth || track.scrollLeft >= maxScrollLeft) {
                nextBtn.classList.add('hidden');
            } else {
                nextBtn.classList.remove('hidden');
            }
        };

        prevBtn.addEventListener('click', () => {
            track.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
        });

        nextBtn.addEventListener('click', () => {
            track.scrollBy({ left: scrollAmount, behavior: 'smooth' });
        });

        track.addEventListener('scroll', checkArrows);
        checkArrows();
        window.addEventListener('resize', checkArrows);
    });
});
</script>