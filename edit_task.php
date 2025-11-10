<?php
require_once 'config/config.php';
require_once 'config/db.php';
require_once 'config/twig.php';

// Require login to access this page
requireLogin();

// Get task ID from URL
$task_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Handle form submission (UPDATE)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize and validate input
    $title = htmlspecialchars(trim($_POST['title']), ENT_QUOTES, 'UTF-8');
    $description = htmlspecialchars(trim($_POST['description']), ENT_QUOTES, 'UTF-8');
    $priority = htmlspecialchars(trim($_POST['priority']), ENT_QUOTES, 'UTF-8');
    $status = htmlspecialchars(trim($_POST['status']), ENT_QUOTES, 'UTF-8');
    $due_date = !empty($_POST['due_date']) ? htmlspecialchars(trim($_POST['due_date']), ENT_QUOTES, 'UTF-8') : NULL;
    $task_id = intval($_POST['task_id']);
    
    // Validate required fields
    if (empty($title)) {
        header("Location: edit_task.php?id=$task_id&error=Title is required");
        exit();
    }
    
    // Validate priority and status
    $valid_priorities = ['low', 'medium', 'high'];
    $valid_statuses = ['pending', 'completed'];
    
    if (!in_array($priority, $valid_priorities)) {
        $priority = 'medium';
    }
    
    if (!in_array($status, $valid_statuses)) {
        $status = 'pending';
    }
    
    // Prepare statement to prevent SQL injection
    $stmt = $mysqli->prepare("UPDATE tasks SET title = ?, description = ?, priority = ?, status = ?, due_date = ? WHERE id = ?");
    $stmt->bind_param("sssssi", $title, $description, $priority, $status, $due_date, $task_id);
    
    if ($stmt->execute()) {
        header("Location: index.php?success=Task updated successfully");
    } else {
        header("Location: edit_task.php?id=$task_id&error=Failed to update task");
    }
    
    $stmt->close();
    $mysqli->close();
    exit();
}

// Fetch task details
$stmt = $mysqli->prepare("SELECT * FROM tasks WHERE id = ?");
$stmt->bind_param("i", $task_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    header("Location: index.php?error=Task not found");
    exit();
}

$task = $result->fetch_assoc();
$stmt->close();

// Prepare data for template
$data = [
    'task' => $task
];

// Add error message if exists
if (isset($_GET['error'])) {
    $data['error'] = htmlspecialchars($_GET['error']);
}

// Render template
echo renderTemplate('edit_task.html.twig', $data);

$mysqli->close();
exit();
?>
