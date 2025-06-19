<?php
session_start();
require_once __DIR__ . '/../../../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'dokter') {
    header("Location: ../../login.php");
    exit();
}

$id_dokter = $_SESSION['user_id'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Chat Pasien</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-light p-4">
<div class="container bg-white p-4 rounded shadow">
    <h4>Chat dari Penjual</h4>
    <form id="formKonsultasi">
        <input type="hidden" name="role" value="dokter">
        <input type="hidden" name="id_dokter" value="<?= $id_dokter ?>">
        <div class="mb-3">
            <label for="penjual">Pilih Penjual</label>
            <select name="id_user" class="form-select" required>
                <option value="">-- Pilih Penjual --</option>
                <?php
                $penjual = $conn->query("SELECT DISTINCT id_user FROM konsultasi WHERE id_dokter = $id_dokter");
                while ($p = $penjual->fetch_assoc()) {
                    $u = $conn->query("SELECT nama FROM users WHERE id = {$p['id_user']}")->fetch_assoc();
                    echo "<option value='{$p['id_user']}'>{$u['nama']}</option>";
                }
                ?>
            </select>
        </div>
        <textarea name="pesan" class="form-control mb-3" placeholder="Balas pesan..."></textarea>
        <button class="btn btn-success">Balas</button>
    </form>

    <hr>
    <div id="chatBox" style="height:300px; overflow-y:auto;"></div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
let id_user = '';

$('select[name="id_user"]').on('change', function() {
    id_user = $(this).val();
    loadChat();
});

function loadChat() {
    if (!id_user) return;
    $.get('../../../ajax/chat_ajax.php', {
        id_user: id_user,
        id_dokter: <?= $id_dokter ?>,
        role: 'dokter'
    }, function(data) {
        $('#chatBox').html(data);
        $('#chatBox').scrollTop($('#chatBox')[0].scrollHeight);
    });
}

$('#formKonsultasi').on('submit', function(e) {
    e.preventDefault();
    $.post('../../../ajax/simpan_konsultasi.php', $(this).serialize(), function(res) {
        if (res.success) {
            $('#formKonsultasi')[0].reset();
            loadChat();
        } else {
            alert(res.message);
        }
    }, 'json');
});

setInterval(loadChat, 1000);
</script>
</body>
</html>
