<?php
// =========================================================================
// Image Serving Endpoint untuk PixelGallery (Kompatibel Vercel & Supabase)
// =========================================================================
require_once __DIR__ . '/includes/koneksi.php';

$id = filter_var($_GET['id'] ?? 0, FILTER_VALIDATE_INT);

if ($id) {
    try {
        $stmt = $pdo->prepare("SELECT file_gambar FROM galeri WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $foto_data = $stmt->fetchColumn();

        if ($foto_data) {
            // 1. Jika data tersimpan dalam bentuk Data URL Base64 di Supabase
            if (str_starts_with($foto_data, 'data:image/')) {
                $pos = strpos($foto_data, ';base64,');
                if ($pos !== false) {
                    $mime = substr($foto_data, 5, $pos - 5);
                    $biner = base64_decode(substr($foto_data, $pos + 8));

                    header("Content-Type: " . $mime);
                    header("Content-Length: " . strlen($biner));
                    header("Cache-Control: public, max-age=86400");
                    echo $biner;
                    exit;
                }
            }

            // 2. Jika tersimpan sebagai nama file fisik di folder assets/uploads/
            $path_lokal = __DIR__ . '/assets/uploads/' . $foto_data;
            if (file_exists($path_lokal)) {
                $mime = mime_content_type($path_lokal) ?: 'image/jpeg';
                header("Content-Type: " . $mime);
                header("Content-Length: " . filesize($path_lokal));
                header("Cache-Control: public, max-age=86400");
                readfile($path_lokal);
                exit;
            }
        }
    } catch (Exception $e) {
        // Abaikan galat dan lanjut ke 404
    }
}

// Jika gambar tidak ditemukan
http_response_code(404);
header("Content-Type: text/plain");
echo "Gambar tidak ditemukan";
exit;
