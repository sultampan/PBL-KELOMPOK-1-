<?php
// ==========================================
// 1. KONEKSI & PERSIAPAN DATA
// ==========================================
$rootPath = dirname(dirname(__DIR__)); 
$koneksiPath = $rootPath . '/config/koneksi.php';

if (file_exists($koneksiPath)) require_once $koneksiPath;

// --- CONFIG PAGINATION & SEARCH ---
$limit = 6; 
$currPage = isset($_GET['p']) ? (int)$_GET['p'] : 1;
if ($currPage < 1) $currPage = 1;
$offset = ($currPage - 1) * $limit;

$searchKeyword = '';
if (isset($_GET['search'])) {
    $searchKeyword = trim($_GET['search']);
}

// Helper Path
$webThumbPathMember = 'uploads/thumb/member-thumb/';
$webImgPathMember   = 'uploads/member/';
$serverThumbPathMember = $rootPath . '/public/uploads/thumb/member-thumb/';
$serverImgPathMember   = $rootPath . '/public/uploads/member/';
$paths = [
    'webThumb' => $webThumbPathMember, 'webImg' => $webImgPathMember,
    'serverThumb' => $serverThumbPathMember, 'serverImg' => $serverImgPathMember
];

// --- FUNGSI HELPER RENDER KARTU (Ditaruh atas agar bisa dipanggil AJAX) ---
function renderMemberCard($row, $isHeadLab, $paths) {
    $id      = $row['id_member'];
    $nama    = $row['nama_member'];
    $nidn    = $row['nidn'];
    $keahlian = $row['keahlian'] ?? ''; 
    $badgeLabel = $isHeadLab ? 'HEAD LAB' : 'MEMBER';
    $badgeClass = $isHeadLab ? 'role-badge-head' : 'role-badge';

    $defaultImg = 'https://ui-avatars.com/api/?name=' . urlencode($nama) . '&background=random&color=fff&size=128&length=1';
    $imgSrc = $defaultImg;
    if (!empty($row['gambar'])) {
        $ext = pathinfo($row['gambar'], PATHINFO_EXTENSION);
        $thumbName = pathinfo($row['gambar'], PATHINFO_FILENAME) . '-thumb.' . $ext;
        if (file_exists($paths['serverThumb'] . $thumbName)) $imgSrc = $paths['webThumb'] . $thumbName;
        elseif (file_exists($paths['serverImg'] . $row['gambar'])) $imgSrc = $paths['webImg'] . $row['gambar'];
        if ($imgSrc !== $defaultImg) $imgSrc .= '?' . time();
    }

    ob_start();
    ?>
    <a href="index.php?page=member-detail&id=<?= $id ?>" class="mit-card-link">
        <div class="mit-card <?= $isHeadLab ? 'head-card-style' : '' ?>">
            <div class="mit-card-role">
                <span class="<?= $badgeClass ?>"><?= $badgeLabel ?></span>
                <?php if($nidn): ?><span><?= htmlspecialchars($nidn) ?></span><?php endif; ?>
            </div>
            <div class="mit-card-name"><?= htmlspecialchars($nama) ?></div>
            <div class="mit-card-content">
                <div class="mit-avatar">
                    <img src="<?= $imgSrc ?>" alt="<?= htmlspecialchars($nama) ?>">
                </div>
                <div class="mit-contact" style="flex:1;">
                    <?php 
                    if (!empty($row['links']) && is_array($row['links'])): 
                        $showLinks = array_slice($row['links'], 0, 3);
                        foreach ($showLinks as $link):
                            $judulLink = $link['judul_link'];
                            $urlLink   = $link['url_link'];
                            if (strpos($urlLink, 'http') === false) $urlLink = 'https://' . $urlLink;
                    ?>
                        <div class="mit-email-row">
                            <i class="fas fa-link" style="color:#ccc; font-size:12px; margin-right:6px;"></i>
                            <span style="color: #01B5B8; font-weight: 600; font-size: 13px;">
                                <?= htmlspecialchars($judulLink) ?>
                            </span>
                        </div>
                    <?php endforeach; else: ?>
                        <span style="color:#ccc; font-style:italic; font-size:12px;">No contact info</span>
                    <?php endif; ?>
                </div>
            </div>
            <div class="mit-expertise-box">
                <span class="mit-expertise-label">Expertise</span>
                <div class="mit-expertise-items">
                    <?php 
                        if($keahlian) {
                            $skillsArr = explode(',', $keahlian);
                            $skillsArr = array_filter(array_map('trim', $skillsArr));
                            $showSkills = array_slice($skillsArr, 0, 3);
                            $sisa = count($skillsArr) - 3;
                            foreach ($showSkills as $sk) echo "<span class='skill-tag'>".htmlspecialchars($sk)."</span>";
                            if($sisa > 0) echo "<span class='skill-more'>+$sisa</span>";
                        } else {
                            echo '<span style="color:#999; font-size:12px;">-</span>';
                        }
                    ?>
                </div>
            </div>
            <div style="margin-top:10px; padding-top:10px; font-size:12px; color:#01B5B8; font-weight:600; text-align:right;">
                View Full Profile <i class="fas fa-arrow-right" style="font-size:10px;"></i>
            </div>
        </div>
    </a>
    <?php
    return ob_get_clean();
}

// --- LOGIKA QUERY DATA (Dipisah fungsi agar bisa dipakai di main & ajax) ---
function getMemberData($pdo, $searchKeyword, $limit, $offset, $currPage) {
    $data = [
        'headLab' => null,
        'members' => [],
        'totalPages' => 0
    ];

    try {
        // 1. QUERY HEAD LAB (IMAM) - Hanya di Halaman 1
        if ($currPage == 1) {
            $sqlHead = "SELECT * FROM member WHERE TRIM(LOWER(jabatan)) = 'head of laboratory'";
            
            // Tambah Filter Search untuk Head Lab juga
            if (!empty($searchKeyword)) {
                $sqlHead .= " AND (nama_member ILIKE :keyword OR nidn ILIKE :keyword OR keahlian ILIKE :keyword)";
            }
            $sqlHead .= " LIMIT 1";

            $stmtHead = $pdo->prepare($sqlHead);
            if (!empty($searchKeyword)) $stmtHead->bindValue(':keyword', "%$searchKeyword%");
            
            $stmtHead->execute();
            $data['headLab'] = $stmtHead->fetch(PDO::FETCH_ASSOC);

            if ($data['headLab']) {
                $stmtLink = $pdo->prepare("SELECT * FROM member_link WHERE id_member = ?");
                $stmtLink->execute([$data['headLab']['id_member']]);
                $data['headLab']['links'] = $stmtLink->fetchAll(PDO::FETCH_ASSOC);
            }
        }

        // 2. QUERY TOTAL MEMBER (Untuk Pagination)
        $sqlCount = "SELECT COUNT(*) FROM member WHERE TRIM(LOWER(jabatan)) != 'head of laboratory'";
        if (!empty($searchKeyword)) {
            $sqlCount .= " AND (nama_member ILIKE :keyword OR nidn ILIKE :keyword OR keahlian ILIKE :keyword)";
        }
        $stmtCount = $pdo->prepare($sqlCount);
        if (!empty($searchKeyword)) $stmtCount->bindValue(':keyword', "%$searchKeyword%");
        $stmtCount->execute();
        $totalData = $stmtCount->fetchColumn();
        $data['totalPages'] = ceil($totalData / $limit);

        // 3. QUERY MEMBER LIST (MAKMUM)
        $sqlMember = "
            SELECT * FROM member 
            WHERE TRIM(LOWER(jabatan)) != 'head of laboratory'
        ";
        if (!empty($searchKeyword)) {
            $sqlMember .= " AND (nama_member ILIKE :keyword OR nidn ILIKE :keyword OR keahlian ILIKE :keyword)";
        }
        $sqlMember .= " ORDER BY nama_member ASC LIMIT :limit OFFSET :offset";

        $stmt = $pdo->prepare($sqlMember);
        if (!empty($searchKeyword)) $stmt->bindValue(':keyword', "%$searchKeyword%");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $data['members'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Ambil link member
        foreach ($data['members'] as &$m) {
            $stmtLink = $pdo->prepare("SELECT * FROM member_link WHERE id_member = ?");
            $stmtLink->execute([$m['id_member']]);
            $m['links'] = $stmtLink->fetchAll(PDO::FETCH_ASSOC);
        }
        unset($m);

    } catch (Exception $e) { }

    return $data;
}

// Ambil Data Utama
$resultData = getMemberData($pdo, $searchKeyword, $limit, $offset, $currPage);
$headLabData = $resultData['headLab'];
$memberList = $resultData['members'];
$totalPages = $resultData['totalPages'];


// ==========================================
// 2. AJAX HANDLER (LIVE SEARCH)
// ==========================================
if (isset($_GET['ajax']) && $_GET['ajax'] == '1') {
    while (ob_get_level()) { ob_end_clean(); }
    
    echo '<div id="ajax-content-wrapper">';
    
    // A. OUTPUT HEAD LAB (IMAM)
    if ($headLabData) {
        echo '<div class="head-lab-wrapper"><div class="head-card-wrapper">';
        echo renderMemberCard($headLabData, true, $paths);
        echo '</div></div>';
    }

    // B. OUTPUT MEMBER (MAKMUM)
    if (!empty($memberList)) {
        echo '<div class="member-grid">';
        foreach ($memberList as $row) {
            echo renderMemberCard($row, false, $paths);
        }
        echo '</div>';
    }

    // C. PAGINATION
    if ($totalPages > 1) {
        echo '<div class="pagination-wrapper">';
        
        $prevDisabled = ($currPage <= 1) ? 'disabled' : '';
        $prevPage = $currPage - 1;
        echo '<button class="page-btn" onclick="changePage('.$prevPage.')" '.$prevDisabled.'><i class="fas fa-chevron-left"></i></button>';

        for ($i = 1; $i <= $totalPages; $i++) {
            $active = ($i == $currPage) ? 'active' : '';
            echo '<button class="page-btn '.$active.'" onclick="changePage('.$i.')">'.$i.'</button>';
        }
        
        $nextDisabled = ($currPage >= $totalPages) ? 'disabled' : '';
        $nextPage = $currPage + 1;
        echo '<button class="page-btn" onclick="changePage('.$nextPage.')" '.$nextDisabled.'><i class="fas fa-chevron-right"></i></button>';
        
        echo '</div>';
    }

    // D. JIKA KOSONG
    if (!$headLabData && empty($memberList)) {
        echo '<div class="no-activity">
                <i class="fas fa-search fa-3x text-muted mb-3"></i>
                <p>No members found.</p>
              </div>';
    }

    echo '</div>'; // End Wrapper
    exit;
}
?>

<style>
    /* CSS SEARCH BAR */
    .search-wrapper-center {
        display: flex; justify-content: center; width: 100%;
        margin-top: 30px; margin-bottom: 40px; padding: 0 15px;
        position: relative; z-index: 2;
    }
    .search-facility-box {
        width: 100%; max-width: 600px; position: relative;
        background: white; border-radius: 50px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    }
    .search-facility-box input {
        width: 100%; border: 1px solid #ddd; border-radius: 50px;
        padding: 15px 60px 15px 30px; background-color: #fff;
        color: #333; font-family: sans-serif; font-size: 16px; outline: none;
        transition: all 0.3s ease;
    }
    .search-facility-box input:focus {
        border-color: #01B5B8; box-shadow: 0 4px 10px rgba(1, 181, 184, 0.15);
    }
    .search-icon-static {
        position: absolute; right: 25px; top: 50%;
        transform: translateY(-50%); color: #aaa;
        font-size: 20px; pointer-events: none;
    }
    .search-loading {
        position: absolute; right: 25px; top: 50%;
        transform: translateY(-50%); color: #01B5B8;
        font-size: 20px; display: none;
    }

    /* CSS UTAMA */
    .inner-banner.facility-banner {
        background: url('assets/images/header-facility.jpeg') no-repeat center;
        background-size: cover; position: relative; z-index: 0;
        min-height: 350px; display: grid; align-items: center;
    }
    .inner-banner.facility-banner:before {
        content: ""; background: rgba(0, 0, 0, 0.6); position: absolute; inset: 0; z-index: -1;
    }
    
    /* LAYOUT IMAM & MAKMUM */
    .head-lab-wrapper {
        display: flex; justify-content: center; margin-bottom: 50px; position: relative;
    }
    .head-lab-wrapper::after {
        content: ""; position: absolute; bottom: -25px; left: 50%; transform: translateX(-50%);
        width: 100px; height: 3px; background: #01B5B8; border-radius: 2px; opacity: 0.3;
    }
    .head-card-wrapper { width: 100%; max-width: 400px; }
    .head-card-style { border-color: #01B5B8; box-shadow: 0 10px 30px rgba(1, 181, 184, 0.15); }

    .member-grid {
        display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 25px; margin-top: 20px;
    }

    /* CARD STYLE */
    .mit-card-link { text-decoration: none; color: inherit; display: block; height: 100%; }
    .mit-card {
        background: #fff; border: 1px solid #e0e0e0; padding: 20px; 
        border-radius: 8px; transition: transform 0.2s, box-shadow 0.2s;
        display: flex; flex-direction: column; position: relative; overflow: hidden; height: 100%; 
    }
    .mit-card-link:hover .mit-card {
        transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.08); border-color: #FE7C11; 
    }
    .mit-card-role {
        display: flex; justify-content: space-between; align-items: center;
        font-size: 11px; text-transform: uppercase; color: #666; font-weight: 600; margin-bottom: 5px;
    }
    .role-badge { background-color: #eee; padding: 3px 8px; border-radius: 4px; }
    .role-badge-head { background-color: #FE7C11; color: #fff; padding: 3px 8px; border-radius: 4px; }
    .mit-card-name { 
        margin: 5px 0 5px 0 !important; font-size: 20px; font-weight: 700; color: #02406C; 
        line-height: 1.3; display: -webkit-box; -webkit-line-clamp: 2; line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
    }
    .mit-card-content { display: flex; gap: 15px; align-items: flex-start; margin-bottom: 15px; min-height: 85px; }
    .mit-avatar img {
        width: 80px; height: 80px; object-fit: cover; border-radius: 50%; 
        border: 3px solid #f8f9fa; background-color: #eee; flex-shrink: 0;
    }
    .mit-contact { margin-top: 5px; display: flex; flex-direction: column; font-size: 13px; gap: 6px; }
    .mit-email-row { display: flex; align-items: center; gap: 8px; }
    .mit-contact a { 
        color: #01B5B8; text-decoration: none; font-weight: 600; transition: color 0.2s;
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 160px; display: inline-block;
    }
    .mit-contact a:hover { color: #008c8e; text-decoration: underline; }
    .mit-expertise-box { margin-top: auto; padding-top: 15px; border-top: 1px solid #f0f0f0; }
    .mit-expertise-label { font-size: 10px; color: #999; text-transform: uppercase; font-weight: 700; margin-bottom: 8px; display: block; letter-spacing: 0.5px; }
    .mit-expertise-items { height: 55px; overflow: hidden; display: flex; flex-wrap: wrap; gap: 5px; align-content: flex-start; }
    .skill-tag {
        display: inline-block; padding: 4px 10px; background: #e0f7fa; color: #006064;
        border-radius: 15px; font-size: 11px; font-weight: 600; border: 1px solid #b2ebf2; white-space: nowrap;
    }
    .skill-more { font-size: 11px; color: #777; font-weight: 600; padding: 4px 5px; }

    /* PAGINATION CSS */
    .pagination-wrapper {
        display: flex; justify-content: center; align-items: center; gap: 8px;
        margin-top: 50px; margin-bottom: 20px;
    }
    .page-btn {
        min-width: 40px; height: 40px; border: 1px solid #ddd;
        background: #fff; color: #555; border-radius: 5px;
        font-weight: 600; cursor: pointer; transition: all 0.3s;
        display: flex; justify-content: center; align-items: center; text-decoration: none; 
    }
    .page-btn:hover:not(:disabled) { background-color: #f0f0f0; border-color: #ccc; color: #333; }
    .page-btn.active { background-color: #01B5B8; color: #fff; border-color: #01B5B8; }
    .page-btn:disabled { opacity: 0.5; cursor: not-allowed; pointer-events: none; }
    .no-activity { text-align: center; color: #888; padding: 40px; width: 100%; }
</style>

<div class="inner-banner facility-banner">
    <section class="w3l-breadcrumb text-center">
        <div class="container">
            <div class="w3breadcrumb-gids">
                <div class="w3breadcrumb-left text-center">
                    <h2 class="inner-w3-title">Member</h2>
                </div>
                <div class="w3breadcrumb-right">
                    <ul class="breadcrumbs-custom-path">
                        <li><a href="index.php?page=home">Home</a></li>
                        <li class="active"><span class="fas fa-angle-double-right mx-2"></span> Member</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>
</div>

<section class="w3l-gallery pb-5 pt-4" style="background-color: #f9f9f9;">
    <div class="container pb-md-5 pt-3">

        <div class="search-wrapper-center">
            <div class="search-facility-box">
                <form action="#" method="GET" onsubmit="return false;">
                    <input type="text" id="searchInput" 
                           placeholder="Search Members..." 
                           value="<?= htmlspecialchars($searchKeyword) ?>"
                           oninput="performLiveSearch(this.value, 1)"> 
                    
                    <i id="staticSearchIcon" class="fas fa-search search-icon-static"></i>
                    <div id="searchSpinner" class="search-loading">
                        <i class="fas fa-spinner fa-spin"></i>
                    </div>
                </form>
            </div>
        </div>

        <div id="member-results-container">
            <div id="ajax-content-wrapper">
                
                <?php if ($headLabData): ?>
                    <div class="head-lab-wrapper">
                        <div class="head-card-wrapper">
                            <?php echo renderMemberCard($headLabData, true, $paths); ?>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (!empty($memberList)): ?>
                    <div class="member-grid">
                        <?php foreach ($memberList as $row): ?>
                            <?php echo renderMemberCard($row, false, $paths); ?>
                        <?php endforeach; ?>
                    </div>

                    <?php if ($totalPages > 1): ?>
                    <div class="pagination-wrapper">
                        <?php 
                        $prevDisabled = ($currPage <= 1) ? 'disabled' : '';
                        $prevPage = $currPage - 1;
                        ?>
                        <button class="page-btn" onclick="changePage(<?= $prevPage ?>)" <?= $prevDisabled ?>><i class="fas fa-chevron-left"></i></button>

                        <?php 
                        for ($i = 1; $i <= $totalPages; $i++) {
                            $active = ($i == $currPage) ? 'active' : '';
                            echo '<button class="page-btn '.$active.'" onclick="changePage('.$i.')">'.$i.'</button>';
                        }
                        
                        $nextDisabled = ($currPage >= $totalPages) ? 'disabled' : '';
                        $nextPage = $currPage + 1;
                        ?>
                        <button class="page-btn" onclick="changePage(<?= $nextPage ?>)" <?= $nextDisabled ?>><i class="fas fa-chevron-right"></i></button>
                    </div>
                    <?php endif; ?>

                <?php else: ?>
                    <?php if(!$headLabData): ?>
                        <div class="no-activity">
                            <i class="fas fa-search fa-3x text-muted mb-3"></i>
                            <p>No members found matching your search.</p>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>

            </div>
        </div>
        
    </div>
</section>

<script>
    let searchTimeout;
    
    function changePage(pageNum) {
        const keyword = document.getElementById('searchInput').value;
        performLiveSearch(keyword, pageNum);
        
        // Scroll halus ke atas grid
        const container = document.getElementById('member-results-container');
        window.scrollTo({
            top: container.offsetTop - 150, 
            behavior: 'smooth'
        });
    }

    function performLiveSearch(keyword, page = 1) {
        const spinner = document.getElementById('searchSpinner');
        const staticIcon = document.getElementById('staticSearchIcon');
        const container = document.getElementById('member-results-container');

        spinner.style.display = 'block';
        staticIcon.style.opacity = '0';
        container.style.opacity = '0.5';

        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            // Menggunakan page=member
            fetch(`index.php?page=member&ajax=1&search=${encodeURIComponent(keyword)}&p=${page}`)
                .then(res => res.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    
                    const newContent = doc.getElementById('ajax-content-wrapper');

                    if (newContent) {
                        container.innerHTML = '';
                        container.appendChild(newContent);
                    } else {
                        container.innerHTML = '<div class="no-activity"><p>No members found.</p></div>';
                    }

                    spinner.style.display = 'none';
                    staticIcon.style.opacity = '1';
                    container.style.opacity = '1';
                })
                .catch(err => {
                    console.error('Search error:', err);
                    spinner.style.display = 'none';
                    staticIcon.style.opacity = '1';
                    container.style.opacity = '1';
                });
        }, 300);
    }
</script>