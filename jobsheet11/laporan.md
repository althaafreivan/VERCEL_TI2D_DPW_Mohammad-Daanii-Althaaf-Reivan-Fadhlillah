# Jobsheet 11 — Keamanan Aplikasi Web Dasar (Web Application Hardening)
Nama: Mohammad Daanii Althaaf Reivan Fadhlillah <br>
Kelas: TI 2D <br>
NIM: 254107020123 <br>

## Tugas Praktikum & Mandiri
1. **Audit & Mitigasi SQL Injection (100% Prepared Statements)**:
   - Melakukan audit menyeluruh terhadap seluruh query database di modul `auth/` dan `collection/`.
   - Memastikan tidak ada penggabungan string langsung (concatenation) pada perintah SQL. Seluruh query menggunakan antarmuka PDO PostgreSQL (`$pdo->prepare()` dan binding parameter dengan placeholder bertipe `:named`).
   - Melakukan pengujian penetrasi dengan menyuntikkan payload SQLi `' OR '1'='1` pada kolom input login, pencarian katalog, dan form edit. Sistem secara konsisten memperlakukan masukan sebagai nilai literal teks sehingga serangan SQLi gagal mengeksploitasi basis data.

2. **Mitigasi Cross-Site Scripting / XSS (`includes/helpers.php` & sanitasi output)**:
   - Membuat fungsi pembantu global `e($value)` yang membungkus fungsi `htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')`.
   - Mengaudit dan menyaring setiap data dinamis pengguna yang dicetak ke antarmuka HTML pada `catalog.php`, `index.php`, dan form edit.
   - Pengujian input payload `<script>alert('XSS')</script>` pada judul gambar berhasil dinetralkan menjadi entitas teks HTML aman (`&lt;script&gt;...`), mencegah browser mengeksekusi skrip jahat.

3. **Penerapan Proteksi Cross-Site Request Forgery / CSRF (`includes/csrf.php`)**:
   - Membangun mekanisme token anti-CSRF berbasis sesi server yang aman secara kriptografis:
     - `csrf_token()`: Mengenerate token acak 32-byte menggunakan `bin2hex(random_bytes(32))`.
     - `csrf_field()`: Menghasilkan elemen HTML input tersembunyi `name="csrf_token"`.
     - `csrf_verify()`: Memvalidasi kecocokan token antara request POST dan session menggunakan `hash_equals()` untuk mencegah timing attacks.
   - Mengintegrasikan field CSRF pada seluruh form pengubah data: login, registrasi, unggah gambar, edit gambar, dan form penghapusan gambar.

4. **Validasi & Sanitasi Input Sisi Server**:
   - Menggunakan `filter_var()` dengan `FILTER_VALIDATE_INT` untuk memvalidasi parameter numerik (`id`, `page`).
   - Menerapkan whitelist validation untuk nilai kolom tertentu, seperti kategori gambar: hanya mengizinkan `['Wallpaper', 'Pemandangan', 'Fotografi', 'Ilustrasi', 'Arsitektur']`.
   - Melakukan pembatasan tipe file upload secara ketat (hanya ekstensi `jpg`, `jpeg`, `png`, `webp`, `gif` dengan batas ukuran maksimal 5MB).

5. **Penyusunan Dokumen Checklist Keamanan (`docs/security-checklist.md`)**:
   - Mendokumentasikan tabel temuan audit per halaman/modul dengan status *Before* dan *After* perbaikan.
   - Menyajikan bukti uji penetrasi kegagalan serangan SQL Injection dan XSS.

6. **Mitigasi Serangan Session Fixation (Tugas Mandiri)**:
   - Mengimplementasikan `session_regenerate_id(true)` pada `auth/proses_login.php` tepat setelah verifikasi kata sandi (`password_verify`) sukses.
   - Menjamin bahwa ID sesi pengunjung sebelum login dihancurkan secara permanen dari server dan digantikan dengan ID sesi baru yang baru diotentikasi, menutup celah pembajakan sesi (Session Hijacking).

## Latihan Reflektif

1. **Mengapa penggunaan Prepared Statement dengan parameter binding pada PDO mampu mencegah serangan SQL Injection secara tuntas?**  
   Prepared statement memisahkan fase kompilasi query SQL dari fase transmisi data input. Mesin database terlebih dahulu mem-parsing, mengompilasi, dan mengoptimalkan struktur query SQL dengan slot parameter kosong. Ketika nilai masukan pengguna dikirimkan via parameter binding, database memperlakukannya murni sebagai data literal (string, integer, dll.), bukan sebagai instruksi bahasa SQL yang dapat mengubah logika query, terlepas dari karakter kutip atau operator logika apapun di dalam teks tersebut.

2. **Apa perbedaan antara fungsi `htmlspecialchars()` dan `strip_tags()`, serta kapan waktu yang tepat menggunakannya?**  
   `htmlspecialchars()` mengonversi karakter khusus HTML (seperti `<`, `>`, `&`, `"`, `'`) menjadi entitas HTML aman (`&lt;`, `&gt;`, dll.) sehingga teks tetap utuh dan terbaca oleh pengguna tanpa dieksekusi sebagai tag browser. Sementara itu, `strip_tags()` menghapus secara permanen seluruh tag HTML dari teks. Untuk mencegah XSS saat menampilkan input pengguna di layar (seperti nama, judul, komentar), `htmlspecialchars()` adalah standar utama karena menjaga integritas isi teks tanpa merusak teks yang mungkin memang memiliki karakter simbol matematika atau tanda panah.

3. **Bagaimana mekanisme CSRF Token melindungi form penghapusan atau mutasi data dari eksploitasi oleh situs pihak ketiga?**  
   Serangan CSRF memanfaatkan fakta bahwa browser secara otomatis menyertakan cookie sesi target ketika pengguna mengunjungi tautan atau skrip yang dipicu situs penyerang. Dengan menambahkan token CSRF rahasia yang disimpan di session server dan diwajibkan ada di badan form POST, situs pihak ketiga yang jahat tidak memiliki cara untuk membaca token tersebut (dibatasi oleh *Same-Origin Policy* browser). Ketika formulir palsu dikirimkan tanpa token valid, server menolak permintaan tersebut seketika.

---
### Self Question
1. Mengapa fungsi `hash_equals()` lebih aman digunakan untuk membandingkan token CSRF atau hash dibandingkan operator kesetaraan biasa (`===`)?
2. Bagaimana strategi Content Security Policy (CSP) pada HTTP header dapat bekerja sama dengan sanitasi input untuk memperkuat pertahanan berlapis (Defense in Depth) terhadap XSS?

### Notes
1. Pengecekan `csrf_verify()` harus selalu dilakukan sebelum mengeksekusi logika apapun pada file pemroses form POST.
2. Regenerasi ID sesi dengan `session_regenerate_id(true)` adalah langkah wajib pada setiap peralihan tingkat privilese (seperti saat tamu berubah menjadi pengguna terotentikasi).
3. Pendekatan *Whitelisting* (hanya menerima data yang dikenal sah) jauh lebih aman daripada pendekatan *Blacklisting* (mencoba menyaring karakter buruk).
