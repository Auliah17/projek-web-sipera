<?php
session_start();
require_once __DIR__ . '/../../../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'pembeli') {
    header("Location: ../../login.php");
    exit();
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    exit("ID aduan tidak valid.");
}

$id_aduan = intval($_GET['id']);
$id_pembeli = $_SESSION['user_id'];

// Cek apakah aduan milik pembeli ini
$stmt_check = $conn->prepare(
    "SELECT * FROM aduan WHERE id = ? AND id_pengirim = ? AND role_pengirim = 'pembeli'"
);
$stmt_check->bind_param("ii", $id_aduan, $id_pembeli);
$stmt_check->execute();
$result_check = $stmt_check->get_result();
if ($result_check->num_rows === 0) {
    exit("Aduan tidak ditemukan atau bukan milik Anda.");
}
$aduan = $result_check->fetch_assoc();

// Proses kirim pesan
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['aksi']) && $_POST['aksi'] === 'kirim_pesan') {
    $pesan = trim($_POST['pesan'] ?? '');
    if ($pesan === '') {
        exit("EMPTY");
    }
    $stmt = $conn->prepare(
        "INSERT INTO balasan_aduan (id_aduan, pengirim, pesan, tanggal)
         VALUES (?, 'pembeli', ?, NOW())"
    );
    $stmt->bind_param("is", $id_aduan, $pesan);
    echo $stmt->execute() ? "OK" : "ERROR";
    exit();
}

// Proses load pesan
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['aksi']) && $_GET['aksi'] === 'load_pesan') {
    // Tampilkan pesan utama dari aduan
    echo "<div style='background-color:#fff3cd; padding:10px; margin-bottom:10px; border-radius:10px; max-width:80%; border-left:5px solid #ffc107;'>"
       . "<strong>Pesan Awal Anda</strong><br>"
       . nl2br(htmlspecialchars($aduan['pesan'])) . "<br>"
       . "<small>" . date('d M Y H:i', strtotime($aduan['tanggal'])) . "</small>"
       . "</div>";

    // Tampilkan balasan dari tabel balasan_aduan
    $stmt = $conn->prepare(
        "SELECT * FROM balasan_aduan WHERE id_aduan = ? ORDER BY tanggal ASC"
    );
    $stmt->bind_param("i", $id_aduan);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $sender = $row['pengirim'] === 'pembeli' ? 'Anda' : 'Admin';
        $bgColor = $row['pengirim'] === 'pembeli' ? '#dcedc8' : '#bbdefb';
        echo "<div style='background-color:{$bgColor}; padding:10px; margin-bottom:10px; border-radius:10px; max-width:80%;'>"
           . "<strong>{$sender}</strong><br>"
           . nl2br(htmlspecialchars($row['pesan'])) . "<br>"
           . "<small>" . date('d M Y H:i', strtotime($row['tanggal'])) . "</small>"
           . "</div>";
    }
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Chat Aduan - Pembeli</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f0f4f8;
            padding: 30px;
        }
        .container {
            max-width: 700px;
            margin: auto;
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            display: flex;
            flex-direction: column;
            height: 80vh;
        }
        #chat-box {
            flex-grow: 1;
            overflow-y: auto;
            padding: 15px;
            background: #f1f8e9;
            border-radius: 10px;
            margin-bottom: 15px;
            box-shadow: inset 0 0 10px #c5e1a5;
        }
        form {
            display: flex;
            gap: 10px;
        }
        textarea {
            flex-grow: 1;
            padding: 10px;
            border-radius: 10px;
            border: 2px solid #8bc34a;
            font-size: 1rem;
            resize: none;
        }
        textarea:focus {
            border-color: #558b2f;
            box-shadow: 0 0 8px #558b2faa;
        }
        button {
            background: #689f38;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 12px;
            cursor: pointer;
            font-weight: 600;
        }
        button:hover {
            background: #33691e;
        }
        #error-msg {
            color: red;
            margin-top: 10px;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Chat Aduan Anda</h2>
    <p><strong>Subjek:</strong> <?= htmlspecialchars($aduan['subjek']) ?></p>
    <div id="chat-box"></div>
    <form id="chatForm">
        <textarea name="pesan" id="pesan" rows="2" placeholder="Tulis pesan balasan..." required></textarea>
        <button type="submit">Kirim</button>
    </form>
    <div id="error-msg"></div>
</div>
<script>
    const chatBox = document.getElementById('chat-box');
    const form = document.getElementById('chatForm');
    const errorMsg = document.getElementById('error-msg');

    async function loadPesan() {
        const resp = await fetch(`?aksi=load_pesan&id=<?= $id_aduan ?>`);
        chatBox.innerHTML = await resp.text();
        chatBox.scrollTop = chatBox.scrollHeight;
    }

    form.addEventListener('submit', async e => {
        e.preventDefault();
        errorMsg.textContent = '';
        const pesan = form.pesan.value.trim();
        if (!pesan) {
            errorMsg.textContent = 'Pesan tidak boleh kosong.';
            return;
        }
        const fd = new FormData();
        fd.append('aksi', 'kirim_pesan');
        fd.append('pesan', pesan);
        const resp = await fetch(`?id=<?= $id_aduan ?>`, {
            method: 'POST',
            body: fd
        });
        const text = await resp.text();
        if (text === 'OK') {
            form.reset();
            loadPesan();
        } else if (text === 'EMPTY') {
            errorMsg.textContent = 'Pesan tidak boleh kosong.';
        } else {
            errorMsg.textContent = 'Gagal mengirim pesan.';
        }
    });

    setInterval(loadPesan, 3000);
    loadPesan();
</script>
</body>
</html>
