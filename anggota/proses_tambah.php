<?php
session_start();
require __DIR__ . '/../includes/koneksi.php';

$nama = trim($_POST['nama'] ?? '');
$noAnggota = trim($_POST['no_anggota'] ?? '');
$alamat = trim($_POST['alamat'] ?? '');
$noHp = trim($_POST['no_hp'] ?? '');

$errors = [];
if ($nama === '') {
    $errors[] = "Nama wajib diisi.";
}
if ($noAnggota === '') {
    $errors[] = "No. Anggota wajib diisi.";
}

if (!empty($errors)) {
    $pesan = implode(' ', $errors);
    $_SESSION['flash'] = ['type' => 'error', 'pesan' => $pesan];
    setcookie('flash_type', 'error', time() + 30, '/');
    setcookie('flash_pesan', $pesan, time() + 30, '/');
    if (!headers_sent()) {
        header('Location: tambah.php');
    } else {
        echo "<script>location.replace('tambah.php');</script>";
    }
    exit;
}

try {
    $stmt = $pdo->prepare(
        "INSERT INTO anggota (nama, no_anggota, alamat, no_hp)
         VALUES (:nama, :no_anggota, :alamat, :no_hp)
         RETURNING id"
    );
    $stmt->execute([
        'nama' => $nama,
        'no_anggota' => $noAnggota,
        'alamat' => $alamat,
        'no_hp' => $noHp,
    ]);

    $_SESSION['flash'] = ['type' => 'success', 'pesan' => 'Anggota berhasil ditambahkan.'];
    setcookie('flash_type', 'success', time() + 30, '/');
    setcookie('flash_pesan', 'Anggota berhasil ditambahkan.', time() + 30, '/');
    if (!headers_sent()) {
        header('Location: list.php');
    } else {
        echo "<script>location.replace('list.php');</script>";
    }
} catch (Throwable $e) {
    $msg = $e->getMessage();
    if (str_contains(strtolower($msg), 'unique') || str_contains(strtolower($msg), 'duplicate') || str_contains($msg, '23505')) {
        $pesan = "Gagal: No. Anggota '$noAnggota' sudah terdaftar! Gunakan nomor lain.";
    } else {
        $pesan = "Gagal menambahkan anggota: $msg";
    }
    $_SESSION['flash'] = ['type' => 'error', 'pesan' => $pesan];
    setcookie('flash_type', 'error', time() + 30, '/');
    setcookie('flash_pesan', $pesan, time() + 30, '/');
    if (!headers_sent()) {
        header('Location: tambah.php');
    } else {
        echo "<script>location.replace('tambah.php');</script>";
    }
}
exit;
