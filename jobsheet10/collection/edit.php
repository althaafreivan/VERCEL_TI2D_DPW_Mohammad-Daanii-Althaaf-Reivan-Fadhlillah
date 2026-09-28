<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Guard Clause Autentikasi (Jobsheet 10)
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/koneksi.php';

// Validasi parameter ID
$id = filter_var($_GET['id'] ?? 0, FILTER_VALIDATE_INT);
if (!$id) {
    $_SESSION['flash'] = [
        'type'  => 'error',
        'pesan' => 'ID gambar tidak valid.'
    ];
    header('Location: catalog.php');
    exit;
}

// Ambil data gambar dari database
try {
    $stmt = $pdo->prepare("SELECT id, judul, pengunggah, kategori, tahun, deskripsi, 
                                  (CASE WHEN file_gambar IS NOT NULL AND file_gambar != '' THEN 1 ELSE 0 END) AS ada_gambar
                           FROM galeri 
                           WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $item = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$item) {
        $_SESSION['flash'] = [
            'type'  => 'error',
            'pesan' => 'Gambar tidak ditemukan dalam database.'
        ];
        header('Location: catalog.php');
        exit;
    }

    // Role-based Access Control (Tugas Mandiri Jobsheet 10):
    // Hanya Admin atau Pengunggah asli yang boleh mengedit
    $is_admin = ($_SESSION['role'] === 'admin');
    $is_owner = (strtolower(trim($item['pengunggah'])) === strtolower(trim($_SESSION['nama'] ?? '')));

    if (!$is_admin && !$is_owner) {
        $_SESSION['flash'] = [
            'type'  => 'error',
            'pesan' => 'Akses ditolak! Anda hanya memiliki izin untuk mengedit karya yang Anda unggah sendiri.'
        ];
        header('Location: catalog.php');
        exit;
    }

} catch (PDOException $e) {
    $_SESSION['flash'] = [
        'type'  => 'error',
        'pesan' => 'Gagal mengambil data: ' . $e->getMessage()
    ];
    header('Location: catalog.php');
    exit;
}

$page_title = 'Edit Gambar - ' . htmlspecialchars($item['judul']);
include __DIR__ . '/../includes/header.php';

$error = $_SESSION['flash_error'] ?? null;
unset($_SESSION['flash_error']);
?>

<div class="upload-wrapper">
    <!-- Navigasi Atas -->
    <div class="page-topbar">
        <a href="catalog.php" class="back-link">&larr; Kembali ke Galeri</a>
        <span class="section-tag" style="margin: 0;">Jobsheet 10 &bull; Role-based Edit</span>
    </div>

    <div class="form-container">
        <!-- Judul Halaman -->
        <div class="form-header text-center">
            <span class="section-tag">PixelGallery Protected Editor</span>
            <h2 class="form-title">Edit Metadata &amp; Gambar</h2>
            <p class="form-subtitle">
                Anda masuk sebagai <strong><?php echo htmlspecialchars($_SESSION['nama']); ?></strong> 
                (Peran: <span class="cat-pill" style="text-transform: uppercase;"><?php echo htmlspecialchars($_SESSION['role']); ?></span>)
            </p>
        </div>

        <?php if ($error): ?>
            <div class="nm-alert nm-alert-error">
                <span class="alert-icon">⚠️</span>
                <div class="alert-text"><?php echo $error; ?></div>
            </div>
        <?php endif; ?>

        <form action="proses_edit.php" method="POST" enctype="multipart/form-data" class="nm-form-card">
            <input type="hidden" name="id" value="<?php echo (int)$item['id']; ?>">

            <div class="form-group">
                <label for="judul">Judul Gambar <span class="required-mark">*</span></label>
                <input type="text" name="judul" id="judul" class="nm-input" 
                       value="<?php echo htmlspecialchars($item['judul']); ?>" required>
            </div>

            <div class="form-grid-2">
                <div class="form-group">
                    <label for="pengunggah">Nama Pengunggah <span class="required-mark">*</span></label>
                    <input type="text" name="pengunggah" id="pengunggah" class="nm-input" 
                           value="<?php echo htmlspecialchars($item['pengunggah']); ?>" 
                           <?php echo (!$is_admin) ? 'readonly' : ''; ?> required>
                    <span class="field-hint">
                        <?php echo $is_admin ? 'Sebagai Admin, Anda dapat mengubah nama pemilik karya.' : 'Terkunci sesuai kepemilikan akun Anda.'; ?>
                    </span>
                </div>

                <div class="form-group">
                    <label for="kategori">Kategori Gambar <span class="required-mark">*</span></label>
                    <select name="kategori" id="kategori" class="nm-select" required>
                        <?php 
                        $kategori_list = ['Wallpaper', 'Pemandangan', 'Fotografi', 'Ilustrasi', 'Arsitektur'];
                        foreach ($kategori_list as $kat): ?>
                            <option value="<?php echo $kat; ?>" <?php echo ($item['kategori'] === $kat) ? 'selected' : ''; ?>>
                                <?php echo $kat; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="form-grid-2">
                <div class="form-group">
                    <label for="tahun">Tahun Pengambilan <span class="required-mark">*</span></label>
                    <input type="number" name="tahun" id="tahun" class="nm-input" min="2000" max="2035" 
                           value="<?php echo htmlspecialchars($item['tahun']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="deskripsi">Deskripsi Singkat</label>
                    <input type="text" name="deskripsi" id="deskripsi" class="nm-input" 
                           value="<?php echo htmlspecialchars($item['deskripsi'] ?? ''); ?>">
                </div>
            </div>

            <div class="form-group" style="margin-top: 1rem;">
                <label>Foto Saat Ini:</label>
                <div style="display: flex; align-items: center; gap: 1.25rem; background: var(--nm-bg); padding: 1rem; border-radius: var(--radius-sm); box-shadow: var(--nm-shadow-pressed);">
                    <?php if ($item['ada_gambar']): ?>
                        <img src="<?php echo $base; ?>gambar.php?id=<?php echo $item['id']; ?>" 
                             alt="<?php echo htmlspecialchars($item['judul']); ?>" 
                             style="width: 90px; height: 90px; object-fit: cover; border-radius: var(--radius-sm); box-shadow: var(--nm-shadow-sm);">
                    <?php else: ?>
                        <div style="width: 90px; height: 90px; background: #cbd5e1; border-radius: var(--radius-sm); display: flex; align-items: center; justify-content: center; font-size: 2rem;">🖼️</div>
                    <?php endif; ?>
                    <div>
                        <p style="font-weight: 600; color: var(--text-title); font-size: 0.95rem; margin-bottom: 0.25rem;">
                            <?php echo htmlspecialchars($item['judul']); ?>
                        </p>
                        <p style="font-size: 0.825rem; color: var(--text-muted); margin: 0;">
                            Jika tidak ingin mengganti file gambar, biarkan input di bawah ini kosong.
                        </p>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="gambar">Ganti File Gambar (Opsional)</label>
                <div class="nm-file-dropzone">
                    <input type="file" name="gambar" id="gambar" class="nm-file-input" 
                           accept="image/png, image/jpeg, image/jpg, image/webp, image/gif">
                    <p class="file-hint">Biarkan kosong jika tetap menggunakan gambar lama. Maksimal 5MB.</p>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary btn-submit">
                    <span>Simpan Perubahan</span>
                </button>
                <a href="catalog.php" class="btn btn-secondary">Batal</a>
            </div>

        </form>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
