<?php
/**
 * admin/detail_laporan.php
 * Detail Berkas Aduan & Form Penugasan Teknisi Lapangan (Minimalist Light UI)
 */
require_once dirname(__DIR__) . '/config/database.php';
require_once dirname(__DIR__) . '/config/app.php';
requireAdminLogin();

$pageTitle   = 'Detail Laporan';
$currentPage = 'laporan';
$db          = getDB();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// 1. Tarik Data Detail Laporan
$stmt = $db->prepare("
    SELECT l.*, t.nama AS nama_teknisi, t.no_hp AS hp_teknisi
    FROM laporan l
    LEFT JOIN teknisi t ON l.teknisi_id = t.id
    WHERE l.id = ? LIMIT 1
");
$stmt->execute([$id]);
$l = $stmt->fetch();

if (!$l) {
    setFlash('error', 'Berkas laporan tidak ditemukan.');
    redirect(BASE_URL . '/admin/laporan.php');
}

// 2. PROSES POST: PROSES UPDATE TEKNISI / STATUS
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'tugaskan') {
    if (!verifyCsrf($_POST['csrf_token'] ?? '')) {
        setFlash('error', 'Token keamanan kedaluwarsa.');
    } else {
        $teknisiId = (int)$_POST['teknisi_id'];
        $statusBaru = $_POST['status'];

        // Update data laporan utama
        $stmtUpdate = $db->prepare("UPDATE laporan SET teknisi_id = ?, status = ? WHERE id = ?");
        $stmtUpdate->execute([$teknisiId, $statusBaru, $id]);

        // Ambil nama teknisi terpilih untuk catatan log tracking
        $stmtT = $db->prepare("SELECT nama FROM teknisi WHERE id = ?");
        $stmtT->execute([$teknisiId]);
        $namaTeknisi = $stmtT->fetchColumn() ?: 'Kru Lapangan';

        // Masukkan riwayat baru ke tabel tracking status biar user bisa lacak
        $keteranganLog = "Laporan diperbarui ke status [" . statusLabel($statusBaru) . "] dan ditugaskan kepada: " . $namaTeknisi;
        $stmtLog = $db->prepare("INSERT INTO tracking_status (laporan_id, status, keterangan, diubah_oleh, created_at) VALUES (?, ?, ?, ?, NOW())");
        $stmtLog->execute([$id, $statusBaru, $keteranganLog, $_SESSION['admin_nama']]);

        setFlash('success', 'Teknisi lapangan berhasil didelegasikan!');
        redirect(BASE_URL . "/admin/detail_laporan.php?id=" . $id);
    }
}

// 3. Ambil Semua Daftar Teknisi yang Berstatus Aktif untuk Dropdown Menu
$daftarTeknisi = $db->query("SELECT id, nama FROM teknisi WHERE status = 'aktif' ORDER BY nama ASC")->fetchAll();

include dirname(__DIR__) . '/templates/header.php';
?>

<style>
  body { background-color: #FAFAFB !important; color: #1E293B; }
  .admin-wrapper { display: flex; min-height: 100vh; }
  .sidebar { width: 260px; background: #FFFFFF !important; position: fixed; height: 100vh; border-right: 1px solid #E2E8F0; z-index: 100; }
  .sidebar .nav-item.active { background: #F1F5F9 !important; color: #6366F1 !important; font-weight: 600; }
  .main-content { margin-left: 260px; flex-grow: 1; padding: 0; background-color: #FAFAFB; }
  .topbar-clean { background: #FFFFFF; padding: 20px 32px; border-bottom: 1px solid #E2E8F0; }
  .content-padding { padding: 32px; }
  .card-clean-panel { background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 14px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.02); }
  .info-title { font-size: 0.75rem; font-weight: 700; color: #64748B; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px; }
  .info-value { font-size: 0.95rem; font-weight: 600; color: #0F172A; }
</style>

<div class="admin-wrapper">
  <?php include dirname(__DIR__) . '/templates/admin_sidebar.php'; ?>

  <main class="main-content">
    <div class="topbar-clean d-flex justify-content-between align-items-center">
      <div class="d-flex align-items-center gap-3">
        <a href="<?= BASE_URL ?>/admin/laporan.php" class="btn btn-sm btn-light border px-2.5 py-1.5" style="border-radius: 8px;">
          <i class="bi bi-arrow-left"></i> Kembali
        </a>
        <h4 class="fw-800 mb-0" style="color: #0F172A;">Detail Berkas Tiket: <?= e($l['kode_laporan']) ?></h4>
      </div>
      <div><?= statusBadge($l['status']) ?></div>
    </div>

    <div class="content-padding">
      <div class="row g-4">
        
        <div class="col-12 col-lg-7">
          <div class="card-clean-panel d-flex flex-column gap-4">
            
            <div class="row g-3">
              <div class="col-6">
                <div class="info-title">Nama Pelapor</div>
                <div class="info-value"><?= e($l['nama_pelapor']) ?></div>
              </div>
              <div class="col-6">
                <div class="info-title">Nomor Kontak (WA)</div>
                <div class="info-value font-monospace"><?= e($l['no_hp']) ?></div>
              </div>
            </div>

            <div>
              <div class="info-title">Isi Deskripsi Keluhan</div>
              <div class="info-value p-3 bg-light rounded-3 fw-500" style="line-height: 1.6;"><?= e($l['deskripsi']) ?></div>
            </div>

            <div>
              <div class="info-title">Estimasi Alamat Fisik</div>
              <div class="info-value text-muted fw-500" style="font-size: 0.9rem;"><i class="bi bi-geo-alt me-1 text-danger"></i><?= e($l['alamat_lokasi'] ?? 'Koordinat terlampir') ?></div>
            </div>

            <div>
              <div class="info-title mb-2">Lampiran Foto Lapangan</div>
              <?php if ($l['foto']): ?>
                <img src="<?= UPLOAD_URL . e($l['foto']) ?>" class="img-fluid rounded-3 border w-100 shadow-sm" style="max-height: 280px; object-fit: cover;">
              <?php else: ?>
                <div class="p-4 bg-light text-center rounded-3 text-muted small fw-600">Pelapor tidak melampirkan foto fisik.</div>
              <?php endif; ?>
            </div>

          </div>
        </div>

        <div class="col-12 col-lg-5">
          <div class="card-clean-panel mb-4">
            <h5 class="fw-800 text-dark mb-4" style="font-size: 1rem;"><i class="bi bi-person-check-fill me-2 text-primary"></i>Delegasikan Tugas Lapangan</h5>
            
            <form method="POST" action="">
              <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
              <input type="hidden" name="action" value="top up" style="display:none;">
              <input type="hidden" name="action" value="tugaskan">

              <div class="mb-3">
                <label class="small fw-700 mb-2 d-block text-secondary">Pilih Personel Teknisi</label>
                <select name="teknisi_id" class="form-select p-2.5 bg-light fw-600" style="border-radius: 10px;" required>
                  <option value="">-- Pilih Kru Lapangan --</option>
                  <?php foreach ($daftarTeknisi as $tek): ?>
                    <option value="<?= $tek['id'] ?>" <?= $l['teknisi_id'] == $tek['id'] ? 'selected' : '' ?>>
                      <?= e($tek['nama']) ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>

              <div class="mb-4">
                <label class="small fw-700 mb-2 d-block text-secondary">Update Status Penanganan</label>
                <select name="status" class="form-select p-2.5 bg-light fw-600" style="border-radius: 10px;" required>
                  <option value="menunggu"         <?= $l['status'] === 'menunggu' ? 'selected' : '' ?>>Menunggu Antrean</option>
                  <option value="diproses"         <?= $l['status'] === 'diproses' ? 'selected' : '' ?>>Sedang Diproses Kerja</option>
                  <option value="dalam_perjalanan" <?= $l['status'] === 'dalam_perjalanan' ? 'selected' : '' ?>>Dalam Perjalanan Menuju Lokasi</option>
                  <option value="selesai"          <?= $l['status'] === 'selesai' ? 'selected' : '' ?>>Selesai Diperbaiki (Lampu Nyala)</option>
                  <option value="dibatalkan"       <?= $l['status'] === 'dibatalkan' ? 'selected' : '' ?>>Dibatalkan Sistem</option>
                </select>
              </div>

              <button type="submit" class="btn text-white w-100 py-2.5 fw-700" 
                      style="background-color: #6366F1; border-radius: 10px; box-shadow: 0 4px 12px rgba(99,102,241,0.2);">
                <i class="bi bi-send-fill me-1"></i> Simpan & Kirim Penugasan
              </button>
            </form>
          </div>

          <div class="card-clean-panel p-2">
            <div id="map-detail-tiang" style="height: 180px; border-radius: 10px;"></div>
          </div>

        </div>

      </div>
    </div>
  </main>
</div>

<?php
// Inject kordinat untuk Leaflet Map mini di pojok kanan bawah
$latMap = $l['latitude']  ?? '-6.9175';
$lngMap = $l['longitude'] ?? '107.6191';
$kodeTiket = $l['kode_laporan'];

$extraJs = "
<script>
document.addEventListener('DOMContentLoaded', () => {
  const mapElement = document.getElementById('map-detail-tiang');
  if (mapElement) {
    const map = L.map('map-detail-tiang').setView([$latMap, $lngMap], 15);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
    
    const customIcon = L.divIcon({
      html: '<div style=\"background:#6366F1; width:14px; height:14px; border-radius:50%; border:2px solid #fff; box-shadow:0 1px 4px rgba(0,0,0,0.2);\"><\/div>',
      className: '', iconSize: [14, 14]
    });

    L.marker([$latMap, $lngMap], {icon: customIcon})
     .addTo(map)
     .bindPopup('<b>$kodeTiket<\/b>')
     .openPopup();
  }
});
</script>
";
include dirname(__DIR__) . '/templates/footer.php';
?>