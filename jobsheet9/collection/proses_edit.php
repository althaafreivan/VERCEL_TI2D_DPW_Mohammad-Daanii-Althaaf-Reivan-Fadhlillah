<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../includes/koneksi.php';

// Pastikan hanya request POST yang dapat mengeksekusi proses edit
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

// Validasi kolom wajib
if (empty($judul) || empty($pengunggah)) {
    $_SESSION['flash_error'] = 'Judul gambar dan nama pengunggah wajib diisi!';
    header('Location: edit.php?id=' . $id);
    exit;
}

// Cek apakah ada file gambar baru yang diunggah
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

    // Konversi gambar baru ke Base64 Data URL (Serverless Safe)
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

    // Salin lokal jika folder uploads lokal ada dan writable
    $folder_uploads = __DIR__ . '/../assets/uploads/';
    if (@is_dir($folder_uploads) || @mkdir($folder_uploads, 0777, true)) {
        $nama_file_lokal = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $nama_file);
        @copy($tmp_file, $folder_uploads . $nama_file_lokal);
    }

    // Update beserta file gambar baru
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
            'pesan' => "Data dan foto gambar '<strong>" . htmlspecialchars($judul) . "</strong>' berhasil diperbarui!"
        ];
        header('Location: catalog.php');
        exit;
    } catch (PDOException $e) {
        $_SESSION['flash_error'] = 'Gagal memperbarui database: ' . $e->getMessage();
        header('Location: edit.php?id=' . $id);
        exit;
    }

} else {
    // Update HANYA teks metadata (file gambar lama tetap dipertahankan)
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
