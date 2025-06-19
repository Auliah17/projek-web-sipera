<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'dokter') {
    header("Location: ../../login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard Dokter - SIPERA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f2f2f2; margin-bottom: 70px; }
        .sidebar { background-color: #1ca127; color: white; min-height: 100vh; padding: 20px 10px; }
        .sidebar a { color: white; display: block; padding: 10px; border-radius: 6px; text-decoration: none; }
        .sidebar a:hover { background-color: #158e1f; }
        .topbar { background-color: #1ca127; color: white; padding: 15px 20px; display: flex; justify-content: space-between; align-items: center; }
    </style>
</head>
<body>

<div class="topbar">
    <strong>SIPERA - Dokter</strong>
    <div>
        <a href="notifikasi.php" class="text-white me-3">Notifikasi</a>
        <a href="/sipera/logout.php" class="text-white">Logout</a>
    </div>
</div>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-3 sidebar">
            <h5 class="text-white mb-4">Menu</h5>
            <a href="dashboard.php">🏠 Dashboard</a>
            <a href="konsultasi_masuk.php">💬 Konsultasi Masuk</a>
            <a href="profil.php">👤 Profil</a>
            <a href="/sipera/logout.php">🚪 Logout</a>
        </div>
        <div class="col-md-9 mt-4">
            <h4>Selamat Datang, Dokter <?= htmlspecialchars($_SESSION['nama'] ?? ''); ?>!</h4>

            <?php if (isset($_SESSION['notifikasi_verifikasi'])): ?>
                <div class="alert alert-success mt-3">
                    <?= $_SESSION['notifikasi_verifikasi'] ?>
                </div>
                <?php unset($_SESSION['notifikasi_verifikasi']); ?>
            <?php endif; ?>

            <p class="text-muted">Gunakan menu di samping untuk mengakses fitur.</p>

            <div class="card mt-4">
                <div class="card-body">
                    <h5 class="card-title">Panduan Cepat</h5>
                    <ul>
                        <li>Lihat pesan masuk dari penjual di menu <strong>Konsultasi Masuk</strong>.</li>
                        <li>Perbarui informasi akun melalui menu <strong>Profil</strong>.</li>
                        <li>Logout setelah menyelesaikan sesi.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
