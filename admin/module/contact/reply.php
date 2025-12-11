<?php
// admin/module/contact/reply.php

require 'module/contact/PHPMailer/Exception.php';
require 'module/contact/PHPMailer/PHPMailer.php';
require 'module/contact/PHPMailer/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$id = $_GET['id'] ?? 0;

// =================================================================
// PROSES KIRIM (TANPA ENKRIPSI)
// =================================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $balasan      = $_POST['balasan'];
    $idContact    = $_POST['id_contact'];
    $emailTujuan  = $_POST['email_pengirim'];
    $namaTujuan   = $_POST['nama_pengirim'];
    $subjekAsal   = $_POST['subjek_asal'];
    $idAdmin      = $_SESSION['id_admin'] ?? 1;

    try {
        // 1. AMBIL KREDENSIAL DARI DB
        $stmtAdmin = $pdo->prepare("SELECT email, email_password FROM admin WHERE id_admin = :id");
        $stmtAdmin->execute([':id' => $idAdmin]);
        $adminData = $stmtAdmin->fetch(PDO::FETCH_ASSOC);

        if (!$adminData || empty($adminData['email']) || empty($adminData['email_password'])) {
            throw new Exception("Email pengirim belum diatur di Database Admin.");
        }

        $senderEmail = $adminData['email'];
        $senderPass  = $adminData['email_password'];

        // 2. SIMPAN BALASAN KE DB
        $stmt = $pdo->prepare("INSERT INTO contact_reply (id_contact, id_admin, balasan) VALUES (:idc, :ida, :bls)");
        $stmt->execute([':idc' => $idContact, ':ida' => $idAdmin, ':bls' => $balasan]);

        // 3. UPDATE STATUS
        $upd = $pdo->prepare("UPDATE contact SET status = 'replied'::contact_status WHERE id_contact = :idc");
        $upd->execute([':idc' => $idContact]);

        // 4. KIRIM EMAIL
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = $senderEmail; 
        $mail->Password   = $senderPass; 
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->setFrom($senderEmail, 'Admin Lab AI'); 
        $mail->addAddress($emailTujuan, $namaTujuan);

        $mail->isHTML(true);
        $mail->Subject = 'Balasan: ' . $subjekAsal;
        
        $bodyContent = "
            <div style='font-family: Arial, sans-serif; line-height: 1.6; color: #333;'>
                <p>Halo <b>$namaTujuan</b>,</p>
                <p>Terima kasih telah menghubungi kami. Berikut adalah balasan untuk pesan Anda:</p>
                <div style='background:#f9f9f9; padding:15px; border-left:4px solid #01B5B8; margin: 15px 0; border-radius: 4px;'>
                    " . nl2br(htmlspecialchars($balasan)) . "
                </div>
                <br>
                <p>Salam Hangat,<br><b>Applied Informatics Laboratory</b></p>
            </div>
        ";
        
        $mail->Body = $bodyContent;
        $mail->AltBody = strip_tags($balasan);

        $mail->send();

        // --- PERUBAHAN DISINI (REDIRECT LANGSUNG) ---
        // Kita kirim parameter ?status=success ke index.php
        header("Location: index.php?page=contact&status=success");
        exit;

    } catch (Exception $e) {
        $msg = (isset($mail)) ? $mail->ErrorInfo : $e->getMessage();
        // Redirect dengan pesan error
        header("Location: index.php?page=contact&status=error&msg=" . urlencode($msg));
        exit;
    } catch (PDOException $e) {
        header("Location: index.php?page=contact&status=error&msg=" . urlencode($e->getMessage()));
        exit;
    }
}

// ... Sisa kode HTML form di bawah tetap sama ...
// (Bagian SELECT data pesan dan form HTML biarkan saja seperti sebelumnya)
// Hanya saja tambahkan kode ini jika ingin memastikan data terambil:

$stmt = $pdo->prepare("SELECT * FROM contact WHERE id_contact = :id");
$stmt->execute([':id' => $id]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$data) exit("<div class='alert warning'>Data pesan tidak ditemukan.</div>");

if ($data['status'] == 'pending') {
    $pdo->prepare("UPDATE contact SET status = 'read'::contact_status WHERE id_contact = ?")->execute([$id]);
}
?>

<link rel="stylesheet" href="assets/css/forms.css">
<link rel="stylesheet" href="assets/css/components.css">

<div class="card">
    <div class="card-header-flex">
        <h3>Balas Pesan</h3>
        <a href="index.php?page=contact" class="btn-reset" style="text-decoration:none; padding: 8px 15px; border:1px solid #ccc;">
            <i class="fas fa-arrow-left"></i> 
        </a>
    </div>

    <div class="card-body">
        <div class="reply-info-box">
            <div class="reply-info-title">
                <i class="fas fa-user-circle"></i> Pesan dari Pengunjung
            </div>
            <table class="info-table">
                <tr><td class="label">Nama</td><td class="value">: <?= htmlspecialchars($data['nama_pengirim']) ?></td></tr>
                <tr><td class="label">Email</td><td class="value">: <?= htmlspecialchars($data['email_pengirim']) ?></td></tr>
                <tr><td class="label">Subjek</td><td class="value">: <?= htmlspecialchars($data['subjek']) ?></td></tr>
                <tr>
                    <td class="label">Pesan</td>
                    <td class="value">
                        <div class="message-box"><?= nl2br(htmlspecialchars($data['pesan'])) ?></div>
                    </td>
                </tr>
            </table>
        </div>

        <form method="POST" class="form-grid">
            <input type="hidden" name="id_contact" value="<?= $data['id_contact'] ?>">
            <input type="hidden" name="email_pengirim" value="<?= $data['email_pengirim'] ?>">
            <input type="hidden" name="nama_pengirim" value="<?= $data['nama_pengirim'] ?>">
            <input type="hidden" name="subjek_asal" value="<?= $data['subjek'] ?>">

            <div>
                <label class="form-label">Isi Balasan:</label>
                <textarea name="balasan" class="form-control" rows="8" required placeholder="Tulis balasan..."></textarea>
            </div>

            <div class="button-group">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-paper-plane"></i> Kirim Balasan
                </button>
            </div>
        </form>
    </div>
</div>