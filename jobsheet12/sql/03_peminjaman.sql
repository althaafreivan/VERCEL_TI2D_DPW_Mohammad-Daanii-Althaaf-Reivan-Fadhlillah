-- ==============================================================================
-- JOBSHEET 12: SKEMA TABEL TRANSAKSI PEMINJAMAN & LISENSI ASET (PixelGallery)
-- ==============================================================================

-- 1. Penambahan Kolom Stok Lisensi pada Tabel Galeri
ALTER TABLE galeri 
ADD COLUMN IF NOT EXISTS stok INTEGER NOT NULL DEFAULT 5;

-- 2. Pembuatan Tabel Relasional Peminjaman (Menghubungkan galeri & users)
CREATE TABLE IF NOT EXISTS peminjaman (
    id SERIAL PRIMARY KEY,
    galeri_id INTEGER NOT NULL REFERENCES galeri(id) ON DELETE CASCADE,
    user_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    tanggal_pinjam DATE NOT NULL DEFAULT CURRENT_DATE,
    tanggal_kembali DATE,
    status VARCHAR(20) NOT NULL DEFAULT 'dipinjam',
    catatan VARCHAR(255)
);

-- Indeks untuk mempercepat pencarian histori dan pengecekan keterlambatan pinjaman
CREATE INDEX IF NOT EXISTS idx_peminjaman_user ON peminjaman(user_id);
CREATE INDEX IF NOT EXISTS idx_peminjaman_status ON peminjaman(status);
CREATE INDEX IF NOT EXISTS idx_peminjaman_galeri ON peminjaman(galeri_id);
