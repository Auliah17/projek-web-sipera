<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'penjual') {
    header("Location: ../../login.php");
    exit();
}

require_once __DIR__ . '/../../../config/database.php';

$id_penjual = $_SESSION['user_id'];
$keyword = isset($_GET['keyword']) ? "%" . $_GET['keyword'] . "%" : "%";

// Ambil ternak milik penjual berdasarkan keyword
$query = $conn->prepare("SELECT * FROM ternak WHERE id_penjual = ? AND (jenis LIKE ? OR deskripsi LIKE ?)");
$query->bind_param("iss", $id_penjual, $keyword, $keyword);
$query->execute();
$result = $query->get_result();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pencarian Ternak - Penjual</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            background-color: #f4f6f8;
            padding-bottom: 90px;
            font-family: 'Segoe UI', sans-serif;
        }
        .navbar-sipera {
            background-color: #2ecc71;
        }
        .navbar-brand {
            font-weight: bold;
            color: #fff !important;
        }
        .nav-link.logout {
            color: white !important;
        }
        h5 {
            color: #1ca127;
            font-weight: bold;
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
            height: 200px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f8f9fa;
            overflow: hidden;
        }
        .image-container img {
            max-width: 100%;
            max-height: 100%;
        }
        .info {
            padding: 15px;
            text-align: center;
        }
        .info h6 {
            font-weight: 600;
            margin-bottom: 4px;
        }
        .info small {
            color: #777;
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
            padding: 10px 0;
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

<!-- ✅ Navbar SIPERA -->
<nav class="navbar navbar-expand-lg navbar-sipera px-4">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">SIPERA - Penjual</a>
        <a class="nav-link text-white" href="/sipera/logout.php">
                <i class="bi bi-box-arrow-right"></i> Logout
        </div>
    </div>
</nav>

<!-- Konten -->
<div class="container mt-4">
    <!-- Form Pencarian -->
    <form method="GET" class="d-flex mb-4">
        <input type="text" name="keyword" class="form-control me-2" placeholder="Cari jenis atau deskripsi ternak..." value="<?= htmlspecialchars($_GET['keyword'] ?? '') ?>">
        <button type="submit" class="btn btn-success"><i class="bi bi-search"></i> Cari</button>
    </form>

    <!-- Hasil Pencarian -->
    <h5 class="mb-3">📋 Hasil Pencarian Ternak Anda</h5>
    <div class="row">
        <?php if ($result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
                <div class="col-md-4 mb-4">
                    <div class="animal-card" data-bs-toggle="modal" data-bs-target="#modal<?= $row['id'] ?>">
                        <div class="image-container">
                            <img src="../../../<?= htmlspecialchars($row['foto']) ?>" alt="<?= htmlspecialchars($row['jenis']) ?>">
                        </div>
                        <div class="info">
                            <h6><?= ucfirst($row['jenis']) ?></h6>
                            <p class="mb-1">Rp <?= number_format($row['harga'], 0, ',', '.') ?></p>
                            <small>Usia: <?= $row['usia'] ?> tahun</small><br>
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
                                <img src="../../../<?= htmlspecialchars($row['foto']) ?>" class="img-fluid mb-3" alt="Foto Ternak">
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
        <?php else: ?>
            <div class="col-12 text-center text-muted mt-4">
                <i class="bi bi-exclamation-circle"></i> Tidak ada hasil ditemukan.
            </div>
        <?php endif; ?>
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
