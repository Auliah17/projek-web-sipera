<?php
session_start();
require_once __DIR__ . '/../../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_aduan = intval($_POST['id_aduan']);
    $pesan = trim($_POST['pesan']);
    $pengirim = $_SESSION['role']; // biasanya 'admin'
    $tanggal = date("Y-m-d H:i:s");

    if (!empty($id_aduan) && !empty($pesan)) {
        $stmt = mysqli_prepare($conn, "INSERT INTO balasan_aduan (id_aduan, pengirim, pesan, tanggal) VALUES (?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "isss", $id_aduan, $pengirim, $pesan, $tanggal);
        $success = mysqli_stmt_execute($stmt);

        if ($success) {
            // Update status aduan jadi "sudah dibalas"
            mysqli_query($conn, "UPDATE aduan SET status = 'sudah dibalas' WHERE id = $id_aduan");
            header("Location: detail_aduan.php?id=" . $id_aduan);
            exit();
        } else {
            echo "Gagal menyimpan balasan.";
        }
    } else {
        echo "ID atau pesan kosong.";
    }
} else {
    echo "Akses ditolak.";
}
