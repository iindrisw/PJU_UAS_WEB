<?php
/**
 * admin/peta.php
 * Peta Sebaran Penuh Keluhan Lampu Jalan — (Minimalist Light UI)
 */
require_once dirname(__DIR__) . '/config/database.php'; 
require_once dirname(__DIR__) . '/config/app.php'; 
requireAdminLogin(); 

$pageTitle   = 'Peta Laporan';
$currentPage = 'peta';
$db          = getDB(); 

// Ambil seluruh titik koordinat keluhan warga
$lokasiPeta = $db->query("SELECT id, kode_laporan, latitude, longitude, status, alamat_lokasi, nama_pelapor FROM laporan WHERE latitude IS NOT NULL AND longitude IS NOT NULL")->fetchAll();

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
      <h4 class="fw-800 mb-0" style="color: #0F172A;">Peta Pemetaan Jaringan PJU</h4>
      <div class="small text-muted fw-500">Memonitor <strong><?= count($lokasiPeta) ?></strong> Titik Koordinat</div>
    </div>

    <div class="content-padding">
      <div class="card-clean-panel p-2">
        <div id="map-peta-penuh" style="height: calc(100vh - 210px); border-radius: 10px; border: 1px solid #E2E8F0;"></div>
      </div>
    </div>
  </main>
</div>

<?php
$jsLokasi = json_encode($lokasiPeta);
$extraJs = "
<script>
document.addEventListener('DOMContentLoaded', () => {
  const mapElement = document.getElementById('map-peta-penuh');
  if (mapElement) {
    const map = L.map('map-peta-penuh').setView([-6.9175, 107.6191], 13);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '&copy; OpenStreetMap'
    }).addTo(map);

    const dataTitik = $jsLokasi;
    const dotColors = { menunggu:'#F59E0B', diproses:'#3B82F6', dalam_perjalanan:'#8B5CF6', selesai:'#10B981', dibatalkan:'#EF4444' };

    dataTitik.forEach(d => {
      const col = dotColors[d.status] || '#CBD5E1';
      const mapIcon = L.divIcon({
        html: `<div style=\"background:\${col}; width:16px; height:16px; border-radius:50%; border:2px solid #fff; box-shadow:0 2px 6px rgba(0,0,0,0.15);\"><\/div>`,
        className: '', iconSize: [16, 16]
      });

      L.marker([d.latitude, d.longitude], {icon: mapIcon})
       .addTo(map)
       .bindPopup(`
          <div style=\"font-family:'Plus Jakarta Sans',sans-serif; font-size:0.85rem;\">
            <strong style=\"color:#4F46E5;\">\${d.kode_laporan}<\/strong><br>
            <small class=\"text-muted d-block my-1\">Pelapor: \${d.nama_pelapor}<\/small>
            <span class=\"badge bg-light text-dark border px-2 py-0.5\" style=\"font-size:0.7rem;\">\${d.status.toUpperCase()}</span>
          </div>
       `);
    });
  }
});
</script>
";
include dirname(__DIR__) . '/templates/footer.php';
?>