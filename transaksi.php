<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'pembeli') {
    header("Location: ../../login.php");
    exit();
}

require_once __DIR__ . '/../../../config/database.php';
$id_pembeli = $_SESSION['user_id'];

// Proses upload bukti pembayaran
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload_bukti'])) {
    $id_pemesanan = intval($_POST['id_pemesanan']);

    if (isset($_FILES['bukti_pembayaran']) && $_FILES['bukti_pembayaran']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['bukti_pembayaran']['tmp_name'];
        $file_ext = strtolower(pathinfo($_FILES['bukti_pembayaran']['name'], PATHINFO_EXTENSION));
        $file_name = 'bukti_' . time() . '_' . rand(1000, 9999) . '.' . $file_ext;
        $destination = __DIR__ . '/../../../public/jpg/' . $file_name;

        if (move_uploaded_file($file_tmp, $destination)) {
            $update = $conn->prepare("UPDATE pemesanan SET bukti_pembayaran = ? WHERE id = ? AND id_pembeli = ?");
            $update->bind_param("sii", $file_name, $id_pemesanan, $id_pembeli);
            $update->execute();
        }
    }
}

// Ambil data transaksi
$query = $conn->prepare("
    SELECT p.*, t.jenis
    FROM pemesanan p
    JOIN ternak t ON p.id_ternak = t.id
    WHERE p.id_pembeli = ?
    ORDER BY p.id DESC
");
$query->bind_param("i", $id_pembeli);
$query->execute();
$result = $query->get_result();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Riwayat Transaksi - SIPERA</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            background-color: #f0fdf4;
        }
        .navbar {
            background-color: #16a34a;
        }
        .navbar-brand, .nav-link {
            color: white !important;
        }
        .content-box {
            max-width: 1000px;
            margin: 40px auto;
            background: #fff;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 6px 15px rgba(0,0,0,0.08);
        }
        .btn-sipera {
            background-color: #16a34a;
            color: white;
        }
        .btn-sipera:hover {
            background-color: #15803d;
        }
        .alert-success {
            background-color: #bbf7d0;
            color: #166534;
            border-color: #86efac;
        }
        .alert-info {
            background-color: #e0f2fe;
            color: #0369a1;
            border-color: #7dd3fc;
        }
    </style>
</head>
<body>

<!-- Navbar SIPERA -->
<nav class="navbar navbar-expand-lg px-4">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="/sipera/index.php">SIPERA</a>
        <div class="d-flex">
            <a href="/sipera/logout.php" class="nav-link">
                <i class="bi bi-box-arrow-right"></i> Logout
            </a>
        </div>
    </div>
</nav>

<!-- Konten Utama -->
<div class="content-box">
    <h4 class="mb-4 text-success"><i class="bi bi-receipt"></i> Riwayat Transaksi Anda</h4>

    <?php if ($result->num_rows > 0): ?>
        <div class="table-responsive">
            <table class="table table-bordered align-middle text-center">
                <thead class="table-success">
                    <tr>
                        <th>No</th>
                        <th>Jenis Ternak</th>
                        <th>Jumlah</th>
                        <th>Total Harga</th>
                        <th>Metode</th>
                        <th>Status</th>
                        <th>Bukti Pembayaran</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= htmlspecialchars($row['jenis']) ?></td>
                        <td><?= $row['jumlah'] ?></td>
                        <td>Rp <?= number_format($row['total_harga'], 0, ',', '.') ?></td>
                        <td><?= htmlspecialchars($row['metode']) ?></td>
                        <td>
                            <?php if ($row['status'] == 'Menunggu') : ?>
                                <span class="badge bg-warning text-dark"><?= $row['status'] ?></span>
                            <?php elseif ($row['status'] == 'Diterima') : ?>
                                <span class="badge bg-success"><?= $row['status'] ?></span>
                            <?php else: ?>
                                <span class="badge bg-secondary"><?= $row['status'] ?></span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($row['metode'] !== 'Tunai'): ?>
                                <?php if (!empty($row['bukti_pembayaran'])): ?>
                                    <a href="/sipera/public/jpg/<?= htmlspecialchars($row['bukti_pembayaran']) ?>" 
                                       target="_blank" 
                                       class="btn btn-sm btn-outline-primary">Lihat Bukti</a>
                                <?php else: ?>
                                    <form method="POST" enctype="multipart/form-data" class="d-grid gap-1">
                                        <input type="hidden" name="id_pemesanan" value="<?= $row['id'] ?>">
                                        <input type="file" name="bukti_pembayaran" required class="form-control form-control-sm">
                                        <button type="submit" name="upload_bukti" class="btn btn-sm btn-success">Upload</button>
                                    </form>
                                <?php endif; ?>
                            <?php else: ?>
                                <span class="text-muted">Tunai - Tidak Perlu Bukti</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="alert alert-info">Belum ada transaksi yang dilakukan.</div>
    <?php endif; ?>

    <div class="mt-4">
        <a href="dashboard.php" class="btn btn-outline-success">
        <i class="bi bi-arrow-left-circle"></i> Kembali ke Dashboard
    </a>
</div>

</body>
</html>
