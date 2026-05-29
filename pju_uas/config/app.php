<?php
/**
 * config/app.php
 * Konfigurasi aplikasi & fungsi helper global (Fixed Auto Folder & Light UI Badges)
 */

// Base URL — sesuaikan dengan nama folder project di htdocs
define('BASE_URL',      'http://localhost/pju_uas');
define('BASE_PATH',     dirname(__DIR__));
define('UPLOAD_PATH',   BASE_PATH . '/uploads/laporan/');
define('UPLOAD_URL',    BASE_URL  . '/uploads/laporan/');
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5 MB
define('ALLOWED_TYPES', ['image/jpeg', 'image/png', 'image/webp', 'image/gif']);
define('APP_NAME',      'Sistem PJU');
define('APP_VERSION',   '1.0.0');

// Timezone
date_default_timezone_set('Asia/Jakarta');

// Session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ── Helpers ────────────────────────────────────────────────

/**
 * Sanitasi output ke HTML
 */
function e(string $str): string {
    return htmlspecialchars($str, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

/**
 * Generate kode laporan unik: PJU-YYYYMMDD-XXXX
 */
function generateKodeLaporan(): string {
    $db   = getDB();
    $date = date('Ymd');
    $stmt = $db->prepare("SELECT COUNT(*) FROM laporan WHERE DATE(created_at) = CURDATE()");
    $stmt->execute();
    $count = (int)$stmt->fetchColumn() + 1;
    return sprintf('PJU-%s-%04d', $date, $count);
}

/**
 * Upload foto laporan dengan validasi ketat & Saklar Pembuat Folder Otomatis
 */
function uploadFoto(array $file): string|false {
    if ($file['error'] !== UPLOAD_ERR_OK) return false;
    if ($file['size'] > MAX_FILE_SIZE)    return false;

    // SAKLAR OTOMATIS: Deteksi dan buat folder 'uploads/laporan' jika belum ada di laptop
    if (!is_dir(UPLOAD_PATH)) {
        mkdir(UPLOAD_PATH, 0777, true);
    }

    // Validasi MIME type sebenarnya (bukan hanya ekstensi)
    $finfo    = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $finfo->file($file['tmp_name']);
    if (!in_array($mimeType, ALLOWED_TYPES, true)) return false;

    $ext = match($mimeType) {
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
        'image/gif'  => 'gif',
    };
    $filename = 'PJU_' . date('YmdHis') . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
    $dest     = UPLOAD_PATH . $filename;

    if (!move_uploaded_file($file['tmp_name'], $dest)) return false;

    return $filename;
}

/**
 * Flash message ke session
 */
function setFlash(string $type, string $msg): void {
    $_SESSION['flash'] = ['type' => $type, 'msg' => $msg];
}

function getFlash(): ?array {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

/**
 * Redirect
 */
function redirect(string $url): void {
    header("Location: $url");
    exit;
}

/**
 * Cek apakah admin sudah login
 */
function isAdminLoggedIn(): bool {
    return isset($_SESSION['admin_id']) && !empty($_SESSION['admin_id']);
}

/**
 * Guard — paksa login admin
 */
function requireAdminLogin(): void {
    if (!isAdminLoggedIn()) {
        redirect(BASE_URL . '/admin/login.php');
    }
}

/**
 * Format tanggal Indonesia
 */
function formatTanggal(string $date, bool $withTime = false): string {
    $bulan = ['','Januari','Februari','Maret','April','Mei','Juni',
              'Juli','Agustus','September','Oktober','November','Desember'];
    $ts    = strtotime($date);
    $d     = date('j', $ts);
    $m     = $bulan[(int)date('n', $ts)];
    $y     = date('Y', $ts);
    $fmt   = "$d $m $y";
    if ($withTime) $fmt .= ' ' . date('H:i', $ts) . ' WIB';
    return $fmt;
}

/**
 * Label badge diselaraskan dengan gaya Minimalist Light UI (Warna Soft Pastel & Border)
 */
function statusBadge(string $status): string {
    return match($status) {
        'menunggu'         => '<span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2.5 py-1.5 rounded-pill fw-700 text-uppercase" style="font-size:0.65rem;"><i class="bi bi-clock me-1"></i>Menunggu</span>',
        'diproses'         => '<span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2.5 py-1.5 rounded-pill fw-700 text-uppercase" style="font-size:0.65rem;"><i class="bi bi-gear me-1"></i>Diproses</span>',
        'dalam_perjalanan' => '<span class="badge bg-info-subtle text-info border border-info-subtle px-2.5 py-1.5 rounded-pill fw-700 text-uppercase" style="font-size:0.65rem;"><i class="bi bi-truck me-1"></i>Di Jalan</span>',
        'selesai'          => '<span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1.5 rounded-pill fw-700 text-uppercase" style="font-size:0.65rem;"><i class="bi bi-check-circle me-1"></i>Selesai</span>',
        'dibatalkan'       => '<span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2.5 py-1.5 rounded-pill fw-700 text-uppercase" style="font-size:0.65rem;"><i class="bi bi-x-circle me-1"></i>Batal</span>',
        default            => '<span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2.5 py-1.5 rounded-pill fw-700 text-uppercase" style="font-size:0.65rem;">Tidak Diketahui</span>',
    };
}

/**
 * Label status Indonesia
 */
function statusLabel(string $status): string {
    return match($status) {
        'menunggu'         => 'Menunggu',
        'diproses'         => 'Diproses',
        'dalam_perjalanan' => 'Dalam Perjalanan',
        'selesai'          => 'Selesai',
        'dibatalkan'       => 'Dibatalkan',
        default            => 'Tidak Diketahui',
    };
}

/**
 * Paginasi sederhana
 */
function paginate(int $total, int $perPage, int $currentPage, string $baseUrl): array {
    $totalPages = (int)ceil($total / $perPage);
    return [
        'total'       => $total,
        'per_page'    => $perPage,
        'current'     => $currentPage,
        'total_pages' => $totalPages,
        'offset'      => ($currentPage - 1) * $perPage,
        'base_url'    => $baseUrl,
    ];
}

/**
 * JSON response helper (untuk endpoint AJAX)
 */
function jsonResponse(bool $success, string $message, array $data = []): never {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(array_merge(['success' => $success, 'message' => $message], $data));
    exit;
}

/**
 * CSRF token generator & validator
 */
function csrfToken(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCsrf(string $token): bool {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}