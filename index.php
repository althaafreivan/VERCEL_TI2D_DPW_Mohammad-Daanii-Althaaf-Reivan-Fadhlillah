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
        :root {
            --primary: #2563eb;
            --primary-hover: #1d4ed8;
            --primary-light: #eff6ff;
            --surface: #ffffff;
            --background: #f8fafc;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --border: #e2e8f0;
            --radius-lg: 16px;
            --radius-md: 10px;
            --shadow-sm: 0 1px 3px rgba(0,0,0,0.06), 0 1px 2px rgba(0,0,0,0.04);
            --shadow-md: 0 4px 6px -1px rgba(0,0,0,0.07), 0 2px 4px -2px rgba(0,0,0,0.05);
            --shadow-hover: 0 10px 15px -3px rgba(37,99,235,0.12), 0 4px 6px -4px rgba(37,99,235,0.08);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            background-color: var(--background);
            color: var(--text-main);
            line-height: 1.6;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        header.hero {
            background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 50%, #3b82f6 100%);
            color: #ffffff;
            padding: 3.5rem 1.5rem 4.5rem;
            position: relative;
            overflow: hidden;
        }

        header.hero::after {
            content: "";
            position: absolute;
            bottom: -30px;
            left: 0;
            right: 0;
            height: 60px;
            background-color: var(--background);
            border-radius: 50% 50% 0 0;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1.5rem;
        }

        .hero-badge {
            display: inline-block;
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 0.25);
            padding: 0.35rem 0.85rem;
            border-radius: 999px;
            font-size: 0.85rem;
            font-weight: 600;
            letter-spacing: 0.03em;
            text-transform: uppercase;
            margin-bottom: 1rem;
        }

        .hero-title {
            font-size: clamp(2rem, 4vw, 2.75rem);
            font-weight: 800;
            letter-spacing: -0.02em;
            line-height: 1.2;
            margin-bottom: 0.75rem;
        }

        .hero-desc {
            font-size: 1.1rem;
            color: rgba(255, 255, 255, 0.9);
            max-width: 720px;
            margin-bottom: 1.75rem;
        }

        .hero-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 1.25rem;
            font-size: 0.95rem;
            color: rgba(255, 255, 255, 0.95);
            background: rgba(0, 0, 0, 0.12);
            padding: 0.85rem 1.25rem;
            border-radius: var(--radius-md);
            backdrop-filter: blur(4px);
            max-width: fit-content;
        }

        .hero-meta span strong {
            color: #ffffff;
        }

        main {
            flex: 1;
            padding: 1rem 0 4rem;
        }

        .stats-strip {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.25rem;
            margin-top: -2.5rem;
            margin-bottom: 3rem;
            position: relative;
            z-index: 10;
        }

        .stat-card {
            background: var(--surface);
            padding: 1.25rem 1.5rem;
            border-radius: var(--radius-lg);
            border: 1px solid var(--border);
            box-shadow: var(--shadow-md);
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            background: var(--primary-light);
            color: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            flex-shrink: 0;
        }

        .stat-info h4 {
            font-size: 0.825rem;
            color: var(--text-muted);
            text-transform: uppercase;
            font-weight: 600;
        }

        .stat-info p {
            font-size: 1.4rem;
            font-weight: 800;
            color: var(--text-main);
        }

        .section-header {
            text-align: center;
            margin-bottom: 2.5rem;
        }

        .section-header h2 {
            font-size: 1.85rem;
            font-weight: 800;
            color: var(--text-main);
            margin-bottom: 0.5rem;
        }

        .section-header p {
            color: var(--text-muted);
            font-size: 1.05rem;
        }

        .cards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 1.75rem;
        }

        .card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 1.75rem;
            box-shadow: var(--shadow-sm);
            display: flex;
            flex-direction: column;
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
            position: relative;
        }

        .card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-hover);
            border-color: #bfdbfe;
        }

        .card.featured {
            border: 2px solid #3b82f6;
            background: linear-gradient(180deg, #ffffff 0%, #f0f7ff 100%);
        }

        .card-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 0.85rem;
        }

        .js-badge {
            font-size: 0.8rem;
            font-weight: 700;
            padding: 0.3rem 0.75rem;
            border-radius: 999px;
            background: #f1f5f9;
            color: #334155;
            letter-spacing: 0.02em;
        }

        .card.featured .js-badge {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .pill-tag {
            font-size: 0.75rem;
            font-weight: 600;
            padding: 0.25rem 0.6rem;
            border-radius: 6px;
            background: #e0f2fe;
            color: #0369a1;
        }

        .card-title {
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 0.6rem;
            line-height: 1.35;
        }

        .card-title a {
            color: var(--text-main);
            text-decoration: none;
            transition: color 0.15s;
        }

        .card-title a:hover {
            color: var(--primary);
        }

        .card-desc {
            font-size: 0.925rem;
            color: var(--text-muted);
            margin-bottom: 1.25rem;
            flex: 1;
        }

        .tech-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 0.4rem;
            margin-bottom: 1.5rem;
        }

        .tech-tag {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            font-size: 0.75rem;
            font-weight: 600;
            padding: 0.2rem 0.55rem;
            border-radius: 4px;
            color: #475569;
        }

        .card-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 0.6rem;
            padding-top: 1rem;
            border-top: 1px solid var(--border);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.4rem;
            padding: 0.55rem 1rem;
            border-radius: var(--radius-md);
            font-size: 0.875rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s;
            cursor: pointer;
        }

        .btn-primary {
            background: var(--primary);
            color: #ffffff;
            flex: 1;
        }

        .btn-primary:hover {
            background: var(--primary-hover);
        }

        .btn-outline {
            background: transparent;
            border: 1px solid var(--border);
            color: var(--text-main);
        }

        .btn-outline:hover {
            background: #f1f5f9;
            border-color: #cbd5e1;
        }

        footer.page-footer {
            background: #ffffff;
            border-top: 1px solid var(--border);
            padding: 2rem 0;
            text-align: center;
            color: var(--text-muted);
            font-size: 0.9rem;
        }

        footer.page-footer strong {
            color: var(--text-main);
        }

        @media (max-width: 640px) {
            .cards-grid {
                grid-template-columns: 1fr;
            }
            .hero-meta {
                flex-direction: column;
                gap: 0.4rem;
            }
        }
    </style>
</head>
<body>

    <header class="hero">
        <div class="container">
            <span class="hero-badge">Portal Praktikum &bull; TI-2D</span>
            <h1 class="hero-title">Desain &amp; Pemrograman Web</h1>
            <p class="hero-desc">Kompilasi dan repositori pengerjaan praktikum modul perpustakaan (SIMPUS-Mini) mulai dari HTML dasar hingga integrasi basis data PostgreSQL.</p>
            <div class="hero-meta">
                <span>Nama: <strong>Mohammad Daanii Althaaf Reivan Fadhlillah</strong></span>
                <span>NIM: <strong>254107020123</strong></span>
                <span>Kelas: <strong>TI 2D</strong></span>
            </div>
        </div>
    </header>

    <main class="container">
        <section class="stats-strip">
            <div class="stat-card">
                <div class="stat-icon">📚</div>
                <div class="stat-info">
                    <h4>Total Jobsheet</h4>
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
                    <h4>Status Deployment</h4>
                    <p style="font-size: 1rem; color: #16a34a; font-weight: 700;">Live on Vercel</p>
                </div>
            </div>
        </section>

        <section>
            <div class="section-header">
                <h2>Katalog Modul Praktikum</h2>
                <p>Pilih salah satu modul di bawah ini untuk menguji dan meninjau implementasi aplikasi.</p>
            </div>

            <div class="cards-grid">

                <!-- Jobsheet 1 -->
                <article class="card">
                    <div class="card-top">
                        <span class="js-badge">Jobsheet 01</span>
                        <span class="pill-tag">Static HTML</span>
                    </div>
                    <h3 class="card-title"><a href="/jobsheet1/">HTML Dasar &amp; Tag Semantik</a></h3>
                    <p class="card-desc">Pondasi dasar struktur dokumen web SIMPUS-Mini menggunakan elemen semantik HTML5 murni tanpa format CSS.</p>
                    <div class="tech-tags">
                        <span class="tech-tag">HTML5</span>
                        <span class="tech-tag">Semantic Tags</span>
                        <span class="tech-tag">Hyperlink</span>
                    </div>
                    <div class="card-actions">
                        <a href="/jobsheet1/" class="btn btn-primary">Buka Aplikasi &rarr;</a>
                        <a href="/jobsheet1/buku/list.html" class="btn btn-outline">Buku</a>
                        <a href="/jobsheet1/anggota/list.html" class="btn btn-outline">Anggota</a>
                    </div>
                </article>

                <!-- Jobsheet 2 -->
                <article class="card">
                    <div class="card-top">
                        <span class="js-badge">Jobsheet 02</span>
                        <span class="pill-tag">HTML + CSS</span>
                    </div>
                    <h3 class="card-title"><a href="/jobsheet2/">Dasar CSS &amp; Visual Styling</a></h3>
                    <p class="card-desc">Pengaplikasian CSS eksternal, aturan hierarki pewarnaan, tipografi, box model, dan styling elemen antarmuka.</p>
                    <div class="tech-tags">
                        <span class="tech-tag">CSS3</span>
                        <span class="tech-tag">Box Model</span>
                        <span class="tech-tag">External Stylesheet</span>
                    </div>
                    <div class="card-actions">
                        <a href="/jobsheet2/" class="btn btn-primary">Buka Aplikasi &rarr;</a>
                        <a href="/jobsheet2/buku/list.html" class="btn btn-outline">Buku</a>
                        <a href="/jobsheet2/anggota/list.html" class="btn btn-outline">Anggota</a>
                    </div>
                </article>

                <!-- Jobsheet 3 -->
                <article class="card">
                    <div class="card-top">
                        <span class="js-badge">Jobsheet 03</span>
                        <span class="pill-tag">Responsive UI</span>
                    </div>
                    <h3 class="card-title"><a href="/jobsheet3/">Layout Responsif &amp; Framework CSS</a></h3>
                    <p class="card-desc">Desain responsif multi-perangkat menggunakan Bootstrap 5, navbar kolaps, dan pengaturan tata letak form modern.</p>
                    <div class="tech-tags">
                        <span class="tech-tag">Bootstrap 5</span>
                        <span class="tech-tag">Responsive Grid</span>
                        <span class="tech-tag">Flexbox</span>
                    </div>
                    <div class="card-actions">
                        <a href="/jobsheet3/" class="btn btn-primary">Buka Aplikasi &rarr;</a>
                        <a href="/jobsheet3/buku/list.html" class="btn btn-outline">Buku</a>
                        <a href="/jobsheet3/anggota/list.html" class="btn btn-outline">Anggota</a>
                    </div>
                </article>

                <!-- Jobsheet 4 -->
                <article class="card">
                    <div class="card-top">
                        <span class="js-badge">Jobsheet 04</span>
                        <span class="pill-tag">UI/UX Design</span>
                    </div>
                    <h3 class="card-title"><a href="/jobsheet4/">Wireframing &amp; Infografis Desain</a></h3>
                    <p class="card-desc">Perancangan arsitektur antarmuka, infografis alur sistem perpustakaan, dan spesifikasi wireframe terstruktur.</p>
                    <div class="tech-tags">
                        <span class="tech-tag">Wireframing</span>
                        <span class="tech-tag">UI/UX</span>
                        <span class="tech-tag">Infografis</span>
                    </div>
                    <div class="card-actions">
                        <a href="/jobsheet4/" class="btn btn-primary">Buka Aplikasi &rarr;</a>
                        <a href="/jobsheet4/buku/list.html" class="btn btn-outline">Buku</a>
                        <a href="/jobsheet4/anggota/list.html" class="btn btn-outline">Anggota</a>
                    </div>
                </article>

                <!-- Jobsheet 5 -->
                <article class="card">
                    <div class="card-top">
                        <span class="js-badge">Jobsheet 05</span>
                        <span class="pill-tag">Client Script</span>
                    </div>
                    <h3 class="card-title"><a href="/jobsheet5/">Manipulasi DOM &amp; Validasi Form JS</a></h3>
                    <p class="card-desc">Interaktivitas form sisi klien menggunakan JavaScript, validasi input waktu nyata, dan feedback error interaktif.</p>
                    <div class="tech-tags">
                        <span class="tech-tag">JavaScript</span>
                        <span class="tech-tag">DOM Events</span>
                        <span class="tech-tag">Client Validation</span>
                    </div>
                    <div class="card-actions">
                        <a href="/jobsheet5/" class="btn btn-primary">Buka Aplikasi &rarr;</a>
                        <a href="/jobsheet5/buku/list.html" class="btn btn-outline">Buku</a>
                        <a href="/jobsheet5/anggota/list.html" class="btn btn-outline">Anggota</a>
                    </div>
                </article>

                <!-- Jobsheet 6 -->
                <article class="card">
                    <div class="card-top">
                        <span class="js-badge">Jobsheet 06</span>
                        <span class="pill-tag">Async Data</span>
                    </div>
                    <h3 class="card-title"><a href="/jobsheet6/">Pengolahan Data JSON &amp; Fetch API</a></h3>
                    <p class="card-desc">Pemuatan data katalog dan anggota secara asinkron dari berkas JSON eksternal dengan indikator pemuatan (loading state).</p>
                    <div class="tech-tags">
                        <span class="tech-tag">JSON</span>
                        <span class="tech-tag">Fetch API</span>
                        <span class="tech-tag">Async / Await</span>
                    </div>
                    <div class="card-actions">
                        <a href="/jobsheet6/" class="btn btn-primary">Buka Aplikasi &rarr;</a>
                        <a href="/jobsheet6/buku/list.html" class="btn btn-outline">Buku</a>
                        <a href="/jobsheet6/anggota/list.html" class="btn btn-outline">Anggota</a>
                    </div>
                </article>

                <!-- Jobsheet 7 -->
                <article class="card">
                    <div class="card-top">
                        <span class="js-badge">Jobsheet 07</span>
                        <span class="pill-tag">Server-side PHP</span>
                    </div>
                    <h3 class="card-title"><a href="/jobsheet7/">PHP Modular &amp; Session Handling</a></h3>
                    <p class="card-desc">Arsitektur server-side dengan modular header/footer PHP, validasi form sisi server, dan penyimpanan data via Session.</p>
                    <div class="tech-tags">
                        <span class="tech-tag">PHP 8</span>
                        <span class="tech-tag">Modular Includes</span>
                        <span class="tech-tag">$_SESSION State</span>
                    </div>
                    <div class="card-actions">
                        <a href="/jobsheet7/" class="btn btn-primary">Buka Aplikasi &rarr;</a>
                        <a href="/jobsheet7/buku/list.php" class="btn btn-outline">Buku</a>
                        <a href="/jobsheet7/anggota/list.php" class="btn btn-outline">Anggota</a>
                    </div>
                </article>

                <!-- Jobsheet 8 (Highlight) -->
                <article class="card featured">
                    <div class="card-top">
                        <span class="js-badge">Jobsheet 08 &bull; Terakhir</span>
                        <span class="pill-tag" style="background: #2563eb; color: #ffffff;">Full Stack CRUD</span>
                    </div>
                    <h3 class="card-title"><a href="/jobsheet8/">PHP PDO &amp; Database PostgreSQL</a></h3>
                    <p class="card-desc">Integrasi basis data relasional penuh menggunakan PDO (PHP Data Objects), kueri prepared statement terproteksi, fitur Edit, Hapus, dan Ringkasan Statistik live.</p>
                    <div class="tech-tags">
                        <span class="tech-tag">PostgreSQL</span>
                        <span class="tech-tag">PHP PDO</span>
                        <span class="tech-tag">Prepared Statement</span>
                        <span class="tech-tag">Serverless Ready</span>
                    </div>
                    <div class="card-actions">
                        <a href="/jobsheet8/" class="btn btn-primary" style="font-weight: 700;">Buka Aplikasi SIMPUS &rarr;</a>
                        <a href="/jobsheet8/buku/list.php" class="btn btn-outline">Daftar Buku</a>
                        <a href="/jobsheet8/anggota/list.php" class="btn btn-outline">Daftar Anggota</a>
                    </div>
                </article>

            </div>
        </section>
    </main>

    <footer class="page-footer">
        <div class="container">
            <p>&copy; 2026 <strong>SIMPUS-Mini Multi-Jobsheet Portal</strong> &bull; Dibuat oleh Mohammad Daanii Althaaf Reivan Fadhlillah (TI-2D / 254107020123)</p>
            <p style="font-size: 0.8rem; margin-top: 0.25rem;">Jurusan Teknologi Informasi &mdash; Politeknik Negeri Malang</p>
        </div>
    </footer>

</body>
</html>
