-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Nov 29, 2025 at 11:15 PM
-- Server version: 10.11.6-MariaDB-0+deb12u1-log
-- PHP Version: 8.4.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `bigzdemo_elern`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `log_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(100) DEFAULT NULL,
  `table_name` varchar(50) DEFAULT NULL,
  `record_id` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `activity_logs`
--

INSERT INTO `activity_logs` (`log_id`, `user_id`, `action`, `table_name`, `record_id`, `description`, `ip_address`, `created_at`) VALUES
(1, 1, 'login', 'users', 1, 'เข้าสู่ระบบ', '49.228.126.109', '2025-10-03 08:00:01'),
(2, 1, 'create', 'levels', 3, 'เพิ่มระดับชั้น: ปวส.3', '49.228.126.109', '2025-10-03 08:00:22'),
(3, 1, 'update', 'levels', 3, 'แก้ไขระดับชั้น: ปวส.3', '49.228.126.109', '2025-10-03 08:00:28'),
(4, 1, 'update', 'levels', 3, 'แก้ไขระดับชั้น: ปวส.3', '49.228.126.109', '2025-10-03 09:54:37'),
(5, 1, 'logout', 'users', 1, 'ออกจากระบบ', '49.228.126.109', '2025-10-03 09:55:25'),
(6, 1, 'login', 'users', 1, 'เข้าสู่ระบบ', '49.228.126.109', '2025-10-03 09:55:33'),
(7, 1, 'login', 'users', 1, 'เข้าสู่ระบบ', '49.228.126.109', '2025-10-03 11:16:09'),
(8, 1, 'update', 'users', 1, 'แก้ไขผู้ใช้งาน: admin', '49.228.126.109', '2025-10-03 11:16:30'),
(9, 1, 'create', 'users', 2, 'เพิ่มผู้ใช้งาน: test01', '49.228.126.109', '2025-10-03 11:17:22'),
(10, 1, 'update', 'lessons', 1, 'แก้ไขบทเรียน: บทที่ 1 แนะนำระบบเครื่องกล้าง', '49.228.126.109', '2025-10-03 11:21:28'),
(11, 1, 'login', 'users', 1, 'เข้าสู่ระบบ', '49.228.126.109', '2025-10-03 12:30:16'),
(12, 1, 'update', 'lessons', 1, 'แก้ไขบทเรียน: บทที่ 1 แนะนำระบบเครื่องกล้าง', '49.228.126.109', '2025-10-03 12:30:46'),
(13, 1, 'logout', 'users', 1, 'ออกจากระบบ', '49.228.126.109', '2025-10-03 13:09:53'),
(14, 2, 'login', 'users', 2, 'เข้าสู่ระบบ', '49.228.126.109', '2025-10-03 13:10:04'),
(15, 1, 'login', 'users', 1, 'เข้าสู่ระบบ', '49.228.126.109', '2025-10-03 13:17:08'),
(16, 1, 'login', 'users', 1, 'เข้าสู่ระบบ', '49.228.186.139', '2025-10-03 19:17:53'),
(17, 1, 'login', 'users', 1, 'เข้าสู่ระบบ', '49.237.21.129', '2025-11-15 22:18:44'),
(18, 1, 'delete', 'levels', 3, 'ลบระดับชั้น: ปวส.3', '49.237.21.129', '2025-11-15 22:19:29'),
(19, 1, 'login', 'users', 1, 'เข้าสู่ระบบ', '49.237.186.108', '2025-11-16 03:35:59'),
(20, 1, 'login', 'users', 1, 'เข้าสู่ระบบ', '49.237.8.246', '2025-11-28 15:27:08'),
(21, 1, 'login', 'users', 1, 'เข้าสู่ระบบ', '49.237.8.246', '2025-11-28 16:12:04'),
(22, 1, 'login', 'users', 1, 'เข้าสู่ระบบ', '49.237.185.187', '2025-11-29 02:37:25'),
(23, 1, 'login', 'users', 1, 'เข้าสู่ระบบ', '49.229.204.226', '2025-11-29 02:58:28'),
(24, 1, 'login', 'users', 1, 'เข้าสู่ระบบ', '49.237.185.187', '2025-11-29 03:21:31'),
(25, 1, 'login', 'users', 1, 'เข้าสู่ระบบ', '49.228.126.243', '2025-11-29 03:31:41'),
(26, 1, 'login', 'users', 1, 'เข้าสู่ระบบ', '49.237.185.187', '2025-11-29 03:36:40'),
(27, 1, 'login', 'users', 1, 'เข้าสู่ระบบ', '49.229.211.112', '2025-11-29 03:38:13'),
(28, 1, 'login', 'users', 1, 'เข้าสู่ระบบ', '202.176.124.240', '2025-11-29 06:24:46'),
(29, 1, 'login', 'users', 1, 'เข้าสู่ระบบ', '49.237.180.7', '2025-11-29 07:25:53'),
(30, 1, 'logout', 'users', 1, 'ออกจากระบบ', '49.237.180.7', '2025-11-29 07:48:25'),
(31, 1, 'login', 'users', 1, 'เข้าสู่ระบบ', '49.237.180.7', '2025-11-29 07:49:18'),
(32, 1, 'logout', 'users', 1, 'ออกจากระบบ', '49.237.180.7', '2025-11-29 07:51:55'),
(33, 1, 'login', 'users', 1, 'เข้าสู่ระบบ', '49.229.211.112', '2025-11-29 07:53:01'),
(34, 1, 'login', 'users', 1, 'เข้าสู่ระบบ', '49.237.180.7', '2025-11-29 07:57:10'),
(35, 1, 'create', 'users', 3, 'เพิ่มผู้ใช้งาน: student', '49.237.180.7', '2025-11-29 08:00:02'),
(36, 1, 'logout', 'users', 1, 'ออกจากระบบ', '49.237.180.7', '2025-11-29 08:03:28'),
(37, 1, 'login', 'users', 1, 'เข้าสู่ระบบ', '49.237.180.7', '2025-11-29 08:04:09');

-- --------------------------------------------------------

--
-- Table structure for table `attachments`
--

CREATE TABLE `attachments` (
  `attachment_id` int(11) NOT NULL,
  `lesson_id` int(11) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_original_name` varchar(255) NOT NULL,
  `display_name` varchar(255) DEFAULT NULL,
  `file_path` varchar(500) NOT NULL,
  `file_type` varchar(50) DEFAULT NULL,
  `file_size` int(11) DEFAULT NULL,
  `uploaded_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attachments`
--

INSERT INTO `attachments` (`attachment_id`, `lesson_id`, `file_name`, `file_original_name`, `display_name`, `file_path`, `file_type`, `file_size`, `uploaded_at`) VALUES
(1, 1, '68dfb1b8a7517_1759490488.jpg', 'images.jpg', 'เอกสาร 1', '/var/www/vhosts/bigzdemo12.com/bigzdemo19.live/uploads/attachments/68dfb1b8a7517_1759490488.jpg', 'image/jpeg', 7866, '2025-10-03 11:21:28'),
(2, 1, '68dfb1b8a7ca8_1759490488.jpg', 'f16c8115-b7af-4ef9-a63a-e4dfd40075fb.jpg', 'f16c8115-b7af-4ef9-a63a-e4dfd40075fb.jpg', '/var/www/vhosts/bigzdemo12.com/bigzdemo19.live/uploads/attachments/68dfb1b8a7ca8_1759490488.jpg', 'image/jpeg', 393165, '2025-10-03 11:21:28');

-- --------------------------------------------------------

--
-- Table structure for table `lessons`
--

CREATE TABLE `lessons` (
  `lesson_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `lesson_title` varchar(255) NOT NULL,
  `lesson_content` longtext DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `status` enum('draft','published') DEFAULT 'published',
  `view_count` int(11) DEFAULT 0,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lessons`
--

INSERT INTO `lessons` (`lesson_id`, `subject_id`, `lesson_title`, `lesson_content`, `sort_order`, `status`, `view_count`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 1, 'บทที่ 1 แนะนำระบบเครื่องกล้าง', '<h2>บทที่ 1 แนะนำระบบเครื่องกล้าง</h2><p>เนื้อหาเกี่ยวกับระบบเครื่องกล้างพื้นฐาน...</p>', 1, 'published', 33, 1, '2025-10-03 07:06:14', '2025-11-29 07:53:44'),
(2, 1, 'บทที่ 2 ชนิดของเครื่องกล้าง', '<h2>บทที่ 2 ชนิดของเครื่องกล้าง</h2><p>ประเภทต่างๆ ของเครื่องกล้าง...</p>', 2, 'published', 18, 1, '2025-10-03 07:06:14', '2025-11-29 02:59:24');

-- --------------------------------------------------------

--
-- Table structure for table `levels`
--

CREATE TABLE `levels` (
  `level_id` int(11) NOT NULL,
  `level_code` varchar(20) NOT NULL,
  `level_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `levels`
--

INSERT INTO `levels` (`level_id`, `level_code`, `level_name`, `description`, `sort_order`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'PVOS1', 'ปวส.1', 'ประกาศนียบัตรวิชาชีพชั้นสูง ชั้นปีที่ 1', 1, 1, '2025-10-03 07:06:14', '2025-10-03 07:06:14'),
(2, 'PVOS2', 'ปวส.2', 'ประกาศนียบัตรวิชาชีพชั้นสูง ชั้นปีที่ 2', 2, 1, '2025-10-03 07:06:14', '2025-10-03 07:06:14');

-- --------------------------------------------------------

--
-- Table structure for table `positions`
--

CREATE TABLE `positions` (
  `position_id` int(11) NOT NULL,
  `position_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `positions`
--

INSERT INTO `positions` (`position_id`, `position_name`, `description`, `is_active`, `created_at`) VALUES
(1, 'ครูผู้สอน', 'ครูผู้สอนประจำวิชา', 1, '2025-10-03 11:11:40'),
(2, 'อาจารย์', 'อาจารย์ประจำหลักสูตร', 1, '2025-10-03 11:11:40'),
(3, 'หัวหน้าแผนก', 'หัวหน้าแผนกวิชา', 1, '2025-10-03 11:11:40'),
(4, 'หัวหน้าสาขา', 'หัวหน้าสาขาวิชา', 1, '2025-10-03 11:11:40'),
(5, 'ผู้อำนวยการ', 'ผู้บริหารสถานศึกษา', 1, '2025-10-03 11:11:40');

-- --------------------------------------------------------

--
-- Table structure for table `site_settings`
--

CREATE TABLE `site_settings` (
  `setting_id` int(11) NOT NULL,
  `setting_key` varchar(50) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `site_settings`
--

INSERT INTO `site_settings` (`setting_id`, `setting_key`, `setting_value`, `updated_at`) VALUES
(1, 'site_name', 'ระบบบทเรียนออนไลน์', '2025-10-03 07:06:14'),
(2, 'site_description', 'ระบบจัดเก็บบทเรียน ปวส.', '2025-10-03 07:06:14'),
(3, 'site_logo', 'logo.png', '2025-10-03 07:06:14'),
(4, 'contact_email', 'admin@example.com', '2025-10-03 07:06:14');

-- --------------------------------------------------------

--
-- Table structure for table `subjects`
--

CREATE TABLE `subjects` (
  `subject_id` int(11) NOT NULL,
  `level_id` int(11) NOT NULL,
  `subject_code` varchar(20) NOT NULL,
  `subject_name` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `hours_theory` int(11) DEFAULT 0,
  `hours_practice` int(11) DEFAULT 0,
  `hours_self` int(11) DEFAULT 0,
  `sort_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subjects`
--

INSERT INTO `subjects` (`subject_id`, `level_id`, `subject_code`, `subject_name`, `description`, `hours_theory`, `hours_practice`, `hours_self`, `sort_order`, `is_active`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 1, '30143-0001', 'งานเครื่องกล้างและส่งกำลังงานต์เบื้องต้น', 'ศึกษาเกี่ยวกับระบบเครื่องกล้างและการส่งกำลัง', 2, 3, 3, 1, 1, 1, '2025-10-03 07:06:14', '2025-10-03 13:20:16'),
(2, 1, '30143-0002', 'เทคโนโลยีอาชานอนต์ไฟฟ้า', 'ศึกษาหลักการทำงานของระบบไฟฟ้าพื้นฐาน', 2, 0, 2, 2, 1, 1, '2025-10-03 07:06:14', '2025-10-03 13:20:16'),
(3, 1, '30143-0003', 'เครื่องมือวัดในงานอาชานอนต์ไฟฟ้า', 'ศึกษาเครื่องมือวัดทางไฟฟ้าและการใช้งาน', 1, 3, 2, 3, 1, 1, '2025-10-03 07:06:14', '2025-10-03 13:20:16'),
(4, 2, '30100-0001', 'งานเทคนิคเบื้องต้น', 'ศึกษาทักษะพื้นฐานทางเทคนิค', 0, 6, 2, 1, 1, 1, '2025-10-03 07:06:14', '2025-10-03 13:20:16'),
(5, 2, '30100-0002', 'เขียนแบบเทคนิค', 'ศึกษาการเขียนแบบทางเทคนิค', 1, 3, 2, 2, 1, 1, '2025-10-03 07:06:14', '2025-10-03 13:20:16');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `position_id` int(11) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `role` enum('admin','teacher') DEFAULT 'teacher',
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `password`, `full_name`, `position_id`, `email`, `role`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'ผู้ดูแลระบบ', 5, 'admin@admin.com', 'admin', 1, '2025-10-03 07:06:14', '2025-10-03 11:16:30'),
(2, 'test01', '$2y$10$S23Db0vRYrdMCge7Uq/DnOJDe7Z8y5XL8ecNJsKIUhkuD2X/Pt9we', 'นายทดสอบ', 2, 'test01@test.com', 'teacher', 1, '2025-10-03 11:17:22', '2025-10-03 11:17:22'),
(3, 'student', '$2y$10$ze63Z9rgYrSaJ6ABqiZKKe3JK.YwTw.wkHoSvc4jFvPxNZhlIMpVC', 'สราวุธ', 1, 'kitispeed7@gmail.com', 'teacher', 1, '2025-11-29 08:00:02', '2025-11-29 08:00:02');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_created` (`created_at`);

--
-- Indexes for table `attachments`
--
ALTER TABLE `attachments`
  ADD PRIMARY KEY (`attachment_id`),
  ADD KEY `idx_lesson` (`lesson_id`);

--
-- Indexes for table `lessons`
--
ALTER TABLE `lessons`
  ADD PRIMARY KEY (`lesson_id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `idx_subject` (`subject_id`),
  ADD KEY `idx_sort` (`sort_order`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `levels`
--
ALTER TABLE `levels`
  ADD PRIMARY KEY (`level_id`),
  ADD UNIQUE KEY `level_code` (`level_code`),
  ADD KEY `idx_sort` (`sort_order`),
  ADD KEY `idx_active` (`is_active`);

--
-- Indexes for table `positions`
--
ALTER TABLE `positions`
  ADD PRIMARY KEY (`position_id`);

--
-- Indexes for table `site_settings`
--
ALTER TABLE `site_settings`
  ADD PRIMARY KEY (`setting_id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- Indexes for table `subjects`
--
ALTER TABLE `subjects`
  ADD PRIMARY KEY (`subject_id`),
  ADD UNIQUE KEY `subject_code` (`subject_code`),
  ADD KEY `idx_level` (`level_id`),
  ADD KEY `idx_sort` (`sort_order`),
  ADD KEY `idx_active` (`is_active`),
  ADD KEY `idx_created_by` (`created_by`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_username` (`username`),
  ADD KEY `idx_position` (`position_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `attachments`
--
ALTER TABLE `attachments`
  MODIFY `attachment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `lessons`
--
ALTER TABLE `lessons`
  MODIFY `lesson_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `levels`
--
ALTER TABLE `levels`
  MODIFY `level_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `positions`
--
ALTER TABLE `positions`
  MODIFY `position_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `site_settings`
--
ALTER TABLE `site_settings`
  MODIFY `setting_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `subjects`
--
ALTER TABLE `subjects`
  MODIFY `subject_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD CONSTRAINT `activity_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `attachments`
--
ALTER TABLE `attachments`
  ADD CONSTRAINT `attachments_ibfk_1` FOREIGN KEY (`lesson_id`) REFERENCES `lessons` (`lesson_id`) ON DELETE CASCADE;

--
-- Constraints for table `lessons`
--
ALTER TABLE `lessons`
  ADD CONSTRAINT `lessons_ibfk_1` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`subject_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `lessons_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `subjects`
--
ALTER TABLE `subjects`
  ADD CONSTRAINT `subjects_ibfk_1` FOREIGN KEY (`level_id`) REFERENCES `levels` (`level_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `subjects_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`position_id`) REFERENCES `positions` (`position_id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
