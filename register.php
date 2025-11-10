<?php
require_once 'config/config.php';
require_once 'config/db.php';
require_once 'config/twig.php';

// Redirect if already logged in
if (isLoggedIn()) {
    header("Location: index.php");
    exit();
}

$data = [];

// Handle registration form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = htmlspecialchars(trim($_POST['username']), ENT_QUOTES, 'UTF-8');
    $email = htmlspecialchars(trim($_POST['email']), ENT_QUOTES, 'UTF-8');
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $recaptcha_response = $_POST['g-recaptcha-response'] ?? '';
    
    // Validate inputs
    if (empty($username) || empty($email) || empty($password)) {
        $data['error'] = "All fields are required";
    } elseif ($password !== $confirm_password) {
        $data['error'] = "Passwords do not match";
    } elseif (strlen($password) < 6) {
        $data['error'] = "Password must be at least 6 characters";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $data['error'] = "Invalid email format";
    } elseif (!verifyRecaptcha($recaptcha_response)) {
        $data['error'] = "Please complete the reCAPTCHA verification";
    } else {
        // Check if username or email already exists
        $stmt = $mysqli->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $data['error'] = "Username or email already exists";
        } else {
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Insert new user
            $stmt = $mysqli->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $username, $email, $hashed_password);
            
            if ($stmt->execute()) {
                header("Location: login.php?success=Registration successful! Please login.");
                exit();
            } else {
                $data['error'] = "Registration failed. Please try again.";
            }
        }
        $stmt->close();
    }
    
    // Keep form values on error
    if (isset($data['error'])) {
        $data['username'] = $username;
        $data['email'] = $email;
    }
}

// Render template
echo renderTemplate('register.html.twig', $data);

$mysqli->close();
?>
