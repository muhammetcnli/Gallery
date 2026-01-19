<?php
// use both require_once (core dependencies) and include (views/layout)
session_start();

// Enable error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../src/controllers.php';

$action = $_GET['action'] ?? 'gallery';

// Simple routing
switch ($action) {
    case 'upload':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            upload_action();
        }
        $content = __DIR__ . '/../views/upload_view.php';
        include __DIR__ . '/../views/layout.php';
        break;

    case 'gallery':
        $page = (int)($_GET['page'] ?? 1);
        $gallery = gallery_action($page, 12);
        $photos = $gallery['photos'];
        $pagination = $gallery;
        $content = __DIR__ . '/../views/gallery_view.php';
        include __DIR__ . '/../views/layout.php';
        break;

    case 'save_selected':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            save_selected_action();
        }
        header('Location: index.php?action=gallery');
        exit;

    case 'saved':
        $saved = saved_action();
        $content = __DIR__ . '/../views/saved_view.php';
        include __DIR__ . '/../views/layout.php';
        break;

    case 'delete_photo':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            delete_photo_action();
        }
        header('Location: index.php?action=gallery');
        exit;

    case 'profile':
        $profile = profile_action();
        $content = __DIR__ . '/../views/profile_view.php';
        include __DIR__ . '/../views/layout.php';
        break;

    case 'remove_saved':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            remove_saved_action();
        }
        header('Location: index.php?action=saved');
        exit;

    case 'search':
        search_action();
        $content = __DIR__ . '/../views/search_view.php';
        include __DIR__ . '/../views/layout.php';
        break;

    case 'search_ajax':
        search_ajax_action();
        exit;

    case 'login':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            login_action();
        }
        $content = __DIR__ . '/../views/login_view.php';
        include __DIR__ . '/../views/layout.php';
        break;

    case 'register':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            register_action();
        }
        $content = __DIR__ . '/../views/register_view.php';
        include __DIR__ . '/../views/layout.php';
        break;

    case 'logout':
        logout_action();
        break;
    default:
        // Unknown route
        header('Location: index.php?action=gallery');
        exit;
}
