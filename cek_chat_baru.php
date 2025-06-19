<?php
session_start();
require_once '../../../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'penjual') {
    echo json_encode(['status' => 'unauthorized']);
    exit();
}

$id_penjual = $_SESSION['user_id'];

$query = $conn->query("
    SELECT 
        c.id_ternak,
        c.pengirim_id AS id_pembeli,
        u.nama AS nama_pembeli,
        t.jenis AS jenis_ternak,
        MAX(c.created_at) AS last_chat,
        SUM(CASE WHEN c.is_read = 0 AND c.penerima_id = $id_penjual THEN 1 ELSE 0 END) AS unread_count
    FROM chat c
    JOIN users u ON c.pengirim_id = u.id
    JOIN ternak t ON c.id_ternak = t.id
    WHERE c.penerima_id = $id_penjual
    GROUP BY c.id_ternak, c.pengirim_id
    ORDER BY last_chat DESC
");

$chats = [];

while ($row = $query->fetch_assoc()) {
    $chats[] = $row;
}

echo json_encode($chats);
