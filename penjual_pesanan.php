<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'penjual') {
    header("Location: ../../login.php");
    exit();
}

$id_penjual = $_SESSION['user_id'];

require_once '../../../config/database.php';

$query = "SELECT p.*, t.jenis, t.foto, u.nama AS nama_pembeli 
          FROM pesanan p 
          JOIN ternak t ON p.id_ternak = t.id 
          JOIN users u ON p.id_pembeli = u.id 
          WHERE t.id_penjual = ? 
          ORDER BY p.created_at DESC";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id_penjual);
$stmt->execute();
$result = $stmt->get_result();
?>

<h3>Pesanan Masuk (Penjual)</h3>
<table class="table table-bordered">
    <thead>
        <tr>
            <th>Foto</th>
            <th>Jenis</th>
            <th>Pembeli</th>
            <th>Jumlah</th>
            <th>Total</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><img src="../../public/<?= $row['foto'] ?>" width="80"></td>
            <td><?= $row['jenis'] ?></td>
            <td><?= $row['nama_pembeli'] ?></td>
            <td><?= $row['jumlah'] ?></td>
            <td>Rp <?= number_format($row['total']) ?></td>
            <td><?= $row['status'] ?></td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>
