<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'pembeli') {
    header("Location: ../../login.php");
    exit();
}
require_once __DIR__ . '/../../../config/database.php';

$id_pembeli = $_SESSION['user_id'];
$id_ternak = $_GET['id_ternak'] ?? null;

if (!$id_ternak) {
    echo "<script>alert('Ternak tidak ditemukan'); window.location='dashboard.php';</script>";
    exit();
}

$stmt = $conn->prepare("SELECT t.id_penjual FROM ternak t WHERE t.id = ?");
$stmt->bind_param("i", $id_ternak);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

if (!$data) {
    echo "<script>alert('Ternak tidak ditemukan'); window.location='dashboard.php';</script>";
    exit();
}

$id_penjual = $data['id_penjual'];

if ($id_penjual == $id_pembeli) {
    echo "<script>alert('Anda tidak bisa mengirim pesan ke diri sendiri'); window.location='dashboard.php';</script>";
    exit();
}

$pesan = "Halo, saya tertarik dengan ternak ini. Apakah masih tersedia?";
$stmt = $conn->prepare("INSERT INTO chat (pengirim_id, penerima_id, id_ternak, pesan, created_at, is_read) VALUES (?, ?, ?, ?, NOW(), 0)");
$stmt->bind_param("iiis", $id_pembeli, $id_penjual, $id_ternak, $pesan);
$stmt->execute();

header("Location: inbox_chat.php");
exit();
?>
