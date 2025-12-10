<?php
require_once __DIR__ . '/../../config/koneksi.php';
?>
<section class="py-5">
    <div class="container py-md-5 py-3">
        <h3 class="title-w3l mb-4">Our Products</h3>

        <div class="row">

        <?php
        try {
            $stmt = $pdo->query("SELECT * FROM produk ORDER BY id_produk DESC");
            $produk = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (empty($produk)) {
                echo "<p>No products available at the moment.</p>";
            } else {
                foreach ($produk as $p): ?>

                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="card h-100 shadow-sm">
                            <img src="/PBL-KELOMPOK-1-/public/uploads/produk/<?= $p['gambar'] ?>" 
                                 class="card-img-top" 
                                 alt="<?= htmlspecialchars($p['nama']) ?>"
                                 style="height: 220px; object-fit: cover;">

                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($p['nama']) ?></h5>
                                <p class="card-text">
                                    <?= nl2br(htmlspecialchars(substr($p['deskripsi'], 0, 120))) ?>...
                                </p>
                            </div>
                        </div>
                    </div>

                <?php endforeach;
            }
        } catch (PDOException $e) {
            echo "<p>Error loading products: " . $e->getMessage() . "</p>";
        }
        ?>

        </div>
    </div>
</section>
