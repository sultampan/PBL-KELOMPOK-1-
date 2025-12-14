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

</style>

<section class="inner-banner activity-banner">
    <div>
        <h2>About</h2>
        <div class="breadcrumb-activity">
            <a href="index.php">About</a>
            <span>›</span>
            <span>About</span>
        </div>
    </div>
</section>

<section class="w3l-about-breadcrumb">
    <div class="breadcrumb-bg breadcrumb-bg-about py-sm-5 py-4">
        <div class="container py-2">
            <h2 class="title">About the Applied Informatics Laboratory</h2>
            <p class="mt-2 text-white">Politeknik Negeri Malang</p>
        </div>
    </div>
</section>

<!-- ABOUT SECTION -->
<section class="w3l-features py-5" id="about-lab">
    <div class="container py-md-5 py-4">
        <div class="row align-items-center">
            <div class="col-lg-6 pe-lg-5">
                <h6 class="title-subw3hny mb-1">Our Profile</h6>
                <h3 class="title-w3l">Applied Informatics Laboratory</h3>
                <p class="mt-3">
                    The Applied Informatics Laboratory at Politeknik Negeri Malang serves as a center for research,
                    innovation, and hands-on development in applied information technology. 
                    The lab supports academic activities, industry collaboration, and technology-based solutions 
                    in artificial intelligence, cybersecurity, software engineering, and data-driven systems.
                </p>
                <p class="mt-3">
                    Through interdisciplinary collaboration, our laboratory aims to contribute to digital transformation, 
                    support student competencies, and produce impactful solutions for real-world needs.
                </p>
            </div>
            <div class="col-lg-6">
                <img src="assets/images/about.jpg" class="img-fluid radius-image" alt="">
            </div>
        </div>
    </div>
</section>

<!-- VISION & MISSION -->
<section class="w3l-servicesblock py-5" id="vision-mission">
    <div class="container py-md-5 py-4">
        <div class="title-content text-center mb-5">
            <h6 class="title-subw3hny">Our Foundation</h6>
            <h3 class="title-w3l">Vision & Mission</h3>
        </div>

        <div class="row justify-content-center align-items-stretch">
            
            <!-- VISION -->
            <div class="col-lg-6 mb-4 d-flex">
                <div class="p-4 shadow radius-image h-100 d-flex flex-column" style="background: #fafafa;">
                    <h4 class="mb-3">Vision</h4>
                    <p>
                        To become an innovative and impactful research laboratory in applied informatics,
                        supporting education, industry, and community development.
                    </p>
                </div>
            </div>

            <!-- MISSION -->
            <div class="col-lg-6 mb-4 d-flex">
                <div class="p-4 shadow radius-image h-100 d-flex flex-column" style="background: #fafafa;">
                    <h4 class="mb-3">Mission</h4>
                    <ul style="line-height: 1.9;">
                        <li>Conduct applied research in artificial intelligence, cybersecurity, and information systems.</li>
                        <li>Develop practical digital solutions that support industrial and community needs.</li>
                        <li>Support student competency through real-world projects, internships, and collaboration.</li>
                        <li>Promote innovation and interdisciplinary technology development.</li>
                    </ul>
                </div>
            </div>

        </div>
    </div>
</section>


<!-- RESEARCH AREAS -->
<section class="w3l-services py-5" id="research">
    <div class="container py-md-5 py-4">
        <div class="title-content text-center">
            <h6 class="title-subw3hny mb-1">Research Focus</h6>
            <h3 class="title-w3l mb-5">Our Research Areas</h3>
        </div>

        <div class="row text-center align-items-stretch">

            <div class="col-lg-3 col-md-6 mb-4 d-flex">
                <div class="service-box p-4 shadow-sm radius-image h-100 d-flex flex-column">
                    <i class="fas fa-robot fa-3x mb-3"></i>
                    <h5>Artificial Intelligence</h5>
                    <p>Machine learning, computer vision, intelligent systems, and automation research.</p>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-4 d-flex">
                <div class="service-box p-4 shadow-sm radius-image h-100 d-flex flex-column">
                    <i class="fas fa-shield-alt fa-3x mb-3"></i>
                    <h5>Cybersecurity</h5>
                    <p>Security assessment, penetration testing, and digital threat mitigation solutions.</p>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-4 d-flex">
                <div class="service-box p-4 shadow-sm radius-image h-100 d-flex flex-column">
                    <i class="fas fa-code fa-3x mb-3"></i>
                    <h5>Software Engineering</h5>
                    <p>Application development, system design, and applied informatics solutions.</p>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-4 d-flex">
                <div class="service-box p-4 shadow-sm radius-image h-100 d-flex flex-column">
                    <i class="fas fa-database fa-3x mb-3"></i>
                    <h5>Data Science</h5>
                    <p>Data analytics, visualization, and applied statistical modeling.</p>
                </div>
            </div>

        </div>
    </div>
</section>


<!-- FACILITIES -->
<section class="w3l-features py-5" id="facilities">
    <div class="container py-md-5 py-4">
        <div class="title-content text-center mb-5">
            <h6 class="title-subw3hny">Our Facilities</h6>
            <h3 class="title-w3l">Supporting Your Innovation</h3>
        </div>

        <div class="row align-items-stretch">

            <div class="col-lg-4 col-md-6 mb-4 d-flex">
                <div class="feature-box p-4 shadow radius-image h-100 d-flex flex-column">
                    <h5>High-Performance Workstations</h5>
                    <p>Powerful computers equipped for AI training, security testing, and development work.</p>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 mb-4 d-flex">
                <div class="feature-box p-4 shadow radius-image h-100 d-flex flex-column">
                    <h5>AI & IoT Development Kits</h5>
                    <p>Tools and devices for machine learning, robotics, and smart agriculture projects.</p>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 mb-4 d-flex">
                <div class="feature-box p-4 shadow radius-image h-100 d-flex flex-column">
                    <h5>Collaboration & Meeting Space</h5>
                    <p>Comfortable shared area for teamwork, presentations, and industry discussions.</p>
                </div>
            </div>

        </div>
    </div>
</section>


<!-- TEAM -->
<section class="w3l-team py-5" id="team">
    <div class="container py-md-5 py-4">
        <div class="title-content text-center mb-5">
            <h6 class="title-subw3hny">Meet the Team</h6>
            <h3 class="title-w3l">Our Laboratory Staff</h3>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="team-member text-center p-4 shadow radius-image">
                    <img src="assets/images/tim1.jpg" class="img-fluid radius-image mb-3" alt="">
                    <h5>Ir. Yan Watequlis Syaifudin, S.T., M.MT., Ph.D</h5>
                    <p>Head of Laboratory</p>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-4">
                <div class="team-member text-center p-4 shadow radius-image">
                    <img src="assets/images/tim2.jpg" class="img-fluid radius-image mb-3" alt="">
                    <h5>Ir. Yan Watequlis Syaifudin, S.T., M.MT., Ph.D</h5>
                    <p>Member Lab</p>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-4">
                <div class="team-member text-center p-4 shadow radius-image">
                    <img src="assets/images/tim3.jpg" class="img-fluid radius-image mb-3" alt="">
                    <h5>Ir. Yan Watequlis Syaifudin, S.T., M.MT., Ph.D</h5>
                    <p>Member Lab</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CONTACT -->
<section class="w3l-join-main py-5">
    <div class="container py-md-5">
        <div class="row">
            <div class="col-lg-6">
                <h5 class="title-subw3hny mb-2">Contact Us</h5>
                <h3 class="title-w3l mb-4">Stay Connected</h3>
                <p>
                    For collaboration, research opportunities, or inquiries about laboratory activities,
                    feel free to reach out to us.
                </p>
                <ul class="list-unstyled mt-3">
                    <li><strong>Email:</strong> lab.informatics@polinema.ac.id</li>
                    <li><strong>Location:</strong> ICT Building, Politeknik Negeri Malang</li>
                    <li><strong>Phone:</strong> +62 812-3456-7890</li>
                </ul>
            </div>

            <div class="col-lg-6 mt-lg-0 mt-4">
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3951.398494638768!2d112.613297!3d-7.958315!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2dd628315cb1ffb7%3A0xdea9da6afdd1fb26!2sPoliteknik%20Negeri%20Malang!5e0!3m2!1sen!2sid!4v1632890177262!5m2!1sen!2sid"
                    width="100%" height="300" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
            </div>
        </div>
    </div>
</section>
