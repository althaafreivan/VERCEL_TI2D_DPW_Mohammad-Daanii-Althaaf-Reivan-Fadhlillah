# Jobsheet 9 — Full Stack CRUD & Pagination (PixelGallery)

Proyek ini adalah implementasi **Jobsheet 09: Membangun Fitur CRUD Penuh & Pagination Server-side** pada platform **PixelGallery**.

## Fitur Utama Jobsheet 9
1. **Create (Unggah Gambar Baru)**: Mengunggah berkas gambar dengan validasi tipe mime, ekstensi whitelist, dan batas ukuran 5MB, tersimpan persisten di PostgreSQL Supabase cloud.
2. **Read (Jelajah Galeri & Detail)**: Menampilkan koleksi foto dalam tata letak kartu grid Neumorphism responsif.
3. **Update (Edit Gambar & Metadata)**: Formulir pra-isi untuk mengubah judul, pengunggah, kategori, tahun, deskripsi, serta opsi penggantian berkas foto.
4. **Delete (Hapus Data via POST)**: Aksi penghapusan aman menggunakan metode HTTP POST dan dialog konfirmasi JavaScript.
5. **Pagination Server-side**: Pembagian halaman otomatis berbasis query `LIMIT` dan `OFFSET` dengan tautan halaman dinamis.
6. **Pencarian Server-side**: Pencarian kata kunci fleksibel (*case-insensitive*) dengan PostgreSQL `ILIKE`.

## Struktur Direktori
```text
jobsheet9/
├── assets/
│   ├── css/style.css       # Neumorphic layout + pagination styles
│   └── js/app.js           # Client interactions
├── collection/
│   ├── catalog.php         # Galeri foto dengan pagination & pencarian
│   ├── edit.php            # Form edit data gambar
│   ├── hapus.php           # Handler hapus data berbasis POST
│   ├── proses_edit.php     # Handler update data PostgreSQL
│   └── upload-asset.php    # Form tambah gambar
├── includes/
│   ├── footer.php          # Modular footer
│   ├── header.php          # Modular header
│   └── koneksi.php         # PDO PostgreSQL connection
├── sql/
│   └── 01_galeri.sql       # Skema DDL tabel galeri
├── gambar.php              # Image serving streaming endpoint
├── index.php               # Beranda Jobsheet 9
├── laporan.md              # Laporan praktikum & tugas mandiri
└── README.md               # Dokumentasi modul
```
