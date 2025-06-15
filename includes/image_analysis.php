<?php
require_once __DIR__ . '/database.php';

/**
 * Получает статистику галереи для пользователя
 */
function getGalleryStatistics($user_id) {
    $pdo = Database::getInstance();
    
    // Базовые метрики
    $stmt = $pdo->prepare("
        SELECT 
            COUNT(*) as total_images,
            AVG(LENGTH(filename)) as avg_size_bytes,
            SUM(LENGTH(filename)) as total_size_bytes,
            MIN(LENGTH(filename)) as min_size_bytes,
            MAX(LENGTH(filename)) as max_size_bytes,
            MIN(uploaded_at) as first_upload,
            MAX(uploaded_at) as last_upload
        FROM images 
        WHERE user_id = :user_id
    ");
    $stmt->execute(['user_id' => $user_id]);
    $metrics = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Распределение по форматам
    $formatStmt = $pdo->prepare("
        SELECT 
            LOWER(SUBSTRING(filename FROM '\.([^\.]+)$')) as format,
            COUNT(*) as count
        FROM images
        WHERE user_id = :user_id
        GROUP BY format
        ORDER BY count DESC
    ");
    $formatStmt->execute(['user_id' => $user_id]);
    $formats = $formatStmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Рассчитываем проценты
    $total = $metrics['total_images'];
    foreach ($formats as &$format) {
        $format['percentage'] = ($format['count'] / $total) * 100;
    }
    

    $firstUpload = new DateTime($metrics['first_upload']);
    $lastUpload = new DateTime($metrics['last_upload']);
    $interval = $firstUpload->diff($lastUpload);
    $days = $interval->days ?: 1;
    $uploadFrequency = $total / $days;
    
    // Рассчитываем сколько дней прошло с последней загрузки
    $now = new DateTime();
    $lastUploadDiff = $now->diff(new DateTime($metrics['last_upload']))->days;
    
    return [
        'total_images' => $total,
        'avg_size_kb' => $metrics['avg_size_bytes'] / 1024,
        'total_size_mb' => $metrics['total_size_bytes'] / (1024 * 1024),
        'min_size_kb' => $metrics['min_size_bytes'] / 1024,
        'max_size_kb' => $metrics['max_size_bytes'] / 1024,
        'format_distribution' => $formats,
        'upload_frequency' => round($uploadFrequency, 2),
        'last_upload_diff' => $lastUploadDiff
    ];
    try {
        $pdo = Database::getInstance();
        // ... остальной код
    } catch (PDOException $e) {
        error_log("Gallery statistics error: " . $e->getMessage());
        return [
            'error' => true,
            'message' => 'Could not load gallery statistics'
        ];
    }
    
}

/**
 * Получает список расширений изображений пользователя
 */
function getImageExtensions($user_id) {
    $pdo = Database::getInstance();
    $stmt = $pdo->prepare("
        SELECT DISTINCT LOWER(SUBSTRING(filename FROM '\.([^\.]+)$')) AS ext 
        FROM images 
        WHERE user_id = :user_id
        ORDER BY ext
    ");
    $stmt->execute(['user_id' => $user_id]);
    return $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
}

function getActualImageSize($filename) {
    $filepath = __DIR__ . '/../uploads/images/' . $filename;
    return file_exists($filepath) ? filesize($filepath) : 0;
}

function getUploadActivityStats($user_id) {
    $pdo = Database::getInstance();
    
    $stmt = $pdo->prepare("
        SELECT 
            EXTRACT(DOW FROM uploaded_at) AS day_of_week,
            COUNT(*) AS upload_count
        FROM images
        WHERE user_id = :user_id
        GROUP BY day_of_week
        ORDER BY day_of_week
    ");
    $stmt->execute(['user_id' => $user_id]);
    $dayStats = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $stmt = $pdo->prepare("
        SELECT 
            EXTRACT(HOUR FROM uploaded_at) AS hour_of_day,
            COUNT(*) AS upload_count
        FROM images
        WHERE user_id = :user_id
        GROUP BY hour_of_day
        ORDER BY hour_of_day
    ");
    $stmt->execute(['user_id' => $user_id]);
    $hourStats = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    return [
        'by_day' => $dayStats,
        'by_hour' => $hourStats
    ];
}

function predictNextUploadTime($user_id) {
    $activity = getUploadActivityStats($user_id);
    
    $maxHour = array_reduce($activity['by_hour'], function($carry, $item) {
        return ($item['upload_count'] > $carry['count']) ? 
            ['hour' => $item['hour_of_day'], 'count' => $item['upload_count']] : $carry;
    }, ['hour' => 12, 'count' => 0]);
    
    return [
        'predicted_hour' => $maxHour['hour'],
        'confidence' => min(100, $maxHour['count'] * 10) 
    ];
}

function getGalleryGrowth($user_id) {
    $pdo = Database::getInstance();
    
    $stmt = $pdo->prepare("
        SELECT 
            DATE_TRUNC('month', uploaded_at) AS month,
            COUNT(*) AS uploads,
            SUM(LENGTH(filename)) AS total_size
        FROM images
        WHERE user_id = :user_id
        GROUP BY month
        ORDER BY month
    ");
    $stmt->execute(['user_id' => $user_id]);
    $monthlyStats = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $growthRates = [];
    for ($i = 1; $i < count($monthlyStats); $i++) {
        $growth = ($monthlyStats[$i]['uploads'] - $monthlyStats[$i-1]['uploads']) / 
                 $monthlyStats[$i-1]['uploads'] * 100;
        $growthRates[] = round($growth, 2);
    }
    
    return [
        'monthly_stats' => $monthlyStats,
        'growth_rates' => $growthRates,
        'average_growth' => count($growthRates) > 0 ? 
            round(array_sum($growthRates) / count($growthRates), 2) : 0
    ];
}

