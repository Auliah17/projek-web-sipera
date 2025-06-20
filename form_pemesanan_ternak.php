<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'pembeli') {
    header("Location: ../../login.php");
    exit();
}

require_once '../../../config/database.php';

$id_ternak = $_GET['id'] ?? 0;
$query = $conn->prepare("SELECT * FROM ternak WHERE id = ?");
$query->bind_param("i", $id_ternak);
$query->execute();
$result = $query->get_result();
$ternak = $result->fetch_assoc();

if (!$ternak) {
    echo "<div class='text-center text-danger'>Ternak tidak ditemukan.</div>";
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Pemesanan Ternak - SIPERA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #f0fdf4;
        }
        .container {
            max-width: 850px;
        }
        .header-box {
            background-color: #22c55e;
            color: white;
            text-align: center;
            padding: 12px;
            font-size: 20px;
            font-weight: bold;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .ternak-img {
            width: 100%;
            height: auto;
            border-radius: 10px;
            border: 3px solid #16a34a;
        }
        .qr-img {
            width: 180px;
            height: auto;
            border: 2px solid #16a34a;
            border-radius: 10px;
        }
        .dana-info-box {
            border-left: 6px solid #16a34a;
            background: #e6f8ec;
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0 0 5px rgba(22, 163, 74, 0.3);
        }
        .navbar {
            background-color: #16a34a;
        }
        .navbar-brand, .nav-link {
            color: white !important;
            font-weight: bold;
        }
    </style>
</head>
<body>

<!-- ✅ Navbar SIPERA -->
<nav class="navbar navbar-expand-lg px-3 mb-4 shadow-sm">
    <div class="container-fluid">
        <span class="navbar-brand">SIPERA</span>
        <div class="d-flex">
            <a href="/sipera/logout.php" class="nav-link">
                <i class="bi bi-box-arrow-right"></i> Logout
            </a>
        </div>
    </div>
</nav>

<div class="container bg-white shadow p-4 rounded mb-5">

    <!-- ✅ Notifikasi -->
    <?php if (isset($_SESSION['notif'])): ?>
        <div class="alert alert-<?= $_SESSION['notif']['type'] ?> alert-dismissible fade show" role="alert">
            <?= $_SESSION['notif']['message'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['notif']); ?>
    <?php endif; ?>

    <div class="header-box">PEMESANAN TERNAK</div>

    <div class="row mb-4">
        <div class="col-md-6">
            <img src="../../<?= htmlspecialchars($ternak['foto']) ?>" class="ternak-img" alt="Foto Ternak">
        </div>
        <div class="col-md-6">
            <h5 class="fw-bold"><?= strtoupper($ternak['jenis']) ?></h5>
            <p>Rp <?= number_format($ternak['harga']) ?> / ekor</p>
            <p>Usia: <?= $ternak['usia'] ?> tahun</p>
            <p>Lokasi Peternak: <?= $ternak['lokasi'] ?? 'Jalan Pasar Kerbau Toraja Utara' ?></p>
            <p>Stok Tersisa: <?= $ternak['stok_sisa'] ?> ekor</p>
        </div>
    </div>

    <form method="POST" action="proses_pemesanan.php">
        <input type="hidden" name="id_ternak" value="<?= $ternak['id'] ?>">

        <div class="mb-3">
            <label for="jumlah" class="form-label">Jumlah Ternak</label>
            <input type="number" name="jumlah" id="jumlah" class="form-control" value="1" min="1" max="<?= $ternak['stok_sisa'] ?>" required>
        </div>

        <div class="mb-3">
            <label for="metode" class="form-label">Metode Pembayaran</label>
            <select name="metode" class="form-select" id="metode" required>
                <option value="">-- Pilih Metode --</option>
                <option value="Tunai">Tunai</option>
                <option value="DANA">DANA</option>
            </select>
        </div>

        <div id="info-dana" class="dana-info-box d-none mt-3">
            <div class="d-flex align-items-center mb-2">
                <img src="/sipera/jpg/dana.jpg" alt="Logo DANA" class="me-2" style="width: 40px;">
                <h6 class="mb-0">Pembayaran via DANA</h6>
            </div>
            <p class="mb-1">Silakan transfer ke nomor DANA berikut:</p>
            <p class="fw-bold text-success">08xx-xxxx-xxxx</p>
            <p class="mb-2">✅ Kode unik akan ditambahkan secara otomatis untuk verifikasi.</p>
            <div class="text-center">
                <p class="mb-1"><strong>Atau scan QR Code berikut:</strong></p>
                <img src="/sipera/jpg/qr.jpg" alt="QR Code DANA" class="qr-img">
            </div>
        </div>

        <div class="mb-3 mt-3">
            <label for="tanggal" class="form-label">Tanggal Kunjungan</label>
            <input type="date" name="tanggal" id="tanggal" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="catatan" class="form-label">Catatan Tambahan</label>
            <textarea name="catatan" id="catatan" class="form-control" rows="3" placeholder="Contoh: Saya ingin melihat kondisi langsung..."></textarea>
        </div>

        <div class="mb-3">
            <label for="total" class="form-label">Total Harga</label>
            <input type="text" id="total" class="form-control" readonly>
        </div>

        <!-- ✅ Tombol aksi -->
        <div class="d-flex justify-content-between align-items-center mt-4">
            <a href="dashboard.php" class="btn btn-outline-success">← Kembali</a>
            <div>
                <a href="dashboard.php" class="btn btn-secondary me-2">Batal</a>
                <button type="submit" class="btn btn-success">Pesan Sekarang</button>
            </div>
        </div>
    </form>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const harga = <?= $ternak['harga'] ?>;
    const jumlah = document.getElementById("jumlah");
    const total = document.getElementById("total");
    const metode = document.getElementById("metode");
    const infoDana = document.getElementById("info-dana");

    function updateTotal() {
        const totalHarga = harga * jumlah.value;
        total.value = "Rp " + totalHarga.toLocaleString("id-ID");
    }

    jumlah.addEventListener("input", updateTotal);
    metode.addEventListener("change", () => {
        infoDana.classList.toggle("d-none", metode.value !== "DANA");
    });

    updateTotal(); // Set awal
</script>
</body>
</html>
