<?php
require_once __DIR__ . '/../../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_pemesanan = $_POST['id_pemesanan'] ?? null;

    if ($id_pemesanan) {
        $stmt = $conn->prepare("UPDATE pemesanan SET status = 'Dikonfirmasi' WHERE id = ?");
        $stmt->bind_param("i", $id_pemesanan);

        if ($stmt->execute()) {
            // Redirect ke halaman notifikasi dengan pesan sukses
            header("Location: notifikasi.php?pesan=success");
            exit;
        } else {
            echo "Gagal mengupdate status pemesanan.";
        }
    } else {
        echo "ID pemesanan tidak ditemukan.";
    }
} else {
    echo "Akses tidak valid.";
}
