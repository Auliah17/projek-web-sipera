<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'dokter') {
    header("Location: ../../login.php");
    exit();
}

require_once '../../../config/database.php';
$id_dokter = $_SESSION['user_id'];
$success = $error = "";

// Proses update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama']);
    $email = trim($_POST['email']);
    $no_hp = trim($_POST['no_hp']);
    $alamat = trim($_POST['alamat']);

    if ($nama && $email && $no_hp && $alamat) {
        if (!empty($_FILES['foto']['name'])) {
            $foto_name = uniqid() . '_' . basename($_FILES['foto']['name']);
            $foto_path = '../../../public/jpg/' . $foto_name;
            move_uploaded_file($_FILES['foto']['tmp_name'], $foto_path);
            $updateFoto = ", foto = '$foto_name'";
        } else {
            $updateFoto = "";
        }

        $sql = "UPDATE users SET nama=?, email=?, no_hp=?, alamat=? $updateFoto WHERE id=? AND role='dokter'";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssi", $nama, $email, $no_hp, $alamat, $id_dokter);
        $stmt->execute();

        $success = ($stmt->affected_rows > 0) ? "Profil berhasil diperbarui." : "Tidak ada perubahan atau gagal memperbarui profil.";
    } else {
        $error = "Semua field harus diisi.";
    }
}

// Ambil data
$sql = "SELECT nama, email, no_hp, alamat, foto FROM users WHERE id=? AND role='dokter' LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_dokter);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Data tidak ditemukan.";
    exit();
}
$user = $result->fetch_assoc();
$foto_path = !empty($user['foto']) ? '../../../public/jpg/' . $user['foto'] : 'https://via.placeholder.com/150';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Profil Dokter - SIPERA</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Style -->
    <style>
        body {
            background-color: #f5f9f6;
            font-family: 'Segoe UI', sans-serif;
        }
        .navbar {
            background-color: #7b1e1e;
        }
        .navbar .navbar-brand, .navbar .nav-link {
            color: white;
        }
        .profile-container {
            max-width: 600px;
            margin: 90px auto 30px;
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 0 12px rgba(0, 0, 0, 0.1);
        }
        .profile-image {
            width: 130px;
            height: 130px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid #ddd;
        }
        .upload-label {
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            margin-top: 10px;
            color: #444;
        }
        .upload-label img {
            margin-right: 6px;
        }
        .alert {
            font-size: 14px;
            padding: 10px 16px;
        }
        .btn-sipera-merah {
            background-color: #7b1e1e;
            color: white;
            border: none;
        }
        .btn-sipera-merah:hover {
            background-color: #5c1616;
            color: white;
        }

    </style>
</head>
<body>

<!-- ✅ Navbar SIPERA -->
<nav class="navbar navbar-expand-lg fixed-top">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">SIPERA - Dokter</a>
        <div class="d-flex">
            <a href="../../logout.php" class="nav-link"><i class="bi bi-box-arrow-right"></i> Logout</a>
        </div>
    </div>
</nav>

<!-- ✅ Konten Profil -->
<div class="profile-container">
    <h3 class="text-center" style="color: #7b1e1e;">Profil Dokter</h3>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php elseif ($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <div class="text-center">
        <img src="<?= htmlspecialchars($foto_path) ?>" class="profile-image mb-2" id="previewFoto">
        <br>
        <label for="foto" class="upload-label">
            <img src="https://img.icons8.com/ios-filled/24/000000/camera.png" alt="Camera Icon"/>
            <span>Ganti Foto</span>
        </label>
    </div>

    <form method="POST" action="" enctype="multipart/form-data" class="mt-4">
        <input type="file" name="foto" id="foto" accept="image/*" style="display: none;" onchange="previewImage(event)">

        <div class="mb-3">
            <label class="form-label">Nama</label>
            <input type="text" name="nama" class="form-control" required value="<?= htmlspecialchars($user['nama']) ?>">
        </div>
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" required value="<?= htmlspecialchars($user['email']) ?>">
        </div>
        <div class="mb-3">
            <label class="form-label">No. HP</label>
            <input type="text" name="no_hp" class="form-control" required value="<?= htmlspecialchars($user['no_hp']) ?>">
        </div>
        <div class="mb-3">
            <label class="form-label">Alamat</label>
            <textarea name="alamat" class="form-control" required rows="3"><?= htmlspecialchars($user['alamat']) ?></textarea>
        </div>

        <button type="submit" class="btn btn-sipera-merah w-100">Simpan Perubahan</button>
    </form>

    <div class="text-center mt-4">
        <a href="dashboard.php" class="btn btn-secondary">← Kembali ke Dashboard</a>
    </div>
</div>

<script>
function previewImage(event) {
    const reader = new FileReader();
    reader.onload = function(){
        document.getElementById('previewFoto').src = reader.result;
    };
    reader.readAsDataURL(event.target.files[0]);
}
</script>

</body>
</html>
