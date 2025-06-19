<?php
session_start();
require_once __DIR__ . '/../../../config/database.php';

$keyword = isset($_GET['keyword']) ? "%" . $_GET['keyword'] . "%" : "%";

$query = $conn->prepare("SELECT * FROM ternak WHERE jenis LIKE ? OR deskripsi LIKE ?");
$query->bind_param("ss", $keyword, $keyword);
$query->execute();
$result = $query->get_result();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pencarian Ternak - SIPERA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: #f4f4f4;
        }
        .header {
            background: #4CAF50;
            color: white;
            padding: 15px 20px;
            font-size: 18px;
            font-weight: bold;
        }
        .search-box {
            padding: 20px;
            background: white;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            border-bottom: 1px solid #ccc;
        }
        .search-box input {
            width: 100%;
            max-width: 500px;
        }
        .card-animal {
            border: 1px solid #ccc;
            border-radius: 10px;
            overflow: hidden;
            background: white;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            transition: 0.3s;
        }
        .card-animal:hover {
            box-shadow: 0 3px 8px rgba(0,0,0,0.15);
        }
        .card-animal img {
            height: 160px;
            width: 100%;
            object-fit: cover;
        }
        .card-body {
            padding: 10px;
        }
        .card-title {
            margin: 0;
            font-size: 16px;
            font-weight: 600;
        }
        .card-price {
            font-weight: bold;
            color: #4CAF50;
        }
        .card-menu {
            position: absolute;
            top: 10px;
            right: 10px;
        }
    </style>
</head>
<body>

<div class="header">Pencarian</div>

<form method="GET" class="search-box">
    <input type="text" name="keyword" class="form-control" placeholder="Cari ternak..." value="<?= htmlspecialchars($_GET['keyword'] ?? '') ?>">
    <button class="btn btn-success" type="submit"><i class="bi bi-search"></i> Cari</button>
</form>

<div class="container mt-3 mb-5">
    <div class="row">
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="col-md-4 mb-4">
                <div class="card-animal position-relative">
                    <img src="../../../<?= $row['foto'] ?>" alt="<?= $row['jenis'] ?>">
                    <div class="card-body">
                        <p class="card-title"><?= ucfirst($row['jenis']) ?></p>
                        <p class="card-price">Rp <?= number_format($row['harga'], 0, ',', '.') ?></p>
                        <small>Stok: <?= $row['stok'] ?> | Tersisa: <?= $row['stok_tersisa'] ?></small>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>

</body>
</html>
