<?php
// public/index.php
session_start();

// Hata raporlamayı açalım (Geliştirme aşamasında hayat kurtarır)
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../src/controllers.php';

$action = $_GET['action'] ?? 'gallery';

// Basit Routing (Yönlendirme)
switch ($action) {
    case 'upload':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            upload_action();
        } else {
            // View dosyasını değişkenlerle çağırabiliriz
            $content = __DIR__ . '/../views/upload_view.php';
            include __DIR__ . '/../views/layout.php';
        }
        break;

    case 'gallery':
        $photos = gallery_action();
        $content = __DIR__ . '/../views/gallery_view.php';
        include __DIR__ . '/../views/layout.php';
        break;

    case 'login':
        // Basit bir login yönlendirmesi (Henüz logic boş)
        $content = __DIR__ . '/../views/login_view.php';
        include __DIR__ . '/../views/layout.php';
        break;

    default:
        // Bilinmeyen sayfa
        header('Location: index.php?action=gallery');
        exit;
}
?>