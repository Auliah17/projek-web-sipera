<?php
session_start();
require_once __DIR__ . '/../../../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'pembeli') {
    header("Location: ../../login.php");
    exit();
}

$id_user = $_SESSION['user_id'];

function getAduanList($conn, $id_user) {
    $stmt = $conn->prepare("SELECT id, subjek, tanggal, status FROM aduan WHERE id_pengirim = ? AND role_pengirim = 'pembeli' ORDER BY tanggal DESC");
    $stmt->bind_param('i', $id_user);
    $stmt->execute();
    $result = $stmt->get_result();

    $aduanList = [];
    while ($row = $result->fetch_assoc()) {
        $aduanList[] = $row;
    }
    return $aduanList;
}

if (isset($_GET['action']) && $_GET['action'] === 'list_aduan') {
    header('Content-Type: application/json');
    echo json_encode(getAduanList($conn, $id_user));
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['subjek'], $_POST['pesan'])) {
    $subjek = trim($_POST['subjek']);
    $pesan = trim($_POST['pesan']);

    if ($subjek === '' || $pesan === '') {
        http_response_code(400);
        echo json_encode(['error' => 'Subjek dan pesan wajib diisi']);
        exit();
    }

    $stmt = $conn->prepare("INSERT INTO aduan (id_pengirim, role_pengirim, subjek, pesan, tanggal, status) VALUES (?, 'pembeli', ?, ?, NOW(), 'baru')");
    $stmt->bind_param('iss', $id_user, $subjek, $pesan);
    if ($stmt->execute()) {
        echo json_encode(['success' => 'Aduan berhasil dikirim']);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Gagal mengirim aduan']);
    }
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <title>Aduan Pembeli</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', sans-serif;
            padding: 40px;
        }
        .container {
            max-width: 2000px;
            margin: auto;
        }
        .card {
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }
        .card-header {
            background-color: #7B1E1E;
            color: white;
            font-weight: bold;
        }
        .btn-submit {
            background-color: #7B1E1E;
            border: none;
        }
        .btn-submit:hover {
            background-color: #7B1E1E;
        }
        .message.success {
            background: #d4edda;
            color: #7B1E1E;
        }
        .message.error {
            background: #f8d7da;
            color: #721c24;
        }
        .message {
            margin-top: 15px;
            padding: 10px 15px;
            border-radius: 6px;
            display: none;
        }
        table {
            background: white;
        }
        th {
            background-color: #7B1E1E;
            color: white;
        }
        .table td, .table th {
            vertical-align: middle;
        }
        .no-data {
            text-align: center;
            padding: 20px;
            color: #777;
        }
        /* Tombol kembali ke beranda */
        .btn-outline-danger {
            color: #7B1E1E;
            border: 2px solid #7B1E1E;
            font-weight: bold;
        }
        .btn-outline-danger:hover {
            background-color: #7B1E1E;
            color: white;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="card mb-4">
        <div class="card-header">Kirim Aduan ke Admin</div>
        <div class="card-body">
            <form id="aduanForm">
                <div class="mb-3">
                    <label for="subjek" class="form-label">Subjek</label>
                    <input type="text" class="form-control" id="subjek" name="subjek" maxlength="100" required>
                </div>
                <div class="mb-3">
                    <label for="pesan" class="form-label">Pesan</label>
                    <textarea class="form-control" id="pesan" name="pesan" rows="5" required></textarea>
                </div>
                <button type="submit" class="btn btn-submit text-white">Kirim Aduan</button>
                <div id="message" class="message mt-3"></div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">Daftar Aduan Saya</div>
        <div class="card-body" id="aduanListContainer">
            <!-- Data akan dimuat oleh JavaScript -->
        </div>
    </div>
</div>

<!-- Tombol kembali -->
<div class="container mt-4 text-start">
    <a href="dashboard.php" class="btn btn-outline-danger">
        ← Kembali ke Beranda
    </a>
</div>

<script>
const pollingInterval = 5000;

async function fetchAduanList() {
    try {
        const response = await fetch('?action=list_aduan');
        if (!response.ok) throw new Error('Gagal mengambil data');
        const data = await response.json();

        const container = document.getElementById('aduanListContainer');
        if (data.length === 0) {
            container.innerHTML = '<p class="no-data">Belum ada aduan yang kamu kirim.</p>';
            return;
        }

        let html = `<div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead>
                    <tr>
                        <th>Subjek</th>
                        <th>Tanggal</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>`;
        data.forEach(aduan => {
            html += `<tr>
                <td>${escapeHtml(aduan.subjek)}</td>
                <td>${formatDate(aduan.tanggal)}</td>
                <td><span class="badge bg-${aduan.status === 'baru' ? 'warning' : 'success'}">${escapeHtml(aduan.status)}</span></td>
                <td><a href="detail_aduan.php?id=${aduan.id}" class="btn btn-sm btn-outline-primary">Buka Chat</a></td>
            </tr>`;
        });
        html += '</tbody></table></div>';
        container.innerHTML = html;
    } catch (error) {
        console.error(error);
    }
}

function escapeHtml(text) {
    if (!text) return '';
    return text
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}

function formatDate(datetime) {
    const d = new Date(datetime);
    const options = { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' };
    return d.toLocaleString('id-ID', options);
}

document.getElementById('aduanForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    const subjek = document.getElementById('subjek').value.trim();
    const pesan = document.getElementById('pesan').value.trim();
    const messageDiv = document.getElementById('message');

    if (!subjek || !pesan) {
        messageDiv.textContent = 'Subjek dan pesan wajib diisi.';
        messageDiv.className = 'message error';
        messageDiv.style.display = 'block';
        return;
    }

    try {
        const response = await fetch('', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({ subjek, pesan })
        });

        const result = await response.json();

        if (response.ok) {
            messageDiv.textContent = result.success;
            messageDiv.className = 'message success';
            document.getElementById('aduanForm').reset();
            fetchAduanList();
        } else {
            messageDiv.textContent = result.error || 'Terjadi kesalahan.';
            messageDiv.className = 'message error';
        }
        messageDiv.style.display = 'block';
    } catch (err) {
        messageDiv.textContent = 'Gagal mengirim aduan, coba lagi.';
        messageDiv.className = 'message error';
        messageDiv.style.display = 'block';
    }
});

function startPolling() {
    fetchAduanList();
    setInterval(fetchAduanList, pollingInterval);
}

startPolling();
</script>
</body>
</html>
