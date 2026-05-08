-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 08, 2026 at 02:41 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `jobyaari_blog_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_users`
--

CREATE TABLE `admin_users` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_users`
--

INSERT INTO `admin_users` (`id`, `username`, `password`, `email`, `created_at`) VALUES
(1, 'admin', '0192023a7bbd73250516f069df18b500', 'admin@jobyaari.com', '2026-05-08 11:08:58');

-- --------------------------------------------------------

--
-- Table structure for table `blogs`
--

CREATE TABLE `blogs` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `short_description` text NOT NULL,
  `content` longtext NOT NULL,
  `category_id` int(11) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `views` int(11) DEFAULT 0,
  `status` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `blogs`
--

INSERT INTO `blogs` (`id`, `title`, `slug`, `short_description`, `content`, `category_id`, `image`, `views`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Government Jobs 2024 - 5000+ Vacancies', 'government-jobs-2024', 'Apply for 5000+ government vacancies across India. Last date approaching soon.', '<h2>Government Jobs 2024 Notification</h2><p>Great opportunity for job seekers! Various departments have announced 5000+ vacancies.</p><h3>Eligibility:</h3><ul><li>Graduation from recognized university</li><li>Age: 21-30 years</li><li>Computer knowledge required</li></ul><h3>How to Apply:</h3><p>Visit official website and submit online application before deadline.</p>', 1, 'job-1.jpg', 151, 1, '2026-05-08 11:08:59', '2026-05-08 12:23:23'),
(2, 'SSC CGL Result 2024 Declared', 'ssc-cgl-result-2024', 'SSC CGL Tier 1 Result 2024 has been released. Check your results now.', '<h2>SSC CGL Result 2024 - Direct Link</h2><p>The Staff Selection Commission has declared the CGL Tier 1 result.</p><table border=\"1\"><tr><th>Category</th><th>Cutoff</th></tr><tr><td>General</td><td>145</td></tr><tr><td>OBC</td><td>138</td></tr></table><p>Download your result from official website.</p>', 2, 'result-1.jpg', 200, 1, '2026-05-08 11:08:59', '2026-05-08 11:08:59'),
(3, 'UPSC Admit Card 2024 Released', 'upsc-admit-card-2024', 'UPSC Civil Services Prelims Admit Card 2024 now available for download.', '<h2>UPSC Admit Card 2024 - Download Now</h2><p>Union Public Service Commission has released admit cards for Civil Services Prelims 2024.</p><h3>Exam Details:</h3><ul><li>Date: 15th June 2024</li><li>Time: 9:30 AM to 11:30 AM</li><li>Venue: As mentioned on admit card</li></ul><p>Carry original ID proof along with admit card.</p>', 3, 'admit-1.jpg', 98, 1, '2026-05-08 11:08:59', '2026-05-08 11:08:59'),
(4, 'Bank PO Recruitment 2024', 'bank-po-2024', 'IBPS PO 2024 notification out. 4000+ vacancies for Probationary Officers.', '<h2>IBPS PO Recruitment 2024</h2><p>Institute of Banking Personnel Selection has released notification for PO exam.</p><p>Important Dates:<br/>Application Start: 1st June 2024<br/>Last Date: 30th June 2024<br/>Exam Date: September 2024</p>', 1, 'job-2.jpg', 75, 1, '2026-05-08 11:08:59', '2026-05-08 11:08:59'),
(5, 'Test Blog', 'test-blog', 'This is a test blog', '<h2>Hello World</h2>\r\n<p>This is my first blog post!</p>', 1, '1778242559_2041.jpg', 1, 1, '2026-05-08 12:15:59', '2026-05-08 12:23:03');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `category_name` varchar(100) NOT NULL,
  `category_slug` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `category_name`, `category_slug`, `created_at`) VALUES
(1, 'Latest Jobs', 'latest-jobs', '2026-05-08 11:08:59'),
(2, 'Results', 'results', '2026-05-08 11:08:59'),
(3, 'Admit Card', 'admit-card', '2026-05-08 11:08:59');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_users`
--
ALTER TABLE `admin_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `blogs`
--
ALTER TABLE `blogs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `category_name` (`category_name`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_users`
--
ALTER TABLE `admin_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `blogs`
--
ALTER TABLE `blogs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `blogs`
--
ALTER TABLE `blogs`
  ADD CONSTRAINT `blogs_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
