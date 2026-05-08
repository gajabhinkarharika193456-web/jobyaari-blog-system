<?php
require_once '../config.php';
requireLogin();

$success = "";
$error = "";

// Fetch categories
$categories_query = "SELECT * FROM categories ORDER BY category_name";
$categories = mysqli_query($conn, $categories_query);

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    echo "Form submitted!"; // Debug line - remove after testing
    
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $short_description = mysqli_real_escape_string($conn, $_POST['short_description']);
    $content = mysqli_real_escape_string($conn, $_POST['content']);
    $category_id = mysqli_real_escape_string($conn, $_POST['category_id']);
    
    // Create slug from title
    $slug = createSlug($title);
    
    // Handle image upload
    $image_name = "";
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['image']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (in_array($ext, $allowed)) {
            $image_name = time() . "_" . rand(1000, 9999) . "." . $ext;
            $upload_path = "../uploads/" . $image_name;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                $success = "Image uploaded successfully! ";
            } else {
                $error = "Failed to upload image!";
            }
        } else {
            $error = "Only JPG, JPEG, PNG, GIF files are allowed!";
        }
    }
    
    // Insert into database
    if (empty($error)) {
        $query = "INSERT INTO blogs (title, slug, short_description, content, category_id, image) 
                  VALUES ('$title', '$slug', '$short_description', '$content', '$category_id', '$image_name')";
        
        if (mysqli_query($conn, $query)) {
            $success = "Blog added successfully! Redirecting...";
            // Redirect after 2 seconds
            echo "<script>
                    setTimeout(function() {
                        window.location.href = 'dashboard.php';
                    }, 2000);
                  </script>";
        } else {
            $error = "Database Error: " . mysqli_error($conn);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Blog - Jobyaari Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- CKEditor 5 -->
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
                    <a class="nav-link active" href="add-blog.php">
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
                        <h5 class="mb-0">Add New Blog</h5>
                    </div>
                    <div>
                        <span class="me-3">
                            <i class="fas fa-user-circle"></i> <?php echo $_SESSION['admin_username']; ?>
                        </span>
                    </div>
                </nav>
                
                <div class="p-4">
                    <!-- Alerts -->
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
                    
                    <!-- Add Blog Form - IMPORTANT: Check the form tags -->
                    <div class="card">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">Blog Information</h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="" enctype="multipart/form-data">
                                <!-- Title -->
                                <div class="mb-3">
                                    <label class="form-label">Blog Title <span class="text-danger">*</span></label>
                                    <input type="text" name="title" class="form-control" required 
                                           value="<?php echo isset($_POST['title']) ? $_POST['title'] : ''; ?>">
                                </div>
                                
                                <!-- Category -->
                                <div class="mb-3">
                                    <label class="form-label">Category <span class="text-danger">*</span></label>
                                    <select name="category_id" class="form-control" required>
                                        <option value="">Select Category</option>
                                        <?php 
                                        // Reset categories pointer
                                        mysqli_data_seek($categories, 0);
                                        while($cat = mysqli_fetch_assoc($categories)): ?>
                                            <option value="<?php echo $cat['id']; ?>"
                                                <?php echo (isset($_POST['category_id']) && $_POST['category_id'] == $cat['id']) ? 'selected' : ''; ?>>
                                                <?php echo $cat['category_name']; ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                                
                                <!-- Short Description -->
                                <div class="mb-3">
                                    <label class="form-label">Short Description <span class="text-danger">*</span></label>
                                    <textarea name="short_description" class="form-control" rows="3" required><?php echo isset($_POST['short_description']) ? $_POST['short_description'] : ''; ?></textarea>
                                    <small class="text-muted">This will appear on blog cards (150-200 characters)</small>
                                </div>
                                
                                <!-- Blog Image -->
                                <div class="mb-3">
                                    <label class="form-label">Blog Image</label>
                                    <input type="file" name="image" class="form-control" accept="image/*">
                                    <small class="text-muted">Supported formats: JPG, JPEG, PNG, GIF (Max 2MB)</small>
                                </div>
                                
                                <!-- Rich Text Content -->
                                <div class="mb-3">
                                    <label class="form-label">Blog Content <span class="text-danger">*</span></label>
                                    <textarea name="content" id="editor" required><?php echo isset($_POST['content']) ? $_POST['content'] : ''; ?></textarea>
                                </div>
                                
                                <button type="submit" name="submit_blog" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Publish Blog
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
        // Initialize CKEditor
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
            .then(editor => {
                console.log('CKEditor initialized successfully');
            })
            .catch(error => {
                console.error('CKEditor error:', error);
            });
    </script>
</body>
</html>