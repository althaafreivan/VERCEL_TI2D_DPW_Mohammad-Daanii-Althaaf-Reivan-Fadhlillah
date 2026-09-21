<?php
session_start();
require __DIR__ . '/../includes/koneksi.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id > 0) {
    try {
        $stmt = $pdo->prepare("DELETE FROM buku WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $_SESSION['flash'] = ['type' => 'success', 'pesan' => 'Buku berhasil dihapus.'];
        setcookie('flash_type', 'success', time() + 30, '/');
        setcookie('flash_pesan', 'Buku berhasil dihapus.', time() + 30, '/');
    } catch (Throwable $e) {
        $_SESSION['flash'] = ['type' => 'error', 'pesan' => 'Gagal menghapus buku: ' . $e->getMessage()];
        setcookie('flash_type', 'error', time() + 30, '/');
        setcookie('flash_pesan', 'Gagal menghapus buku: ' . $e->getMessage(), time() + 30, '/');
    }
}

if (!headers_sent()) {
    header('Location: list.php');
} else {
    echo "<script>location.replace('list.php');</script>";
}
exit;
