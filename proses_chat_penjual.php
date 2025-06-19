<?php
session_start();
require_once '../../../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'penjual') {
    header("Location: ../../login.php");
    exit();
}

$id_penjual = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pengirim_id = $_POST['pengirim_id'];
    $penerima_id = $_POST['penerima_id'];
    $id_ternak = $_POST['id_ternak'];
    $pesan = mysqli_real_escape_string($conn, $_POST['pesan']);

    $stmt = $conn->prepare("INSERT INTO chat (pengirim_id, penerima_id, id_ternak, pesan, is_read) VALUES (?, ?, ?, ?, 0)");
    $stmt->bind_param("iiis", $pengirim_id, $penerima_id, $id_ternak, $pesan);
    $stmt->execute();
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $id_pembeli = $_GET['id_pembeli'];
    $id_ternak = $_GET['id_ternak'];

    $stmt = $conn->prepare("
        SELECT * FROM chat 
        WHERE id_ternak = ? AND 
        (
            (pengirim_id = ? AND penerima_id = ?) OR 
            (pengirim_id = ? AND penerima_id = ?)
        )
        ORDER BY created_at ASC
    ");
    $stmt->bind_param("iiiii", $id_ternak, $id_penjual, $id_pembeli, $id_pembeli, $id_penjual);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $class = $row['pengirim_id'] == $id_penjual ? 'sent' : 'received';
        echo '<div class="message ' . $class . '">' . htmlspecialchars($row['pesan']) . '</div>';
    }
}
