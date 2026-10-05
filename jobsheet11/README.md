# Jobsheet 11 - Keamanan Aplikasi Web Dasar (Hardening)

Modul praktikum ini berfokus pada audit dan perkuatan keamanan (*security hardening*) pada aplikasi **PixelGallery** yang telah dibangun dari Jobsheet 8 hingga 10. Tidak ada penambahan entitas bisnis baru pada modul ini, melainkan peningkatan postur keamanan aplikasi web mengikuti rekomendasi OWASP.

## Pilar Keamanan yang Diimplementasikan
1. **Pencegahan SQL Injection (SQLi)**: 100% query database menggunakan PDO Prepared Statements dengan parameter binding terpisah.
2. **Pencegahan Cross-Site Scripting (XSS)**: Seluruh pencetakan output dinamis disanitasi menggunakan `htmlspecialchars()` dan fungsi helper `e()`.
3. **Pencegahan Cross-Site Request Forgery (CSRF)**: Mekanisme token acak 32-byte kriptografis via sesi server dan divalidasi dengan `hash_equals()` pada setiap form mutasi POST.
4. **Validasi & Whitelisting Input**: Validasi tipe data numerik dan whitelist nilai kategori serta format file gambar.
5. **Mitigasi Session Fixation (Tugas Mandiri)**: Pemanggilan `session_regenerate_id(true)` saat proses autentikasi login berhasil.

## Panduan Pengujian & Audit
Dokumen checklist audit keamanan lengkap dan hasil uji penetrasi dapat dilihat pada [docs/security-checklist.md](docs/security-checklist.md).
