<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'penjual') {
    header("Location: ../../login.php");
    exit();
}

require_once __DIR__ . '/../../../config/database.php';

// Proses update status jika form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_pemesanan'], $_POST['status'])) {
    $id = $_POST['id_pemesanan'];
    $status = $_POST['status'];

    if (!empty($id) && !empty($status)) {
        $stmt = $conn->prepare("UPDATE pemesanan SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $id);
        $stmt->execute();
        $stmt->close();
    }
}

// Ambil data pesanan untuk penjual ini
$query = $conn->query("
    SELECT p.*, t.foto AS foto_ternak, t.jenis, u.nama AS nama_pembeli 
    FROM pemesanan p
    JOIN ternak t ON p.id_ternak = t.id
    JOIN users u ON p.id_pembeli = u.id
    ORDER BY p.id DESC
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Pesanan Masuk - Penjual</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light p-4">
<div class="container">
    <h3 class="mb-4 text-center">📥 Pesanan Masuk</h3>

    <?php if ($query->num_rows === 0): ?>
        <div class="alert alert-info text-center">Belum ada pesanan masuk.</div>
    <?php else: ?>
        <table class="table table-bordered table-striped">
            <thead class="table-success">
                <tr>
                    <th>Foto</th>
                    <th>Jenis</th>
                    <th>Jumlah</th>
                    <th>Total Harga</th>
                    <th>Pembeli</th>
                    <th>Bukti Pembayaran</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $query->fetch_assoc()): ?>
                    <tr>
                        <td><img src="../../public/jpg/<?= htmlspecialchars($row['foto_ternak']) ?>" width="60" height="60" style="object-fit:cover;"></td>
                        <td><?= htmlspecialchars($row['jenis']) ?></td>
                        <td><?= $row['jumlah'] ?></td>
                        <td>Rp <?= number_format($row['total_harga']) ?></td>
                        <td><?= htmlspecialchars($row['nama_pembeli']) ?></td>
                        <td>
                            <?php if (!empty($row['bukti_pembayaran'])): ?>
                                <a href="../../public/jpg/<?= htmlspecialchars($row['bukti_pembayaran']) ?>" target="_blank">Lihat</a>
                            <?php else: ?>
                                <small class="text-muted">Belum ada</small>
                            <?php endif; ?>
                        </td>
                        <td>
                            <form method="POST" class="d-flex gap-1">
                                <input type="hidden" name="id_pemesanan" value="<?= $row['id'] ?>">
                                <select name="status" class="form-select form-select-sm" required>
                                    <option value="">Pilih</option>
                                    <option value="Dikonfirmasi">Dikonfirmasi</option>
                                    <option value="Selesai">Selesai</option>
                                    <option value="Ditolak">Ditolak</option>
                                </select>
                                <button type="submit" class="btn btn-sm btn-primary">Ubah</button>
                            </form>
                            <div><small class="text-muted">Status: <?= htmlspecialchars($row['status']) ?></small></div>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
</body>
</html>
