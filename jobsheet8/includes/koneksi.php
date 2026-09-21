<?php
error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);
// Konfigurasi koneksi basis data SIMPUS-Mini
// Mendukung PostgreSQL via PDO (Direct / Pooler), Supabase REST API, dan SQLite Fallback.

// Muat variabel lingkungan dari .env jika ada (untuk lingkungan lokal)
$envFiles = [
    dirname(__DIR__) . '/.env',
    dirname(dirname(__DIR__)) . '/.env'
];
foreach ($envFiles as $envFile) {
    if (file_exists($envFile)) {
        $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#')) continue;
            if (strpos($line, '=') !== false) {
                list($key, $val) = explode('=', $line, 2);
                $key = trim($key);
                $val = trim($val);
                if (!getenv($key)) {
                    putenv("$key=$val");
                    $_ENV[$key] = $val;
                }
            }
        }
        break;
    }
}

$dbUrl       = getenv('DATABASE_URL') ?: getenv('POSTGRES_URL');
$supabaseUrl = getenv('SUPABASE_URL');
$supabaseKey = getenv('SUPABASE_SECRET_KEY') ?: getenv('SUPABASE_PUBLISHABLE_KEY');

$host = getenv('DB_HOST') ?: "localhost";
$port = getenv('DB_PORT') ?: "5432";
$db   = getenv('DB_NAME') ?: "postgres";
$user = getenv('DB_USER') ?: "postgres";
$pass = getenv('DB_PASSWORD') ?: "";

$pdo = null;

// 1. Coba koneksi langsung PostgreSQL via PDO pgsql (misal jika POSTGRES_URL / DATABASE_URL disetel)
if (extension_loaded('pdo_pgsql') && ($dbUrl || getenv('DB_HOST') || !empty($pass))) {
    try {
        if (!empty($dbUrl)) {
            $parsed = parse_url($dbUrl);
            $pHost  = $parsed['host'] ?? 'localhost';
            $pPort  = $parsed['port'] ?? 5432;
            $pUser  = isset($parsed['user']) ? urldecode($parsed['user']) : 'postgres';
            $pPass  = isset($parsed['pass']) ? urldecode($parsed['pass']) : '';
            $pDb    = ltrim($parsed['path'] ?? 'postgres', '/');
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

        // Pastikan tabel ada di PostgreSQL
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

// 2. Adapter Supabase REST API (jika SUPABASE_URL & SUPABASE_SECRET_KEY tersedia dan tabel sudah ada)
if (!$pdo && !empty($supabaseUrl) && !empty($supabaseKey)) {
    class SupabaseRestDriver {
        private string $url;
        private string $key;

        public function __construct(string $url, string $key) {
            $this->url = rtrim($url, '/');
            $this->key = $key;
        }

        private function request(string $endpoint, string $method = 'GET', ?array $data = null, array $extraHeaders = []): ?array {
            $ch = curl_init($this->url . '/rest/v1/' . $endpoint);
            $headers = array_merge([
                'apikey: ' . $this->key,
                'Authorization: Bearer ' . $this->key,
                'Content-Type: application/json',
                'User-Agent: SIMPUS-Mini/1.0',
            ], $extraHeaders);

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_TIMEOUT, 4);

            if ($data !== null) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            }

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            if ($httpCode >= 200 && $httpCode < 300) {
                return json_decode($response, true) ?: [];
            }

            // Jika operasi POST/DELETE gagal, lemparkan PDOException agar ditangkap try..catch
            if ($method === 'POST' || $method === 'DELETE') {
                $err = json_decode($response, true);
                $msg = $err['message'] ?? $err['details'] ?? "Database error HTTP $httpCode";
                throw new PDOException($msg, (int)$httpCode);
            }
            return null;
        }

        public function test(): bool {
            $res = $this->request('buku?select=id&limit=1');
            return $res !== null;
        }

        public function count(string $table): int {
            $ch = curl_init($this->url . '/rest/v1/' . $table . '?select=id');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HEADER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'apikey: ' . $this->key,
                'Authorization: Bearer ' . $this->key,
                'Prefer: count=exact',
                'Range: 0-0',
                'User-Agent: SIMPUS-Mini/1.0',
            ]);
            curl_setopt($ch, CURLOPT_TIMEOUT, 4);
            $resp = curl_exec($ch);

            if (preg_match('/content-range:\s*(?:\d+-\d+|\*)\/(\d+)/i', (string)$resp, $matches)) {
                return (int)$matches[1];
            }
            return 0;
        }

        public function selectAll(string $table): array {
            return $this->request($table . '?select=*&order=id.desc') ?? [];
        }

        public function selectOne(string $table, int $id): ?array {
            $res = $this->request($table . '?id=eq.' . $id . '&limit=1');
            return !empty($res[0]) ? $res[0] : null;
        }

        public function insert(string $table, array $data): ?int {
            $res = $this->request($table, 'POST', $data, ['Prefer: return=representation']);
            return isset($res[0]['id']) ? (int)$res[0]['id'] : 1;
        }

        public function update(string $table, int $id, array $data): bool {
            $ch = curl_init($this->url . '/rest/v1/' . $table . '?id=eq.' . $id);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'apikey: ' . $this->key,
                'Authorization: Bearer ' . $this->key,
                'Content-Type: application/json',
                'Prefer: return=representation',
                'User-Agent: SIMPUS-Mini/1.0',
            ]);
            curl_setopt($ch, CURLOPT_TIMEOUT, 4);
            $res = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if ($httpCode >= 200 && $httpCode < 300) {
                return true;
            }
            $err = json_decode($res, true);
            $msg = $err['message'] ?? $err['details'] ?? "Database error HTTP $httpCode";
            throw new PDOException($msg, (int)$httpCode);
        }

        public function delete(string $table, int $id): bool {
            $ch = curl_init($this->url . '/rest/v1/' . $table . '?id=eq.' . $id);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'apikey: ' . $this->key,
                'Authorization: Bearer ' . $this->key,
                'User-Agent: SIMPUS-Mini/1.0',
            ]);
            curl_setopt($ch, CURLOPT_TIMEOUT, 4);
            $res = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            return $httpCode >= 200 && $httpCode < 300;
        }
    }

    class SupabaseStatement {
        private SupabaseRestDriver $driver;
        private string $table;

        public function __construct(SupabaseRestDriver $driver, string $table) {
            $this->driver = $driver;
            $this->table = $table;
        }

        public function execute(array $params = []): bool {
            // Mapping placeholder nama parameter ke kolom tabel
            $cleanData = [];
            foreach ($params as $k => $v) {
                $cleanKey = ltrim($k, ':');
                $cleanData[$cleanKey] = $v;
            }
            $res = $this->driver->insert($this->table, $cleanData);
            return $res !== null;
        }
    }

    class SupabasePdoAdapter {
        private SupabaseRestDriver $driver;

        public function __construct(SupabaseRestDriver $driver) {
            $this->driver = $driver;
        }

        public function query(string $sql) {
            $sqlTrim = trim($sql);
            if (preg_match('/SELECT\s+COUNT\(\*\)\s+FROM\s+(\w+)/i', $sqlTrim, $m)) {
                $count = $this->driver->count($m[1]);
                return new class($count) {
                    private int $count;
                    public function __construct(int $c) { $this->count = $c; }
                    public function fetchColumn() { return $this->count; }
                };
            }

            if (preg_match('/SELECT\s+\*\s+FROM\s+(\w+)/i', $sqlTrim, $m)) {
                $data = $this->driver->selectAll($m[1]);
                return new class($data) {
                    private array $data;
                    public function __construct(array $d) { $this->data = $d; }
                    public function fetchAll(int $mode = PDO::FETCH_ASSOC) { return $this->data; }
                };
            }

            return null;
        }

        public function prepare(string $sql) {
            if (preg_match('/INSERT\s+INTO\s+(\w+)/i', $sql, $m)) {
                return new SupabaseStatement($this->driver, $m[1]);
            }

            if (preg_match('/SELECT\s+\*\s+FROM\s+(\w+)\s+WHERE\s+id\s*=\s*:id/i', $sql, $m)) {
                return new class($this->driver, $m[1]) {
                    private SupabaseRestDriver $driver;
                    private string $table;
                    private ?array $data = null;
                    public function __construct(SupabaseRestDriver $d, string $t) {
                        $this->driver = $d;
                        $this->table = $t;
                    }
                    public function execute(array $params = []): bool {
                        $id = (int)($params['id'] ?? $params[':id'] ?? 0);
                        $this->data = $this->driver->selectOne($this->table, $id);
                        return $this->data !== null;
                    }
                    public function fetch(int $mode = PDO::FETCH_ASSOC): ?array {
                        return $this->data;
                    }
                };
            }

            if (preg_match('/UPDATE\s+(\w+)\s+SET\s+(.+?)\s+WHERE\s+id\s*=\s*:id/i', $sql, $m)) {
                return new class($this->driver, $m[1]) {
                    private SupabaseRestDriver $driver;
                    private string $table;
                    public function __construct(SupabaseRestDriver $d, string $t) {
                        $this->driver = $d;
                        $this->table = $t;
                    }
                    public function execute(array $params = []): bool {
                        $id = (int)($params['id'] ?? $params[':id'] ?? 0);
                        $cleanData = [];
                        foreach ($params as $k => $v) {
                            $cleanKey = ltrim($k, ':');
                            if ($cleanKey === 'id') continue;
                            $cleanData[$cleanKey] = $v;
                        }
                        return $this->driver->update($this->table, $id, $cleanData);
                    }
                };
            }

            if (preg_match('/DELETE\s+FROM\s+(\w+)\s+WHERE\s+id\s*=\s*:id/i', $sql, $m)) {
                return new class($this->driver, $m[1]) {
                    private SupabaseRestDriver $driver;
                    private string $table;
                    public function __construct(SupabaseRestDriver $d, string $t) {
                        $this->driver = $d;
                        $this->table = $t;
                    }
                    public function execute(array $params = []): bool {
                        $id = (int)($params['id'] ?? $params[':id'] ?? 0);
                        return $this->driver->delete($this->table, $id);
                    }
                };
            }

            throw new Exception("Query not supported by Supabase adapter: $sql");
        }

        public function setAttribute($attr, $val) {}
    }

    $sbDriver = new SupabaseRestDriver($supabaseUrl, $supabaseKey);
    // Hanya gunakan Supabase REST jika tabel buku sudah dibuat di Supabase
    if ($sbDriver->test()) {
        $pdo = new SupabasePdoAdapter($sbDriver);
    }
}

// 3. Fallback SQLite otomatis (apabila koneksi Postgres/Supabase belum terkonfigurasi atau tabel belum di-create)
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
    die("Koneksi database gagal: driver basis data tidak tersedia.");
}
