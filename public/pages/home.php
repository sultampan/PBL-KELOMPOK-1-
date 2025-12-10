<?php
require_once __DIR__ . '/../../config/koneksi.php';
?>

<style>
/* ===================================
   NAVBAR MODIFICATIONS
=================================== */
#site-header {
    position: fixed !important;
    top: 0;
    left: 0;
    right: 0;
    z-index: 9999;
    background: transparent;
    transition: all 0.3s ease;
}

/* Kotak putih transparan memanjang di navbar */
#site-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 100%;
    background: rgba(255, 255, 255, 0.3);
    backdrop-filter: blur(10px);
    z-index: -1;
    box-shadow: 0 2px 15px rgba(0, 0, 0, 0.15);
}

/* Logo tetap di atas saat scroll */
.navbar-brand {
    position: absolute !important;
    top: 50% !important;
    transform: translateY(-50%) !important;
    left: 20px !important;
    z-index: 10000;
    margin: 0 !important;
    background: transparent !important;
    padding: 0 !important;
}

.navbar-brand h1 {
    margin: 0 !important;
    padding: 0 !important;
    background: transparent !important;
}

.navbar-brand img {
    height: 80px !important;
    width: auto;
    transition: all 0.3s ease;
    filter: drop-shadow(0 2px 6px rgba(0,0,0,0.25));
    background: transparent !important;
    display: block;
}

/* Saat navbar di-scroll, logo mengecil sedikit */
#site-header.nav-fixed .navbar-brand img {
    height: 72px !important;
}

/* Saat scroll, background navbar lebih solid */
#site-header.nav-fixed::before {
    background: rgba(255, 255, 255, 0.95);
}

/* Menu navbar positioning */
.navbar-nav {
    margin-left: auto !important;
    padding-left: 120px !important;
}

/* ===================================
   GLOBAL SECTION STYLE
=================================== */
section {
    padding-top: 60px;
    padding-bottom: 60px;
}

h3.section-title {
    font-size: 32px;
    font-weight: 800;
    margin-bottom: 40px;
    color: #222;
}

/* ===================================
   HERO SECTION MODIFICATIONS
=================================== */
.hero-section {
    position: relative;
    height: 100vh;
    min-height: 600px;
    background-image: url('assets/images/banner1.jpg');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    display: flex;
    align-items: center;
    justify-content: center;
}

.hero-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.25);
}

.hero-content {
    position: relative;
    z-index: 2;
    max-width: 1000px;
    margin: 0 auto;
    padding: 0 30px;
    text-align: center;
}

.hero-title {
    font-size: 4rem !important;
    font-weight: 800;
    color: white;
    margin-bottom: 2.5rem;
    text-transform: uppercase;
    letter-spacing: 3px;
    text-shadow: 2px 2px 8px rgba(0, 0, 0, 0.6);
    line-height: 1.2;
}

.hero-description-box {
    background: rgba(41, 128, 185, 0.7);
    border: 3px solid rgba(255, 255, 255, 0.9);
    border-radius: 10px;
    padding: 4rem 5rem !important;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.3);
    backdrop-filter: blur(5px);
    max-width: 1100px;
    margin: 0 auto;
}

.hero-description {
    font-size: 1.3rem !important;
    line-height: 2;
    color: white;
    margin: 0;
    text-align: justify;
}

.scroll-indicator {
    position: absolute;
    bottom: 30px;
    left: 50%;
    transform: translateX(-50%);
    z-index: 3;
    animation: bounce 2s infinite;
}

.scroll-indicator i {
    font-size: 2rem;
    color: white;
    text-shadow: 0 2px 4px rgba(0,0,0,0.3);
}

@keyframes bounce {
    0%, 20%, 50%, 80%, 100% {
        transform: translateX(-50%) translateY(0);
    }
    40% {
        transform: translateX(-50%) translateY(-10px);
    }
    60% {
        transform: translateX(-50%) translateY(-5px);
    }
}

/* ===================================
   PRODUCT CARD
=================================== */
.product-card {
    background: #ffffff;
    padding: 25px;
    border-radius: 12px;
    text-align: center;
    transition: 0.3s ease-in-out;
    box-shadow: 0 4px 16px rgba(0,0,0,0.08);
    height: 100%;
    display: flex;
    flex-direction: column;
}

.product-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 12px 28px rgba(0,0,0,0.15);
}

.product-card .product-img {
    width: 100% !important;
    height: 280px !important;
    object-fit: contain !important;
    border-radius: 10px;
    margin-bottom: 20px;
    display: block !important;
    background: #f8f9fa;
    padding: 15px;
}

.product-title {
    font-size: 20px;
    font-weight: 700;
    margin-bottom: 12px;
    margin-top: 10px;
}

.product-desc {
    font-size: 15px;
    color: #555;
    line-height: 1.6;
    text-align: center;
}

/* ===================================
   PARTNER LOGO STYLE
=================================== */
.partner-logo {
    max-height: 90px;
    object-fit: contain;
    transition: 0.3s;
}

.partner-logo:hover {
    transform: scale(1.1);
}

/* ===================================
   SCROLL FADE ANIMATION
=================================== */
.fade-in {
    opacity: 0;
    transform: translateY(35px);
    transition: all 0.9s ease-out;
}

.fade-in.show {
    opacity: 1;
    transform: translateY(0px);
}

/* ===================================
   RESPONSIVE DESIGN
=================================== */
@media (max-width: 768px) {
    .navbar-brand {
        left: 15px;
    }
    
    .navbar-brand img {
        height: 55px !important;
    }
    
    #site-header.nav-fixed .navbar-brand img {
        height: 50px !important;
    }
    
    .navbar-nav {
        padding-left: 0 !important;
    }
    
    .hero-title {
        font-size: 2.2rem !important;
    }
    
    .hero-description-box {
        padding: 2.5rem 3rem !important;
    }
    
    .hero-description {
        font-size: 1.05rem !important;
    }
    
    .product-card .product-img {
        height: 220px !important;
    }
}
</style>

<!-- ===================================
     JAVASCRIPT ANIMASI SCROLL & NAVBAR
=================================== -->
<script>
document.addEventListener("DOMContentLoaded", () => {
    // Fade-in animation
    const fadeElements = document.querySelectorAll(".fade-in");
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add("show");
            }
        });
    }, { threshold: 0.2 });

    fadeElements.forEach(el => observer.observe(el));

    // Navbar scroll effect
    const header = document.getElementById('site-header');
    window.addEventListener('scroll', () => {
        if (window.scrollY > 50) {
            header.classList.add('nav-fixed');
        } else {
            header.classList.remove('nav-fixed');
        }
    });
});
</script>

<!-- ===================================
     HERO SECTION WITH IMAGE BACKGROUND
=================================== -->
<section class="hero-section">
    <div class="hero-overlay"></div>
    
    <div class="hero-content">
        <h1 class="hero-title">
            LABORATORY FOR APPLIED INFORMATICS
        </h1>
        
        <div class="hero-description-box">
            <p class="hero-description">
                The Applied Informatics Laboratory at Malang State Polytechnic is an innovation center focused on
                developing information technology-based solutions. Through collaboration, the Applied Informatics Laboratory
                continuously strives to deliver relevant and useful technology, strengthening Malang State Polytechnic's
                position as a leading educational institution in applied informatics.
            </p>
        </div>
    </div>
    
    <div class="scroll-indicator">
        <i class="fas fa-chevron-down"></i>
    </div>
</section>

<!-- ===================================
     PRODUCT SECTION
=================================== -->
<section id="product" class="fade-in">
    <div class="container">
        <h3 class="section-title text-center">PRODUCT</h3>

        <div class="row justify-content-center">

            <?php
            $query = $pdo->query("SELECT * FROM produk ORDER BY id_produk LIMIT 3");
            while ($row = $query->fetch(PDO::FETCH_ASSOC)):
            ?>

            <div class="col-lg-4 col-md-6 mb-4 fade-in">
                <div class="product-card">
                    <img src="uploads/produk/<?= htmlspecialchars($row['gambar']) ?>" 
                         class="product-img" 
                         alt="<?= htmlspecialchars($row['nama']) ?>">

                    <h5 class="product-title"><?= htmlspecialchars($row['nama']) ?></h5>

                    <p class="product-desc">
                        <?= nl2br(htmlspecialchars($row['deskripsi'])) ?>
                    </p>
                </div>
            </div>

            <?php endwhile; ?>
        </div>

        <div class="text-center mt-4">
            <a href="?page=product" class="btn btn-link">View more</a>
        </div>
    </div>
</section>

<!-- ===================================
     PARTNER SECTIONS (4 kategori) WITH CAROUSEL
=================================== -->
<style>
.partner-section {
    padding: 80px 0;
    position: relative;
}

.partner-section.bg-blue {
    background: linear-gradient(135deg, #89CFF0 0%, #5DADE2 100%);
}

.partner-section.bg-white {
    background: #ffffff;
}

.partner-section .section-title {
    color: #000;
    font-size: 32px;
    font-weight: 800;
    margin-bottom: 50px;
    text-transform: uppercase;
    text-align: center;
}

.partner-carousel {
    position: relative;
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 80px;
}

.partner-carousel.no-arrows {
    padding: 0 20px;
}

.partner-slider-wrapper {
    overflow: hidden;
}

.partner-slider {
    display: flex;
    gap: 40px;
    transition: transform 0.5s ease;
    align-items: center;
    padding: 20px 0;
}

.partner-slider.centered {
    justify-content: center;
}

.partner-item {
    flex: 0 0 200px;
    height: 140px;
    background: white;
    border-radius: 15px;
    padding: 25px;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 6px 20px rgba(0,0,0,0.12);
    transition: all 0.3s ease;
}

.partner-section.bg-white .partner-item {
    box-shadow: 0 6px 20px rgba(0,0,0,0.08);
}

.partner-item:hover {
    transform: translateY(-8px);
    box-shadow: 0 12px 30px rgba(0,0,0,0.2);
}

.partner-item img {
    max-width: 100%;
    max-height: 90px;
    object-fit: contain;
}

.carousel-arrow {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    background: white;
    border: 2px solid #2980B9;
    width: 50px;
    height: 50px;
    border-radius: 50%;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    z-index: 10;
    transition: all 0.3s ease;
    color: #2980B9;
}

.carousel-arrow:hover {
    background: #2980B9;
    color: white;
    transform: translateY(-50%) scale(1.15);
    box-shadow: 0 6px 20px rgba(0,0,0,0.3);
}

.carousel-arrow.prev {
    left: 10px;
}

.carousel-arrow.next {
    right: 10px;
}

.carousel-arrow i {
    font-size: 22px;
    font-weight: bold;
}

@media (max-width: 768px) {
    .partner-carousel {
        padding: 0 60px;
    }
    
    .partner-item {
        flex: 0 0 150px;
        height: 110px;
    }
    
    .partner-item img {
        max-height: 70px;
    }
    
    .carousel-arrow {
        width: 40px;
        height: 40px;
    }
    
    .carousel-arrow i {
        font-size: 18px;
    }
}
</style>

<?php
// Cek apakah tabel partner ada
try {
    $checkTable = $pdo->query("SELECT to_regclass('partner')");
    $tableExists = $checkTable->fetchColumn() !== null;
} catch (Exception $e) {
    $tableExists = false;
}

if ($tableExists):
    $sections = [
        ["title" => "INDUSTRY PARTNERS", "kategori" => "Industry", "bg" => "bg-blue"],
        ["title" => "EDUCATIONAL INSTITUTIONS", "kategori" => "Educational", "bg" => "bg-white"],
        ["title" => "GOVERNMENT INSTITUTIONS", "kategori" => "Government", "bg" => "bg-blue"],
        ["title" => "INTERNATIONAL INSTITUTIONS", "kategori" => "International", "bg" => "bg-white"]
    ];

    foreach ($sections as $index => $section):
        try {
            $q = $pdo->prepare("SELECT * FROM partner WHERE kategori = ?");
            $q->execute([$section['kategori']]);
            $partners = $q->fetchAll(PDO::FETCH_ASSOC);
            
            if (count($partners) > 0):
                $isLastSection = ($section['kategori'] === 'International');
                $needsCarousel = count($partners) > 5 && !$isLastSection;
?>

<section class="partner-section <?= $section['bg'] ?> fade-in">
    <div class="container-fluid">
        <h3 class="section-title"><?= $section['title'] ?></h3>
        
        <div class="partner-carousel <?= !$needsCarousel ? 'no-arrows' : '' ?>" id="carousel-<?= $index ?>">
            
            <?php if ($needsCarousel): ?>
            <button class="carousel-arrow prev" onclick="slidePartner(<?= $index ?>, -1)">
                <i class="fas fa-chevron-left"></i>
            </button>
            <?php endif; ?>
            
            <div class="partner-slider-wrapper">
                <div class="partner-slider <?= !$needsCarousel ? 'centered' : '' ?>" id="slider-<?= $index ?>">
                    <?php foreach ($partners as $partner): ?>
                    <div class="partner-item">
                        <img src="uploads/partner/<?= htmlspecialchars($partner['logo']) ?>" 
                             alt="<?= htmlspecialchars($partner['nama'] ?? 'Partner') ?>">
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <?php if ($needsCarousel): ?>
            <button class="carousel-arrow next" onclick="slidePartner(<?= $index ?>, 1)">
                <i class="fas fa-chevron-right"></i>
            </button>
            <?php endif; ?>
            
        </div>
    </div>
</section>

<?php 
            endif;
        } catch (Exception $e) {
            // Silent fail
        }
    endforeach;
else:
    // Jika tabel tidak ada, tampilkan struktur dengan dummy data
?>

<!-- Section 1: Industry Partners - BIRU -->
<section class="partner-section bg-blue fade-in">
    <div class="container-fluid">
        <h3 class="section-title">INDUSTRY PARTNERS</h3>
        <div class="partner-carousel" id="carousel-0">
            <button class="carousel-arrow prev" onclick="slidePartner(0, -1)">
                <i class="fas fa-chevron-left"></i>
            </button>
            <div class="partner-slider-wrapper">
                <div class="partner-slider" id="slider-0">
                    <div class="partner-item"><div style="color: #999; font-weight: bold;">Logo 1</div></div>
                    <div class="partner-item"><div style="color: #999; font-weight: bold;">Logo 2</div></div>
                    <div class="partner-item"><div style="color: #999; font-weight: bold;">Logo 3</div></div>
                    <div class="partner-item"><div style="color: #999; font-weight: bold;">Logo 4</div></div>
                    <div class="partner-item"><div style="color: #999; font-weight: bold;">Logo 5</div></div>
                    <div class="partner-item"><div style="color: #999; font-weight: bold;">Logo 6</div></div>
                </div>
            </div>
            <button class="carousel-arrow next" onclick="slidePartner(0, 1)">
                <i class="fas fa-chevron-right"></i>
            </button>
        </div>
    </div>
</section>

<!-- Section 2: Educational Institutions - PUTIH -->
<section class="partner-section bg-white fade-in">
    <div class="container-fluid">
        <h3 class="section-title">EDUCATIONAL INSTITUTIONS</h3>
        <div class="partner-carousel" id="carousel-1">
            <button class="carousel-arrow prev" onclick="slidePartner(1, -1)">
                <i class="fas fa-chevron-left"></i>
            </button>
            <div class="partner-slider-wrapper">
                <div class="partner-slider" id="slider-1">
                    <div class="partner-item"><div style="color: #999; font-weight: bold;">Logo 1</div></div>
                    <div class="partner-item"><div style="color: #999; font-weight: bold;">Logo 2</div></div>
                    <div class="partner-item"><div style="color: #999; font-weight: bold;">Logo 3</div></div>
                    <div class="partner-item"><div style="color: #999; font-weight: bold;">Logo 4</div></div>
                    <div class="partner-item"><div style="color: #999; font-weight: bold;">Logo 5</div></div>
                </div>
            </div>
            <button class="carousel-arrow next" onclick="slidePartner(1, 1)">
                <i class="fas fa-chevron-right"></i>
            </button>
        </div>
    </div>
</section>

<!-- Section 3: Government Institutions - BIRU -->
<section class="partner-section bg-blue fade-in">
    <div class="container-fluid">
        <h3 class="section-title">GOVERNMENT INSTITUTIONS</h3>
        <div class="partner-carousel" id="carousel-2">
            <button class="carousel-arrow prev" onclick="slidePartner(2, -1)">
                <i class="fas fa-chevron-left"></i>
            </button>
            <div class="partner-slider-wrapper">
                <div class="partner-slider" id="slider-2">
                    <div class="partner-item"><div style="color: #999; font-weight: bold;">Logo 1</div></div>
                    <div class="partner-item"><div style="color: #999; font-weight: bold;">Logo 2</div></div>
                    <div class="partner-item"><div style="color: #999; font-weight: bold;">Logo 3</div></div>
                    <div class="partner-item"><div style="color: #999; font-weight: bold;">Logo 4</div></div>
                    <div class="partner-item"><div style="color: #999; font-weight: bold;">Logo 5</div></div>
                </div>
            </div>
            <button class="carousel-arrow next" onclick="slidePartner(2, 1)">
                <i class="fas fa-chevron-right"></i>
            </button>
        </div>
    </div>
</section>

<!-- Section 4: International Institutions - PUTIH (No Arrows, 2 logos only) -->
<section class="partner-section bg-white fade-in">
    <div class="container-fluid">
        <h3 class="section-title">INTERNATIONAL INSTITUTIONS</h3>
        <div class="partner-carousel no-arrows">
            <div class="partner-slider-wrapper">
                <div class="partner-slider centered">
                    <div class="partner-item"><div style="color: #999; font-weight: bold;">Logo 1</div></div>
                    <div class="partner-item"><div style="color: #999; font-weight: bold;">Logo 2</div></div>
                </div>
            </div>
        </div>
    </div>
</section>

<p class="text-center text-danger mt-3">
    <small>Note: Partner data table not found. Please create 'partner' table in database.</small>
</p>

<?php endif; ?>

<script>
// Carousel functionality
const carouselStates = {};

function slidePartner(carouselId, direction) {
    const slider = document.getElementById(`slider-${carouselId}`);
    if (!slider) return;
    
    const items = slider.querySelectorAll('.partner-item');
    if (items.length === 0) return;
    
    const itemWidth = items[0].offsetWidth + 40; // width + gap
    
    if (!carouselStates[carouselId]) {
        carouselStates[carouselId] = { position: 0 };
    }
    
    const state = carouselStates[carouselId];
    const containerWidth = slider.parentElement.offsetWidth;
    const visibleItems = Math.floor(containerWidth / itemWidth);
    const maxPosition = Math.max(0, items.length - visibleItems);
    
    state.position += direction;
    
    // Loop around
    if (state.position < 0) {
        state.position = maxPosition;
    } else if (state.position > maxPosition) {
        state.position = 0;
    }
    
    slider.style.transform = `translateX(-${state.position * itemWidth}px)`;
}

// Auto-slide setiap 4 detik (kecuali section terakhir)
setInterval(() => {
    for (let i = 0; i < 3; i++) { // Hanya 3 section pertama (0, 1, 2)
        const slider = document.getElementById(`slider-${i}`);
        if (slider) {
            slidePartner(i, 1);
        }
    }
}, 4000);
</script>

<!-- ===================================
     LOCATION SECTION
=================================== -->
<section class="fade-in">

    <style>
        /* Container map agar center dan responsif */
        .map-wrapper {
            width: 100%;
            max-width: 1000px; /* batas maksimum lebar */
            margin: 0 auto; /* center */
            padding: 0 15px; /* jarak kiri-kanan */
        }

        .map-wrapper iframe {
            width: 100%;
            height: 450px; /* tinggi map */
            border: 0;
            border-radius: 10px; /* opsional – biar lebih rapi */
        }
    </style>

    <div class="container">
        <h3 class="section-title text-center">Laboratory Location</h3>

        <div class="map-wrapper">
            <iframe
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3951.5395186403593!2d112.61141517500657!3d-7.943064192081103!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e78833cae0fc99f%3A0x20d162669317de31!2sGedung%20Pascasarjana%20POLINEMA!5e0!3m2!1sid!2sid!4v1765355201892!5m2!1sid!2sid"
                allowfullscreen
                loading="lazy"
                referrerpolicy="no-referrer-when-downgrade">
            </iframe>
        </div>
    </div>
</section>

