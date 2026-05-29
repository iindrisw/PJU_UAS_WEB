<?php
/**
 * templates/admin_sidebar.php
 * Sidebar Navigasi Admin — Minimalist Light Clean UI (Fixed Alignment)
 * Sistem PJU Kota Bandung
 */
$currentPage = $currentPage ?? '';
?>

<div class="sidebar d-flex flex-column justify-content-between" id="adminSidebar">
  
  <div>
    <div class="sidebar-brand p-4 d-flex align-items-center gap-3">
      <div class="d-flex align-items-center justify-content-center rounded-3" 
           style="width: 36px; height: 36px; background-color: #EEF2FF; color: #6366F1 !important;">
        <i class="bi bi-lightbulb-fill fs-5"></i>
      </div>
      <div>
        <h6 class="fw-800 mb-0 text-dark" style="letter-spacing: -0.02em; font-size: 0.95rem;">PJU System</h6>
        <small class="text-muted fw-600" style="font-size: 0.75rem;">Admin Panel</small>
      </div>
    </div>

    <div class="p-3">
      
      <div class="nav-label px-3 pt-2 pb-1" style="font-size: 0.72rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #94A3B8;">Menu Utama</div>
      <nav class="d-flex flex-column gap-1">
        <a href="<?= BASE_URL ?>/admin/dashboard.php" class="nav-item d-flex align-items-center gap-3 px-3 py-2.5 rounded-3 text-decoration-none <?= $currentPage === 'dashboard' ? 'active' : '' ?>">
          <i class="bi bi-grid-1x2-fill"></i>
          <span>Dashboard</span>
        </a>

        <a href="<?= BASE_URL ?>/admin/laporan.php" class="nav-item d-flex align-items-center justify-content-between px-3 py-2.5 rounded-3 text-decoration-none <?= $currentPage === 'laporan' ? 'active' : '' ?>">
          <div class="d-flex align-items-center gap-3">
            <i class="bi bi-file-earmark-text-fill"></i>
            <span>Manajemen Laporan</span>
          </div>
          <?php
          $db = getDB();
          $s  = $db->query("SELECT COUNT(*) FROM laporan WHERE status='menunggu'")->fetchColumn();
          if ($s > 0): ?>
            <span class="badge rounded-pill bg-warning text-dark fw-700 small" style="font-size: 0.7rem; padding: 4px 8px;"><?= $s ?></span>
          <?php endif; ?>
        </a>

        <a href="<?= BASE_URL ?>/admin/teknisi.php" class="nav-item d-flex align-items-center gap-3 px-3 py-2.5 rounded-3 text-decoration-none <?= $currentPage === 'teknisi' ? 'active' : '' ?>">
          <i class="bi bi-people-fill"></i>
          <span>Manajemen Teknisi</span>
        </a>

        <a href="<?= BASE_URL ?>/admin/peta.php" class="nav-item d-flex align-items-center gap-3 px-3 py-2.5 rounded-3 text-decoration-none <?= $currentPage === 'peta' ? 'active' : '' ?>">
          <i class="bi bi-map-fill"></i>
          <span>Peta Laporan</span>
        </a>
      </nav>

      <div class="nav-label px-3 pt-3 pb-1" style="font-size: 0.72rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #94A3B8;">Statistik</div>
      <nav class="d-flex flex-column gap-1">
        <a href="<?= BASE_URL ?>/admin/grafik.php" class="nav-item d-flex align-items-center gap-3 px-3 py-2.5 rounded-3 text-decoration-none <?= $currentPage === 'grafik' ? 'active' : '' ?>">
          <i class="bi bi-bar-chart-fill"></i>
          <span>Grafik & Laporan</span>
        </a>
      </nav>

      <div class="nav-label px-3 pt-3 pb-1" style="font-size: 0.72rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #94A3B8;">Akun</div>
      <nav class="d-flex flex-column gap-1">
        <a href="<?= BASE_URL ?>/admin/profil.php" class="nav-item d-flex align-items-center gap-3 px-3 py-2.5 rounded-3 text-decoration-none <?= $currentPage === 'profil' ? 'active' : '' ?>">
          <i class="bi bi-person-fill"></i>
          <span>Profil Admin</span>
        </a>

        <a href="<?= BASE_URL ?>/admin/logout.php" class="nav-item d-flex align-items-center gap-3 px-3 py-2.5 rounded-3 text-decoration-none text-danger">
          <i class="bi bi-box-arrow-left"></i>
          <span>Keluar</span>
        </a>
      </nav>

    </div>
  </div>

  <div class="p-3 border-top" style="border-color: #F1F5F9 !important;">
    <div class="d-flex align-items-center gap-3 px-2">
      <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold" 
           style="width: 38px; height: 38px; background: linear-gradient(135deg, #6366F1, #4F46E5); font-size: 0.9rem; box-shadow: 0 4px 10px rgba(99,102,241,0.25);">
        <?= strtoupper(substr($_SESSION['admin_nama'] ?? 'A', 0, 1)) ?>
      </div>
      <div class="overflow-hidden" style="line-height: 1.2;">
        <div class="fw-700 text-dark text-truncate" style="font-size: 0.85rem;"><?= e($_SESSION['admin_nama'] ?? 'Admin Baru') ?></div>
        <small class="text-muted fw-500" style="font-size: 0.75rem;"><?= e(ucfirst($_SESSION['admin_role'] ?? 'Admin')) ?></small>
      </div>
    </div>
  </div>

</div>

<div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>