<?php
/**
 * user/laporan.php
 * Form pelaporan aduan lampu jalan mati versi High Contrast & GPS Aktif 100%
 */
require_once dirname(__DIR__) . '/config/database.php';
require_once dirname(__DIR__) . '/config/app.php';

$pageTitle = 'Buat Laporan';
$errors    = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrf($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Token keamanan tidak valid. Silakan refresh halaman.';
    } else {
        $nama_pelapor   = trim($_POST['nama_pelapor']   ?? '');
        $no_hp          = trim($_POST['no_hp']          ?? '');
        $deskripsi      = trim($_POST['deskripsi']      ?? '');
        $latitude       = trim($_POST['latitude']       ?? '');
        $longitude      = trim($_POST['longitude']      ?? '');
        $alamat_lokasi  = trim($_POST['alamat_lokasi']  ?? '');

        if (empty($nama_pelapor)) $errors[] = 'Nama pelapor wajib diisi.';
        if (empty($no_hp))        $errors[] = 'Nomor HP wajib diisi.';
        if (empty($deskripsi) || strlen($deskripsi) < 10) $errors[] = 'Deskripsi minimal 10 karakter.';
        if (empty($latitude) || empty($longitude))  $errors[] = 'Lokasi GPS wajib dideteksi otomatis.';

        $namaFoto = null;
        if (!empty($_FILES['foto']['name'])) {
            $namaFoto = uploadFoto($_FILES['foto']);
            if ($namaFoto === false) {
                $errors[] = 'Upload foto gagal, pastikan format sesuai ketentuan.';
            }
        }

        if (empty($errors)) {
            $db           = getDB();
            $kodeLaporan  = generateKodeLaporan();

            $stmt = $db->prepare("INSERT INTO laporan (kode_laporan, nama_pelapor, no_hp, deskripsi, foto, latitude, longitude, alamat_lokasi, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'menunggu')");
            $stmt->execute([$kodeLaporan, $nama_pelapor, $no_hp, $deskripsi, $namaFoto, $latitude, $longitude, $alamat_lokasi]);
            
            $laporanId = $db->lastInsertId();
            $db->prepare("INSERT INTO tracking_status (laporan_id, status, keterangan, diubah_oleh) VALUES (?, 'menunggu', 'Laporan berhasil terdaftar ke database', 'Masyarakat')")->execute([$laporanId]);

            redirect(BASE_URL . '/user/tracking.php?kode=' . $kodeLaporan);
        }
    }
}

include dirname(__DIR__) . '/templates/header.php';
?>
<meta name="base-url" content="<?= BASE_URL ?>">

<style>
  body { background-color: #FAFAFB; color: #0F172A; font-family: 'Plus Jakarta Sans', sans-serif; }
  .navbar-clean { background: #FFFFFF; border-bottom: 1px solid #E2E8F0; padding: 16px 0; }
  .nav-link-clean { color: #475569 !important; font-weight: 600; font-size: 0.95rem; }
  .card-clean-input { background: #FFFFFF; border: 1px solid #CBD5E1; border-radius: 16px; box-shadow: 0 2px 5px rgba(0,0,0,0.01); }
  
  /* FORCE HIGH CONTRAST TEXT - ANTI KELELEP */
  .clean-label { color: #0F172A !important; font-weight: 700; font-size: 0.92rem; }
  .clean-header { color: #0F172A !important; font-weight: 800; font-size: 1.1rem; }
  .form-control { background: #FFFFFF !important; border: 1.5px solid #94A3B8 !important; color: #0F172A !important; font-weight: 600; }
  .form-control::placeholder { color: #64748B !important; font-weight: 500; }
  .form-control:focus { border-color: #6366F1 !important; box-shadow: 0 0 0 4px rgba(99,102,241,0.15) !important; }
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
        <li><a class="nav-link nav-link-clean active" href="#" style="color: #6366F1 !important;">Buat Laporan</a></li>
        <li><a class="nav-link nav-link-clean" href="<?= BASE_URL ?>/user/tracking.php">Cek Status</a></li>
      </ul>
    </div>
  </div>
</nav>

<div class="container py-5" style="max-width: 800px;">
  
  <div class="text-center mb-5">
    <h2 style="font-weight: 800; color: #0F172A; letter-spacing: -0.03em;">Formulir Pengaduan Lampu Jalan</h2>
    <p style="color: #334155; font-weight: 600;">Isi data dengan benar agar penugasan kru teknisi akurat di lapangan</p>
  </div>

  <?php if (!empty($errors)): ?>
  <div class="alert border-0 mb-4" style="background: #FEF2F2; border-left: 4px solid #EF4444 !important; color: #991B1B; border-radius: 12px; font-weight: 600;">
    <ul class="mb-0 ps-3">
      <?php foreach ($errors as $err): ?> <li><?= e($err) ?></li> <?php endforeach; ?>
    </ul>
  </div>
  <?php endif; ?>

  <form id="form-laporan" method="POST" enctype="multipart/form-data" novalidate>
    <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
    
    <input type="hidden" id="latitude"  name="latitude"  value="<?= e($_POST['latitude'] ?? '') ?>">
    <input type="hidden" id="longitude" name="longitude" value="<?= e($_POST['longitude'] ?? '') ?>">

    <div class="card-clean-input p-4 mb-4">
      <div class="clean-header mb-4 border-bottom pb-2"><i class="bi bi-person-badge-fill text-primary me-2"></i>Data Diri Kontak Pelapor</div>
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label clean-label">Nama Lengkap Sesuai KTP <span class="text-danger">*</span></label>
          <input type="text" class="form-control p-2.5" name="nama_pelapor" value="<?= e($_POST['nama_pelapor'] ?? '') ?>" placeholder="Ketik nama lengkap Anda" required>
        </div>
        <div class="col-md-6">
          <label class="form-label clean-label">Nomor HP Aktif (WhatsApp) <span class="text-danger">*</span></label>
          <input type="tel" class="form-control p-2.5" name="no_hp" value="<?= e($_POST['no_hp'] ?? '') ?>" placeholder="Contoh: 08123456789" required>
        </div>
      </div>
    </div>

    <div class="card-clean-input p-4 mb-4">
      <div class="clean-header mb-4 border-bottom pb-2"><i class="bi bi-exclamation-triangle-fill text-warning me-2"></i>Rincian Kondisi Lapangan</div>
      <div class="mb-4">
        <label class="form-label clean-label">Deskripsi Kerusakan Lampu PJU <span class="text-danger">*</span></label>
        <textarea class="form-control p-3" name="deskripsi" rows="3" placeholder="Ceritakan detail kerusakan atau patokan jalan sekitar agar mudah dicari teknisi..." required><?= e($_POST['deskripsi'] ?? '') ?></textarea>
      </div>
      <div class="mb-2">
        <label class="form-label clean-label">Unggah Bukti Foto Kondisi Lampu</label>
        <div class="p-4 text-center rounded-3 cursor-pointer" id="upload-zone" style="background: #F8FAFC; border: 2px dashed #94A3B8; transition: all 0.2s ease;">
          <i class="bi bi-cloud-arrow-up-fill fs-1" style="color: #6366F1;"></i>
          <p class="mb-1 text-dark fw-bold mt-2" id="upload-text">Klik atau Seret Foto ke Sini</p>
          <small class="text-secondary fw-500">Format: JPG, PNG, WebP · Ukuran Maksimal 5MB</small>
          <input type="file" id="foto" name="foto" accept="image/*" class="d-none">
        </div>
        <div class="mt-3 text-center" id="preview-container" style="display: none;">
          <img id="foto-preview" src="" class="img-fluid rounded border border-2" style="max-height: 180px; object-fit: cover;">
        </div>
      </div>
    </div>

    <div class="card-clean-input p-4 mb-4">
      <div class="clean-header mb-4 border-bottom pb-2"><i class="bi bi-geo-alt-fill text-danger me-2"></i>Sistem Penguncian Koordinat GPS</div>
      
      <button type="button" id="btn-gps" class="btn text-white w-100 py-3 mb-4 fw-bold shadow-sm" style="background: #0F172A; border-radius: 10px; transition: all 0.2s;">
        <i class="bi bi-geo-fill me-2 text-warning" id="gps-icon"></i><span id="gps-text">Kunci Lokasi Otomatis Sekarang</span>
      </button>
      
      <div class="row g-3 mb-3">
        <div class="col-6">
          <label class="form-label small text-dark fw-bold">Garis Lintang (Latitude)</label>
          <input type="text" class="form-control bg-light text-dark fw-bold" id="lat-display" placeholder="Belum terkunci" readonly>
        </div>
        <div class="col-6">
          <label class="form-label small text-dark fw-bold">Garis Bujur (Longitude)</label>
          <input type="text" class="form-control bg-light text-dark fw-bold" id="lng-display" placeholder="Belum terkunci" readonly>
        </div>
      </div>
      
      <div class="mb-3">
        <label class="form-label small text-dark fw-bold">Estimasi Alamat Terlacak</label>
        <textarea class="form-control bg-light text-dark fw-semibold" id="alamat_lokasi" name="alamat_lokasi" rows="2" readonly placeholder="Alamat hasil tracking API Geolocation..."><?= e($_POST['alamat_lokasi'] ?? '') ?></textarea>
      </div>
      
      <div id="map-preview" class="rounded border mt-3" style="height: 250px; display: none;"></div>
    </div>

    <div class="text-end mb-5">
      <button type="submit" class="btn text-white px-5 py-3 fw-bold" style="background: #6366F1; border-radius: 12px; font-size: 1rem; box-shadow: 0 4px 14px rgba(99,102,241,0.25);">
        <i class="bi bi-send-check-fill me-2"></i>Kirim Pengaduan Lampu Jalan
      </button>
    </div>

  </form>
</div>

<?php
$extraJs = "
<script>
document.addEventListener('DOMContentLoaded', () => {
  // ── A. LOGIKA DRAG & DROP FOTO ──
  const zone       = document.getElementById('upload-zone');
  const fileInput  = document.getElementById('foto');
  const previewImg = document.getElementById('foto-preview');
  const container  = document.getElementById('preview-container');
  const uploadText = document.getElementById('upload-text');

  if (zone && fileInput) {
    zone.addEventListener('click', (e) => { if (e.target !== fileInput) fileInput.click(); });
    ['dragenter', 'dragover'].forEach(eventName => {
      zone.addEventListener(eventName, (e) => {
        e.preventDefault(); e.stopPropagation();
        zone.style.borderColor = '#6366F1'; zone.style.background = '#EEF2FF';
      }, false);
    });
    ['dragleave', 'drop'].forEach(eventName => {
      zone.addEventListener(eventName, (e) => {
        e.preventDefault(); e.stopPropagation();
        zone.style.borderColor = '#94A3B8'; zone.style.background = '#F8FAFC';
      }, false);
    });
    zone.addEventListener('drop', (e) => {
      const files = e.dataTransfer.files;
      if (files.length > 0) { fileInput.files = files; handleFilePreview(files[0]); }
    }, false);
    fileInput.addEventListener('change', function() { if (this.files.length > 0) handleFilePreview(this.files[0]); });
  }

  function handleFilePreview(file) {
    if (!file.type.startsWith('image/')) { alert('Format file harus gambar!'); fileInput.value = ''; return; }
    if (file.size > 5 * 1024 * 1024) { alert('Maksimal ukuran foto 5MB!'); fileInput.value = ''; return; }
    const reader = new FileReader();
    reader.onload = (e) => {
      previewImg.src = e.target.result; container.style.display = 'block';
      if (uploadText) uploadText.innerText = 'Berkas Terkunci: ' + file.name;
    };
    reader.readAsDataURL(file);
  }

  // ── B. LOGIKA TOMBOL GPS AKTIF & REVERSE GEOCODING ──
  const btnGps    = document.getElementById('btn-gps');
  const gpsText   = document.getElementById('gps-text');
  const gpsIcon   = document.getElementById('gps-icon');
  const latHidden = document.getElementById('latitude');
  const lngHidden = document.getElementById('longitude');
  const latDisp   = document.getElementById('lat-display');
  const lngDisp   = document.getElementById('lng-display');
  const txtAlamat = document.getElementById('alamat_lokasi');
  const mapDiv    = document.getElementById('map-preview');
  let leafletMap  = null;
  let currentMarker = null;

  if (btnGps) {
    btnGps.addEventListener('click', () => {
      if (!navigator.geolocation) {
        alert('Browser Anda tidak mendukung deteksi lokasi GPS.');
        return;
      }

      gpsText.innerText = 'Sedang Mengunci Sinyal Satelit GPS...';
      gpsIcon.className = 'bi bi-arrow-repeat';

      navigator.geolocation.getCurrentPosition(
        (position) => {
          const lat = position.coords.latitude;
          const lng = position.coords.longitude;

          latHidden.value = lat;
          lngHidden.value = lng;
          latDisp.value   = lat.toFixed(6);
          lngDisp.value   = lng.toFixed(6);

          gpsText.innerText = 'Lokasi Berhasil Dikunci!';
          gpsIcon.className = 'bi bi-check-circle-fill text-success';
          btnGps.style.background = '#10B981';

          mapDiv.style.display = 'block';
          if (!leafletMap) {
            leafletMap = L.map('map-preview').setView([lat, lng], 16);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
              attribution: '&copy; OpenStreetMap'
            }).addTo(leafletMap);
            currentMarker = L.marker([lat, lng]).addTo(leafletMap);
          } else {
            leafletMap.setView([lat, lng], 16);
            currentMarker.setLatLng([lat, lng]);
          }

          txtAlamat.value = 'Mendeteksi titik koordinat nama wilayah jalan...';
          
          fetch(`https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=\${lat}&lon=\${lng}`)
            .then(res => res.json())
            .then(data => {
              if (data && data.display_name) {
                txtAlamat.value = data.display_name;
              } else {
                txtAlamat.value = 'Jalan Terdeteksi sekitar koordinat: ' + lat + ', ' + lng;
              }
            })
            .catch(() => {
              txtAlamat.value = 'Gagal mengambil nama jalan, koordinat tetap aman terkunci: ' + lat + ', ' + lng;
            });
        },
        (error) => {
          gpsText.innerText = 'Kunci Lokasi Otomatis Sekarang';
          gpsIcon.className = 'bi bi-geo-fill text-warning';
          alert('Gagal mengambil lokasi GPS. Pastikan izin lokasi browser Anda aktif.');
        },
        { enableHighAccuracy: true, timeout: 10000 }
      );
    });
  }
});
</script>
";
include dirname(__DIR__) . '/templates/footer.php';
?>