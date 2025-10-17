<?php
session_start();
require_once 'includes/config.php';

$title = "Contact Us";
$page = "contact";
$subtitle = "We'd love to hear from you";

// Fetch contact information from new contact management system
function get_contact_information() {
    $conn = connect_db();
    $contact_info = [];
    
    $sql = "SELECT * FROM contact_information WHERE status = 'active' ORDER BY display_order ASC, id ASC";
    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $contact_info[] = $row;
        }
    }
    
    $conn->close();
    return $contact_info;
}

// Fetch social media links from new system
function get_social_media() {
    $conn = connect_db();
    $social_media = [];
    
    $sql = "SELECT * FROM social_media WHERE status = 'active' ORDER BY display_order ASC, id ASC";
    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $social_media[] = $row;
        }
    }
    
    $conn->close();
    return $social_media;
}

// Fetch FAQs from new system
function get_faqs() {
    $conn = connect_db();
    $faqs = [];
    
    $sql = "SELECT * FROM faqs WHERE status = 'active' ORDER BY display_order ASC, id ASC";
    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $faqs[] = $row;
        }
    }
    
    $conn->close();
    return $faqs;
}

// Fetch departments from database
function get_departments() {
    $conn = connect_db();
    $departments = [];
    
    $sql = "SELECT * FROM departments WHERE status = 'active' ORDER BY display_order ASC, title ASC";
    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $departments[] = $row;
        }
    }
    
    $conn->close();
    return $departments;
}

$contact_information = get_contact_information();
$social_media = get_social_media();
$faqs = get_faqs();
$departments = get_departments();

include 'components/header.php';
?>



<div class="contact-page">

<?php
// Display success or error messages
if (isset($_SESSION['contact_success'])) {
    echo '<div class="alert alert-success">
        <i class="fas fa-check-circle"></i> ' . htmlspecialchars($_SESSION['contact_success']) . '
        <button onclick="this.parentElement.style.display=\'none\'" class="alert-close">&times;</button>
    </div>';
    unset($_SESSION['contact_success']);
}

if (isset($_SESSION['contact_error'])) {
    echo '<div class="alert alert-error">
        <i class="fas fa-exclamation-triangle"></i> ' . htmlspecialchars($_SESSION['contact_error']) . '
        <button onclick="this.parentElement.style.display=\'none\'" class="alert-close">&times;</button>
    </div>';
    unset($_SESSION['contact_error']);
}
?>

<section class="contact-info">
    <div class="container">
        <div class="info-grid">
            <?php
            // Group contact information by type for better organization
            $grouped_info = [];
            foreach ($contact_information as $info) {
                $grouped_info[$info['type']][] = $info;
            }
            
            // Define type configurations
            $type_configs = [
                'address' => [
                    'icon' => 'fas fa-map-marker-alt',
                    'title' => 'Our Location'
                ],
                'phone' => [
                    'icon' => 'fas fa-phone',
                    'title' => 'Phone Numbers'
                ],
                'email' => [
                    'icon' => 'fas fa-envelope',
                    'title' => 'Email Addresses'
                ],
                'hours' => [
                    'icon' => 'fas fa-clock',
                    'title' => 'Office Hours'
                ],
                'other' => [
                    'icon' => 'fas fa-info-circle',
                    'title' => 'Additional Information'
                ]
            ];
            
            // Display each type group
            foreach ($type_configs as $type => $config) {
                if (isset($grouped_info[$type]) && !empty($grouped_info[$type])) {
                    echo '<div class="info-item" data-aos="fade-up" data-aos-duration="600">
                        <div class="info-icon">
                            <i class="' . $config['icon'] . '"></i>
                        </div>
                        <h3>' . $config['title'] . '</h3>';
                    
                    // For addresses and hours, show full content
                    if ($type === 'address' || $type === 'hours') {
                        foreach ($grouped_info[$type] as $item) {
                            echo '<p>' . nl2br(htmlspecialchars($item['value'])) . '</p>';
                        }
                    } else {
                        // For phones and emails, show label: value format
                        echo '<p>';
                        foreach ($grouped_info[$type] as $index => $item) {
                            if ($index > 0) echo '<br>';
                            echo htmlspecialchars($item['label']) . ': ' . htmlspecialchars($item['value']);
                        }
                        echo '</p>';
                    }
                    
                    echo '</div>';
                }
            }
            
            // Fallback if no contact information is available
            if (empty($contact_information)) {
                echo '<div class="info-item" data-aos="fade-up" data-aos-duration="600">
                    <div class="info-icon">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <h3>Contact Us</h3>
                    <p>matamelasecondary@gmail.com<br>Monday - Friday: 8:00 AM - 3:00 PM</p>
                </div>';
            }
            ?>
        </div>
    </div>
</section>

<section class="contact-form-section">
    <div class="container">
        <div class="form-container" data-aos="fade-up" data-aos-duration="600">
            <h2>Send Us a Message</h2>
            <p>Have questions or feedback? Fill out the form below and we'll get back to you as soon as possible.</p>
            <form class="contact-form" action="process_contact.php" method="post">
                <div class="form-group">
                    <label for="name">Full Name</label>
                    <input type="text" id="name" name="name" required>
                </div>
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="tel" id="phone" name="phone">
                </div>
                <div class="form-group">
                    <label for="subject">Subject</label>
                    <select id="subject" name="subject" required>
                        <option value="" disabled selected>Select a subject</option>
                        <option value="general">General Inquiry</option>
                        <option value="admissions">Admissions</option>
                        <option value="academics">Academics</option>
                        <option value="events">Events</option>
                        <option value="facilities">Facilities</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="message">Message</label>
                    <textarea id="message" name="message" rows="6" required></textarea>
                </div>
                <div class="form-group checkbox-group">
                    <input type="checkbox" id="newsletter" name="newsletter">
                    <label for="newsletter">Subscribe to our newsletter</label>
                </div>
                <button type="submit" class="btn btn-submit">Send Message</button>
            </form>
        </div>
        <div class="map-container" data-aos="fade-up" data-aos-duration="600">
            <h2>Find Us</h2>
            <div class="map">
                <!-- Embed Google Map -->
                <?php 
                // Find map embed URL from contact information
                $map_embed_url = '';
                foreach ($contact_information as $info) {
                    if ($info['label'] === 'Google Maps Embed' || strpos(strtolower($info['label']), 'map') !== false) {
                        $map_embed_url = $info['value'];
                        break;
                    }
                }
                
                if (!empty($map_embed_url)): ?>
                    <iframe src="<?php echo htmlspecialchars($map_embed_url); ?>" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                <?php else: ?>
                    <!-- Default embed for Bela-Bela, South Africa -->
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d57998.47431691097!2d28.26138111230468!3d-24.88299599999999!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x1ec3c9c2d8b7b30d%3A0x7f7f2d5b6d3a6d9a!2sBela-Bela%2C%200480%2C%20South%20Africa!5e0!3m2!1sen!2sus!4v1627293600000!5m2!1sen!2sus" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
</div>

<section class="department-contacts">
    <div class="container">
        <h2 data-aos="fade-up" data-aos-duration="600">Department Contacts</h2>
        <div class="department-grid">
            <?php
            // Use dynamic departments from database

            if (!empty($departments)) {
                foreach ($departments as $index => $dept) {
                    echo '<div class="department" data-aos="fade-up" data-aos-duration="600">
                        <h3>' . htmlspecialchars($dept['title']) . '</h3>
                        <p><i class="fas fa-user"></i> ' . htmlspecialchars($dept['contact_person']) . '</p>';
                    
                    if (!empty($dept['phone'])) {
                        echo '<p><i class="fas fa-phone"></i> ' . htmlspecialchars($dept['phone']) . '</p>';
                    }
                    
                    if (!empty($dept['email'])) {
                        echo '<p><i class="fas fa-envelope"></i> ' . htmlspecialchars($dept['email']) . '</p>';
                    }
                    
                    if (!empty($dept['office_hours'])) {
                        echo '<p><i class="fas fa-clock"></i> ' . htmlspecialchars($dept['office_hours']) . '</p>';
                    }
                    
                    if (!empty($dept['description'])) {
                        echo '<p class="department-description">' . htmlspecialchars($dept['description']) . '</p>';
                    }
                    
                    echo '</div>';
                }
            } else {
                // Fallback to show a message if no departments are found
                echo '<div class="department" data-aos="fade-up" data-aos-duration="600">
                    <h3>Contact Information</h3>
                    <p><i class="fas fa-envelope"></i> matamelasecondary@gmail.com</p>
                    <p><i class="fas fa-clock"></i> Monday - Friday: 8:00 AM - 3:00 PM</p>
                    <p>Please contact us for department-specific inquiries.</p>
                </div>';
            }
            ?>
        </div>
    </div>
</section>

<section class="faq-section">
    <div class="container">
        <h2 data-aos="fade-up" data-aos-duration="600">Frequently Asked Questions</h2>
        <div class="faq-container">
            <?php
            // Use dynamic FAQs from contact management system
            if (!empty($faqs)) {
                foreach ($faqs as $index => $faq) {
                    echo '<div class="faq-item" data-aos="fade-up" data-aos-duration="600">
                        <div class="faq-question">
                            <h3>' . htmlspecialchars($faq['question']) . '</h3>
                            <span class="toggle-icon"><i class="fas fa-plus"></i></span>
                        </div>
                        <div class="faq-answer">
                            <p>' . nl2br(htmlspecialchars($faq['answer'])) . '</p>
                        </div>
                    </div>';
                }
            } else {
                // Fallback FAQs if none are configured in the admin
                $fallback_faqs = [
                    [
                        'question' => 'How can I apply for admission to Matamela Ramaphosa Secondary School?',
                        'answer' => 'To apply for admission, please contact our Admissions Office directly via email at matamelasecondary@gmail.com or visit the school in person during office hours to collect and submit an application form.'
                    ],
                    [
                        'question' => 'What grades does the school offer?',
                        'answer' => 'Matamela Ramaphosa Secondary School offers education from Grade 8 through Grade 12, following the South African national curriculum.'
                    ],
                    [
                        'question' => 'How do I report my child\'s absence?',
                        'answer' => 'To report a student absence, please call the school office before 8:00 AM on the day of the absence. You can also send an email to matamelasecondary@gmail.com with your child\'s name, grade, and reason for absence.'
                    ],
                    [
                        'question' => 'How can parents get involved with the school?',
                        'answer' => 'We welcome parent involvement! You can join our School Governing Body (SGB), attend parent meetings, or volunteer for specific events and activities. Please contact the school office for more information on how you can contribute to our school community.'
                    ]
                ];
                
                foreach ($fallback_faqs as $index => $faq) {
                    echo '<div class="faq-item" data-aos="fade-up" data-aos-duration="600">
                        <div class="faq-question">
                            <h3>' . htmlspecialchars($faq['question']) . '</h3>
                            <span class="toggle-icon"><i class="fas fa-plus"></i></span>
                        </div>
                        <div class="faq-answer">
                            <p>' . htmlspecialchars($faq['answer']) . '</p>
                        </div>
                    </div>';
                }
            }
            ?>
        </div>
    </div>
</section>

<section class="social-connect">
    <div class="container">
        <h2 data-aos="fade-up" data-aos-duration="600">Connect With Us</h2>
        <p data-aos="fade-up" data-aos-duration="600">Follow us on social media to stay updated with the latest news and events.</p>
        <div class="social-icons" data-aos="fade-up" data-aos-duration="600">
            <?php 
            // Use dynamic social media from contact management system
            if (!empty($social_media)) {
                foreach ($social_media as $social) {
                    if (!empty($social['url']) && $social['url'] !== '#') {
                        $platform_class = strtolower($social['platform']);
                        echo '<a href="' . htmlspecialchars($social['url']) . '" target="_blank" class="social-icon ' . $platform_class . '">
                            <i class="' . htmlspecialchars($social['icon']) . '"></i>
                            <span>' . htmlspecialchars($social['platform']) . '</span>
                        </a>';
                    }
                }
            } else {
                // Fallback social media links if none are configured
                echo '<a href="#" class="social-icon facebook">
                    <i class="fab fa-facebook-f"></i>
                    <span>Facebook</span>
                </a>
                <a href="#" class="social-icon twitter">
                    <i class="fab fa-twitter"></i>
                    <span>Twitter</span>
                </a>
                <a href="#" class="social-icon instagram">
                    <i class="fab fa-instagram"></i>
                    <span>Instagram</span>
                </a>';
            }
            ?>
        </div>
    </div>
</section>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // FAQ Accordion functionality
    const faqItems = document.querySelectorAll('.faq-item');
    
    faqItems.forEach(item => {
        const question = item.querySelector('.faq-question');
        
        question.addEventListener('click', function() {
            const isActive = item.classList.contains('active');
            
            // Close all FAQ items
            faqItems.forEach(faq => {
                faq.classList.remove('active');
            });
            
            // If this item wasn't active, open it
            if (!isActive) {
                item.classList.add('active');
            }
        });
    });
    
    // Form validation and enhancement
    const contactForm = document.querySelector('.contact-form');
    const submitButton = document.querySelector('.btn-submit');
    
    if (contactForm) {
        contactForm.addEventListener('submit', function(e) {
            // Add loading state to submit button
            submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
            submitButton.disabled = true;
            
            // You can add additional form validation here
            // For now, we'll let the default form submission proceed
        });
        
        // Form field animations
        const formInputs = document.querySelectorAll('.contact-form input, .contact-form select, .contact-form textarea');
        
        formInputs.forEach(input => {
            input.addEventListener('focus', function() {
                this.parentNode.classList.add('focused');
            });
            
            input.addEventListener('blur', function() {
                if (this.value === '') {
                    this.parentNode.classList.remove('focused');
                }
            });
            
            // Check if input has value on page load
            if (input.value !== '') {
                input.parentNode.classList.add('focused');
            }
        });
    }
    
    // Smooth scrolling for anchor links
    const anchorLinks = document.querySelectorAll('a[href^="#"]');
    
    anchorLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
    
    // Add animation to contact info items on scroll
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.animationDelay = Math.random() * 0.5 + 's';
                entry.target.classList.add('animate-in');
            }
        });
    }, observerOptions);
    
    // Observe all animatable elements
    const animatableElements = document.querySelectorAll('.info-item, .department, .faq-item, .social-icon');
    animatableElements.forEach(el => observer.observe(el));
});
</script>

<style>
/* Additional CSS for enhanced interactions */
.form-group.focused label {
    color: var(--primary-color);
    transform: translateY(-2px);
}

.animate-in {
    animation: fadeInUp 0.6s ease-out forwards;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Loading button state */
.btn-submit:disabled {
    opacity: 0.7;
    cursor: not-allowed;
    transform: none !important;
}

.btn-submit:disabled:hover {
    transform: none !important;
}

/* Enhanced focus states for better accessibility */
.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(215, 47, 37, 0.1);
}

/* Custom scrollbar for FAQ answers */
.faq-answer::-webkit-scrollbar {
    width: 4px;
}

.faq-answer::-webkit-scrollbar-track {
    background: var(--light-bg);
}

.faq-answer::-webkit-scrollbar-thumb {
    background: var(--primary-color);
    border-radius: 2px;
}


</style>

<?php include 'components/newsletter.php'; ?>

<?php include 'components/footer.php'; ?> 