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
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            padding: 0;
        }

        .navbar {
            background-color: #7b1e1e !important;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
        }

        .navbar .navbar-brand {
            color: #fff !important;
            font-weight: bold;
        }

        .header-banner {
            background: url('jpg/tor.jpg') center center/cover no-repeat;
            height: 320px;
            position: relative;
            border: 3px solid #7b1e1e;
            border-radius: 16px;
            overflow: hidden;
        }

        .header-overlay {
            position: absolute;
            bottom: 25px;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(255, 255, 255, 0.95);
            padding: 25px 35px;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 4px 16px rgba(0,0,0,0.1);
        }

        .header-overlay h2 {
            font-size: 2.8rem;
            color: #7b1e1e;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .header-overlay p {
            font-size: 1.2rem;
            color: #444;
            margin-bottom: 15px;
        }

        .header-overlay .btn {
            padding: 8px 20px;
            font-weight: 500;
            font-size: 1rem;
            border-radius: 6px;
            transition: 0.3s ease;
        }

        .header-overlay .btn-success {
            background-color: #7b1e1e;
            border: none;
        }

        .header-overlay .btn-success:hover {
            background-color: #5e1616;
        }

        .header-overlay .btn-light {
            border: 1px solid #7b1e1e;
            color: #7b1e1e;
        }

        .header-overlay .btn-light:hover {
            background-color: #7b1e1e;
            color: #fff;
        }

        .animal-card img {
            height: 180px;
            object-fit: cover;
            border-radius: 10px;
            transition: transform 0.3s ease;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .animal-card img:hover {
            transform: scale(1.03);
        }

        .animal-card h5 {
            font-size: 1.1rem;
            font-weight: 600;
            color: #7b1e1e;
            margin-top: 10px;
        }

        h4 {
            font-size: 1.8rem;
            font-weight: bold;
            color: #7b1e1e;
        }

        p {
            font-size: 1.05rem;
            color: #333;
        }

        .frame-box {
            background: white;
            padding: 20px 25px;
            border-radius: 12px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.05);
            margin-top: 20px;
        }

        .feature-icon {
            margin-right: 12px;
            color: #7b1e1e;
        }

        ul li {
            margin-bottom: 10px;
            font-weight: 500;
        }

        .alert-success {
            background-color: #fcebea;
            color: #7b1e1e;
            border: 1px solid #f5c6cb;
        }

        footer {
            margin-top: 60px;
            background: #333;
            color: white;
            padding: 25px 0;
            font-size: 0.95rem;
        }

        footer small {
            display: block;
            margin-top: 5px;
            color: #bbb;
        }

        @media (max-width: 768px) {
            .header-overlay {
                width: 90%;
                padding: 20px;
            }

            .header-overlay h2 {
                font-size: 2rem;
            }

            .header-overlay p {
                font-size: 1rem;
            }

            .animal-card h5 {
                font-size: 1rem;
            }

            .frame-box {
                padding: 15px;
            }

            footer {
                text-align: center;
            }
        }

    </style>
</head>
<body>


<nav class="navbar navbar-expand-lg navbar-dark bg-success mb-4">
    <div class="container">
        <a class="navbar-brand fw-bold" href="#">SIPERA</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
    </div>
</nav>

<div class="container">
    
    <div class="header-banner mb-4">
        <div class="header-overlay">
            <h2>SIPERA</h2>
            <p>Sistem Informasi Penjualan Ternak</p>
            <a href="/sipera/register.php" class="btn btn-success me-2">Daftar</a>
            <a href="/sipera/login.php" class="btn btn-light border">Login</a>
        </div>
    </div>

    
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


<footer class="text-center">
    <div class="container">
        <p class="mb-1">© 2025 SIPERA - Sistem Informasi Penjualan Ternak</p>
        <small>Kontak: info@sipera.com | Instagram: @sipera.id</small>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
