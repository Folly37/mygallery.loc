<div class="row justify-content-center mt-5">
    <div class="col-md-6 col-lg-4">
        <?php if (isset($_SESSION['login_error'])): ?>
            <div class="alert alert-danger">
                <?= $_SESSION['login_error'] ?>
                <?php unset($_SESSION['login_error']); ?>
            </div>
        <?php endif; ?>

        <div class="card shadow">
            <div class="card-body p-4">
                <h3 class="text-center mb-4">Login</h3>
                <form action="<?= BASE_URL ?>/includes/login_handler.php" method="POST">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">Login</button>
                </form>
                <div class="mt-3 text-center">
                    <a href="<?= BASE_URL ?>/?page=register">Don't have an account? Register</a>
                </div>
            </div>
        </div>
    </div>
</div>