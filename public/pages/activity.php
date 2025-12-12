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
    cursor: pointer; /* Tambahan: menunjukkan card bisa diklik */
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

.member-list {
    margin: 10px 0 0 0;
    padding-left: 20px;
    list-style-type: disc;
}

.member-list li {
    color: #555;
    font-weight: normal;
    margin-bottom: 5px;
    line-height: 1.5;
}

.no-activity {
    text-align: center;
    color: #888;
    grid-column: 1 / -1;
    padding: 40px;
    font-size: 1.1rem;
}

/* ============================================= */
/* === TAMBAHAN CSS UNTUK LIGHTBOX MODAL === */
/* ============================================= */

.lightbox-modal {
    display: none;
    position: fixed;
    z-index: 9999;
    inset: 0;
    background-color: rgba(0, 0, 0, 0.95);
    align-items: center;
    justify-content: center;
    animation: fadeIn 0.3s ease;
}

.lightbox-modal.active {
    display: flex;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

.lightbox-content {
    position: relative;
    max-width: 90%;
    max-height: 90vh;
}

.lightbox-image {
    animation: zoomIn 0.3s ease;
}

@keyframes zoomIn {
    from { transform: scale(0.8); opacity: 0; }
    to { transform: scale(1); opacity: 1; }
}

.lightbox-image {
    max-width: 100%;
    max-height: 90vh;
    object-fit: contain;
    border-radius: 8px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.5);
}

.lightbox-close {
    position: absolute;
    top: -40px;
    right: 0;
    color: white;
    font-size: 40px;
    font-weight: bold;
    cursor: pointer;
    background: none;
    border: none;
    transition: color 0.3s ease;
    padding: 0;
    line-height: 1;
}

.lightbox-close:hover {
    color: #ffb400;
}

.lightbox-info {
    position: fixed;
    bottom: 30px;
    left: 0;
    right: 0;
    color: white;
    text-align: center;
    padding: 10px;
}

.lightbox-title {
    font-size: 1.3rem;
    font-weight: 600;
    margin-bottom: 5px;
}

.lightbox-date {
    font-size: 0.9rem;
    color: #ccc;
}



/* Responsive */
@media (max-width: 768px) {
    .lightbox-close {
        top: 10px;
        right: 10px;
        font-size: 30px;
    }

    .lightbox-info {
        position: static;
        margin-top: 20px;
    }
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
        
        // Format tanggal
        $date = date_create($row['tanggal_kegiatan']);
        $formatted_date = date_format($date, 'd F Y');
        
        // Escape data untuk JavaScript
        $safe_image = htmlspecialchars($image_path, ENT_QUOTES);
        $safe_title = htmlspecialchars($row['judul'], ENT_QUOTES);
        $safe_desc = htmlspecialchars($row['deskripsi'], ENT_QUOTES);
        $safe_date = htmlspecialchars($formatted_date, ENT_QUOTES);
?>
    <div class="activity-card" onclick="openLightbox('<?= $safe_image; ?>', '<?= $safe_title; ?>', '<?= $safe_date; ?>', '<?= $safe_desc; ?>')">
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
                <?= $formatted_date; ?>
            </div>

            <p class="activity-description">
                <?= htmlspecialchars($row['deskripsi']); ?>
            </p>

            <div class="activity-members">
                <strong>Member Berpartisipasi:</strong>
                <?php 
                if (!empty($row['members'])) {
                    // Pisahkan berdasarkan koma dan spasi
                    $members_array = array_map('trim', explode(',', $row['members']));
                    
                    // Filter nama yang valid (tidak kosong dan tidak hanya gelar)
                    $valid_members = [];
                    $current_member = '';
                    
                    foreach ($members_array as $part) {
                        // Cek apakah ini gelar (S.T., M.MT., Ph.D, dll)
                        if (preg_match('/^(S\.|M\.|Ph\.D|Dr\.|Ir\.)/', $part)) {
                            $current_member .= ', ' . $part;
                        } else {
                            // Ini nama baru
                            if (!empty($current_member)) {
                                $valid_members[] = trim($current_member, ', ');
                            }
                            $current_member = $part;
                        }
                    }
                    // Tambahkan member terakhir
                    if (!empty($current_member)) {
                        $valid_members[] = trim($current_member, ', ');
                    }
                    
                    if (count($valid_members) > 1) {
                        echo '<ul class="member-list">';
                        foreach ($valid_members as $member) {
                            echo '<li>' . htmlspecialchars($member) . '</li>';
                        }
                        echo '</ul>';
                    } else {
                        echo '<br><span>' . htmlspecialchars($row['members']) . '</span>';
                    }
                } else {
                    echo '<br><span>Belum ada member</span>';
                }
                ?>
            </div>

        </div>
    </div>
<?php
    }
} else {
    echo '<p class="no-activity">Belum ada aktivitas yang tersedia.</p>';
}

pg_close($conn);
?>
</div>
</div>
</section>

<!-- ============================================= -->
<!-- === TAMBAHAN: LIGHTBOX MODAL === -->
<!-- ============================================= -->
<div class="lightbox-modal" id="lightboxModal">
    <div class="lightbox-content">
        <button class="lightbox-close" id="lightboxClose">&times;</button>
        <img src="" alt="" class="lightbox-image" id="lightboxImage">
        <div class="lightbox-info">
            <div class="lightbox-title" id="lightboxTitle"></div>
            <div class="lightbox-date" id="lightboxDate"></div>
        </div>
    </div>
</div>

<!-- ============================================= -->
<!-- === TAMBAHAN: JAVASCRIPT UNTUK LIGHTBOX === -->
<!-- ============================================= -->
<script>
// Fungsi untuk membuka lightbox
function openLightbox(imageSrc, title, date, description) {
    const modal = document.getElementById('lightboxModal');
    const image = document.getElementById('lightboxImage');
    const titleEl = document.getElementById('lightboxTitle');
    const dateEl = document.getElementById('lightboxDate');
    
    image.src = imageSrc;
    image.alt = title;
    titleEl.textContent = title;
    dateEl.textContent = date;
    
    modal.classList.add('active');
    document.body.style.overflow = 'hidden'; // Prevent scrolling
}

// Fungsi untuk menutup lightbox
function closeLightbox() {
    const modal = document.getElementById('lightboxModal');
    modal.classList.remove('active');
    document.body.style.overflow = ''; // Enable scrolling
}

// Event listeners
document.getElementById('lightboxClose').addEventListener('click', closeLightbox);

document.getElementById('lightboxModal').addEventListener('click', function(e) {
    if (e.target.id === 'lightboxModal') {
        closeLightbox();
    }
});

// Keyboard ESC untuk menutup lightbox
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeLightbox();
    }
});
</script>