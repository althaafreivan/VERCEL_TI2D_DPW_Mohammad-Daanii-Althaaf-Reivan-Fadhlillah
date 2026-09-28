# Jobsheet 10 — Autentikasi & Manajemen Sesi Pengguna
Nama: Mohammad Daanii Althaaf Reivan Fadhlillah <br>
Kelas: TI 2D <br>
NIM: 254107020123 <br>

## Tugas Praktikum & Mandiri
1. **Perancangan Skema Tabel `users` (`sql/02_users.sql`)**:
   - Mendefinisikan tabel `users` di basis data PostgreSQL dengan kolom: `id` (SERIAL PRIMARY KEY), `nama` (VARCHAR), `username` (VARCHAR UNIQUE), `password` (VARCHAR 255), dan `role` (VARCHAR 20 DEFAULT 'user').
   - Menerapkan batasan `UNIQUE` pada kolom `username` untuk mencegah duplikasi akun pengguna.

2. **Fitur Registrasi Pengguna (`auth/register.php` & `auth/proses_register.php`)**:
   - Menyediakan form input pendaftaran akun baru: Nama Lengkap, Username, Role, Password, dan Konfirmasi Password.
   - Mengamankan penyimpanan kata sandi menggunakan fungsi hash bawaan standar industri PHP `password_hash($password, PASSWORD_DEFAULT)` yang menghasilkan hash BCRYPT aman (**bukan plaintext**).
   - Validasi ketat sisi server: panjang password minimal 6 karakter, kecocokan password dengan konfirmasi, format karakter username, dan pengecekan keunikan username di database.

3. **Fitur Autentikasi Login & Logout (`auth/login.php`, `auth/proses_login.php`, `auth/logout.php`)**:
   - Memverifikasi kecocokan kata sandi menggunakan `password_verify($password, $user['password'])`.
   - Jika kredensial valid, data pengguna disimpan ke dalam variabel sesi server: `$_SESSION['user_id']`, `$_SESSION['username']`, `$_SESSION['nama']`, dan `$_SESSION['role']`.
   - Proses logout (`auth/logout.php`) membersihkan seluruh variabel sesi dengan `session_unset()`, menghapus cookie sesi, dan menghancurkan sesi dengan `session_destroy()`.

4. **Guard Clause Middleware (`includes/auth.php`)**:
   - Mengamankan seluruh rute halaman operasi data (`upload-asset.php`, `edit.php`, `proses_edit.php`, dan `hapus.php`).
   - Jika pengunjung belum memiliki sesi `$_SESSION['user_id']`, sistem secara otomatis menolak akses dan mengalihkan pengguna ke halaman login (`auth/login.php`) disertai notifikasi *flash message* peringatan.

5. **Kontrol Akses Berbasis Peran / Role-Based Access Control (Tugas Mandiri)**:
   - Pengguna dengan role `admin` memiliki hak istimewa penuh untuk mengedit dan menghapus semua gambar di galeri.
   - Pengguna biasa (`user`) hanya memiliki hak untuk mengedit dan menghapus gambar yang ia unggah sendiri (`pengunggah === $_SESSION['nama']`).
   - Pada halaman katalog (`catalog.php`), tombol *Edit* dan *Hapus* hanya dimunculkan jika pengguna berhak mengelolanya. Jika bukan haknya, tombol disembunyikan dan sistem tetap memvalidasi hak akses di tingkat *backend* untuk mencegah manipulasi ID lewat URL.

6. **Antarmuka Navigasi Dinamis (`includes/header.php`)**:
   - Navbar secara dinamis mendeteksi status login:
     - Jika tamu (*guest*): Menampilkan menu *Beranda*, *Galeri*, tombol *Login*, dan *Daftar*.
     - Jika sudah login: Menampilkan menu *Beranda*, *Galeri*, *+ Upload*, lencana identitas pengguna (Avatar, Nama, Role), dan tombol *Logout*.

## Latihan Reflektif

1. **Mengapa kata sandi pengguna wajib disimpan dalam bentuk hash menggunakan `password_hash()` dan dilarang keras disimpan dalam bentuk teks polos (*plaintext*)?**  
   Menyimpan kata sandi dalam bentuk *plaintext* merupakan pelanggaran fatal dalam keamanan perangkat lunak. Apabila basis data mengalami kebocoran (*data breach* atau *SQL Injection*), seluruh kata sandi pengguna akan langsung terbaca oleh penyerang. Fungsi `password_hash()` menggunakan algoritma hashing satu arah yang kuat (seperti BCRYPT) dilengkapi dengan *salt* unik secara otomatis. Hash satu arah ini mustahil dibalikkan (*irreversible*) menjadi teks aslinya, sehingga meskipun data hash dicuri, penyerang tidak dapat langsung mengetahui kata sandi asli pengguna.

2. **Bagaimana cara kerja fungsi `password_verify()` dalam mencocokkan kata sandi masukan pengguna dengan hash yang tersimpan di database?**  
   Fungsi `password_verify($password_input, $hash_database)` mengekstrak algoritma dan nilai *salt* yang tertanam di dalam string `$hash_database`. Fungsi ini kemudian menghitung hash dari kata sandi masukan menggunakan *salt* dan parameter yang sama, lalu membandingkan hasilnya dalam waktu konstan (*constant-time comparison*) untuk mencegah serangan celah waktu (*timing attacks*). Jika kedua hash identik, fungsi mengembalikan nilai `true`.

3. **Apa fungsi dari file `includes/auth.php` sebagai Guard Clause, dan mengapa harus diletakkan di baris paling atas halaman yang dilindungi?**  
   File `includes/auth.php` bertindak sebagai pintu gerbang verifikasi hak akses (*authorization middleware*). Berkas ini memeriksa eksistensi `$_SESSION['user_id']`. Jika tidak ada, eksekusi kode langsung dihentikan menggunakan `exit` setelah melakukan redirect HTTP. Meletakkannya di baris paling atas (sebelum query SQL atau output HTML apapun) sangat krusial agar pengguna yang tidak terautentikasi tidak dapat melihat data sensitif ataupun memicu perubahan data di server.

---
### Self Question
1. Bagaimana cara menerapkan mekanisme proteksi *Brute Force Login* (misalnya membatasi maksimal 5 percobaan login gagal sebelum akun terkunci selama 15 menit)?
2. Mengapa fungsi `session_regenerate_id(true)` sangat direkomendasikan untuk dipanggil sesaat setelah proses verifikasi login berhasil (pencegahan serangan *Session Fixation*)?

### Notes
1. Algoritma `PASSWORD_DEFAULT` pada PHP secara berkala diperbarui mengikuti standar keamanan kriptografi terbaru tanpa perlu mengubah kode aplikasi pemanggil.
2. Penanganan `session_status() === PHP_SESSION_NONE` sebelum memanggil `session_start()` mencegah timbulnya peringatan (*PHP Notice: session already started*).
3. Penerapan validasi kepemilikan data di sisi server (backend) adalah kewajiban mutlak, karena menyembunyikan tombol di antarmuka (frontend) saja tidak cukup melindungi sistem dari manipulasi permintaan HTTP langsung.
