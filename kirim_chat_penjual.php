<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'penjual') {
    echo json_encode(['success' => false, 'message' => 'Akses ditolak']);
    exit();
}

require_once '../../../config/database.php';

$id_konsultasi = $_POST['id_konsultasi'] ?? 0;
$pesan = trim($_POST['pesan'] ?? '');
$pengirim = $_POST['pengirim'] ?? 'penjual';

if (!$id_konsultasi || !$pesan) {
    echo json_encode(['success' => false, 'message' => 'Isi pesan tidak boleh kosong']);
    exit();
}

$stmt = $conn->prepare("INSERT INTO chat_konsultasi (id_konsultasi, pengirim, pesan, waktu_kirim) VALUES (?, ?, ?, NOW())");
$stmt->bind_param("iss", $id_konsultasi, $pengirim, $pesan);
$stmt->execute();
$stmt->close();

echo json_encode(['success' => true]);
