<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'dokter') {
    header("Location: ../../login.php");
    exit();
}

require_once '../../../config/database.php';
$id_dokter = $_SESSION['user_id'];

$sql = "
    SELECT k1.id AS konsultasi_id, k1.pesan, k1.status, k1.created_at,
           u.nama AS nama_penjual
    FROM konsultasi k1
    JOIN users u ON k1.id_user = u.id
    WHERE k1.id_dokter = ?
      AND k1.id = (
          SELECT MAX(k2.id)
          FROM konsultasi k2
          WHERE k2.id_user = k1.id_user AND k2.id_dokter = k1.id_dokter
      )
    ORDER BY k1.created_at DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_dokter);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Konsultasi Masuk - SIPERA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f2f2f2;
            margin-bottom: 70px;
        }
        .topbar {
            background-color: #1ca127;
            color: white;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .container {
            max-width: 900px;
            margin: 30px auto;
        }
        .konsultasi-card {
            background-color: #fff;
            border-radius: 10px;
            padding: 15px 20px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.05);
            margin-bottom: 15px;
        }
        .konsultasi-card h6 {
            margin: 0;
            font-weight: 600;
        }
        .konsultasi-card small {
            color: #666;
        }
        .btn-lihat {
            background-color: #1ca127;
            color: white;
            border: none;
            padding: 6px 14px;
            border-radius: 20px;
            text-decoration: none;
            font-size: 14px;
        }
        .status-badge {
            font-size: 12px;
            padding: 4px 10px;
            border-radius: 10px;
        }
        .status-baru { background-color: #ffc107; color: black; }
        .status-dibalas { background-color: #28a745; color: white; }
        .btn-kembali {
            background-color: #6c757d;
            color: white;
            border: none;
            padding: 8px 20px;
            border-radius: 20px;
            font-size: 14px;
            text-decoration: none;
        }
    </style>
</head>
<body>

<!-- Navbar SIPERA -->
<div class="topbar">
    <strong>SIPERA - Dokter</strong>
    <div>
        <a href="../../logout.php" class="text-white">Logout</a>
    </div>
</div>

<!-- Konten -->
<div class="container">
    <h4 class="my-4">Konsultasi Masuk</h4>

    <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="konsultasi-card">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <h6><?= htmlspecialchars($row['nama_penjual']) ?></h6>
                    <span class="badge status-badge <?= $row['status'] === 'dibalas' ? 'status-dibalas' : 'status-baru' ?>">
                        <?= ucfirst($row['status']) ?>
                    </span>
                </div>
                <small class="text-muted mb-2 d-block">📅 <?= date('d M Y H:i', strtotime($row['created_at'])) ?></small>
                <a href="detail_konsultasi.php?id=<?= $row['konsultasi_id'] ?>" class="btn btn-lihat">Lihat</a>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="alert alert-info">Belum ada konsultasi masuk.</div>
    <?php endif; ?>

    <!-- Tombol Kembali -->
        <a href="dashboard.php" class="btn btn-kembali">← Kembali ke Dashboard</a>
    </div>
</div>

</body>
</html>
