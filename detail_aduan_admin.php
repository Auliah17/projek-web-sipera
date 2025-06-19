<?php
session_start();
require_once __DIR__ . '/../../../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../login.php");
    exit();
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "ID aduan tidak valid.";
    exit();
}

$id_aduan = intval($_GET['id']);

// Ambil detail aduan
$stmt_check = $conn->prepare("SELECT a.*, u.nama FROM aduan a JOIN users u ON a.id_pengirim = u.id WHERE a.id = ?");
$stmt_check->bind_param("i", $id_aduan);
$stmt_check->execute();
$result_check = $stmt_check->get_result();

if ($result_check->num_rows === 0) {
    echo "Aduan tidak ditemukan.";
    exit();
}

$aduan = $result_check->fetch_assoc();

// Kirim pesan balasan admin via AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['aksi']) && $_POST['aksi'] === 'kirim_pesan') {
    $pesan = trim($_POST['pesan'] ?? '');
    if ($pesan === '') {
        echo "EMPTY";
        exit();
    }

    $stmt = $conn->prepare("INSERT INTO balasan_aduan (id_aduan, pengirim, pesan, tanggal) VALUES (?, 'admin', ?, NOW())");
    $stmt->bind_param("is", $id_aduan, $pesan);
    if ($stmt->execute()) {
        $stmt2 = $conn->prepare("UPDATE aduan SET status = 'dibalas' WHERE id = ?");
        $stmt2->bind_param("i", $id_aduan);
        $stmt2->execute();
        echo "OK";
    } else {
        echo "ERROR";
    }
    exit();
}

// Load pesan chat via AJAX (termasuk pesan awal aduan)
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['aksi']) && $_GET['aksi'] === 'load_pesan') {
    ?>
    <!-- Pesan Awal dari Penjual -->
    <div style="background-color: #dcedc8; padding: 10px; margin-bottom: 10px; border-radius: 10px; max-width: 80%;">
        <strong><?= htmlspecialchars($aduan['nama']) ?> (Pengguna)</strong><br>
        <?= nl2br(htmlspecialchars($aduan['pesan'])) ?><br>
        <small><?= date('d M Y H:i', strtotime($aduan['tanggal'])) ?></small>
    </div>
    <?php

    // Balasan dari admin dan penjual
    $stmt = $conn->prepare("SELECT * FROM balasan_aduan WHERE id_aduan = ? ORDER BY tanggal ASC");
    $stmt->bind_param("i", $id_aduan);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $sender = ($row['pengirim'] === 'admin') ? 'Anda (Admin)' : 'Pengguna';
        $bgColor = ($row['pengirim'] === 'admin') ? '#bbdefb' : '#dcedc8';
        ?>
        <div style="background-color: <?= $bgColor ?>; padding: 10px; margin-bottom: 10px; border-radius: 10px; max-width: 80%;">
            <strong><?= $sender ?></strong><br>
            <?= nl2br(htmlspecialchars($row['pesan'])) ?><br>
            <small><?= date('d M Y H:i', strtotime($row['tanggal'])) ?></small>
        </div>
        <?php
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Detail Aduan - SIPERA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #f1f8e9;
            font-family: 'Segoe UI', sans-serif;
        }
        .navbar-sipera {
            background-color: #43a047;
        }
        .navbar-brand {
            font-weight: bold;
            color: white;
        }
        .navbar-brand:hover {
            color: #f1f1f1;
        }
        .chat-container {
            max-width: 900px;
            margin: 40px auto;
            background: white;
            padding: 30px;
            border-radius: 16px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.08);
        }
        #chat-box {
            height: 400px;
            overflow-y: auto;
            padding: 15px;
            background: #e8f5e9;
            border-radius: 10px;
            margin-bottom: 15px;
            box-shadow: inset 0 0 8px #a5d6a7;
        }
        textarea {
            resize: none;
        }
        .btn-kembali {
            background: none;
            border: 1px solid #43a047;
            color: #2e7d32;
        }
        .btn-kembali:hover {
            background: #c8e6c9;
        }
    </style>
</head>
<body>

<!-- ✅ Navbar SIPERA -->
<nav class="navbar navbar-expand-lg navbar-sipera">
    <div class="container-fluid px-4">
        <a class="navbar-brand" href="#">SIPERA - Admin</a>
        <div class="ms-auto">
            <a href="/sipera/logout.php" class="nav-link text-white">
                <i class="bi bi-box-arrow-right"></i> Logout
            </a>
        </div>
    </div>
</nav>

<!-- ✅ Chat Aduan -->
<div class="chat-container">
    <h4 class="mb-3"><i class="bi bi-chat-dots"></i> Chat Aduan dari: <?= htmlspecialchars($aduan['nama']) ?></h4>
    <p><strong>Subjek:</strong> <?= htmlspecialchars($aduan['subjek']) ?></p>

    <div id="chat-box"></div>

    <form id="chatForm" class="d-flex mt-3 gap-2">
        <textarea name="pesan" id="pesan" rows="2" class="form-control" placeholder="Tulis pesan balasan..." required></textarea>
        <button type="submit" class="btn btn-success px-4">Kirim</button>
    </form>
    <div id="error-msg" class="text-danger mt-2"></div>

    <div class="mt-4">
        <a href="daftar_aduan.php" class="btn btn-outline-success btn-kembali">
            ← Kembali ke Daftar Aduan
        </a>
    </div>
</div>

<script>
const chatBox = document.getElementById('chat-box');
const form = document.getElementById('chatForm');
const errorMsg = document.getElementById('error-msg');

function loadPesan(){
    fetch('?aksi=load_pesan&id=<?= $id_aduan ?>')
    .then(res => res.text())
    .then(html => {
        chatBox.innerHTML = html;
        chatBox.scrollTop = chatBox.scrollHeight;
    });
}

form.addEventListener('submit', function(e){
    e.preventDefault();
    errorMsg.textContent = '';

    const pesan = form.pesan.value.trim();
    if(pesan === '') {
        errorMsg.textContent = 'Pesan tidak boleh kosong.';
        return;
    }

    const formData = new FormData();
    formData.append('aksi', 'kirim_pesan');
    formData.append('pesan', pesan);

    fetch('?id=<?= $id_aduan ?>', {
        method: 'POST',
        body: formData
    }).then(res => res.text())
    .then(response => {
        if(response === 'OK'){
            form.reset();
            loadPesan();
        } else if(response === 'EMPTY'){
            errorMsg.textContent = 'Pesan tidak boleh kosong.';
        } else {
            errorMsg.textContent = 'Gagal mengirim pesan.';
        }
    }).catch(() => {
        errorMsg.textContent = 'Terjadi kesalahan jaringan.';
    });
});

setInterval(loadPesan, 3000);
loadPesan();
</script>
</body>
</html>
