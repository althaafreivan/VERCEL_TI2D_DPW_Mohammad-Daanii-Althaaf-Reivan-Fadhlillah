<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['user_id'])) {
    header('Location: ../collection/catalog.php');
    exit;
}

$page_title = 'Daftar Akun Baru (Register)';
include __DIR__ . '/../includes/header.php';

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
?>

<div class="upload-wrapper" style="max-width: 580px; margin: 2rem auto;">
    <div class="page-topbar">
        <a href="login.php" class="back-link">&larr; Kembali ke Login</a>
        <span class="section-tag" style="margin: 0;">Jobsheet 10 &bull; Registrasi</span>
    </div>

    <div class="form-container" style="max-width: 100%;">
        <div class="form-header text-center">
            <span class="section-tag">PixelGallery Auth</span>
            <h2 class="form-title">Registrasi Akun Baru</h2>
            <p class="form-subtitle">Buat akun untuk bergabung ke komunitas PixelGallery dan mengelola karya Anda.</p>
        </div>

        <?php if ($flash): ?>
            <div class="nm-alert nm-alert-<?php echo $flash['type']; ?>">
                <span class="alert-icon"><?php echo $flash['type'] === 'success' ? '✓' : '⚠️'; ?></span>
                <div class="alert-text"><?php echo $flash['pesan']; ?></div>
            </div>
        <?php endif; ?>

        <form action="proses_register.php" method="POST" class="nm-form-card">
            <div class="form-group">
                <label for="nama">Nama Lengkap <span class="required-mark">*</span></label>
                <input type="text" name="nama" id="nama" class="nm-input" 
                       placeholder="Misal: Daanii Althaaf" required autofocus>
                <span class="field-hint">Nama asli atau nama pena yang akan ditampilkan pada foto.</span>
            </div>

            <div class="form-grid-2">
                <div class="form-group">
                    <label for="username">Username <span class="required-mark">*</span></label>
                    <input type="text" name="username" id="username" class="nm-input" 
                           placeholder="Misal: daanii" required>
                    <span class="field-hint">Hanya huruf, angka, dan garis bawah (_).</span>
                </div>

                <div class="form-group">
                    <label for="role">Pilihan Peran (Role) <span class="required-mark">*</span></label>
                    <select name="role" id="role" class="nm-select" required>
                        <option value="user" selected>Pengguna Biasa (User)</option>
                        <option value="admin">Administrator (Admin)</option>
                    </select>
                    <span class="field-hint">Admin dapat mengelola seluruh gambar.</span>
                </div>
            </div>

            <div class="form-grid-2">
                <div class="form-group">
                    <label for="password">Kata Sandi <span class="required-mark">*</span></label>
                    <input type="password" name="password" id="password" class="nm-input" 
                           placeholder="Minimal 6 karakter" required minlength="6">
                </div>

                <div class="form-group">
                    <label for="konfirmasi_password">Ulangi Kata Sandi <span class="required-mark">*</span></label>
                    <input type="password" name="konfirmasi_password" id="konfirmasi_password" class="nm-input" 
                           placeholder="Ketik ulang kata sandi" required minlength="6">
                </div>
            </div>

            <div class="form-actions" style="margin-top: 1.75rem;">
                <button type="submit" class="btn btn-primary btn-submit" style="width: 100%;">
                    <span>Daftar Akun Baru Sekarang</span>
                </button>
            </div>

            <div class="text-center" style="margin-top: 1.5rem; font-size: 0.875rem;">
                Sudah memiliki akun? <a href="login.php" style="font-weight: 700;">Masuk di sini &rarr;</a>
            </div>
        </form>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
