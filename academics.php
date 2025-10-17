<?php
$title = "Academics";
$page = "academics";
$subtitle = "Academic Excellence and Achievement";
include 'components/header.php';

// Include database connection
require_once 'includes/config.php';
require_once 'includes/Database.php';

// Create database connection
$db = new Database();



// Get recent awards ceremonies
$awards_ceremonies = $db->getRows("SELECT * FROM awards_ceremonies WHERE status = 'active' ORDER BY ceremony_date DESC LIMIT 6");

// Get current year and term (default to current year, term 4)
$current_year = date('Y');
$current_term = 4; // Default to term 4

// Override if year and term are provided in URL
if (isset($_GET['year']) && is_numeric($_GET['year'])) {
    $current_year = (int)$_GET['year'];
}
if (isset($_GET['term']) && in_array($_GET['term'], [1, 2, 3, 4])) {
    $current_term = (int)$_GET['term'];
}

// Check if top_achievers_per_term table exists, if not create it
try {
    $db->query("SELECT 1 FROM top_achievers_per_term LIMIT 1");
} catch (Exception $e) {
    // Table doesn't exist, create it
    $create_table_sql = "
    CREATE TABLE IF NOT EXISTS `top_achievers_per_term` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `student_name` varchar(255) NOT NULL,
      `grade` int(2) NOT NULL,
      `year` int(4) NOT NULL,
      `term` int(1) NOT NULL,
      `ranking` int(2) NOT NULL,
      `photo` varchar(255),
      `status` enum('active','inactive','deleted') DEFAULT 'active',
      `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
      `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`),
      INDEX `idx_grade_year_term` (`grade`, `year`, `term`),
      INDEX `idx_ranking` (`ranking`),
      INDEX `idx_status` (`status`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    
    $db->query($create_table_sql);
    
    // Insert sample data (just names, grades, years, terms, and rankings)
    $sample_data = [
        // Grade 8, 2024, Term 4
        ['Mandla Sithole', 8, 2024, 4, 1, null, 'active'],
        ['Nomthandazo Mbeki', 8, 2024, 4, 2, null, 'active'],
        ['Tshepo Maleka', 8, 2024, 4, 3, null, 'active'],
        ['Palesa Mokoena', 8, 2024, 4, 4, null, 'active'],
        ['Sipho Ndaba', 8, 2024, 4, 5, null, 'active'],
        ['Katlego Moroka', 8, 2024, 4, 6, null, 'active'],
        ['Thandi Cele', 8, 2024, 4, 7, null, 'active'],
        ['Lucky Mthembu', 8, 2024, 4, 8, null, 'active'],
        ['Precious Mahlangu', 8, 2024, 4, 9, null, 'active'],
        ['Bongani Zulu', 8, 2024, 4, 10, null, 'active'],
        
        // Grade 9, 2024, Term 4
        ['Lerato Mogale', 9, 2024, 4, 1, null, 'active'],
        ['Tebogo Maseko', 9, 2024, 4, 2, null, 'active'],
        ['Nomsa Dlamini', 9, 2024, 4, 3, null, 'active'],
        ['Kagiso Molefe', 9, 2024, 4, 4, null, 'active'],
        ['Thabo Nkomo', 9, 2024, 4, 5, null, 'active'],
        ['Zanele Khumalo', 9, 2024, 4, 6, null, 'active'],
        ['Sello Mabena', 9, 2024, 4, 7, null, 'active'],
        ['Busisiwe Radebe', 9, 2024, 4, 8, null, 'active'],
        ['Mpho Lekota', 9, 2024, 4, 9, null, 'active'],
        ['Ntombi Shabalala', 9, 2024, 4, 10, null, 'active'],
        
        // Grade 10, 2024, Term 4
        ['Thabo Mthembu', 10, 2024, 4, 1, null, 'active'],
        ['Nomsa Khumalo', 10, 2024, 4, 2, null, 'active'],
        ['Sipho Dlamini', 10, 2024, 4, 3, null, 'active'],
        ['Lerato Mogale', 10, 2024, 4, 4, null, 'active'],
        ['Kagiso Molefe', 10, 2024, 4, 5, null, 'active'],
        ['Palesa Mokoena', 10, 2024, 4, 6, null, 'active'],
        ['Tshepo Maleka', 10, 2024, 4, 7, null, 'active'],
        ['Nomthandazo Mbeki', 10, 2024, 4, 8, null, 'active'],
        ['Mandla Sithole', 10, 2024, 4, 9, null, 'active'],
        ['Katlego Moroka', 10, 2024, 4, 10, null, 'active'],
        
        // Grade 11, 2024, Term 4
        ['Amogelang Motaung', 11, 2024, 4, 1, null, 'active'],
        ['Boitumelo Ngwenya', 11, 2024, 4, 2, null, 'active'],
        ['Dineo Mahlangu', 11, 2024, 4, 3, null, 'active'],
        ['Fikile Mokoena', 11, 2024, 4, 4, null, 'active'],
        ['Goitsemang Mokone', 11, 2024, 4, 5, null, 'active'],
        ['Hlengiwe Zulu', 11, 2024, 4, 6, null, 'active'],
        ['Itumeleng Phiri', 11, 2024, 4, 7, null, 'active'],
        ['Jabulani Nkomo', 11, 2024, 4, 8, null, 'active'],
        ['Kgothatso Maseko', 11, 2024, 4, 9, null, 'active'],
        ['Lebohang Dlamini', 11, 2024, 4, 10, null, 'active'],
        
        // Grade 12, 2024, Term 4
        ['Mpumelelo Cele', 12, 2024, 4, 1, null, 'active'],
        ['Nompumelelo Shabalala', 12, 2024, 4, 2, null, 'active'],
        ['Onkgopotse Mogale', 12, 2024, 4, 3, null, 'active'],
        ['Phakamani Mthembu', 12, 2024, 4, 4, null, 'active'],
        ['Qondile Radebe', 12, 2024, 4, 5, null, 'active'],
        ['Refiloe Lekota', 12, 2024, 4, 6, null, 'active'],
        ['Siyabonga Khumalo', 12, 2024, 4, 7, null, 'active'],
        ['Tebello Molefe', 12, 2024, 4, 8, null, 'active'],
        ['Unathi Mbeki', 12, 2024, 4, 9, null, 'active'],
        ['Vuyiswa Moroka', 12, 2024, 4, 10, null, 'active']
    ];
    
    foreach ($sample_data as $student) {
        $insert_sql = "INSERT INTO `top_achievers_per_term` 
                      (`student_name`, `grade`, `year`, `term`, `ranking`, `photo`, `status`) 
                      VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        $db->query($insert_sql, $student);
    }
}

// Get top achievers per grade for the selected year and term
$top_achievers_grade_8 = $db->getRows("SELECT * FROM top_achievers_per_term WHERE grade = 8 AND year = $current_year AND term = $current_term AND status = 'active' ORDER BY ranking ASC LIMIT 10");
$top_achievers_grade_9 = $db->getRows("SELECT * FROM top_achievers_per_term WHERE grade = 9 AND year = $current_year AND term = $current_term AND status = 'active' ORDER BY ranking ASC LIMIT 10");
$top_achievers_grade_10 = $db->getRows("SELECT * FROM top_achievers_per_term WHERE grade = 10 AND year = $current_year AND term = $current_term AND status = 'active' ORDER BY ranking ASC LIMIT 10");
$top_achievers_grade_11 = $db->getRows("SELECT * FROM top_achievers_per_term WHERE grade = 11 AND year = $current_year AND term = $current_term AND status = 'active' ORDER BY ranking ASC LIMIT 10");
$top_achievers_grade_12 = $db->getRows("SELECT * FROM top_achievers_per_term WHERE grade = 12 AND year = $current_year AND term = $current_term AND status = 'active' ORDER BY ranking ASC LIMIT 10");

// Get available years and terms for filter dropdown
$available_years = $db->getRows("SELECT DISTINCT year FROM top_achievers_per_term WHERE status = 'active' ORDER BY year DESC");
$available_terms = [1, 2, 3, 4];

// Get academic statistics
$total_students = $db->getCount("SELECT COUNT(*) FROM students WHERE status = 'active'");
$honor_roll_students = $db->getCount("SELECT COUNT(*) FROM top_students WHERE status = 'active'");
$awards_count = $db->getCount("SELECT COUNT(*) FROM awards_ceremonies WHERE status = 'active'");
?>

<!-- Top 10 Per Grade Section -->
<section class="top-achievers-per-grade">
    <div class="container">
        <div class="section-header" data-aos="fade-up" data-aos-duration="600">
            <div class="section-subtitle">Academic Excellence</div>
            <h2>Top 10 Students Per Grade</h2>
            <p class="section-description">Recognizing the top performing students in each grade for <?php echo "Year $current_year, Term $current_term"; ?></p>
        </div>
        
        <!-- Year and Term Filter -->
        <div class="filters-container" data-aos="fade-up" data-aos-duration="600">
            <form method="GET" class="filter-form">
                <div class="filter-group">
                    <label for="year">Year:</label>
                    <select name="year" id="year" onchange="this.form.submit()">
                        <?php foreach ($available_years as $year_option): ?>
                            <option value="<?php echo $year_option['year']; ?>" <?php echo ($year_option['year'] == $current_year) ? 'selected' : ''; ?>>
                                <?php echo $year_option['year']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label for="term">Term:</label>
                    <select name="term" id="term" onchange="this.form.submit()">
                        <?php foreach ($available_terms as $term_option): ?>
                            <option value="<?php echo $term_option; ?>" <?php echo ($term_option == $current_term) ? 'selected' : ''; ?>>
                                Term <?php echo $term_option; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </form>
        </div>
        
        <!-- Grades Tabs -->
        <div class="grades-tabs" data-aos="fade-up" data-aos-duration="600">
            <button class="tab-button active" data-grade="8">Grade 8</button>
            <button class="tab-button" data-grade="9">Grade 9</button>
            <button class="tab-button" data-grade="10">Grade 10</button>
            <button class="tab-button" data-grade="11">Grade 11</button>
            <button class="tab-button" data-grade="12">Grade 12</button>
        </div>
        
        <!-- Grade 8 Tab Content -->
        <div class="tab-content active" id="grade-8">
            <div class="top-achievers-grid">
                <?php if (!empty($top_achievers_grade_8)): ?>
                    <?php foreach ($top_achievers_grade_8 as $index => $achiever): ?>
                        <div class="achiever-card" data-aos="fade-up" data-aos-duration="600" data-aos-delay="<?php echo $index * 50; ?>">
                            <div class="achiever-ranking">
                                <span class="rank-number"><?php echo $achiever['ranking']; ?></span>
                            </div>
                            
                            <div class="achiever-photo">
                                <img src="<?php echo $achiever['photo'] ?: 'images/default-student.jpg'; ?>" alt="<?php echo htmlspecialchars($achiever['student_name']); ?>">
                            </div>
                            
                            <div class="achiever-info">
                                <h3><?php echo htmlspecialchars($achiever['student_name']); ?></h3>
                                <p class="achiever-grade">Grade <?php echo $achiever['grade']; ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="no-data" data-aos="fade-up" data-aos-duration="600">
                        <i class="fas fa-user-graduate"></i>
                        <h3>No Top Achievers Data</h3>
                        <p>Grade 8 top achievers data for Year <?php echo $current_year; ?>, Term <?php echo $current_term; ?> will be updated soon.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Grade 9 Tab Content -->
        <div class="tab-content" id="grade-9">
            <div class="top-achievers-grid">
                <?php if (!empty($top_achievers_grade_9)): ?>
                    <?php foreach ($top_achievers_grade_9 as $index => $achiever): ?>
                        <div class="achiever-card" data-aos="fade-up" data-aos-duration="600" data-aos-delay="<?php echo $index * 50; ?>">
                            <div class="achiever-ranking">
                                <span class="rank-number"><?php echo $achiever['ranking']; ?></span>
                            </div>
                            
                            <div class="achiever-photo">
                                <img src="<?php echo $achiever['photo'] ?: 'images/default-student.jpg'; ?>" alt="<?php echo htmlspecialchars($achiever['student_name']); ?>">
                            </div>
                            
                            <div class="achiever-info">
                                <h3><?php echo htmlspecialchars($achiever['student_name']); ?></h3>
                                <p class="achiever-grade">Grade <?php echo $achiever['grade']; ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="no-data" data-aos="fade-up" data-aos-duration="600">
                        <i class="fas fa-user-graduate"></i>
                        <h3>No Top Achievers Data</h3>
                        <p>Grade 9 top achievers data for Year <?php echo $current_year; ?>, Term <?php echo $current_term; ?> will be updated soon.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Grade 10 Tab Content -->
        <div class="tab-content" id="grade-10">
            <div class="top-achievers-grid">
                <?php if (!empty($top_achievers_grade_10)): ?>
                    <?php foreach ($top_achievers_grade_10 as $index => $achiever): ?>
                        <div class="achiever-card" data-aos="fade-up" data-aos-duration="600" data-aos-delay="<?php echo $index * 50; ?>">
                            <div class="achiever-ranking">
                                <span class="rank-number"><?php echo $achiever['ranking']; ?></span>
                            </div>
                            
                            <div class="achiever-photo">
                                <img src="<?php echo $achiever['photo'] ?: 'images/default-student.jpg'; ?>" alt="<?php echo htmlspecialchars($achiever['student_name']); ?>">
                            </div>
                            
                            <div class="achiever-info">
                                <h3><?php echo htmlspecialchars($achiever['student_name']); ?></h3>
                                <p class="achiever-grade">Grade <?php echo $achiever['grade']; ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="no-data" data-aos="fade-up" data-aos-duration="600">
                        <i class="fas fa-user-graduate"></i>
                        <h3>No Top Achievers Data</h3>
                        <p>Grade 10 top achievers data for Year <?php echo $current_year; ?>, Term <?php echo $current_term; ?> will be updated soon.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Grade 11 Tab Content -->
        <div class="tab-content" id="grade-11">
            <div class="top-achievers-grid">
                <?php if (!empty($top_achievers_grade_11)): ?>
                    <?php foreach ($top_achievers_grade_11 as $index => $achiever): ?>
                        <div class="achiever-card" data-aos="fade-up" data-aos-duration="600" data-aos-delay="<?php echo $index * 50; ?>">
                            <div class="achiever-ranking">
                                <span class="rank-number"><?php echo $achiever['ranking']; ?></span>
                            </div>
                            
                            <div class="achiever-photo">
                                <img src="<?php echo $achiever['photo'] ?: 'images/default-student.jpg'; ?>" alt="<?php echo htmlspecialchars($achiever['student_name']); ?>">
                            </div>
                            
                            <div class="achiever-info">
                                <h3><?php echo htmlspecialchars($achiever['student_name']); ?></h3>
                                <p class="achiever-grade">Grade <?php echo $achiever['grade']; ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="no-data" data-aos="fade-up" data-aos-duration="600">
                        <i class="fas fa-user-graduate"></i>
                        <h3>No Top Achievers Data</h3>
                        <p>Grade 11 top achievers data for Year <?php echo $current_year; ?>, Term <?php echo $current_term; ?> will be updated soon.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Grade 12 Tab Content -->
        <div class="tab-content" id="grade-12">
            <div class="top-achievers-grid">
                <?php if (!empty($top_achievers_grade_12)): ?>
                    <?php foreach ($top_achievers_grade_12 as $index => $achiever): ?>
                        <div class="achiever-card" data-aos="fade-up" data-aos-duration="600" data-aos-delay="<?php echo $index * 50; ?>">
                            <div class="achiever-ranking">
                                <span class="rank-number"><?php echo $achiever['ranking']; ?></span>
                            </div>
                            
                            <div class="achiever-photo">
                                <img src="<?php echo $achiever['photo'] ?: 'images/default-student.jpg'; ?>" alt="<?php echo htmlspecialchars($achiever['student_name']); ?>">
                            </div>
                            
                            <div class="achiever-info">
                                <h3><?php echo htmlspecialchars($achiever['student_name']); ?></h3>
                                <p class="achiever-grade">Grade <?php echo $achiever['grade']; ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="no-data" data-aos="fade-up" data-aos-duration="600">
                        <i class="fas fa-user-graduate"></i>
                        <h3>No Top Achievers Data</h3>
                        <p>Grade 12 top achievers data for Year <?php echo $current_year; ?>, Term <?php echo $current_term; ?> will be updated soon.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- Awards Ceremonies Section -->
<section class="awards-ceremonies">
    <div class="container">
        <div class="section-header" data-aos="fade-up" data-aos-duration="600">
            <div class="section-subtitle">Recognition Events</div>
            <h2>Awards Ceremonies</h2>
            <p class="section-description">Celebrating achievements and recognizing excellence in our school community</p>
        </div>
        
        <div class="ceremonies-grid">
            <?php if (!empty($awards_ceremonies)): ?>
                <?php foreach ($awards_ceremonies as $index => $ceremony): ?>
                    <div class="ceremony-card" data-aos="fade-up" data-aos-duration="600" data-aos-delay="<?php echo $index * 100; ?>">
                        <div class="ceremony-image">
                            <img src="<?php echo $ceremony['image'] ?: 'images/default-ceremony.jpg'; ?>" alt="<?php echo htmlspecialchars($ceremony['ceremony_name']); ?>">
                            <div class="ceremony-date">
                                <span class="day"><?php echo date('d', strtotime($ceremony['ceremony_date'])); ?></span>
                                <span class="month"><?php echo date('M', strtotime($ceremony['ceremony_date'])); ?></span>
                                <span class="year"><?php echo date('Y', strtotime($ceremony['ceremony_date'])); ?></span>
                            </div>
                        </div>
                        
                        <div class="ceremony-content">
                            <h3><?php echo htmlspecialchars($ceremony['ceremony_name']); ?></h3>
                            <p class="ceremony-description"><?php echo htmlspecialchars($ceremony['description']); ?></p>
                            <div class="ceremony-details">
                                <div class="detail-item">
                                    <i class="fas fa-trophy"></i>
                                    <span><?php echo $ceremony['awards_given']; ?> Awards Given</span>
                                </div>
                                <div class="detail-item">
                                    <i class="fas fa-users"></i>
                                    <span><?php echo $ceremony['students_honored']; ?> Students Honored</span>
                                </div>
                                <div class="detail-item">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <span><?php echo htmlspecialchars($ceremony['venue']); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-data" data-aos="fade-up" data-aos-duration="600">
                    <i class="fas fa-trophy"></i>
                    <h3>No Awards Ceremonies</h3>
                    <p>Awards ceremony information will be updated soon.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Academic Programs Section -->
<section class="academic-programs">
    <div class="container">
        <div class="section-header" data-aos="fade-up" data-aos-duration="600">
            <div class="section-subtitle">Education Excellence</div>
            <h2>Academic Programs</h2>
            <p class="section-description">Comprehensive educational programs designed to foster academic excellence and personal growth</p>
        </div>
        
        <div class="programs-grid">
            <div class="program-card" data-aos="fade-up" data-aos-duration="600">
                <div class="program-icon">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <h3>Grade 8-9 Foundation</h3>
                <p>Building strong academic foundations with comprehensive subjects including Mathematics, Science, Languages, and Social Studies.</p>
                <ul>
                    <li>Core subject mastery</li>
                    <li>Study skills development</li>
                    <li>Academic support programs</li>
                </ul>
            </div>
            
            <div class="program-card" data-aos="fade-up" data-aos-duration="600" data-aos-delay="100">
                <div class="program-icon">
                    <i class="fas fa-flask"></i>
                </div>
                <h3>Grade 10-12 Specialization</h3>
                <p>Advanced academic programs preparing students for tertiary education with specialized subject choices and career guidance.</p>
                <ul>
                    <li>Subject specialization</li>
                    <li>University preparation</li>
                    <li>Career guidance counseling</li>
                </ul>
            </div>
            
            <div class="program-card" data-aos="fade-up" data-aos-duration="600" data-aos-delay="200">
                <div class="program-icon">
                    <i class="fas fa-trophy"></i>
                </div>
                <h3>Academic Excellence Program</h3>
                <p>Special program for high-achieving students with advanced coursework, research projects, and enrichment activities.</p>
                <ul>
                    <li>Advanced placement courses</li>
                    <li>Research opportunities</li>
                    <li>Leadership development</li>
                </ul>
            </div>
            
            <div class="program-card" data-aos="fade-up" data-aos-duration="600" data-aos-delay="300">
                <div class="program-icon">
                    <i class="fas fa-hands-helping"></i>
                </div>
                <h3>Academic Support Services</h3>
                <p>Comprehensive support services including tutoring, study groups, and individualized learning plans for all students.</p>
                <ul>
                    <li>Peer tutoring programs</li>
                    <li>Study skills workshops</li>
                    <li>Individual learning support</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<script>
// Animated counter for statistics
function animateCounters() {
    const counters = document.querySelectorAll('.stat-number');
    
    counters.forEach(counter => {
        const target = parseInt(counter.getAttribute('data-target'));
        const increment = target / 50;
        let current = 0;
        
        const updateCounter = () => {
            if (current < target) {
                current += increment;
                counter.textContent = Math.floor(current);
                setTimeout(updateCounter, 40);
            } else {
                counter.textContent = target;
            }
        };
        
        updateCounter();
    });
}

// Trigger animation when stats section is in view
const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            animateCounters();
            observer.unobserve(entry.target);
        }
    });
});

// Tab functionality for grade selection
function initializeTabs() {
    const tabButtons = document.querySelectorAll('.tab-button');
    const tabContents = document.querySelectorAll('.tab-content');
    
    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            const targetGrade = this.getAttribute('data-grade');
            
            // Remove active class from all buttons and contents
            tabButtons.forEach(btn => btn.classList.remove('active'));
            tabContents.forEach(content => content.classList.remove('active'));
            
            // Add active class to clicked button and corresponding content
            this.classList.add('active');
            document.getElementById('grade-' + targetGrade).classList.add('active');
        });
    });
}

document.addEventListener('DOMContentLoaded', function() {
    const statsSection = document.querySelector('.academic-stats');
    if (statsSection) {
        observer.observe(statsSection);
    }
    
    // Initialize tabs
    initializeTabs();
});
</script>

<style>
/* Grade Tabs Styling */
.grades-tabs {
    display: flex;
    justify-content: center;
    gap: 10px;
    margin-bottom: 30px;
    background: white;
    padding: 10px;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.tab-button {
    padding: 12px 24px;
    border: 2px solid #e0e0e0;
    background: white;
    color: #666;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 600;
    transition: all 0.3s ease;
    font-size: 14px;
}

.tab-button:hover {
    border-color: #d72f25;
    color: #d72f25;
    transform: translateY(-2px);
}

.tab-button.active {
    background: #d72f25;
    border-color: #d72f25;
    color: white;
    box-shadow: 0 4px 15px rgba(215, 47, 37, 0.3);
}

/* Tab Content Styling */
.tab-content {
    display: none;
}

.tab-content.active {
    display: block;
}

/* Top Achievers Grid */
.top-achievers-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
    margin-top: 20px;
}

.achiever-card {
    background: white;
    border-radius: 12px;
    padding: 0;
    display: flex;
    flex-direction: column;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
    height: 300px;
}

.achiever-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.achiever-ranking {
    position: absolute;
    top: 15px;
    right: 15px;
    width: 40px;
    height: 40px;
    background: #d72f25;
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 14px;
    z-index: 2;
    box-shadow: 0 2px 8px rgba(0,0,0,0.2);
}

.achiever-ranking .rank-number {
    font-size: 14px;
    font-weight: 700;
}

.achiever-photo {
    width: 100%;
    height: 70%;
    border-radius: 12px 12px 0 0;
    overflow: hidden;
    flex-shrink: 0;
    position: relative;
}

.achiever-photo img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}

.achiever-info {
    height: 30%;
    text-align: center;
    padding: 15px;
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.achiever-info h3 {
    margin: 0 0 8px;
    color: #333;
    font-size: 16px;
    font-weight: 600;
    line-height: 1.2;
}

.achiever-info .achiever-grade {
    color: #d72f25;
    font-size: 14px;
    font-weight: 500;
    margin: 0;
    background: rgba(215, 47, 37, 0.1);
    padding: 6px 10px;
    border-radius: 4px;
    display: inline-block;
    align-self: center;
}

/* Filters Container */
.filters-container {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 10px;
    margin-bottom: 30px;
}

.filter-form {
    display: flex;
    gap: 30px;
    align-items: center;
    justify-content: center;
    flex-wrap: wrap;
}

.filter-group {
    display: flex;
    align-items: center;
    gap: 10px;
    background: white;
    padding: 10px 15px;
    border-radius: 8px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}

.filter-group label {
    font-weight: 600;
    color: #333;
    font-size: 14px;
    white-space: nowrap;
}

.filter-group select {
    padding: 8px 12px;
    border: 1px solid #ddd;
    border-radius: 4px;
    background: white;
    font-size: 14px;
    min-width: 100px;
}

/* No Data Styling */
.no-data {
    text-align: center;
    padding: 60px 20px;
    color: #666;
}

.no-data i {
    font-size: 4rem;
    color: #ddd;
    margin-bottom: 20px;
}

.no-data h3 {
    margin: 0 0 10px;
    color: #999;
}

.no-data p {
    margin: 0;
    color: #bbb;
}

/* Responsive Design */
@media (max-width: 768px) {
    .grades-tabs {
        flex-wrap: wrap;
        gap: 8px;
    }
    
    .tab-button {
        padding: 10px 16px;
        font-size: 13px;
    }
    
    .top-achievers-grid {
        grid-template-columns: 1fr;
        gap: 15px;
    }
}

@media (max-width: 1024px) and (min-width: 769px) {
    .top-achievers-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 18px;
    }
}
    
    .achiever-card {
        height: 250px;
    }
    
    .achiever-photo {
        height: 65%;
    }
    
    .achiever-info {
        height: 35%;
        padding: 12px;
    }
    
    .achiever-info h3 {
        font-size: 14px;
        margin-bottom: 6px;
    }
    
    .achiever-info .achiever-grade {
        font-size: 12px;
        padding: 5px 8px;
    }
    
    .achiever-ranking {
        width: 35px;
        height: 35px;
        font-size: 12px;
        top: 10px;
        right: 10px;
    }
    
    .achiever-ranking .rank-number {
        font-size: 12px;
    }
    
    .filter-form {
        gap: 15px;
        justify-content: center;
    }
    
    .filter-group {
        padding: 8px 12px;
    }
    
    .filter-group label {
        font-size: 13px;
    }
    
    .filter-group select {
        min-width: 80px;
        font-size: 13px;
        padding: 6px 10px;
    }
}
</style>

<?php include 'components/newsletter.php'; ?>

<?php include 'components/footer.php'; ?> 