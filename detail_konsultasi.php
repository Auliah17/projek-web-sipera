<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'dokter') {
    header("Location: ../../login.php");
    exit();
}

require_once '../../../config/database.php';

$id_dokter = $_SESSION['user_id'];
$id_konsultasi = $_GET['id'] ?? 0;

// Ambil data konsultasi
$stmt = $conn->prepare("SELECT k.*, u.nama AS nama_penjual FROM konsultasi k JOIN users u ON k.id_user = u.id WHERE k.id = ? AND k.id_dokter = ?");
$stmt->bind_param("ii", $id_konsultasi, $id_dokter);
$stmt->execute();
$konsultasi = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$konsultasi) {
    echo "Konsultasi tidak ditemukan.";
    exit();
}

// Proses kirim pesan via AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pesan'])) {
    $pesan = trim($_POST['pesan']);
    if (!empty($pesan)) {
        $stmt = $conn->prepare("INSERT INTO chat_konsultasi (id_konsultasi, pengirim, pesan, waktu_kirim) VALUES (?, 'dokter', ?, NOW())");
        $stmt->bind_param("is", $id_konsultasi, $pesan);
        $stmt->execute();
        echo 'ok';
    }
    exit(); // penting untuk menghentikan HTML setelah AJAX
}

// Ambil isi chat
$stmt = $conn->prepare("SELECT * FROM chat_konsultasi WHERE id_konsultasi = ? ORDER BY waktu_kirim ASC");
$stmt->bind_param("i", $id_konsultasi);
$stmt->execute();
$res = $stmt->get_result();
$chats = $res->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Detail Konsultasi - Dokter</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .chat-box {
            max-height: 400px;
            overflow-y: auto;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 10px;
            background: #fff;
        }
        .chat-message { margin-bottom: 12px; }
        .chat-message.penjual { text-align: left; }
        .chat-message.dokter { text-align: right; }
        .bubble {
            display: inline-block;
            padding: 10px 15px;
            border-radius: 10px;
            max-width: 70%;
        }
        .bubble.penjual { background-color: #f1f0f0; }
        .bubble.dokter { background-color: #d4edda; }
    </style>
</head>
<body class="p-4 bg-light">
<div class="container bg-white p-4 rounded shadow">
    <h4>Konsultasi dengan Penjual: <?= htmlspecialchars($konsultasi['nama_penjual']) ?></h4>

    <div class="chat-box my-4" id="chatBox">
        <?php foreach ($chats as $chat): ?>
            <div class="chat-message <?= $chat['pengirim'] ?>">
                <div class="bubble <?= $chat['pengirim'] ?>">
                    <?= nl2br(htmlspecialchars($chat['pesan'])) ?>
                    <div class="text-muted small mt-1"><?= date('d/m/Y H:i', strtotime($chat['waktu_kirim'])) ?></div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <form id="formChat">
        <div class="input-group">
            <input type="text" name="pesan" id="pesanInput" class="form-control" placeholder="Tulis balasan..." required>
            <button type="submit" class="btn btn-success">Kirim</button>
        </div>
    </form>

    <a href="konsultasi_masuk.php" class="btn btn-secondary mt-3">← Kembali</a>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
// Fungsi load chat realtime
function loadChat() {
    $.get('detail_konsultasi.php?id=<?= $id_konsultasi ?>&load=1', function(data) {
        $('#chatBox').html($(data).find('#chatBox').html());
        $('#chatBox').scrollTop($('#chatBox')[0].scrollHeight);
    });
}

$('#formChat').on('submit', function(e) {
    e.preventDefault();
    $.post('detail_konsultasi.php?id=<?= $id_konsultasi ?>', {
        pesan: $('#pesanInput').val()
    }, function(response) {
        if (response === 'ok') {
            $('#pesanInput').val('');
            loadChat();
        } else {
            alert('Gagal mengirim pesan.');
        }
    });
});

setInterval(loadChat, 3000);
</script>
</body>
</html>
