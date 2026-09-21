<?php
ob_start();
// Entrypoint Router Serverless Function untuk Vercel Deployment
// Mendukung Dynamic PHP (Jobsheet 7, Jobsheet 8, & Portal) serta Static Files (Jobsheet 1 - 6)

$requestUri = $_SERVER['REQUEST_URI'] ?? '/';
$path = parse_url($requestUri, PHP_URL_PATH) ?: '/';
$path = urldecode($path);

$projectRoot = dirname(__DIR__);

// 1. Blokir akses langsung ke berkas/folder sensitif
if (
    str_contains($path, '/includes/') ||
    str_contains($path, '/sql/') ||
    str_contains($path, '/api/') ||
    str_contains($path, '/.env') ||
    str_contains($path, '/.git')
) {
    http_response_code(403);
    echo "<h1>403 Forbidden</h1>";
    exit;
}

// 2. Redirect rute warisan /buku/* dan /anggota/* ke /jobsheet8/*
if (str_starts_with($path, '/buku/') || str_starts_with($path, '/anggota/')) {
    header("Location: /jobsheet8" . $path, true, 301);
    exit;
}

// 3. Pastikan direktori selalu berakhiran slash (/) agar path relatif HTML/CSS bekerja presisi
$rawPath = rtrim($projectRoot . $path, '/\\');
if ($path !== '/' && is_dir($rawPath) && !str_ends_with($path, '/')) {
    $qs = !empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : '';
    header("Location: " . $path . '/' . $qs, true, 301);
    exit;
}

// 4. Resolusi path berkas target
$targetRel = $path;

if ($path === '/' || $path === '') {
    $targetRel = '/index.php';
} else {
    if (is_dir($rawPath)) {
        if (file_exists($rawPath . '/index.php')) {
            $targetRel = rtrim($path, '/') . '/index.php';
        } elseif (file_exists($rawPath . '/index.html')) {
            $targetRel = rtrim($path, '/') . '/index.html';
        }
    } elseif (!file_exists($projectRoot . $path)) {
        if (file_exists($projectRoot . $path . '.php')) {
            $targetRel = $path . '.php';
        } elseif (file_exists($projectRoot . $path . '.html')) {
            $targetRel = $path . '.html';
        }
    }
}

$targetFile = realpath($projectRoot . $targetRel);

// 5. Validasi keamanan direktori berkas
if (!$targetFile || !str_starts_with($targetFile, $projectRoot) || !is_file($targetFile)) {
    http_response_code(404);
    echo "<h1>404 Not Found</h1><p>Halaman atau berkas tidak ditemukan: " . htmlspecialchars($path) . "</p>";
    exit;
}

$normalizedRel = str_replace('\\', '/', substr($targetFile, strlen($projectRoot)));
$ext = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

// 6. Layani berkas statis jika berkas bukan PHP
$mimeTypes = [
    'html' => 'text/html; charset=UTF-8',
    'css'  => 'text/css; charset=UTF-8',
    'js'   => 'application/javascript; charset=UTF-8',
    'json' => 'application/json; charset=UTF-8',
    'png'  => 'image/png',
    'jpg'  => 'image/jpeg',
    'jpeg' => 'image/jpeg',
    'gif'  => 'image/gif',
    'svg'  => 'image/svg+xml',
    'ico'  => 'image/x-icon',
    'md'   => 'text/markdown; charset=UTF-8',
];

if ($ext !== 'php') {
    $contentType = $mimeTypes[$ext] ?? mime_content_type($targetFile) ?: 'application/octet-stream';
    header("Content-Type: $contentType");
    header("Cache-Control: public, max-age=3600");
    readfile($targetFile);
    exit;
}

// 7. Eksekusi berkas dinamis PHP
$_SERVER['SCRIPT_FILENAME'] = $targetFile;
$_SERVER['PHP_SELF'] = $normalizedRel;
chdir(dirname($targetFile));

require $targetFile;
