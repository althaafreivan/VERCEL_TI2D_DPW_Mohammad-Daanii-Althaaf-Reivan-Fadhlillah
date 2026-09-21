-- Jobsheet 8: Skema Database PixelGallery (PostgreSQL / Supabase)
-- Jalankan di SQL Editor Supabase atau terminal psql:

CREATE TABLE IF NOT EXISTS galeri (
    id SERIAL PRIMARY KEY,
    judul VARCHAR(255) NOT NULL,
    pengunggah VARCHAR(255) NOT NULL,
    kategori VARCHAR(100) DEFAULT 'Umum',
    file_gambar VARCHAR(255) NOT NULL,
    tahun INTEGER NOT NULL,
    deskripsi VARCHAR(255)
);

CREATE TABLE IF NOT EXISTS anggota (
    id SERIAL PRIMARY KEY,
    nama VARCHAR(255) NOT NULL,
    no_anggota VARCHAR(50) NOT NULL UNIQUE,
    alamat VARCHAR(255),
    no_hp VARCHAR(30)
);
