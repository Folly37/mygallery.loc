<div class="row justify-content-center mt-5">
    <div class="col-md-6 col-lg-4">
        <?php if (isset($_SESSION['register_errors'])): ?>
            <div class="alert alert-danger">
                <?php foreach ($_SESSION['register_errors'] as $error): ?>
                    <p class="mb-1"><?= $error ?></p>
                <?php endforeach; ?>
                <?php unset($_SESSION['register_errors']); ?>
            </div>
        <?php endif; ?>

        <div class="card shadow animate-fade">
            <div class="card-body p-4">
                <h3 class="text-center mb-4">Create Account</h3>
                <form action="<?= BASE_URL ?>/includes/register_handler.php" method="POST">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>

                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Confirm Password</label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-2">Register</button>
                </form>
                <div class="mt-3 text-center">
                    <p>Already have an account? <a href="<?= BASE_URL ?>/?page=login">Sign in</a></p>
                </div>
            </div>
        </div>
    </div>
</div>