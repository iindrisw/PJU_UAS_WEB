<?php
/**
 * config/database.php
 * Konfigurasi koneksi database menggunakan PDO
 * Sistem PJU - Pelaporan Lampu Jalan Mati
 */

define('DB_HOST', 'localhost');
define('DB_NAME', 'db_pju');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

/**
 * Membuat koneksi PDO singleton
 */
function getDB(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            // Jangan tampilkan detail error di production
            error_log("DB Connection Error: " . $e->getMessage());
            die(json_encode(['success' => false, 'message' => 'Koneksi database gagal.']));
        }
    }
    return $pdo;
}