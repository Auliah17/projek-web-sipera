<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

require_once __DIR__ . '/../../../config/database.php';

// PROSES VERIFIKASI
if (isset($_GET['verifikasi_id'])) {
    $id = $_GET['verifikasi_id'];
    $stmt = $conn->prepare("UPDATE users SET verifikasi = 1 WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header("Location: verifikasi.php?success=1");
        exit();
    }
}

// AMBIL DATA USER DOKTER YANG BELUM DIVERIFIKASI
$query = $conn->query("SELECT * FROM users WHERE verifikasi = 0 AND role = 'dokter'");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Verifikasi Akun Dokter - SIPERA</title>
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
        .btn-logout {
            background-color: #388e3c;
            color: white;
        }
        .btn-logout:hover {
            background-color: #2e7d32;
        }
        .container-box {
            max-width: 1000px;
            background: white;
            padding: 30px;
            border-radius: 16px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.08);
            margin: 40px auto;
        }
    </style>
</head>
<body>

<!-- ✅ Navbar SIPERA -->
<nav class="navbar navbar-expand-lg navbar-sipera">
    <div class="container-fluid px-4">
        <a class="navbar-brand" href="#">SIPERA - Admin</a>
        <div class="ms-auto">
            <a href="../../logout.php" class="btn btn-logout btn-sm">
                <i class="bi bi-box-arrow-right"></i> Logout
            </a>
        </div>
    </div>
</nav>

<!-- ✅ Konten Utama -->
<div class="container-box">
    <h3 class="mb-4 text-center"><i class="bi bi-shield-check"></i> Verifikasi Akun Dokter</h3>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success">✅ Akun dokter berhasil diverifikasi!</div>
    <?php endif; ?>

    <?php if ($query->num_rows > 0): ?>
        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead class="table-success text-center">
                    <tr>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($user = $query->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($user['nama']) ?></td>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                        <td><?= htmlspecialchars($user['role']) ?></td>
                        <td class="text-center">
                            <a href="verifikasi.php?verifikasi_id=<?= $user['id'] ?>" 
                               class="btn btn-sm btn-success">
                               <i class="bi bi-check-circle"></i> Verifikasi
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="alert alert-info">🎉 Semua akun dokter sudah diverifikasi!</div>
    <?php endif; ?>

    <!-- ✅ Tombol kembali -->
    <div class="d-flex justify-content-start mt-3">
        <a href="dashboard.php" class="btn btn-outline-success">
            ← Kembali ke Dashboard
        </a>
    </div>
</div>

</body>
</html>
