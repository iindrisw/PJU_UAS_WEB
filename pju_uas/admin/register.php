<?php
/**
 * admin/register.php
 * Halaman Registrasi Admin (Split Screen Layout 1/4 Menu & 3/4 Konten)
 * Sistem PJU Versi Deep Blue Cyber UI
 */
require_once dirname(__DIR__) . '/config/database.php';
require_once dirname(__DIR__) . '/config/app.php';

// Jika admin sudah login, langsung lempar ke dashboard
if (isAdminLoggedIn()) {
    redirect(BASE_URL . '/admin/dashboard.php');
}

$error   = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrf($_POST['csrf_token'] ?? '')) {
        $error = 'Token keamanan tidak valid, silakan muat ulang halaman.';
    } else {
        $username = trim($_POST['username'] ?? '');
        $nama     = trim($_POST['nama'] ?? '');
        $password = $_POST['password'] ?? '';
        $role     = $_POST['role'] ?? 'admin';

        if (empty($username) || empty($nama) || empty($password)) {
            $error = 'Seluruh kolom pendaftaran wajib diisi.';
        } elseif (strlen($username) < 4) {
            $error = 'Username minimal harus berukuran 4 karakter.';
        } elseif (strlen($password) < 6) {
            $error = 'Password minimal harus berukuran 6 karakter.';
        } else {
            $db = getDB();
            
            // Cek ketersediaan username di database
            $stmtCheck = $db->prepare("SELECT id FROM admin WHERE username = ? LIMIT 1");
            $stmtCheck->execute([$username]);
            
            if ($stmtCheck->fetch()) {
                $error = 'Username tersebut sudah terdaftar di sistem.';
            } else {
                // Amankan password menggunakan standar password_hash BCRYPT
                $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
                
                $stmtInsert = $db->prepare("INSERT INTO admin (username, password, nama, role, created_at) VALUES (?, ?, ?, ?, NOW())");
                $execute = $stmtInsert->execute([$username, $hashedPassword, $nama, $role]);
                
                if ($execute) {
                    $success = 'Akun berhasil dibuat! Silakan masuk halaman login.';
                    // Kosongkan field input setelah sukses
                    $username = $nama = '';
                } else {
                    $error = 'Terjadi kendala internal saat menyimpan data ke database.';
                }
            }
        }
    }
}

$pageTitle = 'Registrasi Administrator';
include dirname(__DIR__) . '/templates/header.php';
?>
<meta name="base-url" content="<?= BASE_URL ?>">

<style>
body {
  background: #0F172A;
  color: #fff;
  font-family: 'Plus Jakarta Sans', sans-serif;
  overflow-x: hidden;
}

/* Kiri: Menu Navigasi (1/4 Lebar / col-lg-3) */
.sidebar-menu-panel {
  background: #0B132B;
  border-right: 1px solid rgba(255, 255, 255, 0.05);
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
}

/* Kanan: Main Form Area (3/4 Lebar / col-lg-9) */
.main-content-panel {
  background: #0F172A;
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
}

.register-box-card {
  background: #1C2541;
  border: 1px solid rgba(255, 255, 255, 0.05);
  border-radius: 20px;
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
  width: 100%;
  max-width: 440px;
}

.custom-input-cyber {
  background: #0B132B !important;
  border: 1.5px solid rgba(255, 255, 255, 0.1) !important;
  border-radius: 12px !important;
  color: #fff !important;
  padding: 12px 16px !important;
}

.custom-input-cyber:focus {
  border-color: #00F5D4 !important;
  box-shadow: 0 0 0 4px rgba(0, 245, 212, 0.15) !important;
}

/* Item Menu List Bergaya Cyber */
.menu-nav-list {
  list-style: none;
  padding: 0;
  margin: 0;
}

.menu-nav-item {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 14px 20px;
  color: #94A3B8;
  text-decoration: none;
  border-radius: 12px;
  font-weight: 600;
  margin-bottom: 8px;
  transition: all 0.2s ease-in-out;
  border: 1px solid transparent;
}

.menu-nav-item:hover {
  color: #fff;
  background: rgba(255, 255, 255, 0.02);
}

.menu-nav-item.active-menu {
  color: #00F5D4;
  background: rgba(0, 245, 212, 0.05);
  border-color: rgba(0, 245, 212, 0.15);
}
</style>

<div class="container-fluid p-0">
  <div class="row g-0">
    
    <div class="col-12 col-lg-3 sidebar-menu-panel p-4">
      <div>
        <div class="d-flex align-items-center gap-2 mb-5 mt-2">
          <div style="width: 36px; height: 36px; background: rgba(0, 245, 212, 0.1); border-radius: 10px; display: flex; align-items: center; justify-content: center; color: #00F5D4;">
            <i class="bi bi-shield-lock-fill fs-5"></i>
          </div>
          <span class="fw-800 text-white fs-5" style="letter-spacing: -0.02em;">PJU Admin</span>
        </div>

        <p class="small text-uppercase fw-700 tracking-wider mb-3" style="color: #475569; font-size: 0.75rem;">Opsi Otentikasi</p>
        <nav class="menu-nav-list">
          <a href="<?= BASE_URL ?>/admin/login.php" class="menu-nav-item">
            <i class="bi bi-box-arrow-in-right fs-5"></i>
            <span>Masuk Sesi (Login)</span>
          </a>
          <a href="<?= BASE_URL ?>/admin/register.php" class="menu-nav-item active-menu">
            <i class="bi bi-person-plus-fill fs-5"></i>
            <span>Daftar Baru (Register)</span>
          </a>
        </nav>
      </div>

      <div class="pt-3 border-top border-secondary border-opacity-10">
        <a href="<?= BASE_URL ?>/" class="small text-decoration-none fw-600 d-inline-flex align-items-center gap-2" style="color: #94A3B8;">
          <i class="bi bi-arrow-left"></i> Kembali ke Beranda
        </a>
      </div>
    </div>

    <div class="col-12 col-lg-9 main-content-panel p-4">
      <div class="register-box-card p-4 p-md-5">
        
        <div class="text-center mb-4">
          <h3 class="text-white fw-800 fs-4 mb-1" style="letter-spacing: -0.02em;">Pendaftaran Admin</h3>
          <p class="small mb-0" style="color: #94A3B8; font-weight: 500;">Buat akun administrator baru untuk pengelolaan PJU</p>
        </div>

        <?php if ($error): ?>
        <div class="alert d-flex align-items-center gap-2 small mb-4" style="background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.2); border-radius: 10px; color: #fca5a5;">
          <i class="bi bi-exclamation-circle-fill"></i> <?= e($error) ?>
        </div>
        <?php endif; ?>

        <?php if ($success): ?>
        <div class="alert d-flex align-items-center gap-2 small mb-4" style="background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.2); border-radius: 10px; color: #a7f3d0;">
          <i class="bi bi-check-circle-fill"></i> <?= e($success) ?>
        </div>
        <?php endif; ?>

        <form method="POST" action="" novalidate>
          <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">

          <div class="mb-3">
            <label class="small fw-600 mb-2 d-block" style="color: #94A3B8;">Nama Lengkap</label>
            <input type="text" name="nama" class="custom-input-cyber form-control" 
                   value="<?= e($nama ?? '') ?>" placeholder="Masukkan nama lengkap" required autofocus>
          </div>

          <div class="mb-3">
            <label class="small fw-600 mb-2 d-block" style="color: #94A3B8;">Nama Pengguna (Username)</label>
            <input type="text" name="username" class="custom-input-cyber form-control" 
                   value="<?= e($username ?? '') ?>" placeholder="Buat nama pengguna unik" required>
          </div>

          <div class="mb-4">
            <label class="small fw-600 mb-2 d-block" style="color: #94A3B8;">Kata Sandi (Password)</label>
            <input type="password" name="password" class="custom-input-cyber form-control" 
                   placeholder="Minimal 6 karakter" required>
          </div>

          <button type="submit" class="btn btn-pju-soft-primary w-100 text-center py-3">
            <i class="bi bi-clipboard-check-fill me-2"></i>Daftarkan Akun Baru
          </button>
        </form>

        <div class="text-center mt-4 pt-3 border-top border-secondary border-opacity-10">
          <a href="<?= BASE_URL ?>/admin/login.php" class="small fw-700" style="color: #00F5D4;">
            Sudah memiliki kredensial? Masuk Sesi <i class="bi bi-arrow-right ms-1"></i>
          </a>
        </div>

      </div>
    </div>

  </div>
</div>

<?php include dirname(__DIR__) . '/templates/footer.php'; ?>