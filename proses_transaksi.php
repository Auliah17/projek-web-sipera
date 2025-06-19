<?php
session_start();
require_once __DIR__ . '/../../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_pemesanan'])) {
    $id_pemesanan = intval($_POST['id_pemesanan']);

    // Ambil data pemesanan
    $q = $conn->prepare("SELECT * FROM pemesanan WHERE id = ?");
    $q->bind_param("i", $id_pemesanan);
    $q->execute();
    $res = $q->get_result();
    if ($res->num_rows === 0) {
        echo "Pemesanan tidak ditemukan.";
        exit();
    }
    $p = $res->fetch_assoc();

    // Simpan ke tabel transaksi
    $stmt = $conn->prepare("INSERT INTO transaksi (id_pembeli, id_ternak, jumlah, metode, tanggal, status) 
                            VALUES (?, ?, ?, ?, NOW(), 'Belum Bayar')");
    $stmt->bind_param("iiis", $p['id_pembeli'], $p['id_ternak'], $p['jumlah'], $p['metode']);
    $stmt->execute();

    // Update status pemesanan
    $update = $conn->prepare("UPDATE pemesanan SET status='Menunggu Pembayaran' WHERE id=?");
    $update->bind_param("i", $id_pemesanan);
    $update->execute();

    header("Location: transaksi.php");
    exit();
}
?>
