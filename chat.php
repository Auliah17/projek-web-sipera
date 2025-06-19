<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'dokter') {
    header("Location: ../../login.php");
    exit();
}

require_once '../../../config/database.php';

$id_dokter = $_SESSION['user_id'];
$id_konsultasi = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id_konsultasi <= 0) {
    echo "ID konsultasi tidak valid.";
    exit();
}

$sql_konsultasi = "
    SELECT k.*, u.nama AS nama_penjual, d.nama AS nama_dokter
    FROM konsultasi k
    JOIN users u ON k.id_user = u.id
    JOIN users d ON k.id_dokter = d.id
    WHERE k.id = ? AND k.id_dokter = ?
";
$stmt = $conn->prepare($sql_konsultasi);
$stmt->bind_param("ii", $id_konsultasi, $id_dokter);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    echo "Konsultasi tidak ditemukan atau akses ditolak.";
    exit();
}

$konsultasi = $res->fetch_assoc();

// Proses kirim pesan
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $pesan_balas = trim($_POST['pesan']);
    $foto_balas = "";

    if (!empty($_FILES['foto']['name'])) {
        $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
        $namaFile = time() . '_' . uniqid() . '.' . $ext;
        $foto_balas = "uploads/" . $namaFile;

        $target_path = "../../jpg/" . $foto_balas;
        move_uploaded_file($_FILES['foto']['tmp_name'], $target_path);
    }

    if (!empty($pesan_balas)) {
        $sql_insert = "INSERT INTO konsultasi_balas (id_konsultasi, id_pengirim, pesan, foto) VALUES (?, ?, ?, ?)";
        $stmt_ins = $conn->prepare($sql_insert);
        $stmt_ins->bind_param("iiss", $id_konsultasi, $id_dokter, $pesan_balas, $foto_balas);
        $stmt_ins->execute();
    }
}

// Ambil semua balasan
$sql_balas = "SELECT * FROM konsultasi_balas WHERE id_konsultasi = ? ORDER BY created_at ASC";
$stmt_balas = $conn->prepare($sql_balas);
$stmt_balas->bind_param("i", $id_konsultasi);
$stmt_balas->execute();
$result_balas = $stmt_balas->get_result();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <title>Chat Konsultasi #<?= $id_konsultasi ?> - Dokter</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" />
    <style>
        body {
            background-color: #f9f9f9;
        }
        .chat-container {
            max-width: 700px;
            margin: 40px auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 8px #ccc;
            padding: 20px;
        }
        .chat-header {
            border-bottom: 1px solid #ddd;
            margin-bottom: 15px;
            padding-bottom: 10px;
        }
        .chat-messages {
            height: 400px;
            overflow-y: auto;
            margin-bottom: 15px;
        }
        .message {
            margin-bottom: 15px;
            padding: 10px;
            border-radius: 15px;
            max-width: 70%;
            clear: both;
        }
        .from-penjual {
            background-color: #e0f7fa;
            float: left;
        }
        .from-dokter {
            background-color: #c8e6c9;
            float: right;
            text-align: right;
        }
        .message img {
            max-width: 100%;
            margin-top: 5px;
            border-radius: 10px;
        }
        .chat-input {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        .chat-input textarea {
            flex-grow: 1;
            resize: none;
            height: 70px;
            border-radius: 10px;
            padding: 10px;
            border: 1px solid #ddd;
        }
        .chat-input button {
            background-color: #1ca127;
            color: white;
            border: none;
            padding: 10px 25px;
            border-radius: 10px;
            cursor: pointer;
        }
    </style>
</head>
<body>

<div class="chat-container">
    <div class="chat-header">
        <h4>Konsultasi dengan Penjual: <?= htmlspecialchars($konsultasi['nama_penjual']) ?></h4>
        <small>ID Konsultasi: <?= $id_konsultasi ?> | <?= $konsultasi['created_at'] ?></small>
        <br>
        <a href="konsultasi.php" class="btn btn-sm btn-secondary mt-2">⬅️ Kembali ke Daftar Konsultasi</a>
        <hr>
        <div><strong>Pesan awal:</strong> <?= nl2br(htmlspecialchars($konsultasi['pesan'])) ?></div>
        <?php if (!empty($konsultasi['foto'])): ?>
            <div class="mt-2">
                <strong>Foto Ternak:</strong><br>
                <img src="../../jpg/<?= htmlspecialchars($konsultasi['foto']) ?>" alt="Foto Ternak" style="max-width: 200px; border-radius: 10px;">
            </div>
        <?php endif; ?>
    </div>

    <div class="chat-messages" id="chat-messages">
        <?php if ($result_balas->num_rows > 0): ?>
            <?php while ($msg = $result_balas->fetch_assoc()): ?>
                <?php $is_dokter = ($msg['id_pengirim'] == $id_dokter); ?>
                <div class="message <?= $is_dokter ? 'from-dokter' : 'from-penjual' ?>">
                    <div><?= nl2br(htmlspecialchars($msg['pesan'])) ?></div>
                    <?php if (!empty($msg['foto'])): ?>
                        <img src="../../jpg/<?= htmlspecialchars($msg['foto']) ?>" alt="Foto Pesan">
                    <?php endif; ?>
                    <small style="font-size: 0.75em; color: #666;"><?= $msg['created_at'] ?></small>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="text-muted">Belum ada pesan balasan.</p>
        <?php endif; ?>
    </div>

    <form id="chatForm" class="chat-input mt-3">
        <textarea name="pesan" id="pesan" placeholder="Tulis balasan..." required></textarea>
        <input type="hidden" name="id" value="<?= $id_konsultasi ?>">
        <button type="submit">Kirim</button>
    </form>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
function loadChat() {
    $.get('proses_chat_dokter.php', { id: '<?= $id_konsultasi ?>' }, function(data) {
        $('#chat-messages').html(data);
        $('#chat-messages').scrollTop($('#chat-messages')[0].scrollHeight);
    });
}

$(document).ready(function() {
    // Kirim pesan tanpa reload
    $('#chatForm').submit(function(e) {
        e.preventDefault();
        $.post('proses_chat_dokter.php', $(this).serialize(), function() {
            loadChat();
            $('#pesan').val('').focus();
        });
    });
    // Polling chat tiap 2 detik
    setInterval(loadChat, 2000);
    // Scroll ke bawah saat load awal
    loadChat();
});
</script>

</body>
</html>
