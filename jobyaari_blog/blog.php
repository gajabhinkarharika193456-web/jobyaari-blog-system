<?php
require_once 'config.php';

$slug = isset($_GET['slug']) ? mysqli_real_escape_string($conn, $_GET['slug']) : '';

// Update view count
$update_views = "UPDATE blogs SET views = views + 1 WHERE slug = '$slug'";
mysqli_query($conn, $update_views);

// Fetch blog details
$query = "SELECT b.*, c.category_name 
          FROM blogs b 
          LEFT JOIN categories c ON b.category_id = c.id 
          WHERE b.slug = '$slug' AND b.status = 1";
$result = mysqli_query($conn, $query);
$blog = mysqli_fetch_assoc($result);

if (!$blog) {
    header("Location: index.php");
    exit();
}

// Fetch related blogs (same category)
$related_query = "SELECT * FROM blogs 
                  WHERE category_id = {$blog['category_id']} 
                  AND id != {$blog['id']} 
                  AND status = 1 
                  ORDER BY created_at DESC 
                  LIMIT 3";
$related_blogs = mysqli_query($conn, $related_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $blog['title']; ?> - Jobyaari Blog</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .navbar-brand {
            font-size: 1.8rem;
            font-weight: bold;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .blog-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 60px 0;
            margin-bottom: 40px;
        }
        .blog-content {
            font-size: 1.1rem;
            line-height: 1.8;
        }
        .blog-content h1, .blog-content h2 {
            margin-top: 30px;
            margin-bottom: 15px;
        }
        .blog-content h3 {
            margin-top: 25px;
            margin-bottom: 15px;
        }
        .blog-content p {
            margin-bottom: 20px;
        }
        .blog-content ul, .blog-content ol {
            margin-bottom: 20px;
            padding-left: 25px;
        }
        .blog-content li {
            margin-bottom: 8px;
        }
        .blog-content table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .blog-content th, .blog-content td {
            border: 1px solid #ddd;
            padding: 10px;
        }
        .blog-content th {
            background: #f5f5f5;
        }
        .blog-image {
            width: 100%;
            max-height: 500px;
            object-fit: cover;
            border-radius: 15px;
            margin-bottom: 30px;
        }
        .related-card {
            transition: transform 0.3s;
            border-radius: 10px;
            overflow: hidden;
            margin-bottom: 20px;
        }
        .related-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .share-buttons {
            margin: 30px 0;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 10px;
        }
        .footer {
            background: #2c3e50;
            color: white;
            padding: 40px 0;
            margin-top: 60px;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-blog"></i> Jobyaari Blog
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php#categories">Categories</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    
    <!-- Blog Header -->
    <div class="blog-header">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 mx-auto text-center">
                    <span class="badge bg-light text-dark mb-3">
                        <i class="fas fa-folder"></i> <?php echo $blog['category_name']; ?>
                    </span>
                    <h1 class="display-5 fw-bold"><?php echo $blog['title']; ?></h1>
                    <div class="mt-3">
                        <span><i class="fas fa-calendar-alt"></i> <?php echo date('d M Y', strtotime($blog['created_at'])); ?></span>
                        <span class="ms-3"><i class="fas fa-eye"></i> <?php echo $blog['views']; ?> views</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <!-- Blog Image -->
                <?php if($blog['image']): ?>
                    <img src="uploads/<?php echo $blog['image']; ?>" class="blog-image" alt="<?php echo $blog['title']; ?>">
                <?php endif; ?>
                
                <!-- Blog Content -->
                <article class="blog-content">
                    <?php echo $blog['content']; ?>
                </article>
                
                <!-- Share Buttons -->
                <div class="share-buttons">
                    <h5><i class="fas fa-share-alt"></i> Share this article:</h5>
                    <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode('http://localhost/jobyaari_blog/blog.php?slug=' . $blog['slug']); ?>" 
                       target="_blank" class="btn btn-primary btn-sm">
                        <i class="fab fa-facebook"></i> Facebook
                    </a>
                    <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode('http://localhost/jobyaari_blog/blog.php?slug=' . $blog['slug']); ?>&text=<?php echo urlencode($blog['title']); ?>" 
                       target="_blank" class="btn btn-info btn-sm text-white">
                        <i class="fab fa-twitter"></i> Twitter
                    </a>
                    <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo urlencode('http://localhost/jobyaari_blog/blog.php?slug=' . $blog['slug']); ?>" 
                       target="_blank" class="btn btn-secondary btn-sm">
                        <i class="fab fa-linkedin"></i> LinkedIn
                    </a>
                </div>
                
                <!-- Back Button -->
                <div class="text-center mt-4">
                    <a href="index.php" class="btn btn-outline-primary">
                        <i class="fas fa-arrow-left"></i> Back to Home
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Related Posts -->
        <?php if(mysqli_num_rows($related_blogs) > 0): ?>
        <div class="row mt-5">
            <div class="col-12">
                <h3 class="text-center mb-4">Related Posts</h3>
            </div>
            <?php while($related = mysqli_fetch_assoc($related_blogs)): ?>
                <div class="col-md-4">
                    <div class="card related-card">
                        <?php if($related['image']): ?>
                            <img src="uploads/<?php echo $related['image']; ?>" class="card-img-top" style="height: 150px; object-fit: cover;" alt="<?php echo $related['title']; ?>">
                        <?php endif; ?>
                        <div class="card-body">
                            <h6 class="card-title"><?php echo substr($related['title'], 0, 50); ?>...</h6>
                            <a href="blog.php?slug=<?php echo $related['slug']; ?>" class="btn btn-sm btn-primary">
                                Read More <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
        <?php endif; ?>
    </div>
    
    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h5><i class="fas fa-blog"></i> Jobyaari Blog</h5>
                    <p>Your trusted source for government job notifications, exam results, and admit cards.</p>
                </div>
                <div class="col-md-4">
                    <h5>Quick Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="index.php" class="text-white text-decoration-none">Home</a></li>
                        <li><a href="index.php#categories" class="text-white text-decoration-none">Categories</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h5>Follow Us</h5>
                    <p>
                        <a href="#" class="text-white me-2"><i class="fab fa-facebook fa-2x"></i></a>
                        <a href="#" class="text-white me-2"><i class="fab fa-twitter fa-2x"></i></a>
                        <a href="#" class="text-white me-2"><i class="fab fa-instagram fa-2x"></i></a>
                    </p>
                </div>
            </div>
            <hr class="mt-3">
            <div class="text-center">
                <p class="mb-0">&copy; 2024 Jobyaari Blog. All rights reserved.</p>
            </div>
        </div>
    </footer>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>