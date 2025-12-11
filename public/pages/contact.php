<section class="w3l-contact-main" id="contact">
    <div class="contact-infhny py-5 pb-0">
        <div class="container py-lg-3 pb-0">
            <div class="top-map">
                <div class="map-content-9">
                    
                    <form id="contactForm" action="save_contact.php" method="post">
                        <div class="form-top1">
                            <div class="w3header-section text-center">
                                <h6 class="title-subw3hny">Get In Touch </h6>
                                <h3 class="title-w3l mb-0">Fill the form and send your query</h3>
                                <p class="mb-lg-5 mb-4 text-center">We have made it easy for clients to reach us and get their solutions weaved</p>
                            </div>

                            <div id="formMessage" style="display:none; margin-bottom: 20px; text-align: center; padding: 15px; border-radius: 4px;"></div>

                            <div class="form-top">
                                <div class="form-top-left">
                                    <input type="text" name="nama_pengirim" id="w3lName" placeholder="Name" required="">
                                    <input type="email" name="email_pengirim" id="w3lSender" placeholder="Email*" required="">
                                    <input type="text" name="subjek" id="w3lSubject" placeholder="Subject" required="">
                                </div>
                                <div class="form-top-righ">
                                    <textarea name="pesan" id="w3lMessage" placeholder="Message*" required=""></textarea>
                                </div>
                            </div>
                            
                            <div class="text-lg-right text-center">
                                <button type="submit" id="btnSubmit" class="btn btn-style btn-primary">
                                    <span id="btnText">Submit Now</span> 
                                    <i class="fas fa-paper-plane ms-2"></i>
                                </button>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const contactForm = document.getElementById('contactForm');
    const msgBox = document.getElementById('formMessage');
    const btnSubmit = document.getElementById('btnSubmit');
    const btnText = document.getElementById('btnText');

    contactForm.addEventListener('submit', function(e) {
        e.preventDefault(); // 1. Mencegah form reload halaman

        // 2. Ubah tombol jadi Loading
        const originalText = btnText.innerText;
        btnText.innerText = 'Sending...';
        btnSubmit.disabled = true;
        msgBox.style.display = 'none'; // Sembunyikan pesan lama

        // 3. Ambil data form
        const formData = new FormData(contactForm);

        // 4. Kirim via Fetch (AJAX)
        fetch('save_contact.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json()) // Ubah respon jadi JSON
        .then(data => {
            // 5. Tampilkan Pesan
            msgBox.style.display = 'block';
            
            if (data.status === 'success') {
                // Style Sukses
                msgBox.style.backgroundColor = '#d4edda';
                msgBox.style.color = '#155724';
                msgBox.style.border = '1px solid #c3e6cb';
                msgBox.innerHTML = `<strong>Success!</strong> ${data.message}`;
                
                // Reset form jika sukses
                contactForm.reset();
            } else {
                // Style Error
                msgBox.style.backgroundColor = '#f8d7da';
                msgBox.style.color = '#721c24';
                msgBox.style.border = '1px solid #f5c6cb';
                msgBox.innerHTML = `<strong>Error!</strong> ${data.message}`;
            }
        })
        .catch(error => {
            // Error Jaringan / Server 500
            msgBox.style.display = 'block';
            msgBox.style.backgroundColor = '#fff3cd';
            msgBox.style.color = '#856404';
            msgBox.style.border = '1px solid #ffeeba';
            msgBox.innerHTML = `<strong>Connection Error!</strong> Gagal menghubungi server.`;
            console.error('Error:', error);
        })
        .finally(() => {
            // 6. Kembalikan tombol seperti semula
            btnText.innerText = originalText;
            btnSubmit.disabled = false;
        });
    });
});
</script>