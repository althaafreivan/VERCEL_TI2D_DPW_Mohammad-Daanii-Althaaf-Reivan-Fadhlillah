<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Guard Clause Autentikasi
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/koneksi.php';
require_once __DIR__ . '/../includes/csrf.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: riwayat.php');
    exit;
}

// 1. Proteksi CSRF (Jobsheet 11)
if (!csrf_verify()) {
    $_SESSION['flash'] = [
        'type'  => 'error',
        'pesan' => 'Keamanan: Token CSRF tidak valid atau sesi Anda telah kadaluarsa!'
    ];
    header('Location: riwayat.php');
    exit;
}

$is_admin = ($_SESSION['role'] === 'admin');
$target_user_id = $is_admin 
    ? filter_var($_POST['user_id'] ?? 0, FILTER_VALIDATE_INT) 
    : (int)$_SESSION['user_id'];

$galeri_id = filter_var($_POST['galeri_id'] ?? 0, FILTER_VALIDATE_INT);
$catatan   = trim($_POST['catatan'] ?? '');

if (!$target_user_id || !$galeri_id) {
    $_SESSION['flash_error'] = 'Harap pilih peminjam dan karya foto yang valid!';
    header('Location: tambah.php');
    exit;
}

// 2. Validasi Aturan Bisnis (Tugas Mandiri Jobsheet 12):
// Anggota dengan peminjaman terlambat (> 14 hari & belum kembali) tidak bisa meminjam foto baru
try {
    $cekOverdue = $pdo->prepare("
        SELECT COUNT(*) 
        FROM peminjaman 
        WHERE user_id = :uid 
          AND status = 'dipinjam' 
          AND tanggal_pinjam < (CURRENT_DATE - INTERVAL '14 days')
    ");
    $cekOverdue->execute([':uid' => $target_user_id]);
    if ($cekOverdue->fetchColumn() > 0) {
        $_SESSION['flash_error'] = 'Peminjaman ditolak! Pengguna ini memiliki transaksi peminjaman yang terlambat lebih dari 14 hari dan belum dikembalikan.';
        header('Location: tambah.php');
        exit;
    }
} catch (PDOException $e) {
    $_SESSION['flash_error'] = 'Kesalahan pengecekan aturan bisnis: ' . $e->getMessage();
    header('Location: tambah.php');
    exit;
}

// 3. Eksekusi Transaksi Database Terintegrasi (ACID Transaction)
try {
    $pdo->beginTransaction();

    // Kunci baris galeri dan periksa stok
    $stmtCek = $pdo->prepare("SELECT id, judul, stok FROM galeri WHERE id = :id FOR UPDATE");
    $stmtCek->execute([':id' => $galeri_id]);
    $foto = $stmtCek->fetch(PDO::FETCH_ASSOC);

    if (!$foto) {
        throw new Exception("Karya foto yang dipilih tidak ditemukan dalam database.");
    }

    if ($foto['stok'] <= 0) {
        throw new Exception("Kuota lisensi untuk karya '" . $foto['judul'] . "' saat ini sedang habis.");
    }

    // Insert rekaman peminjaman baru
    $stmtInsert = $pdo->prepare("
        INSERT INTO peminjaman (galeri_id, user_id, tanggal_pinjam, status, catatan)
        VALUES (:gid, :uid, CURRENT_DATE, 'dipinjam', :catatan)
    ");
    $stmtInsert->execute([
        ':gid'     => $galeri_id,
        ':uid'     => $target_user_id,
        ':catatan' => $catatan
    ]);

    // Kurangi kuota stok lisensi galeri (stok = stok - 1)
    $stmtUpdateStok = $pdo->prepare("UPDATE galeri SET stok = stok - 1 WHERE id = :id");
    $stmtUpdateStok->execute([':id' => $galeri_id]);

    // Commit transaksi jika semua langkah berhasil tanpa kendala
    $pdo->commit();

    $_SESSION['flash'] = [
        'type'  => 'success',
        'pesan' => "Peminjaman lisensi karya '<strong>" . htmlspecialchars($foto['judul']) . "</strong>' berhasil dicatat! Sisa kuota: " . ($foto['stok'] - 1)
    ];
    header('Location: riwayat.php');
    exit;

} catch (Exception $e) {
    // Rollback transaksi jika terjadi galat agar data tetap konsisten
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    $_SESSION['flash_error'] = 'Transaksi database gagal: ' . $e->getMessage();
    header('Location: tambah.php');
    exit;
}
