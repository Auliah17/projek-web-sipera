<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'penjual') {
    header("Location: ../../login.php");
    exit();
}

require_once __DIR__ . '/../../../config/database.php';

$id_penjual = $_SESSION['user_id'];
$id = $_GET['id'] ?? null;

if (!$id) {
    header("Location: dashboard.php");
    exit();
}

$query = $conn->prepare("SELECT * FROM ternak WHERE id = ? AND id_penjual = ?");
$query->bind_param("ii", $id, $id_penjual);
$query->execute();
$result = $query->get_result();

if ($result->num_rows === 0) {
    header("Location: dashboard.php");
    exit();
}

$ternak = $result->fetch_assoc();

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $jenis = trim($_POST['jenis']);
    $harga = (int)$_POST['harga'];
    $stok_awal = (int)$_POST['stok_awal'];
    $stok_sisa = (int)$_POST['stok_sisa'];
    $deskripsi = trim($_POST['deskripsi']);

    if (empty($jenis)) $errors[] = "Jenis ternak harus diisi.";
    if ($harga <= 0) $errors[] = "Harga harus lebih dari 0.";
    if ($stok_awal < 0) $errors[] = "Stok awal tidak valid.";
    if ($stok_sisa < 0 || $stok_sisa > $stok_awal) $errors[] = "Stok sisa tidak valid.";

    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
        $allowed = ['jpg','jpeg','png','webp'];
        if (!in_array(strtolower($ext), $allowed)) {
            $errors[] = "Format foto harus JPG, JPEG, PNG, atau WEBP.";
        } else {
            $newName = 'public/jpg/' . 'ternak_' . time() . '.' . $ext;
            if (!move_uploaded_file($_FILES['foto']['tmp_name'], __DIR__ . '/../../../' . $newName)) {
                $errors[] = "Gagal mengupload foto.";
            } else {
                if (!empty($ternak['foto']) && file_exists(__DIR__ . '/../../../' . $ternak['foto'])) {
                    unlink(__DIR__ . '/../../../' . $ternak['foto']);
                }
                $ternak['foto'] = $newName;
            }
        }
    }

    if (empty($errors)) {
        $update = $conn->prepare("UPDATE ternak SET jenis = ?, harga = ?, stok_awal = ?, stok_sisa = ?, deskripsi = ?, foto = ? WHERE id = ? AND id_penjual = ?");
        $update->bind_param("siisssii", $jenis, $harga, $stok_awal, $stok_sisa, $deskripsi, $ternak['foto'], $id, $id_penjual);

        if ($update->execute()) {
            header("Location: dashboard.php");
            exit();
        } else {
            $errors[] = "Gagal memperbarui data ternak.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Ternak - SIPERA</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            background-color: #f9f9f9;
            padding-bottom: 100px;
        }
        .navbar-brand {
            font-weight: bold;
        }
        .form-container {
            background-color: #fff;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 0 10px rgba(0,0,0,0.05);
            margin-top: 20px;
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
        .btn-logout {
            background: none;
            border: none;
            color: #fff;
            padding: 0;
            margin: 0;
        }
        .btn-logout:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-success">
    <div class="container">
        <a class="navbar-brand" href="#">SIPERA</a>
        <div class="ms-auto">
            <a href="../../logout.php" class="btn btn-logout btn-sm">
                <i class="bi bi-box-arrow-right"></i> Logout
            </a>
        </div>
    </div>
</nav>

<!-- Konten Form Edit -->
<div class="container">
    <div class="form-container">
        <h4 class="mb-3">Edit Data Ternak</h4>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul>
                    <?php foreach($errors as $e): ?>
                        <li><?= htmlspecialchars($e) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="" method="post" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="jenis" class="form-label">Jenis Ternak</label>
                <input type="text" name="jenis" id="jenis" class="form-control" required value="<?= htmlspecialchars($ternak['jenis']) ?>">
            </div>
            <div class="mb-3">
                <label for="harga" class="form-label">Harga (Rp)</label>
                <input type="number" name="harga" id="harga" class="form-control" required min="1" value="<?= htmlspecialchars($ternak['harga']) ?>">
            </div>
            <div class="mb-3">
                <label for="stok_awal" class="form-label">Stok Awal</label>
                <input type="number" name="stok_awal" id="stok_awal" class="form-control" required min="0" value="<?= htmlspecialchars($ternak['stok_awal']) ?>">
            </div>
            <div class="mb-3">
                <label for="stok_sisa" class="form-label">Stok Sisa</label>
                <input type="number" name="stok_sisa" id="stok_sisa" class="form-control" required min="0" value="<?= htmlspecialchars($ternak['stok_sisa']) ?>">
            </div>
            <div class="mb-3">
                <label for="deskripsi" class="form-label">Deskripsi</label>
                <textarea name="deskripsi" id="deskripsi" class="form-control" rows="3"><?= htmlspecialchars($ternak['deskripsi']) ?></textarea>
            </div>
            <div class="mb-3">
                <label for="foto" class="form-label">Foto Saat Ini</label><br>
                <img src="../../../<?= htmlspecialchars($ternak['foto']) ?>" alt="Foto ternak" class="img-thumbnail mb-2" style="max-width: 150px;"><br>
                <input type="file" name="foto" id="foto" accept="image/*" class="form-control">
            </div>
            <div class="d-flex justify-content-start">
                <button type="submit" class="btn btn-success me-2">
                    <i class="bi bi-save"></i> Simpan Perubahan
                </button>
                <a href="dashboard.php" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Footer Navigasi -->
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
