-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 11, 2025 at 03:45 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `warungpumps`
--

-- --------------------------------------------------------

--
-- Table structure for table `contact_enquiries`
--

CREATE TABLE `contact_enquiries` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `mobile` varchar(20) NOT NULL,
  `location` varchar(150) NOT NULL,
  `interest` varchar(80) NOT NULL,
  `message` text DEFAULT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contact_enquiries`
--

INSERT INTO `contact_enquiries` (`id`, `name`, `mobile`, `location`, `interest`, `message`, `created_at`) VALUES
(1, 'Test', '1234567890', 'Gaya', 'Dealer Inquiry', 'Want to be dealer in Saharanpur.', '2025-08-11 05:47:10');

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

CREATE TABLE `contact_messages` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `mobile` varchar(20) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `interest` varchar(255) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dealer_applications`
--

CREATE TABLE `dealer_applications` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `mobile` varchar(20) NOT NULL,
  `location` varchar(150) NOT NULL,
  `type` varchar(80) NOT NULL,
  `experience` varchar(80) DEFAULT NULL,
  `query` text DEFAULT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dealer_applications`
--

INSERT INTO `dealer_applications` (`id`, `name`, `mobile`, `location`, `type`, `experience`, `query`, `created_at`) VALUES
(1, 'qwertyuiop', '1234567890', 'zxcvbnm', 'Service Agent', '10', 'asdfghjkl', '2025-08-11 06:01:23');

-- --------------------------------------------------------

--
-- Table structure for table `dealer_inquiries`
--

CREATE TABLE `dealer_inquiries` (
  `id` int(11) NOT NULL,
  `company_name` varchar(200) NOT NULL,
  `contact_person` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `phone` varchar(30) NOT NULL,
  `city` varchar(120) DEFAULT NULL,
  `state` varchar(120) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `documents`
--

CREATE TABLE `documents` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `category` enum('Brochures','Technical Sheets','Installation Manuals','Warranty & Support Docs') NOT NULL,
  `file_path` varchar(500) NOT NULL,
  `published` tinyint(1) NOT NULL DEFAULT 1,
  `downloads_count` int(11) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `documents`
--

INSERT INTO `documents` (`id`, `title`, `description`, `category`, `file_path`, `published`, `downloads_count`, `created_at`) VALUES
(2, 'New Doc', 'New Description', 'Brochures', '', 1, 0, '2025-08-11 06:10:31');

-- --------------------------------------------------------

--
-- Table structure for table `faqs`
--

CREATE TABLE `faqs` (
  `id` int(11) NOT NULL,
  `category` varchar(80) NOT NULL DEFAULT 'General',
  `question` varchar(255) NOT NULL,
  `answer` text NOT NULL,
  `published` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `faqs`
--

INSERT INTO `faqs` (`id`, `category`, `question`, `answer`, `published`, `sort_order`, `created_at`) VALUES
(1, 'General', 'Are products made of pure steel?', 'No, it is not. But by cast iron metal.', 1, 0, '2025-08-11 06:18:39');

-- --------------------------------------------------------

--
-- Table structure for table `gallery_images`
--

CREATE TABLE `gallery_images` (
  `id` int(11) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `alt_text` varchar(255) DEFAULT NULL,
  `category` enum('Agricultural','Domestic','Industrial','Govt Projects') NOT NULL DEFAULT 'Agricultural',
  `display_order` int(11) DEFAULT 100,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `gallery_images`
--

INSERT INTO `gallery_images` (`id`, `image_path`, `alt_text`, `category`, `display_order`, `is_active`, `created_at`) VALUES
(1, '/assets/gallery/agri1.jpg', 'Etawah Farm Setup', 'Agricultural', 1, 1, '2025-08-11 00:59:42'),
(2, '/assets/gallery/home1.jpg', 'Kanpur Residential Pump', 'Domestic', 2, 1, '2025-08-11 00:59:42'),
(3, '/assets/gallery/const1.jpg', 'Building Water Line', 'Industrial', 3, 1, '2025-08-11 00:59:42'),
(4, '/assets/gallery/govt1.jpg', 'School Borewell Setup', 'Govt Projects', 4, 1, '2025-08-11 00:59:42'),
(5, '/assets/gallery/agri2.jpg', 'Drip Irrigation Motor', 'Agricultural', 5, 1, '2025-08-11 00:59:42'),
(6, '/assets/gallery/domestic2.jpg', 'Roof Tank Pump', 'Domestic', 6, 1, '2025-08-11 00:59:42');

-- --------------------------------------------------------

--
-- Table structure for table `gallery_projects`
--

CREATE TABLE `gallery_projects` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `pump_used` varchar(120) DEFAULT NULL,
  `outcome` varchar(400) DEFAULT NULL,
  `image_path` varchar(255) NOT NULL,
  `product_href` varchar(255) DEFAULT NULL,
  `display_order` int(11) DEFAULT 100,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `gallery_projects`
--

INSERT INTO `gallery_projects` (`id`, `title`, `pump_used`, `outcome`, `image_path`, `product_href`, `display_order`, `is_active`, `created_at`) VALUES
(1, 'Submersible Setup for 5 Acre Field – Etawah', 'WRG-3HP-SUB', 'Reduced watering time by 30%, full 2-acre spray range', '/assets/gallery/project-etawah.jpg', '/product-detail.php', 1, 1, '2025-08-11 00:59:42'),
(2, 'Residential Booster Setup – Kanpur', 'WRG-1HP-OPEN', '3-floor tank filled in 10 mins, noise-free', '/assets/gallery/project-kanpur.jpg', '/product-detail.php', 2, 1, '2025-08-11 00:59:42');

-- --------------------------------------------------------

--
-- Table structure for table `gallery_videos`
--

CREATE TABLE `gallery_videos` (
  `id` int(11) NOT NULL,
  `youtube_id` varchar(32) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `display_order` int(11) DEFAULT 100,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `gallery_videos`
--

INSERT INTO `gallery_videos` (`id`, `youtube_id`, `title`, `display_order`, `is_active`, `created_at`) VALUES
(1, 'YOUR_VIDEO_ID_1', 'Project Walkthrough 1', 1, 1, '2025-08-11 00:59:42'),
(2, 'YOUR_VIDEO_ID_2', 'Project Walkthrough 2', 2, 1, '2025-08-11 00:59:42'),
(3, 'YOUR_VIDEO_ID_3', 'Project Walkthrough 3', 3, 1, '2025-08-11 00:59:42');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(160) NOT NULL,
  `slug` varchar(160) NOT NULL,
  `short_description` varchar(255) DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `type` varchar(40) DEFAULT NULL,
  `phase` varchar(16) DEFAULT NULL,
  `usage_tag` varchar(40) DEFAULT NULL,
  `hp_min` decimal(4,1) DEFAULT NULL,
  `hp_max` decimal(4,1) DEFAULT NULL,
  `popularity` int(11) DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `slug`, `short_description`, `image_url`, `type`, `phase`, `usage_tag`, `hp_min`, `hp_max`, `popularity`, `is_active`, `created_at`) VALUES
(1, 'WRG Submersible 0.75HP', 'wrg-sub-075', 'Compact submersible for small plots/tanks.', '/assets/products/sub-075.jpg', 'Submersible', 'Single', 'Domestic', 0.5, 1.0, 85, 1, '2025-08-01 01:37:27'),
(2, 'WRG Submersible 1.5HP', 'wrg-sub-15', 'Efficient model for mid-size fields.', '/assets/products/sub-15.jpg', 'Submersible', 'Single', 'Agricultural', 1.5, 1.5, 120, 1, '2025-08-02 01:37:27'),
(3, 'WRG Submersible 3HP', 'wrg-sub-3', 'Higher head with strong discharge.', '/assets/products/sub-3.jpg', 'Submersible', 'Three', 'Agricultural', 3.0, 3.0, 190, 1, '2025-08-03 01:37:27'),
(4, 'WRG Submersible 5HP', 'wrg-sub-5', 'For deep borewells and long runs.', '/assets/products/sub-5.jpg', 'Submersible', 'Three', 'Commercial', 5.0, 5.0, 220, 1, '2025-08-04 01:37:27'),
(5, 'WRG Tubewell 1HP', 'wrg-tube-1', 'Starter tubewell for shallow depths.', '/assets/products/tube-1.jpg', 'Tubewell', 'Single', 'Domestic', 1.0, 1.0, 60, 1, '2025-08-05 01:37:27'),
(6, 'WRG Tubewell 2HP', 'wrg-tube-2', 'Reliable for village/colony water lines.', '/assets/products/tube-2.jpg', 'Tubewell', 'Single', 'Commercial', 2.0, 2.0, 130, 1, '2025-08-06 01:37:27'),
(7, 'WRG Tubewell 3HP', 'wrg-tube-3', 'Rugged build for heavy-duty loads.', '/assets/products/tube-3.jpg', 'Tubewell', 'Three', 'Agricultural', 3.0, 3.0, 170, 1, '2025-08-07 01:37:27'),
(8, 'WRG Tubewell 5HP', 'wrg-tube-5', 'High discharge for long irrigation lines.', '/assets/products/tube-5.jpg', 'Tubewell', 'Three', 'Agricultural', 5.0, 5.0, 210, 1, '2025-08-08 01:37:27'),
(9, 'WRG Openwell 0.5–1HP', 'wrg-open-05-1', 'Quiet operation; ideal for shallow lifting.', '/assets/products/open-1.jpg', 'Openwell', 'Single', 'Domestic', 0.5, 1.0, 95, 1, '2025-07-30 01:37:27'),
(10, 'WRG Openwell 1.5–2HP', 'wrg-open-15-2', 'Energy‑efficient and durable.', '/assets/products/open-2.jpg', 'Openwell', 'Single', 'Commercial', 1.5, 2.0, 110, 1, '2025-07-31 01:37:27'),
(11, 'WRG Openwell 3–4HP', 'wrg-open-3-4', 'Higher lift for moderate distances.', '/assets/products/open-3.jpg', 'Openwell', 'Three', 'Agricultural', 3.0, 4.0, 140, 1, '2025-08-09 01:37:27'),
(12, 'WRG Booster 1HP', 'wrg-boost-1', 'Compact booster for rooftop tanks.', '/assets/products/boost-1.jpg', 'Openwell', 'Single', 'Domestic', 1.0, 1.0, 80, 1, '2025-07-27 01:37:27'),
(13, 'WRG Booster 1.5HP', 'wrg-boost-15', 'High pressure for multi-storey buildings.', '/assets/products/boost-15.jpg', 'Openwell', 'Single', 'Commercial', 1.5, 1.5, 100, 1, '2025-07-28 01:37:27'),
(14, 'WRG Bore 7.5HP', 'wrg-bore-75', 'Deep bore application with long throw.', '/assets/products/bore-75.jpg', 'Tubewell', 'Three', 'Agricultural', 7.5, 7.5, 240, 1, '2025-08-10 01:37:27'),
(15, 'WRG AgroLine 2–3HP', 'wrg-agro-2-3', 'Optimized for drip & sprinkler systems.', '/assets/products/agro-23.jpg', 'Submersible', 'Single', 'Agricultural', 2.0, 3.0, 160, 1, '2025-07-29 01:37:27'),
(16, 'WRG InduFlow 5+HP', 'wrg-indu-5p', 'Built for continuous industrial duty.', '/assets/products/indu-5p.jpg', 'Submersible', 'Three', 'Commercial', 5.0, 8.0, 260, 1, '2025-08-11 01:37:27'),
(17, 'WRG Legacy 2HP (Inactive)', 'wrg-legacy-2', 'Old model, inactive.', '/assets/products/legacy-2.jpg', 'Tubewell', 'Single', 'Domestic', 2.0, 2.0, 10, 0, '2025-05-03 01:37:27'),
(18, 'WRG Legacy 3HP (Inactive)', 'wrg-legacy-3', 'Old model, inactive.', '/assets/products/legacy-3.jpg', 'Openwell', 'Three', 'Commercial', 3.0, 3.0, 15, 0, '2025-05-13 01:37:27');

-- --------------------------------------------------------

--
-- Table structure for table `product_categories`
--

CREATE TABLE `product_categories` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(120) NOT NULL,
  `slug` varchar(140) NOT NULL,
  `display_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_categories`
--

INSERT INTO `product_categories` (`id`, `name`, `slug`, `display_order`, `is_active`, `created_at`) VALUES
(1, 'Tubewell', 'tubewell', 10, 1, '2025-08-11 01:37:27'),
(2, 'Submersible', 'submersible', 20, 1, '2025-08-11 01:37:27'),
(3, 'Openwell', 'openwell', 30, 1, '2025-08-11 01:37:27');

-- --------------------------------------------------------

--
-- Table structure for table `product_features`
--

CREATE TABLE `product_features` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `feature` varchar(255) NOT NULL,
  `sort_order` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product_files`
--

CREATE TABLE `product_files` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(140) NOT NULL,
  `file_url` varchar(255) NOT NULL,
  `sort_order` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product_media`
--

CREATE TABLE `product_media` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `kind` varchar(24) NOT NULL,
  `url` varchar(255) NOT NULL,
  `sort_order` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product_specs`
--

CREATE TABLE `product_specs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `param` varchar(120) NOT NULL,
  `value` varchar(255) NOT NULL,
  `sort_order` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product_tags`
--

CREATE TABLE `product_tags` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `tag` varchar(60) NOT NULL,
  `sort_order` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `contact_enquiries`
--
ALTER TABLE `contact_enquiries`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `dealer_applications`
--
ALTER TABLE `dealer_applications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `dealer_inquiries`
--
ALTER TABLE `dealer_inquiries`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `documents`
--
ALTER TABLE `documents`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `faqs`
--
ALTER TABLE `faqs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `gallery_images`
--
ALTER TABLE `gallery_images`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `gallery_projects`
--
ALTER TABLE `gallery_projects`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `gallery_videos`
--
ALTER TABLE `gallery_videos`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `idx_type` (`type`),
  ADD KEY `idx_phase` (`phase`),
  ADD KEY `idx_usage` (`usage_tag`),
  ADD KEY `idx_hp_min` (`hp_min`),
  ADD KEY `idx_hp_max` (`hp_max`),
  ADD KEY `idx_active` (`is_active`),
  ADD KEY `idx_popularity` (`popularity`),
  ADD KEY `idx_created` (`created_at`);

--
-- Indexes for table `product_categories`
--
ALTER TABLE `product_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `product_features`
--
ALTER TABLE `product_features`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `product_files`
--
ALTER TABLE `product_files`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `product_media`
--
ALTER TABLE `product_media`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `product_specs`
--
ALTER TABLE `product_specs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `product_tags`
--
ALTER TABLE `product_tags`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `contact_enquiries`
--
ALTER TABLE `contact_enquiries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dealer_applications`
--
ALTER TABLE `dealer_applications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `dealer_inquiries`
--
ALTER TABLE `dealer_inquiries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `documents`
--
ALTER TABLE `documents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `faqs`
--
ALTER TABLE `faqs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `gallery_images`
--
ALTER TABLE `gallery_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `gallery_projects`
--
ALTER TABLE `gallery_projects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `gallery_videos`
--
ALTER TABLE `gallery_videos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `product_categories`
--
ALTER TABLE `product_categories`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `product_features`
--
ALTER TABLE `product_features`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `product_files`
--
ALTER TABLE `product_files`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `product_media`
--
ALTER TABLE `product_media`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `product_specs`
--
ALTER TABLE `product_specs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `product_tags`
--
ALTER TABLE `product_tags`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `product_features`
--
ALTER TABLE `product_features`
  ADD CONSTRAINT `fk_pf_prod` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `product_files`
--
ALTER TABLE `product_files`
  ADD CONSTRAINT `fk_pfiles_prod` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `product_media`
--
ALTER TABLE `product_media`
  ADD CONSTRAINT `fk_pmedia_prod` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `product_specs`
--
ALTER TABLE `product_specs`
  ADD CONSTRAINT `fk_ps_prod` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `product_tags`
--
ALTER TABLE `product_tags`
  ADD CONSTRAINT `fk_ptags_prod` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
