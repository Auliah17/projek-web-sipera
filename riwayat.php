<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'penjual') {
    header("Location: ../../login.php");
    exit();
}

require_once __DIR__ . '/../../../config/database.php';

$id_penjual = $_SESSION['user_id'];
$query = "SELECT * FROM ternak WHERE id_penjual = $id_penjual ORDER BY created_at DESC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Riwayat Ternak - SIPERA</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            background-color: #f4f6f8;
            padding-bottom: 80px;
        }
        .navbar {
            background-color: #388E3C;
        }
        .navbar .nav-link, .navbar .navbar-brand {
            color: white;
        }
        .navbar .nav-link:hover {
            color: #d4f4d7;
        }
        .table th {
            background-color: #e0f2f1;
        }
        .footer-menu {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            background: #fff;
            border-top: 1px solid #ccc;
            display: flex;
            justify-content: space-around;
            padding: 8px 0;
            z-index: 1000;
        }
        .footer-menu a {
            text-align: center;
            color: #333;
            text-decoration: none;
            font-size: 12px;
        }
        .footer-menu i {
            font-size: 20px;
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg px-3 py-2">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold text-white" href="#">SIPERA</a>
        <div class="d-flex gap-3">
            <a class="nav-link text-white" href="/sipera/logout.php">
                <i class="bi bi-box-arrow-right"></i> Logout
            </a>
        </div>
    </div>
</nav>

<!-- Konten -->
<div class="container mt-4 mb-5">
    <h4 class="mb-4 text-center"><i class="bi bi-clock-history me-2"></i>Riwayat Data Ternak</h4>

    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <thead class="text-center">
                <tr>
                    <th>No</th>
                    <th>Jenis</th>
                    <th>Usia</th>
                    <th>Harga</th>
                    <th>Kesehatan</th>
                    <th>Stok Awal</th>
                    <th>Stok Sisa</th>
                    <th>Deskripsi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $no = 1;
                while ($row = mysqli_fetch_assoc($result)):
                ?>
                <tr>
                    <td class="text-center"><?= $no++; ?></td>
                    <td><?= htmlspecialchars($row['jenis']); ?></td>
                    <td><?= htmlspecialchars($row['usia']); ?> tahun</td>
                    <td>Rp<?= number_format($row['harga'], 0, ',', '.'); ?></td>
                    <td><?= htmlspecialchars($row['status_kesehatan']); ?></td>
                    <td class="text-center"><?= $row['stok_awal']; ?></td>
                    <td class="text-center"><?= $row['stok_sisa']; ?></td>
                    <td><?= nl2br(htmlspecialchars($row['deskripsi'])); ?></td>
                </tr>
                <?php endwhile; ?>
                <?php if (mysqli_num_rows($result) === 0): ?>
                    <tr><td colspan="8" class="text-center text-muted">Belum ada data ternak.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <a href="dashboard.php" class="btn btn-outline-success">
        <i class="bi bi-arrow-left-circle"></i> Kembali ke Dashboard
    </a>
</div>

<!-- Footer Menu -->
<div class="footer-menu">
    <a href="dashboard.php">
        <i class="bi bi-house-door-fill"></i><div>Beranda</div>
    </a>
    <a href="pencarian.php">
        <i class="bi bi-search"></i><div>Cari</div>
    </a>
    <a href="inbox_chat.php">
        <i class="bi bi-chat-dots-fill"></i><div>Obrolan</div>
    </a>
    <a href="profile.php">
        <i class="bi bi-person-circle"></i><div>Profil</div>
    </a>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
