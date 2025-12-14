<section class="w3l-main-slider banner-slider" id="home">
        <div class="owl-one owl-carousel owl-theme">
            <div class="item">
                <div class="slider-info banner-view banner-top1">
                    <div class="container">
                        <div class="banner-info header-hero-19">
                            <h3 class="title-hero-19">Laboratory for Applied Informatics</h3>
                            <p class="w3ban-para">The Applied Informatics Laboratory at Malang State Polytechnic is an innovation center focused on developing information technology-based solutions. Through collaboration, the Applied Informatics Laboratory continuously strives to deliver relevant and useful technology. </p>

                            <a href="index.php?page=about"
                                class="btn btn-style btn-primary mt-sm-5 mt-4">
                                Read More <i class="fas fa-angle-double-right ms-2"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <?php
    // Pastikan path koneksi benar
    require_once __DIR__ . "/../../config/koneksi.php";

    // Ambil data produk
    $stmt = $pdo->prepare("SELECT * FROM produk ORDER BY id_produk ASC LIMIT 3");
    $stmt->execute();
    $produk = $stmt->fetchAll(PDO::FETCH_ASSOC);
    ?>

    <style>
        .box-wrap {
            height: 460px;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            padding: 30px;
            border-radius: 12px;
            background: #f7f7f7;
        }
    </style>

    <section class="w3l-features py-5" id="work">
        <div class="container py-lg-5 py-md-4 py-2">

            <div class="title-content text-center mb-lg-3 mb-4">
                <h6 class="title-subw3hny mb-1">Our Products</h6>
                <h3 class="title-w3l">Innovative Solutions Developed by the Applied Informatics Laboratory</h3>
            </div>

            <div class="main-cont-wthree-2">
                <div class="row justify-content-center">
                    <?php if(count($produk) > 0): ?>
                        <?php foreach ($produk as $p): ?>
                            <div class="col-lg-4 col-md-6 mt-lg-5 mt-4">
                                <div class="grids-1 box-wrap">
                                    <div class="icon">
                                        <?php
                                        $gambar = $p['gambar'];
                                        $ext = pathinfo($gambar, PATHINFO_EXTENSION);
                                        $base = pathinfo($gambar, PATHINFO_FILENAME);
                                        $thumbName = $base . '-thumb.' . $ext;
                                        $srcThumb = "uploads/thumb/produk-thumb/" . $thumbName;
                                        $srcAsli  = "uploads/produk/" . $gambar;
                                        ?>
                                        <img src="<?php echo htmlspecialchars($srcThumb); ?>"
                                            alt="<?php echo htmlspecialchars($p['nama']); ?>"
                                            style="width: 180px; height: 120px; object-fit: contain; border-radius: 8px;"
                                            onerror="this.onerror=null; this.src='<?php echo htmlspecialchars($srcAsli); ?>';">
                                    </div>
                                    <h4>
                                        <a href="<?php echo htmlspecialchars($p['link_produk']); ?>" class="title-head mb-3" target="_blank">
                                            <?php echo htmlspecialchars($p['nama']); ?>
                                        </a>
                                    </h4>
                                    <p class="text-para">
                                        <?php echo htmlspecialchars($p['deskripsi']); ?>
                                    </p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-12 text-center"><p>Belum ada data produk.</p></div>
                    <?php endif; ?>
                </div>
                <div class="text-center mt-5">
                    <a href="index.php?page=product" class="btn btn-style btn-primary mt-lg-5 mt-4 me-2">
                        Read More <i class="fas fa-angle-double-right ms-2"></i>
                    </a>
                </div>
            </div>
        </div>
    </section>


<?php
    $activities = [];
    try {
        // Query: Kategori Research, Urutkan ID (Terbaru), Limit 3
        $query = "SELECT id_activity, judul, deskripsi, gambar FROM activity WHERE kategori = 'Research' ORDER BY id_activity DESC LIMIT 3";
        $stmt = $pdo->prepare($query);
        $stmt->execute();
        $activities = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        // Silent error
    }
    ?>

    <section class="w3l-gallery" id="gallery">
        <div class="destionation-innf py-5">
            <div class="container py-lg-5 py-md-4 py-2 HomePageGallery">
                <div class="title-content text-center">
                    <h6 class="title-subw3hny text-center">Laboratory Activities</h6>
                    <h3 class="title-w3l mb-5 text-center">Latest Research</h3>
                </div>
                
                <ul class="gallery_agile">
                    <?php
                    if (count($activities) > 0) {
                        foreach ($activities as $activity) {
                            $title = htmlspecialchars($activity['judul']);
                            
                            // Logika Gambar
                            $hasImage = false;
                            $displayImage = "";

                            if (!empty($activity['gambar'])) {
                                $hasImage = true;
                                $imagePath = "uploads/activity/" . $activity['gambar'];
                                
                                // Cek Thumbnail
                                $baseName = pathinfo($activity['gambar'], PATHINFO_FILENAME);
                                $ext = pathinfo($activity['gambar'], PATHINFO_EXTENSION);
                                $thumbPath = "uploads/thumb/activity-thumb/" . $baseName . "-thumb." . $ext;
                                
                                if(file_exists(__DIR__ . '/../../public/' . $thumbPath)) {
                                    $displayImage = $thumbPath;
                                } else {
                                    $displayImage = $imagePath;
                                }
                            }
                    ?>
                            <li class="research-item-li">
                                <div class="w3_agile_portfolio_grid">
                                    <a href="javascript:void(0);" class="gallery-item" 
                                       data-image="<?= $hasImage ? htmlspecialchars($imagePath) : '' ?>" 
                                       data-title="<?= $title ?>">
                                        
                                        <div class="image-wrapper-research">
                                            <?php if ($hasImage): ?>
                                                <img src="<?= htmlspecialchars($displayImage) ?>" 
                                                     alt="<?= $title ?>" 
                                                     class="img-fluid radius-image" />
                                            <?php else: ?>
                                                <div class="no-image-placeholder radius-image" style="height: 100%; border-radius: 0;">
                                                    <i class="fas fa-image"></i>
                                                    <span>Tidak ada gambar</span>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <div class="w3layouts_news_grid_pos">
                                            <div class="wthree_text">
                                                <h3><?= $title ?></h3>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </li>
                    <?php
                        }
                    } else {
                        echo '<li style="width:100%;"><p class="text-center">Belum ada riset terbaru.</p></li>';
                    }
                    ?>
                </ul>
            </div>
        </div>
    </section>    
    <div id="imageModal" class="modal-gallery" style="display:none;">
        <span class="close-modal">&times;</span>
        <img class="modal-content-gallery" id="modalImage">
        <div id="modalCaption"></div>
    </div>
    
    <style>
        .modal-gallery { display: none; position: fixed; z-index: 9999; padding-top: 50px; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0, 0, 0, 0.9); }
        .modal-content-gallery { margin: auto; display: block; max-width: 90%; max-height: 80%; animation: zoom 0.3s; }
        @keyframes zoom { from { transform: scale(0.8) } to { transform: scale(1) } }
        .close-modal { position: absolute; top: 15px; right: 35px; color: #f1f1f1; font-size: 40px; font-weight: bold; transition: 0.3s; cursor: pointer; z-index: 10000; }
        #modalCaption { margin: auto; display: block; width: 80%; max-width: 700px; text-align: center; color: #ccc; padding: 10px 0; font-size: 18px; }
        /* CSS RESEARCH (SERAGAM) */
    
    /* 1. Wrapper Li agar lebar konsisten */
    .research-item-li {
        width: 350px; 
        flex-grow: 1;
        max-width: 400px;
        margin-bottom: 20px;
    }

    /* 2. Wrapper Pembungkus (Agar tinggi sama rata) */
    .image-wrapper-research {
        width: 100%;
        height: 260px; /* Tinggi Fix */
        overflow: hidden;
        border-radius: 8px; /* Radius sudut */
    }

    /* Style untuk Gambar Asli */
    .image-wrapper-research img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }

    /* Efek Zoom saat Hover (Berlaku juga buat yang gada gambar kalau mau) */
    .w3_agile_portfolio_grid:hover .image-wrapper-research img {
        transform: scale(1.1);
    }
    
    /* Grid System */
    .gallery_agile {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        justify-content: center;
        padding: 0;
        list-style: none;
    }                    
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var modal = document.getElementById('imageModal');
            var modalImg = document.getElementById('modalImage');
            var captionText = document.getElementById('modalCaption');
            var closeBtn = document.getElementsByClassName('close-modal')[0];
            document.querySelectorAll('.gallery-item').forEach(function(item) {
                item.addEventListener('click', function(e) {
                    e.preventDefault(); modal.style.display = 'block'; modalImg.src = this.getAttribute('data-image'); captionText.innerHTML = this.getAttribute('data-title');
                });
            });
            closeBtn.addEventListener('click', function() { modal.style.display = 'none'; });
            modal.addEventListener('click', function(e) { if (e.target === modal) modal.style.display = 'none'; });
        });
    </script>

<?php
    // Variabel Default
    $projTitle = "Our Project";
    $projDesc  = "Our lab showcases a diverse range of applied AI projects designed to solve real-world problems and support innovation across multiple domains.";
    
    // Defaultnya anggap TIDAK ADA gambar
    $projImage = ""; 
    $hasProjImage = false; 

    try {
        // QUERY: Ambil 1 activity terbaru kategori 'Projects'
        $stmtProj = $pdo->prepare("SELECT * FROM activity WHERE kategori = 'Projects' ORDER BY id_activity DESC LIMIT 1");
        $stmtProj->execute();
        $latestProject = $stmtProj->fetch(PDO::FETCH_ASSOC);

        if ($latestProject) {
            $projTitle = htmlspecialchars($latestProject['judul']);
            $descRaw   = $latestProject['deskripsi'];
            $projDesc  = mb_strimwidth(htmlspecialchars($descRaw), 0, 500, "..."); // Biarkan panjang, nanti CSS yang potong

            // Cek apakah kolom gambar terisi
            if (!empty($latestProject['gambar'])) {
                $checkPath = "uploads/activity/" . $latestProject['gambar'];
                
                // Opsional: Cek file fisik ada atau tidak (kalau mau lebih strict)
                // if (file_exists($checkPath)) { ... }
                
                $projImage = $checkPath;
                $hasProjImage = true;
            }
        }
    } catch (Exception $e) {
        // Silent error
    }
    ?>

    <section class="w3l-passion-mid-sec py-5">
        <div class="container py-md-5 py-3">
            <div class="container">
                <div class="row w3l-passion-mid-grids">
                    
                    <div class="col-lg-6 passion-grid-item-info pe-lg-5 mb-lg-0 mb-5">
                        <h6 class="title-subw3hny mb-1">Our latest project</h6>
                        
                        <h3 class="title-w3l mb-4 latest-project-title" title="<?= htmlspecialchars($projTitle) ?>">
                            <?= $projTitle ?>
                        </h3>
                        
                        <p class="mt-3 pe-lg-5 latest-project-desc">
                            <?= $projDesc ?>
                        </p>
                        
                        <div class="w3banner-content-btns">
                            <a href="index.php?page=activity" class="btn btn-style btn-primary mt-lg-5 mt-4 me-2">
                                Read More <i class="fas fa-angle-double-right ms-2"></i>
                            </a>
                        </div>
                    </div>
                    
                    <div class="col-lg-6 passion-grid-item-info">
                        <?php if ($hasProjImage): ?>
                            <img src="<?= htmlspecialchars($projImage) ?>" 
                                 alt="<?= htmlspecialchars($projTitle) ?>" 
                                 class="img-fluid radius-image project-image-style">
                        <?php else: ?>
                            <div class="no-image-placeholder radius-image">
                                <i class="fas fa-image"></i>
                                <span>Tidak ada gambar</span>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                </div>
            </div>
        </div>
    </section>

    <style>
        /* Style Gambar Asli */
        .project-image-style {
            width: 100%; 
            height: 300px; 
            object-fit: cover; 
            border-radius: 10px; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        /* Style Kotak "Tidak Ada Gambar" */
        /* Style Kotak "Tidak Ada Gambar" (Mirip Screenshot) */
    .no-image-placeholder {
        width: 100%;
        height: 300px; /* Tinggi disamakan dengan gambar project */
        
        /* Background abu-abu muda solid */
        background-color: #f1f5f9; 
        
        border-radius: 10px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        
        /* Warna Ikon dan Teks (Abu-abu agak gelap) */
        color: #94a3b8; 
        
        font-weight: 500;
        font-size: 16px;
        
        /* Hapus border dashed yang tadi, biar bersih */
        border: none; 
    }
    
    /* Ukuran ikon diperbesar sedikit biar proporsional */
    .no-image-placeholder i {
        font-size: 60px;
        margin-bottom: 15px;
        opacity: 0.8;
    }
        
        /* CSS Line Clamp (Pembatas Teks) yang tadi */
        .latest-project-title {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .latest-project-desc {
            display: -webkit-box;
            -webkit-line-clamp: 5;
            line-clamp: 5;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
            text-align: justify;
        }
    </style>
<?php
    // Ambil data Partner dari Database
    $partners = [];
    try {
        $stmtPartner = $pdo->prepare("SELECT * FROM partner ORDER BY id_partner DESC");
        $stmtPartner->execute();
        $partners = $stmtPartner->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        // Silent error
    }
    ?>

<?php
    // Ambil data Partner dari Database
    $partners = [];
    try {
        // [UBAH DISINI] Menggunakan ORDER BY RANDOM() untuk PostgreSQL
        // Jika pakai MySQL, ganti RANDOM() menjadi RAND()
        $stmtPartner = $pdo->prepare("SELECT * FROM partner ORDER BY RANDOM()");
        $stmtPartner->execute();
        $partners = $stmtPartner->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        // Silent error
    }
    ?>

    <section class="w3l-testimonials" id="testimonials">
        <div class="cusrtomer-layout py-5">
            <div class="container py-lg-4 py-md-3 py-2 pb-lg-0">
                <div class="title-content text-center">
                    <h6 class="title-subw3hny">Our Partners</h6>
                    <h3 class="title-w3l mb-5">Collaborative Partners</h3>
                </div>

                <div class="partners-carousel-wrapper pt-lg-4">
                    <div class="carousel-partners-container">
                        <button class="carousel-btn-partner btn-prev" id="prevBtnPartner">‹</button>

                        <div class="carousel-track-wrapper">
                            <div class="carousel-track-partner" id="carouselTrackPartner">
                                
                                <?php if(count($partners) > 0): ?>
                                    <?php foreach($partners as $ptr): ?>
                                        <?php 
                                            // 1. Ambil nama file gambar
                                            $logo = $ptr['gambar'];
                                            
                                            // 2. Default value
                                            $pathLogo = "";
                                            $thumbLogo = "";
                                            $hasImage = false;

                                            // 3. Cek apakah gambar ada isinya
                                            if (!empty($logo)) {
                                                $hasImage = true;
                                                $pathLogo = "uploads/partner/" . $logo;
                                                
                                                // Proses pathinfo
                                                $info = pathinfo($logo);
                                                $ext = isset($info['extension']) ? $info['extension'] : 'jpg';
                                                $filename = $info['filename'];
                                                
                                                $thumbLogo = "uploads/thumb/partner-thumb/" . $filename . "-thumb." . $ext;
                                            }
                                        ?>
                                        
                                        <div class="logo-card-partner">
                                            <?php if ($hasImage): ?>
                                                <img src="<?= htmlspecialchars($thumbLogo) ?>" 
                                                     alt="<?= htmlspecialchars($ptr['nama']) ?>"
                                                     loading="lazy"
                                                     onerror="this.onerror=null; this.src='<?= htmlspecialchars($pathLogo) ?>';">
                                            <?php else: ?>
                                                <div class="partner-name-text" title="<?= htmlspecialchars($ptr['nama']) ?>">
                                                    <?= htmlspecialchars($ptr['nama']) ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>

                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="logo-card-partner" style="width:100%; text-align:center;">
                                        <span>Belum ada partner</span>
                                    </div>
                                <?php endif; ?>

                            </div>
                        </div>

                        <button class="carousel-btn-partner btn-next" id="nextBtnPartner">›</button>
                    </div>
                </div>

            </div>
        </div>
    </section>

<style>
    /* WRAPPER UTAMA */
    .partners-carousel-wrapper {
        position: relative;
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 20px;
    }

    .carousel-partners-container {
        position: relative;
        background: white;
        padding: 30px 0;
        border-radius: 10px;
    }

    /* TRACK WRAPPER (JENDELA SCROLL) */
    .carousel-track-wrapper {
        overflow-x: auto; /* Area yang bisa di-scroll */
        width: 100%;
        
        /* Sembunyikan Scrollbar */
        -ms-overflow-style: none;
        scrollbar-width: none;
        scroll-behavior: smooth;
    }
    
    .carousel-track-wrapper::-webkit-scrollbar {
        display: none;
    }

    /* TRACK PARTNER (ISINYA YANG MEMANJANG) */
    /* [PERBAIKAN DISINI] Flex dipindah ke sini */
    .carousel-track-partner {
        display: flex; 
        gap: 20px;
        width: max-content; /* Pastikan lebarnya mengikuti isi konten */
        padding: 10px 5px;
    }

    /* KARTU LOGO */
    .logo-card-partner {
        flex: 0 0 auto; /* Jangan menyusut */
        width: 180px;
        height: 140px;
        background: white;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
        border: 1px solid #eee;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        transition: transform 0.2s ease;
        padding: 15px;
        overflow: hidden;
    }

    .partner-name-text {
        font-size: 13px; /* Ukuran font pas */
        font-weight: 700;
        color: #555;
        text-align: center;
        line-height: 1.4;
        width: 100%;

        /* TEKNIK PEMBATAS BARIS (LINE CLAMP) */
        display: -webkit-box;
        -webkit-line-clamp: 3; /* Maksimal 3 baris */
        line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis; /* Kasih titik-titik (...) */
        word-wrap: break-word; /* Pecah kata yang kepanjangan */
    }

    .logo-card-partner:hover {
        transform: translateY(-5px);
        border-color: #02406C;
    }

    .logo-card-partner img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
    }

    /* TOMBOL NAVIGASI */
    .carousel-btn-partner {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        width: 45px;
        height: 45px;
        background: #fff;
        border: 1px solid #ddd;
        border-radius: 50%;
        cursor: pointer;
        z-index: 10;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        color: #333;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        transition: all 0.3s;
    }

    .carousel-btn-partner:hover {
        background-color: #02406C;
        color: #fff;
        border-color: #02406C;
    }

    .btn-prev { left: -20px; }
    .btn-next { right: -20px; }

    @media (max-width: 768px) {
        .carousel-btn-partner { display: none; }
        .logo-card-partner { width: 140px; height: 110px; }
    }
</style>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Kita scroll wrapper-nya, bukan isinya
        const track = document.querySelector('.carousel-track-wrapper');
        const prevBtn = document.getElementById('prevBtnPartner');
        const nextBtn = document.getElementById('nextBtnPartner');

        if (!track || !prevBtn || !nextBtn) return;

        const scrollAmount = 220; // Lebar kartu (180) + Gap (20) + Padding dikit

        nextBtn.addEventListener('click', () => {
            track.scrollBy({ left: scrollAmount, behavior: 'smooth' });
        });

        prevBtn.addEventListener('click', () => {
            track.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
        });

        // Auto Scroll
        let autoScroll;
        function startAutoScroll() {
            autoScroll = setInterval(() => {
                // Jika sudah mentok kanan, balik ke 0
                if (Math.ceil(track.scrollLeft + track.clientWidth) >= track.scrollWidth) {
                    track.scrollTo({ left: 0, behavior: 'smooth' });
                } else {
                    track.scrollBy({ left: scrollAmount, behavior: 'smooth' });
                }
            }, 3000);
        }

        function stopAutoScroll() {
            clearInterval(autoScroll);
        }

        startAutoScroll();
        
        // Stop kalau mouse diarahkan ke area carousel (bukan cuma track)
        const container = document.querySelector('.partners-carousel-wrapper');
        container.addEventListener('mouseenter', stopAutoScroll);
        container.addEventListener('mouseleave', startAutoScroll);
    });
</script>