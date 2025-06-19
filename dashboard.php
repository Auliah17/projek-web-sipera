<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'penjual') {
    header("Location: ../../login.php");
    exit();
}

require_once __DIR__ . '/../../../config/database.php';

$id_penjual = $_SESSION['user_id'];

// Ambil ternak milik penjual
$query = $conn->prepare("SELECT * FROM ternak WHERE id_penjual = ?");
$query->bind_param("i", $id_penjual);
$query->execute();
$result = $query->get_result();

// Hitung notifikasi pemesanan pending
$notifQuery = $conn->prepare("
    SELECT COUNT(*) as total_notif 
    FROM pemesanan p
    JOIN ternak t ON p.id_ternak = t.id
    WHERE t.id_penjual = ? AND p.status = 'pending'
");
$notifQuery->bind_param("i", $id_penjual);
$notifQuery->execute();
$notifResult = $notifQuery->get_result();
$notifData = $notifResult->fetch_assoc();
$total_notif = $notifData['total_notif'] ?? 0;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Penjual - SIPERA</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            background-color: #f4f6f8;
            padding-bottom: 90px;
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
        .animal-card {
            background: #fff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            transition: transform 0.2s;
            cursor: pointer;
        }
        .animal-card:hover {
            transform: translateY(-4px);
        }
        .image-container {
            width: 100%;
            height: 220px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            background: #f8f9fa;
        }
        .image-container img {
            max-width: 100%;
            max-height: 100%;
        }
        .animal-card .info {
            padding: 15px;
            text-align: center;
        }
        .animal-card .info h6 {
            font-weight: 600;
        }
        .animal-card .info small {
            color: #666;
        }
        .modal-body img {
            max-width: 100%;
            height: auto;
            border-radius: 10px;
            background-color: #f8f9fa;
            padding: 5px;
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
<nav class="navbar navbar-expand-lg">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="#">SIPERA</a>
        <div class="d-flex gap-3">
            <a class="nav-link" href="form_konsultasi.php">🩺 Konsultasi</a>
            <a class="nav-link position-relative" href="notifikasi.php">
                🔔 Notifikasi
                <?php if ($total_notif > 0): ?>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"><?= $total_notif ?></span>
                <?php endif; ?>
            </a>
            <a class="nav-link" href="riwayat.php">📜 Riwayat</a>
            <a class="nav-link" href="aduan_penjual.php">📢 Aduan</a>
            <a class="nav-link" href="/sipera/logout.php">🚪 Logout</a>
        </div>
    </div>
</nav>

<!-- Konten -->
<div class="container mt-4">

    <!-- Tombol tambah ternak -->
    <div class="text-center mb-4">
        <a href="form_tambah_ternak.php" class="btn btn-success w-100 py-2 fw-semibold">
            + Tambah Ternak Baru
        </a>
    </div>

    <!-- Galeri ternak -->
    <h5 class="mb-3">Daftar Ternak Anda</h5>
    <div class="row">
        <?php while($row = $result->fetch_assoc()): ?>
            <div class="col-md-4 mb-4">
                <div class="animal-card" data-bs-toggle="modal" data-bs-target="#modal<?= $row['id'] ?>">
                    <div class="image-container">
                        <img src="../../<?= htmlspecialchars($row['foto']) ?>" alt="<?= htmlspecialchars($row['jenis']) ?>">
                    </div>
                    <div class="info">
                        <h6><?= ucfirst($row['jenis']) ?></h6>
                        <div>Rp <?= number_format($row['harga'], 0, ',', '.') ?></div>
                        <p class="card-text mb-1"><small class="text-muted">Usia: <?= $row['usia'] ?> tahun</small></p>
                        <small>Stok: <?= $row['stok_sisa'] ?> / <?= $row['stok_awal'] ?></small>
                    </div>
                </div>
            </div>

            <!-- Modal Detail -->
            <div class="modal fade" id="modal<?= $row['id'] ?>" tabindex="-1" aria-hidden="true">
              <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title"><?= ucfirst($row['jenis']) ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                  </div>
                  <div class="modal-body text-center">
                    <img src="../../<?= htmlspecialchars($row['foto']) ?>" class="img-fluid" alt="Foto Ternak">
                    <p><strong>Harga:</strong> Rp <?= number_format($row['harga'], 0, ',', '.') ?></p>
                    <p><strong>Usia:</strong> <?= $row['usia'] ?> tahun</p>
                    <p><strong>Stok:</strong> <?= $row['stok_sisa'] ?> dari <?= $row['stok_awal'] ?> ekor</p>
                    <p><strong>Deskripsi:</strong> <?= htmlspecialchars($row['deskripsi']) ?></p>
                  </div>
                  <div class="modal-footer">
                    <a href="edit_ternak.php?id=<?= $row['id'] ?>" class="btn btn-warning">Edit</a>
                    <a href="hapus_ternak.php?id=<?= $row['id'] ?>" class="btn btn-danger" onclick="return confirm('Yakin ingin menghapus ternak ini?')">Hapus</a>
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                  </div>
                </div>
              </div>
            </div>

        <?php endwhile; ?>
    </div>
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
