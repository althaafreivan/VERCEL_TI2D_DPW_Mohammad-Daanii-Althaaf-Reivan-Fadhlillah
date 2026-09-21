<?php
// Portal Katalog Praktikum Desain & Pemrograman Web
// Mohammad Daanii Althaaf Reivan Fadhlillah (TI-2D / 254107020123)

$totalBuku = '-';
$totalAnggota = '-';
$dbStatus = 'Offline';

if (file_exists(__DIR__ . '/jobsheet8/includes/koneksi.php')) {
    try {
        require_once __DIR__ . '/jobsheet8/includes/koneksi.php';
        if (isset($pdo) && $pdo) {
            $totalBuku = $pdo->query("SELECT COUNT(*) FROM buku")->fetchColumn();
            $totalAnggota = $pdo->query("SELECT COUNT(*) FROM anggota")->fetchColumn();
            $dbStatus = 'Terhubung';
        }
    } catch (Throwable $e) {
        $dbStatus = 'Fallback Aktif';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Katalog Praktikum DPW | Mohammad Daanii Althaaf</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        /* ==========================================================================
           Ultra Soft Neumorphic Design System (Clean, Borderless, Natural Shadows)
           Inspired by neumorphism.io — strictly seamless surfaces without forced insets
           ========================================================================== */
        :root {
            --nm-bg: #e0e5ec;
            
            /* Soft, diffused directional shadows */
            --nm-dark: rgba(163, 177, 198, 0.45);
            --nm-dark-soft: rgba(163, 177, 198, 0.3);
            --nm-light: rgba(255, 255, 255, 0.9);

            /* Typography & Accents */
            --nm-accent: #2563eb;
            --nm-accent-hover: #1d4ed8;
            --nm-success: #059669;
            --nm-text-main: #334155;
            --nm-text-muted: #64748b;
            --nm-text-sub: #475569;

            /* Soft Extrusions */
            --nm-shadow-xs: 3px 3px 8px var(--nm-dark-soft), -3px -3px 8px var(--nm-light);
            --nm-shadow-sm: 5px 5px 12px var(--nm-dark-soft), -5px -5px 12px var(--nm-light);
            --nm-shadow-md: 8px 8px 20px var(--nm-dark), -8px -8px 20px var(--nm-light);
            --nm-shadow-lg: 12px 12px 28px var(--nm-dark), -12px -12px 28px var(--nm-light);
            --nm-shadow-hover: 15px 15px 34px var(--nm-dark), -15px -15px 34px var(--nm-light);

            /* Gentle Pressed State (only for active clicks) */
            --nm-shadow-pressed: inset 3px 3px 6px var(--nm-dark), inset -3px -3px 6px var(--nm-light);

            --radius-pill: 50px;
            --radius-card: 26px;
            --radius-btn: 14px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            border: none;
            outline: none;
        }

        body {
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            background-color: var(--nm-bg);
            color: var(--nm-text-main);
            line-height: 1.6;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            padding: 2.5rem 1.25rem 3.5rem;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            width: 100%;
        }

        /* ==========================================================================
           Hero Section — Soft Floating Panel
           ========================================================================== */
        header.hero-panel {
            background: var(--nm-bg);
            border-radius: 32px;
            box-shadow: var(--nm-shadow-lg);
            padding: 3rem 2.5rem;
            margin-bottom: 2.75rem;
            position: relative;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: var(--nm-bg);
            box-shadow: var(--nm-shadow-xs);
            padding: 0.4rem 1.1rem;
            border-radius: var(--radius-pill);
            font-size: 0.8rem;
            font-weight: 700;
            color: var(--nm-accent);
            letter-spacing: 0.03em;
            margin-bottom: 1.25rem;
        }

        .hero-badge .dot {
            width: 7px;
            height: 7px;
            background-color: var(--nm-success);
            border-radius: 50%;
        }

        .hero-title {
            font-size: clamp(2.1rem, 4vw, 2.85rem);
            font-weight: 800;
            letter-spacing: -0.03em;
            color: var(--nm-text-main);
            line-height: 1.2;
            margin-bottom: 0.75rem;
        }

        .hero-desc {
            font-size: 1.05rem;
            color: var(--nm-text-muted);
            max-width: 740px;
            margin-bottom: 2rem;
            line-height: 1.65;
        }

        /* Biodata Mahasiswa — Clean, Soft & Unforced */
        .hero-identity {
            display: flex;
            flex-wrap: wrap;
            gap: 1.25rem;
            align-items: center;
        }

        .identity-chip {
            background: var(--nm-bg);
            box-shadow: var(--nm-shadow-xs);
            border-radius: 14px;
            padding: 0.65rem 1.15rem;
            display: flex;
            align-items: baseline;
            gap: 0.5rem;
        }

        .identity-chip .label {
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: var(--nm-text-muted);
        }

        .identity-chip .value {
            font-size: 0.95rem;
            font-weight: 700;
            color: var(--nm-text-main);
        }

        /* ==========================================================================
           Stats Strip
           ========================================================================== */
        .stats-strip {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1.5rem;
            margin-bottom: 3.5rem;
        }

        .stat-card {
            background: var(--nm-bg);
            box-shadow: var(--nm-shadow-md);
            border-radius: 20px;
            padding: 1.35rem 1.5rem;
            display: flex;
            align-items: center;
            gap: 1.25rem;
            transition: transform 0.25s ease, box-shadow 0.25s ease;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--nm-shadow-lg);
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            background: var(--nm-bg);
            box-shadow: var(--nm-shadow-xs);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            flex-shrink: 0;
            color: var(--nm-accent);
        }

        .stat-info h4 {
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: var(--nm-text-muted);
            margin-bottom: 0.15rem;
        }

        .stat-info p {
            font-size: 1.35rem;
            font-weight: 800;
            color: var(--nm-text-main);
        }

        /* ==========================================================================
           Cards Catalog
           ========================================================================== */
        .section-header {
            text-align: center;
            margin-bottom: 2.75rem;
        }

        .section-header h2 {
            font-size: 2rem;
            font-weight: 800;
            letter-spacing: -0.02em;
            color: var(--nm-text-main);
            margin-bottom: 0.5rem;
        }

        .section-header p {
            color: var(--nm-text-muted);
            font-size: 1.05rem;
        }

        .cards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 2rem;
        }

        .card {
            background: var(--nm-bg);
            box-shadow: var(--nm-shadow-md);
            border-radius: var(--radius-card);
            padding: 2.25rem 2rem;
            display: flex;
            flex-direction: column;
            transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.25s cubic-bezier(0.16, 1, 0.3, 1);
            position: relative;
        }

        .card:hover {
            transform: translateY(-4px);
            box-shadow: var(--nm-shadow-hover);
        }

        /* Featured Card: Jobsheet 8 (Subtle Highlight) */
        .card.featured {
            background: linear-gradient(145deg, #e4eaf2, #dbe2eb);
        }

        .card-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.15rem;
        }

        .js-badge {
            background: var(--nm-bg);
            box-shadow: var(--nm-shadow-xs);
            padding: 0.35rem 0.85rem;
            border-radius: var(--radius-pill);
            font-size: 0.775rem;
            font-weight: 700;
            color: var(--nm-text-sub);
            letter-spacing: 0.02em;
        }

        .card.featured .js-badge {
            color: var(--nm-accent);
            font-weight: 800;
        }

        .type-pill {
            font-size: 0.725rem;
            font-weight: 600;
            padding: 0.3rem 0.75rem;
            border-radius: var(--radius-pill);
            color: var(--nm-text-muted);
        }

        .card.featured .type-pill {
            background: var(--nm-accent);
            color: #ffffff;
            box-shadow: 0 4px 10px rgba(37, 99, 235, 0.3);
        }

        .card-title {
            font-size: 1.275rem;
            font-weight: 800;
            letter-spacing: -0.015em;
            margin-bottom: 0.65rem;
            line-height: 1.35;
        }

        .card-title a {
            color: var(--nm-text-main);
            text-decoration: none;
            transition: color 0.15s ease;
        }

        .card-title a:hover {
            color: var(--nm-accent);
        }

        .card-desc {
            font-size: 0.925rem;
            color: var(--nm-text-muted);
            margin-bottom: 1.4rem;
            flex: 1;
            line-height: 1.6;
        }

        .tech-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-bottom: 1.75rem;
        }

        .tech-tag {
            background: var(--nm-bg);
            box-shadow: var(--nm-shadow-xs);
            font-size: 0.725rem;
            font-weight: 600;
            padding: 0.25rem 0.65rem;
            border-radius: 8px;
            color: var(--nm-text-sub);
        }

        .card-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
            margin-top: auto;
        }

        /* ==========================================================================
           Soft Neumorphic Buttons
           ========================================================================== */
        .btn-nm {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.4rem;
            padding: 0.65rem 1.15rem;
            border-radius: var(--radius-btn);
            font-size: 0.875rem;
            font-weight: 700;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
            user-select: none;
        }

        /* Primary Button */
        .btn-nm-primary {
            background: var(--nm-bg);
            box-shadow: var(--nm-shadow-sm);
            color: var(--nm-accent);
            flex: 1;
        }

        .btn-nm-primary:hover {
            color: var(--nm-accent-hover);
            box-shadow: var(--nm-shadow-xs);
            transform: translateY(-1px);
        }

        .btn-nm-primary:active {
            box-shadow: var(--nm-shadow-pressed);
            transform: translateY(1px);
        }

        /* Featured Button */
        .card.featured .btn-nm-primary {
            background: linear-gradient(145deg, #2563eb, #1d4ed8);
            box-shadow: 4px 4px 12px rgba(37, 99, 235, 0.35), -4px -4px 12px var(--nm-light);
            color: #ffffff;
        }

        .card.featured .btn-nm-primary:hover {
            background: linear-gradient(145deg, #2a6bf2, #1a49cc);
            box-shadow: 2px 2px 6px rgba(37, 99, 235, 0.3), -2px -2px 6px var(--nm-light);
        }

        .card.featured .btn-nm-primary:active {
            box-shadow: inset 3px 3px 6px rgba(0, 0, 0, 0.25);
        }

        /* Secondary Button */
        .btn-nm-secondary {
            background: var(--nm-bg);
            box-shadow: var(--nm-shadow-xs);
            color: var(--nm-text-sub);
            padding: 0.65rem 0.95rem;
        }

        .btn-nm-secondary:hover {
            color: var(--nm-text-main);
            box-shadow: 2px 2px 5px var(--nm-dark-soft), -2px -2px 5px var(--nm-light);
            transform: translateY(-1px);
        }

        .btn-nm-secondary:active {
            box-shadow: var(--nm-shadow-pressed);
            transform: translateY(1px);
        }

        /* ==========================================================================
           Footer
           ========================================================================== */
        footer.page-footer {
            margin-top: 4rem;
            background: var(--nm-bg);
            border-radius: 24px;
            box-shadow: var(--nm-shadow-md);
            padding: 2.25rem 1.5rem;
            text-align: center;
            color: var(--nm-text-muted);
            font-size: 0.925rem;
        }

        footer.page-footer strong {
            color: var(--nm-text-main);
        }

        @media (max-width: 768px) {
            body {
                padding: 1.25rem 0.85rem 2.5rem;
            }
            header.hero-panel {
                padding: 2rem 1.5rem;
                border-radius: 24px;
            }
            .cards-grid {
                grid-template-columns: 1fr;
            }
            .hero-identity {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.75rem;
                width: 100%;
            }
            .identity-chip {
                width: 100%;
                justify-content: space-between;
            }
        }
    </style>
</head>
<body>

    <div class="container">

        <!-- Soft Hero Panel -->
        <header class="hero-panel">
            <div class="hero-badge">
                <span class="dot"></span>
                <span>Portal Praktikum &bull; TI-2D</span>
            </div>
            <h1 class="hero-title">Desain &amp; Pemrograman Web</h1>
            <p class="hero-desc">Kompilasi dan repositori pengerjaan praktikum modul perpustakaan (SIMPUS-Mini) mulai dari HTML dasar hingga integrasi basis data PostgreSQL ter-deploy di Vercel.</p>
            
            <div class="hero-identity">
                <div class="identity-chip">
                    <span class="label">Nama:</span>
                    <span class="value">Mohammad Daanii Althaaf Reivan Fadhlillah</span>
                </div>
                <div class="identity-chip">
                    <span class="label">NIM:</span>
                    <span class="value">254107020123</span>
                </div>
                <div class="identity-chip">
                    <span class="label">Kelas:</span>
                    <span class="value">TI-2D (Teknik Informatika)</span>
                </div>
            </div>
        </header>

        <!-- Stats Strip -->
        <section class="stats-strip">
            <div class="stat-card">
                <div class="stat-icon">📚</div>
                <div class="stat-info">
                    <h4>Total Modul</h4>
                    <p>8 Selesai</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">📖</div>
                <div class="stat-info">
                    <h4>Data Buku (DB)</h4>
                    <p><?php echo htmlspecialchars($totalBuku); ?></p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">👥</div>
                <div class="stat-info">
                    <h4>Data Anggota (DB)</h4>
                    <p><?php echo htmlspecialchars($totalAnggota); ?></p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">⚡</div>
                <div class="stat-info">
                    <h4>Deployment</h4>
                    <p style="font-size: 1.05rem; color: var(--nm-success); font-weight: 800;">Live on Vercel</p>
                </div>
            </div>
        </section>

        <!-- Cards Catalog -->
        <section>
            <div class="section-header">
                <h2>Katalog Modul Praktikum</h2>
                <p>Silakan pilih salah satu modul di bawah untuk menguji fungsionalitas dan tampilan aplikasi.</p>
            </div>

            <div class="cards-grid">

                <!-- Jobsheet 1 -->
                <article class="card">
                    <div class="card-top">
                        <span class="js-badge">Jobsheet 01</span>
                        <span class="type-pill">Static HTML</span>
                    </div>
                    <h3 class="card-title"><a href="/jobsheet1/">HTML Dasar &amp; Tag Semantik</a></h3>
                    <p class="card-desc">Pondasi dasar struktur dokumen web SIMPUS-Mini menggunakan elemen semantik HTML5 murni tanpa format CSS.</p>
                    <div class="tech-tags">
                        <span class="tech-tag">HTML5</span>
                        <span class="tech-tag">Semantic Elements</span>
                        <span class="tech-tag">Hyperlink</span>
                    </div>
                    <div class="card-actions">
                        <a href="/jobsheet1/" class="btn-nm btn-nm-primary">Buka Modul &rarr;</a>
                        <a href="/jobsheet1/buku/list.html" class="btn-nm btn-nm-secondary">Buku</a>
                        <a href="/jobsheet1/anggota/list.html" class="btn-nm btn-nm-secondary">Anggota</a>
                    </div>
                </article>

                <!-- Jobsheet 2 -->
                <article class="card">
                    <div class="card-top">
                        <span class="js-badge">Jobsheet 02</span>
                        <span class="type-pill">HTML + CSS</span>
                    </div>
                    <h3 class="card-title"><a href="/jobsheet2/">Dasar CSS &amp; Visual Styling</a></h3>
                    <p class="card-desc">Pengaplikasian CSS eksternal, aturan hierarki pewarnaan, tipografi, box model, dan styling elemen antarmuka.</p>
                    <div class="tech-tags">
                        <span class="tech-tag">CSS3</span>
                        <span class="tech-tag">Box Model</span>
                        <span class="tech-tag">External Stylesheet</span>
                    </div>
                    <div class="card-actions">
                        <a href="/jobsheet2/" class="btn-nm btn-nm-primary">Buka Modul &rarr;</a>
                        <a href="/jobsheet2/buku/list.html" class="btn-nm btn-nm-secondary">Buku</a>
                        <a href="/jobsheet2/anggota/list.html" class="btn-nm btn-nm-secondary">Anggota</a>
                    </div>
                </article>

                <!-- Jobsheet 3 -->
                <article class="card">
                    <div class="card-top">
                        <span class="js-badge">Jobsheet 03</span>
                        <span class="type-pill">Responsive UI</span>
                    </div>
                    <h3 class="card-title"><a href="/jobsheet3/">Layout Responsif &amp; Framework CSS</a></h3>
                    <p class="card-desc">Desain responsif multi-perangkat menggunakan Bootstrap 5, navbar kolaps, dan pengaturan tata letak form modern.</p>
                    <div class="tech-tags">
                        <span class="tech-tag">Bootstrap 5</span>
                        <span class="tech-tag">Responsive Grid</span>
                        <span class="tech-tag">Flexbox</span>
                    </div>
                    <div class="card-actions">
                        <a href="/jobsheet3/" class="btn-nm btn-nm-primary">Buka Modul &rarr;</a>
                        <a href="/jobsheet3/buku/list.html" class="btn-nm btn-nm-secondary">Buku</a>
                        <a href="/jobsheet3/anggota/list.html" class="btn-nm btn-nm-secondary">Anggota</a>
                    </div>
                </article>

                <!-- Jobsheet 4 -->
                <article class="card">
                    <div class="card-top">
                        <span class="js-badge">Jobsheet 04</span>
                        <span class="type-pill">UI/UX Design</span>
                    </div>
                    <h3 class="card-title"><a href="/jobsheet4/">Wireframing &amp; Infografis Desain</a></h3>
                    <p class="card-desc">Perancangan arsitektur antarmuka, infografis alur sistem perpustakaan, dan spesifikasi wireframe terstruktur.</p>
                    <div class="tech-tags">
                        <span class="tech-tag">Wireframing</span>
                        <span class="tech-tag">UI/UX</span>
                        <span class="tech-tag">Infografis</span>
                    </div>
                    <div class="card-actions">
                        <a href="/jobsheet4/" class="btn-nm btn-nm-primary">Buka Modul &rarr;</a>
                        <a href="/jobsheet4/buku/list.html" class="btn-nm btn-nm-secondary">Buku</a>
                        <a href="/jobsheet4/anggota/list.html" class="btn-nm btn-nm-secondary">Anggota</a>
                    </div>
                </article>

                <!-- Jobsheet 5 -->
                <article class="card">
                    <div class="card-top">
                        <span class="js-badge">Jobsheet 05</span>
                        <span class="type-pill">Client Script</span>
                    </div>
                    <h3 class="card-title"><a href="/jobsheet5/">Manipulasi DOM &amp; Validasi Form JS</a></h3>
                    <p class="card-desc">Interaktivitas form sisi klien menggunakan JavaScript, validasi input waktu nyata, dan feedback error interaktif.</p>
                    <div class="tech-tags">
                        <span class="tech-tag">JavaScript</span>
                        <span class="tech-tag">DOM Events</span>
                        <span class="tech-tag">Client Validation</span>
                    </div>
                    <div class="card-actions">
                        <a href="/jobsheet5/" class="btn-nm btn-nm-primary">Buka Modul &rarr;</a>
                        <a href="/jobsheet5/buku/list.html" class="btn-nm btn-nm-secondary">Buku</a>
                        <a href="/jobsheet5/anggota/list.html" class="btn-nm btn-nm-secondary">Anggota</a>
                    </div>
                </article>

                <!-- Jobsheet 6 -->
                <article class="card">
                    <div class="card-top">
                        <span class="js-badge">Jobsheet 06</span>
                        <span class="type-pill">Async Data</span>
                    </div>
                    <h3 class="card-title"><a href="/jobsheet6/">Pengolahan Data JSON &amp; Fetch API</a></h3>
                    <p class="card-desc">Pemuatan data katalog dan anggota secara asinkron dari berkas JSON eksternal dengan indikator pemuatan (loading state).</p>
                    <div class="tech-tags">
                        <span class="tech-tag">JSON</span>
                        <span class="tech-tag">Fetch API</span>
                        <span class="tech-tag">Async / Await</span>
                    </div>
                    <div class="card-actions">
                        <a href="/jobsheet6/" class="btn-nm btn-nm-primary">Buka Modul &rarr;</a>
                        <a href="/jobsheet6/buku/list.html" class="btn-nm btn-nm-secondary">Buku</a>
                        <a href="/jobsheet6/anggota/list.html" class="btn-nm btn-nm-secondary">Anggota</a>
                    </div>
                </article>

                <!-- Jobsheet 7 -->
                <article class="card">
                    <div class="card-top">
                        <span class="js-badge">Jobsheet 07</span>
                        <span class="type-pill">Server-side PHP</span>
                    </div>
                    <h3 class="card-title"><a href="/jobsheet7/">PHP Modular &amp; Session Handling</a></h3>
                    <p class="card-desc">Arsitektur server-side dengan modular header/footer PHP, validasi form sisi server, dan penyimpanan data via Session.</p>
                    <div class="tech-tags">
                        <span class="tech-tag">PHP 8</span>
                        <span class="tech-tag">Modular Includes</span>
                        <span class="tech-tag">$_SESSION State</span>
                    </div>
                    <div class="card-actions">
                        <a href="/jobsheet7/" class="btn-nm btn-nm-primary">Buka Modul &rarr;</a>
                        <a href="/jobsheet7/buku/list.php" class="btn-nm btn-nm-secondary">Buku</a>
                        <a href="/jobsheet7/anggota/list.php" class="btn-nm btn-nm-secondary">Anggota</a>
                    </div>
                </article>

                <!-- Jobsheet 8 (Featured Highlight) -->
                <article class="card featured">
                    <div class="card-top">
                        <span class="js-badge">Jobsheet 08 &bull; Terakhir</span>
                        <span class="type-pill">Full Stack CRUD</span>
                    </div>
                    <h3 class="card-title"><a href="/jobsheet8/">PHP PDO &amp; Database PostgreSQL</a></h3>
                    <p class="card-desc">Integrasi basis data relasional penuh menggunakan PDO, transaksi CRUD lengkap (Tambah, Edit, Hapus), query agregasi statistik, dan multi-driver serverless.</p>
                    <div class="tech-tags">
                        <span class="tech-tag">PostgreSQL</span>
                        <span class="tech-tag">PHP PDO</span>
                        <span class="tech-tag">Prepared Statement</span>
                        <span class="tech-tag">Serverless Ready</span>
                    </div>
                    <div class="card-actions">
                        <a href="/jobsheet8/" class="btn-nm btn-nm-primary">Buka Aplikasi SIMPUS &rarr;</a>
                        <a href="/jobsheet8/buku/list.php" class="btn-nm btn-nm-secondary">Daftar Buku</a>
                        <a href="/jobsheet8/anggota/list.php" class="btn-nm btn-nm-secondary">Daftar Anggota</a>
                    </div>
                </article>

            </div>
        </section>

        <!-- Footer -->
        <footer class="page-footer">
            <p>&copy; 2026 <strong>SIMPUS-Mini Multi-Jobsheet Showcase</strong> &bull; Dibuat oleh Mohammad Daanii Althaaf Reivan Fadhlillah (TI-2D / 254107020123)</p>
            <p style="font-size: 0.825rem; margin-top: 0.35rem; color: var(--nm-text-muted);">Jurusan Teknologi Informasi &mdash; Politeknik Negeri Malang</p>
        </footer>

    </div>

</body>
</html>
