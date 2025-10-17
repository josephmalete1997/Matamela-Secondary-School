<?php
$title = "Admissions";
$page = "admissions";
$subtitle = "Join our vibrant learning community";
include 'components/header.php';
?>

<!-- Standard Admissions Header -->
<section class="admissions-header">
    <div class="container">
        <h1>Admissions Information</h1>
        <p style="color:white;">Your pathway to academic excellence at Matamela Ramaphosa Secondary School</p>
        <div class="breadcrumb">Home > Admissions</div>
    </div>
</section>

<!-- Admissions Introduction -->
<section class="admissions-intro">
    <div class="container">
        <div class="admissions-intro-content">
            <div class="intro-text">
                <div class="section-header">
                    <h6 class="section-subtitle">Welcome to Excellence</h6>
                    <h2>Join Our Academic Community</h2>
                    <p class="section-description">At Matamela Ramaphosa Secondary School, we believe every student has the potential to achieve greatness. Our comprehensive educational program, dedicated faculty, and state-of-the-art facilities create an environment where students can flourish academically, socially, and personally.</p>
                </div>
            </div>
            <div class="intro-image">
                <img src="images/categories/campus/campus_life_01.jpg" alt="Matamela Ramaphosa Secondary School Campus" class="admissions-hero-image">
                <div class="image-overlay">
                    <div class="overlay-content">
                        <h3>Excellence in Education</h3>
                        <p>Since 2022</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Grade Levels Section -->
<section class="grade-levels" id="grade-levels">
    <div class="container">
        <div class="section-header">
            <h6 class="section-subtitle">Academic Programs</h6>
            <h2>Grade Levels</h2>
            <p class="section-description">We offer comprehensive education for students in Grades 8-12, providing a solid foundation for future success.</p>
        </div>
        <div class="grade-levels-grid">
            <div class="grade-level">
                <div class="grade-icon">
                    <i class="fas fa-book"></i>
                </div>
                <h3>Junior Secondary</h3>
                <p>Grades 8-9 (Ages 13-15)</p>
                <ul>
                    <li>Foundation in core subjects</li>
                    <li>Development of critical thinking skills</li>
                    <li>Introduction to specialized subjects</li>
                    <li>Character and leadership development</li>
                </ul>
            </div>
            <div class="grade-level">
                <div class="grade-icon">
                    <i class="fas fa-user-graduate"></i>
                </div>
                <h3>Senior Secondary</h3>
                <p>Grades 10-12 (Ages 16-18)</p>
                <ul>
                    <li>Preparation for National Senior Certificate</li>
                    <li>Advanced academic curriculum</li>
                    <li>Career guidance and counseling</li>
                    <li>University preparation</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- Application Process Section -->
<section class="application-process" id="application-process">
    <div class="container">
        <div class="section-header">
            <h6 class="section-subtitle">Getting Started</h6>
            <h2>Application Process</h2>
            <p class="section-description">Follow these simple steps to begin your journey with us</p>
        </div>
        <div class="process-timeline">
            <?php
            $steps = [
                [
                    'number' => '1',
                    'title' => 'Submit Application',
                    'description' => 'Complete and submit the admission application form along with all required documents and supporting materials.'
                ],
                [
                    'number' => '2',
                    'title' => 'Document Review',
                    'description' => 'Our admissions team will review your application and verify all submitted documents for completeness and accuracy.'
                ],
                [
                    'number' => '3',
                    'title' => 'Assessment & Interview',
                    'description' => 'Attend an academic assessment and interview session to evaluate readiness and fit for our programs.'
                ],
                [
                    'number' => '4',
                    'title' => 'Admission Decision',
                    'description' => 'Receive notification of admission decision and enrollment instructions for successful applicants.'
                ],
                [
                    'number' => '5',
                    'title' => 'Enrollment & Orientation',
                    'description' => 'Complete enrollment procedures and attend orientation to begin your academic journey with us.'
                ]
            ];
            
            foreach ($steps as $step) {
                echo '<div class="process-step">
                    <div class="step-number">' . $step['number'] . '</div>
                    <div class="step-content">
                        <h3>' . $step['title'] . '</h3>
                        <p>' . $step['description'] . '</p>
                    </div>
                </div>';
            }
            ?>
        </div>
    </div>
</section>

<!-- Required Documents Section -->
<section class="required-documents">
    <div class="container">
        <div class="section-header">
            <h6 class="section-subtitle">Documentation</h6>
            <h2>Required Documents</h2>
            <p class="section-description">Please ensure you have all necessary documents ready for your application</p>
        </div>
        <div class="documents-grid">
            <?php
            $documents = [
                [
                    'icon' => 'fas fa-id-card',
                    'title' => 'Identity Document',
                    'description' => 'Certified copy of student\'s ID document or birth certificate for identification purposes.'
                ],
                [
                    'icon' => 'fas fa-graduation-cap',
                    'title' => 'Academic Records',
                    'description' => 'Previous school reports, transcripts, and academic achievement records from last attended institution.'
                ],
                [
                    'icon' => 'fas fa-user-friends',
                    'title' => 'Parent/Guardian Details',
                    'description' => 'Identification and contact information for parents or legal guardians including emergency contacts.'
                ],
                [
                    'icon' => 'fas fa-home',
                    'title' => 'Proof of Residence',
                    'description' => 'Recent utility bill, lease agreement, or municipal account showing current residential address.'
                ],
                [
                    'icon' => 'fas fa-heartbeat',
                    'title' => 'Medical Information',
                    'description' => 'Health records, immunization certificates, and any special medical needs or conditions.'
                ],
                [
                    'icon' => 'fas fa-camera',
                    'title' => 'Passport Photos',
                    'description' => 'Recent passport-size photographs for student records and identification card purposes.'
                ]
            ];
            
            foreach ($documents as $document) {
                echo '<div class="document-item">
                    <div class="document-icon">
                        <i class="' . $document['icon'] . '"></i>
                    </div>
                    <h3>' . $document['title'] . '</h3>
                    <p>' . $document['description'] . '</p>
                </div>';
            }
            ?>
        </div>
    </div>
</section>

<!-- School Resources Section -->
<section class="school-resources">
    <div class="container">
        <div class="section-header">
            <h6 class="section-subtitle">Support & Resources</h6>
            <h2>Educational Resources & Support</h2>
        </div>
        <p class="fees-intro">Matamela Ramaphosa Secondary School provides comprehensive educational resources and support to ensure every student can succeed academically and personally.</p>
        
        <div class="educational-benefits">
            <h3>Educational Benefits</h3>
            <div class="benefits-grid">
                <div class="benefit-item">
                    <div class="benefit-icon">
                        <i class="fas fa-book-reader"></i>
                    </div>
                    <h4>Academic Excellence</h4>
                    <p>Rigorous curriculum aligned with national standards and university preparation requirements.</p>
                </div>
                <div class="benefit-item">
                    <div class="benefit-icon">
                        <i class="fas fa-trophy"></i>
                    </div>
                    <h4>Extracurricular Activities</h4>
                    <p>Diverse sports, arts, and club programs to develop well-rounded students.</p>
                </div>
            </div>
        </div>

        <div class="department-support">
            <h3>Student Support Services</h3>
            <p>Our dedicated support team ensures every student receives the guidance and assistance needed to thrive:</p>
            <ul>
                <li>Academic counseling and tutoring services</li>
                <li>Career guidance and university preparation</li>
                <li>Personal development and mentorship programs</li>
                <li>Special needs support and accommodations</li>
                <li>Parent-teacher collaboration and communication</li>
            </ul>
        </div>

        <div class="admission-requirements">
            <h3>Admission Requirements</h3>
            <p>To be considered for admission to Matamela Ramaphosa Secondary School, applicants must meet the following criteria:</p>
            <ul>
                <li>Successful completion of previous grade level with satisfactory academic performance</li>
                <li>Commitment to upholding school values and code of conduct</li>
                <li>Completion of all required application forms and documentation</li>
            </ul>
        </div>
    </div>
</section>

<!-- FAQ Section -->
<section class="admissions-faq">
    <div class="container">
        <div class="section-header">
            <h6 class="section-subtitle">Help & Information</h6>
            <h2>Frequently Asked Questions</h2>
            <p class="section-description">Find answers to common questions about our admissions process</p>
        </div>
        <div class="faq-container">
            <?php
            $faqs = [
                [
                    'question' => 'What are the admission requirements for Matamela Ramaphosa Secondary School?',
                    'answer' => 'Students must have successfully completed their previous grade level, demonstrate appropriate academic readiness, submit all required documentation, and participate in our assessment process. We also consider character, motivation, and commitment to learning.'
                ],
                [
                    'question' => 'When does the application period open for the next academic year?',
                    'answer' => 'Applications typically open in August for the following year. Early applications are encouraged as spaces are limited. Please check our website or contact the admissions office for specific dates and deadlines.'
                ],
                [
                    'question' => 'What subjects are offered at the school?',
                    'answer' => 'We offer all core subjects required by the South African curriculum including Mathematics, English, Afrikaans, Natural Sciences, Social Sciences, and Life Orientation. Additional subjects include Arts, Technology, and various electives for senior grades.'
                ],
                [
                    'question' => 'Are there scholarship or financial assistance programs available?',
                    'answer' => 'Yes, we offer limited financial assistance programs for deserving students based on academic merit and financial need. Applications for financial aid must be submitted with the admission application and supporting financial documentation.'
                ],
                [
                    'question' => 'What is the school\'s policy on transfers from other schools?',
                    'answer' => 'We welcome transfer students throughout the year, subject to space availability. Transfer students must provide complete academic records, meet our admission requirements, and may need to complete an assessment to determine appropriate grade placement.'
                ],
                [
                    'question' => 'Does the school provide transportation services?',
                    'answer' => 'Transportation arrangements vary depending on location and demand. Please contact the school office to inquire about available transport options and routes for your specific area.'
                ]
            ];
            
            foreach ($faqs as $index => $faq) {
                echo '<div class="faq-item">
                    <div class="faq-question">
                        <h3>' . $faq['question'] . '</h3>
                        <span class="toggle-icon">+</span>
                    </div>
                    <div class="faq-answer">
                        <p>' . $faq['answer'] . '</p>
                    </div>
                </div>';
            }
            ?>
        </div>
    </div>
</section>
<style>
    .toggle-icon{
        color: white;
    }
</style>
<!-- CTA Section -->

<script>
// FAQ functionality
document.querySelectorAll('.faq-question').forEach(question => {
    question.addEventListener('click', () => {
        const faqItem = question.parentElement;
        const answer = faqItem.querySelector('.faq-answer');
        const icon = question.querySelector('.toggle-icon');
        
        // Close other FAQ items
        document.querySelectorAll('.faq-item').forEach(item => {
            if (item !== faqItem) {
                item.classList.remove('active');
                const otherAnswer = item.querySelector('.faq-answer');
                const otherIcon = item.querySelector('.toggle-icon');
                if (otherAnswer) otherAnswer.style.display = 'none';
                if (otherIcon) otherIcon.style.transform = 'rotate(0deg)';
            }
        });
        
        // Toggle current FAQ item
        if (faqItem.classList.contains('active')) {
            faqItem.classList.remove('active');
            answer.style.display = 'none';
            icon.style.transform = 'rotate(0deg)';
        } else {
            faqItem.classList.add('active');
            answer.style.display = 'block';
            icon.style.transform = 'rotate(45deg)';
        }
    });
});

// Simple smooth scrolling for anchor links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
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
</script>

<?php include 'components/newsletter.php'; ?>
<?php include 'components/footer.php'; ?> 