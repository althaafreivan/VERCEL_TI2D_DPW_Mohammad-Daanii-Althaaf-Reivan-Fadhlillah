<?php
// =========================================================================
// Konfigurasi Basis Data (Supabase Cloud / PostgreSQL Lokal)
// =========================================================================

// Kredensial Supabase (isi bagian ini dengan data dari dashboard Supabase Anda)
// Supabase Dashboard -> Project Settings -> Database
$supabase_host = getenv('DB_HOST') ?: 'db.kwxjostfwuvxrpuskzxo.supabase.co';       // Contoh: aws-0-ap-southeast-1.pooler.supabase.com
$supabase_port = getenv('DB_PORT') ?: '5432';   // Port pooler: 6543 atau direct: 5432
$supabase_db   = getenv('DB_NAME') ?: 'postgres';
$supabase_user = getenv('DB_USER') ?: 'postgres';       // Contoh: postgres.projectref atau postgres
$supabase_pass = getenv('DB_PASS') ?: 'youneedastrongerpassword';       // Password database Supabase Anda


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

// 2. Fallback otomatis ke PostgreSQL lokal jika Supabase belum diisi atau gagal
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
