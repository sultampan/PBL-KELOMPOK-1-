<?php
// ==========================================
// 1. KONFIGURASI & LOGIKA BACKEND
// ==========================================
$rootPath = dirname(dirname(__DIR__)); 
$koneksiPath = $rootPath . '/config/koneksi.php';

if (file_exists($koneksiPath)) {
    require_once $koneksiPath;
} else {
    $altKoneksiPath = $_SERVER['DOCUMENT_ROOT'] . '/config/koneksi.php';
    if (file_exists($altKoneksiPath)) {
        require_once $altKoneksiPath;
    }
}

// Ambil Keyword Pencarian
$searchKeyword = isset($_GET['search']) ? trim($_GET['search']) : '';

// --- FUNGSI RENDER KONTEN (Agar bisa dipanggil ulang saat AJAX) ---
function renderActivityContent($pdo, $keyword) {
    try {
        // Query Database dengan Filter Pencarian
        $sql = "
            SELECT 
                a.id_activity, a.judul, a.deskripsi, a.tanggal_kegiatan, a.gambar, a.kategori,
                STRING_AGG(m.nama_member, ', ') AS members
            FROM activity a
            LEFT JOIN activity_member am ON a.id_activity = am.id_activity
            LEFT JOIN member m ON am.id_member = m.id_member
        ";

        // Tambahkan klausa WHERE jika ada pencarian
        if (!empty($keyword)) {
            $sql .= " WHERE a.judul LIKE :k1 OR a.deskripsi LIKE :k2";
        }

        $sql .= " GROUP BY a.id_activity ORDER BY a.tanggal_kegiatan DESC";

        $stmt = $pdo->prepare($sql);

        if (!empty($keyword)) {
            $stmt->bindValue(':k1', "%$keyword%", PDO::PARAM_STR);
            $stmt->bindValue(':k2', "%$keyword%", PDO::PARAM_STR);
        }

        $stmt->execute();
        $allActivities = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Grouping Data
        $grouped = [
            'Research' => [],
            'Projects' => [],
            'Activity' => []
        ];

        if (count($allActivities) > 0) {
            foreach ($allActivities as $row) {
                $cat = $row['kategori'];
                if (array_key_exists($cat, $grouped)) {
                    $grouped[$cat][] = $row;
                } else {
                    $grouped['Activity'][] = $row; 
                }
            }
        }

        // Render Loop
        $hasData = false;
        $html = '';

        foreach ($grouped as $kategoriName => $listActivity) {
            if (!empty($listActivity)) {
                $hasData = true;
                
                $html .= '<div class="category-section">';
                $html .= '<h4 class="category-title">' . htmlspecialchars($kategoriName) . '</h4>';
                
                $html .= '<div class="activity-carousel-wrapper">';
                $html .= '<button class="nav-btn prev-btn hidden"><i class="fas fa-chevron-left"></i></button>';
                
                $html .= '<div class="activity-track">';
                
                foreach ($listActivity as $row) {
                    $image_path = $row['gambar'];
                    $hasImage = !empty($image_path);
                    if ($hasImage && strpos($image_path, '/') === false) {
                        $image_path = 'uploads/activity/' . $image_path;
                    }

                    $date = date_create($row['tanggal_kegiatan']);
                    $formatted_date = date_format($date, 'd F Y');
                    
                    $html .= '<div class="activity-card">';
                    
                    // Gambar
                    $html .= '<div class="activity-image-wrapper">';
                    if ($hasImage) {
                        $html .= '<img src="' . htmlspecialchars($image_path) . '" alt="' . htmlspecialchars($row['judul']) . '" class="activity-image" loading="lazy" onerror="this.parentElement.innerHTML=\'<div class=\\\'no-image-placeholder\\\'> <i class=\\\'fas fa-image\\\'></i> <span>Error Image</span> </div>\';">';
                    } else {
                        $html .= '<div class="no-image-placeholder"><i class="fas fa-image"></i><span>No Image</span></div>';
                    }
                    $html .= '</div>';

                    // Konten
                    $html .= '<div class="activity-content">';
                    $html .= '<div class="activity-title-main" title="' . htmlspecialchars($row['judul']) . '">' . htmlspecialchars($row['judul']) . '</div>';
                    $html .= '<div class="activity-date"><i class="far fa-calendar-alt"></i> ' . $formatted_date . '</div>';
                    $html .= '<div class="activity-description">' . mb_strimwidth(htmlspecialchars($row['deskripsi']), 0, 120, "...") . '</div>';
                    
                    // Members
                    $html .= '<div class="activity-members">';
                    $html .= '<span class="activity-members-label">Members:</span>';
                    $html .= '<div class="activity-members-tags">';
                    
                    if (!empty($row['members'])) {
                        $members_array = explode(',', $row['members']);
                        $members_array = array_map('trim', $members_array);
                        $display_count = min(2, count($members_array));
                        
                        for ($i = 0; $i < $display_count; $i++) {
                            $html .= '<span class="member-tag">' . htmlspecialchars($members_array[$i]) . '</span>';
                        }
                        
                        $remaining = count($members_array) - $display_count;
                        if ($remaining > 0) {
                            $html .= '<span class="member-more">+' . $remaining . '</span>';
                        }
                    } else {
                        $html .= '<span class="member-tag" style="background: #f5f5f5; color: #999;">-</span>';
                    }
                    
                    $html .= '</div></div></div>'; // End content & members wrapper

                    // Footer
                    $html .= '<div class="activity-footer">';
                    $html .= '<a href="index.php?page=activity-detail&id=' . $row['id_activity'] . '" class="view-more-btn">View More <i class="fas fa-arrow-right"></i></a>';
                    $html .= '</div>';
                    
                    $html .= '</div>'; // End Card
                }

                $html .= '</div>'; // End Track
                $html .= '<button class="nav-btn next-btn hidden"><i class="fas fa-chevron-right"></i></button>';
                $html .= '</div></div>'; // End Carousel Wrapper & Section
            }
        }

        if (!$hasData) {
            $html .= '<div class="no-data">Belum ada aktivitas yang cocok dengan pencarian Anda.</div>';
        }

        return $html;

    } catch (PDOException $e) {
        return '<div class="no-data">Terjadi kesalahan sistem database.</div>';
    }
}

// --- HANDLER AJAX ---
if (isset($_GET['ajax']) && $_GET['ajax'] == '1') {
    while (ob_get_level()) { ob_end_clean(); }
    echo renderActivityContent($pdo, $searchKeyword);
    exit;
}
?>

<style>
    /* =========================================
       1. HEADER BANNER
       ========================================= */
    .inner-banner.activity-banner {
        background: url('assets/images/header-facility.jpeg') no-repeat center;
        background-size: cover;
        position: relative;
        z-index: 0;
        min-height: 350px;
        display: grid;
        align-items: center;
    }

    .inner-banner.activity-banner:before {
        content: "";
        background: rgba(0, 0, 0, 0.6);
        position: absolute;
        top: 0; bottom: 0; left: 0; right: 0;
        z-index: -1;
    }

    h2.inner-w3-title {
        font-family: 'Source Sans Pro', sans-serif;
        font-size: 56px !important;
        line-height: 1.1;
        font-weight: 700 !important;
        text-transform: capitalize;
        color: #fff;
        margin-bottom: 0;
        text-align: center;
    }

    .w3breadcrumb-gids {
        text-align: center;
        width: 100%;
    }

    .w3breadcrumb-right {
        margin-top: 10px;
        display: flex;
        justify-content: center;
    }

    ul.breadcrumbs-custom-path {
        padding: 0;
        margin: 0;
        list-style: none;
        display: flex;
        align-items: center;
        justify-content: center;
        font-family: 'Source Sans Pro', sans-serif;
    }

    ul.breadcrumbs-custom-path li {
        display: inline-block;
        font-size: 16px;
        font-weight: 700;
        line-height: 1.5;
    }

    ul.breadcrumbs-custom-path li a {
        color: #fbb034 !important;
        text-decoration: none;
        opacity: 0.9;
        transition: 0.3s ease;
    }

    ul.breadcrumbs-custom-path li a:hover {
        color: #fff !important;
        opacity: 1;
    }

    ul.breadcrumbs-custom-path li span.fa-angle-double-right {
        font-size: 12px;
        margin: 0 12px;
        color: #fff;
        font-weight: 700;
        opacity: 0.8;
    }

    ul.breadcrumbs-custom-path li.active {
        color: #fff;
        font-weight: 700;
        text-transform: capitalize;
    }

    @media (max-width: 992px) {
        h2.inner-w3-title {
            font-size: 40px !important;
            line-height: 50px;
        }
    }

    /* =========================================
       2. SEARCH BAR (STYLE FACILITY)
       ========================================= */
       .search-wrapper-center { 
        display: flex; justify-content: center; width: 100%; 
        margin-top: 20px; margin-bottom: 50px; 
        padding: 0 15px; 
    }
    
    /* GANTI NAMA CLASS DI SINI */
    .search-activity-box { 
        width: 100%; 
        max-width: 450px; /* Ukuran tetap 450px, tidak akan ketimpa style global */
        position: relative; 
    }
    
    .search-activity-box input {
        width: 100%; 
        border: 1px solid #ccc; 
        border-radius: 50px; 
        padding: 10px 45px 10px 20px; 
        background-color: #fff; 
        color: #333; 
        font-size: 15px;
        outline: none; 
        transition: all 0.3s ease;
        box-shadow: 0 3px 6px rgba(0,0,0,0.05); 
        height: 45px;
    }
    
    .search-activity-box input:focus { 
        border-color: var(--primary-color, #007bff); 
        box-shadow: 0 4px 10px rgba(0,0,0,0.1); 
    }
    .search-icon-static { 
        position: absolute; right: 18px; top: 50%; transform: translateY(-50%); 
        color: #aaa; font-size: 16px; pointer-events: none; 
    }
    .search-loading { 
        position: absolute; right: 18px; top: 50%; transform: translateY(-50%); 
        color: #aaa; font-size: 16px; display: none; 
    }

    /* =========================================
       3. ACTIVITY CONTENT STYLING
       ========================================= */
    .activity-section-bg { background-color: #f8f9fa; }
    .category-section { margin-bottom: 4rem; position: relative; }
    .category-title {
        font-size: 2rem; font-weight: 700; color: #02406C;
        margin-bottom: 1.5rem; padding-left: 15px;
        border-left: 5px solid #ff9800; display: inline-block;
    }

    .activity-carousel-wrapper { position: relative; width: 100%; display: flex; align-items: center; }
    .activity-track {
        display: flex; gap: 30px; overflow-x: auto; scroll-behavior: smooth;
        padding: 20px 5px; width: 100%; -ms-overflow-style: none; scrollbar-width: none;
    }
    .activity-track::-webkit-scrollbar { display: none; }

    .activity-card {
        background: white; min-width: 300px; max-width: 300px; height: 540px;
        border-radius: 12px; overflow: hidden;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        display: flex; flex-direction: column; border: 1px solid #f0f0f0;
    }
    .activity-card:hover { transform: translateY(-8px); box-shadow: 0 12px 25px rgba(0, 0, 0, 0.15); }

    .activity-image-wrapper { position: relative; width: 100%; height: 200px; overflow: hidden; background-color: #eee; flex-shrink: 0; }
    .activity-image { width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s ease; }
    .activity-card:hover .activity-image { transform: scale(1.1); }

    .no-image-placeholder {
        display: flex; flex-direction: column; align-items: center; justify-content: center;
        width: 100%; height: 100%; background-color: #f4f4f4; color: #aaa;
    }
    .no-image-placeholder i { font-size: 40px; margin-bottom: 10px; opacity: 0.5; }
    .no-image-placeholder span { font-size: 14px; font-weight: 600; }

    .activity-content { padding: 20px; display: flex; flex-direction: column; flex-grow: 1; }
    .activity-title-main {
        font-size: 1.2rem; font-weight: 700; color: #222; margin-bottom: 8px;
        line-height: 1.4; height: 52px; overflow: hidden; display: -webkit-box;
        -webkit-line-clamp: 2; -webkit-box-orient: vertical;
    }
    .activity-date {
        color: #888; font-size: 0.85rem; margin-bottom: 12px; display: flex;
        align-items: center; gap: 6px; font-weight: 500;
    }
    .activity-description {
        color: #666; font-size: 0.95rem; line-height: 1.6; margin-bottom: 15px;
        height: 75px; overflow: hidden; display: -webkit-box;
        -webkit-line-clamp: 3; -webkit-box-orient: vertical;
    }

    .activity-members { margin-top: auto; padding-top: 15px; border-top: 1px solid #f0f0f0; }
    .activity-members-label { font-size: 0.75rem; font-weight: 700; color: #444; margin-bottom: 8px; display: block; text-transform: uppercase; letter-spacing: 0.5px; }
    .activity-members-tags { display: flex; flex-wrap: wrap; gap: 6px; }
    .member-tag { display: inline-block; padding: 4px 10px; background: #e3f2fd; color: #1976d2; border-radius: 50px; font-size: 0.75rem; font-weight: 600; white-space: nowrap; }
    .member-more { display: inline-block; padding: 4px 10px; background: #f5f5f5; color: #666; border-radius: 50px; font-size: 0.75rem; font-weight: 600; }

    .activity-footer { padding: 15px 20px; background-color: #fff; border-top: 1px solid #f9f9f9; text-align: right; }
    .view-more-btn { display: inline-flex; align-items: center; gap: 6px; color: #02406C; font-size: 0.9rem; font-weight: 700; text-decoration: none; transition: all 0.3s ease; }
    .view-more-btn:hover { color: #ff9800; transform: translateX(5px); }

    .nav-btn {
        position: absolute; top: 50%; transform: translateY(-50%);
        width: 50px; height: 50px; background-color: #fff; border: 1px solid #eee;
        border-radius: 50%; color: #333; font-size: 18px; display: flex;
        align-items: center; justify-content: center; cursor: pointer; z-index: 10;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1); transition: all 0.3s ease; opacity: 1;
    }
    .nav-btn:hover { background-color: #02406C; color: white; border-color: #02406C; }
    .nav-btn.hidden { opacity: 0; pointer-events: none; }
    .prev-btn { left: -25px; }
    .next-btn { right: -25px; }

    @media (max-width: 768px) {
        .nav-btn { display: none !important; }
        .activity-track { padding-right: 20px; }
    }
    .no-data { text-align: center; color: #888; padding: 40px; width: 100%; font-size: 1.1rem; }
</style>

<div class="inner-banner activity-banner">
    <section class="w3l-breadcrumb text-center">
        <div class="container">
            <div class="w3breadcrumb-gids">
                <div class="w3breadcrumb-left text-center">
                    <h2 class="inner-w3-title">Activity</h2>
                </div>
                <div class="w3breadcrumb-right">
                    <ul class="breadcrumbs-custom-path">
                        <li><a href="index.php?page=home">Home</a></li>
                        <li class="active">
                            <span class="fas fa-angle-double-right" aria-hidden="true"></span> Activity
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>
</div>

<section class="py-5 activity-section-bg">
    <div class="container py-md-5 py-3">

        <div class="search-wrapper-center">
            <div class="search-activity-box">
                <form action="#" method="GET" onsubmit="return false;">
                    <input type="text" id="searchInput" placeholder="Cari aktivitas..." value="<?= htmlspecialchars($searchKeyword) ?>"
                           oninput="performLiveSearch(this.value)"> 
                    <i id="staticSearchIcon" class="fas fa-search search-icon-static"></i>
                    <div id="searchSpinner" class="search-loading"><i class="fas fa-spinner fa-spin"></i></div>
                </form>
            </div>
        </div>

        <div id="activity-results-container">
            <?= renderActivityContent($pdo, $searchKeyword); ?>
        </div>

    </div>
</section>

<script>
// Fungsi inisialisasi carousel (dipisah agar bisa dipanggil ulang setelah AJAX)
function initCarousels() {
    const carousels = document.querySelectorAll('.activity-carousel-wrapper');

    carousels.forEach(wrapper => {
        const track = wrapper.querySelector('.activity-track');
        const prevBtn = wrapper.querySelector('.prev-btn');
        const nextBtn = wrapper.querySelector('.next-btn');
        const scrollAmount = 320; 

        // Fungsi Cek Panah
        const checkArrows = () => {
            if (!track) return;
            const maxScrollLeft = track.scrollWidth - track.clientWidth - 1;
            
            if (track.scrollLeft <= 0) {
                if(prevBtn) prevBtn.classList.add('hidden');
            } else {
                if(prevBtn) prevBtn.classList.remove('hidden');
            }

            if (track.scrollWidth <= track.clientWidth || track.scrollLeft >= maxScrollLeft) {
                if(nextBtn) nextBtn.classList.add('hidden');
            } else {
                if(nextBtn) nextBtn.classList.remove('hidden');
            }
        };

        // Event Listener (Hapus dulu biar ga numpuk klo AJAX call)
        if (prevBtn) {
            prevBtn.replaceWith(prevBtn.cloneNode(true)); // Trik simpel hapus listener lama
            const newPrev = wrapper.querySelector('.prev-btn');
            newPrev.addEventListener('click', () => {
                track.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
            });
        }

        if (nextBtn) {
            nextBtn.replaceWith(nextBtn.cloneNode(true));
            const newNext = wrapper.querySelector('.next-btn');
            newNext.addEventListener('click', () => {
                track.scrollBy({ left: scrollAmount, behavior: 'smooth' });
            });
        }

        if (track) {
            track.addEventListener('scroll', checkArrows);
            checkArrows();
        }
    });
}

// Fungsi Pencarian Live
let searchTimeout;
function performLiveSearch(keyword) {
    const spinner = document.getElementById('searchSpinner');
    const staticIcon = document.getElementById('staticSearchIcon');
    const container = document.getElementById('activity-results-container');

    if(spinner) spinner.style.display = 'block'; 
    if(staticIcon) staticIcon.style.opacity = '0'; 
    if(container) container.style.opacity = '0.5';
    
    clearTimeout(searchTimeout);

    // Update URL tanpa reload
    if (window.history.pushState) {
        const newUrl = new URL(window.location.href);
        newUrl.searchParams.set('search', keyword);
        window.history.pushState({}, '', newUrl);
    }

    searchTimeout = setTimeout(() => {
        const timestamp = new Date().getTime();
        fetch(`index.php?page=activity&ajax=1&search=${encodeURIComponent(keyword)}&_t=${timestamp}`)
            .then(res => res.text())
            .then(html => {
                if(container) {
                    container.innerHTML = html;
                    container.style.opacity = '1';
                    // Re-init carousel setelah konten baru masuk
                    initCarousels();
                }
                if(spinner) spinner.style.display = 'none';
                if(staticIcon) staticIcon.style.opacity = '1';
            })
            .catch(err => {
                console.error(err);
                if(spinner) spinner.style.display = 'none';
                if(staticIcon) staticIcon.style.opacity = '1';
                if(container) container.style.opacity = '1';
            });
    }, 300);
}

// Jalankan saat halaman pertama kali load
document.addEventListener("DOMContentLoaded", function() {
    initCarousels();
    window.addEventListener('resize', initCarousels);
});
</script>