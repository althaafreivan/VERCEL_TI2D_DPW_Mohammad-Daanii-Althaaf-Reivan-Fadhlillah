# Jobsheet 6
Nama: Mohammad Daanii Althaaf Reivan Fadhlillah <br>
Kelas: TI 2D <br>
NIM: 254107020123 <br>


## Tugas Mandiri
1. Penerapan komunikasi asinkron menggunakan Fetch API dan format pertukaran data JSON pada antarmuka SIMPUS-Mini:
- Pembuatan berkas sumber data statis `data/buku.json` (10 data buku) dan `data/anggota.json` (4 data anggota) sebagai representasi sementara antarmuka API.
- Pemuatan data tabel secara asinkron (`async/await` dan `fetch()`) pada halaman Daftar Buku (`assets/js/buku.js`) dan Daftar Anggota (`assets/js/anggota.js`) dengan mengosongkan `<tbody>` statis.
- Penyediaan indikator pemuatan (*loading indicator*) yang tampil selama proses penarikan data berlangsung dengan simulasi jeda jaringan (*delay* 600ms).
- Penanganan galat jaringan (*error handling*) terstruktur menggunakan blok `try...catch...finally` yang menampilkan pesan galat informatif di dalam tabel saat proses fetch gagal.
- Penerapan pola *Event Delegation* pada tombol Hapus di `assets/js/app.js` (`document.addEventListener("click", ...)`) untuk menangani interaksi pada elemen baris tabel yang dirender secara dinamis setelah dokumen selesai dimuat.

## Latihan Reflektif

1. Mengapa pemeriksaan status `res.ok` harus dilakukan secara manual di dalam blok `try` saat menggunakan `fetch()`? Karena Promise yang dihasilkan oleh `fetch()` tidak otomatis mengalami penolakan (*reject*) saat server memberikan kode status galat HTTP seperti 404 (Not Found) atau 500 (Internal Server Error). `fetch()` hanya melakukan *reject* jika terjadi kegagalan jaringan tingkat fisik (seperti koneksi internet terputus), sehingga pemeriksaan `if (!res.ok) throw new Error(...)` wajib ditulis secara eksplisit agar respons HTTP yang tidak berhasil dapat diteruskan ke blok penanganan galat `catch`.
2. Mengapa tombol Hapus pada baris tabel yang dirender secara dinamis via `fetch` membutuhkan teknik *Event Delegation* (`document.addEventListener("click")`) alih-alih `querySelectorAll(".btn-hapus").forEach(...)`? Ketika kode inisialisasi dijalankan saat event `DOMContentLoaded`, elemen baris tabel (`<tr>`) dan tombol aksi belum terbentuk di dalam DOM karena proses `fetch` masih berlangsung secara asinkron di latar belakang. Jika menggunakan `querySelectorAll`, pencarian tersebut akan menghasilkan NodeList kosong; dengan *Event Delegation*, listener didaftarkan pada elemen leluhur permanen (`document`) dan memanfaatkan sifat perambatan event (*event bubbling*) serta `e.target.closest(".btn-hapus")` untuk merespons klik tombol baru kapan pun elemen tersebut diciptakan.
3. Mengapa pengujian antarmuka yang menggunakan `fetch()` terhadap berkas JSON lokal wajib dijalankan melalui web server lokal (seperti `php -S localhost:8000` atau Live Server) dan akan gagal jika dibuka langsung dengan protokol `file://`? Peramban modern menerapkan kebijakan keamanan *Same-Origin Policy* dan regulasi CORS (*Cross-Origin Resource Sharing*) yang ketat, di mana permintaan asinkron XMLHttpRequest dan Fetch API secara sengaja diblokir pada skema protokol `file://` demi mencegah script berbahaya membaca berkas sensitif secara ilegal dari media penyimpanan lokal komputer pengguna.


---
### Self Question
1. Bagaimana cara mengimplementasikan strategi penyimpanan sementara (*client-side caching*) menggunakan `localStorage` atau `sessionStorage` agar berkas data JSON tidak perlu diambil ulang dari jaringan setiap kali pengguna berpindah halaman?
2. Apa kelebihan dan kelemahan arsitektur perenderan data di sisi klien (*Client-Side Rendering* via Fetch/AJAX) dibandingkan perenderan langsung di sisi server (*Server-Side Rendering* via PHP) ditinjau dari aspek performa awal (*First Contentful Paint*) dan keramahan terhadap mesin pencari (SEO)?

### Notes
1. `fetch()` adalah fungsi bawaan JavaScript modern berbasis Promise untuk melakukan komunikasi data HTTP secara asinkron.
2. Sintaks `async/await` mempermudah penulisan dan pembacaan kode asinkron sehingga tampak sekuensial tanpa ketergantungan pada *chaining* `.then()` yang berantai.
3. Blok `finally` dijamin selalu dieksekusi di akhir alur baik saat proses `try` berhasil maupun saat tertangkap di blok `catch`, sangat ideal untuk menyembunyikan status pemuatan (*loading indicator*).
4. Format JSON (*JavaScript Object Notation*) merepresentasikan pertukaran data terstruktur berbasis teks yang dapat langsung diurai menjadi array atau objek JavaScript melalui method `res.json()`.
5. Mekanisme *Event Bubbling* memungkinkan event klik yang dipicu dari elemen terdalam menjalar naik ke simpul dokumen terluar, menjadi fondasi utama teknik *Event Delegation*.
6. Baris tabel dinamis yang dibuat dari pembacaan data eksternal sebaiknya dipastikan aman dari celah XSS dengan menghindari injeksi teks mentah yang tidak tersanitasi ke dalam DOM.
