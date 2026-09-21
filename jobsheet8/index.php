<?php
$page_title = "PixelGallery - Platform Galeri & Upload Foto";
include __DIR__ . '/includes/header.php';
require __DIR__ . '/includes/koneksi.php';

// Ambil statistik ringkas langsung dari database PostgreSQL (sesuai tugas Jobsheet 8)
$totalFoto = $pdo->query("SELECT COUNT(*) FROM galeri")->fetchColumn();
$totalPengunggah = $pdo->query("SELECT COUNT(DISTINCT pengunggah) FROM galeri")->fetchColumn();

// Ambil 4 gambar terbaru dari database (tanpa membebani ukuran memori HTML)
$fotoTerbaru = $pdo->query("SELECT id, judul, pengunggah, kategori, tahun, deskripsi, 
                                   (CASE WHEN file_gambar IS NOT NULL AND file_gambar != '' THEN 1 ELSE 0 END) AS ada_gambar 
                            FROM galeri 
                            ORDER BY id DESC LIMIT 4")->fetchAll(PDO::FETCH_ASSOC);
?>

    <!-- Hero Section -->
    <section class="hero-section">
        <h1 class="hero-headline">Bagikan & Temukan Gambar Inspiratif</h1>
        <p class="hero-subtext">
            Platform galeri foto, wallpaper, dan karya gambar digital. Unggah fotomu dengan mudah, simpan aman ke database PostgreSQL, dan bagikan dalam tampilan galeri yang rapi.
        </p>

        <div class="hero-actions">
            <a href="collection/catalog.php" class="btn btn-primary">
                <span>Jelajahi Galeri Foto</span>
                <span class="btn-arrow">&rarr;</span>
            </a>
            <a href="collection/upload-asset.php" class="btn btn-secondary">
                <span>+ Upload Gambar Baru</span>
            </a>
        </div>

        <!-- Live Platform Stats -->
        <div class="hero-stats">
            <div class="stat-item">
                <span class="stat-num"><?php echo htmlspecialchars($totalFoto); ?></span>
                <span class="stat-label">Total Foto</span>
            </div>
            <div class="stat-divider"></div>
            <div class="stat-item">
                <span class="stat-num"><?php echo htmlspecialchars($totalPengunggah); ?></span>
                <span class="stat-label">Pengunggah</span>
            </div>
            <div class="stat-divider"></div>
            <div class="stat-item">
                <span class="stat-num">HD</span>
                <span class="stat-label">Kualitas Bebas</span>
            </div>
            <div class="stat-divider"></div>
            <div class="stat-item">
                <span class="stat-num">100%</span>
                <span class="stat-label">Bebas Akses</span>
            </div>
        </div>
    </section>

    <!-- Categories Section -->
    <section class="categories-section">
        <div class="section-title-wrap">
            <span class="section-tag">Kategori Gambar</span>
            <h2>Jelajahi Berdasarkan Tema</h2>
        </div>

        <div class="category-grid">
            <a href="collection/catalog.php?q=Wallpaper" class="category-card">
                <span class="cat-icon">🖼️</span>
                <div class="cat-info">
                    <h3>Wallpaper</h3>
                    <p>Latar desktop & layar ponsel</p>
                </div>
            </a>
            <a href="collection/catalog.php?q=Pemandangan" class="category-card">
                <span class="cat-icon">🌄</span>
                <div class="cat-info">
                    <h3>Pemandangan & Alam</h3>
                    <p>Pantai, gunung, dan alam bebas</p>
                </div>
            </a>
            <a href="collection/catalog.php?q=Fotografi" class="category-card">
                <span class="cat-icon">📸</span>
                <div class="cat-info">
                    <h3>Fotografi</h3>
                    <p>Potret, jalanan, dan human interest</p>
                </div>
            </a>
            <a href="collection/catalog.php?q=Ilustrasi" class="category-card">
                <span class="cat-icon">🎨</span>
                <div class="cat-info">
                    <h3>Ilustrasi & Seni</h3>
                    <p>Karya digital art dan grafis</p>
                </div>
            </a>
        </div>
    </section>

    <!-- Featured / Latest Uploads -->
    <section class="featured-section">
        <div class="section-title-wrap flex-between">
            <div>
                <span class="section-tag">Koleksi Terkini</span>
                <h2>Foto Terbaru di Galeri</h2>
            </div>
            <a href="collection/catalog.php" class="link-more">Buka Semua Gambar &rarr;</a>
        </div>

        <?php if (empty($fotoTerbaru)): ?>
            <div class="nm-form-card text-center" style="padding: 2.5rem 1rem;">
                <p style="color: var(--text-muted); margin-bottom: 1rem;">Belum ada foto yang diunggah ke sistem.</p>
                <a href="collection/upload-asset.php" class="btn btn-primary" style="display: inline-flex;">+ Unggah Foto Pertama</a>
            </div>
        <?php else: ?>
            <div class="gallery-grid">
                <?php foreach ($fotoTerbaru as $item): ?>
                    <?php 
                        $ada_file   = !empty($item['ada_gambar']);
                        $url_gambar = 'gambar.php?id=' . (int)$item['id'];
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
                            <p class="asset-desc" style="margin-bottom: 0.5rem; font-size: 0.825rem;">
                                Pengunggah: <strong><?php echo htmlspecialchars($item['pengunggah']); ?></strong>
                            </p>

                            <div class="gallery-footer">
                                <?php if ($ada_file): ?>
                                    <a href="<?php echo $url_gambar; ?>" target="_blank" class="btn-card" style="font-size: 0.775rem;">
                                        Lihat Foto ↗
                                    </a>
                                <?php else: ?>
                                    <span style="font-size: 0.75rem; color: var(--text-light);">Placeholder</span>
                                <?php endif; ?>
                                <a href="collection/catalog.php" class="btn-card" style="font-size: 0.775rem;">
                                    Galeri &rarr;
                                </a>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>

    <!-- Keunggulan Sistem -->
    <section class="features-section">
        <div class="section-title-wrap text-center">
            <span class="section-tag">Fitur Aplikasi</span>
            <h2>Sederhana, Cepat, dan Mudah Dipahami</h2>
        </div>

        <div class="features-grid">
            <div class="feature-item">
                <div class="feature-icon">📁</div>
                <h3>Upload Gambar Instan</h3>
                <p>Mengunggah file foto (JPG, PNG, WEBP) dengan validasi ekstensi berkas di sisi server menggunakan fungsi PHP bawaan.</p>
            </div>
            <div class="feature-item">
                <div class="feature-icon">🗄️</div>
                <h3>Database PostgreSQL</h3>
                <p>Penyimpanan persisten relasional menggunakan ekstensi PDO dan Prepared Statement untuk mencegah celah SQL Injection.</p>
            </div>
            <div class="feature-item">
                <div class="feature-icon">✨</div>
                <h3>Desain Soft Neumorphism</h3>
                <p>Antarmuka visual modern yang bersih dan halus berbasis neumorphism.io tanpa efek animasi berat saat memuat halaman.</p>
            </div>
        </div>
    </section>

    <!-- CTA Box -->
    <section class="cta-banner">
        <div class="cta-content">
            <h2>Punya Foto atau Wallpaper Menarik?</h2>
            <p>Unggah sekarang dan simpan ke galeri digital pribadi Anda dalam hitungan detik.</p>
            <div class="cta-buttons">
                <a href="collection/upload-asset.php" class="btn btn-primary">+ Upload Gambar Sekarang</a>
                <a href="collection/catalog.php" class="btn btn-secondary">Buka Galeri Foto &rarr;</a>
            </div>
        </div>
    </section>

<?php include __DIR__ . '/includes/footer.php'; ?>
