# Jobsheet 2
Nama: Mohammad Daanii Althaaf Reivan Fadhlillah <br>
Kelas: TI 2D <br>
NIM: 254107020123 <br>


## Tugas Mandiri
1. Penataan kartu ringkasan statistik di Beranda menggunakan CSS Grid (`repeat(3, 1fr)`): [screenshot HTML](Screenshot/Screenshot%202026-08-30%20195518.png) | [screenshot CSS](Screenshot/Screenshot%202026-08-30%20195435.png)

## Latihan Reflektif

1. `box-sizing: border-box` wajib digunakan agar padding dan border dihitung ke dalam lebar elemen, sehingga layout tidak melebar melebihi `width`.
2. Flexbox dipakai di navbar karena tata letaknya 1 dimensi (horizontal), sedangkan CSS Grid dipakai di kartu statistik karena butuh pembagian 3 kolom yang presisi.
3. Tombol Edit dan Hapus diwarnai tanpa class tambahan dengan memanfaatkan pseudo-class `:first-of-type` (oranye) dan `:last-of-type` (merah).


---
### Self Question
1. Kenapa navbar lebih disarankan pakai Flexbox daripada Grid, padahal sama-sama bisa bikin elemen sejajar?
2. Kalau styling terlalu bergantung pada posisi tag seperti `:nth-of-type(2)`, bukannya nanti berisiko kalau urutan tag HTML-nya diubah?

### Notes
1. `box-sizing: border-box` membuat perhitungan ukuran kotak elemen lebih mudah diprediksi.
2. `margin: auto` pada elemen dengan `max-width` digunakan untuk menengahkan posisi secara horizontal.
3. Selector yang lebih spesifik (`header nav a`) akan menimpa aturan selector umum (`a`).
4. `repeat(3, 1fr)` membagi ruang menjadi 3 kolom yang sama besar secara otomatis.
5. `tbody tr:nth-child(even)` membuat efek zebra-stripe pada tabel agar baris lebih mudah dibaca.
6. `form label` diubah ke `display: block` agar input otomatis berada di bawah label.
