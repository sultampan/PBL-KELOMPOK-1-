<footer class="lab-footer">

    <style>
        .lab-footer {
            width: 100%;
            background: #fff;
            padding: 50px 0;
            border-top: 1px solid #e6e6e6;
            font-family: Arial, sans-serif;
        }

        .footer-container {
            display: flex;
            justify-content: space-around; /* supaya melebar */
            align-items: flex-start;
            width: 100%; /* full width */
            padding: 0 60px; /* jarak kiri kanan */
            gap: 60px; /* jarak antar kolom */
            flex-wrap: wrap; /* responsif di HP */
        }

        .footer-col {
            flex: 1;
            min-width: 250px; /* agar tetap proporsional */
        }

        .footer-logo {
            width: 180px;
            margin-bottom: 18px;
        }

        .footer-title {
            font-weight: 700;
            margin-bottom: 12px;
            font-size: 18px;
        }

        .footer-menu li,
        .footer-social li {
            list-style: none;
            margin-bottom: 8px;
            font-size: 16px;
        }

        .footer-menu a {
            text-decoration: none;
            color: #0062cc;
        }

        .footer-menu a:hover {
            text-decoration: underline;
        }

        .footer-social i {
            margin-right: 8px;
        }

        .footer-bottom {
            text-align: center;
            padding-top: 20px;
            margin-top: 30px;
            border-top: 1px solid #ddd;
            font-size: 15px;
            color: #666;
        }
    </style>

    <div class="footer-container">

        <!-- Column 1 -->
        <div class="footer-col">
            <img src="assets/images/logo.png" class="footer-logo" alt="">
            <h5 class="footer-title">Laboratory</h5>
            <p>2nd Floor of the Postgraduate Building of<br>Malang State Polytechnic</p>
            <p>email@labai.polinema.ac.id</p>
        </div>

        <!-- Column 2 -->
        <div class="footer-col">
            <h5 class="footer-title">Company</h5>
            <ul class="footer-menu">
                <li><a href="?page=about">About us</a></li>
                <li><a href="?page=contact">Contact us</a></li>
            </ul>
        </div>

        <!-- Column 3 -->
        <div class="footer-col">
            <h5 class="footer-title">Social Media</h5>
            <ul class="footer-social">
                <li><i class="fab fa-instagram"></i> Instagram</li>
                <li><i class="fab fa-facebook"></i> Facebook</li>
                <li><i class="fab fa-twitter"></i> Twitter</li>
                <li><i class="fab fa-github"></i> GitHub</li>
            </ul>
        </div>

    </div>

    <div class="footer-bottom">
        © 2025 Laboratory for Applied Informatics — All Rights Reserved
    </div>
</footer>
