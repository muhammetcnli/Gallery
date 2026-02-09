<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header">Upload Photo</div>
            <div class="card-body">
                <form action="index.php?action=upload" method="post" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label class="form-label">Title</label>
                        <input type="text" name="title" class="form-control" required>
                    </div>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <div class="mb-3">
                            <label class="form-label">Author</label>
                            <input type="text" name="author" class="form-control" value="<?php echo htmlspecialchars($_SESSION['user_login']); ?>" readonly>
                            <div class="form-text">Author is set from your account.</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Visibility</label>
                            <div class="d-flex gap-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="visibility" id="visPublic" value="public" checked>
                                    <label class="form-check-label" for="visPublic">Public</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="visibility" id="visPrivate" value="private">
                                    <label class="form-check-label" for="visPrivate">Private</label>
                                </div>
                            </div>
                            <div class="form-text">Private photos are visible only to you.</div>
                        </div>
                    <?php else: ?>
                        <div class="mb-3">
                            <label class="form-label">Author</label>
                            <input type="text" name="author" class="form-control" placeholder="Your name" required>
                            <div class="form-text">Anonymous uploads are treated as public.</div>
                        </div>
                    <?php endif; ?>
                    <div class="mb-3">
                        <label class="form-label">Photo (Max 1MB, JPG/PNG)</label>
                        <input type="file" name="image" class="form-control" required>
                        <div class="form-text">A 200×125 thumbnail will be generated automatically.</div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Upload</button>
                </form>
            </div>
        </div>
    </div>
</div>