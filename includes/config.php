<?php
define('APP_NAME', 'Photo Gallery');
define('BASE_URL', 'https://mygallery.loc');

define('BASE_PATH', __DIR__ . '/..');
define('UPLOAD_DIR', BASE_PATH . '/uploads/images');
define('AVATAR_DIR', BASE_PATH . '/uploads/avatars');

define('DB_HOST', 'localhost');
define('DB_NAME', 'gallery');
define('DB_USER', 'postgres');
define('DB_PASS', '1234');
define('DB_PORT', '5432');

define('ALLOWED_MIME_TYPES', serialize(['image/jpeg', 'image/png', 'image/gif']));

define('MAX_FILE_SIZE', 5242880);


if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>