<footer class="text-white py-4 mt-5 footer">
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <h5><img width="30px" src="favicon.ico" alt=""></i> <?= APP_NAME ?></h5>
                <p class="text-muted">Your personal photo gallery solution.</p>
            </div>
            <div class="col-md-3">
                <h5>Quick Links</h5>
                <ul class="list-unstyled">
                    <li><a href="<?= BASE_URL ?>" class="text-white">Home</a></li>
                    <?php if (isLoggedIn()): ?>
                        <li><a href="<?= BASE_URL ?>/?page=gallery" class="text-white">Gallery</a></li>
                        <li><a href="<?= BASE_URL ?>/?page=upload" class="text-white">Upload</a></li>
                    <?php else: ?>
                        <li><a href="<?= BASE_URL ?>/?page=login" class="text-white">Login</a></li>
                        <li><a href="<?= BASE_URL ?>/?page=register" class="text-white">Register</a></li>
                    <?php endif; ?>
                </ul>
            </div>
            <div class="col-md-3">
                <h5>Contact</h5>
                <ul class="list-unstyled text-muted">
                    <li><i class="fas fa-envelope me-2"></i> follochka11@gmail.com</li>
                    <li><i class="fas fa-phone me-2"></i> +7 (912) 991 11 02</li>
                </ul>
            </div>
        </div>
        <hr class="my-4 bg-light">
        <div class="text-center">
            <small>&copy; <?= date('Y') ?> <?= APP_NAME ?>. All rights reserved.</small>
        </div>
    </div>
</footer>