<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

$page = $_GET['page'] ?? 'home';
$protected_pages = ['upload', 'gallery', 'profile'];

if (in_array($page, $protected_pages)) {
    redirectIfNotLoggedIn();
}

include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/navbar.php';
?>

<main class="container my-5">
    <?php
    $page_file = __DIR__ . '/pages/' . $page . '.php';
    if (file_exists($page_file)) {
        include $page_file;
    } else {
        include __DIR__ . '/pages/404.php';
    }
    ?>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>