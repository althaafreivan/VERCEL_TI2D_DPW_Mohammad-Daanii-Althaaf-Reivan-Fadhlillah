<?php
session_start();
require __DIR__ . '/../includes/koneksi.php';

$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$judul = trim($_POST['judul'] ?? '');
$pengarang = trim($_POST['pengarang'] ?? '');
$tahun = $_POST['tahun'] ?? '';
$isbn = trim($_POST['isbn'] ?? '');
$stok = $_POST['stok'] ?? '';
$kategori = trim($_POST['kategori'] ?? '');

$errors = [];
if ($id <= 0) {
    $errors[] = "ID buku tidak valid.";
}
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
        header("Location: edit.php?id=$id");
    } else {
        echo "<script>location.replace('edit.php?id=$id');</script>";
    }
    exit;
}

try {
    $stmt = $pdo->prepare(
        "UPDATE buku SET judul = :judul, pengarang = :pengarang, tahun = :tahun, isbn = :isbn, stok = :stok, kategori = :kategori WHERE id = :id"
    );
    $stmt->execute([
        'id' => $id,
        'judul' => $judul,
        'pengarang' => $pengarang,
        'tahun' => (int)$tahun,
        'isbn' => $isbn,
        'stok' => (int)$stok,
        'kategori' => $kategori,
    ]);

    $_SESSION['flash'] = ['type' => 'success', 'pesan' => 'Data buku berhasil diperbarui.'];
    setcookie('flash_type', 'success', time() + 30, '/');
    setcookie('flash_pesan', 'Data buku berhasil diperbarui.', time() + 30, '/');
    if (!headers_sent()) {
        header('Location: list.php');
    } else {
        echo "<script>location.replace('list.php');</script>";
    }
} catch (Throwable $e) {
    $pesan = 'Gagal memperbarui buku: ' . $e->getMessage();
    $_SESSION['flash'] = ['type' => 'error', 'pesan' => $pesan];
    setcookie('flash_type', 'error', time() + 30, '/');
    setcookie('flash_pesan', $pesan, time() + 30, '/');
    if (!headers_sent()) {
        header("Location: edit.php?id=$id");
    } else {
        echo "<script>location.replace('edit.php?id=$id');</script>";
    }
}
exit;
