<?php
session_start();
require_once __DIR__ . '/../../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_pemesanan']) && isset($_FILES['bukti'])) {
    $id_pemesanan = intval($_POST['id_pemesanan']);
    $folder = __DIR__ . '/../../../public/jpg/';
    $file = $_FILES['bukti'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $new_name = uniqid('bukti_', true) . '.' . $ext;
    $upload_path = $folder . $new_name;

    if (move_uploaded_file($file['tmp_name'], $upload_path)) {
        // Simpan hanya nama file
        $stmt = $conn->prepare("UPDATE pemesanan SET bukti_pembayaran = ? WHERE id = ?");
        $stmt->bind_param("si", $new_name, $id_pemesanan);
        $stmt->execute();
        header("Location: riwayat.php");
        exit();
    } else {
        echo "Gagal mengupload bukti pembayaran.";
    }
} else {
    echo "Permintaan tidak valid.";
}
