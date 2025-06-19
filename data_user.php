<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../login.php");
    exit();
}
require_once __DIR__ . '/../../../config/database.php';

// Ambil semua user
$result = $conn->query("SELECT * FROM users ORDER BY nama ASC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Verifikasi Pengguna - SIPERA</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            background-color: #f1f8e9;
            font-family: 'Segoe UI', sans-serif;
        }
        .navbar-sipera {
            background-color: #43a047;
        }
        .navbar-brand {
            font-weight: bold;
            color: white;
        }
        .navbar-brand:hover {
            color: #f1f1f1;
        }
        .btn-logout {
            background-color: #388e3c;
            color: white;
        }
        .btn-logout:hover {
            background-color: #2e7d32;
        }
        .table-wrapper {
            max-width: 1100px;
            margin: 40px auto;
            background: white;
            padding: 30px;
            border-radius: 16px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.08);
        }
        h3 {
            color: #2e7d32;
            margin-bottom: 30px;
            text-align: center;
        }
        .table th {
            background-color: #c8e6c9;
        }
        .icon-verifikasi {
            font-size: 18px;
        }
        .btn-verifikasi {
            padding: 4px 10px;
        }
    </style>
</head>
<body>

<!-- ✅ Navbar SIPERA -->
<nav class="navbar navbar-expand-lg navbar-sipera">
    <div class="container-fluid px-4">
        <a class="navbar-brand" href="#">SIPERA - Admin</a>
        <div class="ms-auto">
            <a href="/sipera/logout.php" class="nav-link">
                <i class="bi bi-box-arrow-right"></i> Logout
            </a>
        </div>
    </div>
</nav>

<!-- ✅ Konten utama -->
<div class="table-wrapper">
    <h3><i class="bi bi-person-check-fill"></i> Verifikasi Dokumen Pengguna</h3>
    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-success text-center">
                <tr>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Alamat</th>
                    <th>Dokumen</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['nama']) ?></td>
                    <td><?= htmlspecialchars($row['email']) ?></td>
                    <td class="text-capitalize"><?= htmlspecialchars($row['role']) ?></td>
                    <td><?= htmlspecialchars($row['alamat']) ?></td>
                    
                    <!-- Kolom Dokumen: hanya untuk dokter -->
                    <td class="text-center">
                        <?php if ($row['role'] === 'dokter'): ?>
                            <?php if (!empty($row['foto_ktp'])): ?>
                                <a href="../../../<?= $row['foto_ktp'] ?>" target="_blank" class="btn btn-sm btn-outline-primary">Lihat Dokumen</a>
                            <?php else: ?>
                                <span class="text-danger">Tidak ada dokumen</span>
                            <?php endif; ?>
                        <?php else: ?>
                            <span class="text-muted">-</span>
                        <?php endif; ?>
                    </td>

                    <!-- Kolom Status: hanya untuk dokter -->
                    <td class="text-center">
                        <?php if ($row['role'] === 'dokter'): ?>
                            <?php if ($row['verifikasi'] == 1): ?>
                                <span class="text-success"><i class="bi bi-check-circle-fill icon-verifikasi"></i> Terverifikasi</span>
                            <?php else: ?>
                                <span class="text-warning"><i class="bi bi-clock icon-verifikasi"></i> Belum diverifikasi</span>
                            <?php endif; ?>
                        <?php else: ?>
                            <span class="text-muted">-</span>
                        <?php endif; ?>
                    </td>

                    <!-- Kolom Aksi: hanya untuk dokter -->
                    <td class="text-center">
                        <?php if ($row['role'] === 'dokter' && $row['verifikasi'] == 0): ?>
                            <a href="verifikasi.php?verifikasi_id=<?= $row['id'] ?>" class="btn btn-sm btn-success btn-verifikasi">
                                <i class="bi bi-check2-circle"></i> Verifikasi
                            </a>
                        <?php else: ?>
                            <span class="text-muted">-</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-start mt-3">
        <a href="dashboard.php" class="btn btn-outline-success">
            ← Kembali ke Dashboard
        </a>
    </div>
</div>

</body>
</html>
