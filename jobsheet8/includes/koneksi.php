<?php
// =========================================================================
// Konfigurasi Basis Data (Supabase Cloud / PostgreSQL Lokal)
// =========================================================================

// Kredensial Supabase (Region: Seoul ap-northeast-2 via Connection Pooler IPv4)
$supabase_host = getenv('DB_HOST') ?: 'aws-0-ap-northeast-2.pooler.supabase.com';
$supabase_port = getenv('DB_PORT') ?: '6543';
$supabase_db   = getenv('DB_NAME') ?: 'postgres';
$supabase_user = getenv('DB_USER') ?: 'postgres.kwxjostfwuvxrpuskzxo';
$supabase_pass = getenv('DB_PASS') ?: 'youneedastrongerpassword';

$pdo = null;
$lastError = null;

// 1. Coba koneksi ke Supabase jika host telah diisi
if (!empty($supabase_host)) {
    try {
        $dsn = "pgsql:host={$supabase_host};port={$supabase_port};dbname={$supabase_db};sslmode=require";
        $pdo = new PDO($dsn, $supabase_user, $supabase_pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_TIMEOUT => 5
        ]);
    } catch (PDOException $e) {
        $lastError = $e;
    }
}

// 2. Fallback otomatis ke PostgreSQL lokal jika Supabase tidak aktif / offline
if (!$pdo) {
    $local_host = "localhost";
    $local_db   = "simpus_mini";
    $local_user = "postgres";

    $local_configs = [
        ['port' => '5433', 'pass' => 'admin'],
        ['port' => '5432', 'pass' => 'postgres'],
        ['port' => '5433', 'pass' => 'postgres'],
        ['port' => '5432', 'pass' => 'admin'],
    ];

    foreach ($local_configs as $cfg) {
        try {
            $dsn = "pgsql:host={$local_host};port={$cfg['port']};dbname={$local_db}";
            $pdo = new PDO($dsn, $local_user, $cfg['pass'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_TIMEOUT => 2
            ]);
            break;
        } catch (PDOException $e) {
            $lastError = $e;
        }
    }
}

// 3. Jika kedua koneksi gagal, tampilkan pesan galat
if (!$pdo) {
    die("Koneksi database gagal: " . ($lastError ? $lastError->getMessage() : 'Gagal terhubung ke basis data PostgreSQL'));
}
