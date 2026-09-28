<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../includes/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: register.php');
    exit;
}

$nama        = trim($_POST['nama'] ?? '');
$username    = strtolower(trim($_POST['username'] ?? ''));
$password    = trim($_POST['password'] ?? '');
$konfirmasi  = trim($_POST['konfirmasi_password'] ?? '');
$role        = in_array($_POST['role'] ?? '', ['user', 'admin']) ? $_POST['role'] : 'user';

// Validasi kelengkapan data
if (empty($nama) || empty($username) || empty($password)) {
    $_SESSION['flash'] = [
        'type'  => 'error',
        'pesan' => 'Semua kolom yang bertanda bintang (*) wajib diisi!'
    ];
    header('Location: register.php');
    exit;
}

// Validasi format username
if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
    $_SESSION['flash'] = [
        'type'  => 'error',
        'pesan' => 'Username hanya boleh terdiri dari huruf, angka, dan underscore (_).'
    ];
    header('Location: register.php');
    exit;
}

// Validasi panjang password
if (strlen($password) < 6) {
    $_SESSION['flash'] = [
        'type'  => 'error',
        'pesan' => 'Kata sandi minimal harus 6 karakter.'
    ];
    header('Location: register.php');
    exit;
}

// Validasi kecocokan password dan konfirmasi
if ($password !== $konfirmasi) {
    $_SESSION['flash'] = [
        'type'  => 'error',
        'pesan' => 'Konfirmasi kata sandi tidak cocok dengan kata sandi.'
    ];
    header('Location: register.php');
    exit;
}

try {
    // 1. Cek apakah username sudah pernah digunakan
    $cek = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = :username");
    $cek->execute([':username' => $username]);
    if ($cek->fetchColumn() > 0) {
        $_SESSION['flash'] = [
            'type'  => 'error',
            'pesan' => "Username '<strong>" . htmlspecialchars($username) . "</strong>' sudah terdaftar. Silakan pilih username lain."
        ];
        header('Location: register.php');
        exit;
    }

    // 2. Hash kata sandi menggunakan algoritma BCRYPT bawaan PHP (password_hash)
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    // 3. Simpan data pengguna baru ke database PostgreSQL
    $sql = "INSERT INTO users (nama, username, password, role) VALUES (:nama, :username, :password, :role)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':nama'     => $nama,
        ':username' => $username,
        ':password' => $password_hash,
        ':role'     => $role
    ]);

    $_SESSION['flash'] = [
        'type'  => 'success',
        'pesan' => 'Pendaftaran akun berhasil! Silakan masuk menggunakan username dan kata sandi Anda.'
    ];
    header('Location: login.php');
    exit;

} catch (PDOException $e) {
    $_SESSION['flash'] = [
        'type'  => 'error',
        'pesan' => 'Gagal mendaftarkan akun ke database: ' . $e->getMessage()
    ];
    header('Location: register.php');
    exit;
}
