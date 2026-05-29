<?php
/**
 * admin/laporan.php
 * Manajemen laporan — CRUD, search, filter, pagination (Minimalist Light UI)
 */
require_once dirname(__DIR__) . '/config/database.php'; 
require_once dirname(__DIR__) . '/config/app.php'; 
requireAdminLogin(); 

$pageTitle   = 'Manajemen Laporan';
$currentPage = 'laporan';
$db          = getDB(); 

$search  = trim($_GET['search']  ?? ''); 
$status  = trim($_GET['status']  ?? ''); 
$page    = max(1, (int)($_GET['page'] ?? 1));
$perPage = 15;

$where  = 'WHERE 1=1'; 
$params = []; 

if ($search) {
    $where   .= " AND (l.kode_laporan LIKE ? OR l.nama_pelapor LIKE ? OR l.no_hp LIKE ? OR l.alamat_lokasi LIKE ?)";
    $params   = array_merge($params, ["%$search%","%$search%","%$search%","%$search%"]);
}
if ($status) {
    $where  .= " AND l.status = ?";
    $params[] = $status;
}

$stmtCount = $db->prepare("SELECT COUNT(*) FROM laporan l $where");
$stmtCount->execute($params);
$total = (int)$stmtCount->fetchColumn();

$pag    = paginate($total, $perPage, $page, BASE_URL . '/admin/laporan.php');
$offset = $pag['offset'];

$stmt = $db->prepare("
    SELECT l.*, t.nama AS nama_teknisi
    FROM laporan l
    LEFT JOIN teknisi t ON l.teknisi_id = t.id
    $where
    ORDER BY l.created_at DESC
    LIMIT :limit OFFSET :offset
");

foreach ($params as $key => $val) {
    $stmt->bindValue($key + 1, $val);
}
$stmt->bindValue(':limit', (int)$perPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
$stmt->execute();
$laporan = $stmt->fetchAll();

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
      <h4 class="fw-800 mb-0" style="color: #0F172A;">Manajemen Berkas Laporan</h4>
      <div class="small text-muted fw-500">Total: <strong><?= number_format($total) ?></strong> Dokumen</div>
    </div>

    <div class="content-padding">
      <div class="card-clean-panel mb-4">
        <div class="row g-3 align-items-center">
          <div class="col-md-6">
            <div class="input-group">
              <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
              <input type="text" id="search-input" class="form-control bg-light border-start-0" placeholder="Cari kode tiket, nama pelapor..." value="<?= e($search) ?>">
            </div>
          </div>
          <div class="col-md-4">
            <select id="filter-status" class="form-select bg-light">
              <option value="">Semua Status Keluhan</option>
              <option value="menunggu"         <?= $status==='menunggu'?'selected':'' ?>>Menunggu</option>
              <option value="diproses"         <?= $status==='diproses'?'selected':'' ?>>Diproses</option>
              <option value="dalam_perjalanan" <?= $status==='dalam_perjalanan'?'selected':'' ?>>Dalam Perjalanan</option>
              <option value="selesai"          <?= $status==='selesai'?'selected':'' ?>>Selesai</option>
              <option value="dibatalkan"       <?= $status==='dibatalkan'?'selected':'' ?>>Dibatalkan</option>
            </select>
          </div>
        </div>
      </div>

      <div class="card-clean-panel p-0 overflow-hidden">
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0" style="font-size: 0.92rem;">
            <thead class="table-light">
              <tr>
                <th class="ps-4 py-3">Kode</th>
                <th>Pelapor</th>
                <th>Foto Bukti</th>
                <th>Alamat Wilayah</th>
                <th>Status</th>
                <th>Teknisi Lapangan</th>
                <th class="pe-4 text-end">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($laporan)): ?>
              <tr><td colspan="7" class="text-center py-5 text-muted">Tidak ditemukan berkas aduan.</td></tr>
              <?php endif; ?>
              <?php foreach ($laporan as $row): ?>
              <tr>
                <td class="ps-4 fw-bold text-primary" style="font-family:'Space Mono', monospace;"><?= e($row['kode_laporan']) ?></td>
                <td>
                  <div class="fw-600 text-dark"><?= e($row['nama_pelapor']) ?></div>
                  <small class="text-muted"><?= e($row['no_hp']) ?></small>
                </td>
                <td>
                  <?php if ($row['foto']): ?>
                  <img src="<?= UPLOAD_URL . e($row['foto']) ?>" class="rounded cursor-pointer shadow-sm" style="width: 44px; height: 44px; object-fit: cover;" onclick="Swal.fire({imageUrl:this.src, showConfirmButton:false})"> 
                  <?php else: ?>
                  <span class="text-muted small">-</span> 
                  <?php endif; ?>
                </td>
                <td class="text-muted text-truncate" style="max-width: 200px;"><?= e($row['alamat_lokasi'] ?? 'Koordinat Terlampir') ?></td> 
                <td><?= statusBadge($row['status']) ?></td> 
                <td class="fw-600 text-success"><?= e($row['nama_teknisi'] ?? 'Belum Ditugaskan') ?></td> 
                <td class="pe-4 text-end">
                  <div class="btn-group gap-1">
                    <a href="<?= BASE_URL ?>/admin/detail_laporan.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-light border"><i class="bi bi-eye"></i></a> 
                    <button onclick="confirmDelete('<?= BASE_URL ?>/admin/hapus_laporan.php?id=<?= $row['id'] ?>', 'laporan <?= e($row['kode_laporan']) ?>')" class="btn btn-sm btn-light border text-danger"><i class="bi bi-trash"></i></button> 
                  </div>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>

        <?php if ($pag['total_pages'] > 1): ?> 
        <div class="p-4 d-flex justify-content-between align-items-center border-top bg-light">
          <small class="text-muted">Halaman <?= $pag['current'] ?> dari <?= $pag['total_pages'] ?></small> 
          <ul class="pagination mb-0">
            <?php for ($i = 1; $i <= $pag['total_pages']; $i++): ?> 
            <li class="page-item <?= $i === $page ? 'active' : '' ?>"><a class="page-link" href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&status=<?= $status ?>"><?= $i ?></a></li> 
            <?php endfor; ?>
          </ul>
        </div>
        <?php endif; ?>
      </div>
    </div>
  </main>
</div>

<?php include dirname(__DIR__) . '/templates/footer.php'; ?>