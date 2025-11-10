<?php
require_once 'config/config.php';
require_once 'config/db.php';
require_once 'config/twig.php';

// Require login to access this page
requireLogin();

// Initialize search parameters
$search_title = isset($_GET['title']) ? htmlspecialchars(trim($_GET['title']), ENT_QUOTES, 'UTF-8') : '';
$search_status = isset($_GET['status']) ? htmlspecialchars(trim($_GET['status']), ENT_QUOTES, 'UTF-8') : '';
$search_priority = isset($_GET['priority']) ? htmlspecialchars(trim($_GET['priority']), ENT_QUOTES, 'UTF-8') : '';
$search_date_from = isset($_GET['date_from']) ? htmlspecialchars(trim($_GET['date_from']), ENT_QUOTES, 'UTF-8') : '';
$search_date_to = isset($_GET['date_to']) ? htmlspecialchars(trim($_GET['date_to']), ENT_QUOTES, 'UTF-8') : '';

// Build dynamic SQL query with security
$sql = "SELECT * FROM tasks WHERE 1=1";
$params = [];
$types = "";

if (!empty($search_title)) {
    $sql .= " AND (title LIKE ? OR description LIKE ?)";
    $search_param = "%$search_title%";
    $params[] = $search_param;
    $params[] = $search_param;
    $types .= "ss";
}

if (!empty($search_status) && in_array($search_status, ['pending', 'completed'])) {
    $sql .= " AND status = ?";
    $params[] = $search_status;
    $types .= "s";
}

if (!empty($search_priority) && in_array($search_priority, ['low', 'medium', 'high'])) {
    $sql .= " AND priority = ?";
    $params[] = $search_priority;
    $types .= "s";
}

if (!empty($search_date_from)) {
    $sql .= " AND due_date >= ?";
    $params[] = $search_date_from;
    $types .= "s";
}

if (!empty($search_date_to)) {
    $sql .= " AND due_date <= ?";
    $params[] = $search_date_to;
    $types .= "s";
}

$sql .= " ORDER BY created_at DESC";

// Prepare and execute statement using $mysqli
$stmt = $mysqli->prepare($sql);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

// Convert result to array
$tasks = [];
while ($row = $result->fetch_assoc()) {
    $tasks[] = $row;
}

// Prepare data for template
$data = [
    'tasks' => $tasks,
    'search_title' => $search_title,
    'search_status' => $search_status,
    'search_priority' => $search_priority,
    'search_date_from' => $search_date_from,
    'search_date_to' => $search_date_to
];

// Render template
echo renderTemplate('search.html.twig', $data);

$stmt->close();
$mysqli->close();
exit();
?>
