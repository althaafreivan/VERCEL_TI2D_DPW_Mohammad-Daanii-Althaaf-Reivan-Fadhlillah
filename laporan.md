# Jobsheet 4
Nama: Mohammad Daanii Althaaf Reivan Fadhlillah <br>
Kelas: TI 2D <br>
NIM: 254107020123 <br>


## Tugas Mandiri
1. Perancangan UI/UX menyeluruh untuk fitur yang belum dibangun (Login Petugas, Dashboard Petugas, Form Peminjaman Buku, Form Pengembalian Buku, dan Riwayat Peminjaman per Anggota) yang dituangkan dalam dokumen wireframe teks ASCII dan diagram alur pengguna (*user flow*), dilengkapi spesifikasi mockup visual (palet warna, tipografi, komponen UI) sesuai *style guide* Jobsheet 2–3 serta penanganan *edge cases* (stok habis dan tunggakan): [Dokumen Wireframe & User Flow](docs/wireframe.md) | [Infografis Alur](Screenshot/Infografis.png)

## Latihan Reflektif

1. UI (*User Interface*) berfokus pada aspek visual tampilan (warna, tata letak, dan tipografi seperti yang diatur di CSS), sedangkan UX (*User Experience*) berfokus pada efisiensi alur, kemudahan penggunaan, dan kepuasan pengguna saat menyelesaikan suatu tugas. Merancang UI/UX terlebih dahulu melalui wireframe dan user flow sangat penting karena mengidentifikasi serta memperbaiki kelemahan alur di tahap sketsa jauh lebih mudah dan murah daripada membongkar ulang kode program yang sudah jadi.
2. Mendefinisikan aktor di awal (Tamu vs Petugas) secara langsung menetapkan batasan hak akses (*otorisasi*) sistem sebelum kode autentikasi ditulis. Aktor Tamu hanya diperbolehkan mengakses katalog publik (Beranda dan Daftar Buku) tanpa login, sedangkan Petugas memiliki hak akses istimewa untuk mengelola data buku/anggota dan memproses transaksi sirkulasi (peminjaman dan pengembalian) di dalam Dashboard.
3. Aturan bisnis dan *edge cases* (seperti filter buku hanya yang memiliki `stok > 0`, penyesuaian stok otomatis, serta peringatan anggota bertunggakan) wajib didefinisikan sejak tahap user flow agar pengembang memiliki acuan validasi logika yang jelas dan tidak ada kondisi kritis yang terlewat saat implementasi JavaScript (Jobsheet 5) maupun backend PHP/PostgreSQL nantinya.


---
### Self Question
1. Mengapa wireframe pada tahap awal perancangan justru lebih efektif disajikan dalam bentuk *low-fidelity* (seperti sketsa teks ASCII) dibandingkan langsung membuat mockup *high-fidelity* penuh warna?
2. Kapan sebaiknya alur transaksi peminjaman dan pengembalian dipisahkan ke dalam form/halaman yang berbeda, dan kapan lebih efektif disatukan dalam satu antarmuka sirkulasi terpadu?

### Notes
1. Tidak semua jobsheet harus menambah baris kode; fase analisis kebutuhan dan perancangan UI/UX adalah tahapan rekayasa perangkat lunak yang sah dan penting agar penulisan kode selanjutnya lebih terarah.
2. Wireframe berfungsi menjawab pertanyaan "elemen apa saja yang ada di halaman dan di mana letaknya", sedangkan user flow menjawab "bagaimana langkah urutan pengguna untuk menyelesaikan suatu tugas".
3. Konvensi simbol ASCII pada wireframe teks memetakan representasi semantik HTML: kotak garis sebagai kontainer/kartu, `[_______]` sebagai input form, dan `[ Teks ]` sebagai tombol aksi.
4. Desain antarmuka baru memaksimalkan penggunaan ulang (*reusability*) komponen visual yang sudah ada (navbar, skema warna `#1d5b8a`, kartu statistik, dan tata letak tabel) sehingga tidak perlu menulis CSS dari awal.
5. Efek samping transaksi (seperti perubahan angka stok buku dan mutasi status transaksi) dipetakan di diagram alur sebagai pengingat logika proses backend yang wajib dibangun.
6. Dokumen rancangan pada `docs/wireframe.md` menjadi cetak biru (*blueprint*) acuan pembangunan struktur antarmuka baru mulai Jobsheet 5 hingga tahap akhir proyek.
