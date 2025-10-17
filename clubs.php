<?php
$title = "Clubs & Activities";
$page = "clubs";
$subtitle = "Discover and join our vibrant school clubs and extracurricular activities";

require_once 'includes/config.php';
require_once 'includes/Database.php';

// Initialize database connection
$db = new Database();

// Get filter parameters
$category_filter = isset($_GET['category']) ? $_GET['category'] : '';
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Build query for active clubs
$where_conditions = ["status = 'active'"];
$params = [];

if (!empty($category_filter) && $category_filter !== 'all') {
    $where_conditions[] = "category = ?";
    $params[] = ucfirst($category_filter);
}

if (!empty($search)) {
    $where_conditions[] = "(name LIKE ? OR description LIKE ? OR supervisor_name LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$where_clause = "WHERE " . implode(" AND ", $where_conditions);

// Get clubs from database
try {
    $clubs = $db->getRows("SELECT * FROM clubs $where_clause ORDER BY featured DESC, name ASC", $params);
    $total_clubs = $db->getRow("SELECT COUNT(*) as count FROM clubs WHERE status = 'active'")['count'];
    $featured_clubs = $db->getRow("SELECT COUNT(*) as count FROM clubs WHERE status = 'active' AND featured = 1")['count'];
} catch (Exception $e) {
    $clubs = [];
    $total_clubs = 0;
    $featured_clubs = 0;
}

// Helper function to get category icon
function getCategoryIcon($category) {
    $icons = [
        'Academic' => 'fas fa-book',
        'Sports' => 'fas fa-futbol',
        'Arts' => 'fas fa-palette',
        'Cultural' => 'fas fa-theater-masks',
        'Community' => 'fas fa-hands-helping',
        'Leadership' => 'fas fa-crown',
        'Technology' => 'fas fa-laptop-code'
    ];
    return $icons[$category] ?? 'fas fa-circle';
}

include 'components/header.php';
?>



<section class="clubs-filter">
    <div class="container">
        <div class="filter-tabs">
            <a href="?<?php echo !empty($search) ? 'search=' . urlencode($search) : ''; ?>" 
               class="filter-tab <?php echo empty($category_filter) ? 'active' : ''; ?>">All Clubs</a>
            <a href="?category=academic<?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>" 
               class="filter-tab <?php echo $category_filter == 'academic' ? 'active' : ''; ?>">Academic</a>
            <a href="?category=arts<?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>" 
               class="filter-tab <?php echo $category_filter == 'arts' ? 'active' : ''; ?>">Arts & Culture</a>
            <a href="?category=sports<?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>" 
               class="filter-tab <?php echo $category_filter == 'sports' ? 'active' : ''; ?>">Sports</a>
            <a href="?category=technology<?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>" 
               class="filter-tab <?php echo $category_filter == 'technology' ? 'active' : ''; ?>">Technology</a>
            <a href="?category=community<?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>" 
               class="filter-tab <?php echo $category_filter == 'community' ? 'active' : ''; ?>">Community Service</a>
            <a href="?category=leadership<?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>" 
               class="filter-tab <?php echo $category_filter == 'leadership' ? 'active' : ''; ?>">Leadership</a>
        </div>
    </div>
</section>

<section class="clubs-grid">
    <div class="container">
        <?php if (!empty($clubs)): ?>
            <div class="clubs-container">
                <?php foreach ($clubs as $index => $club): ?>
                <div class="club-card" data-category="<?php echo strtolower($club['category']); ?>">
                    <div class="club-image">
                        <?php if ($club['image']): ?>
                            <img src="<?php echo htmlspecialchars($club['image']); ?>" alt="<?php echo htmlspecialchars($club['name']); ?>">
                        <?php else: ?>
                            <!-- Use default image based on category -->
                            <?php
                            $default_images = [
                                'Academic' => 'images/categories/classroom/classroom_group_01.jpg',
                                'Sports' => 'images/categories/facilities/sports_field_01.jpg',
                                'Arts' => 'images/categories/classroom/art_class_01.jpg',
                                'Cultural' => 'images/categories/events/cultural_day_01.jpg',
                                'Community' => 'images/categories/campus/garden_area_01.jpg',
                                'Leadership' => 'images/categories/students/student_council_01.jpg',
                                'Technology' => 'images/categories/classroom/computer_lab_01.jpg'
                            ];
                            $default_image = $default_images[$club['category']] ?? 'images/categories/classroom/classroom_group_01.jpg';
                            ?>
                            <img src="<?php echo $default_image; ?>" alt="<?php echo htmlspecialchars($club['name']); ?>">
                        <?php endif; ?>
                        <div class="club-category"><?php echo htmlspecialchars($club['category']); ?></div>
                    </div>
                    
                    <div class="club-content">
                        <h3><?php echo htmlspecialchars($club['name']); ?></h3>
                        <p class="club-description"><?php echo htmlspecialchars($club['description']); ?></p>
                        
                        <div class="club-details">
                            <div class="detail-item">
                                <i class="fas fa-clock"></i>
                                <span><?php echo htmlspecialchars($club['meeting_day']); ?>s at <?php echo date('g:i A', strtotime($club['meeting_time'])); ?></span>
                            </div>
                            <div class="detail-item">
                                <i class="fas fa-map-marker-alt"></i>
                                <span><?php echo htmlspecialchars($club['meeting_location']); ?></span>
                            </div>
                            <div class="detail-item">
                                <i class="fas fa-user-tie"></i>
                                <span><?php echo htmlspecialchars($club['supervisor_name']); ?></span>
                            </div>
                            <div class="detail-item">
                                <i class="fas fa-users"></i>
                                <span><?php echo $club['current_members']; ?> members</span>
                            </div>
                        </div>
                        
                        <?php if ($club['benefits']): ?>
                        <div class="club-achievements">
                            <h4>About This Club:</h4>
                            <ul>
                                <li><?php echo htmlspecialchars($club['benefits']); ?></li>
                            </ul>
                        </div>
                        <?php endif; ?>
                        
                        <div class="club-activities">
                            <h4>Meeting Schedule:</h4>
                            <div class="activity-tags">
                                <span class="activity-tag"><?php echo htmlspecialchars($club['meeting_day']); ?>s</span>
                                <span class="activity-tag"><?php echo date('g:i A', strtotime($club['meeting_time'])); ?></span>
                                <span class="activity-tag"><?php echo htmlspecialchars($club['meeting_location']); ?></span>
                            </div>
                        </div>
                        
                        <div class="club-actions">
                            <?php if ($club['supervisor_email']): ?>
                                <button class="btn btn-primary join-club-btn" 
                                        onclick="window.location.href='mailto:<?php echo htmlspecialchars($club['supervisor_email']); ?>?subject=Interest in <?php echo urlencode($club['name']); ?>'">
                                    <i class="fas fa-envelope"></i> Contact Supervisor
                                </button>
                            <?php else: ?>
                                <button class="btn btn-primary join-club-btn" data-club="<?php echo htmlspecialchars($club['name']); ?>">
                                    <i class="fas fa-plus"></i> Join Club
                                </button>
                            <?php endif; ?>
                            <button class="btn btn-outline learn-more-btn" data-index="<?php echo $index; ?>">
                                <i class="fas fa-info-circle"></i> Learn More
                            </button>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="no-clubs-message">
                <h3>No clubs found</h3>
                <p>
                    <?php if (!empty($search) || !empty($category_filter)): ?>
                        No clubs match your current filters. Try adjusting your search criteria.
                    <?php else: ?>
                        There are currently no active clubs available.
                    <?php endif; ?>
                </p>
                <?php if (!empty($search) || !empty($category_filter)): ?>
                    <a href="clubs.php" class="btn btn-primary">View All Clubs</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
<!-- Club Details Modal -->
<div id="clubModal" class="club-modal">
    <div class="modal-content">
        <span class="modal-close">&times;</span>
        <div id="modalContent">
            <!-- Content will be populated by JavaScript -->
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Filter functionality
    const filterTabs = document.querySelectorAll('.filter-tab');
    const clubCards = document.querySelectorAll('.club-card');
    
    filterTabs.forEach(tab => {
        tab.addEventListener('click', (e) => {
            e.preventDefault();
            // The filtering is now handled by server-side PHP
            window.location.href = tab.href;
        });
    });
    
    // Join club functionality
    const joinButtons = document.querySelectorAll('.join-club-btn');
    joinButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            const clubName = btn.getAttribute('data-club');
            if (clubName) {
                alert(`Thank you for your interest in joining ${clubName}! Please contact the school office for registration details.`);
            }
        });
    });
    
    // Learn more functionality
    const learnMoreButtons = document.querySelectorAll('.learn-more-btn');
    const modal = document.getElementById('clubModal');
    const modalContent = document.getElementById('modalContent');
    const closeModal = document.querySelector('.modal-close');
    
    learnMoreButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            const clubName = btn.closest('.club-card').querySelector('h3').textContent;
            const clubDescription = btn.closest('.club-card').querySelector('.club-description').textContent;
            modalContent.innerHTML = `
                <h2>${clubName}</h2>
                <p>${clubDescription}</p>
                <p>For more detailed information about ${clubName}, including meeting schedules, requirements, and how to join, please contact our school office.</p>
                <p><strong>Next Steps:</strong></p>
                <ul>
                    <li>Visit the school office during regular hours</li>
                    <li>Speak with the club supervisor</li>
                    <li>Complete the membership form</li>
                    <li>Get parent/guardian approval if required</li>
                </ul>
                <div style="margin-top: 20px;">
                    <a href="contact.php" class="btn btn-primary">Contact School Office</a>
                </div>
            `;
            modal.style.display = 'block';
        });
    });
    
    // Close modal
    closeModal.addEventListener('click', () => {
        modal.style.display = 'none';
    });
    
    window.addEventListener('click', (e) => {
        if (e.target === modal) {
            modal.style.display = 'none';
        }
    });
    
    // Download guide
    const downloadBtn = document.getElementById('downloadClubsGuide');
    if (downloadBtn) {
        downloadBtn.addEventListener('click', () => {
            alert('Clubs guide download will be available soon. Please contact the school office for a printed copy.');
        });
    }
    
    // Animate cards on page load
    clubCards.forEach((card, index) => {
        card.style.animationDelay = `${index * 0.1}s`;
    });
});
</script>

<?php include 'components/newsletter.php'; ?>

<?php include 'components/footer.php'; ?> 