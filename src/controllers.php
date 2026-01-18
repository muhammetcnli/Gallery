<?php

// Business/utility functions (image processing, filtering, helpers)
require_once __DIR__ . '/services.php';

// Simple flash message + redirect helper.
function flash_and_redirect($message, $location, $type = 'info') {
    $_SESSION['flash_message'] = $message;
    $_SESSION['flash_type'] = $type;
    header("Location: $location");
    exit;
}

// Logged-in user id from session (or null).
function current_user_id() {
    return isset($_SESSION['user_id']) ? (string)$_SESSION['user_id'] : null;
}

// Logged-in username/login from session (or null).
function current_user_login() {
    return isset($_SESSION['user_login']) ? (string)$_SESSION['user_login'] : null;
}

// Get saved cart items from session.
function saved_items() {
    $items = $_SESSION['saved_items'] ?? [];
    return is_array($items) ? $items : [];
}

// Total quantity across all saved items.
function saved_total_count() {
    $total = 0;
    foreach (saved_items() as $qty) {
        $q = (int)$qty;
        if ($q > 0) {
            $total += $q;
        }
    }
    return $total;
}

// (parse_positive_int, photo_urls, oid_from_string, is_jpeg_type, gallery_filter_for_user)
// are implemented in src/services.php

function gallery_action($page = 1, $perPage = 12) {
    // Returns paginated photos for the gallery.
    $userId = current_user_id();
    $filter = gallery_filter_for_user($userId);

    $total = countDocuments('photos', $filter);
    $totalPages = max(1, (int)ceil($total / $perPage));
    $page = min(max(1, $page), $totalPages);
    $skip = ($page - 1) * $perPage;

    $photos = findAll('photos', $filter, [
        'sort' => ['created_at' => -1],
        'skip' => $skip,
        'limit' => $perPage,
    ]);

    return [
        'photos' => $photos,
        'page' => $page,
        'perPage' => $perPage,
        'total' => $total,
        'totalPages' => $totalPages,
    ];
}

// Handles photo upload + validation, then stores metadata in MongoDB
function upload_action() {
    $targetDir = __DIR__ . '/../public/images/';
    $thumbDir = __DIR__ . '/../public/images/thumbnails/';
    $watermarkDir = __DIR__ . '/../public/images/watermarked/';
    
    // Create directories if they don't exist
    if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);
    if (!is_dir($thumbDir)) mkdir($thumbDir, 0777, true);
    if (!is_dir($watermarkDir)) mkdir($watermarkDir, 0777, true);

    if (!isset($_FILES['image'])) {
        flash_and_redirect('No file selected.', 'index.php?action=upload', 'danger');
    }

    // Check for upload errors
    $uploadErr = (int)($_FILES['image']['error'] ?? UPLOAD_ERR_NO_FILE);
    if ($uploadErr !== UPLOAD_ERR_OK) {
        if ($uploadErr === UPLOAD_ERR_INI_SIZE || $uploadErr === UPLOAD_ERR_FORM_SIZE) {
            flash_and_redirect('File is too large. Maximum allowed size is 1MB.', 'index.php?action=upload', 'danger');
        }
        if ($uploadErr === UPLOAD_ERR_NO_FILE) {
            flash_and_redirect('No file selected.', 'index.php?action=upload', 'danger');
        }
        flash_and_redirect('Upload error occurred. Please try again.', 'index.php?action=upload', 'danger');
    }

    $originalName = $_FILES['image']['name'] ?? '';
    $fileType = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

    // Check for file type
    $isTypeValid = in_array($fileType, ['jpg', 'jpeg', 'png'], true);

    // Check for file size (1MB)
    $isSizeValid = (($_FILES['image']['size'] ?? 0) <= 1048576);

    if (!$isTypeValid && !$isSizeValid) {
        flash_and_redirect('Invalid file type and file size exceeds 1MB. Only JPG/PNG up to 1MB is allowed.', 'index.php?action=upload', 'danger');
    }
    if (!$isTypeValid) {
        flash_and_redirect('Only JPG and PNG files are allowed.', 'index.php?action=upload', 'danger');
    }
    if (!$isSizeValid) {
        flash_and_redirect('File size must be 1MB or less.', 'index.php?action=upload', 'danger');
    }

    $fileName = uniqid('img_', true) . '.' . $fileType;
    $targetFile = $targetDir . $fileName;

    if (!move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
        flash_and_redirect('Upload failed while saving the file.', 'index.php?action=upload', 'danger');
    }

    // Generate thumbnail (200x125).
    create_thumbnail($targetFile, $thumbDir . $fileName, $fileType);

    // Generate watermarked copy used for full-size preview.
    create_watermarked($targetFile, $watermarkDir . $fileName, $fileType, 'Photo Gallery');

    $title = trim($_POST['title'] ?? '');
    if ($title === '') {
        $title = 'Untitled';
    }

    $userLogin = current_user_login();
    $author = trim($_POST['author'] ?? '');
    if ($userLogin !== null) {
        $author = $userLogin;
    }
    if ($author === '') {
        $author = 'Anonymous';
    }

    $visibility = 'public';
    if ($userLogin !== null) {
        $posted = ($_POST['visibility'] ?? 'public');
        $visibility = ($posted === 'private') ? 'private' : 'public';
    }

    $document = [
        'filename' => $fileName,
        'title' => $title,
        'author' => $author,
        'created_at' => new MongoDB\BSON\UTCDateTime(),
        'visibility' => $visibility,
        'owner_user_id' => current_user_id(),
    ];
    insertOne('photos', $document);

    flash_and_redirect('Photo uploaded successfully.', 'index.php?action=gallery', 'success');
}

function register_action() {
    // Creates a new user account (hash password + save profile photo thumbnail).
    $login = trim($_POST['login'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $repeat_password = $_POST['repeat_password'] ?? '';

        if ($login === '' || $email === '') {
            flash_and_redirect('Login and email are required.', 'index.php?action=register', 'danger');
        }

        if ($password !== $repeat_password) {
            flash_and_redirect('Passwords do not match.', 'index.php?action=register', 'danger');
        }

        $existing = findAll('users', ['login' => $login]);
        if (!empty($existing->toArray())) {
            flash_and_redirect('That username is already taken.', 'index.php?action=register', 'warning');
        }

        // Profile picture is required
        if (!isset($_FILES['profile_pic']) || ($_FILES['profile_pic']['error'] ?? 1) !== 0) {
            flash_and_redirect('Profile picture is required.', 'index.php?action=register', 'danger');
        }

        $uploadDir = __DIR__ . '/../public/images/ProfilesFoto/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $tmpName = $_FILES['profile_pic']['tmp_name'];
        $extension = strtolower(pathinfo($_FILES['profile_pic']['name'], PATHINFO_EXTENSION));
        if ($extension !== 'jpg' && $extension !== 'jpeg' && $extension !== 'png') {
            flash_and_redirect('Profile picture must be JPG or PNG.', 'index.php?action=register', 'danger');
        }

        $profileThumbName = uniqid('profile_', true) . '.' . $extension;
        create_thumbnail($tmpName, $uploadDir . $profileThumbName, $extension);

        $user = [
            'login' => $login,
            'email' => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'profile_image' => $profileThumbName
        ];
        
        insertOne('users', $user);

    flash_and_redirect('Registration successful. You can now log in.', 'index.php?action=login', 'success');
}

function login_action() {
    // Authenticates user and stores identity in session.
    if (isset($_SESSION['user_id'])) {
        flash_and_redirect('You are already logged in.', 'index.php?action=gallery', 'info');
    }

    $login = trim($_POST['login'] ?? '');
    $password = $_POST['password'] ?? '';

        if ($login === '' || $password === '') {
            flash_and_redirect('Login and password are required.', 'index.php?action=login', 'danger');
        }

        $cursor = findAll('users', ['login' => $login]);
        $users = $cursor->toArray();

        if (count($users) > 0) {
            $user = $users[0];
            if (password_verify($password, $user->password)) {
                session_regenerate_id(true);
                $_SESSION['user_id'] = (string)$user->_id;
                $_SESSION['user_login'] = $user->login;
                if (isset($user->profile_image)) {
                    $_SESSION['user_profile_image'] = $user->profile_image;
                }
                flash_and_redirect('Login successful.', 'index.php?action=gallery', 'success');
            }
        }

    flash_and_redirect('Invalid username or password.', 'index.php?action=login', 'danger');
}

function logout_action() {
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }
    session_destroy();
    session_start();
    flash_and_redirect('You have been logged out.', 'index.php?action=login', 'info');
}

function save_selected_action() {
    // Saves selected photos into session (cart-like) with quantity.
    $selected = $_POST['selected'] ?? [];
    $qtyMap = $_POST['qty'] ?? [];

    if (!is_array($selected)) {
        $selected = [];
    }
    if (!is_array($qtyMap)) {
        $qtyMap = [];
    }

    $items = saved_items();
    foreach ($selected as $id) {
        $id = (string)$id;
        $qty = parse_positive_int($qtyMap[$id] ?? 1, 1);
        $items[$id] = $qty;
    }

    $_SESSION['saved_items'] = $items;
    flash_and_redirect('Saved selection updated.', 'index.php?action=gallery', 'success');
}

function remove_saved_action() {
    $selected = $_POST['selected'] ?? [];
    if (!is_array($selected)) {
        $selected = [];
    }

    $items = saved_items();
    foreach ($selected as $id) {
        $id = (string)$id;
        unset($items[$id]);
    }

    $_SESSION['saved_items'] = $items;
    flash_and_redirect('Removed selected items from saved.', 'index.php?action=saved', 'info');
}

function saved_action() {
    // Loads saved (cart) photos and keeps session consistent.
    $items = saved_items();
    $ids = array_keys($items);

    $objectIds = [];
    foreach ($ids as $id) {
        $oid = oid_from_string((string)$id);
        if ($oid !== null) {
            $objectIds[] = $oid;
        }
    }

    $photos = [];
    if (!empty($objectIds)) {
        $filter = gallery_filter_for_user(current_user_id());
        $filter['_id'] = ['$in' => $objectIds];
        $cursor = findAll('photos', $filter, ['sort' => ['created_at' => -1]]);
        $photos = $cursor->toArray();
    }

    // Cleanup: remove stale saved IDs that no longer exist / not visible
    if (!empty($items)) {
        $found = [];
        foreach ($photos as $p) {
            if (isset($p->_id)) {
                $found[(string)$p->_id] = true;
            }
        }
        $changed = false;
        foreach (array_keys($items) as $id) {
            if (!isset($found[$id])) {
                unset($items[$id]);
                $changed = true;
            }
        }
        if ($changed) {
            $_SESSION['saved_items'] = $items;
        }
    }

    return [
        'items' => $items,
        'photos' => $photos,
        'totalCount' => saved_total_count(),
    ];
}

function delete_photo_action() {
    $userId = current_user_id();
    if ($userId === null) {
        flash_and_redirect('Please log in to delete photos.', 'index.php?action=login', 'warning');
    }

    $id = isset($_POST['id']) ? (string)$_POST['id'] : '';
    $oid = oid_from_string($id);
    if ($oid === null) {
        flash_and_redirect('Invalid photo id.', 'index.php?action=gallery', 'danger');
    }

    $cursor = findAll('photos', ['_id' => $oid], ['limit' => 1]);
    $arr = $cursor->toArray();
    if (count($arr) === 0) {
        flash_and_redirect('Photo not found.', 'index.php?action=gallery', 'warning');
    }

    $photo = $arr[0];
    $owner = isset($photo->owner_user_id) ? (string)$photo->owner_user_id : null;
    if ($owner === null || $owner !== $userId) {
        flash_and_redirect('You can delete only your own uploads.', 'index.php?action=gallery', 'danger');
    }

    $filename = isset($photo->filename) ? basename((string)$photo->filename) : '';
    if ($filename === '') {
        flash_and_redirect('Invalid photo filename.', 'index.php?action=gallery', 'danger');
    }

    deleteOne('photos', ['_id' => $oid, 'owner_user_id' => $userId]);

    $paths = [
        __DIR__ . '/../public/images/' . $filename,
        __DIR__ . '/../public/images/thumbnails/' . $filename,
        __DIR__ . '/../public/images/watermarked/' . $filename,
    ];
    foreach ($paths as $p) {
        if (is_file($p)) {
            @unlink($p);
        }
    }

    // Keep saved-items consistent
    $items = saved_items();
    if (isset($items[$id])) {
        unset($items[$id]);
        $_SESSION['saved_items'] = $items;
    }

    flash_and_redirect('Photo deleted.', 'index.php?action=gallery', 'success');
}

function profile_action() {
    $userId = current_user_id();
    if ($userId === null) {
        flash_and_redirect('Please log in to view your profile.', 'index.php?action=login', 'warning');
    }

    $login = current_user_login();
    $profileImage = isset($_SESSION['user_profile_image']) ? (string)$_SESSION['user_profile_image'] : null;

    $cursor = findAll('photos', ['owner_user_id' => $userId], ['sort' => ['created_at' => -1], 'limit' => 100]);
    $myPhotos = $cursor->toArray();

    return [
        'login' => $login,
        'profileImage' => $profileImage,
        'myPhotos' => $myPhotos,
    ];
}

function search_action() {
    // view-only
}

function search_ajax_action() {
    // AJAX endpoint: returns an HTML fragment of matching thumbnails.
    header('Content-Type: text/html; charset=utf-8');

    $q = trim($_GET['q'] ?? '');
    $photos = [];

    if ($q !== '') {
        $userId = current_user_id();
        $filter = gallery_filter_for_user($userId);
        $filter['title'] = new MongoDB\BSON\Regex(preg_quote($q), 'i');
        $cursor = findAll('photos', $filter, ['sort' => ['created_at' => -1], 'limit' => 24]);
        $photos = $cursor->toArray();
    }

    include __DIR__ . '/../views/partials/search_results.php';
}