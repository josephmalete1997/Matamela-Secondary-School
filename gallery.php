<?php
$title = "Gallery";
$page = "gallery";
$subtitle = "Explore our school life through images";
require_once 'includes/config.php';
require_once 'includes/Database.php';

// Initialize database connection
$db = new Database();

// Check if we need to create the new tables
$check_collections = $db->getRow("SHOW TABLES LIKE 'gallery_collections'");
if (!$check_collections) {
    // Run the migration script
    $migration_sql = file_get_contents('database/gallery_collections.sql');
    $statements = explode(';', $migration_sql);
    foreach ($statements as $statement) {
        $statement = trim($statement);
        if (!empty($statement)) {
            try {
                $db->query($statement);
            } catch (Exception $e) {
                // Continue if there are errors (like duplicate entries)
            }
        }
    }
}

// Get filter parameter
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';

// Get all categories for filter buttons
$categories_query = "SELECT DISTINCT category FROM gallery_collections WHERE status = 'active' ORDER BY category ASC";
$categories = $db->getRows($categories_query);

// Build query based on filter
$where_clause = "WHERE gc.status = 'active'";
if ($filter != 'all') {
    $filter = $db->escapeString($filter);
    $where_clause .= " AND gc.category = '$filter'";
}

// Get gallery collections with image count
$gallery_query = "
    SELECT gc.*, 
           COUNT(gi.id) as image_count,
           gi.image_path as sample_images
    FROM gallery_collections gc
    LEFT JOIN gallery_images gi ON gc.id = gi.collection_id
    $where_clause
    GROUP BY gc.id
    ORDER BY gc.created_at DESC
";
$gallery_collections = $db->getRows($gallery_query);

// Get collection ID if viewing specific collection
$collection_id = isset($_GET['collection']) ? intval($_GET['collection']) : null;
$collection_images = [];
$current_collection = null;

if ($collection_id) {
    // Get specific collection details
    $current_collection = $db->getRow("SELECT * FROM gallery_collections WHERE id = ? AND status = 'active'", [$collection_id]);
    
    if ($current_collection) {
        // Get all images in this collection
        $collection_images = $db->getRows("
            SELECT * FROM gallery_images 
            WHERE collection_id = ? 
            ORDER BY sort_order ASC, created_at ASC
        ", [$collection_id]);
    }
}

include 'components/header.php';
?>



<section class="gallery-section">
    <div class="container">
        <div class="gallery-filter">
            <div class="filter-options">
                <button class="filter-btn <?php echo ($filter == 'all') ? 'active' : ''; ?>" data-filter="all">All</button>
                <?php foreach ($categories as $category): ?>
                    <button class="filter-btn <?php echo ($filter == $category['category']) ? 'active' : ''; ?>" data-filter="<?php echo $category['category']; ?>">
                        <?php echo ucfirst($category['category']); ?>
                    </button>
                <?php endforeach; ?>
            </div>
        </div>
        
        <div class="gallery-collections">
            <?php if (!empty($gallery_collections)): ?>
                <?php foreach ($gallery_collections as $collection): ?>
                    <div class="gallery-collection" data-category="<?php echo $collection['category']; ?>" onclick="window.location.href='collection?id=<?php echo $collection['id']; ?>'">
                        <div class="collection-cover">
                            <img src="<?php echo $collection['cover_image']; ?>" alt="<?php echo $collection['title']; ?>">
                            <div class="collection-overlay">
                                <div class="collection-info">
                                    <h4><?php echo $collection['title']; ?></h4>
                                    <?php if (!empty($collection['description'])): ?>
                                        <p><?php echo substr($collection['description'], 0, 100) . (strlen($collection['description']) > 100 ? '...' : ''); ?></p>
                                    <?php endif; ?>
                                    <?php if (!empty($collection['event_date'])): ?>
                                        <p class="collection-date"><?php echo date('F d, Y', strtotime($collection['event_date'])); ?></p>
                                    <?php endif; ?>
                                    <p class="image-count"><?php echo $collection['image_count']; ?> image<?php echo $collection['image_count'] != 1 ? 's' : ''; ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-results">
                    <p>No gallery collections found. Please try a different filter.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>



<section class="gallery-cta">
    <div class="container">
        <div class="cta-content">
            <h2>Share Your Memories</h2>
            <p>Have photos from school events? Share them with us!</p>
            <a href="contact.php" class="btn btn-primary">Contact Us</a>
        </div>
    </div>
</section>

<script>
let currentFilter = '<?php echo $filter; ?>';

function openCollection(collectionId) {
    // Fetch collection images via AJAX
    fetch(`get-collection-images.php?id=${collectionId}`)
        .then(response => response.json())
        .then(data => {
            console.log('Collection data received:', data); // Debug log
            
            if (data.success) {
                currentCollectionImages = data.images || [];
                currentImageIndex = 0;
                
                console.log('Number of images:', currentCollectionImages.length); // Debug log
                
                // Set collection info
                document.getElementById('collection-title').textContent = data.collection.title;
                document.getElementById('collection-description').textContent = data.collection.description || '';
                
                if (data.collection.event_date) {
                    const date = new Date(data.collection.event_date);
                    document.getElementById('collection-date').textContent = date.toLocaleDateString('en-US', {
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric'
                    });
                } else {
                    document.getElementById('collection-date').textContent = '';
                }
                
                // Check if there are images
                if (currentCollectionImages.length > 0) {
                    // Create thumbnails
                    createThumbnails();
                    
                    // Show first image
                    showCurrentImage();
                    
                    // Open lightbox
                    document.getElementById('collection-lightbox').style.display = 'flex';
                    document.body.style.overflow = 'hidden';
                } else {
                    // Show empty state message
                    alert('This collection is empty. No images to display.');
                }
            } else {
                console.error('API Error:', data.error);
                alert('Error loading collection: ' + (data.error || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error loading collection:', error);
            alert('Failed to load collection. Please try again.');
        });
}

function closeLightbox() {
    document.getElementById('collection-lightbox').style.display = 'none';
    document.body.style.overflow = 'auto';
}

function navigateImage(direction) {
    currentImageIndex += direction;
    
    if (currentImageIndex >= currentCollectionImages.length) {
        currentImageIndex = 0;
    } else if (currentImageIndex < 0) {
        currentImageIndex = currentCollectionImages.length - 1;
    }
    
    showCurrentImage();
    updateActiveThumbnail();
}

function showCurrentImage() {
    if (currentCollectionImages.length > 0 && currentImageIndex >= 0 && currentImageIndex < currentCollectionImages.length) {
        const currentImage = currentCollectionImages[currentImageIndex];
        console.log('Showing image:', currentImage); // Debug log
        
        document.getElementById('lightbox-image').src = currentImage.image_path;
        document.getElementById('lightbox-image').alt = currentImage.title || '';
        document.getElementById('image-title').textContent = currentImage.title || '';
        document.getElementById('image-counter').textContent = `${currentImageIndex + 1} of ${currentCollectionImages.length}`;
        
        updateActiveThumbnail();
    } else {
        console.error('No images to show or invalid index:', currentImageIndex, currentCollectionImages.length);
    }
}

function createThumbnails() {
    const thumbnailContainer = document.getElementById('lightbox-thumbnails');
    thumbnailContainer.innerHTML = '';
    
    console.log('Creating thumbnails for', currentCollectionImages.length, 'images'); // Debug log
    
    if (currentCollectionImages.length === 0) {
        console.log('No images to create thumbnails for');
        return;
    }
    
    currentCollectionImages.forEach((image, index) => {
        console.log('Creating thumbnail for image:', image.image_path); // Debug log
        
        const thumbnail = document.createElement('div');
        thumbnail.className = 'thumbnail';
        thumbnail.onclick = () => {
            currentImageIndex = index;
            showCurrentImage();
            updateActiveThumbnail();
        };
        
        const img = document.createElement('img');
        img.src = image.image_path;
        img.alt = image.title || '';
        img.onerror = () => {
            console.error('Failed to load thumbnail image:', image.image_path);
            thumbnail.style.backgroundColor = '#f0f0f0';
            thumbnail.innerHTML = '<span style="color: #999; font-size: 12px;">Error</span>';
        };
        
        thumbnail.appendChild(img);
        thumbnailContainer.appendChild(thumbnail);
    });
}

function updateActiveThumbnail() {
    const thumbnails = document.querySelectorAll('.thumbnail');
    thumbnails.forEach((thumb, index) => {
        if (index === currentImageIndex) {
            thumb.classList.add('active');
        } else {
            thumb.classList.remove('active');
        }
    });
}

// Keyboard navigation
document.addEventListener('keydown', function(e) {
    const lightbox = document.getElementById('collection-lightbox');
    if (lightbox.style.display === 'flex') {
        switch(e.key) {
            case 'Escape':
                closeLightbox();
                break;
            case 'ArrowLeft':
                navigateImage(-1);
                break;
            case 'ArrowRight':
                navigateImage(1);
                break;
        }
    }
});

document.addEventListener('DOMContentLoaded', function() {
    // Gallery filtering
    const filterButtons = document.querySelectorAll('.filter-btn');
    
    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            const filter = this.getAttribute('data-filter');
            currentFilter = filter;
            
            // Update URL with filter parameter
            const url = new URL(window.location);
            if (filter === 'all') {
                url.searchParams.delete('filter');
            } else {
                url.searchParams.set('filter', filter);
            }
            window.history.pushState({}, '', url);
            
            // Update active class
            filterButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            
            // Filter gallery collections
            const galleryCollections = document.querySelectorAll('.gallery-collection');
            
            galleryCollections.forEach(collection => {
                if (filter === 'all' || collection.getAttribute('data-category') === filter) {
                    collection.style.display = 'block';
                } else {
                    collection.style.display = 'none';
                }
            });
        });
    });

    // Gallery filtering functionality only
    // (Collection viewing now handled by dedicated collection.php page)
});
</script>
<style>


    .filter-btn{
        background-color:rgb(255, 255, 255);
        color:rgb(0, 0, 0);
        padding: 10px 20px;
        border: none;
        border-radius: 20px;
        box-shadow: 0 0 10px 0 rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
        cursor: pointer;
        margin: 5px;
    }

    .filter-btn:hover{
        background-color: #d72f25;
        color: #fff;
    }

    .filter-btn.active{
        background-color: #d72f25;
        color: #fff;
    }

    .gallery-collections {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 20px;
        margin-top: 30px;
    }

    .gallery-collection {
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
        cursor: pointer;
        background: white;
    }

    .gallery-collection:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
    }

    .collection-cover {
        position: relative;
        height: 250px;
        overflow: hidden;
    }

    .collection-cover img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }

    .gallery-collection:hover .collection-cover img {
        transform: scale(1.05);
    }

    .collection-overlay {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background: linear-gradient(transparent, rgba(0,0,0,0.8));
        color: white;
        padding: 20px;
        transform: translateY(100%);
        transition: transform 0.3s ease;
    }

    .gallery-collection:hover .collection-overlay {
        transform: translateY(0);
    }

    .collection-info h4 {
        margin: 0 0 8px 0;
        font-size: 18px;
        font-weight: 600;
        color: white;
    }

    .collection-info p {
        margin: 4px 0;
        font-size: 13px;
        opacity: 0.9;
        color: white;
    }

    .image-count {
        background: rgba(255, 255, 255, 0.2);
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 11px !important;
        display: inline-block;
        margin-top: 8px;
    }

    /* Enhanced Lightbox Styles */
    .lightbox {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.95);
        justify-content: center;
        align-items: center;
    }

    .lightbox-content {
        position: relative;
        max-width: 95%;
        max-height: 95%;
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .lightbox-header {
        text-align: center;
        color: white;
        margin-bottom: 20px;
        max-width: 600px;
    }

    .lightbox-header h2 {
        margin: 0 0 10px 0;
        font-size: 28px;
    }

    .lightbox-header p {
        margin: 5px 0;
        opacity: 0.9;
    }

    .lightbox-close {
        position: absolute;
        top: -50px;
        right: 0;
        color: white;
        font-size: 40px;
        font-weight: bold;
        cursor: pointer;
        z-index: 1001;
    }

    .lightbox-close:hover {
        opacity: 0.7;
    }

    .lightbox-nav {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        background: rgba(255, 255, 255, 0.2);
        color: white;
        border: none;
        font-size: 30px;
        padding: 15px 20px;
        cursor: pointer;
        border-radius: 5px;
        transition: background-color 0.3s ease;
        z-index: 1001;
    }

    .lightbox-nav:hover {
        background: rgba(255, 255, 255, 0.4);
    }

    .lightbox-prev {
        left: -60px;
    }

    .lightbox-next {
        right: -60px;
    }

    .lightbox-image-container {
        text-align: center;
        margin-bottom: 20px;
    }

    #lightbox-image {
        max-width: 100%;
        max-height: 60vh;
        object-fit: contain;
        border-radius: 10px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.5);
    }

    .lightbox-info {
        text-align: center;
        color: white;
        margin-bottom: 20px;
    }

    .lightbox-info h3 {
        margin: 0 0 5px 0;
        font-size: 20px;
    }

    .lightbox-info p {
        margin: 0;
        opacity: 0.9;
    }

    .lightbox-thumbnails {
        display: flex;
        gap: 10px;
        max-width: 800px;
        overflow-x: auto;
        padding: 10px;
    }

    .thumbnail {
        flex-shrink: 0;
        width: 60px;
        height: 60px;
        border-radius: 8px;
        overflow: hidden;
        cursor: pointer;
        border: 2px solid transparent;
        transition: border-color 0.3s ease;
    }

    .thumbnail.active {
        border-color: #d72f25;
    }

    .thumbnail img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .gallery-collections {
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 15px;
        }

        .collection-cover {
            height: 200px;
        }

        .lightbox-nav {
            font-size: 24px;
            padding: 10px 15px;
        }

        .lightbox-prev {
            left: 10px;
        }

        .lightbox-next {
            right: 10px;
        }

        .lightbox-close {
            top: 10px;
            right: 10px;
            font-size: 30px;
        }

        #lightbox-image {
            max-height: 50vh;
        }

        .lightbox-thumbnails {
            max-width: 100%;
        }
    }

    /* Hero Section Responsive */
    @media (max-width: 768px) {
        .hero-section {
            min-height: 300px;
        }

        .hero-title {
            font-size: 2.5rem;
        }

        .hero-subtitle {
            font-size: 1.1rem;
        }

        .hero-breadcrumb {
            padding: 10px 20px;
            font-size: 0.9rem;
        }

        .breadcrumb-separator {
            margin: 0 8px;
        }
    }

    @media (max-width: 480px) {
        .hero-section {
            min-height: 250px;
        }

        .hero-title {
            font-size: 2rem;
            margin-bottom: 15px;
        }

        .hero-subtitle {
            font-size: 1rem;
            margin-bottom: 20px;
        }

        .hero-content {
            padding: 0 15px;
        }

        .gallery-collections {
            grid-template-columns: 1fr;
        }

        .filter-btn {
            padding: 8px 16px;
            font-size: 14px;
        }

        .thumbnail {
            width: 50px;
            height: 50px;
        }
    }
</style>
<?php include 'components/newsletter.php'; ?>

<?php include 'components/footer.php'; ?>