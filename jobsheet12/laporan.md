# Jobsheet 12 — Integrasi Front-End dan Back-End Proyek Secara Utuh
Nama: Mohammad Daanii Althaaf Reivan Fadhlillah <br>
Kelas: TI 2D <br>
NIM: 254107020123 <br>

## Tugas Praktikum & Mandiri
1. **Perancangan Skema Relasional Tabel Transaksi (`sql/03_peminjaman.sql`)**:
   - Menambahkan kolom kuota lisensi `stok` (INTEGER DEFAULT 5) pada tabel entitas `galeri`.
   - Membuat tabel relasional `peminjaman` yang menghubungkan entitas karya (`galeri_id` FK references `galeri(id)`) dan entitas anggota (`user_id` FK references `users(id)`).
   - Menyimpan atribut transaksi: `tanggal_pinjam` (DATE DEFAULT CURRENT_DATE), `tanggal_kembali` (DATE NULLABLE), `status` (VARCHAR DEFAULT 'dipinjam'), dan `catatan` (VARCHAR).
   - Menambahkan index basis data (`idx_peminjaman_user`, `idx_peminjaman_galeri`, `idx_peminjaman_status`) untuk optimasi performa query JOIN.

2. **Form Peminjaman & Transaksi ACID Atomik (`peminjaman/tambah.php` & `proses_tambah.php`)**:
   - Membatasi pemilihan karya foto hanya pada aset yang memiliki stok kuota lisensi tersedia (`stok > 0`).
   - Menerapkan **Transaksi Database Atomik (ACID)** menggunakan PDO:
     ```php
     $pdo->beginTransaction();
     // 1. SELECT ... FOR UPDATE mengunci baris stok galeri
     // 2. INSERT INTO peminjaman
     // 3. UPDATE galeri SET stok = stok - 1
     $pdo->commit();
     ```
   - Mekanisme ini menjamin tidak terjadi *race condition* atau inkonsistensi data ketika kuota berkurang.

3. **Modul Pengembalian Aset Visual (`peminjaman/kembali.php`)**:
   - Memproses pengembalian lisensi karya dengan mengubah `status` menjadi `'dikembalikan'` dan mencatat waktu sekarang (`tanggal_kembali = CURRENT_DATE`).
   - Mengembalikan kuota lisensi foto secara otomatis ke galeri (`UPDATE galeri SET stok = stok + 1`) di dalam blok transaksi terisolasi.
   - Hak akses pengembalian dilindungi: hanya peminjam aset bersangkutan atau akun administrator yang dapat memproses pengembalian.

4. **Histori Transaksi dengan Multi-Table SQL JOIN (`peminjaman/riwayat.php`)**:
   - Menggabungkan 3 tabel relasional sekaligus dalam satu query SQL yang efisien:
     ```sql
     SELECT p.id, p.tanggal_pinjam, p.tanggal_kembali, p.status, p.catatan,
            g.judul AS judul_foto, g.kategori,
            u.nama AS nama_peminjam, u.username,
            (CURRENT_DATE - p.tanggal_pinjam) AS lama_hari
     FROM peminjaman p
     JOIN galeri g ON p.galeri_id = g.id
     JOIN users u ON p.user_id = u.id
     ORDER BY p.id DESC;
     ```
   - Menampilkan kalkulasi durasi peminjaman dan label status interaktif (*Dipinjam*, *Terlambat*, *Dikembalikan*).

5. **Statistik Real-time Beranda Terintegrasi (`index.php`)**:
   - Menghubungkan seluruh kartu metrik di halaman utama langsung ke basis data PostgreSQL aktif:
     - Total Foto (`COUNT(*) FROM galeri`)
     - Total Anggota Terdaftar (`COUNT(*) FROM users`)
     - Peminjaman Aktif (`COUNT(*) FROM peminjaman WHERE status = 'dipinjam'`)
     - Total Transaksi Lisensi (`COUNT(*) FROM peminjaman`)

6. **Penerapan Validasi Aturan Bisnis / Business Rules (Tugas Mandiri)**:
   - Diterapkan pemeriksaan ketat sebelum formulir peminjaman diproses:
     - Jika anggota memiliki peminjaman dengan status `'dipinjam'` dan selisih tanggal pinjam lebih dari 14 hari (`tanggal_pinjam < CURRENT_DATE - INTERVAL '14 days'`), sistem **menolak peminjaman baru**.
     - Antarmuka menampilkan banner peringatan penangguhan hak pinjam dan mengarahkan pengguna untuk menyelesaikan pengembalian terlebih dahulu.

## Latihan Reflektif

1. **Mengapa transaksi basis data (`beginTransaction()`, `commit()`, `rollBack()`) mutlak diperlukan pada proses peminjaman yang melibatkan dua tabel berbeda?**  
   Proses peminjaman terdiri dari dua operasi mutasi data yang saling bergantung: pencatatan baris transaksi pada tabel `peminjaman` dan pengurangan kuota stok pada tabel `galeri`. Apabila terjadi kegagalan sistem (seperti jaringan terputus atau server mati) tepat setelah baris peminjaman dicatat namun sebelum stok berkurang, data akan menjadi korup dan tidak konsisten (*phantom allocation*). Dengan transaksi ACID, seluruh rangkaian query dieksekusi sebagai satu kesatuan utuh: jika salah satu gagal, seluruh perubahan dibatalkan (*rollback*) ke kondisi semula.

2. **Bagaimana cara kerja klausa SQL `JOIN` dalam merelasikan 3 tabel (`peminjaman`, `galeri`, `users`), dan apa keuntungan menggunakan `INNER JOIN` dibanding query bertingkat?**  
   Klausa `INNER JOIN` mencocokkan nilai kunci asing (*foreign key*) `galeri_id` ke *primary key* `galeri.id`, serta `user_id` ke *primary key* `users.id`. Keuntungan menggunakan `JOIN` adalah mesin PostgreSQL dapat mengoptimalkan rencana eksekusi (*query execution plan*) dan mengambil seluruh data terkait dalam satu kali perjalanan jaringan (*single network round-trip*), alih-alih melakukan query N+1 yang membebani kinerja server dan lambat.

3. **Mengapa aturan bisnis seperti batas waktu peminjaman 14 hari wajib divalidasi di sisi *backend* (PHP/SQL) dan tidak cukup hanya di sisi *frontend* (JavaScript/HTML)?**  
   Validasi di sisi *frontend* hanya berfungsi sebagai panduan kenyamanan antarmuka (*user experience*), namun sangat mudah dimanipulasi atau dilewati (misalnya dengan menonaktifkan JavaScript browser, memodifikasi elemen form lewat Developer Tools, atau mengirimkan request POST langsung via cURL/Postman). Validasi di sisi *backend* merupakan gerbang pertahanan mutlak yang menjamin integritas aturan sistem tidak dapat dikompromikan oleh request apapun yang datang dari luar.

---
### Self Question
1. Bagaimana cara mengimplementasikan denda otomatis atau notifikasi email jika peminjaman telah melewati batas waktu 14 hari menggunakan cron job terjadwal?
2. Bagaimana perbandingan performa antara *Pessimistic Locking* (`SELECT ... FOR UPDATE`) dengan *Optimistic Locking* (kolom `version`/`timestamp`) pada skenario transaksi peminjaman bervolume tinggi?

### Notes
1. Seluruh transaksi mutasi stok diapit blok `try-catch` dengan `$pdo->rollBack()` pada blok penanganan exception untuk menjamin keandalan data.
2. Penggunaan operator tanggal native PostgreSQL `(CURRENT_DATE - p.tanggal_pinjam)` menyederhanakan perhitungan selisih hari langsung di tingkat database tanpa komputasi tambahan di PHP.
3. Hak akses role `admin` dan `user` dihormati secara konsisten: pengguna biasa hanya dapat mengelola data transaksinya sendiri, sementara administrator memiliki visibilitas atas seluruh anggota.
