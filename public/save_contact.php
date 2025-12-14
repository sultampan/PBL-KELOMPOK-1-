<?php
// public/save_contact.php

// Set header agar browser tahu ini respon JSON
header('Content-Type: application/json');

require_once '../config/koneksi.php';

// Inisialisasi respon default
$response = [
    'status' => 'error',
    'message' => 'Terjadi kesalahan sistem.'
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama    = trim($_POST['nama_pengirim'] ?? '');
    $email   = trim($_POST['email_pengirim'] ?? '');
    $subjek  = trim($_POST['subjek'] ?? '');
    $pesan   = trim($_POST['pesan'] ?? '');

    if (!empty($nama) && !empty($email) && !empty($pesan)) {
        try {
            $sql = "INSERT INTO contact (nama_pengirim, email_pengirim, subjek, pesan) 
                    VALUES (:nama, :email, :subjek, :pesan)";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':nama'   => $nama,
                ':email'  => $email,
                ':subjek' => $subjek,
                ':pesan'  => $pesan
            ]);

            // Jika berhasil
            $response['status'] = 'success';
            $response['message'] = 'Terima kasih! Pesan Anda telah berhasil dikirim.';

        } catch (PDOException $e) {
            // Jika error database
            $response['message'] = 'Database Error: ' . $e->getMessage();
        }
    } else {
        $response['message'] = 'Harap lengkapi semua kolom yang wajib diisi.';
    }
} else {
    $response['message'] = 'Invalid Request Method.';
}

// Kirim respon JSON ke Javascript
echo json_encode($response);
exit;
?>