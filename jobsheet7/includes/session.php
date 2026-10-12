<?php
// jobsheet7/includes/session.php
// Manajemen sesi yang terikat pada siklus hidup proses server PHP yang aktif.
// Pada Jobsheet 7, data hanya disimpan sementara di sesi (in-memory/per-server lifecycle).
// Jika server PHP dimatikan dan dinyalakan kembali, data sesi lama akan di-reset otomatis.

if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
    session_start();
}

// Identifikasi instance proses server PHP saat ini
$__srvPid = getmypid();
$__runtimeMarker = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'php_simpus_instance_' . $__srvPid . '.tmp';

if (!file_exists($__runtimeMarker)) {
    // Bersihkan marker lama dari proses sebelumnya
    $oldMarkers = glob(sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'php_simpus_instance_*.tmp');
    if ($oldMarkers) {
        foreach ($oldMarkers as $oldM) {
            @unlink($oldM);
        }
    }
    file_put_contents($__runtimeMarker, bin2hex(random_bytes(16)));
}

$__currentInstanceToken = @file_get_contents($__runtimeMarker) ?: (string)$__srvPid;

// Jika sesi berasal dari proses server sebelumnya (server sempat dimatikan), reset data
if (!isset($_SESSION['__server_instance']) || $_SESSION['__server_instance'] !== $__currentInstanceToken) {
    $_SESSION = [];
    $_SESSION['__server_instance'] = $__currentInstanceToken;
    $_SESSION['buku'] = [];
    $_SESSION['anggota'] = [];
}
