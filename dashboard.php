<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'pembeli') {
    header("Location: ../../login.php");
    exit();
}
require_once __DIR__ . '/../../../config/database.php';

$query = $conn->query("SELECT * FROM ternak ORDER BY id DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard Pembeli - SIPERA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f9f9f9; }
        .navbar {
            background-color: #4CAF50;
            padding: 10px 20px;
            color: white;
        }
        .navbar a {
            color: white;
            margin-right: 15px;
            text-decoration: none;
            font-weight: bold;
        }
        .animal-card img {
            height: 180px;
            object-fit: cover;
            border-radius: 6px;
        }
        .animal-card {
            border: 1px solid #ddd;
            padding: 10px;
            border-radius: 10px;
            background: #fff;
            text-align: center;
        }
        .btn-small {
            font-size: 0.85rem;
            padding: 4px 10px;
            margin: 2px;
        }
    </style>
</head>
<body>

<div class="navbar d-flex justify-content-between">
    <div><strong>SIPERA</strong></div>
    <div>
        <a href="#">Ternak</a>
        <a href="#">Konsultasi Ternak</a>
        <a href="#">Transaksi Saya</a>
        <a href="#">Profile</a>
        <a href="../../logout.php">Logout</a>
    </div>
</div>

<div class="container mt-4">
    <h4>Beranda Utama Pembeli</h4>

    <div class="my-3">
        <button class="btn btn-success">Atur Jadwal Kunjungan</button>
    </div>

    <div class="row g-2">
        <div class="col-md-6 mb-2">
            <label>Pilih Tanggal</label>
            <input type="date" class="form-control">
        </div>
        <div class="col-md-6 mb-2">
            <label>Pilih Waktu</label>
            <input type="time" class="form-control">
        </div>
    </div>

    <hr class="my-4">

    <div class="row">
        <?php while($row = $query->fetch_assoc()): ?>
        <div class="col-md-4 mb-4">
            <div class="animal-card shadow-sm">
                <img src="../../<?= $row['foto'] ?>" class="w-100 mb-2">
                <div><strong><?= ucfirst($row['jenis']) ?></strong></div>
                <div>Rp <?= number_format($row['harga']) ?></div>
                <div class="mt-2">
                    <button class="btn btn-primary btn-small">Chat Penjual</button>
                    <button class="btn btn-secondary btn-small">Chat Dokter</button>
                    <a href="form_pemesanan_ternak.php?id=<?= $row['id'] ?>" class="btn btn-success btn-small">Pesan Sekarang</a>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
    </div>

    <div class="mt-4">
        <input type="text" class="form-control w-50 d-inline-block" placeholder="Pencarian">
        <button class="btn btn-outline-dark"><i class="bi bi-search"></i></button>
    </div>
</div>
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</body>
</html>
