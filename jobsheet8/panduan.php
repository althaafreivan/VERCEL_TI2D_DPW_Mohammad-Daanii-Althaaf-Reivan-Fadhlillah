<?php
// jobsheet8/panduan.php — Buku Panduan & Dokumentasi Teknis Jobsheet 8 (PixelGallery)
// Dirancang mandiri dengan layout dokumentasi modern, clean, dan mudah dibaca tanpa neumorphism.
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buku Panduan & Dokumentasi Teknis | PixelGallery (Jobsheet 8)</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        /* ===== CSS Reset & Theme Variables ===== */
        *, *::before, *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        :root {
            --bg-page: #f8fafc;
            --bg-surface: #ffffff;
            --bg-sidebar: #ffffff;
            --bg-code: #0f172a;
            --border-color: #e2e8f0;
            --border-subtle: #f1f5f9;
            
            --text-heading: #0f172a;
            --text-body: #334155;
            --text-muted: #64748b;
            --text-light: #94a3b8;

            --primary: #2563eb;
            --primary-hover: #1d4ed8;
            --primary-light: #eff6ff;
            --primary-border: #bfdbfe;

            --success-bg: #f0fdf4;
            --success-border: #bbf7d0;
            --success-text: #166534;

            --warning-bg: #fffbeb;
            --warning-border: #fde68a;
            --warning-text: #92400e;

            --font-main: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            --font-code: 'JetBrains Mono', Consolas, Monaco, monospace;

            --sidebar-width: 290px;
            --header-height: 64px;
        }

        html {
            scroll-behavior: smooth;
            font-size: 16px;
        }

        body {
            font-family: var(--font-main);
            background-color: var(--bg-page);
            color: var(--text-body);
            line-height: 1.7;
            -webkit-font-smoothing: antialiased;
        }

        a {
            color: var(--primary);
            text-decoration: none;
            transition: color 0.15s ease;
        }

        a:hover {
            color: var(--primary-hover);
            text-decoration: underline;
        }

        /* ===== Top Navigation Header ===== */
        .top-nav {
            position: sticky;
            top: 0;
            height: var(--header-height);
            background: rgba(255, 255, 255, 0.96);
            backdrop-filter: blur(8px);
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1.75rem;
            z-index: 100;
        }

        .nav-brand-group {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .mobile-toggle {
            display: none;
            background: transparent;
            border: 1px solid var(--border-color);
            border-radius: 6px;
            padding: 0.4rem 0.65rem;
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--text-heading);
            cursor: pointer;
        }

        .brand-badge {
            background: #dbeafe;
            color: #1e40af;
            font-size: 0.725rem;
            font-weight: 700;
            padding: 0.2rem 0.55rem;
            border-radius: 999px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .brand-title {
            font-size: 1.05rem;
            font-weight: 700;
            color: var(--text-heading);
            letter-spacing: -0.01em;
        }

        .brand-title span {
            color: var(--primary);
        }

        .top-nav-actions {
            display: flex;
            align-items: center;
            gap: 0.65rem;
        }

        .nav-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            font-size: 0.85rem;
            font-weight: 600;
            padding: 0.45rem 0.95rem;
            border-radius: 6px;
            text-decoration: none;
            transition: all 0.15s ease;
        }

        .nav-btn-secondary {
            background: #f1f5f9;
            color: var(--text-heading);
            border: 1px solid var(--border-color);
        }

        .nav-btn-secondary:hover {
            background: #e2e8f0;
            text-decoration: none;
        }

        .nav-btn-primary {
            background: var(--primary);
            color: #ffffff;
        }

        .nav-btn-primary:hover {
            background: var(--primary-hover);
            color: #ffffff;
            text-decoration: none;
        }

        /* ===== Layout Shell ===== */
        .layout-shell {
            display: flex;
            min-height: calc(100vh - var(--header-height));
        }

        /* ===== Left Sidebar ===== */
        .sidebar {
            width: var(--sidebar-width);
            flex-shrink: 0;
            background: var(--bg-sidebar);
            border-right: 1px solid var(--border-color);
            position: sticky;
            top: var(--header-height);
            height: calc(100vh - var(--header-height));
            overflow-y: auto;
            padding: 1.5rem 1rem 2rem 1.25rem;
        }

        .sidebar-section {
            margin-bottom: 1.75rem;
        }

        .sidebar-heading {
            font-size: 0.725rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--text-light);
            margin-bottom: 0.65rem;
            padding-left: 0.6rem;
        }

        .sidebar-links {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 0.2rem;
        }

        .sidebar-link {
            display: block;
            padding: 0.45rem 0.65rem;
            font-size: 0.875rem;
            font-weight: 500;
            color: var(--text-body);
            border-radius: 6px;
            text-decoration: none;
            transition: all 0.15s ease;
            border-left: 2px solid transparent;
        }

        .sidebar-link:hover {
            background: #f8fafc;
            color: var(--primary);
            text-decoration: none;
        }

        .sidebar-link.active {
            background: var(--primary-light);
            color: var(--primary);
            font-weight: 600;
            border-left-color: var(--primary);
        }

        /* ===== Main Content Area ===== */
        .main-content {
            flex: 1;
            max-width: 900px;
            margin: 0 auto;
            padding: 2.5rem 3rem 5rem 3rem;
            overflow-x: hidden;
        }

        /* ===== Typography & Section Styling ===== */
        .doc-header {
            margin-bottom: 2.75rem;
            padding-bottom: 2rem;
            border-bottom: 1px solid var(--border-color);
        }

        .doc-pill-tag {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            background: var(--primary-light);
            color: var(--primary);
            font-size: 0.8rem;
            font-weight: 700;
            padding: 0.25rem 0.7rem;
            border-radius: 999px;
            margin-bottom: 0.75rem;
            border: 1px solid var(--primary-border);
        }

        .doc-title {
            font-size: 2.2rem;
            font-weight: 800;
            color: var(--text-heading);
            letter-spacing: -0.025em;
            line-height: 1.25;
            margin-bottom: 0.85rem;
        }

        .doc-lead {
            font-size: 1.1rem;
            color: var(--text-muted);
            line-height: 1.7;
        }

        .doc-section {
            margin-bottom: 3.5rem;
            scroll-margin-top: calc(var(--header-height) + 1.5rem);
        }

        .doc-section h2 {
            font-size: 1.55rem;
            font-weight: 800;
            color: var(--text-heading);
            letter-spacing: -0.02em;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .doc-section h3 {
            font-size: 1.15rem;
            font-weight: 700;
            color: var(--text-heading);
            margin: 1.75rem 0 0.65rem 0;
        }

        .doc-section p {
            margin-bottom: 1rem;
            color: var(--text-body);
        }

        .doc-section ul,
        .doc-section ol {
            margin-bottom: 1.25rem;
            padding-left: 1.5rem;
        }

        .doc-section li {
            margin-bottom: 0.4rem;
        }

        /* ===== Callout Boxes ===== */
        .callout {
            border-radius: 8px;
            padding: 1rem 1.25rem;
            margin: 1.25rem 0;
            border-left: 4px solid;
            font-size: 0.925rem;
            line-height: 1.6;
        }

        .callout-info {
            background-color: var(--primary-light);
            border-color: var(--primary);
            color: #1e3a8a;
        }

        .callout-success {
            background-color: var(--success-bg);
            border-color: #10b981;
            color: var(--success-text);
        }

        .callout-warning {
            background-color: var(--warning-bg);
            border-color: #f59e0b;
            color: var(--warning-text);
        }

        .callout-title {
            font-weight: 700;
            margin-bottom: 0.35rem;
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }

        /* ===== Pipeline Steps Grid ===== */
        .step-cards {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1rem;
            margin: 1.25rem 0;
        }

        .step-card {
            background: var(--bg-surface);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 1.15rem 1.25rem;
            display: flex;
            gap: 1rem;
            align-items: flex-start;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03);
        }

        .step-badge {
            width: 32px;
            height: 32px;
            background: var(--primary);
            color: #ffffff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 0.9rem;
            flex-shrink: 0;
        }

        .step-body h4 {
            font-size: 1rem;
            font-weight: 700;
            color: var(--text-heading);
            margin-bottom: 0.25rem;
        }

        .step-body p {
            font-size: 0.9rem;
            color: var(--text-muted);
            margin: 0;
        }

        /* ===== Code Blocks & Inline Code ===== */
        code {
            font-family: var(--font-code);
            font-size: 0.85em;
            background-color: #f1f5f9;
            color: #0f172a;
            padding: 0.15rem 0.45rem;
            border-radius: 4px;
            border: 1px solid #e2e8f0;
        }

        .code-container {
            border-radius: 8px;
            overflow: hidden;
            margin: 1.25rem 0;
            border: 1px solid #1e293b;
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.1);
        }

        .code-header {
            background: #1e293b;
            color: #94a3b8;
            padding: 0.6rem 1rem;
            font-size: 0.775rem;
            font-family: var(--font-code);
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid #334155;
        }

        .code-lang-tag {
            background: #334155;
            color: #f8fafc;
            padding: 0.15rem 0.5rem;
            border-radius: 4px;
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        pre {
            background: var(--bg-code);
            color: #f8fafc;
            padding: 1.15rem 1.25rem;
            overflow-x: auto;
            font-family: var(--font-code);
            font-size: 0.85rem;
            line-height: 1.6;
        }

        pre code {
            background: transparent;
            color: inherit;
            padding: 0;
            border: none;
            font-size: inherit;
        }

        .c-kw { color: #f472b6; font-weight: 600; }
        .c-fn { color: #60a5fa; }
        .c-str { color: #86efac; }
        .c-var { color: #fde047; }
        .c-com { color: #64748b; font-style: italic; }

        /* ===== FAQ / Cheat Sheet Accordion ===== */
        .faq-group {
            display: flex;
            flex-direction: column;
            gap: 0.85rem;
            margin-top: 1.25rem;
        }

        .faq-item {
            background: var(--bg-surface);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            overflow: hidden;
            transition: border-color 0.15s ease;
        }

        .faq-item[open] {
            border-color: var(--primary-border);
            box-shadow: 0 2px 8px rgba(37, 99, 235, 0.06);
        }

        .faq-summary {
            padding: 1rem 1.25rem;
            font-weight: 700;
            font-size: 0.975rem;
            color: var(--text-heading);
            cursor: pointer;
            list-style: none;
            display: flex;
            align-items: center;
            justify-content: space-between;
            user-select: none;
            background: #ffffff;
        }

        .faq-summary::-webkit-details-marker {
            display: none;
        }

        .faq-summary::after {
            content: "↓";
            font-size: 1rem;
            color: var(--text-muted);
            transition: transform 0.2s ease;
        }

        .faq-item[open] .faq-summary::after {
            transform: rotate(180deg);
            color: var(--primary);
        }

        .faq-body {
            padding: 1rem 1.25rem 1.25rem 1.25rem;
            font-size: 0.925rem;
            line-height: 1.65;
            color: var(--text-body);
            border-top: 1px solid var(--border-subtle);
            background: #fbfcfe;
        }

        .faq-keypoint {
            display: inline-block;
            background: #dbeafe;
            color: #1e40af;
            font-weight: 700;
            padding: 0.1rem 0.45rem;
            border-radius: 4px;
            font-size: 0.825rem;
            margin-right: 0.35rem;
        }

        /* ===== Responsive Breakpoints ===== */
        @media (max-width: 992px) {
            .mobile-toggle {
                display: block;
            }

            .sidebar {
                position: fixed;
                left: -100%;
                top: var(--header-height);
                z-index: 200;
                transition: left 0.25s ease;
                box-shadow: 4px 0 24px rgba(0, 0, 0, 0.15);
                width: 300px;
            }

            .sidebar.sidebar-open {
                left: 0;
            }

            .sidebar-backdrop {
                display: none;
                position: fixed;
                inset: 0;
                top: var(--header-height);
                background: rgba(15, 23, 42, 0.4);
                z-index: 150;
            }

            .sidebar-backdrop.active {
                display: block;
            }

            .main-content {
                padding: 2rem 1.5rem;
            }

            .doc-title {
                font-size: 1.75rem;
            }
        }
    </style>
</head>
<body>

    <!-- Top Navigation Bar -->
    <header class="top-nav">
        <div class="nav-brand-group">
            <button type="button" class="mobile-toggle" id="sidebarToggle" aria-label="Buka Menu">☰ Daftar Isi</button>
            <span class="brand-badge">Jobsheet 8</span>
            <div class="brand-title">PixelGallery <span>Documentation</span></div>
        </div>
        <div class="top-nav-actions">
            <a href="/" class="nav-btn nav-btn-secondary">&larr; Portal Jobsheet</a>
            <a href="/jobsheet8/" class="nav-btn nav-btn-primary">Buka Aplikasi PixelGallery &rarr;</a>
        </div>
    </header>

    <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

    <div class="layout-shell">
        <!-- Sticky Navigation Sidebar -->
        <aside class="sidebar" id="sidebarNav">
            <div class="sidebar-section">
                <div class="sidebar-heading">Ikhtisar Sistem</div>
                <ul class="sidebar-links">
                    <li><a href="#ringkasan" class="sidebar-link active">Ringkasan Aplikasi</a></li>
                    <li><a href="#arsitektur" class="sidebar-link">Alur Data &amp; Arsitektur</a></li>
                </ul>
            </div>

            <div class="sidebar-section">
                <div class="sidebar-heading">1. Struktur HTML &amp; CSS</div>
                <ul class="sidebar-links">
                    <li><a href="#html-form" class="sidebar-link">Form Upload Berkas</a></li>
                    <li><a href="#html-grid" class="sidebar-link">Grid Galeri Responsif</a></li>
                    <li><a href="#html-modular" class="sidebar-link">Modular Header &amp; Footer</a></li>
                </ul>
            </div>

            <div class="sidebar-section">
                <div class="sidebar-heading">2. Logika Backend PHP</div>
                <ul class="sidebar-links">
                    <li><a href="#php-koneksi" class="sidebar-link">Koneksi PDO Supabase</a></li>
                    <li><a href="#php-upload" class="sidebar-link">Upload &amp; Base64 Cloud</a></li>
                    <li><a href="#php-stream" class="sidebar-link">Streaming biner gambar.php</a></li>
                    <li><a href="#php-crud" class="sidebar-link">Pencarian &amp; Penghapusan</a></li>
                    <li><a href="#php-session" class="sidebar-link">Flash Message Session</a></li>
                </ul>
            </div>

            <div class="sidebar-section">
                <div class="sidebar-heading">3. JavaScript &amp; UX</div>
                <ul class="sidebar-links">
                    <li><a href="#js-nav" class="sidebar-link">Navigasi Hamburger</a></li>
                    <li><a href="#js-confirm" class="sidebar-link">Dialog Konfirmasi Hapus</a></li>
                    <li><a href="#js-validasi" class="sidebar-link">Validasi Form Sisi Klien</a></li>
                </ul>
            </div>

            <div class="sidebar-section">
                <div class="sidebar-heading">4. Evaluasi Dosen</div>
                <ul class="sidebar-links">
                    <li><a href="#cheat-sheet" class="sidebar-link">Cheat Sheet Tanya-Jawab</a></li>
                </ul>
            </div>
        </aside>

        <!-- Main Documentation Body -->
        <main class="main-content">
            <!-- Header Ringkasan -->
            <div class="doc-header">
                <span class="doc-pill-tag">Praktikum Desain &amp; Pemrograman Web</span>
                <h1 class="doc-title">Buku Panduan Teknis PixelGallery</h1>
                <p class="doc-lead">
                    Dokumentasi ringkas arsitektur aplikasi pengunggahan dan galeri gambar berbasis PHP 8, basis data PostgreSQL Supabase cloud, serta penanganan berkas serverless di Vercel.
                </p>
            </div>

            <!-- Bagian: Ringkasan Aplikasi -->
            <section class="doc-section" id="ringkasan">
                <h2>Ringkasan Aplikasi</h2>
                <p>
                    <strong>PixelGallery</strong> adalah implementasi tugas <strong>Jobsheet 08 (Full-Stack CRUD &amp; Basis Data Relasional)</strong>. Aplikasi ini berfokus pada alur lengkap pengelolaan gambar: pengguna dapat mengunggah file foto (JPG, PNG, WEBP), memberi metadata (judul, pengunggah, kategori, tahun, deskripsi), menjelajahi galeri dalam kartu responsif, mencari berdasarkan kata kunci, serta menghapus data.
                </p>
                <div class="callout callout-info">
                    <div class="callout-title">Tujuan Praktikum Jobsheet 8</div>
                    Menerapkan koneksi database relasional menggunakan ekstensi <strong>PHP PDO</strong>, mengamankan query dengan <strong>Prepared Statements</strong>, menangani file upload via <code>$_FILES</code>, dan merancang sistem yang kompatibel dengan arsitektur cloud serverless.
                </div>
            </section>

            <!-- Bagian: Arsitektur & Alur Data -->
            <section class="doc-section" id="arsitektur">
                <h2>Alur Data &amp; Arsitektur Sistem</h2>
                <p>
                    Aplikasi ini mengadopsi alur data <em>End-to-End</em> yang memisahkan logika antarmuka, validasi server, penyimpanan persisten cloud, dan penyajian data biner secara efisien:
                </p>

                <div class="step-cards">
                    <div class="step-card">
                        <div class="step-badge">1</div>
                        <div class="step-body">
                            <h4>Pengguna Mengisi Form &amp; Memilih Gambar</h4>
                            <p>Data teks dan file biner dikirim melalui HTTP POST dengan tipe payload multipart.</p>
                        </div>
                    </div>
                    <div class="step-card">
                        <div class="step-badge">2</div>
                        <div class="step-body">
                            <h4>Validasi Sisi Server &amp; Konversi Base64 (upload-asset.php)</h4>
                            <p>PHP memvalidasi status upload, whitelist ekstensi (JPG/PNG/WEBP), dan batas ukuran 5MB. Berkas di-encode ke Base64 Data URL agar aman di lingkungan serverless yang bersifat read-only.</p>
                        </div>
                    </div>
                    <div class="step-card">
                        <div class="step-badge">3</div>
                        <div class="step-body">
                            <h4>Penyimpanan Persisten ke Cloud Supabase PostgreSQL</h4>
                            <p>Query INSERT dieksekusi menggunakan Prepared Statement PDO ke tabel <code>galeri</code> di Supabase Seoul Pooler (port 6543).</p>
                        </div>
                    </div>
                    <div class="step-card">
                        <div class="step-badge">4</div>
                        <div class="step-body">
                            <h4>Penyajian Gambar Teroptimasi (gambar.php)</h4>
                            <p>Endpoint khusus biner mengalirkan data gambar langsung dengan header <code>Content-Type</code> dan HTTP Browser Cache sehingga ukuran dokumen HTML halaman katalog tetap ringan (&lt; 10 KB).</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Bagian: HTML Form -->
            <section class="doc-section" id="html-form">
                <h2>1. Struktur HTML &amp; CSS</h2>
                <h3>A. Form Upload Berkas (upload-asset.php)</h3>
                <p>
                    Secara default, form HTML mengirim data dengan encoding teks biasa. Untuk mengirimkan file gambar, form <strong>wajib</strong> menyertakan atribut <code>enctype="multipart/form-data"</code>. Tanpa atribut ini, berkas tidak akan masuk ke array <code>$_FILES</code> di server.
                </p>

                <div class="code-container">
                    <div class="code-header">
                        <span>collection/upload-asset.php</span>
                        <span class="code-lang-tag">HTML</span>
                    </div>
                    <pre><code><span class="c-com">&lt;!-- Form pengunggahan gambar --&gt;</span>
&lt;<span class="c-kw">form</span> <span class="c-var">action</span>=<span class="c-str">"upload-asset.php"</span> <span class="c-var">method</span>=<span class="c-str">"POST"</span> <span class="c-var">enctype</span>=<span class="c-str">"multipart/form-data"</span>&gt;
    &lt;<span class="c-kw">input</span> <span class="c-var">type</span>=<span class="c-str">"text"</span> <span class="c-var">name</span>=<span class="c-str">"judul"</span> <span class="c-var">placeholder</span>=<span class="c-str">"Judul foto"</span> <span class="c-var">required</span>&gt;
    &lt;<span class="c-kw">input</span> <span class="c-var">type</span>=<span class="c-str">"text"</span> <span class="c-var">name</span>=<span class="c-str">"pengunggah"</span> <span class="c-var">placeholder</span>=<span class="c-str">"Nama Anda"</span> <span class="c-var">required</span>&gt;
    &lt;<span class="c-kw">select</span> <span class="c-var">name</span>=<span class="c-str">"kategori"</span> <span class="c-var">required</span>&gt;
        &lt;<span class="c-kw">option</span> <span class="c-var">value</span>=<span class="c-str">"Wallpaper"</span>&gt;Wallpaper&lt;/<span class="c-kw">option</span>&gt;
        &lt;<span class="c-kw">option</span> <span class="c-var">value</span>=<span class="c-str">"Pemandangan"</span>&gt;Pemandangan &amp; Alam&lt;/<span class="c-kw">option</span>&gt;
        &lt;<span class="c-kw">option</span> <span class="c-var">value</span>=<span class="c-str">"Fotografi"</span>&gt;Fotografi Umum&lt;/<span class="c-kw">option</span>&gt;
    &lt;/<span class="c-kw">select</span>&gt;
    &lt;<span class="c-kw">input</span> <span class="c-var">type</span>=<span class="c-str">"file"</span> <span class="c-var">name</span>=<span class="c-str">"gambar"</span> <span class="c-var">accept</span>=<span class="c-str">"image/png, image/jpeg, image/webp"</span> <span class="c-var">required</span>&gt;
    &lt;<span class="c-kw">button</span> <span class="c-var">type</span>=<span class="c-str">"submit"</span>&gt;Unggah Gambar&lt;/<span class="c-kw">button</span>&gt;
&lt;/<span class="c-kw">form</span>&gt;</code></pre>
                </div>
            </section>

            <!-- Bagian: HTML Grid -->
            <section class="doc-section" id="html-grid">
                <h3>B. Grid Galeri Responsif (CSS Grid)</h3>
                <p>
                    Koleksi gambar disusun menggunakan <strong>CSS Grid</strong> modern dengan fungsi bawaan <code>repeat(auto-fill, minmax(280px, 1fr))</code>. Pendekatan ini membuat galeri otomatis responsif di semua ukuran layar (desktop 3-4 kolom, tablet 2 kolom, dan smartphone 1 kolom) tanpa library eksternal.
                </p>
                <div class="code-container">
                    <div class="code-header">
                        <span>assets/css/style.css</span>
                        <span class="code-lang-tag">CSS</span>
                    </div>
                    <pre><code><span class="c-fn">.gallery-grid</span> {
    <span class="c-var">display</span>: grid;
    <span class="c-var">grid-template-columns</span>: <span class="c-fn">repeat</span>(auto-fill, <span class="c-fn">minmax</span>(280px, 1fr));
    <span class="c-var">gap</span>: 1.5rem;
}</code></pre>
                </div>
            </section>

            <!-- Bagian: HTML Modular -->
            <section class="doc-section" id="html-modular">
                <h3>C. Modular Header &amp; Footer Layout</h3>
                <p>
                    Struktur halaman menerapkan prinsip modular DRY (<em>Don't Repeat Yourself</em>). Navigasi atas diletakkan di <code>includes/header.php</code> dan bagian bawah di <code>includes/footer.php</code>.
                </p>
                <p>
                    Variabel dinamis <code>$base</code> secara otomatis menghitung kedalaman direktori (<code>../</code>) menggunakan fungsi <code>dirname()</code> dan <code>substr_count()</code>, sehingga aset CSS/JS dan tautan navigasi tidak pernah rusak saat diakses dari subfolder lokal maupun root domain Vercel.
                </p>
            </section>

            <!-- Bagian: PHP Koneksi -->
            <section class="doc-section" id="php-koneksi">
                <h2>2. Logika Backend PHP &amp; Basis Data</h2>
                <h3>A. Koneksi Database PDO (includes/koneksi.php)</h3>
                <p>
                    Koneksi database dibuat menggunakan <strong>PHP Data Objects (PDO)</strong> dengan driver <code>pgsql</code>. Koneksi dirancang memiliki <em>failover</em>: memprioritaskan cloud PostgreSQL Supabase (port 6543 pooler IPv4), dan jika dijalankan di komputer lokal tanpa internet otomatis mencoba PostgreSQL lokal (port 5433 / 5432).
                </p>

                <div class="code-container">
                    <div class="code-header">
                        <span>includes/koneksi.php</span>
                        <span class="code-lang-tag">PHP</span>
                    </div>
                    <pre><code><span class="c-var">$dsn</span> = <span class="c-str">"pgsql:host={$host};port={$port};dbname={$db};sslmode=require"</span>;
<span class="c-var">$pdo</span> = <span class="c-kw">new</span> <span class="c-fn">PDO</span>(<span class="c-var">$dsn</span>, <span class="c-var">$user</span>, <span class="c-var">$pass</span>, [
    <span class="c-fn">PDO</span>::<span class="c-var">ATTR_ERRMODE</span>            => <span class="c-fn">PDO</span>::<span class="c-var">ERRMODE_EXCEPTION</span>,
    <span class="c-fn">PDO</span>::<span class="c-var">ATTR_DEFAULT_FETCH_MODE</span> => <span class="c-fn">PDO</span>::<span class="c-var">FETCH_ASSOC</span>,
    <span class="c-fn">PDO</span>::<span class="c-var">ATTR_EMULATE_PREPARES</span>   => <span class="c-kw">false</span>,
]);</code></pre>
                </div>
            </section>

            <!-- Bagian: PHP Upload -->
            <section class="doc-section" id="php-upload">
                <h3>B. Penanganan Upload File &amp; Base64 Serverless (upload-asset.php)</h3>
                <p>
                    Sebelum berkas disimpan, PHP menjalankan serangkaian validasi ketat:
                </p>
                <ol>
                    <li><strong>Status Unggah:</strong> Memeriksa <code>$_FILES['gambar']['error'] === UPLOAD_ERR_OK</code>.</li>
                    <li><strong>Ekstensi Berkas:</strong> Mengambil ekstensi menggunakan <code>pathinfo($nama, PATHINFO_EXTENSION)</code> dan mencocokkannya dengan daftar izin (<code>jpg, jpeg, png, webp, gif</code>).</li>
                    <li><strong>Batas Ukuran:</strong> Memastikan <code>$_FILES['gambar']['size'] &lt;= 5MB</code>.</li>
                </ol>

                <div class="callout callout-warning">
                    <div class="callout-title">Strategi Serverless Vercel Read-Only</div>
                    Pada serverless platform seperti Vercel, filesystem bersifat <em>read-only</em> dan <em>ephemeral</em> (sementara). File yang disimpan di folder fisik akan hilang saat container serverless mati. Karena itu, file biner dibaca via <code>file_get_contents()</code>, dikonversi menjadi format <strong>Base64 Data URL</strong>, dan disimpan persisten di kolom <code>file_gambar</code> (tipe <code>TEXT</code>) di PostgreSQL Supabase cloud.
                </div>

                <div class="code-container">
                    <div class="code-header">
                        <span>collection/upload-asset.php</span>
                        <span class="code-lang-tag">PHP</span>
                    </div>
                    <pre><code><span class="c-com">// 1. Baca isi berkas dan encode ke Base64 Data URL</span>
<span class="c-var">$konten</span> = <span class="c-fn">file_get_contents</span>(<span class="c-var">$_FILES</span>[<span class="c-str">'gambar'</span>][<span class="c-str">'tmp_name'</span>]);
<span class="c-var">$data_simpan</span> = <span class="c-str">'data:'</span> . <span class="c-var">$tipe_mime</span> . <span class="c-str">';base64,'</span> . <span class="c-fn">base64_encode</span>(<span class="c-var">$konten</span>);

<span class="c-com">// 2. Simpan menggunakan Prepared Statement anti-SQL Injection</span>
<span class="c-var">$sql</span> = <span class="c-str">"INSERT INTO galeri (judul, pengunggah, kategori, file_gambar, tahun, deskripsi) 
        VALUES (:judul, :pengunggah, :kategori, :file_gambar, :tahun, :deskripsi)"</span>;
<span class="c-var">$stmt</span> = <span class="c-var">$pdo</span>-><span class="c-fn">prepare</span>(<span class="c-var">$sql</span>);
<span class="c-var">$stmt</span>-><span class="c-fn">execute</span>([
    <span class="c-str">':judul'</span>       => <span class="c-var">$judul</span>,
    <span class="c-str">':pengunggah'</span>  => <span class="c-var">$pengunggah</span>,
    <span class="c-str">':kategori'</span>    => <span class="c-var">$kategori</span>,
    <span class="c-str">':file_gambar'</span> => <span class="c-var">$data_simpan</span>,
    <span class="c-str">':tahun'</span>       => (<span class="c-kw">int</span>)<span class="c-var">$tahun</span>,
    <span class="c-str">':deskripsi'</span>   => <span class="c-var">$deskripsi</span>
]);</code></pre>
                </div>
            </section>

            <!-- Bagian: PHP Streaming -->
            <section class="doc-section" id="php-stream">
                <h3>C. Streaming Biner Teroptimasi (gambar.php)</h3>
                <p>
                    Jika Base64 gambar disematkan langsung di dalam tag <code>&lt;img src="data:image/..."&gt;</code> pada halaman katalog, dokumen HTML akan berukuran hingga puluhan megabyte. Di Vercel, ini akan memicu error fatal <strong>500 FUNCTION_RESPONSE_PAYLOAD_TOO_LARGE</strong> (batas respon maksimal 4.5MB).
                </p>
                <p>
                    Solusinya adalah membuat endpoint <code>gambar.php?id=ID</code> yang membaca data gambar dari database, mendekode Base64 kembali ke format biner murni, dan mengirimkannya ke browser dengan header HTTP yang tepat:
                </p>

                <div class="code-container">
                    <div class="code-header">
                        <span>gambar.php</span>
                        <span class="code-lang-tag">PHP</span>
                    </div>
                    <pre><code><span class="c-var">$id</span> = (<span class="c-kw">int</span>)(<span class="c-var">$_GET</span>[<span class="c-str">'id'</span>] ?? <span class="c-str">0</span>);
<span class="c-var">$stmt</span> = <span class="c-var">$pdo</span>-><span class="c-fn">prepare</span>(<span class="c-str">"SELECT file_gambar FROM galeri WHERE id = :id"</span>);
<span class="c-var">$stmt</span>-><span class="c-fn">execute</span>([<span class="c-str">':id'</span> => <span class="c-var">$id</span>]);
<span class="c-var">$foto</span> = <span class="c-var">$stmt</span>-><span class="c-fn">fetch</span>(<span class="c-fn">PDO</span>::<span class="c-var">FETCH_ASSOC</span>);

<span class="c-com">// Kirim HTTP Header & stream biner</span>
<span class="c-fn">header</span>(<span class="c-str">"Content-Type: "</span> . <span class="c-var">$mime</span>);
<span class="c-fn">header</span>(<span class="c-str">"Cache-Control: public, max-age=86400"</span>); <span class="c-com">// Cache browser 24 jam</span>
<span class="c-kw">echo</span> <span class="c-fn">base64_decode</span>(<span class="c-var">$biner_data</span>);
<span class="c-kw">exit</span>;</code></pre>
                </div>
            </section>

            <!-- Bagian: PHP CRUD -->
            <section class="doc-section" id="php-crud">
                <h3>D. Pencarian &amp; Penghapusan Data (catalog.php)</h3>
                <p>
                    <strong>Pencarian:</strong> Menggunakan klausa SQL <code>ILIKE :q</code> (PostgreSQL <em>case-insensitive matching</em>) untuk mencocokkan kata kunci pada judul, pengunggah, atau kategori tanpa membedakan huruf besar/kecil.
                </p>
                <p>
                    <strong>Penghapusan:</strong> Menerima parameter GET <code>?hapus=ID</code>, divalidasi dengan <code>filter_var(..., FILTER_VALIDATE_INT)</code>, lalu mengeksekusi <code>DELETE FROM galeri WHERE id = :id</code>.
                </p>
            </section>

            <!-- Bagian: PHP Session -->
            <section class="doc-section" id="php-session">
                <h3>E. Flash Message Session State</h3>
                <p>
                    Pemberitahuan sukses atau gagal disimpan ke variabel sesi <code>$_SESSION['flash']</code> sebelum melakukan HTTP Redirect (<code>header("Location: ...")</code>). Saat halaman tujuan dimuat, notifikasi dirender dan langsung dibersihkan dengan <code>unset($_SESSION['flash'])</code> agar pesan tidak muncul berulang saat halaman di-refresh.
                </p>
            </section>

            <!-- Bagian: JavaScript -->
            <section class="doc-section" id="js-nav">
                <h2>3. Interaktivitas JavaScript (UX &amp; DOM)</h2>
                <h3>A. Mobile Navigation Toggle (Hamburger Menu)</h3>
                <p>
                    Pada tampilan mobile, menu navigasi disembunyikan. Saat tombol hamburger <code>#nav-toggle-btn</code> diklik, fungsi <code>initNavToggle()</code> memanipulasi class DOM dengan menjalankan <code>nav.classList.toggle("nav-open")</code> secara mulus tanpa reload halaman.
                </p>
            </section>

            <section class="doc-section" id="js-confirm">
                <h3>B. Konfirmasi Hapus Data Interaktif</h3>
                <p>
                    Tautan tombol hapus pada kartu galeri dilengkapi atribut inline:
                </p>
                <div class="code-container">
                    <div class="code-header">
                        <span>Inline JavaScript</span>
                        <span class="code-lang-tag">JS</span>
                    </div>
                    <pre><code><span class="c-kw">onclick</span>=<span class="c-str">"return confirm('Apakah Anda yakin ingin menghapus gambar ini?');"</span></code></pre>
                </div>
                <p>
                    Jika pengguna menekan tombol <em>Cancel</em>, fungsi <code>confirm()</code> mengembalikan nilai <code>false</code> dan peramban membatalkan aksi navigasi URL sehingga data tidak terhapus tanpa sengaja.
                </p>
            </section>

            <section class="doc-section" id="js-validasi">
                <h3>C. Validasi Sisi Klien (app.js)</h3>
                <p>
                    Event listener <code>submit</code> pada form upload memeriksa kelengkapan kolom wajib (judul, nama pengunggah, berkas foto) sebelum data dikirim ke server. Jika terdapat kolom kosong, form menampilkan pesan kesalahan secara instan di bawah input bersangkutan.
                </p>
            </section>

            <!-- Bagian: Cheat Sheet Evaluasi Dosen -->
            <section class="doc-section" id="cheat-sheet">
                <h2>4. Cheat Sheet Tanya-Jawab Evaluasi Dosen</h2>
                <p>
                    Gunakan poin-poin jawaban di bawah ini saat dosen menanyakan konsep teknis aplikasi:
                </p>

                <div class="faq-group">
                    <details class="faq-item" open>
                        <summary class="faq-summary">1. Mengapa memilih PHP PDO daripada ekstensi MySQLi?</summary>
                        <div class="faq-body">
                            <span class="faq-keypoint">Kunci Jawaban</span>
                            <strong>PDO bersifat database-agnostic:</strong> PDO mendukung berbagai driver basis data (PostgreSQL, MySQL, SQLite) hanya dengan mengganti string DSN, sedangkan MySQLi hanya terbatas pada MySQL. Selain itu, PDO menyediakan Prepared Statements native dan penanganan kesalahan berbasis Exception yang konsisten.
                        </div>
                    </details>

                    <details class="faq-item">
                        <summary class="faq-summary">2. Bagaimana aplikasi ini mencegah serangan SQL Injection?</summary>
                        <div class="faq-body">
                            <span class="faq-keypoint">Kunci Jawaban</span>
                            <strong>Menggunakan Prepared Statements &amp; Parameter Binding:</strong> Semua nilai dari input pengguna tidak pernah disambung langsung menggunakan konkatenasi string (<code>$sql = "SELECT ... " . $input</code>). Kami menggunakan placeholder (<code>:judul</code>) melalui <code>$pdo-&gt;prepare()</code> dan <code>$stmt-&gt;execute()</code>. Mesin basis data memperlakukan nilai tersebut murni sebagai data, bukan perintah SQL yang dapat dieksekusi.
                        </div>
                    </details>

                    <details class="faq-item">
                        <summary class="faq-summary">3. Bagaimana cara kerja upload file di serverless Vercel yang read-only?</summary>
                        <div class="faq-body">
                            <span class="faq-keypoint">Kunci Jawaban</span>
                            <strong>Encoding Base64 ke Supabase PostgreSQL:</strong> Di Vercel, filesystem bersifat read-only dan container serverless bersifat sementara (ephemeral). Jika file disimpan di folder fisik biasa, file akan hilang saat instance dimatikan. Karena itu, file biner dibaca dan diubah ke format <strong>Base64 Data URL</strong> yang tersimpan aman secara persisten di kolom <code>file_gambar</code> basis data cloud Supabase.
                        </div>
                    </details>

                    <details class="faq-item">
                        <summary class="faq-summary">4. Mengapa dibuat file gambar.php terpisah dan tidak dicetak langsung di src?</summary>
                        <div class="faq-body">
                            <span class="faq-keypoint">Kunci Jawaban</span>
                            <strong>Mencegah Payload Limit &amp; Mengaktifkan Cache Browser:</strong> Menaruh puluhan gambar Base64 langsung di dalam HTML akan membuat ukuran halaman membengkak hingga puluhan MB (memicu error Vercel <em>500 Function Response Payload Too Large</em>). Dengan endpoint <code>gambar.php?id=ID</code>, HTML tetap sangat kecil (&lt; 10 KB), gambar dialirkan secara independen sebagai biner murni, dan browser dapat melakukan HTTP caching (<code>Cache-Control: public, max-age=86400</code>).
                        </div>
                    </details>

                    <details class="faq-item">
                        <summary class="faq-summary">5. Apa fungsi htmlspecialchars() pada saat menampilkan data ke layar?</summary>
                        <div class="faq-body">
                            <span class="faq-keypoint">Kunci Jawaban</span>
                            <strong>Pencegahan Serangan XSS (Cross-Site Scripting):</strong> Fungsi <code>htmlspecialchars()</code> menyaring karakter khusus HTML (seperti <code>&lt;</code>, <code>&gt;</code>, <code>&amp;</code>, dan <code>"</code>) menjadi entitas karakter HTML yang aman (seperti <code>&amp;lt;</code> dan <code>&amp;gt;</code>). Hal ini mencegah eksekusi skrip berbahaya jika pengguna memasukkan tag <code>&lt;script&gt;</code> pada input judul atau pengunggah.
                        </div>
                    </details>
                </div>
            </section>

        </main>
    </div>

    <!-- Script Interaktif untuk Sidebar & TOC Active State -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const toggle = document.getElementById("sidebarToggle");
            const sidebar = document.getElementById("sidebarNav");
            const backdrop = document.getElementById("sidebarBackdrop");
            const links = document.querySelectorAll(".sidebar-link");

            function toggleSidebar() {
                sidebar.classList.toggle("sidebar-open");
                backdrop.classList.toggle("active");
            }

            if (toggle && sidebar && backdrop) {
                toggle.addEventListener("click", toggleSidebar);
                backdrop.addEventListener("click", toggleSidebar);
            }

            // Close drawer on link click in mobile
            links.forEach(link => {
                link.addEventListener("click", () => {
                    if (window.innerWidth <= 992) {
                        sidebar.classList.remove("sidebar-open");
                        backdrop.classList.remove("active");
                    }
                });
            });

            // Active section indicator on scroll
            const sections = document.querySelectorAll(".doc-section");
            window.addEventListener("scroll", () => {
                let current = "";
                sections.forEach(section => {
                    const top = section.offsetTop - 120;
                    if (window.pageYOffset >= top) {
                        current = section.getAttribute("id");
                    }
                });

                if (current) {
                    links.forEach(link => {
                        link.classList.remove("active");
                        if (link.getAttribute("href") === "#" + current) {
                            link.classList.add("active");
                        }
                    });
                }
            });
        });
    </script>
</body>
</html>
