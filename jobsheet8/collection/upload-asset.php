<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../includes/koneksi.php';

$error = null;

// Cek jika ada request POST (saat form disubmit)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Ambil data teks dari form
    $judul      = trim($_POST['judul'] ?? '');
    $pengunggah = trim($_POST['pengunggah'] ?? '');
    $kategori   = trim($_POST['kategori'] ?? 'Fotografi');
    $tahun      = trim($_POST['tahun'] ?? date('Y'));
    $deskripsi  = trim($_POST['deskripsi'] ?? '');

    // 2. Validasi sederhana
    if (empty($judul) || empty($pengunggah)) {
        $error = "Judul gambar dan nama pengunggah wajib diisi!";
    } elseif (!isset($_FILES['gambar']) || $_FILES['gambar']['error'] !== UPLOAD_ERR_OK) {
        $error = "Silakan pilih file gambar yang ingin diunggah!";
    } else {
        // 3. Validasi dan proses upload file gambar
        $nama_file = $_FILES['gambar']['name'];
        $tmp_file  = $_FILES['gambar']['tmp_name'];

        // Cek ekstensi file
        $ekstensi = strtolower(pathinfo($nama_file, PATHINFO_EXTENSION));
        $ekstensi_boleh = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

        if (!in_array($ekstensi, $ekstensi_boleh)) {
            $error = "Format file tidak didukung! Harap pilih gambar dengan format JPG, JPEG, PNG, WEBP, atau GIF.";
        } elseif ($_FILES['gambar']['size'] > 5 * 1024 * 1024) {
            $error = "Ukuran gambar terlalu besar! Maksimal 5MB.";
        } else {
            // 1. Baca data gambar dan ubah ke format Data URL Base64
            // (Kompatibel 100% dengan Vercel Serverless read-only filesystem & tersimpan langsung di Supabase cloud)
            $mime_types = [
                'jpg'  => 'image/jpeg',
                'jpeg' => 'image/jpeg',
                'png'  => 'image/png',
                'webp' => 'image/webp',
                'gif'  => 'image/gif'
            ];
            $tipe_mime = $mime_types[$ekstensi] ?? 'image/jpeg';
            $konten_gambar = file_get_contents($tmp_file);
            $data_simpan = 'data:' . $tipe_mime . ';base64,' . base64_encode($konten_gambar);

            // 2. Simpan juga salinan fisik jika di folder lokal yang writable
            $folder_uploads = __DIR__ . '/../assets/uploads/';
            if (@is_dir($folder_uploads) || @mkdir($folder_uploads, 0777, true)) {
                $nama_file_lokal = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $nama_file);
                @copy($tmp_file, $folder_uploads . $nama_file_lokal);
            }

            // 3. Simpan info gambar ke database PostgreSQL tabel galeri
            try {
                $sql = "INSERT INTO galeri (judul, pengunggah, kategori, file_gambar, tahun, deskripsi) 
                        VALUES (:judul, :pengunggah, :kategori, :file_gambar, :tahun, :deskripsi)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':judul'       => $judul,
                    ':pengunggah'  => $pengunggah,
                    ':kategori'    => $kategori,
                    ':file_gambar' => $data_simpan, // Disimpan langsung di Supabase cloud
                    ':tahun'       => (int)$tahun,
                    ':deskripsi'   => $deskripsi
                ]);

                // Buat flash message sukses dan redirect ke katalog galeri
                $_SESSION['flash'] = [
                    'type'  => 'success',
                    'pesan' => "Gambar '<strong>" . htmlspecialchars($judul) . "</strong>' berhasil diunggah ke galeri!"
                ];
                header('Location: catalog.php');
                exit;
            } catch (PDOException $e) {
                $error = "Gagal menyimpan ke database: " . $e->getMessage();
            }
        }
    }
}

$page_title = 'Upload Gambar';
include __DIR__ . '/../includes/header.php';
?>

<div class="upload-wrapper">
    <!-- Navigasi Atas -->
    <div class="page-topbar">
        <a href="<?php echo $base; ?>index.php" class="back-link">&larr; Kembali ke Beranda</a>
        <a href="<?php echo $base; ?>collection/catalog.php" class="catalog-link">Buka Galeri Foto &rarr;</a>
    </div>

    <div class="form-container">
        <!-- Judul Halaman -->
        <div class="form-header text-center">
            <span class="section-tag">PixelGallery Creator Hub</span>
            <h2 class="form-title">Upload Gambar Baru</h2>
            <p class="form-subtitle">Tambahkan foto, wallpaper, atau ilustrasi baru ke koleksi galeri.</p>
        </div>

        <!-- Pesan Error -->
        <?php if ($error): ?>
            <div class="nm-alert nm-alert-error">
                <span class="alert-icon">⚠️</span>
                <div class="alert-text"><?php echo $error; ?></div>
            </div>
        <?php endif; ?>

        <!-- Form Upload Gambar -->
        <form action="upload-asset.php" method="POST" enctype="multipart/form-data" class="nm-form-card">
            
            <div class="form-group">
                <label for="judul">Judul Gambar <span class="required-mark">*</span></label>
                <input type="text" name="judul" id="judul" class="nm-input" placeholder="Misal: Pemandangan Senja di Pantai" value="<?php echo htmlspecialchars($_POST['judul'] ?? ''); ?>" required>
                <span class="field-hint">Beri judul foto atau gambar yang menarik dan jelas.</span>
            </div>

            <div class="form-grid-2">
                <div class="form-group">
                    <label for="pengunggah">Nama Pengunggah / Fotografer <span class="required-mark">*</span></label>
                    <input type="text" name="pengunggah" id="pengunggah" class="nm-input" placeholder="Misal: Daanii Althaaf" value="<?php echo htmlspecialchars($_POST['pengunggah'] ?? ''); ?>" required>
                    <span class="field-hint">Nama orang yang memotret atau membuat gambar.</span>
                </div>

                <div class="form-group">
                    <label for="kategori">Kategori Gambar <span class="required-mark">*</span></label>
                    <select name="kategori" id="kategori" class="nm-select" required>
                        <option value="Wallpaper" <?php echo (($_POST['kategori'] ?? '') === 'Wallpaper') ? 'selected' : ''; ?>>Wallpaper</option>
                        <option value="Pemandangan" <?php echo (($_POST['kategori'] ?? '') === 'Pemandangan') ? 'selected' : ''; ?>>Pemandangan & Alam</option>
                        <option value="Fotografi" <?php echo (($_POST['kategori'] ?? '') === 'Fotografi') ? 'selected' : ''; ?>>Fotografi Umum</option>
                        <option value="Ilustrasi" <?php echo (($_POST['kategori'] ?? '') === 'Ilustrasi') ? 'selected' : ''; ?>>Ilustrasi & Digital Art</option>
                        <option value="Arsitektur" <?php echo (($_POST['kategori'] ?? '') === 'Arsitektur') ? 'selected' : ''; ?>>Arsitektur & Kota</option>
                    </select>
                </div>
            </div>

            <div class="form-grid-2">
                <div class="form-group">
                    <label for="tahun">Tahun Pengambilan <span class="required-mark">*</span></label>
                    <input type="number" name="tahun" id="tahun" class="nm-input" min="2000" max="2035" value="<?php echo htmlspecialchars($_POST['tahun'] ?? date('Y')); ?>" required>
                    <span class="field-hint">Tahun foto diambil atau dibuat.</span>
                </div>

                <div class="form-group">
                    <label for="deskripsi">Deskripsi Singkat (Opsional)</label>
                    <input type="text" name="deskripsi" id="deskripsi" class="nm-input" placeholder="Misal: Difoto saat matahari terbenam di Bali" value="<?php echo htmlspecialchars($_POST['deskripsi'] ?? ''); ?>">
                    <span class="field-hint">Keterangan atau lokasi pengambilan foto.</span>
                </div>
            </div>

            <div class="form-group">
                <label for="gambar">Pilih File Gambar <span class="required-mark">*</span></label>
                <div class="nm-file-dropzone">
                    <input type="file" name="gambar" id="gambar" class="nm-file-input" accept="image/png, image/jpeg, image/jpg, image/webp, image/gif" required>
                    <p class="file-hint">Mendukung format: JPG, JPEG, PNG, WEBP, GIF (Maksimal 5MB)</p>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary btn-submit">
                    <span>Unggah Gambar Sekarang</span>
                </button>
                <a href="<?php echo $base; ?>collection/catalog.php" class="btn btn-secondary">Batal</a>
            </div>

        </form>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>