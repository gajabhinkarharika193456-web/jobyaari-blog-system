<?php
echo "<h2>Testing Database Connection</h2>";

// Include config
include 'config.php';

// Test connection
if ($conn) {
    echo "✅ Database connected successfully!<br><br>";
    
    // Test query
    $query = "SELECT * FROM categories";
    $result = mysqli_query($conn, $query);
    
    echo "<h3>Categories in database:</h3>";
    echo "<ul>";
    while($row = mysqli_fetch_assoc($result)) {
        echo "<li>" . $row['category_name'] . "</li>";
    }
    echo "</ul>";
    
    // Test blogs count
    $blog_query = "SELECT COUNT(*) as total FROM blogs";
    $blog_result = mysqli_query($conn, $blog_query);
    $blog_count = mysqli_fetch_assoc($blog_result);
    
    echo "<h3>Total Blogs: " . $blog_count['total'] . "</h3>";
    
} else {
    echo "❌ Connection failed!";
}
?>