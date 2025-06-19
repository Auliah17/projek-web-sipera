<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
require_once 'config/database.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($email) || empty($password)) {
        $error = "Email dan password harus diisi!";
    } else {
        $stmt = $conn->prepare("SELECT id, nama, password, role, verifikasi FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            if (password_verify($password, $user['password'])) {

                // Cek verifikasi khusus untuk dokter
                if ($user['role'] === 'dokter' && $user['verifikasi'] == 0) {
                    $error = "Akun Anda belum diverifikasi oleh admin.";
                } else {
                    // Simpan session login
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['nama'] = $user['nama'];
                    $_SESSION['role'] = $user['role'];

                    // Tambahkan notifikasi khusus dokter yang baru diverifikasi
                    if ($user['role'] === 'dokter' && $user['verifikasi'] == 1) {
                        $_SESSION['notifikasi_verifikasi'] = "Akun Anda telah berhasil diverifikasi oleh admin. Silakan lanjutkan.";
                    }

                    // Redirect sesuai role
                    switch ($user['role']) {
                        case 'admin':
                            header("Location: app/views/admin/dashboard.php");
                            break;
                        case 'dokter':
                            header("Location: app/views/dokter/dashboard.php");
                            break;
                        case 'penjual':
                            header("Location: app/views/penjual/dashboard.php");
                            break;
                        case 'pembeli':
                            header("Location: app/views/pembeli/dashboard.php");
                            break;
                        default:
                            $error = "Role tidak dikenali.";
                            break;
                    }
                    exit();
                }
            } else {
                $error = "Password salah!";
            }
        } else {
            $error = "Email tidak ditemukan!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login - SIPERA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .login-container {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
        }
        .logo {
            text-align: center;
            margin-bottom: 2rem;
            font-size: 2rem;
            font-weight: bold;
            color: #4CAF50;
        }
        .btn-login {
            background-color: #4CAF50;
            border: none;
            width: 100%;
            padding: 10px;
        }
        .btn-login:hover {
            background-color: #45a049;
        }
        .register-link {
            text-align: center;
            margin-top: 1rem;
        }
    </style>
</head>
<body>
<div class="login-container">
    <div class="logo">SIPERA</div>
    <h2 class="text-center mb-4">Login</h2>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="login.php">
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="text" class="form-control" id="email" name="email" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <button type="submit" class="btn btn-login btn-primary">Login</button>
    </form>

    <div class="register-link">
        Belum punya akun? <a href="register.php">Daftar disini</a>
    </div>
</div>
</body>
</html>
