<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'penjual') {
    header("Location: ../../login.php");
    exit();
}
require_once __DIR__ . '/../../../config/database.php';
$id_penjual = $_SESSION['user_id'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Chat Konsultasi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4 bg-light">
<div class="container bg-white p-4 rounded shadow">
    <h4>💬 Konsultasi ke Dokter</h4>

    <select id="dokterSelect" class="form-select mb-3">
        <option value="">-- Pilih Dokter --</option>
        <?php
        $dokter = $conn->query("SELECT id, nama FROM users WHERE role='dokter'");
        while ($d = $dokter->fetch_assoc()) {
            echo "<option value='{$d['id']}'>{$d['nama']}</option>";
        }
        ?>
    </select>

    <div id="chatBox" class="border p-3 mb-3" style="height: 300px; overflow-y: auto;"></div>

    <form id="formChat">
        <div class="input-group">
            <input type="text" name="pesan" class="form-control" placeholder="Tulis pesan..." required>
            <button class="btn btn-success">Kirim</button>
        </div>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
let id_dokter = '';

$('#dokterSelect').on('change', function() {
    id_dokter = $(this).val();
    loadChat();
});

function loadChat() {
    if (!id_dokter) return;
    $.get('load_chat_konsultasi.php', {
        id_user: <?= $id_penjual ?>,
        id_dokter: id_dokter
    }, function(data) {
        $('#chatBox').html(data);
        $('#chatBox').scrollTop($('#chatBox')[0].scrollHeight);
    });
}

$('#formChat').on('submit', function(e) {
    e.preventDefault();
    if (!id_dokter) return alert("Pilih dokter dulu!");

    $.post('simpan_chat_konsultasi.php', {
        id_dokter: id_dokter,
        pesan: $('[name="pesan"]').val()
    }, function(res) {
        if (res.success) {
            $('[name="pesan"]').val('');
            loadChat();
        } else {
            alert(res.message);
        }
    }, 'json');
});

setInterval(loadChat, 3000);
</script>
</body>
</html>
