<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Guard Clause Autentikasi (Jobsheet 10)
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: catalog.php');
    exit;
}

$id         = filter_var($_POST['id'] ?? 0, FILTER_VALIDATE_INT);
$judul      = trim($_POST['judul'] ?? '');
$pengunggah = trim($_POST['pengunggah'] ?? '');
$kategori   = trim($_POST['kategori'] ?? 'Fotografi');
$tahun      = trim($_POST['tahun'] ?? date('Y'));
$deskripsi  = trim($_POST['deskripsi'] ?? '');

if (!$id) {
    $_SESSION['flash'] = [
        'type'  => 'error',
        'pesan' => 'ID gambar tidak valid.'
    ];
    header('Location: catalog.php');
    exit;
}

// Cek hak akses kepemilikan data (Role-based Authorization)
try {
    $cek = $pdo->prepare("SELECT pengunggah FROM galeri WHERE id = :id");
    $cek->execute([':id' => $id]);
    $foto_lama = $cek->fetch(PDO::FETCH_ASSOC);

    if (!$foto_lama) {
        $_SESSION['flash'] = ['type' => 'error', 'pesan' => 'Data gambar tidak ditemukan.'];
        header('Location: catalog.php');
        exit;
    }

    $is_admin = ($_SESSION['role'] === 'admin');
    $is_owner = (strtolower(trim($foto_lama['pengunggah'])) === strtolower(trim($_SESSION['nama'] ?? '')));

    if (!$is_admin && !$is_owner) {
        $_SESSION['flash'] = [
            'type'  => 'error',
            'pesan' => 'Akses ditolak! Anda tidak memiliki izin untuk mengedit karya ini.'
        ];
        header('Location: catalog.php');
        exit;
    }

    // Jika bukan admin, pastikan nama pengunggah tidak diganti sembarangan
    if (!$is_admin) {
        $pengunggah = $foto_lama['pengunggah'];
    }

} catch (PDOException $e) {
    $_SESSION['flash'] = ['type' => 'error', 'pesan' => 'Kesalahan database: ' . $e->getMessage()];
    header('Location: catalog.php');
    exit;
}

if (empty($judul) || empty($pengunggah)) {
    $_SESSION['flash_error'] = 'Judul gambar dan nama pengunggah wajib diisi!';
    header('Location: edit.php?id=' . $id);
    exit;
}

$ada_gambar_baru = isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK;

if ($ada_gambar_baru) {
    $nama_file = $_FILES['gambar']['name'];
    $tmp_file  = $_FILES['gambar']['tmp_name'];
    $ukuran    = $_FILES['gambar']['size'];

    $ekstensi = strtolower(pathinfo($nama_file, PATHINFO_EXTENSION));
    $ekstensi_boleh = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

    if (!in_array($ekstensi, $ekstensi_boleh)) {
        $_SESSION['flash_error'] = 'Format file tidak didukung! Harap unggah JPG, PNG, WEBP, atau GIF.';
        header('Location: edit.php?id=' . $id);
        exit;
    }

    if ($ukuran > 5 * 1024 * 1024) {
        $_SESSION['flash_error'] = 'Ukuran gambar baru terlalu besar! Maksimal 5MB.';
        header('Location: edit.php?id=' . $id);
        exit;
    }

    $mime_types = [
        'jpg'  => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png'  => 'image/png',
        'webp' => 'image/webp',
        'gif'  => 'image/gif'
    ];
    $tipe_mime = $mime_types[$ekstensi] ?? 'image/jpeg';
    $konten_gambar = file_get_contents($tmp_file);
    $data_simpan = 'data:' . $tipe_mime . ';base64,' . base64_encode($konten_gambar);

    $folder_uploads = __DIR__ . '/../assets/uploads/';
    if (@is_dir($folder_uploads) || @mkdir($folder_uploads, 0777, true)) {
        $nama_file_lokal = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $nama_file);
        @copy($tmp_file, $folder_uploads . $nama_file_lokal);
    }

    try {
        $sql = "UPDATE galeri 
                SET judul = :judul, 
                    pengunggah = :pengunggah, 
                    kategori = :kategori, 
                    file_gambar = :file_gambar, 
                    tahun = :tahun, 
                    deskripsi = :deskripsi 
                WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':judul'       => $judul,
            ':pengunggah'  => $pengunggah,
            ':kategori'    => $kategori,
            ':file_gambar' => $data_simpan,
            ':tahun'       => (int)$tahun,
            ':deskripsi'   => $deskripsi,
            ':id'          => $id
        ]);

        $_SESSION['flash'] = [
            'type'  => 'success',
            'pesan' => "Data dan gambar '<strong>" . htmlspecialchars($judul) . "</strong>' berhasil diperbarui!"
        ];
        header('Location: catalog.php');
        exit;
    } catch (PDOException $e) {
        $_SESSION['flash_error'] = 'Gagal memperbarui database: ' . $e->getMessage();
        header('Location: edit.php?id=' . $id);
        exit;
    }

} else {
    try {
        $sql = "UPDATE galeri 
                SET judul = :judul, 
                    pengunggah = :pengunggah, 
                    kategori = :kategori, 
                    tahun = :tahun, 
                    deskripsi = :deskripsi 
                WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':judul'       => $judul,
            ':pengunggah'  => $pengunggah,
            ':kategori'    => $kategori,
            ':tahun'       => (int)$tahun,
            ':deskripsi'   => $deskripsi,
            ':id'          => $id
        ]);

        $_SESSION['flash'] = [
            'type'  => 'success',
            'pesan' => "Metadata gambar '<strong>" . htmlspecialchars($judul) . "</strong>' berhasil diperbarui!"
        ];
        header('Location: catalog.php');
        exit;
    } catch (PDOException $e) {
        $_SESSION['flash_error'] = 'Gagal memperbarui database: ' . $e->getMessage();
        header('Location: edit.php?id=' . $id);
        exit;
    }
}
