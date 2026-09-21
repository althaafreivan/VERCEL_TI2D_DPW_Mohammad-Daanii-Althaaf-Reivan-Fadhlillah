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
            $dbStatus = 'Terhubung (PostgreSQL / Supabase)';
        }
    } catch (Throwable $e) {
        $dbStatus = 'Tersedia via Fallback';
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
           Neumorphism Design System (Berdasarkan Spesifikasi neumorphism.io)
           Base Surface: #e0e5ec
           Shadow Light: #ffffff (-x, -y)
           Shadow Dark:  #a3b1c6 (+x, +y)
           ========================================================================== */
        :root {
            --nm-bg: #e0e5ec;
            --nm-light: #ffffff;
            --nm-dark: #a3b1c6;
            --nm-dark-soft: #b8b9be;
            --nm-accent: #2563eb;
            --nm-accent-hover: #1d4ed8;
            --nm-success: #10b981;
            --nm-text-main: #2d3748;
            --nm-text-muted: #64748b;
            --nm-text-sub: #475569;

            /* Flat Extrusion */
            --nm-flat-sm: 4px 4px 8px var(--nm-dark), -4px -4px 8px var(--nm-light);
            --nm-flat-md: 8px 8px 16px var(--nm-dark), -8px -8px 16px var(--nm-light);
            --nm-flat-lg: 12px 12px 24px var(--nm-dark), -12px -12px 24px var(--nm-light);
            --nm-flat-xl: 16px 16px 32px var(--nm-dark), -16px -16px 32px var(--nm-light);

            /* Inset / Sunken Wells */
            --nm-inset-xs: inset 2px 2px 5px var(--nm-dark), inset -2px -2px 5px var(--nm-light);
            --nm-inset-sm: inset 4px 4px 8px var(--nm-dark), inset -4px -4px 8px var(--nm-light);
            --nm-inset-md: inset 6px 6px 12px var(--nm-dark), inset -6px -6px 12px var(--nm-light);

            /* Convex / Concave */
            --nm-convex: linear-gradient(145deg, #f0f5fd, #cacfd4);
            --nm-concave: linear-gradient(145deg, #cacfd4, #f0f5fd);

            /* Border Radii */
            --radius-pill: 50px;
            --radius-card: 28px;
            --radius-btn: 14px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            background-color: var(--nm-bg);
            color: var(--nm-text-main);
            line-height: 1.6;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            padding: 2rem 1rem 3rem;
        }

        .container {
            max-width: 1240px;
            margin: 0 auto;
            width: 100%;
        }

        /* ==========================================================================
           Hero Header Neumorphic Box
           ========================================================================== */
        header.hero-neumorphic {
            background: var(--nm-bg);
            border-radius: 36px;
            box-shadow: var(--nm-flat-xl);
            padding: 3rem 2.5rem;
            margin-bottom: 2.5rem;
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.4);
        }

        .hero-top-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: var(--nm-bg);
            box-shadow: var(--nm-inset-sm);
            padding: 0.45rem 1.15rem;
            border-radius: var(--radius-pill);
            font-size: 0.8rem;
            font-weight: 700;
            color: var(--nm-accent);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 1.25rem;
        }

        .hero-top-badge .dot {
            width: 8px;
            height: 8px;
            background-color: var(--nm-success);
            border-radius: 50%;
            box-shadow: 0 0 6px var(--nm-success);
        }

        .hero-title {
            font-size: clamp(2rem, 3.8vw, 2.85rem);
            font-weight: 800;
            letter-spacing: -0.03em;
            color: var(--nm-text-main);
            line-height: 1.2;
            margin-bottom: 0.85rem;
        }

        .hero-desc {
            font-size: 1.05rem;
            color: var(--nm-text-muted);
            max-width: 780px;
            margin-bottom: 2rem;
        }

        .hero-identity-well {
            background: var(--nm-bg);
            box-shadow: var(--nm-inset-md);
            border-radius: 20px;
            padding: 1.25rem 1.75rem;
            display: flex;
            flex-wrap: wrap;
            gap: 1.75rem;
            align-items: center;
            max-width: fit-content;
        }

        .identity-item {
            display: flex;
            flex-direction: column;
            gap: 0.15rem;
        }

        .identity-item .label {
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: var(--nm-text-muted);
        }

        .identity-item .value {
            font-size: 0.975rem;
            font-weight: 700;
            color: var(--nm-text-main);
        }

        /* ==========================================================================
           Stats Strip
           ========================================================================== */
        .stats-strip {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1.75rem;
            margin-bottom: 3.5rem;
        }

        .stat-card {
            background: var(--nm-bg);
            box-shadow: var(--nm-flat-md);
            border-radius: 22px;
            padding: 1.4rem 1.5rem;
            display: flex;
            align-items: center;
            gap: 1.25rem;
            border: 1px solid rgba(255, 255, 255, 0.4);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--nm-flat-lg);
        }

        .stat-icon-well {
            width: 52px;
            height: 52px;
            border-radius: 16px;
            background: var(--nm-bg);
            box-shadow: var(--nm-inset-sm);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            flex-shrink: 0;
            color: var(--nm-accent);
        }

        .stat-info h4 {
            font-size: 0.775rem;
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
           Catalog Section & Neumorphic Cards Grid
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
            grid-template-columns: repeat(auto-fill, minmax(360px, 1fr));
            gap: 2.25rem;
        }

        .card {
            background: var(--nm-bg);
            box-shadow: var(--nm-flat-lg);
            border-radius: var(--radius-card);
            padding: 2.25rem 2rem;
            display: flex;
            flex-direction: column;
            border: 1px solid rgba(255, 255, 255, 0.45);
            transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.25s cubic-bezier(0.16, 1, 0.3, 1);
            position: relative;
        }

        .card:hover {
            transform: translateY(-4px);
            box-shadow: 16px 16px 32px var(--nm-dark), -16px -16px 32px var(--nm-light);
        }

        /* Featured Card: Jobsheet 8 (Convex Surface) */
        .card.featured {
            background: linear-gradient(145deg, #ebf0f7, #d8dee6);
            box-shadow: 14px 14px 28px var(--nm-dark), -14px -14px 28px var(--nm-light);
            border: 2px solid rgba(37, 99, 235, 0.15);
        }

        .card.featured:hover {
            box-shadow: 18px 18px 36px var(--nm-dark), -18px -18px 36px var(--nm-light);
        }

        .card-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.15rem;
        }

        .js-badge-well {
            background: var(--nm-bg);
            box-shadow: var(--nm-inset-xs);
            padding: 0.35rem 0.9rem;
            border-radius: var(--radius-pill);
            font-size: 0.775rem;
            font-weight: 700;
            color: var(--nm-text-sub);
            letter-spacing: 0.03em;
        }

        .card.featured .js-badge-well {
            box-shadow: var(--nm-inset-sm);
            color: var(--nm-accent);
            font-weight: 800;
        }

        .type-pill {
            font-size: 0.725rem;
            font-weight: 700;
            padding: 0.3rem 0.75rem;
            border-radius: var(--radius-pill);
            background: var(--nm-bg);
            box-shadow: var(--nm-flat-sm);
            color: var(--nm-text-muted);
        }

        .card.featured .type-pill {
            background: var(--nm-accent);
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.35);
        }

        .card-title {
            font-size: 1.3rem;
            font-weight: 800;
            letter-spacing: -0.015em;
            margin-bottom: 0.75rem;
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
            margin-bottom: 1.5rem;
            flex: 1;
            line-height: 1.6;
        }

        .tech-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-bottom: 1.75rem;
        }

        .tech-tag-well {
            background: var(--nm-bg);
            box-shadow: var(--nm-inset-xs);
            font-size: 0.725rem;
            font-weight: 700;
            padding: 0.25rem 0.65rem;
            border-radius: 8px;
            color: var(--nm-text-sub);
        }

        .card-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
            padding-top: 1.25rem;
            border-top: 1px solid rgba(163, 177, 198, 0.25);
        }

        /* ==========================================================================
           Neumorphic Buttons
           ========================================================================== */
        .btn-nm {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.4rem;
            padding: 0.7rem 1.15rem;
            border-radius: var(--radius-btn);
            font-size: 0.875rem;
            font-weight: 700;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
            user-select: none;
            border: none;
            outline: none;
        }

        /* Primary Action Button */
        .btn-nm-primary {
            background: var(--nm-bg);
            box-shadow: var(--nm-flat-sm);
            color: var(--nm-accent);
            flex: 1;
        }

        .btn-nm-primary:hover {
            color: var(--nm-accent-hover);
            box-shadow: 2px 2px 5px var(--nm-dark), -2px -2px 5px var(--nm-light);
            transform: translateY(-1px);
        }

        .btn-nm-primary:active {
            box-shadow: var(--nm-inset-sm);
            transform: translateY(1px);
        }

        /* Featured Button */
        .card.featured .btn-nm-primary {
            background: linear-gradient(145deg, #286cfb, #215be3);
            box-shadow: 5px 5px 12px #98a5b8, -5px -5px 12px #ffffff;
            color: #ffffff;
        }

        .card.featured .btn-nm-primary:hover {
            background: linear-gradient(145deg, #2e71fc, #1d55d8);
            box-shadow: 2px 2px 6px #98a5b8, -2px -2px 6px #ffffff;
        }

        .card.featured .btn-nm-primary:active {
            box-shadow: inset 3px 3px 6px rgba(0, 0, 0, 0.3), inset -3px -3px 6px rgba(255, 255, 255, 0.2);
        }

        /* Secondary / Outline Button */
        .btn-nm-secondary {
            background: var(--nm-bg);
            box-shadow: var(--nm-flat-sm);
            color: var(--nm-text-sub);
            padding: 0.7rem 0.95rem;
        }

        .btn-nm-secondary:hover {
            color: var(--nm-text-main);
            box-shadow: 2px 2px 5px var(--nm-dark), -2px -2px 5px var(--nm-light);
            transform: translateY(-1px);
        }

        .btn-nm-secondary:active {
            box-shadow: var(--nm-inset-xs);
            transform: translateY(1px);
        }

        /* ==========================================================================
           Page Footer
           ========================================================================== */
        footer.page-footer {
            margin-top: 4rem;
            background: var(--nm-bg);
            border-radius: 28px;
            box-shadow: var(--nm-flat-md);
            padding: 2.25rem 1.5rem;
            text-align: center;
            color: var(--nm-text-muted);
            font-size: 0.925rem;
            border: 1px solid rgba(255, 255, 255, 0.4);
        }

        footer.page-footer strong {
            color: var(--nm-text-main);
        }

        @media (max-width: 768px) {
            body {
                padding: 1rem 0.75rem 2rem;
            }
            header.hero-neumorphic {
                padding: 2rem 1.5rem;
                border-radius: 24px;
            }
            .cards-grid {
                grid-template-columns: 1fr;
            }
            .hero-identity-well {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.85rem;
                width: 100%;
            }
        }
    </style>
</head>
<body>

    <div class="container">

        <!-- Neumorphic Hero Panel -->
        <header class="hero-neumorphic">
            <div class="hero-top-badge">
                <span class="dot"></span>
                <span>Portal Praktikum &bull; TI-2D</span>
            </div>
            <h1 class="hero-title">Desain &amp; Pemrograman Web</h1>
            <p class="hero-desc">Kompilasi dan repositori pengerjaan praktikum modul perpustakaan (SIMPUS-Mini) mulai dari HTML dasar hingga integrasi basis data PostgreSQL ter-deploy di Vercel.</p>
            
            <div class="hero-identity-well">
                <div class="identity-item">
                    <span class="label">Nama Mahasiswa</span>
                    <span class="value">Mohammad Daanii Althaaf Reivan Fadhlillah</span>
                </div>
                <div class="identity-item">
                    <span class="label">Nomor Induk Mahasiswa</span>
                    <span class="value">254107020123</span>
                </div>
                <div class="identity-item">
                    <span class="label">Kelas / Jurusan</span>
                    <span class="value">TI-2D &bull; Teknologi Informasi</span>
                </div>
            </div>
        </header>

        <!-- Stats Strip -->
        <section class="stats-strip">
            <div class="stat-card">
                <div class="stat-icon-well">📚</div>
                <div class="stat-info">
                    <h4>Total Modul</h4>
                    <p>8 Selesai</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon-well">📖</div>
                <div class="stat-info">
                    <h4>Data Buku (DB)</h4>
                    <p><?php echo htmlspecialchars($totalBuku); ?></p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon-well">👥</div>
                <div class="stat-info">
                    <h4>Data Anggota (DB)</h4>
                    <p><?php echo htmlspecialchars($totalAnggota); ?></p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon-well">⚡</div>
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
                        <span class="js-badge-well">Jobsheet 01</span>
                        <span class="type-pill">Static HTML</span>
                    </div>
                    <h3 class="card-title"><a href="/jobsheet1/">HTML Dasar &amp; Tag Semantik</a></h3>
                    <p class="card-desc">Pondasi dasar struktur dokumen web SIMPUS-Mini menggunakan elemen semantik HTML5 murni tanpa format CSS.</p>
                    <div class="tech-tags">
                        <span class="tech-tag-well">HTML5</span>
                        <span class="tech-tag-well">Semantic Elements</span>
                        <span class="tech-tag-well">Hyperlink</span>
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
                        <span class="js-badge-well">Jobsheet 02</span>
                        <span class="type-pill">HTML + CSS</span>
                    </div>
                    <h3 class="card-title"><a href="/jobsheet2/">Dasar CSS &amp; Visual Styling</a></h3>
                    <p class="card-desc">Pengaplikasian CSS eksternal, aturan hierarki pewarnaan, tipografi, box model, dan styling elemen antarmuka.</p>
                    <div class="tech-tags">
                        <span class="tech-tag-well">CSS3</span>
                        <span class="tech-tag-well">Box Model</span>
                        <span class="tech-tag-well">External Stylesheet</span>
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
                        <span class="js-badge-well">Jobsheet 03</span>
                        <span class="type-pill">Responsive UI</span>
                    </div>
                    <h3 class="card-title"><a href="/jobsheet3/">Layout Responsif &amp; Framework CSS</a></h3>
                    <p class="card-desc">Desain responsif multi-perangkat menggunakan Bootstrap 5, navbar kolaps, dan pengaturan tata letak form modern.</p>
                    <div class="tech-tags">
                        <span class="tech-tag-well">Bootstrap 5</span>
                        <span class="tech-tag-well">Responsive Grid</span>
                        <span class="tech-tag-well">Flexbox</span>
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
                        <span class="js-badge-well">Jobsheet 04</span>
                        <span class="type-pill">UI/UX Design</span>
                    </div>
                    <h3 class="card-title"><a href="/jobsheet4/">Wireframing &amp; Infografis Desain</a></h3>
                    <p class="card-desc">Perancangan arsitektur antarmuka, infografis alur sistem perpustakaan, dan spesifikasi wireframe terstruktur.</p>
                    <div class="tech-tags">
                        <span class="tech-tag-well">Wireframing</span>
                        <span class="tech-tag-well">UI/UX</span>
                        <span class="tech-tag-well">Infografis</span>
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
                        <span class="js-badge-well">Jobsheet 05</span>
                        <span class="type-pill">Client Script</span>
                    </div>
                    <h3 class="card-title"><a href="/jobsheet5/">Manipulasi DOM &amp; Validasi Form JS</a></h3>
                    <p class="card-desc">Interaktivitas form sisi klien menggunakan JavaScript, validasi input waktu nyata, dan feedback error interaktif.</p>
                    <div class="tech-tags">
                        <span class="tech-tag-well">JavaScript</span>
                        <span class="tech-tag-well">DOM Events</span>
                        <span class="tech-tag-well">Client Validation</span>
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
                        <span class="js-badge-well">Jobsheet 06</span>
                        <span class="type-pill">Async Data</span>
                    </div>
                    <h3 class="card-title"><a href="/jobsheet6/">Pengolahan Data JSON &amp; Fetch API</a></h3>
                    <p class="card-desc">Pemuatan data katalog dan anggota secara asinkron dari berkas JSON eksternal dengan indikator pemuatan (loading state).</p>
                    <div class="tech-tags">
                        <span class="tech-tag-well">JSON</span>
                        <span class="tech-tag-well">Fetch API</span>
                        <span class="tech-tag-well">Async / Await</span>
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
                        <span class="js-badge-well">Jobsheet 07</span>
                        <span class="type-pill">Server-side PHP</span>
                    </div>
                    <h3 class="card-title"><a href="/jobsheet7/">PHP Modular &amp; Session Handling</a></h3>
                    <p class="card-desc">Arsitektur server-side dengan modular header/footer PHP, validasi form sisi server, dan penyimpanan data via Session.</p>
                    <div class="tech-tags">
                        <span class="tech-tag-well">PHP 8</span>
                        <span class="tech-tag-well">Modular Includes</span>
                        <span class="tech-tag-well">$_SESSION State</span>
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
                        <span class="js-badge-well">Jobsheet 08 &bull; Terakhir</span>
                        <span class="type-pill">Full Stack CRUD</span>
                    </div>
                    <h3 class="card-title"><a href="/jobsheet8/">PHP PDO &amp; Database PostgreSQL</a></h3>
                    <p class="card-desc">Integrasi basis data relasional penuh menggunakan PDO, transaksi CRUD lengkap (Tambah, Edit, Hapus), query agregasi statistik, dan multi-driver serverless.</p>
                    <div class="tech-tags">
                        <span class="tech-tag-well">PostgreSQL</span>
                        <span class="tech-tag-well">PHP PDO</span>
                        <span class="tech-tag-well">Prepared Statement</span>
                        <span class="tech-tag-well">Serverless Ready</span>
                    </div>
                    <div class="card-actions">
                        <a href="/jobsheet8/" class="btn-nm btn-nm-primary">Buka Aplikasi SIMPUS &rarr;</a>
                        <a href="/jobsheet8/buku/list.php" class="btn-nm btn-nm-secondary">Daftar Buku</a>
                        <a href="/jobsheet8/anggota/list.php" class="btn-nm btn-nm-secondary">Daftar Anggota</a>
                    </div>
                </article>

            </div>
        </section>

        <!-- Neumorphic Footer -->
        <footer class="page-footer">
            <p>&copy; 2026 <strong>SIMPUS-Mini Multi-Jobsheet Showcase</strong> &bull; Dibuat oleh Mohammad Daanii Althaaf Reivan Fadhlillah (TI-2D / 254107020123)</p>
            <p style="font-size: 0.825rem; margin-top: 0.35rem; color: var(--nm-text-muted);">Jurusan Teknologi Informasi &mdash; Politeknik Negeri Malang</p>
        </footer>

    </div>

</body>
</html>
