<div class="row justify-content-center">
    <div class="col-md-4">
        <div class="card shadow-sm">
            <div class="card-header">Login</div>
            <div class="card-body">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <div class="alert alert-info mb-0">You are already logged in.</div>
                    <div class="mt-3 d-flex gap-2">
                        <a class="btn btn-primary w-100" href="index.php?action=gallery">Go to Gallery</a>
                        <a class="btn btn-outline-secondary w-100" href="index.php?action=logout">Logout</a>
                    </div>
                <?php else: ?>
                    <form action="index.php?action=login" method="post">
                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" name="login" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Login</button>
                    </form>
                    <hr>
                    <a href="index.php?action=register" class="btn btn-outline-secondary w-100">Don't have an account? Register</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>