<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Guard Clause Autentikasi
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/koneksi.php';

$page_title = 'Riwayat Peminjaman Lisensi';
include __DIR__ . '/../includes/header.php';

$current_user_id = $_SESSION['user_id'];
$is_admin = ($_SESSION['role'] === 'admin');

// Flash message
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

// Query JOIN 3 tabel (peminjaman, galeri, users)
try {
    $sql = "
        SELECT p.id, p.tanggal_pinjam, p.tanggal_kembali, p.status, p.catatan,
               g.id AS galeri_id, g.judul AS judul_foto, g.kategori,
               (CASE WHEN g.file_gambar IS NOT NULL AND g.file_gambar != '' THEN 1 ELSE 0 END) AS ada_gambar,
               u.id AS user_id, u.nama AS nama_peminjam, u.username, u.role,
               (CURRENT_DATE - p.tanggal_pinjam) AS lama_hari
        FROM peminjaman p
        JOIN galeri g ON p.galeri_id = g.id
        JOIN users u ON p.user_id = u.id
    ";

    // Jika pengguna biasa, utamakan transaksi miliknya (atau tampilkan semua dengan highlight)
    // Sesuai modul: histori per anggota / semua untuk admin
    if (!$is_admin && isset($_GET['filter']) && $_GET['filter'] === 'all') {
        // Tampilkan semua
        $sql .= " ORDER BY p.id DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
    } elseif (!$is_admin) {
        $sql .= " WHERE p.user_id = :uid ORDER BY p.id DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':uid' => $current_user_id]);
    } else {
        $sql .= " ORDER BY p.id DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
    }

    $riwayat = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $riwayat = [];
    $flash = ['type' => 'error', 'pesan' => 'Gagal memuat riwayat: ' . $e->getMessage()];
}
?>

<div class="catalog-wrapper">
    <div class="catalog-header">
        <div>
            <span class="section-tag">Jobsheet 12 &bull; Integrasi Front-End &amp; Back-End</span>
            <h1 class="catalog-title">Riwayat Transaksi Lisensi Foto</h1>
            <p class="catalog-subtitle">
                Pencatatan peminjaman hak pakai aset visual digital terintegrasi (Relasi 3 Tabel: <code>peminjaman</code> &bull; <code>galeri</code> &bull; <code>users</code>).
            </p>
        </div>

        <div style="display: flex; gap: 0.75rem; align-items: center; flex-wrap: wrap;">
            <?php if (!$is_admin): ?>
                <?php if (isset($_GET['filter']) && $_GET['filter'] === 'all'): ?>
                    <a href="riwayat.php" class="btn btn-secondary" style="font-size: 0.85rem; padding: 0.5rem 1rem;">
                        Tampilkan Milik Saya Saja
                    </a>
                <?php else: ?>
                    <a href="riwayat.php?filter=all" class="btn btn-secondary" style="font-size: 0.85rem; padding: 0.5rem 1rem;">
                        Lihat Semua Riwayat
                    </a>
                <?php endif; ?>
            <?php endif; ?>

            <a href="tambah.php" class="btn btn-primary" style="font-size: 0.85rem; padding: 0.5rem 1rem;">
                <span>+ Pinjam Lisensi Foto</span>
            </a>
        </div>
    </div>

    <?php if ($flash): ?>
        <div class="nm-alert nm-alert-<?php echo ($flash['type'] === 'success') ? 'success' : 'error'; ?>" style="margin-bottom: 1.5rem;">
            <span class="alert-icon"><?php echo ($flash['type'] === 'success') ? '✅' : '⚠️'; ?></span>
            <div class="alert-text"><?php echo $flash['pesan']; ?></div>
        </div>
    <?php endif; ?>

    <?php if (empty($riwayat)): ?>
        <div class="nm-form-card text-center" style="padding: 3rem 1rem; margin-top: 1rem;">
            <p style="font-size: 1.1rem; color: var(--text-muted); margin-bottom: 1rem;">
                Belum ada transaksi peminjaman lisensi foto yang tercatat.
            </p>
            <a href="tambah.php" class="btn btn-primary" style="display: inline-flex;">
                Mulai Pinjam Lisensi Pertama &rarr;
            </a>
        </div>
    <?php else: ?>
        <div class="nm-form-card" style="padding: 1rem; overflow-x: auto; margin-top: 1rem;">
            <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.875rem;">
                <thead>
                    <tr style="border-bottom: 2px solid var(--border-subtle); color: var(--text-muted); font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.05em;">
                        <th style="padding: 0.75rem 1rem;">ID</th>
                        <th style="padding: 0.75rem 1rem;">Karya Foto</th>
                        <th style="padding: 0.75rem 1rem;">Peminjam</th>
                        <th style="padding: 0.75rem 1rem;">Tanggal Pinjam</th>
                        <th style="padding: 0.75rem 1rem;">Tanggal Kembali</th>
                        <th style="padding: 0.75rem 1rem;">Status</th>
                        <th style="padding: 0.75rem 1rem;">Catatan</th>
                        <th style="padding: 0.75rem 1rem; text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($riwayat as $row): ?>
                        <?php 
                            $is_active = ($row['status'] === 'dipinjam');
                            $is_overdue = ($is_active && (int)$row['lama_hari'] > 14);
                            $can_return = ($is_admin || (int)$row['user_id'] === (int)$current_user_id);
                        ?>
                        <tr style="border-bottom: 1px solid var(--border-subtle); transition: background 0.2s ease;">
                            <td style="padding: 0.85rem 1rem; font-weight: 700; color: var(--text-muted);">
                                #<?php echo (int)$row['id']; ?>
                            </td>

                            <td style="padding: 0.85rem 1rem;">
                                <div style="display: flex; align-items: center; gap: 0.75rem;">
                                    <?php if (!empty($row['ada_gambar'])): ?>
                                        <img src="../gambar.php?id=<?php echo (int)$row['galeri_id']; ?>" 
                                             alt="<?php echo e($row['judul_foto']); ?>" 
                                             style="width: 44px; height: 44px; object-fit: cover; border-radius: 8px; box-shadow: var(--nm-shadow-flat);" loading="lazy">
                                    <?php else: ?>
                                        <div style="width: 44px; height: 44px; display: flex; align-items: center; justify-content: center; background: rgba(0,0,0,0.05); border-radius: 8px;">🖼️</div>
                                    <?php endif; ?>
                                    <div>
                                        <div style="font-weight: 700; color: var(--text-primary);">
                                            <?php echo e($row['judul_foto']); ?>
                                        </div>
                                        <span class="cat-pill" style="font-size: 0.65rem; padding: 0.1rem 0.4rem;">
                                            <?php echo e($row['kategori']); ?>
                                        </span>
                                    </div>
                                </div>
                            </td>

                            <td style="padding: 0.85rem 1rem;">
                                <div style="font-weight: 600;"><?php echo e($row['nama_peminjam']); ?></div>
                                <div style="font-size: 0.75rem; color: var(--text-muted);">@<?php echo e($row['username']); ?></div>
                            </td>

                            <td style="padding: 0.85rem 1rem;">
                                <div><?php echo e($row['tanggal_pinjam']); ?></div>
                                <?php if ($is_active): ?>
                                    <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.2rem;">
                                        Durasi: <strong><?php echo (int)$row['lama_hari']; ?> hari</strong>
                                    </div>
                                <?php endif; ?>
                            </td>

                            <td style="padding: 0.85rem 1rem;">
                                <?php if (!empty($row['tanggal_kembali'])): ?>
                                    <span style="color: #10b981; font-weight: 600;"><?php echo e($row['tanggal_kembali']); ?></span>
                                <?php else: ?>
                                    <span style="color: var(--text-light);">&mdash;</span>
                                <?php endif; ?>
                            </td>

                            <td style="padding: 0.85rem 1rem;">
                                <?php if ($is_active): ?>
                                    <?php if ($is_overdue): ?>
                                        <span style="background: #fee2e2; color: #ef4444; padding: 0.2rem 0.6rem; border-radius: 999px; font-size: 0.725rem; font-weight: 700; display: inline-flex; align-items: center; gap: 0.25rem;">
                                            ⚠️ Terlambat (<?php echo (int)$row['lama_hari']; ?> Hari)
                                        </span>
                                    <?php else: ?>
                                        <span style="background: #e0f2fe; color: #0284c7; padding: 0.2rem 0.6rem; border-radius: 999px; font-size: 0.725rem; font-weight: 700;">
                                            Dipinjam
                                        </span>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span style="background: #dcfce7; color: #16a34a; padding: 0.2rem 0.6rem; border-radius: 999px; font-size: 0.725rem; font-weight: 700;">
                                        ✓ Dikembalikan
                                    </span>
                                <?php endif; ?>
                            </td>

                            <td style="padding: 0.85rem 1rem; color: var(--text-muted); font-size: 0.8rem; max-width: 180px;">
                                <?php echo !empty($row['catatan']) ? e($row['catatan']) : '<em style="color: var(--text-light);">Tidak ada</em>'; ?>
                            </td>

                            <td style="padding: 0.85rem 1rem; text-align: right;">
                                <?php if ($is_active && $can_return): ?>
                                    <form action="kembali.php" method="POST" 
                                          onsubmit="return confirm('Apakah Anda yakin ingin memproses pengembalian lisensi untuk foto \'<?php echo addslashes(htmlspecialchars($row['judul_foto'])); ?>\'?');" 
                                          style="display: inline; margin: 0;">
                                        <?php echo csrf_field(); ?>
                                        <input type="hidden" name="id" value="<?php echo (int)$row['id']; ?>">
                                        <button type="submit" class="btn-card" 
                                                style="font-size: 0.75rem; padding: 0.35rem 0.75rem; background: #10b981; color: white; border: none; cursor: pointer; border-radius: var(--radius-sm);">
                                            Kembalikan
                                        </button>
                                    </form>
                                <?php elseif ($is_active): ?>
                                    <span style="font-size: 0.725rem; color: var(--text-light);">Dipinjam user</span>
                                <?php else: ?>
                                    <span style="font-size: 0.75rem; color: #16a34a; font-weight: 600;">Selesai</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
