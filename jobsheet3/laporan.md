# Jobsheet 3
Nama: Mohammad Daanii Althaaf Reivan Fadhlillah <br>
Kelas: TI 2D <br>
NIM: 254107020123 <br>


## Tugas Mandiri
1. Penerapan desain responsif pada seluruh halaman (Beranda, Buku, dan Anggota) menggunakan framework Bootstrap 5 (CDN) dan penyesuaian gaya brand SIMPUS-Mini:
- Migrasi tata letak halaman ke sistem grid 12 kolom Bootstrap (`.row`, `.col-12`, `.col-md-4`) dan komponen pembungkus `.card` dengan `.shadow-sm`.
- Navigasi responsif menggunakan komponen `.navbar` dengan hamburger toggle berbasis JavaScript (`.navbar-toggler` dan `.collapse`) menggantikan teknik *checkbox hack*.
- Penyajian data tabel responsif menggunakan `.table`, `.table-striped`, `.table-hover`, dan pembungkus `.table-responsive` serta komponen tombol aksi (`.btn-warning`, `.btn-danger`).
- Penataan formulir responsif berbasis utility class (`.form-label`, `.form-control`, `.form-select`, `.mb-3`).

## Latihan Reflektif

1. Mengapa transisi dari CSS murni ke framework CSS (seperti Bootstrap 5) dapat mempercepat proses pengembangan aplikasi, namun tetap memerlukan penulisan CSS custom atau inline style? Bootstrap menyediakan ratusan utility class dan komponen UI siap pakai yang mengeliminasi kebutuhan menulis ulang kode CSS umum dari nol, namun kebutuhan identitas visual khusus (seperti warna brand `#1d5b8a`) berada di luar palet bawaan sehingga tetap membutuhkan override CSS terarah.
2. Apa perbedaan mekanisme kerja menu hamburger antara teknik *checkbox hack* (CSS murni) dengan komponen `.navbar-toggler` milik Bootstrap? *Checkbox hack* bekerja sepenuhnya di sisi browser tanpa JavaScript dengan mengandalkan status pseudo-class `:checked` dan sibling combinator `~`, sedangkan komponen Bootstrap memanfaatkan atribut data HTML5 (`data-bs-toggle="collapse"`, `data-bs-target`) yang dikendalikan secara dinamis oleh pustaka JavaScript `bootstrap.bundle.min.js` serta dilengkapi penanda aksesibilitas (`aria-expanded`).
3. Bagaimana sistem grid 12 kolom pada Bootstrap mengimplementasikan prinsip *mobile-first* pada kartu statistik Beranda? Melalui penulisan class `.col-12 .col-md-4`, elemen kartu secara default memakan 12 kolom penuh (1 kolom bertumpuk) pada layar perangkat mobile, dan baru membagi tata letak menjadi 3 kolom sejajar (12/4 = 3) ketika lebar viewport mencapai batas breakpoint tablet atau layar menengah ke atas (`md` ≥768px).


---
### Self Question
1. Kapan suatu proyek sistem informasi web lebih disarankan menggunakan CSS murni (hand-crafted) dibandingkan mengadopsi framework besar seperti Bootstrap?
2. Bagaimana strategi terbaik mengelola override warna brand pada Bootstrap tanpa harus berlebihan menyematkan deklarasi `!important` di dalam berkas CSS?

### Notes
1. Pemuatan Bootstrap 5.3 melalui CDN (`bootstrap.min.css` dan `bootstrap.bundle.min.js`) mempermudah integrasi tanpa memerlukan konfigurasi build tools lokal.
2. Kontainer utama `.container` berfungsi membatasi lebar konten maksimum secara terpusat (`margin: auto`) pada berbagai ukuran layar monitor.
3. Pembagian kolom pada grid Bootstrap selalu dihitung dari kelipatan total 12 kolom di dalam pembungkus baris bertanda `.row`.
4. Komponen `.card` secara otomatis menyediakan latar putih, sudut melengkung (*border-radius*), dan padding rapi pengganti styling `<section>` manual.
5. Tabel responsif tetap memerlukan pembungkus `.table-responsive` untuk menjamin adanya scrollbar horizontal lokal jika kolom data melebihi layar ponsel.
6. Deklarasi `!important` pada CSS kustom diperlukan secara selektif untuk menimpa spesifisitas tinggi dari class bawaan framework Bootstrap.
