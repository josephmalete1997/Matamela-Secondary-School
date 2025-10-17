<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/Database.php';

// Initialize database connection
$db = new Database();

// Get news ID from URL
$news_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$news_id) {
    $_SESSION['error_message'] = "No news article ID provided.";
    header("Location: news.php");
    exit;
}

// Get news data with more robust error handling
$article_not_found = false;
$error_message = '';
$news_data = null;

try {
    // First, let's try a direct MySQLi approach to bypass potential Database class issues
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if ($conn->connect_error) {
        throw new Exception("Database connection failed: " . $conn->connect_error);
    }
    
    $stmt = $conn->prepare("SELECT * FROM news_events WHERE id = ?");
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    
    $stmt->bind_param("i", $news_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result && $result->num_rows > 0) {
        $news_data = $result->fetch_assoc();
    
    // Check if article is published
    if ($news_data['status'] !== 'published') {
            $article_not_found = true;
            $error_message = "This article is not published yet.";
            $news_data = null; // Clear the data since it's not published
        }
    } else {
        $article_not_found = true;
        $error_message = "Article with ID {$news_id} not found in database.";
    }
    
    $stmt->close();
    $conn->close();
    
} catch (Exception $e) {
    $article_not_found = true;
    $error_message = "Error loading article: " . $e->getMessage();
    $news_data = null;
}

// Get related news/events (only if article was found)
$related_news = [];
if (!$article_not_found && $news_data) {
    try {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if (!$conn->connect_error) {
            $stmt = $conn->prepare("
                SELECT id, title, category, 
                       CASE WHEN event_date IS NOT NULL THEN event_date ELSE created_at END as display_date,
                       featured_image
                FROM news_events 
                WHERE id != ? AND status = 'published' AND category = ?
                ORDER BY created_at DESC 
                LIMIT 3
            ");
            if ($stmt) {
                $stmt->bind_param("is", $news_id, $news_data['category']);
                $stmt->execute();
                $result = $stmt->get_result();
                
                while ($row = $result->fetch_assoc()) {
                    $related_news[] = $row;
                }
                
                $stmt->close();
            }
            $conn->close();
        }
    } catch (Exception $e) {
        $related_news = [];
    }
}

// Set page metadata
if ($article_not_found) {
    $title = "Article Not Found";
    $meta_description = "The requested article could not be found.";
    $meta_keywords = '';
} else {
$title = $news_data['title'];
    $meta_description = !empty($news_data['meta_description']) ? $news_data['meta_description'] : substr(strip_tags($news_data['content']), 0, 160);
    $meta_keywords = $news_data['meta_keywords'] ?? '';
}
$page = "news";

// Set current URL for share buttons
$current_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

// Debug information (remove in production)
if (isset($_GET['debug'])) {
    echo "<div style='background: #f0f0f0; padding: 20px; margin: 20px; border: 1px solid #ccc; border-radius: 8px;'>";
    echo "<h3>Debug Information:</h3>";
    echo "<p><strong>Article ID:</strong> " . $news_id . "</p>";
    echo "<p><strong>Article Found:</strong> " . ($news_data ? 'Yes' : 'No') . "</p>";
    echo "<p><strong>Article Not Found Flag:</strong> " . ($article_not_found ? 'Yes' : 'No') . "</p>";
    echo "<p><strong>Error Message:</strong> " . htmlspecialchars($error_message) . "</p>";
    
    // Test database connection
    echo "<p><strong>Database Connection Test:</strong></p>";
    try {
        // Test simple query first
        $test_query = $db->query("SELECT 1 as test");
        echo "<p>✓ Basic database connection: OK</p>";
        
        // Test news_events table exists
        $table_check = $db->query("SHOW TABLES LIKE 'news_events'");
        if ($table_check && $table_check->num_rows > 0) {
            echo "<p>✓ news_events table exists</p>";
        } else {
            echo "<p>✗ news_events table NOT found</p>";
        }
        
        // Test the exact query being used with Database class
        echo "<p><strong>Testing Database class query:</strong></p>";
        $debug_query = "SELECT * FROM news_events WHERE id = ?";
        echo "<p>Query: " . $debug_query . "</p>";
        echo "<p>Parameter: " . $news_id . " (type: " . gettype($news_id) . ")</p>";
        
        $debug_result = $db->getRow($debug_query, [$news_id]);
        echo "<p>Database class result: " . ($debug_result ? 'Found' : 'Not found') . "</p>";
        
        if ($debug_result) {
            echo "<p>Database class data: " . json_encode($debug_result) . "</p>";
        }
        
        // Test direct MySQLi approach
        echo "<p><strong>Testing direct MySQLi query:</strong></p>";
        $direct_conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if (!$direct_conn->connect_error) {
            $direct_stmt = $direct_conn->prepare("SELECT * FROM news_events WHERE id = ?");
            if ($direct_stmt) {
                $direct_stmt->bind_param("i", $news_id);
                $direct_stmt->execute();
                $direct_result = $direct_stmt->get_result();
                
                if ($direct_result && $direct_result->num_rows > 0) {
                    $direct_data = $direct_result->fetch_assoc();
                    echo "<p>Direct MySQLi result: Found</p>";
                    echo "<p>Direct MySQLi data: " . json_encode($direct_data) . "</p>";
                } else {
                    echo "<p>Direct MySQLi result: Not found</p>";
                }
                $direct_stmt->close();
            } else {
                echo "<p>Direct MySQLi prepare failed: " . $direct_conn->error . "</p>";
            }
            $direct_conn->close();
        } else {
            echo "<p>Direct MySQLi connection failed: " . $direct_conn->connect_error . "</p>";
        }
        
        // Check database error
        $db_error = $db->getError();
        if ($db_error) {
            echo "<p><strong>Database Class Error:</strong> " . $db_error . "</p>";
        }
        
    } catch (Exception $e) {
        echo "<p><strong>Database Connection Error:</strong> " . $e->getMessage() . "</p>";
    }
    
    // Check if we can connect to database and see all articles
    try {
        $all_articles = $db->getRows("SELECT id, title, status, category FROM news_events ORDER BY id DESC LIMIT 10");
        echo "<p><strong>Total articles in database:</strong> " . count($all_articles) . "</p>";
        echo "<p><strong>Recent articles:</strong></p>";
        echo "<ul>";
        foreach ($all_articles as $article) {
            echo "<li>ID: {$article['id']} - {$article['title']} ({$article['status']}) - {$article['category']}</li>";
        }
        echo "</ul>";
    } catch (Exception $e) {
        echo "<p><strong>Database Error:</strong> " . $e->getMessage() . "</p>";
    }
    
    if ($news_data) {
        echo "<p><strong>Title:</strong> " . htmlspecialchars($news_data['title']) . "</p>";
        echo "<p><strong>Status:</strong> " . htmlspecialchars($news_data['status']) . "</p>";
        echo "<p><strong>Category:</strong> " . htmlspecialchars($news_data['category']) . "</p>";
        echo "<p><strong>Content Length:</strong> " . strlen($news_data['content']) . " characters</p>";
        echo "<p><strong>Content Preview:</strong> " . htmlspecialchars(substr($news_data['content'], 0, 200)) . "...</p>";
        echo "<p><strong>Created At:</strong> " . $news_data['created_at'] . "</p>";
        if (!empty($news_data['event_date'])) {
            echo "<p><strong>Event Date:</strong> " . $news_data['event_date'] . "</p>";
        }
    }
    echo "</div>";
}

include 'components/header.php';
?>

<article class="news-detail">
    <div class="container">
        <?php if ($article_not_found): ?>
        <!-- Article Not Found Content -->
        <div class="article-not-found">
            <div class="not-found-content">
                <div class="not-found-icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <h1>Article Not Found</h1>
                <p class="error-message"><?php echo htmlspecialchars($error_message); ?></p>
                <div class="not-found-actions">
                    <a href="news.php" class="btn btn-primary">
                        <i class="fas fa-arrow-left"></i> Back to News
                    </a>
                    <a href="index.php" class="btn btn-outline">
                        <i class="fas fa-home"></i> Go Home
                    </a>
                </div>
                
                <!-- Show recent articles as alternatives -->
                <?php
                try {
                    $recent_articles = $db->getRows("
                        SELECT id, title, category, created_at, featured_image
                        FROM news_events 
                        WHERE status = 'published'
                        ORDER BY created_at DESC 
                        LIMIT 6
                    ");
                    if (!empty($recent_articles)): ?>
                    <div class="suggested-articles">
                        <h3>You might be interested in these articles:</h3>
                        <div class="suggested-articles-grid">
                            <?php foreach ($recent_articles as $article): ?>
                            <div class="suggested-article">
                                <div class="suggested-article-image" style="background-image: url('<?php echo !empty($article['featured_image']) ? htmlspecialchars($article['featured_image']) : 'images/categories/events/school_event_01.jpg'; ?>');">
                                </div>
                                <div class="suggested-article-content">
                                    <div class="article-category"><?php echo htmlspecialchars($article['category']); ?></div>
                                    <h4><a href="news-detail.php?id=<?php echo $article['id']; ?>"><?php echo htmlspecialchars($article['title']); ?></a></h4>
                                    <div class="article-date">
                                        <i class="fas fa-calendar"></i>
                                        <?php echo date('M j, Y', strtotime($article['created_at'])); ?>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif;
                } catch (Exception $e) {
                    // Silently fail if we can't get recent articles
                }
                ?>
            </div>
        </div>
        <?php else: ?>
        <!-- Normal Article Content -->
        <div class="news-detail-content">
            <div class="news-detail-header">
                <div class="news-breadcrumb">
                    <a href="index.php">Home</a>
                    <span class="separator"><i class="fas fa-chevron-right"></i></span>
                    <a href="news.php">News & Events</a>
                    <span class="separator"><i class="fas fa-chevron-right"></i></span>
                    <span class="current"><?php echo htmlspecialchars($news_data['title']); ?></span>
                </div>
                
                <div class="news-category-badge">
                    <span class="category-badge <?php echo strtolower($news_data['category']); ?>">
                        <?php echo htmlspecialchars($news_data['category']); ?>
                    </span>
                </div>
                
                <h1><?php echo htmlspecialchars($news_data['title']); ?></h1>
                
                <div class="news-meta">
                    <div class="meta-item">
                        <i class="fas fa-calendar"></i>
                        <span>
                            <?php 
                            if (!empty($news_data['event_date'])) {
                                echo date('F j, Y', strtotime($news_data['event_date']));
                            } else {
                                echo date('F j, Y', strtotime($news_data['created_at']));
                            }
                            ?>
                        </span>
                    </div>
                    
                    <?php if (!empty($news_data['event_time'])): ?>
                    <div class="meta-item">
                        <i class="fas fa-clock"></i>
                        <span><?php echo date('g:i A', strtotime($news_data['event_time'])); ?></span>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($news_data['location'])): ?>
                    <div class="meta-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <span><?php echo htmlspecialchars($news_data['location']); ?></span>
                    </div>
                    <?php endif; ?>
                    
                    <div class="meta-item">
                        <i class="fas fa-user"></i>
                        <span>By <?php echo htmlspecialchars($news_data['author']); ?></span>
                    </div>
                </div>
            </div>
            
            <?php if (!empty($news_data['featured_image'])): ?>
            <div class="news-featured-image" style="background-image: url('<?php echo htmlspecialchars($news_data['featured_image']); ?>');">
                <div class="image-overlay"></div>
            </div>
            <?php endif; ?>
            
            <div class="news-content">
                <?php 
                if (!empty($news_data['content'])) {
                // Check if content contains HTML tags (from Quill editor)
                if (strip_tags($news_data['content']) !== $news_data['content']) {
                    // Content has HTML, render it directly (Quill produces safe HTML)
                    echo $news_data['content'];
                } else {
                    // Plain text content, convert line breaks
                    echo nl2br(htmlspecialchars($news_data['content']));
                    }
                } else {
                    echo '<p class="no-content-message">No content available for this article.</p>';
                }
                ?>
            </div>
            
            <div class="news-actions">
                <div class="share-buttons">
                    <span class="share-label">Share this article:</span>
                    <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode($current_url ?? ''); ?>" target="_blank" class="share-btn facebook">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode($current_url ?? ''); ?>&text=<?php echo urlencode($news_data['title']); ?>" target="_blank" class="share-btn twitter">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <a href="https://wa.me/?text=<?php echo urlencode($news_data['title'] . ' - ' . ($current_url ?? '')); ?>" target="_blank" class="share-btn whatsapp">
                        <i class="fab fa-whatsapp"></i>
                    </a>
                    <a href="mailto:?subject=<?php echo urlencode($news_data['title']); ?>&body=<?php echo urlencode('Check out this article: ' . ($current_url ?? '')); ?>" class="share-btn email">
                        <i class="fas fa-envelope"></i>
                    </a>
                </div>
                
                <div class="back-to-news">
                    <a href="news.php" class="btn btn-outline">
                        <i class="fas fa-arrow-left"></i> Back to News
                    </a>
                </div>
            </div>
        </div>
        
        <?php if (!empty($related_news)): ?>
        <div class="related-news">
            <h3>Related <?php echo $news_data['category'] === 'Event' ? 'Events' : 'Articles'; ?></h3>
            <div class="related-news-grid">
                <?php foreach ($related_news as $related): ?>
                <div class="related-news-item">
                    <div class="related-news-image" style="background-image: url('<?php echo !empty($related['featured_image']) ? htmlspecialchars($related['featured_image']) : 'images/categories/events/school_event_01.jpg'; ?>');">
                    </div>
                    <div class="related-news-content">
                        <div class="news-tag"><?php echo htmlspecialchars($related['category']); ?></div>
                        <h4><?php echo htmlspecialchars($related['title']); ?></h4>
                        <div class="news-date">
                            <i class="fas fa-calendar"></i>
                            <?php echo date('M j, Y', strtotime($related['display_date'])); ?>
                        </div>
                        <a href="news-detail.php?id=<?php echo $related['id']; ?>" class="read-more">Read More</a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</article>

<style>
.news-detail {
    padding: 40px 0 80px;
    background: #fff;
}

.news-detail-content {
    max-width: 800px;
    margin: 0 auto;
}

.news-detail-header {
    margin-bottom: 30px;
}

.news-breadcrumb {
    margin-bottom: 20px;
    font-size: 0.9rem;
    color: #666;
}

.news-breadcrumb a {
    color: var(--primary-color);
    text-decoration: none;
}

.news-breadcrumb a:hover {
    text-decoration: underline;
}

.separator {
    margin: 0 10px;
    color: #ccc;
}

.current {
    color: #999;
}

.news-category-badge {
    margin-bottom: 15px;
}

.category-badge {
    padding: 6px 12px;
    border-radius: 15px;
    font-size: 0.8rem;
    font-weight: 600;
    text-transform: uppercase;
    display: inline-block;
}

.category-badge.news {
    background: #e3f2fd;
    color: #1976d2;
}

.category-badge.event {
    background: #f3e5f5;
    color: #7b1fa2;
}

.category-badge.notice {
    background: #fff3e0;
    color: #f57c00;
}

.category-badge.announcement {
    background: #e8f5e8;
    color: #388e3c;
}

.news-detail-header h1 {
    font-size: 2.5rem;
    font-weight: 700;
    color: #333;
    line-height: 1.2;
    margin-bottom: 20px;
}

.news-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    margin-bottom: 30px;
    padding-bottom: 20px;
    border-bottom: 1px solid #eee;
}

.meta-item {
    display: flex;
    align-items: center;
    gap: 8px;
    color: #666;
    font-size: 0.9rem;
}

.meta-item i {
    color: var(--primary-color);
}

.news-featured-image {
    margin-bottom: 30px;
    height: 400px;
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    border-radius: 12px;
    box-shadow: 0 8px 30px rgba(0,0,0,0.15);
    position: relative;
    overflow: hidden;
}

.image-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(
        to bottom,
        rgba(0,0,0,0) 0%,
        rgba(0,0,0,0.1) 50%,
        rgba(0,0,0,0.3) 100%
    );
    border-radius: 12px;
}

.news-content {
    font-size: 1.1rem;
    line-height: 1.8;
    color: #333;
    margin-bottom: 40px;
}

.no-content-message {
    background: #fff3cd;
    border: 1px solid #ffeaa7;
    color: #856404;
    padding: 20px;
    border-radius: 8px;
    text-align: center;
    font-style: italic;
}

/* Article Not Found Styles */
.article-not-found {
    padding: 60px 0;
    text-align: center;
}

.not-found-content {
    max-width: 600px;
    margin: 0 auto;
}

.not-found-icon {
    font-size: 4rem;
    color: #f39c12;
    margin-bottom: 20px;
}

.article-not-found h1 {
    font-size: 2.5rem;
    color: #333;
    margin-bottom: 20px;
}

.error-message {
    font-size: 1.1rem;
    color: #666;
    margin-bottom: 30px;
    line-height: 1.6;
}

.not-found-actions {
    display: flex;
    gap: 15px;
    justify-content: center;
    margin-bottom: 50px;
    flex-wrap: wrap;
}

.suggested-articles {
    margin-top: 50px;
    text-align: left;
}

.suggested-articles h3 {
    font-size: 1.5rem;
    color: #333;
    margin-bottom: 30px;
    text-align: center;
}

.suggested-articles-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 20px;
}

.suggested-article {
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    overflow: hidden;
    transition: all 0.3s ease;
    background: white;
}

.suggested-article:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.1);
}

.suggested-article-image {
    height: 120px;
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    transition: transform 0.3s ease;
}

.suggested-article:hover .suggested-article-image {
    transform: scale(1.05);
}

.suggested-article-content {
    padding: 15px;
}

.article-category {
    font-size: 0.7rem;
    font-weight: 600;
    text-transform: uppercase;
    color: var(--primary-color);
    margin-bottom: 8px;
}

.suggested-article-content h4 {
    font-size: 0.9rem;
    font-weight: 600;
    margin-bottom: 8px;
    line-height: 1.3;
}

.suggested-article-content h4 a {
    color: #333;
    text-decoration: none;
    transition: color 0.3s ease;
}

.suggested-article-content h4 a:hover {
    color: var(--primary-color);
}

.article-date {
    font-size: 0.8rem;
    color: #666;
    display: flex;
    align-items: center;
    gap: 5px;
}

.news-actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 20px;
    padding: 30px 0;
    border-top: 1px solid #eee;
    border-bottom: 1px solid #eee;
    margin-bottom: 40px;
}

.share-buttons {
    display: flex;
    align-items: center;
    gap: 15px;
    flex-wrap: wrap;
}

.share-label {
    font-weight: 600;
    color: #666;
    margin-right: 10px;
}

.share-btn {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    text-decoration: none;
    transition: all 0.3s ease;
    font-size: 1rem;
}

.share-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.2);
}

.share-btn.facebook {
    background: #3b5998;
}

.share-btn.twitter {
    background: #1da1f2;
}

.share-btn.whatsapp {
    background: #25d366;
}

.share-btn.email {
    background: #666;
}

.related-news {
    margin-top: 60px;
}

.related-news h3 {
    font-size: 1.8rem;
    margin-bottom: 30px;
    color: #333;
    border-bottom: 2px solid var(--primary-color);
    padding-bottom: 10px;
}

.related-news-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 30px;
}

.related-news-item {
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    overflow: hidden;
    transition: all 0.3s ease;
    background: white;
}

.related-news-item:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
}

.related-news-image {
    height: 150px;
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    transition: transform 0.3s ease;
}

.related-news-item:hover .related-news-image {
    transform: scale(1.05);
}

.related-news-content {
    padding: 20px;
}

.related-news-content .news-tag {
    font-size: 0.7rem;
    font-weight: 600;
    text-transform: uppercase;
    color: var(--primary-color);
    margin-bottom: 10px;
}

.related-news-content h4 {
    font-size: 1rem;
    font-weight: 600;
    margin-bottom: 10px;
    line-height: 1.3;
    color: #333;
}

.news-date {
    font-size: 0.85rem;
    color: #666;
    margin-bottom: 15px;
}

.related-news-content .read-more {
    font-size: 0.9rem;
    color: var(--primary-color);
    text-decoration: none;
    font-weight: 600;
}

.related-news-content .read-more:hover {
    text-decoration: underline;
}

@media (max-width: 768px) {
    .news-detail-header h1 {
        font-size: 2rem;
    }
    
    .news-meta {
        flex-direction: column;
        gap: 10px;
    }
    
    .news-featured-image {
        height: 250px;
        margin-bottom: 20px;
        border-radius: 8px;
    }
    
    .image-overlay {
        border-radius: 8px;
    }
    
    .news-actions {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .share-buttons {
        width: 100%;
        justify-content: center;
    }
    
    .related-news-grid {
        grid-template-columns: 1fr;
    }
    
    /* Article Not Found Mobile Styles */
    .article-not-found h1 {
        font-size: 2rem;
    }
    
    .not-found-actions {
        flex-direction: column;
        align-items: center;
    }
    
    .not-found-actions .btn {
        width: 200px;
        justify-content: center;
    }
    
    .suggested-articles-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 480px) {
    .news-detail {
        padding: 20px 0 60px;
    }
    
    .news-detail-header h1 {
        font-size: 1.6rem;
    }
    
    .news-featured-image {
        height: 200px;
        margin-bottom: 15px;
        border-radius: 6px;
    }
    
    .image-overlay {
        border-radius: 6px;
    }
    
    .news-content {
        font-size: 1rem;
    }
}
</style>

<?php include 'components/newsletter.php'; ?>
<?php include 'components/footer.php'; ?>
