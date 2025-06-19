<?php
session_start();
require_once __DIR__ . '/../../../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'penjual') {
    header("Location: ../../login.php");
    exit();
}

$id_user = $_SESSION['user_id'];

function getAduanList($conn, $id_user) {
    $stmt = $conn->prepare("SELECT id, subjek, tanggal, status FROM aduan WHERE id_pengirim = ? AND role_pengirim = 'penjual' ORDER BY tanggal DESC");
    $stmt->bind_param('i', $id_user);
    $stmt->execute();
    $result = $stmt->get_result();

    $aduanList = [];
    while ($row = $result->fetch_assoc()) {
        $aduanList[] = $row;
    }
    return $aduanList;
}

// Handle AJAX list
if (isset($_GET['action']) && $_GET['action'] === 'list_aduan') {
    header('Content-Type: application/json');
    echo json_encode(getAduanList($conn, $id_user));
    exit();
}

// Handle Kirim
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['subjek'], $_POST['pesan'])) {
    $subjek = trim($_POST['subjek']);
    $pesan = trim($_POST['pesan']);

    if ($subjek === '' || $pesan === '') {
        http_response_code(400);
        echo json_encode(['error' => 'Subjek dan pesan wajib diisi']);
        exit();
    }

    $stmt = $conn->prepare("INSERT INTO aduan (id_pengirim, role_pengirim, subjek, pesan, tanggal, status) VALUES (?, 'penjual', ?, ?, NOW(), 'baru')");
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
    <meta charset="UTF-8">
    <title>Aduan Penjual</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f0f4f8;
            padding: 20px;
        }

        .back-btn {
            margin-bottom: 20px;
        }

        .back-btn a {
            text-decoration: none;
            background: #ccc;
            color: #000;
            padding: 8px 14px;
            border-radius: 6px;
            font-weight: bold;
        }

        h2 {
            color: #2c3e50;
        }

        form {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 6px 20px rgba(0,0,0,0.1);
            max-width: 600px;
            margin-bottom: 30px;
        }

        label {
            display: block;
            margin-bottom: 6px;
            font-weight: 600;
        }

        input[type="text"], textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 1rem;
            resize: vertical;
        }

        button {
            background: #27ae60;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 700;
            font-size: 1rem;
        }

        button:hover {
            background: #219150;
        }

        .message {
            margin-bottom: 20px;
            padding: 12px;
            border-radius: 6px;
        }

        .message.success {
            background: #d4edda;
            color: #155724;
        }

        .message.error {
            background: #f8d7da;
            color: #721c24;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 12px;
            box-shadow: 0 6px 20px rgba(0,0,0,0.1);
        }

        th, td {
            padding: 12px 15px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }

        th {
            background: #27ae60;
            color: white;
        }

        tr:hover {
            background: #eafaf1;
        }

        a.detail-link {
            color: #219150;
            text-decoration: none;
            font-weight: 600;
        }

        a.detail-link:hover {
            text-decoration: underline;
        }

        .no-data {
            text-align: center;
            padding: 20px;
            font-style: italic;
            color: #555;
        }
    </style>
</head>
<body>
<div class="text-start mt-4">
    <a href="dashboard.php" class="btn btn-outline-secondary">
        &larr; Kembali ke Dashboard
    </a>
</div>
<h2>Kirim Aduan ke Admin</h2>
<form id="aduanForm">
    <label for="subjek">Subjek</label>
    <input type="text" id="subjek" name="subjek" required maxlength="100" />

    <label for="pesan">Pesan</label>
    <textarea id="pesan" name="pesan" rows="5" required></textarea>

    <button type="submit">Kirim Aduan</button>
</form>

<div id="message" class="message" style="display:none;"></div>

<h2>Daftar Aduan Saya</h2>
<div id="aduanListContainer"></div>

<script>
const pollingInterval = 5000;

async function fetchAduanList() {
    try {
        const response = await fetch('?action=list_aduan');
        if (!response.ok) throw new Error('Gagal mengambil data');
        const data = await response.json();

        const container = document.getElementById('aduanListContainer');
        if (data.length === 0) {
            container.innerHTML = '<p class="no-data">Belum ada aduan yang Anda kirim.</p>';
            return;
        }

        let html = '<table><thead><tr><th>Subjek</th><th>Tanggal</th><th>Status</th><th>Chat</th></tr></thead><tbody>';
        data.forEach(aduan => {
            html += `<tr>
                <td>${escapeHtml(aduan.subjek)}</td>
                <td>${formatDate(aduan.tanggal)}</td>
                <td>${escapeHtml(aduan.status)}</td>
                <td><a class="detail-link" href="detail_aduan.php?id=${aduan.id}">Buka Chat</a></td>
            </tr>`;
        });
        html += '</tbody></table>';
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
