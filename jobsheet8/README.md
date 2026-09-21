# Panduan Koneksi Supabase & Dokumentasi Proyek — Jobsheet 8

Aplikasi web **PixelGallery** (Galeri & Upload Foto/Gambar) menggunakan PHP PDO (*PHP Data Objects*) yang dapat terhubung langsung ke basis data cloud **Supabase (PostgreSQL)** maupun basis data lokal.

---

## 1. Kueri SQL yang Harus Dijalankan di Supabase

Buka dashboard Supabase Anda &rarr; pilih menu **SQL Editor** di bilah navigasi kiri &rarr; klik tombol **+ New query** &rarr; salin dan jalankan (*Run*) kueri SQL berikut:

```sql
-- =========================================================================
-- Skema Database PixelGallery (Jobsheet 8)
-- =========================================================================

-- 1. Tabel galeri: Menyimpan data gambar, pengunggah, dan nama file fisik
CREATE TABLE IF NOT EXISTS galeri (
    id SERIAL PRIMARY KEY,
    judul VARCHAR(255) NOT NULL,
    pengunggah VARCHAR(255) NOT NULL,
    kategori VARCHAR(100) DEFAULT 'Umum',
    file_gambar VARCHAR(255) NOT NULL,   -- Menyimpan nama file fisik di folder assets/uploads/
    tahun INTEGER NOT NULL,
    deskripsi VARCHAR(255)               -- Keterangan atau lokasi pengambilan foto
);

-- 2. Tabel anggota: Menyimpan data pengguna/anggota (kelengkapan jobsheet)
CREATE TABLE IF NOT EXISTS anggota (
    id SERIAL PRIMARY KEY,
    nama VARCHAR(255) NOT NULL,
    no_anggota VARCHAR(50) NOT NULL UNIQUE,
    alamat VARCHAR(255),
    no_hp VARCHAR(30)
);

-- 3. Cek apakah tabel sudah aktif
SELECT * FROM galeri;
```

> **Penjelasan Struktur Tabel untuk Presentasi Kuliah:**
> - `id`: Kunci primer (*primary key*) berurutan otomatis (`SERIAL`).
> - `judul`: Judul foto atau gambar yang diunggah.
> - `pengunggah`: Nama mahasiswa / fotografer yang mengunggah foto.
> - `kategori`: Kategori foto (*Wallpaper*, *Pemandangan*, *Fotografi*, *Ilustrasi*, dll).
> - `file_gambar`: Nama berkas gambar fisik yang tersimpan di folder `assets/uploads/` (misal: `1726912345_pantai.jpg`).
> - `tahun`: Tahun foto diambil atau dibuat.
> - `deskripsi`: Keterangan singkat mengenai foto (opsional).

---

## 2. Cara Mengambil Kredensial Koneksi di Supabase

1. Buka [https://supabase.com/dashboard](https://supabase.com/dashboard) dan pilih proyek Anda.
2. Klik ikon gerigi **Project Settings** di bagian kiri bawah.
3. Pilih menu **Database**.
4. Gulir ke bawah hingga menemukan bagian **Connection Parameters** atau **Connection String**:
   - Pilih tab **Connection Pooling** (sangat disarankan karena mendukung koneksi IPv4 tanpa kendala di jaringan internet lokal):
     - **Host**: Contoh: `aws-0-ap-southeast-1.pooler.supabase.com`
     - **Port**: `6543` (atau `5432` jika menggunakan session pooler)
     - **Database**: `postgres`
     - **User**: Contoh: `postgres.projectrefanda`
     - **Password**: Password database yang Anda buat saat pertama kali membuat proyek Supabase.

---

## 3. Cara Menghubungkan ke Kode PHP (`includes/koneksi.php`)

Buka berkas [includes/koneksi.php](file:///C:/Users/Foliv/OneDrive/Documents/GitHub/TI2D_DPW_Mohammad-Daanii-Althaaf-Reivan-Fadhlillah/jobsheet8/includes/koneksi.php). Masukkan kredensial Supabase Anda pada variabel di bagian atas:

```php
// Ganti dengan kredensial dari dashboard Supabase Anda:
$supabase_host = 'aws-0-ap-southeast-1.pooler.supabase.com'; // Host Supabase Anda
$supabase_port = '6543';                                     // Port pooler
$supabase_db   = 'postgres';                                 // Nama database default
$supabase_user = 'postgres.xxxxxxxxxxxxxxxxxxxx';            // Username Anda
$supabase_pass = 'PasswordSupabaseAnda123';                  // Password database Anda
```

> **Fitur Otomatis Fallback:**
> - Jika data Supabase belum diisi atau sedang tidak ada koneksi internet, sistem secara **otomatis beralih ke PostgreSQL lokal** (`localhost:5433` / `5432`), sehingga aplikasi Anda tetap dapat berjalan saat *offline*.

---

## 4. Cara Menjalankan & Menguji Aplikasi

1. Buka terminal PowerShell di folder proyek `jobsheet8`:
   ```powershell
   cd C:\Users\Foliv\OneDrive\Documents\GitHub\TI2D_DPW_Mohammad-Daanii-Althaaf-Reivan-Fadhlillah\jobsheet8
   ```

2. Jalankan server lokal bawaan PHP:
   ```powershell
   php -S localhost:8000
   ```

3. Buka peramban (*browser*) ke alamat:
   - **Beranda**: `http://localhost:8000/index.php`
   - **Galeri Gambar**: `http://localhost:8000/collection/catalog.php`
   - **Upload Gambar**: `http://localhost:8000/collection/upload-asset.php`

4. Coba lakukan upload foto:
   - Pilih file gambar (JPG/PNG/WEBP).
   - Isi judul dan nama pengunggah, lalu klik **Unggah Gambar Sekarang**.
   - Foto akan langsung tampil di galeri dan datanya dapat Anda verifikasi di dashboard Supabase pada menu **Table Editor** &rarr; tabel `galeri`!

---

## 5. Ringkasan Alur Kerja untuk Dijelaskan ke Dosen

1. **Form Upload (`collection/upload-asset.php`)**:
   Form HTML mengirim berkas via `enctype="multipart/form-data"`. Di sisi server, PHP mengambil file dari `$_FILES['gambar']`, memeriksa ekstensinya dengan `pathinfo()`, lalu memindahkannya ke direktori `assets/uploads/` menggunakan `move_uploaded_file()`.
2. **Penyimpanan Database**:
   Nama berkas yang tersimpan dicatat ke dalam database tabel `galeri` kolom `file_gambar` menggunakan **PDO Prepared Statement** (`$pdo->prepare(...)` & `$stmt->execute(...)`) agar aman dari serangan *SQL Injection*.
3. **Penyajian Data (`collection/catalog.php` & `index.php`)**:
   Data diambil dari database dengan kueri `SELECT * FROM galeri ORDER BY id DESC`. Di peramban, gambar ditampilkan menggunakan elemen `<img src="assets/uploads/nama_file">`.
