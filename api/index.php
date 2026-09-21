<?php
ob_start();
// Entrypoint Serverless Function untuk deployment di Vercel

$requestUri = $_SERVER['REQUEST_URI'] ?? '/';
$path = parse_url($requestUri, PHP_URL_PATH) ?: '/';
$path = urldecode($path);

$projectRoot = dirname(__DIR__);

// Mapping request path ke berkas PHP tujuan
if ($path === '/' || $path === '') {
    $targetRel = '/index.php';
} else {
    // Jika tidak memiliki ekstensi .php dan berkas .php ada, tambahkan .php
    if (!str_ends_with($path, '.php') && file_exists($projectRoot . $path . '.php')) {
        $targetRel = $path . '.php';
    } else {
        $targetRel = $path;
    }
}

$targetFile = realpath($projectRoot . $targetRel);

// Validasi keamanan: pastikan berkas berada dalam direktori proyek dan bertipe .php
if (!$targetFile || !str_starts_with($targetFile, $projectRoot) || !is_file($targetFile) || !str_ends_with($targetFile, '.php')) {
    http_response_code(404);
    echo "<h1>404 Not Found</h1>";
    exit;
}

// Larang akses langsung ke berkas includes atau sql sebagai halaman utama
$normalizedRel = str_replace('\\', '/', substr($targetFile, strlen($projectRoot)));
if (str_starts_with($normalizedRel, '/includes/') || str_starts_with($normalizedRel, '/sql/') || str_starts_with($normalizedRel, '/api/')) {
    http_response_code(403);
    echo "<h1>403 Forbidden</h1>";
    exit;
}

// Sinkronisasi variabel lingkungan PHP agar header, footer, dan path relatif bekerja normal
$_SERVER['SCRIPT_FILENAME'] = $targetFile;
$_SERVER['PHP_SELF'] = $normalizedRel;
chdir(dirname($targetFile));

require $targetFile;
