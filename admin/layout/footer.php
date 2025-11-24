</div>
</div>
</div> <!-- container -->
<script src="assets/js/produk.js"></script>
<div style="font-size: 12px; padding: 10px; text-align: center; color: #7f8c8d; background: #ecf0f1; margin-top: 15px;">
    <?php
    // Pastikan $start_time sudah didefinisikan di admin/index.php
    $end_time = microtime(true);
    $execution_time = ($end_time - $start_time);
    
    echo "Waktu Eksekusi PHP: " . round($execution_time, 4) . " detik";
    ?>
</div>
</body>
</html>
