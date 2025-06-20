<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'pembeli') {
    header("Location: ../../login.php");
    exit();
}

require_once __DIR__ . '/../../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_pembeli = $_SESSION['user_id'];
    $id_ternak = $_POST['id_ternak'];
    $jumlah = $_POST['jumlah'];
    $metode = $_POST['metode'];
    $tanggal = $_POST['tanggal'];
    $catatan = $_POST['catatan'] ?? '';

    // Ambil data ternak dari database
    $stmt = $conn->prepare("SELECT jenis, harga, foto, stok_sisa FROM ternak WHERE id = ?");
    $stmt->bind_param("i", $id_ternak);
    $stmt->execute();
    $result = $stmt->get_result();
    $ternak = $result->fetch_assoc();

    if (!$ternak) {
        echo "<div class='text-danger text-center'>Ternak tidak ditemukan.</div>";
        exit();
    }

    if ($jumlah > $ternak['stok_sisa']) {
        echo "<div class='text-danger text-center'>Jumlah pesanan melebihi stok tersedia.</div>";
        exit();
    }

    $jenis = $ternak['jenis'];
    $foto = $ternak['foto'];
    $harga_satuan = $ternak['harga'];
    $total_harga = $harga_satuan * $jumlah;

    // Simpan ke tabel pemesanan
    $insert = $conn->prepare("INSERT INTO pemesanan (id_pembeli, id_ternak, jenis, foto, jumlah, metode, tanggal_kunjungan, catatan, total_harga, status)
                              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'Menunggu Konfirmasi')");
    $insert->bind_param("iississsi", $id_pembeli, $id_ternak, $jenis, $foto, $jumlah, $metode, $tanggal, $catatan, $total_harga);

    if ($insert->execute()) {
        // Kurangi stok_sisa ternak
        $updateStok = $conn->prepare("UPDATE ternak SET stok_sisa = stok_sisa - ? WHERE id = ?");
        $updateStok->bind_param("ii", $jumlah, $id_ternak);
        $updateStok->execute();

        header("Location: riwayat.php?status=berhasil");
        exit();
    } else {
        echo "<div class='text-danger text-center'>Gagal menyimpan pemesanan.</div>";
    }
} else {
    echo "<div class='text-danger text-center'>Akses tidak valid.</div>";
}
