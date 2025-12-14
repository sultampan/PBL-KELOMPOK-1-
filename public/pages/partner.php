<style>
    /* --- BANNER HEADER --- */
    .inner-banner.partner-banner {
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

    .inner-banner.partner-banner:before {
        content: "";
        background: rgba(0, 0, 0, 0.6);
        position: absolute;
        inset: 0;
        z-index: -1;
    }

    .inner-banner.partner-banner h2 {
        font-size: 3rem;
        font-weight: 700;
        color: white;
        margin-bottom: 10px;
    }

    .breadcrumb-partner { color: white; font-size: 1rem; }
    .breadcrumb-partner a { color: #ffb400; text-decoration: none; }
    .breadcrumb-partner span { margin: 0 5px; }

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
    .partner-carousel-wrapper {
        position: relative;
        width: 100%;
        display: flex;
        align-items: center;
    }

    .partner-track {
        display: flex;
        gap: 25px; 
        overflow-x: auto;
        scroll-behavior: smooth;
        padding: 15px 5px;
        width: 100%;
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
    
    .partner-track::-webkit-scrollbar { display: none; }

    /* --- CARD STYLE --- */
    .partner-card {
        background: white;
        min-width: 250px; 
        max-width: 250px;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border: 1px solid #f0f0f0;
        display: flex;
        flex-direction: column;
    }

    .partner-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
        border-color: #02406C;
    }

    /* Image Wrapper untuk Logo */
    .partner-logo-wrapper {
        width: 100%;
        height: 180px; 
        background-color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
        border-bottom: 1px solid #eee;
    }

    .partner-logo {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain; 
        transition: transform 0.3s ease;
    }

    .partner-card:hover .partner-logo { transform: scale(1.1); }

    .partner-content {
        padding: 15px;
        text-align: center;
        background: #fafafa;
        flex-grow: 1;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .partner-name {
        font-size: 1rem;
        font-weight: 700;
        color: #333;
        line-height: 1.3;
    }

    .no-logo-placeholder {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        color: #ccc;
    }
    .no-logo-placeholder i { font-size: 40px; margin-bottom: 5px; }

    /* --- NAVIGATION BUTTONS --- */
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
        .partner-track { padding-right: 20px; }
    }

    .no-data { text-align: center; color: #888; padding: 20px; width: 100%; }
</style>

<section class="inner-banner partner-banner">
    <div>
        <h2>Partner</h2>
        <div class="breadcrumb-partner">
            <a href="index.php">Home</a>
            <span>›</span>
            <span>Partner</span>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container py-md-5 py-3">
        <h3 class="title-w3l mb-5 text-center">Collaboration & Network</h3>

        <?php
        /* === 1. KONEKSI DATABASE === */
        $koneksiPath = dirname(dirname(__DIR__)) . '/config/koneksi.php';
        if (file_exists($koneksiPath)) {
            require_once $koneksiPath;
        } else {
            echo '<p class="no-data">Config DB tidak ditemukan.</p>';
            exit;
        }

        try {
            /* === 2. QUERY SEMUA PARTNER === */
            $stmt = $pdo->prepare("SELECT * FROM partner ORDER BY id_partner DESC");
            $stmt->execute();
            $allPartners = $stmt->fetchAll(PDO::FETCH_ASSOC);

            /* === 3. GROUPING DATA === */
            $grouped = [
                'Industry Partner'           => [],
                'Educational Institutions'   => [],
                'Government Institutions'    => [],
                'International Institutions' => [],
                'Other'                      => [] 
            ];

            if (count($allPartners) > 0) {
                foreach ($allPartners as $row) {
                    $cat = $row['kategori'];
                    if (array_key_exists($cat, $grouped)) {
                        $grouped[$cat][] = $row;
                    } else {
                        $grouped['Other'][] = $row;
                    }
                }
            }

            /* === 4. TAMPILKAN PER KATEGORI === */
            $hasData = false;
            foreach ($grouped as $kategoriName => $listPartner) {
                if (!empty($listPartner)) {
                    $hasData = true;
                    ?>
                    
                    <div class="category-section">
                        <h4 class="category-title"><?= htmlspecialchars($kategoriName); ?></h4>
                        
                        <div class="partner-carousel-wrapper">
                            <button class="nav-btn prev-btn hidden"><i class="fas fa-chevron-left"></i></button>

                            <div class="partner-track">
                                <?php foreach ($listPartner as $p): 
                                    // === LOGIKA THUMBNAIL ===
                                    $rawImg = $p['gambar'];
                                    $hasImage = false;
                                    $thumbPath = '';
                                    $originalPath = '';

                                    if (!empty($rawImg)) {
                                        $hasImage = true;
                                        
                                        // 1. Siapkan Path Asli (untuk backup/onerror)
                                        $originalPath = 'uploads/partner/' . $rawImg;

                                        // 2. Siapkan Path Thumbnail
                                        // Ambil nama file tanpa ekstensi dan ekstensinya
                                        $info = pathinfo($rawImg);
                                        $ext = isset($info['extension']) ? '.' . $info['extension'] : '';
                                        $filename = $info['filename'];

                                        // Format: namafile-thumb.jpg
                                        // Path sesuai folder upload Anda
                                        $thumbName = $filename . '-thumb' . $ext;
                                        $thumbPath = 'uploads/thumb/partner-thumb/' . $thumbName;
                                    }
                                ?>
                                    <div class="partner-card">
                                        <div class="partner-logo-wrapper">
                                            <?php if ($hasImage): ?>
                                                <img src="<?= htmlspecialchars($thumbPath); ?>" 
                                                     alt="<?= htmlspecialchars($p['nama']); ?>" 
                                                     class="partner-logo"
                                                     loading="lazy"
                                                     onerror="this.onerror=null; this.src='<?= htmlspecialchars($originalPath); ?>';">
                                            <?php else: ?>
                                                <div class="no-logo-placeholder">
                                                    <i class="fas fa-image"></i>
                                                    <span>No Logo</span>
                                                </div>
                                            <?php endif; ?>
                                        </div>

                                        <div class="partner-content">
                                            <div class="partner-name">
                                                <?= htmlspecialchars($p['nama']); ?>
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
                echo '<div class="no-data">Belum ada data partner saat ini.</div>';
            }

        } catch (PDOException $e) {
            echo '<div class="no-data">Terjadi kesalahan sistem.</div>';
        }
        ?>

    </div>
</section>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const carousels = document.querySelectorAll('.partner-carousel-wrapper');

    carousels.forEach(wrapper => {
        const track = wrapper.querySelector('.partner-track');
        const prevBtn = wrapper.querySelector('.prev-btn');
        const nextBtn = wrapper.querySelector('.next-btn');
        const scrollAmount = 275; 

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

        if(prevBtn && nextBtn) {
            prevBtn.addEventListener('click', () => {
                track.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
            });

            nextBtn.addEventListener('click', () => {
                track.scrollBy({ left: scrollAmount, behavior: 'smooth' });
            });
        }

        track.addEventListener('scroll', checkArrows);
        checkArrows();
        window.addEventListener('resize', checkArrows);
        setTimeout(checkArrows, 500); 
    });
});
</script>