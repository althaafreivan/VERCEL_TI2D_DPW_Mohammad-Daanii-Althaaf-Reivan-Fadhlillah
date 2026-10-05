<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Guard Clause Autentikasi
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/koneksi.php';
require_once __DIR__ . '/../includes/csrf.php';

// Jika request POST: Eksekusi pengembalian lisensi
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Verifikasi CSRF Token
    if (!csrf_verify()) {
        $_SESSION['flash'] = [
            'type'  => 'error',
            'pesan' => 'Keamanan: Token CSRF tidak valid atau sesi telah kadaluarsa!'
        ];
        header('Location: riwayat.php');
        exit;
    }

    $id = filter_var($_POST['id'] ?? 0, FILTER_VALIDATE_INT);
    if (!$id) {
        $_SESSION['flash'] = ['type' => 'error', 'pesan' => 'ID peminjaman tidak valid.'];
        header('Location: riwayat.php');
        exit;
    }

    // 2. Eksekusi Transaksi Pengembalian (ACID)
    try {
        $pdo->beginTransaction();

        // Kunci baris peminjaman
        $stmtCek = $pdo->prepare("
            SELECT p.id, p.galeri_id, p.user_id, p.status, g.judul 
            FROM peminjaman p
            JOIN galeri g ON p.galeri_id = g.id
            WHERE p.id = :id 
            FOR UPDATE
        ");
        $stmtCek->execute([':id' => $id]);
        $pinjam = $stmtCek->fetch(PDO::FETCH_ASSOC);

        if (!$pinjam) {
            throw new Exception("Data transaksi peminjaman tidak ditemukan.");
        }

        if ($pinjam['status'] === 'dikembalikan') {
            throw new Exception("Transaksi lisensi ini telah dikembalikan sebelumnya.");
        }

        // Cek Hak Akses: Admin atau pemilik peminjaman itu sendiri
        $is_admin = ($_SESSION['role'] === 'admin');
        $is_borrower = ((int)$pinjam['user_id'] === (int)$_SESSION['user_id']);

        if (!$is_admin && !$is_borrower) {
            throw new Exception("Akses ditolak! Anda tidak memiliki hak untuk mengembalikan transaksi pengguna lain.");
        }

        // Update status peminjaman menjadi 'dikembalikan' dan catat tanggal hari ini
        $stmtKembali = $pdo->prepare("
            UPDATE peminjaman 
            SET status = 'dikembalikan', 
                tanggal_kembali = CURRENT_DATE 
            WHERE id = :id
        ");
        $stmtKembali->execute([':id' => $id]);

        // Tambah kembali stok lisensi foto di galeri (stok = stok + 1)
        $stmtRestock = $pdo->prepare("UPDATE galeri SET stok = stok + 1 WHERE id = :gid");
        $stmtRestock->execute([':gid' => $pinjam['galeri_id']]);

        $pdo->commit();

        $_SESSION['flash'] = [
            'type'  => 'success',
            'pesan' => "Lisensi karya '<strong>" . htmlspecialchars($pinjam['judul']) . "</strong>' berhasil dikembalikan. Kuota aset telah dipulihkan!"
        ];
        header('Location: riwayat.php');
        exit;

    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        $_SESSION['flash'] = [
            'type'  => 'error',
            'pesan' => 'Gagal memproses pengembalian: ' . $e->getMessage()
        ];
        header('Location: riwayat.php');
        exit;
    }
}

// Jika request GET: Tampilkan konfirmasi pengembalian jika diakses langsung
$id = filter_var($_GET['id'] ?? 0, FILTER_VALIDATE_INT);
if (!$id) {
    header('Location: riwayat.php');
    exit;
}

$stmt = $pdo->prepare("
    SELECT p.id, p.tanggal_pinjam, p.status, p.catatan, g.judul, g.kategori, u.nama AS nama_peminjam
    FROM peminjaman p
    JOIN galeri g ON p.galeri_id = g.id
    JOIN users u ON p.user_id = u.id
    WHERE p.id = :id
");
$stmt->execute([':id' => $id]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$data) {
    header('Location: riwayat.php');
    exit;
}

$page_title = 'Konfirmasi Pengembalian Lisensi';
include __DIR__ . '/../includes/header.php';
?>

<div class="upload-wrapper">
    <div class="page-topbar">
        <a href="riwayat.php" class="back-link">&larr; Kembali ke Riwayat</a>
        <span class="section-tag" style="margin: 0;">Jobsheet 12 &bull; Pengembalian</span>
    </div>

    <div class="form-container">
        <div class="form-header text-center">
            <span class="section-tag">Asset Return</span>
            <h2 class="form-title">Konfirmasi Pengembalian</h2>
            <p class="form-subtitle">Pulihkan kuota lisensi aset foto digital ke galeri.</p>
        </div>

        <div class="nm-form-card">
            <p style="margin-bottom: 1.25rem;">
                Apakah Anda yakin ingin mengembalikan lisensi untuk karya foto 
                <strong>"<?php echo e($data['judul']); ?>"</strong> yang dipinjam oleh 
                <strong><?php echo e($data['nama_peminjam']); ?></strong> pada tanggal 
                <strong><?php echo e($data['tanggal_pinjam']); ?></strong>?
            </p>

            <form action="kembali.php" method="POST">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="id" value="<?php echo (int)$data['id']; ?>">
                
                <div class="form-actions">
                    <a href="riwayat.php" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary" style="background: #10b981; color: #fff;">
                        <span>Ya, Kembalikan Lisensi Sekarang</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
