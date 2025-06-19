<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'penjual') {
    header("Location: ../../login.php");
    exit();
}

require_once __DIR__ . '/../../../config/database.php';

$id_penjual = $_SESSION['user_id'];
$id_dokter = isset($_GET['id_dokter']) ? (int)$_GET['id_dokter'] : 0;

if ($id_dokter <= 0) {
    echo "ID dokter tidak valid.";
    exit;
}

// Ambil data dokter
$stmt = $conn->prepare("SELECT nama FROM dokter WHERE id = ?");
$stmt->bind_param("i", $id_dokter);
$stmt->execute();
$resultDokter = $stmt->get_result();
if ($resultDokter->num_rows === 0) {
    echo "Dokter tidak ditemukan.";
    exit;
}
$nama_dokter = $resultDokter->fetch_assoc()['nama'];

// Proses jika ada pengiriman pesan balasan dari penjual
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pesan_baru = trim($_POST['pesan']);
    if (!empty($pesan_baru)) {
        $stmtInsert = $conn->prepare("INSERT INTO konsultasi (id_penjual, id_dokter, pesan, pengirim, waktu) VALUES (?, ?, ?, 'penjual', NOW())");
        $stmtInsert->bind_param("iis", $id_penjual, $id_dokter, $pesan_baru);
        $stmtInsert->execute();

        // Redirect agar form tidak submit ulang
        header("Location: balasan_konsultasi.php?id_dokter=" . $id_dokter);
        exit;
    } else {
        $error = "Pesan tidak boleh kosong.";
    }
}

// Ambil semua pesan konsultasi dengan dokter ini (urut waktu naik)
$stmt = $conn->prepare("SELECT * FROM konsultasi WHERE id_penjual = ? AND id_dokter = ? ORDER BY waktu ASC");
$stmt->bind_param("ii", $id_penjual, $id_dokter);
$stmt->execute();
$resultChat = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <title>Balasan Konsultasi dengan Dr. <?= htmlspecialchars($nama_dokter) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        body {
            background: #f9f9f9;
            padding: 20px;
        }
        .chat-container {
            max-width: 700px;
            margin: 0 auto;
            background: #fff;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 8px rgb(0 0 0 / 0.1);
            height: 80vh;
            display: flex;
            flex-direction: column;
        }
        .chat-messages {
            flex-grow: 1;
            overflow-y: auto;
            margin-bottom: 15px;
            padding-right: 10px;
        }
        .message {
            max-width: 75%;
            padding: 10px 15px;
            border-radius: 20px;
            margin-bottom: 10px;
            clear: both;
            font-size: 14px;
        }
        .message.dokter {
            background-color: #d1e7dd;
            color: #0f5132;
            float: left;
            border-bottom-left-radius: 0;
        }
        .message.penjual {
            background-color: #cfe2ff;
            color: #084298;
            float: right;
            border-bottom-right-radius: 0;
        }
        .timestamp {
            font-size: 0.75rem;
            color: #666;
            margin-top: 3px;
            clear: both;
        }
        .form-message {
            display: flex;
            gap: 10px;
        }
        .form-message textarea {
            flex-grow: 1;
            resize: none;
            padding: 10px;
            border-radius: 10px;
            border: 1px solid #ccc;
            font-size: 14px;
            height: 60px;
        }
        .form-message button {
            background-color: #0d6efd;
            color: white;
            border: none;
            padding: 0 20px;
            border-radius: 10px;
            font-size: 14px;
        }
        a.btn-back {
            margin-bottom: 20px;
            display: inline-block;
        }
        .error-message {
            color: red;
            margin-bottom: 10px;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="container chat-container">
        <a href="form_konsultasi.php" class="btn btn-secondary btn-back">&larr; Kembali ke Daftar Konsultasi</a>
        <h4>Balasan Konsultasi dengan Dr. <?= htmlspecialchars($nama_dokter) ?></h4>
        
        <div class="chat-messages" id="chatMessages">
            <?php if ($resultChat->num_rows === 0): ?>
                <p class="text-muted">Belum ada pesan di konsultasi ini.</p>
            <?php else: ?>
                <?php while ($chat = $resultChat->fetch_assoc()): ?>
                    <div class="message <?= $chat['pengirim'] ?>">
                        <?= nl2br(htmlspecialchars($chat['pesan'])) ?>
                        <div class="timestamp"><?= date('d M Y H:i', strtotime($chat['waktu'])) ?></div>
                    </div>
                <?php endwhile; ?>
            <?php endif; ?>
        </div>

        <?php if (isset($error)): ?>
            <div class="error-message"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" class="form-message" onsubmit="return validateForm()">
            <textarea name="pesan" id="pesan" placeholder="Tulis balasan Anda..." required></textarea>
            <button type="submit">Kirim</button>
        </form>
    </div>

    <script>
        // Scroll chat ke bawah otomatis saat halaman dimuat
        const chatMessages = document.getElementById('chatMessages');
        chatMessages.scrollTop = chatMessages.scrollHeight;

        // Validasi form agar pesan tidak kosong
        function validateForm() {
            const pesan = document.getElementById('pesan').value.trim();
            if (pesan === '') {
                alert('Pesan tidak boleh kosong!');
                return false;
            }
            return true;
        }
    </script>
</body>
</html>
