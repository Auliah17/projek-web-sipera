<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'penjual') {
    header("Location: ../../login.php");
    exit();
}

require_once __DIR__ . '/../../../config/database.php';
$id_penjual = $_SESSION['user_id'];

if (isset($_GET['hapus']) && isset($_GET['id_pembeli']) && isset($_GET['id_ternak'])) {
    $id_pembeli = (int) $_GET['id_pembeli'];
    $id_ternak = (int) $_GET['id_ternak'];
    $conn->query("DELETE FROM chat WHERE 
        (pengirim_id = $id_pembeli AND penerima_id = $id_penjual AND id_ternak = $id_ternak)
        OR 
        (pengirim_id = $id_penjual AND penerima_id = $id_pembeli AND id_ternak = $id_ternak)");
    header("Location: inbox_chat.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Inbox Chat - SIPERA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .sipera-header {
            background-color: #2e7d32;
            color: white;
            padding: 15px 25px;
            border-radius: 0 0 10px 10px;
            margin-bottom: 30px;
        }
        .chat-card {
            border: 1px solid #ddd;
            border-left: 5px solid #2e7d32;
            margin-bottom: 15px;
            transition: 0.2s ease;
        }
        .chat-card:hover {
            background-color: #f1f1f1;
        }
        .btn-sipera {
            background-color: #2e7d32;
            color: white;
        }
        .btn-sipera:hover {
            background-color: #256029;
        }
        .badge-warning {
            background-color: #ffc107;
            color: #000;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="sipera-header mb-4">
        <h4 class="mb-0">📥 Obrolan Masuk dari Pembeli</h4>
    </div>

    <div id="chat-container">
        <div class="alert alert-info">Memuat data chat...</div>
    </div>
</div>

<script>
    function loadInbox() {
        fetch("cek_chat_baru.php")
            .then(res => res.json())
            .then(data => {
                let container = document.getElementById('chat-container');
                if (!data || data.length === 0) {
                    container.innerHTML = '<div class="alert alert-info">Belum ada chat dari pembeli.</div>';
                    return;
                }

                let html = '<div class="list-group">';
                data.forEach(row => {
                    html += `
                        <div class="list-group-item chat-card">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>${row.nama_pembeli}</strong> 
                                    <span class="text-muted">(${row.jenis_ternak})</span><br>
                                    <small class="text-muted">🕒 Terakhir: ${row.last_chat}</small>
                                </div>
                                <div class="text-end">
                                    <a href="chat_penjual.php?id_pembeli=${row.id_pembeli}&id_ternak=${row.id_ternak}" 
                                       class="btn btn-sm btn-sipera">
                                       💬 Buka Chat 
                                       ${row.unread_count > 0 ? `<span class='badge badge-warning ms-1'>${row.unread_count}</span>` : ''}
                                    </a>
                                    <a href="inbox_chat.php?hapus=1&id_pembeli=${row.id_pembeli}&id_ternak=${row.id_ternak}" 
                                       class="btn btn-sm btn-danger ms-1"
                                       onclick="return confirm('Yakin ingin menghapus chat ini?')">
                                       🗑 Hapus
                                    </a>
                                </div>
                            </div>
                        </div>
                    `;
                });
                html += '</div>';
                container.innerHTML = html;
            })
            .catch(err => {
                console.error('Gagal memuat chat:', err);
            });
    }

    setInterval(loadInbox, 3000); // muat ulang setiap 3 detik
    loadInbox(); // muat pertama kali saat halaman dibuka
</script>

</body>
</html>
