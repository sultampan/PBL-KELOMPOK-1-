<?php
$page = $_GET['page'] ?? 'home';
include 'layout/header.php';
include 'layout/navbar.php';


if (file_exists("pages/$page.php")) {
    include "pages/$page.php";
} else {
    echo "<h1>404 - Page Not Found</h1>";
}

include 'layout/footer.php';
?>
