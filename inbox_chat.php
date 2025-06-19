<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'pembeli') {
    header("Location: ../../login.php");
    exit();
}
require_once __DIR__ . '/../../../config/database.php';

$id_pembeli = $_SESSION['user_id'];

// Hapus chat
if (isset($_GET['hapus']) && isset($_GET['id_penjual']) && isset($_GET['id_ternak'])) {
    $id_penjual = (int) $_GET['id_penjual'];
    $id_ternak = (int) $_GET['id_ternak'];
    $conn->query("DELETE FROM chat WHERE 
        (pengirim_id = $id_penjual AND penerima_id = $id_pembeli AND id_ternak = $id_ternak) 
        OR 
        (pengirim_id = $id_pembeli AND penerima_id = $id_penjual AND id_ternak = $id_ternak)");
    header("Location: inbox_chat.php");
    exit();
}

// Ambil daftar chat unik berdasarkan ternak dan pengirim
$query = $conn->query("
    SELECT 
        c.id_ternak,
        IF(c.pengirim_id = $id_pembeli, c.penerima_id, c.pengirim_id) AS id_penjual,
        u.nama AS nama_penjual,
        t.jenis AS jenis_ternak,
        MAX(c.created_at) AS last_chat,
        SUM(CASE 
                WHEN c.is_read = 0 
                     AND c.penerima_id = $id_pembeli 
                     AND c.pengirim_id != $id_pembeli 
                THEN 1 
                ELSE 0 
            END) AS unread_count
    FROM chat c
    JOIN users u ON u.id = IF(c.pengirim_id = $id_pembeli, c.penerima_id, c.pengirim_id)
    JOIN ternak t ON t.id = c.id_ternak
    WHERE c.penerima_id = $id_pembeli OR c.pengirim_id = $id_pembeli
    GROUP BY c.id_ternak, id_penjual
    ORDER BY last_chat DESC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Inbox Chat - Pembeli | SIPERA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f5f9f6;
            font-family: 'Segoe UI', sans-serif;
        }
        .navbar {
            background-color: #2e7d32;
        }
        .navbar .navbar-brand, .navbar .nav-link {
            color: #fff;
        }
        .card {
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.07);
        }
        .chat-item {
            padding: 15px;
            border-bottom: 1px solid #e0e0e0;
        }
        .chat-item:last-child {
            border-bottom: none;
        }
        .chat-actions {
            text-align: right;
        }
        .btn-chat {
            background-color: #4caf50;
            color: #fff;
        }
        .btn-chat:hover {
            background-color: #43a047;
        }
        .btn-delete {
            background-color: #e53935;
            color: white;
        }
        .btn-delete:hover {
            background-color: #d32f2f;
        }
        .badge-unread {
            font-size: 12px;
            margin-left: 5px;
        }
        .back-button {
            margin-top: 20px;
        }
    </style>
</head>
<body>

<!-- ✅ Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark fixed-top">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">SIPERA - Pembeli</a>
        <div class="ms-auto">
            <a href="../../logout.php" class="nav-link">Logout</a>
        </div>
    </div>
</nav>

<!-- ✅ Main Content -->
<div class="container mt-5 pt-5">
    <h4 class="mb-4 text-success">📥 Obrolan Masuk</h4>

    <?php if ($query->num_rows > 0): ?>
        <div class="card">
            <?php while ($row = $query->fetch_assoc()): ?>
                <div class="chat-item d-flex justify-content-between align-items-center flex-wrap">
                    <div>
                        <strong><?= htmlspecialchars($row['nama_penjual']) ?></strong> - 
                        <span class="text-muted"><?= ucfirst($row['jenis_ternak']) ?></span><br>
                        <small class="text-muted">Terakhir: <?= $row['last_chat'] ?></small>
                    </div>
                    <div class="chat-actions mt-2 mt-md-0">
                        <a href="chat.php?id_ternak=<?= $row['id_ternak'] ?>&id_penjual=<?= $row['id_penjual'] ?>" 
                           class="btn btn-sm btn-chat">
                            Buka Chat
                            <?php if ($row['unread_count'] > 0): ?>
                                <span class="badge bg-warning text-dark badge-unread"><?= $row['unread_count'] ?></span>
                            <?php endif; ?>
                        </a>
                        <a href="inbox_chat.php?hapus=1&id_penjual=<?= $row['id_penjual'] ?>&id_ternak=<?= $row['id_ternak'] ?>"
                           class="btn btn-sm btn-delete"
                           onclick="return confirm('Yakin ingin menghapus chat ini?')">Hapus</a>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-info">Belum ada chat dari penjual.</div>
    <?php endif; ?>

    <div class="back-button">
        <a href="dashboard.php" class="btn btn-secondary">← Kembali ke Dashboard</a>
    </div>
</div>

</body>
</html>
