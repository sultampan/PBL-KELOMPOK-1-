<!-- ========== index.php ========== -->
<?php
session_start();
if (!isset($_SESSION['admin'])) {
    $_SESSION['admin'] = true; // Simple auth, ganti dengan sistem login yang proper
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - LAB AI</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f0f2f5; }
        .container { display: flex; min-height: 100vh; }
        .sidebar { width: 250px; background: #2c3e50; color: white; padding: 20px; }
        .sidebar h2 { margin-bottom: 30px; color: #3498db; }
        .sidebar ul { list-style: none; }
        .sidebar li { margin-bottom: 10px; }
        .sidebar a { color: white; text-decoration: none; padding: 12px 15px; display: block; border-radius: 5px; transition: 0.3s; }
        .sidebar a:hover, .sidebar a.active { background: #34495e; }
        .main-content { flex: 1; padding: 30px; }
        .header { background: white; padding: 20px; border-radius: 8px; margin-bottom: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .content-box { background: white; padding: 25px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        iframe { width: 100%; height: calc(100vh - 180px); border: none; }
        .welcome { text-align: center; padding: 50px; }
        .welcome h1 { color: #2c3e50; margin-bottom: 10px; }
        .welcome p { color: #7f8c8d; font-size: 18px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="sidebar">
            <h2>🎓 LAB AI Admin</h2>
            <ul>
                <li><a href="index.php" class="active">Dashboard</a></li>
                <li><a href="?page=activity">Activity</a></li>
                <li><a href="?page=fasilitas">Fasilitas</a></li>
                <li><a href="?page=member">Member</a></li>
                <li><a href="?page=produk">Produk</a></li>
            </ul>
        </div>
        <div class="main-content">
            <div class="header">
                <h3>Dashboard Admin</h3>
            </div>
            <div class="content-box">
                <?php
                $page = isset($_GET['page']) ? $_GET['page'] : 'home';
                
                switch($page) {
                    case 'activity':
                        include 'module/activity/index.php';
                        break;
                    case 'fasilitas':
                        include 'module/fasilitas/index.php';
                        break;
                    case 'member':
                        include 'module/member/index.php';
                        break;
                    case 'produk':
                        include 'module/produk/index.php';
                        break;
                    default:
                        echo '<div class="welcome">
                                <h1>Selamat Datang di Admin Panel</h1>
                                <p>Pilih menu di sidebar untuk mengelola data</p>
                              </div>';
                }
                ?>
            </div>
        </div>
    </div>
</body>
</html>