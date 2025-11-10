<?php
// Configuration file for Task Manager

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database configuration is in db.php

// Google reCAPTCHA Configuration
// Get your keys from: https://www.google.com/recaptcha/admin
define('RECAPTCHA_SITE_KEY', '6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI'); // Test key
define('RECAPTCHA_SECRET_KEY', '6LeIxAcTAAAAAGG-vFI1TnRWxMZNFuojJ4WifJWe'); // Test key

// Site settings
define('SITE_NAME', 'Task Manager');
define('SITE_URL', 'http://mi-linux.wlv.ac.uk/~2503539/task_manager/');

// Security settings
define('SESSION_TIMEOUT', 3600); // 1 hour

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

// Check session timeout
function checkSessionTimeout() {
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > SESSION_TIMEOUT)) {
        session_unset();
        session_destroy();
        return false;
    }
    $_SESSION['last_activity'] = time();
    return true;
}

// Require login (use on protected pages)
function requireLogin() {
    if (!isLoggedIn() || !checkSessionTimeout()) {
        header("Location: login.php?error=Please login to access this page");
        exit();
    }
}

// Verify reCAPTCHA
function verifyRecaptcha($response) {
    $url = 'https://www.google.com/recaptcha/api/siteverify';
    $data = [
        'secret' => RECAPTCHA_SECRET_KEY,
        'response' => $response,
        'remoteip' => $_SERVER['REMOTE_ADDR']
    ];
    
    $options = [
        'http' => [
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => http_build_query($data)
        ]
    ];
    
    $context  = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    $resultJson = json_decode($result);
    
    return $resultJson->success;
}

// CSRF Token functions
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}
?>

