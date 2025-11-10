<?php
require_once 'config/config.php';
require_once 'config/db.php';

// Require login to access this page
requireLogin();

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Verify reCAPTCHA
    $recaptcha_response = $_POST['g-recaptcha-response'] ?? '';
    if (!verifyRecaptcha($recaptcha_response)) {
        header("Location: index.php?error=Please complete the reCAPTCHA verification");
        exit();
    }
    
    // Sanitize and validate input to prevent XSS
    $title = htmlspecialchars(trim($_POST['title']), ENT_QUOTES, 'UTF-8');
    $description = htmlspecialchars(trim($_POST['description']), ENT_QUOTES, 'UTF-8');
    $priority = htmlspecialchars(trim($_POST['priority']), ENT_QUOTES, 'UTF-8');
    $due_date = !empty($_POST['due_date']) ? htmlspecialchars(trim($_POST['due_date']), ENT_QUOTES, 'UTF-8') : NULL;
    
    // Validate required fields
    if (empty($title)) {
        header("Location: index.php?error=Title is required");
        exit();
    }
    
    // Validate priority value
    $valid_priorities = ['low', 'medium', 'high'];
    if (!in_array($priority, $valid_priorities)) {
        $priority = 'medium'; // Default to medium if invalid
    }
    
    // Prepare statement to prevent SQL injection
    $stmt = $mysqli->prepare("INSERT INTO tasks (title, description, priority, due_date, status) VALUES (?, ?, ?, ?, 'pending')");
    if (!$stmt) {
        header("Location: index.php?error=Database error: " . $mysqli->error);
        exit();
    }

    $stmt->bind_param("ssss", $title, $description, $priority, $due_date);
    
    if ($stmt->execute()) {
        header("Location: index.php?success=Task added successfully");
    } else {
        header("Location: index.php?error=Failed to add task");
    }
    
    $stmt->close();
    $mysqli->close();
    exit();
} else {
    // If accessed directly without POST, redirect to index
    header("Location: index.php");
    exit();
}
?>
