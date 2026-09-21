<?php
$page_title = "LowPolyTech - 3D Asset Store for Game Developers";
include __DIR__ . '/includes/header.php';
require __DIR__ . '/includes/koneksi.php';

$totalBuku = $pdo->query("SELECT COUNT(*) FROM buku")->fetchColumn();
$totalAnggota = $pdo->query("SELECT COUNT(*) FROM anggota")->fetchColumn();
?>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="hero-badge">
            <span class="badge-dot"></span>
            <span>Game-Ready 3D Asset Store</span>
        </div>
        <h1 class="hero-headline">Speed Up Your Development</h1>
        <p class="hero-subtext">
            Koleksi aset 3D Low Poly berkualitas tinggi, ringan, dan siap pakai untuk Unity, Unreal Engine, Godot, dan Blender. Hemat waktu pembuatan model dan fokus kembangkan gameplay impianmu.
        </p>

        <div class="hero-actions">
            <a href="buku/list.php" class="btn btn-primary">
                <span>Jelajahi Katalog Aset</span>
                <span class="btn-arrow">&rarr;</span>
            </a>
            <a href="buku/tambah.php" class="btn btn-secondary">
                <span>+ Upload Aset Baru</span>
            </a>
            <a href="anggota/list.php" class="btn btn-outline">
                <span>Daftar Creator</span>
            </a>
        </div>

        <!-- Live Platform Stats -->
        <div class="hero-stats">
            <div class="stat-item">
                <span class="stat-num"><?php echo htmlspecialchars($totalBuku); ?>+</span>
                <span class="stat-label">Asset Packs Live</span>
            </div>
            <div class="stat-divider"></div>
            <div class="stat-item">
                <span class="stat-num"><?php echo htmlspecialchars($totalAnggota); ?></span>
                <span class="stat-label">Active Creators</span>
            </div>
            <div class="stat-divider"></div>
            <div class="stat-item">
                <span class="stat-num">&lt; 1.5K</span>
                <span class="stat-label">Avg. Tris Count</span>
            </div>
            <div class="stat-divider"></div>
            <div class="stat-item">
                <span class="stat-num">100%</span>
                <span class="stat-label">Commercial Ready</span>
            </div>
        </div>
    </section>

    <!-- Categories Section -->
    <section class="categories-section">
        <div class="section-title-wrap">
            <span class="section-tag">Kategori Populer</span>
            <h2>Temukan Aset Sesuai Genre Game Kamu</h2>
        </div>

        <div class="category-grid">
            <a href="buku/list.php" class="category-card">
                <span class="cat-icon">🏰</span>
                <div class="cat-info">
                    <h3>Environment &amp; World</h3>
                    <p>Modular dungeon, castle, &amp; city</p>
                </div>
            </a>
            <a href="buku/list.php" class="category-card">
                <span class="cat-icon">⚔️</span>
                <div class="cat-info">
                    <h3>Weapons &amp; Armory</h3>
                    <p>Swords, shields, axes, &amp; bows</p>
                </div>
            </a>
            <a href="buku/list.php" class="category-card">
                <span class="cat-icon">🚗</span>
                <div class="cat-info">
                    <h3>Vehicles &amp; Sci-Fi</h3>
                    <p>Spaceships, mecha, &amp; retro cars</p>
                </div>
            </a>
            <a href="buku/list.php" class="category-card">
                <span class="cat-icon">👾</span>
                <div class="cat-info">
                    <h3>Characters &amp; Mobs</h3>
                    <p>Rigged warriors, NPCs, &amp; monsters</p>
                </div>
            </a>
            <a href="buku/list.php" class="category-card">
                <span class="cat-icon">🌲</span>
                <div class="cat-info">
                    <h3>Nature &amp; Foliage</h3>
                    <p>Low poly trees, rocks, &amp; terrain</p>
                </div>
            </a>
            <a href="buku/list.php" class="category-card">
                <span class="cat-icon">📦</span>
                <div class="cat-info">
                    <h3>UI &amp; Icons</h3>
                    <p>3D item badges &amp; game icons</p>
                </div>
            </a>
        </div>
    </section>

    <!-- Featured Asset Packs -->
    <section class="featured-section">
        <div class="section-title-wrap flex-between">
            <div>
                <span class="section-tag">Featured Packs</span>
                <h2>Pilihan Aset Unggulan</h2>
            </div>
            <a href="buku/list.php" class="link-more">Lihat Semua Aset &rarr;</a>
        </div>

        <div class="asset-grid">
            <!-- Asset 1 -->
            <article class="asset-card">
                <div class="asset-preview preview-dungeon">
                    <span class="pack-badge">Modular</span>
                    <span class="poly-badge">1.2k Tris</span>
                </div>
                <div class="asset-body">
                    <div class="asset-meta">
                        <span class="asset-author">LowPolyTech Studio</span>
                        <span class="asset-rating">★ 4.9 (128)</span>
                    </div>
                    <h3 class="asset-title"><a href="buku/list.php">Dungeon Crawler Modular Pack</a></h3>
                    <p class="asset-desc">140+ modul ruangan batu, peti harta, obor, jebakan duri, dan pilar bertekstur palet warna efisien.</p>
                    <div class="asset-tags">
                        <span class="tag">FBX</span>
                        <span class="tag">Blend</span>
                        <span class="tag">Collision</span>
                    </div>
                    <div class="asset-footer">
                        <span class="asset-price">Free / Open</span>
                        <a href="buku/list.php" class="btn-card">Inspect Pack &rarr;</a>
                    </div>
                </div>
            </article>

            <!-- Asset 2 -->
            <article class="asset-card">
                <div class="asset-preview preview-cyber">
                    <span class="pack-badge">Sci-Fi</span>
                    <span class="poly-badge">980 Tris</span>
                </div>
                <div class="asset-body">
                    <div class="asset-meta">
                        <span class="asset-author">Nexus Labs</span>
                        <span class="asset-rating">★ 5.0 (94)</span>
                    </div>
                    <h3 class="asset-title"><a href="buku/list.php">Cyberpunk Mini Metropolis</a></h3>
                    <p class="asset-desc">Gedung pencakar langit neon, drone patroli, kendaraan melayang, pipa jalanan, dan reklame holografis.</p>
                    <div class="asset-tags">
                        <span class="tag">Emissive</span>
                        <span class="tag">PBR</span>
                        <span class="tag">Unity Prefab</span>
                    </div>
                    <div class="asset-footer">
                        <span class="asset-price">Free / Open</span>
                        <a href="buku/list.php" class="btn-card">Inspect Pack &rarr;</a>
                    </div>
                </div>
            </article>

            <!-- Asset 3 -->
            <article class="asset-card">
                <div class="asset-preview preview-weapon">
                    <span class="pack-badge">Weapons</span>
                    <span class="poly-badge">450 Tris</span>
                </div>
                <div class="asset-body">
                    <div class="asset-meta">
                        <span class="asset-author">IronForge Dev</span>
                        <span class="asset-rating">★ 4.8 (76)</span>
                    </div>
                    <h3 class="asset-title"><a href="buku/list.php">Medieval Weapons &amp; Shields</a></h3>
                    <p class="asset-desc">60 model pedang ksatria, busur panah, tameng kayu, kapak viking, dan tombak tempur siap pasang ke tangan karakter.</p>
                    <div class="asset-tags">
                        <span class="tag">Low Tris</span>
                        <span class="tag">Mobile Ready</span>
                        <span class="tag">OBJ</span>
                    </div>
                    <div class="asset-footer">
                        <span class="asset-price">Free / Open</span>
                        <a href="buku/list.php" class="btn-card">Inspect Pack &rarr;</a>
                    </div>
                </div>
            </article>

            <!-- Asset 4 -->
            <article class="asset-card">
                <div class="asset-preview preview-creature">
                    <span class="pack-badge">Animated</span>
                    <span class="poly-badge">1.8k Tris</span>
                </div>
                <div class="asset-body">
                    <div class="asset-meta">
                        <span class="asset-author">MonsterCraft</span>
                        <span class="asset-rating">★ 4.9 (112)</span>
                    </div>
                    <h3 class="asset-title"><a href="buku/list.php">Stylized Fantasy Mobs &amp; Slimes</a></h3>
                    <p class="asset-desc">10 model monster fantasi lengkap dengan rig tulang biped &amp; quadruped beserta animasi dasar Idle dan Walk cycle.</p>
                    <div class="asset-tags">
                        <span class="tag">Rigged</span>
                        <span class="tag">Animations</span>
                        <span class="tag">Humanoid</span>
                    </div>
                    <div class="asset-footer">
                        <span class="asset-price">Free / Open</span>
                        <a href="buku/list.php" class="btn-card">Inspect Pack &rarr;</a>
                    </div>
                </div>
            </article>
        </div>
    </section>

    <!-- Why Choose LowPolyTech -->
    <section class="features-section">
        <div class="section-title-wrap text-center">
            <span class="section-tag">Kenapa LowPolyTech?</span>
            <h2>Didesain Khusus untuk Performa Game Maksimal</h2>
        </div>

        <div class="features-grid">
            <div class="feature-item">
                <div class="feature-icon">⚡</div>
                <h3>Polycount Super Ringan</h3>
                <p>Topologi rapi tanpa vertex mubazir. Sangat bersahabat untuk game Mobile (Android/iOS), WebGL, dan Virtual Reality.</p>
            </div>
            <div class="feature-item">
                <div class="feature-icon">🎮</div>
                <h3>Multi-Engine Compatible</h3>
                <p>Telah diuji dan kompatibel langsung dengan Unity Engine, Unreal Engine 5, Godot 4, hingga software Blender.</p>
            </div>
            <div class="feature-item">
                <div class="feature-icon">💼</div>
                <h3>Lisensi Bebas Royalti</h3>
                <p>Semua aset dapat kamu gunakan langsung di dalam game komersial yang kamu jual di Steam, Google Play, atau itch.io.</p>
            </div>
        </div>
    </section>

    <!-- CTA Box -->
    <section class="cta-banner">
        <div class="cta-content">
            <h2>Mulai Bangun Game Impianmu Sekarang</h2>
            <p>Hemat ratusan jam pengerjaan 3D modeling dan fokus pada serunya mekanisme gameplay.</p>
            <div class="cta-buttons">
                <a href="buku/list.php" class="btn btn-primary">Buka Database Katalog Aset &rarr;</a>
                <a href="buku/tambah.php" class="btn btn-secondary">Upload Aset Baru</a>
            </div>
        </div>
    </section>

<?php include __DIR__ . '/includes/footer.php'; ?>
