<?php
$login = $profile['login'] ?? '';
$profileImage = $profile['profileImage'] ?? null;
$myPhotos = $profile['myPhotos'] ?? [];
?>

<div class="d-flex align-items-center justify-content-between mb-3">
    <div>
        <h2 class="mb-1">Profile</h2>
        <div class="text-muted">Signed in as: <?php echo htmlspecialchars($login); ?></div>
    </div>
    <a class="btn btn-outline-secondary" href="index.php?action=gallery"><i class="bi bi-arrow-left me-1"></i>Back to Gallery</a>
</div>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card shadow-sm">
            <div class="card-body text-center">
                <?php if (!empty($profileImage)): ?>
                    <img class="rounded-circle mb-3" style="width: 96px; height: 96px; object-fit: cover;" src="images/ProfilesFoto/<?php echo htmlspecialchars($profileImage); ?>" alt="Profile picture">
                <?php else: ?>
                    <div class="display-5 text-muted mb-2"><i class="bi bi-person-circle"></i></div>
                <?php endif; ?>

                <div class="fw-semibold"><?php echo htmlspecialchars($login); ?></div>
                <div class="text-muted small">Your uploads are shown below.</div>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="d-flex align-items-center justify-content-between mb-2">
            <h4 class="mb-0">My Uploads</h4>
            <a class="btn btn-primary btn-sm" href="index.php?action=upload"><i class="bi bi-cloud-upload me-1"></i>Upload</a>
        </div>

        <?php if (empty($myPhotos)): ?>
            <div class="alert alert-info">You have not uploaded any photos yet.</div>
        <?php else: ?>
            <div class="row g-3">
                <?php foreach ($myPhotos as $photo): ?>
                    <?php
                    $idStr = isset($photo->_id) ? (string)$photo->_id : '';
                    $urls = function_exists('photo_urls') ? photo_urls(isset($photo->filename) ? $photo->filename : '') : ['full' => '', 'thumb' => ''];
                    $fullHref = $urls['full'];
                    $thumbSrc = $urls['thumb'];
                    ?>
                    <div class="col-6 col-md-4">
                        <div class="card h-100 shadow-sm">
                            <a href="<?php echo htmlspecialchars($fullHref); ?>" target="_blank">
                                <img src="<?php echo htmlspecialchars($thumbSrc); ?>" class="card-img-top" alt="Photo">
                            </a>
                            <div class="card-body">
                                <div class="fw-semibold small mb-2"><?php echo htmlspecialchars($photo->title ?? 'Untitled'); ?></div>
                                <div class="d-flex gap-2">
                                    <?php if ($idStr !== ''): ?>
                                        <form action="index.php?action=delete_photo" method="post" onsubmit="return confirm('Delete this photo?');">
                                            <input type="hidden" name="id" value="<?php echo htmlspecialchars($idStr); ?>">
                                            <button type="submit" class="btn btn-outline-danger btn-sm">
                                                <i class="bi bi-trash me-1"></i>Delete
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
