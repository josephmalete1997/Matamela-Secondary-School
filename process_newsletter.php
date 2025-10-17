<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/Database.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);

if (!$email) {
    echo json_encode(['success' => false, 'message' => 'Please enter a valid email address']);
    exit;
}

try {
    $db = new Database();
    
    // Create newsletter table if it doesn't exist
    $db->query("CREATE TABLE IF NOT EXISTS newsletter_subscribers (
        id INT AUTO_INCREMENT PRIMARY KEY,
        email VARCHAR(255) UNIQUE NOT NULL,
        subscribed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        is_active BOOLEAN DEFAULT TRUE,
        ip_address VARCHAR(45),
        user_agent TEXT
    )");
    
    // Check if email already exists
    $existing = $db->getRow("SELECT id, is_active FROM newsletter_subscribers WHERE email = ?", [$email]);
    
    if ($existing) {
        if ($existing['is_active']) {
            echo json_encode(['success' => false, 'message' => 'You are already subscribed to our newsletter']);
        } else {
            // Reactivate subscription
            $db->query("UPDATE newsletter_subscribers SET is_active = TRUE, subscribed_at = CURRENT_TIMESTAMP WHERE email = ?", [$email]);
            echo json_encode(['success' => true, 'message' => 'Welcome back! Your subscription has been reactivated']);
        }
    } else {
        // Add new subscriber
        $ip_address = $_SERVER['REMOTE_ADDR'] ?? '';
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        
        $db->query("INSERT INTO newsletter_subscribers (email, ip_address, user_agent) VALUES (?, ?, ?)", 
                   [$email, $ip_address, $user_agent]);
        
        echo json_encode(['success' => true, 'message' => 'Thank you for subscribing! You will receive our latest updates']);
    }
    
} catch (Exception $e) {
    error_log("Newsletter subscription error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Something went wrong. Please try again later']);
}
?> 