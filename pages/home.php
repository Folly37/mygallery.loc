<div class="home-page">
    <section class="hero-section text-center py-5">
        <div class="container">
            <h1 class="display-3 fw-bold mb-4">Welcome to <span class="text-accent"><?= APP_NAME ?></span></h1>
            <p class="lead mb-5">Your premium platform for storing and sharing beautiful moments</p>
            
            <div class="cta-buttons">
                <?php if (isLoggedIn()): ?>
                    <a href="<?= BASE_URL ?>/?page=gallery" class="btn btn-primary btn-lg mx-2 ">
                        <i class="fas fa-images me-2"></i>View Gallery
                    </a>
                    <a href="<?= BASE_URL ?>/?page=upload" class="btn btn-outline-light btn-lg mx-2">
                        <i class="fas fa-cloud-upload-alt me-2"></i>Upload Photos
                    </a>
                <?php else: ?>
                    <a href="<?= BASE_URL ?>/?page=register" class="btn btn-primary btn-lg mx-2">
                        <i class="fas fa-user-plus me-2"></i>Join Now
                    </a>
                    <a href="<?= BASE_URL ?>/?page=login" class="btn btn-outline-light btn-lg mx-2">
                        <i class="fas fa-sign-in-alt me-2"></i>Sign In
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </section>


    <section class="mt-5 features-section py-5 bg-dark">
        <div class="container">
            <h2 class="text-center mb-5">Why Choose <?= APP_NAME ?>?</h2>
            
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="feature-card card h-100 border-0">
                        <div class="card-body text-center p-4">
                            <div class="feature-icon mb-4">
                                <i class="fas fa-bolt fa-3x text-accent"></i>
                            </div>
                            <h3>Lightning Fast</h3>
                            <p class="text-muted">Optimized for speed with instant image loading and previews.</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="feature-card card h-100 border-0">
                        <div class="card-body text-center p-4">
                            <div class="feature-icon mb-4">
                                <i class="fas fa-shield-alt fa-3x text-accent"></i>
                            </div>
                            <h3>Secure Storage</h3>
                            <p class="text-muted">Military-grade encryption for your precious memories.</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="feature-card card h-100 border-0">
                        <div class="card-body text-center p-4">
                            <div class="feature-icon mb-4">
                                <i class="fas fa-mobile-alt fa-3x text-accent"></i>
                            </div>
                            <h3>Mobile Friendly</h3>
                            <p class="text-muted">Perfectly responsive on all devices from desktop to mobile.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>





