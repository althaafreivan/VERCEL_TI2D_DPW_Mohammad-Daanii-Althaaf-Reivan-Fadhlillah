<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../includes/koneksi.php';

// Proses hapus gambar jika ada parameter ?hapus=ID
if (isset($_GET['hapus'])) {
    $id = filter_var($_GET['hapus'], FILTER_VALIDATE_INT);
    if ($id) {
        try {
            // Ambil nama file jika ada file lokal untuk dibersihkan
            $cek = $pdo->prepare("SELECT file_gambar FROM galeri WHERE id = :id");
            $cek->execute([':id' => $id]);
            $foto = $cek->fetch(PDO::FETCH_ASSOC);

            if ($foto && !empty($foto['file_gambar']) && !str_starts_with($foto['file_gambar'], 'data:')) {
                $file_path = __DIR__ . '/../assets/uploads/' . $foto['file_gambar'];
                if (file_exists($file_path)) {
                    @unlink($file_path);
                }
            }

            // Hapus rekaman dari tabel galeri
            $stmt = $pdo->prepare("DELETE FROM galeri WHERE id = :id");
            $stmt->execute([':id' => $id]);

            $_SESSION['flash'] = [
                'type'  => 'success',
                'pesan' => "Gambar berhasil dihapus dari galeri!"
            ];
        } catch (PDOException $e) {
            $_SESSION['flash'] = [
                'type'  => 'error',
                'pesan' => "Gagal menghapus gambar: " . $e->getMessage()
            ];
        }
    }
    header("Location: catalog.php");
    exit;
}

$page_title = 'Galeri Foto & Gambar';
include __DIR__ . '/../includes/header.php';

// Ambil flash message dari session
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

// Ambil data gambar (tanpa membebani memori dengan isi biner gambar di halaman list)
$cari = trim($_GET['q'] ?? '');
if ($cari !== '') {
    $sql = "SELECT id, judul, pengunggah, kategori, tahun, deskripsi, 
                   (CASE WHEN file_gambar IS NOT NULL AND file_gambar != '' THEN 1 ELSE 0 END) AS ada_gambar 
            FROM galeri 
            WHERE judul ILIKE :q OR pengunggah ILIKE :q OR kategori ILIKE :q 
            ORDER BY id DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':q' => "%$cari%"]);
    $daftar_gambar = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $sql = "SELECT id, judul, pengunggah, kategori, tahun, deskripsi, 
                   (CASE WHEN file_gambar IS NOT NULL AND file_gambar != '' THEN 1 ELSE 0 END) AS ada_gambar 
            FROM galeri 
            ORDER BY id DESC";
    $daftar_gambar = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
}
?>

<div class="catalog-wrapper">
    <!-- Top Bar Navigasi -->
    <div class="page-topbar">
        <a href="<?php echo $base; ?>index.php" class="back-link">&larr; Kembali ke Beranda</a>
        <a href="upload-asset.php" class="btn btn-primary">+ Upload Gambar Baru</a>
    </div>

    <!-- Header Galeri -->
    <div class="catalog-header">
        <div>
            <span class="section-tag">Koleksi Gambar</span>
            <h2 class="form-title">Galeri Foto & Wallpaper</h2>
            <p class="form-subtitle" style="margin: 0;">Semua gambar yang telah diunggah dan tersimpan di database.</p>
        </div>

        <!-- Form Pencarian Sederhana -->
        <form action="catalog.php" method="GET" class="catalog-search-wrap">
            <input type="text" name="q" class="nm-input search-input" placeholder="Cari judul, pengunggah, kategori..." value="<?php echo htmlspecialchars($cari); ?>">
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

    <!-- Tampilan Galeri Foto (Grid Kartu Gambar) -->
    <?php if (empty($daftar_gambar)): ?>
        <div class="nm-table-container text-center" style="padding: 3rem 1rem;">
            <p style="color: var(--text-muted); margin-bottom: 1.5rem;">
                Belum ada gambar yang diunggah atau gambar tidak ditemukan.
            </p>
            <a href="upload-asset.php" class="btn btn-primary">+ Unggah Gambar Pertama</a>
        </div>
    <?php else: ?>
        <div class="gallery-grid">
            <?php foreach ($daftar_gambar as $item): ?>
                <?php 
                    $ada_file   = !empty($item['ada_gambar']);
                    $url_gambar = $base . 'gambar.php?id=' . (int)$item['id'];
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
                        </p>

                        <?php if (!empty($item['deskripsi'])): ?>
                            <p style="font-size: 0.775rem; color: var(--text-light); margin-bottom: 0.5rem;">
                                <?php echo htmlspecialchars($item['deskripsi']); ?>
                            </p>
                        <?php endif; ?>

                        <div class="gallery-footer">
                            <?php if ($ada_file): ?>
                                <a href="<?php echo $url_gambar; ?>" target="_blank" class="btn-card" style="font-size: 0.775rem;">
                                    Lihat Penuh ↗
                                </a>
                            <?php else: ?>
                                <span style="font-size: 0.75rem; color: var(--text-light);">Placeholder</span>
                            <?php endif; ?>

                            <a href="catalog.php?hapus=<?php echo $item['id']; ?>" 
                               class="btn-outline" 
                               onclick="return confirm('Apakah Anda yakin ingin menghapus gambar ini?');"
                               style="color: #ef4444; font-size: 0.8rem; font-weight: 700;">
                                Hapus
                            </a>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
