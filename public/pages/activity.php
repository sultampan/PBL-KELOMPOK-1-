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

    /* --- CAROUSEL WRAPPER (Sama seperti Partner) --- */
    .activity-carousel-wrapper {
        position: relative;
        width: 100%;
        display: flex;
        align-items: center;
    }

    .activity-track {
        display: flex;
        gap: 25px; /* Jarak antar kartu */
        overflow-x: auto;
        scroll-behavior: smooth;
        padding: 15px 5px;
        width: 100%;
        -ms-overflow-style: none;  
        scrollbar-width: none;  
    }
    
    .activity-track::-webkit-scrollbar { display: none; }

    /* --- CARD STYLE (Diadaptasi agar masuk ke Carousel) --- */
    .activity-card {
        background: white;
        min-width: 320px; /* Lebar Fix agar rapi di carousel */
        max-width: 320px;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        cursor: pointer;
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
        height: 200px; /* Tinggi gambar fix */
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

    /* No Image Placeholder (Style Partner) */
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

    /* Overlay Title on Hover */
    .activity-title-overlay {
        position: absolute; inset: 0; background: rgba(0, 0, 0, 0.7);
        display: flex; align-items: center; justify-content: center;
        opacity: 0; transition: opacity 0.3s ease; padding: 20px;
    }
    .activity-card:hover .activity-title-overlay { opacity: 1; }
    
    .activity-title-hover {
        color: white; font-size: 1.1rem; font-weight: 600;
        text-align: center; line-height: 1.3;
    }

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
        /* Batasi 2 baris judul */
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
        /* Batasi 3 baris deskripsi */
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

    /* --- LIGHTBOX (Tetap ada) --- */
    .lightbox-modal {
        display: none; position: fixed; z-index: 9999; inset: 0;
        background-color: rgba(0, 0, 0, 0.95);
        align-items: center; justify-content: center; animation: fadeIn 0.3s ease;
    }
    .lightbox-modal.active { display: flex; }
    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
    .lightbox-content { position: relative; max-width: 90%; max-height: 90vh; }
    .lightbox-image {
        max-width: 100%; max-height: 90vh; object-fit: contain;
        border-radius: 8px; box-shadow: 0 10px 40px rgba(0, 0, 0, 0.5);
        animation: zoomIn 0.3s ease;
    }
    @keyframes zoomIn { from { transform: scale(0.8); opacity: 0; } to { transform: scale(1); opacity: 1; } }
    .lightbox-close {
        position: absolute; top: -40px; right: 0; color: white; font-size: 40px; font-weight: bold;
        cursor: pointer; background: none; border: none; padding: 0; line-height: 1;
    }
    .lightbox-close:hover { color: #ffb400; }
    .lightbox-info {
        position: fixed; bottom: 30px; left: 0; right: 0; color: white; text-align: center; padding: 10px;
    }
    .lightbox-title { font-size: 1.3rem; font-weight: 600; margin-bottom: 5px; }
    .lightbox-date { font-size: 0.9rem; color: #ccc; }
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
            // Mengambil semua activity, diurutkan tanggal terbaru
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
            // Kita inisialisasi array agar urutannya rapi
            $grouped = [
                'Research' => [],
                'Projects' => [],
                'Activity' => []
                // Hapus 'Other' jika tidak ada di ENUM
            ];

            // B. Masukkan Data ke Group
            if (count($allActivities) > 0) {
                foreach ($allActivities as $row) {
                    $cat = $row['kategori'];

                    // Cek apakah kategori dari database ada di daftar $grouped kita
                    if (array_key_exists($cat, $grouped)) {
                        $grouped[$cat][] = $row;
                    } 
                    // Jika data punya kategori yang tidak dikenali (bukan salah satu dari 3 di atas),
                    // data tersebut akan otomatis terabaikan/tidak tampil agar tidak merusak layout.
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
                                    <div class="activity-card" onclick="openLightbox('<?= $safe_image; ?>', '<?= $safe_title; ?>', '<?= $safe_date; ?>', '<?= $safe_desc; ?>')">
                                        
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
                                                    <span>Tidak ada gambar</span>
                                                </div>
                                            <?php endif; ?>

                                            <div class="activity-title-overlay">
                                                <div class="activity-title-hover">Lihat Detail</div>
                                            </div>
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
                                                    // Ambil 2 member pertama saja biar gak kepanjangan
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

<div class="lightbox-modal" id="lightboxModal">
    <div class="lightbox-content">
        <button class="lightbox-close" id="lightboxClose">&times;</button>
        <img src="" alt="" class="lightbox-image" id="lightboxImage">
        <div class="lightbox-info">
            <div class="lightbox-title" id="lightboxTitle"></div>
            <div class="lightbox-date" id="lightboxDate"></div>
        </div>
    </div>
</div>

<script>
// === 1. LOGIKA CAROUSEL SCROLL (Sama seperti Partner) ===
document.addEventListener("DOMContentLoaded", function() {
    const carousels = document.querySelectorAll('.activity-carousel-wrapper');

    carousels.forEach(wrapper => {
        const track = wrapper.querySelector('.activity-track');
        const prevBtn = wrapper.querySelector('.prev-btn');
        const nextBtn = wrapper.querySelector('.next-btn');
        const scrollAmount = 340; // Sesuaikan dengan lebar kartu + gap

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

// === 2. LOGIKA LIGHTBOX ===
function openLightbox(imageSrc, title, date, description) {
    if(!imageSrc) return; // Jika tidak ada gambar, jangan buka

    const modal = document.getElementById('lightboxModal');
    const image = document.getElementById('lightboxImage');
    const titleEl = document.getElementById('lightboxTitle');
    const dateEl = document.getElementById('lightboxDate');
    
    image.src = imageSrc;
    image.alt = title;
    titleEl.textContent = title;
    dateEl.textContent = date;
    
    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeLightbox() {
    const modal = document.getElementById('lightboxModal');
    modal.classList.remove('active');
    document.body.style.overflow = '';
}

document.getElementById('lightboxClose').addEventListener('click', closeLightbox);
document.getElementById('lightboxModal').addEventListener('click', function(e) {
    if (e.target.id === 'lightboxModal') closeLightbox();
});
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeLightbox();
});
</script>