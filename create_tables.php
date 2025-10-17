<?php
// Database configuration
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'matamela_db';

// Connect to database
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "Connected to database successfully.<br>";

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
)";

if ($conn->query($testimonials_sql) === TRUE) {
    echo "Testimonials table created successfully.<br>";
} else {
    echo "Error creating testimonials table: " . $conn->error . "<br>";
}

// Insert sample testimonials
$testimonials_data = "
INSERT INTO testimonials (name, position, content, image, rating, status, display_order) VALUES
('John Smith', 'Grade 12 Student', 'Matamela School has provided me with an excellent education and countless opportunities to grow. The teachers are supportive and the facilities are top-notch.', 'images/categories/students/student_group_01.jpg', 5, 'active', 1),
('Sarah Johnson', 'Parent', 'As a parent, I\'ve been impressed by the dedication of the teachers and staff at Matamela School. My child has thrived academically and socially since enrolling here.', 'images/categories/staff/staff_group_01.jpg', 5, 'active', 2),
('Emily Davis', 'Grade 10 Student', 'The extracurricular activities at Matamela School have helped me discover my passion for music and sports. I\'ve made great friends and learned valuable skills.', 'images/categories/students/student_performance_01.jpg', 4, 'active', 3)";

if ($conn->query($testimonials_data) === TRUE) {
    echo "Sample testimonials inserted successfully.<br>";
} else {
    echo "Error inserting testimonials: " . $conn->error . "<br>";
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
)";

if ($conn->query($achievements_sql) === TRUE) {
    echo "Achievements table created successfully.<br>";
} else {
    echo "Error creating achievements table: " . $conn->error . "<br>";
}

// Insert sample achievements
$achievements_data = "
INSERT INTO achievements (title, description, icon, status, display_order) VALUES
('National Science Competition Winners', 'Our students secured first place in the National Science Competition for three consecutive years.', 'medal', 'active', 1),
('Regional Sports Champions', 'Our school teams have won multiple championships in football, basketball, and athletics.', 'trophy', 'active', 2),
('Excellence in Education Award', 'Recognized for outstanding educational practices and student performance outcomes.', 'award', 'active', 3),
('100% University Acceptance Rate', 'All our graduates have been accepted into prestigious universities nationwide.', 'star', 'active', 4)";

if ($conn->query($achievements_data) === TRUE) {
    echo "Sample achievements inserted successfully.<br>";
} else {
    echo "Error inserting achievements: " . $conn->error . "<br>";
}

// Close connection
$conn->close();

echo "<p>Tables creation completed. <a href='index.php'>Go to homepage</a></p>";
?> 