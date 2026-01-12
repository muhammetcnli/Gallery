<?php
$items = $saved['items'] ?? [];
$photos = $saved['photos'] ?? [];
$totalCount = $saved['totalCount'] ?? 0;
?>

<div class="d-flex align-items-center justify-content-between mb-3">
    <div>
        <h2 class="mb-1">Saved Photos</h2>
        <div class="text-muted">Total saved items: <?php echo (int)$totalCount; ?></div>
    </div>
    <a class="btn btn-outline-secondary" href="index.php?action=gallery"><i class="bi bi-arrow-left me-1"></i>Back to Gallery</a>
</div>

<?php if (empty($photos)): ?>
    <div class="alert alert-warning">You have not saved any photos yet.</div>
<?php else: ?>
    <form action="index.php?action=remove_saved" method="post">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <div class="text-muted small">Select photos to remove from saved.</div>
            <button type="submit" class="btn btn-outline-danger">
                <i class="bi bi-trash me-1"></i>Remove selected from saved
            </button>
        </div>

        <div class="row g-4">
            <?php foreach ($photos as $photo): ?>
                <?php
                $idStr = isset($photo->_id) ? (string)$photo->_id : '';
                $qty = isset($items[$idStr]) ? (int)$items[$idStr] : 1;
                if ($qty <= 0) { $qty = 1; }

                $urls = function_exists('photo_urls') ? photo_urls(isset($photo->filename) ? $photo->filename : '') : ['full' => '', 'thumb' => ''];
                $fullHref = $urls['full'];
                $thumbSrc = $urls['thumb'];
                ?>
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="card h-100 shadow-sm">
                        <a href="<?php echo htmlspecialchars($fullHref); ?>" target="_blank">
                            <img src="<?php echo htmlspecialchars($thumbSrc); ?>" class="card-img-top" alt="Photo">
                        </a>
                        <div class="card-body">
                            <h5 class="card-title mb-1"><?php echo htmlspecialchars($photo->title); ?></h5>
                            <div class="text-muted small">
                                <div>Author: <?php echo htmlspecialchars($photo->author); ?></div>
                                <div>Quantity: <?php echo (int)$qty; ?></div>
                            </div>

                            <?php if ($idStr !== ''): ?>
                                <div class="mt-3 form-check">
                                    <input class="form-check-input" type="checkbox" name="selected[]" value="<?php echo htmlspecialchars($idStr); ?>" id="rm_<?php echo htmlspecialchars($idStr); ?>">
                                    <label class="form-check-label" for="rm_<?php echo htmlspecialchars($idStr); ?>">Remove</label>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </form>
<?php endif; ?>
