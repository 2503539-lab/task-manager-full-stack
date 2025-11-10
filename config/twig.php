<?php
// Twig Template Engine Configuration

require_once __DIR__ . '/../vendor/autoload.php';

use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\Extension\DebugExtension;

// Define template directory
$loader = new FilesystemLoader(__DIR__ . '/../templates');

// Create Twig environment
$twig = new Environment($loader, [
    'cache' => __DIR__ . '/../cache', // Cache directory
    'debug' => true, // Enable debug mode
    'auto_reload' => true, // Auto-reload templates in development
]);

// Add debug extension
$twig->addExtension(new DebugExtension());

// Global variables available to all templates
$twig->addGlobal('site_name', 'Task Manager');
$twig->addGlobal('site_url', 'http://mi-linux.wlv.ac.uk/~2503539/task_manager/');

// Helper function to render template
function renderTemplate($template, $data = []) {
    global $twig;
    
    // Add session data if logged in
    if (isset($_SESSION['user_id'])) {
        $data['user'] = [
            'id' => $_SESSION['user_id'],
            'username' => $_SESSION['username']
        ];
    }
    
    // Add CSRF token
    if (function_exists('generateCSRFToken')) {
        $data['csrf_token'] = generateCSRFToken();
    }
    
    // Add reCAPTCHA site key
    if (defined('RECAPTCHA_SITE_KEY')) {
        $data['recaptcha_site_key'] = RECAPTCHA_SITE_KEY;
    }
    
    return $twig->render($template, $data);
}
?>

