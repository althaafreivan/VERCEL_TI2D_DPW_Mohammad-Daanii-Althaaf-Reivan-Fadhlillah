# Security Audit Checklist & Hardening Report
**Mata Kuliah:** Desain dan Pemrograman Web (DPW)  
**Jobsheet:** 11 - Keamanan Aplikasi Web Dasar (Web Application Hardening)  
**Proyek:** PixelGallery (Platform Digital Asset & Showcase Galeri Foto)  
**Backend:** PHP 8.5.2 & PostgreSQL (Supabase)  
**Pengembang:** Mohammad Daanii Althaaf Reivan Fadhlillah (TI-2D)

---

## 1. Ringkasan Eksekutif
Audit keamanan aplikasi web ini dilakukan secara menyeluruh terhadap seluruh komponen form dan query pada modul **PixelGallery** (`auth/`, `collection/`, `includes/`). Fokus audit mencakup empat pilar keamanan OWASP Top 10 dasar:
1. **SQL Injection (SQLi)**
2. **Cross-Site Scripting (XSS)**
3. **Cross-Site Request Forgery (CSRF)**
4. **Session Fixation & Broken Authentication**
5. **Input Validation & Sanitization Whitelisting**

---

## 2. Checklist Audit Keamanan & Bukti Before / After

| No | Modul / Halaman | Kategori Kerentanan | Status Sebelum Hardening (Before) | Status Sesudah Hardening (After) | Status |
|---|---|---|---|---|---|
| 1 | `auth/proses_login.php` | SQL Injection | Berpotensi SQLi jika menggunakan string concatenation | **100% PDO Prepared Statement** dengan named parameters `:username`. Input `' OR '1'='1` dianggap sebagai string literal, bukan ekspresi SQL. | SECURE |
| 2 | `auth/proses_login.php` | Session Fixation | Session ID tetap sama sebelum dan sesudah proses login | Memanggil **`session_regenerate_id(true)`** saat password valid, memusnahkan ID sesi lama dan menerbitkan sesi baru. | SECURE |
| 3 | `auth/login.php` & `register.php` | CSRF | Form POST tidak memiliki verifikasi keaslian pengirim | Ditambahkan **`csrf_field()`** token acak kriptografis 32-byte, divalidasi dengan **`hash_equals()`**. | SECURE |
| 4 | `collection/catalog.php` | XSS (Cross-Site Scripting) | Teks dinamis seperti judul, pengunggah, deskripsi berpotensi mengeksekusi tag skrip | Semua output dibungkus fungsi helper **`htmlspecialchars($str, ENT_QUOTES, 'UTF-8')`** / `e()`. | SECURE |
| 5 | `collection/upload-asset.php` | Input Tampering (Category) | Kategori diterima apa adanya tanpa pemeriksaan nilai terdaftar | Diterapkan **Whitelist Validation**: hanya mengizinkan kategori `['Wallpaper', 'Pemandangan', 'Fotografi', 'Ilustrasi', 'Arsitektur']`. | SECURE |
| 6 | `collection/upload-asset.php` | Unrestricted File Upload | Ekstensi file dan MIME type bisa dipalsukan | Validasi ganda: whitelist ekstensi `['jpg', 'jpeg', 'png', 'webp', 'gif']`, validasi ukuran `<= 5MB`, dan encoding Base64 terisolasi. | SECURE |
| 7 | `collection/hapus.php` | CSRF (State-changing action) | Hapus data bisa dipicu pihak ketiga tanpa validasi token | Divalidasi dengan **`csrf_verify()`** dan wajib menggunakan HTTP method **POST**. Akses GET otomatis diblokir. | SECURE |
| 8 | `collection/edit.php` | Broken Access Control & CSRF | Form edit tanpa token CSRF dan hak milik data bisa dimanipulasi | Dilengkapi token CSRF dan **Role-Based Access Control** (Hanya admin atau pemilik foto yang diizinkan memodifikasi). | SECURE |

---

## 3. Detail Implementasi Teknis

### A. Pencegahan SQL Injection (100% Prepared Statements)
Seluruh query SELECT, INSERT, UPDATE, dan DELETE di seluruh proyek menggunakan `PDO::prepare()` dan parameter binding. Tidak ada satupun query yang menggabungkan variabel input langsung ke sintaks SQL.

```php
// Contoh pada proses_login.php
$stmt = $pdo->prepare("SELECT id, username, password, nama, role FROM users WHERE username = :username LIMIT 1");
$stmt->execute([':username' => $username]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
```

**Uji Penetrasi:**
- Input Username: `' OR '1'='1`
- Input Password: `password123`
- **Hasil:** Login ditolak dengan pesan *"Username atau password salah!"*. Input string `' OR '1'='1` dicari sebagai teks literal, tidak mengeksekusi bypass SQL.

### B. Pencegahan Cross-Site Scripting (XSS Sanitization)
Dibuat fungsi helper global `e($value)` di `includes/helpers.php`:
```php
function e($value) {
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
```
Setiap judul, nama pengunggah, dan deskripsi diparsing melalui fungsi ini sebelum dirender ke DOM HTML.

**Uji Penetrasi:**
- Input Judul: `<script>alert('XSS-PixelGallery')</script>`
- **Hasil:** Karakter `<` dan `>` dikonversi menjadi entitas HTML `&lt;` dan `&gt;`. Skrip tidak dieksekusi oleh browser dan hanya tampil sebagai teks biasa di kartu galeri.

### C. Pencegahan Cross-Site Request Forgery (CSRF Protection)
Dibuat modul proteksi CSRF di `includes/csrf.php` dengan mekanisme token berbasis sesi:
1. `csrf_token()`: Menghasilkan token kriptografis acak `bin2hex(random_bytes(32))`.
2. `csrf_field()`: Merender input tersembunyi `<input type="hidden" name="csrf_token" value="...">`.
3. `csrf_verify()`: Memvalidasi kecocokan token menggunakan `hash_equals()` untuk mencegah timing-attack vulnerability.

Semua form POST (`login.php`, `register.php`, `upload-asset.php`, `edit.php`, `hapus.php`) mewajibkan dan memverifikasi token ini sebelum mengeksekusi logika bisnis database.

### D. Pencegahan Session Fixation (Tugas Mandiri)
Pada `auth/proses_login.php`, setelah verifikasi `password_verify()` berhasil, dijalankan perintah:
```php
// Mencegah Session Fixation attack
session_regenerate_id(true);
```
Parameter `true` memastikan file/ID sesi lama dihapus secara permanen dari server dan diganti dengan ID sesi baru yang segar.

---

## 4. Kesimpulan Hasil Audit
Seluruh kriteria penilaian Jobsheet 11 telah terpenuhi 100%:
- [x] Prepared Statements PDO_PGSQL aktif di semua endpoint.
- [x] Output XSS disanitasi menggunakan `htmlspecialchars()`.
- [x] Token CSRF aktif pada seluruh form mutasi data.
- [x] Whitelist validation diterapkan pada input kategori dan role.
- [x] `session_regenerate_id(true)` aktif untuk mitigasi Session Fixation.
- [x] Uji penetrasi SQLi dan XSS terbukti gagal dieksploitasi.
