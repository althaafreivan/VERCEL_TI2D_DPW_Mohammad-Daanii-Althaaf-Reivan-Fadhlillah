<?php
// jobsheet10/includes/auth.php - Guard Clause Autentikasi Pengguna (Jobsheet 10)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Cek apakah pengguna sudah memiliki sesi login aktif
if (!isset($_SESSION['user_id'])) {
    $_SESSION['flash'] = [
        'type'  => 'error',
        'pesan' => 'Akses dibatasi! Silakan login terlebih dahulu untuk mengelola galeri gambar.'
    ];

    // Kalkulasi path relatif dinamis menuju auth/login.php
    $__jsRoot = dirname(__DIR__);
    $__scrDir = dirname($_SERVER['SCRIPT_FILENAME']);
    $__rel = ltrim(str_replace('\\', '/', substr($__scrDir, strlen($__jsRoot))), '/');
    $base_auth = $__rel === '' ? '' : str_repeat('../', substr_count($__rel, '/') + 1);

    header("Location: " . $base_auth . "auth/login.php");
    exit;
}
