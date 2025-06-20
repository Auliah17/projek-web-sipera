<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit();
}
require_once __DIR__ . '/../../../config/database.php';

$user_id = $_SESSION['user_id'];
$id_ternak = $_GET['id_ternak'] ?? null;
$pesan_error = $pesan_sukses = '';

// Ambil data ternak + penjual
$stmt = $conn->prepare("SELECT t.*, u.id AS id_penjual, u.nama AS nama_penjual
                        FROM ternak t JOIN users u ON t.id_penjual = u.id
                        WHERE t.id = ?");
$stmt->bind_param("i", $id_ternak);
$stmt->execute();
$ternak = $stmt->get_result()->fetch_assoc();

$id_penjual = $ternak['id_penjual'] ?? null;

// Proses kirim pesan via AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mode']) && $_POST['mode'] == 'kirim') {
    $pesan = trim($_POST['pesan']);
    if ($pesan !== '') {
        $stmt = $conn->prepare("INSERT INTO chat (pengirim_id, penerima_id, id_ternak, pesan) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiis", $user_id, $id_penjual, $id_ternak, $pesan);
        $stmt->execute();
    }
    exit();
}

// Proses ambil chat via AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mode']) && $_POST['mode'] == 'ambil') {
    $stmt = $conn->prepare("SELECT c.*, u.nama FROM chat c
                            JOIN users u ON c.pengirim_id = u.id
                            WHERE id_ternak = ? AND
                                ((pengirim_id = ? AND penerima_id = ?) OR
                                 (pengirim_id = ? AND penerima_id = ?))
                            ORDER BY created_at ASC");
    $stmt->bind_param("iiiii", $id_ternak, $user_id, $id_penjual, $id_penjual, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $chats = [];
    while ($row = $result->fetch_assoc()) {
        $chats[] = $row;
    }
    header('Content-Type: application/json');
    echo json_encode($chats);
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Chat dengan Penjual</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <<style>
        body {
    background: #ffffff;
}

.chat-container {
    max-width: 1200px;
    margin: 40px auto;
    background:  #fffff ;
    border-radius: 12px;
    box-shadow: 0 6 20px rgba(0,0,0,.1);
     display: flex;
    flex-direction: column;
     height: 80vh;
        }


.chat-box {
    height: 400px;
    overflow-y: scroll;
    padding: 10px;
    border: none;
    border-radius: 8px;
    background: #ffffff; 
}

.chat-message {
    margin-bottom: 10px;
}

.chat-bubble {
    display: inline-block;
    padding: 10px 15px;
    border-radius: 20px;
    max-width: 100%;
    word-wrap: break-word;
}

.from-me {
    background-color: #7b1e1e;
    color: white;
    margin-left: auto;
    text-align: right;
}

.from-them {
            background-color:rgb(209, 128, 128);
            color: #000;
            margin-right: auto;
            text-align: left;
        }

.btn-kirim {
    background-color: #7b1e1e;
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 20px;
    font-weight: 500;
    transition: background-color 0.3s ease;
}

.btn-kirim:hover {
    background-color: #5a1313;
    color: white;
}

.btn-kembali {
    background-color: #7b1e1e;
    color: white;
    border: none;
    padding: 6px 14px;
    border-radius: 5px;
    font-weight: 500;
    transition: background-color 0.3s ease;
    text-decoration: none;
}

.btn-kembali:hover {
    background-color: #5a1313;
    color: white;
    text-decoration: none;
}
    </style>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
        const id_ternak = <?= json_encode($id_ternak) ?>;
        const id_penjual = <?= json_encode($id_penjual) ?>;

        function loadChat() {
            $.post('', {mode: 'ambil'}, function(res) {
                let html = '';
                res.forEach(msg => {
                    let bubble = msg.pengirim_id == <?= $user_id ?> ? 'from-me' : 'from-them';
                    let align = msg.pengirim_id == <?= $user_id ?> ? 'justify-content-end' : 'justify-content-start';
                    html += `<div class="chat-message d-flex ${align}">
                                <div class="chat-bubble ${bubble}">
                                    <strong>${msg.nama}</strong><br>${msg.pesan}<br>
                                    <small>${msg.created_at}</small>
                                </div>
                             </div>`;
                });
                $('.chat-box').html(html);
                $('.chat-box').scrollTop($('.chat-box')[0].scrollHeight);
            }, 'json');
        }

        $(document).ready(function(){
            loadChat();
            setInterval(loadChat, 3000);

            $('#form-chat').submit(function(e){
                e.preventDefault();
                let pesan = $('[name=pesan]').val().trim();
                if (pesan === '') return;
                $.post('', {mode: 'kirim', pesan}, function(){
                    $('[name=pesan]').val('');
                    loadChat();
                });
            });
        });
    </script>
</head>
<body>
<div class="chat-container">
    <h5>💬 Chat dengan <b><?= htmlspecialchars($ternak['nama_penjual']) ?></b> (Ternak: <?= htmlspecialchars($ternak['jenis']) ?>)</h5>
    <div class="chat-box my-3"></div>
    <form id="form-chat" class="d-flex">
        <input type="text" name="pesan" class="form-control me-2" placeholder="Ketik pesan..." autocomplete="off" required>
        <button type="submit" class="btn btn-success">Kirim</button>
    </form>
    <div class="mt-3">
        <a href="inbox_chat.php" class="btn btn-secondary btn-sm">← Kembali ke inbox</a>
    </div>
</div>
</body>
</html>
