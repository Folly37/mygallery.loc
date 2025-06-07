<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';

try {
    $pdo = new PDO(
        'pgsql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME,
        DB_USER,
        DB_PASS
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("PostgreSQL connection error: " . $e->getMessage());
}

$user = getCurrentUser();
if (!$user) {
    header("Location: " . BASE_URL . "/?page=login");
    exit;
}

try {
    $extStmt = $pdo->prepare("SELECT DISTINCT LOWER(SUBSTRING(filename FROM '\\.([^\\.]+)$')) AS ext 
                             FROM images 
                             WHERE user_id = :user_id
                             ORDER BY ext");
    $extStmt->execute(['user_id' => $_SESSION['user_id']]);
    $extensions = $extStmt->fetchAll(PDO::FETCH_COLUMN, 0);
} catch (PDOException $e) {
    die("Database query failed: " . $e->getMessage());
}

$selectedExt = $_POST['ext'] ?? null;
$whereClause = "WHERE user_id = :user_id";
$params = ['user_id' => $_SESSION['user_id']];

if ($selectedExt && $selectedExt !== 'all') {
    $whereClause .= " AND LOWER(filename) LIKE :ext";
    $params['ext'] = '%.' . strtolower($selectedExt);
}

try {
    $stmt = $pdo->prepare("SELECT * FROM images $whereClause ORDER BY uploaded_at DESC");
    $stmt->execute($params);
    $images = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Database query failed: " . $e->getMessage());
}
?>

<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-5">
            <h2><i class="fas fa-images"></i> My Gallery</h2>
            <a href="<?= BASE_URL ?>/?page=upload" class="btn btn-primary">
                <i class="fas fa-plus"></i> Upload New
            </a>
        </div>

        <form action="<?= BASE_URL ?>/?page=gallery" method="POST" class="mb-4">
            <div class="form-row align-items-center">
                <div class="col-auto">
                    <label class="mr-2">Filter by extension:</label>
                </div>
                <div class="col- mb-4 mt-2">
                    <select name="ext" class="form-control">
                        <option value="all">All formats</option>
                        <?php foreach ($extensions as $ext): ?>
                            <option value="<?= htmlspecialchars($ext) ?>" <?= ($selectedExt === $ext) ? 'selected' : '' ?>>
                                .<?= htmlspecialchars($ext) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-dark">Filter</button>
                </div>
            </div>
        </form>

        <?php if (empty($images)): ?>
            <div class="alert alert-info">
                No images found<?= $selectedExt ? " with .$selectedExt extension" : '' ?>. 
                <a href="<?= BASE_URL ?>/?page=upload">Upload new images</a>.
            </div>
        <?php else: ?>
            <div class="row">
                <?php foreach ($images as $image): 
                    $fileExt = pathinfo($image['filename'], PATHINFO_EXTENSION);
                ?>
                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                            <img src="<?= BASE_URL ?>/uploads/images/<?= htmlspecialchars($image['filename']) ?>" 
                                 class="card-img-top" alt="<?= htmlspecialchars($image['title'] ?? 'Image') ?>">
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($image['title'] ?? 'Untitled') ?></h5>
                                <?php if (!empty($image['description'])): ?>
                                    <p class="card-text"><?= htmlspecialchars($image['description']) ?></p>
                                <?php endif; ?>
                                <span class="badge badge-secondary">.<?= strtolower($fileExt) ?></span>
                            </div>
                            <div class="card-footer bg-transparent">
                                <small class="text-muted">
                                    Uploaded: <?= date('M d, Y', strtotime($image['uploaded_at'])) ?>
                                </small>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>