<?php
/**
 * index.php — Halaman Utama Publik (Minimalist Clean UI & High Contrast)
 * Sistem Pelaporan Lampu Jalan Mati (PJU)
 */
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/app.php';

$pageTitle = 'Beranda';
$db        = getDB();

// Ambil 5 aktivitas pengaduan masyarakat paling baru untuk tabel bawah
$latestLaporan = $db->query("
    SELECT l.*, t.nama AS nama_teknisi
    FROM laporan l
    LEFT JOIN teknisi t ON l.teknisi_id = t.id
    ORDER BY l.created_at DESC
    LIMIT 5
")->fetchAll();

include __DIR__ . '/templates/header.php';
?>
<meta name="base-url" content="<?= BASE_URL ?>">

<style>
  body {
    background-color: #FAFAFB;
    color: #1E293B;
    font-family: 'Plus Jakarta Sans', sans-serif;
  }
  /* Navbar styling custom agar teks tajam */
  .navbar-clean {
    background: #FFFFFF;
    border-bottom: 1px solid #E2E8F0;
    padding: 16px 0;
  }
  .nav-link-clean {
    color: #475569 !important;
    font-weight: 600;
    font-size: 0.95rem;
    transition: all 0.2s;
  }
  .nav-link-clean:hover, .nav-link-clean.active {
    color: #6366F1 !important;
  }
  /* Teks Hero Section - Anti Kelelep */
  .hero-title-clean {
    font-size: 3rem;
    font-weight: 800;
    color: #0F172A; /* Hitam pekat agar sangat mudah dibaca */
    letter-spacing: -0.03em;
    line-height: 1.2;
  }
  .hero-subtitle-clean {
    font-size: 1.1rem;
    color: #334155; /* Abu-abu gelap kontras tinggi */
    font-weight: 500;
    line-height: 1.7;
  }
  /* Card Alur Minimalis */
  .card-alur {
    background: #FFFFFF;
    border: 1px solid #E2E8F0;
    border-radius: 16px;
    padding: 28px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.02);
    transition: transform 0.2s;
  }
  .card-alur:hover {
    transform: translateY(-4px);
  }
  /* Tabel custom light clean */
  .table-card {
    background: #FFFFFF;
    border: 1px solid #E2E8F0;
    border-radius: 16px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.02);
  }
</style>

<nav class="navbar navbar-expand-lg navbar-clean">
  <div class="container">
    <a class="navbar-brand d-flex align-items-center gap-2" href="<?= BASE_URL ?>/">
      <div class="text-primary fs-4" style="color: #6366F1 !important;"><i class="bi bi-lightbulb-fill"></i></div>
      <span style="font-weight: 800; color: #0F172A; letter-spacing: -0.02em; font-size: 1.15rem;">Sistem PJU</span>
    </a>
    <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navMain">
      <i class="bi bi-list fs-3"></i>
    </button>
    <div class="collapse navbar-collapse" id="navMain">
      <ul class="navbar-nav ms-auto align-items-lg-center gap-4">
        <li class="nav-item"><a class="nav-link nav-link-clean active" href="<?= BASE_URL ?>/">Beranda</a></li>
        <li class="nav-item"><a class="nav-link nav-link-clean" href="<?= BASE_URL ?>/user/laporan.php">Buat Laporan</a></li>
        <li class="nav-item"><a class="nav-link nav-link-clean" href="<?= BASE_URL ?>/user/tracking.php">Cek Status</a></li>
        <li class="nav-item ms-lg-2">
          <a class="btn px-4 py-2 fw-bold text-white" href="<?= BASE_URL ?>/admin/login.php"
             style="background: #6366F1; border-radius: 10px; font-size: 0.9rem; box-shadow: 0 4px 14px rgba(99,102,241,0.25);">
            <i class="bi bi-box-arrow-in-right me-1"></i> Login
          </a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<section class="py-5 my-3">
  <div class="container">
    <div class="row align-items-center g-5">
      
      <div class="col-lg-7 text-lg-start text-center">
        <div class="d-inline-flex align-items-center gap-2 mb-3"
             style="background:#EEF2FF; border: 1px solid #C7D2FE; border-radius:50px; padding:6px 16px;">
          <span class="badge rounded-pill text-white p-1.5 px-2.5" style="font-size:0.68rem; font-weight:800; background: #6366F1;">MONITORING LIVE</span>
          <span class="fw-700" style="font-size:0.82rem; color: #4F46E5;">Layanan Infrastruktur Jaringan Lampu</span>
        </div>
        
        <h1 class="hero-title-clean mb-4">
          Laporkan Lampu Jalan Padam Presisi Lewat GPS.
        </h1>
        
        <p class="hero-subtitle-clean mb-4" style="max-width: 620px;">
          Infrastruktur terang, warga aman. Sampaikan keluhan kerusakan Penerangan Jalan Umum (PJU) secara instan. Sistem otomatis mendeteksi koordinat tiang lampu dan memonitor status penugasan teknisi secara terbuka.
        </p>
        
        <div class="d-flex flex-wrap justify-content-lg-start justify-content-center gap-3">
          <a href="<?= BASE_URL ?>/user/laporan.php" class="btn btn-lg fw-700 text-white px-4 py-2.5" style="background: #6366F1; border-radius: 10px; font-size: 0.95rem;">
            <i class="bi bi-plus-circle-fill me-2"></i>Buat Laporan Sekarang
          </a>
          <a href="<?= BASE_URL ?>/user/tracking.php" class="btn btn-lg btn-light border fw-700 px-4 py-2.5" style="border-radius: 10px; font-size: 0.95rem; color: #475569;">
            <i class="bi bi-geo-alt-fill me-2"></i>Lacak Lokasi Perbaikan
          </a>
        </div>
      </div>
      
      <div class="col-lg-5 text-center d-none d-lg-block">
        <div class="p-4 rounded-4" style="background: #EEF2FF; border: 1px dashed #C7D2FE;">
          <i class="bi bi-lightbulb-fill text-primary" style="font-size: 6rem; color: #6366F1 !important; filter: drop-shadow(0 10px 20px rgba(99,102,241,0.2));"></i>
        </div>
      </div>

    </div>
  </div>
</section>

<section class="py-5" style="background: #FFFFFF; border-top: 1px solid #E2E8F0; border-bottom: 1px solid #E2E8F0;">
  <div class="container py-2">
    <div class="text-center mb-5">
      <h2 class="fw-800 text-dark" style="letter-spacing: -0.03em;">Alur Penanganan Laporan</h2>
      <p class="fw-500" style="color: #64748B;">Transparansi penuh mulai dari aduan masuk hingga peninjauan tim lapangan</p>
    </div>
    
    <div class="row g-4">
      <div class="col-md-4">
        <div class="card-alur h-100">
          <div class="mb-3 fs-3" style="color: #6366F1;"><i class="bi bi-geo-alt-fill"></i></div>
          <h5 class="fw-800 text-dark mb-2">1. Kunci Koordinat GPS</h5>
          <p class="small mb-0 text-secondary" style="line-height: 1.6; font-weight: 500;">Tekan tombol lokasi otomatis pada form. API Geolocation akan mengunci titik latitude dan longitude lampu secara akurat.</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card-alur h-100">
          <div class="mb-3 fs-3" style="color: #F59E0B;"><i class="bi bi-camera-fill"></i></div>
          <h5 class="fw-800 text-dark mb-2">2. Unggah Foto Lampu</h5>
          <p class="small mb-0 text-secondary" style="line-height: 1.6; font-weight: 500;">Lampirkan bukti foto kondisi lampu jalan yang mati untuk validasi fisik sebelum tim teknisi berangkat ke lokasi.</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card-alur h-100">
          <div class="mb-3 fs-3" style="color: #10B981;"><i class="bi bi-arrow-repeat"></i></div>
          <h5 class="fw-800 text-dark mb-2">3. Pantau Kerja Teknisi</h5>
          <p class="small mb-0 text-secondary" style="line-height: 1.6; font-weight: 500;">Gunakan kode unik untuk melihat pergerakan teknisi dari status penugasan hingga lampu menyala kembali.</p>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="py-5">
  <div class="container py-2">
    <div class="d-flex flex-sm-row flex-column justify-content-between align-items-start align-items-sm-center mb-4 gap-2">
      <div>
        <h4 class="fw-800 text-dark mb-0" style="letter-spacing: -0.02em;">Aktivitas Pengaduan Terkini</h4>
        <p class="text-muted small mb-0">Daftar berkas laporan masuk dari masyarakat sekitar</p>
      </div>
      <a href="<?= BASE_URL ?>/user/tracking.php" class="btn btn-sm btn-light border px-3 py-2 fw-700 text-secondary" style="border-radius:8px; font-size:0.8rem;">
        Lihat Semua Log <i class="bi bi-arrow-right ms-1"></i>
      </a>
    </div>

    <div class="table-card overflow-hidden">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" style="font-size: 0.92rem;">
          <thead class="table-light">
            <tr class="text-secondary" style="font-weight: 600;">
              <th class="ps-4 py-3">Kode Tiket</th>
              <th>Nama Warga</th>
              <th>Alamat / Titik Lokasi</th>
              <th>Status Kerja</th>
              <th class="pe-4">Waktu Masuk</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($latestLaporan as $row): ?>
            <tr>
              <td class="ps-4">
                <a href="<?= BASE_URL ?>/user/tracking.php?kode=<?= e($row['kode_laporan']) ?>"
                   class="fw-bold text-decoration-none" style="font-family:'Space Mono', monospace; color: #6366F1;">
                  <?= e($row['kode_laporan']) ?>
                </a>
              </td>
              <td class="fw-700 text-dark"><?= e($row['nama_pelapor']) ?></td>
              <td class="text-muted text-truncate" style="max-width:260px; font-weight: 500;">
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
            <?php if (empty($latestLaporan)): ?>
            <tr><td colspan="5" class="text-center text-muted py-5 fw-600">Belum ada aduan lampu masuk saat ini.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</section>

<footer class="py-4 text-center border-top" style="background: #FFFFFF;">
  <div class="container">
    <p class="mb-1 text-muted small fw-600">© <?= date('Y') ?> <strong>Sistem Pelaporan PJU</strong> — Pemkot Bandung</p>
    <p class="mb-0 text-muted opacity-50" style="font-size:0.75rem;">
      Infrastruktur Terbuka - Layanan Sipil Digital Terintegrasi
    </p>
  </div>
</footer>

<?php include __DIR__ . '/templates/footer.php'; ?>