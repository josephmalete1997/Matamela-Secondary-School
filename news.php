<?php
$title = "News & Events";
$page = "news";
$subtitle = "Stay updated with the latest happenings at Matamela School";
require_once 'includes/config.php';
require_once 'includes/Database.php';

// Initialize database connection
$db = new Database();

// Get pagination parameters
$page_num = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$per_page = 6;
$offset = ($page_num - 1) * $per_page;

// Get filter parameters
$category_filter = isset($_GET['category']) ? $_GET['category'] : 'all';
$search_filter = isset($_GET['search']) ? trim($_GET['search']) : '';

// Build WHERE clause
$where_conditions = ["status = 'published'"];
$params = [];

if ($category_filter !== 'all') {
    $where_conditions[] = "category = ?";
    $params[] = ucfirst($category_filter);
}

if (!empty($search_filter)) {
    $where_conditions[] = "(title LIKE ? OR content LIKE ?)";
    $params[] = "%{$search_filter}%";
    $params[] = "%{$search_filter}%";
}

$where_clause = implode(' AND ', $where_conditions);

// Get total count for pagination
try {
    $count_result = $db->getRow("SELECT COUNT(*) as total FROM news_events WHERE {$where_clause}", $params);
    $total_news = $count_result ? $count_result['total'] : 0;
} catch (Exception $e) {
    $total_news = 0;
}

$total_pages = ceil($total_news / $per_page);

// Get news and events from database
try {
    $news_events = $db->getRows("
        SELECT id, title, content, category, event_date, event_time, location, 
               featured_image, author, created_at
        FROM news_events 
        WHERE {$where_clause}
        ORDER BY 
            CASE WHEN event_date IS NOT NULL THEN event_date ELSE created_at END DESC
        LIMIT {$per_page} OFFSET {$offset}
    ", $params);
} catch (Exception $e) {
    $news_events = [];
    error_log("News query error: " . $e->getMessage());
}

// Get featured news (latest published news/event)
try {
    $featured_news = $db->getRow("
        SELECT * FROM news_events 
        WHERE status = 'published' 
        ORDER BY created_at DESC 
        LIMIT 1
    ");
} catch (Exception $e) {
    $featured_news = null;
}

// Get upcoming events for the calendar
try {
    $upcoming_events = $db->getRows("
        SELECT id, title, event_date, event_time, location
        FROM news_events 
        WHERE status = 'published' 
        AND category = 'Event' 
        AND event_date >= CURDATE()
        ORDER BY event_date ASC
        LIMIT 10
    ");
} catch (Exception $e) {
    $upcoming_events = [];
}

include 'components/header.php';
?>

<section class="news-filter">
    <div class="container">
        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="alert alert-error" style="margin-bottom: 20px; padding: 12px 16px; background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; border-radius: 4px;">
                <?php echo htmlspecialchars($_SESSION['error_message']); unset($_SESSION['error_message']); ?>
            </div>
        <?php endif; ?>
        
        <div class="filter-options">
            <button class="filter-btn <?php echo $category_filter === 'all' ? 'active' : ''; ?>" data-filter="all">All</button>
            <button class="filter-btn <?php echo $category_filter === 'news' ? 'active' : ''; ?>" data-filter="news">News</button>
            <button class="filter-btn <?php echo $category_filter === 'event' ? 'active' : ''; ?>" data-filter="events">Events</button>
            <button class="filter-btn <?php echo $category_filter === 'notice' ? 'active' : ''; ?>" data-filter="notice">Notices</button>
            <button class="filter-btn <?php echo $category_filter === 'announcement' ? 'active' : ''; ?>" data-filter="announcements">Announcements</button>
        </div>
        <div class="search-box">
            <form method="GET" class="search-form">
                <input type="hidden" name="category" value="<?php echo htmlspecialchars($category_filter); ?>">
                <input type="text" name="search" placeholder="Search news & events..." value="<?php echo htmlspecialchars($search_filter); ?>">
                <button type="submit"><i class="fas fa-search"></i></button>
            </form>
        </div>
    </div>
</section>

<?php if ($featured_news): ?>
<section class="featured-news">
    <div class="container">
        <div class="featured-news-item">
            <div class="featured-news-image">
                <img src="<?php echo !empty($featured_news['featured_image']) ? htmlspecialchars($featured_news['featured_image']) : 'images/categories/events/school_event_01.jpg'; ?>" alt="<?php echo htmlspecialchars($featured_news['title']); ?>">
            </div>
            <div class="featured-news-content">
                <div class="news-tag"><?php echo htmlspecialchars($featured_news['category']); ?></div>
                <h2><?php echo htmlspecialchars($featured_news['title']); ?></h2>
                <div class="news-meta">
                    <?php if (!empty($featured_news['event_date'])): ?>
                        <span><i class="fas fa-calendar"></i> <?php echo date('F j, Y', strtotime($featured_news['event_date'])); ?></span>
                    <?php else: ?>
                        <span><i class="fas fa-calendar"></i> <?php echo date('F j, Y', strtotime($featured_news['created_at'])); ?></span>
                    <?php endif; ?>
                    
                    <?php if (!empty($featured_news['event_time'])): ?>
                        <span><i class="fas fa-clock"></i> <?php echo date('g:i A', strtotime($featured_news['event_time'])); ?></span>
                    <?php endif; ?>
                    
                    <?php if (!empty($featured_news['location'])): ?>
                        <span><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($featured_news['location']); ?></span>
                    <?php endif; ?>
                </div>
                <p><?php echo htmlspecialchars(substr(strip_tags($featured_news['content']), 0, 300)) . (strlen(strip_tags($featured_news['content'])) > 300 ? '...' : ''); ?></p>
                <a href="news-detail.php?id=<?php echo $featured_news['id']; ?>" class="btn">Learn More</a>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<section class="news-grid">
    <div class="container">
        <div class="news-items">
            <?php if (!empty($news_events)): ?>
                <?php foreach ($news_events as $item): ?>
                <div class="news-item" data-category="<?php echo strtolower($item['category']); ?>">
                    <div class="news-image">
                        <img src="<?php echo !empty($item['featured_image']) ? htmlspecialchars($item['featured_image']) : 'images/categories/events/school_event_01.jpg'; ?>" alt="<?php echo htmlspecialchars($item['title']); ?>">
                    </div>
                    <div class="news-content">
                        <div class="news-tag"><?php echo htmlspecialchars($item['category']); ?></div>
                        <h3><?php echo htmlspecialchars($item['title']); ?></h3>
                        <div class="news-meta">
                            <?php if (!empty($item['event_date'])): ?>
                                <span><i class="fas fa-calendar"></i> <?php echo date('F j, Y', strtotime($item['event_date'])); ?></span>
                            <?php else: ?>
                                <span><i class="fas fa-calendar"></i> <?php echo date('F j, Y', strtotime($item['created_at'])); ?></span>
                            <?php endif; ?>
                            
                            <?php if (!empty($item['event_time'])): ?>
                                <span><i class="fas fa-clock"></i> <?php echo date('g:i A', strtotime($item['event_time'])); ?></span>
                            <?php endif; ?>
                        </div>
                        <p><?php echo htmlspecialchars(substr(strip_tags($item['content']), 0, 150)) . (strlen(strip_tags($item['content'])) > 150 ? '...' : ''); ?></p>
                        <a href="news-detail.php?id=<?php echo $item['id']; ?>" class="read-more">Read More</a>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-news">
                    <div style="text-align: center; padding: 40px;">
                        <i class="fas fa-newspaper" style="font-size: 3rem; color: #ccc; margin-bottom: 20px;"></i>
                        <h3>No news or events found</h3>
                        <p>Try adjusting your search criteria or check back later for updates.</p>
                        <?php if (!empty($search_filter) || $category_filter !== 'all'): ?>
                            <a href="news.php" class="btn btn-primary">Show All News</a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <?php if ($total_pages > 1): ?>
        <div class="pagination">
            <?php if ($page_num > 1): ?>
                <a href="?page=<?php echo $page_num - 1; ?>&category=<?php echo urlencode($category_filter); ?>&search=<?php echo urlencode($search_filter); ?>" class="prev">
                    <i class="fas fa-chevron-left"></i> Previous
                </a>
            <?php endif; ?>
            
            <?php
            $start_page = max(1, $page_num - 2);
            $end_page = min($total_pages, $page_num + 2);
            
            for ($i = $start_page; $i <= $end_page; $i++):
            ?>
                <a href="?page=<?php echo $i; ?>&category=<?php echo urlencode($category_filter); ?>&search=<?php echo urlencode($search_filter); ?>" 
                   class="<?php echo $i === $page_num ? 'active' : ''; ?>">
                    <?php echo $i; ?>
                </a>
            <?php endfor; ?>
            
            <?php if ($page_num < $total_pages): ?>
                <a href="?page=<?php echo $page_num + 1; ?>&category=<?php echo urlencode($category_filter); ?>&search=<?php echo urlencode($search_filter); ?>" class="next">
                    Next <i class="fas fa-chevron-right"></i>
                </a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php if (!empty($upcoming_events)): ?>
<section class="upcoming-events">
    <div class="container">
        <h2>Upcoming Events</h2>
        <div class="event-list">
            <?php foreach ($upcoming_events as $event): ?>
            <div class="event-item">
                <div class="event-date">
                    <span class="day"><?php echo date('j', strtotime($event['event_date'])); ?></span>
                    <span class="month"><?php echo date('M', strtotime($event['event_date'])); ?></span>
                </div>
                <div class="event-details">
                    <h3><?php echo htmlspecialchars($event['title']); ?></h3>
                    <?php if (!empty($event['event_time'])): ?>
                        <p><i class="fas fa-clock"></i> <?php echo date('g:i A', strtotime($event['event_time'])); ?></p>
                    <?php endif; ?>
                    <?php if (!empty($event['location'])): ?>
                        <p><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($event['location']); ?></p>
                    <?php endif; ?>
                </div>
                <div class="event-actions">
                    <a href="news-detail.php?id=<?php echo $event['id']; ?>" class="btn btn-sm">View Details</a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php include 'components/newsletter.php'; ?>

<script>
// News Page Interactive Functionality
document.addEventListener('DOMContentLoaded', function() {
    // Filter functionality
    const filterButtons = document.querySelectorAll('.filter-btn');
    
    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            const filter = this.getAttribute('data-filter');
            const currentUrl = new URL(window.location);
            
            // Update URL parameters
            if (filter === 'all') {
                currentUrl.searchParams.delete('category');
            } else {
                currentUrl.searchParams.set('category', filter);
            }
            currentUrl.searchParams.delete('page'); // Reset to first page
            
            // Navigate to new URL
            window.location.href = currentUrl.toString();
        });
    });
    
    // Search form handling
    const searchForm = document.querySelector('.search-form');
    if (searchForm) {
        searchForm.addEventListener('submit', function(e) {
            const searchInput = this.querySelector('input[name="search"]');
            if (!searchInput.value.trim()) {
                searchInput.removeAttribute('name'); // Don't include empty search
            }
        });
    }
    
    // Add CSS animations
    const style = document.createElement('style');
    style.textContent = `
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
        
        .news-item {
            animation: fadeInUp 0.6s ease forwards;
        }
        
        .event-item {
            animation: fadeInUp 0.6s ease forwards;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 15px;
            background: white;
            display: flex;
            align-items: center;
            gap: 20px;
            transition: all 0.3s ease;
        }
        
        .event-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .event-actions {
            margin-left: auto;
        }
        
        .no-news {
            grid-column: 1 / -1;
        }
        
        .filter-btn {
            position: relative;
            overflow: hidden;
        }
        
        .filter-btn::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            background: rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            transform: translate(-50%, -50%);
            transition: all 0.4s ease;
        }
        
        .filter-btn:active::before {
            width: 100px;
            height: 100px;
        }
    `;
    document.head.appendChild(style);
    
    // Stagger animation for initial load
    const newsItems = document.querySelectorAll('.news-item');
    newsItems.forEach((item, index) => {
        item.style.animationDelay = `${index * 0.1}s`;
    });
    
    const eventItems = document.querySelectorAll('.event-item');
    eventItems.forEach((item, index) => {
        item.style.animationDelay = `${index * 0.1}s`;
    });
});
</script>

<?php include 'components/footer.php'; ?> 