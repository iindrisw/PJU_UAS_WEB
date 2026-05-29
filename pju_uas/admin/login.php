<?php
/**
 * admin/login.php
 * Halaman Login Administrator — Minimalist Light UI (High Contrast & Clean)
 * Sistem PJU Kota Bandung
 */
require_once dirname(__DIR__) . '/config/database.php';
require_once dirname(__DIR__) . '/config/app.php';

// Jika admin sudah login, langsung dialihkan ke dashboard internal
if (isAdminLoggedIn()) {
    redirect(BASE_URL . '/admin/dashboard.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrf($_POST['csrf_token'] ?? '')) {
        $error = 'Token keamanan tidak valid. Silakan muat ulang halaman.';
    } else {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($username) || empty($password)) {
            $error = 'Username dan password wajib diisi.';
        } else {
            $db   = getDB();
            $stmt = $db->prepare("SELECT * FROM admin WHERE username = ? LIMIT 1");
            $stmt->execute([$username]);
            $admin = $stmt->fetch();

            if ($admin && password_verify($password, $admin['password'])) {
                session_regenerate_id(true);
                $_SESSION['admin_id']   = $admin['id'];
                $_SESSION['admin_nama'] = $admin['nama'];
                $_SESSION['admin_role'] = $admin['role'];

                // Update info last login jika kolomnya tersedia
                try {
                    $db->prepare("UPDATE admin SET last_login = NOW() WHERE id = ?")->execute([$admin['id']]);
                } catch(Exception $e){}

                redirect(BASE_URL . '/admin/dashboard.php');
            } else {
                $error = 'Kredensial login salah atau tidak cocok.';
            }
        }
    }
}

$pageTitle = 'Login Administrator';
include dirname(__DIR__) . '/templates/header.php';
?>
<meta name="base-url" content="<?= BASE_URL ?>">

<style>
  body {
    background-color: #FAFAFB !important; /* Latar belakang abu-abu super soft cerah */
    color: #1E293B;
    font-family: 'Plus Jakarta Sans', sans-serif;
  }
  
  .login-wrapper {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 24px;
  }

  /* CARD LOGIN PUTIH CLEAN SLIM */
  .card-login-clean {
    background: #FFFFFF;
    border: 1px solid #E2E8F0;
    border-radius: 16px;
    padding: 40px;
    width: 100%;
    max-width: 420px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02);
  }

  /* HIGH CONTRAST INPUT FIELDS */
  .clean-label {
    color: #0F172A !important;
    font-weight: 700;
    font-size: 0.88rem;
  }

  .form-control {
    background: #FFFFFF !important;
    border: 1.5px solid #94A3B8 !important; /* Tebal dan jelas */
    color: #0F172A !important;
    font-weight: 600;
    padding: 12px 16px;
    border-radius: 10px;
  }

  .form-control::placeholder {
    color: #64748B !important;
    font-weight: 500;
  }

  .form-control:focus {
    border-color: #6366F1 !important;
    box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.15) !important;
  }

  /* TOMBOL UTAMA INDIGO */
  .btn-login-pju {
    background-color: #6366F1;
    color: #FFFFFF;
    font-weight: 700;
    padding: 12px 16px;
    border-radius: 10px;
    border: none;
    box-shadow: 0 4px 14px rgba(99, 102, 241, 0.2);
    transition: all 0.2s ease;
  }

  .btn-login-pju:hover {
    background-color: #4F46E5;
    color: #FFFFFF;
    transform: translateY(-1px);
  }
</style>

<div class="login-wrapper">
  <div class="card-login-clean">
    
    <div class="text-center mb-4">
      <div class="d-inline-flex align-items-center justify-content-center rounded-3 mb-3" 
           style="width: 44px; height: 44px; background-color: #EEF2FF; color: #6366F1;">
        <i class="bi bi-shield-lock-fill fs-4"></i>
      </div>
      <h4 class="fw-800 text-dark mb-1" style="letter-spacing: -0.02em;">Login Administrator</h4>
      <p class="text-muted small fw-500 mb-0">Masukkan kredensial untuk akses kontrol PJU</p>
    </div>

    <?php if ($error): ?>
    <div class="alert border-0 mb-4 d-flex align-items-center gap-2 small" 
         style="background: #FEF2F2; border-left: 4px solid #EF4444 !important; color: #991B1B; border-radius: 10px; font-weight: 600;">
      <i class="bi bi-exclamation-circle-fill"></i> <?= e($error) ?>
    </div>
    <?php endif; ?>

    <form method="POST" action="" novalidate>
      <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">

      <div class="mb-3">
        <label class="form-label clean-label mb-2">Nama Pengguna (Username)</label>
        <input type="text" name="username" class="form-control" 
               value="<?= e($username ?? '') ?>" placeholder="Masukkan nama pengguna" required autofocus>
      </div>

      <div class="mb-4">
        <label class="form-label clean-label mb-2">Kata Sandi (Password)</label>
        <div class="position-relative">
          <input type="password" name="password" id="input-pwd" class="form-control" style="padding-right: 46px;" placeholder="Masukkan kata sandi" required>
          <button type="button" onclick="togglePwdVisibility()" class="position-absolute border-0 bg-transparent text-muted" style="right: 14px; top: 50%; transform: translateY(-50%); padding: 0; z-index: 5;">
            <i class="bi bi-eye" id="eye-icon-toggle"></i>
          </button>
        </div>
      </div>

      <button type="submit" class="btn btn-login-pju w-100 text-center py-3 mb-3">
        <i class="bi bi-box-arrow-in-right me-2"></i>Masuk Sesi Kontrol
      </button>

      <div class="text-center mt-3 border-top pt-3 border-light">
        <a href="<?= BASE_URL ?>/" class="text-decoration-none small fw-600 text-secondary">
          <i class="bi bi-arrow-left me-1"></i> Kembali ke Beranda Publik
        </a>
      </div>
    </form>

  </div>
</div>

<?php
$extraJs = "
<script>
function togglePwdVisibility() {
  const pwdField = document.getElementById('input-pwd');
  const eyeIcon = document.getElementById('eye-icon-toggle');
  if (pwdField.type === 'password') {
    pwdField.type = 'text';
    eyeIcon.className = 'bi bi-eye-slash';
  } else {
    pwdField.type = 'password';
    eyeIcon.className = 'bi bi-eye';
  }
}
</script>
";
include dirname(__DIR__) . '/templates/footer.php';
?>