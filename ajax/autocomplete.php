<?php
require_once '../config/db.php';

// Set JSON header
header('Content-Type: application/json');

// Check if term is provided
if (!isset($_GET['term']) || empty($_GET['term'])) {
    echo json_encode(['success' => false, 'suggestions' => []]);
    exit();
}

// Sanitize input to prevent XSS
$search_term = htmlspecialchars(trim($_GET['term']), ENT_QUOTES, 'UTF-8');

// Prepare statement to prevent SQL injection
$stmt = $mysqli->prepare("SELECT title, description FROM tasks WHERE title LIKE ? OR description LIKE ? LIMIT 5");
$search_param = "%$search_term%";
$stmt->bind_param("ss", $search_param, $search_param);
$stmt->execute();
$result = $stmt->get_result();

$suggestions = [];
while ($row = $result->fetch_assoc()) {
    $suggestions[] = [
        'title' => htmlspecialchars($row['title']),
        'description' => htmlspecialchars($row['description'])
    ];
}

echo json_encode([
    'success' => true,
    'suggestions' => $suggestions
]);

$stmt->close();
$mysqli->close();
?>

