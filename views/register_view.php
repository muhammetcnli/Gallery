<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header">Register</div>
            <div class="card-body">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <div class="alert alert-info mb-0">You are already logged in.</div>
                    <div class="mt-3">
                        <a class="btn btn-primary w-100" href="index.php?action=gallery">Go to Gallery</a>
                    </div>
                <?php else: ?>
                    <form action="index.php?action=register" method="post" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label class="form-label">Email Address</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Username (Login)</label>
                            <input type="text" name="login" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Repeat Password</label>
                            <input type="password" name="repeat_password" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Profile Picture (Required)</label>
                            <input type="file" name="profile_pic" class="form-control" required>
                            <div class="form-text">JPG/PNG only.</div>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Create Account</button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>