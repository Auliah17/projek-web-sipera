<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'penjual') {
    header("Location: ../../login.php");
    exit();
}

require_once __DIR__ . '/../../../config/database.php';
$id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT nama, email, no_hp, alamat, foto FROM users WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();

$success = $error = '';
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $no_hp = $_POST['no_hp'];
    $alamat = $_POST['alamat'];
    $foto_nama = $data['foto'];

    if (!empty($_FILES['foto']['name'])) {
        $fileTmp = $_FILES['foto']['tmp_name'];
        $fileName = basename($_FILES['foto']['name']);
        $fileType = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        $allowed = ['jpg', 'jpeg', 'png'];
        if (in_array($fileType, $allowed)) {
            $targetDir = realpath(__DIR__ . "/../../../public/jpg/") . "/";
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0777, true);
            }

            $newFileName = time() . "_" . preg_replace('/\s+/', '_', $fileName);
            $targetFile = $targetDir . $newFileName;

            if (move_uploaded_file($fileTmp, $targetFile)) {
                $foto_nama = $newFileName;
            } else {
                $error = "❌ Gagal upload foto.";
            }
        } else {
            $error = "❌ Format foto tidak valid. Gunakan JPG atau PNG.";
        }
    }

    if (!$error) {
        $update = $conn->prepare("UPDATE users SET nama = ?, email = ?, no_hp = ?, alamat = ?, foto = ? WHERE id = ?");
        $update->bind_param("sssssi", $nama, $email, $no_hp, $alamat, $foto_nama, $id);
        if ($update->execute()) {
            $success = "✅ Profil berhasil diperbarui.";
            $_SESSION['nama'] = $nama;
            $data = compact('nama', 'email', 'no_hp', 'alamat');
            $data['foto'] = $foto_nama;
        } else {
            $error = "❌ Gagal memperbarui profil.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Profil Penjual - SIPERA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #e9f5ee;
            font-family: 'Segoe UI', sans-serif;
        }
        .navbar-sipera {
            background-color: #2ecc71;
        }
        .navbar-brand {
            font-weight: bold;
            color: white !important;
        }
        .nav-link.logout-link {
            color: white !important;
            font-weight: 500;
        }
        .container-box {
            background: #fff;
            padding: 30px;
            margin-top: 40px;
            border-radius: 12px;
            box-shadow: 0 0 12px rgba(0,0,0,0.1);
        }
        .profile-img {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 50%;
            display: block;
            margin: 0 auto 20px auto;
            border: 3px solid #2ecc71;
        }
        .upload-label {
            display: block;
            text-align: center;
            color: #2c3e50;
            cursor: pointer;
            margin-bottom: 20px;
        }
        .upload-label input {
            display: none;
        }
        .btn-success {
            background-color: #27ae60;
            border: none;
        }
        .btn-success:hover {
            background-color: #219150;
        }
        textarea.form-control {
            resize: vertical;
        }
    </style>
</head>
<body>

<!-- ✅ Navbar SIPERA -->
<nav class="navbar navbar-expand-lg navbar-sipera px-4">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">SIPERA</a>
        <div class="ms-auto">
            <a class="nav-link logout-link" href="/sipera/logout.php">Logout</a>
        </div>
    </div>
</nav>

<div class="container">
    <div class="container-box">
        <h4 class="text-center mb-4">Edit Profil Anda</h4>

        <?php if ($success): ?>
            <div class="alert alert-success"><?= $success ?></div>
        <?php elseif ($error): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <div class="text-center">
                <img src="<?= $data['foto'] ? '/Sipera/public/jpg/' . htmlspecialchars($data['foto']) : 'https://via.placeholder.com/120?text=👤' ?>" class="profile-img" alt="Foto Profil">
                <label class="upload-label">
                    📷 Ganti Foto
                    <input type="file" name="foto" accept=".jpg,.jpeg,.png">
                </label>
            </div>

            <div class="mb-3">
                <label>Nama Lengkap</label>
                <input type="text" name="nama" value="<?= htmlspecialchars($data['nama']) ?>" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Email</label>
                <input type="email" name="email" value="<?= htmlspecialchars($data['email']) ?>" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>No. Handphone</label>
                <input type="text" name="no_hp" value="<?= htmlspecialchars($data['no_hp']) ?>" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Alamat Lengkap</label>
                <textarea name="alamat" class="form-control" rows="3" required><?= htmlspecialchars($data['alamat']) ?></textarea>
            </div>

            <a href="dashboard.php" class="btn btn-secondary">🔙 Kembali</a>
            <button type="submit" class="btn btn-success">💾 Simpan Perubahan</button>
        </form>
    </div>
</div>

</body>
</html>
