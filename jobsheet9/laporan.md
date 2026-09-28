# Jobsheet 9 — Full Stack CRUD & Pagination
Nama: Mohammad Daanii Althaaf Reivan Fadhlillah <br>
Kelas: TI 2D <br>
NIM: 254107020123 <br>

## Tugas Praktikum & Mandiri
1. **Fitur Edit / Update Data (`collection/edit.php` & `collection/proses_edit.php`)**:
   - Menampilkan form pra-isi (*pre-filled form*) berdasarkan data rekaman yang diambil dari PostgreSQL dengan kueri `SELECT ... WHERE id = :id`.
   - Menggunakan input tersembunyi (*hidden input*) `<input type="hidden" name="id" value="...">` untuk meneruskan ID record secara aman.
   - Mendukung pembaruan metadata (judul, pengunggah, kategori, tahun, deskripsi) sekaligus opsi penggantian file gambar baru (dikonversi ke Base64 secara dinamis) atau tetap mempertahankan gambar lama.
   - Mengeksekusi perintah SQL `UPDATE galeri SET ... WHERE id = :id` melalui *prepared statement* PDO.

2. **Fitur Hapus Data Berbasis HTTP POST (`collection/hapus.php`)**:
   - Sesuai standar keamanan web dan modul praktikum, aksi penghapusan dipicu melalui form ber-metode **POST** (bukan GET) untuk mencegah eksekusi tak sengaja akibat prefetching browser atau web crawler.
   - Dilengkapi dialog konfirmasi JavaScript `onsubmit="return confirm('...');"` sebelum formulir dikirimkan ke server.
   - Mengeksekusi perintah SQL `DELETE FROM galeri WHERE id = :id` secara terproteksi.

3. **Pagination Server-side (`collection/catalog.php`)**:
   - Mengimplementasikan pemotongan data dinamis pada server menggunakan klausa `LIMIT :limit OFFSET :offset`.
   - Menghitung total rekaman menggunakan `SELECT COUNT(*)` dan membaginya dengan batas item per halaman untuk memperoleh total halaman `ceil($total / $limit)`.
   - Menyediakan navigasi navigasi halaman (*Sebelumnya*, *Nomor Halaman*, *Selanjutnya*) yang tetap mempertahankan status kata kunci pencarian.

4. **Pencarian Server-side (Tugas Mandiri)**:
   - Pencarian berbasis server menggunakan klausa `WHERE judul ILIKE :q OR pengunggah ILIKE :q OR kategori ILIKE :q` yang bersifat *case-insensitive* pada database PostgreSQL.

## Latihan Reflektif

1. **Mengapa aksi penghapusan (Delete) data harus menggunakan metode HTTP POST dan bukan HTTP GET?**  
   Metode HTTP GET menurut spesifikasi RFC bersifat *idempotent* dan *safe*, artinya pemanggilan URL GET seharusnya tidak boleh mengubah status atau menghapus data pada server. Jika aksi hapus menggunakan GET (misalnya `hapus.php?id=1`), tautan tersebut rentan dipicu secara tidak sengaja oleh mesin pencari (*web crawler*), ekstensi *browser prefetching*, atau serangan CSRF melalui tag `<img src="hapus.php?id=1">`. Dengan menggunakan metode POST, permintaan harus dikirimkan secara eksplisit melalui pengiriman formulir dengan konfirmasi pengguna.

2. **Bagaimana cara kerja klausa `LIMIT` dan `OFFSET` dalam pagination basis data relasional?**  
   Klausa `LIMIT` menentukan jumlah maksimum baris rekaman yang akan dikembalikan oleh kueri dalam satu kali eksekusi (misalnya `LIMIT 6` untuk 6 gambar per halaman). Sedangkan `OFFSET` menentukan jumlah baris yang harus dilewati sebelum mulai mengambil data. Nilai offset dihitung dengan rumus: `offset = (halaman_aktif - 1) * limit`. Dengan demikian, server hanya memproses dan mengirimkan potongan data yang dibutuhkan oleh halaman yang sedang aktif sehingga menghemat memori dan bandwidth.

3. **Bagaimana alur pembaruan data (Update) yang melibatkan berkas gambar opsional?**  
   Sistem memeriksa apakah pengguna mengunggah berkas baru melalui `$_FILES['gambar']['error'] === UPLOAD_ERR_OK`. Jika ada berkas baru yang valid, berkas tersebut dikonversi dan kolom `file_gambar` ikut diperbarui dalam query `UPDATE`. Jika tidak ada berkas baru yang diunggah, kueri `UPDATE` hanya memperbarui kolom teks metadata (judul, pengunggah, kategori, tahun, deskripsi), sehingga gambar lama yang tersimpan di basis data tetap aman dan tidak tertimpa nilai kosong.

---
### Self Question
1. Bagaimana cara mengoptimalkan performa pagination pada tabel dengan jutaan baris data di mana klausa `OFFSET` besar dapat menyebabkan penurunan performa (*keyset pagination* vs *offset pagination*)?
2. Bagaimana mekanisme penanganan transaksi database (`beginTransaction()`, `commit()`, `rollBack()`) saat melakukan update data berseri agar mencegah terjadinya kondisi data inkonsisten (*partial update*)?

### Notes
1. Input tipe hidden (`type="hidden"`) sangat penting untuk membawa identitas primer rekaman (`id`) saat melakukan proses update tanpa menampilkan kontrol input yang dapat diedit oleh pengguna.
2. Penanganan pesan notifikasi (*flash message*) pada session memastikan pengguna selalu mendapatkan umpan balik visual setelah pengalihan halaman (*Post/Redirect/Get pattern*).
3. Penggunaan fungsi `filter_var($id, FILTER_VALIDATE_INT)` memberikan sanitasi awal sebelum parameter ID diproses lebih lanjut oleh database.
