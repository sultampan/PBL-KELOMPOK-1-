</div> </div> <script src="assets/js/produk.js"></script>

<div style="font-size: 12px; padding: 10px; text-align: center; color: #7f8c8d; background: #ecf0f1; margin-top: 15px;">
    <?php
    $end_time = microtime(true);
    $execution_time = ($end_time - $start_time);
    echo "Waktu Eksekusi PHP: " . round($execution_time, 4) . " detik";
    ?>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {

    const sidebar   = document.getElementById('sidebar');
    const main      = document.getElementById('main');
    const toggleBtn = document.getElementById('toggleSidebar');

    // kalau tombol tidak ditemukan, keluar aman
    if (!toggleBtn) return;

    toggleBtn.addEventListener('click', () => {
        // 1. Toggle collapse (Cukup ini saja!)
        // CSS akan mendeteksi class 'collapsed' dan otomatis memutar ikonnya.
        sidebar.classList.toggle('collapsed');
        main.classList.toggle('collapsed');
        
        // ❌ BAGIAN INI DIHAPUS SAJA BIAR GAK BENTROK SAMA CSS ROTATE
        /* const toggleIcon = toggleBtn.querySelector(".toggle-icon");
        if (toggleIcon) {
            toggleIcon.classList.remove("fa-angle-left", "fa-angle-right");
            toggleIcon.classList.add(collapsed ? "fa-angle-right" : "fa-angle-left");
        }
        */
    });
});
</script>

</body>
</html>