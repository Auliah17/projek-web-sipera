<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'penjual') {
    header("Location: ../../login.php");
    exit();
}

require_once __DIR__ . '/../../../config/database.php';

$success = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_penjual = $_SESSION["user_id"];
    $jenis = trim($_POST["jenis"]);
    $usia = $_POST["usia"];
    $harga = $_POST["harga"];
    $status_kesehatan = $_POST["status_kesehatan"];
    $stok_awal = $_POST["stok_awal"];
    $stok_sisa = $stok_awal;
    $deskripsi = $_POST["deskripsi"];

    $target_dir = "../../../jpg/";
    $foto = $_FILES["foto"]["name"];
    $foto_tmp = $_FILES["foto"]["tmp_name"];
    $foto_ext = strtolower(pathinfo($foto, PATHINFO_EXTENSION));
    $foto_name = uniqid() . "." . $foto_ext;
    $foto_path = "jpg/" . $foto_name;

    $allowed = ['jpg', 'jpeg', 'png'];

    if (!in_array($foto_ext, $allowed)) {
        $error = "File foto harus berupa JPG atau PNG!";
    } else {
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $upload_foto = move_uploaded_file($foto_tmp, $target_dir . $foto_name);

        if ($upload_foto) {
            $stmt = $conn->prepare("INSERT INTO ternak 
                (id_penjual, jenis, usia, harga, status_kesehatan, stok_awal, stok_sisa, foto, deskripsi) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("isidsiiss", $id_penjual, $jenis, $usia, $harga, $status_kesehatan, $stok_awal, $stok_sisa, $foto_path, $deskripsi);
            if ($stmt->execute()) {
                $success = "✅ Ternak berhasil ditambahkan.";
            } else {
                $error = "❌ Gagal menyimpan ke database: " . $stmt->error;
            }
        } else {
            $error = "❌ Gagal mengunggah foto.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Ternak - SIPERA</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            background-color: #f0f4f7;
        }
        .navbar {
            background-color: #7b1e1e;
        }
        .navbar-brand, .nav-link {
            color: white !important;
        }
        .container-form {
            max-width: 700px;
            margin: 40px auto;
        }
        .card {
            border-radius: 15px;
        }
        .card-header {
            background-color: #7b1e1e;
            color: white;
            border-top-left-radius: 15px;
            border-top-right-radius: 15px;
            text-align: center;
        }
        .btn-maroon {
            background-color: #7b1e1e;
            color: white;
            border: none;
        }
        .btn-maroon:hover {
            background-color: #5c1616;
            color: white;
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

<!-- Form Tambah -->
<div class="container-form">
    <div class="card shadow-lg">
        <div class="card-header">
            <h4 class="mb-0"><i class="bi bi-plus-circle me-2"></i>Tambah Data Ternak</h4>
        </div>
        <div class="card-body">
            <?php if ($success): ?>
                <div class="alert alert-success"><?= $success; ?></div>
            <?php elseif ($error): ?>
                <div class="alert alert-danger"><?= $error; ?></div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label class="form-label">Jenis Ternak</label>
                    <select name="jenis" class="form-select" required>
                        <option value="">-- Pilih Jenis --</option>
                        <option value="kerbau">Kerbau</option>
                        <option value="ayam kampung">Ayam Kampung</option>
                        <option value="kambing">Kambing</option>
                        <option value="babi">Babi</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Usia (Tahun)</label>
                    <input type="number" name="usia" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Harga (Rp)</label>
                    <input type="number" name="harga" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Status Kesehatan</label>
                    <input type="text" name="status_kesehatan" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label">Stok Awal</label>
                    <input type="number" name="stok_awal" class="form-control" required>
                </div>
                <!-- stok_sisa diatur otomatis, jadi tidak ditampilkan -->
                <div class="mb-3">
                    <label class="form-label">Foto Ternak</label>
                    <input type="file" name="foto" class="form-control" accept=".jpg,.jpeg,.png" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Deskripsi Tambahan</label>
                    <textarea name="deskripsi" class="form-control" rows="3"></textarea>
                </div>
                <button type="submit" class="btn btn-maroon w-100">
                    <i class="bi bi-check-circle"></i> Simpan Data
                </button>
            </form>

            <a href="dashboard.php" class="btn btn-secondary w-100 mt-3">
                <i class="bi bi-arrow-left-circle"></i> Kembali ke Dashboard
            </a>
        </div>
    </div>
</div>

</body>
</html>
