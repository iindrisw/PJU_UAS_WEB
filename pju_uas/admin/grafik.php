<?php
/**
 * admin/grafik.php
 * Analisis Grafik & Laporan Statistik Penanganan — (Minimalist Light UI)
 */
require_once dirname(__DIR__) . '/config/database.php'; 
require_once dirname(__DIR__) . '/config/app.php'; 
requireAdminLogin(); 

$pageTitle   = 'Grafik & Laporan';
$currentPage = 'grafik';
$db          = getDB(); 

// Data Grafik Batang Bulanan
$grafikBulan = $db->query("
    SELECT DATE_FORMAT(created_at,'%Y-%m') AS bulan, COUNT(*) AS total, SUM(status='selesai') AS selesai
    FROM laporan
    WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 11 MONTH)
    GROUP BY DATE_FORMAT(created_at,'%Y-%m')
    ORDER BY bulan ASC
")->fetchAll();

$bulanIndo = ['01'=>'Jan','02'=>'Feb','03'=>'Mar','04'=>'Apr','05'=>'Mei','06'=>'Jun','07'=>'Jul','08'=>'Ags','09'=>'Sep','10'=>'Okt','11'=>'Nov','12'=>'Des'];
$formattedLabels = [];
foreach ($grafikBulan as $gb) {
    list($tahun, $bln) = explode('-', $gb['bulan']);
    $formattedLabels[] = ($bulanIndo[$bln] ?? $bln) . ' ' . $tahun;
}

// Data Donut Status
$pieData = $db->query("SELECT status, COUNT(*) AS total FROM laporan GROUP BY status")->fetchAll();
$pieLabels = ['menunggu', 'diproses', 'dalam_perjalanan', 'selesai', 'dibatalkan'];
$pieValues = [0, 0, 0, 0, 0];
foreach ($pieData as $row) {
    $idx = array_search($row['status'], $pieLabels);
    if ($idx !== false) {
        $pieValues[$idx] = (int)$row['total'];
    }
}

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
</style>

<div class="admin-wrapper">
  <?php include dirname(__DIR__) . '/templates/admin_sidebar.php'; ?>

  <main class="main-content">
    <div class="topbar-clean d-flex justify-content-between align-items-center">
      <h4 class="fw-800 mb-0" style="color: #0F172A;">Analisis Performa Jaringan</h4>
      <div class="small text-muted fw-500">Metrik Data Real-Time Keluhan Warga</div>
    </div>

    <div class="content-padding">
      <div class="row g-4">
        <div class="col-lg-8">
          <div class="card-clean-panel">
            <div class="fw-bold text-dark mb-4">Grafik Laporan Masuk vs Selesai</div>
            <div style="position: relative; height: 320px;">
              <canvas id="chartFullTren"></canvas>
            </div>
          </div>
        </div>
        <div class="col-lg-4">
          <div class="card-clean-panel text-center">
            <div class="fw-bold text-dark text-start mb-4">Rasio Penyelesaian Sesi</div>
            <div class="mx-auto" style="max-width: 190px;">
              <canvas id="chartFullDonut"></canvas>
            </div>
            <div id="grafik-legend-list" class="d-flex flex-column gap-2 small mt-4 pt-3 border-top"></div>
          </div>
        </div>
      </div>
    </div>
  </main>
</div>

<?php
$jsLabels     = json_encode($formattedLabels);
$jsTotalBar   = json_encode(array_map('intval', array_column($grafikBulan, 'total')));
$jsSelesaiBar = json_encode(array_map('intval', array_column($grafikBulan, 'selesai')));
$dLabels      = json_encode(array_map('statusLabel', $pieLabels));
$dValues      = json_encode($pieValues);
$dColors      = json_encode(['#F59E0B', '#3B82F6', '#8B5CF6', '#10B981', '#EF4444']);

$extraJs = "
<script>
document.addEventListener('DOMContentLoaded', () => {
  const ctxBar = document.getElementById('chartFullTren').getContext('2d');
  new Chart(ctxBar, {
    type: 'bar',
    data: {
      labels: $jsLabels,
      datasets: [
        { label: 'Aduan Masuk', data: $jsTotalBar, backgroundColor: '#6366F1', borderRadius: 4 },
        { label: 'Selesai Perbaikan', data: $jsSelesaiBar, backgroundColor: '#10B981', borderRadius: 4 }
      ]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } }, x: { grid: { display: false } } }
    }
  });

  const ctxDonut = document.getElementById('chartFullDonut').getContext('2d');
  const labelsArr = $dLabels;
  const valuesArr = $dValues;
  const colorsArr = $dColors;

  new Chart(ctxDonut, {
    type: 'doughnut',
    data: { labels: labelsArr, datasets: [{ data: valuesArr, backgroundColor: colorsArr, borderWidth: 0 }] },
    options: { cutout: '78%', plugins: { legend: { display: false } } }
  });

  const legContainer = document.getElementById('grafik-legend-list');
  if (legContainer) {
    let html = '';
    labelsArr.forEach((label, i) => {
      html += `
        <div class=\"d-flex justify-content-between align-items-center\">
          <span class=\"text-muted d-flex align-items-center\"><span style=\"display:inline-block;width:10px;height:10px;background:\${colorsArr[i]};border-radius:50%;margin-right:8px;\"></span>\${label}</span>
          <span class=\"fw-bold text-dark\">\${valuesArr[i]} Unit</span>
        </div>`;
    });
    legContainer.innerHTML = html;
  }
});
</script>
";
include dirname(__DIR__) . '/templates/footer.php';
?>