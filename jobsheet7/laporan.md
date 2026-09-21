# Jobsheet 7
Nama: Mohammad Daanii Althaaf Reivan Fadhlillah <br>
Kelas: TI 2D <br>
NIM: 254107020123 <br>


## Tugas Mandiri
1. Penerapan dasar PHP, modularisasi template, pengolahan form, dan penyimpanan sementara berbasis session pada antarmuka SIMPUS-Mini:
- Konversi seluruh berkas antarmuka HTML statis menjadi skrip PHP dinamis (`.php`).
- Modularisasi tata letak menggunakan komponen template bersama pada `includes/header.php` dan `includes/footer.php` dengan perhitungan jalur relatif otomatis (`$base`).
- Penanganan input formulir Tambah Buku dan Tambah Anggota melalui metode HTTP POST (`$_POST`) yang diproses secara terpisah pada `buku/proses_tambah.php` dan `anggota/proses_tambah.php`.
- Penerapan validasi sisi server (*server-side validation*) independen untuk field wajib, rentang angka tahun terbit, dan batasan stok non-negatif.
- Penyimpanan data sementara menggunakan array superglobal `$_SESSION['buku']` dan `$_SESSION['anggota']` serta perenderan tabel dinamis dengan perulangan `foreach`.
- Pengelolaan notifikasi sekali-tampil (*flash message*) menggunakan `unset($_SESSION['flash'])` dan pola pengalihan halaman (*redirect*) pasca-POST via `header('Location: ...')` yang diakhiri pemanggilan `exit`.

## Latihan Reflektif

1. Mengapa validasi sisi server (*server-side validation*) pada `proses_tambah.php` tetap mutlak diperlukan meskipun formulir telah dilengkapi validasi atribut HTML5 (`required`, `min`, `max`) dan validasi JavaScript di sisi klien? Validasi sisi klien hanya berfungsi meningkatkan pengalaman pengguna (*user experience*) agar kesalahan pengisian terdeteksi langsung tanpa perlu memuat ulang halaman (*page reload*). Namun, validasi klien sangat mudah dilewati atau dimanipulasi oleh pengguna (dengan menonaktifkan JavaScript pada peramban, menyunting DOM melalui DevTools, atau mengirimkan request POST langsung menggunakan cURL/Postman). Oleh karena itu, validasi sisi server bertindak sebagai benteng pengaman data utama yang tidak dapat dimanipulasi dari sisi klien.
2. Mengapa setelah memproses data POST pada `proses_tambah.php` sangat disarankan melakukan pengalihan halaman (*redirect*) menggunakan `header('Location: ...')` yang diakhiri `exit;`? Praktik ini dikenal sebagai pola *Post/Redirect/Get (PRG)*. Jika halaman langsung merender tampilan tanpa pengalihan setelah request POST, maka ketika pengguna menekan tombol muat ulang (*refresh* atau F5), peramban akan mengirim ulang request POST tersebut sehingga memicu duplikasi data ke dalam sistem. Pemanggilan `exit;` wajib disertakan agar eksekusi script PHP segera dihentikan sehingga kode di bawahnya tidak sempat dijalankan.
3. Bagaimana mekanisme kerja *flash message* pada `$_SESSION['flash']` sehingga pesan notifikasi hanya muncul satu kali dan langsung hilang saat halaman di-refresh? Flash message disimpan ke dalam superglobal `$_SESSION['flash']` oleh script pemroses saat validasi berhasil atau gagal. Ketika halaman tujuan (`list.php` atau `tambah.php`) dibuka, isi pesan disalin ke dalam variabel lokal lalu sesegera mungkin dihapus dari memori session menggunakan fungsi `unset($_SESSION['flash'])`. Ketika halaman tersebut dimuat ulang (*refresh*), nilai `$_SESSION['flash']` sudah bernilai `null`, sehingga pesan notifikasi tidak akan muncul untuk kedua kalinya.


---
### Self Question
1. Apa bahaya keamanan yang dapat terjadi jika data dari `$_POST` atau `$_SESSION` langsung dicetak ke elemen HTML tanpa melalui fungsi pengamanan entitas seperti `htmlspecialchars()`?
2. Mengapa fungsi `session_start()` wajib dipanggil pada baris paling awal sebelum ada teks HTML, karakter spasi, atau output lain yang dikirimkan ke peramban?

### Notes
1. Kode PHP dieksekusi sepenuhnya di lingkungan server (*server-side*), sehingga peramban pengguna hanya menerima dokumen HTML hasil keluaran akhir tanpa dapat melihat source code PHP.
2. Modularisasi layout dengan `include` menerapkan prinsip *Don't Repeat Yourself* (DRY), memudahkan pembaruan navigasi dan footer secara serentak di seluruh halaman.
3. Superglobal `$_SESSION` menjaga kesinambungan status dan data antar-halaman selama sesi peramban berlangsung, sebelum basis data permanen diterapkan.
4. Operator *Null Coalescing* (`??`) menyederhanakan pengecekan keberadaan key array atau variabel tanpa memicu peringatan *undefined key*.
5. Pembersihan input menggunakan `trim()` memastikan data masukan tidak mengandung spasi kosong yang tidak diinginkan di awal maupun akhir teks.
6. Penerapan arsitektur PRG (*Post/Redirect/Get*) merupakan standar industri untuk menghindari masalah pengiriman ulang formulir secara ganda (*form resubmission*).
