<?php
session_start();
require_once __DIR__ . '/../../../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php"); exit();
}

$id_user = $_SESSION['user_id'];
$role    = $_SESSION['role'];
$id_aduan = isset($_GET['id']) ? (int) $_GET['id'] : 0;

/* ---------- 1. HANDLER AJAX LOAD PESAN ---------- */
if (isset($_GET['aksi']) && $_GET['aksi'] === 'load' && is_numeric($id_aduan)) {
    // ambil pesan awal + balasan
    $stmt = $conn->prepare(
        "SELECT 'aduan' AS tipe, id_pengirim AS pengirim_id, role_pengirim AS pengirim, pesan, tanggal
         FROM aduan WHERE id = ?
         UNION ALL
         SELECT 'balas', NULL, pengirim, pesan, tanggal
         FROM balasan_aduan WHERE id_aduan = ?
         ORDER BY tanggal ASC"
    );
    $stmt->bind_param("ii", $id_aduan, $id_aduan);
    $stmt->execute();
    $res = $stmt->get_result();

    ob_start();          // buat HTML bubble
    while ($row = $res->fetch_assoc()):
        $cls = htmlspecialchars($row['pengirim']);      // admin | pembeli | penjual
    ?>
        <div class="chat-item <?= $cls ?>">
            <div class="sender"><?= ucfirst($cls) ?>:</div>
            <div class="message"><?= nl2br(htmlspecialchars($row['pesan'])) ?></div>
            <div class="timestamp"><?= date('d-m-Y H:i', strtotime($row['tanggal'])) ?></div>
        </div>
    <?php endwhile;
    echo ob_get_clean();
    exit();               // kembali ke JS
}
/* ---------- END AJAX ---------- */

/* ---------- LOGIKA NORMAL HALAMAN ---------- */
$stmt = $conn->prepare("SELECT * FROM aduan WHERE id = ?");
$stmt->bind_param("i", $id_aduan); $stmt->execute();
$aduan = $stmt->get_result()->fetch_assoc();
if (!$aduan || ($aduan['id_pengirim'] != $id_user && $role != 'admin')) {
    echo "<div class='alert alert-danger'>Aduan tidak ditemukan atau Anda tidak memiliki akses.</div>"; exit();
}

/* kirim balasan (normal POST reload boleh, atau nanti bisa di‑AJAX juga) */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pesan'])) {
    $pesan = trim($_POST['pesan']);
    if ($pesan !== '') {
        $stmt = $conn->prepare(
            "INSERT INTO balasan_aduan (id_aduan, pengirim, pesan, tanggal)
             VALUES (?, ?, ?, NOW())"
        );
        $stmt->bind_param('iss', $id_aduan, $role, $pesan);
        $stmt->execute();

        if ($role === 'admin') {
            $up = $conn->prepare("UPDATE aduan SET status='ditanggapi' WHERE id=?");
            $up->bind_param("i", $id_aduan); $up->execute();
        }
    }
    header("Location: detail_aduan.php?id=$id_aduan"); exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Detail Aduan</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body{background:#f8f9fa;font-family:'Segoe UI',sans-serif;padding:40px}
.container{max-width:800px;margin:auto}
.card{border-radius:12px;box-shadow:0 4px 12px rgba(0,0,0,.05)}
.chat-box{max-height:400px;overflow-y:auto}
.chat-item{padding:10px 15px;border-radius:10px;margin-bottom:10px;max-width:75%}
.chat-item.admin{background:#e7f1ff;align-self:flex-start}
.chat-item.pembeli,.chat-item.penjual{background:#d4edda;align-self:flex-end;margin-left:auto}
.sender{font-weight:600}
.timestamp{font-size:.8rem;color:#666;text-align:right;margin-top:4px}
.chat-container{display:flex;flex-direction:column}
textarea{resize:vertical}
</style>
</head>
<body>
<div class="container">
    <div class="card p-4 mb-3">
        <h4 class="mb-3">Detail Aduan</h4>
        <p><strong>Subjek:</strong> <?= htmlspecialchars($aduan['subjek']) ?></p>
        <p><strong>Tanggal:</strong> <?= date('d-m-Y H:i',strtotime($aduan['tanggal'])) ?></p>
        <p><strong>Status:</strong>
            <span class="badge bg-<?= $aduan['status']=='baru'?'warning':'success' ?>">
                <?= ucfirst($aduan['status']) ?>
            </span>
        </p>
    </div>

    <div class="card p-4 mb-3">
        <div id="chat-box" class="chat-box chat-container"></div>
    </div>

    <form method="POST" class="card p-4 mb-3">
        <div class="mb-3">
            <label for="pesan" class="form-label">Balas Pesan:</label>
            <textarea name="pesan" id="pesan" class="form-control" rows="4" required></textarea>
        </div>
        <button type="submit" class="btn btn-success">Kirim Balasan</button>
    </form>

    <a href="javascript:history.back()" class="btn btn-secondary">← Kembali</a>
</div>

<script>
const chatBox = document.getElementById('chat-box');

function loadChat(){
    fetch('?aksi=load&id=<?= $id_aduan ?>')
      .then(r=>r.text())
      .then(html=>{
          chatBox.innerHTML = html;
          chatBox.scrollTop = chatBox.scrollHeight;
      });
}

loadChat();                // pertama kali
setInterval(loadChat, 3000); // refresh tiap 3 detik
</script>
</body>
</html>
