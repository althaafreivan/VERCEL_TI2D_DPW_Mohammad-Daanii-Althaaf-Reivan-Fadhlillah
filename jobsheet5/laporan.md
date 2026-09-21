# Jobsheet 5
Nama: Mohammad Daanii Althaaf Reivan Fadhlillah <br>
Kelas: TI 2D <br>
NIM: 254107020123 <br>


## Tugas Mandiri
1. Penerapan manipulasi DOM dan event handling berbasis JavaScript murni (Vanilla JS) pada seluruh antarmuka SIMPUS-Mini:
- Penggantian navigasi hamburger CSS (*checkbox hack*) menjadi interaksi tombol berbasis JavaScript (`classList.toggle("nav-open")`) melalui fungsi `initNavToggle`.
- Fitur pencarian dan penyaringan data secara *real-time* pada tabel Buku dan Anggota menggunakan event `keyup` dan seleksi baris dinamis (`includes()`) melalui fungsi `initTableFilter`.
- Dialog konfirmasi hapus data interaktif menggunakan `confirm()` dan penghapusan baris tabel langsung dari tampilan DOM (`row.remove()`) melalui fungsi `initHapusConfirm`.
- Validasi form di sisi klien (*client-side*) pada form Tambah Buku dan Tambah Anggota yang mencegah pengiriman form kosong/tidak valid (`preventDefault()`) serta memunculkan/menghapus pesan galat inline melalui manipulasi DOM (`createElement`, `insertAdjacentElement`) melalui fungsi `initValidasiForm`.

## Latihan Reflektif

1. Mengapa fungsi pemicu event listener pada `app.js` memerlukan *guard clause* (seperti `if (!form) return;` atau `if (!toggleBtn || !nav) return;`)? *Guard clause* berfungsi sebagai mekanisme pengaman agar satu berkas JavaScript global yang sama dapat dimuat di berbagai halaman berbeda tanpa memicu galat eksekusi (*null reference error*) saat elemen tertentu tidak ditemukan pada halaman yang sedang dibuka.
2. Apa keunggulan penggunaan manipulasi elemen dinamis (`createElement` dan `insertAdjacentElement`) untuk menampilkan pesan galat form dibandingkan menyembunyikan/menampilkan elemen error statis yang sudah tertulis di HTML? Pembuatan elemen galat secara dinamis menjaga struktur kode HTML tetap bersih dan semantik saat halaman dimuat pertama kali, menghemat konsumsi memori, serta memastikan pesan galat lama dapat dibersihkan terlebih dahulu (`remove()`) sehingga tidak terjadi penumpukan elemen duplikat saat pengguna menekan tombol simpan berulang kali.
3. Mengapa validasi data di sisi klien (*client-side* menggunakan JavaScript) tidak boleh dijadikan satu-satunya sistem validasi dan tetap wajib didampingi validasi di sisi server (*server-side*)? Validasi JavaScript di browser berfokus pada kenyamanan pengalaman pengguna (*user experience*) untuk memberikan umpan balik langsung tanpa memuat ulang halaman (*page reload*), namun validasi ini sangat mudah dilewati atau dinonaktifkan oleh pengguna (melalui pengaturan browser, DevTools, atau pengiriman request HTTP langsung), sehingga validasi sisi server tetap menjadi garda pertahanan utama dalam menjaga keabsahan dan keamanan data aplikasi.


---
### Self Question
1. Bagaimana dampak performa dari pencarian tabel *real-time* menggunakan event `keyup` jika jumlah baris tabel mencapai ratusan atau ribuan data, dan apakah teknik *debouncing* dapat diterapkan untuk mengatasinya?
2. Mengapa penggunaan method `closest("tr")` lebih tangguh (*robust*) terhadap perubahan struktur tata letak tombol di dalam baris tabel dibandingkan penelusuran hierarki kaku seperti `btn.parentElement.parentElement`?

### Notes
1. Tag `<script>` dengan atribut `src` sebaiknya diletakkan di bagian akhir sebelum penutup `</body>` agar penguraian pohon dokumen HTML selesai sebelum script dieksekusi.
2. Event `DOMContentLoaded` memastikan seluruh logika manipulasi DOM hanya berjalan setelah struktur dokumen HTML selesai diproses oleh browser.
3. Method `e.preventDefault()` pada event `submit` membatalkan perilaku bawaan peramban untuk mencegah *refresh* halaman saat input belum memenuhi syarat validasi.
4. Method `insertAdjacentElement("afterend", span)` menyisipkan elemen saudara (*sibling*) baru tepat setelah elemen input target tanpa merusak struktur internal elemen tersebut.
5. Pemakaian `textContent` jauh lebih aman dari ancaman celah keamanan *Cross-Site Scripting* (XSS) dibandingkan `innerHTML` saat membaca maupun menampilkan teks ke antarmuka.
6. Method `closest(selector)` menelusuri rantai pohon DOM ke atas (*ancestor*) untuk menemukan elemen induk terdekat yang sesuai dengan selector yang ditentukan.
