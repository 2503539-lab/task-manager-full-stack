<?php
require_once '../config/db.php';

// Set JSON header
header('Content-Type: application/json');

// Get statistics
$total_query = "SELECT COUNT(*) as count FROM tasks";
$completed_query = "SELECT COUNT(*) as count FROM tasks WHERE status = 'completed'";
$pending_query = "SELECT COUNT(*) as count FROM tasks WHERE status = 'pending'";

$total = $mysqli->query($total_query)->fetch_assoc()['count'];
$completed = $mysqli->query($completed_query)->fetch_assoc()['count'];
$pending = $mysqli->query($pending_query)->fetch_assoc()['count'];

echo json_encode([
    'success' => true,
    'total' => $total,
    'completed' => $completed,
    'pending' => $pending
]);

$mysqli->close();
?>

