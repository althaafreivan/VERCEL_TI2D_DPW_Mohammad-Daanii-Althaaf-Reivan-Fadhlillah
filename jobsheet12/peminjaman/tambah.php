<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Guard Clause Autentikasi
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/koneksi.php';

$page_title = 'Peminjaman Lisensi Foto';
include __DIR__ . '/../includes/header.php';

$current_user_id = $_SESSION['user_id'];
$is_admin = ($_SESSION['role'] === 'admin');

// 1. Cek Validasi Bisnis (Tugas Mandiri):
// Anggota dengan peminjaman terlambat (> 14 hari & belum kembali) tidak bisa meminjam foto baru
$overdue_check = $pdo->prepare("
    SELECT COUNT(*) 
    FROM peminjaman 
    WHERE user_id = :uid 
      AND status = 'dipinjam' 
      AND tanggal_pinjam < (CURRENT_DATE - INTERVAL '14 days')
");
$overdue_check->execute([':uid' => $current_user_id]);
$has_overdue = ($overdue_check->fetchColumn() > 0);

// 2. Ambil daftar foto yang tersedia (stok > 0)
$daftar_foto = $pdo->query("
    SELECT id, judul, pengunggah, kategori, stok 
    FROM galeri 
    WHERE stok > 0 
    ORDER BY judul ASC
")->fetchAll(PDO::FETCH_ASSOC);

// 3. Ambil daftar anggota/pengguna jika admin
$daftar_users = [];
if ($is_admin) {
    $daftar_users = $pdo->query("SELECT id, nama, username, role FROM users ORDER BY nama ASC")->fetchAll(PDO::FETCH_ASSOC);
}

// Flash messages
$flash_error = $_SESSION['flash_error'] ?? null;
unset($_SESSION['flash_error']);
?>

<div class="upload-wrapper">
    <div class="page-topbar">
        <a href="riwayat.php" class="back-link">&larr; Kembali ke Riwayat</a>
        <span class="section-tag" style="margin: 0;">Jobsheet 12 &bull; Transaksi Terintegrasi</span>
    </div>

    <div class="form-container">
        <div class="form-header text-center">
            <span class="section-tag">Digital License Loan</span>
            <h2 class="form-title">Peminjaman Lisensi Karya Foto</h2>
            <p class="form-subtitle">
                Ajukan izin hak pakai aset digital foto dari PixelGallery untuk keperluan proyek desain atau portofolio Anda.
            </p>
        </div>

        <?php if ($flash_error): ?>
            <div class="nm-alert nm-alert-error">
                <span class="alert-icon">⚠️</span>
                <div class="alert-text"><?php echo e($flash_error); ?></div>
            </div>
        <?php endif; ?>

        <?php if ($has_overdue && !$is_admin): ?>
            <!-- Peringatan Aturan Bisnis Tugas Mandiri -->
            <div class="nm-alert nm-alert-error" style="border-left: 4px solid #ef4444; margin-bottom: 2rem;">
                <span class="alert-icon">⛔</span>
                <div class="alert-text">
                    <strong style="display: block; font-size: 1rem; margin-bottom: 0.25rem;">Akses Peminjaman Ditangguhkan!</strong>
                    Anda memiliki transaksi peminjaman foto yang telah melewati batas <strong>14 hari</strong> dan belum dikembalikan. 
                    Sesuai kebijakan lisensi sistem, Anda diwajibkan mengembalikan aset tersebut terlebih dahulu sebelum dapat meminjam karya baru.
                    <div style="margin-top: 0.75rem;">
                        <a href="riwayat.php" class="btn btn-secondary" style="font-size: 0.8rem; padding: 0.35rem 0.8rem;">
                            Buka Riwayat Pinjaman Saya &rarr;
                        </a>
                    </div>
                </div>
            </div>
        <?php else: ?>

            <form action="proses_tambah.php" method="POST" class="nm-form-card">
                <?php echo csrf_field(); ?>

                <!-- Peminjam / Anggota -->
                <div class="form-group">
                    <label for="user_id">Peminjam / Pengguna <span class="required-mark">*</span></label>
                    <?php if ($is_admin): ?>
                        <select name="user_id" id="user_id" class="nm-select" required>
                            <option value="">-- Pilih Anggota / Pengguna --</option>
                            <?php foreach ($daftar_users as $u): ?>
                                <option value="<?php echo (int)$u['id']; ?>" <?php echo ((int)$u['id'] === (int)$current_user_id) ? 'selected' : ''; ?>>
                                    <?php echo e($u['nama']); ?> (@<?php echo e($u['username']); ?> - <?php echo strtoupper(e($u['role'])); ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <span class="field-hint">Sebagai Admin, Anda dapat mencatatkan peminjaman atas nama pengguna lain.</span>
                    <?php else: ?>
                        <input type="hidden" name="user_id" value="<?php echo (int)$current_user_id; ?>">
                        <input type="text" class="nm-input" value="<?php echo e($_SESSION['nama']); ?> (@<?php echo e($_SESSION['username']); ?>)" readonly style="background: rgba(0,0,0,0.03);">
                        <span class="field-hint">Peminjaman dicatat secara otomatis atas nama akun Anda yang sedang aktif.</span>
                    <?php endif; ?>
                </div>

                <!-- Pilihan Foto Galeri -->
                <div class="form-group">
                    <label for="galeri_id">Pilih Karya Foto (Stok Tersedia) <span class="required-mark">*</span></label>
                    <?php if (empty($daftar_foto)): ?>
                        <div style="padding: 1rem; background: #fff5f5; border-radius: var(--radius-sm); color: #dc2626; font-size: 0.85rem;">
                            Saat ini seluruh stok lisensi karya foto sedang habis dipinjam.
                        </div>
                    <?php else: ?>
                        <select name="galeri_id" id="galeri_id" class="nm-select" required>
                            <option value="">-- Pilih Foto dari Katalog --</option>
                            <?php foreach ($daftar_foto as $f): ?>
                                <option value="<?php echo (int)$f['id']; ?>">
                                    <?php echo e($f['judul']); ?> &bull; Kategori: <?php echo e($f['kategori']); ?> (Sisa Kuota: <?php echo (int)$f['stok']; ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <span class="field-hint">Hanya menampilkan karya foto dengan kuota lisensi tersisa (stok &gt; 0).</span>
                    <?php endif; ?>
                </div>

                <!-- Info Tanggal Peminjaman -->
                <div class="form-grid-2">
                    <div class="form-group">
                        <label>Tanggal Pinjam</label>
                        <input type="text" class="nm-input" value="<?php echo date('Y-m-d'); ?> (Hari ini)" readonly style="background: rgba(0,0,0,0.03);">
                        <span class="field-hint">Ditetapkan otomatis sesuai tanggal transaksi saat ini.</span>
                    </div>

                    <div class="form-group">
                        <label>Batas Maksimal Pengembalian</label>
                        <input type="text" class="nm-input" value="<?php echo date('Y-m-d', strtotime('+14 days')); ?> (14 Hari)" readonly style="background: rgba(0,0,0,0.03);">
                        <span class="field-hint">Durasi standar peminjaman lisensi foto digital.</span>
                    </div>
                </div>

                <!-- Catatan / Tujuan Penggunaan -->
                <div class="form-group">
                    <label for="catatan">Catatan / Keperluan Penggunaan</label>
                    <input type="text" name="catatan" id="catatan" class="nm-input" 
                           placeholder="Contoh: Desain Web Landing Page Klien, Proyek Kampus, dll." maxlength="255">
                    <span class="field-hint">Opsional: Deskripsikan rencana pemanfaatan karya foto.</span>
                </div>

                <!-- Tombol Submit -->
                <div class="form-actions">
                    <a href="riwayat.php" class="btn btn-secondary">Batal</a>
                    <?php if (!empty($daftar_foto)): ?>
                        <button type="submit" class="btn btn-primary">
                            <span>Konfirmasi Peminjaman Lisensi</span>
                            <span class="btn-arrow">&rarr;</span>
                        </button>
                    <?php endif; ?>
                </div>
            </form>

        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
