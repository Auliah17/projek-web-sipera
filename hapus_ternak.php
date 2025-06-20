<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'penjual') {
    header("Location: ../../login.php");
    exit();
}

require_once __DIR__ . '/../../../config/database.php';

$id_penjual = $_SESSION['user_id'];
$id = $_GET['id'] ?? null;

if (!$id) {
    header("Location: dashboard.php");
    exit();
}

// Cek data ternak
$query = $conn->prepare("SELECT foto FROM ternak WHERE id = ? AND id_penjual = ?");
$query->bind_param("ii", $id, $id_penjual);
$query->execute();
$result = $query->get_result();

if ($result->num_rows === 0) {
    // Ternak tidak ditemukan atau bukan milik penjual
    header("Location: dashboard.php");
    exit();
}

$ternak = $result->fetch_assoc();

// Hapus data ternak
$del = $conn->prepare("DELETE FROM ternak WHERE id = ? AND id_penjual = ?");
$del->bind_param("ii", $id, $id_penjual);

if ($del->execute()) {
    // Hapus file foto jika ada
    if (!empty($ternak['foto']) && file_exists(__DIR__ . '/../../../' . $ternak['foto'])) {
        unlink(__DIR__ . '/../../../' . $ternak['foto']);
    }
    header("Location: dashboard.php?hapus=success");
} else {
    header("Location: dashboard.php?hapus=failed");
}
exit();
