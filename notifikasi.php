<?php
require_once __DIR__ . '/../../../config/database.php';

$query = "
    SELECT p.*, t.jenis AS jenis_ternak, u.nama AS nama_pembeli
    FROM pemesanan p
    JOIN ternak t ON p.id_ternak = t.id
    JOIN users u ON p.id_pembeli = u.id
    ORDER BY p.id DESC
";

$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Notifikasi Pemesanan - SIPERA</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', sans-serif;
        }

        nav.navbar {
            background-color: #4CAF50;
        }

        nav.navbar .navbar-brand,
        nav.navbar .nav-link {
            color: white;
        }

        nav.navbar .nav-link:hover {
            color: #e8e8e8;
        }

        .container {
            margin-top: 30px;
            margin-bottom: 40px;
        }

        .table th {
            background-color: #4CAF50;
            color: white;
            text-align: center;
        }

        .table td, .table th {
            vertical-align: middle;
        }

        .btn-konfirmasi {
            background-color: #4CAF50;
            color: white;
        }

        .btn-konfirmasi:hover {
            background-color: #45a049;
        }

        .status-label {
            font-weight: bold;
        }

        .status-dikonfirmasi {
            color: green;
        }

        .status-menunggu {
            color: orange;
        }

        h2 {
            color: #4CAF50;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .footer-action {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 20px;
        }

        @media (max-width: 576px) {
            .footer-action {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg px-3 py-2">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="#">SIPERA</a>
        <div class="d-flex gap-3">
            <a class="nav-link" href="/sipera/logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
        </div>
    </div>
</nav>

<!-- Container -->
<div class="container">
    <h2 class="text-start">📦 Notifikasi Pemesanan</h2>

    <?php if ($result && $result->num_rows > 0): ?>
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Pembeli</th>
                        <th>Jenis Ternak</th>
                        <th>Jumlah</th>
                        <th>Tgl Kunjungan</th>
                        <th>Metode</th>
                        <th>Bukti Pembayaran</th>
                        <th>Status</th>
                        <th>Catatan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= htmlspecialchars($row['nama_pembeli']) ?></td>
                            <td><?= htmlspecialchars($row['jenis_ternak']) ?></td>
                            <td><?= $row['jumlah'] ?></td>
                            <td><?= $row['tanggal_kunjungan'] ?></td>
                            <td><?= $row['metode'] ?></td>
                            <td class="text-center">
                                <?php if ($row['metode'] === 'Tunai'): ?>
                                    <span class="text-muted">-</span>
                                <?php elseif (!empty($row['bukti_pembayaran'])): ?>
                                    <a href="/sipera/public/jpg/<?= htmlspecialchars($row['bukti_pembayaran']) ?>" target="_blank" class="btn btn-sm btn-outline-success">Lihat</a>
                                <?php else: ?>
                                    <span class="text-danger">Belum Upload</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="status-label <?= $row['status'] === 'Dikonfirmasi' ? 'status-dikonfirmasi' : 'status-menunggu' ?>">
                                    <?= htmlspecialchars($row['status']) ?>
                                </span>
                            </td>
                            <td><?= htmlspecialchars($row['catatan']) ?></td>
                            <td class="text-center">
                                <?php if ($row['status'] != 'Dikonfirmasi'): ?>
                                    <form method="post" action="konfirmasi_pemesanan.php" onsubmit="return confirm('Konfirmasi pesanan ini?');">
                                        <input type="hidden" name="id_pemesanan" value="<?= $row['id'] ?>">
                                        <button type="submit" class="btn btn-konfirmasi btn-sm">Konfirmasi</button>
                                    </form>
                                <?php else: ?>
                                    <span class="text-success">✔️</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <!-- Footer bawah: tombol kembali sejajar pagination -->
        <div class="footer-action">
            <a href="dashboard.php" class="btn btn-outline-secondary">
                ← Kembali ke Dashboard
            </a>
            <!-- Tambahkan tombol pagination jika ingin -->
            <!-- <nav>
                <ul class="pagination mb-0">
                    <li class="page-item disabled"><a class="page-link" href="#">1</a></li>
                    <li class="page-item"><a class="page-link" href="#">2</a></li>
                    <li class="page-item"><a class="page-link" href="#">3</a></li>
                </ul>
            </nav> -->
        </div>
    <?php else: ?>
        <div class="alert alert-info text-center mt-4">
            <i class="bi bi-info-circle"></i> Tidak ada notifikasi pemesanan saat ini.
        </div>

        <div class="mt-4">
            <a href="dashboard.php" class="btn btn-outline-secondary">
                ← Kembali ke Dashboard
            </a>
        </div>
    <?php endif; ?>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
