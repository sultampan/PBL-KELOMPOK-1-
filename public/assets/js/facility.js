/* public/assets/js/facility.js */

document.addEventListener("DOMContentLoaded", function() {
    
    // 1. Definisikan fungsi init secara global (window) 
    // agar bisa dipanggil ulang oleh script di file PHP saat live search berjalan
    window.initChocolat = function() {
        if (typeof $ !== 'undefined' && $.fn.Chocolat) {
            $('.chocolat-image').Chocolat({
                imageSize: 'contain',
                loop: true,
                overlayOpacity: 0.9, // Kegelapan latar belakang
                closeImg: '',        // Biarkan default css
            });
        }
    };

    // 2. Jalankan inisialisasi pertama kali saat halaman dimuat
    window.initChocolat();

    // 3. Logika Khusus: Tutup popup saat area luar (overlay) diklik
    // Kita pasang di 'body' agar elemen dinamis (hasil search) tetap kena efek ini
    if (typeof $ !== 'undefined') {
        $('body').on('click', '.chocolat-overlay, #Choco_overlay', function() {
            // Cari tombol close (bisa berupa class atau ID tergantung versi plugin) lalu klik otomatis
            var closeBtn = $('.chocolat-close, #Choco_close');
            if (closeBtn.length) {
                closeBtn.trigger('click');
            }
        });
    }
});