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

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = htmlspecialchars(trim($_POST['username']), ENT_QUOTES, 'UTF-8');
    $password = $_POST['password'];
    $recaptcha_response = $_POST['g-recaptcha-response'] ?? '';
    
    // Verify reCAPTCHA
    if (!verifyRecaptcha($recaptcha_response)) {
        $data['error'] = "Please complete the reCAPTCHA verification";
    } else {
        // Check credentials
        $stmt = $mysqli->prepare("SELECT id, username, password FROM users WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $username, $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();
            
            // Verify password
            if (password_verify($password, $user['password'])) {
                // Set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['last_activity'] = time();
                
                // Regenerate session ID for security
                session_regenerate_id(true);
                
                header("Location: index.php?success=Welcome back, " . htmlspecialchars($user['username']));
                exit();
            } else {
                $data['error'] = "Invalid username or password";
            }
        } else {
            $data['error'] = "Invalid username or password";
        }
        $stmt->close();
    }

    // Keep username in form if login failed
    $data['username'] = $username;
}

// Add URL parameters
if (isset($_GET['error'])) {
    $data['error'] = htmlspecialchars($_GET['error']);
}
if (isset($_GET['success'])) {
    $data['success'] = htmlspecialchars($_GET['success']);
}

// Render template
echo renderTemplate('login.html.twig', $data);

$mysqli->close();
?>
