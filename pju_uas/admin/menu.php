<?php
/**
 * admin/menu.php
 * Panel Menu Akses Admin — Versi Deep Blue Cyber UI
 */
require_once dirname(__DIR__) . '/config/database.php';
require_once dirname(__DIR__) . '/config/app.php';

// Jika admin ternyata sudah login, langsung lempar ke dashboard
if (isAdminLoggedIn()) {
    redirect(BASE_URL . '/admin/dashboard.php');
}

$pageTitle = 'Panel Akses Admin';
include dirname(__DIR__) . '/templates/header.php';
?>
<meta name="base-url" content="<?= BASE_URL ?>">

<style>
body { 
  background: #0F172A; 
  min-height: 100vh; 
  color: #fff;
  font-family: 'Plus Jakarta Sans', sans-serif;
}
.menu-container {
  background: #0B132B;
  border: 1px solid rgba(255, 255, 255, 0.05);
  border-radius: 24px;
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
}
.menu-box {
  background: #1E293B;
  border: 1px solid rgba(255, 255, 255, 0.03);
  border-radius: 16px;
  transition: all 0.25s ease-in-out;
  text-decoration: none;
  color: #fff;
  display: block;
}
.menu-box:hover {
  background: #1E293B;
  border-color: #00F5D4;
  transform: translateY(-4px);
  box-shadow: 0 8px 24px rgba(0, 245, 212, 0.12);
}
.menu-box:hover .icon-wrapper {
  background: rgba(0, 245, 212, 0.1);
  color: #00F5D4;
  border-color: rgba(0, 245, 212, 0.2);
}
.icon-wrapper {
  width: 56px;
  height: 56px;
  background: rgba(255, 255, 255, 0.02);
  border: 1px solid rgba(255, 255, 255, 0.05);
  border-radius: 14px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.6rem;
  color: #94A3B8;
  transition: all 0.25s ease-in-out;
}
</style>

<div class="d-flex align-items-center justify-content-center" style="min-height: 100vh; padding: 24px;">
  <div class="w-100" style="max-width: 500px;">
    
    <div class="text-center mb-4">
      <div class="mx-auto mb-3" style="width: 52px; height: 52px; background: rgba(0, 245, 212, 0.08); border: 1px solid rgba(0, 245, 212, 0.2); border-radius: 14px; display: flex; align-items: center; justify-content: center; color: #00F5D4; font-size: 1.5rem;">
        <i class="bi bi-shield-lock-fill"></i>
      </div>
      <h4 class="fw-800 text-white mb-1" style="letter-spacing: -0.02em;">Otentikasi Administrator</h4>
      <p class="small mb-0" style="color: #94A3B8; font-weight: 500;">Silakan pilih opsi rute pangkalan data di bawah</p>
    </div>

    <div class="menu-container p-4 p-md-5">
      <div class="d-flex flex-column gap-3">
        
        <a href="<?= BASE_URL ?>/admin/login.php" class="menu-box p-4">
          <div class="d-flex align-items-center gap-3">
            <div class="icon-wrapper">
              <i class="bi bi-box-arrow-in-right"></i>
            </div>
            <div>
              <h6 class="fw-800 mb-1 text-white">Masuk Log Sesi (Login)</h6>
              <p class="small mb-0 text-muted" style="font-size: 0.8rem; font-weight: 500;">Masuk ke sistem kontrol monitoring PJU utama.</p>
            </div>
          </div>
        </a>

        <a href="<?= BASE_URL ?>/admin/register.php" class="menu-box p-4">
          <div class="d-flex align-items-center gap-3">
            <div class="icon-wrapper">
              <i class="bi bi-person-plus-fill"></i>
            </div>
            <div>
              <h6 class="fw-800 mb-1 text-white">Registrasi Akun Baru</h6>
              <p class="small mb-0 text-muted" style="font-size: 0.8rem; font-weight: 500;">Daftarkan kredensial administrator baru secara aman.</p>
            </div>
          </div>
        </a>

      </div>

      <div class="text-center mt-4 pt-3 border-top border-secondary border-opacity-10">
        <a href="<?= BASE_URL ?>/" class="small text-decoration-none fw-600" style="color: #94A3B8;">
          <i class="bi bi-arrow-left me-1"></i> Kembali ke Beranda Publik
        </a>
      </div>
    </div>

  </div>
</div>

<?php include dirname(__DIR__) . '/templates/footer.php'; ?>