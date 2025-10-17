<?php
$title = "Home";
$page = "home";
require_once 'includes/config.php';
require_once 'includes/Database.php';

// Initialize database connection
$db = new Database();

$story_data = $db->getRow("SELECT * FROM about_content WHERE section = 'story'");
// Get latest news items with error handling
try {
    $latest_news = $db->getRows("SELECT * FROM news_events WHERE category = 'News' AND status = 'published' ORDER BY created_at DESC LIMIT 3");
} catch (Exception $e) {
    $latest_news = [];
}

// Get upcoming events with error handling
try {
    $upcoming_events = $db->getRows("SELECT * FROM news_events WHERE category = 'Event' AND event_date >= CURDATE() AND status = 'published' ORDER BY event_date ASC LIMIT 3");
} catch (Exception $e) {
    $upcoming_events = [];
}

// Get random gallery collections with error handling
try {
    $gallery_collections = $db->getRows("SELECT * FROM gallery_collections WHERE status = 'active' ORDER BY RAND() LIMIT 6");
} catch (Exception $e) {
    $gallery_collections = [];
}

// Get active sliders with error handling
try {
    $sliders = $db->getRows("SELECT * FROM sliders WHERE status = 'active' ORDER BY display_order ASC");
} catch (Exception $e) {
    $sliders = [];
}

// Get testimonials with error handling
try {
    $testimonials = $db->getRows("SELECT * FROM testimonials WHERE status = 'active' ORDER BY display_order ASC");
} catch (Exception $e) {
    $testimonials = [];
}

// Get achievements with error handling
try {
    $achievements = $db->getRows("SELECT * FROM achievements WHERE status = 'active' ORDER BY display_order ASC");
} catch (Exception $e) {
    $achievements = [];
}

include 'components/header.php';
?>

<!-- Hero Slider Section -->
<?php if (!empty($sliders)): ?>
<section class="slider-container">
    <div class="slider">
        <?php foreach ($sliders as $index => $slide): ?>
        <div class="slide <?php echo ($index === 0) ? 'active' : ''; ?>" style="background-image: url('<?php echo htmlspecialchars($slide['image']); ?>')">
            <div class="slide-content">
                <h2><?php echo htmlspecialchars($slide['title']); ?></h2>
                <p><?php echo htmlspecialchars($slide['description'] ?? ''); ?></p>
                <?php if (!empty($slide['button_text'])): ?>
                <div class="slide-buttons">
                    <a href="<?php echo htmlspecialchars($slide['button_url'] ?? '#'); ?>" class="btn btn-primary"><?php echo htmlspecialchars($slide['button_text']); ?></a>
                    <?php if ($index === 0): // Show contact button on first slide ?>
                    <a href="contact.php" class="btn btn-outline">Contact Us</a>
                    <?php elseif ($index === 2): // Show about button on third slide ?>
                    <a href="about.php" class="btn btn-outline">Learn More</a>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    
    <div class="slider-nav">
        <?php for ($i = 0; $i < count($sliders); $i++): ?>
        <span class="slider-dot <?php echo ($i === 0) ? 'active' : ''; ?>" data-index="<?php echo $i; ?>"></span>
        <?php endfor; ?>
    </div>
    
    <div class="slider-arrows">
        <span class="slider-arrow prev"><i class="fas fa-chevron-left"></i></span>
        <span class="slider-arrow next"><i class="fas fa-chevron-right"></i></span>
    </div>
</section>
<?php endif; ?>

<!-- Features Section -->
<section class="features">
    <div class="container">
        <div class="feature-items" data-aos="fade-up" data-aos-duration="600">
            <div class="feature-item">
                <div class="feature-icon">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <div class="feature-content">
                    <h4>Quality Education</h4>
                    <p>Comprehensive curriculum designed for Grades 8-12 students</p>
                </div>
            </div>
            
            <div class="feature-item">
                <div class="feature-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="feature-content">
                    <h4>Expert Teachers</h4>
                    <p>Qualified and experienced teaching staff</p>
                </div>
            </div>
            
            <div class="feature-item">
                <div class="feature-icon">
                    <i class="fas fa-trophy"></i>
                </div>
                <div class="feature-content">
                    <h4>Excellence</h4>
                    <p>Committed to academic and personal excellence</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- About Preview Section -->
<section class="about-preview">
    <div class="container">
        <div class="about-preview-content">
            <div class="about-preview-text" data-aos="fade-up" data-aos-duration="600">
                <div class="section-header">
                    <h6 class="section-subtitle">About Our School</h6>
                    <h2>About School Quick Info</h2>
                </div>
                
                <p>At Matamela Ramaphosa Secondary School, we believe in providing quality education that nurtures both academic excellence and character development. Named after South African President Cyril Ramaphosa and established in 2022, our school offers comprehensive education for Grades 8-12, following the South African National Curriculum.</p>
                
                <div class="about-features">
                    <div class="about-feature">
                        <i class="fas fa-check-circle"></i>
                        <span>Grades 8-12 Education</span>
                    </div>
                    <div class="about-feature">
                        <i class="fas fa-check-circle"></i>
                        <span>Dedicated Teaching Staff</span>
                    </div>
                    <div class="about-feature">
                        <i class="fas fa-check-circle"></i>
                        <span>Modern Facilities</span>
                    </div>
                    <div class="about-feature">
                        <i class="fas fa-check-circle"></i>
                        <span>South African Curriculum</span>
                    </div>
                    <div class="about-feature">
                        <i class="fas fa-check-circle"></i>
                        <span>Focus on Academic Excellence</span>
                    </div>
                </div>
                
                <div class="about-buttons">
                    <a href="about.php" class="btn btn-primary">Learn More</a>
                    <a href="contact.php" class="btn btn-secondary">Contact Us</a>
                </div>
            </div>
            
            <div class="about-preview-image" data-aos="fade-up" data-aos-duration="600">
            <img src="<?php echo htmlspecialchars($story_data['story_image']); ?>" alt="Matamela Ramaphosa Secondary School Building">
                <div class="about-badge">
                    <span>Est. 2022</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- News & Events Section -->
<section class="news-events">
    <div class="container">
        <div class="section-header" data-aos="fade-up" data-aos-duration="600">
            <h6 class="section-subtitle">Latest Updates</h6>
            <h2>News & Events</h2>
            <p class="section-description">Stay updated with the latest news and upcoming events at our school</p>
        </div>
        
        <div class="news-events-grid">
            <?php if (!empty($latest_news) || !empty($upcoming_events)): ?>
                <?php 
                // Combine and sort news and events
                $all_items = array_merge($latest_news, $upcoming_events);
                usort($all_items, function($a, $b) {
                    $date_a = isset($a['event_date']) ? $a['event_date'] : $a['created_at'];
                    $date_b = isset($b['event_date']) ? $b['event_date'] : $b['created_at'];
                    return strtotime($date_b) - strtotime($date_a);
                });
                
                $displayed = 0;
                foreach ($all_items as $item): 
                    if ($displayed >= 6) break;
                    $displayed++;
                ?>
                <div class="news-item" data-aos="fade-up" data-aos-duration="600">
                    <div class="news-img">
                        <img src="<?php echo !empty($item['featured_image']) ? $item['featured_image'] : 'images/categories/events/school_event_01.jpg'; ?>" alt="<?php echo htmlspecialchars($item['title']); ?>">
                        <div class="news-category">
                            <?php echo $item['category']; ?>
                        </div>
                    </div>
                    <div class="news-content">
                        <h4><?php echo htmlspecialchars($item['title']); ?></h4>
                        <p><?php echo htmlspecialchars(substr(strip_tags($item['content']), 0, 120)) . '...'; ?></p>
                        <div class="news-meta">
                            <span><i class="fas fa-calendar"></i> 
                                <?php echo format_date(isset($item['event_date']) ? $item['event_date'] : $item['created_at']); ?>
                            </span>
                        </div>
                        <a href="news-detail.php?id=<?php echo $item['id']; ?>" class="read-more">Read More</a>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <!-- Default news items if none in database -->
                <div class="news-item" data-aos="fade-up" data-aos-duration="600">
                    <div class="news-img">
                        <img src="images/categories/events/school_event_01.jpg" alt="School Event">
                        <div class="news-category">Event</div>
                    </div>
                    <div class="news-content">
                        <h4>Welcome to New Academic Year</h4>
                        <p>We're excited to welcome all students and families to another fantastic year of learning and growth at Matamela Ramaphosa Secondary School.</p>
                        <div class="news-meta">
                            <span><i class="fas fa-calendar"></i> <?php echo date('M d, Y'); ?></span>
                        </div>
                        <a href="news.php" class="read-more">Read More</a>
                    </div>
                </div>
                
                <div class="news-item" data-aos="fade-up" data-aos-duration="600">
                    <div class="news-img">
                        <img src="images/categories/events/sports_day_01.jpg" alt="Sports Day">
                        <div class="news-category">Event</div>
                    </div>
                    <div class="news-content">
                        <h4>Annual Sports Day</h4>
                        <p>Join us for our annual sports day where students showcase their athletic talents and school spirit in various competitive events.</p>
                        <div class="news-meta">
                            <span><i class="fas fa-calendar"></i> Coming Soon</span>
                        </div>
                        <a href="news.php" class="read-more">Read More</a>
                    </div>
                </div>
                
                <div class="news-item" data-aos="fade-up" data-aos-duration="600">
                    <div class="news-img">
                        <img src="images/categories/events/graduation_01.jpg" alt="Graduation">
                        <div class="news-category">News</div>
                    </div>
                    <div class="news-content">
                        <h4>Outstanding Academic Results</h4>
                        <p>We're proud to announce excellent matric results for our graduating class, with many students achieving distinctions in multiple subjects.</p>
                        <div class="news-meta">
                            <span><i class="fas fa-calendar"></i> Recent</span>
                        </div>
                        <a href="news.php" class="read-more">Read More</a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="section-footer" data-aos="fade-up" data-aos-duration="600">
            <a href="news.php" class="btn btn-outline">View All News</a>
        </div>
    </div>
</section>

<!-- Achievements Section -->
<section class="achievements">
    <div class="container">
        <div class="section-header" data-aos="fade-up" data-aos-duration="600">
            <h6 class="section-subtitle">Our Success</h6>
            <h2>School Achievements</h2>
            <p class="section-description">Celebrating our accomplishments and recognitions</p>
        </div>
        
        <div class="achievement-items">
            <?php if (!empty($achievements)): ?>
                <?php foreach ($achievements as $achievement): ?>
                <div class="achievement-item" data-aos="fade-up" data-aos-duration="600">
                    <div class="achievement-icon">
                        <i class="<?php echo htmlspecialchars($achievement['icon']); ?>"></i>
                    </div>
                    <div class="achievement-content">
                        <h4><?php echo htmlspecialchars($achievement['title']); ?></h4>
                        <p><?php echo htmlspecialchars($achievement['description']); ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <!-- Default achievements if none in database -->
                <div class="achievement-item" data-aos="fade-up" data-aos-duration="600">
                    <div class="achievement-icon">
                        <i class="fas fa-trophy"></i>
                    </div>
                    <div class="achievement-content">
                        <h4>Academic Excellence</h4>
                        <p>Outstanding matric results and academic performance across all grades</p>
                    </div>
                </div>
                
                <div class="achievement-item" data-aos="fade-up" data-aos-duration="600">
                    <div class="achievement-icon">
                        <i class="fas fa-medal"></i>
                    </div>
                    <div class="achievement-content">
                        <h4>Sports Achievements</h4>
                        <p>Regional champions in various sporting disciplines</p>
                    </div>
                </div>
                
                <div class="achievement-item" data-aos="fade-up" data-aos-duration="600">
                    <div class="achievement-icon">
                        <i class="fas fa-award"></i>
                    </div>
                    <div class="achievement-content">
                        <h4>Community Recognition</h4>
                        <p>Honored for outstanding contribution to local community development</p>
                    </div>
                </div>
                
                <div class="no-achievements" data-aos="fade-up" data-aos-duration="600" style="display: none;">
                    <p>No achievements available at the moment.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Testimonials Section -->
<?php if (!empty($testimonials)): ?>
<section class="testimonials">
    <div class="container">
        <div class="section-header" data-aos="fade-up" data-aos-duration="600">
            <h6 class="section-subtitle">Testimonials</h6>
            <h2 style="color:white;">What Parents & Students Say</h2>
            <p class="section-description" style="color:white;">Hear from our community about their experiences at Matamela Ramaphosa Secondary School</p>
        </div>
        
        <div class="testimonials-container" data-aos="fade-up" data-aos-duration="600" data-aos-delay="200">
            <button class="testimonial-scroll-btn testimonial-scroll-left" id="scrollLeft">
                <i class="fas fa-chevron-left"></i>
            </button>
            <button class="testimonial-scroll-btn testimonial-scroll-right" id="scrollRight">
                <i class="fas fa-chevron-right"></i>
            </button>
            <div class="testimonials-panels" id="testimonialsContainer">
                <?php foreach ($testimonials as $testimonial): ?>
                <div class="testimonial-panel">
                    <div class="testimonial-panel-content">
                        <div class="testimonial-quote">
                            <i class="fas fa-quote-left"></i>
                            <p><?php echo htmlspecialchars($testimonial['content']); ?></p>
                        </div>
                        <div class="testimonial-rating">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <i class="fas fa-star<?php echo ($i > $testimonial['rating']) ? '-o' : ''; ?>"></i>
                            <?php endfor; ?>
                        </div>
                    </div>
                    <div class="testimonial-panel-footer">
                        <div class="testimonial-avatar">
                            <img src="<?php echo !empty($testimonial['image']) ? htmlspecialchars($testimonial['image']) : 'images/categories/students/student_group_01.jpg'; ?>" alt="<?php echo htmlspecialchars($testimonial['name']); ?>">
                        </div>
                        <div class="testimonial-author">
                            <h4><?php echo htmlspecialchars($testimonial['name']); ?></h4>
                            <span class="testimonial-position"><?php echo htmlspecialchars($testimonial['position']); ?></span>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <div class="scroll-hint">
                <span class="scroll-text">Use the arrow buttons to scroll through testimonials</span>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Gallery Preview Section -->
<section class="gallery-preview">
    <div class="container">
        <div class="section-header" data-aos="fade-up" data-aos-duration="600">
            <h6 class="section-subtitle">Our Gallery</h6>
            <h2>Featured Collections</h2>
            <p class="section-description">Discover our latest photo collections showcasing school life and events</p>
        </div>
        
        <div class="gallery-items">
            <?php if (!empty($gallery_collections)): ?>
                <?php foreach ($gallery_collections as $collection): ?>
                <div class="gallery-item" 
                     data-category="<?php echo htmlspecialchars($collection['category']); ?>" 
                     onclick="window.location.href='collection.php?id=<?php echo $collection['id']; ?>'"
                     style="cursor: pointer;">
                    <?php
                    // Use cover image if available, otherwise use a default image based on category
                    $cover_image = !empty($collection['cover_image']) ? $collection['cover_image'] : 'images/categories/' . $collection['category'] . '/default.jpg';
                    
                    // Fallback to a generic default if category-specific default doesn't exist
                    if (!file_exists($cover_image)) {
                        $cover_image = 'images/categories/campus/campus_life_01.jpg';
                    }
                    ?>
                    <img src="<?php echo htmlspecialchars($cover_image); ?>" alt="<?php echo htmlspecialchars($collection['title']); ?>">
                    <div class="gallery-overlay">
                        <div class="gallery-info">
                            <h4><?php echo htmlspecialchars($collection['title']); ?></h4>
                            <p><?php echo htmlspecialchars($collection['description'] ?? 'Click to view this collection'); ?></p>
                            <?php if (!empty($collection['event_date'])): ?>
                                <small class="collection-date"><?php echo date('F j, Y', strtotime($collection['event_date'])); ?></small>
                            <?php endif; ?>
                        </div>
                        <div class="gallery-action">
                            <i class="fas fa-images"></i>
                            <span>View Collection</span>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <!-- Default gallery collections if none in database -->
                <div class="gallery-item" 
                     data-category="campus" 
                     onclick="window.location.href='gallery.php'"
                     style="cursor: pointer;">
                    <img src="images/categories/campus/campus_life_01.jpg" alt="Campus Life">
                    <div class="gallery-overlay">
                        <div class="gallery-info">
                            <h4>Campus Life</h4>
                            <p>Explore our beautiful campus and facilities</p>
                        </div>
                        <div class="gallery-action">
                            <i class="fas fa-images"></i>
                            <span>View Collection</span>
                        </div>
                    </div>
                </div>
                
                <div class="gallery-item" 
                     data-category="students" 
                     onclick="window.location.href='gallery.php'"
                     style="cursor: pointer;">
                    <img src="images/categories/students/student_group_01.jpg" alt="Students">
                    <div class="gallery-overlay">
                        <div class="gallery-info">
                            <h4>Student Life</h4>
                            <p>Our students engaged in various activities</p>
                        </div>
                        <div class="gallery-action">
                            <i class="fas fa-images"></i>
                            <span>View Collection</span>
                        </div>
                    </div>
                </div>
                
                <div class="gallery-item" 
                     data-category="classroom" 
                     onclick="window.location.href='gallery.php'"
                     style="cursor: pointer;">
                    <img src="images/categories/classroom/classroom_group_01.jpg" alt="Classroom">
                    <div class="gallery-overlay">
                        <div class="gallery-info">
                            <h4>Learning Spaces</h4>
                            <p>Modern classrooms and educational facilities</p>
                        </div>
                        <div class="gallery-action">
                            <i class="fas fa-images"></i>
                            <span>View Collection</span>
                        </div>
                    </div>
                </div>
                
                <div class="gallery-item" 
                     data-category="events" 
                     onclick="window.location.href='gallery.php'"
                     style="cursor: pointer;">
                    <img src="images/categories/events/school_event_01.jpg" alt="School Events">
                    <div class="gallery-overlay">
                        <div class="gallery-info">
                            <h4>School Events</h4>
                            <p>Memorable moments from school celebrations</p>
                        </div>
                        <div class="gallery-action">
                            <i class="fas fa-images"></i>
                            <span>View Collection</span>
                        </div>
                    </div>
                </div>
                
                <div class="gallery-item" 
                     data-category="facilities" 
                     onclick="window.location.href='gallery.php'"
                     style="cursor: pointer;">
                    <img src="images/categories/facilities/library_01.jpg" alt="Facilities">
                    <div class="gallery-overlay">
                        <div class="gallery-info">
                            <h4>School Facilities</h4>
                            <p>Our excellent educational infrastructure</p>
                        </div>
                        <div class="gallery-action">
                            <i class="fas fa-images"></i>
                            <span>View Collection</span>
                        </div>
                    </div>
                </div>
                
                <div class="gallery-item" 
                     data-category="staff" 
                     onclick="window.location.href='gallery.php'"
                     style="cursor: pointer;">
                    <img src="images/categories/staff/teacher_01.jpg" alt="Staff">
                    <div class="gallery-overlay">
                        <div class="gallery-info">
                            <h4>Our Staff</h4>
                            <p>Dedicated educators and support staff</p>
                        </div>
                        <div class="gallery-action">
                            <i class="fas fa-images"></i>
                            <span>View Collection</span>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="section-footer" data-aos="fade-up" data-aos-duration="600">
            <a href="gallery.php" class="btn btn-primary">View Full Gallery</a>
        </div>
    </div>
</section>

<?php include 'components/newsletter.php'; ?>

<?php if (!empty($testimonials)): ?>
<script>
// Testimonial scroll functionality
document.addEventListener('DOMContentLoaded', function() {
    const testimonialsContainer = document.getElementById('testimonialsContainer');
    const scrollLeftBtn = document.getElementById('scrollLeft');
    const scrollRightBtn = document.getElementById('scrollRight');
    
    if (testimonialsContainer && scrollLeftBtn && scrollRightBtn) {
        // Calculate scroll amount (width of one panel + gap)
        const getScrollAmount = () => {
            const panel = testimonialsContainer.querySelector('.testimonial-panel');
            if (panel) {
                const panelWidth = panel.offsetWidth;
                const gap = 30; // CSS gap
                return panelWidth + gap;
            }
            return 400; // fallback
        };
        
        // Update button states
        const updateButtonStates = () => {
            const isAtStart = testimonialsContainer.scrollLeft <= 0;
            const isAtEnd = testimonialsContainer.scrollLeft >= 
                           testimonialsContainer.scrollWidth - testimonialsContainer.offsetWidth - 5;
            
            scrollLeftBtn.disabled = isAtStart;
            scrollRightBtn.disabled = isAtEnd;
        };
        
        // Scroll left
        scrollLeftBtn.addEventListener('click', () => {
            const scrollAmount = getScrollAmount();
            testimonialsContainer.scrollBy({
                left: -scrollAmount,
                behavior: 'smooth'
            });
        });
        
        // Scroll right
        scrollRightBtn.addEventListener('click', () => {
            const scrollAmount = getScrollAmount();
            testimonialsContainer.scrollBy({
                left: scrollAmount,
                behavior: 'smooth'
            });
        });
        
        // Update button states on scroll
        testimonialsContainer.addEventListener('scroll', updateButtonStates);
        
        // Update button states on load and resize
        updateButtonStates();
        window.addEventListener('resize', () => {
            setTimeout(updateButtonStates, 100);
        });
        
        // Hide scroll hint after first interaction
        let hasInteracted = false;
        const scrollHint = document.querySelector('.scroll-hint');
        
        const hideScrollHint = () => {
            if (!hasInteracted && scrollHint) {
                hasInteracted = true;
                scrollHint.style.opacity = '0.7';
                setTimeout(() => {
                    if (scrollHint) {
                        scrollHint.style.display = 'none';
                    }
                }, 3000);
            }
        };
        
        scrollLeftBtn.addEventListener('click', hideScrollHint);
        scrollRightBtn.addEventListener('click', hideScrollHint);
        testimonialsContainer.addEventListener('scroll', hideScrollHint);
        
        // Keyboard navigation
        document.addEventListener('keydown', (e) => {
            if (e.target.closest('.testimonials-container')) {
                if (e.key === 'ArrowLeft' && !scrollLeftBtn.disabled) {
                    e.preventDefault();
                    scrollLeftBtn.click();
                } else if (e.key === 'ArrowRight' && !scrollRightBtn.disabled) {
                    e.preventDefault();
                    scrollRightBtn.click();
                }
            }
        });
    }
});
</script>
<style>
    .testimonials-container{
        /* overflow-x: hidden; */
    }
</style>
<?php endif; ?>
<?php include 'components/footer.php'; ?>