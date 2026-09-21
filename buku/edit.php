<?php
$page_title = "Edit Buku";
include __DIR__ . '/../includes/header.php';
require __DIR__ . '/../includes/koneksi.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$stmt = $pdo->prepare("SELECT * FROM buku WHERE id = :id");
$stmt->execute(['id' => $id]);
$buku = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$buku) {
    echo "<section><p class='flash flash-error'>Data buku tidak ditemukan.</p><p><a href='list.php'>Kembali ke Daftar Buku</a></p></section>";
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
            <h2>Edit Buku</h2>

            <?php if ($flash): ?>
                <p class="flash flash-<?php echo $flash['type']; ?>"><?php echo $flash['pesan']; ?></p>
            <?php endif; ?>

            <form id="form-edit" method="post" action="proses_edit.php">
                <input type="hidden" name="id" value="<?php echo $buku['id']; ?>">
                <p>
                    <label for="judul">Judul</label><br>
                    <input type="text" id="judul" name="judul" value="<?php echo htmlspecialchars($buku['judul'], ENT_QUOTES); ?>" required>
                </p>
                <p>
                    <label for="pengarang">Pengarang</label><br>
                    <input type="text" id="pengarang" name="pengarang" value="<?php echo htmlspecialchars($buku['pengarang'], ENT_QUOTES); ?>" required>
                </p>
                <p>
                    <label for="tahun">Tahun Terbit</label><br>
                    <input type="number" id="tahun" name="tahun" min="1900" max="2026" value="<?php echo $buku['tahun']; ?>" required>
                </p>
                <p>
                    <label for="isbn">ISBN</label><br>
                    <input type="text" id="isbn" name="isbn" value="<?php echo htmlspecialchars($buku['isbn'] ?? '', ENT_QUOTES); ?>">
                </p>
                <p>
                    <label for="stok">Stok</label><br>
                    <input type="number" id="stok" name="stok" min="0" value="<?php echo $buku['stok']; ?>" required>
                </p>
                <p>
                    <label for="kategori">Kategori</label><br>
                    <select id="kategori" name="kategori">
                        <option value="fiksi" <?php echo strtolower($buku['kategori'] ?? '') === 'fiksi' ? 'selected' : ''; ?>>Fiksi</option>
                        <option value="non-fiksi" <?php echo strtolower($buku['kategori'] ?? '') === 'non-fiksi' ? 'selected' : ''; ?>>Non-Fiksi</option>
                        <option value="sejarah" <?php echo strtolower($buku['kategori'] ?? '') === 'sejarah' ? 'selected' : ''; ?>>Sejarah</option>
                        <option value="referensi" <?php echo strtolower($buku['kategori'] ?? '') === 'referensi' ? 'selected' : ''; ?>>Referensi</option>
                    </select>
                </p>
                <p>
                    <button type="submit">Simpan Perubahan</button>
                    <a href="list.php" style="margin-left: 0.5rem; text-decoration: none;">Batal</a>
                </p>
            </form>
        </section>
<?php include __DIR__ . '/../includes/footer.php'; ?>
