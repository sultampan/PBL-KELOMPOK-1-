<?php
require_once __DIR__ . '/../../config/koneksi.php';

try {
    // Ambil Head of Lab
    $headStmt = $pdo->query("SELECT * FROM member WHERE jabatan = 'Head of Laboratory' LIMIT 1");
    $head = $headStmt->fetch(PDO::FETCH_ASSOC);

    // Ambil Member lain
    $memberStmt = $pdo->query("SELECT * FROM member WHERE jabatan != 'Head of Laboratory' ORDER BY id_member ASC");
    $members = $memberStmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "<p>Error loading members: " . $e->getMessage() . "</p>";
}
?>

<section class="py-5">
    <div class="container py-md-5 py-3">

        <h2 class="text-center fw-bold mb-5" style="font-size: 38px;">
            Member of Laboratory
        </h2>

        <!-- ==========================
             HEAD OF LABORATORY
        ============================ -->
        <?php if ($head): ?>
        <div class="text-center mb-5 pb-5"
             style="border-bottom: 2px solid #ddd;">

            <img src="/PBL-KELOMPOK-1-/public/uploads/member/<?= $head['gambar'] ?>"
                 alt="<?= htmlspecialchars($head['nama_member']) ?>"
                 class="shadow mb-4"
                 style="width: 300px; height: 360px; object-fit: cover; border-radius: 12px;">

            <h3 class="fw-bold" style="font-size: 32px;">
                <?= htmlspecialchars($head['nama_member']) ?>
            </h3>

            <h5 class="fw-semibold mb-3"
                style="font-size: 22px; color: #0056c7;">
                <?= htmlspecialchars($head['jabatan']) ?>
            </h5>

            <p style="font-size: 18px; text-align: justify; max-width: 850px; margin: auto;">
                <?= nl2br(htmlspecialchars($head['deskripsi'])) ?>
            </p>

            <!-- SOCIAL ICONS -->
            <div class="mt-4 d-flex justify-content-center gap-4">

                <?php if (!empty($head['link_google_scholar'])): ?>
                <a href="<?= $head['link_google_scholar'] ?>" target="_blank">
                    <img src="/PBL-KELOMPOK-1-/public/assets/icons/scholar.png" style="width: 40px;">
                </a>
                <?php endif; ?>

                <?php if (!empty($head['link_orcid'])): ?>
                <a href="<?= $head['link_orcid'] ?>" target="_blank">
                    <img src="/PBL-KELOMPOK-1-/public/assets/icons/orcid.png" style="width: 40px;">
                </a>
                <?php endif; ?>

                <?php if (!empty($head['link_sinta'])): ?>
                <a href="<?= $head['link_sinta'] ?>" target="_blank">
                    <img src="/PBL-KELOMPOK-1-/public/assets/icons/sinta.png" style="width: 40px;">
                </a>
                <?php endif; ?>

            </div>

        </div>
        <?php endif; ?>

        <!-- ===================================================
             MEMBER LAINNYA (BAGIAN BAWAH)
        ===================================================== -->
        <?php foreach ($members as $m): ?>
        <div class="row align-items-center mb-5 pb-5"
            style="border-bottom: 1px solid #eee;">

            <!-- FOTO -->
            <div class="col-md-4 text-center mb-4">
                <img src="/PBL-KELOMPOK-1-/public/uploads/member/<?= $m['gambar'] ?>"
                     class="shadow"
                     style="width: 260px; height: 320px; object-fit: cover; border-radius: 10px;">
            </div>

            <!-- INFORMASI -->
            <div class="col-md-8">
                <h3 class="fw-bold" style="font-size: 28px;">
                    <?= htmlspecialchars($m['nama_member']) ?>
                </h3>

                <h5 class="fw-semibold mb-3" style="font-size: 20px; color:#444;">
                    <?= htmlspecialchars($m['jabatan']) ?>
                </h5>

                <p style="font-size: 18px; text-align: justify;">
                    <?= nl2br(htmlspecialchars($m['deskripsi'])) ?>
                </p>

                <div class="mt-3 d-flex align-items-center gap-4">

                    <?php if (!empty($m['link_google_scholar'])): ?>
                    <a href="<?= $m['link_google_scholar'] ?>" target="_blank">
                        <img src="/PBL-KELOMPOK-1-/public/assets/icons/scholar.png"
                             style="width: 32px;">
                    </a>
                    <?php endif; ?>

                    <?php if (!empty($m['link_orcid'])): ?>
                    <a href="<?= $m['link_orcid'] ?>" target="_blank">
                        <img src="/PBL-KELOMPOK-1-/public/assets/icons/orcid.png"
                             style="width: 32px;">
                    </a>
                    <?php endif; ?>

                    <?php if (!empty($m['link_sinta'])): ?>
                    <a href="<?= $m['link_sinta'] ?>" target="_blank">
                        <img src="/PBL-KELOMPOK-1-/public/assets/icons/sinta.png"
                             style="width: 32px;">
                    </a>
                    <?php endif; ?>

                </div>
            </div>
        </div>
        <?php endforeach; ?>

    </div>
</section>
