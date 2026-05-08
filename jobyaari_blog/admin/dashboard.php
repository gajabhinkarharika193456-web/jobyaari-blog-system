<?php
// Include database configuration
require_once '../config.php';

// Check if admin is logged in
requireLogin();

// Handle delete blog
if (isset($_GET['delete'])) {
    $id = mysqli_real_escape_string($conn, $_GET['delete']);
    
    // Get image filename first
    $img_query = "SELECT image FROM blogs WHERE id = $id";
    $img_result = mysqli_query($conn, $img_query);
    $img_row = mysqli_fetch_assoc($img_result);
    
    // Delete image file if exists
    if ($img_row['image'] && file_exists("../uploads/" . $img_row['image'])) {
        unlink("../uploads/" . $img_row['image']);
    }
    
    // Delete blog from database
    $delete_query = "DELETE FROM blogs WHERE id = $id";
    if (mysqli_query($conn, $delete_query)) {
        header("Location: dashboard.php?msg=deleted");
        exit();
    }
}

// Pagination
$limit = 10;
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Search functionality
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$where = "";
if ($search) {
    $where = "WHERE title LIKE '%$search%' OR short_description LIKE '%$search%'";
}

// Get total blogs count
$count_query = "SELECT COUNT(*) as total FROM blogs $where";
$count_result = mysqli_query($conn, $count_query);
$total_blogs = mysqli_fetch_assoc($count_result)['total'];
$total_pages = ceil($total_blogs / $limit);

// Fetch blogs with category names
$query = "SELECT b.*, c.category_name 
          FROM blogs b 
          LEFT JOIN categories c ON b.category_id = c.id 
          $where 
          ORDER BY b.created_at DESC 
          LIMIT $offset, $limit";
$blogs = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Jobyaari Blog</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .sidebar .nav-link {
            color: white;
            padding: 15px 20px;
            margin: 5px 0;
            border-radius: 10px;
            transition: all 0.3s;
        }
        .sidebar .nav-link:hover {
            background: rgba(255,255,255,0.2);
            transform: translateX(5px);
        }
        .sidebar .nav-link.active {
            background: rgba(255,255,255,0.3);
        }
        .navbar-custom {
            background: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .card-stats {
            transition: transform 0.3s;
            cursor: pointer;
        }
        .card-stats:hover {
            transform: translateY(-5px);
        }
        .table-image {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 10px;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 p-0 sidebar">
                <div class="text-center py-4">
                    <i class="fas fa-blog fa-3x text-white"></i>
                    <h4 class="text-white mt-2">Jobyaari Admin</h4>
                </div>
                <nav class="nav flex-column px-3">
                    <a class="nav-link active" href="dashboard.php">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                    <a class="nav-link" href="add-blog.php">
                        <i class="fas fa-plus-circle"></i> Add New Blog
                    </a>
                    <a class="nav-link" href="../index.php" target="_blank">
                        <i class="fas fa-globe"></i> View Website
                    </a>
                    <a class="nav-link" href="logout.php">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </nav>
            </div>
            
            <!-- Main Content -->
            <div class="col-md-10 p-0">
                <nav class="navbar navbar-custom px-4 py-3">
                    <div class="d-flex align-items-center">
                        <h5 class="mb-0">Dashboard</h5>
                    </div>
                    <div>
                        <span class="me-3">
                            <i class="fas fa-user-circle"></i> <?php echo $_SESSION['admin_username']; ?>
                        </span>
                    </div>
                </nav>
                
                <div class="p-4">
                    <!-- Statistics Cards -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card card-stats bg-primary text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h6>Total Blogs</h6>
                                            <h2><?php echo $total_blogs; ?></h2>
                                        </div>
                                        <i class="fas fa-blog fa-3x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card card-stats bg-success text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h6>Categories</h6>
                                            <h2>3</h2>
                                        </div>
                                        <i class="fas fa-tags fa-3x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card card-stats bg-info text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h6>Total Views</h6>
                                            <h2>523</h2>
                                        </div>
                                        <i class="fas fa-eye fa-3x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Message Alert -->
                    <?php if(isset($_GET['msg'])): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle"></i> Blog deleted successfully!
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Search Bar -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <form method="GET" class="row g-3">
                                <div class="col-md-10">
                                    <input type="text" name="search" class="form-control" placeholder="Search blogs..." value="<?php echo $search; ?>">
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="fas fa-search"></i> Search
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                    
                    <!-- Blogs Table -->
                    <div class="card">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">All Blogs</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Image</th>
                                            <th>Title</th>
                                            <th>Category</th>
                                            <th>Views</th>
                                            <th>Created</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if(mysqli_num_rows($blogs) > 0): ?>
                                            <?php while($blog = mysqli_fetch_assoc($blogs)): ?>
                                            <tr>
                                                <td><?php echo $blog['id']; ?></td>
                                                <td>
                                                    <?php if($blog['image']): ?>
                                                        <img src="../uploads/<?php echo $blog['image']; ?>" class="table-image" alt="Blog Image">
                                                    <?php else: ?>
                                                        <i class="fas fa-image fa-2x text-muted"></i>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?php echo substr($blog['title'], 0, 50); ?>...</td>
                                                <td>
                                                    <span class="badge bg-info">
                                                        <?php echo $blog['category_name']; ?>
                                                    </span>
                                                </td>
                                                <td><?php echo $blog['views']; ?></td>
                                                <td><?php echo date('d M Y', strtotime($blog['created_at'])); ?></td>
                                                <td>
                                                    <a href="edit-blog.php?id=<?php echo $blog['id']; ?>" class="btn btn-sm btn-warning">
                                                        <i class="fas fa-edit"></i> Edit
                                                    </a>
                                                    <a href="?delete=<?php echo $blog['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">
                                                        <i class="fas fa-trash"></i> Delete
                                                    </a>
                                                </td>
                                            </tr>
                                            <?php endwhile; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="7" class="text-center">No blogs found!</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                            
                            <!-- Pagination -->
                            <?php if($total_pages > 1): ?>
                            <nav>
                                <ul class="pagination justify-content-center">
                                    <?php for($i = 1; $i <= $total_pages; $i++): ?>
                                        <li class="page-item <?php echo ($page == $i) ? 'active' : ''; ?>">
                                            <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo $search; ?>">
                                                <?php echo $i; ?>
                                            </a>
                                        </li>
                                    <?php endfor; ?>
                                </ul>
                            </nav>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>