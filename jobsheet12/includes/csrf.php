<?php
// jobsheet11/includes/csrf.php - Proteksi CSRF (Cross-Site Request Forgery) berbasis Token Sesi
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Menghasilkan atau mengambil token CSRF yang tersimpan di sesi
 */
function csrf_token()
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Menghasilkan input hidden HTML berisi token CSRF untuk formulir
 */
function csrf_field()
{
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8') . '">';
}

/**
 * Memvalidasi token CSRF dari permintaan POST menggunakan perbandingan waktu-konstan
 */
function csrf_verify()
{
    $token = $_POST['csrf_token'] ?? '';
    $session_token = $_SESSION['csrf_token'] ?? '';

    if ($token === '' || $session_token === '' || !hash_equals($session_token, $token)) {
        http_response_code(403);
        $_SESSION['flash'] = [
            'type'  => 'error',
            'pesan' => 'Permintaan ditolak: Token CSRF tidak valid atau telah kedaluwarsa. Silakan coba kembali.'
        ];
        // Kembalikan ke halaman sebelumnya jika ada, atau ke beranda
        $referer = $_SERVER['HTTP_REFERER'] ?? '../index.php';
        header("Location: " . $referer);
        exit;
    }
}
