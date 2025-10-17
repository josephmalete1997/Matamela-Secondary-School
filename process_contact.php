<?php
session_start();

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: contact.php");
    exit;
}

// Sanitize and validate form data
$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$subject = trim($_POST['subject'] ?? '');
$message = trim($_POST['message'] ?? '');
$newsletter = isset($_POST['newsletter']) ? 1 : 0;

// Basic validation
$errors = [];

if (empty($name)) {
    $errors[] = "Name is required";
}

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Valid email address is required";
}

if (empty($subject)) {
    $errors[] = "Subject is required";
}

if (empty($message)) {
    $errors[] = "Message is required";
}

// If there are errors, redirect back with error message
if (!empty($errors)) {
    $_SESSION['contact_error'] = implode(", ", $errors);
    header("Location: contact.php");
    exit;
}

// Prepare email content
$to = "matamelasecondary@gmail.com"; // School's email
$email_subject = "Contact Form: " . $subject;

$email_body = "
<html>
<head>
    <title>New Contact Form Submission</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #d72f25; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; background: #f8f9fa; }
        .field { margin-bottom: 15px; }
        .label { font-weight: bold; color: #d72f25; }
        .value { margin-top: 5px; }
        .footer { text-align: center; padding: 20px; color: #666; font-size: 0.9em; }
    </style>
</head>
<body>
    <div class='container'>
        <div class='header'>
            <h2>New Contact Form Submission</h2>
            <p>Matamela Ramaphosa Secondary School</p>
        </div>
        
        <div class='content'>
            <div class='field'>
                <div class='label'>Name:</div>
                <div class='value'>" . htmlspecialchars($name) . "</div>
            </div>
            
            <div class='field'>
                <div class='label'>Email:</div>
                <div class='value'>" . htmlspecialchars($email) . "</div>
            </div>
            
            <div class='field'>
                <div class='label'>Phone:</div>
                <div class='value'>" . (empty($phone) ? 'Not provided' : htmlspecialchars($phone)) . "</div>
            </div>
            
            <div class='field'>
                <div class='label'>Subject:</div>
                <div class='value'>" . htmlspecialchars($subject) . "</div>
            </div>
            
            <div class='field'>
                <div class='label'>Message:</div>
                <div class='value'>" . nl2br(htmlspecialchars($message)) . "</div>
            </div>
            
            <div class='field'>
                <div class='label'>Newsletter Subscription:</div>
                <div class='value'>" . ($newsletter ? 'Yes, subscribed to newsletter' : 'No newsletter subscription') . "</div>
            </div>
            
            <div class='field'>
                <div class='label'>Submitted:</div>
                <div class='value'>" . date('F j, Y \a\t g:i A') . "</div>
            </div>
        </div>
        
        <div class='footer'>
            <p>This message was sent from the contact form on the Matamela Ramaphosa Secondary School website.</p>
        </div>
    </div>
</body>
</html>
";

// Email headers
$headers = [
    'MIME-Version: 1.0',
    'Content-Type: text/html; charset=UTF-8',
    'From: ' . $email,
    'Reply-To: ' . $email,
    'X-Mailer: PHP/' . phpversion()
];

// Attempt to send email
$mail_sent = false;
try {
    $mail_sent = mail($to, $email_subject, $email_body, implode("\r\n", $headers));
} catch (Exception $e) {
    error_log("Contact form email error: " . $e->getMessage());
}

// Store contact submission in database (optional - you can implement this if needed)
try {
    require_once 'includes/config.php';
    require_once 'includes/Database.php';
    
    $db = new Database();
    
    // Create contact_submissions table if it doesn't exist
    $create_table_sql = "CREATE TABLE IF NOT EXISTS contact_submissions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL,
        phone VARCHAR(50),
        subject VARCHAR(255) NOT NULL,
        message TEXT NOT NULL,
        newsletter_signup BOOLEAN DEFAULT FALSE,
        ip_address VARCHAR(45),
        user_agent TEXT,
        submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    $db->query($create_table_sql);
    
    // Insert the submission
    $submission_data = [
        'name' => $name,
        'email' => $email,
        'phone' => $phone,
        'subject' => $subject,
        'message' => $message,
        'newsletter_signup' => $newsletter,
        'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
    ];
    
    $db->insert('contact_submissions', $submission_data);
    
} catch (Exception $e) {
    // Log database error but don't prevent the success message
    error_log("Contact form database error: " . $e->getMessage());
}

// Set success message
if ($mail_sent) {
    $_SESSION['contact_success'] = "Thank you for your message! We'll get back to you soon.";
} else {
    $_SESSION['contact_success'] = "Your message has been received. We'll get back to you soon.";
}

// Redirect back to contact page
header("Location: contact.php");
exit;
?> 