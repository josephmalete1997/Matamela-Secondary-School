<?php
$title = "Collection";
$page = "collection";

// Include database connection
require_once 'includes/config.php';
require_once 'includes/Database.php';

// Initialize database connection
$db = new Database();

// Get collection ID from URL
$collection_id = isset($_GET['id']) ? intval($_GET['id']) : null;

// Debug: Log the collection ID being requested (remove this in production)
// error_log("Collection.php: Requested collection ID: " . var_export($collection_id, true));

if (!$collection_id) {
    header('Location: gallery');
    exit;
}

// Get collection details
try {
    $collection = $db->getRow("SELECT * FROM gallery_collections WHERE id = ? AND status = 'active'", [$collection_id]);
    
    if (!$collection) {
        header('Location: gallery');
        exit;
    }
    
    // Update page title
    $title = $collection['title'] . " - Collection";
    
    // Get collection images (we already verified collection is active above)
    $collection_images = $db->getRows("SELECT * FROM gallery_images WHERE collection_id = ? ORDER BY sort_order ASC, created_at ASC", [$collection_id]);

    
} catch (Exception $e) {
    header('Location: gallery');
    exit;
}

include 'components/header.php';
?>

<div class="collection-page">
    <!-- Collection Header -->
    <?php
    $hero_bg = !empty($collection['cover_image']) ? $collection['cover_image'] : 'images/categories/campus/campus_life_01.jpg';
    ?>
    <section class="collection-header" style="background: linear-gradient(135deg, rgba(0, 0, 0, 0.5) 0%, rgba(0, 0, 0, 0.9) 100%), url('<?php echo $hero_bg; ?>') center/cover; background-attachment: fixed;">
        <div class="hero-overlay"></div>
        <div class="container">
            <div class="collection-breadcrumb">
                <a href="index">Home</a>
                <span>/</span>
                <a href="gallery">Gallery</a>
                <span>/</span>
                <span class="current"><?php echo htmlspecialchars($collection['title']); ?></span>
            </div>
            
            <div class="collection-info">
                <h1 class="collection-title"><?php echo htmlspecialchars($collection['title']); ?></h1>
                
                <?php if (!empty($collection['description'])): ?>
                    <p class="collection-description"><?php echo htmlspecialchars($collection['description']); ?></p>
                <?php endif; ?>
                
                <div class="collection-meta">
                    <span class="collection-category">
                        <i class="fas fa-tag"></i>
                        <?php echo ucfirst(str_replace('_', ' ', $collection['category'])); ?>
                    </span>
                    
                    <?php if (!empty($collection['event_date'])): ?>
                        <span class="collection-date">
                            <i class="fas fa-calendar"></i>
                            <?php echo date('F j, Y', strtotime($collection['event_date'])); ?>
                        </span>
                    <?php endif; ?>
                    
                    <span class="collection-count">
                        <i class="fas fa-images"></i>
                        <?php 
                        $image_count = count($collection_images);
                        echo $image_count . ' ' . ($image_count === 1 ? 'Image' : 'Images'); 
                        ?>
                    </span>
                </div>
            </div>
        </div>
    </section>

    <!-- Collection Images Grid -->
    <section class="collection-gallery">
        <div class="container">




            <?php if (!empty($collection_images)): ?>
                <div class="images-grid">
                    <?php foreach ($collection_images as $index => $image): ?>
                        <div class="image-item" onclick="openLightbox(<?php echo $index; ?>)">
                            <img src="<?php echo htmlspecialchars($image['image_path']); ?>" 
                                 alt="<?php echo htmlspecialchars($image['title'] ?? 'Collection Image'); ?>"
                                 loading="lazy">
                            <div class="image-overlay">
                                <i class="fas fa-search-plus"></i>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="no-images">
                    <i class="fas fa-images"></i>
                    <h3>No Images Found</h3>
                    <p>This collection doesn't have any images yet.</p>
                    <a href="gallery.php" class="btn btn-primary">Back to Gallery</a>
                </div>
            <?php endif; ?>
        </div>
    </section>
</div>

<!-- Lightbox Modal -->
<div id="lightbox" class="lightbox" onclick="closeLightbox()">
    <div class="lightbox-content" onclick="event.stopPropagation()">
        <span class="lightbox-close" onclick="closeLightbox()">&times;</span>
        
        <div class="lightbox-image-container">
            <img id="lightbox-image" src="" alt="">
            
            <!-- Navigation arrows -->
            <button class="lightbox-nav lightbox-prev" onclick="changeImage(-1)">
                <i class="fas fa-chevron-left"></i>
            </button>
            <button class="lightbox-nav lightbox-next" onclick="changeImage(1)">
                <i class="fas fa-chevron-right"></i>
            </button>
        </div>
        
        <!-- Image info -->
        <div class="lightbox-info">
            <h4 id="lightbox-title"></h4>
            <p id="lightbox-description"></p>
            <div class="lightbox-counter">
                <span id="lightbox-current">1</span> / <span id="lightbox-total"><?php echo count($collection_images); ?></span>
            </div>
        </div>
        
        <!-- Thumbnails -->
        <div class="lightbox-thumbnails">
            <?php foreach ($collection_images as $index => $image): ?>
                <img src="<?php echo htmlspecialchars($image['image_path']); ?>" 
                     alt="Thumbnail <?php echo $index + 1; ?>"
                     onclick="showImage(<?php echo $index; ?>)"
                     class="thumbnail <?php echo $index === 0 ? 'active' : ''; ?>">
            <?php endforeach; ?>
        </div>
    </div>
</div>

<script>
const collectionImages = <?php echo json_encode($collection_images); ?>;
let currentImageIndex = 0;

function openLightbox(index) {
    currentImageIndex = index;
    showImage(index);
    document.getElementById('lightbox').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function closeLightbox() {
    document.getElementById('lightbox').style.display = 'none';
    document.body.style.overflow = 'auto';
}

function changeImage(direction) {
    currentImageIndex += direction;
    
    if (currentImageIndex >= collectionImages.length) {
        currentImageIndex = 0;
    } else if (currentImageIndex < 0) {
        currentImageIndex = collectionImages.length - 1;
    }
    
    showImage(currentImageIndex);
}

function showImage(index) {
    currentImageIndex = index;
    const image = collectionImages[index];
    
    document.getElementById('lightbox-image').src = image.image_path;
    document.getElementById('lightbox-title').textContent = image.title.slice(0,15) + "..."|| 'Collection Image';
    document.getElementById('lightbox-description').textContent = image.description || '';
    document.getElementById('lightbox-current').textContent = index + 1;
    
    // Update active thumbnail
    document.querySelectorAll('.thumbnail').forEach((thumb, i) => {
        thumb.classList.toggle('active', i === index);
    });
}

// Keyboard navigation
document.addEventListener('keydown', function(e) {
    if (document.getElementById('lightbox').style.display === 'flex') {
        if (e.key === 'ArrowLeft') {
            changeImage(-1);
        } else if (e.key === 'ArrowRight') {
            changeImage(1);
        } else if (e.key === 'Escape') {
            closeLightbox();
        }
    }
});
</script>

<style>
/* Collection Page Styles */
.collection-page {
    min-height: 100vh;
    background: #f8f9fa;
}

/* Collection Header */
.collection-header {
    color: white;
    padding: 120px 0 80px 0;
    margin-top: -80px;
    position: relative;
    overflow: hidden;
}

.hero-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.3);
    z-index: 1;
}

.collection-header .container {
    position: relative;
    z-index: 2;
}

.collection-breadcrumb {
    margin-bottom: 30px;
    font-size: 0.9rem;
    opacity: 0.9;
}

.collection-breadcrumb a {
    color: white;
    text-decoration: none;
    transition: opacity 0.3s ease;
}

.collection-breadcrumb a:hover {
    opacity: 0.8;
}

.collection-breadcrumb span {
    margin: 0 10px;
    opacity: 0.7;
}

.collection-breadcrumb .current {
    font-weight: 500;
}

.collection-title {
    font-size: 3rem;
    font-weight: 700;
    margin-bottom: 20px;
    text-shadow: 0 2px 4px rgba(0,0,0,0.3);
    color: white;
}

.collection-description {
    font-size: 1.2rem;
    line-height: 1.6;
    margin-bottom: 30px;
    opacity: 0.95;
    max-width: 800px;
    color: rgba(255, 255, 255, 0.5);
}

.collection-meta {
    display: flex;
    gap: 30px;
    flex-wrap: wrap;
}

.collection-meta span {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 0.95rem;
    background: rgba(255,255,255,0.1);
    padding: 8px 16px;
    border-radius: 20px;
    backdrop-filter: blur(10px);
}

.collection-meta i {
    opacity: 0.8;
}

/* Collection Gallery */
.collection-gallery {
    padding: 80px 0;
}

.images-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 20px;
}

.image-item {
    position: relative;
    aspect-ratio: 1;
    border-radius: 12px;
    overflow: hidden;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.image-item:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.image-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.image-item:hover img {
    transform: scale(1.05);
}

.image-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.image-item:hover .image-overlay {
    opacity: 1;
}

.image-overlay i {
    color: white;
    font-size: 2rem;
}

/* No Images State */
.no-images {
    text-align: center;
    padding: 80px 20px;
    color: #666;
}

.no-images i {
    font-size: 4rem;
    margin-bottom: 20px;
    opacity: 0.5;
}

.no-images h3 {
    margin-bottom: 10px;
    color: #333;
}

/* Lightbox Styles */
.lightbox {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.9);
    z-index: 1000;
    align-items: center;
    justify-content: center;
}

.lightbox-content {
    max-width: 90vw;
    max-width: 50vw;
    max-height: 95vh;
    background: white;
    border-radius: 12px;
    overflow: hidden;
    position: relative;
}

.lightbox-close {
    position: absolute;
    top: 15px;
    right: 20px;
    font-size: 2rem;
    color: white;
    cursor: pointer;
    z-index: 10;
    background: rgba(0,0,0,0.5);
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background 0.3s ease;
}

.lightbox-close:hover {
    background: rgba(0,0,0,0.7);
}

.lightbox-image-container {
    position: relative;
    max-height: 70vh;
    overflow: hidden;
}

.lightbox-image-container img {
    width: 100%;
    height: auto;
    display: block;
}

.lightbox-nav {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    background: rgba(0,0,0,0.5);
    color: white;
    border: none;
    width: 50px;
    height: 50px;
    border-radius: 50%;
    cursor: pointer;
    font-size: 1.2rem;
    transition: background 0.3s ease;
}

.lightbox-nav:hover {
    background: rgba(0,0,0,0.7);
}

.lightbox-prev {
    left: 20px;
}

.lightbox-next {
    right: 20px;
}

.lightbox-info {
    padding: 20px;
    background: white;
}

.lightbox-info h4 {
    margin: 0 0 10px 0;
    color: #333;
}

.lightbox-info p {
    margin: 0 0 15px 0;
    color: #666;
}

.lightbox-counter {
    font-size: 0.9rem;
    color: #999;
    text-align: center;
}

.lightbox-thumbnails {
    display: flex;
    gap: 10px;
    padding: 20px;
    background: #f8f9fa;
    overflow-x: auto;
    max-height: 120px;
}

.thumbnail {
    width: 80px;
    height: 80px;
    object-fit: cover;
    border-radius: 8px;
    cursor: pointer;
    opacity: 0.6;
    transition: opacity 0.3s ease;
    flex-shrink: 0;
}

.thumbnail:hover,
.thumbnail.active {
    opacity: 1;
}

.thumbnail.active {
    border: 3px solid #d7302f;
}

/* Responsive Design */
@media (max-width: 768px) {
    .collection-header {
        padding: 100px 0 60px 0;
    }
    
    .collection-title {
        font-size: 2rem;
    }
    
    .collection-description {
        font-size: 1rem;
    }
    
    .collection-meta {
        gap: 15px;
    }
    
    .collection-meta span {
        font-size: 0.85rem;
        padding: 6px 12px;
    }
    
    .images-grid {
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 15px;
    }
    
    .lightbox-content {
        max-width: 95vw;
        max-height: 95vh;
    }
    
    .lightbox-nav {
        width: 40px;
        height: 40px;
        font-size: 1rem;
    }
    
    .lightbox-prev {
        left: 10px;
    }
    
    .lightbox-next {
        right: 10px;
    }
    
    .thumbnail {
        width: 60px;
        height: 60px;
    }
}

@media (max-width: 480px) {
    .collection-title {
        font-size: 1.5rem;
    }
    
    .images-grid {
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 10px;
    }
    
    .lightbox-info {
        padding: 15px;
    }
    
    .lightbox-thumbnails {
        padding: 15px;
        gap: 8px;
    }
}
</style>

<?php include 'components/newsletter.php'; ?>

<?php include 'components/footer.php'; ?> 