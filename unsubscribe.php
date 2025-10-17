<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/Database.php';

$title = "Unsubscribe from Newsletter";
$page = "unsubscribe";
$subtitle = "Manage your email preferences";

$message = '';
$email = '';

// Handle unsubscribe request
if ($_SERVER['REQUEST_METHOD'] === 'POST' || isset($_GET['email'])) {
    $email = $_POST['email'] ?? $_GET['email'] ?? '';
    $email = filter_var($email, FILTER_VALIDATE_EMAIL);
    
    if ($email) {
        try {
            $db = new Database();
            
            // Check if email exists in our database
            $subscriber = $db->getRow("SELECT id, is_active FROM newsletter_subscribers WHERE email = ?", [$email]);
            
            if ($subscriber) {
                if ($subscriber['is_active']) {
                    // Deactivate subscription
                    $db->query("UPDATE newsletter_subscribers SET is_active = FALSE WHERE email = ?", [$email]);
                    $message = "You have been successfully unsubscribed from our newsletter. We're sorry to see you go!";
                } else {
                    $message = "This email address is already unsubscribed from our newsletter.";
                }
            } else {
                $message = "This email address was not found in our subscriber list.";
            }
        } catch (Exception $e) {
            error_log("Unsubscribe error: " . $e->getMessage());
            $message = "There was an error processing your request. Please try again later.";
        }
    } else {
        $message = "Please provide a valid email address.";
    }
}

include 'components/header.php';
?>

<section class="page-header">
    <div class="container">
        <h1>Unsubscribe from Newsletter</h1>
        <p>We're sorry to see you go. You can unsubscribe from our email notifications below.</p>
    </div>
</section>

<section class="unsubscribe-section">
    <div class="container">
        <div class="unsubscribe-container">
            
            <?php if ($message): ?>
                <div class="alert <?php echo (strpos($message, 'successfully') !== false) ? 'alert-success' : 'alert-info'; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
                
                <?php if (strpos($message, 'successfully') !== false): ?>
                    <div class="success-actions">
                        <a href="<?php echo SITE_URL; ?>" class="btn btn-primary">Return to Homepage</a>
                        <a href="contact.php" class="btn btn-secondary">Contact Us</a>
                    </div>
                <?php endif; ?>
                
            <?php endif; ?>
            
            <?php if (!$message || strpos($message, 'successfully') === false): ?>
                <div class="unsubscribe-form">
                    <h3>Enter your email address to unsubscribe</h3>
                    <p>Enter the email address you want to unsubscribe from our newsletter notifications.</p>
                    
                    <form method="POST" action="">
                        <div class="form-group">
                            <label for="email">Email Address</label>
                            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Unsubscribe</button>
                        <a href="<?php echo SITE_URL; ?>" class="btn btn-secondary">Cancel</a>
                    </form>
                </div>
                
                <div class="alternative-options">
                    <h4>Alternative Options</h4>
                    <p>Instead of completely unsubscribing, you might want to:</p>
                    <ul>
                        <li><a href="contact.php">Contact us</a> to discuss your email preferences</li>
                        <li><a href="<?php echo SITE_URL; ?>">Visit our website</a> to stay updated without email notifications</li>
                        <li>Follow us on social media for occasional updates</li>
                    </ul>
                </div>
            <?php endif; ?>
            
        </div>
    </div>
</section>

<style>
.page-header {
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
    color: white;
    padding: 80px 0;
    text-align: center;
}

.page-header h1 {
    font-size: 2.5rem;
    margin-bottom: 10px;
}

.page-header p {
    font-size: 1.1rem;
    opacity: 0.9;
    margin: 0;
}

.unsubscribe-section {
    padding: 80px 0;
    background: #f8f9fa;
}

.unsubscribe-container {
    max-width: 600px;
    margin: 0 auto;
    background: white;
    padding: 40px;
    border-radius: 10px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.1);
}

.alert {
    padding: 15px 20px;
    border-radius: 8px;
    margin-bottom: 25px;
    border-left: 4px solid;
}

.alert-success {
    background: #d4edda;
    color: #155724;
    border-color: #28a745;
}

.alert-info {
    background: #cce7ff;
    color: #004085;
    border-color: #007bff;
}

.success-actions {
    text-align: center;
    margin-top: 30px;
}

.success-actions .btn {
    margin: 0 10px;
}

.unsubscribe-form h3 {
    color: var(--text-color);
    margin-bottom: 10px;
}

.unsubscribe-form p {
    color: #666;
    margin-bottom: 25px;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: var(--text-color);
}

.form-group input {
    width: 100%;
    padding: 12px 15px;
    border: 2px solid #ddd;
    border-radius: 5px;
    font-size: 1rem;
    transition: border-color 0.3s ease;
}

.form-group input:focus {
    outline: none;
    border-color: var(--primary-color);
}

.btn {
    display: inline-block;
    padding: 12px 25px;
    border: none;
    border-radius: 5px;
    font-weight: 600;
    text-decoration: none;
    cursor: pointer;
    transition: all 0.3s ease;
    margin-right: 10px;
}

.btn-primary {
    background: var(--primary-color);
    color: white;
}

.btn-primary:hover {
    background: var(--primary-dark);
    transform: translateY(-1px);
}

.btn-secondary {
    background: #6c757d;
    color: white;
}

.btn-secondary:hover {
    background: #545b62;
    color: white;
    text-decoration: none;
    transform: translateY(-1px);
}

.alternative-options {
    margin-top: 40px;
    padding-top: 30px;
    border-top: 1px solid #eee;
}

.alternative-options h4 {
    color: var(--text-color);
    margin-bottom: 10px;
}

.alternative-options p {
    color: #666;
    margin-bottom: 15px;
}

.alternative-options ul {
    color: #666;
    padding-left: 20px;
}

.alternative-options li {
    margin-bottom: 8px;
}

.alternative-options a {
    color: var(--primary-color);
    text-decoration: none;
}

.alternative-options a:hover {
    text-decoration: underline;
}

@media (max-width: 768px) {
    .page-header {
        padding: 60px 0;
    }
    
    .page-header h1 {
        font-size: 2rem;
    }
    
    .unsubscribe-container {
        margin: 0 20px;
        padding: 30px 20px;
    }
    
    .btn {
        display: block;
        width: 100%;
        margin: 10px 0;
        text-align: center;
    }
    
    .success-actions .btn {
        margin: 10px 0;
    }
}
</style>

<?php include 'components/footer.php'; ?>

