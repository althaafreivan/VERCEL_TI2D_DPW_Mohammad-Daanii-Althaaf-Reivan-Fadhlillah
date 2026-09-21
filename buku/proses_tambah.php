<?php
session_start();
require __DIR__ . '/../includes/koneksi.php';

$judul = trim($_POST['judul'] ?? '');
$pengarang = trim($_POST['pengarang'] ?? '');
$tahun = $_POST['tahun'] ?? '';
$isbn = trim($_POST['isbn'] ?? '');
$stok = $_POST['stok'] ?? '';
$kategori = trim($_POST['kategori'] ?? '');

// Validasi server-side — wajib ada meski sudah divalidasi JS di Jobsheet 5,
// karena validasi client bisa dilewati (nonaktifkan JS / kirim request manual).
$errors = [];
if ($judul === '') {
    $errors[] = "Judul wajib diisi.";
}
if ($pengarang === '') {
    $errors[] = "Pengarang wajib diisi.";
}
if (!is_numeric($tahun) || $tahun < 1900 || $tahun > 2026) {
    $errors[] = "Tahun harus di antara 1900-2026.";
}
if (!is_numeric($stok) || $stok < 0) {
    $errors[] = "Stok tidak boleh negatif.";
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
        "INSERT INTO buku (judul, pengarang, tahun, isbn, stok, kategori)
         VALUES (:judul, :pengarang, :tahun, :isbn, :stok, :kategori)
         RETURNING id"
    );
    $stmt->execute([
        'judul' => $judul,
        'pengarang' => $pengarang,
        'tahun' => (int) $tahun,
        'isbn' => $isbn,
        'stok' => (int) $stok,
        'kategori' => $kategori,
    ]);

    $_SESSION['flash'] = ['type' => 'success', 'pesan' => 'Buku berhasil ditambahkan.'];
    setcookie('flash_type', 'success', time() + 30, '/');
    setcookie('flash_pesan', 'Buku berhasil ditambahkan.', time() + 30, '/');
    if (!headers_sent()) {
        header('Location: list.php');
    } else {
        echo "<script>location.replace('list.php');</script>";
    }
} catch (Throwable $e) {
    $pesan = 'Gagal menambahkan buku: ' . $e->getMessage();
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
