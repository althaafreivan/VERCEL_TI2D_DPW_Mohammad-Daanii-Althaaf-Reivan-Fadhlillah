<?php
$page_title = "Edit Anggota";
include __DIR__ . '/../includes/header.php';
require __DIR__ . '/../includes/koneksi.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$stmt = $pdo->prepare("SELECT * FROM anggota WHERE id = :id");
$stmt->execute(['id' => $id]);
$anggota = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$anggota) {
    echo "<section><p class='flash flash-error'>Data anggota tidak ditemukan.</p><p><a href='list.php'>Kembali ke Daftar Anggota</a></p></section>";
    include __DIR__ . '/../includes/footer.php';
    exit;
}

$flash = $_SESSION['flash'] ?? null;
if (!$flash && isset($_COOKIE['flash_pesan'])) {
    $flash = [
        'type' => $_COOKIE['flash_type'] ?? 'info',
        'pesan' => $_COOKIE['flash_pesan'],
    ];
    setcookie('flash_type', '', time() - 3600, '/');
    setcookie('flash_pesan', '', time() - 3600, '/');
}
unset($_SESSION['flash']);
?>
        <section>
            <h2>Edit Anggota</h2>

            <?php if ($flash): ?>
                <p class="flash flash-<?php echo $flash['type']; ?>"><?php echo $flash['pesan']; ?></p>
            <?php endif; ?>

            <form id="form-edit" method="post" action="proses_edit.php">
                <input type="hidden" name="id" value="<?php echo $anggota['id']; ?>">
                <p>
                    <label for="nama">Nama</label><br>
                    <input type="text" id="nama" name="nama" value="<?php echo htmlspecialchars($anggota['nama'], ENT_QUOTES); ?>" required>
                </p>
                <p>
                    <label for="no_anggota">No. Anggota</label><br>
                    <input type="text" id="no_anggota" name="no_anggota" value="<?php echo htmlspecialchars($anggota['no_anggota'], ENT_QUOTES); ?>" required>
                </p>
                <p>
                    <label for="alamat">Alamat</label><br>
                    <input type="text" id="alamat" name="alamat" value="<?php echo htmlspecialchars($anggota['alamat'] ?? '', ENT_QUOTES); ?>">
                </p>
                <p>
                    <label for="no_hp">No. HP</label><br>
                    <input type="text" id="no_hp" name="no_hp" value="<?php echo htmlspecialchars($anggota['no_hp'] ?? '', ENT_QUOTES); ?>">
                </p>
                <p>
                    <button type="submit">Simpan Perubahan</button>
                    <a href="list.php" style="margin-left: 0.5rem; text-decoration: none;">Batal</a>
                </p>
            </form>
        </section>
<?php include __DIR__ . '/../includes/footer.php'; ?>
