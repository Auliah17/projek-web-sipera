<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'penjual') {
    header("Location: ../../login.php");
    exit();
}

require_once '../../../config/database.php';
$id_penjual = $_SESSION['user_id'];
$notif = "";

// Proses kirim konsultasi baru
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_dokter'], $_POST['pesan'])) {
    $id_dokter = $_POST['id_dokter'];
    $pesan = trim($_POST['pesan']);

    if (!empty($id_dokter) && !empty($pesan)) {
        // Buat entri di `konsultasi`
        $stmt = $conn->prepare("INSERT INTO konsultasi (id_user, id_dokter, pesan, pengirim, created_at) VALUES (?, ?, ?, 'penjual', NOW())");
        $stmt->bind_param("iis", $id_penjual, $id_dokter, $pesan);
        if ($stmt->execute()) {
            $id_konsultasi = $conn->insert_id;

            // Simpan juga ke `chat_konsultasi`
            $stmt2 = $conn->prepare("INSERT INTO chat_konsultasi (id_konsultasi, pengirim, pesan, waktu_kirim) VALUES (?, 'penjual', ?, NOW())");
            $stmt2->bind_param("is", $id_konsultasi, $pesan);
            $stmt2->execute();
            $notif = '<div class="alert alert-success mt-2">✅ Konsultasi berhasil dikirim.</div>';
        } else {
            $notif = '<div class="alert alert-danger mt-2">❌ Gagal mengirim konsultasi.</div>';
        }
    } else {
        $notif = '<div class="alert alert-warning mt-2">⚠️ Semua kolom harus diisi.</div>';
    }
}

// Ambil semua dokter
$dokters = $conn->query("SELECT id, nama FROM users WHERE role='dokter'")->fetch_all(MYSQLI_ASSOC);

// Ambil riwayat konsultasi terbaru per dokter
$riwayat = [];
$stmt = $conn->prepare("
    SELECT k.id, k.id_dokter, k.created_at, u.nama AS nama_dokter
    FROM konsultasi k
    JOIN users u ON k.id_dokter = u.id
    JOIN (
        SELECT id_dokter, MAX(created_at) AS latest
        FROM konsultasi
        WHERE id_user = ?
        GROUP BY id_dokter
    ) lk ON k.id_dokter = lk.id_dokter AND k.created_at = lk.latest
    WHERE k.id_user = ?
    ORDER BY k.created_at DESC
");
$stmt->bind_param("ii", $id_penjual, $id_penjual);
$stmt->execute();
$res = $stmt->get_result();
while ($row = $res->fetch_assoc()) {
    $riwayat[] = $row;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Forum Konsultasi Penjual</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-light p-4">
<div class="container bg-white p-4 rounded shadow">
    <h3>🩺 Forum Konsultasi dengan Dokter Hewan</h3>

    <?= $notif ?>

    <form method="POST" class="mb-4">
        <label class="form-label mt-2">Pilih Dokter:</label>
        <select name="id_dokter" class="form-select" required>
            <option value="">-- Pilih Dokter --</option>
            <?php foreach ($dokters as $dokter): ?>
                <option value="<?= $dokter['id'] ?>"><?= htmlspecialchars($dokter['nama']) ?></option>
            <?php endforeach; ?>
        </select>

        <label class="form-label mt-3">Pesan Awal:</label>
        <textarea name="pesan" class="form-control" rows="3" placeholder="Tulis keluhan atau pertanyaan..." required></textarea>

        <button type="submit" class="btn btn-success mt-3">Kirim Konsultasi</button>
    </form>

    <hr>
    <h5>📋 Riwayat Konsultasi:</h5>
    <?php if (empty($riwayat)): ?>
        <p class="text-muted">Belum ada konsultasi yang dikirim.</p>
    <?php else: ?>
        <?php foreach ($riwayat as $item): ?>
            <div class="border rounded p-2 mb-2 d-flex justify-content-between">
                <div>
                    <strong>👨‍⚕ Dokter: <?= htmlspecialchars($item['nama_dokter']) ?></strong><br>
                    <small><em><?= date('d M Y H:i', strtotime($item['created_at'])) ?></em></small>
                </div>
                <div>
                    <a href="detail_konsultasi.php?id=<?= $item['id'] ?>" class="btn btn-sm btn-primary">Lihat Chat</a>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
        <div class="mt-4">
        <a href="dashboard.php" class="btn btn-secondary">← Kembali ke Dashboard</a>
    </div>
</div>
</body>
</html>
