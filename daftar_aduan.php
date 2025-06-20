<?php
session_start();
require_once __DIR__ . '/../../../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <title>Daftar Aduan Pengguna - SIPERA Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- Bootstrap & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f1f8e9;
            margin: 0;
            padding: 0;
        }

        .navbar-sipera {
            background-color: #7b1e1e;
        }

        .navbar-brand {
            font-weight: bold;
            color: white;
            font-size: 20px;
        }

        .navbar-brand:hover {
            color: #f1f1f1;
        }

        .nav-link.text-white {
            color: white !important;
        }

        .nav-link.text-white:hover {
            text-decoration: underline;
        }

        .btn-logout {
            background-color: #7b1e1e;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 4px;
        }

        .btn-logout:hover {
            background-color: #5e1616;
        }

        .container-wrapper {
            max-width: 1100px;
            margin: 40px auto;
            background: white;
            padding: 30px;
            border-radius: 16px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
        }

        h2 {
            color: rgb(0, 0, 0);
            margin-bottom: 30px;
            text-align: center;
        }

        .table thead.thead-merah {
            background-color: #7b1e1e !important;
        }

        .table thead.thead-merah th {
            color: white !important;
            background-color: #7b1e1e !important;
        }

        .table td {
            vertical-align: middle;
            text-align: center;
            padding: 12px 10px;
        }

        a.detail-link {
            color: #7b1e1e;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.2s;
        }

        a.detail-link:hover {
            text-decoration: underline;
            color: #5e1616;
        }

        .btn-outline-success {
            border-color: #7b1e1e;
            color: #7b1e1e;
            font-weight: 500;
        }

        .btn-outline-success:hover {
            background-color: #7b1e1e;
            color: white;
        }
        .table thead.thead-merah {
            background-color: #7b1e1e !important;
        }
        .table thead.thead-merah th {
            color: white !important;
            background-color: #7b1e1e !important;
        }


    </style>
</head>
<body>

<!-- ✅ Navbar SIPERA -->
<nav class="navbar navbar-expand-lg navbar-sipera">
    <div class="container-fluid px-4">
        <a class="navbar-brand" href="#">SIPERA - Admin</a>
        <div class="ms-auto">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a href="/sipera/logout.php" class="nav-link text-white">
                        <i class="bi bi-box-arrow-right"></i> Logout
                    </a>
                </li>
            </ul>
        </div>
</nav>

<!-- ✅ Konten Daftar Aduan -->
<div class="container-wrapper">
    <h2><i class="bi bi-chat-dots-fill"></i> Daftar Aduan Pengguna</h2>
    
    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <thead class="thead-merah text-center">
                <tr>
                    <th>Nama Pengguna</th>
                    <th>Peran</th>
                    <th>Subjek</th>
                    <th>Tanggal</th>
                    <th>Status</th>
                    <th>Chat</th>
                </tr>
            </thead>
            <tbody id="aduan-body">
                <tr><td colspan="6" class="text-center">Memuat data...</td></tr>
            </tbody>
        </table>
    </div>

    <!-- ✅ Tombol Kembali -->
    <div class="mt-4 text-start">
        <a href="dashboard.php" class="btn btn-outline-success">
            ← Kembali ke Dashboard
        </a>
    </div>
</div>

<!-- ✅ Script AJAX -->
<script>
    function loadAduan() {
        fetch("ajax_aduan_admin.php")
            .then(res => res.text())
            .then(html => {
                document.getElementById("aduan-body").innerHTML = html;
            })
            .catch(err => {
                console.error("Gagal memuat aduan:", err);
            });
    }

    loadAduan(); // Muat awal
    setInterval(loadAduan, 5000); // Refresh tiap 5 detik
</script>

</body>
</html>
