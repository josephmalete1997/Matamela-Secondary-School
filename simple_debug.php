<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "Starting debug...\n";

try {
    require_once 'includes/config.php';
    echo "Config loaded\n";
    
    require_once 'includes/Database.php';
    echo "Database class loaded\n";
    
    $db = new Database();
    echo "Database connected\n";
    
    // Test basic query
    $test = $db->getRows("SELECT COUNT(*) as count FROM gallery_collections");
    echo "Collections count: " . $test[0]['count'] . "\n";
    
    // Test specific collection
    $collection_id = 21;
    $collection = $db->getRow("SELECT * FROM gallery_collections WHERE id = ?", [$collection_id]);
    
    if ($collection) {
        echo "Collection 21 found: " . $collection['title'] . "\n";
        
        $images = $db->getRows("SELECT * FROM gallery_images WHERE collection_id = ?", [$collection_id]);
        echo "Images in collection 21: " . count($images) . "\n";
        
        foreach ($images as $image) {
            echo "- " . $image['title'] . " (" . $image['image_path'] . ")\n";
        }
    } else {
        echo "Collection 21 not found\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
?> 