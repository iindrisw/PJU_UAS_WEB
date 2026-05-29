<?php
/**
 * admin/profil.php
 * Informasi Data Akun Profil Administrator — (Minimalist Light UI)
 */
require_once dirname(__DIR__) . '/config/database.php'; 
require_once dirname(__DIR__) . '/config/app.php'; 
requireAdminLogin(); 

$pageTitle   = 'Profil Admin';
$currentPage = 'profil';
$db          = getDB();

// Tarik profil admin dari session aktif
$adminId   = $_SESSION['admin_id'];
$stmt      = $db->prepare("SELECT username, nama, role FROM admin WHERE id = ? LIMIT 1");
$stmt->execute([$adminId]);
$adminData = $stmt->fetch();

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
      <h4 class="fw-800 mb-0" style="color: #0F172A;">Pengaturan Profil Akun</h4>
      <div class="small text-muted fw-500">Identitas Otoritas Terdaftar</div>
    </div>

    <div class="content-padding">
      <div class="row justify-content-center">
        <div class="col-md-6">
          <div class="card-clean-panel text-center p-5">
            <div class="mx-auto mb-4 d-flex align-items-center justify-content-center text-white fw-bold" 
                 style="width: 80px; height: 80px; background: linear-gradient(135deg, #6366F1, #4F46E5); font-size: 2rem; border-radius: 50%; box-shadow: 0 10px 20px rgba(99,102,241,0.2);">
              <?= strtoupper(substr($adminData['nama'] ?? 'A', 0, 1)) ?>
            </div>
            
            <h5 class="fw-800 text-dark mb-1"><?= e($adminData['nama'] ?? 'Administrator') ?></h5>
            <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill px-3 py-1.5 text-uppercase fw-700 mb-4" style="font-size: 0.65rem;"><?= e($adminData['role'] ?? 'Admin') ?></span>

            <div class="text-start border-top pt-4 w-100 mx-auto" style="max-width: 340px; font-size: 0.9rem;">
              <div class="mb-3">
                <span class="text-muted d-block small fw-600 mb-1">Nama Pengguna (Username)</span>
                <div class="p-3 bg-light rounded-3 fw-700 text-dark font-monospace"><?= e($adminData['username'] ?? '-') ?></div>
              </div>
              <div>
                <span class="text-muted d-block small fw-600 mb-1">Tingkat Hak Akses</span>
                <div class="p-3 bg-light rounded-3 fw-600 text-dark"><i class="bi bi-shield-check text-primary me-2"></i>Full System Controller</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>
</div>

<?php include dirname(__DIR__) . '/templates/footer.php'; ?>