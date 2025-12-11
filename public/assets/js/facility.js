/* public/assets/js/facility.js */

document.addEventListener("DOMContentLoaded", function() {
    // Pastikan jQuery & Plugin Chocolat sudah di-load di layout utama (header/footer)
    if (typeof $ !== 'undefined' && $.fn.Chocolat) {
        $('.chocolat-image').Chocolat();
    }
});