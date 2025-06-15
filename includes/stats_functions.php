<?php
require_once __DIR__ . '/database.php';

function getAverageImageSize() {
    $pdo = getPDO();
    $stmt = $pdo->query("SELECT AVG(LENGTH(filename)) as avg_size FROM images");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return round($result['avg_size'] / 1024, 2);
}

function getImageFormatDistribution() {
    $pdo = getPDO();
    $stmt = $pdo->query("
        SELECT 
            SUBSTRING(filename FROM '\.([^\.]+)$') as format,
            COUNT(*) as count
        FROM images
        GROUP BY format
        ORDER BY count DESC
    ");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>