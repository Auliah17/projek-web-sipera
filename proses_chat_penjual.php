<?php
session_start();
require_once '../../../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'pembeli') {
    http_response_code(403);
    exit("Akses ditolak");
}

$id_pembeli = $_SESSION['user_id'];
$id_penjual = $_POST['id_penjual'] ?? $_GET['id_penjual'] ?? null;
$id_ternak  = $_POST['id_ternak'] ?? $_GET['id_ternak'] ?? null;

if (!$id_penjual || !$id_ternak) {
    http_response_code(400);
    exit("Data tidak lengkap");
}

// Kirim pesan (via AJAX POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pesan'])) {
    $pesan = trim($_POST['pesan']);
    if ($pesan !== '') {
        $stmt = $conn->prepare("INSERT INTO chat (pengirim_id, penerima_id, id_ternak, pesan, created_at, is_read) VALUES (?, ?, ?, ?, NOW(), 0)");
        $stmt->bind_param("iiis", $id_pembeli, $id_penjual, $id_ternak, $pesan);
        $stmt->execute();
    }
    exit;
}

// Ambil pesan (ditampilkan langsung sebagai HTML)
$stmt = $conn->prepare("
    SELECT * FROM chat 
    WHERE id_ternak = ? 
      AND ((pengirim_id = ? AND penerima_id = ?) OR (pengirim_id = ? AND penerima_id = ?)) 
    ORDER BY created_at ASC
");
$stmt->bind_param("iiiii", $id_ternak, $id_pembeli, $id_penjual, $id_penjual, $id_pembeli);
$stmt->execute();
$result = $stmt->get_result();

// CSS inline (jika ini digunakan langsung di view):
echo <<<STYLE
<style>
.message {
    display: flex;
    margin-bottom: 12px;
    padding: 0 10px;
}
.from-me {
    justify-content: flex-end;
}
.from-them {
    justify-content: flex-start;
}
.bubble {
    max-width: 70%;
    padding: 12px 16px;
    border-radius: 16px;
    position: relative;
    background-color: #dcf8c6;
    color: #000;
    box-shadow: 0 1px 2px rgba(0,0,0,0.1);
    word-wrap: break-word;
}
.from-them .bubble {
    background-color: #ffffff;
    border: 1px solid #ddd;
}
.bubble .time {
    font-size: 11px;
    color: #555;
    margin-top: 5px;
    text-align: right;
}
</style>
STYLE;

while ($row = $result->fetch_assoc()):
    $dariPembeli = $row['pengirim_id'] == $id_pembeli;
    $class = $dariPembeli ? 'from-me' : 'from-them';
    $waktuLengkap = date('d M Y H:i', strtotime($row['created_at']));
    $pesan = nl2br(htmlspecialchars($row['pesan']));
    echo "<div class='message {$class}'>
            <div class='bubble'>
                {$pesan}
                <div class='time'>{$waktuLengkap}</div>
            </div>
          </div>";
endwhile;
?>
