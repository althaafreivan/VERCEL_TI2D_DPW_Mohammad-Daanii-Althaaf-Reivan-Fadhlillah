<?php
session_start();
require __DIR__ . '/../includes/koneksi.php';

$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$nama = trim($_POST['nama'] ?? '');
$noAnggota = trim($_POST['no_anggota'] ?? '');
$alamat = trim($_POST['alamat'] ?? '');
$noHp = trim($_POST['no_hp'] ?? '');

$errors = [];
if ($id <= 0) {
    $errors[] = "ID anggota tidak valid.";
}
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
        header("Location: edit.php?id=$id");
    } else {
        echo "<script>location.replace('edit.php?id=$id');</script>";
    }
    exit;
}

try {
    $stmt = $pdo->prepare(
        "UPDATE anggota SET nama = :nama, no_anggota = :no_anggota, alamat = :alamat, no_hp = :no_hp WHERE id = :id"
    );
    $stmt->execute([
        'id' => $id,
        'nama' => $nama,
        'no_anggota' => $noAnggota,
        'alamat' => $alamat,
        'no_hp' => $noHp,
    ]);

    $_SESSION['flash'] = ['type' => 'success', 'pesan' => 'Data anggota berhasil diperbarui.'];
    setcookie('flash_type', 'success', time() + 30, '/');
    setcookie('flash_pesan', 'Data anggota berhasil diperbarui.', time() + 30, '/');
    if (!headers_sent()) {
        header('Location: list.php');
    } else {
        echo "<script>location.replace('list.php');</script>";
    }
} catch (Throwable $e) {
    $msg = $e->getMessage();
    if (str_contains(strtolower($msg), 'unique') || str_contains(strtolower($msg), 'duplicate') || str_contains($msg, '23505')) {
        $pesan = "Gagal: No. Anggota '$noAnggota' sudah terdaftar pada anggota lain!";
    } else {
        $pesan = "Gagal memperbarui anggota: $msg";
    }
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
