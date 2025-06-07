<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="<?= BASE_URL ?>">
           <img width="30px" src="favicon.ico" alt=""> Photo Gallery
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" 
                data-bs-target="#mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="mainNavbar">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">   
                    <a class="nav-link <?= ($page === 'home') ? 'active' : '' ?>" 
                       href="<?= BASE_URL ?>/?page=home">
                        <i class="fas fa-home me-1"></i> Home
                    </a>
                </li>
                
                <?php if (isLoggedIn()): ?>
                <li class="nav-item">
                    <a class="nav-link <?= ($page === 'gallery') ? 'active' : '' ?>" 
                       href="<?= BASE_URL ?>/?page=gallery">
                        <i class="fas fa-images me-1"></i> Gallery
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= ($page === 'upload') ? 'active' : '' ?>" 
                       href="<?= BASE_URL ?>/?page=upload">
                        <i class="fas fa-upload me-1"></i> Upload
                    </a>
                </li>
                <?php endif; ?>
            </ul>
            
            <ul class="navbar-nav">
                <?php if (isLoggedIn()): ?>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="userDropdown" 
                       role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-user-circle me-1"></i> <?= htmlspecialchars($_SESSION['username']) ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                        <li><a class="dropdown-item" href="<?= BASE_URL ?>/?page=profile">
                            <i class="fas fa-user me-1"></i> Profile</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="<?= BASE_URL ?>/includes/logout.php">
                            <i class="fas fa-sign-out-alt me-1"></i> Logout</a></li>
                    </ul>
                </li>
                <?php else: ?>
                <li class="nav-item">
                    <a class="nav-link <?= ($page === 'login') ? 'active' : '' ?>" 
                       href="<?= BASE_URL ?>/?page=login">
                        <i class="fas fa-sign-in-alt me-1"></i> Login
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= ($page === 'register') ? 'active' : '' ?>" 
                       href="<?= BASE_URL ?>/?page=register">
                        <i class="fas fa-user-plus me-1"></i> Register
                    </a>
                </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>