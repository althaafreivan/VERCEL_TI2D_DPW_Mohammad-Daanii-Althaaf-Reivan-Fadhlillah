<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../includes/koneksi.php';
require_once __DIR__ . '/../includes/csrf.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit;
}

// 1. Verifikasi Token CSRF (Jobsheet 11)
csrf_verify();

$username = trim($_POST['username'] ?? '');
$password = trim($_POST['password'] ?? '');

if (empty($username) || empty($password)) {
    $_SESSION['flash'] = [
        'type'  => 'error',
        'pesan' => 'Username dan kata sandi wajib diisi!'
    ];
    header('Location: login.php');
    exit;
}

try {
    // 2. Query aman menggunakan Prepared Statement PDO (Anti SQL Injection)
    $stmt = $pdo->prepare("SELECT id, nama, username, password, role FROM users WHERE username = :username");
    $stmt->execute([':username' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verifikasi hash password
    if ($user && password_verify($password, $user['password'])) {
        // 3. Regenerasi ID sesi untuk mencegah serangan Session Fixation (Tugas Mandiri Jobsheet 11)
        session_regenerate_id(true);

        // Simpan data identitas pengguna ke dalam sesi server
        $_SESSION['user_id']  = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['nama']     = $user['nama'];
        $_SESSION['role']     = $user['role'];

        $_SESSION['flash'] = [
            'type'  => 'success',
            'pesan' => "Selamat datang kembali, <strong>" . htmlspecialchars($user['nama']) . "</strong>! (Role: " . ucfirst($user['role']) . ")"
        ];
        header('Location: ../collection/catalog.php');
        exit;
    } else {
        $_SESSION['flash'] = [
            'type'  => 'error',
            'pesan' => 'Username atau kata sandi yang Anda masukkan salah.'
        ];
        header('Location: login.php');
        exit;
    }
} catch (PDOException $e) {
    $_SESSION['flash'] = [
        'type'  => 'error',
        'pesan' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
    ];
    header('Location: login.php');
    exit;
}
