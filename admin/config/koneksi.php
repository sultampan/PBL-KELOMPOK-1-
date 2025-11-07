<?php
    date_default_timezone_set("Asia/Jakarta");
    $host = "localhost";
    $port = "5432";
    $user = "postgres";
    $pass = "12345678";
    $db = "LAB_AI";
    $koneksi = pg_connect("host=$host port=$port dbname=$db user=$user password=$pass") or die("Koneksi gagal");

    if (!$koneksi) {
        die("Koneksi gagal: " . pg_last_error());
    }
?>