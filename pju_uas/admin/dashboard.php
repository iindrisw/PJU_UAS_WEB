<?php
/**
 * admin/dashboard.php
 * Dashboard Utama Admin — Sistem PJU (Minimalist Light UI)
 */
require_once dirname(__DIR__) . '/config/database.php';
require_once dirname(__DIR__) . '/config/app.php';
requireAdminLogin();

$pageTitle   = 'Dashboard';
$currentPage = 'dashboard';
$db          = getDB();

// ── Statistik Utama ────────────────────────────────────────
$totalAll      = $db->query("SELECT COUNT(*) FROM laporan")->fetchColumn();
$totalSelesai  = $db->query("SELECT COUNT(*) FROM laporan WHERE status='selesai'")->fetchColumn();
$totalDiproses = $db->query("SELECT COUNT(*) FROM laporan WHERE status IN('diproses','dalam_perjalanan')")->fetchColumn();
$totalMenunggu = $db->query("SELECT COUNT(*) FROM laporan WHERE status='menunggu'")->fetchColumn();
$totalHariIni  = $db->query("SELECT COUNT(*) FROM laporan WHERE DATE(created_at)=CURDATE()")->fetchColumn();
$totalTeknisi  = $db->query("SELECT COUNT(*) FROM teknisi WHERE status='aktif'")->fetchColumn();

// ── Laporan Terbaru ────────────────────────────────────────
$laporanTerbaru = $db->query("
    SELECT l.*, t.nama AS nama_teknisi
    FROM laporan l
    LEFT JOIN teknisi t ON l.teknisi_id = t.id
    ORDER BY l.created_at DESC
    LIMIT 10
")->fetchAll();

// ── Data Grafik Bulanan ────────────────────────────────────
$grafikBulan = $db->query("
    SELECT DATE_FORMAT(created_at,'%Y-%m') AS bulan, COUNT(*) AS total
    FROM laporan
    WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 5 MONTH)
    GROUP BY DATE_FORMAT(created_at,'%Y-%m')
    ORDER BY bulan ASC
")->fetchAll();

$bulanIndo = ['01'=>'Jan','02'=>'Feb','03'=>'Mar','04'=>'Apr','05'=>'Mei','06'=>'Jun','07'=>'Jul','08'=>'Ags','09'=>'Sep','10'=>'Okt','11'=>'Nov','12'=>'Des'];
$barLabels = [];
$barValues = [];
foreach ($grafikBulan as $gb) {
    list($tahun, $bln) = explode('-', $gb['bulan']);
    $barLabels[] = ($bulanIndo[$bln] ?? $bln) . ' ' . $tahun;
    $barValues[] = (int)$gb['total'];
}

// ── Data Proporsi Status ───────────────────────────────────
$pieData = $db->query("SELECT status, COUNT(*) AS total FROM laporan GROUP BY status")->fetchAll();
$pieLabels = ['menunggu', 'diproses', 'dalam_perjalanan', 'selesai', 'dibatalkan'];
$pieValues = [0, 0, 0, 0, 0];
foreach ($pieData as $row) {
    $idx = array_search($row['status'], $pieLabels);
    if ($idx !== false) {
        $pieValues[$idx] = (int)$row['total'];
    }
}

// ── Data Peta ──────────────────────────────────────────────
$lokasiPeta = $db->query("SELECT id, kode_laporan, latitude, longitude, status FROM laporan WHERE latitude IS NOT NULL AND longitude IS NOT NULL ORDER BY id DESC LIMIT 50")->fetchAll();

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
  
  /* PJU Stat Grid Metrics */
  .metric-label { font-size: 0.78rem; font-weight: 700; color: #64748B; text-transform: uppercase; letter-spacing: 0.5px; }
  .metric-value { font-size: 1.75rem; font-weight: 800; color: #0F172A; line-height: 1.2; margin-top: 4px; }
</style>

<div class="admin-wrapper">
  <?php include dirname(__DIR__) . '/templates/admin_sidebar.php'; ?>

  <main class="main-content">
    <div class="topbar-clean d-flex justify-content-between align-items-center">
      <h4 class="fw-800 mb-0" style="color: #0F172A;">Ringkasan Sistem PJU</h4>
      <div class="small text-muted fw-500">Overview Operasional Jaringan</div>
    </div>

    <div class="content-padding">
      <div class="row g-4 mb-4">
        <div class="col-6 col-lg-3">
          <div class="card-clean-panel">
            <div class="metric-label">Total Laporan</div>
            <div class="metric-value"><?= number_format($totalAll) ?></div>
          </div>
        </div>
        <div class="col-6 col-lg-3">
          <div class="card-clean-panel">
            <div class="metric-label" style="color: #10B981;">Selesai</div>
            <div class="metric-value"><?= number_format($totalSelesai) ?></div>
          </div>
        </div>
        <div class="col-6 col-lg-3">
          <div class="card-clean-panel">
            <div class="metric-label" style="color: #3B82F6;">Diproses</div>
            <div class="metric-value"><?= number_format($totalDiproses) ?></div>
          </div>
        </div>
        <div class="col-6 col-lg-3">
          <div class="card-clean-panel">
            <div class="metric-label" style="color: #F59E0B;">Menunggu</div>
            <div class="metric-value"><?= number_format($totalMenunggu) ?></div>
          </div>
        </div>
      </div>

      <div class="row g-4 mb-4">
        <div class="col-lg-8">
          <div class="card-clean-panel">
            <div class="fw-bold text-dark mb-3">Tren Aduan Masuk</div>
            <div style="position: relative; height: 260px;">
              <canvas id="chart-tren-bulanan"></canvas>
            </div>
          </div>
        </div>
        <div class="col-lg-4">
          <div class="card-clean-panel d-flex flex-column align-items-center justify-content-center text-center">
            <div class="fw-bold text-dark align-self-start mb-3">Rasio Status</div>
            <div style="position: relative; width: 140px; height: 140px;">
              <canvas id="chart-rasio-status"></canvas>
            </div>
            <div class="w-100 d-flex justify-content-around mt-3 small text-muted">
              <div><span class="badge bg-warning p-1.5 rounded-circle me-1"></span>Wrg</div>
              <div><span class="badge bg-primary p-1.5 rounded-circle me-1"></span>Prs</div>
              <div><span class="badge bg-success p-1.5 rounded-circle me-1"></span>Slsi</div>
            </div>
          </div>
        </div>
      </div>

      <div class="card-clean-panel mb-4 p-2">
        <div id="map-admin-panel" style="height: 300px; border-radius: 10px;"></div>
      </div>

      <div class="card-clean-panel p-0 overflow-hidden">
        <div class="p-4 border-bottom bg-light">
          <div class="fw-bold text-dark">10 Berkas Laporan Terbaru</div>
        </div>
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0" style="font-size: 0.92rem;">
            <thead class="table-light">
              <tr>
                <th class="ps-4 py-3">Kode</th>
                <th>Nama Pelapor</th>
                <th>Alamat Lokasi</th>
                <th>Status Penanganan</th>
                <th class="pe-4">Teknisi Lapangan</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($laporanTerbaru)): ?>
              <tr><td colspan="5" class="text-center py-4 text-muted">Belum ada data antrean laporan.</td></tr>
              <?php endif; ?>
              <?php foreach ($laporanTerbaru as $row): ?>
              <tr>
                <td class="ps-4 fw-bold text-primary" style="font-family:'Space Mono', monospace;"><?= e($row['kode_laporan']) ?></td>
                <td class="fw-600 text-dark"><?= e($row['nama_pelapor']) ?></td>
                <td class="text-muted text-truncate" style="max-width: 250px;"><?= e($row['alamat_lokasi'] ?? 'Koordinat Terlampir') ?></td>
                <td><?= statusBadge($row['status']) ?></td>
                <td class="pe-4 fw-600 text-success"><?= e($row['nama_teknisi'] ?? 'Belum Ditugaskan') ?></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </main>
</div>

<?php
// JSON injector untuk Chart & Leaflet
$jsLabels     = json_encode($barLabels);
$jsValues     = json_encode($barValues);
$jsPieLabels  = json_encode(array_map('statusLabel', $pieLabels));
$jsPieValues  = json_encode($pieValues);
$jsLokasiPeta = json_encode($lokasiPeta);

$extraJs = "
<script>
document.addEventListener('DOMContentLoaded', () => {
  // 1. Grafik Batang Tren Bulanan
  const ctxBar = document.getElementById('chart-tren-bulanan')?.getContext('2d');
  if (ctxBar) {
    new Chart(ctxBar, {
      type: 'bar',
      data: {
        labels: $jsLabels,
        datasets: [{
          label: 'Jumlah Aduan',
          data: $jsValues,
          backgroundColor: '#6366F1',
          borderRadius: 5
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } }, x: { grid: { display: false } } }
      }
    });
  }

  // 2. Grafik Lingkaran Proporsi Status
  const ctxPie = document.getElementById('chart-rasio-status')?.getContext('2d');
  if (ctxPie) {
    new Chart(ctxPie, {
      type: 'doughnut',
      data: {
        labels: $jsPieLabels,
        datasets: [{
          data: $jsPieValues,
          backgroundColor: ['#F59E0B', '#3B82F6', '#8B5CF6', '#10B981', '#EF4444'],
          borderWidth: 0
        }]
      },
      options: { cutout: '74%', plugins: { legend: { display: false } } }
    });
  }

  // 3. Mini Peta Dashboard Admin
  const mapElement = document.getElementById('map-admin-panel');
  if (mapElement) {
    const map = L.map('map-admin-panel').setView([-6.9175, 107.6191], 12);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '&copy; OpenStreetMap'
    }).addTo(map);

    const titikKoor = $jsLokasiPeta;
    const dotColors = { menunggu:'#F59E0B', diproses:'#3B82F6', dalam_perjalanan:'#8B5CF6', selesai:'#10B981', dibatalkan:'#EF4444' };

    titikKoor.forEach(t => {
      const col = dotColors[t.status] || '#CBD5E1';
      const mapIcon = L.divIcon({
        html: `<div style=\"background:\${col}; width:12px; height:12px; border-radius:50%; border:2px solid #fff; box-shadow:0 1px 4px rgba(0,0,0,0.2);\"><\/div>`,
        className: '', iconSize: [12, 12]
      });
      L.marker([t.latitude, t.longitude], {icon: mapIcon}).addTo(map);
    });
  }
});
</script>
";
include dirname(__DIR__) . '/templates/footer.php';
?>