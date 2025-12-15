<?php
// Start session di awal
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../../config/koneksi.php';
require_once __DIR__ . '/model.php';

$searchKeyword = $_GET['keyword'] ?? null;

/* ambil data */
$list = getSocialMediaAll($pdo, $searchKeyword);

/* edit mode */
$editData = null;
if (isset($_GET['edit'])) {
    $editData = getSocialMediaById($pdo, (int)$_GET['edit']);
}
?>

<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
.card {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    margin-bottom: 20px;
}

.card-header {
    padding: 20px;
    border-bottom: 1px solid #ecf0f1;
}

.card-header h3 {
    margin: 0;
    font-size: 18px;
    color: #2c3e50;
    font-weight: 600;
}

.card-body {
    padding: 20px;
}

.form-label {
    display: block;
    margin-bottom: 8px;
    font-weight: 500;
    color: #2c3e50;
    font-size: 14px;
}

.form-control {
    width: 100%;
    padding: 10px 12px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
    transition: border-color 0.3s;
    box-sizing: border-box;
}

.form-control:focus {
    outline: none;
    border-color: #3498db;
}

.mb-3 {
    margin-bottom: 15px;
}

/* Alert Messages */
.alert {
    padding: 12px 20px;
    border-radius: 4px;
    margin-bottom: 20px;
}

.alert-success {
    background-color: #d4edda;
    border: 1px solid #c3e6cb;
    color: #155724;
}

.alert-error {
    background-color: #f8d7da;
    border: 1px solid #f5c6cb;
    color: #721c24;
}
</style>

<!-- Alert Messages -->
<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success">
        <?= $_SESSION['success']; unset($_SESSION['success']); ?>
    </div>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-error">
        <?= $_SESSION['error']; unset($_SESSION['error']); ?>
    </div>
<?php endif; ?>

<!-- ===== FORM ===== -->
<div class="card">
    <?php include __DIR__ . '/form.php'; ?>
</div>

<!-- ===== TABLE ===== -->
<div class="card">
    <?php include __DIR__ . '/table.php'; ?>
</div>