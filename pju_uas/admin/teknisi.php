<?php
/**
 * admin/teknisi.php
 * Manajemen data teknisi lapangan — CRUD Lengkap (Minimalist Light UI)
 */
require_once dirname(__DIR__) . '/config/database.php'; 
require_once dirname(__DIR__) . '/config/app.php'; 
requireAdminLogin(); 

$pageTitle   = 'Manajemen Teknisi';
$currentPage = 'teknisi';
$db          = getDB(); 

$error = '';
$success = '';

// ── PROSES POST: TAMBAH TEKNISI BARU ────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    if (!verifyCsrf($_POST['csrf_token'] ?? '')) {
        $error = 'Token keamanan tidak valid.';
    } else {
        $nama  = trim($_POST['nama'] ?? '');
        $no_hp = trim($_POST['no_hp'] ?? '');

        if (empty($nama) || empty($no_hp)) {
            $error = 'Nama teknisi dan Nomor HP wajib diisi.';
        } else {
            $stmtInsert = $db->prepare("INSERT INTO teknisi (nama, no_hp, status, created_at) VALUES (?, ?, 'aktif', NOW())");
            if ($stmtInsert->execute([$nama, $no_hp])) {
                setFlash('success', 'Teknisi baru berhasil didaftarkan!');
                redirect(BASE_URL . '/admin/teknisi.php');
            } else {
                $error = 'Gagal menyimpan data teknisi ke database.';
            }
        }
    }
}

// ── PROSES GET: HAPUS TEKNISI ───────────────────────────────
if (isset($_GET['delete_id'])) {
    $deleteId = (int)$_GET['delete_id'];
    $stmtDelete = $db->prepare("DELETE FROM teknisi WHERE id = ?");
    if ($stmtDelete->execute([$deleteId])) {
        setFlash('success', 'Data teknisi berhasil dihapus dari sistem.');
    } else {
        setFlash('error', 'Gagal menghapus data teknisi.');
    }
    redirect(BASE_URL . '/admin/teknisi.php');
}

// Ambil semua data teknisi
$stmt = $db->query("SELECT * FROM teknisi ORDER BY nama ASC");
$teknisi = $stmt->fetchAll();

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
  
  .custom-modal-input {
    background: #F8FAFC !important;
    border: 1.5px solid #E2E8F0 !important;
    border-radius: 10px !important;
    padding: 10px 14px !important;
  }
  .custom-modal-input:focus {
    border-color: #6366F1 !important;
    box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.15) !important;
  }
</style>

<div class="admin-wrapper">
  <?php include dirname(__DIR__) . '/templates/admin_sidebar.php'; ?>

  <main class="main-content">
    <div class="topbar-clean d-flex justify-content-between align-items-center">
      <h4 class="fw-800 mb-0" style="color: #0F172A;">Manajemen Teknisi Lapangan</h4>
      <button class="btn btn-sm fw-700 px-3 py-2 text-white" data-bs-toggle="modal" data-bs-target="#modalAddTeknisi"
              style="background-color: #6366F1; border-radius: 10px; box-shadow: 0 4px 12px rgba(99,102,241,0.2);">
        <i class="bi bi-person-plus-fill me-1"></i> Tambah Teknisi
      </button>
    </div>

    <div class="content-padding">

      <?php if ($error): ?>
      <div class="alert d-flex align-items-center gap-2 small mb-4" style="background: rgba(239, 68, 68, 0.08); border: 1px solid rgba(239, 68, 68, 0.15); border-radius: 10px; color: #dc2626;">
        <i class="bi bi-exclamation-circle-fill"></i> <?= e($error) ?>
      </div>
      <?php endif; ?>

      <div class="card-clean-panel p-0 overflow-hidden">
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0" style="font-size: 0.92rem;">
            <thead class="table-light">
              <tr>
                <th class="ps-4 py-3" style="width: 80px;">Inisial</th>
                <th>Nama Lengkap</th>
                <th>Nomor HP / WhatsApp</th>
                <th>Status Operasional</th>
                <th class="pe-4 text-end">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($teknisi)): ?>
              <tr><td colspan="5" class="text-center py-5 text-muted">Belum ada data teknisi yang terdaftar.</td></tr>
              <?php endif; ?>
              <?php foreach ($teknisi as $t): ?>
              <tr>
                <td class="ps-4">
                  <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold" 
                       style="width: 36px; height: 36px; background: linear-gradient(135deg, #6366F1, #4F46E5); font-size: 0.85rem;">
                    <?= strtoupper(substr($t['nama'], 0, 1)) ?>
                  </div>
                </td>
                <td class="fw-700 text-dark"><?= e($t['nama']) ?></td>
                <td class="font-monospace text-muted"><?= e($t['no_hp']) ?></td>
                <td>
                  <?php if (($t['status'] ?? 'aktif') === 'aktif'): ?>
                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5 py-1.5 text-uppercase fw-700" style="font-size: 0.65rem;">Siap Bertugas</span>
                  <?php else: ?>
                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2.5 py-1.5 text-uppercase fw-700" style="font-size: 0.65rem;">Tidak Aktif</span>
                  <?php endif; ?>
                </td>
                <td class="pe-4 text-end">
                  <button onclick="confirmDelete('<?= BASE_URL ?>/admin/teknisi.php?delete_id=<?= $t['id'] ?>', 'teknisi bernama <?= e($t['nama']) ?>')" 
                          class="btn btn-sm btn-light border text-danger" style="border-radius: 8px;">
                    <i class="bi bi-trash"></i>
                  </button>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </main>
</div>

<div class="modal fade" id="modalAddTeknisi" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" style="max-width: 400px;">
    <div class="modal-content border-0" style="border-radius: 18px; background-color: #FFFFFF; box-shadow: 0 20px 50px rgba(0,0,0,0.15);">
      <div class="modal-header border-bottom border-light p-4">
        <h5 class="fw-800 mb-0 text-dark" style="font-size: 1.1rem; letter-spacing: -0.02em;">Registrasi Teknisi Baru</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form method="POST" action="" novalidate>
        <div class="modal-body p-4">
          <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
          <input type="hidden" name="action" value="add">

          <div class="mb-3">
            <label class="small fw-700 mb-2 d-block text-secondary">Nama Lengkap Personel</label>
            <input type="text" name="nama" class="form-control custom-modal-input" placeholder="Contoh: Fariz Hilal" required>
          </div>

          <div class="mb-2">
            <label class="small fw-700 mb-2 d-block text-secondary">Nomor HP / WhatsApp</label>
            <input type="tel" name="no_hp" class="form-control custom-modal-input" placeholder="Contoh: 08123456789" required>
          </div>
        </div>
        <div class="modal-footer border-top border-light p-3 d-flex gap-2">
          <button type="button" class="btn btn-sm btn-light border px-3 py-2 fw-600" data-bs-dismiss="modal" style="border-radius: 10px;">Batal</button>
          <button type="submit" class="btn btn-sm text-white px-4 py-2 fw-700" style="background-color: #6366F1; border-radius: 10px;">Simpan Personel</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php include dirname(__DIR__) . '/templates/footer.php'; ?>