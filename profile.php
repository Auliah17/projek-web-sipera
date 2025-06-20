<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'pembeli') {
    header("Location: ../../login.php");
    exit();
}

require_once __DIR__ . '/../../../config/database.php';
$id_pembeli = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $id_pembeli);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();

$success = $error = "";
$foto = $data['foto'];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nama = $_POST['nama'];
    $no_hp = $_POST['no_hp'];
    $alamat = $_POST['alamat'];

    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
        $newFileName = uniqid('foto_') . '.' . $ext;

        $uploadDir = realpath(__DIR__ . '/../../../public/jpg/') . '/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $uploadPath = $uploadDir . $newFileName;
        if (move_uploaded_file($_FILES['foto']['tmp_name'], $uploadPath)) {
            $foto = $newFileName;
        } else {
            $error = "❌ Gagal upload foto.";
        }
    }

    if (!$error) {
        $update = $conn->prepare("UPDATE users SET nama = ?, no_hp = ?, alamat = ?, foto = ? WHERE id = ?");
        $update->bind_param("ssssi", $nama, $no_hp, $alamat, $foto, $id_pembeli);
        if ($update->execute()) {
            $success = "✅ Profil berhasil diperbarui.";
            $data['nama'] = $nama;
            $data['no_hp'] = $no_hp;
            $data['alamat'] = $alamat;
            $data['foto'] = $foto;
        } else {
            $error = "❌ Gagal memperbarui profil.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Profil Pembeli - SIPERA</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            background-color: #f0fdf4;
            font-family: 'Segoe UI', sans-serif;
        }
        .navbar {
            background-color: #16a34a;
        }
        .navbar-brand, .nav-link {
            color: white !important;
        }
        .container-box {
            max-width: 700px;
            margin: 40px auto;
            background: #ffffff;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.08);
        }
        .profile-img {
            width: 110px;
            height: 110px;
            object-fit: cover;
            border-radius: 50%;
            border: 3px solid #16a34a;
            display: block;
            margin: 20px auto;
        }
        .upload-label {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }
        .upload-label input[type="file"] {
            display: none;
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
        .alert-danger {
            background-color: #fee2e2;
            color: #991b1b;
            border-color: #fca5a5;
        }
    </style>
</head>
<body>

<!-- Navbar SIPERA -->
<nav class="navbar navbar-expand-lg px-3">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="/sipera/index.php">SIPERA</a>
        <div class="d-flex">
            <a href="/sipera/logout.php" class="nav-link">
                <i class="bi bi-box-arrow-right"></i> Logout
            </a>
        </div>
    </div>
</nav>

<!-- Konten Profil -->
<div class="container-box">
    <h3 class="text-center text-success mb-4">Profil Pembeli</h3>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php elseif ($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <div class="text-center">
            <?php
                $fotoPath = __DIR__ . '/../../../public/jpg/' . $data['foto'];
                $fotoUrl = '/Sipera/public/jpg/' . $data['foto'];
            ?>
            <img src="<?= (!empty($data['foto']) && file_exists($fotoPath)) ? $fotoUrl : '../../../assets/default-user.png' ?>" class="profile-img">
            <label class="upload-label">
                📷 Ganti Foto
                <input type="file" name="foto" accept=".jpg,.jpeg,.png">
            </label>
        </div>

        <div class="mb-3">
            <label class="form-label">Nama</label>
            <input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($data['nama']) ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" class="form-control" value="<?= htmlspecialchars($data['email']) ?>" readonly>
        </div>

        <div class="mb-3">
            <label class="form-label">No. Handphone</label>
            <input type="text" name="no_hp" class="form-control" value="<?= htmlspecialchars($data['no_hp']) ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Alamat</label>
            <textarea name="alamat" class="form-control" rows="3" required><?= htmlspecialchars($data['alamat']) ?></textarea>
        </div>

        <div class="d-grid gap-2">
            <button type="submit" class="btn btn-sipera">
                💾 Simpan Perubahan
            </button>
            <a href="dashboard.php" class="btn btn-outline-success">
                ⬅️ Kembali ke Dashboard
            </a>
        </div>
    </form>
</div>

</body>
</html>
