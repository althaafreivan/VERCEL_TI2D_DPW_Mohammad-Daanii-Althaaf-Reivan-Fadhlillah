<?php
// jobsheet10/auth/logout.php - Menghapus sesi pengguna
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Kosongkan seluruh variabel sesi
$_SESSION = [];

// Hapus cookie sesi jika ada
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Hancurkan sesi di server
session_destroy();

// Mulai sesi baru secara bersih khusus untuk menampung pesan flash
session_start();
$_SESSION['flash'] = [
    'type'  => 'success',
    'pesan' => 'Anda telah berhasil keluar (logout) dari sistem PixelGallery.'
];

header('Location: login.php');
exit;
