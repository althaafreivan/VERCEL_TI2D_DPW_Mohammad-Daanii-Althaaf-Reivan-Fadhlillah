<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Prefix relatif ke root proyek Jobsheet 10
$__jobsheetRoot = dirname(__DIR__);
$__scriptDir = dirname($_SERVER['SCRIPT_FILENAME']);
$__rel = ltrim(str_replace('\\', '/', substr($__scriptDir, strlen($__jobsheetRoot))), '/');
$base = $__rel === '' ? '' : str_repeat('../', substr_count($__rel, '/') + 1);

// Cek status sesi login pengguna
$is_logged_in = isset($_SESSION['user_id']);
$user_name    = $_SESSION['nama'] ?? '';
$user_role    = $_SESSION['role'] ?? 'user';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PixelGallery<?php echo isset($page_title) ? ' | ' . $page_title : ''; ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo $base; ?>assets/css/style.css">
</head>
<body>
    <header>
        <h1><a href="<?php echo $base; ?>index.php" class="home">PixelGallery</a></h1>
        <button type="button" id="nav-toggle-btn" class="nav-toggle-label" aria-label="Menu">&#9776;</button>
        <nav>
            <ul>
                <li><a href="<?php echo $base; ?>index.php">Beranda</a></li>
                <li><a href="<?php echo $base; ?>collection/catalog.php">Galeri</a></li>
                <?php if ($is_logged_in): ?>
                    <li><a href="<?php echo $base; ?>collection/upload-asset.php">+ Upload</a></li>
                    <li style="display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.35rem 0.75rem; background: var(--nm-bg); box-shadow: var(--nm-shadow-pressed); border-radius: var(--radius-sm); font-size: 0.825rem;">
                        <span style="font-weight: 700; color: var(--primary);">👤 <?php echo htmlspecialchars($user_name); ?></span>
                        <span class="cat-pill" style="font-size: 0.65rem; padding: 0.1rem 0.4rem; text-transform: uppercase;"><?php echo htmlspecialchars($user_role); ?></span>
                    </li>
                    <li><a href="<?php echo $base; ?>auth/logout.php" style="color: #ef4444; font-weight: 700;">Logout</a></li>
                <?php else: ?>
                    <li><a href="<?php echo $base; ?>auth/login.php" class="btn-card" style="padding: 0.35rem 0.75rem;">Login</a></li>
                    <li><a href="<?php echo $base; ?>auth/register.php" style="font-weight: 600; color: var(--primary);">Daftar</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>

    <main>
