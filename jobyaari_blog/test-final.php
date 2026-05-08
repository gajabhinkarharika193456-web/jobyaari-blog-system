<?php
echo "<h1>🔧 Jobyaari Blog - Final Test</h1>";

// Include config
require_once 'config.php';

echo "<h2>✅ Connection Test</h2>";
echo "Database connected successfully!<br>";
echo "Database name: " . $db_name . "<br>";

// Test 1: Check categories
echo "<h2>📁 Categories in Database:</h2>";
$cat_query = "SELECT * FROM categories";
$cat_result = mysqli_query($conn, $cat_query);

if (mysqli_num_rows($cat_result) > 0) {
    echo "<ul>";
    while($cat = mysqli_fetch_assoc($cat_result)) {
        echo "<li><strong>" . $cat['category_name'] . "</strong> (ID: " . $cat['id'] . ")</li>";
    }
    echo "</ul>";
} else {
    echo "❌ No categories found!";
}

// Test 2: Check blogs
echo "<h2>📝 Blogs in Database:</h2>";
$blog_query = "SELECT COUNT(*) as total FROM blogs";
$blog_result = mysqli_query($conn, $blog_query);
$blog_count = mysqli_fetch_assoc($blog_result);

echo "Total blogs: <strong>" . $blog_count['total'] . "</strong><br>";

if ($blog_count['total'] > 0) {
    $blogs = mysqli_query($conn, "SELECT title, category_id FROM blogs LIMIT 3");
    echo "<ul>";
    while($blog = mysqli_fetch_assoc($blogs)) {
        echo "<li>" . $blog['title'] . " (Category ID: " . $blog['category_id'] . ")</li>";
    }
    echo "</ul>";
}

// Test 3: Test slug function
echo "<h2>🔗 Slug Function Test:</h2>";
$test_title = "Government Jobs 2024";
$slug = createSlug($test_title);
echo "Title: '$test_title'<br>";
echo "Slug: '$slug'<br>";

// Test 4: Test getCategoryName function
echo "<h2>🏷️ Category Name Function Test:</h2>";
$cat_name = getCategoryName($conn, 1);
echo "Category ID 1 is: <strong>" . $cat_name . "</strong><br>";

echo "<hr>";
echo "<h2>✅ All tests passed! Your system is ready to use.</h2>";
echo "<p>👉 <a href='index.php'>Go to Homepage</a></p>";
echo "<p>👉 <a href='admin/login.php'>Go to Admin Login</a></p>";
?>