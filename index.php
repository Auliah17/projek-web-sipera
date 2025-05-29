<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIPERA - Sistem Informasi Penjualan Ternak</title>
    <link rel="shortcut icon" href="jpg/favicon.png" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {
            background-color: #f5f5f5;
        }
        .header-banner {
            background: url('jpg/tor.jpg') center center/cover no-repeat;
            height: 300px;
            position: relative;
            border: 3px solid #4CAF50;
            border-radius: 10px;
        }
        .header-overlay {
            position: absolute;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(255, 255, 255, 0.9);
            padding: 20px 30px;
            border-radius: 10px;
            text-align: center;
        }
        .header-overlay h2 {
            font-size: 2.8rem;
            color: #4CAF50;
            font-weight: bold;
        }
        .header-overlay p {
            font-size: 1.3rem;
            font-weight: 500;
            color: #333;
        }
        .animal-card img {
            height: 180px;
            object-fit: cover;
            border-radius: 10px;
        }
        .animal-card h5 {
            font-size: 1.2rem;
            font-weight: bold;
            color: #4CAF50;
        }
        .frame-box {
            background: white;
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0 0 5px rgba(0,0,0,0.1);
            margin-top: 20px;
        }
        .feature-icon {
            margin-right: 10px;
            animation: bounce 1.5s infinite;
            color: #4CAF50;
        }
        @keyframes bounce {
            0%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-5px);
            }
        }
        h4 {
            font-size: 1.8rem;
            font-weight: bold;
            color: #4CAF50;
        }
        p {
            font-size: 1.1rem;
        }
        footer {
            margin-top: 50px;
            background: #333;
            color: white;
            padding: 20px 0;
        }
    </style>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-dark bg-success mb-4">
    <div class="container">
        <a class="navbar-brand fw-bold" href="#">SIPERA</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
       
</nav>

<div class="container">
    <!-- Header -->
    <div class="header-banner mb-4">
        <div class="header-overlay">
            <h2>SIPERA</h2>
            <p>Sistem Informasi Penjualan Ternak</p>
            <a href="/sipera/register.php" class="btn btn-success me-2">Daftar</a>
            <a href="/sipera/login.php" class="btn btn-light border">Login</a>
        </div>
    </div>

    <!-- Jenis Ternak -->
    <h4 class="text-center" id="jenis">Jenis Hewan Ternak Kami</h4>
    <p class="text-center text-muted mb-4">Temukan berbagai jenis hewan ternak berkualitas</p>

    <div class="row text-center">
        <div class="col-md-3 mb-3 animal-card">
            <img src="jpg/kerbau.jpg" class="img-fluid" alt="Kerbau">
            <h5 class="mt-2">Kerbau</h5>
        </div>
        <div class="col-md-3 mb-3 animal-card">
            <img src="jpg/Babi.jpg" class="img-fluid" alt="Babi">
            <h5 class="mt-2">Babi</h5>
        </div>
        <div class="col-md-3 mb-3 animal-card">
            <img src="jpg/kambing.jpg" class="img-fluid" alt="Kambing">
            <h5 class="mt-2">Kambing</h5>
        </div>
        <div class="col-md-3 mb-3 animal-card">
            <img src="jpg/ayam.jpg" class="img-fluid" alt="Ayam">
            <h5 class="mt-2">Ayam</h5>
        </div>
    </div>

    <!-- Informasi -->
    <div class="row mt-4" id="fitur">
        <div class="col-md-6">
            <div class="frame-box">
                <div class="alert alert-success fw-bold">Selamat Datang di SIPERA</div>
                <p>
                    Sistem Informasi Penjualan Ternak (SIPERA) adalah platform yang memudahkan peternak untuk memasarkan hewan ternak mereka dan memudahkan pembeli untuk menemukan ternak berkualitas.
                </p>
            </div>
        </div>
        <div class="col-md-6">
            <div class="frame-box">
                <div class="alert alert-success fw-bold">Fitur SIPERA</div>
                <ul class="list-unstyled">
                    <li><i class="fas fa-search feature-icon"></i> Pencarian Ternak dengan Filter</li>
                    <li><i class="fas fa-wallet feature-icon"></i> Sistem Pembayaran DANA & COD</li>
                    <li><i class="fas fa-calendar-alt feature-icon"></i> Jadwal Kunjungan</li>
                    <li><i class="fas fa-user-md feature-icon"></i> Konsultasi dengan Dokter Hewan</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Footer -->
<footer class="text-center">
    <div class="container">
        <p class="mb-1">© 2025 SIPERA - Sistem Informasi Penjualan Ternak</p>
        <small>Kontak: info@sipera.com | Instagram: @sipera.id</small>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
