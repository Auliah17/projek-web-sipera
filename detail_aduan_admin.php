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
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['aksi'] ?? '') === 'kirim_pesan') {
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

// Load pesan chat via AJAX
if ($_SERVER['REQUEST_METHOD'] === 'GET' && ($_GET['aksi'] ?? '') === 'load_pesan') {
    ?>
    <div class="bubble you">
        <strong><?= htmlspecialchars($aduan['nama']) ?> (Pengguna)</strong><br>
        <?= nl2br(htmlspecialchars($aduan['pesan'])) ?>
        <small><?= date('d M Y H:i', strtotime($aduan['tanggal'])) ?></small>
    </div>
    <?php
    $stmt = $conn->prepare("SELECT * FROM balasan_aduan WHERE id_aduan = ? ORDER BY tanggal ASC");
    $stmt->bind_param("i", $id_aduan);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $sender = ($row['pengirim'] === 'admin') ? 'Anda (Admin)' : 'Pengguna';
        $class = ($row['pengirim'] === 'admin') ? 'me' : 'you';
        ?>
        <div class="bubble <?= $class ?>">
            <strong><?= $sender ?></strong><br>
            <?= nl2br(htmlspecialchars($row['pesan'])) ?>
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
            background-color: #7b1e1e;
        }
        .navbar-brand {
            font-weight: bold;
            color: white;
        }
        .navbar-brand:hover {
            color: #f1f1f1;
        }
        .chat-container {
            max-width: 1500px;
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
            box-shadow: inset 0 0 8px #7b1e1e;
        }
        textarea {
            resize: none;
        }
        .bubble {
            max-width: 40%;
            padding: 12px 16px;
            border-radius: 18px;
            margin-bottom: 10px;
            position: relative;
            font-size: 20px;
            line-height: 1.5;
            clear: both;
            word-wrap: break-word;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .bubble.me {
            background-color:rgb(168, 47, 57); /* merah muda */
            margin-left: auto;
            text-align: right;
            border-bottom-right-radius: 0;
            font-weight: bold
        }

        .bubble.you {
            background-color:rgb(212, 178, 178); /* merah terang */
            margin-right: auto;
            text-align: left;
            border-bottom-left-radius: 0;
            font-weight: bold
        }

        .bubble small {
            display: block;
            margin-top: 6px;
            font-size: 15px;
            color: black;
            font-weight: bold
        }
        .btn-kembali {
            background-color: #ffffff;
            color: #7b1e1e;
            border: none;
            padding: 8px 16px;
            font-weight: 500;
            border-radius: 8px;
            transition: background-color 0.3s ease;
        }
        .btn-kembali:hover {
            background-color: #5e1616;
            color: #fff;
            text-decoration: none;
        }
        .btn-kirim-merah {
            background-color: #b02a37; /* merah tua */
            color: white;
            border: none;
            border-radius: 6px;
            padding: 8px 20px;
            font-weight: bold;
            transition: 0.3s ease;
        }

        .btn-kirim-merah:hover {
            background-color: #8b1e2a;
        }
    </style>
</head>
<body>

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

<div class="chat-container">
    <h4 class="mb-3"><i class="bi bi-chat-dots"></i> Chat Aduan dari: <?= htmlspecialchars($aduan['nama']) ?></h4>
    <p><strong>Subjek:</strong> <?= htmlspecialchars($aduan['subjek']) ?></p>

    <div id="chat-box"></div>

    <form id="chatForm" class="d-flex mt-3 gap-2">
        <textarea name="pesan" id="pesan" rows="2" class="form-control" placeholder="Tulis pesan balasan..." required></textarea>
        <button type="submit" class="btn-kirim-merah px-4">Kirim</button>
    </form>
    <div id="error-msg" class="text-danger mt-2"></div>

    <div class="mt-4">
        <a href="daftar_aduan.php" class="btn btn-kembali">← Kembali ke Daftar Aduan</a>
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
