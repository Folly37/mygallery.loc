<?php
require_once __DIR__ . '/../includes/auth.php';

$user = getCurrentUser();

if (!$user) {
    header("Location: " . BASE_URL . "/?page=login");
    exit;
}
?>
<div class="row">
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-body text-center">
                <img src="<?= BASE_URL ?>/uploads/avatars/<?= $user['avatar'] ?? 'default.jpg' ?>" 
                    class="rounded-circle mb-3" width="150" height="150" alt="Avatar">
                <h4><?= htmlspecialchars($user['username']) ?></h4>
                <p class="text-muted"><?= htmlspecialchars($user['email']) ?></p>

                <form action="<?= BASE_URL ?>/includes/avatar_handler.php" method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="avatar" class="form-label">Change Avatar</label>
                        <input class="form-control" type="file" id="avatar" name="avatar" accept="image/*">
                    </div>
                    <button type="submit" class="btn btn-sm btn-primary">Update</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-user-edit"></i> Edit Profile</h3>
            </div>
            <div class="card-body">
                <?php if (isset($_SESSION['profile_success'])): ?>
                    <div class="alert alert-success">
                        <?= $_SESSION['profile_success'] ?>
                        <?php unset($_SESSION['profile_success']); ?>
                    </div>
                <?php endif; ?>

                <form action="<?= BASE_URL ?>/includes/profile_handler.php" method="POST">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" 
                            value="<?= htmlspecialchars($user['username']) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" 
                            value="<?= htmlspecialchars($user['email']) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="current_password" class="form-label">Current Password (leave blank to keep unchanged)</label>
                        <input type="password" class="form-control" id="current_password" name="current_password">
                    </div>

                    <div class="mb-3">
                        <label for="new_password" class="form-label">New Password</label>
                        <input type="password" class="form-control" id="new_password" name="new_password">
                    </div>

                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Confirm New Password</label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password">
                    </div>

                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </form>
            </div>
        </div>
    </div>
</div>