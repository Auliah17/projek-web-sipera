<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'pembeli') {
    header("Location: ../../login.php");
    exit();
}
require_once __DIR__ . '/../../../config/database.php';

$id_pembeli = $_SESSION['user_id'];

$query = $conn->query("
    SELECT p.*, t.jenis AS jenis_ternak, t.harga, t.usia, u.nama AS nama_penjual
    FROM pemesanan p
    JOIN ternak t ON p.id_ternak = t.id
    JOIN users u ON t.id_penjual = u.id
    WHERE p.id_pembeli = $id_pembeli
    ORDER BY p.id DESC
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Riwayat Pemesanan - SIPERA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        body {
            background-color: #f4f9f4;
            font-family: 'Segoe UI', sans-serif;
        }
        .navbar-sipera {
            background-color: #2ecc71;
        }
        .navbar-brand {
            color: white !important;
            font-weight: bold;
        }
        .container {
            max-width: 1100px;
            margin-top: 30px;
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        table {
            font-size: 15px;
        }
        .btn-secondary {
            background-color: #95a5a6;
            border: none;
        }
        .btn-secondary:hover {
            background-color: #7f8c8d;
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-sipera px-3 py-2">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">SIPERA</a>
        <div class="d-flex">
            <a class="nav-link text-white" href="/sipera/logout.php">Logout</a>
        </div>
    </div>
</nav>

<div class="container">
    <h3 class="mb-4">📜 Riwayat Pemesanan Saya</h3>

    <?php if ($query && $query->num_rows > 0): ?>
        <table class="table table-bordered table-striped">
            <thead class="table-success">
                <tr>
                    <th>Jenis Ternak</th>
                    <th>Harga</th>
                    <th>Jumlah</th>
                    <th>Usia</th>
                    <th>Penjual</th>
                    <th>Tanggal Kunjungan</th>
                    <th>Status</th>
                    <th>Metode</th>
                    <th>Bukti Pembayaran</th>
                </tr>
            </thead>
            <tbody>
            <?php while ($row = $query->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['jenis_ternak']) ?></td>
                    <td>Rp <?= number_format($row['harga']) ?></td>
                    <td><?= $row['jumlah'] ?></td>
                    <td><?= $row['usia'] ?> tahun</td>
                    <td><?= htmlspecialchars($row['nama_penjual']) ?></td>
                    <td><?= $row['tanggal_kunjungan'] ?></td>
                    <td><?= $row['status'] ?></td>
                    <td><?= $row['metode'] ?></td>
                    <td>
                        <?php if ($row['metode'] != 'Tunai' && $row['status'] === 'Belum Bayar'): ?>
                            <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#uploadModal<?= $row['id'] ?>">
                                Kirim Bukti
                            </button>

                            <!-- Modal Upload -->
                            <div class="modal fade" id="uploadModal<?= $row['id'] ?>" tabindex="-1" aria-labelledby="modalLabel<?= $row['id'] ?>" aria-hidden="true">
                                <div class="modal-dialog">
                                    <form method="post" action="upload_bukti.php" enctype="multipart/form-data" class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="modalLabel<?= $row['id'] ?>">Upload Bukti Pembayaran</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <input type="hidden" name="id_pemesanan" value="<?= $row['id'] ?>">
                                            <div class="mb-3">
                                                <label class="form-label">Pilih File Bukti (jpg/png)</label>
                                                <input type="file" name="bukti" class="form-control" accept=".jpg,.jpeg,.png" required>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-primary">Upload</button>
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        <?php elseif (!empty($row['bukti_pembayaran'])): ?>
                            <a href="/sipera/public/jpg/<?= htmlspecialchars($row['bukti_pembayaran']) ?>"
                               target="_blank"
                               class="btn btn-sm btn-success">Lihat Bukti</a>
                        <?php elseif ($row['metode'] != 'Tunai'): ?>
                            <em class="text-muted">Belum upload</em>
                        <?php else: ?>
                            <em class="text-muted">Tidak diperlukan</em>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="alert alert-info">Belum ada pemesanan.</div>
    <?php endif; ?>

    <!-- ✅ Tombol Kembali -->
    <a href="dashboard.php" class="btn btn-outline-success">
        <i class="bi bi-arrow-left-circle"></i> Kembali ke Dashboard
    </a>
</div>

</body>
</html>
