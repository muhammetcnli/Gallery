<div class="d-flex align-items-center justify-content-between mb-3">
    <div>
        <h2 class="mb-1">Art Gallery</h2>
        <div class="text-muted">Browse uploaded photos, open full images, and save selections for later.</div>
        <div class="text-muted small">Select photos and remember them.</div>
    </div>
    <a class="btn btn-primary" href="index.php?action=upload"><i class="bi bi-cloud-upload me-1"></i>Upload</a>
</div>

<?php $saved = function_exists('saved_items') ? saved_items() : []; ?>

<form action="index.php?action=save_selected" method="post">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div class="text-muted small">Tip: you can set a quantity before remembering.</div>
        <button type="submit" class="btn btn-outline-primary">
            <i class="bi bi-bookmark-check me-1"></i>Remember selected
        </button>
    </div>

    <div class="row g-4">
    <?php 
    $count = 0;
    foreach ($photos as $photo): 
        $count++;
        $idStr = isset($photo->_id) ? (string)$photo->_id : '';
        $checked = ($idStr !== '' && isset($saved[$idStr]));
        $qty = $checked ? (int)$saved[$idStr] : 1;
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
                    <div class="d-flex align-items-start justify-content-between gap-2">
                        <h5 class="card-title mb-1"><?php echo htmlspecialchars($photo->title); ?></h5>
                        <?php if (isset($photo->visibility) && $photo->visibility === 'private'): ?>
                            <span class="badge bg-secondary">Private</span>
                        <?php endif; ?>
                    </div>
                    <div class="text-muted small">
                        <div>Author: <?php echo htmlspecialchars($photo->author); ?></div>
                        <?php if (isset($photo->created_at) && $photo->created_at instanceof MongoDB\BSON\UTCDateTime): ?>
                            <div>Created: <?php echo htmlspecialchars($photo->created_at->toDateTime()->format('Y-m-d H:i')); ?></div>
                        <?php endif; ?>
                    </div>

                    <?php if ($idStr !== ''): ?>
                        <div class="mt-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="selected[]" value="<?php echo htmlspecialchars($idStr); ?>" id="sel_<?php echo htmlspecialchars($idStr); ?>" <?php echo $checked ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="sel_<?php echo htmlspecialchars($idStr); ?>">Save</label>
                            </div>
                            <div class="mt-2">
                                <label class="form-label small mb-1" for="qty_<?php echo htmlspecialchars($idStr); ?>">Quantity</label>
                                <input class="form-control form-control-sm" type="number" min="1" name="qty[<?php echo htmlspecialchars($idStr); ?>]" id="qty_<?php echo htmlspecialchars($idStr); ?>" value="<?php echo (int)$qty; ?>">
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>

    <?php if ($count == 0): ?>
        <div class="col-12">
            <div class="alert alert-warning">No photos uploaded yet.</div>
        </div>
    <?php endif; ?>
    </div>
</form>

<?php if (isset($pagination) && !empty($pagination['totalPages']) && $pagination['totalPages'] > 1): ?>
    <nav class="mt-4" aria-label="Gallery pagination">
        <ul class="pagination justify-content-center">
            <?php $p = (int)$pagination['page']; $tp = (int)$pagination['totalPages']; ?>
            <li class="page-item <?php echo ($p <= 1) ? 'disabled' : ''; ?>">
                <a class="page-link" href="index.php?action=gallery&page=<?php echo max(1, $p - 1); ?>">Previous</a>
            </li>
            <?php for ($i = 1; $i <= $tp; $i++): ?>
                <li class="page-item <?php echo ($i === $p) ? 'active' : ''; ?>">
                    <a class="page-link" href="index.php?action=gallery&page=<?php echo $i; ?>"><?php echo $i; ?></a>
                </li>
            <?php endfor; ?>
            <li class="page-item <?php echo ($p >= $tp) ? 'disabled' : ''; ?>">
                <a class="page-link" href="index.php?action=gallery&page=<?php echo min($tp, $p + 1); ?>">Next</a>
            </li>
        </ul>
    </nav>
<?php endif; ?>