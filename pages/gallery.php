<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/database.php';
require_once __DIR__ . '/../includes/image_analysis.php';

$user = getCurrentUser();
if (!$user) {
    header("Location: " . BASE_URL . "/?page=login");
    exit;
}

$stats = getGalleryStatistics($_SESSION['user_id']);
$extensions = getImageExtensions($_SESSION['user_id']);
$uploadActivity = getUploadActivityStats($_SESSION['user_id']);
$nextUploadPrediction = predictNextUploadTime($_SESSION['user_id']);
$galleryGrowth = getGalleryGrowth($_SESSION['user_id']);


$selectedExt = $_POST['ext'] ?? null;
$whereClause = "WHERE user_id = :user_id";
$params = ['user_id' => $_SESSION['user_id']];

if ($selectedExt && $selectedExt !== 'all') {
    $whereClause .= " AND LOWER(filename) LIKE :ext";
    $params['ext'] = '%.' . strtolower($selectedExt);
}

try {
    $pdo = Database::getInstance();
    $stmt = $pdo->prepare("SELECT * FROM images $whereClause ORDER BY uploaded_at DESC");
    $stmt->execute($params);
    $images = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Database query failed: " . $e->getMessage());
}
?>

<div class="container mt-4">
    <div class="card mb-4">
        <div class="card-header">
            <h4><i class="fas fa-chart-pie"></i> Gallery Statistics</h4>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h5>Basic Metrics</h5>
                    <ul class="list-group">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Total Images
                            <span class="badge bg-primary rounded-pill"><?= $stats['total_images'] ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Average Size
                            <span class="badge bg-primary rounded-pill"><?= round($stats['avg_size_kb'], 2) ?> KB</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Storage Used
                            <span class="badge bg-primary rounded-pill"><?= round($stats['total_size_mb'], 2) ?> MB</span>
                        </li>
                    </ul>
                    
                    <h5 class="mt-4">Gallery Growth</h5>
                    <p>Average monthly growth: <span class="badge bg-<?= $galleryGrowth['average_growth'] >= 0 ? 'success' : 'danger' ?>">
                        <?= $galleryGrowth['average_growth'] ?>%
                    </span></p>
                    
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Month</th>
                                    <th>Uploads</th>
                                    <th>Size (MB)</th>
                                    <th>Growth</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($galleryGrowth['monthly_stats'] as $index => $month): ?>
                                    <tr>
                                        <td><?= date('M Y', strtotime($month['month'])) ?></td>
                                        <td><?= $month['uploads'] ?></td>
                                        <td><?= round($month['total_size'] / (1024 * 1024), 2) ?></td>
                                        <td>
                                            <?php if ($index > 0): ?>
                                                <span class="badge bg-<?= $galleryGrowth['growth_rates'][$index-1] >= 0 ? 'success' : 'danger' ?>">
                                                    <?= $galleryGrowth['growth_rates'][$index-1] ?>%
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">N/A</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <h5>Format Distribution</h5>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Format</th>
                                    <th>Count</th>
                                    <th>Percentage</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($stats['format_distribution'] as $format): ?>
                                    <tr>
                                        <td>.<?= strtoupper($format['format']) ?></td>
                                        <td><?= $format['count'] ?></td>
                                        <td>
                                            <div class="progress">
                                                <div class="progress-bar" 
                                                    role="progressbar" 
                                                    style="width: <?= $format['percentage'] ?>%" 
                                                    aria-valuenow="<?= $format['percentage'] ?>" 
                                                    aria-valuemin="0" 
                                                    aria-valuemax="100">
                                                    <?= round($format['percentage'], 1) ?>%
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <h5 class="mt-4">Upload Time Prediction</h5>
                    <div class="alert alert-info">
                        <p>Next upload likely around <strong><?= $nextUploadPrediction['predicted_hour'] ?>:00</strong></p>
                        <div class="progress">
                            <div class="progress-bar" 
                                role="progressbar" 
                                style="width: <?= $nextUploadPrediction['confidence'] ?>%" 
                                aria-valuenow="<?= $nextUploadPrediction['confidence'] ?>" 
                                aria-valuemin="0" 
                                aria-valuemax="100">
                                Confidence: <?= $nextUploadPrediction['confidence'] ?>%
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row mt-4">
                <div class="col-md-6">
                    <h5>Activity by Day of Week</h5>
                    <canvas id="dayOfWeekChart" height="200"></canvas>
                </div>
                <div class="col-md-6">
                    <h5>Activity by Hour of Day</h5>
                    <canvas id="hourOfDayChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-images"></i> My Gallery</h2>
                <a href="<?= BASE_URL ?>/?page=upload" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Upload New
                </a>
            </div>

            <form action="<?= BASE_URL ?>/?page=gallery" method="POST" class="mb-4">
                <div class="row g-3 align-items-center">
                    <div class="col-auto">
                        <label class="col-form-label">Filter by format:</label>
                    </div>
                    <div class="col-md-3">
                        <select name="ext" class="form-select">
                            <option value="all">All formats</option>
                            <?php foreach ($extensions as $ext): ?>
                                <option value="<?= htmlspecialchars($ext) ?>" <?= ($selectedExt === $ext) ? 'selected' : '' ?>>
                                    .<?= strtoupper(htmlspecialchars($ext)) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-dark">Apply Filter</button>
                    </div>
                </div>
            </form>

            <?php if (empty($images)): ?>
                <div class="alert alert-info">
                    No images found<?= $selectedExt ? " with .$selectedExt format" : '' ?>.
                    <a href="<?= BASE_URL ?>/?page=upload" class="alert-link">Upload new images</a>.
                </div>
            <?php else: ?>
                <div class="row row-cols-1 row-cols-md-3 g-4">
                    <?php foreach ($images as $image): 
                        $fileExt = pathinfo($image['filename'], PATHINFO_EXTENSION);
                        $sizeKB = round(filesize(__DIR__ . '/../uploads/images/' . $image['filename']) / 1024, 2);
                    ?>
                        <div class="col">
                            <div class="card h-100">
                                <img src="<?= BASE_URL ?>/uploads/images/<?= htmlspecialchars($image['filename']) ?>" 
                                    class="card-img-top" 
                                    alt="<?= htmlspecialchars($image['title'] ?? 'Image') ?>"
                                    style="height: 200px; object-fit: cover;">
                                <div class="card-body">
                                    <h5 class="card-title"><?= htmlspecialchars($image['title'] ?? 'Untitled') ?></h5>
                                    <?php if (!empty($image['description'])): ?>
                                        <p class="card-text"><?= htmlspecialchars($image['description']) ?></p>
                                    <?php endif; ?>
                                    <div class="d-flex justify-content-between">
                                        <span class="badge bg-secondary">.<?= strtoupper($fileExt) ?></span>
                                        <small class="text-muted"><?= $sizeKB ?> KB</small>
                                    </div>
                                </div>
                                <div class="card-footer bg-transparent">
                                    <small class="text-muted">
                                        Uploaded: <?= date('M d, Y H:i', strtotime($image['uploaded_at'])) ?>
                                    </small>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const dayCtx = document.getElementById('dayOfWeekChart').getContext('2d');
const days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
const dayData = Array(7).fill(0);
<?php foreach ($uploadActivity['by_day'] as $day): ?>
    dayData[<?= $day['day_of_week'] ?>] = <?= $day['upload_count'] ?>;
<?php endforeach; ?>

new Chart(dayCtx, {
    type: 'bar',
    data: {
        labels: days,
        datasets: [{
            label: 'Uploads',
            data: dayData,
            backgroundColor: 'rgba(54, 162, 235, 0.5)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

const hourCtx = document.getElementById('hourOfDayChart').getContext('2d');
const hours = Array.from({length: 24}, (_, i) => i + ':00');
const hourData = Array(24).fill(0);
<?php foreach ($uploadActivity['by_hour'] as $hour): ?>
    hourData[<?= $hour['hour_of_day'] ?>] = <?= $hour['upload_count'] ?>;
<?php endforeach; ?>

new Chart(hourCtx, {
    type: 'line',
    data: {
        labels: hours,
        datasets: [{
            label: 'Uploads',
            data: hourData,
            fill: true,
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
            borderColor: 'rgba(75, 192, 192, 1)',
            tension: 0.4
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});
</script>