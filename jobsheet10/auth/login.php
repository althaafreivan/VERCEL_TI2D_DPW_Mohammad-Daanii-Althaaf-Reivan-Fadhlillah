<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Jika sudah login, redirect langsung ke katalog
if (isset($_SESSION['user_id'])) {
    header('Location: ../collection/catalog.php');
    exit;
}

$page_title = 'Masuk (Login)';
include __DIR__ . '/../includes/header.php';

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
?>

<div class="upload-wrapper" style="max-width: 520px; margin: 2rem auto;">
    <div class="page-topbar">
        <a href="<?php echo $base; ?>collection/catalog.php" class="back-link">&larr; Kembali ke Galeri</a>
        <span class="section-tag" style="margin: 0;">Jobsheet 10 &bull; Autentikasi</span>
    </div>

    <div class="form-container" style="max-width: 100%;">
        <div class="form-header text-center">
            <span class="section-tag">PixelGallery Auth</span>
            <h2 class="form-title">Masuk ke Akun Anda</h2>
            <p class="form-subtitle">Login untuk mengunggah, memperbarui, dan mengelola galeri foto.</p>
        </div>

        <?php if ($flash): ?>
            <div class="nm-alert nm-alert-<?php echo $flash['type']; ?>">
                <span class="alert-icon"><?php echo $flash['type'] === 'success' ? '✓' : '⚠️'; ?></span>
                <div class="alert-text"><?php echo $flash['pesan']; ?></div>
            </div>
        <?php endif; ?>

        <form action="proses_login.php" method="POST" class="nm-form-card">
            <div class="form-group">
                <label for="username">Username <span class="required-mark">*</span></label>
                <input type="text" name="username" id="username" class="nm-input" 
                       placeholder="Masukkan username" required autofocus>
            </div>

            <div class="form-group">
                <label for="password">Kata Sandi (Password) <span class="required-mark">*</span></label>
                <input type="password" name="password" id="password" class="nm-input" 
                       placeholder="Masukkan kata sandi" required>
            </div>

            <div class="form-actions" style="margin-top: 1.5rem;">
                <button type="submit" class="btn btn-primary btn-submit" style="width: 100%;">
                    <span>Masuk Sekarang &rarr;</span>
                </button>
            </div>

            <div class="text-center" style="margin-top: 1.5rem; font-size: 0.875rem;">
                Belum memiliki akun? <a href="register.php" style="font-weight: 700;">Daftar Akun Baru &rarr;</a>
            </div>

            <div style="margin-top: 1.75rem; padding: 1rem; border-radius: var(--radius-sm); background: var(--nm-bg); box-shadow: var(--nm-shadow-pressed); font-size: 0.8rem; color: var(--text-muted);">
                <strong style="color: var(--text-title); display: block; margin-bottom: 0.35rem;">💡 Akun Demo Praktikum:</strong>
                <ul style="margin-left: 1.25rem; line-height: 1.5;">
                    <li><strong>Admin:</strong> username <code>admin</code> / password <code>admin123</code></li>
                    <li><strong>User:</strong> username <code>user</code> / password <code>user123</code></li>
                </ul>
            </div>
        </form>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
