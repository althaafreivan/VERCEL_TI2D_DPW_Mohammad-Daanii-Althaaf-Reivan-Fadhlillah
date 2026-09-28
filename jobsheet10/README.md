# Jobsheet 10 — Autentikasi & Manajemen Sesi Pengguna (PixelGallery Auth)

Proyek ini adalah implementasi **Jobsheet 10: Menerapkan Autentikasi Pengguna & Manajemen Sesi Terproteksi** pada platform **PixelGallery**.

## Fitur Utama Jobsheet 10
1. **Registrasi Akun Baru (`auth/register.php`)**:
   - Pembuatan akun baru dengan enkripsi kata sandi kuat menggunakan algoritma bawaan PHP `password_hash($password, PASSWORD_DEFAULT)`.
   - Validasi keunikan username dan format input data.
2. **Autentikasi Login (`auth/login.php`)**:
   - Verifikasi kata sandi dengan `password_verify()`.
   - Pengelolaan sesi server: `$_SESSION['user_id']`, `$_SESSION['username']`, `$_SESSION['nama']`, `$_SESSION['role']`.
3. **Logout Sesi Bersih (`auth/logout.php`)**:
   - Penghancuran sesi server (`session_destroy()`) dan pembersihan cookie sesi pengguna.
4. **Guard Clause Auth (`includes/auth.php`)**:
   - Middleware perlindungan rute: mencegah akses tanpa login pada aksi Create, Edit, dan Delete.
5. **Otorisasi Berbasis Peran / Role-Based Access Control (RBAC)**:
   - **Admin**: Berhak mengedit dan menghapus seluruh foto dalam galeri.
   - **User**: Hanya berhak mengedit dan menghapus foto karyanya sendiri.
   - **Guest**: Dapat melihat galeri foto dan melakukan pencarian, namun dibatasi dari tindakan manipulasi data.
6. **Navbar Adaptif**:
   - Tampilan navigasi yang dinamis menyesuaikan status login pengguna dan role aktif.

## Akun Demo Pengujian
- **Admin**: username `admin` | kata sandi `admin123`
- **User**: username `user` | kata sandi `user123`
- Atau buat akun baru langsung melalui menu **Daftar Akun Baru**.

## Struktur Direktori
```text
jobsheet10/
├── assets/
│   ├── css/style.css       # Neumorphic layout + auth UI
│   └── js/app.js           # Client interactions
├── auth/
│   ├── login.php           # Form login akun
│   ├── logout.php          # Handler logout sesi
│   ├── proses_login.php    # Handler verifikasi password_verify
│   ├── proses_register.php # Handler hashing password_hash
│   └── register.php        # Form registrasi pengguna
├── collection/
│   ├── catalog.php         # Galeri foto dengan filter hak akses role
│   ├── edit.php            # Form edit terproteksi auth & role
│   ├── hapus.php           # Handler hapus terproteksi auth & role
│   ├── proses_edit.php     # Handler update terproteksi
│   └── upload-asset.php    # Form tambah terproteksi
├── includes/
│   ├── auth.php            # Guard clause middleware
│   ├── footer.php          # Modular footer
│   ├── header.php          # Modular dynamic navbar
│   └── koneksi.php         # PDO PostgreSQL connection
├── sql/
│   ├── 01_galeri.sql       # Skema DDL tabel galeri
│   └── 02_users.sql        # Skema DDL tabel users & seed akun
├── gambar.php              # Image serving streaming endpoint
├── index.php               # Beranda Jobsheet 10
├── laporan.md              # Laporan praktikum & tugas mandiri
└── README.md               # Dokumentasi modul
```
