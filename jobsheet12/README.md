# Jobsheet 12 - Integrasi Front-End & Back-End Sistem Terpadu

Modul ini merupakan puncak integrasi arsitektur **PixelGallery**, menghubungkan seluruh komponen antarmuka (*front-end*) dan logika server (*back-end*) yang telah dibangun dari Jobsheet 8 hingga 11 menjadi satu sistem terpadu yang siap beroperasi secara *end-to-end*.

## Fitur Utama Modul
1. **Modul Transaksi Lisensi Aset (`peminjaman/`)**:
   - `tambah.php`: Formulir peminjaman hak pakai aset foto digital dengan penyaringan stok kuota otomatis.
   - `proses_tambah.php`: Eksekusi transaksi basis data atomik (ACID) yang mengikat pencatatan peminjaman dan pengurangan stok galeri secara konsisten.
   - `kembali.php`: Pemrosesan pengembalian lisensi dan pemulihan kuota stok foto secara atomik.
   - `riwayat.php`: Tampilan histori transaksi relasional hasil **JOIN 3 tabel** (`peminjaman`, `galeri`, `users`).
2. **Statistik Real-time Beranda (`index.php`)**:
   - Metrik hidup dari PostgreSQL: Total Foto, Total Anggota, Lisensi Aktif Dipinjam, dan Total Transaksi.
3. **Validasi Aturan Bisnis (Tugas Mandiri)**:
   - Pencegahan peminjaman baru bagi anggota yang memiliki transaksi terlambat (> 14 hari) dan belum dikembalikan.
4. **Keamanan Berlapis**:
   - Mewarisi seluruh hardening Jobsheet 11: CSRF protection, 100% Prepared Statements, XSS sanitization, and Session Fixation protection.
