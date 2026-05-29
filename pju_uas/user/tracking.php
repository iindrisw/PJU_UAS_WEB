<?php
/**
 * user/tracking.php
 * Halaman Lacak Aduan & Daftar Log Semua Laporan Warga (Minimalist Light UI)
 */
require_once dirname(__DIR__) . '/config/database.php';
require_once dirname(__DIR__) . '/config/app.php';

$pageTitle = 'Cek Status Laporan';
$db        = getDB();

$kodeInput = isset($_GET['kode']) ? trim($_GET['kode']) : '';
$laporan   = null;
$logs      = [];

// Jika ada input kode tiket, tarik detail tracking spesifik
if ($kodeInput) {
    $stmt = $db->prepare("
        SELECT l.*, t.nama AS nama_teknisi 
        FROM laporan l 
        LEFT JOIN teknisi t ON l.teknisi_id = t.id 
        WHERE l.kode_laporan = ? LIMIT 1
    ");
    $stmt->execute([$kodeInput]);
    $laporan = $stmt->fetch();

    if ($laporan) {
        $stmtLogs = $db->prepare("SELECT * FROM tracking_status WHERE laporan_id = ? ORDER BY created_at DESC");
        $stmtLogs->execute([$laporan['id']]);
        $logs = $stmtLogs->fetchAll();
    }
}

// Tarik SEMUA laporan untuk log publik masyarakat
$stmtAll = $db->query("
    SELECT l.*, t.nama AS nama_teknisi 
    FROM laporan l 
    LEFT JOIN teknisi t ON l.teknisi_id = t.id 
    ORDER BY l.created_at DESC
");
$allLaporan = $stmtAll->fetchAll();

include dirname(__DIR__) . '/templates/header.php';
?>

<style>
  body { background-color: #FAFAFB; color: #1E293B; font-family: 'Plus Jakarta Sans', sans-serif; }
  .navbar-clean { background: #FFFFFF; border-bottom: 1px solid #E2E8F0; padding: 16px 0; }
  .nav-link-clean { color: #475569 !important; font-weight: 600; font-size: 0.95rem; }
  .nav-link-clean:hover, .nav-link-clean.active { color: #6366F1 !important; }
  .card-clean { background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 16px; box-shadow: 0 1px 3px rgba(0,0,0,0.02); }
  .text-dark-solid { color: #0F172A !important; font-weight: 700; }
</style>

<nav class="navbar navbar-expand-lg navbar-clean">
  <div class="container">
    <a class="navbar-brand d-flex align-items-center gap-2" href="<?= BASE_URL ?>/">
      <div class="text-primary fs-4" style="color: #6366F1 !important;"><i class="bi bi-lightbulb-fill"></i></div>
      <span style="font-weight: 800; color: #0F172A; letter-spacing: -0.02em;">Sistem PJU</span>
    </a>
    <div class="collapse navbar-collapse" id="navMain">
      <ul class="navbar-nav ms-auto align-items-lg-center gap-4">
        <li><a class="nav-link nav-link-clean" href="<?= BASE_URL ?>/">Beranda</a></li>
        <li><a class="nav-link nav-link-clean" href="<?= BASE_URL ?>/user/laporan.php">Buat Laporan</a></li>
        <li><a class="nav-link nav-link-clean active" href="<?= BASE_URL ?>/user/tracking.php">Cek Status</a></li>
      </ul>
    </div>
  </div>
</nav>

<div class="container py-5">
  
  <div class="card-clean p-4 mb-5 mx-auto" style="max-width: 600px;">
    <h5 class="text-dark-solid mb-3 text-center"><i class="bi bi-search me-2 text-primary"></i>Lacak Tiket Aduan Anda</h5>
    <form method="GET" action="">
      <div class="input-group">
        <input type="text" name="kode" class="form-control p-3 border-end-0" style="border-radius: 10px 0 0 10px;" placeholder="Contoh: PJU-202605-0001" value="<?= e($kodeInput) ?>" required>
        <button class="btn px-4 text-white" type="submit" style="background: #6366F1; border-radius: 0 10px 10px 0; font-weight: 700;">Cari Berkas</button>
      </div>
    </form>
  </div>

  <?php if ($kodeInput): ?>
    <?php if (!$laporan): ?>
      <div class="alert text-center card-clean p-5 mb-5" style="border-left: 4px solid #EF4444 !important;">
        <i class="bi bi-exclamation-triangle text-danger fs-1 d-block mb-3"></i>
        <h5 class="text-dark-solid">Kode Tiket Tidak Ditemukan</h5>
        <p class="text-muted mb-0">Pastikan kombinasi nomor unik laporan yang Anda masukkan sudah sesuai.</p>
      </div>
    <?php else: ?>
      <div class="card-clean p-4 mb-5">
        <h5 class="text-dark-solid mb-4 pb-2 border-bottom">Status Keluhan: <?= e($laporan['kode_laporan']) ?></h5>
        <div class="row g-4 mb-4">
          <div class="col-md-6">
            <small class="text-muted d-block fw-600">NAMA PELAPOR</small>
            <div class="text-dark-solid fs-6"><?= e($laporan['nama_pelapor']) ?></div>
          </div>
          <div class="col-md-6">
            <small class="text-muted d-block fw-600">STATUS SEKARANG</small>
            <div><?= statusBadge($laporan['status']) ?></div>
          </div>
        </div>
        
        <h6 class="text-dark-solid mb-3 mt-4"><i class="bi bi-clock-history me-2 text-primary"></i>Log Kemajuan Perbaikan</h6>
        <div class="ps-3 border-start style-timeline">
          <?php foreach ($logs as $log): ?>
            <div class="mb-3 position-relative pb-1">
              <span class="text-dark-solid d-block" style="font-size: 0.92rem;"><?= e($log['keterangan']) ?></span>
              <small class="text-muted"><?= date('d M Y H:i', strtotime($log['created_at'])) ?> WIB · Oleh: <?= e($log['diubah_oleh']) ?></small>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    <?php endif; ?>
  <?php endif; ?>

  <div class="card-clean overflow-hidden">
    <div class="p-4 bg-light border-bottom">
      <h5 class="text-dark-solid mb-1">Seluruh Log Aktivitas Pengaduan Jaringan</h5>
      <p class="text-muted small mb-0">Daftar transparan berkas aduan yang masuk dari seluruh masyarakat kota</p>
    </div>
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0" style="font-size: 0.92rem;">
        <thead class="table-light">
          <tr class="text-secondary" style="font-weight: 600;">
            <th class="ps-4 py-3">Kode Tiket</th>
            <th>Nama Warga</th>
            <th>Alamat Wilayah</th>
            <th>Status Kerja</th>
            <th class="pe-4">Waktu Masuk</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($allLaporan as $row): ?>
          <tr>
            <td class="ps-4">
              <a href="?kode=<?= e($row['kode_laporan']) ?>" class="fw-bold text-decoration-none" style="font-family:'Space Mono', monospace; color: #6366F1;">
                <?= e($row['kode_laporan']) ?>
              </a>
            </td>
            <td class="fw-700 text-dark-solid"><?= e($row['nama_pelapor']) ?></td>
            <td class="text-muted text-truncate" style="max-width:280px; font-weight: 500;">
              <?= e($row['alamat_lokasi'] ?? 'Koordinat terlampir') ?>
            </td>
            <td>
              <?php 
              $badgeColor = match($row['status']) {
                  'menunggu' => 'bg-warning-subtle text-warning border border-warning-subtle',
                  'diproses', 'dalam_perjalanan' => 'bg-primary-subtle text-primary border border-primary-subtle',
                  'selesai' => 'bg-success-subtle text-success border border-success-subtle',
                  default => 'bg-danger-subtle text-danger border border-danger-subtle'
              };
              ?>
              <span class="badge <?= $badgeColor ?> px-2.5 py-1.5 rounded-pill text-uppercase fw-700" style="font-size: 0.65rem;">
                  <?= statusLabel($row['status']) ?>
              </span>
            </td>
            <td class="text-muted small pe-4 fw-500"><?= date('d/m/Y H:i', strtotime($row['created_at'])) ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

</div>
<?php include dirname(__DIR__) . '/templates/footer.php'; ?>