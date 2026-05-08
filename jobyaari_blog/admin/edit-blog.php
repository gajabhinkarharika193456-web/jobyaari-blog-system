<?php
require_once '../config.php';
requireLogin();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch blog data
$query = "SELECT * FROM blogs WHERE id = $id";
$result = mysqli_query($conn, $query);
$blog = mysqli_fetch_assoc($result);

if (!$blog) {
    header("Location: dashboard.php");
    exit();
}

$success = "";
$error = "";

// Fetch categories
$categories_query = "SELECT * FROM categories ORDER BY category_name";
$categories = mysqli_query($conn, $categories_query);

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $short_description = mysqli_real_escape_string($conn, $_POST['short_description']);
    $content = mysqli_real_escape_string($conn, $_POST['content']);
    $category_id = mysqli_real_escape_string($conn, $_POST['category_id']);
    
    // Create slug from title
    $slug = createSlug($title);
    
    // Handle image upload
    $image_name = $blog['image'];
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['image']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (in_array($ext, $allowed)) {
            // Delete old image
            if ($blog['image'] && file_exists("../uploads/" . $blog['image'])) {
                unlink("../uploads/" . $blog['image']);
            }
            
            $image_name = time() . "_" . rand(1000, 9999) . "." . $ext;
            $upload_path = "../uploads/" . $image_name;
            move_uploaded_file($_FILES['image']['tmp_name'], $upload_path);
        } else {
            $error = "Only JPG, JPEG, PNG, GIF files are allowed!";
        }
    }
    
    // Update database
    if (empty($error)) {
        $update_query = "UPDATE blogs SET 
                        title = '$title',
                        slug = '$slug',
                        short_description = '$short_description',
                        content = '$content',
                        category_id = '$category_id',
                        image = '$image_name'
                        WHERE id = $id";
        
        if (mysqli_query($conn, $update_query)) {
            $success = "Blog updated successfully!";
            // Refresh blog data
            $result = mysqli_query($conn, $query);
            $blog = mysqli_fetch_assoc($result);
        } else {
            $error = "Error: " . mysqli_error($conn);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Blog - Jobyaari Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
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
        .navbar-custom {
            background: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .ck-editor__editable {
            min-height: 300px;
        }
        .current-image {
            max-width: 200px;
            border-radius: 10px;
            margin-top: 10px;
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
                    <a class="nav-link" href="dashboard.php">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                    <a class="nav-link" href="add-blog.php">
                        <i class="fas fa-plus-circle"></i> Add New Blog
                    </a>
                    <a class="nav-link active" href="#">
                        <i class="fas fa-edit"></i> Edit Blog
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
                        <h5 class="mb-0">Edit Blog</h5>
                    </div>
                    <div>
                        <span class="me-3">
                            <i class="fas fa-user-circle"></i> <?php echo $_SESSION['admin_username']; ?>
                        </span>
                    </div>
                </nav>
                
                <div class="p-4">
                    <?php if($success): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle"></i> <?php echo $success; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <?php if($error): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle"></i> <?php echo $error; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <div class="card">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">Edit Blog: <?php echo $blog['title']; ?></h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="" enctype="multipart/form-data">
                                <div class="mb-3">
                                    <label class="form-label">Blog Title <span class="text-danger">*</span></label>
                                    <input type="text" name="title" class="form-control" required value="<?php echo $blog['title']; ?>">
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Category <span class="text-danger">*</span></label>
                                    <select name="category_id" class="form-control" required>
                                        <option value="">Select Category</option>
                                        <?php mysqli_data_seek($categories, 0); ?>
                                        <?php while($cat = mysqli_fetch_assoc($categories)): ?>
                                            <option value="<?php echo $cat['id']; ?>"
                                                <?php echo ($blog['category_id'] == $cat['id']) ? 'selected' : ''; ?>>
                                                <?php echo $cat['category_name']; ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Short Description <span class="text-danger">*</span></label>
                                    <textarea name="short_description" class="form-control" rows="3" required><?php echo $blog['short_description']; ?></textarea>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Blog Image</label>
                                    <?php if($blog['image']): ?>
                                        <div>
                                            <img src="../uploads/<?php echo $blog['image']; ?>" class="current-image" alt="Current Image">
                                            <br>
                                            <small class="text-muted">Current image. Upload new to replace.</small>
                                        </div>
                                    <?php endif; ?>
                                    <input type="file" name="image" class="form-control mt-2" accept="image/*">
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Blog Content <span class="text-danger">*</span></label>
                                    <textarea name="content" id="editor"><?php echo $blog['content']; ?></textarea>
                                </div>
                                
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Update Blog
                                </button>
                                <a href="dashboard.php" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Cancel
                                </a>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        ClassicEditor
            .create(document.querySelector('#editor'), {
                toolbar: ['heading', 'bold', 'italic', 'bulletedList', 'numberedList', 'blockQuote', 'insertTable', 'undo', 'redo'],
                heading: {
                    options: [
                        { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
                        { model: 'heading1', view: 'h1', title: 'Heading 1', class: 'ck-heading_heading1' },
                        { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
                        { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' }
                    ]
                }
            })
            .catch(error => {
                console.error(error);
            });
    </script>
</body>
</html>