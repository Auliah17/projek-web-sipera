<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'pembeli') {
    header("Location: ../../login.php");
    exit();
}
require_once __DIR__ . '/../../../config/database.php';

$id_pembeli = $_SESSION['user_id'];

$notif_query = $conn->prepare("SELECT COUNT(*) AS total_unread FROM chat WHERE penerima_id = ? AND is_read = 0");
$notif_query->bind_param("i", $id_pembeli);
$notif_query->execute();
$total_unread = $notif_query->get_result()->fetch_assoc()['total_unread'] ?? 0;

$keyword = isset($_GET['keyword']) ? $_GET['keyword'] : '';
$query = $conn->prepare("SELECT * FROM ternak WHERE jenis LIKE ? ORDER BY id DESC");
$like = "%$keyword%";
$query->bind_param("s", $like);
$query->execute();
$result = $query->get_result();
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" /><meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Dashboard Pembeli - SIPERA</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
  <style>
    body { background: #f8f9fa; padding-bottom: 80px; font-family: 'Segoe UI', sans-serif; }
    .navbar { background-color: #7b1e1e; padding: 10px 20px; color: white; display: flex; justify-content: space-between; }
    .navbar a { color: white; margin-left: 15px; text-decoration: none; font-weight: 500; }
    .animal-card { background: white; border-radius: 12px; padding: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); height: 100%; }
    .image-container {
      width: 100%;
      height: 220px;
      display: flex;
      justify-content: center;
      align-items: center;
      overflow: hidden;
      border-radius: 8px;
      background: #f8f9fa;
    }
    .image-container img {
      max-width: 100%;
      max-height: 100%;
      object-fit: cover;
      cursor: pointer;
    }
    .btn-small { font-size: .85rem; padding: 6px 12px; margin: 2px; }
    .search-bar { margin-top: 20px; display: flex; justify-content: center; gap: 10px; }
    .footer-menu { position: fixed; bottom: 0; left: 0; right: 0; background: white; border-top: 1px solid #ddd; display: flex; justify-content: space-around; padding: 10px 0; }
    .footer-menu a { color: #555; text-align: center; text-decoration: none; font-size: 12px; }
    .footer-menu i { font-size: 20px; }
    .footer-menu a:hover { color: #7b1e1e; }
    <style>
    .btn-sipera-merah {
      background-color: #7b1e1e;
      color: white;
      border: none;
    }
    .btn-sipera-merah:hover {
      background-color: #7b1e1e;
      color: white;
    }
  </style>
</head>
<body>

  <nav class="navbar">
    <div class="logo-text fw-bold">SIPERA</div>
    <div>
      <a href="riwayat.php"><i class="bi bi-clock-history"></i> Riwayat</a>
      <a href="aduan.php"><i class="bi bi-exclamation-triangle"></i> Aduan</a>
      <a href="/sipera/logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
    </div>
  </nav>

  <div class="container mt-4">
    <h4 class="mb-3 text-center">Selamat datang di SIPERA!</h4>

    <form method="GET" class="search-bar">
      <input type="text" name="keyword" value="<?= htmlspecialchars($keyword) ?>" class="form-control w-50" placeholder="Cari jenis ternak...">
      <button class="btn btn-danger"><i class="bi bi-search"></i></button>
    </form>

    <div class="row mt-4">
      <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
        <div class="col-md-4 mb-4">
          <div class="animal-card">
            <div class="image-container">
              <img src="../../../<?= htmlspecialchars($row['foto']) ?>"
                   alt="<?= htmlspecialchars($row['jenis']) ?>"
                   data-foto="../../../<?= htmlspecialchars($row['foto']) ?>"
                   data-bs-toggle="modal"
                   data-bs-target="#detailModal"
                   data-jenis="<?= htmlspecialchars($row['jenis']) ?>"
                   data-harga="<?= number_format($row['harga']) ?>"
                   data-usia="<?= htmlspecialchars($row['usia']) ?>"
                   data-stok_awal="<?= htmlspecialchars($row['stok_awal']) ?>"
                   data-stok_sisa="<?= htmlspecialchars($row['stok_sisa']) ?>"
                   data-deskripsi="<?= htmlspecialchars($row['deskripsi'] ?: 'Deskripsi tidak tersedia.') ?>">
            </div>
            <div class="mt-2"><strong><?= ucfirst(htmlspecialchars($row['jenis'])) ?></strong></div>
            <div class="text-success fw-bold">Rp <?= number_format($row['harga']) ?></div>
            <p class="card-text mb-1"><small class="text-muted">Usia: <?= $row['usia'] ?> tahun</small></p>
            <div><small>Stok: <?= htmlspecialchars($row['stok_sisa']) ?> / <?= htmlspecialchars($row['stok_awal']) ?></small></div>
            <div class="mt-2">
              <a href="form_pemesanan_ternak.php?id=<?= $row['id'] ?>" class="btn btn-danger btn-small"><i class="bi bi-cart-fill"></i> Pesan</a>
              <a href="auto_chat.php?id_ternak=<?= $row['id'] ?>" class="btn btn-outline-primary btn-small"><i class="bi bi-chat-dots"></i> Chat</a>
            </div>
          </div>
        </div>
        <?php endwhile; ?>
      <?php else: ?>
        <div class="col-12 text-center text-muted mt-4">
          <i class="bi bi-exclamation-circle"></i> Tidak ada ternak ditemukan.
        </div>
      <?php endif; ?>
    </div>
  </div>

  <!-- Modal Detail -->
  <div class="modal fade" id="detailModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content">
        <div class="modal-header"><h5 class="modal-title">Detail Ternak</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body d-flex flex-column flex-md-row gap-3">
          <img src="" id="modalFoto" class="img-fluid rounded" style="max-width:300px; object-fit:cover;">
          <div>
            <h4 id="modalJenis"></h4>
            <h5 class="text-success" id="modalHarga"></h5>
            <p><strong>Usia:</strong> <span id="modalUsia"></span> tahun</p>
            <p><strong>Stok:</strong> <span id="modalStokSisa"></span> / <span id="modalStokAwal"></span></p>
            <p id="modalDeskripsi" style="white-space: pre-wrap;"></p>
          </div>
        </div>
        <div class="modal-footer"><button class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button></div>
      </div>
    </div>
  </div>

  <div class="footer-menu">
    <a href="dashboard.php"><i class="bi bi-house-door-fill"></i><div>Beranda</div></a>
    <a href="transaksi.php"><i class="bi bi-receipt"></i><div>Transaksi</div></a>
    <a href="inbox_chat.php"><i class="bi bi-chat-dots-fill"></i><div>Obrolan<?= $total_unread>0?" ($total_unread)":"" ?></div></a>
    <a href="profile.php"><i class="bi bi-person-circle"></i><div>Profil</div></a>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    const modal = document.getElementById('detailModal');
    modal.addEventListener('show.bs.modal', e => {
      const img = e.relatedTarget;
      document.getElementById('modalFoto').src = img.dataset.foto;
      document.getElementById('modalJenis').textContent = img.dataset.jenis;
      document.getElementById('modalHarga').textContent = "Rp " + img.dataset.harga;
      document.getElementById('modalUsia').textContent = img.dataset.usia;
      document.getElementById('modalStokAwal').textContent = img.dataset.stok_awal;
      document.getElementById('modalStokSisa').textContent = img.dataset.stok_sisa;
      document.getElementById('modalDeskripsi').textContent = img.dataset.deskripsi;
    });
  </script>
</body>
</html>
