<?php
require_once __DIR__ . '/../../../config/koneksi.php';

if (!isset($_GET['id'])) exit("ID tidak ditemukan.");
$id = $_GET['id'];

$del = $pdo->prepare("DELETE FROM contact WHERE id = ?");
$del->execute([$id]);

echo "<script>alert('Data berhasil dihapus!');window.location='index.php?page=contact';</script>";
