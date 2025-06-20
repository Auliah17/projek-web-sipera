<?php
session_start();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin - SIPERA</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">

    <style>
        body {
            margin: 0;
            font-family: 'Roboto', sans-serif;
            background-color: #f5f5f5;
        }

        .main-container {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 250px;
            background: linear-gradient(180deg, rgb(136, 34, 34), #7b1e1e);
            color: white;
            padding: 20px;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 0;
        }

        .sidebar h5 {
            text-align: center;
            margin-bottom: 30px;
            font-size: 22px;
            font-weight: bold;
        }

        .sidebar a {
            display: flex;
            align-items: center;
            gap: 10px;
            color: white;
            padding: 12px 15px;
            margin-bottom: 15px;
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.2s ease-in-out;
        }

        .sidebar a:hover {
            background-color: rgba(255, 255, 255, 0.1);
            transform: translateX(5px);
        }

        .logout-button {
            background-color: #7b1e1e;
            padding: 10px 15px;
            border-radius: 8px;
        }

        .logout-button:hover {
            background-color: #5e1616;
        }

        .content {
            flex: 1;
            padding: 40px 30px;
        }

        .content h3 {
            font-weight: bold;
            color: #7b1e1e;
        }

        .content p {
            font-size: 16px;
            color: #555;
        }

        .alert {
            background-color: #e8f5e9;
            border: 1px solid #c8e6c9;
            color: #7b1e1e;
        }

        @media (max-width: 768px) {
            .main-container {
                flex-direction: column;
            }

            .sidebar {
                width: 100%;
                position: relative;
            }

        }
    </style>
</head>
<body>

<div class="main-container">
    <!-- Sidebar -->
    <div class="sidebar">
        <h5><i class="bi bi-speedometer2"></i> Admin SIPERA</h5>
        <a href="data_user.php"><i class="bi bi-people-fill"></i> Data Pengguna</a>
        <a href="daftar_aduan.php"><i class="bi bi-chat-left-text-fill"></i> Daftar Aduan</a>
        <a href="verifikasi.php"><i class="bi bi-person-check-fill"></i> Verifikasi Dokter</a>
        <a href="/sipera/logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
        </a>
    </div>

    <!-- Konten Utama -->
    <div class="content">
        <h3>Selamat Datang, <?= htmlspecialchars($_SESSION['nama']) ?>!</h3>
        <p>Gunakan menu di sebelah kiri untuk mengelola sistem SIPERA.</p>

        <div class="alert mt-4" role="alert">
            <i class="bi bi-info-circle-fill"></i> Pastikan semua data pengguna, verifikasi, dan laporan telah diperiksa dan ditindaklanjuti dengan benar.
        </div>
    </div>
</div>

</body>
</html>
