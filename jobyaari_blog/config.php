<?php
// ===========================================
// DATABASE CONFIGURATION - FIXED VERSION
// ===========================================

// Database connection settings
$db_host = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "jobyaari_blog_db";  // ✅ Your database name is correct

// Create connection
$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Set charset to UTF-8
mysqli_set_charset($conn, "utf8");

// Start session for login management
session_start();

// Base URL - FIXED (remove space)
$base_url = "http://localhost/jobyaari_blog/";
$admin_base_url = "http://localhost/jobyaari_blog/admin/";

// Function to check if admin is logged in
function isAdminLoggedIn() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

// Function to redirect if not logged in
function requireLogin() {
    if (!isAdminLoggedIn()) {
        header("Location: login.php");
        exit();
    }
}

// Function to get category name by ID - FIXED THE TYPO
function getCategoryName($conn, $category_id) {
    $query = "SELECT category_name FROM categories WHERE id = '$category_id'";
    $result = mysqli_query($conn, $query);  // ← FIXED: was mysql_query, now mysqli_query
    if ($row = mysqli_fetch_assoc($result)) {  // ← FIXED: was mysql_fetch_assoc
        return $row['category_name'];
    }
    return "Uncategorized";
}

// Function to create URL slug - FIXED THE REGEX
function createSlug($string) {
    $string = strtolower($string);
    $string = preg_replace('/[^a-z0-9-]/', '-', $string);  // ← FIXED: missing quotes
    $string = preg_replace('/-+/', '-', $string);  // ← FIXED: pattern was wrong
    return trim($string, '-');
}
?>