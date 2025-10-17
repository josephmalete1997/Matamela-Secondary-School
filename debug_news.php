<?php
require_once 'includes/config.php';
require_once 'includes/Database.php';

// Initialize database connection
$db = new Database();

echo "<h2>Debug News Detail Issue</h2>";

// Check if news with ID 5 exists
$news_id = 5;
echo "<h3>1. Checking for News ID: $news_id</h3>";

try {
    $news_data = $db->getRow("
        SELECT * FROM news_events 
        WHERE id = ?
    ", [$news_id]);
    
    if ($news_data) {
        echo "<p><strong>✅ News found!</strong></p>";
        echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
        echo "<tr><th style='padding: 5px;'>Field</th><th style='padding: 5px;'>Value</th></tr>";
        foreach ($news_data as $key => $value) {
            echo "<tr><td style='padding: 5px;'>" . htmlspecialchars($key) . "</td>";
            echo "<td style='padding: 5px;'>" . htmlspecialchars($value ?? 'NULL') . "</td></tr>";
        }
        echo "</table>";
        
        // Check status specifically
        if ($news_data['status'] !== 'published') {
            echo "<p><strong>❌ Issue: Status is '{$news_data['status']}' but should be 'published'</strong></p>";
        } else {
            echo "<p><strong>✅ Status is 'published'</strong></p>";
        }
    } else {
        echo "<p><strong>❌ No news found with ID $news_id</strong></p>";
    }
} catch (Exception $e) {
    echo "<p><strong>❌ Database Error:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
}

// List all news items
echo "<h3>2. All News Items</h3>";
try {
    $all_news = $db->getRows("SELECT id, title, status, created_at FROM news_events ORDER BY id");
    
    if ($all_news) {
        echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
        echo "<tr><th style='padding: 5px;'>ID</th><th style='padding: 5px;'>Title</th><th style='padding: 5px;'>Status</th><th style='padding: 5px;'>Created</th></tr>";
        foreach ($all_news as $item) {
            echo "<tr>";
            echo "<td style='padding: 5px;'>" . htmlspecialchars($item['id']) . "</td>";
            echo "<td style='padding: 5px;'>" . htmlspecialchars($item['title']) . "</td>";
            echo "<td style='padding: 5px;'>" . htmlspecialchars($item['status']) . "</td>";
            echo "<td style='padding: 5px;'>" . htmlspecialchars($item['created_at']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No news items found in database.</p>";
    }
} catch (Exception $e) {
    echo "<p><strong>❌ Database Error:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<h3>3. Test URL</h3>";
echo "<p>Try these links:</p>";
echo "<ul>";
if ($all_news) {
    foreach ($all_news as $item) {
        echo "<li><a href='news-detail.php?id={$item['id']}'>{$item['title']} (ID: {$item['id']})</a></li>";
    }
}
echo "</ul>";
?>



