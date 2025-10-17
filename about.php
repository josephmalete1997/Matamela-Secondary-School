<?php
$title = "About Us";
$page = "about";
$subtitle = "Learn about our history, mission, and values";

// Include database connection
require_once 'includes/config.php';
require_once 'includes/Database.php';

// Get content from database
$db = new Database();

// Get story content
$story_data = $db->getRow("SELECT * FROM about_content WHERE section = 'story'");
if (!$story_data) {
    // Fallback to default content if database is empty
    $story_data = [
        'story_title' => 'Our Story',
        'story_content_1' => 'Established in 2022, Matamela Ramaphosa Secondary School was named in honor of South African President Cyril Ramaphosa. Our institution operates under the South African Department of Basic Education, dedicated to providing quality education and fostering academic excellence in the Bela-Bela community.',
        'story_content_2' => 'As a comprehensive secondary school, we offer education from Grade 8 to Grade 12, following the South African National Curriculum. We focus on preparing our students for tertiary education and future careers through a well-rounded educational experience that develops both academic skills and character.',
        'story_content_3' => 'Though we are a young institution, we are committed to building a strong foundation for educational success and creating a legacy of excellence. Our school aims to empower students with knowledge, skills, and values that will prepare them for future challenges and opportunities in an ever-evolving world.',
        'story_image' => 'images/categories/campus/campus_life_01.jpg'
    ];
}

// Get mission and vision content
$mission_vision_data = $db->getRow("SELECT * FROM about_content WHERE section = 'mission_vision'");
if (!$mission_vision_data) {
    // Fallback to default content
    $mission_vision_data = [
        'mission_content' => 'To provide a nurturing and inclusive educational environment that inspires lifelong learning, critical thinking, and responsible citizenship. We are committed to developing well-rounded individuals who are academically proficient, socially responsible, and prepared to make positive contributions to South African society.',
        'vision_content' => 'To be a leading educational institution in Limpopo Province that empowers students to reach their full potential and become agents of positive change in an ever-evolving South Africa. We envision a school that sets the standard for educational excellence, innovation, and holistic development.'
    ];
}

// Get values content
$values_data = $db->getRow("SELECT * FROM about_content WHERE section = 'values'");
$values = [];
if ($values_data && !empty($values_data['values'])) {
    $values = json_decode($values_data['values'], true) ?: [];
}

// Fallback values if database is empty or JSON decode fails
if (empty($values)) {
    $values = [
        ["title" => "Excellence", "content" => "We strive for excellence in all aspects of education, continuously raising the bar for academic and personal achievement.", "icon" => "fas fa-star"],
        ["title" => "Integrity", "content" => "We uphold the highest standards of honesty, ethics, and transparency in all our actions and interactions.", "icon" => "fas fa-handshake"],
        ["title" => "Ubuntu", "content" => "We embrace the African philosophy of Ubuntu - \"I am because we are\" - fostering a sense of community, compassion, and mutual respect.", "icon" => "fas fa-users"],
        ["title" => "Innovation", "content" => "We encourage creative thinking and innovative approaches to teaching and learning that prepare students for the 21st century.", "icon" => "fas fa-lightbulb"],
        ["title" => "Responsibility", "content" => "We foster a sense of responsibility towards oneself, others, and the broader South African community.", "icon" => "fas fa-hands-helping"],
        ["title" => "Equality", "content" => "We promote equal opportunities for all students regardless of background, embracing South Africa's diverse cultural heritage.", "icon" => "fas fa-balance-scale"]
    ];
}

// Get leadership team from database
try {
    $leadership_team = $db->getRows("SELECT * FROM leadership WHERE status = 'active' ORDER BY display_order ASC");
} catch (Exception $e) {
    $leadership_team = [];
}

include 'components/header.php';
?>


<section class="about-intro">
    <div class="container">
        <div class="about-content" data-aos="fade-up" data-aos-duration="600">
            <h2><?php echo htmlspecialchars($story_data['story_title']); ?></h2>
            <p><?php echo htmlspecialchars($story_data['story_content_1']); ?></p>
            <p><?php echo htmlspecialchars($story_data['story_content_2']); ?></p>
            <p><?php echo htmlspecialchars($story_data['story_content_3']); ?></p>
        </div>
        <div class="about-image" data-aos="fade-up" data-aos-duration="600">
            <img src="<?php echo htmlspecialchars($story_data['story_image']); ?>" alt="Matamela Ramaphosa Secondary School Building">
        </div>
    </div>
</section>

<section class="mission-vision">
    <div class="container">
        <div class="mission" data-aos="fade-up" data-aos-duration="600">
            <div class="icon">
                <i class="fas fa-bullseye"></i>
            </div>
            <h2>Our Mission</h2>
            <p><?php echo htmlspecialchars($mission_vision_data['mission_content']); ?></p>
        </div>
        <div class="vision" data-aos="fade-up" data-aos-duration="600">
            <div class="icon">
                <i class="fas fa-eye"></i>
            </div>
            <h2>Our Vision</h2>
            <p><?php echo htmlspecialchars($mission_vision_data['vision_content']); ?></p>
        </div>
    </div>
</section>

<section class="values">
    <div class="container">
        <h2 data-aos="fade-up" data-aos-duration="600">Our Core Values</h2>
        <div class="values-grid">
            <?php foreach ($values as $value): ?>
            <div class="value-item" data-aos="fade-up" data-aos-duration="600">
                <div class="icon">
                    <i class="<?php echo htmlspecialchars($value['icon']); ?>"></i>
                </div>
                <h3><?php echo htmlspecialchars($value['title']); ?></h3>
                <p><?php echo htmlspecialchars($value['content']); ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="leadership">
    <div class="container">
        <h2 data-aos="fade-up" data-aos-duration="600">Our Leadership Team</h2>
        <div class="leadership-grid">
            <?php if (!empty($leadership_team)): ?>
                <?php foreach ($leadership_team as $leader): ?>
                <div class="leader" data-aos="fade-up" data-aos-duration="600">
                    <div class="leader-image">
                        <img src="<?php echo htmlspecialchars($leader['image'] ?? 'images/categories/staff/teacher_01.jpg'); ?>" alt="<?php echo htmlspecialchars($leader['name']); ?>">
                    </div>
                    <h3><?php echo htmlspecialchars($leader['name']); ?></h3>
                    <p class="position"><?php echo htmlspecialchars($leader['role']); ?></p>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <!-- Fallback content if no leadership data in database -->
                <div class="leader" data-aos="fade-up" data-aos-duration="600">
                    <div class="leader-image">
                        <img src="images/categories/staff/principal_01.jpg" alt="School Principal">
                    </div>
                    <h3>Principal</h3>
                    <p class="position">School Head</p>
                </div>
                <div class="leader" data-aos="fade-up" data-aos-duration="600">
                    <div class="leader-image">
                        <img src="images/categories/staff/teacher_01.jpg" alt="Vice Principal">
                    </div>
                    <h3>Vice Principal</h3>
                    <p class="position">Academic Head</p>
                </div>
                <div class="leader" data-aos="fade-up" data-aos-duration="600">
                    <div class="leader-image">
                        <img src="images/categories/staff/staff_group_01.jpg" alt="Head of Administration">
                    </div>
                    <h3>Administrative Team</h3>
                    <p class="position">School Management</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php include 'components/newsletter.php'; ?>
<?php include 'components/footer.php'; ?> 