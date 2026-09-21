<?php
// Konfigurasi basis data simpus_mini
// Mendukung PostgreSQL (lokal & cloud via DATABASE_URL/POSTGRES_URL) serta fallback SQLite otomatis untuk Vercel.

$dbUrl = getenv('DATABASE_URL') ?: getenv('POSTGRES_URL');
$host  = getenv('DB_HOST') ?: "localhost";
$port  = getenv('DB_PORT') ?: "5432";
$db    = getenv('DB_NAME') ?: "simpus_mini";
$user  = getenv('DB_USER') ?: "postgres";
$pass  = getenv('DB_PASSWORD') ?: "postgres";

$pdo = null;

// 1. Mencoba koneksi PostgreSQL via PDO jika driver pdo_pgsql tersedia
if (extension_loaded('pdo_pgsql')) {
    try {
        if (!empty($dbUrl)) {
            $parsed = parse_url($dbUrl);
            $pHost  = $parsed['host'] ?? 'localhost';
            $pPort  = $parsed['port'] ?? 5432;
            $pUser  = $parsed['user'] ?? '';
            $pPass  = $parsed['pass'] ?? '';
            $pDb    = ltrim($parsed['path'] ?? 'simpus_mini', '/');
            $dsn    = "pgsql:host=$pHost;port=$pPort;dbname=$pDb;sslmode=require";
            $pdo    = new PDO($dsn, $pUser, $pPass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_TIMEOUT => 3,
            ]);
        } else {
            $dsn = "pgsql:host=$host;port=$port;dbname=$db";
            $pdo = new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_TIMEOUT => 2,
            ]);
        }

        // Inisialisasi skema tabel jika belum dibuat
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS buku (
                id SERIAL PRIMARY KEY,
                judul VARCHAR(255) NOT NULL,
                pengarang VARCHAR(255) NOT NULL,
                tahun INTEGER NOT NULL,
                isbn VARCHAR(50),
                stok INTEGER NOT NULL DEFAULT 0,
                kategori VARCHAR(50)
            );
            CREATE TABLE IF NOT EXISTS anggota (
                id SERIAL PRIMARY KEY,
                nama VARCHAR(255) NOT NULL,
                no_anggota VARCHAR(50) NOT NULL UNIQUE,
                alamat VARCHAR(255),
                no_hp VARCHAR(30)
            );
        ");
    } catch (Throwable $e) {
        $pdo = null;
    }
}

// 2. Fallback aman ke SQLite (misal saat dideploy di Vercel tanpa Postgres cloud atau driver pgsql lokal belum aktif)
if (!$pdo && extension_loaded('pdo_sqlite')) {
    try {
        $dbPath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'simpus_mini.db';
        $isNew = !file_exists($dbPath);
        $pdo = new PDO("sqlite:" . $dbPath, null, null, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        ]);

        $pdo->exec("
            CREATE TABLE IF NOT EXISTS buku (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                judul VARCHAR(255) NOT NULL,
                pengarang VARCHAR(255) NOT NULL,
                tahun INTEGER NOT NULL,
                isbn VARCHAR(50),
                stok INTEGER NOT NULL DEFAULT 0,
                kategori VARCHAR(50)
            );
            CREATE TABLE IF NOT EXISTS anggota (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                nama VARCHAR(255) NOT NULL,
                no_anggota VARCHAR(50) NOT NULL UNIQUE,
                alamat VARCHAR(255),
                no_hp VARCHAR(30)
            );
        ");

        if ($isNew) {
            $bukuCount = $pdo->query("SELECT COUNT(*) FROM buku")->fetchColumn();
            if ($bukuCount == 0) {
                $pdo->exec("
                    INSERT INTO buku (judul, pengarang, tahun, isbn, stok, kategori) VALUES
                    ('Laskar Pelangi', 'Andrea Hirata', 2005, '978-979-3062-79-2', 5, 'Fiksi'),
                    ('Bumi Manusia', 'Pramoedya Ananta Toer', 1980, '978-979-97312-3-4', 3, 'Sejarah'),
                    ('Filosofi Kopi', 'Dee Lestari', 2006, '978-979-96257-3-1', 4, 'Sastra');
                    INSERT INTO anggota (nama, no_anggota, alamat, no_hp) VALUES
                    ('Mohammad Daanii Althaaf', 'AG-001', 'Malang', '081234567890'),
                    ('Reivan Fadhlillah', 'AG-002', 'Surabaya', '081298765432');
                ");
            }
        }
    } catch (Throwable $e) {
        die("Koneksi database gagal: " . $e->getMessage());
    }
}

if (!$pdo) {
    die("Koneksi database gagal: ekstensi PDO tidak ditemukan.");
}
