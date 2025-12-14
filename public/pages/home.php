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
        // Ambil data activity terbaru
        $query = "SELECT id_activity, judul, deskripsi, gambar FROM activity ORDER BY tanggal_kegiatan DESC LIMIT 9";
        $stmt = $pdo->prepare($query);
        $stmt->execute();
        $activities = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        // Silent error agar web tetap jalan
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
                            // Path gambar (gunakan logika thumbnail jika mau, tapi ini pakai original dulu sesuai kodemu)
                            $imagePath = "uploads/activity/" . htmlspecialchars($activity['gambar']);
                            
                            // Cek thumbnail (Opsional, agar lebih ringan)
                            $baseName = pathinfo($activity['gambar'], PATHINFO_FILENAME);
                            $ext = pathinfo($activity['gambar'], PATHINFO_EXTENSION);
                            $thumbPath = "uploads/thumb/activity-thumb/" . $baseName . "-thumb." . $ext;
                            
                            // Jika thumbnail ada di folder, pakai thumbnail
                            if(file_exists(__DIR__ . '/../../public/' . $thumbPath)) {
                                $displayImage = $thumbPath;
                            } else {
                                $displayImage = $imagePath;
                            }

                            $title = htmlspecialchars($activity['judul']);
                    ?>
                            <li>
                                <div class="w3_agile_portfolio_grid">
                                    <a href="javascript:void(0);" class="gallery-item" data-image="<?php echo $imagePath; ?>" data-title="<?php echo $title; ?>">
                                        <img src="<?php echo $displayImage; ?>" alt="<?php echo $title; ?>" class="img-fluid radius-image" 
                                             style="height: 250px; width: 100%; object-fit: cover;"/>
                                        <div class="w3layouts_news_grid_pos">
                                            <div class="wthree_text">
                                                <h3><?php echo $title; ?></h3>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </li>
                    <?php
                        }
                    } else {
                        echo '<li><p class="text-center" style="width:100%;">Belum ada kegiatan yang tersedia.</p></li>';
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


    <section class="w3l-passion-mid-sec py-5">
        <div class="container py-md-5 py-3">
            <div class="container">
                <div class="row w3l-passion-mid-grids">
                    <div class="col-lg-6 passion-grid-item-info pe-lg-5 mb-lg-0 mb-5">
                        <h6 class="title-subw3hny mb-1">What We Offer</h6>
                        <h3 class="title-w3l mb-4">Our Project</h3>
                        <p class="mt-3 pe-lg-5">Our lab showcases a diverse range of applied AI projects designed to solve real-world problems and support innovation across multiple domains.</p>
                        <div class="w3banner-content-btns">
                            <a href="index.php?page=activity" class="btn btn-style btn-primary mt-lg-5 mt-4 me-2">
                                Read More <i class="fas fa-angle-double-right ms-2"></i>
                            </a>
                        </div>
                    </div>
                    <div class="col-lg-6 passion-grid-item-info">
                        <img src="assets/images/g3.jpg" alt="" class="img-fluid radius-image">
                    </div>
                </div>
            </div>
        </div>
    </section>


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
                                            
                                            // 2. Default value (kosong)
                                            $pathLogo = "";
                                            $thumbLogo = "";
                                            $hasImage = false;

                                            // 3. CEK DULU: Apakah $logo ada isinya? (Solusi Error Deprecated)
                                            if (!empty($logo)) {
                                                $hasImage = true;
                                                $pathLogo = "uploads/partner/" . $logo;
                                                
                                                // Proses pathinfo hanya jika $logo TIDAK NULL
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
                                                <div style="text-align:center; font-weight:bold; font-size:14px; color:#555;">
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
        /* ... (CSS Asli Partner Kamu) ... */
        .partners-carousel-wrapper { position: relative; max-width: 1200px; margin: 0 auto; }
        .carousel-partners-container { position: relative; display: flex; align-items: center; justify-content: center; gap: 20px; background: white; padding: 40px 20px; border-radius: 10px; }
        .carousel-track-wrapper { overflow: hidden; width: 100%; max-width: 900px; }
        .carousel-track-partner { display: flex; transition: transform 0.5s ease-in-out; gap: 20px; }
        .logo-card-partner { flex: 0 0 auto; width: 180px; height: 140px; background: white; border-radius: 10px; display: flex; align-items: center; justify-content: center; padding: 20px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08); transition: transform 0.3s ease, box-shadow 0.3s ease; }
        .logo-card-partner:hover { transform: translateY(-5px); box-shadow: 0 5px 20px rgba(0, 0, 0, 0.12); }
        .logo-card-partner img { max-width: 100%; max-height: 100%; object-fit: contain; }
        .carousel-btn-partner { background: white; border: 2px solid #e0e0e0; width: 50px; height: 50px; border-radius: 50%; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 24px; color: #333; transition: all 0.3s ease; flex-shrink: 0; font-weight: bold; }
        .carousel-btn-partner:hover { background: #f8f9fa; border-color: #007bff; color: #007bff; transform: scale(1.1); }
        .carousel-btn-partner:active { transform: scale(0.95); }
        .carousel-btn-partner:disabled { opacity: 0.3; cursor: not-allowed; border-color: #e0e0e0; }
        @media (max-width: 768px) { .logo-card-partner { width: 150px; height: 120px; } .carousel-btn-partner { width: 40px; height: 40px; font-size: 20px; } .carousel-partners-container { gap: 10px; padding: 30px 10px; } }
        @media (max-width: 480px) { .logo-card-partner { width: 130px; height: 100px; padding: 15px; } }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const trackPartner = document.getElementById('carouselTrackPartner');
            const prevBtnPartner = document.getElementById('prevBtnPartner');
            const nextBtnPartner = document.getElementById('nextBtnPartner');

            // Cek apakah elemen ada sebelum lanjut
            if(!trackPartner) return;

            let currentIndexPartner = 0;
            let autoSlideIntervalPartner;
            let visibleCardsPartner = 4;

            function updateVisibleCardsPartner() {
                if (window.innerWidth <= 480) { visibleCardsPartner = 1; } 
                else if (window.innerWidth <= 768) { visibleCardsPartner = 2; } 
                else if (window.innerWidth <= 1024) { visibleCardsPartner = 3; } 
                else { visibleCardsPartner = 4; }
            }

            function updateCarouselPartner() {
                const cards = trackPartner.querySelectorAll('.logo-card-partner');
                if(cards.length === 0) return;

                const cardWidth = cards[0].offsetWidth;
                const gap = 20;
                const moveDistance = (cardWidth + gap) * currentIndexPartner;

                trackPartner.style.transform = `translateX(-${moveDistance}px)`;

                prevBtnPartner.disabled = currentIndexPartner === 0;
                nextBtnPartner.disabled = currentIndexPartner >= cards.length - visibleCardsPartner;
            }

            function nextSlidePartner() {
                const cards = trackPartner.querySelectorAll('.logo-card-partner');
                if (currentIndexPartner < cards.length - visibleCardsPartner) {
                    currentIndexPartner++; updateCarouselPartner();
                } else {
                    currentIndexPartner = 0; updateCarouselPartner();
                }
            }
            function prevSlidePartner() {
                if (currentIndexPartner > 0) { currentIndexPartner--; updateCarouselPartner(); }
            }
            function startAutoSlidePartner() { autoSlideIntervalPartner = setInterval(nextSlidePartner, 3000); }
            function stopAutoSlidePartner() { clearInterval(autoSlideIntervalPartner); }

            if(prevBtnPartner && nextBtnPartner) {
                prevBtnPartner.addEventListener('click', function() { prevSlidePartner(); stopAutoSlidePartner(); startAutoSlidePartner(); });
                nextBtnPartner.addEventListener('click', function() { nextSlidePartner(); stopAutoSlidePartner(); startAutoSlidePartner(); });
                trackPartner.addEventListener('mouseenter', stopAutoSlidePartner);
                trackPartner.addEventListener('mouseleave', startAutoSlidePartner);
            }

            window.addEventListener('resize', function() {
                updateVisibleCardsPartner();
                const cards = trackPartner.querySelectorAll('.logo-card-partner');
                if (currentIndexPartner > cards.length - visibleCardsPartner) {
                    currentIndexPartner = Math.max(0, cards.length - visibleCardsPartner);
                }
                updateCarouselPartner();
            });

            updateVisibleCardsPartner();
            updateCarouselPartner();
            startAutoSlidePartner();
        });
    </script>