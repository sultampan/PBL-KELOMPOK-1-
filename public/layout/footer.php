<footer class="w3l-footer9">
        <section class="footer-inner-main py-5">
            <div class="container py-md-3">
                <div class="right-side">
                    <div class="row footer-hny-grids sub-columns">
                        <div class="col-lg-3 sub-one-left">
                            <h6>Laboratory </h6>
                            <p class="footer-phny pe-lg-5">2nd Floor of the Postgraduate Building of Malang State Polytechnic.</p>
                            <div class="columns-2 mt-lg-5 mt-4">
                                <ul class="social">
                                    <li><a href="#facebook"><span class="fab fa-facebook-f"></span></a>
                                    </li>
                                    <li><a href="#linkedin"><span class="fab fa-linkedin-in"></span></a>
                                    </li>
                                    <li><a href="#twitter"><span class="fab fa-twitter"></span></a>
                                    </li>
                                    <li><a href="#google"><span class="fab fa-google-plus-g"></span></a>
                                    </li>

                                </ul>
                            </div>
                        </div>
                        <div class="col-lg-3 sub-two-right">
                            <h6>Company</h6>
                            <ul>
                                <li><a href="#processing"><i class="fas fa-angle-right"></i> Agriculture Processing</a>
                                </li>
                                <li><a href="#research"><i class="fas fa-angle-right"></i> Chemical Research</a>
                                </li>
                                <li><a href="#metal"><i class="fas fa-angle-right"></i> Metal Engineering</a>
                                </li>
                                <li><a href="#gas"><i class="fas fa-angle-right"></i> Petroleum & Gas</a>
                                </li>
                                <li><a href="#work"><i class="fas fa-angle-right"></i> Mechanical Engineering</a></li>


                            </ul>
                        </div>
                        <div class="col-lg-3 sub-two-right">
                            <h6>Our Links</h6>
                            <ul>

                                <li><a href="#why"><i class="fas fa-angle-right"></i> Why us</a>
                                </li>
                                <li><a href="#licence"><i class="fas fa-angle-right"></i> Licensing
                                    </a>
                                </li>
                                <li><a href="#log"><i class="fas fa-angle-right"></i> Offers
                                    </a></li>
                                <li><a href="#log"><i class="fas fa-angle-right"></i> Changelog
                                    </a></li>
                                <li><a href="#career"><i class="fas fa-angle-right"></i> Careers</a></li>

                            </ul>
                        </div>
                        <div class="col-lg-3 sub-one-left">
                            <h6>Recent Posts </h6>
                            <div class="row fposts-grid-inner mb-4">
                                <div class="col-4 fposts-grid-left ps-0">
                                    <a href="blog-single.html">
                                        <img src="assets/images/g2.jpg" alt=" " class="img-fluid radius-image">
                                    </a>
                                </div>
                                <div class="col-8 fposts-grid-right">
                                    <h4>
                                        <a href="#home" class="text-bl text-left">Lorem ipsum viverra feugiat libero.</a>
                                    </h4>
                                    <p class="time"> 11 Minutes ago</p>
                                </div>
                            </div>
                            <div class="row fposts-grid-inner mb-4">
                                <div class="col-4 fposts-grid-left ps-0">
                                    <a href="blog-single.html">
                                        <img src="assets/images/g3.jpg" alt=" " class="img-fluid radius-image">
                                    </a>
                                </div>
                                <div class="col-8 fposts-grid-right">
                                    <h4>
                                        <a href="#home" class="text-bl text-left">Lorem ipsum viverra feugiat libero.</a>
                                    </h4>
                                    <p class="time"> 11 Minutes ago</p>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="below-section mt-5">
                    <div class="copyright-footer">
                        <div class="columns text-left">
                            
                        </div>
                        <ul class="footer-w3list text-right">
                            <li><a href="#url">Privacy Policy</a>
                            </li>
                            <li><a href="#url">Terms &amp; Conditions</a>
                            </li>
                        </ul>
                    </div>

                </div>
            </div>
        </section>

        <!-- Js scripts -->
        <!-- move top -->
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
        <!-- //move top -->
    </footer>
    <!--//footer-9 -->

    <!-- Template JavaScript -->
    <script src="assets/js/jquery-3.3.1.min.js"></script>
    <script src="assets/js/theme-change.js"></script>
    <!--/search-->
    <script src="assets/js/modernizr.custom.js"></script>
    <script src="assets/js/classie.js"></script>
    <script src="assets/js/demo1.js"></script>
    <!--//search-->
    <!-- MENU-JS -->
    <script>
        $(window).on("scroll", function() {
            var scroll = $(window).scrollTop();

            if (scroll >= 80) {
                $("#site-header").addClass("nav-fixed");
            } else {
                $("#site-header").removeClass("nav-fixed");
            }
        });

        //Main navigation Active Class Add Remove
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

    </script>
    <!-- //MENU-JS -->

    <!-- disable body scroll which navbar is in active -->
    <script>
        $(function() {
            $('.navbar-toggler').click(function() {
                $('body').toggleClass('noscroll');
            })
        });

    </script>
    <!-- //disable body scroll which navbar is in active -->
    <!-- //bootstrap -->
    <script src="assets/js/bootstrap.min.js"></script>

</body>

</html>
