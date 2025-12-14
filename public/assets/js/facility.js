/* public/assets/js/facility.js */

document.addEventListener("DOMContentLoaded", function() {
    // Pastikan jQuery & Plugin Chocolat sudah di-load
    if (typeof $ !== 'undefined' && $.fn.Chocolat) {
        $('.chocolat-image').Chocolat({
            // Opsi ini WAJIB ada agar tombol close muncul lewat CSS kita
            imageSize: 'contain', // Agar gambar tidak terlalu zoom in
            loop: true,           // Bisa geser kanan terus
            overlayOpacity: 0.9,  // Latar belakang gelap 90%
            closeImg: '',         // PENTING: Kosongkan ini biar tidak cari file close.gif
            leftImg: '',          // Kosongkan juga icon panah bawaan (opsional)
            rightImg: ''          // Kosongkan juga icon panah bawaan (opsional)
        });
    }
});