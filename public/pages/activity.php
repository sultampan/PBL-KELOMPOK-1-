<style>
    /* --- BANNER ACTIVITY (SAMA DENGAN FACILITY) --- */
.inner-banner.activity-banner {
    background: url('assets/images/header-facility.jpeg') no-repeat center;
    background-size: cover;
    position: relative;
    z-index: 0;
    min-height: 350px;
    display: grid;
    align-items: center;
    justify-content: center;
    text-align: center;
}

/* Overlay Gelap */
.inner-banner.activity-banner:before {
    content: "";
    background: rgba(0, 0, 0, 0.6);
    position: absolute;
    inset: 0;
    z-index: -1;
}

.inner-banner.activity-banner h2 {
    font-size: 3rem;
    font-weight: 700;
    color: white;
    margin-bottom: 10px;
}

/* Breadcrumb */
.breadcrumb-activity {
    color: white;
    font-size: 1rem;
}
.breadcrumb-activity a {
    color: #ffb400;
    text-decoration: none;
}

.breadcrumb-activity span {
    margin: 0 5px;
}


.activity-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 30px;
    margin-top: 2rem;
}

.activity-card {
    background: white;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.activity-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
}

.activity-image-wrapper {
    position: relative;
    width: 100%;
    height: 250px;
    overflow: hidden;
    background-color: #e0e0e0;
}

.activity-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.activity-card:hover .activity-image {
    transform: scale(1.1);
}

.activity-title-overlay {
    position: absolute;
    inset: 0;
    background: rgba(0, 0, 0, 0.7);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s ease;
    padding: 20px;
}

.activity-card:hover .activity-title-overlay {
    opacity: 1;
}

.activity-title {
    color: white;
    font-size: 1.2rem;
    font-weight: 600;
    text-align: center;
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
    line-height: 1.3;
}

.activity-content {
    padding: 20px;
}

.activity-date {
    color: #888;
    font-size: 0.85rem;
    margin-bottom: 10px;
    display: flex;
    align-items: center;
    gap: 5px;
}

.activity-date::before {
    content: "📅";
}

.activity-description {
    color: #555;
    font-size: 0.95rem;
    line-height: 1.6;
    margin-bottom: 10px;
}

.activity-members {
    font-size: 0.9rem;
    color: #333;
    font-weight: 500;
}

.activity-members span {
    font-weight: normal;
    color: #555;
}

.no-activity {
    text-align: center;
    color: #888;
    grid-column: 1 / -1;
    padding: 40px;
    font-size: 1.1rem;
}
</style>

<!-- ================= BANNER ACTIVITY ================= -->
<section class="inner-banner activity-banner">
    <div>
        <h2>Activity</h2>

        <div class="breadcrumb-activity">
            <a href="index.php">Home</a>
            <span>›</span>
            <span>Activity</span>
        </div>
    </div>
</section>


<!-- ================= CONTENT ACTIVITY ================= -->
<section class="py-5 activity-section-bg">
<div class="container py-md-5 py-3">
<h3 class="title-w3l mb-4 text-center">Our Activities</h3>

<div class="activity-grid">

<?php
/* === KONEKSI DATABASE === */
$host = "localhost";
$port = "5432";
$dbname = "pblfixxx";
$user = "postgres";
$password = "12345678";

$conn = pg_connect("host=$host port=$port dbname=$dbname user=$user password=$password");

if (!$conn) {
    echo '<p class="no-activity">Koneksi database gagal.</p>';
    exit;
}

/* === QUERY ACTIVITY === */
$query = "
   SELECT 
    a.id_activity,
    a.judul,
    a.deskripsi,
    a.tanggal_kegiatan,
    a.gambar,
    STRING_AGG(m.nama_member, ', ') AS members
FROM activity a
LEFT JOIN activity_member am ON a.id_activity = am.id_activity
LEFT JOIN member m ON am.id_member = m.id_member
GROUP BY a.id_activity
ORDER BY a.tanggal_kegiatan DESC
";

$result = pg_query($conn, $query);

if ($result && pg_num_rows($result) > 0) {
    while ($row = pg_fetch_assoc($result)) {

        $image_path = $row['gambar'];
        if (strpos($image_path, '/') === false) {
            $image_path = 'uploads/activity/' . $image_path;
        }
?>
    <div class="activity-card">
        <div class="activity-image-wrapper">

            <img src="<?= htmlspecialchars($image_path); ?>"
                 alt="<?= htmlspecialchars($row['judul']); ?>"
                 class="activity-image"
                 onerror="this.src='assets/images/no-image.jpg'">

            <div class="activity-title-overlay">
                <h4 class="activity-title"><?= htmlspecialchars($row['judul']); ?></h4>
            </div>

        </div>

        <div class="activity-content">

            <div class="activity-date">
                <?php
                $date = date_create($row['tanggal_kegiatan']);
                echo date_format($date, 'd F Y');
                ?>
            </div>

            <p class="activity-description">
                <?= htmlspecialchars($row['deskripsi']); ?>
            </p>

            <div class="activity-members">
                <strong>Member Berpartisipasi:</strong><br>
                <span>
                    <?= !empty($row['members']) ? htmlspecialchars($row['members']) : 'Belum ada member'; ?>
                </span>
            </div>

        </div>
    </div>
<?php
    }
} else {
    echo '<p class="no-activity">Belum ada aktivitas yang tersedia.</p>';
}
?>
</div>
</div>
</section>

