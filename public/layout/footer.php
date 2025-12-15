<?php
// 1. Ambil Data Sosmed
$activeSocials = [];
try {
    if (isset($pdo)) {
        $stmtSoc = $pdo->prepare("SELECT * FROM social_media WHERE is_active = TRUE ORDER BY id_social ASC");
        $stmtSoc->execute();
        $activeSocials = $stmtSoc->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (Exception $e) {}
?>

<footer class="w3l-footer9">
    <section class="footer-inner-main py-3">
        <div class="container py-md-3">
            <div class="row align-items-start">
                
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="footer-logo">
                        <a href="index.php?page=home">
                            <img src="assets/images/logo.png" alt="Applied Informatics Laboratory" style="max-width: 200px; height: auto;">
                        </a>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 mb-4">
                    <h6 class="footer-title mb-3">Laboratory</h6>
                    <p class="footer-text mb-2">2nd Floor of the Postgraduate Building of Malang State Polytechnic</p>
                    <p class="footer-text mb-0">
                        <a href="mailto:email@labai.polinema.ac.id" class="footer-link-text">
                            email@labai.polinema.ac.id
                        </a>
                    </p>
                </div>

                <div class="col-lg-2 col-md-6 mb-3">
                    <h6 class="footer-title mb-2">Company</h6>
                    <ul class="footer-list list-unstyled mb-0">
                        <li class="mb-2"><a href="index.php?page=about" class="footer-link">About us</a></li>
                        <li class="mb-2"><a href="index.php?page=contact" class="footer-link">Contact us</a></li>
                    </ul>
                </div>

                <div class="col-lg-4 col-md-6 mb-4">
                    <h6 class="footer-title mb-3">Social Media</h6>
                    <ul class="list-unstyled social-media-grid">
                        <?php if (!empty($activeSocials)): ?>
                            <?php foreach($activeSocials as $soc): ?>
                                <li>
                                    <a href="<?= htmlspecialchars($soc['link_url']) ?>" target="_blank" class="social-link d-flex align-items-center">
                                        <div class="social-icon-box">
                                            <i class="<?= htmlspecialchars($soc['icon_class']) ?>"></i>
                                        </div>
                                        <span class="social-name ms-3"><?= htmlspecialchars($soc['nama_platform']) ?></span>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <li style="color: #aaa; grid-column: span 2;">Sosial media belum diatur.</li>
                        <?php endif; ?>
                    </ul>
                </div>

            </div>

            <div class="row mt-2 pt-4" style="border-top: 1px solid #333;">
                <div class="col-md-12 text-center">
                    <p class="copyright-text mb-0">
                        © 2025 Applied Informatics Laboratory. All rights reserved.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <button onclick="topFunction()" id="movetop" title="Go to top">
        <span class="fas fa-level-up-alt" aria-hidden="true"></span>
    </button>
</footer>

<style>
    /* Footer Global */
    .w3l-footer9, .footer-inner-main { background-color: #1a1a1a; color: #ffffff; }
    
    /* JUDUL (Title) */
    .footer-title { 
        color: #ffffff; font-size: 16px; font-weight: 700; 
        margin-bottom: 15px; 
        text-transform: uppercase; letter-spacing: 0.5px; 
    }
    
    /* TEKS BIASA (Laboratory) & LINK TEXT (Email) */
    .footer-text, .footer-link-text { 
        color: #aaa !important; 
        font-size: 13px; 
        line-height: 1.8; /* SPASI BARIS 1.8 */
        text-decoration: none; 
        transition: 0.3s; 
    }
    .footer-link-text:hover { color: #ff9800; }
    
    /* === LINK MENU (Company: About & Contact) === */
    .footer-link { 
        /* Pakai !important biar gak dipaksa putih sama template */
        color: #aaa !important; 
        
        font-size: 13px; 
        line-height: 1.8; 
        text-decoration: none; 
        transition: color 0.3s ease; 
        display: block; 
    }
    
    /* Saat di-hover tetap jadi Orange */
    .footer-link:hover { 
        color: #ff9800 !important; 
    }
    
    .footer-logo img { max-width: 100%; width: 180px; height: auto; }

    /* === GRID LAYOUT (SUPER RAPAT) === */
    .social-media-grid {
        display: grid;
        grid-template-columns: max-content max-content; 
        gap: 8px 20px; 
        padding-left: 0; margin: 0;
    }

    .social-icon-box { width: 36px; height: 36px; background-color: #ff9800; border-radius: 5px; display: flex; align-items: center; justify-content: center; transition: all 0.3s ease; flex-shrink: 0; }
    .social-icon-box i { color: #ffffff; font-size: 18px; }
    
    .social-link { text-decoration: none; transition: all 0.3s ease; display: flex; align-items: center; }
    .social-link:hover .social-icon-box { background-color: #e68a00; transform: translateY(-2px); }
    .social-link:hover .social-name { color: #ff9800; }
    
    .social-name { color: #ffffff; font-size: 13px; font-weight: 500; margin-left: 10px; white-space: nowrap; }
    .copyright-text { color: #888; font-size: 13px; }
    
    @media (max-width: 768px) {
        .footer-logo img { max-width: 150px; }
        .footer-title { margin-top: 15px; margin-bottom: 10px; }
    }
</style>

<script src="assets/js/jquery-3.3.1.min.js"></script>
<script src="assets/js/theme-change.js"></script>
<script src="assets/js/modernizr.custom.js"></script>
<script src="assets/js/classie.js"></script>
<script src="assets/js/demo1.js"></script>
<script src="assets/js/bootstrap.min.js"></script>

<script>
    // 1. Navbar Sticky & Toggle (INI YANG HILANG KEMARIN)
    $(window).on("scroll", function() {
        var scroll = $(window).scrollTop();
        if (scroll >= 80) {
            $("#site-header").addClass("nav-fixed");
        } else {
            $("#site-header").removeClass("nav-fixed");
        }
    });

    $(".navbar-toggler").on("click", function() {
        $("header").toggleClass("active");
    });
    
    $(document).on("ready", function() {
        if ($(window).width() > 991) {
            $("header").removeClass("active");
        }
        $(window).on("resize", function() {
            if ($(window).width() > 991) {
                $("header").removeClass("active");
            }
        });
    });

    $(function() {
        $('.navbar-toggler').click(function() {
            $('body').toggleClass('noscroll');
        })
    });

    // 2. Tombol Move to Top
    window.onscroll = function() { scrollFunction() };
    function scrollFunction() {
        if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
            document.getElementById("movetop").style.display = "block";
        } else {
            document.getElementById("movetop").style.display = "none";
        }
    }
    function topFunction() {
        document.body.scrollTop = 0;
        document.documentElement.scrollTop = 0;
    }
</script>