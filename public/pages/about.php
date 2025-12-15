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
