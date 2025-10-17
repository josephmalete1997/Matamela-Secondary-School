<?php
header('Content-Type: application/json');
require_once 'includes/config.php';
require_once 'includes/Database.php';

// Initialize database connection
$db = new Database();

// Get collection ID from request
$collection_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$collection_id) {
    echo json_encode(['success' => false, 'error' => 'Invalid collection ID']);
    exit;
}

try {
    // Get collection details
    $collection = $db->getRow(
        "SELECT * FROM gallery_collections WHERE id = ? AND status = 'active'", 
        [$collection_id]
    );
    
    if (!$collection) {
        echo json_encode(['success' => false, 'error' => 'Collection not found']);
        exit;
    }
    
    // Get all images in this collection
    $images = $db->getRows(
        "SELECT * FROM gallery_images WHERE collection_id = ? ORDER BY sort_order ASC, created_at ASC", 
        [$collection_id]
    );
    
    // If no images in gallery_images table, try to get from old gallery table as fallback
    if (empty($images)) {
        $legacy_images = $db->getRows(
            "SELECT id, image as image_path, title FROM gallery WHERE category = ? AND status = 'active' ORDER BY created_at ASC",
            [$collection['category']]
        );
        $images = $legacy_images;
    }
    
    echo json_encode([
        'success' => true,
        'collection' => $collection,
        'images' => $images
    ]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
}
?> 