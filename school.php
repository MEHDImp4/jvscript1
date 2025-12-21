<?php
// AJAX endpoint for school autocomplete
require_once 'pdo.php';
require_once 'util.php';

// Get search term
$term = $_REQUEST['term'] ?? '';

if (strlen($term) === 0) {
    echo json_encode([]);
    exit();
}

// Search institutions by prefix
$stmt = $pdo->prepare('SELECT name FROM Institution WHERE name LIKE :prefix ORDER BY name LIMIT 10');
$stmt->execute([':prefix' => $term . '%']);

$results = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $results[] = $row['name'];
}

header('Content-Type: application/json');
echo json_encode($results, JSON_PRETTY_PRINT);
