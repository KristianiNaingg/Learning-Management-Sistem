-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 04, 2025 at 03:42 PM
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
-- Database: `lms_x`
--

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `short_name` varchar(255) NOT NULL,
  `summary` text DEFAULT NULL,
  `cpmk` text DEFAULT NULL,
  `course_image` varchar(255) DEFAULT NULL,
  `semester` int(11) NOT NULL,
  `visible` tinyint(1) NOT NULL DEFAULT 1,
  `category` varchar(255) DEFAULT NULL,
  `start_date` datetime DEFAULT NULL,
  `end_date` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `course_users`
--

CREATE TABLE `course_users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `course_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `participant_role` enum('Student','Teacher','Admin','') NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `learning_dimensions`
--

CREATE TABLE `learning_dimensions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `style_name` varchar(255) NOT NULL,
  `dimension` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `learning_dimensions`
--

INSERT INTO `learning_dimensions` (`id`, `style_name`, `dimension`, `description`, `created_at`, `updated_at`) VALUES
(1, 'ACT/REF', 'Processing', 'Active/Reflective', '2025-06-20 05:15:02', '2025-06-20 05:15:02'),
(2, 'SNS/INT', 'Perception', 'Sensing/Intuitive', '2025-06-20 05:15:02', '2025-06-20 05:15:02'),
(3, 'VIS/VRB', 'Input', 'Visual/Verbal', '2025-06-20 05:15:02', '2025-06-20 05:15:02'),
(4, 'SEQ/GLO', 'Understanding', 'Sequential/Global', '2025-06-20 05:15:02', '2025-06-20 05:15:02');

-- --------------------------------------------------------

--
-- Table structure for table `learning_style_options`
--

CREATE TABLE `learning_style_options` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `style_option_name` varchar(255) NOT NULL,
  `learning_dimensions_id` bigint(20) UNSIGNED NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `learning_style_options`
--

INSERT INTO `learning_style_options` (`id`, `style_option_name`, `learning_dimensions_id`, `description`, `created_at`, `updated_at`) VALUES
(1, 'Visual', 3, NULL, '2025-06-20 05:52:25', '2025-06-20 05:52:25'),
(2, 'Verbal', 3, NULL, '2025-06-20 05:52:25', '2025-06-20 05:52:25'),
(3, 'Active', 1, NULL, '2025-06-20 05:52:25', '2025-06-20 05:52:25'),
(4, 'Reflective', 1, NULL, '2025-06-20 05:52:25', '2025-06-20 05:52:25'),
(5, 'Sensitive', 2, NULL, '2025-06-20 05:52:25', '2025-06-20 05:52:25'),
(6, 'Intuitive', 2, NULL, '2025-06-20 05:52:25', '2025-06-20 05:52:25'),
(7, 'Sequential', 4, NULL, '2025-06-20 05:52:25', '2025-06-20 05:52:25'),
(8, 'Global', 4, NULL, '2025-06-20 05:52:25', '2025-06-20 05:52:25');

-- --------------------------------------------------------

--
-- Table structure for table `lom_assignfeedback_comments`
--

CREATE TABLE `lom_assignfeedback_comments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `submission_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `comment` text NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lom_assigns`
--

CREATE TABLE `lom_assigns` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `subtopic_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `content` text DEFAULT NULL,
  `due_date` datetime DEFAULT NULL,
  `learning_style_option_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lom_assign_grades`
--

CREATE TABLE `lom_assign_grades` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `assign_id` bigint(20) UNSIGNED NOT NULL,
  `submission_id` bigint(20) UNSIGNED DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `grade` decimal(5,2) DEFAULT 0.00,
  `feedback` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lom_assign_submissions`
--

CREATE TABLE `lom_assign_submissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `assign_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `file_path` text DEFAULT NULL,
  `status` varchar(50) DEFAULT 'pending',
  `submitted_at` datetime DEFAULT current_timestamp(),
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lom_files`
--

CREATE TABLE `lom_files` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `file_path` text DEFAULT NULL,
  `subtopic_id` bigint(20) UNSIGNED NOT NULL,
  `learning_style_option_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lom_file_saves`
--

CREATE TABLE `lom_file_saves` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `folder_id` bigint(20) UNSIGNED NOT NULL,
  `file_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lom_folders`
--

CREATE TABLE `lom_folders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `subtopic_id` bigint(20) UNSIGNED NOT NULL,
  `learning_style_option_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lom_forums`
--

CREATE TABLE `lom_forums` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `subtopic_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `learning_style_option_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lom_forum_posts`
--

CREATE TABLE `lom_forum_posts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `forum_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `content` text NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lom_infographics`
--

CREATE TABLE `lom_infographics` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `file_path` text NOT NULL,
  `subtopic_id` bigint(20) UNSIGNED NOT NULL,
  `learning_style_option_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lom_labels`
--

CREATE TABLE `lom_labels` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `content` text NOT NULL,
  `subtopic_id` bigint(20) UNSIGNED NOT NULL,
  `learning_style_option_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lom_lessons`
--

CREATE TABLE `lom_lessons` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `subtopic_id` bigint(20) UNSIGNED NOT NULL,
  `learning_style_option_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lom_pages`
--

CREATE TABLE `lom_pages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `content` text DEFAULT NULL,
  `subtopic_id` bigint(20) UNSIGNED NOT NULL,
  `learning_style_option_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lom_quizzes`
--

CREATE TABLE `lom_quizzes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `subtopic_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `time_open` datetime DEFAULT NULL,
  `time_close` datetime DEFAULT NULL,
  `time_limit` int(10) UNSIGNED DEFAULT NULL,
  `max_attempts` int(10) UNSIGNED DEFAULT 1,
  `grade_to_pass` decimal(5,2) DEFAULT 0.00,
  `learning_dimension_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lom_quiz_answers`
--

CREATE TABLE `lom_quiz_answers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `attempt_id` bigint(20) UNSIGNED NOT NULL,
  `question_id` bigint(20) UNSIGNED NOT NULL,
  `answer` text NOT NULL,
  `poin` decimal(5,2) DEFAULT 0.00,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lom_quiz_attempts`
--

CREATE TABLE `lom_quiz_attempts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `quiz_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `attempt_number` int(10) UNSIGNED DEFAULT 1,
  `start_time` datetime DEFAULT NULL,
  `end_time` datetime DEFAULT NULL,
  `score` decimal(5,2) DEFAULT 0.00,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lom_quiz_grades`
--

CREATE TABLE `lom_quiz_grades` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `quiz_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `attempt_id` bigint(20) UNSIGNED NOT NULL,
  `grade` decimal(5,2) DEFAULT 0.00,
  `attempt_number` int(10) UNSIGNED DEFAULT 1,
  `completed_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lom_quiz_questions`
--

CREATE TABLE `lom_quiz_questions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `quiz_id` bigint(20) UNSIGNED NOT NULL,
  `question_text` text NOT NULL,
  `options_a` text NOT NULL,
  `options_b` text NOT NULL,
  `options_c` text NOT NULL,
  `options_d` text NOT NULL,
  `correct_answer` enum('a','b','c','d') NOT NULL,
  `poin` decimal(5,2) DEFAULT 0.00,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lom_urls`
--

CREATE TABLE `lom_urls` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `subtopic_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `url_link` text NOT NULL,
  `learning_style_option_id` bigint(20) UNSIGNED DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lom_user_logs`
--

CREATE TABLE `lom_user_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `lom_id` bigint(20) UNSIGNED NOT NULL,
  `lom_type` varchar(50) NOT NULL,
  `action` varchar(100) NOT NULL,
  `duration` int(11) DEFAULT NULL,
  `accessed_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_reset_tokens_table', 1),
(3, '2019_08_19_000000_create_failed_jobs_table', 1),
(4, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(5, '2025_06_20_041731_create_courses_table', 2),
(6, '2025_06_20_042949_create_dimension_options_table', 3),
(7, '2025_06_20_043042_create_dimensions_table', 3),
(8, '2025_06_20_043850_create_learning_dimensions_table', 3),
(9, '2025_06_20_044128_create_learning_style_options_table', 3),
(10, '2025_06_20_045828_create_topics_table', 3),
(11, '2025_06_20_053233_create_user_learning_style_options_table', 4),
(12, '2025_06_20_054654_create_learning_style_options_table', 5),
(13, '2025_06_20_071913_create_subtopics_table', 6),
(14, '2025_06_20_073039_create_topic_references_table', 7);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id_role` bigint(20) UNSIGNED NOT NULL,
  `name_role` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id_role`, `name_role`, `created_at`, `updated_at`) VALUES
(1, 'Admin', NULL, NULL),
(2, 'Teacher', NULL, NULL),
(3, 'Student', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `subtopics`
--

CREATE TABLE `subtopics` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `topic_id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `content` text DEFAULT NULL,
  `visible` tinyint(1) NOT NULL DEFAULT 1,
  `sortorder` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `topics`
--

CREATE TABLE `topics` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `course_id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `visible` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `sub_cpmk` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `topic_references`
--

CREATE TABLE `topic_references` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `topic_id` bigint(20) UNSIGNED NOT NULL,
  `content` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(10) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `level` int(11) DEFAULT 1,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `last_login_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('pending','active','non-active','') DEFAULT 'pending',
  `id_role` bigint(20) UNSIGNED DEFAULT 3
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `image`, `level`, `remember_token`, `created_at`, `updated_at`, `last_login_at`, `status`, `id_role`) VALUES
(1, 'admin', 'admin-lms-x@gmail.com', NULL, '$2y$10$ru6CMZ6Iu.dnXyevUA9r.epWWCLwH2Rk6PVJMSGUG/KWrmiaVjnuG', NULL, NULL, NULL, '2025-05-03 04:54:30', '2025-05-26 10:13:12', '2025-05-26 10:13:12', 'active', 1),
(2, 'Dosen', 'dosen-lmsx@gmail.com', NULL, '$2y$10$eYQge8F7ZawJpLIWbKwUfODoGt2OSegNAZBZX5SOUsOjo/G1uJJOC', NULL, 1, NULL, NULL, '2025-06-08 14:43:23', '2025-06-08 14:43:23', 'active', 2),
(2172036, 'Frangky Hernandez', '2172036@maranatha.ac.id', NULL, '$2y$10$lO15dyppO.m5qxwoqs7de.wsn1ZrBryMFa3sk2tP8CBi2EyQUKFWK', 'profile_1_1745888675.jpg', 1, NULL, '2025-03-09 22:21:49', '2025-05-26 00:13:58', '2025-05-26 00:13:58', 'active', 3),
(2172039, 'Rizky Jeremia Simanjuntak', '2172039@maranatha.ac.id', NULL, '$2y$10$uBtE5Wleji8GMZG1G1yr3.LyAqpNaH.KngJbFjN1b9JkLMN0IzPvG', '', 1, NULL, '2025-03-10 02:10:57', '2025-05-20 14:27:43', '2025-05-20 14:27:43', 'active', 3),
(2172044, 'Kristiani Nainggolan', '2172044@maranatha.ac.id', NULL, '$2y$10$fPIvnHHCuB/Xit4VRbHF0el3UWyd.t040CjrJHorSbm8fVUbQv3Ui', '', 1, NULL, NULL, '2025-05-02 08:05:21', '2025-05-02 08:05:21', 'pending', 3);

-- --------------------------------------------------------

--
-- Table structure for table `user_learning_style_options`
--

CREATE TABLE `user_learning_style_options` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `learning_style_option_id` bigint(20) UNSIGNED DEFAULT NULL,
  `dimension` varchar(255) DEFAULT NULL,
  `a_count` int(11) DEFAULT NULL,
  `b_count` int(11) DEFAULT NULL,
  `final_score` varchar(255) DEFAULT NULL,
  `category` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_learning_style_options`
--

INSERT INTO `user_learning_style_options` (`id`, `user_id`, `learning_style_option_id`, `dimension`, `a_count`, `b_count`, `final_score`, `category`, `description`, `created_at`, `updated_at`) VALUES
(1, 2172036, 4, 'ACT/REF', 5, 6, '1Reflective', 'Balanced', NULL, '2025-05-07 19:34:35', '2025-05-07 19:34:52'),
(2, 2172036, 6, 'SNS/INT', 1, 10, '9Intuitive', 'Strong', NULL, '2025-05-07 19:34:35', '2025-05-07 19:34:52'),
(3, 2172036, 2, 'VIS/VRB', 8, 3, '5Verbal', 'Moderate', NULL, '2025-05-07 19:34:35', '2025-05-07 19:34:52'),
(4, 2172036, 8, 'SEQ/GLO', 6, 5, '1Global', 'Balanced', NULL, '2025-05-07 19:34:35', '2025-05-07 19:34:52'),
(5, 2172039, 4, 'ACT/REF', 8, 3, '5Reflective', 'Moderate', NULL, '2025-05-07 19:34:35', '2025-05-07 19:34:52'),
(6, 2172039, 6, 'SNS/INT', 8, 3, '5Intuitive', 'Moderate', NULL, '2025-05-07 19:34:35', '2025-05-07 19:34:52'),
(7, 2172039, 2, 'VIS/VRB', 9, 2, '7Verbal', 'Moderate', NULL, '2025-05-07 19:34:35', '2025-05-07 19:34:52'),
(8, 2172039, 8, 'SEQ/GLO', 8, 3, '5Global', 'Moderate', NULL, '2025-05-07 19:34:35', '2025-05-07 19:34:52'),
(9, 2172044, 3, 'ACT/REF', 9, 2, '7Active', 'Moderate', NULL, '2025-05-07 19:34:35', '2025-05-07 19:34:52'),
(10, 2172044, 5, 'SNS/INT', 6, 5, '1Sensing', 'Balanced', NULL, '2025-05-07 19:34:35', '2025-05-07 19:34:52'),
(11, 2172044, 1, 'VIS/VRB', 7, 4, '3Visual', 'Balanced', NULL, '2025-05-07 19:34:35', '2025-05-07 19:34:52'),
(12, 2172044, 7, 'SEQ/GLO', 8, 3, '5Sequential', 'Moderate', NULL, '2025-05-07 19:34:35', '2025-05-07 19:34:52');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `course_users`
--
ALTER TABLE `course_users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `course_id` (`course_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `learning_dimensions`
--
ALTER TABLE `learning_dimensions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `learning_style_options`
--
ALTER TABLE `learning_style_options`
  ADD PRIMARY KEY (`id`),
  ADD KEY `learning_style_options_learning_dimensions_id_foreign` (`learning_dimensions_id`);

--
-- Indexes for table `lom_assignfeedback_comments`
--
ALTER TABLE `lom_assignfeedback_comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `submission_id_idx` (`submission_id`),
  ADD KEY `user_id_idx` (`user_id`);

--
-- Indexes for table `lom_assigns`
--
ALTER TABLE `lom_assigns`
  ADD PRIMARY KEY (`id`),
  ADD KEY `subtopic_id_idx` (`subtopic_id`);

--
-- Indexes for table `lom_assign_grades`
--
ALTER TABLE `lom_assign_grades`
  ADD PRIMARY KEY (`id`),
  ADD KEY `assign_id_idx` (`assign_id`),
  ADD KEY `submission_id_idx` (`submission_id`),
  ADD KEY `user_id_idx` (`user_id`);

--
-- Indexes for table `lom_assign_submissions`
--
ALTER TABLE `lom_assign_submissions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `assign_id_idx` (`assign_id`),
  ADD KEY `user_id_idx` (`user_id`);

--
-- Indexes for table `lom_files`
--
ALTER TABLE `lom_files`
  ADD PRIMARY KEY (`id`),
  ADD KEY `subtopic_id_idx` (`subtopic_id`),
  ADD KEY `learning_style_option_id_idx` (`learning_style_option_id`);

--
-- Indexes for table `lom_file_saves`
--
ALTER TABLE `lom_file_saves`
  ADD PRIMARY KEY (`id`),
  ADD KEY `folder_id_idx` (`folder_id`),
  ADD KEY `file_id_idx` (`file_id`);

--
-- Indexes for table `lom_folders`
--
ALTER TABLE `lom_folders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `subtopic_id_idx` (`subtopic_id`),
  ADD KEY `learning_style_option_id_idx` (`learning_style_option_id`);

--
-- Indexes for table `lom_forums`
--
ALTER TABLE `lom_forums`
  ADD PRIMARY KEY (`id`),
  ADD KEY `subtopic_id_idx` (`subtopic_id`),
  ADD KEY `learning_style_option_id_idx` (`learning_style_option_id`);

--
-- Indexes for table `lom_forum_posts`
--
ALTER TABLE `lom_forum_posts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `forum_id_idx` (`forum_id`),
  ADD KEY `user_id_idx` (`user_id`);

--
-- Indexes for table `lom_infographics`
--
ALTER TABLE `lom_infographics`
  ADD PRIMARY KEY (`id`),
  ADD KEY `subtopic_id_idx` (`subtopic_id`),
  ADD KEY `learning_style_option_id_idx` (`learning_style_option_id`);

--
-- Indexes for table `lom_labels`
--
ALTER TABLE `lom_labels`
  ADD PRIMARY KEY (`id`),
  ADD KEY `subtopic_id_idx` (`subtopic_id`),
  ADD KEY `learning_style_option_id_idx` (`learning_style_option_id`);

--
-- Indexes for table `lom_lessons`
--
ALTER TABLE `lom_lessons`
  ADD PRIMARY KEY (`id`),
  ADD KEY `subtopic_id_idx` (`subtopic_id`),
  ADD KEY `learning_style_option_id_idx` (`learning_style_option_id`);

--
-- Indexes for table `lom_pages`
--
ALTER TABLE `lom_pages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `subtopic_id_idx` (`subtopic_id`),
  ADD KEY `learning_style_option_id_idx` (`learning_style_option_id`);

--
-- Indexes for table `lom_quizzes`
--
ALTER TABLE `lom_quizzes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `lom_quiz_answers`
--
ALTER TABLE `lom_quiz_answers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `attempt_id_idx` (`attempt_id`),
  ADD KEY `question_id_idx` (`question_id`),
  ADD KEY `user_id_idx` (`user_id`);

--
-- Indexes for table `lom_quiz_attempts`
--
ALTER TABLE `lom_quiz_attempts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `quiz_id_idx` (`quiz_id`),
  ADD KEY `user_id_idx` (`user_id`);

--
-- Indexes for table `lom_quiz_grades`
--
ALTER TABLE `lom_quiz_grades`
  ADD PRIMARY KEY (`id`),
  ADD KEY `attempt_id` (`attempt_id`),
  ADD KEY `lom_quiz_grades_ibfk_2` (`quiz_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `lom_quiz_questions`
--
ALTER TABLE `lom_quiz_questions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `quiz_id_idx` (`quiz_id`);

--
-- Indexes for table `lom_urls`
--
ALTER TABLE `lom_urls`
  ADD PRIMARY KEY (`id`),
  ADD KEY `subtopic_id_idx` (`subtopic_id`),
  ADD KEY `learning_style_option_id_idx` (`learning_style_option_id`);

--
-- Indexes for table `lom_user_logs`
--
ALTER TABLE `lom_user_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id_idx` (`user_id`),
  ADD KEY `lom_id_idx` (`lom_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id_role`);

--
-- Indexes for table `subtopics`
--
ALTER TABLE `subtopics`
  ADD PRIMARY KEY (`id`),
  ADD KEY `subtopics_topic_id_foreign` (`topic_id`);

--
-- Indexes for table `topics`
--
ALTER TABLE `topics`
  ADD PRIMARY KEY (`id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `topic_references`
--
ALTER TABLE `topic_references`
  ADD PRIMARY KEY (`id`),
  ADD KEY `topic_references_topic_id_foreign` (`topic_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD KEY `id_role` (`id_role`);

--
-- Indexes for table `user_learning_style_options`
--
ALTER TABLE `user_learning_style_options`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_learning_style_options_ibfk_1` (`learning_style_option_id`),
  ADD KEY `user_learning_style_options_user_id_foreign` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `course_users`
--
ALTER TABLE `course_users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `learning_dimensions`
--
ALTER TABLE `learning_dimensions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `learning_style_options`
--
ALTER TABLE `learning_style_options`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `lom_assignfeedback_comments`
--
ALTER TABLE `lom_assignfeedback_comments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lom_assigns`
--
ALTER TABLE `lom_assigns`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lom_assign_grades`
--
ALTER TABLE `lom_assign_grades`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lom_assign_submissions`
--
ALTER TABLE `lom_assign_submissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lom_files`
--
ALTER TABLE `lom_files`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lom_file_saves`
--
ALTER TABLE `lom_file_saves`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lom_folders`
--
ALTER TABLE `lom_folders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lom_forums`
--
ALTER TABLE `lom_forums`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lom_forum_posts`
--
ALTER TABLE `lom_forum_posts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lom_infographics`
--
ALTER TABLE `lom_infographics`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lom_labels`
--
ALTER TABLE `lom_labels`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lom_lessons`
--
ALTER TABLE `lom_lessons`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lom_pages`
--
ALTER TABLE `lom_pages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lom_quizzes`
--
ALTER TABLE `lom_quizzes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lom_quiz_answers`
--
ALTER TABLE `lom_quiz_answers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lom_quiz_attempts`
--
ALTER TABLE `lom_quiz_attempts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lom_quiz_grades`
--
ALTER TABLE `lom_quiz_grades`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lom_quiz_questions`
--
ALTER TABLE `lom_quiz_questions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lom_urls`
--
ALTER TABLE `lom_urls`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lom_user_logs`
--
ALTER TABLE `lom_user_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id_role` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `subtopics`
--
ALTER TABLE `subtopics`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `topics`
--
ALTER TABLE `topics`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `topic_references`
--
ALTER TABLE `topic_references`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_learning_style_options`
--
ALTER TABLE `user_learning_style_options`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `course_users`
--
ALTER TABLE `course_users`
  ADD CONSTRAINT `course_users_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `course_users_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `learning_style_options`
--
ALTER TABLE `learning_style_options`
  ADD CONSTRAINT `learning_style_options_learning_dimensions_id_foreign` FOREIGN KEY (`learning_dimensions_id`) REFERENCES `learning_dimensions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `lom_assignfeedback_comments`
--
ALTER TABLE `lom_assignfeedback_comments`
  ADD CONSTRAINT `feedback_submission_fk` FOREIGN KEY (`submission_id`) REFERENCES `lom_assign_submissions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `feedback_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `lom_assigns`
--
ALTER TABLE `lom_assigns`
  ADD CONSTRAINT `lom_assigns_subtopic_fk` FOREIGN KEY (`subtopic_id`) REFERENCES `subtopics` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `lom_assign_grades`
--
ALTER TABLE `lom_assign_grades`
  ADD CONSTRAINT `grades_assign_fk` FOREIGN KEY (`assign_id`) REFERENCES `lom_assigns` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `grades_submission_fk` FOREIGN KEY (`submission_id`) REFERENCES `lom_assign_submissions` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `grades_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `lom_assign_submissions`
--
ALTER TABLE `lom_assign_submissions`
  ADD CONSTRAINT `submissions_assign_fk` FOREIGN KEY (`assign_id`) REFERENCES `lom_assigns` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `submissions_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `lom_files`
--
ALTER TABLE `lom_files`
  ADD CONSTRAINT `files_learning_style_fk` FOREIGN KEY (`learning_style_option_id`) REFERENCES `learning_style_options` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `files_subtopic_fk` FOREIGN KEY (`subtopic_id`) REFERENCES `subtopics` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `lom_file_saves`
--
ALTER TABLE `lom_file_saves`
  ADD CONSTRAINT `file_saves_file_fk` FOREIGN KEY (`file_id`) REFERENCES `lom_files` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `file_saves_folder_fk` FOREIGN KEY (`folder_id`) REFERENCES `lom_folders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `lom_folders`
--
ALTER TABLE `lom_folders`
  ADD CONSTRAINT `folders_learning_style_fk` FOREIGN KEY (`learning_style_option_id`) REFERENCES `learning_style_options` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `folders_subtopic_fk` FOREIGN KEY (`subtopic_id`) REFERENCES `subtopics` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `lom_forums`
--
ALTER TABLE `lom_forums`
  ADD CONSTRAINT `forums_learning_style_fk` FOREIGN KEY (`learning_style_option_id`) REFERENCES `learning_style_options` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `forums_subtopic_fk` FOREIGN KEY (`subtopic_id`) REFERENCES `subtopics` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `lom_forum_posts`
--
ALTER TABLE `lom_forum_posts`
  ADD CONSTRAINT `posts_forum_fk` FOREIGN KEY (`forum_id`) REFERENCES `lom_forums` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `posts_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `lom_infographics`
--
ALTER TABLE `lom_infographics`
  ADD CONSTRAINT `infographics_learning_style_fk` FOREIGN KEY (`learning_style_option_id`) REFERENCES `learning_style_options` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `infographics_subtopic_fk` FOREIGN KEY (`subtopic_id`) REFERENCES `subtopics` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `lom_labels`
--
ALTER TABLE `lom_labels`
  ADD CONSTRAINT `labels_learning_style_fk` FOREIGN KEY (`learning_style_option_id`) REFERENCES `learning_style_options` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `labels_subtopic_fk` FOREIGN KEY (`subtopic_id`) REFERENCES `subtopics` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `lom_lessons`
--
ALTER TABLE `lom_lessons`
  ADD CONSTRAINT `lessons_learning_style_fk` FOREIGN KEY (`learning_style_option_id`) REFERENCES `learning_style_options` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `lessons_subtopic_fk` FOREIGN KEY (`subtopic_id`) REFERENCES `subtopics` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `lom_pages`
--
ALTER TABLE `lom_pages`
  ADD CONSTRAINT `pages_learning_style_fk` FOREIGN KEY (`learning_style_option_id`) REFERENCES `learning_style_options` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `pages_subtopic_fk` FOREIGN KEY (`subtopic_id`) REFERENCES `subtopics` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `lom_quiz_answers`
--
ALTER TABLE `lom_quiz_answers`
  ADD CONSTRAINT `answers_attempt_fk` FOREIGN KEY (`attempt_id`) REFERENCES `lom_quiz_attempts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `answers_question_fk` FOREIGN KEY (`question_id`) REFERENCES `lom_quiz_questions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `answers_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `lom_quiz_attempts`
--
ALTER TABLE `lom_quiz_attempts`
  ADD CONSTRAINT `attempts_quiz_fk` FOREIGN KEY (`quiz_id`) REFERENCES `lom_quizzes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `attempts_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `lom_quiz_grades`
--
ALTER TABLE `lom_quiz_grades`
  ADD CONSTRAINT `lom_quiz_grades_ibfk_1` FOREIGN KEY (`attempt_id`) REFERENCES `lom_quiz_attempts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `lom_quiz_grades_ibfk_2` FOREIGN KEY (`quiz_id`) REFERENCES `lom_quizzes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `lom_quiz_grades_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `lom_quiz_questions`
--
ALTER TABLE `lom_quiz_questions`
  ADD CONSTRAINT `questions_quiz_fk` FOREIGN KEY (`quiz_id`) REFERENCES `lom_quizzes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `lom_urls`
--
ALTER TABLE `lom_urls`
  ADD CONSTRAINT `urls_learning_style_fk` FOREIGN KEY (`learning_style_option_id`) REFERENCES `learning_style_options` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `urls_subtopic_fk` FOREIGN KEY (`subtopic_id`) REFERENCES `subtopics` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `lom_user_logs`
--
ALTER TABLE `lom_user_logs`
  ADD CONSTRAINT `lom_user_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `subtopics`
--
ALTER TABLE `subtopics`
  ADD CONSTRAINT `subtopics_topic_id_foreign` FOREIGN KEY (`topic_id`) REFERENCES `topics` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `topics`
--
ALTER TABLE `topics`
  ADD CONSTRAINT `topics_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `topic_references`
--
ALTER TABLE `topic_references`
  ADD CONSTRAINT `topic_references_topic_id_foreign` FOREIGN KEY (`topic_id`) REFERENCES `topics` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`id_role`) REFERENCES `roles` (`id_role`) ON DELETE CASCADE;

--
-- Constraints for table `user_learning_style_options`
--
ALTER TABLE `user_learning_style_options`
  ADD CONSTRAINT `user_learning_style_options_ibfk_1` FOREIGN KEY (`learning_style_option_id`) REFERENCES `learning_style_options` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `user_learning_style_options_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
