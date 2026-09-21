# Jobsheet 8
Nama: Mohammad Daanii Althaaf Reivan Fadhlillah <br>
Kelas: TI 2D <br>
NIM: 254107020123 <br>


## Tugas Mandiri
1. Menghubungkan aplikasi SIMPUS-Mini dengan basis data relasional PostgreSQL menggunakan ekstensi PDO (PHP Data Objects):
- Perancangan dan pembuatan skema basis data `simpus_mini` melalui berkas DDL `sql/01_buku_anggota.sql` yang mendefinisikan tabel `buku` dan `anggota`.
- Pembuatan modul koneksi basis data terpusat pada `includes/koneksi.php` menggunakan PDO driver `pgsql` yang dilengkapi penanganan eksepsi `try...catch (PDOException $e)`.
- Migrasi penyimpanan data dari array sementara `$_SESSION` menjadi penyimpanan persisten di tabel PostgreSQL pada `buku/proses_tambah.php` dan `anggota/proses_tambah.php` menggunakan *prepared statement* (`$pdo->prepare()` dan `$stmt->execute()`) dengan klausa `RETURNING id`.
- Pembacaan dan perenderan data dinamis pada halaman Daftar Buku dan Daftar Anggota menggunakan kueri SQL `SELECT * FROM ... ORDER BY id DESC` dan method `fetchAll(PDO::FETCH_ASSOC)`.
- Pembaruan informasi kartu statistik pada Beranda (`index.php`) secara real-time langsung dari basis data menggunakan kueri agregasi `SELECT COUNT(*) FROM ...` dan method `fetchColumn()`.

## Latihan Reflektif

1. Apa keuntungan utama menggunakan PDO (*PHP Data Objects*) dibandingkan fungsi basis data spesifik vendor (seperti pustaka lawas `pg_connect()` atau `mysqli_*`)? PDO menyediakan antarmuka lapisan abstraksi akses data (*data-access abstraction layer*) yang seragam untuk berbagai jenis mesin database (seperti PostgreSQL, MySQL, hingga SQLite). Hal ini memberikan fleksibilitas tinggi karena apabila sistem di kemudian hari berganti mesin basis data, kode pemrosesan data aplikasi tidak perlu dirombak total. Selain itu, PDO mendukung penanganan galat terstruktur berbasis objek menggunakan `PDOException` serta memiliki fitur *prepared statement* bawaan yang memisahkan instruksi SQL dengan data input.
2. Mengapa penyusunan query SQL yang melibatkan input pengguna wajib menggunakan *prepared statement* (`prepare()` + placeholder `:nama`) dan dilarang keras menggunakan penggabungan string (*string concatenation*)? Penggabungan string secara langsung (misalnya `"INSERT INTO buku VALUES ('" . $judul . "')"`) membuka celah kerentanan fatal *SQL Injection*, di mana karakter khusus seperti tanda kutip dapat disalahgunakan penyerang untuk mengubah struktur perintah kueri yang dieksekusi database. Dengan *prepared statement*, pola perintah kueri dikompilasi terlebih dahulu oleh database, dan parameter masukan dikirim secara terpisah sehingga nilai input selalu diperlakukan murni sebagai nilai data literal (bukan bagian dari sintaks SQL yang dapat dieksekusi).
3. Apa perbedaan fungsi antara method `fetchAll(PDO::FETCH_ASSOC)` dengan `fetchColumn()`, dan pada kondisi apa masing-masing method tersebut tepat digunakan? Method `fetchAll(PDO::FETCH_ASSOC)` mengambil seluruh baris rekaman hasil kueri ke dalam bentuk array asosiatif multidimensi dengan nama kolom sebagai indeksnya, sangat ideal digunakan untuk menampilkan daftar data tabel seperti pada `buku/list.php` dan `anggota/list.php`. Sebaliknya, `fetchColumn()` hanya membaca satu nilai skalar dari satu kolom pada baris pertama hasil kueri, sehingga sangat efisien dan optimal untuk eksekusi kueri agregat seperti `SELECT COUNT(*)` pada ringkasan statistik Beranda tanpa membebani alokasi memori array.


---
### Self Question
1. Bagaimana pendekatan terbaik untuk menangani galat pelanggaran batasan unik (*unique constraint violation*, misalnya saat `no_anggota` yang sama diinputkan) agar pengguna mendapatkan pesan *flash message* yang ramah alih-alih layar galat sistem?
2. Mengapa tipe data `SERIAL` pada PostgreSQL secara otomatis menghasilkan objek *sequence* penomoran, dan apa konsekuensinya terhadap urutan nomor ID jika terjadi transaksi penambahan data yang dibatalkan (*rollback*)?

### Notes
1. Basis data PostgreSQL menjamin data tersimpan secara persisten pada media penyimpanan fisik, menyelesaikan keterbatasan penyimpanan sementara session yang terhapus saat peramban ditutup.
2. Klausa `RETURNING id` pada operasi `INSERT` di PostgreSQL memungkinkan aplikasi langsung memperoleh nilai ID auto-increment yang baru saja dibuat tanpa perlu menjalankan kueri seleksi terpisah.
3. Konfigurasi `PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION` memastikan setiap kegagalan operasi SQL akan memicu eksepsi yang dapat ditangani secara elegan dalam blok `try...catch`.
4. Klausa `ORDER BY id DESC` digunakan untuk menyajikan baris data terbaru di posisi paling atas tabel, memudahkan pengguna melihat rekaman yang baru saja dimasukkan.
5. Pemanggilan berkas koneksi `includes/koneksi.php` menggunakan pernyataan `require` alih-alih `include` karena ketiadaan koneksi database merupakan kesalahan fatal yang menghentikan aplikasi.
6. Batasan skema seperti `PRIMARY KEY`, `NOT NULL`, dan `UNIQUE` berfungsi sebagai lapis pertahanan integritas data di tingkat fisik basis data untuk mencegah inkonsistensi data.
