<?php
$page_title = 'Buku Panduan & Dokumentasi Teknis';
include __DIR__ . '/includes/header.php';
?>

<div class="guide-wrapper">
    <!-- Topbar Navigasi -->
    <div class="page-topbar">
        <a href="<?php echo $base; ?>index.php" class="back-link">&larr; Kembali ke Beranda</a>
        <div style="display: flex; gap: 0.5rem;">
            <a href="<?php echo $base; ?>collection/upload-asset.php" class="btn btn-secondary" style="font-size: 0.8rem; padding: 0.4rem 0.8rem;">+ Upload Gambar</a>
            <a href="<?php echo $base; ?>collection/catalog.php" class="btn btn-primary" style="font-size: 0.8rem; padding: 0.4rem 0.8rem;">Buka Galeri &rarr;</a>
        </div>
    </div>

    <!-- Header Dokumen -->
    <div class="form-header text-center" style="margin-bottom: 2rem;">
        <span class="section-tag">Dokumentasi Jobsheet 8 &bull; Praktikum DPW</span>
        <h1 class="form-title" style="font-size: 1.85rem; margin-top: 0.35rem;">Buku Panduan Teknis PixelGallery</h1>
        <p class="form-subtitle" style="max-width: 720px; margin: 0.5rem auto 0;">
            Panduan komprehensif cara kerja arsitektur aplikasi, mulai dari struktur HTML, pemrosesan PHP &amp; database PostgreSQL Supabase, interaktivitas JavaScript, hingga tips menjawab pertanyaan evaluasi dosen.
        </p>
    </div>

    <!-- Ringkasan Arsitektur & Alur Data -->
    <div class="doc-section">
        <h3>
            <span class="doc-pill">Arsitektur</span>
            Alur Data Aplikasi (End-to-End Flow)
        </h3>
        <p style="margin-bottom: 1rem;">
            PixelGallery adalah aplikasi web galeri gambar yang mengimplementasikan operasi <strong>CRUD (Create, Read, Delete)</strong> penuh dengan arsitektur modern yang siap dijalankan di lingkungan lokal maupun cloud serverless (Vercel + Supabase PostgreSQL).
        </p>

        <div class="pipeline-list">
            <div class="pipeline-step">
                <span class="step-num">1</span>
                <div class="step-content">
                    <strong>Input Pengguna (HTML Form)</strong>
                    <p>Pengguna mengisi metadata gambar dan memilih berkas (JPG/PNG/WEBP) melalui form dengan atribut <code>enctype="multipart/form-data"</code>.</p>
                </div>
            </div>
            <div class="pipeline-step">
                <span class="step-num">2</span>
                <div class="step-content">
                    <strong>Validasi &amp; Konversi Serverless (PHP Backend)</strong>
                    <p>PHP memvalidasi ekstensi dan ukuran berkas dari array <code>$_FILES</code>, kemudian mengonversi biner berkas ke string <em>Data URL Base64</em> agar persisten di database cloud tanpa bergantung pada penyimpanan fisik serverless yang bersifat <em>read-only</em>.</p>
                </div>
            </div>
            <div class="pipeline-step">
                <span class="step-num">3</span>
                <div class="step-content">
                    <strong>Penyimpanan Persisten (Supabase PostgreSQL via PDO)</strong>
                    <p>Data disimpan menggunakan PHP PDO dengan <em>Prepared Statements</em> untuk mencegah celah keamanan SQL Injection.</p>
                </div>
            </div>
            <div class="pipeline-step">
                <span class="step-num">4</span>
                <div class="step-content">
                    <strong>Streaming Biner Gambar Efisien (<code>gambar.php</code>)</strong>
                    <p>Gambar disajikan via endpoint khusus biner yang mengirimkan HTTP header <code>Content-Type</code> dan HTTP Caching. Solusi ini menjaga dokumen HTML tetap sangat ringan (&lt; 10 KB) dan mencegah error <code>500 FUNCTION_RESPONSE_PAYLOAD_TOO_LARGE</code> pada Vercel.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Bagian 1: HTML & Antarmuka -->
    <div class="doc-section">
        <h3>
            <span class="doc-pill html">HTML &amp; CSS</span>
            1. Struktur Antarmuka &amp; Komponen Markup
        </h3>
        <p style="margin-bottom: 0.85rem;">
            Struktur antarmuka dirancang semantik dengan pendekatan modular dan desain <strong>Soft Neumorphism</strong> yang bersih tanpa animasi berat saat dimuat.
        </p>

        <h4 style="color: var(--text-title); font-size: 0.95rem; margin-top: 1rem; margin-bottom: 0.4rem;">A. Form Upload dengan <code>multipart/form-data</code></h4>
        <p style="font-size: 0.875rem; color: var(--text-body); margin-bottom: 0.5rem;">
            Secara default form HTML mengirimkan data dengan tipe teks URL-encoded. Saat mengirim file biner (gambar), form <strong>wajib</strong> menyertakan <code>enctype="multipart/form-data"</code> agar browser memecah payload menjadi bagian multipart yang dapat dibaca oleh PHP melalui variabel global <code>$_FILES</code>.
        </p>
        <pre><code>&lt;!-- Form Upload Gambar (upload-asset.php) --&gt;
&lt;form action="upload-asset.php" method="POST" enctype="multipart/form-data" class="nm-form-card"&gt;
    &lt;input type="text" name="judul" required&gt;
    &lt;input type="text" name="pengunggah" required&gt;
    &lt;select name="kategori" required&gt;...&lt;/select&gt;
    &lt;input type="number" name="tahun" required&gt;
    &lt;input type="file" name="gambar" accept="image/png, image/jpeg, image/webp" required&gt;
    &lt;button type="submit" class="btn btn-primary"&gt;Unggah Gambar&lt;/button&gt;
&lt;/form&gt;</code></pre>

        <h4 style="color: var(--text-title); font-size: 0.95rem; margin-top: 1.25rem; margin-bottom: 0.4rem;">B. Grid Galeri Responsif</h4>
        <p style="font-size: 0.875rem; color: var(--text-body); margin-bottom: 0.5rem;">
            Daftar foto dirender menggunakan CSS Grid (<code>display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));</code>). Layout ini secara otomatis menyesuaikan jumlah kolom berdasarkan lebar layar perangkat tanpa membutuhkan framework eksternal berat.
        </p>

        <h4 style="color: var(--text-title); font-size: 0.95rem; margin-top: 1.25rem; margin-bottom: 0.4rem;">C. Modular Header &amp; Footer</h4>
        <p style="font-size: 0.875rem; color: var(--text-body); margin-bottom: 0.5rem;">
            Bagian atas halaman (navigasi) dan bagian bawah halaman dipecah ke <code>includes/header.php</code> dan <code>includes/footer.php</code>. Variabel <code>$base</code> secara dinamis menghitung jarak folder relatif sehingga link aset CSS dan JS selalu tepat, baik saat dijalankan di root domain Vercel maupun di subfolder lokal.
        </p>
    </div>

    <!-- Bagian 2: Logika Pemrosesan PHP -->
    <div class="doc-section">
        <h3>
            <span class="doc-pill php">PHP Backend</span>
            2. Logika Pemrosesan Sisi Server (PHP &amp; Database)
        </h3>
        <p style="margin-bottom: 1rem;">
            Sisi server menangani validasi keamanan, manajemen sesi, pemrosesan file, dan transaksi basis data PostgreSQL.
        </p>

        <h4 style="color: var(--text-title); font-size: 0.95rem; margin-top: 0.5rem; margin-bottom: 0.4rem;">A. Koneksi Database PDO (<code>includes/koneksi.php</code>)</h4>
        <p style="font-size: 0.875rem; color: var(--text-body); margin-bottom: 0.5rem;">
            Menggunakan ekstensi <strong>PHP Data Objects (PDO)</strong> driver <code>pgsql</code>. Koneksi dirancang cerdas dengan mencoba koneksi cloud Supabase PostgreSQL terlebih dahulu via pooler port <code>6543</code> (IPv4), dan jika offline/lokal otomatis beralih ke PostgreSQL lokal (port <code>5433</code>/<code>5432</code>).
        </p>
        <pre><code>$dsn = "pgsql:host={$host};port={$port};dbname={$db};sslmode=require";
$pdo = new PDO($dsn, $user, $pass, [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
]);</code></pre>

        <h4 style="color: var(--text-title); font-size: 0.95rem; margin-top: 1.25rem; margin-bottom: 0.4rem;">B. Validasi Berkas &amp; Konversi Base64 (<code>upload-asset.php</code>)</h4>
        <p style="font-size: 0.875rem; color: var(--text-body); margin-bottom: 0.5rem;">
            File dari form diperiksa keabsahan dan keamanannya melalui 3 lapis validasi sebelum disimpan:
        </p>
        <ol style="margin-left: 1.25rem; font-size: 0.875rem; margin-bottom: 0.75rem;">
            <li><strong>Status Upload:</strong> Memastikan <code>$_FILES['gambar']['error'] === UPLOAD_ERR_OK</code>.</li>
            <li><strong>Whitelist Ekstensi:</strong> Menggunakan <code>pathinfo($nama_file, PATHINFO_EXTENSION)</code> hanya mengizinkan <code>jpg, jpeg, png, webp, gif</code>.</li>
            <li><strong>Batas Ukuran:</strong> Memastikan ukuran berkas <code>$_FILES['gambar']['size'] &lt;= 5MB</code>.</li>
        </ol>
        <pre><code>// Konversi gambar ke format Data URL Base64
$tipe_mime = $mime_types[$ekstensi] ?? 'image/jpeg';
$konten_gambar = file_get_contents($_FILES['gambar']['tmp_name']);
$data_simpan = 'data:' . $tipe_mime . ';base64,' . base64_encode($konten_gambar);

// Simpan ke database via Prepared Statement
$sql = "INSERT INTO galeri (judul, pengunggah, kategori, file_gambar, tahun, deskripsi) 
        VALUES (:judul, :pengunggah, :kategori, :file_gambar, :tahun, :deskripsi)";
$stmt = $pdo->prepare($sql);
$stmt->execute([
    ':judul'       => $judul,
    ':pengunggah'  => $pengunggah,
    ':kategori'    => $kategori,
    ':file_gambar' => $data_simpan,
    ':tahun'       => (int)$tahun,
    ':deskripsi'   => $deskripsi
]);</code></pre>

        <h4 style="color: var(--text-title); font-size: 0.95rem; margin-top: 1.25rem; margin-bottom: 0.4rem;">C. Streaming Gambar Khusus (<code>gambar.php</code>)</h4>
        <p style="font-size: 0.875rem; color: var(--text-body); margin-bottom: 0.5rem;">
            Daripada mencetak string Base64 langsung ke dalam atribut <code>src</code> dokumen HTML (yang dapat menghabiskan kuota memori serverless), file <code>gambar.php</code> bertindak sebagai <strong>Image Server</strong> independen:
        </p>
        <pre><code>// gambar.php?id=12
$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT file_gambar FROM galeri WHERE id = :id");
$stmt->execute([':id' => $id]);
$item = $stmt->fetch(PDO::FETCH_ASSOC);

// Ekstrak MIME dan Stream Data Biner ke Browser
header("Content-Type: " . $mime);
header("Cache-Control: public, max-age=86400"); // Cache browser 24 jam
echo base64_decode($base64_data);
exit;</code></pre>

        <h4 style="color: var(--text-title); font-size: 0.95rem; margin-top: 1.25rem; margin-bottom: 0.4rem;">D. Operasi Read, Search, &amp; Delete (<code>catalog.php</code>)</h4>
        <ul style="margin-left: 1.25rem; font-size: 0.875rem;">
            <li><strong>Pencarian:</strong> Menggunakan klausa SQL <code>ILIKE :q</code> (PostgreSQL case-insensitive search) yang mencari kecocokan pada judul, pengunggah, atau kategori.</li>
            <li><strong>Penghapusan:</strong> Menerima parameter GET <code>?hapus=ID</code>, divalidasi dengan <code>FILTER_VALIDATE_INT</code>, lalu mengeksekusi <code>DELETE FROM galeri WHERE id = :id</code>.</li>
            <li><strong>Flash Messages:</strong> Notifikasi keberhasilan disimpan di <code>$_SESSION['flash']</code>, dirender di halaman berikutnya, lalu langsung dibersihkan (<code>unset($_SESSION['flash'])</code>).</li>
        </ul>
    </div>

    <!-- Bagian 3: Interaktivitas JavaScript -->
    <div class="doc-section">
        <h3>
            <span class="doc-pill js">JavaScript</span>
            3. Interaktivitas Sisi Klien (DOM &amp; UX)
        </h3>
        <p style="margin-bottom: 0.85rem;">
            JavaScript digunakan secara murni (Vanilla JS) untuk meningkatkan pengalaman pengguna (UX) tanpa ketergantungan library luar:
        </p>

        <div class="qa-card">
            <div class="qa-q">📱 Navigasi Responsif Mobile (Hamburger Menu)</div>
            <p class="qa-a">
                Fungsi <code>initNavToggle()</code> mendengarkan klik pada tombol <code>#nav-toggle-btn</code> di perangkat mobile dan melakukan <code>nav.classList.toggle("nav-open")</code> untuk membuka/menutup menu tanpa reload halaman.
            </p>
        </div>

        <div class="qa-card">
            <div class="qa-q">🛡️ Konfirmasi Hapus Data Interaktif</div>
            <p class="qa-a">
                Tautan hapus pada kartu galeri dilengkapi atribut inline <code>onclick="return confirm('Apakah Anda yakin ingin menghapus gambar ini?');"</code>. Jika pengguna memilih <em>Cancel</em>, browser membatalkan aksi navigasi sehingga data tidak terhapus secara tidak sengaja.
            </p>
        </div>

        <div class="qa-card">
            <div class="qa-q">⚡ Lazy Loading Gambar Bawaan Browser</div>
            <p class="qa-a">
                Tag <code>&lt;img ... loading="lazy"&gt;</code> diimplementasikan agar peramban hanya mengunduh berkas gambar saat kartu foto mulai masuk ke dalam area pandang (viewport) layar pengguna, menghemat pemakaian bandwidth.
            </p>
        </div>
    </div>

    <!-- Bagian 4: Cheat Sheet Dosen -->
    <div class="doc-section">
        <h3>
            <span class="doc-pill sql">Cheat Sheet</span>
            4. Panduan Tanya-Jawab Evaluasi Dosen (Siap Sidang/Presentasi)
        </h3>
        <p style="margin-bottom: 1rem;">
            Daftar pertanyaan teknis yang paling sering diajukan dosen dan cara menjawabnya secara ringkas, tepat, dan akademis:
        </p>

        <div class="qa-card">
            <div class="qa-q">Q1: Mengapa menggunakan PHP Data Objects (PDO) dan bukan MySQLi?</div>
            <p class="qa-a">
                <strong>Jawaban:</strong> PDO bersifat <em>database-agnostic</em> (mendukung multi-driver seperti PostgreSQL, MySQL, dan SQLite dengan satu sintaks yang konsisten). Selain itu, PDO menyediakan Prepared Statements native dengan penanganan error berbasis Exception yang sangat andal untuk keamanan aplikasi.
            </p>
        </div>

        <div class="qa-card">
            <div class="qa-q">Q2: Bagaimana sistem ini mencegah serangan SQL Injection?</div>
            <p class="qa-a">
                <strong>Jawaban:</strong> Setiap query yang melibatkan input dari pengguna tidak pernah digabung dengan konkatenasi string biasa. Kami menggunakan <code>$pdo-&gt;prepare()</code> dengan <em>named placeholders</em> (seperti <code>:judul</code>) dan dieksekusi melalui <code>execute([...])</code>. Mesin database memperlakukan nilai tersebut murni sebagai data, bukan perintah SQL yang dapat dieksekusi.
            </p>
        </div>

        <div class="qa-card">
            <div class="qa-q">Q3: Bagaimana cara kerja penanganan upload file di serverless Vercel?</div>
            <p class="qa-a">
                <strong>Jawaban:</strong> Lingkungan serverless seperti Vercel memiliki sistem berkas (filesystem) yang bersifat <em>read-only</em> dan <em>ephemeral</em> (sementara). Jika file disimpan di folder fisik biasa, file tersebut akan hilang saat container serverless mati. Karena itu, file gambar dibaca binary-nya dan dikonversi ke format <strong>Base64 Data URL</strong> yang disimpan secara permanen di basis data PostgreSQL Supabase cloud.
            </p>
        </div>

        <div class="qa-card">
            <div class="qa-q">Q4: Mengapa dibuat file <code>gambar.php</code> terpisah untuk menampilkan foto?</div>
            <p class="qa-a">
                <strong>Jawaban:</strong> Jika string Base64 ratusan kilobyte disematkan langsung di dalam tag HTML <code>&lt;img src="data:image/..."&gt;</code>, ukuran dokumen HTML yang dikirimkan server akan membengkak hingga puluhan megabyte sehingga memicu batas limit Vercel (<em>500 FUNCTION_RESPONSE_PAYLOAD_TOO_LARGE</em>). Dengan <code>gambar.php?id=...</code>, HTML hanya berukuran &lt; 10 KB, dan gambar di-stream sebagai biner murni dengan header <code>Content-Type</code> serta dapat di-cache oleh browser.
            </p>
        </div>

        <div class="qa-card">
            <div class="qa-q">Q5: Apa fungsi <code>htmlspecialchars()</code> pada output tampilan?</div>
            <p class="qa-a">
                <strong>Jawaban:</strong> Fungsi <code>htmlspecialchars()</code> digunakan untuk mencegah serangan <strong>Cross-Site Scripting (XSS)</strong> dengan mengubah karakter sensitif HTML (seperti <code>&lt;</code>, <code>&gt;</code>, <code>&amp;</code>, dan <code>"</code>) menjadi entitas karakter HTML yang aman sebelum dicetak ke layar browser.
            </p>
        </div>
    </div>

    <!-- Tombol Aksi Bawah -->
    <div class="cta-banner" style="margin-top: 2rem;">
        <h2>Ingin Mencoba Aplikasi Sekarang?</h2>
        <p>Silakan uji form pengunggahan gambar atau lihat koleksi foto yang sudah tersimpan di database.</p>
        <div class="cta-buttons">
            <a href="<?php echo $base; ?>collection/upload-asset.php" class="btn btn-primary">+ Upload Gambar Baru</a>
            <a href="<?php echo $base; ?>collection/catalog.php" class="btn btn-secondary">Jelajahi Galeri Foto &rarr;</a>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
