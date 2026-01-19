<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Art Gallery</title>
    <link href="https://cdn.jsdelivr.net/npm/bootswatch@5.3.3/dist/lux/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="assets/app.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4 shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-semibold" href="index.php?action=gallery">
                <i class="bi bi-images me-2"></i>Art Gallery
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain" aria-controls="navbarMain" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarMain">
                <div class="navbar-nav ms-auto align-items-lg-center">
                    <a class="nav-link" href="index.php?action=gallery"><i class="bi bi-grid me-1"></i>Gallery</a>
                    <a class="nav-link" href="index.php?action=search"><i class="bi bi-search me-1"></i>Search</a>
                    <?php // Navbar badge is a partial view ?>
                    <?php include __DIR__ . '/partials/saved_badge.php'; ?>
                
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a class="nav-link" href="index.php?action=profile">
                        <?php if (!empty($_SESSION['user_profile_image'])): ?>
                            <img class="nav-avatar me-2" src="images/ProfilesFoto/<?php echo htmlspecialchars($_SESSION['user_profile_image']); ?>" alt="Profile">
                        <?php else: ?>
                            <i class="bi bi-person-circle me-2"></i>
                        <?php endif; ?>
                        <?php echo htmlspecialchars($_SESSION['user_login']); ?>
                    </a>
                    <a class="nav-link" href="index.php?action=upload"><i class="bi bi-cloud-upload me-1"></i>Upload</a>
                    <a class="nav-link" href="index.php?action=logout"><i class="bi bi-box-arrow-right me-1"></i>Logout</a>
                <?php else: ?>
                    <a class="nav-link" href="index.php?action=login"><i class="bi bi-box-arrow-in-right me-1"></i>Login</a>
                    <a class="nav-link" href="index.php?action=register"><i class="bi bi-person-plus me-1"></i>Register</a>
                <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <div class="container">
        <?php if (isset($_SESSION['flash_message'])): ?>
            <?php $type = $_SESSION['flash_type'] ?? 'info'; ?>
            <div class="alert alert-<?php echo htmlspecialchars($type); ?> alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($_SESSION['flash_message']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['flash_message'], $_SESSION['flash_type']); ?>
        <?php endif; ?>
        
        <?php include $content; ?>
    </div>

    <footer class="py-5 mt-5 border-top">
        <div class="container text-center text-muted small">
            Art Gallery • Browse, upload, and save selections.
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>