<?php
/**
 * Database Installation Script
 * 
 * This script will create the database and tables for the Matamela School website.
 */

// Define database configuration
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'matamela_db';

// Define installation status
$installed = false;
$error = '';
$success = '';

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $db_host = $_POST['db_host'] ?? 'localhost';
    $db_user = $_POST['db_user'] ?? 'root';
    $db_pass = $_POST['db_pass'] ?? '';
    $db_name = $_POST['db_name'] ?? 'matamela_db';
    $admin_username = $_POST['admin_username'] ?? 'admin';
    $admin_password = $_POST['admin_password'] ?? '';
    $admin_email = $_POST['admin_email'] ?? '';
    
    // Validate form data
    if (empty($db_host) || empty($db_user) || empty($db_name)) {
        $error = 'Please fill in all required database fields.';
    } elseif (empty($admin_username) || empty($admin_password) || empty($admin_email)) {
        $error = 'Please fill in all required admin user fields.';
    } elseif (!filter_var($admin_email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } else {
        try {
            // Connect to MySQL server
            $conn = new mysqli($db_host, $db_user, $db_pass);
            
            if ($conn->connect_error) {
                throw new Exception("Connection failed: " . $conn->connect_error);
            }
            
            // Create database
            $sql = "CREATE DATABASE IF NOT EXISTS `$db_name`";
            if (!$conn->query($sql)) {
                throw new Exception("Error creating database: " . $conn->error);
            }
            
            // Select database
            $conn->select_db($db_name);
            
            // Read SQL file
            $sql_file = file_get_contents('database/matamela_db.sql');
            
            // Remove CREATE DATABASE statement from SQL file
            $sql_file = preg_replace('/CREATE DATABASE.*?;/s', '', $sql_file);
            $sql_file = preg_replace('/USE.*?;/s', '', $sql_file);
            
            // Remove default admin user insertion
            $sql_file = preg_replace('/INSERT INTO users \(username, password, email, full_name, role\).*?;/s', '', $sql_file);
            
            // Execute SQL statements
            $statements = explode(';', $sql_file);
            foreach ($statements as $statement) {
                $statement = trim($statement);
                if (!empty($statement)) {
                    if (!$conn->query($statement)) {
                        throw new Exception("Error executing SQL: " . $conn->error . "<br>Statement: " . $statement);
                    }
                }
            }
            
            // Read sliders SQL file
            if (file_exists('database/sliders.sql')) {
                $sliders_sql = file_get_contents('database/sliders.sql');
                $statements = explode(';', $sliders_sql);
                foreach ($statements as $statement) {
                    $statement = trim($statement);
                    if (!empty($statement)) {
                        if (!$conn->query($statement)) {
                            throw new Exception("Error executing SQL: " . $conn->error . "<br>Statement: " . $statement);
                        }
                    }
                }
            }
            
            // Create testimonials table
            $testimonials_sql = "
            CREATE TABLE IF NOT EXISTS testimonials (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(100) NOT NULL,
                position VARCHAR(100) NOT NULL,
                content TEXT NOT NULL,
                image VARCHAR(255),
                rating INT DEFAULT 5,
                status ENUM('active', 'inactive') DEFAULT 'active',
                display_order INT DEFAULT 0,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            );
            
            INSERT INTO testimonials (name, position, content, image, rating, status, display_order) VALUES
            ('John Smith', 'Grade 12 Student', 'Matamela School has provided me with an excellent education and countless opportunities to grow. The teachers are supportive and the facilities are top-notch.', 'images/categories/students/student_group_01.jpg', 5, 'active', 1),
            ('Sarah Johnson', 'Parent', 'As a parent, I''ve been impressed by the dedication of the teachers and staff at Matamela School. My child has thrived academically and socially since enrolling here.', 'images/categories/staff/staff_group_01.jpg', 5, 'active', 2),
            ('Emily Davis', 'Grade 10 Student', 'The extracurricular activities at Matamela School have helped me discover my passion for music and sports. I''ve made great friends and learned valuable skills.', 'images/categories/students/student_performance_01.jpg', 4, 'active', 3);
            ";
            
            $statements = explode(';', $testimonials_sql);
            foreach ($statements as $statement) {
                $statement = trim($statement);
                if (!empty($statement)) {
                    if (!$conn->query($statement)) {
                        throw new Exception("Error executing SQL: " . $conn->error . "<br>Statement: " . $statement);
                    }
                }
            }
            
            // Create achievements table
            $achievements_sql = "
            CREATE TABLE IF NOT EXISTS achievements (
                id INT AUTO_INCREMENT PRIMARY KEY,
                title VARCHAR(255) NOT NULL,
                description TEXT NOT NULL,
                icon VARCHAR(50) DEFAULT 'trophy',
                status ENUM('active', 'inactive') DEFAULT 'active',
                display_order INT DEFAULT 0,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            );
            
            INSERT INTO achievements (title, description, icon, status, display_order) VALUES
            ('National Science Competition Winners', 'Our students secured first place in the National Science Competition for three consecutive years.', 'medal', 'active', 1),
            ('Regional Sports Champions', 'Our school teams have won multiple championships in football, basketball, and athletics.', 'trophy', 'active', 2),
            ('Excellence in Education Award', 'Recognized for outstanding educational practices and student performance outcomes.', 'award', 'active', 3),
            ('100% University Acceptance Rate', 'All our graduates have been accepted into prestigious universities nationwide.', 'star', 'active', 4);
            ";
            
            $statements = explode(';', $achievements_sql);
            foreach ($statements as $statement) {
                $statement = trim($statement);
                if (!empty($statement)) {
                    if (!$conn->query($statement)) {
                        throw new Exception("Error executing SQL: " . $conn->error . "<br>Statement: " . $statement);
                    }
                }
            }
            
            // Create admin user
            $hashed_password = password_hash($admin_password, PASSWORD_DEFAULT);
            $admin_username = $conn->real_escape_string($admin_username);
            $admin_email = $conn->real_escape_string($admin_email);
            
            $sql = "INSERT INTO users (username, password, email, full_name, role) 
                    VALUES ('$admin_username', '$hashed_password', '$admin_email', 'Administrator', 'admin')";
            
            if (!$conn->query($sql)) {
                throw new Exception("Error creating admin user: " . $conn->error);
            }
            
            // Create config file
            $config_content = "<?php
/**
 * Database Configuration
 */
define('DB_HOST', '$db_host');
define('DB_USER', '$db_user');
define('DB_PASS', '$db_pass');
define('DB_NAME', '$db_name');

/**
 * Website Configuration
 */
define('SITE_URL', 'http://' . \$_SERVER['HTTP_HOST'] . dirname(\$_SERVER['PHP_SELF']));
define('ADMIN_URL', SITE_URL . '/admin');
define('UPLOAD_PATH', \$_SERVER['DOCUMENT_ROOT'] . dirname(\$_SERVER['PHP_SELF']) . '/uploads');
define('UPLOAD_URL', SITE_URL . '/uploads');

/**
 * Error Reporting
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);

/**
 * Session Configuration
 */
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
session_start();

/**
 * Database Connection
 */
function connect_db() {
    \$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if (\$conn->connect_error) {
        die(\"Connection failed: \" . \$conn->connect_error);
    }
    
    return \$conn;
}

/**
 * Get Settings
 */
function get_setting(\$setting_name) {
    \$conn = connect_db();
    \$setting_name = \$conn->real_escape_string(\$setting_name);
    
    \$sql = \"SELECT setting_value FROM settings WHERE setting_name = '\$setting_name'\";
    \$result = \$conn->query(\$sql);
    
    if (\$result && \$result->num_rows > 0) {
        \$row = \$result->fetch_assoc();
        \$conn->close();
        return \$row['setting_value'];
    }
    
    \$conn->close();
    return null;
}

/**
 * Update Setting
 */
function update_setting(\$setting_name, \$setting_value) {
    \$conn = connect_db();
    \$setting_name = \$conn->real_escape_string(\$setting_name);
    \$setting_value = \$conn->real_escape_string(\$setting_value);
    
    \$sql = \"UPDATE settings SET setting_value = '\$setting_value' WHERE setting_name = '\$setting_name'\";
    
    if (\$conn->query(\$sql) === TRUE) {
        \$conn->close();
        return true;
    }
    
    \$conn->close();
    return false;
}

/**
 * Sanitize Input
 */
function sanitize_input(\$data) {
    \$data = trim(\$data);
    \$data = stripslashes(\$data);
    \$data = htmlspecialchars(\$data);
    return \$data;
}

/**
 * Check if user is logged in
 */
function is_logged_in() {
    return isset(\$_SESSION['admin_logged_in']) && \$_SESSION['admin_logged_in'] === true;
}

/**
 * Redirect to URL
 */
function redirect(\$url) {
    header(\"Location: \$url\");
    exit;
}

/**
 * Get Current Page Name
 */
function get_current_page() {
    return basename(\$_SERVER['PHP_SELF'], '.php');
}

/**
 * Format Date
 */
function format_date(\$date, \$format = 'd M, Y') {
    return date(\$format, strtotime(\$date));
}

/**
 * Generate Random String
 */
function generate_random_string(\$length = 10) {
    \$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    \$random_string = '';
    
    for (\$i = 0; \$i < \$length; \$i++) {
        \$random_string .= \$characters[rand(0, strlen(\$characters) - 1)];
    }
    
    return \$random_string;
}

/**
 * Upload File
 */
function upload_file(\$file, \$directory = 'general') {
    \$target_dir = UPLOAD_PATH . '/' . \$directory . '/';
    
    // Create directory if it doesn't exist
    if (!file_exists(\$target_dir)) {
        mkdir(\$target_dir, 0777, true);
    }
    
    \$file_extension = strtolower(pathinfo(\$file['name'], PATHINFO_EXTENSION));
    \$new_filename = generate_random_string() . '_' . time() . '.' . \$file_extension;
    \$target_file = \$target_dir . \$new_filename;
    
    // Check if file is an actual image
    if (\$directory == 'images' || \$directory == 'gallery') {
        \$check = getimagesize(\$file['tmp_name']);
        if (\$check === false) {
            return [
                'success' => false,
                'message' => 'File is not an image.'
            ];
        }
    }
    
    // Check file size (limit to 5MB)
    if (\$file['size'] > 5000000) {
        return [
            'success' => false,
            'message' => 'File is too large. Maximum size is 5MB.'
        ];
    }
    
    // Allow certain file formats
    if (\$directory == 'images' || \$directory == 'gallery') {
        \$allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array(\$file_extension, \$allowed_extensions)) {
            return [
                'success' => false,
                'message' => 'Only JPG, JPEG, PNG & GIF files are allowed.'
            ];
        }
    } elseif (\$directory == 'documents') {
        \$allowed_extensions = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx'];
        if (!in_array(\$file_extension, \$allowed_extensions)) {
            return [
                'success' => false,
                'message' => 'Only PDF, DOC, DOCX, XLS, XLSX, PPT & PPTX files are allowed.'
            ];
        }
    }
    
    // Upload file
    if (move_uploaded_file(\$file['tmp_name'], \$target_file)) {
        return [
            'success' => true,
            'file_name' => \$new_filename,
            'file_path' => \$target_file,
            'file_url' => UPLOAD_URL . '/' . \$directory . '/' . \$new_filename
        ];
    } else {
        return [
            'success' => false,
            'message' => 'There was an error uploading your file.'
        ];
    }
}

/**
 * Display Alert Message
 */
function display_alert(\$message, \$type = 'success') {
    return '<div class=\"alert alert-' . \$type . '\">' . \$message . '</div>';
}

/**
 * Log Activity
 */
function log_activity(\$action, \$details = '') {
    \$conn = connect_db();
    \$user_id = isset(\$_SESSION['admin_id']) ? \$_SESSION['admin_id'] : 0;
    \$action = \$conn->real_escape_string(\$action);
    \$details = \$conn->real_escape_string(\$details);
    \$ip_address = \$_SERVER['REMOTE_ADDR'];
    
    \$sql = \"INSERT INTO activity_log (user_id, action, details, ip_address) 
            VALUES ('\$user_id', '\$action', '\$details', '\$ip_address')\";
    
    \$conn->query(\$sql);
    \$conn->close();
}

/**
 * Create Activity Log Table if it doesn't exist
 */
function create_activity_log_table() {
    \$conn = connect_db();
    
    \$sql = \"CREATE TABLE IF NOT EXISTS activity_log (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        action VARCHAR(255) NOT NULL,
        details TEXT,
        ip_address VARCHAR(45) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )\";
    
    \$conn->query(\$sql);
    \$conn->close();
}

// Create activity log table
create_activity_log_table();";
            
            // Create includes directory if it doesn't exist
            if (!file_exists('includes')) {
                mkdir('includes', 0777, true);
            }
            
            // Write config file
            if (!file_put_contents('includes/config.php', $config_content)) {
                throw new Exception("Error creating config file. Please check file permissions.");
            }
            
            // Create uploads directory
            if (!file_exists('uploads')) {
                mkdir('uploads', 0777, true);
                mkdir('uploads/images', 0777, true);
                mkdir('uploads/gallery', 0777, true);
                mkdir('uploads/documents', 0777, true);
            }
            
            // Create .htaccess file to protect includes directory
            $htaccess_content = "Deny from all";
            file_put_contents('includes/.htaccess', $htaccess_content);
            
            // Close connection
            $conn->close();
            
            // Set success message
            $success = "Installation completed successfully! You can now <a href='admin/login.php'>login</a> to the admin panel.";
            $installed = true;
            
            // Create installation lock file
            file_put_contents('install.lock', date('Y-m-d H:i:s'));
        } catch (Exception $e) {
            $error = $e->getMessage();
        }
    }
}

// Check if already installed
if (file_exists('install.lock')) {
    $error = "The application is already installed. If you want to reinstall, please delete the 'install.lock' file.";
    $installed = true;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Matamela School - Installation</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .install-container {
            max-width: 800px;
            margin: 50px auto;
            background: #fff;
            padding: 30px;
            border-radius: 5px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
        }
        
        .install-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .install-header h1 {
            color: var(--primary-color);
        }
        
        .form-section {
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid var(--border-color);
        }
        
        .form-section h3 {
            margin-bottom: 20px;
            color: var(--primary-color);
        }
        
        .install-footer {
            text-align: center;
            margin-top: 30px;
        }
        
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        
        .alert-success {
            background-color: rgba(76, 175, 80, 0.1);
            color: var(--success-color);
            border: 1px solid var(--success-color);
        }
        
        .alert-danger {
            background-color: rgba(220, 53, 69, 0.1);
            color: var(--danger-color);
            border: 1px solid var(--danger-color);
        }
    </style>
</head>
<body>
    <div class="install-container">
        <div class="install-header">
            <h1>Matamela School - Installation</h1>
            <p>This wizard will guide you through the installation process.</p>
        </div>
        
        <?php if ($error): ?>
            <div class="alert alert-danger">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success">
                <?php echo $success; ?>
            </div>
        <?php endif; ?>
        
        <?php if (!$installed): ?>
        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
            <div class="form-section">
                <h3>Database Configuration</h3>
                
                <div class="form-group">
                    <label for="db_host">Database Host</label>
                    <input type="text" id="db_host" name="db_host" value="<?php echo $db_host; ?>" required>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="db_user">Database Username</label>
                        <input type="text" id="db_user" name="db_user" value="<?php echo $db_user; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="db_pass">Database Password</label>
                        <input type="password" id="db_pass" name="db_pass" value="<?php echo $db_pass; ?>">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="db_name">Database Name</label>
                    <input type="text" id="db_name" name="db_name" value="<?php echo $db_name; ?>" required>
                </div>
            </div>
            
            <div class="form-section">
                <h3>Admin User</h3>
                
                <div class="form-group">
                    <label for="admin_username">Admin Username</label>
                    <input type="text" id="admin_username" name="admin_username" value="admin" required>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="admin_password">Admin Password</label>
                        <input type="password" id="admin_password" name="admin_password" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="admin_email">Admin Email</label>
                        <input type="email" id="admin_email" name="admin_email" required>
                    </div>
                </div>
            </div>
            
            <div class="install-footer">
                <button type="submit" class="btn">Install</button>
            </div>
        </form>
        <?php else: ?>
        <div class="install-footer">
            <a href="index.php" class="btn">Go to Homepage</a>
            <a href="admin/login.php" class="btn btn-secondary">Go to Admin Panel</a>
        </div>
        <?php endif; ?>
    </div>
</body>
</html> 