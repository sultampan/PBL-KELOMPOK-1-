<?php
require_once __DIR__ . "/../../config/koneksi.php";

// Ambil data produk dari PostgreSQL via PDO
$stmt = $pdo->prepare("SELECT * FROM produk ORDER BY id_produk ASC");
$stmt->execute();
$produk = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<style>
.box-wrap {
    min-height: 420px;
    display: flex;
    flex-direction: column;
    justify-content: flex-start;
    padding: 30px;
    border-radius: 12px;
}
</style>

    <!--//Header-->
    <!--/Banner-Start-->
    <!-- main-slider -->
    <section class="w3l-main-slider banner-slider" id="home">
        <div class="owl-one owl-carousel owl-theme">
            <div class="item">
                <div class="slider-info banner-view banner-top1">
                    <div class="container">
                        <div class="banner-info header-hero-19">
                            <h3 class="title-hero-19">Laboratory for Applied Informatics</h3>
                            <p class="w3ban-para">The Applied Informatics Laboratory at Malang State Polytechnic is an innovation center focused on developing information technology-based solutions. Through collaboration, the Applied Informatics Laboratory continuously strives to deliver relevant and useful technology. </p>

                       <a href="/PBL-KELOMPOK-1-/public/index.php?page=about" 
   class="btn btn-style btn-primary mt-sm-5 mt-4">
   Read More <i class="fas fa-angle-double-right ms-2"></i>
</a>



                        </div>
                    </div>
                </div>
            </div>
     
        </div>
    </section>
    <!-- //main-slider -->
    <!--/grids-->
    <section class="w3l-grids-3 py-5" id="about">
        <div class="container py-md-5 py-3">
            <div class="bottom-ab-grids align-items-center">
                <div class="w3ab-left-top">
                    <h6 class="title-subw3hny mb-1">Our Info</h6>
                    <h3 class="title-w3l mb-2">About the Applied Informatics Laboratory</h3>
                    <p class="my-3 mb-5 px-lg-5"> The Applied Informatics Laboratory at Politeknik Negeri Malang serves as a research and development hub that supports practical learning, collaboration, and innovation in applied information technology.</p>

                    <div class="video-wrapper" style="position: relative; width: 100%; max-width: 900px; margin: auto;">
    <iframe
        id="ytplayer"
        width="100%"
        height="450"
        src="https://www.youtube.com/embed/uNLhOq4C2d4"
        frameborder="0"
        allow="accelerometer; encrypted-media; gyroscope; picture-in-picture"
        allowfullscreen>
    </iframe>

    <!-- Tombol Play Custom -->
    <button id="customPlayBtn" class="play-button-custom"
        style="
            position: absolute;
            top: 50%; left: 50%;
            transform: translate(-50%, -50%);
            border: none;
            background: rgba(255,255,255,0.9);
            width: 80px; height: 80px;
            border-radius: 50%;
            display: flex; justify-content: center; align-items: center;
            cursor: pointer;
            font-size: 30px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
        ">
        <i class="fa fa-play"></i>
    </button>
</div>


                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--//grids-->

    <!--/w3-grids-->
    <section class="w3l-passion-mid-sec home-phny py-5">
        <div class="container py-md-5 py-3">
            <div class="container">
                <div class="row w3l-passion-mid-grids">
                    <div class="col-lg-6 passion-grid-item-info pe-lg-5 mb-lg-0 mb-5">
                        <h6 class="title-subw3hny mb-1">Welcome</h6>
                        <h3 class="title-w3l mb-4">Empowering Innovation Through Applied Informatics</h3>
                        <p class="mt-3 pe-lg-5">The Applied Informatics Laboratory focuses on research, development, and hands-on practice in areas such as cybersecurity, artificial intelligence, software engineering, and data-driven solutions. The lab supports collaborative projects, practical learning, and innovation to help students and researchers build technologies that address real-world needs.</p>

                    </div>
                    <div class="col-lg-6 w3hny-passion-item">
                        <div class="row">
                            <div class="col-6 passion-grid-item-pic">
                                <img src="assets/images/ab1.jpg" alt="" 
                                class="img-fluid radius-image"
                                style="width: 150%; height: 300px; object-fit: cover;">
                            </div>
                            <div class="col-6 passion-grid-item-pic">
                                <img src="assets/images/ab2.jpg" alt="" 
                                class="img-fluid radius-image"
                                style="width: 150%; height: 300px; object-fit: cover;">
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--//w3-grids-->
    <!-- features section -->
<section class="w3l-features py-5" id="work">
    <div class="container py-lg-5 py-md-4 py-2">

        <div class="title-content text-center mb-lg-3 mb-4">
            <h6 class="title-subw3hny mb-1">Our Products</h6>
            <h3 class="title-w3l">Innovative Solutions Developed by the Applied Informatics Laboratory</h3>
        </div>

        <div class="main-cont-wthree-2">
            <div class="row justify-content-center">

                <?php foreach ($produk as $p): ?>
                    <div class="col-lg-4 col-md-6 mt-lg-5 mt-4">
                        <div class="grids-1 box-wrap">

                            <!-- ICON / GAMBAR PRODUK -->
                            <div class="icon">
    <?php 
    // 1. Ambil nama file dari database
    $gambar = $p['gambar']; 
    
    // 2. Pecah nama file untuk menyisipkan "-thumb"
    // Misal: "foto.jpg" jadi "foto" dan "jpg"
    $ext = pathinfo($gambar, PATHINFO_EXTENSION);
    $base = pathinfo($gambar, PATHINFO_FILENAME);
    
    // 3. Susun nama thumbnail: "foto-thumb.jpg"
    $thumbName = $base . '-thumb.' . $ext; 

    // 4. Set Path URL
    // Path Thumbnail
    $srcThumb = "uploads/thumb/produk-thumb/" . $thumbName;
    // Path Gambar Asli (Cadangan jika thumbnail rusak/tidak ada)
    $srcAsli  = "uploads/produk/" . $gambar;
    ?>

    <img src="<?php echo htmlspecialchars($srcThumb); ?>"
         alt="<?php echo htmlspecialchars($p['nama']); ?>"
         style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px;"
         onerror="this.onerror=null; this.src='<?php echo htmlspecialchars($srcAsli); ?>';">
         </div>

                            <!-- NAMA PRODUK -->
                            <h4>
                                <a href="<?php echo htmlspecialchars($p['link_produk']); ?>"
                                   class="title-head mb-3"
                                   target="_blank">
                                   <?php echo htmlspecialchars($p['nama']); ?>
                                </a>
                            </h4>

                            <!-- DESKRIPSI -->
                            <p class="text-para">
                                <?php echo htmlspecialchars($p['deskripsi']); ?>
                            </p>

                        </div>
                    </div>
                <?php endforeach; ?>

            </div>
        </div>
    </div>
</section>
<!--//features section -->

    <!--/Gallery-Section-->
    <section class="w3l-gallery" id="gallery">
        <div class="destionation-innf py-5">
            <div class="container py-lg-5 py-md-4 py-2 HomePageGallery">
                <div class="title-content text-center">
                    <h6 class="title-subw3hny text-center">Laboratory Activities</h6>
                    <h3 class="title-w3l mb-5 text-center">Latest Research & Collaboration Projects</h3>
                </div>
                <!--/grids-grids-->
                <ul class="gallery_agile">
                    <li>
                        <div class="w3_agile_portfolio_grid">
                            <a href="assets/images/g1.jpg">
                                <img src="assets/images/g1.jpg" alt=" " class="img-fluid radius-image" />
                                <div class="w3layouts_news_grid_pos">
                                    <div class="wthree_text">
                                        <h3>Cybersecurity Program Coordination Meeting</h3>
                                 
                                    </div>
                                </div>
                            </a>
                        </div>
                    </li>
                    <li>
                        <div class="w3_agile_portfolio_grid">
                            <a href="assets/images/g2.jpg">
                                <img src="assets/images/g2.jpg" alt=" " class="img-fluid radius-image" />
                                <div class="w3layouts_news_grid_pos">
                                    <div class="wthree_text">
                                        <h3>Applied Informatics Research Presentation</h3>
                                 
                                    </div>
                                </div>
                            </a>
                        </div>
                    </li>
                    <li>
                        <div class="w3_agile_portfolio_grid">
                            <a href="assets/images/g3.jpg">
                                <img src="assets/images/g3.jpg" alt=" " class="img-fluid radius-image" />
                                <div class="w3layouts_news_grid_pos">
                                    <div class="wthree_text">
                                        <h3>AI & Data Science Workshop</h3>
                                    
                                    </div>
                                </div>
                            </a>
                        </div>

                    </li>
                    <li>
                        <div class="w3_agile_portfolio_grid">
                            <a href="assets/images/g4.jpg">
                                <img src="assets/images/g4.jpg" alt=" " class="img-fluid radius-image" />
                                <div class="w3layouts_news_grid_pos">
                                    <div class="wthree_text">
                                        <h3>Industry Collaboration Discussion</h3>
                                     
                                    </div>
                                </div>
                            </a>
                        </div>
                    </li>
                    <li>
                        <div class="w3_agile_portfolio_grid">
                            <a href="assets/images/g5.jpg">
                                <img src="assets/images/g5.jpg" alt=" " class="img-fluid radius-image" />
                                <div class="w3layouts_news_grid_pos">
                                    <div class="wthree_text">
                                        <h3>Training Session on Digital Transformation</h3>
                                   
                                    </div>
                                </div>
                            </a>
                        </div>
                    </li>
                    <li>
                        <div class="w3_agile_portfolio_grid">
                            <a href="assets/images/g6.jpg">
                                <img src="assets/images/g6.jpg" alt=" " class="img-fluid radius-image" />
                                <div class="w3layouts_news_grid_pos">
                                    <div class="wthree_text">
                                        <h3>National Seminar on Technology Innovation</h3>
                                     
                                    </div>
                                </div>
                            </a>
                        </div>

                    </li>
                    <li>
                        <div class="w3_agile_portfolio_grid">
                            <a href="assets/images/g7.jpg">
                                <img src="assets/images/g7.jpg" alt=" " class="img-fluid radius-image" />
                                <div class="w3layouts_news_grid_pos">
                                    <div class="wthree_text">
                                        <h3>Applied Informatics Field Visit</h3>
                                
                                    </div>
                                </div>
                            </a>
                        </div>
                    </li>
                    <li>
                        <div class="w3_agile_portfolio_grid">
                            <a href="assets/images/g8.jpg">
                                <img src="assets/images/g8.jpg" alt=" " class="img-fluid radius-image" />
                                <div class="w3layouts_news_grid_pos">
                                    <div class="wthree_text">
                                        <h3>Research Enhancement Program</h3>
                                
                                    </div>
                                </div>
                            </a>
                        </div>
                    </li>
                    <li>
                        <div class="w3_agile_portfolio_grid">
                            <a href="assets/images/g9.jpg">
                                <img src="assets/images/g9.jpg" alt=" " class="img-fluid radius-image" />
                                <div class="w3layouts_news_grid_pos">
                                    <div class="wthree_text">
                                        <h3>Technology Implementation & Evaluation Meeting</h3>
                        
                                    </div>
                                </div>
                            </a>
                        </div>

                    </li>
                </ul>
                <!--//rids-grids-->
            </div>
        </div>
    </section>
    <!--//Gallery-Section-->
    <!--/w3-grids-->
    <section class="w3l-passion-mid-sec py-5">
        <div class="container py-md-5 py-3">
            <div class="container">
                <div class="row w3l-passion-mid-grids">
                    <div class="col-lg-6 passion-grid-item-info pe-lg-5 mb-lg-0 mb-5">
                        <h6 class="title-subw3hny mb-1">What We Offer</h6>
                        <h3 class="title-w3l mb-4">Practical Research and Innovation for Real-World Solutions</h3>
                        <p class="mt-3 pe-lg-5">Our lab provides facilities for applied research, software development, AI experimentation, cybersecurity practices, and collaborative technology projects. We guide students and partners in creating impactful digital solutions.</p>
                     <div class="w3banner-content-btns">
<a href="index.php?page=home#activity"
   class="btn btn-style btn-primary mt-lg-5 mt-4 me-2">
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
    <!--//w3-grids-->

<!--/partners-->
<section class="w3l-testimonials" id="testimonials">
    <div class="cusrtomer-layout py-5">
        <div class="container py-lg-4 py-md-3 py-2 pb-lg-0">
            <div class="title-content text-center">
                <h6 class="title-subw3hny">Our Partners</h6>
                <h3 class="title-w3l mb-5">Collaborative Partners</h3>
            </div>

            <!-- CAROUSEL PARTNERS BARU -->
            <div class="partners-carousel-wrapper pt-lg-4">
                <div class="carousel-partners-container">
                    <button class="carousel-btn-partner btn-prev" id="prevBtnPartner">‹</button>
                    
                    <div class="carousel-track-wrapper">
                        <div class="carousel-track-partner" id="carouselTrackPartner">
                            <!-- Logo 1 - Bumiaji -->
                            <div class="logo-card-partner">
                                <img src="assets/images/team1.jpg" alt="Bumiaji Sejahtera">
                            </div>
                            <!-- Logo 2 - ARM Solusi -->
                            <div class="logo-card-partner">
                                <img src="assets/images/team2.jpg" alt="ARM Solusi">
                            </div>
                            <!-- Logo 3 - ADS -->
                            <div class="logo-card-partner">
                                <img src="assets/images/team3.jpg" alt="ADS">
                            </div>
                            <!-- Tambahkan logo lain sesuai kebutuhan -->
                            <div class="logo-card-partner">
                                <img src="assets/images/team1.jpg" alt="Partner 4">
                            </div>
                            <div class="logo-card-partner">
                                <img src="assets/images/team2.jpg" alt="Partner 5">
                            </div>
                        </div>
                    </div>
                    
                    <button class="carousel-btn-partner btn-next" id="nextBtnPartner">›</button>
                </div>
            </div>
            <!-- END CAROUSEL -->

        </div>
    </div>
</section>
<!--//testimonials-->

<!-- CSS untuk Carousel Partners -->
<style>
.partners-carousel-wrapper {
    position: relative;
    max-width: 1200px;
    margin: 0 auto;
}

.carousel-partners-container {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 20px;
    background: white;
    padding: 40px 20px;
    border-radius: 10px;
}

.carousel-track-wrapper {
    overflow: hidden;
    width: 100%;
    max-width: 900px;
}

.carousel-track-partner {
    display: flex;
    transition: transform 0.5s ease-in-out;
    gap: 20px;
}

.logo-card-partner {
    flex: 0 0 auto;
    width: 180px;
    height: 140px;
    background: white;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.logo-card-partner:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 20px rgba(0,0,0,0.12);
}

.logo-card-partner img {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
}

.carousel-btn-partner {
    background: white;
    border: 2px solid #e0e0e0;
    width: 50px;
    height: 50px;
    border-radius: 50%;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    color: #333;
    transition: all 0.3s ease;
    flex-shrink: 0;
    font-weight: bold;
}

.carousel-btn-partner:hover {
    background: #f8f9fa;
    border-color: #007bff;
    color: #007bff;
    transform: scale(1.1);
}

.carousel-btn-partner:active {
    transform: scale(0.95);
}

.carousel-btn-partner:disabled {
    opacity: 0.3;
    cursor: not-allowed;
    border-color: #e0e0e0;
}

.carousel-btn-partner:disabled:hover {
    background: white;
    transform: scale(1);
}

@media (max-width: 768px) {
    .logo-card-partner {
        width: 150px;
        height: 120px;
    }

    .carousel-btn-partner {
        width: 40px;
        height: 40px;
        font-size: 20px;
    }

    .carousel-partners-container {
        gap: 10px;
        padding: 30px 10px;
    }
}

@media (max-width: 480px) {
    .logo-card-partner {
        width: 130px;
        height: 100px;
        padding: 15px;
    }
}
</style>

<!-- JavaScript untuk Carousel Partners -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const trackPartner = document.getElementById('carouselTrackPartner');
    const prevBtnPartner = document.getElementById('prevBtnPartner');
    const nextBtnPartner = document.getElementById('nextBtnPartner');
    
    let currentIndexPartner = 0;
    let autoSlideIntervalPartner;
    let visibleCardsPartner = 4;

    function updateVisibleCardsPartner() {
        if (window.innerWidth <= 480) {
            visibleCardsPartner = 1;
        } else if (window.innerWidth <= 768) {
            visibleCardsPartner = 2;
        } else if (window.innerWidth <= 1024) {
            visibleCardsPartner = 3;
        } else {
            visibleCardsPartner = 4;
        }
    }

    function updateCarouselPartner() {
        const cards = trackPartner.querySelectorAll('.logo-card-partner');
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
            currentIndexPartner++;
            updateCarouselPartner();
        } else {
            currentIndexPartner = 0;
            updateCarouselPartner();
        }
    }

    function prevSlidePartner() {
        if (currentIndexPartner > 0) {
            currentIndexPartner--;
            updateCarouselPartner();
        }
    }

    function startAutoSlidePartner() {
        autoSlideIntervalPartner = setInterval(nextSlidePartner, 3000);
    }

    function stopAutoSlidePartner() {
        clearInterval(autoSlideIntervalPartner);
    }

    prevBtnPartner.addEventListener('click', function() {
        prevSlidePartner();
        stopAutoSlidePartner();
        startAutoSlidePartner();
    });

    nextBtnPartner.addEventListener('click', function() {
        nextSlidePartner();
        stopAutoSlidePartner();
        startAutoSlidePartner();
    });

    trackPartner.addEventListener('mouseenter', stopAutoSlidePartner);
    trackPartner.addEventListener('mouseleave', startAutoSlidePartner);

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
    <!--/footer-9-->
<script src="https://www.youtube.com/iframe_api"></script>

<script>
var player;

// YouTube API siap → hubungkan ke iframe
function onYouTubeIframeAPIReady() {
    player = new YT.Player('ytplayer', {});
}

// Klik tombol play custom
document.getElementById('customPlayBtn').addEventListener('click', function () {
    this.style.display = 'none'; // sembunyikan tombol custom
    if (player && player.playVideo) {
        player.playVideo();      // jalankan videonya
    }
});
</script>
