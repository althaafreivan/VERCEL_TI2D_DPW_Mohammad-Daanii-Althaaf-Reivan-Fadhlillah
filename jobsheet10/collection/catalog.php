<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../includes/koneksi.php';

$page_title = 'Galeri Foto (Autentikasi & Hak Akses)';
include __DIR__ . '/../includes/header.php';

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

$is_logged_in = isset($_SESSION['user_id']);
$current_user = $_SESSION['nama'] ?? '';
$is_admin     = ($_SESSION['role'] ?? '') === 'admin';

// 1. Parameter Pencarian Server-side
$cari = trim($_GET['q'] ?? '');

// 2. Parameter Pagination Server-side
$limit = 6;
$page  = max(1, filter_var($_GET['page'] ?? 1, FILTER_VALIDATE_INT) ?: 1);

// 3. Hitung Total Data
try {
    if ($cari !== '') {
        $sql_count = "SELECT COUNT(*) FROM galeri 
                      WHERE judul ILIKE :q OR pengunggah ILIKE :q OR kategori ILIKE :q";
        $stmt_count = $pdo->prepare($sql_count);
        $stmt_count->execute([':q' => "%$cari%"]);
        $total_data = (int)$stmt_count->fetchColumn();
    } else {
        $sql_count = "SELECT COUNT(*) FROM galeri";
        $total_data = (int)$pdo->query($sql_count)->fetchColumn();
    }
} catch (PDOException $e) {
    $total_data = 0;
}

$total_halaman = max(1, ceil($total_data / $limit));
if ($page > $total_halaman) {
    $page = $total_halaman;
}
$offset = ($page - 1) * $limit;

// 4. Query Data dengan LIMIT & OFFSET
try {
    if ($cari !== '') {
        $sql = "SELECT id, judul, pengunggah, kategori, tahun, deskripsi, 
                       (CASE WHEN file_gambar IS NOT NULL AND file_gambar != '' THEN 1 ELSE 0 END) AS ada_gambar 
                FROM galeri 
                WHERE judul ILIKE :q OR pengunggah ILIKE :q OR kategori ILIKE :q 
                ORDER BY id DESC 
                LIMIT :limit OFFSET :offset";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':q', "%$cari%", PDO::PARAM_STR);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $daftar_gambar = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $sql = "SELECT id, judul, pengunggah, kategori, tahun, deskripsi, 
                       (CASE WHEN file_gambar IS NOT NULL AND file_gambar != '' THEN 1 ELSE 0 END) AS ada_gambar 
                FROM galeri 
                ORDER BY id DESC 
                LIMIT :limit OFFSET :offset";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $daftar_gambar = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (PDOException $e) {
    $daftar_gambar = [];
}
?>

<div class="catalog-wrapper">
    <!-- Top Bar Navigasi -->
    <div class="page-topbar">
        <a href="<?php echo $base; ?>index.php" class="back-link">&larr; Kembali ke Beranda</a>
        <div style="display: flex; gap: 0.5rem; align-items: center;">
            <span class="section-tag" style="margin: 0;">Jobsheet 10 &bull; Auth &amp; Session</span>
            <?php if ($is_logged_in): ?>
                <a href="upload-asset.php" class="btn btn-primary">+ Upload Gambar</a>
            <?php else: ?>
                <a href="../auth/login.php" class="btn btn-secondary">Login untuk Upload &rarr;</a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Header Galeri -->
    <div class="catalog-header">
        <div>
            <span class="section-tag">Galeri Multi-Pengguna</span>
            <h2 class="form-title">Koleksi Foto &amp; Karya Digital</h2>
            <p class="form-subtitle" style="margin: 0;">
                Dilengkapi autentikasi login pengguna dan pembatasan hak akses operasi CRUD berbasis Role.
            </p>
        </div>

        <!-- Form Pencarian Server-side -->
        <form action="catalog.php" method="GET" class="catalog-search-wrap">
            <input type="text" name="q" class="nm-input search-input" 
                   placeholder="Cari judul, pengunggah, kategori..." 
                   value="<?php echo htmlspecialchars($cari); ?>">
            <button type="submit" class="btn btn-primary">Cari</button>
            <?php if ($cari !== ''): ?>
                <a href="catalog.php" class="btn btn-secondary">Reset</a>
            <?php endif; ?>
        </form>
    </div>

    <!-- Tampilkan Notifikasi Flash -->
    <?php if ($flash): ?>
        <div class="nm-alert nm-alert-<?php echo $flash['type']; ?>">
            <span class="alert-icon"><?php echo $flash['type'] === 'success' ? '✓' : '⚠️'; ?></span>
            <div class="alert-text"><?php echo $flash['pesan']; ?></div>
        </div>
    <?php endif; ?>

    <!-- Tampilan Galeri Foto -->
    <?php if (empty($daftar_gambar)): ?>
        <div class="nm-table-container text-center" style="padding: 3rem 1rem;">
            <p style="color: var(--text-muted); margin-bottom: 1.5rem;">
                <?php echo $cari !== '' ? 'Tidak ditemukan gambar dengan kata kunci "' . htmlspecialchars($cari) . '".' : 'Belum ada gambar yang tersimpan dalam galeri.'; ?>
            </p>
            <?php if ($is_logged_in): ?>
                <a href="upload-asset.php" class="btn btn-primary">+ Unggah Gambar Pertama</a>
            <?php else: ?>
                <a href="../auth/login.php" class="btn btn-primary">Login untuk Mulai Mengunggah</a>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="gallery-grid">
            <?php foreach ($daftar_gambar as $item): ?>
                <?php 
                    $ada_file   = !empty($item['ada_gambar']);
                    $url_gambar = $base . 'gambar.php?id=' . (int)$item['id'];

                    // Cek Hak Akses (Role-based Authorization Tugas Mandiri Jobsheet 10):
                    // Admin dapat mengelola semua gambar.
                    // User biasa hanya dapat mengedit/menghapus gambar miliknya sendiri.
                    $is_owner    = ($is_logged_in && strtolower(trim($item['pengunggah'])) === strtolower(trim($current_user)));
                    $bisa_kelola = ($is_admin || $is_owner);
                ?>
                <article class="gallery-card">
                    <div class="gallery-thumb-wrap">
                        <?php if ($ada_file): ?>
                            <img src="<?php echo $url_gambar; ?>" alt="<?php echo htmlspecialchars($item['judul']); ?>" class="gallery-thumb" loading="lazy">
                        <?php else: ?>
                            <div class="gallery-thumb-placeholder">🖼️</div>
                        <?php endif; ?>
                    </div>

                    <div class="gallery-body">
                        <div class="gallery-meta">
                            <span class="cat-pill"><?php echo htmlspecialchars($item['kategori'] ?? 'Umum'); ?></span>
                            <span>Tahun <?php echo htmlspecialchars($item['tahun']); ?></span>
                        </div>

                        <h3 class="gallery-title"><?php echo htmlspecialchars($item['judul']); ?></h3>
                        <p class="asset-desc" style="margin-bottom: 0.35rem; font-size: 0.825rem;">
                            Pengunggah: <strong><?php echo htmlspecialchars($item['pengunggah']); ?></strong>
                            <?php if ($is_owner): ?>
                                <span style="color: var(--success); font-weight: 700; font-size: 0.725rem;">(Milik Anda)</span>
                            <?php endif; ?>
                        </p>

                        <?php if (!empty($item['deskripsi'])): ?>
                            <p style="font-size: 0.775rem; color: var(--text-light); margin-bottom: 0.5rem;">
                                <?php echo htmlspecialchars($item['deskripsi']); ?>
                            </p>
                        <?php endif; ?>

                        <!-- Tombol Aksi Berdasarkan Status Sesi & Hak Akses -->
                        <div class="gallery-footer" style="flex-wrap: wrap; gap: 0.4rem;">
                            <?php if ($ada_file): ?>
                                <a href="<?php echo $url_gambar; ?>" target="_blank" class="btn-card" style="font-size: 0.775rem;">
                                    Lihat ↗
                                </a>
                            <?php endif; ?>

                            <?php if ($bisa_kelola): ?>
                                <!-- Tombol Edit (Role-protected) -->
                                <a href="edit.php?id=<?php echo (int)$item['id']; ?>" class="btn-card" style="font-size: 0.775rem; color: var(--primary);">
                                    ✏️ Edit
                                </a>

                                <!-- Tombol Hapus via POST (Role-protected) -->
                                <form action="hapus.php" method="POST" 
                                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus gambar \'<?php echo addslashes(htmlspecialchars($item['judul'])); ?>\'?');" 
                                      style="display: inline; margin: 0;">
                                    <input type="hidden" name="id" value="<?php echo (int)$item['id']; ?>">
                                    <button type="submit" class="btn-outline" 
                                            style="color: #ef4444; font-size: 0.775rem; font-weight: 700; cursor: pointer; padding: 0.35rem 0.6rem; border: none;">
                                        Hapus
                                    </button>
                                </form>
                            <?php elseif (!$is_logged_in): ?>
                                <a href="../auth/login.php" style="font-size: 0.75rem; color: var(--text-light);">
                                    Login untuk kelola
                                </a>
                            <?php else: ?>
                                <span style="font-size: 0.725rem; color: var(--text-light);">
                                    Karya pengguna lain
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>

        <!-- Pagination Bar Server-side -->
        <div class="pagination-bar" style="margin-top: 2rem; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; padding-top: 1rem; border-top: 1px solid var(--border-subtle);">
            <div style="font-size: 0.85rem; color: var(--text-muted);">
                Menampilkan halaman <strong><?php echo $page; ?></strong> dari <strong><?php echo $total_halaman; ?></strong> (Total <?php echo $total_data; ?> gambar)
            </div>

            <div style="display: flex; gap: 0.35rem; align-items: center;">
                <?php 
                $query_param = $cari !== '' ? '&q=' . urlencode($cari) : '';
                ?>

                <!-- Tombol Sebelumnya -->
                <?php if ($page > 1): ?>
                    <a href="catalog.php?page=<?php echo $page - 1 . $query_param; ?>" class="btn-pagination">&larr; Sebelumnya</a>
                <?php else: ?>
                    <span class="btn-pagination disabled">&larr; Sebelumnya</span>
                <?php endif; ?>

                <!-- Nomor Halaman -->
                <?php for ($i = 1; $i <= $total_halaman; $i++): ?>
                    <?php if ($i === $page): ?>
                        <span class="btn-pagination active"><?php echo $i; ?></span>
                    <?php else: ?>
                        <a href="catalog.php?page=<?php echo $i . $query_param; ?>" class="btn-pagination"><?php echo $i; ?></a>
                    <?php endif; ?>
                <?php endfor; ?>

                <!-- Tombol Selanjutnya -->
                <?php if ($page < $total_halaman): ?>
                    <a href="catalog.php?page=<?php echo $page + 1 . $query_param; ?>" class="btn-pagination">Selanjutnya &rarr;</a>
                <?php else: ?>
                    <span class="btn-pagination disabled">Selanjutnya &rarr;</span>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
