<?php
require_once 'config/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama     = $_POST["nama"];
    $email    = $_POST["email"];
    $no_hp    = $_POST["no_hp"];
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);
    $role     = $_POST["role"];
    $alamat   = $_POST["alamat"];
    $upload_path = null;

    if ($role === "dokter") {
        if (isset($_FILES["dokumen"]) && $_FILES["dokumen"]["error"] == 0) {
            $dokumen_name = $_FILES["dokumen"]["name"];
            $dokumen_tmp = $_FILES["dokumen"]["tmp_name"];
            $ext = strtolower(pathinfo($dokumen_name, PATHINFO_EXTENSION));
            $file_name = uniqid() . "." . $ext;
            $upload_path = "public/uploads/" . $file_name;

            if (!move_uploaded_file($dokumen_tmp, $upload_path)) {
                echo "<div style='color:red;text-align:center;'>Gagal mengunggah dokumen.</div>";
                exit;
            }
        } else {
            echo "<div style='color:red;text-align:center;'>Dokumen wajib diunggah untuk dokter hewan.</div>";
            exit;
        }
    }

    $stmt = $conn->prepare("INSERT INTO users (nama, email, no_hp, password, role, alamat, foto_ktp) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssss", $nama, $email, $no_hp, $password, $role, $alamat, $upload_path);

    if ($stmt->execute()) {
        session_start();
        $_SESSION['user_id'] = $stmt->insert_id;
        $_SESSION['role'] = $role;
        header("Location: app/views/$role/dashboard.php");
        exit();
    } else {
        echo "<div style='color:red;text-align:center;'>Gagal mendaftar: " . $conn->error . "</div>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Registrasi Akun - SIPERA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f4f4f4;
            font-family: 'Segoe UI', sans-serif;
        }

        .box {
            max-width: 500px;
            margin: 50px auto;
            padding: 30px 25px;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.08);
        }

        .header-box {
            background: #7b1e1e;
            color: #fff;
            text-align: center;
            padding: 12px;
            font-size: 1.4rem;
            border-top-left-radius: 12px;
            border-top-right-radius: 12px;
            margin: -30px -25px 20px;
            font-weight: bold;
        }

        h4 {
            color: #7b1e1e;
        }

        .form-control, textarea {
            border-radius: 8px;
        }

        .btn-submit {
            background-color: #7b1e1e;
            color: white;
            font-weight: 600;
            border-radius: 8px;
        }

        .btn-submit:hover {
            background-color: #5e1616;
        }

        .text-center a {
            color: #7b1e1e;
            text-decoration: none;
            font-weight: 600;
        }

        .text-center a:hover {
            text-decoration: underline;
        }

        label {
            font-weight: 500;
        }
    </style>
</head>
<body>
<div class="box shadow">
    <div class="header-box">SIPERA</div>
    <h4 class="text-center mb-4">Daftar Akun</h4>
    <form method="POST" enctype="multipart/form-data">
        <div class="mb-2">
            <label>Nama lengkap</label>
            <input type="text" name="nama" class="form-control" required>
        </div>
        <div class="mb-2">
            <label>Email</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <div class="mb-2">
            <label>No. Telepon</label>
            <input type="text" name="no_hp" class="form-control" required>
        </div>
        <div class="mb-2">
            <label>Password (minimal 8 karakter)</label>
            <input type="password" name="password" class="form-control" minlength="8" required>
        </div>
        <div class="mb-2">
            <label>Alamat Lengkap</label>
            <textarea name="alamat" class="form-control" required></textarea>
        </div>
        <div class="mb-2">
            <label>Daftar sebagai</label>
            <select name="role" class="form-control" required>
                <option value="">-- Pilih --</option>
                <option value="penjual">Penjual</option>
                <option value="pembeli">Pembeli</option>
                <option value="dokter">Dokter Hewan</option>
            </select>
        </div>

        <div class="mb-2" id="dokumen-group" style="display: none;">
            <label>Upload Dokumen Sertifikasi Dokter Hewan</label>
            <input type="file" name="dokumen" class="form-control">
        </div>

        <div class="mb-3 form-check">
            <input type="checkbox" class="form-check-input" required>
            <label class="form-check-label">Saya menyetujui kebijakan privasi</label>
        </div>
        <button type="submit" class="btn btn-submit">Buat Akun</button>
        <div class="text-center mt-3">
            Sudah punya akun? <a href="login.php" style="color:green;">Login disini</a>
        </div>
    </form>
</div>

<script>
    function toggleDokumenField() {
        const role = document.querySelector('select[name="role"]').value;
        const dokumenGroup = document.getElementById('dokumen-group');
        const dokumenInput = dokumenGroup.querySelector('input');

        if (role === 'dokter') {
            dokumenGroup.style.display = 'block';
            dokumenInput.required = true;
        } else {
            dokumenGroup.style.display = 'none';
            dokumenInput.required = false;
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        toggleDokumenField(); // Panggil saat halaman load
        document.querySelector('select[name="role"]').addEventListener('change', toggleDokumenField);
    });
</script>
</body>
</html>
