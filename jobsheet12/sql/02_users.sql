-- =========================================================================
-- Jobsheet 10: Skema Tabel users & Seed Akun Awal (Autentikasi & Otorisasi)
-- =========================================================================

-- 1. Buat tabel users
CREATE TABLE IF NOT EXISTS users (
    id SERIAL PRIMARY KEY,
    nama VARCHAR(255) NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(20) NOT NULL DEFAULT 'user'
);

-- 2. Masukkan data akun demo jika belum ada
-- Password 'admin123' dan 'user123' terenkripsi aman dengan algoritma BCRYPT (password_hash)
INSERT INTO users (nama, username, password, role)
VALUES 
    ('Administrator', 'admin', '$2y$12$Rx7J5bZpJyoRDzBVpCsvVezxww.JQzBV6ryVPtMxvldnIdE1mgcO6', 'admin'),
    ('Mohammad Daanii', 'user', '$2y$12$PPOr9kl1w7xUOk6tak8uU.EYqXWJbFc7lYne2SNnmKvLTMLrpulv2', 'user')
ON CONFLICT (username) DO NOTHING;
