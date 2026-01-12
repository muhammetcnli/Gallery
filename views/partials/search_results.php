<?php
// Expects: $photos (array)
?>

<?php if (empty($photos)): ?>
    <div class="alert alert-info">No matches.</div>
<?php else: ?>
    <div class="row g-4">
        <?php foreach ($photos as $photo): ?>
            <?php
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
                        <h6 class="card-title mb-1"><?php echo htmlspecialchars($photo->title); ?></h6>
                        <div class="text-muted small">Author: <?php echo htmlspecialchars($photo->author); ?></div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
