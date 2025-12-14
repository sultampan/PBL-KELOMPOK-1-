<footer class="w3l-footer9">
    <section class="footer-inner-main py-5">
        <div class="container py-md-4">
            <div class="row align-items-start">
                
                <!-- LOGO LABORATORY (Kiri) -->
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="footer-logo">
                        <a href="index.php?page=home">
                            <img src="assets/images/logo.png" alt="Applied Informatics Laboratory" style="max-width: 200px; height: auto;">
                        </a>
                    </div>
                </div>

                <!-- LABORATORY (Lokasi) -->
                <div class="col-lg-3 col-md-6 mb-4">
                    <h6 class="footer-title mb-3">Laboratory</h6>
                    <p class="footer-text mb-2">2nd Floor of the Postgraduate Building of Malang State Polytechnic</p>
                    <p class="footer-text mb-0">
                        <a href="mailto:email@labai.polinema.ac.id" style="color: #aaa; text-decoration: none;">
                            email@labai.polinema.ac.id
                        </a>
                    </p>
                </div>

                <!-- COMPANY -->
                <div class="col-lg-2 col-md-6 mb-4">
                    <h6 class="footer-title mb-3">Company</h6>
                    <ul class="footer-list list-unstyled">
                        <li class="mb-2">
                            <a href="index.php?page=about" class="footer-link">About us</a>
                        </li>
                        <li class="mb-2">
                            <a href="index.php?page=contact" class="footer-link">Contact us</a>
                        </li>
                    </ul>
                </div>

                <!-- SOCIAL MEDIA -->
                <div class="col-lg-4 col-md-6 mb-4">
                    <h6 class="footer-title mb-3">Social Media</h6>
                    <ul class="social-media-list list-unstyled">
                        <li class="d-flex align-items-center mb-3">
                            <a href="https://instagram.com/sallysvsta" target="_blank" class="social-link d-flex align-items-center">
                                <div class="social-icon-box">
                                    <i class="fab fa-instagram"></i>
                                </div>
                                <span class="social-name ms-3">Instagram</span>
                            </a>
                        </li>
                        <li class="d-flex align-items-center mb-3">
                            <a href="https://facebook.com/your_facebook" target="_blank" class="social-link d-flex align-items-center">
                                <div class="social-icon-box">
                                    <i class="fab fa-facebook-f"></i>
                                </div>
                                <span class="social-name ms-3">Facebook</span>
                            </a>
                        </li>
                        <li class="d-flex align-items-center mb-3">
                            <a href="https://twitter.com/your_twitter" target="_blank" class="social-link d-flex align-items-center">
                                <div class="social-icon-box">
                                    <i class="fab fa-twitter"></i>
                                </div>
                                <span class="social-name ms-3">Twitter</span>
                            </a>
                        </li>
                        <li class="d-flex align-items-center mb-3">
                            <a href="https://github.com/sallysvsta" target="_blank" class="social-link d-flex align-items-center">
                                <div class="social-icon-box">
                                    <i class="fab fa-github"></i>
                                </div>
                                <span class="social-name ms-3">GitHub</span>
                            </a>
                        </li>
                    </ul>
                </div>

            </div>

            <!-- COPYRIGHT & TERMS -->
            <div class="row mt-4 pt-4" style="border-top: 1px solid #333;">
                <div class="col-md-12">
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <p class="copyright-text mb-0" style="color: #aaa; font-size: 14px;">
                            © 2025 Applied Informatics Laboratory. All rights reserved.
                        </p>
                     
                    </div>
                </div>
            </div>

        </div>
    </section>

    <!-- Move to Top Button -->
    <button onclick="topFunction()" id="movetop" title="Go to top">
        <span class="fas fa-level-up-alt" aria-hidden="true"></span>
    </button>

    <script>
        // When the user scrolls down 20px from the top of the document, show the button
        window.onscroll = function() {
            scrollFunction()
        };

        function scrollFunction() {
            if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
                document.getElementById("movetop").style.display = "block";
            } else {
                document.getElementById("movetop").style.display = "none";
            }
        }

        // When the user clicks on the button, scroll to the top of the document
        function topFunction() {
            document.body.scrollTop = 0;
            document.documentElement.scrollTop = 0;
        }
    </script>
</footer>

<!-- CSS untuk Footer -->
<style>
/* Footer Background */
.w3l-footer9 {
    background-color: #1a1a1a;
    color: #ffffff;
}

.footer-inner-main {
    background-color: #1a1a1a;
}

/* Footer Title */
.footer-title {
    color: #ffffff;
    font-size: 18px;
    font-weight: 600;
    margin-bottom: 20px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* Footer Text */
.footer-text {
    color: #aaa;
    font-size: 14px;
    line-height: 1.8;
}

/* Footer List */
.footer-list li {
    margin-bottom: 10px;
}

.footer-link {
    color: #aaa;
    font-size: 14px;
    text-decoration: none;
    transition: color 0.3s ease;
    display: inline-block;
}

.footer-link:hover {
    color: #ff9800;
}


/* 1. Logo lebih besar dan lebih kiri */
.footer-logo img {
    max-width: 260px !important; 
    transform: translateX(-10px); 
}

/* 2. Social Media — title ditengah */
.col-lg-4 .footer-title {
    text-align: center;
}

/* 3. Social media list*/
.social-media-list {
    padding-left: 40px; 
}

/* 4. Besarkan ikon sosial (lebih mirip contoh putih) */
.social-icon-box {
    width: 50px;
    height: 50px;
}

.social-icon-box i {
    font-size: 22px;
}

.social-name {
    font-size: 16px;
    font-weight: 500;
}

/* 5. Atur jarak antar kolom biar lebih penuh */
.footer-inner-main .row.align-items-start > div {
    margin-bottom: 20px;
}

/* 6. Copyright ke tengah */
.copyright-text {
    width: 100%;
    text-align: center;
}

/* 7. Sesuaikan */
.footer-inner-main .container {
    max-width: 1150px; /* agar komponennya tidak terlalu mepet */
}


/* Social Media Icons - ORANGE KOTAK */
.social-icon-box {
    width: 40px;
    height: 40px;
    background-color: #ff9800;
    border-radius: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
}

.social-icon-box i {
    color: #ffffff;
    font-size: 18px;
}

/* Social Media Link */
.social-link {
    text-decoration: none;
    transition: all 0.3s ease;
}

.social-link:hover .social-icon-box {
    background-color: #ff9800; /* TETAP ORANGE saat hover */
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(255, 152, 0, 0.3);
}

.social-name {
    color: #ffffff;
    font-size: 15px;
    font-weight: 500;
}

.social-link:hover .social-name {
    color: #ff9800;
}

/* Copyright & Bottom Links */
.copyright-text {
    color: #888;
}

.footer-bottom-link {
    color: #888;
    transition: color 0.3s ease;
}

.footer-bottom-link:hover {
    color: #ff9800;
}

/* Responsive */
@media (max-width: 768px) {
    .footer-logo img {
        max-width: 150px;
    }
    
    .d-flex.justify-content-between {
        flex-direction: column;
        text-align: center;
    }
    
    .footer-bottom-links {
        margin-top: 10px;
    }
}
</style>

<!-- Template JavaScript -->
<script src="assets/js/jquery-3.3.1.min.js"></script>
<script src="assets/js/theme-change.js"></script>
<script src="assets/js/modernizr.custom.js"></script>
<script src="assets/js/classie.js"></script>
<script src="assets/js/demo1.js"></script>

<script>
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
</script>

<script src="assets/js/bootstrap.min.js"></script>

</body>
</html>