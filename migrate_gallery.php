<?php
require_once 'includes/config.php';
require_once 'includes/Database.php';

$db = new Database();

echo "Starting gallery collections migration...\n";

// Create gallery_collections table
$create_collections = "
CREATE TABLE IF NOT EXISTS gallery_collections (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    cover_image VARCHAR(255),
    category VARCHAR(50) NOT NULL,
    event_date DATE,
    folder_name VARCHAR(100) NOT NULL UNIQUE,
    status ENUM('active', 'deleted') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";

try {
    $db->query($create_collections);
    echo "✓ Created gallery_collections table\n";
} catch (Exception $e) {
    echo "• gallery_collections table already exists\n";
}

// Create gallery_images table
$create_images = "
CREATE TABLE IF NOT EXISTS gallery_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    collection_id INT NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    title VARCHAR(255),
    description TEXT,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (collection_id) REFERENCES gallery_collections(id) ON DELETE CASCADE
)";

try {
    $db->query($create_images);
    echo "✓ Created gallery_images table\n";
} catch (Exception $e) {
    echo "• gallery_images table already exists\n";
}

// Check if we need to migrate data
$existing_collections = $db->getCount("SELECT COUNT(*) FROM gallery_collections");
$old_gallery_items = $db->getCount("SELECT COUNT(*) FROM gallery WHERE status = 'active'");

if ($existing_collections == 0 && $old_gallery_items > 0) {
    echo "Migrating existing gallery data...\n";
    
    // Get unique categories to create collections
    $categories = $db->getRows("SELECT DISTINCT category FROM gallery WHERE status = 'active'");
    
    foreach ($categories as $cat) {
        $category = $cat['category'];
        
        // Get a representative image for this category
        $sample_image = $db->getRow("SELECT * FROM gallery WHERE category = ? AND status = 'active' ORDER BY created_at DESC LIMIT 1", [$category]);
        
        if ($sample_image) {
            // Create collection
            $collection_data = [
                'title' => ucfirst($category) . ' Gallery',
                'description' => 'Collection of ' . $category . ' images',
                'cover_image' => $sample_image['image'],
                'category' => $category,
                'event_date' => $sample_image['event_date'],
                'folder_name' => $category . '_collection',
                'status' => 'active'
            ];
            
            $collection_id = $db->insert('gallery_collections', $collection_data);
            echo "✓ Created collection: {$collection_data['title']}\n";
            
            // Get all images in this category
            $category_images = $db->getRows("SELECT * FROM gallery WHERE category = ? AND status = 'active'", [$category]);
            
            foreach ($category_images as $index => $image) {
                $image_data = [
                    'collection_id' => $collection_id,
                    'image_path' => $image['image'],
                    'title' => $image['title'],
                    'description' => $image['description'],
                    'sort_order' => $index
                ];
                
                $db->insert('gallery_images', $image_data);
            }
            
            echo "  → Added " . count($category_images) . " images\n";
        }
    }
} else {
    echo "• No migration needed (collections already exist or no old data)\n";
}

echo "\nMigration completed successfully!\n";
echo "You can now use the new gallery collections system.\n";
?> 