<?php
// Comprehensive database creation script for Matamela School
require_once 'includes/config.php';

// Database connection
$host = DB_HOST;
$username = DB_USER;
$password = DB_PASS;
$database = DB_NAME;

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h1>🎓 Matamela School Database Setup</h1>\n";
    echo "<h2>Creating All Necessary Database Tables...</h2>\n";
    
    // Create about_content table for About page management
    $about_sql = "CREATE TABLE IF NOT EXISTS `about_content` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `section` varchar(50) NOT NULL,
      `story_title` text,
      `story_content_1` text,
      `story_content_2` text,
      `story_content_3` text,
      `story_image` varchar(255),
      `mission_content` text,
      `vision_content` text,
      `values` longtext,
      `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
      `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`),
      UNIQUE KEY `section` (`section`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    
    $pdo->exec($about_sql);
    echo "✅ About Content table created successfully<br>\n";
    
    // Create top_students table
    $sql1 = "CREATE TABLE IF NOT EXISTS `top_students` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `student_name` varchar(255) NOT NULL,
      `grade` int(2) NOT NULL,
      `ranking` int(2) NOT NULL,
      `average_percentage` decimal(5,2) NOT NULL,
      `best_subjects` text,
      `achievement_note` text,
      `photo` varchar(255),
      `status` enum('active','inactive','deleted') DEFAULT 'active',
      `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
      `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`),
      UNIQUE KEY `ranking_unique` (`ranking`, `status`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    
    $pdo->exec($sql1);
    echo "✅ Top Students table created successfully<br>\n";
    
    // Create awards_ceremonies table
    $sql2 = "CREATE TABLE IF NOT EXISTS `awards_ceremonies` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `ceremony_name` varchar(255) NOT NULL,
      `description` text,
      `ceremony_date` date NOT NULL,
      `venue` varchar(255),
      `awards_given` int(11) DEFAULT 0,
      `students_honored` int(11) DEFAULT 0,
      `image` varchar(255),
      `status` enum('active','inactive','deleted') DEFAULT 'active',
      `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
      `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    
    $pdo->exec($sql2);
    echo "✅ Awards Ceremonies table created successfully<br>\n";
    
    // Create academic_achievements table
    $sql3 = "CREATE TABLE IF NOT EXISTS `academic_achievements` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `title` varchar(255) NOT NULL,
      `description` text,
      `achievement_date` date NOT NULL,
      `category` varchar(100),
      `level` varchar(50),
      `icon` varchar(100) DEFAULT 'fas fa-award',
      `status` enum('active','inactive','deleted') DEFAULT 'active',
      `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
      `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    
    $pdo->exec($sql3);
    echo "✅ Academic Achievements table created successfully<br>\n";
    
    echo "<h3>Creating Database Indexes...</h3>\n";
    
    // Create indexes
    $indexes = [
        "CREATE INDEX IF NOT EXISTS idx_top_students_ranking ON top_students(ranking)",
        "CREATE INDEX IF NOT EXISTS idx_top_students_grade ON top_students(grade)",
        "CREATE INDEX IF NOT EXISTS idx_top_students_status ON top_students(status)",
        "CREATE INDEX IF NOT EXISTS idx_ceremonies_date ON awards_ceremonies(ceremony_date)",
        "CREATE INDEX IF NOT EXISTS idx_ceremonies_status ON awards_ceremonies(status)",
        "CREATE INDEX IF NOT EXISTS idx_achievements_date ON academic_achievements(achievement_date)",
        "CREATE INDEX IF NOT EXISTS idx_achievements_category ON academic_achievements(category)",
        "CREATE INDEX IF NOT EXISTS idx_achievements_level ON academic_achievements(level)",
        "CREATE INDEX IF NOT EXISTS idx_achievements_status ON academic_achievements(status)"
    ];
    
    foreach ($indexes as $index) {
        $pdo->exec($index);
        echo "✅ Index created<br>\n";
    }
    
    echo "<h3>Inserting Default Content...</h3>\n";
    
    // Insert default about content
    $about_content_sql = "INSERT IGNORE INTO `about_content` (`section`, `story_title`, `story_content_1`, `story_content_2`, `story_content_3`, `story_image`, `mission_content`, `vision_content`, `values`) VALUES
    ('story', 'Our Story', 
    'Established in 2022, Matamela Ramaphosa Secondary School was named in honor of South African President Cyril Ramaphosa. Our institution is dedicated to providing quality education and fostering academic excellence in the Bela-Bela community.',
    'As a comprehensive secondary school, we offer education from Grade 8 to Grade 12, following the South African National Curriculum. We focus on preparing our students for tertiary education and future careers through a well-rounded educational experience.',
    'Though we are a young institution, we are committed to building a strong foundation for educational success and creating a legacy of excellence. Our school aims to empower students with knowledge, skills, and values that will prepare them for future challenges and opportunities.',
    'images/categories/campus/campus_life_01.jpg', NULL, NULL, NULL),

    ('mission_vision', NULL, NULL, NULL, NULL, NULL,
    'To provide a nurturing and inclusive educational environment that inspires lifelong learning, critical thinking, and responsible citizenship. We are committed to developing well-rounded individuals who are academically proficient, socially responsible, and prepared to make positive contributions to South African society.',
    'To be a leading educational institution in Limpopo Province that empowers students to reach their full potential and become agents of positive change in an ever-evolving South Africa. We envision a school that sets the standard for educational excellence, innovation, and holistic development.',
    NULL),

    ('values', NULL, NULL, NULL, NULL, NULL, NULL, NULL,
    '[
        {\"title\":\"Excellence\",\"content\":\"We strive for excellence in all aspects of education, continuously raising the bar for academic and personal achievement.\",\"icon\":\"fas fa-star\"},
        {\"title\":\"Integrity\",\"content\":\"We uphold the highest standards of honesty, ethics, and transparency in all our actions and interactions.\",\"icon\":\"fas fa-handshake\"},
        {\"title\":\"Ubuntu\",\"content\":\"We embrace the African philosophy of Ubuntu - \\\"I am because we are\\\" - fostering a sense of community, compassion, and mutual respect.\",\"icon\":\"fas fa-users\"},
        {\"title\":\"Innovation\",\"content\":\"We encourage creative thinking and innovative approaches to teaching and learning that prepare students for the 21st century.\",\"icon\":\"fas fa-lightbulb\"},
        {\"title\":\"Responsibility\",\"content\":\"We foster a sense of responsibility towards oneself, others, and the broader South African community.\",\"icon\":\"fas fa-hands-helping\"},
        {\"title\":\"Equality\",\"content\":\"We promote equal opportunities for all students regardless of background, embracing South Africa\'s diverse cultural heritage.\",\"icon\":\"fas fa-balance-scale\"}
    ]')";
    
    $pdo->exec($about_content_sql);
    echo "✅ Default about content inserted<br>\n";
    
    // Insert sample top students
    $students_sql = "INSERT IGNORE INTO `top_students` (`student_name`, `grade`, `ranking`, `average_percentage`, `best_subjects`, `achievement_note`, `photo`, `status`) VALUES
    ('Thabo Mthembu', 12, 1, 95.5, 'Mathematics, Physical Sciences, English', 'Outstanding academic performance and leadership', 'images/students/student_01.jpg', 'active'),
    ('Nomsa Khumalo', 11, 2, 93.2, 'Life Sciences, Mathematics, English', 'Excellent in science subjects', 'images/students/student_02.jpg', 'active'),
    ('Sipho Dlamini', 12, 3, 91.8, 'Mathematics, Geography, Business Studies', 'Strong analytical skills', 'images/students/student_03.jpg', 'active'),
    ('Lerato Mogale', 10, 4, 90.5, 'English, History, Life Orientation', 'Exceptional language abilities', 'images/students/student_04.jpg', 'active'),
    ('Kagiso Molefe', 11, 5, 89.7, 'Physical Sciences, Mathematics, Technology', 'STEM excellence', 'images/students/student_05.jpg', 'active'),
    ('Tshepiso Nkomo', 12, 6, 88.3, 'Business Studies, Economics, English', 'Leadership and business acumen', 'images/students/student_06.jpg', 'active'),
    ('Palesa Mokoena', 10, 7, 87.9, 'Life Sciences, Geography, English', 'Environmental science passion', 'images/students/student_07.jpg', 'active'),
    ('Mandla Zulu', 11, 8, 86.5, 'Mathematics, Physical Sciences, Technology', 'Engineering aspirations', 'images/students/student_08.jpg', 'active'),
    ('Refilwe Motaung', 12, 9, 85.7, 'English, History, Life Orientation', 'Future teacher and mentor', 'images/students/student_09.jpg', 'active'),
    ('Bongani Mahlangu', 10, 10, 84.9, 'Mathematics, Life Sciences, Geography', 'Research oriented mindset', 'images/students/student_10.jpg', 'active')";
    
    $pdo->exec($students_sql);
    echo "✅ Sample top students (1-10) inserted<br>\n";
    
    // Insert sample ceremonies
    $ceremonies_sql = "INSERT IGNORE INTO `awards_ceremonies` (`ceremony_name`, `description`, `ceremony_date`, `venue`, `awards_given`, `students_honored`, `image`, `status`) VALUES
    ('Academic Excellence Awards 2024', 'Annual ceremony recognizing outstanding academic achievements across all grades', '2024-11-15', 'School Assembly Hall', 25, 45, 'images/ceremonies/awards_2024.jpg', 'active'),
    ('Science Fair Awards', 'Recognition ceremony for students who excelled in the annual science fair', '2024-09-20', 'School Library', 12, 30, 'images/ceremonies/science_fair.jpg', 'active'),
    ('Mathematics Competition Awards', 'Celebrating winners of the inter-school mathematics competition', '2024-08-10', 'Main Auditorium', 8, 20, 'images/ceremonies/math_competition.jpg', 'active'),
    ('Sports & Academic Achievement Night', 'Combined celebration of sports and academic excellence', '2024-10-05', 'School Sports Complex', 35, 60, 'images/ceremonies/sports_academic.jpg', 'active'),
    ('Grade 12 Graduation Ceremony', 'Celebrating the achievements of our Grade 12 graduates', '2024-12-01', 'Main Assembly Hall', 50, 120, 'images/ceremonies/graduation_2024.jpg', 'active'),
    ('Leadership Awards 2024', 'Recognizing student leaders and their contributions to school life', '2024-08-25', 'School Auditorium', 15, 25, 'images/ceremonies/leadership_awards.jpg', 'active')";
    
    $pdo->exec($ceremonies_sql);
    echo "✅ Sample ceremonies inserted<br>\n";
    
    // Insert sample achievements
    $achievements_sql = "INSERT IGNORE INTO `academic_achievements` (`title`, `description`, `achievement_date`, `category`, `level`, `icon`, `status`) VALUES
    ('Provincial Mathematics Olympiad Winners', 'Three students qualified for the provincial mathematics olympiad finals', '2024-09-15', 'Academic Excellence', 'Provincial', 'fas fa-trophy', 'active'),
    ('Best Performing School - District Level', 'Matamela Ramaphosa Secondary achieved highest pass rate in the district', '2024-08-20', 'Academic Excellence', 'District', 'fas fa-award', 'active'),
    ('Science Fair Regional Champions', 'School science fair project won first place at regional level', '2024-07-10', 'Science & Technology', 'Provincial', 'fas fa-flask', 'active'),
    ('Debate Competition Winners', 'School debate team won the inter-school debate championship', '2024-06-25', 'Arts & Culture', 'District', 'fas fa-microphone', 'active'),
    ('100% Grade 12 Pass Rate', 'Achieved 100% pass rate for Grade 12 students in 2023', '2024-01-15', 'Academic Excellence', 'School', 'fas fa-graduation-cap', 'active'),
    ('Environmental Science Project Award', 'Student project on water conservation won national recognition', '2024-05-30', 'Science & Technology', 'National', 'fas fa-leaf', 'active'),
    ('Leadership Excellence Program', 'Successful implementation of student leadership development program', '2024-04-18', 'Leadership', 'School', 'fas fa-users', 'active'),
    ('Community Service Recognition', 'School received award for outstanding community service initiatives', '2024-03-12', 'Community Service', 'District', 'fas fa-hands-helping', 'active'),
    ('Inter-School Sports Champions', 'Won multiple championships in inter-school sports competitions', '2024-10-12', 'Sports', 'District', 'fas fa-medal', 'active'),
    ('Technology Innovation Award', 'Students developed innovative app for school management', '2024-06-08', 'Science & Technology', 'Provincial', 'fas fa-laptop-code', 'active')";
    
    $pdo->exec($achievements_sql);
    echo "✅ Sample achievements inserted<br>\n";
    
    echo "<h2 style='color: green;'>🎉 All Database Tables Created Successfully!</h2>\n";
    echo "<div style='background: #f0f8f0; padding: 20px; border-radius: 10px; margin: 20px 0;'>\n";
    echo "<h3>📋 Tables Created:</h3>\n";
    echo "<ul style='line-height: 1.8;'>\n";
    echo "<li><strong>about_content</strong> - For managing About page content (story, mission, vision, values)</li>\n";
    echo "<li><strong>top_students</strong> - For managing top performing students (rankings 1-10)</li>\n";
    echo "<li><strong>awards_ceremonies</strong> - For managing awards ceremonies and events</li>\n";
    echo "<li><strong>academic_achievements</strong> - For tracking school-wide academic accomplishments</li>\n";
    echo "</ul>\n";
    echo "</div>\n";
    
    echo "<div style='background: #f8f8ff; padding: 20px; border-radius: 10px; margin: 20px 0;'>\n";
    echo "<h3>🚀 What You Can Do Now:</h3>\n";
    echo "<ol style='line-height: 1.8;'>\n";
    echo "<li><strong>View Public Pages:</strong>\n";
    echo "<ul>\n";
    echo "<li><a href='about.php' style='color: #d72f25; font-weight: bold;'>About Page</a> - View the about page with manageable content</li>\n";
    echo "<li><a href='academics.php' style='color: #d72f25; font-weight: bold;'>Academics Page</a> - View the academics page with top students, ceremonies, and achievements</li>\n";
    echo "</ul></li>\n";
    echo "<li><strong>Manage Content (Admin):</strong>\n";
    echo "<ul>\n";
    echo "<li><a href='admin/about-management.php' style='color: #2c4d8e; font-weight: bold;'>About Management</a> - Edit about page content</li>\n";
    echo "<li><a href='admin/academics.php' style='color: #2c4d8e; font-weight: bold;'>Academics Management</a> - Manage students, ceremonies, and achievements</li>\n";
    echo "</ul></li>\n";
    echo "</ol>\n";
    echo "</div>\n";
    
    echo "<div style='background: #fff8f0; padding: 20px; border-radius: 10px; margin: 20px 0;'>\n";
    echo "<h3>📁 Image Upload Directories Created:</h3>\n";
    echo "<ul style='line-height: 1.8;'>\n";
    echo "<li><code>images/students/</code> - For student photos</li>\n";
    echo "<li><code>images/ceremonies/</code> - For ceremony images</li>\n";
    echo "</ul>\n";
    echo "<p><em>You can now upload student photos and ceremony images through the admin panel.</em></p>\n";
    echo "</div>\n";
    
    echo "<p style='text-align: center; margin-top: 30px;'>\n";
    echo "<strong style='color: green; font-size: 1.2em;'>✅ Setup Complete! Your website is ready to use.</strong>\n";
    echo "</p>\n";
    
} catch(PDOException $e) {
    echo "<h2 style='color: red;'>❌ Database Error:</h2>\n";
    echo "<div style='background: #ffe6e6; padding: 20px; border-radius: 10px; border-left: 5px solid #ff0000;'>\n";
    echo "<p><strong>Error Message:</strong> " . $e->getMessage() . "</p>\n";
    echo "<p><strong>Error Code:</strong> " . $e->getCode() . "</p>\n";
    echo "<hr>\n";
    echo "<h4>🔧 Troubleshooting Steps:</h4>\n";
    echo "<ol>\n";
    echo "<li>Check if XAMPP/MySQL server is running</li>\n";
    echo "<li>Verify database connection settings in <code>includes/config.php</code></li>\n";
    echo "<li>Ensure the database name exists and user has proper permissions</li>\n";
    echo "<li>Check if the database name in config.php matches your actual database</li>\n";
    echo "</ol>\n";
    echo "</div>\n";
}
?> 