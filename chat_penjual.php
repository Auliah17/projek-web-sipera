<?php
session_start();
require_once '../../../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'penjual') {
    header("Location: ../../login.php");
    exit();
}

$id_penjual = $_SESSION['user_id'];
$id_pembeli = $_GET['id_pembeli'] ?? null;
$id_ternak = $_GET['id_ternak'] ?? null;

$stmt = $conn->prepare("SELECT nama FROM users WHERE id = ?");
$stmt->bind_param("i", $id_pembeli);
$stmt->execute();
$pembeli = $stmt->get_result()->fetch_assoc();
$nama_pembeli = $pembeli['nama'] ?? 'Pembeli';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Chat dengan <?= htmlspecialchars($nama_pembeli) ?> - SIPERA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f1f8e9;
            font-family: 'Segoe UI', sans-serif;
        }
        .chat-container {
            max-width: 720px;
            margin: 40px auto;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 6px 20px rgba(0,0,0,0.1);
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }
        .chat-header {
            background-color: #388e3c;
            color: #fff;
            padding: 16px 20px;
            font-size: 18px;
            font-weight: bold;
        }
        .chat-box {
            flex-grow: 1;
            height: 500px;
            overflow-y: auto;
            padding: 20px;
            background: #e8f5e9;
        }
        .chat-message {
            display: flex;
            margin-bottom: 12px;
        }
        .chat-message .bubble {
            padding: 12px 16px;
            border-radius: 20px;
            max-width: 75%;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            font-size: 15px;
            line-height: 1.4;
        }
        .chat-message.me {
            justify-content: flex-end;
        }
        .chat-message.them {
            justify-content: flex-start;
        }
        .chat-message.me .bubble {
            background-color: #a5d6a7;
            color: #000;
            border-bottom-right-radius: 0;
        }
        .chat-message.them .bubble {
            background-color: #eeeeee;
            color: #000;
            border-bottom-left-radius: 0;
        }
        .chat-footer {
            border-top: 1px solid #ddd;
            background: #fff;
            padding: 15px 20px;
        }
        .chat-footer form {
            display: flex;
            gap: 10px;
        }
        .chat-footer input[type="text"] {
            flex-grow: 1;
            border-radius: 30px;
            border: 1px solid #ccc;
            padding: 10px 20px;
            font-size: 15px;
        }
        .chat-footer button {
            background-color: #43a047;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 30px;
            font-weight: bold;
        }
        .chat-footer button:hover {
            background-color: #2e7d32;
        }
        .back-section {
            padding: 15px 20px;
            background-color: #fff;
            border-top: 1px solid #ddd;
        }
        .btn-back {
            background-color: #8bc34a;
            color: white;
            border: none;
            padding: 8px 20px;
            border-radius: 20px;
            text-decoration: none;
        }
        .btn-back:hover {
            background-color: #689f38;
            color: white;
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</head>
<body>
<div class="chat-container">
    <div class="chat-header">💬 Chat dengan <?= htmlspecialchars($nama_pembeli) ?></div>

    <div id="chat-box" class="chat-box"></div>

    <div class="chat-footer">
        <form id="form-kirim">
            <input type="hidden" name="pengirim_id" value="<?= $id_penjual ?>">
            <input type="hidden" name="penerima_id" value="<?= $id_pembeli ?>">
            <input type="hidden" name="id_ternak" value="<?= $id_ternak ?>">
            <input type="text" name="pesan" id="pesan" placeholder="Ketik pesan..." required autocomplete="off">
            <button type="submit">Kirim</button>
        </form>
    </div>

    <div class="back-section">
        <a href="inbox_chat.php" class="btn-back">← Kembali</a>
    </div>
</div>

<script>
    const form = document.getElementById('form-kirim');
    const chatBox = document.getElementById('chat-box');
    const pesanInput = document.getElementById('pesan');

    function loadChat() {
        $.get('proses_chat_penjual.php', {
            id_pembeli: <?= json_encode($id_pembeli) ?>,
            id_ternak: <?= json_encode($id_ternak) ?>
        }, function(res) {
            chatBox.innerHTML = res;
            chatBox.scrollTop = chatBox.scrollHeight;
        });
    }

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(form);
        $.ajax({
            url: 'proses_chat_penjual.php',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function () {
                pesanInput.value = '';
                loadChat();
            }
        });
    });

    loadChat();
    setInterval(loadChat, 2000);
</script>
</body>
</html>
