<style>
    /* --- BANNER PARTNER (Mirip Activity tapi beda gambar) --- */
    .inner-banner.partner-banner {
        /* Ganti dengan gambar header yang relevan atau gunakan warna solid */
        background: url('assets/images/header-partner.jpg') no-repeat center;
        background-color: #02406C; /* Fallback color */
        background-size: cover;
        position: relative;
        z-index: 0;
        min-height: 350px;
        display: grid;
        align-items: center;
        justify-content: center;
        text-align: center;
    }

    /* Overlay Gelap */
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

    /* Breadcrumb */
    .breadcrumb-partner {
        color: white;
        font-size: 1rem;
    }
    .breadcrumb-partner a {
        color: #ffb400;
        text-decoration: none;
    }
    .breadcrumb-partner span {
        margin: 0 5px;
    }

    /* --- SECTION PARTNER --- */
    .category-section {
        margin-bottom: 3rem;
    }

    .category-title {
        font-size: 1.8rem;
        font-weight: 700;
        color: #02406C; /* Warna Corporate */
        margin-bottom: 1.5rem;
        padding-bottom: 10px;
        border-bottom: 3px solid #f0f0f0;
        display: inline-block;
        padding-right: 20px;
    }

    .partner-grid {
        display: grid;
        /* Grid lebih kecil daripada activity karena ini logo */
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 25px;
    }

    .partner-card {
        background: white;
        border: 1px solid #eaeaea;
        border-radius: 12px;
        padding: 20px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        transition: all 0.3s ease;
        height: 100%;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    }

    .partner-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        border-color: #01B5B8;
    }

    /* Wrapper Gambar Logo - Agar logo presisi */
    .partner-logo-wrapper {
        width: 100%;
        height: 120px; /* Tinggi area logo */
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 15px;
        overflow: hidden;
    }

    .partner-logo {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain; /* PENTING: Agar logo tidak terpotong */
        filter: grayscale(100%); /* Efek hitam putih agar rapi */
        transition: filter 0.3s ease, transform 0.3s ease;
    }

    .partner-card:hover .partner-logo {
        filter: grayscale(0%); /* Berwarna saat dihover */
        transform: scale(1.1);
    }

    .partner-name {
        font-size: 1rem;
        font-weight: 600;
        color: #333;
        line-height: 1.4;
    }

    .no-data {
        text-align: center;
        color: #888;
        padding: 20px;
        width: 100%;
    }

    /* Responsif */
    @media (max-width: 768px) {
        .inner-banner.partner-banner h2 { font-size: 2rem; }
        .partner-grid { grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width: 480px) {
        .partner-grid { grid-template-columns: 1fr; }
    }
</style>

<section class="inner-banner partner-banner">
    <div>
        <h2>Our Partners</h2>
        <div class="breadcrumb-partner">
            <a href="index.php">Home</a>
            <span>›</span>
            <span>Partners</span>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container py-md-5 py-3">
        
        <div class="text-center mb-5">
            <h3 class="title-w3l mb-3">Collaboration & Network</h3>
            <p class="mx-auto" style="max-width: 700px; color: #666;">
                Kami bekerja sama dengan berbagai institusi industri, pendidikan, dan pemerintahan 
                untuk menciptakan inovasi dan peluang riset yang berdampak luas.
            </p>
        </div>

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
            /* === 2. AMBIL SEMUA PARTNER === */
            // Urutkan berdasarkan Nama
            $stmt = $pdo->prepare("SELECT * FROM partner ORDER BY nama ASC");
            $stmt->execute();
            $allPartners = $stmt->fetchAll(PDO::FETCH_ASSOC);

            /* === 3. GROUPING BERDASARKAN KATEGORI === */
            // Kita siapkan array kosong untuk menampung
            $grouped = [
                'Industry Partner' => [],
                'Educational Institutions' => [],
                'Government Institutions' => [],
                'International Institutions' => []
            ];

            // Masukkan data ke kotak masing-masing
            if (count($allPartners) > 0) {
                foreach ($allPartners as $row) {
                    $cat = $row['kategori'];
                    // Cek jika kategorinya valid ada di array kita, kalau tidak masukkan ke others (opsional)
                    if (array_key_exists($cat, $grouped)) {
                        $grouped[$cat][] = $row;
                    } else {
                        // Jika ada kategori custom di luar enum (jaga-jaga)
                        $grouped[$cat][] = $row; 
                    }
                }
            }

            /* === 4. TAMPILKAN PER SECTION === */
            // Cek apakah ada data sama sekali
            if (count($allPartners) > 0) {
                
                // Loop setiap kategori yang sudah kita definisikan di array $grouped
                foreach ($grouped as $kategoriName => $partnersList) {
                    
                    // HANYA TAMPILKAN JIKA ADA ISINYA
                    if (!empty($partnersList)) {
                        ?>
                        <div class="category-section">
                            <h4 class="category-title"><?= htmlspecialchars($kategoriName); ?></h4>
                            
                            <div class="partner-grid">
                                <?php foreach ($partnersList as $p): ?>
                                    <?php 
                                        $img = $p['gambar'];
                                        // Fix path jika belum ada folder uploads/
                                        if (strpos($img, '/') === false) {
                                            $img = 'uploads/partner/' . $img;
                                        }
                                    ?>
                                    <div class="partner-card">
                                        <div class="partner-logo-wrapper">
                                            <img src="<?= htmlspecialchars($img); ?>" 
                                                 alt="<?= htmlspecialchars($p['nama']); ?>" 
                                                 class="partner-logo"
                                                 loading="lazy"
                                                 onerror="this.src='assets/images/no-logo.png'; this.style.filter='none';">
                                        </div>
                                        <div class="partner-name">
                                            <?= htmlspecialchars($p['nama']); ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php
                    } // End if !empty
                } // End Foreach Grouped

            } else {
                echo '<div class="no-data">Belum ada data partner saat ini.</div>';
            }

        } catch (PDOException $e) {
            echo '<div class="no-data">Terjadi kesalahan database: ' . $e->getMessage() . '</div>';
        }
        ?>

    </div>
</section>