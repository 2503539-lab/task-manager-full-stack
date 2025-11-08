<?php
require_once __DIR__ . '/config/db.php';

// Check if ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: index.php?error=Invalid task ID");
    exit();
}

// Sanitize ID to prevent SQL injection
$task_id = intval($_GET['id']);

// Verify task exists before deletion
$check_stmt = $mysqli->prepare("SELECT id FROM tasks WHERE id = ?");
$check_stmt->bind_param("i", $task_id);
$check_stmt->execute();
$result = $check_stmt->get_result();

if ($result->num_rows == 0) {
    $check_stmt->close();
    $mysqli->close();
    header("Location: index.php?error=Task not found");
    exit();
}
$check_stmt->close();

// Prepare delete statement
$stmt = $mysqli->prepare("DELETE FROM tasks WHERE id = ?");
$stmt->bind_param("i", $task_id);

if ($stmt->execute()) {
    header("Location: index.php?success=Task deleted successfully");
} else {
    header("Location: index.php?error=Failed to delete task");
}

$stmt->close();
$mysqli->close();
exit();
?>
