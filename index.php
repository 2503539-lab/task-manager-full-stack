<?php
require_once 'config/config.php';
require_once 'config/db.php';
require_once 'config/twig.php';

// Require login to access this page
requireLogin();

// Fetch all tasks from database
$sql = "SELECT * FROM tasks ORDER BY created_at DESC";
$result = $mysqli->query($sql);

// Convert result to array
$tasks = [];
while ($row = $result->fetch_assoc()) {
    $tasks[] = $row;
}

// Get statistics
$total_tasks = count($tasks);
$completed_tasks = $mysqli->query("SELECT COUNT(*) as count FROM tasks WHERE status = 'completed'")->fetch_assoc()['count'];
$pending_tasks = $mysqli->query("SELECT COUNT(*) as count FROM tasks WHERE status = 'pending'")->fetch_assoc()['count'];

// Prepare data for template
$data = [
    'tasks' => $tasks,
    'total_tasks' => $total_tasks,
    'completed_tasks' => $completed_tasks,
    'pending_tasks' => $pending_tasks
];

// Add success/error messages from URL
if (isset($_GET['success'])) {
    $data['success'] = htmlspecialchars($_GET['success']);
}
if (isset($_GET['error'])) {
    $data['error'] = htmlspecialchars($_GET['error']);
}

// Render template
echo renderTemplate('index.html.twig', $data);

$mysqli->close();
?>
