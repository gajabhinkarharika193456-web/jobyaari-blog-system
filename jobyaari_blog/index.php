<?php
require_once 'config.php';

// Pagination
$limit = 6;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Category filter
$category_filter = isset($_GET['category']) ? (int)$_GET['category'] : 0;
$category_where = "";
if ($category_filter > 0) {
    $category_where = "AND category_id = $category_filter";
}

// Get total blogs count
$count_query = "SELECT COUNT(*) as total FROM blogs WHERE status = 1 $category_where";
$count_result = mysqli_query($conn, $count_query);
$total_blogs = mysqli_fetch_assoc($count_result)['total'];
$total_pages = ceil($total_blogs / $limit);

// Fetch blogs with category names
$query = "SELECT b.*, c.category_name 
          FROM blogs b 
          LEFT JOIN categories c ON b.category_id = c.id 
          WHERE b.status = 1 $category_where 
          ORDER BY b.created_at DESC 
          LIMIT $offset, $limit";
$blogs = mysqli_query($conn, $query);

// Fetch all categories for filter
$categories_query = "SELECT * FROM categories ORDER BY category_name";
$categories = mysqli_query($conn, $categories_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jobyaari Blog - Latest Jobs, Results, Admit Cards</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Custom CSS */
        .navbar-brand {
            font-size: 1.8rem;
            font-weight: bold;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .hero-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 80px 0;
            text-align: center;
            margin-bottom: 50px;
        }
        .blog-card {
            transition: transform 0.3s, box-shadow 0.3s;
            border: none;
            border-radius: 15px;
            overflow: hidden;
            margin-bottom: 30px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .blog-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.2);
        }
        .blog-card-img {
            height: 200px;
            object-fit: cover;
            width: 100%;
        }
        .blog-card-title {
            font-size: 1.2rem;
            font-weight: bold;
            margin: 15px 0 10px;
        }
        .blog-card-text {
            color: #666;
            font-size: 0.9rem;
            line-height: 1.5;
        }
        .category-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            background: rgba(0,0,0,0.7);
            color: white;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
        }
        .filter-btn {
            margin: 5px;
            border-radius: 25px;
            padding: 8px 20px;
            transition: all 0.3s;
        }
        .filter-btn.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-color: transparent;
        }
        .footer {
            background: #2c3e50;
            color: white;
            padding: 40px 0;
            margin-top: 60px;
        }
        .read-more-btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 8px 20px;
            border-radius: 25px;
            color: white;
            transition: all 0.3s;
        }
        .read-more-btn:hover {
            transform: translateX(5px);
            color: white;
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
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
                        <a class="nav-link active" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#categories">Categories</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="admin/login.php">Admin</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    
    <!-- Hero Section -->
    <div class="hero-section">
        <div class="container">
            <h1 class="display-4 fw-bold">Welcome to Jobyaari Blog</h1>
            <p class="lead">Your one-stop destination for Latest Jobs, Results, and Admit Cards</p>
            <div class="mt-4">
                <i class="fas fa-search fa-2x mx-2"></i>
                <i class="fas fa-newspaper fa-2x mx-2"></i>
                <i class="fas fa-chart-line fa-2x mx-2"></i>
            </div>
        </div>
    </div>
    
    <div class="container">
        <!-- Category Filters -->
        <div class="row mb-5" id="categories">
            <div class="col-12">
                <h3 class="text-center mb-4">Browse by Category</h3>
                <div class="text-center">
                    <a href="index.php" class="btn btn-outline-primary filter-btn <?php echo $category_filter == 0 ? 'active' : ''; ?>">
                        <i class="fas fa-th-large"></i> All
                    </a>
                    <?php while($cat = mysqli_fetch_assoc($categories)): ?>
                        <a href="?category=<?php echo $cat['id']; ?>" 
                           class="btn btn-outline-primary filter-btn <?php echo $category_filter == $cat['id'] ? 'active' : ''; ?>">
                            <i class="fas fa-tag"></i> <?php echo $cat['category_name']; ?>
                        </a>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>
        
        <!-- Blog Cards Grid -->
        <div class="row">
            <?php if(mysqli_num_rows($blogs) > 0): ?>
                <?php while($blog = mysqli_fetch_assoc($blogs)): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card blog-card">
                            <div style="position: relative;">
                                <?php if($blog['image']): ?>
                                    <img src="uploads/<?php echo $blog['image']; ?>" class="blog-card-img" alt="<?php echo $blog['title']; ?>">
                                <?php else: ?>
                                    <img src="https://via.placeholder.com/400x200?text=No+Image" class="blog-card-img" alt="No Image">
                                <?php endif; ?>
                                <span class="category-badge">
                                    <i class="fas fa-folder"></i> <?php echo $blog['category_name']; ?>
                                </span>
                            </div>
                            <div class="card-body">
                                <div class="text-muted small mb-2">
                                    <i class="fas fa-calendar-alt"></i> <?php echo date('d M Y', strtotime($blog['created_at'])); ?>
                                    <i class="fas fa-eye ms-2"></i> <?php echo $blog['views']; ?> views
                                </div>
                                <h5 class="blog-card-title"><?php echo substr($blog['title'], 0, 60); ?>...</h5>
                                <p class="blog-card-text"><?php echo substr($blog['short_description'], 0, 100); ?>...</p>
                                <a href="blog.php?slug=<?php echo $blog['slug']; ?>" class="btn read-more-btn">
                                    Read More <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="alert alert-info text-center">
                        <i class="fas fa-info-circle"></i> No blogs found in this category!
                    </div>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Pagination -->
        <?php if($total_pages > 1): ?>
        <nav class="mt-4">
            <ul class="pagination justify-content-center">
                <?php for($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?php echo ($page == $i) ? 'active' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $i; ?>&category=<?php echo $category_filter; ?>">
                            <?php echo $i; ?>
                        </a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
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
                        <li><a href="#categories" class="text-white text-decoration-none">Categories</a></li>
                        <li><a href="admin/login.php" class="text-white text-decoration-none">Admin Panel</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h5>Contact Us</h5>
                    <p><i class="fas fa-envelope"></i> info@jobyaari.com</p>
                    <p><i class="fas fa-phone"></i> +91 9876543210</p>
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