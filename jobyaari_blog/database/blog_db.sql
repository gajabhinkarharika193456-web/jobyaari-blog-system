-- ===========================================
-- DATABASE: jobyaari_blog_db
-- AUTHOR: Jobyaari Blog System
-- DESCRIPTION: Complete database for blog system
-- ===========================================

-- Create database
CREATE DATABASE IF NOT EXISTS jobyaari_blog_db;
USE jobyaari_blog_db;

-- ===========================================
-- TABLE: admin_users
-- Stores admin login information
-- ===========================================
CREATE TABLE IF NOT EXISTS admin_users (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ===========================================
-- TABLE: categories
-- Stores blog categories
-- ===========================================
CREATE TABLE IF NOT EXISTS categories (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    category_name VARCHAR(100) NOT NULL UNIQUE,
    category_slug VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ===========================================
-- TABLE: blogs
-- Stores all blog posts
-- ===========================================
CREATE TABLE IF NOT EXISTS blogs (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    short_description TEXT NOT NULL,
    content LONGTEXT NOT NULL,
    category_id INT(11) NOT NULL,
    image VARCHAR(255),
    views INT(11) DEFAULT 0,
    status TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
);

-- ===========================================
-- INSERT SAMPLE DATA
-- ===========================================

-- Insert admin user (password: admin123)
INSERT INTO admin_users (username, password, email) VALUES 
('admin', MD5('admin123'), 'admin@jobyaari.com');

-- Insert categories
INSERT INTO categories (category_name, category_slug) VALUES 
('Latest Jobs', 'latest-jobs'),
('Results', 'results'),
('Admit Card', 'admit-card');

-- Insert sample blogs
INSERT INTO blogs (title, slug, short_description, content, category_id, image, views, status) VALUES 
('Government Jobs 2024 - 5000+ Vacancies', 'government-jobs-2024', 'Apply for 5000+ government vacancies across India. Last date approaching soon.', '<h2>Government Jobs 2024 Notification</h2><p>Great opportunity for job seekers! Various departments have announced 5000+ vacancies.</p><h3>Eligibility:</h3><ul><li>Graduation from recognized university</li><li>Age: 21-30 years</li><li>Computer knowledge required</li></ul><h3>How to Apply:</h3><p>Visit official website and submit online application before deadline.</p>', 1, 'job-1.jpg', 150, 1),

('SSC CGL Result 2024 Declared', 'ssc-cgl-result-2024', 'SSC CGL Tier 1 Result 2024 has been released. Check your results now.', '<h2>SSC CGL Result 2024 - Direct Link</h2><p>The Staff Selection Commission has declared the CGL Tier 1 result.</p><table border="1"><tr><th>Category</th><th>Cutoff</th></tr><tr><td>General</td><td>145</td></tr><tr><td>OBC</td><td>138</td></tr></table><p>Download your result from official website.</p>', 2, 'result-1.jpg', 200, 1),

('UPSC Admit Card 2024 Released', 'upsc-admit-card-2024', 'UPSC Civil Services Prelims Admit Card 2024 now available for download.', '<h2>UPSC Admit Card 2024 - Download Now</h2><p>Union Public Service Commission has released admit cards for Civil Services Prelims 2024.</p><h3>Exam Details:</h3><ul><li>Date: 15th June 2024</li><li>Time: 9:30 AM to 11:30 AM</li><li>Venue: As mentioned on admit card</li></ul><p>Carry original ID proof along with admit card.</p>', 3, 'admit-1.jpg', 98, 1),

('Bank PO Recruitment 2024', 'bank-po-2024', 'IBPS PO 2024 notification out. 4000+ vacancies for Probationary Officers.', '<h2>IBPS PO Recruitment 2024</h2><p>Institute of Banking Personnel Selection has released notification for PO exam.</p><p>Important Dates:<br/>Application Start: 1st June 2024<br/>Last Date: 30th June 2024<br/>Exam Date: September 2024</p>', 1, 'job-2.jpg', 75, 1);

-- ===========================================
-- INSERTION COMPLETE
-- ===========================================