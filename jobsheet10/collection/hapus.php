<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Guard Clause Autentikasi (Jobsheet 10)
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/koneksi.php';

// Wajib menggunakan metode HTTP POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['flash'] = [
        'type'  => 'error',
        'pesan' => 'Metode permintaan tidak diizinkan. Penghapusan harus menggunakan metode POST.'
    ];
    header('Location: catalog.php');
    exit;
}

$id = filter_var($_POST['id'] ?? 0, FILTER_VALIDATE_INT);

if (!$id) {
    $_SESSION['flash'] = [
        'type'  => 'error',
        'pesan' => 'ID gambar yang ingin dihapus tidak valid.'
    ];
    header('Location: catalog.php');
    exit;
}

try {
    // 1. Cek data dan hak kepemilikan (Role-based Authorization)
    $cek = $pdo->prepare("SELECT judul, pengunggah, file_gambar FROM galeri WHERE id = :id");
    $cek->execute([':id' => $id]);
    $foto = $cek->fetch(PDO::FETCH_ASSOC);

    if (!$foto) {
        $_SESSION['flash'] = [
            'type'  => 'error',
            'pesan' => 'Data gambar tidak ditemukan atau telah dihapus sebelumnya.'
        ];
        header('Location: catalog.php');
        exit;
    }

    $is_admin = ($_SESSION['role'] === 'admin');
    $is_owner = (strtolower(trim($foto['pengunggah'])) === strtolower(trim($_SESSION['nama'] ?? '')));

    if (!$is_admin && !$is_owner) {
        $_SESSION['flash'] = [
            'type'  => 'error',
            'pesan' => 'Akses ditolak! Anda tidak memiliki wewenang untuk menghapus karya milik pengguna lain.'
        ];
        header('Location: catalog.php');
        exit;
    }

    // Jika ada salinan fisik lokal di assets/uploads/, bersihkan
    if (!empty($foto['file_gambar']) && !str_starts_with($foto['file_gambar'], 'data:')) {
        $file_path = __DIR__ . '/../assets/uploads/' . $foto['file_gambar'];
        if (file_exists($file_path)) {
            @unlink($file_path);
        }
    }

    // 2. Eksekusi DELETE via Prepared Statement
    $stmt = $pdo->prepare("DELETE FROM galeri WHERE id = :id");
    $stmt->execute([':id' => $id]);

    $_SESSION['flash'] = [
        'type'  => 'success',
        'pesan' => "Gambar '<strong>" . htmlspecialchars($foto['judul']) . "</strong>' berhasil dihapus dari galeri!"
    ];

} catch (PDOException $e) {
    $_SESSION['flash'] = [
        'type'  => 'error',
        'pesan' => 'Gagal menghapus gambar dari database: ' . $e->getMessage()
    ];
}

header('Location: catalog.php');
exit;
