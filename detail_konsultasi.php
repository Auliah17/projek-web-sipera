<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'penjual') {
    header("Location: ../../login.php");
    exit();
}
require_once '../../../config/database.php';

$id_penjual = $_SESSION['user_id'];
$id_konsultasi = $_GET['id'] ?? 0;

// Ambil data konsultasi
$stmt = $conn->prepare("SELECT k.*, u.nama AS nama_dokter FROM konsultasi k 
    JOIN users u ON k.id_dokter = u.id 
    WHERE k.id = ? AND k.id_user = ?");
$stmt->bind_param("ii", $id_konsultasi, $id_penjual);
$stmt->execute();
$konsultasi = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$konsultasi) {
    echo "Konsultasi tidak ditemukan.";
    exit();
}

// Load chat (AJAX)
if (isset($_GET['load']) && $_GET['load'] == 1) {
    $stmt = $conn->prepare("SELECT * FROM chat_konsultasi WHERE id_konsultasi = ? ORDER BY waktu_kirim ASC");
    $stmt->bind_param("i", $id_konsultasi);
    $stmt->execute();
    $res = $stmt->get_result();

    while ($chat = $res->fetch_assoc()) {
        echo '<div class="chat-message ' . $chat['pengirim'] . '">';
        echo '<div class="bubble ' . $chat['pengirim'] . '">';
        echo nl2br(htmlspecialchars($chat['pesan']));
        echo '<div class="text-muted small mt-1">' . date('d/m/Y H:i', strtotime($chat['waktu_kirim'])) . '</div>';
        echo '</div></div>';
    }
    exit();
}

// Kirim pesan (AJAX)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pesan'])) {
    $pesan = trim($_POST['pesan']);
    if (!empty($pesan)) {
        $stmt = $conn->prepare("INSERT INTO chat_konsultasi (id_konsultasi, pengirim, pesan, waktu_kirim) VALUES (?, 'penjual', ?, NOW())");
        $stmt->bind_param("is", $id_konsultasi, $pesan);
        $stmt->execute();
        echo 'ok';
    }
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Konsultasi Penjual - SIPERA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body {
            background-color: #ffffff;
            font-family: 'Segoe UI', sans-serif;
        }

        .container {
            background-color: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 0 15px rgba(0,0,0,0.08);
            margin-top: 30px;
            max-width: 800px;
        }

        h4 {
            color: #800000;
            font-weight: bold;
            margin-bottom: 20px;
        }

.chat-box {
    max-height: 400px;
    overflow-y: auto;
    padding: 15px;
    border: 1px solid #ddd;
    border-radius: 10px;
    background: #fff;
}
.chat-message { margin-bottom: 12px; }
.chat-message.penjual { text-align: right; }
.chat-message.dokter { text-align: left; }
.bubble {
    display: inline-block;
    padding: 10px 15px;
    border-radius: 10px;
    max-width: 70%;
}

/* WARNA MARON UNTUK PENJUAL */
.bubble.penjual {
    background-color: #800000;
    color: white;
}

/* WARNA ABU TERANG UNTUK DOKTER */
.bubble.dokter {
    background-color: #f0f0f0;
    color: #000;
}

/* AGAR WAKTU TERLIHAT JELAS DI BUBBLE MARON */
.bubble.penjual .text-muted {
    color: rgba(255, 255, 255, 0.8) !important;
}

/* AGAR WAKTU DOKTER MASIH BEDA DAN TERLIHAT JUGA */
.bubble.dokter .text-muted {
    color: #555 !important;
}


        textarea.form-control {
            border-radius: 10px;
            resize: none;
        }

        .btn-success {
            background-color: #800000;
            border: none;
        }

        .btn-success:hover {
            background-color: #a94442;
        }

        .btn-secondary {
            background-color: #bbb;
            border: none;
        }
    </style>
</head>
<body>

<div class="container">
    <h4>Konsultasi dengan Dokter: <?= htmlspecialchars($konsultasi['nama_dokter']) ?></h4>

    <div class="chat-box mb-4" id="chatBox"></div>

    <form id="chatForm">
        <textarea name="pesan" id="pesan" class="form-control mb-3" rows="3" placeholder="Tulis pesan..." required></textarea>
        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-success">Kirim</button>
            <a href="form_konsultasi.php" class="btn btn-secondary">Kembali</a>
        </div>
    </form>
</div>

<script>
function loadChat() {
    $.get("detail_konsultasi.php?id=<?= $id_konsultasi ?>&load=1", function(data) {
        $('#chatBox').html(data);
        $('#chatBox').scrollTop($('#chatBox')[0].scrollHeight);
    });
}

$('#chatForm').submit(function(e) {
    e.preventDefault();
    $.post("detail_konsultasi.php?id=<?= $id_konsultasi ?>", { pesan: $('#pesan').val() }, function() {
        $('#pesan').val('');
        loadChat();
    });
});

setInterval(loadChat, 2500);
loadChat();
</script>

</body>
</html>
