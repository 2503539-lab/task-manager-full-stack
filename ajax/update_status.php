<?php
require_once '../config/db.php';

// Set JSON header
header('Content-Type: application/json');

// Check if request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

// Validate and sanitize input
if (!isset($_POST['task_id']) || !isset($_POST['status'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
    exit();
}

$task_id = intval($_POST['task_id']);
$status = htmlspecialchars(trim($_POST['status']), ENT_QUOTES, 'UTF-8');

// Validate status value
$valid_statuses = ['pending', 'completed'];
if (!in_array($status, $valid_statuses)) {
    echo json_encode(['success' => false, 'message' => 'Invalid status value']);
    exit();
}

// Verify task exists
$check_stmt = $mysqli->prepare("SELECT id FROM tasks WHERE id = ?");
$check_stmt->bind_param("i", $task_id);
$check_stmt->execute();
$result = $check_stmt->get_result();

if ($result->num_rows == 0) {
    echo json_encode(['success' => false, 'message' => 'Task not found']);
    $check_stmt->close();
    $mysqli->close();
    exit();
}
$check_stmt->close();

// Update task status using prepared statement
$stmt = $mysqli->prepare("UPDATE tasks SET status = ? WHERE id = ?");
$stmt->bind_param("si", $status, $task_id);

if ($stmt->execute()) {
    echo json_encode([
        'success' => true, 
        'message' => 'Status updated successfully',
        'new_status' => $status
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update status']);
}

$stmt->close();
$mysqli->close();
?>

