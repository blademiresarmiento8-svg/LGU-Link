-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 21, 2026 at 04:15 PM
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
-- Database: `lgu_link`
--

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE `appointments` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `category` varchar(100) NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` text NOT NULL,
  `attachment_path` varchar(255) DEFAULT NULL,
  `attachment_original_name` varchar(255) DEFAULT NULL,
  `contact_number` varchar(30) NOT NULL,
  `email` varchar(150) NOT NULL,
  `organization` varchar(100) DEFAULT NULL,
  `position_title` varchar(100) DEFAULT NULL,
  `preferred_date` date DEFAULT NULL,
  `preferred_time` time DEFAULT NULL,
  `department_id` int(10) UNSIGNED DEFAULT NULL,
  `status` enum('pending_review','forwarded','scheduled','completed','rejected','cancelled') NOT NULL DEFAULT 'pending_review',
  `scheduled_date` date DEFAULT NULL,
  `scheduled_time` time DEFAULT NULL,
  `admin_notes` text DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL,
  `reviewed_by` int(10) UNSIGNED DEFAULT NULL,
  `scheduled_by` int(10) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `reviewed_at` datetime DEFAULT NULL,
  `scheduled_at` datetime DEFAULT NULL,
  `completed_at` datetime DEFAULT NULL,
  `is_demo` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `appointments`
--

INSERT INTO `appointments` (`id`, `user_id`, `category`, `title`, `description`, `attachment_path`, `attachment_original_name`, `contact_number`, `email`, `organization`, `position_title`, `preferred_date`, `preferred_time`, `department_id`, `status`, `scheduled_date`, `scheduled_time`, `admin_notes`, `rejection_reason`, `reviewed_by`, `scheduled_by`, `created_at`, `updated_at`, `reviewed_at`, `scheduled_at`, `completed_at`, `is_demo`) VALUES
(12, 2, 'Courtesy Call / Meeting Request', 'Sample Title', 'Sample Description', 'assets/uploads/appointments/aff3fe0c794e65b2c68ef415.png', 'sequence diagram.png', '09151713531', 'nikkisanpedro314@gmail.com', NULL, NULL, NULL, NULL, NULL, 'rejected', NULL, NULL, NULL, 'sample reject', 1, NULL, '2026-09-28 01:01:42', '2026-10-21 06:03:44', '2026-09-28 09:06:50', NULL, NULL, 0),
(15, 2, 'Community Concern / Complaint', 'Illegal Business', 'lorem ipsum dolor sit amet consectetur adipiscing elit in ducimus tempore ex dolores amet labore expedita laborum odio illum repellendus dolor eu voluptate sint excepturi do consequatur ut eligendi est fuga est aut imperdiet dolorum deserunt aliqua sint cupidatat in mollit optio imperdiet sunt magna facere qui elit nulla tempor', 'assets/uploads/appointments/dd3a684068181907d7364767.jpg', 'wallpapersden.com_tree-alone-dark-evening-4k_3840x2160.jpg', '09151713522', 'nikkisanpedro314@gmail.com', NULL, NULL, '2026-10-28', '09:07:00', 5, 'forwarded', '2026-10-30', '10:00:00', 'Please address this concern\n\nOffice note: Please be 15 minutes early and bring necessary documents', NULL, 1, 24, '2026-10-18 08:02:15', '2026-10-21 06:03:44', '2026-10-21 09:26:33', '2026-10-21 14:03:44', NULL, 0),
(23, 2, 'Business Proposal / Partnership', 'LGU Link System', 'We will be introducing a new system for your LGU', 'assets/uploads/appointments/f208344556fb04d056510ece.pdf', 'Document 2 (1).pdf', '09151713531', 'nikkisanpedro314@gmail.com', NULL, NULL, NULL, '13:00:00', NULL, 'pending_review', NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-19 11:33:30', '2026-10-19 11:33:30', NULL, NULL, NULL, 0),
(27, 2, 'Courtesy Call / Meeting Request', 'adfa', 'dfasdf', 'assets/uploads/appointments/5c398c22364415f2971b47cf.pdf', 'Document 5.pdf', 'asdfasdf', 'nikkisanpedro314@gmail.com', NULL, NULL, '2026-10-30', '16:30:00', NULL, 'pending_review', NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-19 11:53:03', '2026-10-19 11:53:03', NULL, NULL, NULL, 0),
(28, 2, 'Inter-LGU Coordination', 'serdt', 'sert', NULL, NULL, 'esrt', 'nikkisanpedro314@gmail.com', NULL, NULL, '2026-10-30', NULL, NULL, 'pending_review', NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-19 11:53:32', '2026-10-19 11:53:32', NULL, NULL, NULL, 0),
(810, 139, 'Community Concern / Complaint', 'Broken streetlights', 'We would like to request your assistance regarding: broken streetlights. Please let us know when we may visit the office.', NULL, NULL, '09170031676', 'jose.reyes@example.com', NULL, NULL, NULL, NULL, NULL, 'cancelled', NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-06 03:52:00', '2025-11-06 03:52:00', NULL, NULL, NULL, 1),
(811, 143, 'Community Concern / Complaint', 'Broken streetlights', 'We would like to request your assistance regarding: broken streetlights. Please let us know when we may visit the office.', NULL, NULL, '09170063352', 'ramon.villanueva@example.com', NULL, NULL, NULL, NULL, NULL, 'rejected', NULL, NULL, NULL, 'Outside the scope of the municipality.', 1, NULL, '2025-11-11 05:46:00', '2025-11-17 08:00:00', '2025-11-17 16:00:00', NULL, NULL, 1),
(812, 151, 'Community Concern / Complaint', 'Broken streetlights', 'We would like to request your assistance regarding: broken streetlights. Please let us know when we may visit the office.', NULL, NULL, '09170126704', 'antonio.pascual@example.com', NULL, NULL, NULL, NULL, 22, 'completed', '2025-11-18', '11:00:00', 'Please coordinate with the office.', NULL, 1, 21, '2025-11-11 06:25:00', '2025-11-17 20:00:00', '2025-11-13 10:00:00', '2025-11-17 13:00:00', '2025-11-18 04:00:00', 1),
(813, 144, 'Business Proposal / Partnership', 'Proposal to open a small business', 'We would like to request your assistance regarding: proposal to open a small business. Please let us know when we may visit the office.', NULL, NULL, '09170071271', 'liza.mendoza@example.com', NULL, NULL, NULL, NULL, 10, 'completed', '2025-11-21', '13:00:00', 'Please coordinate with the office.', NULL, 1, 37, '2025-11-12 07:12:00', '2025-11-20 20:00:00', '2025-11-13 16:00:00', '2025-11-17 18:00:00', '2025-11-21 04:00:00', 1),
(814, 139, 'Request for Assistance / Support', 'Burial assistance request', 'We would like to request your assistance regarding: burial assistance request. Please let us know when we may visit the office.', NULL, NULL, '09170031676', 'jose.reyes@example.com', NULL, NULL, NULL, NULL, NULL, 'rejected', NULL, NULL, NULL, 'Outside the scope of the municipality.', 1, NULL, '2025-11-14 01:49:00', '2025-11-17 01:00:00', '2025-11-17 09:00:00', NULL, NULL, 1),
(815, 142, 'Request for Assistance / Support', 'Scholarship support request', 'We would like to request your assistance regarding: scholarship support request. Please let us know when we may visit the office.', NULL, NULL, '09170055433', 'carmela.garcia@example.com', NULL, NULL, NULL, NULL, 15, 'completed', '2025-11-26', '14:00:00', 'Please coordinate with the office.', NULL, 1, 22, '2025-11-17 06:04:00', '2025-11-25 20:00:00', '2025-11-20 13:00:00', '2025-11-24 14:00:00', '2025-11-26 04:00:00', 1),
(816, 141, 'National Government Agency Coordination', 'Coordination for a national vaccination drive', 'We would like to request your assistance regarding: coordination for a national vaccination drive. Please let us know when we may visit the office.', NULL, NULL, '09170047514', 'pedro.bautista@example.com', NULL, NULL, NULL, NULL, NULL, 'rejected', NULL, NULL, NULL, 'Outside the scope of the municipality.', 1, NULL, '2025-11-18 00:10:00', '2025-11-18 04:00:00', '2025-11-18 12:00:00', NULL, NULL, 1),
(817, 139, 'Request for Assistance / Support', 'Request for farm inputs', 'We would like to request your assistance regarding: request for farm inputs. Please let us know when we may visit the office.', NULL, NULL, '09170031676', 'jose.reyes@example.com', NULL, NULL, NULL, NULL, 16, 'completed', '2025-12-01', '10:00:00', 'Please coordinate with the office.', NULL, 1, 11, '2025-11-20 05:24:00', '2025-11-30 20:00:00', '2025-11-21 13:00:00', '2025-11-26 13:00:00', '2025-12-01 04:00:00', 1),
(818, 151, 'Community Concern / Complaint', 'Broken streetlights', 'We would like to request your assistance regarding: broken streetlights. Please let us know when we may visit the office.', NULL, NULL, '09170126704', 'antonio.pascual@example.com', NULL, NULL, NULL, NULL, 1, 'completed', '2025-12-01', '12:00:00', 'Please coordinate with the office.', NULL, 1, 19, '2025-11-21 01:39:00', '2025-11-30 20:00:00', '2025-11-24 14:00:00', '2025-11-27 14:00:00', '2025-12-01 04:00:00', 1),
(819, 150, 'Courtesy Call / Meeting Request', 'Courtesy call from a civic group', 'We would like to request your assistance regarding: courtesy call from a civic group. Please let us know when we may visit the office.', NULL, NULL, '09170118785', 'josefina.ramos@example.com', NULL, NULL, NULL, NULL, 1, 'completed', '2025-11-28', '09:00:00', 'Please coordinate with the office.', NULL, 1, 19, '2025-11-21 02:21:00', '2025-11-27 20:00:00', '2025-11-25 11:00:00', '2025-11-27 11:00:00', '2025-11-28 04:00:00', 1),
(820, 148, 'Request for Assistance / Support', 'Burial assistance request', 'We would like to request your assistance regarding: burial assistance request. Please let us know when we may visit the office.', NULL, NULL, '09170102947', 'marites.domingo@example.com', NULL, NULL, NULL, NULL, 15, 'completed', '2025-12-01', '11:00:00', 'Please coordinate with the office.', NULL, 1, 22, '2025-11-21 04:01:00', '2025-11-30 20:00:00', '2025-11-24 11:00:00', '2025-11-26 11:00:00', '2025-12-01 04:00:00', 1),
(821, 144, 'Request for Assistance / Support', 'Medical assistance for a family member', 'We would like to request your assistance regarding: medical assistance for a family member. Please let us know when we may visit the office.', NULL, NULL, '09170071271', 'liza.mendoza@example.com', NULL, NULL, NULL, NULL, 14, 'completed', '2025-12-04', '12:00:00', 'Please coordinate with the office.', NULL, 1, 25, '2025-11-24 08:14:00', '2025-12-03 20:00:00', '2025-11-26 15:00:00', '2025-12-01 16:00:00', '2025-12-04 04:00:00', 1),
(822, 144, 'Courtesy Call / Meeting Request', 'Meeting request with the Mayor', 'We would like to request your assistance regarding: meeting request with the mayor. Please let us know when we may visit the office.', NULL, NULL, '09170071271', 'liza.mendoza@example.com', NULL, NULL, NULL, NULL, NULL, 'rejected', NULL, NULL, NULL, 'Outside the scope of the municipality.', 1, NULL, '2025-11-24 08:28:00', '2025-11-25 04:00:00', '2025-11-25 12:00:00', NULL, NULL, 1),
(823, 148, 'Request for Assistance / Support', 'Scholarship support request', 'We would like to request your assistance regarding: scholarship support request. Please let us know when we may visit the office.', NULL, NULL, '09170102947', 'marites.domingo@example.com', NULL, NULL, NULL, NULL, 16, 'completed', '2025-12-03', '10:00:00', 'Please coordinate with the office.', NULL, 1, 11, '2025-11-25 00:05:00', '2025-12-02 20:00:00', '2025-11-27 16:00:00', '2025-12-01 16:00:00', '2025-12-03 04:00:00', 1),
(824, 139, 'Community Concern / Complaint', 'Broken streetlights', 'We would like to request your assistance regarding: broken streetlights. Please let us know when we may visit the office.', NULL, NULL, '09170031676', 'jose.reyes@example.com', NULL, NULL, NULL, NULL, 1, 'completed', '2025-12-05', '11:00:00', 'Please coordinate with the office.', NULL, 1, 19, '2025-11-25 04:13:00', '2025-12-04 20:00:00', '2025-11-26 14:00:00', '2025-12-01 16:00:00', '2025-12-05 04:00:00', 1),
(825, 139, 'Request for Assistance / Support', 'Burial assistance request', 'We would like to request your assistance regarding: burial assistance request. Please let us know when we may visit the office.', NULL, NULL, '09170031676', 'jose.reyes@example.com', NULL, NULL, NULL, NULL, 15, 'completed', '2025-12-08', '10:00:00', 'Please coordinate with the office.', NULL, 1, 22, '2025-11-27 02:46:00', '2025-12-07 20:00:00', '2025-12-01 13:00:00', '2025-12-04 15:00:00', '2025-12-08 04:00:00', 1),
(826, 144, 'Project Proposal', 'Youth sports program proposal', 'We would like to request your assistance regarding: youth sports program proposal. Please let us know when we may visit the office.', NULL, NULL, '09170071271', 'liza.mendoza@example.com', NULL, NULL, NULL, NULL, 5, 'completed', '2025-12-11', '11:00:00', 'Please coordinate with the office.', NULL, 1, 24, '2025-11-28 02:06:00', '2025-12-10 20:00:00', '2025-12-01 11:00:00', '2025-12-08 13:00:00', '2025-12-11 04:00:00', 1),
(827, 146, 'Others', 'General inquiry', 'We would like to request your assistance regarding: general inquiry. Please let us know when we may visit the office.', NULL, NULL, '09170087109', 'teresita.aquino@example.com', NULL, NULL, NULL, NULL, 11, 'scheduled', '2025-12-16', '15:00:00', 'Please coordinate with the office.', NULL, 1, 12, '2025-12-03 01:16:00', '2025-12-12 07:00:00', '2025-12-08 12:00:00', '2025-12-12 15:00:00', NULL, 1),
(828, 144, 'Request for Assistance / Support', 'Request for farm inputs', 'We would like to request your assistance regarding: request for farm inputs. Please let us know when we may visit the office.', NULL, NULL, '09170071271', 'liza.mendoza@example.com', NULL, NULL, NULL, NULL, 14, 'completed', '2025-12-16', '09:00:00', 'Please coordinate with the office.', NULL, 1, 25, '2025-12-03 07:59:00', '2025-12-15 20:00:00', '2025-12-08 16:00:00', '2025-12-15 19:00:00', '2025-12-16 04:00:00', 1),
(829, 138, 'Community Concern / Complaint', 'Noise complaint', 'We would like to request your assistance regarding: noise complaint. Please let us know when we may visit the office.', NULL, NULL, '09170023757', 'maria.santos@example.com', NULL, NULL, NULL, NULL, 1, 'completed', '2025-12-15', '11:00:00', 'Please coordinate with the office.', NULL, 1, 19, '2025-12-08 01:57:00', '2025-12-14 20:00:00', '2025-12-08 16:00:00', '2025-12-11 18:00:00', '2025-12-15 04:00:00', 1),
(830, 148, 'Request for Assistance / Support', 'Medical assistance for a family member', 'We would like to request your assistance regarding: medical assistance for a family member. Please let us know when we may visit the office.', NULL, NULL, '09170102947', 'marites.domingo@example.com', NULL, NULL, NULL, NULL, 14, 'completed', '2025-12-12', '14:00:00', 'Please coordinate with the office.', NULL, 1, 25, '2025-12-08 03:23:00', '2025-12-11 20:00:00', '2025-12-09 11:00:00', '2025-12-11 13:00:00', '2025-12-12 04:00:00', 1),
(831, 145, 'Request for Assistance / Support', 'Burial assistance request', 'We would like to request your assistance regarding: burial assistance request. Please let us know when we may visit the office.', NULL, NULL, '09170079190', 'eduardo.cruz@example.com', NULL, NULL, NULL, NULL, 15, 'completed', '2025-12-18', '14:00:00', 'Please coordinate with the office.', NULL, 1, 22, '2025-12-08 04:23:00', '2025-12-17 20:00:00', '2025-12-10 14:00:00', '2025-12-15 15:00:00', '2025-12-18 04:00:00', 1),
(832, 141, 'Request for Assistance / Support', 'Burial assistance request', 'We would like to request your assistance regarding: burial assistance request. Please let us know when we may visit the office.', NULL, NULL, '09170047514', 'pedro.bautista@example.com', NULL, NULL, NULL, NULL, 16, 'completed', '2025-12-19', '10:00:00', 'Please coordinate with the office.', NULL, 1, 11, '2025-12-10 04:35:00', '2025-12-18 20:00:00', '2025-12-15 14:00:00', '2025-12-16 17:00:00', '2025-12-19 04:00:00', 1),
(833, 148, 'Request for Assistance / Support', 'Request for farm inputs', 'We would like to request your assistance regarding: request for farm inputs. Please let us know when we may visit the office.', NULL, NULL, '09170102947', 'marites.domingo@example.com', NULL, NULL, NULL, NULL, 14, 'completed', '2025-12-16', '13:00:00', 'Please coordinate with the office.', NULL, 1, 25, '2025-12-10 07:55:00', '2025-12-15 20:00:00', '2025-12-12 10:00:00', '2025-12-15 10:00:00', '2025-12-16 04:00:00', 1),
(834, 140, 'Request for Assistance / Support', 'Scholarship support request', 'We would like to request your assistance regarding: scholarship support request. Please let us know when we may visit the office.', NULL, NULL, '09170039595', 'ana.delacruz@example.com', NULL, NULL, NULL, NULL, 15, 'completed', '2025-12-17', '12:00:00', 'Please coordinate with the office.', NULL, 1, 22, '2025-12-11 08:47:00', '2025-12-16 20:00:00', '2025-12-15 15:00:00', '2025-12-16 18:00:00', '2025-12-17 04:00:00', 1),
(835, 139, 'Inter-LGU Coordination', 'Shared disaster-response drill', 'We would like to request your assistance regarding: shared disaster-response drill. Please let us know when we may visit the office.', NULL, NULL, '09170031676', 'jose.reyes@example.com', NULL, NULL, NULL, NULL, 1, 'completed', '2025-12-24', '09:00:00', 'Please coordinate with the office.', NULL, 1, 19, '2025-12-16 06:49:00', '2025-12-23 20:00:00', '2025-12-22 13:00:00', '2025-12-23 16:00:00', '2025-12-24 04:00:00', 1),
(836, 142, 'Community Concern / Complaint', 'Broken streetlights', 'We would like to request your assistance regarding: broken streetlights. Please let us know when we may visit the office.', NULL, NULL, '09170055433', 'carmela.garcia@example.com', NULL, NULL, NULL, NULL, 21, 'completed', '2025-12-25', '11:00:00', 'Please coordinate with the office.', NULL, 1, 15, '2025-12-17 04:21:00', '2025-12-24 20:00:00', '2025-12-19 12:00:00', '2025-12-22 15:00:00', '2025-12-25 04:00:00', 1),
(837, 146, 'Community Concern / Complaint', 'Flooded road near the barangay hall', 'We would like to request your assistance regarding: flooded road near the barangay hall. Please let us know when we may visit the office.', NULL, NULL, '09170087109', 'teresita.aquino@example.com', NULL, NULL, NULL, NULL, 21, 'completed', '2026-01-02', '10:00:00', 'Please coordinate with the office.', NULL, 1, 15, '2025-12-23 00:37:00', '2026-01-01 20:00:00', '2025-12-29 10:00:00', '2025-12-31 12:00:00', '2026-01-02 04:00:00', 1),
(838, 143, 'Request for Assistance / Support', 'Request for farm inputs', 'We would like to request your assistance regarding: request for farm inputs. Please let us know when we may visit the office.', NULL, NULL, '09170063352', 'ramon.villanueva@example.com', NULL, NULL, NULL, NULL, NULL, 'rejected', NULL, NULL, NULL, 'Outside the scope of the municipality.', 1, NULL, '2025-12-24 05:55:00', '2025-12-25 05:00:00', '2025-12-25 13:00:00', NULL, NULL, 1),
(839, 138, 'Community Concern / Complaint', 'Broken streetlights', 'We would like to request your assistance regarding: broken streetlights. Please let us know when we may visit the office.', NULL, NULL, '09170023757', 'maria.santos@example.com', NULL, NULL, NULL, NULL, NULL, 'rejected', NULL, NULL, NULL, 'Outside the scope of the municipality.', 1, NULL, '2025-12-25 05:56:00', '2025-12-26 07:00:00', '2025-12-26 15:00:00', NULL, NULL, 1),
(840, 149, 'National Government Agency Coordination', 'Coordination for a national vaccination drive', 'We would like to request your assistance regarding: coordination for a national vaccination drive. Please let us know when we may visit the office.', NULL, NULL, '09170110866', 'felipe.castillo@example.com', NULL, NULL, NULL, NULL, 5, 'completed', '2026-01-02', '14:00:00', 'Please coordinate with the office.', NULL, 1, 24, '2025-12-25 05:57:00', '2026-01-01 20:00:00', '2025-12-26 09:00:00', '2025-12-31 12:00:00', '2026-01-02 04:00:00', 1),
(841, 140, 'Project Proposal', 'Proposed barangay drainage project', 'We would like to request your assistance regarding: proposed barangay drainage project. Please let us know when we may visit the office.', NULL, NULL, '09170039595', 'ana.delacruz@example.com', NULL, NULL, NULL, NULL, 21, 'completed', '2026-01-05', '13:00:00', 'Please coordinate with the office.', NULL, 1, 15, '2025-12-26 02:09:00', '2026-01-04 20:00:00', '2025-12-29 16:00:00', '2026-01-01 16:00:00', '2026-01-05 04:00:00', 1),
(842, 148, 'Community Concern / Complaint', 'Flooded road near the barangay hall', 'We would like to request your assistance regarding: flooded road near the barangay hall. Please let us know when we may visit the office.', NULL, NULL, '09170102947', 'marites.domingo@example.com', NULL, NULL, NULL, NULL, NULL, 'rejected', NULL, NULL, NULL, 'Outside the scope of the municipality.', 1, NULL, '2025-12-29 05:33:00', '2025-12-31 03:00:00', '2025-12-31 11:00:00', NULL, NULL, 1),
(843, 141, 'Community Concern / Complaint', 'Noise complaint', 'We would like to request your assistance regarding: noise complaint. Please let us know when we may visit the office.', NULL, NULL, '09170047514', 'pedro.bautista@example.com', NULL, NULL, NULL, NULL, 22, 'completed', '2026-01-08', '11:00:00', 'Please coordinate with the office.', NULL, 1, 21, '2025-12-29 07:32:00', '2026-01-07 20:00:00', '2025-12-31 15:00:00', '2026-01-05 15:00:00', '2026-01-08 04:00:00', 1),
(844, 149, 'Community Concern / Complaint', 'Illegal dumping complaint', 'We would like to request your assistance regarding: illegal dumping complaint. Please let us know when we may visit the office.', NULL, NULL, '09170110866', 'felipe.castillo@example.com', NULL, NULL, NULL, NULL, NULL, 'rejected', NULL, NULL, NULL, 'Outside the scope of the municipality.', 1, NULL, '2025-12-30 02:45:00', '2025-12-31 02:00:00', '2025-12-31 10:00:00', NULL, NULL, 1),
(845, 144, 'Courtesy Call / Meeting Request', 'Courtesy call from a civic group', 'We would like to request your assistance regarding: courtesy call from a civic group. Please let us know when we may visit the office.', NULL, NULL, '09170071271', 'liza.mendoza@example.com', NULL, NULL, NULL, NULL, NULL, 'cancelled', NULL, NULL, NULL, NULL, NULL, NULL, '2025-12-31 05:49:00', '2025-12-31 05:49:00', NULL, NULL, NULL, 1),
(846, 145, 'Business Proposal / Partnership', 'Sponsorship for the town fiesta', 'We would like to request your assistance regarding: sponsorship for the town fiesta. Please let us know when we may visit the office.', NULL, NULL, '09170079190', 'eduardo.cruz@example.com', NULL, NULL, NULL, NULL, 5, 'completed', '2026-01-09', '15:00:00', 'Please coordinate with the office.', NULL, 1, 24, '2026-01-01 07:20:00', '2026-01-08 20:00:00', '2026-01-05 10:00:00', '2026-01-06 10:00:00', '2026-01-09 04:00:00', 1),
(847, 144, 'Request for Assistance / Support', 'Request for farm inputs', 'We would like to request your assistance regarding: request for farm inputs. Please let us know when we may visit the office.', NULL, NULL, '09170071271', 'liza.mendoza@example.com', NULL, NULL, NULL, NULL, 14, 'completed', '2026-01-09', '11:00:00', 'Please coordinate with the office.', NULL, 1, 25, '2026-01-01 08:49:00', '2026-01-08 20:00:00', '2026-01-05 09:00:00', '2026-01-07 12:00:00', '2026-01-09 04:00:00', 1),
(848, 150, 'Request for Assistance / Support', 'Burial assistance request', 'We would like to request your assistance regarding: burial assistance request. Please let us know when we may visit the office.', NULL, NULL, '09170118785', 'josefina.ramos@example.com', NULL, NULL, NULL, NULL, NULL, 'cancelled', NULL, NULL, NULL, NULL, NULL, NULL, '2026-01-05 04:53:00', '2026-01-05 04:53:00', NULL, NULL, NULL, 1),
(849, 146, 'Project Proposal', 'Livelihood training proposal', 'We would like to request your assistance regarding: livelihood training proposal. Please let us know when we may visit the office.', NULL, NULL, '09170087109', 'teresita.aquino@example.com', NULL, NULL, NULL, NULL, 5, 'completed', '2026-01-14', '09:00:00', 'Please coordinate with the office.', NULL, 1, 24, '2026-01-05 05:31:00', '2026-01-13 20:00:00', '2026-01-05 10:00:00', '2026-01-12 13:00:00', '2026-01-14 04:00:00', 1),
(850, 140, 'Request for Assistance / Support', 'Burial assistance request', 'We would like to request your assistance regarding: burial assistance request. Please let us know when we may visit the office.', NULL, NULL, '09170039595', 'ana.delacruz@example.com', NULL, NULL, NULL, NULL, 1, 'completed', '2026-01-13', '14:00:00', 'Please coordinate with the office.', NULL, 1, 19, '2026-01-06 02:13:00', '2026-01-12 20:00:00', '2026-01-07 11:00:00', '2026-01-12 11:00:00', '2026-01-13 04:00:00', 1),
(851, 148, 'National Government Agency Coordination', 'Coordination with a national agency program', 'We would like to request your assistance regarding: coordination with a national agency program. Please let us know when we may visit the office.', NULL, NULL, '09170102947', 'marites.domingo@example.com', NULL, NULL, NULL, NULL, 11, 'scheduled', '2026-01-12', '15:00:00', 'Please coordinate with the office.', NULL, 1, 12, '2026-01-06 06:30:00', '2026-01-09 02:00:00', '2026-01-07 09:00:00', '2026-01-09 10:00:00', NULL, 1),
(852, 143, 'Project Proposal', 'Proposed barangay drainage project', 'We would like to request your assistance regarding: proposed barangay drainage project. Please let us know when we may visit the office.', NULL, NULL, '09170063352', 'ramon.villanueva@example.com', NULL, NULL, NULL, NULL, 21, 'completed', '2026-01-15', '14:00:00', 'Please coordinate with the office.', NULL, 1, 15, '2026-01-07 00:05:00', '2026-01-14 20:00:00', '2026-01-07 14:00:00', '2026-01-12 17:00:00', '2026-01-15 04:00:00', 1),
(853, 149, 'Request for Assistance / Support', 'Request for farm inputs', 'We would like to request your assistance regarding: request for farm inputs. Please let us know when we may visit the office.', NULL, NULL, '09170110866', 'felipe.castillo@example.com', NULL, NULL, NULL, NULL, NULL, 'cancelled', NULL, NULL, NULL, NULL, NULL, NULL, '2026-01-07 02:11:00', '2026-01-07 02:11:00', NULL, NULL, NULL, 1),
(854, 151, 'Community Concern / Complaint', 'Flooded road near the barangay hall', 'We would like to request your assistance regarding: flooded road near the barangay hall. Please let us know when we may visit the office.', NULL, NULL, '09170126704', 'antonio.pascual@example.com', NULL, NULL, NULL, NULL, 21, 'completed', '2026-01-12', '15:00:00', 'Please coordinate with the office.', NULL, 1, 15, '2026-01-07 06:03:00', '2026-01-11 20:00:00', '2026-01-07 14:00:00', '2026-01-08 14:00:00', '2026-01-12 04:00:00', 1),
(855, 151, 'Business Proposal / Partnership', 'Partnership with the municipal market', 'We would like to request your assistance regarding: partnership with the municipal market. Please let us know when we may visit the office.', NULL, NULL, '09170126704', 'antonio.pascual@example.com', NULL, NULL, NULL, NULL, NULL, 'rejected', NULL, NULL, NULL, 'Outside the scope of the municipality.', 1, NULL, '2026-01-08 02:17:00', '2026-01-12 03:00:00', '2026-01-12 11:00:00', NULL, NULL, 1),
(856, 147, 'Inter-LGU Coordination', 'Shared disaster-response drill', 'We would like to request your assistance regarding: shared disaster-response drill. Please let us know when we may visit the office.', NULL, NULL, '09170095028', 'rogelio.navarro@example.com', NULL, NULL, NULL, NULL, 1, 'completed', '2026-01-21', '10:00:00', 'Please coordinate with the office.', NULL, 1, 19, '2026-01-08 07:22:00', '2026-01-20 20:00:00', '2026-01-12 15:00:00', '2026-01-19 18:00:00', '2026-01-21 04:00:00', 1),
(857, 146, 'Business Proposal / Partnership', 'Sponsorship for the town fiesta', 'We would like to request your assistance regarding: sponsorship for the town fiesta. Please let us know when we may visit the office.', NULL, NULL, '09170087109', 'teresita.aquino@example.com', NULL, NULL, NULL, NULL, 23, 'completed', '2026-01-20', '12:00:00', 'Please coordinate with the office.', NULL, 1, 36, '2026-01-09 05:38:00', '2026-01-19 20:00:00', '2026-01-12 10:00:00', '2026-01-16 11:00:00', '2026-01-20 04:00:00', 1),
(858, 142, 'Project Proposal', 'Livelihood training proposal', 'We would like to request your assistance regarding: livelihood training proposal. Please let us know when we may visit the office.', NULL, NULL, '09170055433', 'carmela.garcia@example.com', NULL, NULL, NULL, NULL, 5, 'completed', '2026-01-16', '09:00:00', 'Please coordinate with the office.', NULL, 1, 24, '2026-01-09 08:21:00', '2026-01-15 20:00:00', '2026-01-12 13:00:00', '2026-01-14 13:00:00', '2026-01-16 04:00:00', 1),
(859, 147, 'Request for Assistance / Support', 'Request for farm inputs', 'We would like to request your assistance regarding: request for farm inputs. Please let us know when we may visit the office.', NULL, NULL, '09170095028', 'rogelio.navarro@example.com', NULL, NULL, NULL, NULL, 14, 'completed', '2026-01-22', '12:00:00', 'Please coordinate with the office.', NULL, 1, 25, '2026-01-13 05:31:00', '2026-01-21 20:00:00', '2026-01-16 16:00:00', '2026-01-19 18:00:00', '2026-01-22 04:00:00', 1),
(860, 151, 'Project Proposal', 'Livelihood training proposal', 'We would like to request your assistance regarding: livelihood training proposal. Please let us know when we may visit the office.', NULL, NULL, '09170126704', 'antonio.pascual@example.com', NULL, NULL, NULL, NULL, 21, 'completed', '2026-01-29', '10:00:00', 'Please coordinate with the office.', NULL, 1, 15, '2026-01-15 05:52:00', '2026-01-28 20:00:00', '2026-01-19 15:00:00', '2026-01-26 16:00:00', '2026-01-29 04:00:00', 1),
(861, 138, 'Request for Assistance / Support', 'Request for farm inputs', 'We would like to request your assistance regarding: request for farm inputs. Please let us know when we may visit the office.', NULL, NULL, '09170023757', 'maria.santos@example.com', NULL, NULL, NULL, NULL, 1, 'completed', '2026-01-26', '12:00:00', 'Please coordinate with the office.', NULL, 1, 19, '2026-01-15 08:02:00', '2026-01-25 20:00:00', '2026-01-19 10:00:00', '2026-01-21 13:00:00', '2026-01-26 04:00:00', 1),
(862, 140, 'National Government Agency Coordination', 'Coordination with a national agency program', 'We would like to request your assistance regarding: coordination with a national agency program. Please let us know when we may visit the office.', NULL, NULL, '09170039595', 'ana.delacruz@example.com', NULL, NULL, NULL, NULL, NULL, 'rejected', NULL, NULL, NULL, 'Outside the scope of the municipality.', 1, NULL, '2026-01-15 08:34:00', '2026-01-16 05:00:00', '2026-01-16 13:00:00', NULL, NULL, 1),
(863, 138, 'Request for Assistance / Support', 'Request for farm inputs', 'We would like to request your assistance regarding: request for farm inputs. Please let us know when we may visit the office.', NULL, NULL, '09170023757', 'maria.santos@example.com', NULL, NULL, NULL, NULL, 15, 'completed', '2026-01-30', '12:00:00', 'Please coordinate with the office.', NULL, 1, 22, '2026-01-16 08:37:00', '2026-01-29 20:00:00', '2026-01-19 12:00:00', '2026-01-26 14:00:00', '2026-01-30 04:00:00', 1),
(864, 143, 'Business Proposal / Partnership', 'Partnership with the municipal market', 'We would like to request your assistance regarding: partnership with the municipal market. Please let us know when we may visit the office.', NULL, NULL, '09170063352', 'ramon.villanueva@example.com', NULL, NULL, NULL, NULL, 9, 'completed', '2026-01-26', '13:00:00', 'Please coordinate with the office.', NULL, 1, 14, '2026-01-19 06:31:00', '2026-01-25 20:00:00', '2026-01-19 13:00:00', '2026-01-23 14:00:00', '2026-01-26 04:00:00', 1),
(865, 143, 'Community Concern / Complaint', 'Broken streetlights', 'We would like to request your assistance regarding: broken streetlights. Please let us know when we may visit the office.', NULL, NULL, '09170063352', 'ramon.villanueva@example.com', NULL, NULL, NULL, NULL, 22, 'completed', '2026-01-29', '13:00:00', 'Please coordinate with the office.', NULL, 1, 21, '2026-01-19 08:47:00', '2026-01-28 20:00:00', '2026-01-20 11:00:00', '2026-01-27 11:00:00', '2026-01-29 04:00:00', 1),
(866, 145, 'Community Concern / Complaint', 'Noise complaint', 'We would like to request your assistance regarding: noise complaint. Please let us know when we may visit the office.', NULL, NULL, '09170079190', 'eduardo.cruz@example.com', NULL, NULL, NULL, NULL, 21, 'completed', '2026-01-29', '12:00:00', 'Please coordinate with the office.', NULL, 1, 15, '2026-01-20 00:30:00', '2026-01-28 20:00:00', '2026-01-22 14:00:00', '2026-01-26 17:00:00', '2026-01-29 04:00:00', 1),
(867, 143, 'Inter-LGU Coordination', 'Joint clean-up drive with a neighboring town', 'We would like to request your assistance regarding: joint clean-up drive with a neighboring town. Please let us know when we may visit the office.', NULL, NULL, '09170063352', 'ramon.villanueva@example.com', NULL, NULL, NULL, NULL, 5, 'completed', '2026-01-26', '15:00:00', 'Please coordinate with the office.', NULL, 1, 24, '2026-01-21 02:48:00', '2026-01-25 20:00:00', '2026-01-22 14:00:00', '2026-01-23 17:00:00', '2026-01-26 04:00:00', 1),
(868, 148, 'Community Concern / Complaint', 'Flooded road near the barangay hall', 'We would like to request your assistance regarding: flooded road near the barangay hall. Please let us know when we may visit the office.', NULL, NULL, '09170102947', 'marites.domingo@example.com', NULL, NULL, NULL, NULL, 22, 'completed', '2026-01-29', '09:00:00', 'Please coordinate with the office.', NULL, 1, 21, '2026-01-21 07:24:00', '2026-01-28 20:00:00', '2026-01-22 15:00:00', '2026-01-27 17:00:00', '2026-01-29 04:00:00', 1),
(869, 143, 'National Government Agency Coordination', 'Coordination for a national vaccination drive', 'We would like to request your assistance regarding: coordination for a national vaccination drive. Please let us know when we may visit the office.', NULL, NULL, '09170063352', 'ramon.villanueva@example.com', NULL, NULL, NULL, NULL, 11, 'completed', '2026-01-29', '09:00:00', 'Please coordinate with the office.', NULL, 1, 12, '2026-01-22 06:45:00', '2026-01-28 20:00:00', '2026-01-26 09:00:00', '2026-01-28 12:00:00', '2026-01-29 04:00:00', 1),
(870, 140, 'Request for Assistance / Support', 'Scholarship support request', 'We would like to request your assistance regarding: scholarship support request. Please let us know when we may visit the office.', NULL, NULL, '09170039595', 'ana.delacruz@example.com', NULL, NULL, NULL, NULL, NULL, 'rejected', NULL, NULL, NULL, 'Outside the scope of the municipality.', 1, NULL, '2026-01-23 03:28:00', '2026-01-27 04:00:00', '2026-01-27 12:00:00', NULL, NULL, 1),
(871, 143, 'Request for Assistance / Support', 'Scholarship support request', 'We would like to request your assistance regarding: scholarship support request. Please let us know when we may visit the office.', NULL, NULL, '09170063352', 'ramon.villanueva@example.com', NULL, NULL, NULL, NULL, 14, 'completed', '2026-02-05', '12:00:00', 'Please coordinate with the office.', NULL, 1, 25, '2026-01-23 03:49:00', '2026-02-04 20:00:00', '2026-01-26 16:00:00', '2026-02-02 17:00:00', '2026-02-05 04:00:00', 1),
(872, 149, 'Business Proposal / Partnership', 'Partnership with the municipal market', 'We would like to request your assistance regarding: partnership with the municipal market. Please let us know when we may visit the office.', NULL, NULL, '09170110866', 'felipe.castillo@example.com', NULL, NULL, NULL, NULL, 9, 'completed', '2026-02-06', '11:00:00', 'Please coordinate with the office.', NULL, 1, 14, '2026-01-27 02:19:00', '2026-02-05 20:00:00', '2026-01-27 11:00:00', '2026-02-02 11:00:00', '2026-02-06 04:00:00', 1),
(873, 138, 'Others', 'Request for a venue', 'We would like to request your assistance regarding: request for a venue. Please let us know when we may visit the office.', NULL, NULL, '09170023757', 'maria.santos@example.com', NULL, NULL, NULL, NULL, 1, 'completed', '2026-02-04', '11:00:00', 'Please coordinate with the office.', NULL, 1, 19, '2026-01-27 08:11:00', '2026-02-03 20:00:00', '2026-01-29 11:00:00', '2026-02-02 11:00:00', '2026-02-04 04:00:00', 1),
(874, 141, 'National Government Agency Coordination', 'Coordination with a national agency program', 'We would like to request your assistance regarding: coordination with a national agency program. Please let us know when we may visit the office.', NULL, NULL, '09170047514', 'pedro.bautista@example.com', NULL, NULL, NULL, NULL, 15, 'completed', '2026-02-09', '12:00:00', 'Please coordinate with the office.', NULL, 1, 22, '2026-01-29 08:41:00', '2026-02-08 20:00:00', '2026-02-02 09:00:00', '2026-02-05 12:00:00', '2026-02-09 04:00:00', 1),
(875, 142, 'Request for Assistance / Support', 'Scholarship support request', 'We would like to request your assistance regarding: scholarship support request. Please let us know when we may visit the office.', NULL, NULL, '09170055433', 'carmela.garcia@example.com', NULL, NULL, NULL, NULL, 15, 'completed', '2026-02-09', '11:00:00', 'Please coordinate with the office.', NULL, 1, 22, '2026-01-30 01:38:00', '2026-02-08 20:00:00', '2026-02-02 10:00:00', '2026-02-06 13:00:00', '2026-02-09 04:00:00', 1),
(876, 145, 'Request for Assistance / Support', 'Scholarship support request', 'We would like to request your assistance regarding: scholarship support request. Please let us know when we may visit the office.', NULL, NULL, '09170079190', 'eduardo.cruz@example.com', NULL, NULL, NULL, NULL, 15, 'completed', '2026-02-10', '11:00:00', 'Please coordinate with the office.', NULL, 1, 22, '2026-01-30 06:04:00', '2026-02-09 20:00:00', '2026-02-03 12:00:00', '2026-02-09 12:00:00', '2026-02-10 04:00:00', 1),
(877, 145, 'Request for Assistance / Support', 'Scholarship support request', 'We would like to request your assistance regarding: scholarship support request. Please let us know when we may visit the office.', NULL, NULL, '09170079190', 'eduardo.cruz@example.com', NULL, NULL, NULL, NULL, 16, 'completed', '2026-02-13', '15:00:00', 'Please coordinate with the office.', NULL, 1, 11, '2026-02-02 02:17:00', '2026-02-12 20:00:00', '2026-02-06 13:00:00', '2026-02-11 13:00:00', '2026-02-13 04:00:00', 1),
(878, 139, 'Others', 'Request for a certified document', 'We would like to request your assistance regarding: request for a certified document. Please let us know when we may visit the office.', NULL, NULL, '09170031676', 'jose.reyes@example.com', NULL, NULL, NULL, NULL, 10, 'completed', '2026-02-09', '11:00:00', 'Please coordinate with the office.', NULL, 1, 37, '2026-02-02 04:08:00', '2026-02-08 20:00:00', '2026-02-03 14:00:00', '2026-02-04 15:00:00', '2026-02-09 04:00:00', 1),
(879, 151, 'Project Proposal', 'Youth sports program proposal', 'We would like to request your assistance regarding: youth sports program proposal. Please let us know when we may visit the office.', NULL, NULL, '09170126704', 'antonio.pascual@example.com', NULL, NULL, NULL, NULL, 5, 'completed', '2026-02-10', '11:00:00', 'Please coordinate with the office.', NULL, 1, 24, '2026-02-02 08:41:00', '2026-02-09 20:00:00', '2026-02-04 12:00:00', '2026-02-06 14:00:00', '2026-02-10 04:00:00', 1),
(880, 143, 'National Government Agency Coordination', 'Coordination for a national vaccination drive', 'We would like to request your assistance regarding: coordination for a national vaccination drive. Please let us know when we may visit the office.', NULL, NULL, '09170063352', 'ramon.villanueva@example.com', NULL, NULL, NULL, NULL, 5, 'completed', '2026-02-20', '11:00:00', 'Please coordinate with the office.', NULL, 1, 24, '2026-02-03 03:37:00', '2026-02-19 20:00:00', '2026-02-09 11:00:00', '2026-02-16 11:00:00', '2026-02-20 04:00:00', 1),
(881, 146, 'Inter-LGU Coordination', 'Joint clean-up drive with a neighboring town', 'We would like to request your assistance regarding: joint clean-up drive with a neighboring town. Please let us know when we may visit the office.', NULL, NULL, '09170087109', 'teresita.aquino@example.com', NULL, NULL, NULL, NULL, 1, 'completed', '2026-02-16', '12:00:00', 'Please coordinate with the office.', NULL, 1, 19, '2026-02-03 06:27:00', '2026-02-15 20:00:00', '2026-02-06 09:00:00', '2026-02-13 12:00:00', '2026-02-16 04:00:00', 1),
(882, 139, 'Inter-LGU Coordination', 'Shared disaster-response drill', 'We would like to request your assistance regarding: shared disaster-response drill. Please let us know when we may visit the office.', NULL, NULL, '09170031676', 'jose.reyes@example.com', NULL, NULL, NULL, NULL, 1, 'completed', '2026-02-11', '15:00:00', 'Please coordinate with the office.', NULL, 1, 19, '2026-02-04 02:35:00', '2026-02-10 20:00:00', '2026-02-05 12:00:00', '2026-02-09 12:00:00', '2026-02-11 04:00:00', 1),
(883, 141, 'Request for Assistance / Support', 'Request for farm inputs', 'We would like to request your assistance regarding: request for farm inputs. Please let us know when we may visit the office.', NULL, NULL, '09170047514', 'pedro.bautista@example.com', NULL, NULL, NULL, NULL, 1, 'completed', '2026-02-11', '14:00:00', 'Please coordinate with the office.', NULL, 1, 19, '2026-02-05 03:06:00', '2026-02-10 20:00:00', '2026-02-06 10:00:00', '2026-02-09 10:00:00', '2026-02-11 04:00:00', 1),
(884, 143, 'Request for Assistance / Support', 'Scholarship support request', 'We would like to request your assistance regarding: scholarship support request. Please let us know when we may visit the office.', NULL, NULL, '09170063352', 'ramon.villanueva@example.com', NULL, NULL, NULL, NULL, 1, 'completed', '2026-02-10', '14:00:00', 'Please coordinate with the office.', NULL, 1, 19, '2026-02-05 03:39:00', '2026-02-09 20:00:00', '2026-02-06 13:00:00', '2026-02-09 14:00:00', '2026-02-10 04:00:00', 1),
(885, 141, 'Request for Assistance / Support', 'Medical assistance for a family member', 'We would like to request your assistance regarding: medical assistance for a family member. Please let us know when we may visit the office.', NULL, NULL, '09170047514', 'pedro.bautista@example.com', NULL, NULL, NULL, NULL, 16, 'completed', '2026-02-17', '14:00:00', 'Please coordinate with the office.', NULL, 1, 11, '2026-02-06 06:06:00', '2026-02-16 20:00:00', '2026-02-10 11:00:00', '2026-02-13 11:00:00', '2026-02-17 04:00:00', 1),
(886, 151, 'National Government Agency Coordination', 'Coordination with a national agency program', 'We would like to request your assistance regarding: coordination with a national agency program. Please let us know when we may visit the office.', NULL, NULL, '09170126704', 'antonio.pascual@example.com', NULL, NULL, NULL, NULL, 15, 'completed', '2026-02-13', '14:00:00', 'Please coordinate with the office.', NULL, 1, 22, '2026-02-06 07:58:00', '2026-02-12 20:00:00', '2026-02-06 16:00:00', '2026-02-11 16:00:00', '2026-02-13 04:00:00', 1),
(887, 149, 'Project Proposal', 'Livelihood training proposal', 'We would like to request your assistance regarding: livelihood training proposal. Please let us know when we may visit the office.', NULL, NULL, '09170110866', 'felipe.castillo@example.com', NULL, NULL, NULL, NULL, 16, 'completed', '2026-02-16', '11:00:00', 'Please coordinate with the office.', NULL, 1, 11, '2026-02-09 00:12:00', '2026-02-15 20:00:00', '2026-02-10 10:00:00', '2026-02-12 11:00:00', '2026-02-16 04:00:00', 1),
(888, 138, 'National Government Agency Coordination', 'Coordination with a national agency program', 'We would like to request your assistance regarding: coordination with a national agency program. Please let us know when we may visit the office.', NULL, NULL, '09170023757', 'maria.santos@example.com', NULL, NULL, NULL, NULL, 5, 'completed', '2026-02-16', '13:00:00', 'Please coordinate with the office.', NULL, 1, 24, '2026-02-09 01:07:00', '2026-02-15 20:00:00', '2026-02-11 12:00:00', '2026-02-13 14:00:00', '2026-02-16 04:00:00', 1),
(889, 144, 'Courtesy Call / Meeting Request', 'Meeting request with the Mayor', 'We would like to request your assistance regarding: meeting request with the mayor. Please let us know when we may visit the office.', NULL, NULL, '09170071271', 'liza.mendoza@example.com', NULL, NULL, NULL, NULL, 1, 'completed', '2026-02-23', '15:00:00', 'Please coordinate with the office.', NULL, 1, 19, '2026-02-12 08:11:00', '2026-02-22 20:00:00', '2026-02-16 13:00:00', '2026-02-17 13:00:00', '2026-02-23 04:00:00', 1),
(890, 142, 'Inter-LGU Coordination', 'Shared disaster-response drill', 'We would like to request your assistance regarding: shared disaster-response drill. Please let us know when we may visit the office.', NULL, NULL, '09170055433', 'carmela.garcia@example.com', NULL, NULL, NULL, NULL, 5, 'completed', '2026-02-24', '11:00:00', 'Please coordinate with the office.', NULL, 1, 24, '2026-02-13 00:54:00', '2026-02-23 20:00:00', '2026-02-16 13:00:00', '2026-02-20 14:00:00', '2026-02-24 04:00:00', 1),
(891, 141, 'Business Proposal / Partnership', 'Sponsorship for the town fiesta', 'We would like to request your assistance regarding: sponsorship for the town fiesta. Please let us know when we may visit the office.', NULL, NULL, '09170047514', 'pedro.bautista@example.com', NULL, NULL, NULL, NULL, 23, 'completed', '2026-02-23', '14:00:00', 'Please coordinate with the office.', NULL, 1, 36, '2026-02-13 07:13:00', '2026-02-22 20:00:00', '2026-02-16 13:00:00', '2026-02-18 16:00:00', '2026-02-23 04:00:00', 1),
(892, 140, 'Courtesy Call / Meeting Request', 'Courtesy call from a civic group', 'We would like to request your assistance regarding: courtesy call from a civic group. Please let us know when we may visit the office.', NULL, NULL, '09170039595', 'ana.delacruz@example.com', NULL, NULL, NULL, NULL, 1, 'completed', '2026-03-02', '09:00:00', 'Please coordinate with the office.', NULL, 1, 19, '2026-02-16 01:21:00', '2026-03-01 20:00:00', '2026-02-20 09:00:00', '2026-02-25 12:00:00', '2026-03-02 04:00:00', 1),
(893, 142, 'Project Proposal', 'Proposed barangay drainage project', 'We would like to request your assistance regarding: proposed barangay drainage project. Please let us know when we may visit the office.', NULL, NULL, '09170055433', 'carmela.garcia@example.com', NULL, NULL, NULL, NULL, 21, 'completed', '2026-02-19', '15:00:00', 'Please coordinate with the office.', NULL, 1, 15, '2026-02-16 03:11:00', '2026-02-18 20:00:00', '2026-02-16 11:00:00', '2026-02-18 12:00:00', '2026-02-19 04:00:00', 1),
(894, 150, 'Request for Assistance / Support', 'Burial assistance request', 'We would like to request your assistance regarding: burial assistance request. Please let us know when we may visit the office.', NULL, NULL, '09170118785', 'josefina.ramos@example.com', NULL, NULL, NULL, NULL, 15, 'completed', '2026-02-23', '11:00:00', 'Please coordinate with the office.', NULL, 1, 22, '2026-02-16 07:16:00', '2026-02-22 20:00:00', '2026-02-16 14:00:00', '2026-02-19 14:00:00', '2026-02-23 04:00:00', 1),
(895, 140, 'Community Concern / Complaint', 'Noise complaint', 'We would like to request your assistance regarding: noise complaint. Please let us know when we may visit the office.', NULL, NULL, '09170039595', 'ana.delacruz@example.com', NULL, NULL, NULL, NULL, 21, 'scheduled', '2026-03-02', '09:00:00', 'Please coordinate with the office.', NULL, 1, 15, '2026-02-17 01:40:00', '2026-02-26 11:00:00', '2026-02-19 16:00:00', '2026-02-26 19:00:00', NULL, 1),
(896, 140, 'Request for Assistance / Support', 'Scholarship support request', 'We would like to request your assistance regarding: scholarship support request. Please let us know when we may visit the office.', NULL, NULL, '09170039595', 'ana.delacruz@example.com', NULL, NULL, NULL, NULL, 1, 'completed', '2026-02-26', '10:00:00', 'Please coordinate with the office.', NULL, 1, 19, '2026-02-18 01:49:00', '2026-02-25 20:00:00', '2026-02-20 13:00:00', '2026-02-24 13:00:00', '2026-02-26 04:00:00', 1),
(897, 150, 'Project Proposal', 'Livelihood training proposal', 'We would like to request your assistance regarding: livelihood training proposal. Please let us know when we may visit the office.', NULL, NULL, '09170118785', 'josefina.ramos@example.com', NULL, NULL, NULL, NULL, 16, 'completed', '2026-02-26', '10:00:00', 'Please coordinate with the office.', NULL, 1, 11, '2026-02-18 04:01:00', '2026-02-25 20:00:00', '2026-02-19 13:00:00', '2026-02-23 15:00:00', '2026-02-26 04:00:00', 1),
(898, 139, 'Courtesy Call / Meeting Request', 'Courtesy call from a civic group', 'We would like to request your assistance regarding: courtesy call from a civic group. Please let us know when we may visit the office.', NULL, NULL, '09170031676', 'jose.reyes@example.com', NULL, NULL, NULL, NULL, NULL, 'rejected', NULL, NULL, NULL, 'Outside the scope of the municipality.', 1, NULL, '2026-02-19 01:09:00', '2026-02-19 04:00:00', '2026-02-19 12:00:00', NULL, NULL, 1),
(899, 146, 'Request for Assistance / Support', 'Scholarship support request', 'We would like to request your assistance regarding: scholarship support request. Please let us know when we may visit the office.', NULL, NULL, '09170087109', 'teresita.aquino@example.com', NULL, NULL, NULL, NULL, 1, 'completed', '2026-02-24', '09:00:00', 'Please coordinate with the office.', NULL, 1, 19, '2026-02-19 03:38:00', '2026-02-23 20:00:00', '2026-02-20 14:00:00', '2026-02-23 15:00:00', '2026-02-24 04:00:00', 1),
(900, 141, 'Others', 'General inquiry', 'We would like to request your assistance regarding: general inquiry. Please let us know when we may visit the office.', NULL, NULL, '09170047514', 'pedro.bautista@example.com', NULL, NULL, NULL, NULL, 3, 'completed', '2026-03-03', '15:00:00', 'Please coordinate with the office.', NULL, 1, 17, '2026-02-20 04:18:00', '2026-03-02 20:00:00', '2026-02-23 09:00:00', '2026-03-02 12:00:00', '2026-03-03 04:00:00', 1),
(901, 148, 'Courtesy Call / Meeting Request', 'Meeting request with the Mayor', 'We would like to request your assistance regarding: meeting request with the mayor. Please let us know when we may visit the office.', NULL, NULL, '09170102947', 'marites.domingo@example.com', NULL, NULL, NULL, NULL, 1, 'completed', '2026-02-26', '10:00:00', 'Please coordinate with the office.', NULL, 1, 19, '2026-02-20 07:12:00', '2026-02-25 20:00:00', '2026-02-20 16:00:00', '2026-02-23 19:00:00', '2026-02-26 04:00:00', 1),
(902, 144, 'Request for Assistance / Support', 'Request for farm inputs', 'We would like to request your assistance regarding: request for farm inputs. Please let us know when we may visit the office.', NULL, NULL, '09170071271', 'liza.mendoza@example.com', NULL, NULL, NULL, NULL, 16, 'completed', '2026-03-03', '12:00:00', 'Please coordinate with the office.', NULL, 1, 11, '2026-02-23 01:46:00', '2026-03-02 20:00:00', '2026-02-27 10:00:00', '2026-03-02 13:00:00', '2026-03-03 04:00:00', 1),
(903, 143, 'Others', 'Request for a venue', 'We would like to request your assistance regarding: request for a venue. Please let us know when we may visit the office.', NULL, NULL, '09170063352', 'ramon.villanueva@example.com', NULL, NULL, NULL, NULL, NULL, 'cancelled', NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-23 07:39:00', '2026-02-23 07:39:00', NULL, NULL, NULL, 1),
(904, 140, 'Community Concern / Complaint', 'Illegal dumping complaint', 'We would like to request your assistance regarding: illegal dumping complaint. Please let us know when we may visit the office.', NULL, NULL, '09170039595', 'ana.delacruz@example.com', NULL, NULL, NULL, NULL, 21, 'completed', '2026-03-06', '10:00:00', 'Please coordinate with the office.', NULL, 1, 15, '2026-02-25 01:12:00', '2026-03-05 20:00:00', '2026-02-26 14:00:00', '2026-03-02 16:00:00', '2026-03-06 04:00:00', 1),
(905, 144, 'Request for Assistance / Support', 'Medical assistance for a family member', 'We would like to request your assistance regarding: medical assistance for a family member. Please let us know when we may visit the office.', NULL, NULL, '09170071271', 'liza.mendoza@example.com', NULL, NULL, NULL, NULL, 14, 'completed', '2026-03-09', '14:00:00', 'Please coordinate with the office.', NULL, 1, 25, '2026-02-26 01:40:00', '2026-03-08 20:00:00', '2026-03-02 14:00:00', '2026-03-05 14:00:00', '2026-03-09 04:00:00', 1);
INSERT INTO `appointments` (`id`, `user_id`, `category`, `title`, `description`, `attachment_path`, `attachment_original_name`, `contact_number`, `email`, `organization`, `position_title`, `preferred_date`, `preferred_time`, `department_id`, `status`, `scheduled_date`, `scheduled_time`, `admin_notes`, `rejection_reason`, `reviewed_by`, `scheduled_by`, `created_at`, `updated_at`, `reviewed_at`, `scheduled_at`, `completed_at`, `is_demo`) VALUES
(906, 147, 'Business Proposal / Partnership', 'Proposal to open a small business', 'We would like to request your assistance regarding: proposal to open a small business. Please let us know when we may visit the office.', NULL, NULL, '09170095028', 'rogelio.navarro@example.com', NULL, NULL, NULL, NULL, 5, 'completed', '2026-03-09', '11:00:00', 'Please coordinate with the office.', NULL, 1, 24, '2026-02-26 04:12:00', '2026-03-08 20:00:00', '2026-03-02 12:00:00', '2026-03-05 14:00:00', '2026-03-09 04:00:00', 1),
(907, 150, 'Project Proposal', 'Livelihood training proposal', 'We would like to request your assistance regarding: livelihood training proposal. Please let us know when we may visit the office.', NULL, NULL, '09170118785', 'josefina.ramos@example.com', NULL, NULL, NULL, NULL, 21, 'completed', '2026-03-12', '11:00:00', 'Please coordinate with the office.', NULL, 1, 15, '2026-02-26 04:37:00', '2026-03-11 20:00:00', '2026-03-02 09:00:00', '2026-03-09 11:00:00', '2026-03-12 04:00:00', 1),
(908, 143, 'Request for Assistance / Support', 'Medical assistance for a family member', 'We would like to request your assistance regarding: medical assistance for a family member. Please let us know when we may visit the office.', NULL, NULL, '09170063352', 'ramon.villanueva@example.com', NULL, NULL, NULL, NULL, 15, 'completed', '2026-03-04', '09:00:00', 'Please coordinate with the office.', NULL, 1, 22, '2026-02-27 02:19:00', '2026-03-03 20:00:00', '2026-02-27 14:00:00', '2026-03-02 15:00:00', '2026-03-04 04:00:00', 1),
(909, 142, 'Request for Assistance / Support', 'Burial assistance request', 'We would like to request your assistance regarding: burial assistance request. Please let us know when we may visit the office.', NULL, NULL, '09170055433', 'carmela.garcia@example.com', NULL, NULL, NULL, NULL, 14, 'completed', '2026-03-03', '12:00:00', 'Please coordinate with the office.', NULL, 1, 25, '2026-02-27 06:53:00', '2026-03-02 20:00:00', '2026-02-27 16:00:00', '2026-03-02 16:00:00', '2026-03-03 04:00:00', 1),
(910, 146, 'Courtesy Call / Meeting Request', 'Courtesy call from a civic group', 'We would like to request your assistance regarding: courtesy call from a civic group. Please let us know when we may visit the office.', NULL, NULL, '09170087109', 'teresita.aquino@example.com', NULL, NULL, NULL, NULL, NULL, 'rejected', NULL, NULL, NULL, 'Outside the scope of the municipality.', 1, NULL, '2026-03-02 08:35:00', '2026-03-04 03:00:00', '2026-03-04 11:00:00', NULL, NULL, 1),
(911, 140, 'Courtesy Call / Meeting Request', 'Courtesy call from a civic group', 'We would like to request your assistance regarding: courtesy call from a civic group. Please let us know when we may visit the office.', NULL, NULL, '09170039595', 'ana.delacruz@example.com', NULL, NULL, NULL, NULL, NULL, 'rejected', NULL, NULL, NULL, 'Outside the scope of the municipality.', 1, NULL, '2026-03-02 08:47:00', '2026-03-06 01:00:00', '2026-03-06 09:00:00', NULL, NULL, 1),
(912, 144, 'Request for Assistance / Support', 'Request for farm inputs', 'We would like to request your assistance regarding: request for farm inputs. Please let us know when we may visit the office.', NULL, NULL, '09170071271', 'liza.mendoza@example.com', NULL, NULL, NULL, NULL, NULL, 'cancelled', NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-03 02:38:00', '2026-03-03 02:38:00', NULL, NULL, NULL, 1),
(913, 147, 'Project Proposal', 'Proposed barangay drainage project', 'We would like to request your assistance regarding: proposed barangay drainage project. Please let us know when we may visit the office.', NULL, NULL, '09170095028', 'rogelio.navarro@example.com', NULL, NULL, NULL, NULL, NULL, 'cancelled', NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-06 05:39:00', '2026-03-06 05:39:00', NULL, NULL, NULL, 1),
(914, 147, 'Community Concern / Complaint', 'Flooded road near the barangay hall', 'We would like to request your assistance regarding: flooded road near the barangay hall. Please let us know when we may visit the office.', NULL, NULL, '09170095028', 'rogelio.navarro@example.com', NULL, NULL, NULL, NULL, 22, 'completed', '2026-03-17', '10:00:00', 'Please coordinate with the office.', NULL, 1, 21, '2026-03-09 03:17:00', '2026-03-16 20:00:00', '2026-03-13 11:00:00', '2026-03-16 12:00:00', '2026-03-17 04:00:00', 1),
(915, 146, 'Courtesy Call / Meeting Request', 'Meeting request with the Mayor', 'We would like to request your assistance regarding: meeting request with the mayor. Please let us know when we may visit the office.', NULL, NULL, '09170087109', 'teresita.aquino@example.com', NULL, NULL, NULL, NULL, NULL, 'rejected', NULL, NULL, NULL, 'Outside the scope of the municipality.', 1, NULL, '2026-03-09 05:20:00', '2026-03-10 02:00:00', '2026-03-10 10:00:00', NULL, NULL, 1),
(916, 146, 'Request for Assistance / Support', 'Medical assistance for a family member', 'We would like to request your assistance regarding: medical assistance for a family member. Please let us know when we may visit the office.', NULL, NULL, '09170087109', 'teresita.aquino@example.com', NULL, NULL, NULL, NULL, 15, 'completed', '2026-03-16', '15:00:00', 'Please coordinate with the office.', NULL, 1, 22, '2026-03-10 02:33:00', '2026-03-15 20:00:00', '2026-03-11 15:00:00', '2026-03-12 18:00:00', '2026-03-16 04:00:00', 1),
(917, 141, 'Courtesy Call / Meeting Request', 'Meeting request with the Mayor', 'We would like to request your assistance regarding: meeting request with the mayor. Please let us know when we may visit the office.', NULL, NULL, '09170047514', 'pedro.bautista@example.com', NULL, NULL, NULL, NULL, NULL, 'rejected', NULL, NULL, NULL, 'Outside the scope of the municipality.', 1, NULL, '2026-03-11 01:24:00', '2026-03-11 02:00:00', '2026-03-11 10:00:00', NULL, NULL, 1),
(918, 149, 'Business Proposal / Partnership', 'Partnership with the municipal market', 'We would like to request your assistance regarding: partnership with the municipal market. Please let us know when we may visit the office.', NULL, NULL, '09170110866', 'felipe.castillo@example.com', NULL, NULL, NULL, NULL, 23, 'completed', '2026-03-27', '11:00:00', 'Please coordinate with the office.', NULL, 1, 36, '2026-03-16 06:26:00', '2026-03-26 20:00:00', '2026-03-20 16:00:00', '2026-03-23 19:00:00', '2026-03-27 04:00:00', 1),
(919, 147, 'Business Proposal / Partnership', 'Proposal to open a small business', 'We would like to request your assistance regarding: proposal to open a small business. Please let us know when we may visit the office.', NULL, NULL, '09170095028', 'rogelio.navarro@example.com', NULL, NULL, NULL, NULL, 5, 'completed', '2026-03-23', '10:00:00', 'Please coordinate with the office.', NULL, 1, 24, '2026-03-17 02:47:00', '2026-03-22 20:00:00', '2026-03-18 09:00:00', '2026-03-20 11:00:00', '2026-03-23 04:00:00', 1),
(920, 147, 'Community Concern / Complaint', 'Noise complaint', 'We would like to request your assistance regarding: noise complaint. Please let us know when we may visit the office.', NULL, NULL, '09170095028', 'rogelio.navarro@example.com', NULL, NULL, NULL, NULL, 1, 'completed', '2026-03-24', '09:00:00', 'Please coordinate with the office.', NULL, 1, 19, '2026-03-19 02:49:00', '2026-03-23 20:00:00', '2026-03-20 12:00:00', '2026-03-23 15:00:00', '2026-03-24 04:00:00', 1),
(921, 138, 'National Government Agency Coordination', 'Coordination with a national agency program', 'We would like to request your assistance regarding: coordination with a national agency program. Please let us know when we may visit the office.', NULL, NULL, '09170023757', 'maria.santos@example.com', NULL, NULL, NULL, NULL, 5, 'completed', '2026-03-26', '10:00:00', 'Please coordinate with the office.', NULL, 1, 24, '2026-03-19 04:03:00', '2026-03-25 20:00:00', '2026-03-19 16:00:00', '2026-03-23 19:00:00', '2026-03-26 04:00:00', 1),
(922, 147, 'Request for Assistance / Support', 'Request for farm inputs', 'We would like to request your assistance regarding: request for farm inputs. Please let us know when we may visit the office.', NULL, NULL, '09170095028', 'rogelio.navarro@example.com', NULL, NULL, NULL, NULL, NULL, 'rejected', NULL, NULL, NULL, 'Outside the scope of the municipality.', 1, NULL, '2026-03-19 07:17:00', '2026-03-19 07:17:00', '2026-03-19 13:00:00', NULL, NULL, 1),
(923, 150, 'Request for Assistance / Support', 'Burial assistance request', 'We would like to request your assistance regarding: burial assistance request. Please let us know when we may visit the office.', NULL, NULL, '09170118785', 'josefina.ramos@example.com', NULL, NULL, NULL, NULL, 16, 'completed', '2026-03-30', '11:00:00', 'Please coordinate with the office.', NULL, 1, 11, '2026-03-20 04:50:00', '2026-03-29 20:00:00', '2026-03-23 09:00:00', '2026-03-26 09:00:00', '2026-03-30 04:00:00', 1),
(924, 151, 'Request for Assistance / Support', 'Burial assistance request', 'We would like to request your assistance regarding: burial assistance request. Please let us know when we may visit the office.', NULL, NULL, '09170126704', 'antonio.pascual@example.com', NULL, NULL, NULL, NULL, 14, 'completed', '2026-03-30', '10:00:00', 'Please coordinate with the office.', NULL, 1, 25, '2026-03-23 03:19:00', '2026-03-29 20:00:00', '2026-03-25 10:00:00', '2026-03-26 12:00:00', '2026-03-30 04:00:00', 1),
(925, 146, 'Request for Assistance / Support', 'Medical assistance for a family member', 'We would like to request your assistance regarding: medical assistance for a family member. Please let us know when we may visit the office.', NULL, NULL, '09170087109', 'teresita.aquino@example.com', NULL, NULL, NULL, NULL, NULL, 'cancelled', NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-23 05:08:00', '2026-03-23 05:08:00', NULL, NULL, NULL, 1),
(926, 150, 'National Government Agency Coordination', 'Coordination for a national vaccination drive', 'We would like to request your assistance regarding: coordination for a national vaccination drive. Please let us know when we may visit the office.', NULL, NULL, '09170118785', 'josefina.ramos@example.com', NULL, NULL, NULL, NULL, 5, 'completed', '2026-04-02', '12:00:00', 'Please coordinate with the office.', NULL, 1, 24, '2026-03-24 01:16:00', '2026-04-01 20:00:00', '2026-03-27 13:00:00', '2026-03-31 13:00:00', '2026-04-02 04:00:00', 1),
(927, 147, 'Business Proposal / Partnership', 'Sponsorship for the town fiesta', 'We would like to request your assistance regarding: sponsorship for the town fiesta. Please let us know when we may visit the office.', NULL, NULL, '09170095028', 'rogelio.navarro@example.com', NULL, NULL, NULL, NULL, 9, 'completed', '2026-04-01', '12:00:00', 'Please coordinate with the office.', NULL, 1, 14, '2026-03-24 07:56:00', '2026-03-31 20:00:00', '2026-03-27 11:00:00', '2026-03-30 11:00:00', '2026-04-01 04:00:00', 1),
(928, 143, 'Business Proposal / Partnership', 'Sponsorship for the town fiesta', 'We would like to request your assistance regarding: sponsorship for the town fiesta. Please let us know when we may visit the office.', NULL, NULL, '09170063352', 'ramon.villanueva@example.com', NULL, NULL, NULL, NULL, 10, 'completed', '2026-04-07', '11:00:00', 'Please coordinate with the office.', NULL, 1, 37, '2026-03-27 01:16:00', '2026-04-06 20:00:00', '2026-03-30 13:00:00', '2026-04-03 13:00:00', '2026-04-07 04:00:00', 1),
(929, 150, 'Inter-LGU Coordination', 'Shared disaster-response drill', 'We would like to request your assistance regarding: shared disaster-response drill. Please let us know when we may visit the office.', NULL, NULL, '09170118785', 'josefina.ramos@example.com', NULL, NULL, NULL, NULL, 5, 'completed', '2026-04-02', '14:00:00', 'Please coordinate with the office.', NULL, 1, 24, '2026-03-27 04:04:00', '2026-04-01 20:00:00', '2026-03-27 14:00:00', '2026-03-31 15:00:00', '2026-04-02 04:00:00', 1),
(930, 138, 'Request for Assistance / Support', 'Request for farm inputs', 'We would like to request your assistance regarding: request for farm inputs. Please let us know when we may visit the office.', NULL, NULL, '09170023757', 'maria.santos@example.com', NULL, NULL, NULL, NULL, NULL, 'rejected', NULL, NULL, NULL, 'Outside the scope of the municipality.', 1, NULL, '2026-03-30 02:46:00', '2026-04-02 07:00:00', '2026-04-02 15:00:00', NULL, NULL, 1),
(931, 141, 'Request for Assistance / Support', 'Burial assistance request', 'We would like to request your assistance regarding: burial assistance request. Please let us know when we may visit the office.', NULL, NULL, '09170047514', 'pedro.bautista@example.com', NULL, NULL, NULL, NULL, 14, 'completed', '2026-04-13', '13:00:00', 'Please coordinate with the office.', NULL, 1, 25, '2026-04-01 02:42:00', '2026-04-12 20:00:00', '2026-04-06 14:00:00', '2026-04-08 14:00:00', '2026-04-13 04:00:00', 1),
(932, 148, 'Courtesy Call / Meeting Request', 'Courtesy call from a civic group', 'We would like to request your assistance regarding: courtesy call from a civic group. Please let us know when we may visit the office.', NULL, NULL, '09170102947', 'marites.domingo@example.com', NULL, NULL, NULL, NULL, 1, 'completed', '2026-04-16', '09:00:00', 'Please coordinate with the office.', NULL, 1, 19, '2026-04-03 01:24:00', '2026-04-15 20:00:00', '2026-04-07 15:00:00', '2026-04-13 17:00:00', '2026-04-16 04:00:00', 1),
(933, 140, 'Inter-LGU Coordination', 'Joint clean-up drive with a neighboring town', 'We would like to request your assistance regarding: joint clean-up drive with a neighboring town. Please let us know when we may visit the office.', NULL, NULL, '09170039595', 'ana.delacruz@example.com', NULL, NULL, NULL, NULL, NULL, 'cancelled', NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-03 03:44:00', '2026-04-03 03:44:00', NULL, NULL, NULL, 1),
(934, 151, 'Request for Assistance / Support', 'Scholarship support request', 'We would like to request your assistance regarding: scholarship support request. Please let us know when we may visit the office.', NULL, NULL, '09170126704', 'antonio.pascual@example.com', NULL, NULL, NULL, NULL, 16, 'completed', '2026-04-13', '13:00:00', 'Please coordinate with the office.', NULL, 1, 11, '2026-04-03 07:19:00', '2026-04-12 20:00:00', '2026-04-06 09:00:00', '2026-04-09 12:00:00', '2026-04-13 04:00:00', 1),
(935, 148, 'National Government Agency Coordination', 'Coordination for a national vaccination drive', 'We would like to request your assistance regarding: coordination for a national vaccination drive. Please let us know when we may visit the office.', NULL, NULL, '09170102947', 'marites.domingo@example.com', NULL, NULL, NULL, NULL, 15, 'completed', '2026-04-10', '11:00:00', 'Please coordinate with the office.', NULL, 1, 22, '2026-04-07 04:55:00', '2026-04-09 20:00:00', '2026-04-08 09:00:00', '2026-04-09 12:00:00', '2026-04-10 04:00:00', 1),
(936, 148, 'Inter-LGU Coordination', 'Shared disaster-response drill', 'We would like to request your assistance regarding: shared disaster-response drill. Please let us know when we may visit the office.', NULL, NULL, '09170102947', 'marites.domingo@example.com', NULL, NULL, NULL, NULL, 5, 'scheduled', '2026-04-20', '10:00:00', 'Please coordinate with the office.', NULL, 1, 24, '2026-04-10 07:28:00', '2026-04-15 09:00:00', '2026-04-13 15:00:00', '2026-04-15 17:00:00', NULL, 1),
(937, 150, 'Community Concern / Complaint', 'Illegal dumping complaint', 'We would like to request your assistance regarding: illegal dumping complaint. Please let us know when we may visit the office.', NULL, NULL, '09170118785', 'josefina.ramos@example.com', NULL, NULL, NULL, NULL, 1, 'completed', '2026-04-23', '15:00:00', 'Please coordinate with the office.', NULL, 1, 19, '2026-04-14 03:42:00', '2026-04-22 20:00:00', '2026-04-16 10:00:00', '2026-04-20 11:00:00', '2026-04-23 04:00:00', 1),
(938, 146, 'Request for Assistance / Support', 'Scholarship support request', 'We would like to request your assistance regarding: scholarship support request. Please let us know when we may visit the office.', NULL, NULL, '09170087109', 'teresita.aquino@example.com', NULL, NULL, NULL, NULL, 15, 'completed', '2026-04-21', '10:00:00', 'Please coordinate with the office.', NULL, 1, 22, '2026-04-14 04:25:00', '2026-04-20 20:00:00', '2026-04-15 09:00:00', '2026-04-17 12:00:00', '2026-04-21 04:00:00', 1),
(939, 147, 'Community Concern / Complaint', 'Noise complaint', 'We would like to request your assistance regarding: noise complaint. Please let us know when we may visit the office.', NULL, NULL, '09170095028', 'rogelio.navarro@example.com', NULL, NULL, NULL, NULL, 1, 'completed', '2026-04-28', '10:00:00', 'Please coordinate with the office.', NULL, 1, 19, '2026-04-16 06:57:00', '2026-04-27 20:00:00', '2026-04-20 12:00:00', '2026-04-24 14:00:00', '2026-04-28 04:00:00', 1),
(940, 140, 'Request for Assistance / Support', 'Scholarship support request', 'We would like to request your assistance regarding: scholarship support request. Please let us know when we may visit the office.', NULL, NULL, '09170039595', 'ana.delacruz@example.com', NULL, NULL, NULL, NULL, 14, 'completed', '2026-04-27', '10:00:00', 'Please coordinate with the office.', NULL, 1, 25, '2026-04-20 03:06:00', '2026-04-26 20:00:00', '2026-04-21 16:00:00', '2026-04-23 17:00:00', '2026-04-27 04:00:00', 1),
(941, 151, 'Project Proposal', 'Proposed barangay drainage project', 'We would like to request your assistance regarding: proposed barangay drainage project. Please let us know when we may visit the office.', NULL, NULL, '09170126704', 'antonio.pascual@example.com', NULL, NULL, NULL, NULL, 16, 'scheduled', '2026-04-23', '14:00:00', 'Please coordinate with the office.', NULL, 1, 11, '2026-04-20 08:45:00', '2026-04-22 06:00:00', '2026-04-21 12:00:00', '2026-04-22 14:00:00', NULL, 1),
(942, 149, 'Courtesy Call / Meeting Request', 'Courtesy call from a civic group', 'We would like to request your assistance regarding: courtesy call from a civic group. Please let us know when we may visit the office.', NULL, NULL, '09170110866', 'felipe.castillo@example.com', NULL, NULL, NULL, NULL, 1, 'completed', '2026-04-28', '10:00:00', 'Please coordinate with the office.', NULL, 1, 19, '2026-04-21 06:21:00', '2026-04-27 20:00:00', '2026-04-23 16:00:00', '2026-04-27 16:00:00', '2026-04-28 04:00:00', 1),
(943, 143, 'Business Proposal / Partnership', 'Proposal to open a small business', 'We would like to request your assistance regarding: proposal to open a small business. Please let us know when we may visit the office.', NULL, NULL, '09170063352', 'ramon.villanueva@example.com', NULL, NULL, NULL, NULL, 23, 'completed', '2026-04-27', '10:00:00', 'Please coordinate with the office.', NULL, 1, 36, '2026-04-22 00:43:00', '2026-04-26 20:00:00', '2026-04-23 11:00:00', '2026-04-24 12:00:00', '2026-04-27 04:00:00', 1),
(944, 140, 'Request for Assistance / Support', 'Medical assistance for a family member', 'We would like to request your assistance regarding: medical assistance for a family member. Please let us know when we may visit the office.', NULL, NULL, '09170039595', 'ana.delacruz@example.com', NULL, NULL, NULL, NULL, 16, 'completed', '2026-05-04', '12:00:00', 'Please coordinate with the office.', NULL, 1, 11, '2026-04-22 02:04:00', '2026-05-03 20:00:00', '2026-04-27 11:00:00', '2026-04-29 14:00:00', '2026-05-04 04:00:00', 1),
(945, 142, 'Courtesy Call / Meeting Request', 'Courtesy call from a civic group', 'We would like to request your assistance regarding: courtesy call from a civic group. Please let us know when we may visit the office.', NULL, NULL, '09170055433', 'carmela.garcia@example.com', NULL, NULL, NULL, NULL, 23, 'completed', '2026-04-28', '12:00:00', 'Please coordinate with the office.', NULL, 1, 36, '2026-04-22 03:29:00', '2026-04-27 20:00:00', '2026-04-22 12:00:00', '2026-04-27 15:00:00', '2026-04-28 04:00:00', 1),
(946, 146, 'Courtesy Call / Meeting Request', 'Meeting request with the Mayor', 'We would like to request your assistance regarding: meeting request with the mayor. Please let us know when we may visit the office.', NULL, NULL, '09170087109', 'teresita.aquino@example.com', NULL, NULL, NULL, NULL, 1, 'scheduled', '2026-05-04', '15:00:00', 'Please coordinate with the office.', NULL, 1, 19, '2026-04-22 08:50:00', '2026-05-01 03:00:00', '2026-04-24 09:00:00', '2026-05-01 11:00:00', NULL, 1),
(947, 138, 'Courtesy Call / Meeting Request', 'Courtesy call from a civic group', 'We would like to request your assistance regarding: courtesy call from a civic group. Please let us know when we may visit the office.', NULL, NULL, '09170023757', 'maria.santos@example.com', NULL, NULL, NULL, NULL, 23, 'completed', '2026-05-04', '09:00:00', 'Please coordinate with the office.', NULL, 1, 36, '2026-04-24 07:31:00', '2026-05-03 20:00:00', '2026-04-27 13:00:00', '2026-05-01 14:00:00', '2026-05-04 04:00:00', 1),
(948, 140, 'Request for Assistance / Support', 'Burial assistance request', 'We would like to request your assistance regarding: burial assistance request. Please let us know when we may visit the office.', NULL, NULL, '09170039595', 'ana.delacruz@example.com', NULL, NULL, NULL, NULL, NULL, 'cancelled', NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-27 00:45:00', '2026-04-27 00:45:00', NULL, NULL, NULL, 1),
(949, 150, 'Request for Assistance / Support', 'Medical assistance for a family member', 'We would like to request your assistance regarding: medical assistance for a family member. Please let us know when we may visit the office.', NULL, NULL, '09170118785', 'josefina.ramos@example.com', NULL, NULL, NULL, NULL, 15, 'completed', '2026-05-11', '11:00:00', 'Please coordinate with the office.', NULL, 1, 22, '2026-04-27 02:03:00', '2026-05-10 20:00:00', '2026-05-01 16:00:00', '2026-05-05 17:00:00', '2026-05-11 04:00:00', 1),
(950, 151, 'Request for Assistance / Support', 'Burial assistance request', 'We would like to request your assistance regarding: burial assistance request. Please let us know when we may visit the office.', NULL, NULL, '09170126704', 'antonio.pascual@example.com', NULL, NULL, NULL, NULL, 14, 'completed', '2026-05-06', '09:00:00', 'Please coordinate with the office.', NULL, 1, 25, '2026-04-27 07:14:00', '2026-05-05 20:00:00', '2026-04-30 14:00:00', '2026-05-04 14:00:00', '2026-05-06 04:00:00', 1),
(951, 149, 'Community Concern / Complaint', 'Noise complaint', 'We would like to request your assistance regarding: noise complaint. Please let us know when we may visit the office.', NULL, NULL, '09170110866', 'felipe.castillo@example.com', NULL, NULL, NULL, NULL, 1, 'completed', '2026-05-06', '15:00:00', 'Please coordinate with the office.', NULL, 1, 19, '2026-04-28 00:54:00', '2026-05-05 20:00:00', '2026-04-30 10:00:00', '2026-05-05 12:00:00', '2026-05-06 04:00:00', 1),
(952, 151, 'Business Proposal / Partnership', 'Partnership with the municipal market', 'We would like to request your assistance regarding: partnership with the municipal market. Please let us know when we may visit the office.', NULL, NULL, '09170126704', 'antonio.pascual@example.com', NULL, NULL, NULL, NULL, 10, 'completed', '2026-05-08', '10:00:00', 'Please coordinate with the office.', NULL, 1, 37, '2026-04-28 01:05:00', '2026-05-07 20:00:00', '2026-04-30 13:00:00', '2026-05-04 13:00:00', '2026-05-08 04:00:00', 1),
(953, 144, 'Courtesy Call / Meeting Request', 'Meeting request with the Mayor', 'We would like to request your assistance regarding: meeting request with the mayor. Please let us know when we may visit the office.', NULL, NULL, '09170071271', 'liza.mendoza@example.com', NULL, NULL, NULL, NULL, 1, 'completed', '2026-05-07', '11:00:00', 'Please coordinate with the office.', NULL, 1, 19, '2026-04-28 05:14:00', '2026-05-06 20:00:00', '2026-04-29 16:00:00', '2026-05-04 17:00:00', '2026-05-07 04:00:00', 1),
(954, 144, 'Community Concern / Complaint', 'Broken streetlights', 'We would like to request your assistance regarding: broken streetlights. Please let us know when we may visit the office.', NULL, NULL, '09170071271', 'liza.mendoza@example.com', NULL, NULL, NULL, NULL, 22, 'completed', '2026-05-01', '10:00:00', 'Please coordinate with the office.', NULL, 1, 21, '2026-04-28 07:24:00', '2026-04-30 20:00:00', '2026-04-28 16:00:00', '2026-04-29 16:00:00', '2026-05-01 04:00:00', 1),
(955, 150, 'Inter-LGU Coordination', 'Joint clean-up drive with a neighboring town', 'We would like to request your assistance regarding: joint clean-up drive with a neighboring town. Please let us know when we may visit the office.', NULL, NULL, '09170118785', 'josefina.ramos@example.com', NULL, NULL, NULL, NULL, 5, 'completed', '2026-05-06', '14:00:00', 'Please coordinate with the office.', NULL, 1, 24, '2026-04-29 04:38:00', '2026-05-05 20:00:00', '2026-04-30 15:00:00', '2026-05-04 15:00:00', '2026-05-06 04:00:00', 1),
(956, 150, 'Business Proposal / Partnership', 'Sponsorship for the town fiesta', 'We would like to request your assistance regarding: sponsorship for the town fiesta. Please let us know when we may visit the office.', NULL, NULL, '09170118785', 'josefina.ramos@example.com', NULL, NULL, NULL, NULL, 9, 'completed', '2026-05-12', '09:00:00', 'Please coordinate with the office.', NULL, 1, 14, '2026-05-05 00:21:00', '2026-05-11 20:00:00', '2026-05-07 15:00:00', '2026-05-11 18:00:00', '2026-05-12 04:00:00', 1),
(957, 141, 'Inter-LGU Coordination', 'Shared disaster-response drill', 'We would like to request your assistance regarding: shared disaster-response drill. Please let us know when we may visit the office.', NULL, NULL, '09170047514', 'pedro.bautista@example.com', NULL, NULL, NULL, NULL, NULL, 'cancelled', NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-05 03:58:00', '2026-05-05 03:58:00', NULL, NULL, NULL, 1),
(958, 146, 'Community Concern / Complaint', 'Flooded road near the barangay hall', 'We would like to request your assistance regarding: flooded road near the barangay hall. Please let us know when we may visit the office.', NULL, NULL, '09170087109', 'teresita.aquino@example.com', NULL, NULL, NULL, NULL, 21, 'completed', '2026-05-18', '09:00:00', 'Please coordinate with the office.', NULL, 1, 15, '2026-05-05 08:22:00', '2026-05-17 20:00:00', '2026-05-11 12:00:00', '2026-05-15 12:00:00', '2026-05-18 04:00:00', 1),
(959, 148, 'Request for Assistance / Support', 'Medical assistance for a family member', 'We would like to request your assistance regarding: medical assistance for a family member. Please let us know when we may visit the office.', NULL, NULL, '09170102947', 'marites.domingo@example.com', NULL, NULL, NULL, NULL, NULL, 'rejected', NULL, NULL, NULL, 'Outside the scope of the municipality.', 1, NULL, '2026-05-07 07:06:00', '2026-05-11 08:00:00', '2026-05-11 16:00:00', NULL, NULL, 1),
(960, 144, 'Request for Assistance / Support', 'Request for farm inputs', 'We would like to request your assistance regarding: request for farm inputs. Please let us know when we may visit the office.', NULL, NULL, '09170071271', 'liza.mendoza@example.com', NULL, NULL, NULL, NULL, 16, 'completed', '2026-05-13', '15:00:00', 'Please coordinate with the office.', NULL, 1, 11, '2026-05-08 05:58:00', '2026-05-12 20:00:00', '2026-05-08 16:00:00', '2026-05-11 17:00:00', '2026-05-13 04:00:00', 1),
(961, 146, 'Business Proposal / Partnership', 'Proposal to open a small business', 'We would like to request your assistance regarding: proposal to open a small business. Please let us know when we may visit the office.', NULL, NULL, '09170087109', 'teresita.aquino@example.com', NULL, NULL, NULL, NULL, 9, 'completed', '2026-05-18', '11:00:00', 'Please coordinate with the office.', NULL, 1, 14, '2026-05-08 08:15:00', '2026-05-17 20:00:00', '2026-05-11 14:00:00', '2026-05-14 15:00:00', '2026-05-18 04:00:00', 1),
(962, 143, 'Business Proposal / Partnership', 'Partnership with the municipal market', 'We would like to request your assistance regarding: partnership with the municipal market. Please let us know when we may visit the office.', NULL, NULL, '09170063352', 'ramon.villanueva@example.com', NULL, NULL, NULL, NULL, 9, 'completed', '2026-05-22', '12:00:00', 'Please coordinate with the office.', NULL, 1, 14, '2026-05-12 07:07:00', '2026-05-21 20:00:00', '2026-05-12 15:00:00', '2026-05-19 18:00:00', '2026-05-22 04:00:00', 1),
(963, 142, 'Business Proposal / Partnership', 'Partnership with the municipal market', 'We would like to request your assistance regarding: partnership with the municipal market. Please let us know when we may visit the office.', NULL, NULL, '09170055433', 'carmela.garcia@example.com', NULL, NULL, NULL, NULL, 10, 'completed', '2026-05-25', '11:00:00', 'Please coordinate with the office.', NULL, 1, 37, '2026-05-13 06:39:00', '2026-05-24 20:00:00', '2026-05-13 15:00:00', '2026-05-20 17:00:00', '2026-05-25 04:00:00', 1),
(964, 140, 'Request for Assistance / Support', 'Medical assistance for a family member', 'We would like to request your assistance regarding: medical assistance for a family member. Please let us know when we may visit the office.', NULL, NULL, '09170039595', 'ana.delacruz@example.com', NULL, NULL, NULL, NULL, 14, 'completed', '2026-05-20', '14:00:00', 'Please coordinate with the office.', NULL, 1, 25, '2026-05-15 05:39:00', '2026-05-19 20:00:00', '2026-05-15 10:00:00', '2026-05-18 11:00:00', '2026-05-20 04:00:00', 1),
(965, 151, 'Courtesy Call / Meeting Request', 'Meeting request with the Mayor', 'We would like to request your assistance regarding: meeting request with the mayor. Please let us know when we may visit the office.', NULL, NULL, '09170126704', 'antonio.pascual@example.com', NULL, NULL, NULL, NULL, 23, 'completed', '2026-05-26', '11:00:00', 'Please coordinate with the office.', NULL, 1, 36, '2026-05-19 07:00:00', '2026-05-25 20:00:00', '2026-05-21 09:00:00', '2026-05-22 12:00:00', '2026-05-26 04:00:00', 1),
(966, 138, 'Business Proposal / Partnership', 'Sponsorship for the town fiesta', 'We would like to request your assistance regarding: sponsorship for the town fiesta. Please let us know when we may visit the office.', NULL, NULL, '09170023757', 'maria.santos@example.com', NULL, NULL, NULL, NULL, 10, 'completed', '2026-06-01', '13:00:00', 'Please coordinate with the office.', NULL, 1, 37, '2026-05-21 01:27:00', '2026-05-31 20:00:00', '2026-05-25 15:00:00', '2026-05-29 15:00:00', '2026-06-01 04:00:00', 1),
(967, 150, 'Request for Assistance / Support', 'Burial assistance request', 'We would like to request your assistance regarding: burial assistance request. Please let us know when we may visit the office.', NULL, NULL, '09170118785', 'josefina.ramos@example.com', NULL, NULL, NULL, NULL, 1, 'completed', '2026-06-01', '09:00:00', 'Please coordinate with the office.', NULL, 1, 19, '2026-05-21 05:55:00', '2026-05-31 20:00:00', '2026-05-22 12:00:00', '2026-05-27 12:00:00', '2026-06-01 04:00:00', 1),
(968, 151, 'Community Concern / Complaint', 'Broken streetlights', 'We would like to request your assistance regarding: broken streetlights. Please let us know when we may visit the office.', NULL, NULL, '09170126704', 'antonio.pascual@example.com', NULL, NULL, NULL, NULL, 22, 'completed', '2026-06-05', '09:00:00', 'Please coordinate with the office.', NULL, 1, 21, '2026-05-21 07:11:00', '2026-06-04 20:00:00', '2026-05-25 15:00:00', '2026-06-01 17:00:00', '2026-06-05 04:00:00', 1),
(969, 138, 'Request for Assistance / Support', 'Request for farm inputs', 'We would like to request your assistance regarding: request for farm inputs. Please let us know when we may visit the office.', NULL, NULL, '09170023757', 'maria.santos@example.com', NULL, NULL, NULL, NULL, NULL, 'cancelled', NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-26 00:02:00', '2026-05-26 00:02:00', NULL, NULL, NULL, 1),
(970, 148, 'Community Concern / Complaint', 'Broken streetlights', 'We would like to request your assistance regarding: broken streetlights. Please let us know when we may visit the office.', NULL, NULL, '09170102947', 'marites.domingo@example.com', NULL, NULL, NULL, NULL, 21, 'completed', '2026-06-04', '15:00:00', 'Please coordinate with the office.', NULL, 1, 15, '2026-05-26 00:27:00', '2026-06-03 20:00:00', '2026-05-28 09:00:00', '2026-06-01 12:00:00', '2026-06-04 04:00:00', 1),
(971, 148, 'Business Proposal / Partnership', 'Proposal to open a small business', 'We would like to request your assistance regarding: proposal to open a small business. Please let us know when we may visit the office.', NULL, NULL, '09170102947', 'marites.domingo@example.com', NULL, NULL, NULL, NULL, NULL, 'cancelled', NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-27 03:55:00', '2026-05-27 03:55:00', NULL, NULL, NULL, 1),
(972, 147, 'Request for Assistance / Support', 'Request for farm inputs', 'We would like to request your assistance regarding: request for farm inputs. Please let us know when we may visit the office.', NULL, NULL, '09170095028', 'rogelio.navarro@example.com', NULL, NULL, NULL, NULL, 15, 'completed', '2026-06-08', '11:00:00', 'Please coordinate with the office.', NULL, 1, 22, '2026-05-28 05:15:00', '2026-06-07 20:00:00', '2026-06-01 14:00:00', '2026-06-03 14:00:00', '2026-06-08 04:00:00', 1),
(973, 141, 'Community Concern / Complaint', 'Noise complaint', 'We would like to request your assistance regarding: noise complaint. Please let us know when we may visit the office.', NULL, NULL, '09170047514', 'pedro.bautista@example.com', NULL, NULL, NULL, NULL, 22, 'completed', '2026-06-03', '14:00:00', 'Please coordinate with the office.', NULL, 1, 21, '2026-05-29 01:01:00', '2026-06-02 20:00:00', '2026-05-29 09:00:00', '2026-06-01 09:00:00', '2026-06-03 04:00:00', 1),
(974, 140, 'Courtesy Call / Meeting Request', 'Meeting request with the Mayor', 'We would like to request your assistance regarding: meeting request with the mayor. Please let us know when we may visit the office.', NULL, NULL, '09170039595', 'ana.delacruz@example.com', NULL, NULL, NULL, NULL, 1, 'completed', '2026-06-04', '10:00:00', 'Please coordinate with the office.', NULL, 1, 19, '2026-05-29 01:28:00', '2026-06-03 20:00:00', '2026-05-29 11:00:00', '2026-06-01 12:00:00', '2026-06-04 04:00:00', 1),
(975, 140, 'National Government Agency Coordination', 'Coordination for a national vaccination drive', 'We would like to request your assistance regarding: coordination for a national vaccination drive. Please let us know when we may visit the office.', NULL, NULL, '09170039595', 'ana.delacruz@example.com', NULL, NULL, NULL, NULL, 5, 'completed', '2026-06-11', '13:00:00', 'Please coordinate with the office.', NULL, 1, 24, '2026-05-29 03:15:00', '2026-06-10 20:00:00', '2026-06-01 15:00:00', '2026-06-08 16:00:00', '2026-06-11 04:00:00', 1),
(976, 147, 'Courtesy Call / Meeting Request', 'Meeting request with the Mayor', 'We would like to request your assistance regarding: meeting request with the mayor. Please let us know when we may visit the office.', NULL, NULL, '09170095028', 'rogelio.navarro@example.com', NULL, NULL, NULL, NULL, 23, 'completed', '2026-06-11', '11:00:00', 'Please coordinate with the office.', NULL, 1, 36, '2026-06-01 00:12:00', '2026-06-10 20:00:00', '2026-06-04 16:00:00', '2026-06-08 18:00:00', '2026-06-11 04:00:00', 1),
(977, 145, 'Community Concern / Complaint', 'Broken streetlights', 'We would like to request your assistance regarding: broken streetlights. Please let us know when we may visit the office.', NULL, NULL, '09170079190', 'eduardo.cruz@example.com', NULL, NULL, NULL, NULL, 21, 'completed', '2026-06-05', '13:00:00', 'Please coordinate with the office.', NULL, 1, 15, '2026-06-01 06:11:00', '2026-06-04 20:00:00', '2026-06-02 13:00:00', '2026-06-03 16:00:00', '2026-06-05 04:00:00', 1),
(978, 138, 'Request for Assistance / Support', 'Medical assistance for a family member', 'We would like to request your assistance regarding: medical assistance for a family member. Please let us know when we may visit the office.', NULL, NULL, '09170023757', 'maria.santos@example.com', NULL, NULL, NULL, NULL, NULL, 'rejected', NULL, NULL, NULL, 'Outside the scope of the municipality.', 1, NULL, '2026-06-02 04:24:00', '2026-06-08 07:00:00', '2026-06-08 15:00:00', NULL, NULL, 1),
(979, 138, 'Inter-LGU Coordination', 'Shared disaster-response drill', 'We would like to request your assistance regarding: shared disaster-response drill. Please let us know when we may visit the office.', NULL, NULL, '09170023757', 'maria.santos@example.com', NULL, NULL, NULL, NULL, 5, 'completed', '2026-06-15', '15:00:00', 'Please coordinate with the office.', NULL, 1, 24, '2026-06-02 05:16:00', '2026-06-14 20:00:00', '2026-06-04 13:00:00', '2026-06-11 16:00:00', '2026-06-15 04:00:00', 1),
(980, 145, 'Inter-LGU Coordination', 'Joint clean-up drive with a neighboring town', 'We would like to request your assistance regarding: joint clean-up drive with a neighboring town. Please let us know when we may visit the office.', NULL, NULL, '09170079190', 'eduardo.cruz@example.com', NULL, NULL, NULL, NULL, 5, 'completed', '2026-06-16', '15:00:00', 'Please coordinate with the office.', NULL, 1, 24, '2026-06-04 01:29:00', '2026-06-15 20:00:00', '2026-06-08 16:00:00', '2026-06-15 16:00:00', '2026-06-16 04:00:00', 1),
(981, 148, 'Courtesy Call / Meeting Request', 'Courtesy call from a civic group', 'We would like to request your assistance regarding: courtesy call from a civic group. Please let us know when we may visit the office.', NULL, NULL, '09170102947', 'marites.domingo@example.com', NULL, NULL, NULL, NULL, NULL, 'rejected', NULL, NULL, NULL, 'Outside the scope of the municipality.', 1, NULL, '2026-06-05 01:44:00', '2026-06-08 06:00:00', '2026-06-08 14:00:00', NULL, NULL, 1),
(982, 142, 'Others', 'Request for a venue', 'We would like to request your assistance regarding: request for a venue. Please let us know when we may visit the office.', NULL, NULL, '09170055433', 'carmela.garcia@example.com', NULL, NULL, NULL, NULL, 3, 'completed', '2026-06-18', '11:00:00', 'Please coordinate with the office.', NULL, 1, 17, '2026-06-11 05:41:00', '2026-06-17 20:00:00', '2026-06-15 13:00:00', '2026-06-16 14:00:00', '2026-06-18 04:00:00', 1),
(983, 150, 'Community Concern / Complaint', 'Noise complaint', 'We would like to request your assistance regarding: noise complaint. Please let us know when we may visit the office.', NULL, NULL, '09170118785', 'josefina.ramos@example.com', NULL, NULL, NULL, NULL, 21, 'completed', '2026-06-19', '10:00:00', 'Please coordinate with the office.', NULL, 1, 15, '2026-06-12 06:18:00', '2026-06-18 20:00:00', '2026-06-15 13:00:00', '2026-06-17 15:00:00', '2026-06-19 04:00:00', 1),
(984, 142, 'Project Proposal', 'Youth sports program proposal', 'We would like to request your assistance regarding: youth sports program proposal. Please let us know when we may visit the office.', NULL, NULL, '09170055433', 'carmela.garcia@example.com', NULL, NULL, NULL, NULL, 16, 'completed', '2026-06-18', '15:00:00', 'Please coordinate with the office.', NULL, 1, 11, '2026-06-12 07:19:00', '2026-06-17 20:00:00', '2026-06-15 16:00:00', '2026-06-16 19:00:00', '2026-06-18 04:00:00', 1),
(985, 143, 'Courtesy Call / Meeting Request', 'Meeting request with the Mayor', 'We would like to request your assistance regarding: meeting request with the mayor. Please let us know when we may visit the office.', NULL, NULL, '09170063352', 'ramon.villanueva@example.com', NULL, NULL, NULL, NULL, 1, 'completed', '2026-06-29', '11:00:00', 'Please coordinate with the office.', NULL, 1, 19, '2026-06-19 06:22:00', '2026-06-28 20:00:00', '2026-06-22 10:00:00', '2026-06-26 11:00:00', '2026-06-29 04:00:00', 1),
(986, 143, 'Courtesy Call / Meeting Request', 'Courtesy call from a civic group', 'We would like to request your assistance regarding: courtesy call from a civic group. Please let us know when we may visit the office.', NULL, NULL, '09170063352', 'ramon.villanueva@example.com', NULL, NULL, NULL, NULL, 23, 'completed', '2026-06-24', '15:00:00', 'Please coordinate with the office.', NULL, 1, 36, '2026-06-19 07:38:00', '2026-06-23 20:00:00', '2026-06-19 15:00:00', '2026-06-22 16:00:00', '2026-06-24 04:00:00', 1),
(987, 140, 'Community Concern / Complaint', 'Illegal dumping complaint', 'We would like to request your assistance regarding: illegal dumping complaint. Please let us know when we may visit the office.', NULL, NULL, '09170039595', 'ana.delacruz@example.com', NULL, NULL, NULL, NULL, 22, 'completed', '2026-07-02', '09:00:00', 'Please coordinate with the office.', NULL, 1, 21, '2026-06-23 07:02:00', '2026-07-01 20:00:00', '2026-06-25 09:00:00', '2026-06-30 10:00:00', '2026-07-02 04:00:00', 1),
(988, 146, 'Community Concern / Complaint', 'Noise complaint', 'We would like to request your assistance regarding: noise complaint. Please let us know when we may visit the office.', NULL, NULL, '09170087109', 'teresita.aquino@example.com', NULL, NULL, NULL, NULL, 1, 'completed', '2026-07-03', '12:00:00', 'Please coordinate with the office.', NULL, 1, 19, '2026-06-24 07:04:00', '2026-07-02 20:00:00', '2026-06-29 13:00:00', '2026-07-02 13:00:00', '2026-07-03 04:00:00', 1),
(989, 144, 'Others', 'General inquiry', 'We would like to request your assistance regarding: general inquiry. Please let us know when we may visit the office.', NULL, NULL, '09170071271', 'liza.mendoza@example.com', NULL, NULL, NULL, NULL, 10, 'scheduled', '2026-07-02', '15:00:00', 'Please coordinate with the office.', NULL, 1, 37, '2026-06-24 08:18:00', '2026-06-30 02:00:00', '2026-06-26 10:00:00', '2026-06-30 10:00:00', NULL, 1),
(990, 147, 'Request for Assistance / Support', 'Burial assistance request', 'We would like to request your assistance regarding: burial assistance request. Please let us know when we may visit the office.', NULL, NULL, '09170095028', 'rogelio.navarro@example.com', NULL, NULL, NULL, NULL, 1, 'completed', '2026-07-02', '13:00:00', 'Please coordinate with the office.', NULL, 1, 19, '2026-06-25 02:02:00', '2026-07-01 20:00:00', '2026-06-25 12:00:00', '2026-06-30 13:00:00', '2026-07-02 04:00:00', 1),
(991, 138, 'Courtesy Call / Meeting Request', 'Courtesy call from a civic group', 'We would like to request your assistance regarding: courtesy call from a civic group. Please let us know when we may visit the office.', NULL, NULL, '09170023757', 'maria.santos@example.com', NULL, NULL, NULL, NULL, 1, 'completed', '2026-07-06', '12:00:00', 'Please coordinate with the office.', NULL, 1, 19, '2026-06-25 05:52:00', '2026-07-05 20:00:00', '2026-06-29 12:00:00', '2026-07-03 12:00:00', '2026-07-06 04:00:00', 1),
(992, 151, 'Business Proposal / Partnership', 'Partnership with the municipal market', 'We would like to request your assistance regarding: partnership with the municipal market. Please let us know when we may visit the office.', NULL, NULL, '09170126704', 'antonio.pascual@example.com', NULL, NULL, NULL, NULL, 5, 'completed', '2026-07-03', '15:00:00', 'Please coordinate with the office.', NULL, 1, 24, '2026-06-26 00:17:00', '2026-07-02 20:00:00', '2026-06-29 12:00:00', '2026-07-02 14:00:00', '2026-07-03 04:00:00', 1),
(993, 151, 'Community Concern / Complaint', 'Noise complaint', 'We would like to request your assistance regarding: noise complaint. Please let us know when we may visit the office.', NULL, NULL, '09170126704', 'antonio.pascual@example.com', NULL, NULL, NULL, NULL, NULL, 'rejected', NULL, NULL, NULL, 'Outside the scope of the municipality.', 1, NULL, '2026-06-26 04:45:00', '2026-06-29 01:00:00', '2026-06-29 09:00:00', NULL, NULL, 1),
(994, 145, 'Community Concern / Complaint', 'Broken streetlights', 'We would like to request your assistance regarding: broken streetlights. Please let us know when we may visit the office.', NULL, NULL, '09170079190', 'eduardo.cruz@example.com', NULL, NULL, NULL, NULL, 21, 'completed', '2026-07-07', '14:00:00', 'Please coordinate with the office.', NULL, 1, 15, '2026-06-29 04:15:00', '2026-07-06 20:00:00', '2026-06-29 15:00:00', '2026-07-03 15:00:00', '2026-07-07 04:00:00', 1),
(995, 140, 'Request for Assistance / Support', 'Medical assistance for a family member', 'We would like to request your assistance regarding: medical assistance for a family member. Please let us know when we may visit the office.', NULL, NULL, '09170039595', 'ana.delacruz@example.com', NULL, NULL, NULL, NULL, NULL, 'rejected', NULL, NULL, NULL, 'Outside the scope of the municipality.', 1, NULL, '2026-06-29 04:31:00', '2026-06-30 08:00:00', '2026-06-30 16:00:00', NULL, NULL, 1),
(996, 148, 'Business Proposal / Partnership', 'Partnership with the municipal market', 'We would like to request your assistance regarding: partnership with the municipal market. Please let us know when we may visit the office.', NULL, NULL, '09170102947', 'marites.domingo@example.com', NULL, NULL, NULL, NULL, NULL, 'rejected', NULL, NULL, NULL, 'Outside the scope of the municipality.', 1, NULL, '2026-07-01 01:49:00', '2026-07-02 07:00:00', '2026-07-02 15:00:00', NULL, NULL, 1),
(997, 141, 'Request for Assistance / Support', 'Request for farm inputs', 'We would like to request your assistance regarding: request for farm inputs. Please let us know when we may visit the office.', NULL, NULL, '09170047514', 'pedro.bautista@example.com', NULL, NULL, NULL, NULL, NULL, 'cancelled', NULL, NULL, NULL, NULL, NULL, NULL, '2026-07-06 04:13:00', '2026-07-06 04:13:00', NULL, NULL, NULL, 1),
(998, 143, 'Courtesy Call / Meeting Request', 'Courtesy call from a civic group', 'We would like to request your assistance regarding: courtesy call from a civic group. Please let us know when we may visit the office.', NULL, NULL, '09170063352', 'ramon.villanueva@example.com', NULL, NULL, NULL, NULL, 23, 'completed', '2026-07-13', '13:00:00', 'Please coordinate with the office.', NULL, 1, 36, '2026-07-07 07:08:00', '2026-07-12 20:00:00', '2026-07-07 14:00:00', '2026-07-08 14:00:00', '2026-07-13 04:00:00', 1),
(999, 145, 'Courtesy Call / Meeting Request', 'Meeting request with the Mayor', 'We would like to request your assistance regarding: meeting request with the mayor. Please let us know when we may visit the office.', NULL, NULL, '09170079190', 'eduardo.cruz@example.com', NULL, NULL, NULL, NULL, 23, 'completed', '2026-07-20', '15:00:00', 'Please coordinate with the office.', NULL, 1, 36, '2026-07-09 05:14:00', '2026-07-19 20:00:00', '2026-07-13 15:00:00', '2026-07-16 16:00:00', '2026-07-20 04:00:00', 1),
(1000, 144, 'Inter-LGU Coordination', 'Joint clean-up drive with a neighboring town', 'We would like to request your assistance regarding: joint clean-up drive with a neighboring town. Please let us know when we may visit the office.', NULL, NULL, '09170071271', 'liza.mendoza@example.com', NULL, NULL, NULL, NULL, NULL, 'rejected', NULL, NULL, NULL, 'Outside the scope of the municipality.', 1, NULL, '2026-07-14 00:12:00', '2026-07-15 08:00:00', '2026-07-15 16:00:00', NULL, NULL, 1),
(1001, 150, 'Request for Assistance / Support', 'Medical assistance for a family member', 'We would like to request your assistance regarding: medical assistance for a family member. Please let us know when we may visit the office.', NULL, NULL, '09170118785', 'josefina.ramos@example.com', NULL, NULL, NULL, NULL, NULL, 'cancelled', NULL, NULL, NULL, NULL, NULL, NULL, '2026-07-14 06:33:00', '2026-07-14 06:33:00', NULL, NULL, NULL, 1),
(1002, 146, 'Community Concern / Complaint', 'Broken streetlights', 'We would like to request your assistance regarding: broken streetlights. Please let us know when we may visit the office.', NULL, NULL, '09170087109', 'teresita.aquino@example.com', NULL, NULL, NULL, NULL, 1, 'completed', '2026-07-24', '11:00:00', 'Please coordinate with the office.', NULL, 1, 19, '2026-07-20 02:48:00', '2026-07-23 20:00:00', '2026-07-21 16:00:00', '2026-07-22 17:00:00', '2026-07-24 04:00:00', 1),
(1003, 146, 'Courtesy Call / Meeting Request', 'Meeting request with the Mayor', 'We would like to request your assistance regarding: meeting request with the mayor. Please let us know when we may visit the office.', NULL, NULL, '09170087109', 'teresita.aquino@example.com', NULL, NULL, NULL, NULL, NULL, 'rejected', NULL, NULL, NULL, 'Outside the scope of the municipality.', 1, NULL, '2026-07-20 03:13:00', '2026-07-23 06:00:00', '2026-07-23 14:00:00', NULL, NULL, 1),
(1004, 141, 'Community Concern / Complaint', 'Broken streetlights', 'We would like to request your assistance regarding: broken streetlights. Please let us know when we may visit the office.', NULL, NULL, '09170047514', 'pedro.bautista@example.com', NULL, NULL, NULL, NULL, 1, 'completed', '2026-07-30', '13:00:00', 'Please coordinate with the office.', NULL, 1, 19, '2026-07-20 07:52:00', '2026-07-29 20:00:00', '2026-07-22 11:00:00', '2026-07-29 14:00:00', '2026-07-30 04:00:00', 1),
(1005, 150, 'National Government Agency Coordination', 'Coordination with a national agency program', 'We would like to request your assistance regarding: coordination with a national agency program. Please let us know when we may visit the office.', NULL, NULL, '09170118785', 'josefina.ramos@example.com', NULL, NULL, NULL, NULL, 5, 'completed', '2026-07-27', '09:00:00', 'Please coordinate with the office.', NULL, 1, 24, '2026-07-20 08:57:00', '2026-07-26 20:00:00', '2026-07-21 15:00:00', '2026-07-24 16:00:00', '2026-07-27 04:00:00', 1);
INSERT INTO `appointments` (`id`, `user_id`, `category`, `title`, `description`, `attachment_path`, `attachment_original_name`, `contact_number`, `email`, `organization`, `position_title`, `preferred_date`, `preferred_time`, `department_id`, `status`, `scheduled_date`, `scheduled_time`, `admin_notes`, `rejection_reason`, `reviewed_by`, `scheduled_by`, `created_at`, `updated_at`, `reviewed_at`, `scheduled_at`, `completed_at`, `is_demo`) VALUES
(1006, 149, 'National Government Agency Coordination', 'Coordination with a national agency program', 'We would like to request your assistance regarding: coordination with a national agency program. Please let us know when we may visit the office.', NULL, NULL, '09170110866', 'felipe.castillo@example.com', NULL, NULL, NULL, NULL, 15, 'completed', '2026-07-28', '11:00:00', 'Please coordinate with the office.', NULL, 1, 22, '2026-07-22 01:16:00', '2026-07-27 20:00:00', '2026-07-24 13:00:00', '2026-07-27 14:00:00', '2026-07-28 04:00:00', 1),
(1007, 145, 'Request for Assistance / Support', 'Scholarship support request', 'We would like to request your assistance regarding: scholarship support request. Please let us know when we may visit the office.', NULL, NULL, '09170079190', 'eduardo.cruz@example.com', NULL, NULL, NULL, NULL, 1, 'completed', '2026-07-30', '11:00:00', 'Please coordinate with the office.', NULL, 1, 19, '2026-07-23 02:21:00', '2026-07-29 20:00:00', '2026-07-27 11:00:00', '2026-07-29 14:00:00', '2026-07-30 04:00:00', 1),
(1008, 145, 'Request for Assistance / Support', 'Medical assistance for a family member', 'We would like to request your assistance regarding: medical assistance for a family member. Please let us know when we may visit the office.', NULL, NULL, '09170079190', 'eduardo.cruz@example.com', NULL, NULL, NULL, NULL, NULL, 'rejected', NULL, NULL, NULL, 'Outside the scope of the municipality.', 1, NULL, '2026-07-23 07:17:00', '2026-07-27 07:00:00', '2026-07-27 15:00:00', NULL, NULL, 1),
(1009, 142, 'Business Proposal / Partnership', 'Partnership with the municipal market', 'We would like to request your assistance regarding: partnership with the municipal market. Please let us know when we may visit the office.', NULL, NULL, '09170055433', 'carmela.garcia@example.com', NULL, NULL, NULL, NULL, 23, 'completed', '2026-08-05', '15:00:00', 'Please coordinate with the office.', NULL, 1, 36, '2026-07-27 08:36:00', '2026-08-04 20:00:00', '2026-07-29 13:00:00', '2026-08-03 14:00:00', '2026-08-05 04:00:00', 1),
(1010, 150, 'Business Proposal / Partnership', 'Partnership with the municipal market', 'We would like to request your assistance regarding: partnership with the municipal market. Please let us know when we may visit the office.', NULL, NULL, '09170118785', 'josefina.ramos@example.com', NULL, NULL, NULL, NULL, 10, 'completed', '2026-08-07', '09:00:00', 'Please coordinate with the office.', NULL, 1, 37, '2026-07-29 04:16:00', '2026-08-06 20:00:00', '2026-08-03 13:00:00', '2026-08-04 13:00:00', '2026-08-07 04:00:00', 1),
(1011, 149, 'National Government Agency Coordination', 'Coordination with a national agency program', 'We would like to request your assistance regarding: coordination with a national agency program. Please let us know when we may visit the office.', NULL, NULL, '09170110866', 'felipe.castillo@example.com', NULL, NULL, NULL, NULL, 11, 'completed', '2026-08-06', '09:00:00', 'Please coordinate with the office.', NULL, 1, 12, '2026-07-29 08:29:00', '2026-08-05 20:00:00', '2026-07-31 14:00:00', '2026-08-03 14:00:00', '2026-08-06 04:00:00', 1),
(1012, 139, 'Request for Assistance / Support', 'Medical assistance for a family member', 'We would like to request your assistance regarding: medical assistance for a family member. Please let us know when we may visit the office.', NULL, NULL, '09170031676', 'jose.reyes@example.com', NULL, NULL, NULL, NULL, 16, 'completed', '2026-08-10', '11:00:00', 'Please coordinate with the office.', NULL, 1, 11, '2026-07-30 04:24:00', '2026-08-09 20:00:00', '2026-08-03 09:00:00', '2026-08-05 11:00:00', '2026-08-10 04:00:00', 1),
(1013, 148, 'Project Proposal', 'Livelihood training proposal', 'We would like to request your assistance regarding: livelihood training proposal. Please let us know when we may visit the office.', NULL, NULL, '09170102947', 'marites.domingo@example.com', NULL, NULL, NULL, NULL, 16, 'completed', '2026-08-07', '11:00:00', 'Please coordinate with the office.', NULL, 1, 11, '2026-07-31 06:05:00', '2026-08-06 20:00:00', '2026-08-03 15:00:00', '2026-08-04 16:00:00', '2026-08-07 04:00:00', 1),
(1014, 149, 'Business Proposal / Partnership', 'Proposal to open a small business', 'We would like to request your assistance regarding: proposal to open a small business. Please let us know when we may visit the office.', NULL, NULL, '09170110866', 'felipe.castillo@example.com', NULL, NULL, NULL, NULL, 10, 'completed', '2026-08-17', '13:00:00', 'Please coordinate with the office.', NULL, 1, 37, '2026-08-03 06:42:00', '2026-08-16 20:00:00', '2026-08-06 10:00:00', '2026-08-13 11:00:00', '2026-08-17 04:00:00', 1),
(1015, 142, 'Request for Assistance / Support', 'Medical assistance for a family member', 'We would like to request your assistance regarding: medical assistance for a family member. Please let us know when we may visit the office.', NULL, NULL, '09170055433', 'carmela.garcia@example.com', NULL, NULL, NULL, NULL, NULL, 'cancelled', NULL, NULL, NULL, NULL, NULL, NULL, '2026-08-05 08:18:00', '2026-08-05 08:18:00', NULL, NULL, NULL, 1),
(1016, 139, 'Request for Assistance / Support', 'Burial assistance request', 'We would like to request your assistance regarding: burial assistance request. Please let us know when we may visit the office.', NULL, NULL, '09170031676', 'jose.reyes@example.com', NULL, NULL, NULL, NULL, 1, 'completed', '2026-08-13', '11:00:00', 'Please coordinate with the office.', NULL, 1, 19, '2026-08-06 01:50:00', '2026-08-12 20:00:00', '2026-08-07 10:00:00', '2026-08-10 12:00:00', '2026-08-13 04:00:00', 1),
(1017, 151, 'Request for Assistance / Support', 'Request for farm inputs', 'We would like to request your assistance regarding: request for farm inputs. Please let us know when we may visit the office.', NULL, NULL, '09170126704', 'antonio.pascual@example.com', NULL, NULL, NULL, NULL, 16, 'completed', '2026-08-24', '12:00:00', 'Please coordinate with the office.', NULL, 1, 11, '2026-08-10 01:14:00', '2026-08-23 20:00:00', '2026-08-12 10:00:00', '2026-08-19 10:00:00', '2026-08-24 04:00:00', 1),
(1018, 138, 'National Government Agency Coordination', 'Coordination with a national agency program', 'We would like to request your assistance regarding: coordination with a national agency program. Please let us know when we may visit the office.', NULL, NULL, '09170023757', 'maria.santos@example.com', NULL, NULL, NULL, NULL, 15, 'completed', '2026-08-21', '15:00:00', 'Please coordinate with the office.', NULL, 1, 22, '2026-08-10 04:52:00', '2026-08-20 20:00:00', '2026-08-14 15:00:00', '2026-08-17 15:00:00', '2026-08-21 04:00:00', 1),
(1019, 149, 'Request for Assistance / Support', 'Burial assistance request', 'We would like to request your assistance regarding: burial assistance request. Please let us know when we may visit the office.', NULL, NULL, '09170110866', 'felipe.castillo@example.com', NULL, NULL, NULL, NULL, 15, 'completed', '2026-08-25', '14:00:00', 'Please coordinate with the office.', NULL, 1, 22, '2026-08-12 03:39:00', '2026-08-24 20:00:00', '2026-08-17 15:00:00', '2026-08-24 16:00:00', '2026-08-25 04:00:00', 1),
(1020, 144, 'Project Proposal', 'Youth sports program proposal', 'We would like to request your assistance regarding: youth sports program proposal. Please let us know when we may visit the office.', NULL, NULL, '09170071271', 'liza.mendoza@example.com', NULL, NULL, NULL, NULL, 5, 'completed', '2026-08-24', '09:00:00', 'Please coordinate with the office.', NULL, 1, 24, '2026-08-13 01:12:00', '2026-08-23 20:00:00', '2026-08-17 09:00:00', '2026-08-20 10:00:00', '2026-08-24 04:00:00', 1),
(1021, 149, 'Request for Assistance / Support', 'Medical assistance for a family member', 'We would like to request your assistance regarding: medical assistance for a family member. Please let us know when we may visit the office.', NULL, NULL, '09170110866', 'felipe.castillo@example.com', NULL, NULL, NULL, NULL, 14, 'completed', '2026-08-26', '13:00:00', 'Please coordinate with the office.', NULL, 1, 25, '2026-08-18 02:34:00', '2026-08-25 20:00:00', '2026-08-20 10:00:00', '2026-08-24 12:00:00', '2026-08-26 04:00:00', 1),
(1022, 148, 'Request for Assistance / Support', 'Burial assistance request', 'We would like to request your assistance regarding: burial assistance request. Please let us know when we may visit the office.', NULL, NULL, '09170102947', 'marites.domingo@example.com', NULL, NULL, NULL, NULL, 15, 'completed', '2026-08-27', '09:00:00', 'Please coordinate with the office.', NULL, 1, 22, '2026-08-18 04:09:00', '2026-08-26 20:00:00', '2026-08-24 12:00:00', '2026-08-26 15:00:00', '2026-08-27 04:00:00', 1),
(1023, 151, 'Request for Assistance / Support', 'Burial assistance request', 'We would like to request your assistance regarding: burial assistance request. Please let us know when we may visit the office.', NULL, NULL, '09170126704', 'antonio.pascual@example.com', NULL, NULL, NULL, NULL, 15, 'completed', '2026-08-24', '12:00:00', 'Please coordinate with the office.', NULL, 1, 22, '2026-08-19 05:37:00', '2026-08-23 20:00:00', '2026-08-19 10:00:00', '2026-08-21 10:00:00', '2026-08-24 04:00:00', 1),
(1024, 151, 'Request for Assistance / Support', 'Scholarship support request', 'We would like to request your assistance regarding: scholarship support request. Please let us know when we may visit the office.', NULL, NULL, '09170126704', 'antonio.pascual@example.com', NULL, NULL, NULL, NULL, 1, 'completed', '2026-08-31', '11:00:00', 'Please coordinate with the office.', NULL, 1, 19, '2026-08-24 07:45:00', '2026-08-30 20:00:00', '2026-08-26 10:00:00', '2026-08-27 11:00:00', '2026-08-31 04:00:00', 1),
(1025, 143, 'Request for Assistance / Support', 'Request for farm inputs', 'We would like to request your assistance regarding: request for farm inputs. Please let us know when we may visit the office.', NULL, NULL, '09170063352', 'ramon.villanueva@example.com', NULL, NULL, NULL, NULL, 15, 'scheduled', '2026-09-03', '10:00:00', 'Please coordinate with the office.', NULL, 1, 22, '2026-08-25 04:09:00', '2026-08-31 06:00:00', '2026-08-26 13:00:00', '2026-08-31 14:00:00', NULL, 1),
(1026, 141, 'Business Proposal / Partnership', 'Proposal to open a small business', 'We would like to request your assistance regarding: proposal to open a small business. Please let us know when we may visit the office.', NULL, NULL, '09170047514', 'pedro.bautista@example.com', NULL, NULL, NULL, NULL, 9, 'completed', '2026-09-14', '14:00:00', 'Please coordinate with the office.', NULL, 1, 14, '2026-08-31 01:32:00', '2026-09-13 20:00:00', '2026-09-04 09:00:00', '2026-09-08 09:00:00', '2026-09-14 04:00:00', 1),
(1027, 151, 'Project Proposal', 'Proposed barangay drainage project', 'We would like to request your assistance regarding: proposed barangay drainage project. Please let us know when we may visit the office.', NULL, NULL, '09170126704', 'antonio.pascual@example.com', NULL, NULL, NULL, NULL, 5, 'completed', '2026-09-14', '15:00:00', 'Please coordinate with the office.', NULL, 1, 24, '2026-09-01 04:36:00', '2026-09-13 20:00:00', '2026-09-03 13:00:00', '2026-09-10 14:00:00', '2026-09-14 04:00:00', 1),
(1028, 145, 'National Government Agency Coordination', 'Coordination for a national vaccination drive', 'We would like to request your assistance regarding: coordination for a national vaccination drive. Please let us know when we may visit the office.', NULL, NULL, '09170079190', 'eduardo.cruz@example.com', NULL, NULL, NULL, NULL, 15, 'completed', '2026-09-04', '11:00:00', 'Please coordinate with the office.', NULL, 1, 22, '2026-09-01 06:36:00', '2026-09-03 20:00:00', '2026-09-02 12:00:00', '2026-09-03 12:00:00', '2026-09-04 04:00:00', 1),
(1029, 142, 'Community Concern / Complaint', 'Broken streetlights', 'We would like to request your assistance regarding: broken streetlights. Please let us know when we may visit the office.', NULL, NULL, '09170055433', 'carmela.garcia@example.com', NULL, NULL, NULL, NULL, 25, 'completed', '2026-09-11', '14:00:00', 'Please coordinate with the office.', NULL, 1, 20, '2026-09-02 02:40:00', '2026-09-10 20:00:00', '2026-09-07 15:00:00', '2026-09-08 16:00:00', '2026-09-11 04:00:00', 1),
(1030, 150, 'Business Proposal / Partnership', 'Proposal to open a small business', 'We would like to request your assistance regarding: proposal to open a small business. Please let us know when we may visit the office.', NULL, NULL, '09170118785', 'josefina.ramos@example.com', NULL, NULL, NULL, NULL, 10, 'completed', '2026-09-14', '15:00:00', 'Please coordinate with the office.', NULL, 1, 37, '2026-09-02 08:06:00', '2026-09-13 20:00:00', '2026-09-03 15:00:00', '2026-09-10 15:00:00', '2026-09-14 04:00:00', 1),
(1031, 139, 'Courtesy Call / Meeting Request', 'Meeting request with the Mayor', 'We would like to request your assistance regarding: meeting request with the mayor. Please let us know when we may visit the office.', NULL, NULL, '09170031676', 'jose.reyes@example.com', NULL, NULL, NULL, NULL, 1, 'completed', '2026-09-09', '10:00:00', 'Please coordinate with the office.', NULL, 1, 19, '2026-09-02 08:06:00', '2026-09-08 20:00:00', '2026-09-07 12:00:00', '2026-09-08 12:00:00', '2026-09-09 04:00:00', 1),
(1032, 148, 'Courtesy Call / Meeting Request', 'Meeting request with the Mayor', 'We would like to request your assistance regarding: meeting request with the mayor. Please let us know when we may visit the office.', NULL, NULL, '09170102947', 'marites.domingo@example.com', NULL, NULL, NULL, NULL, NULL, 'cancelled', NULL, NULL, NULL, NULL, NULL, NULL, '2026-09-08 04:53:00', '2026-09-08 04:53:00', NULL, NULL, NULL, 1),
(1033, 151, 'Community Concern / Complaint', 'Broken streetlights', 'We would like to request your assistance regarding: broken streetlights. Please let us know when we may visit the office.', NULL, NULL, '09170126704', 'antonio.pascual@example.com', NULL, NULL, NULL, NULL, 25, 'completed', '2026-09-21', '13:00:00', 'Please coordinate with the office.', NULL, 1, 20, '2026-09-09 03:29:00', '2026-09-20 20:00:00', '2026-09-14 12:00:00', '2026-09-18 14:00:00', '2026-09-21 04:00:00', 1),
(1034, 150, 'Request for Assistance / Support', 'Request for farm inputs', 'We would like to request your assistance regarding: request for farm inputs. Please let us know when we may visit the office.', NULL, NULL, '09170118785', 'josefina.ramos@example.com', NULL, NULL, NULL, NULL, NULL, 'rejected', NULL, NULL, NULL, 'Outside the scope of the municipality.', 1, NULL, '2026-09-10 04:57:00', '2026-09-14 04:00:00', '2026-09-14 12:00:00', NULL, NULL, 1),
(1035, 143, 'Project Proposal', 'Youth sports program proposal', 'We would like to request your assistance regarding: youth sports program proposal. Please let us know when we may visit the office.', NULL, NULL, '09170063352', 'ramon.villanueva@example.com', NULL, NULL, NULL, NULL, 21, 'completed', '2026-09-17', '10:00:00', 'Please coordinate with the office.', NULL, 1, 15, '2026-09-10 07:59:00', '2026-09-16 20:00:00', '2026-09-14 12:00:00', '2026-09-16 15:00:00', '2026-09-17 04:00:00', 1),
(1036, 139, 'Others', 'Request for a certified document', 'We would like to request your assistance regarding: request for a certified document. Please let us know when we may visit the office.', NULL, NULL, '09170031676', 'jose.reyes@example.com', NULL, NULL, NULL, NULL, 1, 'completed', '2026-09-22', '14:00:00', 'Please coordinate with the office.', NULL, 1, 19, '2026-09-14 06:15:00', '2026-09-21 20:00:00', '2026-09-17 09:00:00', '2026-09-21 12:00:00', '2026-09-22 04:00:00', 1),
(1037, 144, 'Project Proposal', 'Proposed barangay drainage project', 'We would like to request your assistance regarding: proposed barangay drainage project. Please let us know when we may visit the office.', NULL, NULL, '09170071271', 'liza.mendoza@example.com', NULL, NULL, NULL, NULL, 16, 'completed', '2026-09-22', '12:00:00', 'Please coordinate with the office.', NULL, 1, 11, '2026-09-15 07:23:00', '2026-09-21 20:00:00', '2026-09-16 14:00:00', '2026-09-21 14:00:00', '2026-09-22 04:00:00', 1),
(1038, 139, 'Request for Assistance / Support', 'Medical assistance for a family member', 'We would like to request your assistance regarding: medical assistance for a family member. Please let us know when we may visit the office.', NULL, NULL, '09170031676', 'jose.reyes@example.com', NULL, NULL, NULL, NULL, NULL, 'rejected', NULL, NULL, NULL, 'Outside the scope of the municipality.', 1, NULL, '2026-09-17 05:18:00', '2026-09-21 08:00:00', '2026-09-21 16:00:00', NULL, NULL, 1),
(1039, 151, 'Community Concern / Complaint', 'Flooded road near the barangay hall', 'We would like to request your assistance regarding: flooded road near the barangay hall. Please let us know when we may visit the office.', NULL, NULL, '09170126704', 'antonio.pascual@example.com', NULL, NULL, NULL, NULL, 22, 'completed', '2026-09-28', '12:00:00', 'Please coordinate with the office.', NULL, 1, 21, '2026-09-21 01:29:00', '2026-09-27 20:00:00', '2026-09-22 11:00:00', '2026-09-25 11:00:00', '2026-09-28 04:00:00', 1),
(1040, 145, 'Business Proposal / Partnership', 'Partnership with the municipal market', 'We would like to request your assistance regarding: partnership with the municipal market. Please let us know when we may visit the office.', NULL, NULL, '09170079190', 'eduardo.cruz@example.com', NULL, NULL, NULL, NULL, NULL, 'cancelled', NULL, NULL, NULL, NULL, NULL, NULL, '2026-09-21 04:05:00', '2026-09-21 04:05:00', NULL, NULL, NULL, 1),
(1041, 149, 'Business Proposal / Partnership', 'Partnership with the municipal market', 'We would like to request your assistance regarding: partnership with the municipal market. Please let us know when we may visit the office.', NULL, NULL, '09170110866', 'felipe.castillo@example.com', NULL, NULL, NULL, NULL, 5, 'completed', '2026-09-29', '15:00:00', 'Please coordinate with the office.', NULL, 1, 24, '2026-09-21 04:06:00', '2026-09-28 20:00:00', '2026-09-23 13:00:00', '2026-09-28 14:00:00', '2026-09-29 04:00:00', 1),
(1042, 147, 'Request for Assistance / Support', 'Scholarship support request', 'We would like to request your assistance regarding: scholarship support request. Please let us know when we may visit the office.', NULL, NULL, '09170095028', 'rogelio.navarro@example.com', NULL, NULL, NULL, NULL, 1, 'completed', '2026-10-06', '14:00:00', 'Please coordinate with the office.', NULL, 1, 19, '2026-09-23 03:04:00', '2026-10-05 20:00:00', '2026-09-25 13:00:00', '2026-10-02 14:00:00', '2026-10-06 04:00:00', 1),
(1043, 145, 'Courtesy Call / Meeting Request', 'Courtesy call from a civic group', 'We would like to request your assistance regarding: courtesy call from a civic group. Please let us know when we may visit the office.', NULL, NULL, '09170079190', 'eduardo.cruz@example.com', NULL, NULL, NULL, NULL, 1, 'completed', '2026-10-06', '15:00:00', 'Please coordinate with the office.', NULL, 1, 19, '2026-09-28 02:21:00', '2026-10-05 20:00:00', '2026-10-01 16:00:00', '2026-10-02 16:00:00', '2026-10-06 04:00:00', 1),
(1044, 142, 'Request for Assistance / Support', 'Request for farm inputs', 'We would like to request your assistance regarding: request for farm inputs. Please let us know when we may visit the office.', NULL, NULL, '09170055433', 'carmela.garcia@example.com', NULL, NULL, NULL, NULL, 15, 'completed', '2026-10-09', '13:00:00', 'Please coordinate with the office.', NULL, 1, 22, '2026-09-28 06:14:00', '2026-10-08 20:00:00', '2026-09-30 13:00:00', '2026-10-05 13:00:00', '2026-10-09 04:00:00', 1),
(1045, 138, 'Community Concern / Complaint', 'Noise complaint', 'We would like to request your assistance regarding: noise complaint. Please let us know when we may visit the office.', NULL, NULL, '09170023757', 'maria.santos@example.com', NULL, NULL, NULL, NULL, 1, 'completed', '2026-10-06', '14:00:00', 'Please coordinate with the office.', NULL, 1, 19, '2026-09-28 08:19:00', '2026-10-05 20:00:00', '2026-09-29 15:00:00', '2026-10-05 18:00:00', '2026-10-06 04:00:00', 1),
(1046, 150, 'Inter-LGU Coordination', 'Shared disaster-response drill', 'We would like to request your assistance regarding: shared disaster-response drill. Please let us know when we may visit the office.', NULL, NULL, '09170118785', 'josefina.ramos@example.com', NULL, NULL, NULL, NULL, 25, 'completed', '2026-10-07', '14:00:00', 'Please coordinate with the office.', NULL, 1, 20, '2026-09-29 01:24:00', '2026-10-06 20:00:00', '2026-09-29 09:00:00', '2026-10-05 12:00:00', '2026-10-07 04:00:00', 1),
(1047, 144, 'Project Proposal', 'Proposed barangay drainage project', 'We would like to request your assistance regarding: proposed barangay drainage project. Please let us know when we may visit the office.', NULL, NULL, '09170071271', 'liza.mendoza@example.com', NULL, NULL, NULL, NULL, 16, 'completed', '2026-10-05', '09:00:00', 'Please coordinate with the office.', NULL, 1, 11, '2026-09-30 00:39:00', '2026-10-04 20:00:00', '2026-10-01 16:00:00', '2026-10-02 18:00:00', '2026-10-05 04:00:00', 1),
(1048, 146, 'Community Concern / Complaint', 'Noise complaint', 'We would like to request your assistance regarding: noise complaint. Please let us know when we may visit the office.', NULL, NULL, '09170087109', 'teresita.aquino@example.com', NULL, NULL, NULL, NULL, 25, 'completed', '2026-10-12', '10:00:00', 'Please coordinate with the office.', NULL, 1, 20, '2026-10-02 05:04:00', '2026-10-11 20:00:00', '2026-10-05 09:00:00', '2026-10-08 12:00:00', '2026-10-12 04:00:00', 1),
(1049, 148, 'Courtesy Call / Meeting Request', 'Meeting request with the Mayor', 'We would like to request your assistance regarding: meeting request with the mayor. Please let us know when we may visit the office.', NULL, NULL, '09170102947', 'marites.domingo@example.com', NULL, NULL, NULL, NULL, 23, 'completed', '2026-10-13', '09:00:00', 'Please coordinate with the office.', NULL, 1, 36, '2026-10-02 05:55:00', '2026-10-12 20:00:00', '2026-10-05 11:00:00', '2026-10-09 12:00:00', '2026-10-13 04:00:00', 1),
(1050, 149, 'Request for Assistance / Support', 'Scholarship support request', 'We would like to request your assistance regarding: scholarship support request. Please let us know when we may visit the office.', NULL, NULL, '09170110866', 'felipe.castillo@example.com', NULL, NULL, NULL, NULL, 16, 'completed', '2026-10-12', '13:00:00', 'Please coordinate with the office.', NULL, 1, 11, '2026-10-05 05:16:00', '2026-10-11 20:00:00', '2026-10-06 13:00:00', '2026-10-09 15:00:00', '2026-10-12 04:00:00', 1),
(1051, 140, 'Inter-LGU Coordination', 'Shared disaster-response drill', 'We would like to request your assistance regarding: shared disaster-response drill. Please let us know when we may visit the office.', NULL, NULL, '09170039595', 'ana.delacruz@example.com', NULL, NULL, NULL, NULL, NULL, 'rejected', NULL, NULL, NULL, 'Outside the scope of the municipality.', 1, NULL, '2026-10-05 06:45:00', '2026-10-07 03:00:00', '2026-10-07 11:00:00', NULL, NULL, 1),
(1052, 147, 'Project Proposal', 'Youth sports program proposal', 'We would like to request your assistance regarding: youth sports program proposal. Please let us know when we may visit the office.', NULL, NULL, '09170095028', 'rogelio.navarro@example.com', NULL, NULL, NULL, NULL, 5, 'completed', '2026-10-19', '11:00:00', 'Please coordinate with the office.', NULL, 1, 24, '2026-10-08 01:20:00', '2026-10-18 20:00:00', '2026-10-08 12:00:00', '2026-10-15 15:00:00', '2026-10-19 04:00:00', 1),
(1053, 142, 'Request for Assistance / Support', 'Burial assistance request', 'We would like to request your assistance regarding: burial assistance request. Please let us know when we may visit the office.', NULL, NULL, '09170055433', 'carmela.garcia@example.com', NULL, NULL, NULL, NULL, NULL, 'pending_review', NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-08 01:45:00', '2026-10-08 01:45:00', NULL, NULL, NULL, 1),
(1054, 146, 'Request for Assistance / Support', 'Medical assistance for a family member', 'We would like to request your assistance regarding: medical assistance for a family member. Please let us know when we may visit the office.', NULL, NULL, '09170087109', 'teresita.aquino@example.com', NULL, NULL, NULL, NULL, 16, 'completed', '2026-10-19', '09:00:00', 'Please coordinate with the office.', NULL, 1, 11, '2026-10-08 03:10:00', '2026-10-18 20:00:00', '2026-10-12 14:00:00', '2026-10-14 16:00:00', '2026-10-19 04:00:00', 1),
(1055, 140, 'Request for Assistance / Support', 'Burial assistance request', 'We would like to request your assistance regarding: burial assistance request. Please let us know when we may visit the office.', NULL, NULL, '09170039595', 'ana.delacruz@example.com', NULL, NULL, NULL, NULL, NULL, 'rejected', NULL, NULL, NULL, 'Outside the scope of the municipality.', 1, NULL, '2026-10-08 04:21:00', '2026-10-09 04:00:00', '2026-10-09 12:00:00', NULL, NULL, 1),
(1056, 144, 'Business Proposal / Partnership', 'Sponsorship for the town fiesta', 'We would like to request your assistance regarding: sponsorship for the town fiesta. Please let us know when we may visit the office.', NULL, NULL, '09170071271', 'liza.mendoza@example.com', NULL, NULL, NULL, NULL, 5, 'completed', '2026-10-22', '14:00:00', 'Please coordinate with the office.', NULL, 1, 24, '2026-10-09 02:45:00', '2026-10-20 15:00:00', '2026-10-12 09:00:00', '2026-10-19 10:00:00', '2026-10-20 23:00:00', 1),
(1057, 143, 'Community Concern / Complaint', 'Illegal dumping complaint', 'We would like to request your assistance regarding: illegal dumping complaint. Please let us know when we may visit the office.', NULL, NULL, '09170063352', 'ramon.villanueva@example.com', NULL, NULL, NULL, NULL, NULL, 'rejected', NULL, NULL, NULL, 'Outside the scope of the municipality.', 1, NULL, '2026-10-09 07:06:00', '2026-10-12 03:00:00', '2026-10-12 11:00:00', NULL, NULL, 1),
(1058, 151, 'Community Concern / Complaint', 'Broken streetlights', 'We would like to request your assistance regarding: broken streetlights. Please let us know when we may visit the office.', NULL, NULL, '09170126704', 'antonio.pascual@example.com', NULL, NULL, NULL, NULL, 22, 'forwarded', NULL, NULL, 'Please coordinate with the office.', NULL, 1, NULL, '2026-10-12 01:49:00', '2026-10-14 04:00:00', '2026-10-14 12:00:00', NULL, NULL, 1),
(1059, 148, 'Others', 'Request for a certified document', 'We would like to request your assistance regarding: request for a certified document. Please let us know when we may visit the office.', NULL, NULL, '09170102947', 'marites.domingo@example.com', NULL, NULL, NULL, NULL, 3, 'forwarded', NULL, NULL, 'Please coordinate with the office.', NULL, 1, NULL, '2026-10-12 05:03:00', '2026-10-14 02:00:00', '2026-10-14 10:00:00', NULL, NULL, 1),
(1060, 148, 'Project Proposal', 'Youth sports program proposal', 'We would like to request your assistance regarding: youth sports program proposal. Please let us know when we may visit the office.', NULL, NULL, '09170102947', 'marites.domingo@example.com', NULL, NULL, NULL, NULL, NULL, 'rejected', NULL, NULL, NULL, 'Outside the scope of the municipality.', 1, NULL, '2026-10-13 02:19:00', '2026-10-14 05:00:00', '2026-10-14 13:00:00', NULL, NULL, 1),
(1061, 146, 'Request for Assistance / Support', 'Burial assistance request', 'We would like to request your assistance regarding: burial assistance request. Please let us know when we may visit the office.', NULL, NULL, '09170087109', 'teresita.aquino@example.com', NULL, NULL, NULL, NULL, 1, 'scheduled', '2026-10-20', '14:00:00', 'Please coordinate with the office.', NULL, 1, 19, '2026-10-13 03:29:00', '2026-10-19 02:00:00', '2026-10-15 10:00:00', '2026-10-19 10:00:00', NULL, 1),
(1062, 147, 'Inter-LGU Coordination', 'Shared disaster-response drill', 'We would like to request your assistance regarding: shared disaster-response drill. Please let us know when we may visit the office.', NULL, NULL, '09170095028', 'rogelio.navarro@example.com', NULL, NULL, NULL, NULL, NULL, 'cancelled', NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-15 05:08:00', '2026-10-15 05:08:00', NULL, NULL, NULL, 1),
(1063, 143, 'Project Proposal', 'Proposed barangay drainage project', 'We would like to request your assistance regarding: proposed barangay drainage project. Please let us know when we may visit the office.', NULL, NULL, '09170063352', 'ramon.villanueva@example.com', NULL, NULL, NULL, NULL, 21, 'forwarded', NULL, NULL, 'Please coordinate with the office.', NULL, 1, NULL, '2026-10-16 01:19:00', '2026-10-19 01:00:00', '2026-10-19 09:00:00', NULL, NULL, 1),
(1064, 141, 'Inter-LGU Coordination', 'Joint clean-up drive with a neighboring town', 'We would like to request your assistance regarding: joint clean-up drive with a neighboring town. Please let us know when we may visit the office.', NULL, NULL, '09170047514', 'pedro.bautista@example.com', NULL, NULL, NULL, NULL, 25, 'forwarded', NULL, NULL, 'Please coordinate with the office.', NULL, 1, NULL, '2026-10-16 02:07:00', '2026-10-19 02:00:00', '2026-10-19 10:00:00', NULL, NULL, 1),
(1065, 141, 'Request for Assistance / Support', 'Request for farm inputs', 'We would like to request your assistance regarding: request for farm inputs. Please let us know when we may visit the office.', NULL, NULL, '09170047514', 'pedro.bautista@example.com', NULL, NULL, NULL, NULL, 16, 'completed', '2026-10-27', '09:00:00', 'Please coordinate with the office.', NULL, 1, 11, '2026-10-16 03:59:00', '2026-10-23 05:00:00', '2026-10-20 12:00:00', '2026-10-23 13:00:00', '2026-10-20 23:00:00', 1),
(1066, 148, 'Others', 'Request for a venue', 'We would like to request your assistance regarding: request for a venue. Please let us know when we may visit the office.', NULL, NULL, '09170102947', 'marites.domingo@example.com', NULL, NULL, NULL, NULL, 3, 'forwarded', NULL, NULL, 'Please coordinate with the office.', NULL, 1, NULL, '2026-10-16 04:37:00', '2026-10-20 03:00:00', '2026-10-20 11:00:00', NULL, NULL, 1),
(1067, 146, 'Project Proposal', 'Proposed barangay drainage project', 'We would like to request your assistance regarding: proposed barangay drainage project. Please let us know when we may visit the office.', NULL, NULL, '09170087109', 'teresita.aquino@example.com', NULL, NULL, NULL, NULL, NULL, 'pending_review', NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-19 05:17:00', '2026-10-19 05:17:00', NULL, NULL, NULL, 1),
(1068, 149, 'Request for Assistance / Support', 'Request for farm inputs', 'We would like to request your assistance regarding: request for farm inputs. Please let us know when we may visit the office.', NULL, NULL, '09170110866', 'felipe.castillo@example.com', NULL, NULL, NULL, NULL, NULL, 'pending_review', NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-19 08:00:00', '2026-10-19 08:00:00', NULL, NULL, NULL, 1),
(1069, 146, 'Request for Assistance / Support', 'Request for farm inputs', 'We would like to request your assistance regarding: request for farm inputs. Please let us know when we may visit the office.', NULL, NULL, '09170087109', 'teresita.aquino@example.com', NULL, NULL, NULL, NULL, NULL, 'pending_review', NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-20 03:57:00', '2026-10-20 03:57:00', NULL, NULL, NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `audit_log`
--

CREATE TABLE `audit_log` (
  `id` int(10) UNSIGNED NOT NULL,
  `actor_user_id` int(10) UNSIGNED DEFAULT NULL,
  `actor_name` varchar(150) NOT NULL,
  `action` varchar(50) NOT NULL,
  `target_user_id` int(10) UNSIGNED DEFAULT NULL,
  `target_name` varchar(150) DEFAULT NULL,
  `details` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `audit_log`
--

INSERT INTO `audit_log` (`id`, `actor_user_id`, `actor_name`, `action`, `target_user_id`, `target_name`, `details`, `created_at`) VALUES
(28, 19, 'Hon. Maria Elena L. Germar', 'queue_qr_renewed', NULL, 'Mayor\'s Office', 'The queue QR code was renewed early.', '2026-10-20 15:41:08'),
(29, 19, 'Hon. Maria Elena L. Germar', 'queue_event_created', NULL, 'Mayor\'s Office', 'Created the queue event \"ZZ Financial Aid\"', '2026-10-20 15:51:06'),
(30, 19, 'Hon. Maria Elena L. Germar', 'queue_qr_renewed', NULL, 'Mayor\'s Office', 'The queue QR code was renewed early.', '2026-10-20 15:51:48'),
(31, 19, 'Hon. Maria Elena L. Germar', 'queue_settings_changed', NULL, 'Mayor\'s Office', 'Saved a queue staff name: Jean Frias', '2026-10-21 01:17:10'),
(32, 19, 'Hon. Maria Elena L. Germar', 'queue_settings_changed', NULL, 'Mayor\'s Office', 'Saved a queue staff name: Sir Henjie', '2026-10-21 01:17:19'),
(33, 19, 'Hon. Maria Elena L. Germar', 'queue_settings_changed', NULL, 'Mayor\'s Office', 'Saved a queue staff name: Angelu', '2026-10-21 01:17:36'),
(34, 82, 'Zz Sys', 'schedule_updated', NULL, NULL, 'Monday to Friday, 8:00 AM - 5:00 PM, except holidays', '2026-10-21 04:50:32'),
(35, 82, 'Zz Sys', 'schedule_updated', NULL, NULL, 'Monday to Friday, 8:00 AM - 5:00 PM, except holidays', '2026-10-21 04:51:14'),
(36, NULL, 'System', 'demo_data_changed', NULL, NULL, 'Loaded the demo data for the analytics pages', '2026-10-21 06:05:18'),
(37, 112, 'Zz Sys', 'demo_data_changed', NULL, NULL, 'Removed the demo data', '2026-10-21 06:06:42'),
(38, 112, 'Zz Sys', 'demo_data_changed', NULL, NULL, 'Loaded the demo data for the analytics pages', '2026-10-21 06:06:44'),
(39, 131, 'Zz Admin', 'analytics_targets_changed', NULL, NULL, 'Targets: wait 10 min, review alert 3 days, schedule alert 5 days', '2026-10-21 06:18:08'),
(40, 131, 'Zz Admin', 'analytics_targets_changed', NULL, NULL, 'Targets: wait 4 min, review alert 3 days, schedule alert 5 days', '2026-10-21 06:18:08'),
(41, 131, 'Zz Admin', 'analytics_targets_changed', NULL, NULL, 'Targets: wait 30 min, review alert 3 days, schedule alert 5 days', '2026-10-21 06:18:09'),
(42, 131, 'Zz Admin', 'analytics_targets_changed', NULL, NULL, 'Targets: wait 10 min, review alert 3 days, schedule alert 5 days', '2026-10-21 06:18:09'),
(43, NULL, 'System', 'demo_data_changed', NULL, NULL, 'Removed the synthetic data', '2026-10-21 06:39:38'),
(44, NULL, 'System', 'demo_data_changed', NULL, NULL, 'Loaded the synthetic data for the analytics pages', '2026-10-21 06:39:39');

-- --------------------------------------------------------

--
-- Table structure for table `citizens_charter_services`
--

CREATE TABLE `citizens_charter_services` (
  `id` int(10) UNSIGNED NOT NULL,
  `office` varchar(150) NOT NULL,
  `title` varchar(200) NOT NULL,
  `keywords` text NOT NULL,
  `requirements` text NOT NULL,
  `fee` varchar(255) NOT NULL,
  `processing_time` varchar(255) NOT NULL,
  `sort_order` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `citizens_charter_services`
--

INSERT INTO `citizens_charter_services` (`id`, `office`, `title`, `keywords`, `requirements`, `fee`, `processing_time`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 'Business Permits & Licensing Office', 'Issuance of Business Permit (New)', 'new business permit\nbusiness permit\nmayor\'s permit\nmayors permit\nmayor\'s clearance\nmayors clearance\nstart a business\nregister a business\nopen a business', 'Community Tax Certificate / CTC (Municipal Treasurer\'s Office)\nBarangay Business Clearance for the current year\nProof of registration: DTI (sole proprietorship), SEC (partnership/corporation), or CDA (cooperative)\nProof of right to use the property as business location (TCT/Tax Declaration if owned; notarized Lease Contract or Affidavit to Use if not)\nCertificate of Non-Coverage (CNC) and clearances from MENRO, MPDO (zoning), Engineering (occupancy), Rural Health Office (sanitary), and BFP (fire safety) as applicable\nSpecial document requirements for regulated businesses (e.g. FDA license for pharmacies, DOLE/POEA for agencies) — ask BPLO if yours applies', 'Varies by clearance — e.g. CNC around ₱1,000, plus MPDO/BFP assessment based on your business', 'About 1 day and 45 minutes once all requirements are complete', 0, '2026-08-15 00:33:54', '2026-08-15 00:33:54'),
(2, 'Business Permits & Licensing Office', 'Renewal of Business Permit', 'renew business permit\nbusiness permit renewal\nrenewal of business permit', 'Community Tax Certificate / CTC\nBarangay Business Clearance for the current year\nProof of registration (DTI/SEC/CDA)\nPrevious year\'s Mayor\'s Permit\nProof of tax payments (BIR) or notarized Affidavit of Declaration of Gross Income\nCertificate of tax exemption, if your business is tax-exempt', 'Based on assessed gross sales/receipts', 'About 1 day and 45 minutes', 1, '2026-08-15 00:33:54', '2026-08-15 00:33:54'),
(3, 'Business Permits & Licensing Office', 'Motorized Tricycle Operator\'s Permit (MTOP)', 'tricycle permit\nmtop\nmotorized tricycle', 'Barangay Clearance for the current year\nPolice Clearance\nCertification from the TODA President and the Federation President\nOfficial Receipt (OR) and Certificate of Registration (CR) of the tricycle\nProfessional Driver\'s License\nDeed of Sale / Sales Invoice\nVoter\'s ID or Certified True Copy of Voter Registration (VRR)', '₱575 for new, ₱210 for renewal', 'About 1 day and 30 minutes', 2, '2026-08-15 00:33:54', '2026-08-15 00:33:54'),
(4, 'Municipal Assessor\'s Office', 'Transfer of Tax Declaration to New Owner/s', 'transfer tax declaration\ntransfer of ownership property\nchange owner tax declaration', 'New title (if titled) or Deed of Conveyance (Sale, Donation, or Extrajudicial Settlement)\nBIR Certification (Capital Gains Tax, Donor\'s Tax, or Estate Tax)\nTransfer Tax receipt (Provincial Treasurer\'s Office)\nLatest/updated Real Property Tax Receipt\nIf applicable: subdivision/consolidation plan, Affidavit of Publication (inherited property), DAR clearance, SPA if represented by someone else', '₱200 per Tax Declaration', 'About 1 hour', 3, '2026-08-15 00:33:54', '2026-08-15 00:33:54'),
(5, 'Municipal Assessor\'s Office', 'Consolidation and Subdivision of Property', 'consolidation of property\nsubdivision of land\nsubdivide property\nmerge lots\nmerge parcels', 'Title/s (if titled)\nApproved subdivision/consolidation plan (Bureau of Lands)\nLatest/updated Real Property Tax Receipt', '₱200 per Tax Declaration', 'About 1 hour', 4, '2026-08-15 00:33:54', '2026-08-15 00:33:54'),
(6, 'Municipal Assessor\'s Office', 'Demolition and Cancellation of Property Record', 'demolition of property\ncancel tax declaration\ncancellation of property record', 'Written request\nLatest/updated Real Property Tax Receipt\nOcular inspection report (conducted by the Assessor\'s Office)\nDemolition Permit (Engineering Office) and/or Certification of Business Closure (BPLO), if applicable', 'None', 'About 2 hours 31 minutes (includes the ocular inspection)', 5, '2026-08-15 00:33:54', '2026-08-15 00:33:54'),
(7, 'Municipal Assessor\'s Office', 'Reassessment / Assessment of Property', 'reassessment of property\nassess new property\ndeclare new property for tax', 'Written request\nLatest/updated Real Property Tax Receipt\nOcular inspection report', '₱200 per Tax Declaration', 'About 2 hours 56 minutes (includes ocular inspection, 2–8 hours depending on location)', 6, '2026-08-15 00:33:54', '2026-08-15 00:33:54'),
(8, 'Municipal Assessor\'s Office', 'Reclassification of Property', 'reclassification of property\nreclassify land', 'Title/s\nSangguniang Bayan Resolution\nSangguniang Panlalawigan Resolution\nLatest/updated Real Property Tax Receipt', '₱200 per Tax Declaration', 'About 1 hour', 7, '2026-08-15 00:33:54', '2026-08-15 00:33:54'),
(9, 'Municipal Assessor\'s Office', 'Tax Declaration for Newly Assessed Land, Building, or Machinery', 'new tax declaration\ntax declaration for new building\nnewly assessed property\ndeclare new machinery', 'For land: existing Tax Declaration\nFor a building: Building Permit, Architectural Plan, Cost Estimate, Certificate of Occupancy\nFor machinery: Notarized Sworn Statement, plus SPA if represented', '₱200 per Tax Declaration', 'About 2 hours 56 minutes', 8, '2026-08-15 00:33:54', '2026-08-15 00:33:54'),
(10, 'Municipal Assessor\'s Office', 'Certified True Copy of Tax Declaration', 'certified true copy of tax declaration\ncopy of tax declaration', 'Copy of Tax Declaration\nLatest/updated Real Property Tax Receipt\nLetter request from the owner or authorized representative (with SPA if represented)', '₱100 per Tax Declaration', 'About 25 minutes', 9, '2026-08-15 00:33:54', '2026-08-15 00:33:54'),
(11, 'Municipal Assessor\'s Office', 'Property Certifications (With/Without Improvement, Land Holdings, No Property)', 'certificate of no property\ncertificate of land holdings\ncertificate with improvement\ncertificate non improvement\nindigency property certificate', 'Copy of Tax Declaration\nLatest/updated Real Property Tax Receipt\nLetter request from the owner or authorized representative (with SPA if represented)', '₱100 per certification', 'About 25 minutes', 10, '2026-08-15 00:33:54', '2026-08-15 00:33:54'),
(12, 'Municipal Civil Registrar\'s Office', 'Certified True Copy of Birth / Death / Marriage Certificate', 'certified true copy of birth certificate\ncopy of birth certificate\ncopy of death certificate\ncopy of marriage certificate\nform 1a\nform 2a\nform 3a', 'Accomplished request slip\n1 photocopy of 2 valid IDs\nIf through a representative: original authorization letter or SPA, plus valid ID of both the owner and the representative', 'About ₱200 total (verification fee + copy fee)', 'About 12 minutes', 11, '2026-08-15 00:33:54', '2026-08-15 00:33:54'),
(13, 'Municipal Civil Registrar\'s Office', 'Registration of Birth, Death, or Marriage', 'register a birth\nregister a death\nregister a marriage\nbirth registration\ndeath registration\nmarriage registration', 'Duly accomplished Certificate of Live Birth/Death/Marriage (4 original copies, from the hospital/officiant)\nResidence Certificate (Cedula) of parents, if not married\nValid ID of the informant\nMarriage Certificate of parents, if applicable\nAffidavit of Paternity and Affidavit to Use the Surname of the Father, if not married', 'About ₱650 total (birth ₱100, marriage ₱100, burial ₱50, misc ₱100, cadaver transfer ₱200 — as applicable)', 'About 36 minutes', 12, '2026-08-15 00:33:54', '2026-08-15 00:33:54'),
(14, 'Municipal Civil Registrar\'s Office', 'Application for Marriage License', 'marriage license\napply for marriage license', 'PSA CENOMAR (original)\nBirth Certificate\nResidence Certificate (Cedula)\nIf 18–20 years old: parental consent signed by the father at MCRO, plus the father\'s valid ID and Cedula', 'About ₱400 total', 'About 36 minutes to process, but the license can only be released after a mandatory 10-calendar-day posting period', 13, '2026-08-15 00:33:54', '2026-08-15 00:33:54'),
(15, 'Municipal Civil Registrar\'s Office', 'Late Registration / Out-of-Town Registration of Birth, Death, or Marriage', 'late registration of birth\ndelayed registration\nout of town registration', '4 copies of duly accomplished Certificate of Live Birth (or the Death/Marriage equivalent)\nAffidavit of Delayed Registration\nNegative Certification from PSA\nTwo documentary evidence plus Affidavit of Two Disinterested Persons\nNational ID of the registrant and parents, Barangay Certification of residency, 2 recent photos\nAdditional affidavits depending on your case (paternity, whereabouts of mother, etc.)', '₱150', 'About 21 minutes to process, then a required 10-calendar-day posting period before release', 14, '2026-08-15 00:33:54', '2026-08-15 00:33:54'),
(16, 'Municipal Civil Registrar\'s Office', 'Supplemental Report (correcting an omitted entry)', 'supplemental report birth certificate\nmissing entry birth certificate', 'PSA Birth Certificate and local copy\nAffidavit of Supplemental Report (valid for 1 month)\nBaptismal Certificate, School Record, 2 valid IDs\nBirth/Marriage Certificates of parents', '₱150', 'About 1 month (the annotated copy is released by PSA after 1 month)', 15, '2026-08-15 00:33:54', '2026-08-15 00:33:54'),
(17, 'Municipal Civil Registrar\'s Office', 'RA 9255 — Affidavit to Use the Surname of the Father (AUSF)', 'use surname of father\nra 9255\nausf', 'Affidavit of Acknowledgment/Admission of Paternity\nBirth Certificate of the child\nAUSF form (with the mother\'s sworn attestation if the child is 7–20 years old)\nValid IDs of parents and child', '₱150', 'About 1 month (annotated document released by PSA)', 16, '2026-08-15 00:33:54', '2026-08-15 00:33:54'),
(18, 'Municipal Civil Registrar\'s Office', 'Legitimation', 'legitimation of child\nlegitimate a child\nlegitimation', 'Affidavit of Legitimation\nBirth Certificate of the child (PSA and local copy)\nMarriage Certificate of parents, Baptismal Certificate\nPSA Advisory on Marriage (both parents), valid IDs', '₱150', 'About 1 month', 17, '2026-08-15 00:33:54', '2026-08-15 00:33:54'),
(19, 'Municipal Civil Registrar\'s Office', 'Petition for Correction — RA 10172 (Gender / Date / Month of Birth)', 'ra 10172\ncorrect gender\ncorrect date of birth\ncorrect month of birth', 'PSA and local copy of the Birth/Death/Marriage Certificate\nBaptismal Certificate, Medical Record/Certificate\nNBI and Police Clearance, Affidavit of Employment/Non-Employment\nForm 137/Transcript, Cedula, Voter\'s Certification, 2 valid IDs\nNewspaper publication (you shoulder roughly ₱1,500 publisher fee)\nFull checklist can vary by case — MCRO will confirm exactly what applies to you', '₱3,000 plus about ₱1,500 for required newspaper publication', 'About 4 months (includes mandatory 10-day posting and PSA review)', 18, '2026-08-15 00:33:54', '2026-08-15 00:33:54'),
(20, 'Municipal Civil Registrar\'s Office', 'RA 9048 — Correction of Clerical Error', 'ra 9048\nclerical error correction\nmisspelled name correction', 'PSA and local copy of the Birth/Death/Marriage Certificate\nBaptismal Certificate, Marriage Contract\nBirth Certificates of siblings/children, Cedula, Voter\'s Registration, 2 valid IDs\nFull checklist can vary by case — MCRO will confirm exactly what applies to you', '₱1,000', 'About 50 days (includes posting and PSA review)', 19, '2026-08-15 00:33:54', '2026-08-15 00:33:54'),
(21, 'Municipal Civil Registrar\'s Office', 'RA 9048 — Petition for Change of First Name', 'change of first name\npetition for change of name', 'PSA and local copy of Birth Certificate, Baptismal Certificate\nMarriage Contract, NBI and Police Clearance\nAffidavit of Employment/Non-Employment, Form 137/Diploma, Cedula\nNewspaper publication, Voter\'s Certification, 2 valid IDs', '₱3,000 plus about ₱1,500 for required newspaper publication', 'About 4 months', 20, '2026-08-15 00:33:54', '2026-08-15 00:33:54'),
(22, 'Municipal Civil Registrar\'s Office', 'Endorsement of Negative Certification / Clear Copy / Advance Copy', 'negative certification\nadvance copy of birth certificate\nclear copy endorsement', 'PSA Negative Certification\nProof of urgency, 1 valid ID\nCertificate of Live Birth (Municipal Form)', '₱100–150', 'About 31 days', 21, '2026-08-15 00:33:54', '2026-08-15 00:33:54'),
(23, 'Municipal Engineering Office', 'Building Permit', 'building permit\nconstruct a building\nconstruction permit', 'Barangay Clearance for Building Permit\nProof of Lot Ownership\nLocational/Zoning Clearance (MPDO)\nFire Safety Evaluation Clearance (BFP)\nConstruction Safety & Health Program (DOLE)\n5 sets of survey plans, design plans, and related documents\nClearances from other agencies if needed (DPWH, DepEd, CAAP, DENR, DOH, etc.)\nApply online: bldg.ibpls.com/norzagaraybulacan', 'Based on the National Building Code fee schedule and local ordinances', 'About 1 day and 1 hour 22 minutes', 22, '2026-08-15 00:33:54', '2026-08-15 00:33:54'),
(24, 'Municipal Engineering Office', 'Certificate of Occupancy', 'certificate of occupancy\noccupancy certificate\noccupancy permit', 'Issued Building Permit and Fire Safety/Locational Clearance\n3 notarized copies of Certificate of Completion\nAs-Built Plan and Construction Logbook (signed & sealed)\nCaptioned site photos of the completed building\nYellow card from your electrical source provider (e.g. Meralco)\nApply online: co.ibpls.com/norzagaraybulacan', 'Based on the National Building Code fee schedule', 'About 1 day and 53 minutes', 23, '2026-08-15 00:33:54', '2026-08-15 00:33:54'),
(25, 'Municipal Engineering Office', 'Certificate of Electrical Inspection and Wiring Permit', 'electrical inspection\nwiring permit', 'Proof of lot ownership, Barangay Clearance for Electrical/Wiring\nIssued Building Permit, 2 valid IDs\nWiring Permit signed by a licensed professional or registered electrical engineer', 'Based on the National Building Code fee schedule', 'About 2 hours 30 minutes (includes ocular inspection)', 24, '2026-08-15 00:33:54', '2026-08-15 00:33:54'),
(26, 'Municipal Health Office', 'Medical Consultation', 'medical consultation\nsee a doctor\ncheckup at health center', 'Your physical presence\nPrevious medical record related to your condition, if you have one', 'Free', 'About 20–40 minutes, depending on whether lab tests are needed', 25, '2026-08-15 00:33:54', '2026-08-15 00:33:54'),
(27, 'Municipal Health Office', 'Dental Consultation', 'dental consultation\ntooth extraction\nsee a dentist', 'Your physical presence\nPrevious dental record, if applicable', 'Free', 'About 65 minutes', 26, '2026-08-15 00:33:54', '2026-08-15 00:33:54'),
(28, 'Municipal Health Office', 'Maternal Health Care Services', 'maternal health\nprenatal checkup\npregnancy checkup', 'Your physical presence\nMaternal & child booklet, if you have one', 'Free', 'About 35 minutes', 27, '2026-08-15 00:33:54', '2026-08-15 00:33:54'),
(29, 'Municipal Health Office', 'National Immunization Program', 'immunization\nvaccination\nvaccine schedule', 'Your physical presence\nPrevious vaccination record, if applicable', 'Free', 'About 20 minutes', 28, '2026-08-15 00:33:54', '2026-08-15 00:33:54'),
(30, 'Municipal Health Office', 'Family Planning Services', 'family planning', 'Your physical presence\nPrevious record, if applicable', 'Free', 'About 20 minutes', 29, '2026-08-15 00:33:54', '2026-08-15 00:33:54'),
(31, 'Municipal Health Office', 'Processing/Issuance of Death Certificate & Exhumation Permit', 'exhumation permit\nprocess death certificate', 'Filled-out Death Certificate\nOfficial medical records (from within 10 years before death)\nOriginal copy of death certificate, for exhumation', '₱200', 'About 10 minutes', 30, '2026-08-15 00:33:54', '2026-08-15 00:33:54'),
(32, 'Municipal Health Office', 'Health Card for Employment', 'health card\nhealth card for employment', 'Medical laboratory result (from a diagnostic laboratory, clinic, or hospital)', '₱100', 'About 15 minutes', 31, '2026-08-15 00:33:54', '2026-08-15 00:33:54'),
(33, 'Municipal Health Office', 'Sanitary Permit', 'sanitary permit', 'ECC / Discharge Permit / Permit to Operate as applicable\nApplication Permit\nMedical results of employees, if necessary\nWater Test Results, for water refilling stations', '₱300–₱2,550 depending on the business and whether inspection is needed', 'About 5 minutes for simple cases, up to 1 day if inspection is required', 32, '2026-08-15 00:33:54', '2026-08-15 00:33:54'),
(34, 'Municipal Health Office', 'Potability Certificate', 'potability certificate\nwater potability', 'Water Test Results from a DOH-accredited laboratory', '₱100', 'About 5 minutes', 33, '2026-08-15 00:33:54', '2026-08-15 00:33:54'),
(35, 'Municipal Planning & Development Office', 'Zoning Certification for Business Permit — Non-Critical Projects (New)', 'zoning certification business\nzoning clearance business', 'Proof of property ownership/lease\nDTI/SEC/CDA registration\nUpdated Barangay Clearance\nSketch of location, actual project photo\nCertificate of Non-Coverage (CNC)\nLatest tax receipt', '₱500 per hectare', 'About 33 minutes', 34, '2026-08-15 00:33:54', '2026-08-15 00:33:54'),
(36, 'Municipal Planning & Development Office', 'Zoning Certification for Business Permit — Critical Projects (New)', 'zoning certification critical project', 'Same as the non-critical checklist, but with an Environmental Compliance Certificate (ECC) instead of a CNC\nA site inspection is also required before approval', '₱500 per hectare', 'About 53 minutes, plus a site inspection (subject to inspector availability, up to 48 hours)', 35, '2026-08-15 00:33:54', '2026-08-15 00:33:54'),
(37, 'Municipal Planning & Development Office', 'Zoning Certification for Renewal of Business Permit', 'zoning certification renewal', 'Photocopy of Business Permit\nUpdated Barangay Clearance\nLatest tax receipt\nActual photos', 'Minimal, assessed on-site', 'About 33 minutes', 36, '2026-08-15 00:33:54', '2026-08-15 00:33:54'),
(38, 'Municipal Planning & Development Office', 'Zoning Certification (as to Land Use)', 'zoning certification land use\nland use certification', 'Transfer Certificate of Title\nTax Declaration with latest tax receipt\nSketch of location', '₱500 per hectare', 'About 29 minutes', 37, '2026-08-15 00:33:54', '2026-08-15 00:33:54'),
(39, 'Municipal Planning & Development Office', 'Locational Clearance for Building Permit — Critical Projects', 'locational clearance critical project\nlocational clearance', 'Transfer Certificate of Title\nDTI/SEC/CDA registration\nUpdated Barangay Clearance\nSketch of location\nEnvironmental Compliance Certificate (ECC)\nLatest tax receipt\nArchitectural/Site Development Plan with bill of materials\nSupporting affidavits as applicable', 'Based on the Revenue Code and the project\'s bill of materials', 'About 35 minutes, plus a site inspection', 38, '2026-08-15 00:33:54', '2026-08-15 00:33:54'),
(40, 'Municipal Environment & Natural Resources Office', 'Environmental Clearance for Business Permit — Non-Critical (New)', 'environmental clearance non critical\nenvironmental clearance new business', 'Application form\nCertificate of Non-Coverage (apply online via EMB-DENR)\nBarangay Business Clearance\nDTI/SEC/CDA registration\nGeotagged photo of the project/business\nValid government ID', 'Plus environmental inspection and solid waste management fees assessed per project', 'About 12 minutes', 39, '2026-08-15 00:33:54', '2026-10-16 13:31:46'),
(41, 'Municipal Environment & Natural Resources Office', 'Environmental Clearance for Business Permit — Non-Critical (Renewal)', 'environmental clearance renewal', 'Application form\nLocal Certificate of Non-Coverage for renewal, plus its Official Receipt\nBarangay Business Clearance\nDTI/SEC/CDA registration', '₱1,000', 'About 10 minutes', 40, '2026-08-15 00:33:54', '2026-10-16 13:31:46'),
(42, 'Municipal Environment & Natural Resources Office', 'Environmental Clearance for Business Permit — Critical Projects/Areas', 'environmental clearance critical project\nenvironmental clearance critical area', 'Environmental Compliance Certificate (ECC)\nProvincial Government Acknowledgement Certificate\nBarangay Business Clearance\nDTI/SEC registration\nValid ID\nRenewals also need PCO Accreditation, Permit to Operate, and Wastewater Discharge Permit', 'Plus environmental, solid waste, and pollution hazard fees assessed per project', 'About 20 minutes', 41, '2026-08-15 00:33:54', '2026-10-16 13:31:46'),
(43, 'Municipal Environment & Natural Resources Office', 'Certificate of No Objection for Tree Cutting Permit', 'tree cutting permit\ncertificate of no objection tree\ncut down a tree', '2 request/application letters (addressed to CENRO-DENR and the Municipal Mayor)\nAuthenticated land title or proof of ownership\nSketch map\nBarangay Endorsement (no objection)\nGeotagged photos of the trees\nValid government ID\nChainsaw registration (CENRO-DENR)\nDENR clearance that the area is not forestland/protected', '₱300 per tree + ₱100 certification fee + ₱1,000 environmental inspection fee', 'About 2 days (includes a joint ocular inspection with DENR)', 42, '2026-08-15 00:33:54', '2026-10-16 13:31:46'),
(44, 'Municipal Social Welfare & Development Office', 'Assistance to Individuals in Crisis Situation (AICS)', 'aics\nfinancial assistance\nmedical assistance\nburial assistance\neducational assistance\ncrisis assistance', 'Barangay Certificate of Indigency\nLetter of request addressed to the Municipal Mayor\nValid IDs of patient/claimant\nProof of need: hospital bill/medical certificate (medical), Certificate of Registration & Statement of Account (educational), or funeral contract & death certificate (burial)', 'Free — this is a benefit, not a paid service', 'About 14 minutes', 43, '2026-08-15 00:33:54', '2026-10-16 13:31:46'),
(45, 'Municipal Social Welfare & Development Office', 'MSWDO Certification (e.g. Guardianship)', 'guardianship certification\nmswdo certification', 'Barangay Certificate of Indigency\nFor guardianship: birth certificate of children, guardian\'s valid ID, notarized Affidavit of Guardianship, barangay certification of the minor\'s circumstances, death certificate of parents (if applicable)', 'Free', 'About 12 minutes', 44, '2026-08-15 00:33:54', '2026-10-16 13:31:46'),
(46, 'Municipal Social Welfare & Development Office', 'Social Case Study Report (SCSR)', 'social case study report\nscsr', 'Valid ID of client and the family member processing the SCSR\nCase reference (medical protocol, legal protocol, or other supporting document)', 'Free', 'About 10 days (includes a home visit)', 45, '2026-08-15 00:33:54', '2026-10-16 13:31:46'),
(47, 'Municipal Social Welfare & Development Office', 'Adoption', 'adoption\nadopt a child', 'Notarized Petition for Adoption\nHome Study Report / Case Study Report\nBirth and marriage certificates of PAPs (SECPA copy)\nNBI/police clearance, medical & psychological evaluations\nChild care plan, character references, proof of financial capacity\nAttendance at a pre-adoption seminar', 'Free', 'About 37 days (Home Study Report preparation alone takes about 30 working days)', 46, '2026-08-15 00:33:54', '2026-10-16 13:31:46'),
(48, 'Municipal Social Welfare & Development Office', 'Person With Disability (PWD) ID', 'pwd id\ndisability id\nperson with disability id', 'Medical Certificate stating the disability\nCertificate of Residency\n2 pcs 1x1 ID picture', 'Free', 'About 7 minutes', 47, '2026-08-15 00:33:54', '2026-10-16 13:31:46'),
(49, 'Municipal Social Welfare & Development Office', 'Solo Parent\'s ID', 'solo parent id\nsolo parent', 'Sworn Affidavit\nBirth certificate of minor children\nCertificate of Attendance at the RA 11861 orientation (new applicants)\n2 pcs 1x1 ID picture\nProof of income', 'Free', 'About 7 days (includes a home visit)', 48, '2026-08-15 00:33:54', '2026-10-16 13:31:46'),
(50, 'Municipal Social Welfare & Development Office', 'Senior Citizen\'s ID', 'senior citizen id\nsenior citizen', 'Birth Certificate\nCertificate of Residency\n2 pcs 1x1 ID picture', 'Free', 'About 7 minutes', 49, '2026-08-15 00:33:54', '2026-10-16 13:31:46'),
(51, 'Municipal Social Welfare & Development Office', 'Livelihood Assistance', 'livelihood assistance\ncapital assistance\nbusiness capital assistance', 'Barangay Certificate of Indigency\nApplication Form\nLetter of request addressed to the Municipal Mayor\nPhotocopy of ID\nPhoto of yourself with your livelihood project/site\nList of products or materials needed with prices\nSketch of your house location', 'Free — this is interest-free capital assistance', 'About 7 days (includes a home visit)', 50, '2026-08-15 00:33:54', '2026-10-16 13:31:46'),
(52, 'Municipal Social Welfare & Development Office', 'Provision of Assistive Devices', 'assistive device\nwheelchair request\nassistive devices', 'Barangay Certificate of Indigency\nLetter of request addressed to the Municipal Mayor\nValid IDs of patient/claimant\nMedical Certificate\nPhoto of the patient requesting the device', 'Free', 'About 33 minutes', 51, '2026-08-15 00:33:54', '2026-10-16 13:31:46'),
(53, 'Municipal Treasurer\'s Office', 'Real Property Tax (RPT) Receipt / Payment', 'real property tax\npay amilyar\nrpt payment\nproperty tax payment', 'Previous RPT Official Receipt, latest Tax Declaration, or Title\nValid government-issued ID\nFor corporations: letter of request plus SPA if represented', '1% of assessed value (Basic RPT) + 1% of assessed value (Special Education Fund), plus penalties or less discounts as applicable', 'About 15 minutes for individuals; about 1 day 11 minutes for corporations', 52, '2026-08-15 00:33:54', '2026-08-15 00:33:54'),
(54, 'Municipal Treasurer\'s Office', 'Community Tax Certificate (Cedula)', 'cedula\ncommunity tax certificate\nctc', 'Accomplished information slip\nValid government-issued ID\nProof of income/current payslip\nFor corporations: Business Permit and Income Tax Return', '₱5 basic + ₱1 per ₱1,000 of income for individuals (max ₱5,000); ₱500 basic + ₱2 per ₱5,000 of sales/property for corporations (max ₱10,000)', 'About 3 minutes', 53, '2026-08-15 00:33:54', '2026-08-15 00:33:54'),
(55, 'Municipal Treasurer\'s Office', 'Tax Clearance', 'tax clearance', 'Previous RPT Official Receipt, latest Tax Declaration, or Title\nValid government-issued ID\nLatest Community Tax Certificate (Cedula)', '₱100', 'About 15 minutes', 54, '2026-08-15 00:33:54', '2026-08-15 00:33:54'),
(56, 'Norzagaray Municipal Hospital', 'Out-Patient Treatment', 'hospital outpatient\nhospital consultation\nopd norzagaray', 'Just your personal appearance at the Outpatient Department', 'Free, or based on the billing statement if medicines/lab tests are needed', 'About 20–40 minutes', 55, '2026-08-15 00:33:54', '2026-08-15 00:33:54'),
(57, 'Norzagaray Municipal Hospital', 'Emergency Services', 'hospital emergency\nemergency room norzagaray', 'Your personal appearance\nOne companion/relative', 'Free, or based on the billing statement', 'Case-to-case basis', 56, '2026-08-15 00:33:54', '2026-08-15 00:33:54');

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `id` int(10) UNSIGNED NOT NULL,
  `code` varchar(20) NOT NULL,
  `name` varchar(150) NOT NULL,
  `sector` varchar(50) NOT NULL,
  `department_head` varchar(150) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `queue_enabled` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`id`, `code`, `name`, `sector`, `department_head`, `created_at`, `queue_enabled`) VALUES
(1, 'MO', 'Mayor\'s Office', 'Executive', 'Hon. Maria Elena L. Germar', '2026-09-25 01:59:32', 1),
(2, 'SB', 'Sangguniang Bayan', 'Public Safety', 'Hon. Giulius Ceazar S. Iapino', '2026-09-25 01:59:32', 0),
(3, 'LCR', 'Local Civil Registrar', 'Executive', 'Filipina DG. Pascual', '2026-09-25 01:59:32', 0),
(4, 'MHRMO', 'Municipal Human Resource Management Office', 'Executive', 'Fernando DG. Cruz', '2026-09-25 01:59:32', 0),
(5, 'MPDO', 'Municipal Planning and Development Office', 'Executive', 'Mark Anthony Francisco', '2026-09-25 01:59:32', 0),
(6, 'PIO', 'Public Information Office', 'Executive', 'Hon. Maria Elena L. Germar', '2026-09-25 01:59:32', 0),
(8, 'GSO', 'General Services Office', 'Executive', 'Marilyn C. Ocampo', '2026-09-25 01:59:32', 0),
(9, 'BPLO', 'Business Permit and Licensing Office', 'Finance', 'Jemelee John De Silva', '2026-09-25 01:59:32', 0),
(10, 'TREAS', 'Treasurer\'s Office', 'Finance', 'Shena Liz P. Alonzo', '2026-09-25 01:59:32', 0),
(11, 'ASS', 'Assessor\'s Office', 'Finance', 'Portia H. German', '2026-09-25 01:59:32', 0),
(12, 'ACCT', 'Accounting Office', 'Finance', 'Manuel R. Marcial', '2026-09-25 01:59:32', 0),
(13, 'BUDGET', 'Budget Office', 'Finance', 'Fernando DG. Cruz', '2026-09-25 01:59:32', 0),
(14, 'MSWDO', 'Municipal Social Welfare and Development Office', 'Social', 'Annalyn Joy C. San Pedro', '2026-09-25 01:59:32', 0),
(15, 'MHO', 'Municipal Health Office', 'Social', 'Dr. George Peppard C. Paitim', '2026-09-25 01:59:32', 0),
(16, 'AGRIC', 'Agriculture Office', 'Social', NULL, '2026-09-25 01:59:32', 0),
(17, 'OSCA', 'Office of the Senior Citizens\' Affairs', 'Social', 'Hon. Maria Elena L. Germar', '2026-09-25 01:59:32', 0),
(18, 'PDAO', 'Persons with Disability Affairs Office', 'Social', 'Hon. Maria Elena L. Germar', '2026-09-25 01:59:32', 0),
(19, 'PESO', 'Public Employment Service Office', 'Social', 'Atty. Rochelle Marie G. Torres', '2026-09-25 01:59:32', 0),
(20, 'LYDO', 'Local Youth Development Office', 'Social', 'Wilmie Kate Z. Logo', '2026-09-25 01:59:32', 0),
(21, 'ENGR', 'Engineering Office', 'Infrastructure', 'Engr. Silvestre P. De Guzman', '2026-09-25 01:59:32', 0),
(22, 'MENRO', 'Municipal Environment and Natural Resources Office', 'Infrastructure', 'Mark Anthony Francisco', '2026-09-25 01:59:32', 0),
(23, 'TOUR', 'Tourism Office', 'Infrastructure', 'Hon. Maria Elena L. Germar', '2026-09-25 01:59:32', 0),
(25, 'MDRRMO', 'Municipal Disaster Risk Reduction and Management Office', 'Public Safety', 'Jerry R. Sumbillo', '2026-09-25 01:59:32', 0),
(26, 'NTMPSO', 'Norzagaray Traffic Management and Public Safety Office', 'Public Safety', 'Hon. Maria Elena L. Germar', '2026-09-25 01:59:32', 0),
(29, 'MAO', 'Municipal Administrator\'s Office', 'Executive', 'Atty. Jhorolar N. Andres', '2026-10-16 13:26:47', 0),
(47, 'POP', 'Population Office', 'Social', 'Hon. Maria Elena L. Germar', '2026-10-16 13:26:47', 0),
(48, 'NUTR', 'Nutrition Office', 'Social', 'Christilina B. Torres', '2026-10-16 13:26:47', 0),
(54, 'NMH', 'Norzagaray Municipal Hostpital', 'Social', 'Dr. Joselito C. Santiago', '2026-10-16 13:26:47', 0),
(56, 'NC', 'Norzagaray College', 'Social', 'Dr. Maria Liberty DG. Pascual', '2026-10-16 13:26:47', 0);

-- --------------------------------------------------------

--
-- Table structure for table `email_verifications`
--

CREATE TABLE `email_verifications` (
  `id` int(10) UNSIGNED NOT NULL,
  `email` varchar(150) NOT NULL,
  `code_hash` varchar(255) NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `expires_at` datetime NOT NULL,
  `verified_at` datetime DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `email_verifications`
--

INSERT INTO `email_verifications` (`id`, `email`, `code_hash`, `attempts`, `expires_at`, `verified_at`, `ip_address`, `created_at`) VALUES
(22, 'cosmoswithin341@gmail.com', '$2y$10$tdFrkGeCFFEqP9DPSCv5QOBkUoBELck1emKyQU4jGlxWvKpx/WTC.', 1, '2026-10-19 20:48:30', '2026-10-19 20:39:10', '::1', '2026-10-19 12:38:30'),
(23, 'cosmoswithin341@gmail.com', '$2y$10$lSg5ZTi2UKcBImHnJiBc3eOntFabihhPHgkyeSpGPHQg3pRZy2Lji', 0, '2026-10-19 21:19:47', '2026-10-19 21:10:30', '::1', '2026-10-19 13:09:47'),
(26, 'cosmoswithin341@gmail.com', '$2y$10$X7lBuWA2yo4h0cPEqS6z7OuKPLEGhXgky09SYXPRRnK1JQirVaM32', 1, '2026-10-20 11:34:18', '2026-10-20 11:24:55', '::1', '2026-10-20 03:24:18'),
(29, 'cosmoswithin341@gmail.com', '$2y$10$zkwo9ap0TG3rJ//9bcAHs.S/z0zeFc8QYZysFI0RY4K/i8DHgR0Pa', 0, '2026-10-20 14:11:30', '2026-10-20 14:01:49', '::1', '2026-10-20 06:01:30');

-- --------------------------------------------------------

--
-- Table structure for table `holidays`
--

CREATE TABLE `holidays` (
  `id` int(10) UNSIGNED NOT NULL,
  `holiday_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `is_recurring` tinyint(1) NOT NULL DEFAULT 0,
  `source` enum('manual','api') NOT NULL DEFAULT 'manual',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `holidays`
--

INSERT INTO `holidays` (`id`, `holiday_date`, `end_date`, `name`, `is_recurring`, `source`, `created_at`) VALUES
(1, '2000-01-01', NULL, 'New Year\'s Day', 1, 'manual', '2026-10-18 08:09:52'),
(2, '2000-04-09', NULL, 'Araw ng Kagitingan', 1, 'manual', '2026-10-18 08:09:52'),
(3, '2000-05-01', NULL, 'Labor Day', 1, 'manual', '2026-10-18 08:09:52'),
(4, '2000-06-12', NULL, 'Independence Day', 1, 'manual', '2026-10-18 08:09:52'),
(5, '2000-08-21', NULL, 'Ninoy Aquino Day', 1, 'manual', '2026-10-18 08:09:52'),
(6, '2000-11-01', NULL, 'All Saints\' Day', 1, 'manual', '2026-10-18 08:09:52'),
(7, '2000-11-30', NULL, 'Bonifacio Day', 1, 'manual', '2026-10-18 08:09:52'),
(8, '2000-12-08', NULL, 'Feast of the Immaculate Conception', 1, 'manual', '2026-10-18 08:09:52'),
(9, '2000-12-25', NULL, 'Christmas Day', 1, 'manual', '2026-10-18 08:09:52'),
(10, '2000-12-30', NULL, 'Rizal Day', 1, 'manual', '2026-10-18 08:09:52'),
(11, '2000-12-31', NULL, 'New Year\'s Eve', 1, 'manual', '2026-10-18 08:09:52');

-- --------------------------------------------------------

--
-- Table structure for table `mail_log`
--

CREATE TABLE `mail_log` (
  `id` int(10) UNSIGNED NOT NULL,
  `recipient` varchar(150) NOT NULL,
  `subject` varchar(200) NOT NULL,
  `status` enum('sent','failed','skipped') NOT NULL,
  `error` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `mail_log`
--

INSERT INTO `mail_log` (`id`, `recipient`, `subject`, `status`, `error`, `created_at`) VALUES
(106, 'admin@norzagaray.gov.ph', 'New request from Juan Dela Cruz: adfa', 'sent', NULL, '2026-10-19 11:53:08'),
(107, 'nikkisanpedro314@gmail.com', 'We\'ve received your appointment request', 'sent', NULL, '2026-10-19 11:53:12'),
(108, 'admin@norzagaray.gov.ph', 'New request from Juan Dela Cruz: serdt', 'sent', NULL, '2026-10-19 11:53:36'),
(109, 'nikkisanpedro314@gmail.com', 'We\'ve received your appointment request', 'sent', NULL, '2026-10-19 11:53:41'),
(117, 'cosmoswithin341@gmail.com', 'Your LGU Norzagaray verification code', 'sent', NULL, '2026-10-19 12:38:35'),
(118, 'cosmoswithin341@gmail.com', 'Your LGU Norzagaray verification code', 'sent', NULL, '2026-10-19 13:09:52'),
(121, 'cosmoswithin341@gmail.com', 'Your LGU Norzagaray verification code', 'sent', NULL, '2026-10-20 03:24:22'),
(125, 'zz-org2@placeholder.norzagaray.local', 'We\'ve received your appointment request', 'skipped', 'Placeholder email address - replace it on the Office Accounts page.', '2026-10-20 05:59:30'),
(126, 'cosmoswithin341@gmail.com', 'Your LGU Norzagaray verification code', 'sent', NULL, '2026-10-20 06:01:35'),
(234, 'admin@norzagaray.gov.ph', 'Business Permit and Licensing Office sent a post for review: Business Permit Renewal Period', 'sent', NULL, '2026-10-20 12:15:39'),
(235, 'bplo@placeholder.norzagaray.local', 'Your post \"Business Permit Renewal Period\" was approved. It is now live.', 'skipped', 'Placeholder email address - replace it on the Office Accounts page.', '2026-10-20 12:20:40'),
(236, 'admin@norzagaray.gov.ph', 'Assessor\'s Office sent a post for review: Extension of tax discount', 'sent', NULL, '2026-10-20 12:57:16');

-- --------------------------------------------------------

--
-- Table structure for table `meetings`
--

CREATE TABLE `meetings` (
  `id` int(10) UNSIGNED NOT NULL,
  `requester_user_id` int(10) UNSIGNED NOT NULL,
  `requester_department_id` int(10) UNSIGNED NOT NULL,
  `title` varchar(200) NOT NULL,
  `agenda` text NOT NULL,
  `venue` varchar(150) DEFAULT NULL,
  `meeting_date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time DEFAULT NULL,
  `status` enum('pending_approval','approved','rejected','cancelled','completed') NOT NULL DEFAULT 'pending_approval',
  `admin_notes` text DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL,
  `reviewed_by` int(10) UNSIGNED DEFAULT NULL,
  `reviewed_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_demo` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `meetings`
--

INSERT INTO `meetings` (`id`, `requester_user_id`, `requester_department_id`, `title`, `agenda`, `venue`, `meeting_date`, `start_time`, `end_time`, `status`, `admin_notes`, `rejection_reason`, `reviewed_by`, `reviewed_at`, `created_at`, `updated_at`, `is_demo`) VALUES
(11, 24, 5, 'Housing Board Monthly Meeting', 'sample agenda', '3rd Floor Yellow Building', '2026-10-30', '09:00:00', '12:00:00', 'approved', NULL, NULL, 1, '2026-10-18 22:24:38', '2026-10-18 13:37:26', '2026-10-18 14:24:38', 0),
(101, 22, 15, 'Livelihood program planning', 'Coordination and planning for the upcoming activities of the offices involved.', 'Conference Room', '2025-11-12', '09:00:00', '10:30:00', 'completed', NULL, NULL, 1, '2025-11-05 11:00:00', '2025-11-05 02:00:00', '2025-11-05 02:00:00', 1),
(102, 37, 10, 'Monthly coordination meeting', 'Coordination and planning for the upcoming activities of the offices involved.', 'Conference Room', '2025-11-18', '09:00:00', '10:30:00', 'completed', NULL, NULL, 1, '2025-11-13 10:00:00', '2025-11-11 01:00:00', '2025-11-11 01:00:00', 1),
(103, 15, 21, 'Disaster-readiness planning', 'Coordination and planning for the upcoming activities of the offices involved.', 'Conference Room', '2025-11-21', '09:00:00', '10:30:00', 'completed', NULL, NULL, 1, '2025-11-16 11:00:00', '2025-11-14 02:00:00', '2025-11-14 02:00:00', 1),
(104, 22, 15, 'Monthly coordination meeting', 'Coordination and planning for the upcoming activities of the offices involved.', 'Conference Room', '2025-12-02', '09:00:00', '10:30:00', 'completed', NULL, NULL, 1, '2025-11-28 16:00:00', '2025-11-25 07:00:00', '2025-11-25 07:00:00', 1),
(105, 25, 14, 'Health program planning', 'Coordination and planning for the upcoming activities of the offices involved.', 'Conference Room', '2025-12-03', '09:00:00', '10:30:00', 'completed', NULL, NULL, 1, '2025-11-26 13:00:00', '2025-11-26 04:00:00', '2025-11-26 04:00:00', 1),
(106, 24, 5, 'Disaster-readiness planning', 'Coordination and planning for the upcoming activities of the offices involved.', 'Conference Room', '2025-12-16', '09:00:00', '10:30:00', 'completed', NULL, NULL, 1, '2025-12-10 09:00:00', '2025-12-09 00:00:00', '2025-12-09 00:00:00', 1),
(107, 24, 5, 'Preparation for the town fiesta', 'Coordination and planning for the upcoming activities of the offices involved.', 'Conference Room', '2025-12-18', '09:00:00', '10:30:00', 'completed', NULL, NULL, 1, '2025-12-12 12:00:00', '2025-12-11 03:00:00', '2025-12-11 03:00:00', 1),
(108, 15, 21, 'Monthly coordination meeting', 'Coordination and planning for the upcoming activities of the offices involved.', 'Conference Room', '2025-12-26', '09:00:00', '10:30:00', 'completed', NULL, NULL, 1, '2025-12-20 15:00:00', '2025-12-19 06:00:00', '2025-12-19 06:00:00', 1),
(109, 22, 15, 'Monthly coordination meeting', 'Coordination and planning for the upcoming activities of the offices involved.', 'Conference Room', '2026-01-06', '09:00:00', '10:30:00', 'rejected', NULL, 'The room is not available on that date.', 1, '2026-01-02 14:00:00', '2025-12-30 05:00:00', '2025-12-30 05:00:00', 1),
(110, 20, 25, 'Monthly coordination meeting', 'Coordination and planning for the upcoming activities of the offices involved.', 'Conference Room', '2026-01-09', '09:00:00', '10:30:00', 'completed', NULL, NULL, 1, '2026-01-05 11:00:00', '2026-01-02 02:00:00', '2026-01-02 02:00:00', 1),
(111, 11, 16, 'Disaster-readiness planning', 'Coordination and planning for the upcoming activities of the offices involved.', 'Conference Room', '2026-01-16', '09:00:00', '10:30:00', 'completed', NULL, NULL, 1, '2026-01-10 15:00:00', '2026-01-09 06:00:00', '2026-01-09 06:00:00', 1),
(112, 36, 23, 'Livelihood program planning', 'Coordination and planning for the upcoming activities of the offices involved.', 'Conference Room', '2026-02-13', '09:00:00', '10:30:00', 'completed', NULL, NULL, 1, '2026-02-09 16:00:00', '2026-02-06 07:00:00', '2026-02-06 07:00:00', 1),
(113, 25, 14, 'Livelihood program planning', 'Coordination and planning for the upcoming activities of the offices involved.', 'Conference Room', '2026-02-20', '09:00:00', '10:30:00', 'completed', NULL, NULL, 1, '2026-02-16 10:00:00', '2026-02-13 01:00:00', '2026-02-13 01:00:00', 1),
(114, 24, 5, 'Livelihood program planning', 'Coordination and planning for the upcoming activities of the offices involved.', 'Conference Room', '2026-02-24', '09:00:00', '10:30:00', 'completed', NULL, NULL, 1, '2026-02-20 10:00:00', '2026-02-17 01:00:00', '2026-02-17 01:00:00', 1),
(115, 20, 25, 'Monthly coordination meeting', 'Coordination and planning for the upcoming activities of the offices involved.', 'Conference Room', '2026-03-12', '09:00:00', '10:30:00', 'completed', NULL, NULL, 1, '2026-03-05 15:00:00', '2026-03-05 06:00:00', '2026-03-05 06:00:00', 1),
(116, 36, 23, 'Preparation for the town fiesta', 'Coordination and planning for the upcoming activities of the offices involved.', 'Conference Room', '2026-04-03', '09:00:00', '10:30:00', 'completed', NULL, NULL, 1, '2026-03-28 16:00:00', '2026-03-27 07:00:00', '2026-03-27 07:00:00', 1),
(117, 24, 5, 'Budget review for the quarter', 'Coordination and planning for the upcoming activities of the offices involved.', 'Conference Room', '2026-04-15', '09:00:00', '10:30:00', 'cancelled', NULL, NULL, NULL, NULL, '2026-04-08 02:00:00', '2026-04-08 02:00:00', 1),
(118, 21, 22, 'Budget review for the quarter', 'Coordination and planning for the upcoming activities of the offices involved.', 'Conference Room', '2026-04-16', '09:00:00', '10:30:00', 'cancelled', NULL, NULL, NULL, NULL, '2026-04-09 05:00:00', '2026-04-09 05:00:00', 1),
(119, 22, 15, 'Disaster-readiness planning', 'Coordination and planning for the upcoming activities of the offices involved.', 'Conference Room', '2026-04-17', '09:00:00', '10:30:00', 'completed', NULL, NULL, 1, '2026-04-11 15:00:00', '2026-04-10 06:00:00', '2026-04-10 06:00:00', 1),
(120, 25, 14, 'Preparation for the town fiesta', 'Coordination and planning for the upcoming activities of the offices involved.', 'Conference Room', '2026-04-20', '09:00:00', '10:30:00', 'completed', NULL, NULL, 1, '2026-04-15 14:00:00', '2026-04-13 05:00:00', '2026-04-13 05:00:00', 1),
(121, 37, 10, 'Preparation for the town fiesta', 'Coordination and planning for the upcoming activities of the offices involved.', 'Conference Room', '2026-04-30', '09:00:00', '10:30:00', 'completed', NULL, NULL, 1, '2026-04-23 09:00:00', '2026-04-23 00:00:00', '2026-04-23 00:00:00', 1),
(122, 14, 9, 'Budget review for the quarter', 'Coordination and planning for the upcoming activities of the offices involved.', 'Conference Room', '2026-05-06', '09:00:00', '10:30:00', 'completed', NULL, NULL, 1, '2026-05-02 11:00:00', '2026-04-29 02:00:00', '2026-04-29 02:00:00', 1),
(123, 37, 10, 'Livelihood program planning', 'Coordination and planning for the upcoming activities of the offices involved.', 'Conference Room', '2026-05-07', '09:00:00', '10:30:00', 'rejected', NULL, 'The room is not available on that date.', 1, '2026-05-01 11:00:00', '2026-04-30 02:00:00', '2026-04-30 02:00:00', 1),
(124, 21, 22, 'Budget review for the quarter', 'Coordination and planning for the upcoming activities of the offices involved.', 'Conference Room', '2026-05-12', '09:00:00', '10:30:00', 'rejected', NULL, 'The room is not available on that date.', 1, '2026-05-05 14:00:00', '2026-05-05 05:00:00', '2026-05-05 05:00:00', 1),
(125, 22, 15, 'Disaster-readiness planning', 'Coordination and planning for the upcoming activities of the offices involved.', 'Conference Room', '2026-05-27', '09:00:00', '10:30:00', 'completed', NULL, NULL, 1, '2026-05-21 12:00:00', '2026-05-20 03:00:00', '2026-05-20 03:00:00', 1),
(126, 14, 9, 'Livelihood program planning', 'Coordination and planning for the upcoming activities of the offices involved.', 'Conference Room', '2026-06-02', '09:00:00', '10:30:00', 'completed', NULL, NULL, 1, '2026-05-28 09:00:00', '2026-05-26 00:00:00', '2026-05-26 00:00:00', 1),
(127, 20, 25, 'Budget review for the quarter', 'Coordination and planning for the upcoming activities of the offices involved.', 'Conference Room', '2026-06-03', '09:00:00', '10:30:00', 'completed', NULL, NULL, 1, '2026-05-28 12:00:00', '2026-05-27 03:00:00', '2026-05-27 03:00:00', 1),
(128, 21, 22, 'Disaster-readiness planning', 'Coordination and planning for the upcoming activities of the offices involved.', 'Conference Room', '2026-06-18', '09:00:00', '10:30:00', 'completed', NULL, NULL, 1, '2026-06-13 14:00:00', '2026-06-11 05:00:00', '2026-06-11 05:00:00', 1),
(129, 11, 16, 'Monthly coordination meeting', 'Coordination and planning for the upcoming activities of the offices involved.', 'Conference Room', '2026-06-23', '09:00:00', '10:30:00', 'completed', NULL, NULL, 1, '2026-06-18 16:00:00', '2026-06-16 07:00:00', '2026-06-16 07:00:00', 1),
(130, 37, 10, 'Health program planning', 'Coordination and planning for the upcoming activities of the offices involved.', 'Conference Room', '2026-08-06', '09:00:00', '10:30:00', 'completed', NULL, NULL, 1, '2026-07-30 12:00:00', '2026-07-30 03:00:00', '2026-07-30 03:00:00', 1),
(131, 22, 15, 'Disaster-readiness planning', 'Coordination and planning for the upcoming activities of the offices involved.', 'Conference Room', '2026-08-20', '09:00:00', '10:30:00', 'completed', NULL, NULL, 1, '2026-08-14 12:00:00', '2026-08-13 03:00:00', '2026-08-13 03:00:00', 1),
(132, 22, 15, 'Disaster-readiness planning', 'Coordination and planning for the upcoming activities of the offices involved.', 'Conference Room', '2026-08-24', '09:00:00', '10:30:00', 'completed', NULL, NULL, 1, '2026-08-18 11:00:00', '2026-08-17 02:00:00', '2026-08-17 02:00:00', 1),
(133, 24, 5, 'Livelihood program planning', 'Coordination and planning for the upcoming activities of the offices involved.', 'Conference Room', '2026-09-01', '09:00:00', '10:30:00', 'cancelled', NULL, NULL, NULL, NULL, '2026-08-25 05:00:00', '2026-08-25 05:00:00', 1),
(134, 11, 16, 'Preparation for the town fiesta', 'Coordination and planning for the upcoming activities of the offices involved.', 'Conference Room', '2026-09-04', '09:00:00', '10:30:00', 'completed', NULL, NULL, 1, '2026-08-28 10:00:00', '2026-08-28 01:00:00', '2026-08-28 01:00:00', 1),
(135, 36, 23, 'Monthly coordination meeting', 'Coordination and planning for the upcoming activities of the offices involved.', 'Conference Room', '2026-09-11', '09:00:00', '10:30:00', 'completed', NULL, NULL, 1, '2026-09-07 13:00:00', '2026-09-04 04:00:00', '2026-09-04 04:00:00', 1),
(136, 11, 16, 'Preparation for the town fiesta', 'Coordination and planning for the upcoming activities of the offices involved.', 'Conference Room', '2026-09-15', '09:00:00', '10:30:00', 'cancelled', NULL, NULL, NULL, NULL, '2026-09-08 00:00:00', '2026-09-08 00:00:00', 1),
(137, 36, 23, 'Budget review for the quarter', 'Coordination and planning for the upcoming activities of the offices involved.', 'Conference Room', '2026-09-18', '09:00:00', '10:30:00', 'completed', NULL, NULL, 1, '2026-09-14 11:00:00', '2026-09-11 02:00:00', '2026-09-11 02:00:00', 1),
(138, 15, 21, 'Health program planning', 'Coordination and planning for the upcoming activities of the offices involved.', 'Conference Room', '2026-09-21', '09:00:00', '10:30:00', 'completed', NULL, NULL, 1, '2026-09-15 15:00:00', '2026-09-14 06:00:00', '2026-09-14 06:00:00', 1),
(139, 36, 23, 'Disaster-readiness planning', 'Coordination and planning for the upcoming activities of the offices involved.', 'Conference Room', '2026-10-01', '09:00:00', '10:30:00', 'completed', NULL, NULL, 1, '2026-09-25 12:00:00', '2026-09-24 03:00:00', '2026-09-24 03:00:00', 1),
(140, 24, 5, 'Livelihood program planning', 'Coordination and planning for the upcoming activities of the offices involved.', 'Conference Room', '2026-10-05', '09:00:00', '10:30:00', 'rejected', NULL, 'The room is not available on that date.', 1, '2026-09-30 09:00:00', '2026-09-28 00:00:00', '2026-09-28 00:00:00', 1),
(141, 20, 25, 'Health program planning', 'Coordination and planning for the upcoming activities of the offices involved.', 'Conference Room', '2026-10-13', '09:00:00', '10:30:00', 'completed', NULL, NULL, 1, '2026-10-06 11:00:00', '2026-10-06 02:00:00', '2026-10-06 02:00:00', 1),
(142, 20, 25, 'Preparation for the town fiesta', 'Coordination and planning for the upcoming activities of the offices involved.', 'Conference Room', '2026-10-27', '09:00:00', '10:30:00', 'pending_approval', NULL, NULL, NULL, NULL, '2026-10-20 07:00:00', '2026-10-20 07:00:00', 1);

-- --------------------------------------------------------

--
-- Table structure for table `meeting_invitees`
--

CREATE TABLE `meeting_invitees` (
  `meeting_id` int(10) UNSIGNED NOT NULL,
  `department_id` int(10) UNSIGNED NOT NULL,
  `response` enum('pending','acknowledged') NOT NULL DEFAULT 'pending',
  `response_note` varchar(255) DEFAULT NULL,
  `responded_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `meeting_invitees`
--

INSERT INTO `meeting_invitees` (`meeting_id`, `department_id`, `response`, `response_note`, `responded_at`) VALUES
(11, 1, 'pending', NULL, NULL),
(11, 2, 'pending', NULL, NULL),
(11, 3, 'acknowledged', NULL, '2026-10-18 22:25:09'),
(11, 4, 'pending', NULL, NULL),
(11, 6, 'pending', NULL, NULL),
(11, 8, 'pending', NULL, NULL),
(11, 9, 'pending', NULL, NULL),
(11, 10, 'pending', NULL, NULL),
(11, 11, 'pending', NULL, NULL),
(11, 12, 'pending', NULL, NULL),
(11, 13, 'pending', NULL, NULL),
(11, 14, 'pending', NULL, NULL),
(11, 15, 'pending', NULL, NULL),
(11, 16, 'pending', NULL, NULL),
(11, 17, 'pending', NULL, NULL),
(11, 18, 'pending', NULL, NULL),
(11, 19, 'pending', NULL, NULL),
(11, 20, 'pending', NULL, NULL),
(11, 21, 'pending', NULL, NULL),
(11, 22, 'pending', NULL, NULL),
(11, 23, 'pending', NULL, NULL),
(11, 25, 'pending', NULL, NULL),
(11, 26, 'pending', NULL, NULL),
(11, 47, 'pending', NULL, NULL),
(11, 48, 'pending', NULL, NULL),
(11, 54, 'pending', NULL, NULL),
(11, 56, 'pending', NULL, NULL),
(101, 1, 'acknowledged', NULL, '2025-11-05 13:00:00'),
(101, 10, 'pending', NULL, NULL),
(101, 14, 'acknowledged', NULL, '2025-11-05 13:00:00'),
(101, 18, 'pending', NULL, NULL),
(101, 20, 'acknowledged', NULL, '2025-11-05 13:00:00'),
(101, 23, 'acknowledged', NULL, '2025-11-05 13:00:00'),
(101, 26, 'acknowledged', NULL, '2025-11-05 13:00:00'),
(101, 54, 'pending', NULL, NULL),
(102, 11, 'acknowledged', NULL, '2025-11-13 12:00:00'),
(102, 13, 'acknowledged', NULL, '2025-11-13 12:00:00'),
(102, 15, 'acknowledged', NULL, '2025-11-13 12:00:00'),
(102, 26, 'acknowledged', NULL, '2025-11-13 12:00:00'),
(102, 29, 'pending', NULL, NULL),
(102, 47, 'acknowledged', NULL, '2025-11-13 12:00:00'),
(103, 16, 'acknowledged', NULL, '2025-11-16 13:00:00'),
(103, 25, 'pending', NULL, NULL),
(103, 48, 'acknowledged', NULL, '2025-11-16 13:00:00'),
(104, 5, 'acknowledged', NULL, '2025-11-28 18:00:00'),
(104, 17, 'acknowledged', NULL, '2025-11-28 18:00:00'),
(105, 4, 'acknowledged', NULL, '2025-11-26 15:00:00'),
(105, 19, 'acknowledged', NULL, '2025-11-26 15:00:00'),
(105, 20, 'acknowledged', NULL, '2025-11-26 15:00:00'),
(105, 21, 'acknowledged', NULL, '2025-11-26 15:00:00'),
(105, 22, 'acknowledged', NULL, '2025-11-26 15:00:00'),
(106, 16, 'acknowledged', NULL, '2025-12-10 11:00:00'),
(106, 17, 'acknowledged', NULL, '2025-12-10 11:00:00'),
(106, 22, 'acknowledged', NULL, '2025-12-10 11:00:00'),
(106, 26, 'acknowledged', NULL, '2025-12-10 11:00:00'),
(106, 29, 'acknowledged', NULL, '2025-12-10 11:00:00'),
(106, 54, 'acknowledged', NULL, '2025-12-10 11:00:00'),
(107, 2, 'pending', NULL, NULL),
(107, 14, 'acknowledged', NULL, '2025-12-12 14:00:00'),
(107, 17, 'acknowledged', NULL, '2025-12-12 14:00:00'),
(107, 18, 'acknowledged', NULL, '2025-12-12 14:00:00'),
(107, 19, 'acknowledged', NULL, '2025-12-12 14:00:00'),
(107, 22, 'acknowledged', NULL, '2025-12-12 14:00:00'),
(107, 29, 'acknowledged', NULL, '2025-12-12 14:00:00'),
(107, 48, 'acknowledged', NULL, '2025-12-12 14:00:00'),
(108, 5, 'pending', NULL, NULL),
(108, 14, 'acknowledged', NULL, '2025-12-20 17:00:00'),
(108, 16, 'pending', NULL, NULL),
(108, 20, 'acknowledged', NULL, '2025-12-20 17:00:00'),
(109, 17, 'pending', NULL, NULL),
(109, 54, 'pending', NULL, NULL),
(109, 56, 'pending', NULL, NULL),
(110, 18, 'acknowledged', NULL, '2026-01-05 13:00:00'),
(110, 19, 'acknowledged', NULL, '2026-01-05 13:00:00'),
(110, 22, 'pending', NULL, NULL),
(110, 26, 'acknowledged', NULL, '2026-01-05 13:00:00'),
(110, 47, 'acknowledged', NULL, '2026-01-05 13:00:00'),
(111, 3, 'acknowledged', NULL, '2026-01-10 17:00:00'),
(111, 5, 'pending', NULL, NULL),
(111, 10, 'acknowledged', NULL, '2026-01-10 17:00:00'),
(111, 14, 'acknowledged', NULL, '2026-01-10 17:00:00'),
(111, 21, 'acknowledged', NULL, '2026-01-10 17:00:00'),
(111, 23, 'acknowledged', NULL, '2026-01-10 17:00:00'),
(111, 48, 'pending', NULL, NULL),
(111, 56, 'pending', NULL, NULL),
(112, 16, 'acknowledged', NULL, '2026-02-09 18:00:00'),
(112, 18, 'acknowledged', NULL, '2026-02-09 18:00:00'),
(113, 1, 'pending', NULL, NULL),
(113, 9, 'pending', NULL, NULL),
(113, 13, 'acknowledged', NULL, '2026-02-16 12:00:00'),
(113, 15, 'pending', NULL, NULL),
(113, 23, 'acknowledged', NULL, '2026-02-16 12:00:00'),
(113, 26, 'acknowledged', NULL, '2026-02-16 12:00:00'),
(113, 47, 'acknowledged', NULL, '2026-02-16 12:00:00'),
(114, 9, 'acknowledged', NULL, '2026-02-20 12:00:00'),
(114, 12, 'acknowledged', NULL, '2026-02-20 12:00:00'),
(114, 15, 'acknowledged', NULL, '2026-02-20 12:00:00'),
(114, 17, 'pending', NULL, NULL),
(114, 21, 'acknowledged', NULL, '2026-02-20 12:00:00'),
(114, 47, 'acknowledged', NULL, '2026-02-20 12:00:00'),
(114, 48, 'acknowledged', NULL, '2026-02-20 12:00:00'),
(114, 56, 'acknowledged', NULL, '2026-02-20 12:00:00'),
(115, 1, 'acknowledged', NULL, '2026-03-05 17:00:00'),
(115, 8, 'acknowledged', NULL, '2026-03-05 17:00:00'),
(115, 9, 'acknowledged', NULL, '2026-03-05 17:00:00'),
(115, 54, 'pending', NULL, NULL),
(116, 3, 'acknowledged', NULL, '2026-03-28 18:00:00'),
(116, 6, 'pending', NULL, NULL),
(117, 9, 'pending', NULL, NULL),
(117, 22, 'pending', NULL, NULL),
(117, 56, 'pending', NULL, NULL),
(118, 2, 'pending', NULL, NULL),
(118, 15, 'pending', NULL, NULL),
(118, 23, 'pending', NULL, NULL),
(118, 54, 'pending', NULL, NULL),
(119, 9, 'pending', NULL, NULL),
(119, 13, 'pending', NULL, NULL),
(120, 3, 'acknowledged', NULL, '2026-04-15 16:00:00'),
(120, 21, 'acknowledged', NULL, '2026-04-15 16:00:00'),
(121, 6, 'acknowledged', NULL, '2026-04-23 11:00:00'),
(121, 11, 'pending', NULL, NULL),
(121, 18, 'acknowledged', NULL, '2026-04-23 11:00:00'),
(121, 25, 'acknowledged', NULL, '2026-04-23 11:00:00'),
(121, 56, 'pending', NULL, NULL),
(122, 12, 'acknowledged', NULL, '2026-05-02 13:00:00'),
(122, 22, 'acknowledged', NULL, '2026-05-02 13:00:00'),
(122, 25, 'acknowledged', NULL, '2026-05-02 13:00:00'),
(122, 26, 'acknowledged', NULL, '2026-05-02 13:00:00'),
(122, 29, 'acknowledged', NULL, '2026-05-02 13:00:00'),
(123, 5, 'pending', NULL, NULL),
(123, 18, 'pending', NULL, NULL),
(123, 48, 'pending', NULL, NULL),
(123, 56, 'pending', NULL, NULL),
(124, 9, 'pending', NULL, NULL),
(124, 16, 'pending', NULL, NULL),
(124, 26, 'pending', NULL, NULL),
(125, 2, 'acknowledged', NULL, '2026-05-21 14:00:00'),
(125, 9, 'acknowledged', NULL, '2026-05-21 14:00:00'),
(125, 18, 'acknowledged', NULL, '2026-05-21 14:00:00'),
(125, 20, 'acknowledged', NULL, '2026-05-21 14:00:00'),
(125, 23, 'acknowledged', NULL, '2026-05-21 14:00:00'),
(125, 29, 'acknowledged', NULL, '2026-05-21 14:00:00'),
(126, 19, 'acknowledged', NULL, '2026-05-28 11:00:00'),
(126, 20, 'pending', NULL, NULL),
(126, 47, 'pending', NULL, NULL),
(127, 5, 'acknowledged', NULL, '2026-05-28 14:00:00'),
(127, 9, 'acknowledged', NULL, '2026-05-28 14:00:00'),
(127, 12, 'acknowledged', NULL, '2026-05-28 14:00:00'),
(127, 16, 'acknowledged', NULL, '2026-05-28 14:00:00'),
(127, 20, 'acknowledged', NULL, '2026-05-28 14:00:00'),
(127, 22, 'acknowledged', NULL, '2026-05-28 14:00:00'),
(128, 3, 'acknowledged', NULL, '2026-06-13 16:00:00'),
(128, 4, 'pending', NULL, NULL),
(128, 5, 'acknowledged', NULL, '2026-06-13 16:00:00'),
(128, 6, 'acknowledged', NULL, '2026-06-13 16:00:00'),
(128, 48, 'acknowledged', NULL, '2026-06-13 16:00:00'),
(129, 2, 'acknowledged', NULL, '2026-06-18 18:00:00'),
(129, 6, 'acknowledged', NULL, '2026-06-18 18:00:00'),
(129, 15, 'acknowledged', NULL, '2026-06-18 18:00:00'),
(130, 1, 'acknowledged', NULL, '2026-07-30 14:00:00'),
(130, 12, 'acknowledged', NULL, '2026-07-30 14:00:00'),
(130, 15, 'acknowledged', NULL, '2026-07-30 14:00:00'),
(130, 26, 'acknowledged', NULL, '2026-07-30 14:00:00'),
(130, 48, 'acknowledged', NULL, '2026-07-30 14:00:00'),
(131, 1, 'acknowledged', NULL, '2026-08-14 14:00:00'),
(131, 14, 'acknowledged', NULL, '2026-08-14 14:00:00'),
(132, 3, 'acknowledged', NULL, '2026-08-18 13:00:00'),
(132, 4, 'acknowledged', NULL, '2026-08-18 13:00:00'),
(132, 11, 'pending', NULL, NULL),
(132, 26, 'acknowledged', NULL, '2026-08-18 13:00:00'),
(132, 48, 'acknowledged', NULL, '2026-08-18 13:00:00'),
(133, 2, 'pending', NULL, NULL),
(133, 4, 'pending', NULL, NULL),
(133, 23, 'pending', NULL, NULL),
(133, 56, 'pending', NULL, NULL),
(134, 8, 'pending', NULL, NULL),
(134, 10, 'acknowledged', NULL, '2026-08-28 12:00:00'),
(134, 56, 'acknowledged', NULL, '2026-08-28 12:00:00'),
(135, 1, 'pending', NULL, NULL),
(135, 2, 'acknowledged', NULL, '2026-09-07 15:00:00'),
(135, 4, 'acknowledged', NULL, '2026-09-07 15:00:00'),
(135, 12, 'acknowledged', NULL, '2026-09-07 15:00:00'),
(135, 16, 'acknowledged', NULL, '2026-09-07 15:00:00'),
(135, 19, 'acknowledged', NULL, '2026-09-07 15:00:00'),
(135, 21, 'acknowledged', NULL, '2026-09-07 15:00:00'),
(135, 47, 'acknowledged', NULL, '2026-09-07 15:00:00'),
(136, 9, 'pending', NULL, NULL),
(136, 12, 'pending', NULL, NULL),
(136, 18, 'pending', NULL, NULL),
(137, 5, 'acknowledged', NULL, '2026-09-14 13:00:00'),
(137, 8, 'acknowledged', NULL, '2026-09-14 13:00:00'),
(138, 2, 'acknowledged', NULL, '2026-09-15 17:00:00'),
(138, 3, 'acknowledged', NULL, '2026-09-15 17:00:00'),
(138, 5, 'pending', NULL, NULL),
(138, 11, 'acknowledged', NULL, '2026-09-15 17:00:00'),
(138, 14, 'acknowledged', NULL, '2026-09-15 17:00:00'),
(138, 25, 'pending', NULL, NULL),
(138, 54, 'pending', NULL, NULL),
(139, 2, 'acknowledged', NULL, '2026-09-25 14:00:00'),
(139, 16, 'acknowledged', NULL, '2026-09-25 14:00:00'),
(139, 18, 'pending', NULL, NULL),
(139, 25, 'acknowledged', NULL, '2026-09-25 14:00:00'),
(139, 26, 'pending', NULL, NULL),
(140, 3, 'pending', NULL, NULL),
(140, 13, 'pending', NULL, NULL),
(140, 17, 'pending', NULL, NULL),
(140, 21, 'pending', NULL, NULL),
(140, 22, 'pending', NULL, NULL),
(140, 54, 'pending', NULL, NULL),
(140, 56, 'pending', NULL, NULL),
(141, 12, 'acknowledged', NULL, '2026-10-06 13:00:00'),
(141, 14, 'acknowledged', NULL, '2026-10-06 13:00:00'),
(141, 15, 'acknowledged', NULL, '2026-10-06 13:00:00'),
(141, 26, 'acknowledged', NULL, '2026-10-06 13:00:00'),
(141, 47, 'acknowledged', NULL, '2026-10-06 13:00:00'),
(141, 56, 'acknowledged', NULL, '2026-10-06 13:00:00'),
(142, 9, 'pending', NULL, NULL),
(142, 12, 'pending', NULL, NULL),
(142, 13, 'pending', NULL, NULL),
(142, 29, 'pending', NULL, NULL),
(142, 47, 'pending', NULL, NULL),
(142, 54, 'pending', NULL, NULL),
(142, 56, 'pending', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `news_categories`
--

CREATE TABLE `news_categories` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(60) NOT NULL,
  `color` varchar(10) NOT NULL DEFAULT 'blue',
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `news_categories`
--

INSERT INTO `news_categories` (`id`, `name`, `color`, `sort_order`, `is_active`, `created_at`) VALUES
(1, 'Official Announcement', 'blue', 1, 1, '2026-10-20 06:59:36'),
(2, 'Emergency Advisory', 'red', 2, 1, '2026-10-20 06:59:36'),
(3, 'Public Health Update', 'green', 3, 1, '2026-10-20 06:59:36'),
(4, 'Holiday Advisory', 'amber', 4, 1, '2026-10-20 06:59:36'),
(5, 'Public Notice', 'blue', 5, 1, '2026-10-20 06:59:36'),
(6, 'Event', 'purple', 6, 1, '2026-10-20 06:59:36'),
(7, 'Program and Services', 'blue', 7, 1, '2026-10-20 06:59:36'),
(8, 'Community News', 'blue', 8, 1, '2026-10-20 06:59:36');

-- --------------------------------------------------------

--
-- Table structure for table `news_posts`
--

CREATE TABLE `news_posts` (
  `id` int(10) UNSIGNED NOT NULL,
  `category` varchar(60) NOT NULL DEFAULT 'Official Announcement',
  `post_type` varchar(30) NOT NULL DEFAULT 'news',
  `department_id` int(10) UNSIGNED DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `published_at` date NOT NULL,
  `go_live_at` datetime DEFAULT NULL,
  `show_until` datetime DEFAULT NULL,
  `is_activity` tinyint(1) NOT NULL DEFAULT 0,
  `starts_at` datetime DEFAULT NULL,
  `ends_at` datetime DEFAULT NULL,
  `venue` varchar(150) DEFAULT NULL,
  `requirements` text DEFAULT NULL,
  `contact_info` varchar(150) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_demo` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `news_posts`
--

INSERT INTO `news_posts` (`id`, `category`, `post_type`, `department_id`, `title`, `description`, `image`, `is_featured`, `published_at`, `go_live_at`, `show_until`, `is_activity`, `starts_at`, `ends_at`, `venue`, `requirements`, `contact_info`, `created_at`, `updated_at`, `is_demo`) VALUES
(2, 'Official Announcement', 'news', NULL, 'Norzagaray Casayahan Music Festival', 'An unforgettable night of live music in Norzagaray, Bulacan, featuring the iconic Filipino band Orange & Lemons!. Experience their timeless hits, sing along to your favorite songs, and enjoy an electrifying evening filled with great music, good vibes, and unforgettable memories. Don’t miss the chance to see Orange & Lemons live!', 'assets/uploads/news/db5069a940d84068d39e2c80.jpg', 0, '2026-08-16', NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, '2026-08-16 09:28:32', '2026-10-20 06:15:28', 0),
(30, 'Public Notice', 'announcement', 9, 'Business Permit Renewal Period', 'Business renewal shall commence on January 1, 2027 until February 20, 2027 only. Please be guided accordingly.', 'assets/uploads/news/d1560fc6afda59194d1210a1.jpg', 0, '2026-10-20', '2026-10-20 20:20:40', '2027-02-20 17:00:00', 1, '2027-01-01 08:00:00', '2027-02-20 17:00:00', 'Business Permit and Licensing Office', 'Valid ID\nBarangay Business Clearance\nOld Permit', NULL, '2026-10-20 12:20:40', '2026-10-20 12:20:40', 0),
(135, 'Holiday Advisory', 'announcement', 23, 'Reminder for tax payment', 'Residents are advised of the following: reminder for tax payment. For questions, please visit or contact the office concerned.', NULL, 0, '2025-11-21', '2025-11-21 14:00:00', '2025-11-26 14:00:00', 0, NULL, NULL, NULL, NULL, NULL, '2025-11-20 06:00:00', '2026-10-21 06:39:39', 1),
(136, 'Program and Services', 'announcement', 15, 'Town fiesta activities', 'Residents are advised of the following: town fiesta activities. For questions, please visit or contact the office concerned.', NULL, 0, '2025-11-27', '2025-11-27 11:00:00', '2025-12-02 11:00:00', 0, NULL, NULL, NULL, NULL, NULL, '2025-11-26 03:00:00', '2026-10-21 06:39:39', 1),
(137, 'Public Notice', 'news', 23, 'Road repair advisory', 'Residents are advised of the following: road repair advisory. For questions, please visit or contact the office concerned.', NULL, 0, '2025-12-05', '2025-12-05 11:00:00', '2025-12-10 11:00:00', 0, NULL, NULL, NULL, NULL, NULL, '2025-12-02 03:00:00', '2026-10-21 06:39:39', 1),
(138, 'Emergency Advisory', 'news', 25, 'Free medical mission', 'Residents are advised of the following: free medical mission. For questions, please visit or contact the office concerned.', NULL, 0, '2025-12-05', '2025-12-05 15:00:00', '2025-12-10 15:00:00', 0, NULL, NULL, NULL, NULL, NULL, '2025-12-04 07:00:00', '2026-10-21 06:39:39', 1),
(139, 'Program and Services', 'announcement', 10, 'Road repair advisory', 'Residents are advised of the following: road repair advisory. For questions, please visit or contact the office concerned.', NULL, 0, '2025-12-08', '2025-12-08 11:00:00', '2025-12-13 11:00:00', 0, NULL, NULL, NULL, NULL, NULL, '2025-12-05 03:00:00', '2026-10-21 06:39:39', 1),
(140, 'Event', 'announcement', 1, 'Road repair advisory', 'Residents are advised of the following: road repair advisory. For questions, please visit or contact the office concerned.', NULL, 0, '2025-12-13', '2025-12-13 13:00:00', '2025-12-18 13:00:00', 0, NULL, NULL, NULL, NULL, NULL, '2025-12-09 05:00:00', '2026-10-21 06:39:39', 1),
(141, 'Public Notice', 'news', 16, 'Livelihood training for residents', 'Residents are advised of the following: livelihood training for residents. For questions, please visit or contact the office concerned.', NULL, 0, '2025-12-27', '2025-12-27 08:00:00', '2026-01-01 08:00:00', 0, NULL, NULL, NULL, NULL, NULL, '2025-12-23 00:00:00', '2026-10-21 06:39:39', 1),
(142, 'Official Announcement', 'announcement', 14, 'Weather advisory', 'Residents are advised of the following: weather advisory. For questions, please visit or contact the office concerned.', NULL, 0, '2025-12-31', '2025-12-31 08:00:00', '2026-01-05 08:00:00', 0, NULL, NULL, NULL, NULL, NULL, '2025-12-29 00:00:00', '2026-10-21 06:39:39', 1),
(143, 'Official Announcement', 'announcement', 14, 'Free medical mission', 'Residents are advised of the following: free medical mission. For questions, please visit or contact the office concerned.', NULL, 0, '2026-01-02', '2026-01-02 14:00:00', '2026-01-07 14:00:00', 0, NULL, NULL, NULL, NULL, NULL, '2025-12-30 06:00:00', '2026-10-21 06:39:39', 1),
(144, 'Event', 'announcement', 25, 'Road repair advisory', 'Residents are advised of the following: road repair advisory. For questions, please visit or contact the office concerned.', NULL, 0, '2026-01-12', '2026-01-12 09:00:00', '2026-01-17 09:00:00', 0, NULL, NULL, NULL, NULL, NULL, '2026-01-09 01:00:00', '2026-10-21 06:39:39', 1),
(145, 'Event', 'announcement', 23, 'Weather advisory', 'Residents are advised of the following: weather advisory. For questions, please visit or contact the office concerned.', NULL, 0, '2026-02-04', '2026-02-04 10:00:00', '2026-02-09 10:00:00', 0, NULL, NULL, NULL, NULL, NULL, '2026-02-03 02:00:00', '2026-10-21 06:39:39', 1),
(146, 'Event', 'news', 14, 'Road repair advisory', 'Residents are advised of the following: road repair advisory. For questions, please visit or contact the office concerned.', NULL, 0, '2026-02-05', '2026-02-05 11:00:00', '2026-02-10 11:00:00', 0, NULL, NULL, NULL, NULL, NULL, '2026-02-04 03:00:00', '2026-10-21 06:39:39', 1),
(147, 'Program and Services', 'news', 23, 'Free medical mission', 'Residents are advised of the following: free medical mission. For questions, please visit or contact the office concerned.', NULL, 0, '2026-02-09', '2026-02-09 15:00:00', '2026-02-14 15:00:00', 0, NULL, NULL, NULL, NULL, NULL, '2026-02-05 07:00:00', '2026-10-21 06:39:39', 1),
(148, 'Official Announcement', 'news', 14, 'Town fiesta activities', 'Residents are advised of the following: town fiesta activities. For questions, please visit or contact the office concerned.', NULL, 0, '2026-02-11', '2026-02-11 12:00:00', '2026-02-16 12:00:00', 0, NULL, NULL, NULL, NULL, NULL, '2026-02-10 04:00:00', '2026-10-21 06:39:39', 1),
(149, 'Event', 'announcement', 1, 'Schedule of office hours', 'Residents are advised of the following: schedule of office hours. For questions, please visit or contact the office concerned.', NULL, 0, '2026-02-13', '2026-02-13 12:00:00', '2026-02-18 12:00:00', 0, NULL, NULL, NULL, NULL, NULL, '2026-02-12 04:00:00', '2026-10-21 06:39:39', 1),
(150, 'Emergency Advisory', 'announcement', 9, 'Vaccination schedule', 'Residents are advised of the following: vaccination schedule. For questions, please visit or contact the office concerned.', NULL, 0, '2026-02-17', '2026-02-17 11:00:00', '2026-02-22 11:00:00', 0, NULL, NULL, NULL, NULL, NULL, '2026-02-13 03:00:00', '2026-10-21 06:39:39', 1),
(151, 'Official Announcement', 'announcement', 15, 'Livelihood training for residents', 'Residents are advised of the following: livelihood training for residents. For questions, please visit or contact the office concerned.', NULL, 0, '2026-02-21', '2026-02-21 08:00:00', '2026-02-26 08:00:00', 0, NULL, NULL, NULL, NULL, NULL, '2026-02-19 00:00:00', '2026-10-21 06:39:39', 1),
(152, 'Program and Services', 'announcement', 21, 'Reminder for tax payment', 'Residents are advised of the following: reminder for tax payment. For questions, please visit or contact the office concerned.', NULL, 0, '2026-03-14', '2026-03-14 11:00:00', '2026-03-19 11:00:00', 0, NULL, NULL, NULL, NULL, NULL, '2026-03-11 03:00:00', '2026-10-21 06:39:39', 1),
(153, 'Event', 'announcement', 16, 'Vaccination schedule', 'Residents are advised of the following: vaccination schedule. For questions, please visit or contact the office concerned.', NULL, 0, '2026-03-19', '2026-03-19 15:00:00', '2026-03-24 15:00:00', 0, NULL, NULL, NULL, NULL, NULL, '2026-03-18 07:00:00', '2026-10-21 06:39:39', 1),
(154, 'Program and Services', 'announcement', 23, 'Vaccination schedule', 'Residents are advised of the following: vaccination schedule. For questions, please visit or contact the office concerned.', NULL, 0, '2026-04-09', '2026-04-09 14:00:00', '2026-04-14 14:00:00', 0, NULL, NULL, NULL, NULL, NULL, '2026-04-09 06:00:00', '2026-10-21 06:39:39', 1),
(155, 'Program and Services', 'news', 14, 'Town fiesta activities', 'Residents are advised of the following: town fiesta activities. For questions, please visit or contact the office concerned.', NULL, 0, '2026-04-15', '2026-04-15 14:00:00', '2026-04-20 14:00:00', 0, NULL, NULL, NULL, NULL, NULL, '2026-04-13 06:00:00', '2026-10-21 06:39:39', 1),
(156, 'Event', 'announcement', 23, 'Vaccination schedule', 'Residents are advised of the following: vaccination schedule. For questions, please visit or contact the office concerned.', NULL, 0, '2026-04-25', '2026-04-25 13:00:00', '2026-04-30 13:00:00', 0, NULL, NULL, NULL, NULL, NULL, '2026-04-21 05:00:00', '2026-10-21 06:39:39', 1),
(157, 'Public Health Update', 'news', 10, 'Reminder for tax payment', 'Residents are advised of the following: reminder for tax payment. For questions, please visit or contact the office concerned.', NULL, 0, '2026-05-12', '2026-05-12 09:00:00', '2026-05-17 09:00:00', 0, NULL, NULL, NULL, NULL, NULL, '2026-05-07 01:00:00', '2026-10-21 06:39:39', 1),
(158, 'Holiday Advisory', 'news', 22, 'Free medical mission', 'Residents are advised of the following: free medical mission. For questions, please visit or contact the office concerned.', NULL, 0, '2026-05-14', '2026-05-14 09:00:00', '2026-05-19 09:00:00', 0, NULL, NULL, NULL, NULL, NULL, '2026-05-11 01:00:00', '2026-10-21 06:39:39', 1),
(159, 'Program and Services', 'news', 15, 'Weather advisory', 'Residents are advised of the following: weather advisory. For questions, please visit or contact the office concerned.', NULL, 0, '2026-05-15', '2026-05-15 08:00:00', '2026-05-20 08:00:00', 0, NULL, NULL, NULL, NULL, NULL, '2026-05-13 00:00:00', '2026-10-21 06:39:39', 1),
(160, 'Event', 'news', 15, 'Town fiesta activities', 'Residents are advised of the following: town fiesta activities. For questions, please visit or contact the office concerned.', NULL, 0, '2026-05-18', '2026-05-18 14:00:00', '2026-05-23 14:00:00', 0, NULL, NULL, NULL, NULL, NULL, '2026-05-14 06:00:00', '2026-10-21 06:39:39', 1),
(161, 'Holiday Advisory', 'news', 22, 'Reminder for tax payment', 'Residents are advised of the following: reminder for tax payment. For questions, please visit or contact the office concerned.', NULL, 0, '2026-06-07', '2026-06-07 13:00:00', '2026-06-12 13:00:00', 0, NULL, NULL, NULL, NULL, NULL, '2026-06-03 05:00:00', '2026-10-21 06:39:39', 1),
(162, 'Event', 'news', 10, 'Free medical mission', 'Residents are advised of the following: free medical mission. For questions, please visit or contact the office concerned.', NULL, 0, '2026-06-10', '2026-06-10 08:00:00', '2026-06-15 08:00:00', 0, NULL, NULL, NULL, NULL, NULL, '2026-06-05 00:00:00', '2026-10-21 06:39:39', 1),
(163, 'Official Announcement', 'announcement', 21, 'Reminder for tax payment', 'Residents are advised of the following: reminder for tax payment. For questions, please visit or contact the office concerned.', NULL, 0, '2026-06-14', '2026-06-14 13:00:00', '2026-06-19 13:00:00', 0, NULL, NULL, NULL, NULL, NULL, '2026-06-11 05:00:00', '2026-10-21 06:39:39', 1),
(164, 'Community News', 'news', 10, 'Livelihood training for residents', 'Residents are advised of the following: livelihood training for residents. For questions, please visit or contact the office concerned.', NULL, 0, '2026-06-25', '2026-06-25 11:00:00', '2026-06-30 11:00:00', 0, NULL, NULL, NULL, NULL, NULL, '2026-06-23 03:00:00', '2026-10-21 06:39:39', 1),
(165, 'Program and Services', 'announcement', 15, 'Schedule of office hours', 'Residents are advised of the following: schedule of office hours. For questions, please visit or contact the office concerned.', NULL, 0, '2026-06-25', '2026-06-25 13:00:00', '2026-06-30 13:00:00', 0, NULL, NULL, NULL, NULL, NULL, '2026-06-24 05:00:00', '2026-10-21 06:39:39', 1),
(166, 'Emergency Advisory', 'announcement', 14, 'Schedule of office hours', 'Residents are advised of the following: schedule of office hours. For questions, please visit or contact the office concerned.', NULL, 0, '2026-06-28', '2026-06-28 13:00:00', '2026-07-03 13:00:00', 0, NULL, NULL, NULL, NULL, NULL, '2026-06-25 05:00:00', '2026-10-21 06:39:39', 1),
(167, 'Public Notice', 'announcement', 15, 'Weather advisory', 'Residents are advised of the following: weather advisory. For questions, please visit or contact the office concerned.', NULL, 0, '2026-06-30', '2026-06-30 08:00:00', '2026-07-05 08:00:00', 0, NULL, NULL, NULL, NULL, NULL, '2026-06-29 00:00:00', '2026-10-21 06:39:39', 1),
(168, 'Event', 'announcement', 23, 'Weather advisory', 'Residents are advised of the following: weather advisory. For questions, please visit or contact the office concerned.', NULL, 0, '2026-07-06', '2026-07-06 11:00:00', '2026-07-11 11:00:00', 0, NULL, NULL, NULL, NULL, NULL, '2026-07-01 03:00:00', '2026-10-21 06:39:39', 1),
(169, 'Event', 'news', 25, 'Road repair advisory', 'Residents are advised of the following: road repair advisory. For questions, please visit or contact the office concerned.', NULL, 0, '2026-07-29', '2026-07-29 10:00:00', '2026-08-03 10:00:00', 0, NULL, NULL, NULL, NULL, NULL, '2026-07-27 02:00:00', '2026-10-21 06:39:39', 1),
(170, 'Official Announcement', 'announcement', 1, 'Reminder for tax payment', 'Residents are advised of the following: reminder for tax payment. For questions, please visit or contact the office concerned.', NULL, 0, '2026-07-29', '2026-07-29 15:00:00', '2026-08-03 15:00:00', 0, NULL, NULL, NULL, NULL, NULL, '2026-07-28 07:00:00', '2026-10-21 06:39:39', 1),
(171, 'Program and Services', 'announcement', 14, 'Road repair advisory', 'Residents are advised of the following: road repair advisory. For questions, please visit or contact the office concerned.', NULL, 0, '2026-07-31', '2026-07-31 11:00:00', '2026-08-05 11:00:00', 0, NULL, NULL, NULL, NULL, NULL, '2026-07-29 03:00:00', '2026-10-21 06:39:39', 1),
(172, 'Official Announcement', 'news', 22, 'Vaccination schedule', 'Residents are advised of the following: vaccination schedule. For questions, please visit or contact the office concerned.', NULL, 0, '2026-08-07', '2026-08-07 08:00:00', '2026-08-12 08:00:00', 0, NULL, NULL, NULL, NULL, NULL, '2026-08-03 00:00:00', '2026-10-21 06:39:39', 1),
(173, 'Official Announcement', 'announcement', 15, 'Livelihood training for residents', 'Residents are advised of the following: livelihood training for residents. For questions, please visit or contact the office concerned.', NULL, 0, '2026-08-07', '2026-08-07 15:00:00', '2026-08-12 15:00:00', 0, NULL, NULL, NULL, NULL, NULL, '2026-08-06 07:00:00', '2026-10-21 06:39:39', 1),
(174, 'Official Announcement', 'news', 1, 'Reminder for tax payment', 'Residents are advised of the following: reminder for tax payment. For questions, please visit or contact the office concerned.', NULL, 0, '2026-08-16', '2026-08-16 12:00:00', '2026-08-21 12:00:00', 0, NULL, NULL, NULL, NULL, NULL, '2026-08-12 04:00:00', '2026-10-21 06:39:39', 1),
(175, 'Program and Services', 'news', 22, 'Free medical mission', 'Residents are advised of the following: free medical mission. For questions, please visit or contact the office concerned.', NULL, 0, '2026-08-20', '2026-08-20 13:00:00', '2026-08-25 13:00:00', 0, NULL, NULL, NULL, NULL, NULL, '2026-08-17 05:00:00', '2026-10-21 06:39:39', 1),
(176, 'Emergency Advisory', 'announcement', 14, 'Livelihood training for residents', 'Residents are advised of the following: livelihood training for residents. For questions, please visit or contact the office concerned.', NULL, 0, '2026-08-24', '2026-08-24 08:00:00', '2026-08-29 08:00:00', 0, NULL, NULL, NULL, NULL, NULL, '2026-08-20 00:00:00', '2026-10-21 06:39:39', 1),
(177, 'Community News', 'announcement', 15, 'Free medical mission', 'Residents are advised of the following: free medical mission. For questions, please visit or contact the office concerned.', NULL, 0, '2026-08-31', '2026-08-31 13:00:00', '2026-09-05 13:00:00', 0, NULL, NULL, NULL, NULL, NULL, '2026-08-27 05:00:00', '2026-10-21 06:39:39', 1),
(178, 'Program and Services', 'announcement', 23, 'Free medical mission', 'Residents are advised of the following: free medical mission. For questions, please visit or contact the office concerned.', NULL, 0, '2026-09-01', '2026-09-01 13:00:00', '2026-09-06 13:00:00', 0, NULL, NULL, NULL, NULL, NULL, '2026-08-31 05:00:00', '2026-10-21 06:39:39', 1),
(179, 'Official Announcement', 'announcement', 1, 'Weather advisory', 'Residents are advised of the following: weather advisory. For questions, please visit or contact the office concerned.', NULL, 0, '2026-09-05', '2026-09-05 11:00:00', '2026-09-10 11:00:00', 0, NULL, NULL, NULL, NULL, NULL, '2026-09-03 03:00:00', '2026-10-21 06:39:39', 1),
(180, 'Official Announcement', 'news', 14, 'Vaccination schedule', 'Residents are advised of the following: vaccination schedule. For questions, please visit or contact the office concerned.', NULL, 0, '2026-09-11', '2026-09-11 11:00:00', '2026-09-16 11:00:00', 0, NULL, NULL, NULL, NULL, NULL, '2026-09-08 03:00:00', '2026-10-21 06:39:39', 1),
(181, 'Program and Services', 'announcement', 9, 'Schedule of office hours', 'Residents are advised of the following: schedule of office hours. For questions, please visit or contact the office concerned.', NULL, 0, '2026-09-11', '2026-09-11 08:00:00', '2026-09-16 08:00:00', 0, NULL, NULL, NULL, NULL, NULL, '2026-09-10 00:00:00', '2026-10-21 06:39:39', 1),
(182, 'Public Health Update', 'news', 14, 'Livelihood training for residents', 'Residents are advised of the following: livelihood training for residents. For questions, please visit or contact the office concerned.', NULL, 0, '2026-09-15', '2026-09-15 12:00:00', '2026-09-20 12:00:00', 0, NULL, NULL, NULL, NULL, NULL, '2026-09-15 04:00:00', '2026-10-21 06:39:39', 1),
(183, 'Public Notice', 'announcement', 21, 'Weather advisory', 'Residents are advised of the following: weather advisory. For questions, please visit or contact the office concerned.', NULL, 0, '2026-09-25', '2026-09-25 09:00:00', '2026-09-30 09:00:00', 0, NULL, NULL, NULL, NULL, NULL, '2026-09-22 01:00:00', '2026-10-21 06:39:39', 1),
(184, 'Official Announcement', 'news', 1, 'Road repair advisory', 'Residents are advised of the following: road repair advisory. For questions, please visit or contact the office concerned.', NULL, 0, '2026-10-11', '2026-10-11 10:00:00', '2026-10-16 10:00:00', 0, NULL, NULL, NULL, NULL, NULL, '2026-10-07 02:00:00', '2026-10-21 06:39:39', 1),
(185, 'Public Notice', 'announcement', 16, 'Livelihood training for residents', 'Residents are advised of the following: livelihood training for residents. For questions, please visit or contact the office concerned.', NULL, 0, '2026-10-16', '2026-10-16 09:00:00', '2026-10-21 09:00:00', 0, NULL, NULL, NULL, NULL, NULL, '2026-10-14 01:00:00', '2026-10-21 06:39:39', 1),
(186, 'Public Notice', 'announcement', 25, 'Free medical mission', 'Residents are advised of the following: free medical mission. For questions, please visit or contact the office concerned.', NULL, 0, '2026-10-20', '2026-10-20 11:00:00', '2026-10-25 11:00:00', 0, NULL, NULL, NULL, NULL, NULL, '2026-10-15 03:00:00', '2026-10-21 06:39:39', 1);

-- --------------------------------------------------------

--
-- Table structure for table `news_submissions`
--

CREATE TABLE `news_submissions` (
  `id` int(10) UNSIGNED NOT NULL,
  `post_id` int(10) UNSIGNED DEFAULT NULL,
  `department_id` int(10) UNSIGNED NOT NULL,
  `author_user_id` int(10) UNSIGNED NOT NULL,
  `status` enum('draft','pending_review','awaiting_office','approved','rejected','withdrawn') NOT NULL DEFAULT 'draft',
  `post_type` varchar(30) NOT NULL DEFAULT 'news',
  `category` varchar(60) NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` text NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `is_activity` tinyint(1) NOT NULL DEFAULT 0,
  `starts_at` datetime DEFAULT NULL,
  `ends_at` datetime DEFAULT NULL,
  `venue` varchar(150) DEFAULT NULL,
  `requirements` text DEFAULT NULL,
  `contact_info` varchar(150) DEFAULT NULL,
  `go_live_at` datetime DEFAULT NULL,
  `show_until` datetime DEFAULT NULL,
  `reject_reason` text DEFAULT NULL,
  `decided_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_demo` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `news_submissions`
--

INSERT INTO `news_submissions` (`id`, `post_id`, `department_id`, `author_user_id`, `status`, `post_type`, `category`, `title`, `description`, `image`, `is_activity`, `starts_at`, `ends_at`, `venue`, `requirements`, `contact_info`, `go_live_at`, `show_until`, `reject_reason`, `decided_at`, `created_at`, `updated_at`, `is_demo`) VALUES
(35, 30, 9, 14, 'approved', 'announcement', 'Public Notice', 'Business Permit Renewal Period', 'Business renewal shall commence on January 1, 2027 until February 20, 2027 only. Please be guided accordingly.', 'assets/uploads/news/d1560fc6afda59194d1210a1.jpg', 1, '2027-01-01 08:00:00', '2027-02-20 17:00:00', 'Business Permit and Licensing Office', 'Valid ID\nBarangay Business Clearance\nOld Permit', NULL, NULL, '2027-02-20 17:00:00', NULL, '2026-10-20 20:20:40', '2026-10-20 12:15:34', '2026-10-20 12:20:40', 0),
(36, NULL, 11, 12, 'pending_review', 'announcement', 'Public Notice', 'Extension of tax discount', 'lorem ipsum dolor sit amet consectetur adipiscing elit est cupidatat non laborum tempor possimus dolores mollit voluptatum nulla lorem cum dolore cupiditate lorem pariatur assumenda dolor nulla nostrud fugiat est ex dolore quis eu in do dolore dolores amet quod fuga voluptate est et id ducimus fugiat ducimus atque deleniti', 'assets/uploads/news/44cceca80cb0ed1ace26fdd8.png', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-20 12:57:11', '2026-10-20 12:57:11', 0),
(149, 135, 23, 36, 'approved', 'announcement', 'Holiday Advisory', 'Reminder for tax payment', 'Residents are advised of the following: reminder for tax payment. For questions, please visit or contact the office concerned.', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-21 14:00:00', '2025-11-20 06:00:00', '2025-11-20 06:00:00', 1),
(150, 136, 15, 22, 'approved', 'announcement', 'Program and Services', 'Town fiesta activities', 'Residents are advised of the following: town fiesta activities. For questions, please visit or contact the office concerned.', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-27 11:00:00', '2025-11-26 03:00:00', '2025-11-26 03:00:00', 1),
(151, 137, 23, 36, 'approved', 'news', 'Public Notice', 'Road repair advisory', 'Residents are advised of the following: road repair advisory. For questions, please visit or contact the office concerned.', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-12-05 11:00:00', '2025-12-02 03:00:00', '2025-12-02 03:00:00', 1),
(152, 138, 25, 20, 'approved', 'news', 'Emergency Advisory', 'Free medical mission', 'Residents are advised of the following: free medical mission. For questions, please visit or contact the office concerned.', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-12-05 15:00:00', '2025-12-04 07:00:00', '2025-12-04 07:00:00', 1),
(153, 139, 10, 37, 'approved', 'announcement', 'Program and Services', 'Road repair advisory', 'Residents are advised of the following: road repair advisory. For questions, please visit or contact the office concerned.', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-12-08 11:00:00', '2025-12-05 03:00:00', '2025-12-05 03:00:00', 1),
(154, 140, 1, 19, 'approved', 'announcement', 'Event', 'Road repair advisory', 'Residents are advised of the following: road repair advisory. For questions, please visit or contact the office concerned.', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-12-13 13:00:00', '2025-12-09 05:00:00', '2025-12-09 05:00:00', 1),
(155, 141, 16, 11, 'approved', 'news', 'Public Notice', 'Livelihood training for residents', 'Residents are advised of the following: livelihood training for residents. For questions, please visit or contact the office concerned.', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-12-27 08:00:00', '2025-12-23 00:00:00', '2025-12-23 00:00:00', 1),
(156, 142, 14, 25, 'approved', 'announcement', 'Official Announcement', 'Weather advisory', 'Residents are advised of the following: weather advisory. For questions, please visit or contact the office concerned.', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-12-31 08:00:00', '2025-12-29 00:00:00', '2025-12-29 00:00:00', 1),
(157, 143, 14, 25, 'approved', 'announcement', 'Official Announcement', 'Free medical mission', 'Residents are advised of the following: free medical mission. For questions, please visit or contact the office concerned.', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-01-02 14:00:00', '2025-12-30 06:00:00', '2025-12-30 06:00:00', 1),
(158, 144, 25, 20, 'approved', 'announcement', 'Event', 'Road repair advisory', 'Residents are advised of the following: road repair advisory. For questions, please visit or contact the office concerned.', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-01-12 09:00:00', '2026-01-09 01:00:00', '2026-01-09 01:00:00', 1),
(159, 145, 23, 36, 'approved', 'announcement', 'Event', 'Weather advisory', 'Residents are advised of the following: weather advisory. For questions, please visit or contact the office concerned.', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-04 10:00:00', '2026-02-03 02:00:00', '2026-02-03 02:00:00', 1),
(160, 146, 14, 25, 'approved', 'news', 'Event', 'Road repair advisory', 'Residents are advised of the following: road repair advisory. For questions, please visit or contact the office concerned.', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-05 11:00:00', '2026-02-04 03:00:00', '2026-02-04 03:00:00', 1),
(161, 147, 23, 36, 'approved', 'news', 'Program and Services', 'Free medical mission', 'Residents are advised of the following: free medical mission. For questions, please visit or contact the office concerned.', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-09 15:00:00', '2026-02-05 07:00:00', '2026-02-05 07:00:00', 1),
(162, 148, 14, 25, 'approved', 'news', 'Official Announcement', 'Town fiesta activities', 'Residents are advised of the following: town fiesta activities. For questions, please visit or contact the office concerned.', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 12:00:00', '2026-02-10 04:00:00', '2026-02-10 04:00:00', 1),
(163, 149, 1, 19, 'approved', 'announcement', 'Event', 'Schedule of office hours', 'Residents are advised of the following: schedule of office hours. For questions, please visit or contact the office concerned.', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-13 12:00:00', '2026-02-12 04:00:00', '2026-02-12 04:00:00', 1),
(164, 150, 9, 14, 'approved', 'announcement', 'Emergency Advisory', 'Vaccination schedule', 'Residents are advised of the following: vaccination schedule. For questions, please visit or contact the office concerned.', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-17 11:00:00', '2026-02-13 03:00:00', '2026-02-13 03:00:00', 1),
(165, 151, 15, 22, 'approved', 'announcement', 'Official Announcement', 'Livelihood training for residents', 'Residents are advised of the following: livelihood training for residents. For questions, please visit or contact the office concerned.', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-21 08:00:00', '2026-02-19 00:00:00', '2026-02-19 00:00:00', 1),
(166, NULL, 9, 14, 'rejected', 'announcement', 'Public Health Update', 'Vaccination schedule', 'Residents are advised of the following: vaccination schedule. For questions, please visit or contact the office concerned.', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-26 09:00:00', '2026-02-25 01:00:00', '2026-02-25 01:00:00', 1),
(167, 152, 21, 15, 'approved', 'announcement', 'Program and Services', 'Reminder for tax payment', 'Residents are advised of the following: reminder for tax payment. For questions, please visit or contact the office concerned.', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-14 11:00:00', '2026-03-11 03:00:00', '2026-03-11 03:00:00', 1),
(168, 153, 16, 11, 'approved', 'announcement', 'Event', 'Vaccination schedule', 'Residents are advised of the following: vaccination schedule. For questions, please visit or contact the office concerned.', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-19 15:00:00', '2026-03-18 07:00:00', '2026-03-18 07:00:00', 1),
(169, 154, 23, 36, 'approved', 'announcement', 'Program and Services', 'Vaccination schedule', 'Residents are advised of the following: vaccination schedule. For questions, please visit or contact the office concerned.', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-09 14:00:00', '2026-04-09 06:00:00', '2026-04-09 06:00:00', 1),
(170, 155, 14, 25, 'approved', 'news', 'Program and Services', 'Town fiesta activities', 'Residents are advised of the following: town fiesta activities. For questions, please visit or contact the office concerned.', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-15 14:00:00', '2026-04-13 06:00:00', '2026-04-13 06:00:00', 1),
(171, 156, 23, 36, 'approved', 'announcement', 'Event', 'Vaccination schedule', 'Residents are advised of the following: vaccination schedule. For questions, please visit or contact the office concerned.', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-25 13:00:00', '2026-04-21 05:00:00', '2026-04-21 05:00:00', 1),
(172, 157, 10, 37, 'approved', 'news', 'Public Health Update', 'Reminder for tax payment', 'Residents are advised of the following: reminder for tax payment. For questions, please visit or contact the office concerned.', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-12 09:00:00', '2026-05-07 01:00:00', '2026-05-07 01:00:00', 1),
(173, 158, 22, 21, 'approved', 'news', 'Holiday Advisory', 'Free medical mission', 'Residents are advised of the following: free medical mission. For questions, please visit or contact the office concerned.', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-14 09:00:00', '2026-05-11 01:00:00', '2026-05-11 01:00:00', 1),
(174, 159, 15, 22, 'approved', 'news', 'Program and Services', 'Weather advisory', 'Residents are advised of the following: weather advisory. For questions, please visit or contact the office concerned.', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-15 08:00:00', '2026-05-13 00:00:00', '2026-05-13 00:00:00', 1),
(175, 160, 15, 22, 'approved', 'news', 'Event', 'Town fiesta activities', 'Residents are advised of the following: town fiesta activities. For questions, please visit or contact the office concerned.', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-18 14:00:00', '2026-05-14 06:00:00', '2026-05-14 06:00:00', 1),
(176, NULL, 15, 22, 'rejected', 'announcement', 'Public Notice', 'Livelihood training for residents', 'Residents are advised of the following: livelihood training for residents. For questions, please visit or contact the office concerned.', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-15 08:00:00', '2026-05-15 00:00:00', '2026-05-15 00:00:00', 1),
(177, 161, 22, 21, 'approved', 'news', 'Holiday Advisory', 'Reminder for tax payment', 'Residents are advised of the following: reminder for tax payment. For questions, please visit or contact the office concerned.', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-07 13:00:00', '2026-06-03 05:00:00', '2026-06-03 05:00:00', 1),
(178, 162, 10, 37, 'approved', 'news', 'Event', 'Free medical mission', 'Residents are advised of the following: free medical mission. For questions, please visit or contact the office concerned.', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-10 08:00:00', '2026-06-05 00:00:00', '2026-06-05 00:00:00', 1),
(179, 163, 21, 15, 'approved', 'announcement', 'Official Announcement', 'Reminder for tax payment', 'Residents are advised of the following: reminder for tax payment. For questions, please visit or contact the office concerned.', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-14 13:00:00', '2026-06-11 05:00:00', '2026-06-11 05:00:00', 1),
(180, NULL, 1, 19, 'rejected', 'news', 'Emergency Advisory', 'Town fiesta activities', 'Residents are advised of the following: town fiesta activities. For questions, please visit or contact the office concerned.', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-15 09:00:00', '2026-06-12 01:00:00', '2026-06-12 01:00:00', 1),
(181, NULL, 16, 11, 'rejected', 'announcement', 'Program and Services', 'Vaccination schedule', 'Residents are advised of the following: vaccination schedule. For questions, please visit or contact the office concerned.', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-19 09:00:00', '2026-06-17 01:00:00', '2026-06-17 01:00:00', 1),
(182, 164, 10, 37, 'approved', 'news', 'Community News', 'Livelihood training for residents', 'Residents are advised of the following: livelihood training for residents. For questions, please visit or contact the office concerned.', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-25 11:00:00', '2026-06-23 03:00:00', '2026-06-23 03:00:00', 1),
(183, 165, 15, 22, 'approved', 'announcement', 'Program and Services', 'Schedule of office hours', 'Residents are advised of the following: schedule of office hours. For questions, please visit or contact the office concerned.', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-25 13:00:00', '2026-06-24 05:00:00', '2026-06-24 05:00:00', 1),
(184, 166, 14, 25, 'approved', 'announcement', 'Emergency Advisory', 'Schedule of office hours', 'Residents are advised of the following: schedule of office hours. For questions, please visit or contact the office concerned.', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-28 13:00:00', '2026-06-25 05:00:00', '2026-06-25 05:00:00', 1),
(185, 167, 15, 22, 'approved', 'announcement', 'Public Notice', 'Weather advisory', 'Residents are advised of the following: weather advisory. For questions, please visit or contact the office concerned.', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-30 08:00:00', '2026-06-29 00:00:00', '2026-06-29 00:00:00', 1),
(186, 168, 23, 36, 'approved', 'announcement', 'Event', 'Weather advisory', 'Residents are advised of the following: weather advisory. For questions, please visit or contact the office concerned.', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-07-06 11:00:00', '2026-07-01 03:00:00', '2026-07-01 03:00:00', 1),
(187, 169, 25, 20, 'approved', 'news', 'Event', 'Road repair advisory', 'Residents are advised of the following: road repair advisory. For questions, please visit or contact the office concerned.', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-07-29 10:00:00', '2026-07-27 02:00:00', '2026-07-27 02:00:00', 1),
(188, 170, 1, 19, 'approved', 'announcement', 'Official Announcement', 'Reminder for tax payment', 'Residents are advised of the following: reminder for tax payment. For questions, please visit or contact the office concerned.', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-07-29 15:00:00', '2026-07-28 07:00:00', '2026-07-28 07:00:00', 1),
(189, 171, 14, 25, 'approved', 'announcement', 'Program and Services', 'Road repair advisory', 'Residents are advised of the following: road repair advisory. For questions, please visit or contact the office concerned.', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-07-31 11:00:00', '2026-07-29 03:00:00', '2026-07-29 03:00:00', 1),
(190, 172, 22, 21, 'approved', 'news', 'Official Announcement', 'Vaccination schedule', 'Residents are advised of the following: vaccination schedule. For questions, please visit or contact the office concerned.', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-08-07 08:00:00', '2026-08-03 00:00:00', '2026-08-03 00:00:00', 1),
(191, 173, 15, 22, 'approved', 'announcement', 'Official Announcement', 'Livelihood training for residents', 'Residents are advised of the following: livelihood training for residents. For questions, please visit or contact the office concerned.', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-08-07 15:00:00', '2026-08-06 07:00:00', '2026-08-06 07:00:00', 1),
(192, 174, 1, 19, 'approved', 'news', 'Official Announcement', 'Reminder for tax payment', 'Residents are advised of the following: reminder for tax payment. For questions, please visit or contact the office concerned.', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-08-16 12:00:00', '2026-08-12 04:00:00', '2026-08-12 04:00:00', 1),
(193, 175, 22, 21, 'approved', 'news', 'Program and Services', 'Free medical mission', 'Residents are advised of the following: free medical mission. For questions, please visit or contact the office concerned.', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-08-20 13:00:00', '2026-08-17 05:00:00', '2026-08-17 05:00:00', 1),
(194, 176, 14, 25, 'approved', 'announcement', 'Emergency Advisory', 'Livelihood training for residents', 'Residents are advised of the following: livelihood training for residents. For questions, please visit or contact the office concerned.', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-08-24 08:00:00', '2026-08-20 00:00:00', '2026-08-20 00:00:00', 1),
(195, 177, 15, 22, 'approved', 'announcement', 'Community News', 'Free medical mission', 'Residents are advised of the following: free medical mission. For questions, please visit or contact the office concerned.', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-08-31 13:00:00', '2026-08-27 05:00:00', '2026-08-27 05:00:00', 1),
(196, 178, 23, 36, 'approved', 'announcement', 'Program and Services', 'Free medical mission', 'Residents are advised of the following: free medical mission. For questions, please visit or contact the office concerned.', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-09-01 13:00:00', '2026-08-31 05:00:00', '2026-08-31 05:00:00', 1),
(197, 179, 1, 19, 'approved', 'announcement', 'Official Announcement', 'Weather advisory', 'Residents are advised of the following: weather advisory. For questions, please visit or contact the office concerned.', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-09-05 11:00:00', '2026-09-03 03:00:00', '2026-09-03 03:00:00', 1),
(198, 180, 14, 25, 'approved', 'news', 'Official Announcement', 'Vaccination schedule', 'Residents are advised of the following: vaccination schedule. For questions, please visit or contact the office concerned.', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-09-11 11:00:00', '2026-09-08 03:00:00', '2026-09-08 03:00:00', 1),
(199, 181, 9, 14, 'approved', 'announcement', 'Program and Services', 'Schedule of office hours', 'Residents are advised of the following: schedule of office hours. For questions, please visit or contact the office concerned.', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-09-11 08:00:00', '2026-09-10 00:00:00', '2026-09-10 00:00:00', 1),
(200, 182, 14, 25, 'approved', 'news', 'Public Health Update', 'Livelihood training for residents', 'Residents are advised of the following: livelihood training for residents. For questions, please visit or contact the office concerned.', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-09-15 12:00:00', '2026-09-15 04:00:00', '2026-09-15 04:00:00', 1),
(201, 183, 21, 15, 'approved', 'announcement', 'Public Notice', 'Weather advisory', 'Residents are advised of the following: weather advisory. For questions, please visit or contact the office concerned.', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-09-25 09:00:00', '2026-09-22 01:00:00', '2026-09-22 01:00:00', 1),
(202, 184, 1, 19, 'approved', 'news', 'Official Announcement', 'Road repair advisory', 'Residents are advised of the following: road repair advisory. For questions, please visit or contact the office concerned.', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-11 10:00:00', '2026-10-07 02:00:00', '2026-10-07 02:00:00', 1),
(203, 185, 16, 11, 'approved', 'announcement', 'Public Notice', 'Livelihood training for residents', 'Residents are advised of the following: livelihood training for residents. For questions, please visit or contact the office concerned.', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-16 09:00:00', '2026-10-14 01:00:00', '2026-10-14 01:00:00', 1),
(204, 186, 25, 20, 'approved', 'announcement', 'Public Notice', 'Free medical mission', 'Residents are advised of the following: free medical mission. For questions, please visit or contact the office concerned.', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-20 11:00:00', '2026-10-15 03:00:00', '2026-10-15 03:00:00', 1);

-- --------------------------------------------------------

--
-- Table structure for table `news_submission_events`
--

CREATE TABLE `news_submission_events` (
  `id` int(10) UNSIGNED NOT NULL,
  `submission_id` int(10) UNSIGNED NOT NULL,
  `actor_user_id` int(10) UNSIGNED DEFAULT NULL,
  `actor_name` varchar(150) NOT NULL,
  `actor_role` enum('office','admin') NOT NULL,
  `action` varchar(30) NOT NULL,
  `note` text DEFAULT NULL,
  `snapshot` mediumtext DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `news_submission_events`
--

INSERT INTO `news_submission_events` (`id`, `submission_id`, `actor_user_id`, `actor_name`, `actor_role`, `action`, `note`, `snapshot`, `created_at`) VALUES
(141, 35, 14, 'Jemelee John De Silva', 'office', 'created', NULL, '{\"post_type\":\"announcement\",\"category\":\"Public Notice\",\"title\":\"Business Permit Renewal Period\",\"description\":\"Business renewal shall commence on January 1, 2027 until February 20, 2027 only. Please be guided accordingly.\",\"image\":\"assets\\/uploads\\/news\\/d1560fc6afda59194d1210a1.jpg\",\"is_activity\":1,\"starts_at\":\"2027-01-01 08:00:00\",\"ends_at\":\"2027-02-20 17:00:00\",\"venue\":\"Business Permit and Licensing Office\",\"requirements\":\"Valid ID\\nBarangay Business Clearance\\nOld Permit\",\"contact_info\":null,\"go_live_at\":null,\"show_until\":\"2027-02-20 17:00:00\"}', '2026-10-20 12:15:34'),
(142, 35, 14, 'Jemelee John De Silva', 'office', 'submitted', NULL, '{\"post_type\":\"announcement\",\"category\":\"Public Notice\",\"title\":\"Business Permit Renewal Period\",\"description\":\"Business renewal shall commence on January 1, 2027 until February 20, 2027 only. Please be guided accordingly.\",\"image\":\"assets\\/uploads\\/news\\/d1560fc6afda59194d1210a1.jpg\",\"is_activity\":1,\"starts_at\":\"2027-01-01 08:00:00\",\"ends_at\":\"2027-02-20 17:00:00\",\"venue\":\"Business Permit and Licensing Office\",\"requirements\":\"Valid ID\\nBarangay Business Clearance\\nOld Permit\",\"contact_info\":null,\"go_live_at\":null,\"show_until\":\"2027-02-20 17:00:00\"}', '2026-10-20 12:15:34'),
(143, 35, 1, 'Municipal Administrator', 'admin', 'approved', NULL, '{\"post_type\":\"announcement\",\"category\":\"Public Notice\",\"title\":\"Business Permit Renewal Period\",\"description\":\"Business renewal shall commence on January 1, 2027 until February 20, 2027 only. Please be guided accordingly.\",\"image\":\"assets\\/uploads\\/news\\/d1560fc6afda59194d1210a1.jpg\",\"is_activity\":1,\"starts_at\":\"2027-01-01 08:00:00\",\"ends_at\":\"2027-02-20 17:00:00\",\"venue\":\"Business Permit and Licensing Office\",\"requirements\":\"Valid ID\\nBarangay Business Clearance\\nOld Permit\",\"contact_info\":null,\"go_live_at\":null,\"show_until\":\"2027-02-20 17:00:00\"}', '2026-10-20 12:20:40'),
(144, 36, 12, 'Portia H. German', 'office', 'created', NULL, '{\"post_type\":\"announcement\",\"category\":\"Public Notice\",\"title\":\"Extension of tax discount\",\"description\":\"lorem ipsum dolor sit amet consectetur adipiscing elit est cupidatat non laborum tempor possimus dolores mollit voluptatum nulla lorem cum dolore cupiditate lorem pariatur assumenda dolor nulla nostrud fugiat est ex dolore quis eu in do dolore dolores amet quod fuga voluptate est et id ducimus fugiat ducimus atque deleniti\",\"image\":\"assets\\/uploads\\/news\\/44cceca80cb0ed1ace26fdd8.png\",\"is_activity\":0,\"starts_at\":null,\"ends_at\":null,\"venue\":null,\"requirements\":null,\"contact_info\":null,\"go_live_at\":null,\"show_until\":null}', '2026-10-20 12:57:11'),
(145, 36, 12, 'Portia H. German', 'office', 'submitted', NULL, '{\"post_type\":\"announcement\",\"category\":\"Public Notice\",\"title\":\"Extension of tax discount\",\"description\":\"lorem ipsum dolor sit amet consectetur adipiscing elit est cupidatat non laborum tempor possimus dolores mollit voluptatum nulla lorem cum dolore cupiditate lorem pariatur assumenda dolor nulla nostrud fugiat est ex dolore quis eu in do dolore dolores amet quod fuga voluptate est et id ducimus fugiat ducimus atque deleniti\",\"image\":\"assets\\/uploads\\/news\\/44cceca80cb0ed1ace26fdd8.png\",\"is_activity\":0,\"starts_at\":null,\"ends_at\":null,\"venue\":null,\"requirements\":null,\"contact_info\":null,\"go_live_at\":null,\"show_until\":null}', '2026-10-20 12:57:11'),
(542, 149, 36, 'Office staff', 'office', 'created', NULL, NULL, '2025-11-20 06:00:00'),
(543, 149, 36, 'Office staff', 'office', 'submitted', NULL, NULL, '2025-11-20 06:01:00'),
(544, 149, 1, 'Municipal Administrator', 'admin', 'approved', NULL, NULL, '2025-11-21 06:00:00'),
(545, 150, 22, 'Office staff', 'office', 'created', NULL, NULL, '2025-11-26 03:00:00'),
(546, 150, 22, 'Office staff', 'office', 'submitted', NULL, NULL, '2025-11-26 03:01:00'),
(547, 150, 1, 'Municipal Administrator', 'admin', 'approved', NULL, NULL, '2025-11-27 03:00:00'),
(548, 151, 36, 'Office staff', 'office', 'created', NULL, NULL, '2025-12-02 03:00:00'),
(549, 151, 36, 'Office staff', 'office', 'submitted', NULL, NULL, '2025-12-02 03:01:00'),
(550, 151, 1, 'Municipal Administrator', 'admin', 'admin_edited', NULL, NULL, '2025-12-03 03:00:00'),
(551, 151, 1, 'Municipal Administrator', 'admin', 'admin_edited', NULL, NULL, '2025-12-04 03:00:00'),
(552, 151, 1, 'Municipal Administrator', 'admin', 'approved', NULL, NULL, '2025-12-05 03:00:00'),
(553, 152, 20, 'Office staff', 'office', 'created', NULL, NULL, '2025-12-04 07:00:00'),
(554, 152, 20, 'Office staff', 'office', 'submitted', NULL, NULL, '2025-12-04 07:01:00'),
(555, 152, 1, 'Municipal Administrator', 'admin', 'approved', NULL, NULL, '2025-12-05 07:00:00'),
(556, 153, 37, 'Office staff', 'office', 'created', NULL, NULL, '2025-12-05 03:00:00'),
(557, 153, 37, 'Office staff', 'office', 'submitted', NULL, NULL, '2025-12-05 03:01:00'),
(558, 153, 1, 'Municipal Administrator', 'admin', 'approved', NULL, NULL, '2025-12-08 03:00:00'),
(559, 154, 19, 'Office staff', 'office', 'created', NULL, NULL, '2025-12-09 05:00:00'),
(560, 154, 19, 'Office staff', 'office', 'submitted', NULL, NULL, '2025-12-09 05:01:00'),
(561, 154, 1, 'Municipal Administrator', 'admin', 'admin_edited', NULL, NULL, '2025-12-10 05:00:00'),
(562, 154, 1, 'Municipal Administrator', 'admin', 'approved', NULL, NULL, '2025-12-13 05:00:00'),
(563, 155, 11, 'Office staff', 'office', 'created', NULL, NULL, '2025-12-23 00:00:00'),
(564, 155, 11, 'Office staff', 'office', 'submitted', NULL, NULL, '2025-12-23 00:01:00'),
(565, 155, 1, 'Municipal Administrator', 'admin', 'approved', NULL, NULL, '2025-12-27 00:00:00'),
(566, 156, 25, 'Office staff', 'office', 'created', NULL, NULL, '2025-12-29 00:00:00'),
(567, 156, 25, 'Office staff', 'office', 'submitted', NULL, NULL, '2025-12-29 00:01:00'),
(568, 156, 1, 'Municipal Administrator', 'admin', 'approved', NULL, NULL, '2025-12-31 00:00:00'),
(569, 157, 25, 'Office staff', 'office', 'created', NULL, NULL, '2025-12-30 06:00:00'),
(570, 157, 25, 'Office staff', 'office', 'submitted', NULL, NULL, '2025-12-30 06:01:00'),
(571, 157, 1, 'Municipal Administrator', 'admin', 'approved', NULL, NULL, '2026-01-02 06:00:00'),
(572, 158, 20, 'Office staff', 'office', 'created', NULL, NULL, '2026-01-09 01:00:00'),
(573, 158, 20, 'Office staff', 'office', 'submitted', NULL, NULL, '2026-01-09 01:01:00'),
(574, 158, 1, 'Municipal Administrator', 'admin', 'admin_edited', NULL, NULL, '2026-01-10 01:00:00'),
(575, 158, 1, 'Municipal Administrator', 'admin', 'admin_edited', NULL, NULL, '2026-01-11 01:00:00'),
(576, 158, 1, 'Municipal Administrator', 'admin', 'approved', NULL, NULL, '2026-01-12 01:00:00'),
(577, 159, 36, 'Office staff', 'office', 'created', NULL, NULL, '2026-02-03 02:00:00'),
(578, 159, 36, 'Office staff', 'office', 'submitted', NULL, NULL, '2026-02-03 02:01:00'),
(579, 159, 1, 'Municipal Administrator', 'admin', 'approved', NULL, NULL, '2026-02-04 02:00:00'),
(580, 160, 25, 'Office staff', 'office', 'created', NULL, NULL, '2026-02-04 03:00:00'),
(581, 160, 25, 'Office staff', 'office', 'submitted', NULL, NULL, '2026-02-04 03:01:00'),
(582, 160, 1, 'Municipal Administrator', 'admin', 'approved', NULL, NULL, '2026-02-05 03:00:00'),
(583, 161, 36, 'Office staff', 'office', 'created', NULL, NULL, '2026-02-05 07:00:00'),
(584, 161, 36, 'Office staff', 'office', 'submitted', NULL, NULL, '2026-02-05 07:01:00'),
(585, 161, 1, 'Municipal Administrator', 'admin', 'admin_edited', NULL, NULL, '2026-02-06 07:00:00'),
(586, 161, 1, 'Municipal Administrator', 'admin', 'approved', NULL, NULL, '2026-02-09 07:00:00'),
(587, 162, 25, 'Office staff', 'office', 'created', NULL, NULL, '2026-02-10 04:00:00'),
(588, 162, 25, 'Office staff', 'office', 'submitted', NULL, NULL, '2026-02-10 04:01:00'),
(589, 162, 1, 'Municipal Administrator', 'admin', 'approved', NULL, NULL, '2026-02-11 04:00:00'),
(590, 163, 19, 'Office staff', 'office', 'created', NULL, NULL, '2026-02-12 04:00:00'),
(591, 163, 19, 'Office staff', 'office', 'submitted', NULL, NULL, '2026-02-12 04:01:00'),
(592, 163, 1, 'Municipal Administrator', 'admin', 'approved', NULL, NULL, '2026-02-13 04:00:00'),
(593, 164, 14, 'Office staff', 'office', 'created', NULL, NULL, '2026-02-13 03:00:00'),
(594, 164, 14, 'Office staff', 'office', 'submitted', NULL, NULL, '2026-02-13 03:01:00'),
(595, 164, 1, 'Municipal Administrator', 'admin', 'approved', NULL, NULL, '2026-02-17 03:00:00'),
(596, 165, 22, 'Office staff', 'office', 'created', NULL, NULL, '2026-02-19 00:00:00'),
(597, 165, 22, 'Office staff', 'office', 'submitted', NULL, NULL, '2026-02-19 00:01:00'),
(598, 165, 1, 'Municipal Administrator', 'admin', 'admin_edited', NULL, NULL, '2026-02-20 00:00:00'),
(599, 165, 1, 'Municipal Administrator', 'admin', 'approved', NULL, NULL, '2026-02-21 00:00:00'),
(600, 166, 14, 'Office staff', 'office', 'created', NULL, NULL, '2026-02-25 01:00:00'),
(601, 166, 14, 'Office staff', 'office', 'submitted', NULL, NULL, '2026-02-25 01:01:00'),
(602, 166, 1, 'Municipal Administrator', 'admin', 'rejected', NULL, NULL, '2026-02-26 01:00:00'),
(603, 167, 15, 'Office staff', 'office', 'created', NULL, NULL, '2026-03-11 03:00:00'),
(604, 167, 15, 'Office staff', 'office', 'submitted', NULL, NULL, '2026-03-11 03:01:00'),
(605, 167, 1, 'Municipal Administrator', 'admin', 'approved', NULL, NULL, '2026-03-14 03:00:00'),
(606, 168, 11, 'Office staff', 'office', 'created', NULL, NULL, '2026-03-18 07:00:00'),
(607, 168, 11, 'Office staff', 'office', 'submitted', NULL, NULL, '2026-03-18 07:01:00'),
(608, 168, 1, 'Municipal Administrator', 'admin', 'approved', NULL, NULL, '2026-03-19 07:00:00'),
(609, 169, 36, 'Office staff', 'office', 'created', NULL, NULL, '2026-04-09 06:00:00'),
(610, 169, 36, 'Office staff', 'office', 'submitted', NULL, NULL, '2026-04-09 06:01:00'),
(611, 169, 1, 'Municipal Administrator', 'admin', 'approved', NULL, NULL, '2026-04-09 06:00:00'),
(612, 170, 25, 'Office staff', 'office', 'created', NULL, NULL, '2026-04-13 06:00:00'),
(613, 170, 25, 'Office staff', 'office', 'submitted', NULL, NULL, '2026-04-13 06:01:00'),
(614, 170, 1, 'Municipal Administrator', 'admin', 'admin_edited', NULL, NULL, '2026-04-14 06:00:00'),
(615, 170, 1, 'Municipal Administrator', 'admin', 'admin_edited', NULL, NULL, '2026-04-15 06:00:00'),
(616, 170, 1, 'Municipal Administrator', 'admin', 'approved', NULL, NULL, '2026-04-15 06:00:00'),
(617, 171, 36, 'Office staff', 'office', 'created', NULL, NULL, '2026-04-21 05:00:00'),
(618, 171, 36, 'Office staff', 'office', 'submitted', NULL, NULL, '2026-04-21 05:01:00'),
(619, 171, 1, 'Municipal Administrator', 'admin', 'approved', NULL, NULL, '2026-04-25 05:00:00'),
(620, 172, 37, 'Office staff', 'office', 'created', NULL, NULL, '2026-05-07 01:00:00'),
(621, 172, 37, 'Office staff', 'office', 'submitted', NULL, NULL, '2026-05-07 01:01:00'),
(622, 172, 1, 'Municipal Administrator', 'admin', 'admin_edited', NULL, NULL, '2026-05-08 01:00:00'),
(623, 172, 1, 'Municipal Administrator', 'admin', 'approved', NULL, NULL, '2026-05-12 01:00:00'),
(624, 173, 21, 'Office staff', 'office', 'created', NULL, NULL, '2026-05-11 01:00:00'),
(625, 173, 21, 'Office staff', 'office', 'submitted', NULL, NULL, '2026-05-11 01:01:00'),
(626, 173, 1, 'Municipal Administrator', 'admin', 'admin_edited', NULL, NULL, '2026-05-12 01:00:00'),
(627, 173, 1, 'Municipal Administrator', 'admin', 'approved', NULL, NULL, '2026-05-14 01:00:00'),
(628, 174, 22, 'Office staff', 'office', 'created', NULL, NULL, '2026-05-13 00:00:00'),
(629, 174, 22, 'Office staff', 'office', 'submitted', NULL, NULL, '2026-05-13 00:01:00'),
(630, 174, 1, 'Municipal Administrator', 'admin', 'admin_edited', NULL, NULL, '2026-05-14 00:00:00'),
(631, 174, 1, 'Municipal Administrator', 'admin', 'approved', NULL, NULL, '2026-05-15 00:00:00'),
(632, 175, 22, 'Office staff', 'office', 'created', NULL, NULL, '2026-05-14 06:00:00'),
(633, 175, 22, 'Office staff', 'office', 'submitted', NULL, NULL, '2026-05-14 06:01:00'),
(634, 175, 1, 'Municipal Administrator', 'admin', 'approved', NULL, NULL, '2026-05-18 06:00:00'),
(635, 176, 22, 'Office staff', 'office', 'created', NULL, NULL, '2026-05-15 00:00:00'),
(636, 176, 22, 'Office staff', 'office', 'submitted', NULL, NULL, '2026-05-15 00:01:00'),
(637, 176, 1, 'Municipal Administrator', 'admin', 'rejected', NULL, NULL, '2026-05-15 00:00:00'),
(638, 177, 21, 'Office staff', 'office', 'created', NULL, NULL, '2026-06-03 05:00:00'),
(639, 177, 21, 'Office staff', 'office', 'submitted', NULL, NULL, '2026-06-03 05:01:00'),
(640, 177, 1, 'Municipal Administrator', 'admin', 'approved', NULL, NULL, '2026-06-07 05:00:00'),
(641, 178, 37, 'Office staff', 'office', 'created', NULL, NULL, '2026-06-05 00:00:00'),
(642, 178, 37, 'Office staff', 'office', 'submitted', NULL, NULL, '2026-06-05 00:01:00'),
(643, 178, 1, 'Municipal Administrator', 'admin', 'admin_edited', NULL, NULL, '2026-06-06 00:00:00'),
(644, 178, 1, 'Municipal Administrator', 'admin', 'admin_edited', NULL, NULL, '2026-06-07 00:00:00'),
(645, 178, 1, 'Municipal Administrator', 'admin', 'approved', NULL, NULL, '2026-06-10 00:00:00'),
(646, 179, 15, 'Office staff', 'office', 'created', NULL, NULL, '2026-06-11 05:00:00'),
(647, 179, 15, 'Office staff', 'office', 'submitted', NULL, NULL, '2026-06-11 05:01:00'),
(648, 179, 1, 'Municipal Administrator', 'admin', 'approved', NULL, NULL, '2026-06-14 05:00:00'),
(649, 180, 19, 'Office staff', 'office', 'created', NULL, NULL, '2026-06-12 01:00:00'),
(650, 180, 19, 'Office staff', 'office', 'submitted', NULL, NULL, '2026-06-12 01:01:00'),
(651, 180, 1, 'Municipal Administrator', 'admin', 'admin_edited', NULL, NULL, '2026-06-13 01:00:00'),
(652, 180, 1, 'Municipal Administrator', 'admin', 'admin_edited', NULL, NULL, '2026-06-14 01:00:00'),
(653, 180, 1, 'Municipal Administrator', 'admin', 'rejected', NULL, NULL, '2026-06-15 01:00:00'),
(654, 181, 11, 'Office staff', 'office', 'created', NULL, NULL, '2026-06-17 01:00:00'),
(655, 181, 11, 'Office staff', 'office', 'submitted', NULL, NULL, '2026-06-17 01:01:00'),
(656, 181, 1, 'Municipal Administrator', 'admin', 'admin_edited', NULL, NULL, '2026-06-18 01:00:00'),
(657, 181, 1, 'Municipal Administrator', 'admin', 'rejected', NULL, NULL, '2026-06-19 01:00:00'),
(658, 182, 37, 'Office staff', 'office', 'created', NULL, NULL, '2026-06-23 03:00:00'),
(659, 182, 37, 'Office staff', 'office', 'submitted', NULL, NULL, '2026-06-23 03:01:00'),
(660, 182, 1, 'Municipal Administrator', 'admin', 'approved', NULL, NULL, '2026-06-25 03:00:00'),
(661, 183, 22, 'Office staff', 'office', 'created', NULL, NULL, '2026-06-24 05:00:00'),
(662, 183, 22, 'Office staff', 'office', 'submitted', NULL, NULL, '2026-06-24 05:01:00'),
(663, 183, 1, 'Municipal Administrator', 'admin', 'admin_edited', NULL, NULL, '2026-06-25 05:00:00'),
(664, 183, 1, 'Municipal Administrator', 'admin', 'approved', NULL, NULL, '2026-06-25 05:00:00'),
(665, 184, 25, 'Office staff', 'office', 'created', NULL, NULL, '2026-06-25 05:00:00'),
(666, 184, 25, 'Office staff', 'office', 'submitted', NULL, NULL, '2026-06-25 05:01:00'),
(667, 184, 1, 'Municipal Administrator', 'admin', 'admin_edited', NULL, NULL, '2026-06-26 05:00:00'),
(668, 184, 1, 'Municipal Administrator', 'admin', 'approved', NULL, NULL, '2026-06-28 05:00:00'),
(669, 185, 22, 'Office staff', 'office', 'created', NULL, NULL, '2026-06-29 00:00:00'),
(670, 185, 22, 'Office staff', 'office', 'submitted', NULL, NULL, '2026-06-29 00:01:00'),
(671, 185, 1, 'Municipal Administrator', 'admin', 'approved', NULL, NULL, '2026-06-30 00:00:00'),
(672, 186, 36, 'Office staff', 'office', 'created', NULL, NULL, '2026-07-01 03:00:00'),
(673, 186, 36, 'Office staff', 'office', 'submitted', NULL, NULL, '2026-07-01 03:01:00'),
(674, 186, 1, 'Municipal Administrator', 'admin', 'admin_edited', NULL, NULL, '2026-07-02 03:00:00'),
(675, 186, 1, 'Municipal Administrator', 'admin', 'approved', NULL, NULL, '2026-07-06 03:00:00'),
(676, 187, 20, 'Office staff', 'office', 'created', NULL, NULL, '2026-07-27 02:00:00'),
(677, 187, 20, 'Office staff', 'office', 'submitted', NULL, NULL, '2026-07-27 02:01:00'),
(678, 187, 1, 'Municipal Administrator', 'admin', 'admin_edited', NULL, NULL, '2026-07-28 02:00:00'),
(679, 187, 1, 'Municipal Administrator', 'admin', 'approved', NULL, NULL, '2026-07-29 02:00:00'),
(680, 188, 19, 'Office staff', 'office', 'created', NULL, NULL, '2026-07-28 07:00:00'),
(681, 188, 19, 'Office staff', 'office', 'submitted', NULL, NULL, '2026-07-28 07:01:00'),
(682, 188, 1, 'Municipal Administrator', 'admin', 'approved', NULL, NULL, '2026-07-29 07:00:00'),
(683, 189, 25, 'Office staff', 'office', 'created', NULL, NULL, '2026-07-29 03:00:00'),
(684, 189, 25, 'Office staff', 'office', 'submitted', NULL, NULL, '2026-07-29 03:01:00'),
(685, 189, 1, 'Municipal Administrator', 'admin', 'admin_edited', NULL, NULL, '2026-07-30 03:00:00'),
(686, 189, 1, 'Municipal Administrator', 'admin', 'approved', NULL, NULL, '2026-07-31 03:00:00'),
(687, 190, 21, 'Office staff', 'office', 'created', NULL, NULL, '2026-08-03 00:00:00'),
(688, 190, 21, 'Office staff', 'office', 'submitted', NULL, NULL, '2026-08-03 00:01:00'),
(689, 190, 1, 'Municipal Administrator', 'admin', 'approved', NULL, NULL, '2026-08-07 00:00:00'),
(690, 191, 22, 'Office staff', 'office', 'created', NULL, NULL, '2026-08-06 07:00:00'),
(691, 191, 22, 'Office staff', 'office', 'submitted', NULL, NULL, '2026-08-06 07:01:00'),
(692, 191, 1, 'Municipal Administrator', 'admin', 'admin_edited', NULL, NULL, '2026-08-07 07:00:00'),
(693, 191, 1, 'Municipal Administrator', 'admin', 'approved', NULL, NULL, '2026-08-07 07:00:00'),
(694, 192, 19, 'Office staff', 'office', 'created', NULL, NULL, '2026-08-12 04:00:00'),
(695, 192, 19, 'Office staff', 'office', 'submitted', NULL, NULL, '2026-08-12 04:01:00'),
(696, 192, 1, 'Municipal Administrator', 'admin', 'approved', NULL, NULL, '2026-08-16 04:00:00'),
(697, 193, 21, 'Office staff', 'office', 'created', NULL, NULL, '2026-08-17 05:00:00'),
(698, 193, 21, 'Office staff', 'office', 'submitted', NULL, NULL, '2026-08-17 05:01:00'),
(699, 193, 1, 'Municipal Administrator', 'admin', 'admin_edited', NULL, NULL, '2026-08-18 05:00:00'),
(700, 193, 1, 'Municipal Administrator', 'admin', 'admin_edited', NULL, NULL, '2026-08-19 05:00:00'),
(701, 193, 1, 'Municipal Administrator', 'admin', 'approved', NULL, NULL, '2026-08-20 05:00:00'),
(702, 194, 25, 'Office staff', 'office', 'created', NULL, NULL, '2026-08-20 00:00:00'),
(703, 194, 25, 'Office staff', 'office', 'submitted', NULL, NULL, '2026-08-20 00:01:00'),
(704, 194, 1, 'Municipal Administrator', 'admin', 'approved', NULL, NULL, '2026-08-24 00:00:00'),
(705, 195, 22, 'Office staff', 'office', 'created', NULL, NULL, '2026-08-27 05:00:00'),
(706, 195, 22, 'Office staff', 'office', 'submitted', NULL, NULL, '2026-08-27 05:01:00'),
(707, 195, 1, 'Municipal Administrator', 'admin', 'approved', NULL, NULL, '2026-08-31 05:00:00'),
(708, 196, 36, 'Office staff', 'office', 'created', NULL, NULL, '2026-08-31 05:00:00'),
(709, 196, 36, 'Office staff', 'office', 'submitted', NULL, NULL, '2026-08-31 05:01:00'),
(710, 196, 1, 'Municipal Administrator', 'admin', 'approved', NULL, NULL, '2026-09-01 05:00:00'),
(711, 197, 19, 'Office staff', 'office', 'created', NULL, NULL, '2026-09-03 03:00:00'),
(712, 197, 19, 'Office staff', 'office', 'submitted', NULL, NULL, '2026-09-03 03:01:00'),
(713, 197, 1, 'Municipal Administrator', 'admin', 'approved', NULL, NULL, '2026-09-05 03:00:00'),
(714, 198, 25, 'Office staff', 'office', 'created', NULL, NULL, '2026-09-08 03:00:00'),
(715, 198, 25, 'Office staff', 'office', 'submitted', NULL, NULL, '2026-09-08 03:01:00'),
(716, 198, 1, 'Municipal Administrator', 'admin', 'approved', NULL, NULL, '2026-09-11 03:00:00'),
(717, 199, 14, 'Office staff', 'office', 'created', NULL, NULL, '2026-09-10 00:00:00'),
(718, 199, 14, 'Office staff', 'office', 'submitted', NULL, NULL, '2026-09-10 00:01:00'),
(719, 199, 1, 'Municipal Administrator', 'admin', 'admin_edited', NULL, NULL, '2026-09-11 00:00:00'),
(720, 199, 1, 'Municipal Administrator', 'admin', 'approved', NULL, NULL, '2026-09-11 00:00:00'),
(721, 200, 25, 'Office staff', 'office', 'created', NULL, NULL, '2026-09-15 04:00:00'),
(722, 200, 25, 'Office staff', 'office', 'submitted', NULL, NULL, '2026-09-15 04:01:00'),
(723, 200, 1, 'Municipal Administrator', 'admin', 'approved', NULL, NULL, '2026-09-15 04:00:00'),
(724, 201, 15, 'Office staff', 'office', 'created', NULL, NULL, '2026-09-22 01:00:00'),
(725, 201, 15, 'Office staff', 'office', 'submitted', NULL, NULL, '2026-09-22 01:01:00'),
(726, 201, 1, 'Municipal Administrator', 'admin', 'admin_edited', NULL, NULL, '2026-09-23 01:00:00'),
(727, 201, 1, 'Municipal Administrator', 'admin', 'approved', NULL, NULL, '2026-09-25 01:00:00'),
(728, 202, 19, 'Office staff', 'office', 'created', NULL, NULL, '2026-10-07 02:00:00'),
(729, 202, 19, 'Office staff', 'office', 'submitted', NULL, NULL, '2026-10-07 02:01:00'),
(730, 202, 1, 'Municipal Administrator', 'admin', 'approved', NULL, NULL, '2026-10-11 02:00:00'),
(731, 203, 11, 'Office staff', 'office', 'created', NULL, NULL, '2026-10-14 01:00:00'),
(732, 203, 11, 'Office staff', 'office', 'submitted', NULL, NULL, '2026-10-14 01:01:00'),
(733, 203, 1, 'Municipal Administrator', 'admin', 'admin_edited', NULL, NULL, '2026-10-15 01:00:00'),
(734, 203, 1, 'Municipal Administrator', 'admin', 'approved', NULL, NULL, '2026-10-16 01:00:00'),
(735, 204, 20, 'Office staff', 'office', 'created', NULL, NULL, '2026-10-15 03:00:00'),
(736, 204, 20, 'Office staff', 'office', 'submitted', NULL, NULL, '2026-10-15 03:01:00'),
(737, 204, 1, 'Municipal Administrator', 'admin', 'admin_edited', NULL, NULL, '2026-10-16 03:00:00'),
(738, 204, 1, 'Municipal Administrator', 'admin', 'admin_edited', NULL, NULL, '2026-10-17 03:00:00'),
(739, 204, 1, 'Municipal Administrator', 'admin', 'approved', NULL, NULL, '2026-10-20 03:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `news_types`
--

CREATE TABLE `news_types` (
  `id` int(10) UNSIGNED NOT NULL,
  `slug` varchar(30) NOT NULL,
  `label` varchar(40) NOT NULL,
  `description` varchar(120) DEFAULT NULL,
  `icon` varchar(30) NOT NULL DEFAULT 'newspaper',
  `color` varchar(10) NOT NULL DEFAULT 'blue',
  `home_limit` tinyint(3) UNSIGNED NOT NULL DEFAULT 6,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `news_types`
--

INSERT INTO `news_types` (`id`, `slug`, `label`, `description`, `icon`, `color`, `home_limit`, `sort_order`, `is_active`, `created_at`) VALUES
(1, 'announcement', 'Announcement', 'Things you may need to know or do.', 'bullhorn', 'amber', 3, 1, 1, '2026-10-20 06:59:36'),
(2, 'news', 'News', 'Stories and updates from the municipality and its offices.', 'newspaper', 'blue', 6, 2, 1, '2026-10-20 06:59:36');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `message` varchar(255) NOT NULL,
  `link` varchar(255) DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `message`, `link`, `is_read`, `created_at`) VALUES
(20, 1, 'New request from Juan Dela Cruz: Illegal Business', '/LGU-Link/admin/appointments.php', 1, '2026-10-18 08:02:15'),
(27, 2, 'Your request \"Illegal Business\" was forwarded to Municipal Planning and Development Office.', '/LGU-Link/user/appointments.php', 1, '2026-10-18 12:56:14'),
(28, 24, 'New concern forwarded to your office: Illegal Business', '/LGU-Link/office/dashboard.php', 1, '2026-10-18 12:56:15'),
(29, 2, 'Your appointment \"Illegal Business\" is scheduled for October 30, 2026 at 10:00 AM.', '/LGU-Link/user/appointments.php', 1, '2026-10-18 13:07:28'),
(60, 1, 'Municipal Planning and Development Office requested a meeting: Housing Board Monthly Meeting', '/LGU-Link/admin/meetings.php', 1, '2026-10-18 13:37:26'),
(65, 24, 'Your meeting \"Housing Board Monthly Meeting\" was approved for October 30, 2026, 9:00 AM - 12:00 PM.', '/LGU-Link/office/meetings.php', 1, '2026-10-18 14:24:38'),
(66, 10, 'Municipal Planning and Development Office has called a meeting that your office is required to attend: Housing Board Monthly Meeting (October 30, 2026, 9:00 AM - 12:00 PM). Please acknowledge it.', '/LGU-Link/office/meetings.php', 0, '2026-10-18 14:24:39'),
(67, 11, 'Municipal Planning and Development Office has called a meeting that your office is required to attend: Housing Board Monthly Meeting (October 30, 2026, 9:00 AM - 12:00 PM). Please acknowledge it.', '/LGU-Link/office/meetings.php', 0, '2026-10-18 14:24:39'),
(68, 12, 'Municipal Planning and Development Office has called a meeting that your office is required to attend: Housing Board Monthly Meeting (October 30, 2026, 9:00 AM - 12:00 PM). Please acknowledge it.', '/LGU-Link/office/meetings.php', 1, '2026-10-18 14:24:39'),
(69, 13, 'Municipal Planning and Development Office has called a meeting that your office is required to attend: Housing Board Monthly Meeting (October 30, 2026, 9:00 AM - 12:00 PM). Please acknowledge it.', '/LGU-Link/office/meetings.php', 0, '2026-10-18 14:24:39'),
(70, 14, 'Municipal Planning and Development Office has called a meeting that your office is required to attend: Housing Board Monthly Meeting (October 30, 2026, 9:00 AM - 12:00 PM). Please acknowledge it.', '/LGU-Link/office/meetings.php', 1, '2026-10-18 14:24:39'),
(71, 15, 'Municipal Planning and Development Office has called a meeting that your office is required to attend: Housing Board Monthly Meeting (October 30, 2026, 9:00 AM - 12:00 PM). Please acknowledge it.', '/LGU-Link/office/meetings.php', 0, '2026-10-18 14:24:40'),
(72, 16, 'Municipal Planning and Development Office has called a meeting that your office is required to attend: Housing Board Monthly Meeting (October 30, 2026, 9:00 AM - 12:00 PM). Please acknowledge it.', '/LGU-Link/office/meetings.php', 0, '2026-10-18 14:24:40'),
(73, 17, 'Municipal Planning and Development Office has called a meeting that your office is required to attend: Housing Board Monthly Meeting (October 30, 2026, 9:00 AM - 12:00 PM). Please acknowledge it.', '/LGU-Link/office/meetings.php', 1, '2026-10-18 14:24:40'),
(74, 18, 'Municipal Planning and Development Office has called a meeting that your office is required to attend: Housing Board Monthly Meeting (October 30, 2026, 9:00 AM - 12:00 PM). Please acknowledge it.', '/LGU-Link/office/meetings.php', 0, '2026-10-18 14:24:40'),
(75, 19, 'Municipal Planning and Development Office has called a meeting that your office is required to attend: Housing Board Monthly Meeting (October 30, 2026, 9:00 AM - 12:00 PM). Please acknowledge it.', '/LGU-Link/office/meetings.php', 1, '2026-10-18 14:24:40'),
(76, 20, 'Municipal Planning and Development Office has called a meeting that your office is required to attend: Housing Board Monthly Meeting (October 30, 2026, 9:00 AM - 12:00 PM). Please acknowledge it.', '/LGU-Link/office/meetings.php', 0, '2026-10-18 14:24:40'),
(77, 21, 'Municipal Planning and Development Office has called a meeting that your office is required to attend: Housing Board Monthly Meeting (October 30, 2026, 9:00 AM - 12:00 PM). Please acknowledge it.', '/LGU-Link/office/meetings.php', 0, '2026-10-18 14:24:40'),
(78, 22, 'Municipal Planning and Development Office has called a meeting that your office is required to attend: Housing Board Monthly Meeting (October 30, 2026, 9:00 AM - 12:00 PM). Please acknowledge it.', '/LGU-Link/office/meetings.php', 0, '2026-10-18 14:24:40'),
(79, 23, 'Municipal Planning and Development Office has called a meeting that your office is required to attend: Housing Board Monthly Meeting (October 30, 2026, 9:00 AM - 12:00 PM). Please acknowledge it.', '/LGU-Link/office/meetings.php', 0, '2026-10-18 14:24:40'),
(80, 25, 'Municipal Planning and Development Office has called a meeting that your office is required to attend: Housing Board Monthly Meeting (October 30, 2026, 9:00 AM - 12:00 PM). Please acknowledge it.', '/LGU-Link/office/meetings.php', 0, '2026-10-18 14:24:40'),
(81, 26, 'Municipal Planning and Development Office has called a meeting that your office is required to attend: Housing Board Monthly Meeting (October 30, 2026, 9:00 AM - 12:00 PM). Please acknowledge it.', '/LGU-Link/office/meetings.php', 0, '2026-10-18 14:24:40'),
(82, 27, 'Municipal Planning and Development Office has called a meeting that your office is required to attend: Housing Board Monthly Meeting (October 30, 2026, 9:00 AM - 12:00 PM). Please acknowledge it.', '/LGU-Link/office/meetings.php', 0, '2026-10-18 14:24:41'),
(83, 28, 'Municipal Planning and Development Office has called a meeting that your office is required to attend: Housing Board Monthly Meeting (October 30, 2026, 9:00 AM - 12:00 PM). Please acknowledge it.', '/LGU-Link/office/meetings.php', 0, '2026-10-18 14:24:41'),
(84, 29, 'Municipal Planning and Development Office has called a meeting that your office is required to attend: Housing Board Monthly Meeting (October 30, 2026, 9:00 AM - 12:00 PM). Please acknowledge it.', '/LGU-Link/office/meetings.php', 0, '2026-10-18 14:24:41'),
(85, 30, 'Municipal Planning and Development Office has called a meeting that your office is required to attend: Housing Board Monthly Meeting (October 30, 2026, 9:00 AM - 12:00 PM). Please acknowledge it.', '/LGU-Link/office/meetings.php', 0, '2026-10-18 14:24:41'),
(86, 31, 'Municipal Planning and Development Office has called a meeting that your office is required to attend: Housing Board Monthly Meeting (October 30, 2026, 9:00 AM - 12:00 PM). Please acknowledge it.', '/LGU-Link/office/meetings.php', 0, '2026-10-18 14:24:41'),
(87, 32, 'Municipal Planning and Development Office has called a meeting that your office is required to attend: Housing Board Monthly Meeting (October 30, 2026, 9:00 AM - 12:00 PM). Please acknowledge it.', '/LGU-Link/office/meetings.php', 0, '2026-10-18 14:24:41'),
(88, 33, 'Municipal Planning and Development Office has called a meeting that your office is required to attend: Housing Board Monthly Meeting (October 30, 2026, 9:00 AM - 12:00 PM). Please acknowledge it.', '/LGU-Link/office/meetings.php', 0, '2026-10-18 14:24:41'),
(89, 34, 'Municipal Planning and Development Office has called a meeting that your office is required to attend: Housing Board Monthly Meeting (October 30, 2026, 9:00 AM - 12:00 PM). Please acknowledge it.', '/LGU-Link/office/meetings.php', 0, '2026-10-18 14:24:41'),
(90, 35, 'Municipal Planning and Development Office has called a meeting that your office is required to attend: Housing Board Monthly Meeting (October 30, 2026, 9:00 AM - 12:00 PM). Please acknowledge it.', '/LGU-Link/office/meetings.php', 0, '2026-10-18 14:24:41'),
(91, 36, 'Municipal Planning and Development Office has called a meeting that your office is required to attend: Housing Board Monthly Meeting (October 30, 2026, 9:00 AM - 12:00 PM). Please acknowledge it.', '/LGU-Link/office/meetings.php', 0, '2026-10-18 14:24:41'),
(92, 37, 'Municipal Planning and Development Office has called a meeting that your office is required to attend: Housing Board Monthly Meeting (October 30, 2026, 9:00 AM - 12:00 PM). Please acknowledge it.', '/LGU-Link/office/meetings.php', 0, '2026-10-18 14:24:41'),
(93, 24, 'Local Civil Registrar acknowledged your meeting \"Housing Board Monthly Meeting\".', '/LGU-Link/office/meetings.php', 1, '2026-10-18 14:25:10'),
(94, 1, 'New request from Juan Dela Cruz: LGU Link System', '/LGU-Link/admin/appointments.php', 1, '2026-10-19 11:33:30'),
(100, 1, 'New request from Juan Dela Cruz: adfa', '/LGU-Link/admin/appointments.php', 1, '2026-10-19 11:53:03'),
(101, 1, 'New request from Juan Dela Cruz: serdt', '/LGU-Link/admin/appointments.php', 1, '2026-10-19 11:53:32'),
(210, 1, 'Business Permit and Licensing Office sent a post for review: Business Permit Renewal Period', '/LGU-Link/admin/news-review.php?id=35', 1, '2026-10-20 12:15:34'),
(211, 14, 'Your post \"Business Permit Renewal Period\" was approved. It is now live.', '/LGU-Link/office/news-view.php?id=35', 1, '2026-10-20 12:20:40'),
(212, 1, 'Assessor\'s Office sent a post for review: Extension of tax discount', '/LGU-Link/admin/news-review.php?id=36', 1, '2026-10-20 12:57:11');

-- --------------------------------------------------------

--
-- Table structure for table `queues`
--

CREATE TABLE `queues` (
  `id` int(10) UNSIGNED NOT NULL,
  `department_id` int(10) UNSIGNED NOT NULL,
  `kind` enum('general','event') NOT NULL DEFAULT 'general',
  `general_flag` tinyint(4) DEFAULT NULL,
  `name` varchar(120) NOT NULL,
  `prefix` varchar(8) NOT NULL,
  `max_tickets` int(10) UNSIGNED DEFAULT NULL,
  `require_mobile` tinyint(1) NOT NULL DEFAULT 1,
  `opens_at` datetime DEFAULT NULL,
  `closes_at` datetime DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `qr_salt` char(32) DEFAULT NULL,
  `qr_printed_on` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `queues`
--

INSERT INTO `queues` (`id`, `department_id`, `kind`, `general_flag`, `name`, `prefix`, `max_tickets`, `require_mobile`, `opens_at`, `closes_at`, `is_active`, `created_at`, `qr_salt`, `qr_printed_on`) VALUES
(1, 1, 'general', 1, 'Walk-in queue', 'MO', NULL, 1, NULL, NULL, 1, '2026-10-20 15:33:16', 'fbc0447bdec4be589570c2bb1baf4b93', '2026-10-21');

-- --------------------------------------------------------

--
-- Table structure for table `queue_counters`
--

CREATE TABLE `queue_counters` (
  `queue_id` int(10) UNSIGNED NOT NULL,
  `queue_day` date NOT NULL,
  `last_number` int(10) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `queue_counters`
--

INSERT INTO `queue_counters` (`queue_id`, `queue_day`, `last_number`) VALUES
(1, '2026-09-21', 78),
(1, '2026-09-22', 73),
(1, '2026-09-23', 82),
(1, '2026-09-24', 61),
(1, '2026-09-25', 66),
(1, '2026-09-28', 96),
(1, '2026-09-29', 69),
(1, '2026-09-30', 81),
(1, '2026-10-01', 67),
(1, '2026-10-02', 66),
(1, '2026-10-05', 93),
(1, '2026-10-06', 74),
(1, '2026-10-07', 81),
(1, '2026-10-08', 62),
(1, '2026-10-09', 60),
(1, '2026-10-12', 82),
(1, '2026-10-13', 60),
(1, '2026-10-14', 72),
(1, '2026-10-15', 64),
(1, '2026-10-16', 63),
(1, '2026-10-19', 81),
(1, '2026-10-20', 68),
(1, '2026-10-21', 8);

-- --------------------------------------------------------

--
-- Table structure for table `queue_services`
--

CREATE TABLE `queue_services` (
  `id` int(10) UNSIGNED NOT NULL,
  `department_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(80) NOT NULL,
  `letter` char(1) NOT NULL,
  `staff_id` int(10) UNSIGNED DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `queue_services`
--

INSERT INTO `queue_services` (`id`, `department_id`, `name`, `letter`, `staff_id`, `sort_order`, `is_active`, `created_at`) VALUES
(1, 1, 'Mayor\'s Clearance', 'A', NULL, 1, 1, '2026-10-20 15:33:16'),
(2, 1, 'Business concerns', 'B', NULL, 2, 1, '2026-10-20 15:33:16'),
(3, 1, 'Personal concerns', 'C', NULL, 3, 1, '2026-10-20 15:33:16'),
(4, 1, 'Courtesy call', 'D', NULL, 4, 1, '2026-10-20 15:33:16'),
(5, 1, 'Financial assistance', 'E', NULL, 5, 1, '2026-10-20 15:33:16'),
(6, 1, 'Other', 'F', NULL, 7, 1, '2026-10-20 15:33:16');

-- --------------------------------------------------------

--
-- Table structure for table `queue_staff`
--

CREATE TABLE `queue_staff` (
  `id` int(10) UNSIGNED NOT NULL,
  `department_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(80) NOT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `queue_staff`
--

INSERT INTO `queue_staff` (`id`, `department_id`, `name`, `sort_order`, `is_active`, `created_at`) VALUES
(54, 1, 'Jean Frias', 1, 1, '2026-10-21 01:17:10'),
(55, 1, 'Sir Henjie', 2, 1, '2026-10-21 01:17:19'),
(56, 1, 'Angelu', 3, 1, '2026-10-21 01:17:36');

-- --------------------------------------------------------

--
-- Table structure for table `queue_tickets`
--

CREATE TABLE `queue_tickets` (
  `id` int(10) UNSIGNED NOT NULL,
  `queue_id` int(10) UNSIGNED NOT NULL,
  `queue_day` date NOT NULL,
  `number` int(10) UNSIGNED NOT NULL,
  `code` varchar(20) NOT NULL,
  `access_key` char(32) NOT NULL,
  `service_id` int(10) UNSIGNED DEFAULT NULL,
  `service_name` varchar(80) NOT NULL,
  `full_name` varchar(120) NOT NULL,
  `mobile` varchar(20) DEFAULT NULL,
  `phone_key` varchar(20) DEFAULT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `source` enum('self','staff') NOT NULL DEFAULT 'self',
  `status` enum('waiting','called','serving','done','skipped','cancelled','expired') NOT NULL DEFAULT 'waiting',
  `staff_id` int(10) UNSIGNED DEFAULT NULL,
  `staff_name` varchar(80) DEFAULT NULL,
  `is_priority` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `called_at` datetime DEFAULT NULL,
  `started_at` datetime DEFAULT NULL,
  `finished_at` datetime DEFAULT NULL,
  `is_demo` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `queue_tickets`
--

INSERT INTO `queue_tickets` (`id`, `queue_id`, `queue_day`, `number`, `code`, `access_key`, `service_id`, `service_name`, `full_name`, `mobile`, `phone_key`, `user_id`, `source`, `status`, `staff_id`, `staff_name`, `is_priority`, `created_at`, `called_at`, `started_at`, `finished_at`, `is_demo`) VALUES
(6542, 1, '2026-09-21', 1, 'MO-A001', '8d6206391ecd8409c20babc16c2a986f', 1, 'Mayor\'s Clearance', 'Ricardo Garcia', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-09-21 08:00:39', '2026-09-21 08:08:03', '2026-09-21 08:08:03', '2026-09-21 08:10:53', 1),
(6543, 1, '2026-09-21', 2, 'MO-B002', 'fbe2efb938ff76bd9eaa305f39ac6971', 2, 'Business concerns', 'Carmela Flores', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-09-21 08:03:23', '2026-09-21 08:08:03', '2026-09-21 08:08:03', '2026-09-21 08:30:04', 1),
(6544, 1, '2026-09-21', 3, 'MO-F003', '028d5090df84b434d5eb484a56e687c3', 6, 'Other', 'Felipe Bautista', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-09-21 08:25:56', '2026-09-21 08:27:06', '2026-09-21 08:27:06', '2026-09-21 08:40:37', 1),
(6545, 1, '2026-09-21', 4, 'MO-A004', '864c222aa95183873ef886f8348f642d', 1, 'Mayor\'s Clearance', 'Cynthia Torres', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-09-21 08:26:48', '2026-09-21 08:35:25', '2026-09-21 08:35:25', '2026-09-21 08:39:52', 1),
(6546, 1, '2026-09-21', 5, 'MO-A005', 'ce7483db468813f2c65b7882077e65ec', 1, 'Mayor\'s Clearance', 'Jose Dela Cruz', NULL, NULL, NULL, 'self', 'cancelled', NULL, NULL, 0, '2026-09-21 08:30:21', NULL, NULL, NULL, 1),
(6547, 1, '2026-09-21', 6, 'MO-E006', 'ad8962c3fe8fa63368762f7ba542789b', 5, 'Financial assistance', 'Teresita Pascual', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-09-21 08:39:38', '2026-09-21 08:42:25', '2026-09-21 08:42:25', '2026-09-21 08:47:49', 1),
(6548, 1, '2026-09-21', 7, 'MO-E007', '925241518d232a1897c8ea89c8f14a60', 5, 'Financial assistance', 'Danilo Reyes', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-09-21 08:40:01', '2026-09-21 08:43:43', '2026-09-21 08:43:43', '2026-09-21 08:56:54', 1),
(6549, 1, '2026-09-21', 8, 'MO-A008', '6bf7efa61028ec3d60a13954fba23f74', 1, 'Mayor\'s Clearance', 'Lourdes Ramos', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-09-21 08:46:49', '2026-09-21 08:51:38', '2026-09-21 08:51:38', '2026-09-21 08:57:34', 1),
(6550, 1, '2026-09-21', 9, 'MO-B009', '0e663dc7d5f20654f0ec9821c6b10526', 2, 'Business concerns', 'Ramon Santos', NULL, NULL, NULL, 'self', 'cancelled', NULL, NULL, 0, '2026-09-21 08:50:08', NULL, NULL, NULL, 1),
(6551, 1, '2026-09-21', 10, 'MO-C010', '427129f3ff80b50b9d97eb33ffc4cbf9', 3, 'Personal concerns', 'Josefina Castillo', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-09-21 09:18:04', '2026-09-21 09:26:00', '2026-09-21 09:26:00', '2026-09-21 09:32:12', 1),
(6552, 1, '2026-09-21', 11, 'MO-A011', 'fdf3ebe435bcbf492f98c07adfd71f97', 1, 'Mayor\'s Clearance', 'Manuel Cortez', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-09-21 09:20:36', '2026-09-21 09:35:49', '2026-09-21 09:35:49', '2026-09-21 09:39:10', 1),
(6553, 1, '2026-09-21', 12, 'MO-A012', '4788cd6fbd342e9adf3d89c7cca26585', 1, 'Mayor\'s Clearance', 'Ana Domingo', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-09-21 09:28:13', '2026-09-21 09:37:02', '2026-09-21 09:37:02', '2026-09-21 09:46:39', 1),
(6554, 1, '2026-09-21', 13, 'MO-E013', '355e7eb7defbcffacc8e342b7b87e2bb', 5, 'Financial assistance', 'Rogelio Mercado', NULL, NULL, NULL, 'self', 'skipped', 54, 'Jean Frias', 0, '2026-09-21 09:37:13', '2026-09-21 09:42:54', NULL, NULL, 1),
(6555, 1, '2026-09-21', 14, 'MO-A014', '2791adf8f654cb38bcc16b58d87a1a56', 1, 'Mayor\'s Clearance', 'Grace Navarro', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-09-21 09:37:55', '2026-09-21 09:52:48', '2026-09-21 09:52:48', '2026-09-21 09:56:56', 1),
(6556, 1, '2026-09-21', 15, 'MO-D015', 'c730e392508dfaa6b8c7f1420dd9d5cb', 4, 'Courtesy call', 'Gerardo Salazar', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-09-21 09:38:55', '2026-09-21 10:09:10', '2026-09-21 10:09:10', '2026-09-21 10:28:23', 1),
(6557, 1, '2026-09-21', 16, 'MO-A016', 'bb9ab3da61b81f33f8701e7f975f1318', 1, 'Mayor\'s Clearance', 'Liza Aquino', NULL, NULL, NULL, 'staff', 'done', 55, 'Sir Henjie', 0, '2026-09-21 09:42:40', '2026-09-21 09:59:14', '2026-09-21 09:59:14', '2026-09-21 10:02:00', 1),
(6558, 1, '2026-09-21', 17, 'MO-E017', 'efc19f85583731c0e28923f96561dc72', 5, 'Financial assistance', 'Antonio Fernandez', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-09-21 10:01:33', '2026-09-21 10:20:09', '2026-09-21 10:20:09', '2026-09-21 10:24:00', 1),
(6559, 1, '2026-09-21', 18, 'MO-D018', '63b0c038f81b0e9be26885d62d4dcbe7', 4, 'Courtesy call', 'Elena Cruz', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-09-21 10:04:47', '2026-09-21 10:14:54', '2026-09-21 10:14:54', '2026-09-21 10:28:48', 1),
(6560, 1, '2026-09-21', 19, 'MO-A019', 'ea9862eea6b318f8213389d7a00ed1e9', 1, 'Mayor\'s Clearance', 'Pedro Gonzales', NULL, NULL, NULL, 'staff', 'done', 54, 'Jean Frias', 0, '2026-09-21 10:04:52', '2026-09-21 10:11:09', '2026-09-21 10:11:09', '2026-09-21 10:14:07', 1),
(6561, 1, '2026-09-21', 20, 'MO-A020', 'cd53354e9fd80c44cd422ec095bdcf81', 1, 'Mayor\'s Clearance', 'Marites Mendoza', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-09-21 10:12:02', '2026-09-21 10:18:36', '2026-09-21 10:18:36', '2026-09-21 10:25:24', 1),
(6562, 1, '2026-09-21', 21, 'MO-F021', '59792cd8e7da40459c2c52f89727aeb1', 6, 'Other', 'Alfredo Rivera', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-09-21 10:12:15', '2026-09-21 10:16:01', '2026-09-21 10:16:01', '2026-09-21 10:18:11', 1),
(6563, 1, '2026-09-21', 22, 'MO-C022', '8ce8eb8b233f2f5d9e984fdea5381e35', 3, 'Personal concerns', 'Maria Villanueva', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-09-21 10:15:45', '2026-09-21 10:16:15', '2026-09-21 10:16:15', '2026-09-21 10:35:07', 1),
(6564, 1, '2026-09-21', 23, 'MO-B023', '4aa8af877d4efb4a68e46c4919585847', 2, 'Business concerns', 'Eduardo Lopez', NULL, NULL, NULL, 'staff', 'skipped', 55, 'Sir Henjie', 0, '2026-09-21 10:16:43', '2026-09-21 10:33:30', NULL, NULL, 1),
(6565, 1, '2026-09-21', 24, 'MO-E024', '10c448ddd7602c7b6d6fb39a2194d4f0', 5, 'Financial assistance', 'Rosario Garcia', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-09-21 10:17:41', '2026-09-21 10:32:48', '2026-09-21 10:32:48', '2026-09-21 10:40:51', 1),
(6566, 1, '2026-09-21', 25, 'MO-F025', '579fcdf3fc048c96726bb4a7f5f94680', 6, 'Other', 'Ricardo Flores', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-09-21 10:25:45', '2026-09-21 10:29:45', '2026-09-21 10:29:45', '2026-09-21 10:35:22', 1),
(6567, 1, '2026-09-21', 26, 'MO-A026', '91761cc4f03509f45a6b8d425ff4aee6', 1, 'Mayor\'s Clearance', 'Carmela Bautista', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-09-21 10:30:25', '2026-09-21 10:37:21', '2026-09-21 10:37:21', '2026-09-21 10:44:22', 1),
(6568, 1, '2026-09-21', 27, 'MO-B027', '86b1e61e57ed4763af9be670b91c62fd', 2, 'Business concerns', 'Felipe Torres', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-09-21 10:32:22', '2026-09-21 10:46:20', '2026-09-21 10:46:20', '2026-09-21 10:59:37', 1),
(6569, 1, '2026-09-21', 28, 'MO-D028', '04fc4278f0df0945238b63fb78a6cd5a', 4, 'Courtesy call', 'Cynthia Dela Cruz', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-09-21 10:32:44', '2026-09-21 10:35:52', '2026-09-21 10:35:52', '2026-09-21 11:01:47', 1),
(6570, 1, '2026-09-21', 29, 'MO-E029', '5d8829b6a0538b1bd56f1f33c4016566', 5, 'Financial assistance', 'Jose Pascual', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-09-21 10:35:18', '2026-09-21 10:57:54', '2026-09-21 10:57:54', '2026-09-21 11:10:55', 1),
(6571, 1, '2026-09-21', 30, 'MO-E030', 'ce9a06baedd9aaf2f03b02de61505bb0', 5, 'Financial assistance', 'Teresita Reyes', NULL, NULL, NULL, 'staff', 'done', 54, 'Jean Frias', 0, '2026-09-21 10:48:22', '2026-09-21 11:13:34', '2026-09-21 11:13:34', '2026-09-21 11:17:40', 1),
(6572, 1, '2026-09-21', 31, 'MO-E031', '6f510360393280791424f49b6fcf201a', 5, 'Financial assistance', 'Danilo Ramos', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-09-21 10:57:15', '2026-09-21 11:05:20', '2026-09-21 11:05:20', '2026-09-21 11:15:25', 1),
(6573, 1, '2026-09-21', 32, 'MO-B032', '81015d0384a7931af17386beb563b1c0', 2, 'Business concerns', 'Lourdes Santos', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-09-21 11:02:46', '2026-09-21 11:06:45', '2026-09-21 11:06:45', '2026-09-21 11:19:35', 1),
(6574, 1, '2026-09-21', 33, 'MO-E033', '42f42f4de251ce710614dd8631d6e12c', 5, 'Financial assistance', 'Ramon Castillo', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-09-21 11:03:11', '2026-09-21 11:10:09', '2026-09-21 11:10:09', '2026-09-21 11:19:28', 1),
(6575, 1, '2026-09-21', 34, 'MO-E034', 'd0b770dc9f366cd426ec7e2a45633a06', 5, 'Financial assistance', 'Josefina Cortez', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-09-21 11:35:11', '2026-09-21 11:41:39', '2026-09-21 11:41:39', '2026-09-21 11:48:43', 1),
(6576, 1, '2026-09-21', 35, 'MO-C035', '768c75d296f7ba4accac082629a77742', 3, 'Personal concerns', 'Manuel Domingo', NULL, NULL, NULL, 'staff', 'done', 55, 'Sir Henjie', 0, '2026-09-21 11:37:10', '2026-09-21 11:39:38', '2026-09-21 11:39:38', '2026-09-21 11:41:57', 1),
(6577, 1, '2026-09-21', 36, 'MO-A036', 'd2a3dfbc9826883e93dbda55ad387954', 1, 'Mayor\'s Clearance', 'Ana Mercado', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-09-21 11:42:22', '2026-09-21 11:47:40', '2026-09-21 11:47:40', '2026-09-21 11:58:34', 1),
(6578, 1, '2026-09-21', 37, 'MO-B037', '3867cdda01de667ab99663feb8fcf1fc', 2, 'Business concerns', 'Rogelio Navarro', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-09-21 11:45:13', '2026-09-21 11:49:25', '2026-09-21 11:49:25', '2026-09-21 11:58:36', 1),
(6579, 1, '2026-09-21', 38, 'MO-A038', '6fbf6a1f4dd20d4b1e5fc10bbec45574', 1, 'Mayor\'s Clearance', 'Grace Salazar', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-09-21 11:46:06', '2026-09-21 11:47:50', '2026-09-21 11:47:50', '2026-09-21 11:50:43', 1),
(6580, 1, '2026-09-21', 39, 'MO-E039', 'd7fc2823ec2ee971d5e72e0599a0410c', 5, 'Financial assistance', 'Gerardo Aquino', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-09-21 11:56:55', '2026-09-21 12:03:33', '2026-09-21 12:03:33', '2026-09-21 12:13:00', 1),
(6581, 1, '2026-09-21', 40, 'MO-A040', 'd5da1dc7c66713112b66fad61251ff48', 1, 'Mayor\'s Clearance', 'Liza Fernandez', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-09-21 11:57:00', '2026-09-21 11:58:25', '2026-09-21 11:58:25', '2026-09-21 12:04:11', 1),
(6582, 1, '2026-09-21', 41, 'MO-D041', 'fbaae4cb31ed0294159258e642353bbb', 4, 'Courtesy call', 'Antonio Cruz', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-09-21 12:08:23', '2026-09-21 12:13:46', '2026-09-21 12:13:46', '2026-09-21 12:39:23', 1),
(6583, 1, '2026-09-21', 42, 'MO-E042', 'a53eaf6d1a36aee678b9d68b72dee3db', 5, 'Financial assistance', 'Elena Gonzales', NULL, NULL, NULL, 'staff', 'done', 56, 'Angelu', 0, '2026-09-21 12:14:05', '2026-09-21 12:18:41', '2026-09-21 12:18:41', '2026-09-21 12:27:21', 1),
(6584, 1, '2026-09-21', 43, 'MO-A043', '297d5c8ac9518ae0497eb8f19fe8f577', 1, 'Mayor\'s Clearance', 'Pedro Mendoza', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-09-21 12:17:40', '2026-09-21 12:19:49', '2026-09-21 12:19:49', '2026-09-21 12:37:05', 1),
(6585, 1, '2026-09-21', 44, 'MO-A044', '9d6b0384892c9feea1b706b7a28f73ba', 1, 'Mayor\'s Clearance', 'Marites Rivera', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-09-21 12:34:10', '2026-09-21 12:41:44', '2026-09-21 12:41:44', '2026-09-21 12:48:59', 1),
(6586, 1, '2026-09-21', 45, 'MO-C045', 'e8f07faf2a556cd851a370fa8cc12cac', 3, 'Personal concerns', 'Alfredo Villanueva', NULL, NULL, NULL, 'staff', 'done', 56, 'Angelu', 0, '2026-09-21 12:36:56', '2026-09-21 12:40:19', '2026-09-21 12:40:19', '2026-09-21 12:47:28', 1),
(6587, 1, '2026-09-21', 46, 'MO-A046', '10022016b2f4a008152824939a49f4b7', 1, 'Mayor\'s Clearance', 'Maria Lopez', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-09-21 12:39:48', '2026-09-21 12:46:13', '2026-09-21 12:46:13', '2026-09-21 12:54:54', 1),
(6588, 1, '2026-09-21', 47, 'MO-A047', 'a7935170efe9bc25b605e88574e0da5e', 1, 'Mayor\'s Clearance', 'Eduardo Garcia', NULL, NULL, NULL, 'staff', 'done', 55, 'Sir Henjie', 0, '2026-09-21 12:52:22', '2026-09-21 12:54:23', '2026-09-21 12:54:23', '2026-09-21 12:57:20', 1),
(6589, 1, '2026-09-21', 48, 'MO-E048', '53fd978feb83872408552af3f1b666a9', 5, 'Financial assistance', 'Rosario Flores', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-09-21 13:02:27', '2026-09-21 13:13:30', '2026-09-21 13:13:30', '2026-09-21 13:22:34', 1),
(6590, 1, '2026-09-21', 49, 'MO-A049', '4eacc1d1af512be81d038edfa777d2e4', 1, 'Mayor\'s Clearance', 'Ricardo Bautista', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-09-21 13:12:37', '2026-09-21 13:20:08', '2026-09-21 13:20:08', '2026-09-21 13:27:19', 1),
(6591, 1, '2026-09-21', 50, 'MO-D050', '34088c44c95018d4b0fe2831491f133e', 4, 'Courtesy call', 'Carmela Torres', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-09-21 13:24:11', '2026-09-21 13:41:36', '2026-09-21 13:41:36', '2026-09-21 13:50:21', 1),
(6592, 1, '2026-09-21', 51, 'MO-A051', '15fa8443f7c48a5a8f86b1fb1384baf8', 1, 'Mayor\'s Clearance', 'Felipe Dela Cruz', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-09-21 13:28:09', '2026-09-21 13:31:13', '2026-09-21 13:31:13', '2026-09-21 13:34:19', 1),
(6593, 1, '2026-09-21', 52, 'MO-E052', 'ec8ad134d4138502ada8315d6fd6dca2', 5, 'Financial assistance', 'Cynthia Pascual', NULL, NULL, NULL, 'self', 'cancelled', NULL, NULL, 0, '2026-09-21 13:28:31', NULL, NULL, NULL, 1),
(6594, 1, '2026-09-21', 53, 'MO-A053', 'ca72f40876a890d2a262c8f1e7f6d6e0', 1, 'Mayor\'s Clearance', 'Jose Reyes', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-09-21 13:34:33', '2026-09-21 13:37:36', '2026-09-21 13:37:36', '2026-09-21 13:42:32', 1),
(6595, 1, '2026-09-21', 54, 'MO-D054', '23902632b35401a4adfa650f25edb395', 4, 'Courtesy call', 'Teresita Ramos', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-09-21 13:44:43', '2026-09-21 13:51:06', '2026-09-21 13:51:06', '2026-09-21 14:01:58', 1),
(6596, 1, '2026-09-21', 55, 'MO-E055', '338e956255a5f273d5f3a2f634c3a2d2', 5, 'Financial assistance', 'Danilo Santos', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-09-21 14:07:23', '2026-09-21 14:12:37', '2026-09-21 14:12:37', '2026-09-21 14:20:12', 1),
(6597, 1, '2026-09-21', 56, 'MO-A056', '4e15ef25db009501fe36941248d8e364', 1, 'Mayor\'s Clearance', 'Lourdes Castillo', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-09-21 14:13:31', '2026-09-21 14:14:26', '2026-09-21 14:14:26', '2026-09-21 14:20:54', 1),
(6598, 1, '2026-09-21', 57, 'MO-D057', '41cb14ac1b1919372fd507ee07d140fc', 4, 'Courtesy call', 'Ramon Cortez', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-09-21 14:13:41', '2026-09-21 14:14:50', '2026-09-21 14:14:50', '2026-09-21 14:59:50', 1),
(6599, 1, '2026-09-21', 58, 'MO-D058', 'e92c45a9ccf8ee9ca9c5e1b963dbd038', 4, 'Courtesy call', 'Josefina Domingo', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-09-21 14:14:02', '2026-09-21 14:22:57', '2026-09-21 14:22:57', '2026-09-21 14:28:36', 1),
(6600, 1, '2026-09-21', 59, 'MO-B059', '04dcc99941ec41ff512b7f6804d71536', 2, 'Business concerns', 'Manuel Mercado', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-09-21 14:30:24', '2026-09-21 14:32:25', '2026-09-21 14:32:25', '2026-09-21 14:48:28', 1),
(6601, 1, '2026-09-21', 60, 'MO-F060', 'e82f328934892f8b507d0932fde94649', 6, 'Other', 'Ana Navarro', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-09-21 14:41:00', '2026-09-21 14:47:36', '2026-09-21 14:47:36', '2026-09-21 14:53:00', 1),
(6602, 1, '2026-09-21', 61, 'MO-E061', 'd035d960b1392325a6084053f758b259', 5, 'Financial assistance', 'Rogelio Salazar', NULL, NULL, NULL, 'self', 'cancelled', NULL, NULL, 0, '2026-09-21 14:43:27', NULL, NULL, NULL, 1),
(6603, 1, '2026-09-21', 62, 'MO-C062', '7bfb70a62f47a726054149ca8c8ab641', 3, 'Personal concerns', 'Grace Aquino', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-09-21 14:45:04', '2026-09-21 14:57:51', '2026-09-21 14:57:51', '2026-09-21 15:05:47', 1),
(6604, 1, '2026-09-21', 63, 'MO-A063', '4adcb38bc98c34da2f6bf24af24b9bd4', 1, 'Mayor\'s Clearance', 'Gerardo Fernandez', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-09-21 14:45:45', '2026-09-21 14:49:41', '2026-09-21 14:49:41', '2026-09-21 14:54:29', 1),
(6605, 1, '2026-09-21', 64, 'MO-A064', 'dfb532ac75d469e1f8f833932a4c4d26', 1, 'Mayor\'s Clearance', 'Liza Cruz', NULL, NULL, NULL, 'self', 'skipped', 54, 'Jean Frias', 0, '2026-09-21 15:05:40', '2026-09-21 15:08:45', NULL, NULL, 1),
(6606, 1, '2026-09-21', 65, 'MO-D065', 'bd787d1fefe8af87ee65e568e043f4f8', 4, 'Courtesy call', 'Antonio Gonzales', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-09-21 15:27:30', '2026-09-21 15:31:38', '2026-09-21 15:31:38', '2026-09-21 15:44:58', 1),
(6607, 1, '2026-09-21', 66, 'MO-C066', 'a606aeaf039d1c5b388fdbf1eb6d9705', 3, 'Personal concerns', 'Elena Mendoza', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-09-21 15:29:29', '2026-09-21 15:34:00', '2026-09-21 15:34:00', '2026-09-21 15:40:01', 1),
(6608, 1, '2026-09-21', 67, 'MO-A067', '26f0e4739838e34e25d9398fb9d9d20a', 1, 'Mayor\'s Clearance', 'Pedro Rivera', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-09-21 15:30:13', '2026-09-21 15:31:34', '2026-09-21 15:31:34', '2026-09-21 15:35:45', 1),
(6609, 1, '2026-09-21', 68, 'MO-B068', 'ada707f93cdf6c28ceb517aadfbfbcf3', 2, 'Business concerns', 'Marites Villanueva', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-09-21 15:30:30', '2026-09-21 15:36:42', '2026-09-21 15:36:42', '2026-09-21 15:58:18', 1),
(6610, 1, '2026-09-21', 69, 'MO-F069', '71297d0e53e29c82a1312eeac1e96bb6', 6, 'Other', 'Alfredo Lopez', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-09-21 15:37:48', '2026-09-21 15:42:13', '2026-09-21 15:42:13', '2026-09-21 15:45:35', 1),
(6611, 1, '2026-09-21', 70, 'MO-F070', '73fe362ec63b765d66f8f333566d84da', 6, 'Other', 'Maria Garcia', NULL, NULL, NULL, 'staff', 'skipped', 56, 'Angelu', 0, '2026-09-21 15:39:05', '2026-09-21 15:46:19', NULL, NULL, 1),
(6612, 1, '2026-09-21', 71, 'MO-F071', '1dbdd0092bd88fa1c8118d28211ea23f', 6, 'Other', 'Eduardo Flores', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-09-21 15:40:31', '2026-09-21 15:43:54', '2026-09-21 15:43:54', '2026-09-21 15:52:59', 1),
(6613, 1, '2026-09-21', 72, 'MO-B072', '051e49f774f5ff087c37390622355848', 2, 'Business concerns', 'Rosario Bautista', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-09-21 15:42:07', '2026-09-21 15:51:45', '2026-09-21 15:51:45', '2026-09-21 16:06:49', 1),
(6614, 1, '2026-09-21', 73, 'MO-A073', 'df43dc6a627cf03b7f8400ee4d444890', 1, 'Mayor\'s Clearance', 'Ricardo Torres', NULL, NULL, NULL, 'staff', 'done', 56, 'Angelu', 0, '2026-09-21 15:48:41', '2026-09-21 15:51:10', '2026-09-21 15:51:10', '2026-09-21 15:57:02', 1),
(6615, 1, '2026-09-21', 74, 'MO-A074', '3f4f7318be17b06377e8ab38628f5714', 1, 'Mayor\'s Clearance', 'Carmela Dela Cruz', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-09-21 15:55:01', '2026-09-21 15:57:41', '2026-09-21 15:57:41', '2026-09-21 16:02:55', 1),
(6616, 1, '2026-09-21', 75, 'MO-E075', 'ed033688b91d7bdbe5ad9c04a48cf721', 5, 'Financial assistance', 'Felipe Pascual', NULL, NULL, NULL, 'staff', 'done', 54, 'Jean Frias', 0, '2026-09-21 16:01:50', '2026-09-21 16:03:16', '2026-09-21 16:03:16', '2026-09-21 16:11:37', 1),
(6617, 1, '2026-09-21', 76, 'MO-B076', '3a7517b4c718f71fd90e5ff0a6060afa', 2, 'Business concerns', 'Cynthia Reyes', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-09-21 16:06:11', '2026-09-21 16:08:24', '2026-09-21 16:08:24', '2026-09-21 16:16:57', 1),
(6618, 1, '2026-09-21', 77, 'MO-C077', '744869ba8efb3edfda1bc29fd43a141d', 3, 'Personal concerns', 'Jose Ramos', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-09-21 16:52:23', '2026-09-21 16:52:53', '2026-09-21 16:52:53', '2026-09-21 16:55:27', 1),
(6619, 1, '2026-09-21', 78, 'MO-A078', 'fc9ff6c19e4039d50a18efdb387cbe19', 1, 'Mayor\'s Clearance', 'Teresita Santos', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-09-21 16:58:38', '2026-09-21 17:08:19', '2026-09-21 17:08:19', '2026-09-21 17:11:26', 1),
(6620, 1, '2026-09-22', 1, 'MO-C001', '1cba7172797012dd3ffc66733e200944', 3, 'Personal concerns', 'Lourdes Garcia', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-09-22 08:08:23', '2026-09-22 08:11:17', '2026-09-22 08:11:17', '2026-09-22 08:16:20', 1),
(6621, 1, '2026-09-22', 2, 'MO-A002', '16ba089cf36835d2e63b2ad1a46290f8', 1, 'Mayor\'s Clearance', 'Ramon Flores', NULL, NULL, NULL, 'staff', 'done', 55, 'Sir Henjie', 0, '2026-09-22 08:21:52', '2026-09-22 08:25:17', '2026-09-22 08:25:17', '2026-09-22 08:32:33', 1),
(6622, 1, '2026-09-22', 3, 'MO-C003', '08c59827b68a68949fb16beb8a8562ac', 3, 'Personal concerns', 'Josefina Bautista', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-09-22 08:22:11', '2026-09-22 08:26:04', '2026-09-22 08:26:04', '2026-09-22 08:37:14', 1),
(6623, 1, '2026-09-22', 4, 'MO-A004', 'abf4653dc168f50c28063d892c9899a4', 1, 'Mayor\'s Clearance', 'Manuel Torres', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-09-22 08:26:45', '2026-09-22 08:27:26', '2026-09-22 08:27:26', '2026-09-22 08:32:31', 1),
(6624, 1, '2026-09-22', 5, 'MO-A005', 'd2971782621c30983ba43b9779198af5', 1, 'Mayor\'s Clearance', 'Ana Dela Cruz', NULL, NULL, NULL, 'staff', 'done', 55, 'Sir Henjie', 0, '2026-09-22 08:39:58', '2026-09-22 08:41:59', '2026-09-22 08:41:59', '2026-09-22 08:44:49', 1),
(6625, 1, '2026-09-22', 6, 'MO-A006', '60bd045352e638f8bf760cfdbbf62e47', 1, 'Mayor\'s Clearance', 'Rogelio Pascual', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-09-22 08:46:54', '2026-09-22 08:50:06', '2026-09-22 08:50:06', '2026-09-22 08:54:39', 1),
(6626, 1, '2026-09-22', 7, 'MO-D007', 'ba4d149e93a1aed3cdfe244883cbaf24', 4, 'Courtesy call', 'Grace Reyes', NULL, NULL, NULL, 'staff', 'done', 56, 'Angelu', 0, '2026-09-22 09:04:26', '2026-09-22 09:15:56', '2026-09-22 09:15:56', '2026-09-22 09:24:08', 1),
(6627, 1, '2026-09-22', 8, 'MO-E008', '1bff8ce654d04fd18a43cce3b52f6ca5', 5, 'Financial assistance', 'Gerardo Ramos', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-09-22 09:11:17', '2026-09-22 09:20:32', '2026-09-22 09:20:32', '2026-09-22 09:37:50', 1),
(6628, 1, '2026-09-22', 9, 'MO-E009', '0c39d00eb8a00db50d486ca99b39a103', 5, 'Financial assistance', 'Liza Santos', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-09-22 09:13:06', '2026-09-22 09:20:19', '2026-09-22 09:20:19', '2026-09-22 09:22:09', 1),
(6629, 1, '2026-09-22', 10, 'MO-A010', 'e494990debe1761196ca21c0bb669083', 1, 'Mayor\'s Clearance', 'Antonio Castillo', NULL, NULL, NULL, 'staff', 'done', 56, 'Angelu', 0, '2026-09-22 09:18:59', '2026-09-22 09:22:54', '2026-09-22 09:22:54', '2026-09-22 09:30:36', 1),
(6630, 1, '2026-09-22', 11, 'MO-B011', 'fdfe433eed852834bad74624c43ddc9e', 2, 'Business concerns', 'Elena Cortez', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-09-22 09:19:51', '2026-09-22 09:23:13', '2026-09-22 09:23:13', '2026-09-22 09:25:57', 1),
(6631, 1, '2026-09-22', 12, 'MO-B012', '532e4cfbd225900c62a9f60733c6daa7', 2, 'Business concerns', 'Pedro Domingo', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-09-22 09:20:07', '2026-09-22 09:33:33', '2026-09-22 09:33:33', '2026-09-22 09:39:26', 1),
(6632, 1, '2026-09-22', 13, 'MO-A013', '87737032c5ca054fc21c1095f0c9253d', 1, 'Mayor\'s Clearance', 'Marites Mercado', NULL, NULL, NULL, 'staff', 'done', 54, 'Jean Frias', 0, '2026-09-22 09:24:17', '2026-09-22 09:47:58', '2026-09-22 09:47:58', '2026-09-22 09:54:15', 1),
(6633, 1, '2026-09-22', 14, 'MO-B014', '6faac63a3058bd165172215c0120ee24', 2, 'Business concerns', 'Alfredo Navarro', NULL, NULL, NULL, 'staff', 'done', 55, 'Sir Henjie', 0, '2026-09-22 09:42:38', '2026-09-22 09:48:02', '2026-09-22 09:48:02', '2026-09-22 09:54:16', 1),
(6634, 1, '2026-09-22', 15, 'MO-E015', '7a331279181fdabe8b7a7fb6a8c18d7f', 5, 'Financial assistance', 'Maria Salazar', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-09-22 09:44:12', '2026-09-22 10:15:21', '2026-09-22 10:15:21', '2026-09-22 10:19:47', 1),
(6635, 1, '2026-09-22', 16, 'MO-B016', 'f6f9c77a18b149b542b8de22ba1a1e47', 2, 'Business concerns', 'Eduardo Aquino', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-09-22 09:47:49', '2026-09-22 09:59:13', '2026-09-22 09:59:13', '2026-09-22 10:01:46', 1),
(6636, 1, '2026-09-22', 17, 'MO-E017', 'fdcde6a28359afdbd9e1f76704e568d8', 5, 'Financial assistance', 'Rosario Fernandez', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-09-22 09:50:51', '2026-09-22 10:03:53', '2026-09-22 10:03:53', '2026-09-22 10:08:29', 1),
(6637, 1, '2026-09-22', 18, 'MO-A018', 'd36bd11f9299a1b6f4649f0aac14c08c', 1, 'Mayor\'s Clearance', 'Ricardo Cruz', NULL, NULL, NULL, 'self', 'skipped', 56, 'Angelu', 0, '2026-09-22 09:51:55', '2026-09-22 10:20:52', NULL, NULL, 1),
(6638, 1, '2026-09-22', 19, 'MO-A019', 'a088a980afbbeafceba4ab24d3e91d82', 1, 'Mayor\'s Clearance', 'Carmela Gonzales', NULL, NULL, NULL, 'staff', 'done', 54, 'Jean Frias', 0, '2026-09-22 09:53:47', '2026-09-22 10:04:42', '2026-09-22 10:04:42', '2026-09-22 10:10:41', 1),
(6639, 1, '2026-09-22', 20, 'MO-E020', '166214b307ca2f97732e21172b31988d', 5, 'Financial assistance', 'Felipe Mendoza', NULL, NULL, NULL, 'staff', 'done', 55, 'Sir Henjie', 0, '2026-09-22 09:54:35', '2026-09-22 10:00:15', '2026-09-22 10:00:15', '2026-09-22 10:08:47', 1),
(6640, 1, '2026-09-22', 21, 'MO-A021', '94469213d79d2c9d4e217b1ae2235b9e', 1, 'Mayor\'s Clearance', 'Cynthia Rivera', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-09-22 09:59:35', '2026-09-22 10:03:23', '2026-09-22 10:03:23', '2026-09-22 10:07:42', 1),
(6641, 1, '2026-09-22', 22, 'MO-A022', '76fcbb3b4ab6fc4feb60a3066e341b01', 1, 'Mayor\'s Clearance', 'Jose Villanueva', NULL, NULL, NULL, 'staff', 'done', 54, 'Jean Frias', 0, '2026-09-22 10:00:19', '2026-09-22 10:06:04', '2026-09-22 10:06:04', '2026-09-22 10:12:36', 1),
(6642, 1, '2026-09-22', 23, 'MO-A023', '6a88d32d20e3fa9b379a875c51f56887', 1, 'Mayor\'s Clearance', 'Teresita Lopez', NULL, NULL, NULL, 'staff', 'done', 54, 'Jean Frias', 0, '2026-09-22 10:01:15', '2026-09-22 10:08:55', '2026-09-22 10:08:55', '2026-09-22 10:12:49', 1),
(6643, 1, '2026-09-22', 24, 'MO-B024', 'bc60bdb13c13fbed3b2e7b285e9dbc20', 2, 'Business concerns', 'Danilo Garcia', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-09-22 10:01:51', '2026-09-22 10:04:13', '2026-09-22 10:04:13', '2026-09-22 10:12:25', 1),
(6644, 1, '2026-09-22', 25, 'MO-E025', 'da28389aad50c3b789180d7c9774f064', 5, 'Financial assistance', 'Lourdes Flores', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-09-22 10:06:02', '2026-09-22 10:27:10', '2026-09-22 10:27:10', '2026-09-22 10:35:52', 1),
(6645, 1, '2026-09-22', 26, 'MO-A026', '08e176bb71e60b5db5b1bec03a5e9185', 1, 'Mayor\'s Clearance', 'Ramon Bautista', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-09-22 10:27:58', '2026-09-22 10:38:32', '2026-09-22 10:38:32', '2026-09-22 10:44:35', 1),
(6646, 1, '2026-09-22', 27, 'MO-B027', '3ad63eb39d8bf70b377d89f6643afc82', 2, 'Business concerns', 'Josefina Torres', NULL, NULL, NULL, 'staff', 'skipped', 55, 'Sir Henjie', 0, '2026-09-22 10:29:07', '2026-09-22 10:32:42', NULL, NULL, 1),
(6647, 1, '2026-09-22', 28, 'MO-E028', 'b91631280b66723de4027d4acc699a6a', 5, 'Financial assistance', 'Manuel Dela Cruz', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-09-22 10:29:29', '2026-09-22 10:31:46', '2026-09-22 10:31:46', '2026-09-22 10:37:43', 1),
(6648, 1, '2026-09-22', 29, 'MO-E029', '67ce5dde7894901a7acc40be4676d2f5', 5, 'Financial assistance', 'Ana Pascual', NULL, NULL, NULL, 'self', 'skipped', 56, 'Angelu', 0, '2026-09-22 10:31:19', '2026-09-22 10:40:57', NULL, NULL, 1),
(6649, 1, '2026-09-22', 30, 'MO-A030', '0665fa57e455ca224eb001c5f449068e', 1, 'Mayor\'s Clearance', 'Rogelio Reyes', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-09-22 10:43:55', '2026-09-22 10:46:51', '2026-09-22 10:46:51', '2026-09-22 10:56:26', 1),
(6650, 1, '2026-09-22', 31, 'MO-E031', 'e1caf3301414601e430f38dea8beb349', 5, 'Financial assistance', 'Grace Ramos', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-09-22 10:44:55', '2026-09-22 10:48:00', '2026-09-22 10:48:00', '2026-09-22 11:02:39', 1),
(6651, 1, '2026-09-22', 32, 'MO-A032', '2fc140902c99c2bb39a829f811b1d289', 1, 'Mayor\'s Clearance', 'Gerardo Santos', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-09-22 10:46:43', '2026-09-22 10:50:49', '2026-09-22 10:50:49', '2026-09-22 10:52:25', 1),
(6652, 1, '2026-09-22', 33, 'MO-A033', 'd89b957e1b5489c5541e611fe92938f4', 1, 'Mayor\'s Clearance', 'Liza Castillo', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-09-22 10:50:43', '2026-09-22 11:07:24', '2026-09-22 11:07:24', '2026-09-22 11:16:44', 1),
(6653, 1, '2026-09-22', 34, 'MO-C034', 'f7b983b649aa420c7b48b0a15c0424d0', 3, 'Personal concerns', 'Antonio Cortez', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-09-22 11:08:38', '2026-09-22 11:17:16', '2026-09-22 11:17:16', '2026-09-22 11:24:49', 1),
(6654, 1, '2026-09-22', 35, 'MO-A035', '7f065dec281a316c705126b0e143552a', 1, 'Mayor\'s Clearance', 'Elena Domingo', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-09-22 11:16:21', '2026-09-22 11:23:39', '2026-09-22 11:23:39', '2026-09-22 11:30:01', 1),
(6655, 1, '2026-09-22', 36, 'MO-A036', '99aa7ad432e4c7c59f93efe69e7a43e7', 1, 'Mayor\'s Clearance', 'Pedro Mercado', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-09-22 11:18:39', '2026-09-22 11:25:48', '2026-09-22 11:25:48', '2026-09-22 11:29:49', 1),
(6656, 1, '2026-09-22', 37, 'MO-E037', 'e2f20b8e869e62b922bd2b11490503f6', 5, 'Financial assistance', 'Marites Navarro', NULL, NULL, NULL, 'staff', 'skipped', 55, 'Sir Henjie', 0, '2026-09-22 11:35:32', '2026-09-22 11:38:17', NULL, NULL, 1),
(6657, 1, '2026-09-22', 38, 'MO-E038', '7b88ec989fd8e47862628b190fd20534', 5, 'Financial assistance', 'Alfredo Salazar', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-09-22 12:15:48', '2026-09-22 12:19:40', '2026-09-22 12:19:40', '2026-09-22 12:21:53', 1),
(6658, 1, '2026-09-22', 39, 'MO-A039', 'd5354dfbfbb146c3dca9905a40615a0f', 1, 'Mayor\'s Clearance', 'Maria Aquino', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-09-22 12:17:44', '2026-09-22 12:19:12', '2026-09-22 12:19:12', '2026-09-22 12:23:16', 1),
(6659, 1, '2026-09-22', 40, 'MO-B040', '4342c3f904af762f7348a56c3e09690b', 2, 'Business concerns', 'Eduardo Fernandez', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-09-22 12:22:58', '2026-09-22 12:25:31', '2026-09-22 12:25:31', '2026-09-22 12:46:58', 1),
(6660, 1, '2026-09-22', 41, 'MO-C041', '3863952253d39d37a9b85f40db62b867', 3, 'Personal concerns', 'Rosario Cruz', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-09-22 12:32:36', '2026-09-22 12:37:19', '2026-09-22 12:37:19', '2026-09-22 12:46:21', 1),
(6661, 1, '2026-09-22', 42, 'MO-F042', 'c296c045b0511d83910c64a91306c1c2', 6, 'Other', 'Ricardo Gonzales', NULL, NULL, NULL, 'self', 'skipped', 56, 'Angelu', 0, '2026-09-22 12:35:21', '2026-09-22 12:37:42', NULL, NULL, 1),
(6662, 1, '2026-09-22', 43, 'MO-A043', 'b5f601d7bf544caab7283141a849871f', 1, 'Mayor\'s Clearance', 'Carmela Mendoza', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-09-22 12:53:24', '2026-09-22 12:56:40', '2026-09-22 12:56:40', '2026-09-22 13:02:48', 1),
(6663, 1, '2026-09-22', 44, 'MO-C044', '7df5c36b089f799f46566dbf24f30e4c', 3, 'Personal concerns', 'Felipe Rivera', NULL, NULL, NULL, 'self', 'cancelled', NULL, NULL, 0, '2026-09-22 13:03:44', NULL, NULL, NULL, 1),
(6664, 1, '2026-09-22', 45, 'MO-B045', '5a1a8c0fbebfd70ff5f6bdd322257bbe', 2, 'Business concerns', 'Cynthia Villanueva', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-09-22 13:08:21', '2026-09-22 13:18:42', '2026-09-22 13:18:42', '2026-09-22 13:26:55', 1),
(6665, 1, '2026-09-22', 46, 'MO-E046', '92017ebb517650b70e92d6f0003dc563', 5, 'Financial assistance', 'Jose Lopez', NULL, NULL, NULL, 'self', 'skipped', 55, 'Sir Henjie', 0, '2026-09-22 13:11:38', '2026-09-22 13:17:15', NULL, NULL, 1),
(6666, 1, '2026-09-22', 47, 'MO-C047', '1dc03c34c624414287ec2e3b9a1b93fc', 3, 'Personal concerns', 'Teresita Garcia', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-09-22 13:15:14', '2026-09-22 13:22:04', '2026-09-22 13:22:04', '2026-09-22 13:32:32', 1),
(6667, 1, '2026-09-22', 48, 'MO-A048', 'c2495398f9ae6629dea5eefe8c6359c7', 1, 'Mayor\'s Clearance', 'Danilo Flores', NULL, NULL, NULL, 'self', 'skipped', 55, 'Sir Henjie', 0, '2026-09-22 13:23:22', '2026-09-22 13:28:40', NULL, NULL, 1),
(6668, 1, '2026-09-22', 49, 'MO-E049', '09b92d8b06bc00f69f02fa3c8e52b010', 5, 'Financial assistance', 'Lourdes Bautista', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-09-22 13:27:43', '2026-09-22 13:44:16', '2026-09-22 13:44:16', '2026-09-22 13:49:23', 1),
(6669, 1, '2026-09-22', 50, 'MO-F050', '81660b2d2ec62776eca370bc99cc8484', 6, 'Other', 'Ramon Torres', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-09-22 13:36:59', '2026-09-22 13:47:59', '2026-09-22 13:47:59', '2026-09-22 13:49:29', 1),
(6670, 1, '2026-09-22', 51, 'MO-A051', '3fecf07a57f9f97b04fbe4fac7227949', 1, 'Mayor\'s Clearance', 'Josefina Dela Cruz', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-09-22 13:55:26', '2026-09-22 14:03:23', '2026-09-22 14:03:23', '2026-09-22 14:12:30', 1),
(6671, 1, '2026-09-22', 52, 'MO-C052', '6cf657666df8346d615a1e9b8ca9e617', 3, 'Personal concerns', 'Manuel Pascual', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-09-22 14:14:10', '2026-09-22 14:21:11', '2026-09-22 14:21:11', '2026-09-22 14:44:13', 1),
(6672, 1, '2026-09-22', 53, 'MO-F053', '1200a99b9e3d959b90b83de663bee05b', 6, 'Other', 'Ana Reyes', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-09-22 14:16:04', '2026-09-22 14:28:50', '2026-09-22 14:28:50', '2026-09-22 14:32:27', 1),
(6673, 1, '2026-09-22', 54, 'MO-C054', '4a529268b140a11f9c6aa30a0137a29f', 3, 'Personal concerns', 'Rogelio Ramos', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-09-22 14:25:07', '2026-09-22 14:34:20', '2026-09-22 14:34:20', '2026-09-22 15:04:00', 1),
(6674, 1, '2026-09-22', 55, 'MO-A055', 'fa7b14a2f7461bb2f6a048991710f620', 1, 'Mayor\'s Clearance', 'Grace Santos', NULL, NULL, NULL, 'staff', 'done', 56, 'Angelu', 0, '2026-09-22 14:29:25', '2026-09-22 14:31:20', '2026-09-22 14:31:20', '2026-09-22 14:44:05', 1),
(6675, 1, '2026-09-22', 56, 'MO-A056', 'bc18d670be279aad1f3927f0eb7a14ce', 1, 'Mayor\'s Clearance', 'Gerardo Castillo', NULL, NULL, NULL, 'staff', 'done', 56, 'Angelu', 0, '2026-09-22 14:36:00', '2026-09-22 14:48:13', '2026-09-22 14:48:13', '2026-09-22 14:55:33', 1),
(6676, 1, '2026-09-22', 57, 'MO-A057', '625d2111f90ede472dc4e39e84e66c20', 1, 'Mayor\'s Clearance', 'Liza Cortez', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-09-22 14:37:31', '2026-09-22 14:58:51', '2026-09-22 14:58:51', '2026-09-22 15:06:03', 1),
(6677, 1, '2026-09-22', 58, 'MO-E058', 'c6f8b9628ab0bb3d55b38916d48dbe11', 5, 'Financial assistance', 'Antonio Domingo', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-09-22 14:40:51', '2026-09-22 14:49:50', '2026-09-22 14:49:50', '2026-09-22 14:58:22', 1),
(6678, 1, '2026-09-22', 59, 'MO-E059', 'a3ee0cee831537f45657991f091aa686', 5, 'Financial assistance', 'Elena Mercado', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-09-22 14:42:27', '2026-09-22 14:44:14', '2026-09-22 14:44:14', '2026-09-22 14:51:45', 1),
(6679, 1, '2026-09-22', 60, 'MO-A060', '3e024294628714c8a5457a4c719e603a', 1, 'Mayor\'s Clearance', 'Pedro Navarro', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-09-22 14:46:57', '2026-09-22 14:56:29', '2026-09-22 14:56:29', '2026-09-22 14:59:12', 1),
(6680, 1, '2026-09-22', 61, 'MO-D061', '4f2c65b7e452fe08490168164e9ee012', 4, 'Courtesy call', 'Marites Salazar', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-09-22 14:52:38', '2026-09-22 15:02:12', '2026-09-22 15:02:12', '2026-09-22 15:12:12', 1),
(6681, 1, '2026-09-22', 62, 'MO-F062', '6639327440da8349204781f055954028', 6, 'Other', 'Alfredo Aquino', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-09-22 15:14:14', '2026-09-22 15:15:48', '2026-09-22 15:15:48', '2026-09-22 15:24:42', 1),
(6682, 1, '2026-09-22', 63, 'MO-B063', '11e97ad35f56098fe980e80f99df9ace', 2, 'Business concerns', 'Maria Fernandez', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-09-22 15:19:36', '2026-09-22 15:22:04', '2026-09-22 15:22:04', '2026-09-22 15:30:14', 1),
(6683, 1, '2026-09-22', 64, 'MO-E064', 'baf34f836bf5034cc3c635ce0ca080a0', 5, 'Financial assistance', 'Eduardo Cruz', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-09-22 15:30:26', '2026-09-22 15:37:10', '2026-09-22 15:37:10', '2026-09-22 15:49:48', 1),
(6684, 1, '2026-09-22', 65, 'MO-B065', '701ff2ab923569aa917ae8e6042cd27c', 2, 'Business concerns', 'Rosario Gonzales', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-09-22 15:43:43', '2026-09-22 15:44:28', '2026-09-22 15:44:28', '2026-09-22 15:48:57', 1),
(6685, 1, '2026-09-22', 66, 'MO-D066', 'ca23a1871a4a8e441644e523fd6669f0', 4, 'Courtesy call', 'Ricardo Mendoza', NULL, NULL, NULL, 'self', 'cancelled', NULL, NULL, 0, '2026-09-22 15:48:03', NULL, NULL, NULL, 1),
(6686, 1, '2026-09-22', 67, 'MO-B067', '8b1741469d33fd8bb3988a7daa17c11e', 2, 'Business concerns', 'Carmela Rivera', NULL, NULL, NULL, 'staff', 'done', 56, 'Angelu', 0, '2026-09-22 15:48:43', '2026-09-22 15:51:42', '2026-09-22 15:51:42', '2026-09-22 15:59:40', 1),
(6687, 1, '2026-09-22', 68, 'MO-B068', 'bc971fd6d748eaa5d934a5e31249de82', 2, 'Business concerns', 'Felipe Villanueva', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-09-22 15:50:22', '2026-09-22 15:57:05', '2026-09-22 15:57:05', '2026-09-22 16:15:33', 1),
(6688, 1, '2026-09-22', 69, 'MO-B069', '6efa9ac74c983ad40f4a265328d88597', 2, 'Business concerns', 'Cynthia Lopez', NULL, NULL, NULL, 'self', 'skipped', 56, 'Angelu', 0, '2026-09-22 15:56:12', '2026-09-22 15:58:54', NULL, NULL, 1),
(6689, 1, '2026-09-22', 70, 'MO-E070', 'd197d5c6b7a356e4437efcd061b90da2', 5, 'Financial assistance', 'Jose Garcia', NULL, NULL, NULL, 'staff', 'cancelled', NULL, NULL, 0, '2026-09-22 16:02:10', NULL, NULL, NULL, 1),
(6690, 1, '2026-09-22', 71, 'MO-E071', '0c1b92a53c4e7f9e79071c0a14ef3b58', 5, 'Financial assistance', 'Teresita Flores', NULL, NULL, NULL, 'staff', 'done', 54, 'Jean Frias', 0, '2026-09-22 16:32:23', '2026-09-22 16:33:05', '2026-09-22 16:33:05', '2026-09-22 16:41:50', 1),
(6691, 1, '2026-09-22', 72, 'MO-C072', '08a1b36752b1cba295faf8b9c7a96f35', 3, 'Personal concerns', 'Danilo Bautista', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-09-22 16:48:34', '2026-09-22 16:50:33', '2026-09-22 16:50:33', '2026-09-22 16:55:08', 1),
(6692, 1, '2026-09-22', 73, 'MO-B073', 'a379100b83c1eb95fcf819a3ee98d090', 2, 'Business concerns', 'Lourdes Torres', NULL, NULL, NULL, 'staff', 'done', 54, 'Jean Frias', 0, '2026-09-22 16:58:14', '2026-09-22 16:59:35', '2026-09-22 16:59:35', '2026-09-22 17:01:35', 1),
(6693, 1, '2026-09-23', 1, 'MO-F001', '377566b8c7006d6c55cb2aa90146f3e7', 6, 'Other', 'Gerardo Garcia', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-09-23 08:00:14', '2026-09-23 08:03:17', '2026-09-23 08:03:17', '2026-09-23 08:14:01', 1),
(6694, 1, '2026-09-23', 2, 'MO-B002', 'fceaeebd23e16c883f3c1a1e2e44e403', 2, 'Business concerns', 'Liza Flores', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-09-23 08:00:56', '2026-09-23 08:04:45', '2026-09-23 08:04:45', '2026-09-23 08:17:49', 1),
(6695, 1, '2026-09-23', 3, 'MO-C003', '5e4a0fe7df4bd792268a0ceb8fc201f1', 3, 'Personal concerns', 'Antonio Bautista', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-09-23 08:01:45', '2026-09-23 08:08:42', '2026-09-23 08:08:42', '2026-09-23 08:11:35', 1),
(6696, 1, '2026-09-23', 4, 'MO-A004', 'd6b02f4a512af10d0fa390b5b6e270c8', 1, 'Mayor\'s Clearance', 'Elena Torres', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-09-23 08:02:37', '2026-09-23 08:04:51', '2026-09-23 08:04:51', '2026-09-23 08:14:08', 1),
(6697, 1, '2026-09-23', 5, 'MO-F005', '291224d0da5937dfe3b5266ca92b19ee', 6, 'Other', 'Pedro Dela Cruz', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-09-23 08:17:14', '2026-09-23 08:25:10', '2026-09-23 08:25:10', '2026-09-23 08:33:46', 1),
(6698, 1, '2026-09-23', 6, 'MO-A006', 'edae3c1cc19c7175ab581f578f1c5e6b', 1, 'Mayor\'s Clearance', 'Marites Pascual', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-09-23 08:18:28', '2026-09-23 08:20:46', '2026-09-23 08:20:46', '2026-09-23 08:23:44', 1),
(6699, 1, '2026-09-23', 7, 'MO-B007', 'd7583d7505fd75360c29ac67dd6db81f', 2, 'Business concerns', 'Alfredo Reyes', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-09-23 08:20:43', '2026-09-23 08:21:34', '2026-09-23 08:21:34', '2026-09-23 08:34:40', 1),
(6700, 1, '2026-09-23', 8, 'MO-A008', '23a98a2538bab0b056f97c9b475a9ec9', 1, 'Mayor\'s Clearance', 'Maria Ramos', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-09-23 08:24:39', '2026-09-23 08:28:41', '2026-09-23 08:28:41', '2026-09-23 08:36:28', 1),
(6701, 1, '2026-09-23', 9, 'MO-A009', '9d507ae38f9c1174645abd57eceb4e75', 1, 'Mayor\'s Clearance', 'Eduardo Santos', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-09-23 08:29:15', '2026-09-23 08:34:23', '2026-09-23 08:34:23', '2026-09-23 08:41:25', 1),
(6702, 1, '2026-09-23', 10, 'MO-D010', 'a453a9daead3971bc1832692fbcca61a', 4, 'Courtesy call', 'Rosario Castillo', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-09-23 08:52:51', '2026-09-23 08:56:14', '2026-09-23 08:56:14', '2026-09-23 09:15:07', 1),
(6703, 1, '2026-09-23', 11, 'MO-D011', 'd87a7ef1ecefdeb019577830dbd95bd2', 4, 'Courtesy call', 'Ricardo Cortez', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-09-23 08:57:28', '2026-09-23 08:59:49', '2026-09-23 08:59:49', '2026-09-23 09:21:58', 1),
(6704, 1, '2026-09-23', 12, 'MO-A012', 'b6fb73b22134b6352cdf0d1da544c9d4', 1, 'Mayor\'s Clearance', 'Carmela Domingo', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-09-23 09:04:49', '2026-09-23 09:14:12', '2026-09-23 09:14:12', '2026-09-23 09:16:18', 1),
(6705, 1, '2026-09-23', 13, 'MO-B013', '98ee6fac62e04752dd34ab09f6fb5bac', 2, 'Business concerns', 'Felipe Mercado', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-09-23 09:08:40', '2026-09-23 09:17:42', '2026-09-23 09:17:42', '2026-09-23 09:41:44', 1),
(6706, 1, '2026-09-23', 14, 'MO-E014', 'ee181e6d8f30fe79402170447f5c537d', 5, 'Financial assistance', 'Cynthia Navarro', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-09-23 09:10:44', '2026-09-23 09:13:48', '2026-09-23 09:13:48', '2026-09-23 09:23:51', 1),
(6707, 1, '2026-09-23', 15, 'MO-A015', '59316e912696382f6e5ba1963123da5a', 1, 'Mayor\'s Clearance', 'Jose Salazar', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-09-23 09:12:22', '2026-09-23 09:21:47', '2026-09-23 09:21:47', '2026-09-23 09:25:57', 1),
(6708, 1, '2026-09-23', 16, 'MO-C016', 'acc0e3142aef3b3a89adb9fa8f786f93', 3, 'Personal concerns', 'Teresita Aquino', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-09-23 09:14:25', '2026-09-23 09:22:28', '2026-09-23 09:22:28', '2026-09-23 09:26:33', 1),
(6709, 1, '2026-09-23', 17, 'MO-B017', '98d0776e459002c3c0f0df2263f7ea8e', 2, 'Business concerns', 'Danilo Fernandez', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-09-23 09:31:00', '2026-09-23 09:47:31', '2026-09-23 09:47:31', '2026-09-23 10:16:40', 1),
(6710, 1, '2026-09-23', 18, 'MO-A018', '4b42f9f143641d4a82f2fd13e27a181d', 1, 'Mayor\'s Clearance', 'Lourdes Cruz', NULL, NULL, NULL, 'staff', 'done', 56, 'Angelu', 0, '2026-09-23 09:32:21', '2026-09-23 09:42:24', '2026-09-23 09:42:24', '2026-09-23 09:46:49', 1),
(6711, 1, '2026-09-23', 19, 'MO-E019', '69ebb11b3d4ac4facacedf0931b23745', 5, 'Financial assistance', 'Ramon Gonzales', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-09-23 09:33:13', '2026-09-23 09:38:13', '2026-09-23 09:38:13', '2026-09-23 09:56:08', 1),
(6712, 1, '2026-09-23', 20, 'MO-E020', '6e182726f087a2da7a5f6bb5544f9daa', 5, 'Financial assistance', 'Josefina Mendoza', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-09-23 09:39:23', '2026-09-23 09:46:36', '2026-09-23 09:46:36', '2026-09-23 09:57:21', 1),
(6713, 1, '2026-09-23', 21, 'MO-A021', 'ee0c7391d4a09d3827129b8af978686d', 1, 'Mayor\'s Clearance', 'Manuel Rivera', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-09-23 09:44:23', '2026-09-23 09:51:34', '2026-09-23 09:51:34', '2026-09-23 09:55:03', 1),
(6714, 1, '2026-09-23', 22, 'MO-E022', '510c872f3601a6af87afefb19c5c3fec', 5, 'Financial assistance', 'Ana Villanueva', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-09-23 09:47:30', '2026-09-23 10:00:28', '2026-09-23 10:00:28', '2026-09-23 10:02:41', 1),
(6715, 1, '2026-09-23', 23, 'MO-C023', 'b8f22523107f563e5d7a39643adb3f58', 3, 'Personal concerns', 'Rogelio Lopez', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-09-23 09:49:32', '2026-09-23 09:53:41', '2026-09-23 09:53:41', '2026-09-23 09:59:54', 1),
(6716, 1, '2026-09-23', 24, 'MO-F024', '4cd45e16e8db34b5a898211593881fa7', 6, 'Other', 'Grace Garcia', NULL, NULL, NULL, 'staff', 'done', 54, 'Jean Frias', 0, '2026-09-23 09:56:18', '2026-09-23 10:04:39', '2026-09-23 10:04:39', '2026-09-23 10:10:49', 1),
(6717, 1, '2026-09-23', 25, 'MO-A025', 'ed8348dc8d06489198cd6dfcff2d5670', 1, 'Mayor\'s Clearance', 'Gerardo Flores', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-09-23 09:59:56', '2026-09-23 10:05:43', '2026-09-23 10:05:43', '2026-09-23 10:11:49', 1),
(6718, 1, '2026-09-23', 26, 'MO-A026', 'b9ee169f237945574e43b2d3bdd8de2e', 1, 'Mayor\'s Clearance', 'Liza Bautista', NULL, NULL, NULL, 'self', 'skipped', 54, 'Jean Frias', 0, '2026-09-23 10:05:55', '2026-09-23 10:17:25', NULL, NULL, 1),
(6719, 1, '2026-09-23', 27, 'MO-A027', '75f43efdf1a1a673746389b651ce6802', 1, 'Mayor\'s Clearance', 'Antonio Torres', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-09-23 10:14:38', '2026-09-23 10:19:08', '2026-09-23 10:19:08', '2026-09-23 10:25:35', 1),
(6720, 1, '2026-09-23', 28, 'MO-B028', 'f9bd038bd27d2b9541288573a3cf6527', 2, 'Business concerns', 'Elena Dela Cruz', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-09-23 10:15:59', '2026-09-23 10:31:50', '2026-09-23 10:31:50', '2026-09-23 10:43:49', 1),
(6721, 1, '2026-09-23', 29, 'MO-C029', '501fdec51398fefe951bb7311916f0bf', 3, 'Personal concerns', 'Pedro Pascual', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-09-23 10:25:05', '2026-09-23 10:30:40', '2026-09-23 10:30:40', '2026-09-23 10:34:05', 1),
(6722, 1, '2026-09-23', 30, 'MO-F030', '29821aeed0d35ed48ca91ab57f4b2bb1', 6, 'Other', 'Marites Reyes', NULL, NULL, NULL, 'staff', 'done', 55, 'Sir Henjie', 0, '2026-09-23 10:31:39', '2026-09-23 11:03:03', '2026-09-23 11:03:03', '2026-09-23 11:10:20', 1),
(6723, 1, '2026-09-23', 31, 'MO-C031', 'f4f37f273b10101d6d591b92e213941e', 3, 'Personal concerns', 'Alfredo Ramos', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-09-23 10:40:55', '2026-09-23 10:52:46', '2026-09-23 10:52:46', '2026-09-23 10:56:42', 1),
(6724, 1, '2026-09-23', 32, 'MO-A032', '093f098ffed6b6be1e771c4a72499b91', 1, 'Mayor\'s Clearance', 'Maria Santos', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-09-23 10:45:07', '2026-09-23 10:53:20', '2026-09-23 10:53:20', '2026-09-23 11:01:21', 1),
(6725, 1, '2026-09-23', 33, 'MO-A033', 'ad3c7090340e2735ce67350fd2117551', 1, 'Mayor\'s Clearance', 'Eduardo Castillo', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-09-23 10:52:39', '2026-09-23 11:02:33', '2026-09-23 11:02:33', '2026-09-23 11:09:52', 1),
(6726, 1, '2026-09-23', 34, 'MO-C034', '9b1bdaa497d9967cb8fa01b851a18e51', 3, 'Personal concerns', 'Rosario Cortez', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-09-23 10:56:34', '2026-09-23 11:01:13', '2026-09-23 11:01:13', '2026-09-23 11:08:09', 1),
(6727, 1, '2026-09-23', 35, 'MO-D035', '4bb1ad6851d69cd6e2ff14960ead528a', 4, 'Courtesy call', 'Ricardo Domingo', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-09-23 10:57:57', '2026-09-23 11:01:38', '2026-09-23 11:01:38', '2026-09-23 11:18:17', 1),
(6728, 1, '2026-09-23', 36, 'MO-E036', '55dd1077a3508d7007d39b7f84b1b3fc', 5, 'Financial assistance', 'Carmela Mercado', NULL, NULL, NULL, 'self', 'cancelled', NULL, NULL, 0, '2026-09-23 11:01:13', NULL, NULL, NULL, 1),
(6729, 1, '2026-09-23', 37, 'MO-A037', '073e842266493ad6ca6d1bb5ad7a762b', 1, 'Mayor\'s Clearance', 'Felipe Navarro', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-09-23 11:11:39', '2026-09-23 11:22:28', '2026-09-23 11:22:28', '2026-09-23 11:28:46', 1),
(6730, 1, '2026-09-23', 38, 'MO-E038', '6ac1488efd9333a2960c358da99f209c', 5, 'Financial assistance', 'Cynthia Salazar', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-09-23 11:22:50', '2026-09-23 11:24:12', '2026-09-23 11:24:12', '2026-09-23 11:32:33', 1),
(6731, 1, '2026-09-23', 39, 'MO-C039', '18a2c1a6abc0567b51dd0bfbb56efc51', 3, 'Personal concerns', 'Jose Aquino', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-09-23 11:26:01', '2026-09-23 11:29:17', '2026-09-23 11:29:17', '2026-09-23 11:44:27', 1),
(6732, 1, '2026-09-23', 40, 'MO-A040', '9ba7577d33d6228502bbc783cb0b83e8', 1, 'Mayor\'s Clearance', 'Teresita Fernandez', NULL, NULL, NULL, 'staff', 'done', 55, 'Sir Henjie', 0, '2026-09-23 11:26:57', '2026-09-23 11:34:19', '2026-09-23 11:34:19', '2026-09-23 11:36:40', 1);
INSERT INTO `queue_tickets` (`id`, `queue_id`, `queue_day`, `number`, `code`, `access_key`, `service_id`, `service_name`, `full_name`, `mobile`, `phone_key`, `user_id`, `source`, `status`, `staff_id`, `staff_name`, `is_priority`, `created_at`, `called_at`, `started_at`, `finished_at`, `is_demo`) VALUES
(6733, 1, '2026-09-23', 41, 'MO-B041', 'cb65f8c74510df6c2359c958d78a1cb6', 2, 'Business concerns', 'Danilo Cruz', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-09-23 11:30:42', '2026-09-23 11:37:35', '2026-09-23 11:37:35', '2026-09-23 11:41:57', 1),
(6734, 1, '2026-09-23', 42, 'MO-E042', 'b3e575169dadf194a51312e04ee72340', 5, 'Financial assistance', 'Lourdes Gonzales', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-09-23 11:35:19', '2026-09-23 11:39:33', '2026-09-23 11:39:33', '2026-09-23 11:41:03', 1),
(6735, 1, '2026-09-23', 43, 'MO-A043', '0b5edbd1add5321de0f5f2c6bbb5567e', 1, 'Mayor\'s Clearance', 'Ramon Mendoza', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-09-23 11:43:40', '2026-09-23 11:45:29', '2026-09-23 11:45:29', '2026-09-23 11:56:51', 1),
(6736, 1, '2026-09-23', 44, 'MO-A044', 'd928fad8c16e419bd7993530cbd79517', 1, 'Mayor\'s Clearance', 'Josefina Rivera', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-09-23 11:49:21', '2026-09-23 11:53:43', '2026-09-23 11:53:43', '2026-09-23 11:56:05', 1),
(6737, 1, '2026-09-23', 45, 'MO-A045', '537bd14f83417b78b3b333fac91e7c1e', 1, 'Mayor\'s Clearance', 'Manuel Villanueva', NULL, NULL, NULL, 'staff', 'done', 56, 'Angelu', 0, '2026-09-23 11:51:50', '2026-09-23 11:55:50', '2026-09-23 11:55:50', '2026-09-23 12:02:45', 1),
(6738, 1, '2026-09-23', 46, 'MO-A046', '5956c75e355d883e359fd52dd77fe4be', 1, 'Mayor\'s Clearance', 'Ana Lopez', NULL, NULL, NULL, 'self', 'cancelled', NULL, NULL, 0, '2026-09-23 11:52:45', NULL, NULL, NULL, 1),
(6739, 1, '2026-09-23', 47, 'MO-A047', '66e4223fe9561a59db88c37524e2b0f1', 1, 'Mayor\'s Clearance', 'Rogelio Garcia', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-09-23 11:53:37', '2026-09-23 11:56:17', '2026-09-23 11:56:17', '2026-09-23 12:01:37', 1),
(6740, 1, '2026-09-23', 48, 'MO-A048', '9479c6dab047a394ea3f64e324bf3d1c', 1, 'Mayor\'s Clearance', 'Grace Flores', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-09-23 11:55:26', '2026-09-23 12:01:48', '2026-09-23 12:01:48', '2026-09-23 12:07:06', 1),
(6741, 1, '2026-09-23', 49, 'MO-A049', '9992ef7a63133d665ef0914bc722cdcf', 1, 'Mayor\'s Clearance', 'Gerardo Bautista', NULL, NULL, NULL, 'self', 'skipped', 56, 'Angelu', 0, '2026-09-23 12:18:13', '2026-09-23 12:22:39', NULL, NULL, 1),
(6742, 1, '2026-09-23', 50, 'MO-F050', '55a7f302c141c5d35414ae3640716d96', 6, 'Other', 'Liza Torres', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-09-23 12:29:56', '2026-09-23 12:32:11', '2026-09-23 12:32:11', '2026-09-23 12:37:05', 1),
(6743, 1, '2026-09-23', 51, 'MO-E051', 'c32bd0801f559cca69d1c3b39aa73948', 5, 'Financial assistance', 'Antonio Dela Cruz', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-09-23 12:47:26', '2026-09-23 12:49:42', '2026-09-23 12:49:42', '2026-09-23 12:51:32', 1),
(6744, 1, '2026-09-23', 52, 'MO-E052', '0f9cb1817557f9062dcfb51204c3e280', 5, 'Financial assistance', 'Elena Pascual', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-09-23 12:49:24', '2026-09-23 12:51:24', '2026-09-23 12:51:24', '2026-09-23 13:02:43', 1),
(6745, 1, '2026-09-23', 53, 'MO-E053', '5b8d5eb28d8bab94ac36a2d6bb62e804', 5, 'Financial assistance', 'Pedro Reyes', NULL, NULL, NULL, 'self', 'skipped', 56, 'Angelu', 0, '2026-09-23 12:50:34', '2026-09-23 12:53:25', NULL, NULL, 1),
(6746, 1, '2026-09-23', 54, 'MO-E054', '6f157892d738ca78321b510755363c20', 5, 'Financial assistance', 'Marites Ramos', NULL, NULL, NULL, 'self', 'cancelled', NULL, NULL, 0, '2026-09-23 13:04:27', NULL, NULL, NULL, 1),
(6747, 1, '2026-09-23', 55, 'MO-A055', 'a9f4fc0ed6264df76d681530050b4398', 1, 'Mayor\'s Clearance', 'Alfredo Santos', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-09-23 13:18:47', '2026-09-23 13:27:28', '2026-09-23 13:27:28', '2026-09-23 13:33:32', 1),
(6748, 1, '2026-09-23', 56, 'MO-A056', '8e15359811511ddd6dff8200194676db', 1, 'Mayor\'s Clearance', 'Maria Castillo', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-09-23 13:24:19', '2026-09-23 13:32:15', '2026-09-23 13:32:15', '2026-09-23 13:36:12', 1),
(6749, 1, '2026-09-23', 57, 'MO-E057', '4afe43740421d72dbd7a12e7dfb5cfaa', 5, 'Financial assistance', 'Eduardo Cortez', NULL, NULL, NULL, 'staff', 'done', 55, 'Sir Henjie', 0, '2026-09-23 13:26:04', '2026-09-23 13:33:46', '2026-09-23 13:33:46', '2026-09-23 13:49:35', 1),
(6750, 1, '2026-09-23', 58, 'MO-C058', '73b5ba24e32e046cad9b07718d846314', 3, 'Personal concerns', 'Rosario Domingo', NULL, NULL, NULL, 'staff', 'done', 56, 'Angelu', 0, '2026-09-23 13:30:29', '2026-09-23 13:33:57', '2026-09-23 13:33:57', '2026-09-23 13:39:06', 1),
(6751, 1, '2026-09-23', 59, 'MO-F059', '861651d568ddca9362cf0617c3392ad1', 6, 'Other', 'Ricardo Mercado', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-09-23 13:31:29', '2026-09-23 13:36:52', '2026-09-23 13:36:52', '2026-09-23 13:39:32', 1),
(6752, 1, '2026-09-23', 60, 'MO-A060', '662e0a1506934fd0dff34f00b0bbe44a', 1, 'Mayor\'s Clearance', 'Carmela Navarro', NULL, NULL, NULL, 'staff', 'done', 56, 'Angelu', 0, '2026-09-23 13:39:03', '2026-09-23 13:52:11', '2026-09-23 13:52:11', '2026-09-23 13:55:55', 1),
(6753, 1, '2026-09-23', 61, 'MO-B061', 'da059cbfb20184e051c0527704aee801', 2, 'Business concerns', 'Felipe Salazar', NULL, NULL, NULL, 'self', 'skipped', 55, 'Sir Henjie', 0, '2026-09-23 13:43:02', '2026-09-23 13:48:37', NULL, NULL, 1),
(6754, 1, '2026-09-23', 62, 'MO-C062', '0bd8ca5800d806008070abd4613b0e20', 3, 'Personal concerns', 'Cynthia Aquino', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-09-23 13:43:52', '2026-09-23 13:44:39', '2026-09-23 13:44:39', '2026-09-23 13:49:41', 1),
(6755, 1, '2026-09-23', 63, 'MO-A063', '0a766cd0e09a1b6ca50c911bfbe476e9', 1, 'Mayor\'s Clearance', 'Jose Fernandez', NULL, NULL, NULL, 'staff', 'cancelled', NULL, NULL, 0, '2026-09-23 13:48:18', NULL, NULL, NULL, 1),
(6756, 1, '2026-09-23', 64, 'MO-F064', 'fef00921b6e0f52ada20cbab3c329fee', 6, 'Other', 'Teresita Cruz', NULL, NULL, NULL, 'staff', 'done', 54, 'Jean Frias', 0, '2026-09-23 13:54:18', '2026-09-23 14:03:28', '2026-09-23 14:03:28', '2026-09-23 14:13:06', 1),
(6757, 1, '2026-09-23', 65, 'MO-E065', '1c25690df148bc5fbf712c859b6d409a', 5, 'Financial assistance', 'Danilo Gonzales', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-09-23 13:54:35', '2026-09-23 14:05:40', '2026-09-23 14:05:40', '2026-09-23 14:18:55', 1),
(6758, 1, '2026-09-23', 66, 'MO-D066', 'b446e2d2e131d3e1769630179dae5578', 4, 'Courtesy call', 'Lourdes Mendoza', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-09-23 14:00:16', '2026-09-23 14:08:35', '2026-09-23 14:08:35', '2026-09-23 14:16:04', 1),
(6759, 1, '2026-09-23', 67, 'MO-A067', '80bd87d6198781364e800bb0cbaee95a', 1, 'Mayor\'s Clearance', 'Ramon Rivera', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-09-23 14:00:33', '2026-09-23 14:18:00', '2026-09-23 14:18:00', '2026-09-23 14:27:57', 1),
(6760, 1, '2026-09-23', 68, 'MO-F068', '83532df235028559782aee4e616f90e8', 6, 'Other', 'Josefina Villanueva', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-09-23 14:39:26', '2026-09-23 14:40:45', '2026-09-23 14:40:45', '2026-09-23 14:45:15', 1),
(6761, 1, '2026-09-23', 69, 'MO-E069', '05d7d38a907295ff8f2bf21f6e1f69e1', 5, 'Financial assistance', 'Manuel Lopez', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-09-23 14:43:22', '2026-09-23 14:52:16', '2026-09-23 14:52:16', '2026-09-23 15:01:59', 1),
(6762, 1, '2026-09-23', 70, 'MO-E070', '9373c6868a513ae6d1c6a1082a1519bb', 5, 'Financial assistance', 'Ana Garcia', NULL, NULL, NULL, 'self', 'skipped', 54, 'Jean Frias', 0, '2026-09-23 14:44:49', '2026-09-23 14:52:59', NULL, NULL, 1),
(6763, 1, '2026-09-23', 71, 'MO-C071', '8d62a7e92e7a0ebcedd770de5c197cd1', 3, 'Personal concerns', 'Rogelio Flores', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-09-23 14:45:56', '2026-09-23 14:55:00', '2026-09-23 14:55:00', '2026-09-23 15:06:30', 1),
(6764, 1, '2026-09-23', 72, 'MO-A072', 'c71d2a16cd7009a682d2a35843d0c0f0', 1, 'Mayor\'s Clearance', 'Grace Bautista', NULL, NULL, NULL, 'self', 'cancelled', NULL, NULL, 0, '2026-09-23 14:47:20', NULL, NULL, NULL, 1),
(6765, 1, '2026-09-23', 73, 'MO-C073', 'ee425af5c6bdfbdaa931e6bf32d5f775', 3, 'Personal concerns', 'Gerardo Torres', NULL, NULL, NULL, 'staff', 'done', 55, 'Sir Henjie', 0, '2026-09-23 14:54:22', '2026-09-23 15:02:48', '2026-09-23 15:02:48', '2026-09-23 15:21:12', 1),
(6766, 1, '2026-09-23', 74, 'MO-C074', '6dc878feada55f853ab39a1f633eeecd', 3, 'Personal concerns', 'Liza Dela Cruz', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-09-23 14:57:28', '2026-09-23 14:58:40', '2026-09-23 14:58:40', '2026-09-23 15:07:01', 1),
(6767, 1, '2026-09-23', 75, 'MO-E075', '7b63856288baf00556777f3b58aae7a8', 5, 'Financial assistance', 'Antonio Pascual', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-09-23 15:06:16', '2026-09-23 15:07:16', '2026-09-23 15:07:16', '2026-09-23 15:23:22', 1),
(6768, 1, '2026-09-23', 76, 'MO-E076', '800afec8256d579a2b491a4bbf68cfaa', 5, 'Financial assistance', 'Elena Reyes', NULL, NULL, NULL, 'self', 'skipped', 56, 'Angelu', 0, '2026-09-23 15:20:39', '2026-09-23 15:21:58', NULL, NULL, 1),
(6769, 1, '2026-09-23', 77, 'MO-E077', '1af4cff7286fc4a843005c9e2f07289f', 5, 'Financial assistance', 'Pedro Ramos', NULL, NULL, NULL, 'self', 'cancelled', NULL, NULL, 0, '2026-09-23 15:25:36', NULL, NULL, NULL, 1),
(6770, 1, '2026-09-23', 78, 'MO-E078', 'ae2224a75966fced2a0d22cf1b8bafec', 5, 'Financial assistance', 'Marites Santos', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-09-23 15:28:12', '2026-09-23 15:32:20', '2026-09-23 15:32:20', '2026-09-23 15:36:10', 1),
(6771, 1, '2026-09-23', 79, 'MO-E079', 'db297ed2c246d674488d7decf8d9284b', 5, 'Financial assistance', 'Alfredo Castillo', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-09-23 15:31:41', '2026-09-23 15:35:04', '2026-09-23 15:35:04', '2026-09-23 15:47:50', 1),
(6772, 1, '2026-09-23', 80, 'MO-E080', 'adb25db81ac07f68c21ab3e8fa56741b', 5, 'Financial assistance', 'Maria Cortez', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-09-23 15:52:01', '2026-09-23 15:55:11', '2026-09-23 15:55:11', '2026-09-23 15:59:36', 1),
(6773, 1, '2026-09-23', 81, 'MO-B081', 'aa6ab8aa9003fbcdc17d0a2f3535dd9b', 2, 'Business concerns', 'Eduardo Domingo', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-09-23 16:08:48', '2026-09-23 16:12:04', '2026-09-23 16:12:04', '2026-09-23 16:25:29', 1),
(6774, 1, '2026-09-23', 82, 'MO-F082', '73ed005f6c7693a657bad9756fc3b9e8', 6, 'Other', 'Rosario Mercado', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-09-23 16:42:09', '2026-09-23 16:42:39', '2026-09-23 16:42:39', '2026-09-23 16:49:09', 1),
(6775, 1, '2026-09-24', 1, 'MO-C001', '9db684dbfff07e6b8165f8204cdef248', 3, 'Personal concerns', 'Maria Garcia', NULL, NULL, NULL, 'staff', 'done', 56, 'Angelu', 0, '2026-09-24 08:13:18', '2026-09-24 08:21:08', '2026-09-24 08:21:08', '2026-09-24 08:27:14', 1),
(6776, 1, '2026-09-24', 2, 'MO-C002', '6f3a737ccbcb4be0d00959c2b7ea7b9e', 3, 'Personal concerns', 'Eduardo Flores', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-09-24 08:13:40', '2026-09-24 08:21:41', '2026-09-24 08:21:41', '2026-09-24 08:29:27', 1),
(6777, 1, '2026-09-24', 3, 'MO-D003', 'c2cfc0d4a551d287393fdea898b43373', 4, 'Courtesy call', 'Rosario Bautista', NULL, NULL, NULL, 'staff', 'skipped', 56, 'Angelu', 0, '2026-09-24 08:19:55', '2026-09-24 08:23:22', NULL, NULL, 1),
(6778, 1, '2026-09-24', 4, 'MO-E004', 'ac9fe3ac9270059607b924ed2823d655', 5, 'Financial assistance', 'Ricardo Torres', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-09-24 08:28:11', '2026-09-24 08:30:08', '2026-09-24 08:30:08', '2026-09-24 08:36:25', 1),
(6779, 1, '2026-09-24', 5, 'MO-B005', 'a3c83f52cdf33bd2cab9fca16c95d0e6', 2, 'Business concerns', 'Carmela Dela Cruz', NULL, NULL, NULL, 'self', 'cancelled', NULL, NULL, 0, '2026-09-24 08:55:02', NULL, NULL, NULL, 1),
(6780, 1, '2026-09-24', 6, 'MO-A006', '546eafb87719fa6c861f80a9d2d591a8', 1, 'Mayor\'s Clearance', 'Felipe Pascual', NULL, NULL, NULL, 'staff', 'done', 56, 'Angelu', 0, '2026-09-24 08:57:35', '2026-09-24 08:59:45', '2026-09-24 08:59:45', '2026-09-24 09:03:31', 1),
(6781, 1, '2026-09-24', 7, 'MO-A007', '5add7250cf50bb54d4ca9efdf8053e5d', 1, 'Mayor\'s Clearance', 'Cynthia Reyes', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-09-24 08:59:23', '2026-09-24 09:03:35', '2026-09-24 09:03:35', '2026-09-24 09:16:12', 1),
(6782, 1, '2026-09-24', 8, 'MO-A008', 'a2070f83649035cc7cb375d31dfcad53', 1, 'Mayor\'s Clearance', 'Jose Ramos', NULL, NULL, NULL, 'self', 'skipped', 54, 'Jean Frias', 0, '2026-09-24 09:09:49', '2026-09-24 09:19:43', NULL, NULL, 1),
(6783, 1, '2026-09-24', 9, 'MO-B009', 'c594d6c53460a885c415d1ebbe312ceb', 2, 'Business concerns', 'Teresita Santos', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-09-24 09:10:45', '2026-09-24 09:16:41', '2026-09-24 09:16:41', '2026-09-24 09:21:32', 1),
(6784, 1, '2026-09-24', 10, 'MO-A010', 'f6848b407d459779bf8ff400a9bd59e4', 1, 'Mayor\'s Clearance', 'Danilo Castillo', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-09-24 09:14:08', '2026-09-24 09:26:19', '2026-09-24 09:26:19', '2026-09-24 09:34:26', 1),
(6785, 1, '2026-09-24', 11, 'MO-D011', '27dac3826b4948bbe08db4df0cc8fc73', 4, 'Courtesy call', 'Lourdes Cortez', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-09-24 09:17:09', '2026-09-24 09:23:54', '2026-09-24 09:23:54', '2026-09-24 09:40:01', 1),
(6786, 1, '2026-09-24', 12, 'MO-C012', 'f0a6132c54be8d2c6d7309d89bbd2595', 3, 'Personal concerns', 'Ramon Domingo', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-09-24 09:23:42', '2026-09-24 09:28:25', '2026-09-24 09:28:25', '2026-09-24 09:36:58', 1),
(6787, 1, '2026-09-24', 13, 'MO-F013', '528802a9687bd38552341d74e2b5e501', 6, 'Other', 'Josefina Mercado', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-09-24 09:30:29', '2026-09-24 09:35:44', '2026-09-24 09:35:44', '2026-09-24 09:47:11', 1),
(6788, 1, '2026-09-24', 14, 'MO-F014', 'f555c304d42244513781bc8e157bf9f7', 6, 'Other', 'Manuel Navarro', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-09-24 09:43:45', '2026-09-24 09:50:19', '2026-09-24 09:50:19', '2026-09-24 10:02:00', 1),
(6789, 1, '2026-09-24', 15, 'MO-D015', 'f70f6578c0b0eee80e87424fa6ac2fcd', 4, 'Courtesy call', 'Ana Salazar', NULL, NULL, NULL, 'self', 'skipped', 54, 'Jean Frias', 0, '2026-09-24 09:47:01', '2026-09-24 09:58:08', NULL, NULL, 1),
(6790, 1, '2026-09-24', 16, 'MO-A016', 'aa243852a26c035f6376e634bf06b556', 1, 'Mayor\'s Clearance', 'Rogelio Aquino', NULL, NULL, NULL, 'staff', 'done', 54, 'Jean Frias', 0, '2026-09-24 09:52:23', '2026-09-24 09:57:06', '2026-09-24 09:57:06', '2026-09-24 10:08:16', 1),
(6791, 1, '2026-09-24', 17, 'MO-E017', '06776e9be4e43d816b3324e1be64b32d', 5, 'Financial assistance', 'Grace Fernandez', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-09-24 09:57:18', '2026-09-24 10:00:26', '2026-09-24 10:00:26', '2026-09-24 10:03:17', 1),
(6792, 1, '2026-09-24', 18, 'MO-F018', '46f686240a33d1a4b6c99bc7c0f6bb7f', 6, 'Other', 'Gerardo Cruz', NULL, NULL, NULL, 'staff', 'done', 55, 'Sir Henjie', 0, '2026-09-24 09:58:12', '2026-09-24 10:09:29', '2026-09-24 10:09:29', '2026-09-24 10:11:12', 1),
(6793, 1, '2026-09-24', 19, 'MO-B019', 'ccafe99b8c28ba445de95985550a62d5', 2, 'Business concerns', 'Liza Gonzales', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-09-24 09:59:54', '2026-09-24 10:06:15', '2026-09-24 10:06:15', '2026-09-24 10:18:31', 1),
(6794, 1, '2026-09-24', 20, 'MO-C020', '7faeb5eacd157f0340c7f053a17e4fca', 3, 'Personal concerns', 'Antonio Mendoza', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-09-24 09:59:58', '2026-09-24 10:06:26', '2026-09-24 10:06:26', '2026-09-24 10:09:33', 1),
(6795, 1, '2026-09-24', 21, 'MO-A021', '150d1b654d10ef9dde7f63e90dade2f9', 1, 'Mayor\'s Clearance', 'Elena Rivera', NULL, NULL, NULL, 'self', 'cancelled', NULL, NULL, 0, '2026-09-24 10:04:49', NULL, NULL, NULL, 1),
(6796, 1, '2026-09-24', 22, 'MO-A022', 'e544ad4219d88ea0c6950d6e02ac0785', 1, 'Mayor\'s Clearance', 'Pedro Villanueva', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-09-24 10:11:54', '2026-09-24 10:19:38', '2026-09-24 10:19:38', '2026-09-24 10:25:02', 1),
(6797, 1, '2026-09-24', 23, 'MO-A023', '8e7c8d3c02c377e6485719e5088734f0', 1, 'Mayor\'s Clearance', 'Marites Lopez', NULL, NULL, NULL, 'staff', 'skipped', 55, 'Sir Henjie', 0, '2026-09-24 10:15:36', '2026-09-24 10:25:29', NULL, NULL, 1),
(6798, 1, '2026-09-24', 24, 'MO-C024', '7c6e10f46f114b14cef58134a1611a9b', 3, 'Personal concerns', 'Alfredo Garcia', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-09-24 10:16:18', '2026-09-24 10:22:41', '2026-09-24 10:22:41', '2026-09-24 10:28:57', 1),
(6799, 1, '2026-09-24', 25, 'MO-E025', '96f7fd1fe9e7697179866817a2444214', 5, 'Financial assistance', 'Maria Flores', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-09-24 10:31:54', '2026-09-24 10:43:54', '2026-09-24 10:43:54', '2026-09-24 10:46:07', 1),
(6800, 1, '2026-09-24', 26, 'MO-C026', '047f35d8c126a83f9feb7f0d763dc58e', 3, 'Personal concerns', 'Eduardo Bautista', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-09-24 10:34:54', '2026-09-24 10:45:31', '2026-09-24 10:45:31', '2026-09-24 10:51:37', 1),
(6801, 1, '2026-09-24', 27, 'MO-D027', 'ab80f2e286fdc4c9c1aea06778b7dfd9', 4, 'Courtesy call', 'Rosario Torres', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-09-24 10:51:06', '2026-09-24 11:06:23', '2026-09-24 11:06:23', '2026-09-24 11:38:16', 1),
(6802, 1, '2026-09-24', 28, 'MO-B028', 'f99aea11c2255e949cb4f205f9a773cb', 2, 'Business concerns', 'Ricardo Dela Cruz', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-09-24 11:00:29', '2026-09-24 11:04:32', '2026-09-24 11:04:32', '2026-09-24 11:28:35', 1),
(6803, 1, '2026-09-24', 29, 'MO-B029', '85dff4591b8d76e56862039a8b3a4be6', 2, 'Business concerns', 'Carmela Pascual', NULL, NULL, NULL, 'staff', 'done', 54, 'Jean Frias', 0, '2026-09-24 11:20:21', '2026-09-24 11:23:59', '2026-09-24 11:23:59', '2026-09-24 11:30:19', 1),
(6804, 1, '2026-09-24', 30, 'MO-F030', '9028bd2c57cb138014d1f1db0706c48f', 6, 'Other', 'Felipe Reyes', NULL, NULL, NULL, 'staff', 'done', 54, 'Jean Frias', 0, '2026-09-24 11:21:26', '2026-09-24 11:41:49', '2026-09-24 11:41:49', '2026-09-24 11:43:56', 1),
(6805, 1, '2026-09-24', 31, 'MO-F031', '07889e07435e5942383006ead30ac507', 6, 'Other', 'Cynthia Ramos', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-09-24 11:32:32', '2026-09-24 11:53:30', '2026-09-24 11:53:30', '2026-09-24 12:03:16', 1),
(6806, 1, '2026-09-24', 32, 'MO-A032', '88277f19ca580bbb2deb1d0ce8a4c0b8', 1, 'Mayor\'s Clearance', 'Jose Santos', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-09-24 11:43:58', '2026-09-24 11:59:16', '2026-09-24 11:59:16', '2026-09-24 12:02:49', 1),
(6807, 1, '2026-09-24', 33, 'MO-B033', '78155c4e4c9a22679621196255e2f43f', 2, 'Business concerns', 'Teresita Castillo', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-09-24 11:49:08', '2026-09-24 12:22:46', '2026-09-24 12:22:46', '2026-09-24 12:36:51', 1),
(6808, 1, '2026-09-24', 34, 'MO-C034', 'add604b9611f0acabb66380f368d6d43', 3, 'Personal concerns', 'Danilo Cortez', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-09-24 12:26:13', '2026-09-24 12:29:16', '2026-09-24 12:29:16', '2026-09-24 12:36:58', 1),
(6809, 1, '2026-09-24', 35, 'MO-C035', '3df99fdbb130f2ab310e27e2ad3cb392', 3, 'Personal concerns', 'Lourdes Domingo', NULL, NULL, NULL, 'staff', 'cancelled', NULL, NULL, 0, '2026-09-24 12:30:04', NULL, NULL, NULL, 1),
(6810, 1, '2026-09-24', 36, 'MO-A036', '04a41c1ec7d79b40dd3474aa57f9b265', 1, 'Mayor\'s Clearance', 'Ramon Mercado', NULL, NULL, NULL, 'staff', 'done', 56, 'Angelu', 0, '2026-09-24 12:57:01', '2026-09-24 13:04:38', '2026-09-24 13:04:38', '2026-09-24 13:10:47', 1),
(6811, 1, '2026-09-24', 37, 'MO-A037', '6c89bd401ee19c7226a720357eae0181', 1, 'Mayor\'s Clearance', 'Josefina Navarro', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-09-24 13:07:45', '2026-09-24 13:08:18', '2026-09-24 13:08:18', '2026-09-24 13:11:05', 1),
(6812, 1, '2026-09-24', 38, 'MO-B038', 'c45a91fc3fef693f0418540b7b8b5ddb', 2, 'Business concerns', 'Manuel Salazar', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-09-24 13:18:27', '2026-09-24 13:28:58', '2026-09-24 13:28:58', '2026-09-24 13:41:23', 1),
(6813, 1, '2026-09-24', 39, 'MO-A039', 'aa9d3e3f65b01060563c3995813f5677', 1, 'Mayor\'s Clearance', 'Ana Aquino', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-09-24 13:19:51', '2026-09-24 13:26:17', '2026-09-24 13:26:17', '2026-09-24 13:39:02', 1),
(6814, 1, '2026-09-24', 40, 'MO-C040', 'a116bbef1c8cbaccbc81fc3783932783', 3, 'Personal concerns', 'Rogelio Fernandez', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-09-24 13:36:56', '2026-09-24 13:45:01', '2026-09-24 13:45:01', '2026-09-24 13:56:21', 1),
(6815, 1, '2026-09-24', 41, 'MO-C041', 'c5d572a78cdd970d482552ee7e6d30a3', 3, 'Personal concerns', 'Grace Cruz', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-09-24 13:46:09', '2026-09-24 13:51:55', '2026-09-24 13:51:55', '2026-09-24 14:02:41', 1),
(6816, 1, '2026-09-24', 42, 'MO-A042', '8e16a4aeaca98c34d97c2149880a8b3b', 1, 'Mayor\'s Clearance', 'Gerardo Gonzales', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-09-24 13:48:09', '2026-09-24 13:56:33', '2026-09-24 13:56:33', '2026-09-24 13:58:37', 1),
(6817, 1, '2026-09-24', 43, 'MO-E043', '29f8149f67285ee6bec25d0981e98466', 5, 'Financial assistance', 'Liza Mendoza', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-09-24 13:50:16', '2026-09-24 13:56:11', '2026-09-24 13:56:11', '2026-09-24 14:06:36', 1),
(6818, 1, '2026-09-24', 44, 'MO-E044', '2c47b35313722238c099578688fde31c', 5, 'Financial assistance', 'Antonio Rivera', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-09-24 14:09:40', '2026-09-24 14:13:27', '2026-09-24 14:13:27', '2026-09-24 14:19:50', 1),
(6819, 1, '2026-09-24', 45, 'MO-A045', 'c8ec422454d25768a2c11a0307efe608', 1, 'Mayor\'s Clearance', 'Elena Villanueva', NULL, NULL, NULL, 'staff', 'done', 54, 'Jean Frias', 0, '2026-09-24 14:15:43', '2026-09-24 14:29:17', '2026-09-24 14:29:17', '2026-09-24 14:43:22', 1),
(6820, 1, '2026-09-24', 46, 'MO-E046', '8405c83858c47cbf41fc283998a7e1a8', 5, 'Financial assistance', 'Pedro Lopez', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-09-24 14:20:58', '2026-09-24 14:30:38', '2026-09-24 14:30:38', '2026-09-24 14:33:19', 1),
(6821, 1, '2026-09-24', 47, 'MO-F047', '61538f5c1bf4fda18a7d1c391130841a', 6, 'Other', 'Marites Garcia', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-09-24 14:34:02', '2026-09-24 14:41:19', '2026-09-24 14:41:19', '2026-09-24 14:46:59', 1),
(6822, 1, '2026-09-24', 48, 'MO-B048', '0d74a184f4202e280857042936527570', 2, 'Business concerns', 'Alfredo Flores', NULL, NULL, NULL, 'self', 'skipped', 54, 'Jean Frias', 0, '2026-09-24 14:45:01', '2026-09-24 14:45:55', NULL, NULL, 1),
(6823, 1, '2026-09-24', 49, 'MO-F049', 'aaf7645e66e32c4eee4bb5d44570a0e1', 6, 'Other', 'Maria Bautista', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-09-24 14:52:38', '2026-09-24 14:53:36', '2026-09-24 14:53:36', '2026-09-24 15:06:14', 1),
(6824, 1, '2026-09-24', 50, 'MO-C050', '9fb25d6beab3942029d5a92ecf9b40df', 3, 'Personal concerns', 'Eduardo Torres', NULL, NULL, NULL, 'staff', 'skipped', 55, 'Sir Henjie', 0, '2026-09-24 14:57:03', '2026-09-24 14:57:58', NULL, NULL, 1),
(6825, 1, '2026-09-24', 51, 'MO-A051', '70d56d53859959ed26887277420ec6d1', 1, 'Mayor\'s Clearance', 'Rosario Dela Cruz', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-09-24 14:58:26', '2026-09-24 15:02:47', '2026-09-24 15:02:47', '2026-09-24 15:07:21', 1),
(6826, 1, '2026-09-24', 52, 'MO-B052', '31357df327798873aa3204b07e660a4c', 2, 'Business concerns', 'Ricardo Pascual', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-09-24 15:06:21', '2026-09-24 15:07:54', '2026-09-24 15:07:54', '2026-09-24 15:23:01', 1),
(6827, 1, '2026-09-24', 53, 'MO-C053', 'cd18293e9a09fe19c2d02d6b6788d616', 3, 'Personal concerns', 'Carmela Reyes', NULL, NULL, NULL, 'staff', 'cancelled', NULL, NULL, 0, '2026-09-24 15:13:16', NULL, NULL, NULL, 1),
(6828, 1, '2026-09-24', 54, 'MO-E054', '2c602b59a217abbaa2c0675b4594ac24', 5, 'Financial assistance', 'Felipe Ramos', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-09-24 15:16:27', '2026-09-24 15:17:07', '2026-09-24 15:17:07', '2026-09-24 15:22:29', 1),
(6829, 1, '2026-09-24', 55, 'MO-D055', '3c2e3c0f6fe88042a8d0a7598e1a9e03', 4, 'Courtesy call', 'Cynthia Santos', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-09-24 15:17:09', '2026-09-24 15:19:08', '2026-09-24 15:19:08', '2026-09-24 15:24:54', 1),
(6830, 1, '2026-09-24', 56, 'MO-E056', '64b32c9b9b62222029d9adb9e87a1324', 5, 'Financial assistance', 'Jose Castillo', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-09-24 15:29:06', '2026-09-24 15:30:11', '2026-09-24 15:30:11', '2026-09-24 15:36:32', 1),
(6831, 1, '2026-09-24', 57, 'MO-F057', 'c88599872ec116b9021af3abf790755a', 6, 'Other', 'Teresita Cortez', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-09-24 15:36:41', '2026-09-24 15:42:50', '2026-09-24 15:42:50', '2026-09-24 15:46:51', 1),
(6832, 1, '2026-09-24', 58, 'MO-E058', '73259a4ab22df3e221477d3e77ad61cb', 5, 'Financial assistance', 'Danilo Domingo', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-09-24 16:10:55', '2026-09-24 16:12:43', '2026-09-24 16:12:43', '2026-09-24 16:21:31', 1),
(6833, 1, '2026-09-24', 59, 'MO-E059', '83dbc2ef97d8f201ec7dddc3fa306804', 5, 'Financial assistance', 'Lourdes Mercado', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-09-24 16:20:06', '2026-09-24 16:21:23', '2026-09-24 16:21:23', '2026-09-24 16:32:34', 1),
(6834, 1, '2026-09-24', 60, 'MO-F060', '08e93ba5912f8f401581fd3692c2cd9c', 6, 'Other', 'Ramon Navarro', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-09-24 16:27:07', '2026-09-24 16:28:50', '2026-09-24 16:28:50', '2026-09-24 16:36:15', 1),
(6835, 1, '2026-09-24', 61, 'MO-E061', '60bda56825c416fd8436874ac705d101', 5, 'Financial assistance', 'Josefina Salazar', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-09-24 16:53:02', '2026-09-24 16:53:32', '2026-09-24 16:53:32', '2026-09-24 17:02:44', 1),
(6836, 1, '2026-09-25', 1, 'MO-F001', '98ed623649b50ed3590a7fde043329fb', 6, 'Other', 'Jose Garcia', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-09-25 08:01:23', '2026-09-25 08:05:32', '2026-09-25 08:05:32', '2026-09-25 08:09:23', 1),
(6837, 1, '2026-09-25', 2, 'MO-C002', '7b40bcf6a65a01c88384652a5778a857', 3, 'Personal concerns', 'Teresita Flores', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-09-25 08:07:19', '2026-09-25 08:17:48', '2026-09-25 08:17:48', '2026-09-25 08:27:39', 1),
(6838, 1, '2026-09-25', 3, 'MO-B003', '3c588996cd2298f6f5df7b823775697d', 2, 'Business concerns', 'Danilo Bautista', NULL, NULL, NULL, 'self', 'skipped', 56, 'Angelu', 0, '2026-09-25 08:20:41', '2026-09-25 08:24:28', NULL, NULL, 1),
(6839, 1, '2026-09-25', 4, 'MO-A004', '9817e3b78af351d65eeb7a8563626272', 1, 'Mayor\'s Clearance', 'Lourdes Torres', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-09-25 08:27:42', '2026-09-25 08:29:11', '2026-09-25 08:29:11', '2026-09-25 08:34:06', 1),
(6840, 1, '2026-09-25', 5, 'MO-A005', '47e1c92534bb7dfd7001609a93e636df', 1, 'Mayor\'s Clearance', 'Ramon Dela Cruz', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-09-25 08:32:22', '2026-09-25 08:34:40', '2026-09-25 08:34:40', '2026-09-25 08:46:23', 1),
(6841, 1, '2026-09-25', 6, 'MO-F006', 'a031363e848eef5c053c6ac27f6c5c2a', 6, 'Other', 'Josefina Pascual', NULL, NULL, NULL, 'self', 'skipped', 54, 'Jean Frias', 0, '2026-09-25 09:01:58', '2026-09-25 09:21:20', NULL, NULL, 1),
(6842, 1, '2026-09-25', 7, 'MO-B007', '199aac83eb5d95a466e22155a39204d5', 2, 'Business concerns', 'Manuel Reyes', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-09-25 09:28:17', '2026-09-25 09:34:36', '2026-09-25 09:34:36', '2026-09-25 09:43:43', 1),
(6843, 1, '2026-09-25', 8, 'MO-A008', '973f13706f4388abe00a0cb4ef46543f', 1, 'Mayor\'s Clearance', 'Ana Ramos', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-09-25 09:30:56', '2026-09-25 09:39:18', '2026-09-25 09:39:18', '2026-09-25 09:48:00', 1),
(6844, 1, '2026-09-25', 9, 'MO-E009', '11b0a60609050815b54f252d6e616efa', 5, 'Financial assistance', 'Rogelio Santos', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-09-25 09:34:18', '2026-09-25 09:44:34', '2026-09-25 09:44:34', '2026-09-25 09:50:09', 1),
(6845, 1, '2026-09-25', 10, 'MO-F010', 'e0903f7e3485129f7b45c2ed245d3a92', 6, 'Other', 'Grace Castillo', NULL, NULL, NULL, 'staff', 'done', 55, 'Sir Henjie', 0, '2026-09-25 09:40:39', '2026-09-25 09:45:22', '2026-09-25 09:45:22', '2026-09-25 09:58:41', 1),
(6846, 1, '2026-09-25', 11, 'MO-C011', 'd26a6c2b074d7f9a1b38db4d39a12d16', 3, 'Personal concerns', 'Gerardo Cortez', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-09-25 09:49:37', '2026-09-25 09:52:05', '2026-09-25 09:52:05', '2026-09-25 10:07:55', 1),
(6847, 1, '2026-09-25', 12, 'MO-D012', '751aae08706235537d350aafcc07597e', 4, 'Courtesy call', 'Liza Domingo', NULL, NULL, NULL, 'self', 'skipped', 56, 'Angelu', 0, '2026-09-25 09:51:08', '2026-09-25 09:54:03', NULL, NULL, 1),
(6848, 1, '2026-09-25', 13, 'MO-E013', 'd7b8b9d46c7590142b2fd1da034dc865', 5, 'Financial assistance', 'Antonio Mercado', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-09-25 09:54:32', '2026-09-25 10:02:58', '2026-09-25 10:02:58', '2026-09-25 10:14:59', 1),
(6849, 1, '2026-09-25', 14, 'MO-C014', 'b72c63bbd92914b8ef81a9b44105b43f', 3, 'Personal concerns', 'Elena Navarro', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-09-25 10:00:22', '2026-09-25 10:02:25', '2026-09-25 10:02:25', '2026-09-25 10:13:05', 1),
(6850, 1, '2026-09-25', 15, 'MO-E015', '4309849ef972d5dae8fa0dcba6cf7733', 5, 'Financial assistance', 'Pedro Salazar', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-09-25 10:00:42', '2026-09-25 10:10:12', '2026-09-25 10:10:12', '2026-09-25 10:15:35', 1),
(6851, 1, '2026-09-25', 16, 'MO-B016', '46fb877653c36ee4b5295f2481629c5a', 2, 'Business concerns', 'Marites Aquino', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-09-25 10:16:59', '2026-09-25 10:35:20', '2026-09-25 10:35:20', '2026-09-25 10:43:46', 1),
(6852, 1, '2026-09-25', 17, 'MO-C017', '121eea35d11e27cd5a4997f83827a9bc', 3, 'Personal concerns', 'Alfredo Fernandez', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-09-25 10:24:30', '2026-09-25 10:28:30', '2026-09-25 10:28:30', '2026-09-25 10:40:12', 1),
(6853, 1, '2026-09-25', 18, 'MO-A018', '8d139a0c0a0de1414100b12419c2eceb', 1, 'Mayor\'s Clearance', 'Maria Cruz', NULL, NULL, NULL, 'staff', 'done', 54, 'Jean Frias', 0, '2026-09-25 10:24:57', '2026-09-25 10:45:55', '2026-09-25 10:45:55', '2026-09-25 10:50:48', 1),
(6854, 1, '2026-09-25', 19, 'MO-A019', '9a12090e8d67d902685b8d2bbc93df44', 1, 'Mayor\'s Clearance', 'Eduardo Gonzales', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-09-25 10:32:59', '2026-09-25 10:44:25', '2026-09-25 10:44:25', '2026-09-25 10:51:54', 1),
(6855, 1, '2026-09-25', 20, 'MO-B020', 'bcd509ccefc7eebd8f69cef021df258b', 2, 'Business concerns', 'Rosario Mendoza', NULL, NULL, NULL, 'self', 'cancelled', NULL, NULL, 0, '2026-09-25 10:33:50', NULL, NULL, NULL, 1),
(6856, 1, '2026-09-25', 21, 'MO-A021', '2fa31be929b7e1aa56a68bd9f9f3f3d6', 1, 'Mayor\'s Clearance', 'Ricardo Rivera', NULL, NULL, NULL, 'staff', 'done', 54, 'Jean Frias', 0, '2026-09-25 10:39:56', '2026-09-25 10:51:14', '2026-09-25 10:51:14', '2026-09-25 10:58:33', 1),
(6857, 1, '2026-09-25', 22, 'MO-A022', '359dabf0961fbb5e354538ea5212a530', 1, 'Mayor\'s Clearance', 'Carmela Villanueva', NULL, NULL, NULL, 'staff', 'done', 55, 'Sir Henjie', 0, '2026-09-25 10:49:50', '2026-09-25 10:51:22', '2026-09-25 10:51:22', '2026-09-25 10:53:40', 1),
(6858, 1, '2026-09-25', 23, 'MO-C023', 'ac6ba2b78afeba503a65bd2bf3423fd6', 3, 'Personal concerns', 'Felipe Lopez', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-09-25 10:50:06', '2026-09-25 10:54:42', '2026-09-25 10:54:42', '2026-09-25 11:02:42', 1),
(6859, 1, '2026-09-25', 24, 'MO-E024', 'ca088257a936dea04c4bd0d7f7133421', 5, 'Financial assistance', 'Cynthia Garcia', NULL, NULL, NULL, 'staff', 'done', 54, 'Jean Frias', 0, '2026-09-25 10:58:24', '2026-09-25 11:21:52', '2026-09-25 11:21:52', '2026-09-25 11:28:19', 1),
(6860, 1, '2026-09-25', 25, 'MO-F025', '02250cb92eb492e6b3e452df6cea1449', 6, 'Other', 'Jose Flores', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-09-25 10:59:48', '2026-09-25 11:11:54', '2026-09-25 11:11:54', '2026-09-25 11:27:33', 1),
(6861, 1, '2026-09-25', 26, 'MO-C026', 'e52ec52883bc8500b1c389d346569915', 3, 'Personal concerns', 'Teresita Bautista', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-09-25 11:07:27', '2026-09-25 11:09:26', '2026-09-25 11:09:26', '2026-09-25 11:16:34', 1),
(6862, 1, '2026-09-25', 27, 'MO-E027', 'be005c5455d0c8047ce6b22d1c144fbe', 5, 'Financial assistance', 'Danilo Torres', NULL, NULL, NULL, 'staff', 'skipped', 55, 'Sir Henjie', 0, '2026-09-25 11:08:36', '2026-09-25 11:12:01', NULL, NULL, 1),
(6863, 1, '2026-09-25', 28, 'MO-F028', '8cb46c9b492e757afe31d24da0ee67d9', 6, 'Other', 'Lourdes Dela Cruz', NULL, NULL, NULL, 'staff', 'done', 56, 'Angelu', 0, '2026-09-25 11:10:20', '2026-09-25 11:13:22', '2026-09-25 11:13:22', '2026-09-25 11:16:52', 1),
(6864, 1, '2026-09-25', 29, 'MO-E029', 'c60474bb140169cca5592279fed31c8f', 5, 'Financial assistance', 'Ramon Pascual', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-09-25 11:12:09', '2026-09-25 11:12:59', '2026-09-25 11:12:59', '2026-09-25 11:16:08', 1),
(6865, 1, '2026-09-25', 30, 'MO-F030', '7825ff416911ed6a30cc3247413bf98d', 6, 'Other', 'Josefina Reyes', NULL, NULL, NULL, 'staff', 'done', 55, 'Sir Henjie', 0, '2026-09-25 11:24:49', '2026-09-25 11:32:00', '2026-09-25 11:32:00', '2026-09-25 11:45:07', 1),
(6866, 1, '2026-09-25', 31, 'MO-A031', 'e0e85f4830c60a8c63f32dc7f0797815', 1, 'Mayor\'s Clearance', 'Manuel Ramos', NULL, NULL, NULL, 'self', 'skipped', 56, 'Angelu', 0, '2026-09-25 11:26:20', '2026-09-25 11:29:16', NULL, NULL, 1),
(6867, 1, '2026-09-25', 32, 'MO-E032', 'd0e8c1d72a38b786fe055004fc530152', 5, 'Financial assistance', 'Ana Santos', NULL, NULL, NULL, 'self', 'skipped', 55, 'Sir Henjie', 0, '2026-09-25 11:37:33', '2026-09-25 11:41:42', NULL, NULL, 1),
(6868, 1, '2026-09-25', 33, 'MO-A033', '73e7b4234337003ea74a7a3b047aee00', 1, 'Mayor\'s Clearance', 'Rogelio Castillo', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-09-25 11:45:41', '2026-09-25 11:48:05', '2026-09-25 11:48:05', '2026-09-25 11:49:54', 1),
(6869, 1, '2026-09-25', 34, 'MO-E034', '13811d0b9afe0eb3be822b0efb6942ed', 5, 'Financial assistance', 'Grace Cortez', NULL, NULL, NULL, 'staff', 'done', 55, 'Sir Henjie', 0, '2026-09-25 11:58:39', '2026-09-25 12:07:02', '2026-09-25 12:07:02', '2026-09-25 12:09:08', 1),
(6870, 1, '2026-09-25', 35, 'MO-A035', '6b3ff3aa79f813c4acc9da2249517ea3', 1, 'Mayor\'s Clearance', 'Gerardo Domingo', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-09-25 12:06:58', '2026-09-25 12:08:48', '2026-09-25 12:08:48', '2026-09-25 12:15:27', 1),
(6871, 1, '2026-09-25', 36, 'MO-A036', 'b1c5d1dbaf45f236a8cf919b73261819', 1, 'Mayor\'s Clearance', 'Liza Mercado', NULL, NULL, NULL, 'staff', 'done', 55, 'Sir Henjie', 0, '2026-09-25 12:14:56', '2026-09-25 12:17:21', '2026-09-25 12:17:21', '2026-09-25 12:25:45', 1),
(6872, 1, '2026-09-25', 37, 'MO-E037', 'd1cb2fe74336c36dcc0495d5ca8e2b7b', 5, 'Financial assistance', 'Antonio Navarro', NULL, NULL, NULL, 'staff', 'done', 54, 'Jean Frias', 0, '2026-09-25 12:19:56', '2026-09-25 12:29:10', '2026-09-25 12:29:10', '2026-09-25 12:35:08', 1),
(6873, 1, '2026-09-25', 38, 'MO-F038', 'fb6fa1b2dc28169fe2a3d2c04a057533', 6, 'Other', 'Elena Salazar', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-09-25 12:20:31', '2026-09-25 12:22:58', '2026-09-25 12:22:58', '2026-09-25 12:26:56', 1),
(6874, 1, '2026-09-25', 39, 'MO-B039', 'ac3fb4d95dbce8a936fd1becb50cb297', 2, 'Business concerns', 'Pedro Aquino', NULL, NULL, NULL, 'staff', 'done', 54, 'Jean Frias', 0, '2026-09-25 12:29:32', '2026-09-25 12:46:06', '2026-09-25 12:46:06', '2026-09-25 12:58:13', 1),
(6875, 1, '2026-09-25', 40, 'MO-D040', 'fc01257f53288151f7bf82b2228446be', 4, 'Courtesy call', 'Marites Fernandez', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-09-25 12:34:26', '2026-09-25 12:37:25', '2026-09-25 12:37:25', '2026-09-25 12:47:30', 1),
(6876, 1, '2026-09-25', 41, 'MO-E041', 'b4d55f1b0d69c4be49a910fbfa8ffde0', 5, 'Financial assistance', 'Alfredo Cruz', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-09-25 12:36:45', '2026-09-25 12:37:27', '2026-09-25 12:37:27', '2026-09-25 12:47:00', 1),
(6877, 1, '2026-09-25', 42, 'MO-F042', '4f652605401bf98a371051042c8c5141', 6, 'Other', 'Maria Gonzales', NULL, NULL, NULL, 'staff', 'done', 55, 'Sir Henjie', 0, '2026-09-25 12:51:00', '2026-09-25 12:57:09', '2026-09-25 12:57:09', '2026-09-25 13:00:45', 1),
(6878, 1, '2026-09-25', 43, 'MO-B043', '09f2849d7683efe413c70562ab42cdf6', 2, 'Business concerns', 'Eduardo Mendoza', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-09-25 13:01:32', '2026-09-25 13:04:49', '2026-09-25 13:04:49', '2026-09-25 13:18:15', 1),
(6879, 1, '2026-09-25', 44, 'MO-A044', 'fe007f6e02367e17abee86f354178755', 1, 'Mayor\'s Clearance', 'Rosario Rivera', NULL, NULL, NULL, 'staff', 'done', 55, 'Sir Henjie', 0, '2026-09-25 13:08:07', '2026-09-25 13:09:23', '2026-09-25 13:09:23', '2026-09-25 13:19:55', 1),
(6880, 1, '2026-09-25', 45, 'MO-E045', '7c5485fdf31f8cde86c7419cd36721a8', 5, 'Financial assistance', 'Ricardo Villanueva', NULL, NULL, NULL, 'staff', 'done', 54, 'Jean Frias', 0, '2026-09-25 13:09:27', '2026-09-25 13:11:33', '2026-09-25 13:11:33', '2026-09-25 13:15:32', 1),
(6881, 1, '2026-09-25', 46, 'MO-C046', '1bf10a8ef83c7383c8a7c9fa21e6ef49', 3, 'Personal concerns', 'Carmela Lopez', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-09-25 13:10:55', '2026-09-25 13:18:10', '2026-09-25 13:18:10', '2026-09-25 13:29:54', 1),
(6882, 1, '2026-09-25', 47, 'MO-F047', '0f29e371bcf2fff7db6cc958ccb80b01', 6, 'Other', 'Felipe Garcia', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-09-25 13:11:11', '2026-09-25 13:13:36', '2026-09-25 13:13:36', '2026-09-25 13:23:08', 1),
(6883, 1, '2026-09-25', 48, 'MO-A048', 'a737d209c181c720d42742ab357df56f', 1, 'Mayor\'s Clearance', 'Cynthia Flores', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-09-25 13:13:26', '2026-09-25 13:21:05', '2026-09-25 13:21:05', '2026-09-25 13:30:27', 1),
(6884, 1, '2026-09-25', 49, 'MO-F049', 'a698a971224fc2fcef35d209e02ec5ac', 6, 'Other', 'Jose Bautista', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-09-25 13:35:39', '2026-09-25 13:47:08', '2026-09-25 13:47:08', '2026-09-25 13:57:59', 1),
(6885, 1, '2026-09-25', 50, 'MO-F050', 'cc756cf0c391970029c1454d2e631c1f', 6, 'Other', 'Teresita Torres', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-09-25 13:39:39', '2026-09-25 13:51:59', '2026-09-25 13:51:59', '2026-09-25 14:00:43', 1),
(6886, 1, '2026-09-25', 51, 'MO-E051', 'f5fae593b05e8b3130ffd04b7b78725f', 5, 'Financial assistance', 'Danilo Dela Cruz', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-09-25 13:47:29', '2026-09-25 13:50:58', '2026-09-25 13:50:58', '2026-09-25 13:52:55', 1),
(6887, 1, '2026-09-25', 52, 'MO-B052', '3a8940b5e75ce4a56128d61bfac2c3a0', 2, 'Business concerns', 'Lourdes Pascual', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-09-25 13:49:46', '2026-09-25 13:52:18', '2026-09-25 13:52:18', '2026-09-25 13:55:29', 1),
(6888, 1, '2026-09-25', 53, 'MO-B053', '974f55c97c6de563038a8c0b4d199d9a', 2, 'Business concerns', 'Ramon Reyes', NULL, NULL, NULL, 'self', 'cancelled', NULL, NULL, 0, '2026-09-25 13:59:01', NULL, NULL, NULL, 1),
(6889, 1, '2026-09-25', 54, 'MO-E054', 'e59faa4406dfb666a6082314d0bec43a', 5, 'Financial assistance', 'Josefina Ramos', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-09-25 14:02:26', '2026-09-25 14:04:06', '2026-09-25 14:04:06', '2026-09-25 14:11:16', 1),
(6890, 1, '2026-09-25', 55, 'MO-E055', '857952c01f4025103638c7e71aeb1390', 5, 'Financial assistance', 'Manuel Santos', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-09-25 14:02:29', '2026-09-25 14:06:12', '2026-09-25 14:06:12', '2026-09-25 14:21:10', 1),
(6891, 1, '2026-09-25', 56, 'MO-A056', '7a762812aaef501f4144a8a7fc329fb1', 1, 'Mayor\'s Clearance', 'Ana Castillo', NULL, NULL, NULL, 'self', 'cancelled', NULL, NULL, 0, '2026-09-25 14:17:36', NULL, NULL, NULL, 1),
(6892, 1, '2026-09-25', 57, 'MO-B057', '3522d07f0a4e3f65a253819258a24b4a', 2, 'Business concerns', 'Rogelio Cortez', NULL, NULL, NULL, 'self', 'cancelled', NULL, NULL, 0, '2026-09-25 14:31:13', NULL, NULL, NULL, 1),
(6893, 1, '2026-09-25', 58, 'MO-E058', '3c7b65bfa312e855eea1d6dd3ff91370', 5, 'Financial assistance', 'Grace Domingo', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-09-25 14:57:59', '2026-09-25 15:07:56', '2026-09-25 15:07:56', '2026-09-25 15:18:14', 1),
(6894, 1, '2026-09-25', 59, 'MO-A059', '20e365504d3b743c4165a2961feb1565', 1, 'Mayor\'s Clearance', 'Gerardo Mercado', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-09-25 15:03:48', '2026-09-25 15:06:51', '2026-09-25 15:06:51', '2026-09-25 15:19:21', 1),
(6895, 1, '2026-09-25', 60, 'MO-F060', '3f036f86473e7db64089ed08cc1e34b6', 6, 'Other', 'Liza Navarro', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-09-25 15:30:32', '2026-09-25 15:31:03', '2026-09-25 15:31:03', '2026-09-25 15:38:12', 1),
(6896, 1, '2026-09-25', 61, 'MO-F061', '9b315f7ccfeccc82b7e027579183be01', 6, 'Other', 'Antonio Salazar', NULL, NULL, NULL, 'staff', 'done', 56, 'Angelu', 0, '2026-09-25 15:39:48', '2026-09-25 15:44:29', '2026-09-25 15:44:29', '2026-09-25 15:59:04', 1),
(6897, 1, '2026-09-25', 62, 'MO-B062', 'b13e798f706ff4606eddaa57eff902e8', 2, 'Business concerns', 'Elena Aquino', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-09-25 15:43:31', '2026-09-25 15:45:40', '2026-09-25 15:45:40', '2026-09-25 16:02:10', 1),
(6898, 1, '2026-09-25', 63, 'MO-A063', 'be97f140786abdd58e59165f6de050fe', 1, 'Mayor\'s Clearance', 'Pedro Fernandez', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-09-25 16:26:02', '2026-09-25 16:27:51', '2026-09-25 16:27:51', '2026-09-25 16:30:44', 1),
(6899, 1, '2026-09-25', 64, 'MO-F064', 'eca3892bbea44bd7f1a88d60f39d8125', 6, 'Other', 'Marites Cruz', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-09-25 16:35:36', '2026-09-25 16:36:37', '2026-09-25 16:36:37', '2026-09-25 16:39:24', 1),
(6900, 1, '2026-09-25', 65, 'MO-B065', 'ee380f75f6e31462c5177a66ce88df2f', 2, 'Business concerns', 'Alfredo Gonzales', NULL, NULL, NULL, 'staff', 'done', 56, 'Angelu', 0, '2026-09-25 16:47:44', '2026-09-25 16:49:17', '2026-09-25 16:49:17', '2026-09-25 17:19:35', 1),
(6901, 1, '2026-09-25', 66, 'MO-D066', 'd65febdf105e12f69ef1c6babaf44ff2', 4, 'Courtesy call', 'Maria Mendoza', NULL, NULL, NULL, 'staff', 'done', 54, 'Jean Frias', 0, '2026-09-25 16:57:17', '2026-09-25 16:58:01', '2026-09-25 16:58:01', '2026-09-25 17:01:56', 1),
(6902, 1, '2026-09-28', 1, 'MO-B001', '2589635b3d4868e135ca8b71276d2df1', 2, 'Business concerns', 'Carmela Garcia', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-09-28 08:07:54', '2026-09-28 08:11:08', '2026-09-28 08:11:08', '2026-09-28 08:14:53', 1),
(6903, 1, '2026-09-28', 2, 'MO-C002', '7896c01dbe8cd780cd358719cc2f3518', 3, 'Personal concerns', 'Felipe Flores', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-09-28 08:11:18', '2026-09-28 08:12:19', '2026-09-28 08:12:19', '2026-09-28 08:24:09', 1),
(6904, 1, '2026-09-28', 3, 'MO-E003', '3541deeb52df95e2180d5b3c7743118b', 5, 'Financial assistance', 'Cynthia Bautista', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-09-28 08:16:24', '2026-09-28 08:29:57', '2026-09-28 08:29:57', '2026-09-28 08:36:34', 1),
(6905, 1, '2026-09-28', 4, 'MO-A004', '1980bf3c018c07a627aae8655a2451db', 1, 'Mayor\'s Clearance', 'Jose Torres', NULL, NULL, NULL, 'staff', 'done', 56, 'Angelu', 0, '2026-09-28 08:21:55', '2026-09-28 08:24:17', '2026-09-28 08:24:17', '2026-09-28 08:29:21', 1),
(6906, 1, '2026-09-28', 5, 'MO-B005', '5249926f4e37cbaaf32ee73816dc56f0', 2, 'Business concerns', 'Teresita Dela Cruz', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-09-28 08:33:29', '2026-09-28 08:35:26', '2026-09-28 08:35:26', '2026-09-28 08:48:01', 1),
(6907, 1, '2026-09-28', 6, 'MO-B006', '865f4cda515810d2f315be271a21d446', 2, 'Business concerns', 'Danilo Pascual', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-09-28 08:34:08', '2026-09-28 08:35:35', '2026-09-28 08:35:35', '2026-09-28 08:51:04', 1),
(6908, 1, '2026-09-28', 7, 'MO-E007', '8d8b13a299553718b22738fc439af735', 5, 'Financial assistance', 'Lourdes Reyes', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-09-28 08:34:35', '2026-09-28 08:39:04', '2026-09-28 08:39:04', '2026-09-28 08:43:47', 1),
(6909, 1, '2026-09-28', 8, 'MO-D008', '1b922e6a94ce929d3de381cc648c7bb4', 4, 'Courtesy call', 'Ramon Ramos', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-09-28 08:55:08', '2026-09-28 09:06:21', '2026-09-28 09:06:21', '2026-09-28 09:51:21', 1),
(6910, 1, '2026-09-28', 9, 'MO-B009', '47f7fd3f80f444c3aaaf088ce195f579', 2, 'Business concerns', 'Josefina Santos', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-09-28 08:58:46', '2026-09-28 09:06:30', '2026-09-28 09:06:30', '2026-09-28 09:22:37', 1),
(6911, 1, '2026-09-28', 10, 'MO-A010', '86ef5e6d6ee765624983ac5c392ade52', 1, 'Mayor\'s Clearance', 'Manuel Castillo', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-09-28 09:02:42', '2026-09-28 09:06:25', '2026-09-28 09:06:25', '2026-09-28 09:12:06', 1),
(6912, 1, '2026-09-28', 11, 'MO-C011', 'e3a28e860c410da46747b7b0802c4c4e', 3, 'Personal concerns', 'Ana Cortez', NULL, NULL, NULL, 'staff', 'cancelled', NULL, NULL, 0, '2026-09-28 09:11:29', NULL, NULL, NULL, 1),
(6913, 1, '2026-09-28', 12, 'MO-A012', '8a310815f82858672ae413ec6934a61a', 1, 'Mayor\'s Clearance', 'Rogelio Domingo', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-09-28 09:21:45', '2026-09-28 09:24:27', '2026-09-28 09:24:27', '2026-09-28 09:29:31', 1),
(6914, 1, '2026-09-28', 13, 'MO-C013', '1f5aba87f5473c4e3671f9bcad9f4eea', 3, 'Personal concerns', 'Grace Mercado', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-09-28 09:33:12', '2026-09-28 09:35:10', '2026-09-28 09:35:10', '2026-09-28 09:42:07', 1),
(6915, 1, '2026-09-28', 14, 'MO-A014', '5b75d38ef7de255254747f861bea4f69', 1, 'Mayor\'s Clearance', 'Gerardo Navarro', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-09-28 09:35:45', '2026-09-28 09:50:59', '2026-09-28 09:50:59', '2026-09-28 10:01:15', 1),
(6916, 1, '2026-09-28', 15, 'MO-A015', 'e17ed262cb699b9117317d2ab9aa49b1', 1, 'Mayor\'s Clearance', 'Liza Salazar', NULL, NULL, NULL, 'staff', 'done', 56, 'Angelu', 0, '2026-09-28 09:40:14', '2026-09-28 09:49:48', '2026-09-28 09:49:48', '2026-09-28 09:53:00', 1),
(6917, 1, '2026-09-28', 16, 'MO-A016', 'f664d1a35ba70f4a926769a356c2dc1e', 1, 'Mayor\'s Clearance', 'Antonio Aquino', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-09-28 09:44:21', '2026-09-28 09:49:26', '2026-09-28 09:49:26', '2026-09-28 10:00:14', 1),
(6918, 1, '2026-09-28', 17, 'MO-A017', 'd033489e21cd4b0403e029400e9c4ff1', 1, 'Mayor\'s Clearance', 'Elena Fernandez', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-09-28 09:45:44', '2026-09-28 09:51:12', '2026-09-28 09:51:12', '2026-09-28 09:56:52', 1),
(6919, 1, '2026-09-28', 18, 'MO-E018', 'eb5e8af7fc33826af00585d86ac71f9c', 5, 'Financial assistance', 'Pedro Cruz', NULL, NULL, NULL, 'self', 'skipped', 54, 'Jean Frias', 0, '2026-09-28 09:46:44', '2026-09-28 10:01:48', NULL, NULL, 1),
(6920, 1, '2026-09-28', 19, 'MO-B019', '258ffa4af04a38a40c64e48c7ca8e927', 2, 'Business concerns', 'Marites Gonzales', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-09-28 09:50:03', '2026-09-28 09:52:37', '2026-09-28 09:52:37', '2026-09-28 10:05:27', 1),
(6921, 1, '2026-09-28', 20, 'MO-F020', 'ab28ac7e4d80ff925340c474c9ec83f9', 6, 'Other', 'Alfredo Mendoza', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-09-28 09:52:31', '2026-09-28 09:54:29', '2026-09-28 09:54:29', '2026-09-28 09:57:33', 1),
(6922, 1, '2026-09-28', 21, 'MO-F021', '28fa2392e191603bc347761196c65748', 6, 'Other', 'Maria Rivera', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-09-28 09:55:26', '2026-09-28 10:05:18', '2026-09-28 10:05:18', '2026-09-28 10:13:14', 1),
(6923, 1, '2026-09-28', 22, 'MO-D022', 'f08822d684ffe2c2d8986bb83398cf98', 4, 'Courtesy call', 'Eduardo Villanueva', NULL, NULL, NULL, 'staff', 'done', 56, 'Angelu', 0, '2026-09-28 09:57:22', '2026-09-28 10:02:40', '2026-09-28 10:02:40', '2026-09-28 10:24:26', 1),
(6924, 1, '2026-09-28', 23, 'MO-B023', 'f411c540041dd1dfe2249950aeae3ac3', 2, 'Business concerns', 'Rosario Lopez', NULL, NULL, NULL, 'self', 'skipped', 54, 'Jean Frias', 0, '2026-09-28 09:58:26', '2026-09-28 10:05:30', NULL, NULL, 1),
(6925, 1, '2026-09-28', 24, 'MO-A024', '74ee1e6219ecbe9b8cd07fc09833ebb7', 1, 'Mayor\'s Clearance', 'Ricardo Garcia', NULL, NULL, NULL, 'staff', 'done', 54, 'Jean Frias', 0, '2026-09-28 10:07:10', '2026-09-28 10:31:38', '2026-09-28 10:31:38', '2026-09-28 10:49:11', 1);
INSERT INTO `queue_tickets` (`id`, `queue_id`, `queue_day`, `number`, `code`, `access_key`, `service_id`, `service_name`, `full_name`, `mobile`, `phone_key`, `user_id`, `source`, `status`, `staff_id`, `staff_name`, `is_priority`, `created_at`, `called_at`, `started_at`, `finished_at`, `is_demo`) VALUES
(6926, 1, '2026-09-28', 25, 'MO-B025', 'c5b0b25cd7f0bb3dae1f0422c0b3991b', 2, 'Business concerns', 'Carmela Flores', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-09-28 10:08:25', '2026-09-28 10:18:28', '2026-09-28 10:18:28', '2026-09-28 10:31:38', 1),
(6927, 1, '2026-09-28', 26, 'MO-A026', 'ae27f8ba94b6da6c10245f725fd44060', 1, 'Mayor\'s Clearance', 'Felipe Bautista', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-09-28 10:11:05', '2026-09-28 10:13:16', '2026-09-28 10:13:16', '2026-09-28 10:28:54', 1),
(6928, 1, '2026-09-28', 27, 'MO-A027', 'aa2209014816e3cc3b071c19eee58ccb', 1, 'Mayor\'s Clearance', 'Cynthia Torres', NULL, NULL, NULL, 'self', 'skipped', 54, 'Jean Frias', 0, '2026-09-28 10:11:11', '2026-09-28 10:19:16', NULL, NULL, 1),
(6929, 1, '2026-09-28', 28, 'MO-E028', '55d5031d331a27d0726bc346b01a1119', 5, 'Financial assistance', 'Jose Dela Cruz', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-09-28 10:22:05', '2026-09-28 10:27:00', '2026-09-28 10:27:00', '2026-09-28 10:34:50', 1),
(6930, 1, '2026-09-28', 29, 'MO-B029', 'a8ab72e5d496da2d68fe162cefd6583c', 2, 'Business concerns', 'Teresita Pascual', NULL, NULL, NULL, 'staff', 'skipped', 54, 'Jean Frias', 0, '2026-09-28 10:22:14', '2026-09-28 10:32:35', NULL, NULL, 1),
(6931, 1, '2026-09-28', 30, 'MO-F030', '3b5e82c1caec4843af456d4dea1bc87b', 6, 'Other', 'Danilo Reyes', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-09-28 10:24:09', '2026-09-28 10:25:41', '2026-09-28 10:25:41', '2026-09-28 10:37:17', 1),
(6932, 1, '2026-09-28', 31, 'MO-B031', '015923d464b7d272ce4671678e4e7f8d', 2, 'Business concerns', 'Lourdes Ramos', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-09-28 10:27:04', '2026-09-28 10:34:45', '2026-09-28 10:34:45', '2026-09-28 10:48:39', 1),
(6933, 1, '2026-09-28', 32, 'MO-F032', '22cdae63c99978a7aaf85c57a15aef47', 6, 'Other', 'Ramon Santos', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-09-28 10:33:45', '2026-09-28 10:39:09', '2026-09-28 10:39:09', '2026-09-28 10:45:30', 1),
(6934, 1, '2026-09-28', 33, 'MO-A033', '6d23f0af22c9a3e79c6d71309234d0e2', 1, 'Mayor\'s Clearance', 'Josefina Castillo', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-09-28 10:37:13', '2026-09-28 10:55:24', '2026-09-28 10:55:24', '2026-09-28 10:59:11', 1),
(6935, 1, '2026-09-28', 34, 'MO-B034', 'aa0c34de4feedbadadff80ca9b01196e', 2, 'Business concerns', 'Manuel Cortez', NULL, NULL, NULL, 'self', 'skipped', 56, 'Angelu', 0, '2026-09-28 10:42:19', '2026-09-28 10:47:42', NULL, NULL, 1),
(6936, 1, '2026-09-28', 35, 'MO-E035', '311617db1f67ee2f41a9c76b9f06219c', 5, 'Financial assistance', 'Ana Domingo', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-09-28 10:43:23', '2026-09-28 10:45:44', '2026-09-28 10:45:44', '2026-09-28 10:50:56', 1),
(6937, 1, '2026-09-28', 36, 'MO-E036', 'a6cf1b4a2518323da9f3c412cc0db5ab', 5, 'Financial assistance', 'Rogelio Mercado', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-09-28 10:56:22', '2026-09-28 11:00:27', '2026-09-28 11:00:27', '2026-09-28 11:27:13', 1),
(6938, 1, '2026-09-28', 37, 'MO-A037', 'e87a7b0b4dffa32139bb8972ebc55dba', 1, 'Mayor\'s Clearance', 'Grace Navarro', NULL, NULL, NULL, 'self', 'skipped', 56, 'Angelu', 0, '2026-09-28 10:58:15', '2026-09-28 11:14:52', NULL, NULL, 1),
(6939, 1, '2026-09-28', 38, 'MO-F038', '07b8af8ae89acc9c8380504d7a569455', 6, 'Other', 'Gerardo Salazar', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-09-28 11:03:45', '2026-09-28 11:11:03', '2026-09-28 11:11:03', '2026-09-28 11:20:25', 1),
(6940, 1, '2026-09-28', 39, 'MO-B039', 'b0302bd560331673d2817a2fee538ebf', 2, 'Business concerns', 'Liza Aquino', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-09-28 11:03:49', '2026-09-28 11:24:02', '2026-09-28 11:24:02', '2026-09-28 11:49:39', 1),
(6941, 1, '2026-09-28', 40, 'MO-D040', '9f12c0786a8fd9ce0322364f88089be3', 4, 'Courtesy call', 'Antonio Fernandez', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-09-28 11:05:56', '2026-09-28 11:17:14', '2026-09-28 11:17:14', '2026-09-28 11:23:27', 1),
(6942, 1, '2026-09-28', 41, 'MO-D041', 'e37ea105ffc65293312c88d6f0f16b89', 4, 'Courtesy call', 'Elena Cruz', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-09-28 11:11:33', '2026-09-28 11:19:04', '2026-09-28 11:19:04', '2026-09-28 11:37:11', 1),
(6943, 1, '2026-09-28', 42, 'MO-B042', '0a88bca9cf86ed9a21b3af4ea6ed8960', 2, 'Business concerns', 'Pedro Gonzales', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-09-28 11:23:08', '2026-09-28 11:30:41', '2026-09-28 11:30:41', '2026-09-28 11:46:25', 1),
(6944, 1, '2026-09-28', 43, 'MO-F043', '35618d0062df1f91e8e385cb41d2f136', 6, 'Other', 'Marites Mendoza', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-09-28 11:38:16', '2026-09-28 11:55:00', '2026-09-28 11:55:00', '2026-09-28 12:03:18', 1),
(6945, 1, '2026-09-28', 44, 'MO-A044', '345a6816cb4f31873872b7989cf317d0', 1, 'Mayor\'s Clearance', 'Alfredo Rivera', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-09-28 11:42:16', '2026-09-28 11:51:42', '2026-09-28 11:51:42', '2026-09-28 12:00:20', 1),
(6946, 1, '2026-09-28', 45, 'MO-C045', 'be54b6da9cac03c7f54bd9fe8fed800c', 3, 'Personal concerns', 'Maria Villanueva', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-09-28 11:43:41', '2026-09-28 11:47:22', '2026-09-28 11:47:22', '2026-09-28 11:58:29', 1),
(6947, 1, '2026-09-28', 46, 'MO-E046', '8e29cf1e4c45d88ac60d920469a542e9', 5, 'Financial assistance', 'Eduardo Lopez', NULL, NULL, NULL, 'staff', 'cancelled', NULL, NULL, 0, '2026-09-28 11:46:29', NULL, NULL, NULL, 1),
(6948, 1, '2026-09-28', 47, 'MO-D047', 'b89c8c4606c43bcff77503ffc838a19a', 4, 'Courtesy call', 'Rosario Garcia', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-09-28 11:57:12', '2026-09-28 12:02:24', '2026-09-28 12:02:24', '2026-09-28 12:21:01', 1),
(6949, 1, '2026-09-28', 48, 'MO-A048', '4e3ed83f0bbe625303889cce4e470ca5', 1, 'Mayor\'s Clearance', 'Ricardo Flores', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-09-28 11:57:29', '2026-09-28 12:02:31', '2026-09-28 12:02:31', '2026-09-28 12:07:06', 1),
(6950, 1, '2026-09-28', 49, 'MO-E049', 'ac4da6703981d0338f751da169126ec2', 5, 'Financial assistance', 'Carmela Bautista', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-09-28 11:58:10', '2026-09-28 11:59:57', '2026-09-28 11:59:57', '2026-09-28 12:06:14', 1),
(6951, 1, '2026-09-28', 50, 'MO-D050', '5fc4b5ab9f83c0889cc5d5131af73050', 4, 'Courtesy call', 'Felipe Torres', NULL, NULL, NULL, 'staff', 'done', 54, 'Jean Frias', 0, '2026-09-28 12:02:23', '2026-09-28 12:05:34', '2026-09-28 12:05:34', '2026-09-28 12:13:53', 1),
(6952, 1, '2026-09-28', 51, 'MO-E051', '28fb7cbdd004a8b5e1b4d38ec181fd04', 5, 'Financial assistance', 'Cynthia Dela Cruz', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-09-28 12:13:59', '2026-09-28 12:15:10', '2026-09-28 12:15:10', '2026-09-28 12:27:20', 1),
(6953, 1, '2026-09-28', 52, 'MO-B052', '0a3bd92641e50468e655c3ebe9c9b421', 2, 'Business concerns', 'Jose Pascual', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-09-28 12:17:59', '2026-09-28 12:23:29', '2026-09-28 12:23:29', '2026-09-28 12:25:14', 1),
(6954, 1, '2026-09-28', 53, 'MO-B053', '4e43e27035ba69fb65bd85fc1eaf4478', 2, 'Business concerns', 'Teresita Reyes', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-09-28 12:25:36', '2026-09-28 12:26:06', '2026-09-28 12:26:06', '2026-09-28 12:36:52', 1),
(6955, 1, '2026-09-28', 54, 'MO-A054', '0a4b05a997028bad43bdaf791e0afe4a', 1, 'Mayor\'s Clearance', 'Danilo Ramos', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-09-28 12:27:54', '2026-09-28 12:31:12', '2026-09-28 12:31:12', '2026-09-28 12:34:31', 1),
(6956, 1, '2026-09-28', 55, 'MO-D055', '23d258db0f7fa74a55949d501dd38233', 4, 'Courtesy call', 'Lourdes Santos', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-09-28 12:37:59', '2026-09-28 12:42:41', '2026-09-28 12:42:41', '2026-09-28 12:51:40', 1),
(6957, 1, '2026-09-28', 56, 'MO-E056', '016cb0dce0de7aafdcb05dc4bdb4ea14', 5, 'Financial assistance', 'Ramon Castillo', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-09-28 13:00:05', '2026-09-28 13:04:22', '2026-09-28 13:04:22', '2026-09-28 13:06:52', 1),
(6958, 1, '2026-09-28', 57, 'MO-B057', '99e21463f5f15e7d2b7cea4b42a48731', 2, 'Business concerns', 'Josefina Cortez', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-09-28 13:07:39', '2026-09-28 13:11:51', '2026-09-28 13:11:51', '2026-09-28 13:54:41', 1),
(6959, 1, '2026-09-28', 58, 'MO-A058', '46a83926ab706c5cbcf473186e94806a', 1, 'Mayor\'s Clearance', 'Manuel Domingo', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-09-28 13:16:21', '2026-09-28 13:26:01', '2026-09-28 13:26:01', '2026-09-28 13:28:37', 1),
(6960, 1, '2026-09-28', 59, 'MO-F059', '9bba04c342b95adffd367b569ffa1d09', 6, 'Other', 'Ana Mercado', NULL, NULL, NULL, 'staff', 'done', 55, 'Sir Henjie', 0, '2026-09-28 13:17:19', '2026-09-28 13:28:04', '2026-09-28 13:28:04', '2026-09-28 13:30:35', 1),
(6961, 1, '2026-09-28', 60, 'MO-E060', '5b47e16324a5368ece788a4cbe18e37e', 5, 'Financial assistance', 'Rogelio Navarro', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-09-28 13:30:04', '2026-09-28 13:37:52', '2026-09-28 13:37:52', '2026-09-28 13:48:02', 1),
(6962, 1, '2026-09-28', 61, 'MO-A061', '48b237ae1b927b303ff6f2f1693291f5', 1, 'Mayor\'s Clearance', 'Grace Salazar', NULL, NULL, NULL, 'staff', 'skipped', 54, 'Jean Frias', 0, '2026-09-28 13:38:52', '2026-09-28 13:46:18', NULL, NULL, 1),
(6963, 1, '2026-09-28', 62, 'MO-D062', 'fc36eb9741d38276e25890fe8e0a0f80', 4, 'Courtesy call', 'Gerardo Aquino', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-09-28 13:42:21', '2026-09-28 13:45:22', '2026-09-28 13:45:22', '2026-09-28 13:49:37', 1),
(6964, 1, '2026-09-28', 63, 'MO-F063', '487df47d4e657a34ba2b4cf9c472eb6e', 6, 'Other', 'Liza Fernandez', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-09-28 13:44:33', '2026-09-28 13:51:08', '2026-09-28 13:51:08', '2026-09-28 13:57:06', 1),
(6965, 1, '2026-09-28', 64, 'MO-A064', '23397456ff6725eafcffd7ba28b04c75', 1, 'Mayor\'s Clearance', 'Antonio Cruz', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-09-28 13:53:49', '2026-09-28 14:07:08', '2026-09-28 14:07:08', '2026-09-28 14:13:41', 1),
(6966, 1, '2026-09-28', 65, 'MO-A065', '9864775935a4bebdb22d7a48ca282a6b', 1, 'Mayor\'s Clearance', 'Elena Gonzales', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-09-28 14:00:15', '2026-09-28 14:07:35', '2026-09-28 14:07:35', '2026-09-28 14:14:50', 1),
(6967, 1, '2026-09-28', 66, 'MO-E066', '208d2028327386f41a9c1f1b598d8f31', 5, 'Financial assistance', 'Pedro Mendoza', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-09-28 14:00:32', '2026-09-28 14:09:25', '2026-09-28 14:09:25', '2026-09-28 14:14:34', 1),
(6968, 1, '2026-09-28', 67, 'MO-B067', 'd3258fbc905734729e81cd280afb6fbd', 2, 'Business concerns', 'Marites Rivera', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-09-28 14:00:49', '2026-09-28 14:12:33', '2026-09-28 14:12:33', '2026-09-28 14:19:32', 1),
(6969, 1, '2026-09-28', 68, 'MO-D068', '70d788514266fe80644a46df3d1e8f46', 4, 'Courtesy call', 'Alfredo Villanueva', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-09-28 14:03:55', '2026-09-28 14:12:02', '2026-09-28 14:12:02', '2026-09-28 14:21:59', 1),
(6970, 1, '2026-09-28', 69, 'MO-D069', '8440022e130c84e7849bd27b09330df3', 4, 'Courtesy call', 'Maria Lopez', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-09-28 14:05:19', '2026-09-28 14:10:31', '2026-09-28 14:10:31', '2026-09-28 14:25:29', 1),
(6971, 1, '2026-09-28', 70, 'MO-A070', 'a3580b6b9662b20b4decedf49032ddae', 1, 'Mayor\'s Clearance', 'Eduardo Garcia', NULL, NULL, NULL, 'staff', 'cancelled', NULL, NULL, 0, '2026-09-28 14:06:46', NULL, NULL, NULL, 1),
(6972, 1, '2026-09-28', 71, 'MO-A071', '33f8722f3ff96acb411d9aeec0766016', 1, 'Mayor\'s Clearance', 'Rosario Flores', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-09-28 14:15:13', '2026-09-28 14:19:01', '2026-09-28 14:19:01', '2026-09-28 14:22:46', 1),
(6973, 1, '2026-09-28', 72, 'MO-B072', '9e1aad58598da44b3aecd16a1c67754e', 2, 'Business concerns', 'Ricardo Bautista', NULL, NULL, NULL, 'staff', 'done', 56, 'Angelu', 0, '2026-09-28 14:23:06', '2026-09-28 14:30:02', '2026-09-28 14:30:02', '2026-09-28 14:38:06', 1),
(6974, 1, '2026-09-28', 73, 'MO-C073', 'f1bb29b1599b379f4f6cbcfd50dbc422', 3, 'Personal concerns', 'Carmela Torres', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-09-28 14:25:34', '2026-09-28 14:33:18', '2026-09-28 14:33:18', '2026-09-28 14:39:06', 1),
(6975, 1, '2026-09-28', 74, 'MO-A074', 'e2b0b70112bd98cb41aab81e46f935db', 1, 'Mayor\'s Clearance', 'Felipe Dela Cruz', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-09-28 14:27:06', '2026-09-28 14:39:56', '2026-09-28 14:39:56', '2026-09-28 14:48:25', 1),
(6976, 1, '2026-09-28', 75, 'MO-A075', '73738bc085b46a8b8d2afe1c8b7cb2da', 1, 'Mayor\'s Clearance', 'Cynthia Pascual', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-09-28 14:30:05', '2026-09-28 14:33:58', '2026-09-28 14:33:58', '2026-09-28 14:38:59', 1),
(6977, 1, '2026-09-28', 76, 'MO-F076', '44c97109d43503b0658a5c7758e2b67b', 6, 'Other', 'Jose Reyes', NULL, NULL, NULL, 'staff', 'done', 55, 'Sir Henjie', 0, '2026-09-28 14:36:29', '2026-09-28 14:44:00', '2026-09-28 14:44:00', '2026-09-28 14:53:48', 1),
(6978, 1, '2026-09-28', 77, 'MO-B077', '8e7de28536039f3b959737fbd7cbedf8', 2, 'Business concerns', 'Teresita Ramos', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-09-28 14:42:11', '2026-09-28 14:43:35', '2026-09-28 14:43:35', '2026-09-28 14:53:06', 1),
(6979, 1, '2026-09-28', 78, 'MO-B078', '41b578809dbdc389e32b80de432d6c50', 2, 'Business concerns', 'Danilo Santos', NULL, NULL, NULL, 'staff', 'done', 55, 'Sir Henjie', 0, '2026-09-28 14:50:04', '2026-09-28 14:56:03', '2026-09-28 14:56:03', '2026-09-28 15:03:11', 1),
(6980, 1, '2026-09-28', 79, 'MO-A079', 'a42cd188c92f5cdbcd7a240f6f3e73e2', 1, 'Mayor\'s Clearance', 'Lourdes Castillo', NULL, NULL, NULL, 'self', 'skipped', 54, 'Jean Frias', 0, '2026-09-28 14:53:06', '2026-09-28 14:57:10', NULL, NULL, 1),
(6981, 1, '2026-09-28', 80, 'MO-B080', '98998446ceddd93ecf17b7b5f21a8356', 2, 'Business concerns', 'Ramon Cortez', NULL, NULL, NULL, 'staff', 'done', 54, 'Jean Frias', 0, '2026-09-28 14:54:50', '2026-09-28 15:00:48', '2026-09-28 15:00:48', '2026-09-28 15:08:55', 1),
(6982, 1, '2026-09-28', 81, 'MO-F081', '18c24c791a862e5c09e3c0d1b37213c6', 6, 'Other', 'Josefina Domingo', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-09-28 14:59:26', '2026-09-28 15:12:57', '2026-09-28 15:12:57', '2026-09-28 15:26:18', 1),
(6983, 1, '2026-09-28', 82, 'MO-D082', '96bbb8d82fc4e1059b06dcc62ee4a32e', 4, 'Courtesy call', 'Manuel Mercado', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-09-28 15:03:08', '2026-09-28 15:09:36', '2026-09-28 15:09:36', '2026-09-28 15:15:55', 1),
(6984, 1, '2026-09-28', 83, 'MO-C083', '7ff1216847e506a16df46f5b6f37b57d', 3, 'Personal concerns', 'Ana Navarro', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-09-28 15:08:30', '2026-09-28 15:13:12', '2026-09-28 15:13:12', '2026-09-28 15:23:50', 1),
(6985, 1, '2026-09-28', 84, 'MO-B084', '867591b68d0140ef2a5690ac35500dbc', 2, 'Business concerns', 'Rogelio Salazar', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-09-28 15:09:26', '2026-09-28 15:12:05', '2026-09-28 15:12:05', '2026-09-28 15:29:32', 1),
(6986, 1, '2026-09-28', 85, 'MO-C085', 'e4d355dbf1236b8c8b48791bac4366c3', 3, 'Personal concerns', 'Grace Aquino', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-09-28 15:09:56', '2026-09-28 15:11:04', '2026-09-28 15:11:04', '2026-09-28 15:13:53', 1),
(6987, 1, '2026-09-28', 86, 'MO-C086', '328edd48099d56198653a9d614654f61', 3, 'Personal concerns', 'Gerardo Fernandez', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-09-28 15:27:41', '2026-09-28 15:31:40', '2026-09-28 15:31:40', '2026-09-28 15:33:57', 1),
(6988, 1, '2026-09-28', 87, 'MO-C087', '5e5479ae4e940e70f4d9cb285232a6d1', 3, 'Personal concerns', 'Liza Cruz', NULL, NULL, NULL, 'self', 'skipped', 56, 'Angelu', 0, '2026-09-28 15:44:25', '2026-09-28 15:46:30', NULL, NULL, 1),
(6989, 1, '2026-09-28', 88, 'MO-C088', 'd15086de5fb8f22f546f14fe0cd7d415', 3, 'Personal concerns', 'Antonio Gonzales', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-09-28 15:46:50', '2026-09-28 15:49:19', '2026-09-28 15:49:19', '2026-09-28 16:03:03', 1),
(6990, 1, '2026-09-28', 89, 'MO-A089', '7d6ff3274d89e1e3ac1cdfc4b91dfd64', 1, 'Mayor\'s Clearance', 'Elena Mendoza', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-09-28 15:47:15', '2026-09-28 15:54:26', '2026-09-28 15:54:26', '2026-09-28 15:58:02', 1),
(6991, 1, '2026-09-28', 90, 'MO-B090', 'a390c476d666d8611b8a66a9936fdf43', 2, 'Business concerns', 'Pedro Rivera', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-09-28 15:51:26', '2026-09-28 15:55:07', '2026-09-28 15:55:07', '2026-09-28 16:00:39', 1),
(6992, 1, '2026-09-28', 91, 'MO-B091', 'f6348a36f85a2d39700d25f5fc7e47e7', 2, 'Business concerns', 'Marites Villanueva', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-09-28 15:52:15', '2026-09-28 15:54:00', '2026-09-28 15:54:00', '2026-09-28 16:09:33', 1),
(6993, 1, '2026-09-28', 92, 'MO-E092', '12eae8cc69e2084ecfd18a9034b779f4', 5, 'Financial assistance', 'Alfredo Lopez', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-09-28 15:54:06', '2026-09-28 15:59:23', '2026-09-28 15:59:23', '2026-09-28 16:02:49', 1),
(6994, 1, '2026-09-28', 93, 'MO-C093', '374db1891b0db6f24a573a97f32fb16f', 3, 'Personal concerns', 'Maria Garcia', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-09-28 15:58:22', '2026-09-28 16:01:58', '2026-09-28 16:01:58', '2026-09-28 16:20:26', 1),
(6995, 1, '2026-09-28', 94, 'MO-B094', 'ed031fac07b4c1be1761249a66176b62', 2, 'Business concerns', 'Eduardo Flores', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-09-28 16:14:10', '2026-09-28 16:16:38', '2026-09-28 16:16:38', '2026-09-28 16:27:29', 1),
(6996, 1, '2026-09-28', 95, 'MO-D095', 'aec77dd740bbfc2ce196c7867c018506', 4, 'Courtesy call', 'Rosario Bautista', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-09-28 16:17:37', '2026-09-28 16:21:18', '2026-09-28 16:21:18', '2026-09-28 16:30:28', 1),
(6997, 1, '2026-09-28', 96, 'MO-E096', 'c230ee026d5e121e5313517fce80ad2e', 5, 'Financial assistance', 'Ricardo Torres', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-09-28 16:48:33', '2026-09-28 16:49:03', '2026-09-28 16:49:03', '2026-09-28 16:56:21', 1),
(6998, 1, '2026-09-29', 1, 'MO-C001', '661caa81a1db8f519e6e00276fa720b0', 3, 'Personal concerns', 'Ramon Garcia', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-09-29 08:04:13', '2026-09-29 08:09:16', '2026-09-29 08:09:16', '2026-09-29 08:14:13', 1),
(6999, 1, '2026-09-29', 2, 'MO-F002', '19496f4c9da22f645b207b7f8e69dda3', 6, 'Other', 'Josefina Flores', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-09-29 08:21:56', '2026-09-29 08:31:20', '2026-09-29 08:31:20', '2026-09-29 08:38:18', 1),
(7000, 1, '2026-09-29', 3, 'MO-B003', 'd8507cb9cd593e6c578da18463660542', 2, 'Business concerns', 'Manuel Bautista', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-09-29 08:26:42', '2026-09-29 08:30:54', '2026-09-29 08:30:54', '2026-09-29 08:45:49', 1),
(7001, 1, '2026-09-29', 4, 'MO-A004', 'fb25c894b01298083461b6bde16050e4', 1, 'Mayor\'s Clearance', 'Ana Torres', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-09-29 08:53:35', '2026-09-29 09:01:05', '2026-09-29 09:01:05', '2026-09-29 09:05:39', 1),
(7002, 1, '2026-09-29', 5, 'MO-A005', '9d1244e54a5d1c99de8ad2a6d4fe2633', 1, 'Mayor\'s Clearance', 'Rogelio Dela Cruz', NULL, NULL, NULL, 'staff', 'done', 56, 'Angelu', 0, '2026-09-29 08:59:41', '2026-09-29 09:07:24', '2026-09-29 09:07:24', '2026-09-29 09:13:44', 1),
(7003, 1, '2026-09-29', 6, 'MO-D006', '4cd88f5d4af55e267a854f86fe8fa2be', 4, 'Courtesy call', 'Grace Pascual', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-09-29 09:03:44', '2026-09-29 09:20:05', '2026-09-29 09:20:05', '2026-09-29 09:33:33', 1),
(7004, 1, '2026-09-29', 7, 'MO-B007', 'bab2170ef0e5697311dee7eceee0108f', 2, 'Business concerns', 'Gerardo Reyes', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-09-29 09:06:39', '2026-09-29 09:12:38', '2026-09-29 09:12:38', '2026-09-29 09:17:36', 1),
(7005, 1, '2026-09-29', 8, 'MO-E008', 'f3d511f52c6ddd956a28ee0c78777739', 5, 'Financial assistance', 'Liza Ramos', NULL, NULL, NULL, 'staff', 'done', 56, 'Angelu', 0, '2026-09-29 09:08:52', '2026-09-29 09:19:18', '2026-09-29 09:19:18', '2026-09-29 09:28:14', 1),
(7006, 1, '2026-09-29', 9, 'MO-A009', '103ee899e338f49c292d124c389f1ee4', 1, 'Mayor\'s Clearance', 'Antonio Santos', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-09-29 09:21:17', '2026-09-29 09:25:00', '2026-09-29 09:25:00', '2026-09-29 09:28:53', 1),
(7007, 1, '2026-09-29', 10, 'MO-A010', '4fdf50b24edae0b0116ffbda31cb7588', 1, 'Mayor\'s Clearance', 'Elena Castillo', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-09-29 09:27:30', '2026-09-29 09:34:48', '2026-09-29 09:34:48', '2026-09-29 09:41:09', 1),
(7008, 1, '2026-09-29', 11, 'MO-C011', 'd365d90ec8605cabae06c45ec40b8ae5', 3, 'Personal concerns', 'Pedro Cortez', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-09-29 09:30:01', '2026-09-29 09:33:35', '2026-09-29 09:33:35', '2026-09-29 09:44:21', 1),
(7009, 1, '2026-09-29', 12, 'MO-C012', 'a3ffbcd0d0a6dace2a5e046d57376326', 3, 'Personal concerns', 'Marites Domingo', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-09-29 09:30:21', '2026-09-29 09:35:11', '2026-09-29 09:35:11', '2026-09-29 09:46:25', 1),
(7010, 1, '2026-09-29', 13, 'MO-A013', 'cf82bbecbdb282cc18f87de187805745', 1, 'Mayor\'s Clearance', 'Alfredo Mercado', NULL, NULL, NULL, 'self', 'skipped', 54, 'Jean Frias', 0, '2026-09-29 09:30:30', '2026-09-29 09:41:43', NULL, NULL, 1),
(7011, 1, '2026-09-29', 14, 'MO-E014', 'f1205c4d41befaac42c44909faf3e7e2', 5, 'Financial assistance', 'Maria Navarro', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-09-29 09:33:38', '2026-09-29 09:42:09', '2026-09-29 09:42:09', '2026-09-29 09:50:13', 1),
(7012, 1, '2026-09-29', 15, 'MO-F015', '8068fb97add4dc71db525a8d0a56a548', 6, 'Other', 'Eduardo Salazar', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-09-29 09:43:37', '2026-09-29 09:48:07', '2026-09-29 09:48:07', '2026-09-29 09:54:10', 1),
(7013, 1, '2026-09-29', 16, 'MO-B016', '87d44078a775c1007824e558eb7be6ef', 2, 'Business concerns', 'Rosario Aquino', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-09-29 09:44:59', '2026-09-29 09:50:26', '2026-09-29 09:50:26', '2026-09-29 09:54:25', 1),
(7014, 1, '2026-09-29', 17, 'MO-D017', 'b975ee04ef8d71dd4586ca241971559b', 4, 'Courtesy call', 'Ricardo Fernandez', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-09-29 09:56:37', '2026-09-29 10:02:51', '2026-09-29 10:02:51', '2026-09-29 10:17:32', 1),
(7015, 1, '2026-09-29', 18, 'MO-F018', '77eec686295c1962bf071ce259863efe', 6, 'Other', 'Carmela Cruz', NULL, NULL, NULL, 'staff', 'done', 56, 'Angelu', 0, '2026-09-29 10:02:46', '2026-09-29 10:10:56', '2026-09-29 10:10:56', '2026-09-29 10:13:49', 1),
(7016, 1, '2026-09-29', 19, 'MO-F019', '372b0d28d9d83ae015f4019939cdb8d4', 6, 'Other', 'Felipe Gonzales', NULL, NULL, NULL, 'staff', 'done', 54, 'Jean Frias', 0, '2026-09-29 10:05:19', '2026-09-29 10:15:35', '2026-09-29 10:15:35', '2026-09-29 10:17:34', 1),
(7017, 1, '2026-09-29', 20, 'MO-C020', 'a6cd8269fa10206a31e2650652b0e8aa', 3, 'Personal concerns', 'Cynthia Mendoza', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-09-29 10:19:58', '2026-09-29 10:27:17', '2026-09-29 10:27:17', '2026-09-29 10:43:50', 1),
(7018, 1, '2026-09-29', 21, 'MO-A021', '0f7f7192592d15f2cd40c5b92fdf85db', 1, 'Mayor\'s Clearance', 'Jose Rivera', NULL, NULL, NULL, 'staff', 'done', 54, 'Jean Frias', 0, '2026-09-29 10:23:00', '2026-09-29 10:42:38', '2026-09-29 10:42:38', '2026-09-29 10:48:10', 1),
(7019, 1, '2026-09-29', 22, 'MO-F022', '27463a3a77c20921d2369188935ef957', 6, 'Other', 'Teresita Villanueva', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-09-29 10:25:34', '2026-09-29 10:30:07', '2026-09-29 10:30:07', '2026-09-29 10:39:20', 1),
(7020, 1, '2026-09-29', 23, 'MO-A023', '0129428d90f54e2d7bfd89f8c12b6fa1', 1, 'Mayor\'s Clearance', 'Danilo Lopez', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-09-29 10:27:42', '2026-09-29 10:29:43', '2026-09-29 10:29:43', '2026-09-29 10:35:08', 1),
(7021, 1, '2026-09-29', 24, 'MO-C024', '701089fea7fa601ba13429762e279834', 3, 'Personal concerns', 'Lourdes Garcia', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-09-29 10:28:35', '2026-09-29 10:34:37', '2026-09-29 10:34:37', '2026-09-29 10:41:04', 1),
(7022, 1, '2026-09-29', 25, 'MO-C025', '2099fb9aba83516ef0a02d3271c7bc7e', 3, 'Personal concerns', 'Ramon Flores', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-09-29 10:34:58', '2026-09-29 10:46:35', '2026-09-29 10:46:35', '2026-09-29 11:05:17', 1),
(7023, 1, '2026-09-29', 26, 'MO-C026', 'ccfde501d02c3de2cdec5190590170f5', 3, 'Personal concerns', 'Josefina Bautista', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-09-29 10:42:26', '2026-09-29 10:49:52', '2026-09-29 10:49:52', '2026-09-29 10:58:17', 1),
(7024, 1, '2026-09-29', 27, 'MO-B027', '950e1dea3cb6f6de42f5dcbe98073685', 2, 'Business concerns', 'Manuel Torres', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-09-29 10:49:32', '2026-09-29 10:51:35', '2026-09-29 10:51:35', '2026-09-29 10:57:52', 1),
(7025, 1, '2026-09-29', 28, 'MO-D028', 'd6ffbe19fe463bd8fb901d1f4b745ac1', 4, 'Courtesy call', 'Ana Dela Cruz', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-09-29 11:05:32', '2026-09-29 11:08:39', '2026-09-29 11:08:39', '2026-09-29 11:11:52', 1),
(7026, 1, '2026-09-29', 29, 'MO-A029', '62e1d56b0731b0c35fd9a56ffb4e58da', 1, 'Mayor\'s Clearance', 'Rogelio Pascual', NULL, NULL, NULL, 'staff', 'done', 56, 'Angelu', 0, '2026-09-29 11:06:48', '2026-09-29 11:09:27', '2026-09-29 11:09:27', '2026-09-29 11:16:57', 1),
(7027, 1, '2026-09-29', 30, 'MO-E030', '6f32b11ba04a4f834d968636df21879f', 5, 'Financial assistance', 'Grace Reyes', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-09-29 11:14:28', '2026-09-29 11:24:48', '2026-09-29 11:24:48', '2026-09-29 11:29:39', 1),
(7028, 1, '2026-09-29', 31, 'MO-D031', 'af9710496e1f8581835258182a84fc4a', 4, 'Courtesy call', 'Gerardo Ramos', NULL, NULL, NULL, 'self', 'skipped', 54, 'Jean Frias', 0, '2026-09-29 11:14:59', '2026-09-29 11:18:43', NULL, NULL, 1),
(7029, 1, '2026-09-29', 32, 'MO-A032', '50167e74028a93fd6b1aafcd996f9134', 1, 'Mayor\'s Clearance', 'Liza Santos', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-09-29 11:27:11', '2026-09-29 11:29:05', '2026-09-29 11:29:05', '2026-09-29 11:42:02', 1),
(7030, 1, '2026-09-29', 33, 'MO-E033', 'dd958f5bfb2673af548d71cab5b24a2e', 5, 'Financial assistance', 'Antonio Castillo', NULL, NULL, NULL, 'staff', 'cancelled', NULL, NULL, 0, '2026-09-29 11:29:33', NULL, NULL, NULL, 1),
(7031, 1, '2026-09-29', 34, 'MO-C034', '2c476a72e81ddd3c479d012b90d401b4', 3, 'Personal concerns', 'Elena Cortez', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-09-29 11:35:22', '2026-09-29 11:37:17', '2026-09-29 11:37:17', '2026-09-29 11:46:15', 1),
(7032, 1, '2026-09-29', 35, 'MO-A035', 'b82c2551cffea0ad3ae32e93e6f70269', 1, 'Mayor\'s Clearance', 'Pedro Domingo', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-09-29 11:39:33', '2026-09-29 11:45:39', '2026-09-29 11:45:39', '2026-09-29 11:54:15', 1),
(7033, 1, '2026-09-29', 36, 'MO-C036', '918a9a25efffa6862359728f1e40bbc1', 3, 'Personal concerns', 'Marites Mercado', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-09-29 11:46:46', '2026-09-29 11:50:24', '2026-09-29 11:50:24', '2026-09-29 11:52:57', 1),
(7034, 1, '2026-09-29', 37, 'MO-A037', '60e4658853451f25add1f25a3e2e5e43', 1, 'Mayor\'s Clearance', 'Alfredo Navarro', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-09-29 11:52:29', '2026-09-29 12:11:09', '2026-09-29 12:11:09', '2026-09-29 12:16:10', 1),
(7035, 1, '2026-09-29', 38, 'MO-C038', 'ceb67636f6097fe49d274c87d51a1f6c', 3, 'Personal concerns', 'Maria Salazar', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-09-29 11:56:10', '2026-09-29 12:00:10', '2026-09-29 12:00:10', '2026-09-29 12:17:08', 1),
(7036, 1, '2026-09-29', 39, 'MO-A039', '6e460398ce4fa418d85c3d8f5b9ffe98', 1, 'Mayor\'s Clearance', 'Eduardo Aquino', NULL, NULL, NULL, 'staff', 'done', 54, 'Jean Frias', 0, '2026-09-29 11:56:52', '2026-09-29 12:02:27', '2026-09-29 12:02:27', '2026-09-29 12:07:14', 1),
(7037, 1, '2026-09-29', 40, 'MO-B040', '8d0945f851ba84c095b0b7dbef873d94', 2, 'Business concerns', 'Rosario Fernandez', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-09-29 12:02:18', '2026-09-29 12:04:15', '2026-09-29 12:04:15', '2026-09-29 12:15:38', 1),
(7038, 1, '2026-09-29', 41, 'MO-C041', '7838d331b5c1fa19d9412f8163875bd3', 3, 'Personal concerns', 'Ricardo Cruz', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-09-29 12:02:42', '2026-09-29 12:03:46', '2026-09-29 12:03:46', '2026-09-29 12:08:08', 1),
(7039, 1, '2026-09-29', 42, 'MO-D042', '42a4f5413376f09f2b574c7f50384e21', 4, 'Courtesy call', 'Carmela Gonzales', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-09-29 12:09:03', '2026-09-29 12:13:55', '2026-09-29 12:13:55', '2026-09-29 12:20:24', 1),
(7040, 1, '2026-09-29', 43, 'MO-E043', '43fbd57daf0c7073f6e00db2fa5d33f3', 5, 'Financial assistance', 'Felipe Mendoza', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-09-29 12:12:34', '2026-09-29 12:13:04', '2026-09-29 12:13:04', '2026-09-29 12:22:43', 1),
(7041, 1, '2026-09-29', 44, 'MO-F044', '889789399551893437b6ee03dc2f5bb6', 6, 'Other', 'Cynthia Rivera', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-09-29 12:33:01', '2026-09-29 12:36:17', '2026-09-29 12:36:17', '2026-09-29 12:40:19', 1),
(7042, 1, '2026-09-29', 45, 'MO-C045', '289d710b7015292f0bace7629934e5a8', 3, 'Personal concerns', 'Jose Villanueva', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-09-29 12:54:25', '2026-09-29 12:58:12', '2026-09-29 12:58:12', '2026-09-29 13:03:36', 1),
(7043, 1, '2026-09-29', 46, 'MO-F046', '2c5dce4b42bdd9160ab0a4aaf3db825c', 6, 'Other', 'Teresita Lopez', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-09-29 12:59:19', '2026-09-29 13:05:21', '2026-09-29 13:05:21', '2026-09-29 13:21:21', 1),
(7044, 1, '2026-09-29', 47, 'MO-C047', '033a54a3c739c3d8a1f756dfdebcf0fa', 3, 'Personal concerns', 'Danilo Garcia', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-09-29 13:00:50', '2026-09-29 13:19:10', '2026-09-29 13:19:10', '2026-09-29 13:29:00', 1),
(7045, 1, '2026-09-29', 48, 'MO-E048', '3180bd1f014fa8b3b2b756fc2bea13f3', 5, 'Financial assistance', 'Lourdes Flores', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-09-29 13:40:18', '2026-09-29 13:41:35', '2026-09-29 13:41:35', '2026-09-29 13:48:05', 1),
(7046, 1, '2026-09-29', 49, 'MO-A049', '209bd497d46b01018431c8ba001668bc', 1, 'Mayor\'s Clearance', 'Ramon Bautista', NULL, NULL, NULL, 'self', 'cancelled', NULL, NULL, 0, '2026-09-29 13:55:09', NULL, NULL, NULL, 1),
(7047, 1, '2026-09-29', 50, 'MO-A050', '90924f1ea8b6d9c31b7794b156c62313', 1, 'Mayor\'s Clearance', 'Josefina Torres', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-09-29 14:04:13', '2026-09-29 14:05:07', '2026-09-29 14:05:07', '2026-09-29 14:07:12', 1),
(7048, 1, '2026-09-29', 51, 'MO-B051', 'ab4635457e9d5982e15da7ba8ec93488', 2, 'Business concerns', 'Manuel Dela Cruz', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-09-29 14:07:37', '2026-09-29 14:14:02', '2026-09-29 14:14:02', '2026-09-29 14:28:32', 1),
(7049, 1, '2026-09-29', 52, 'MO-E052', 'ad28df0c7d1e075761f2eb0f0fe7c7f8', 5, 'Financial assistance', 'Ana Pascual', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-09-29 14:18:13', '2026-09-29 14:24:26', '2026-09-29 14:24:26', '2026-09-29 14:34:14', 1),
(7050, 1, '2026-09-29', 53, 'MO-A053', '31ca400307a2f04a93c10724cf52a14c', 1, 'Mayor\'s Clearance', 'Rogelio Reyes', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-09-29 14:18:41', '2026-09-29 14:22:58', '2026-09-29 14:22:58', '2026-09-29 14:30:01', 1),
(7051, 1, '2026-09-29', 54, 'MO-C054', 'c7d43b279328cc99b6f3599c0b23fad7', 3, 'Personal concerns', 'Grace Ramos', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-09-29 14:19:13', '2026-09-29 14:21:31', '2026-09-29 14:21:31', '2026-09-29 14:27:46', 1),
(7052, 1, '2026-09-29', 55, 'MO-C055', '657fb8f6a5bb209346c1b158b55ba65f', 3, 'Personal concerns', 'Gerardo Santos', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-09-29 14:23:17', '2026-09-29 14:26:10', '2026-09-29 14:26:10', '2026-09-29 14:37:41', 1),
(7053, 1, '2026-09-29', 56, 'MO-A056', '2560d2f496bbf73e4ddb8cee7cd0a2bc', 1, 'Mayor\'s Clearance', 'Liza Castillo', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-09-29 14:52:42', '2026-09-29 14:54:30', '2026-09-29 14:54:30', '2026-09-29 14:59:08', 1),
(7054, 1, '2026-09-29', 57, 'MO-B057', '9552fe20a22f76afca5d22b324504daa', 2, 'Business concerns', 'Antonio Cortez', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-09-29 14:55:33', '2026-09-29 15:15:22', '2026-09-29 15:15:22', '2026-09-29 15:20:20', 1),
(7055, 1, '2026-09-29', 58, 'MO-D058', 'a10d6f35f659169acdb02b1fe2da2eda', 4, 'Courtesy call', 'Elena Domingo', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-09-29 15:02:31', '2026-09-29 15:09:20', '2026-09-29 15:09:20', '2026-09-29 15:17:49', 1),
(7056, 1, '2026-09-29', 59, 'MO-E059', 'a29ecc868a599565fa8f04001a3ea8fe', 5, 'Financial assistance', 'Pedro Mercado', NULL, NULL, NULL, 'staff', 'done', 54, 'Jean Frias', 0, '2026-09-29 15:03:38', '2026-09-29 15:07:00', '2026-09-29 15:07:00', '2026-09-29 15:10:39', 1),
(7057, 1, '2026-09-29', 60, 'MO-A060', '93795b8e2a95bca906e9d1e0b7e87975', 1, 'Mayor\'s Clearance', 'Marites Navarro', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-09-29 15:04:49', '2026-09-29 15:09:31', '2026-09-29 15:09:31', '2026-09-29 15:21:02', 1),
(7058, 1, '2026-09-29', 61, 'MO-E061', '16015ef4a8d764495bcc17026d95f80e', 5, 'Financial assistance', 'Alfredo Salazar', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-09-29 15:18:23', '2026-09-29 15:20:02', '2026-09-29 15:20:02', '2026-09-29 15:30:42', 1),
(7059, 1, '2026-09-29', 62, 'MO-A062', 'fe00f754fd48f66647e20d4203293c01', 1, 'Mayor\'s Clearance', 'Maria Aquino', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-09-29 15:25:22', '2026-09-29 15:29:22', '2026-09-29 15:29:22', '2026-09-29 15:38:10', 1),
(7060, 1, '2026-09-29', 63, 'MO-A063', 'd8d32e3f2bcd4ed1f477ee383693ed3f', 1, 'Mayor\'s Clearance', 'Eduardo Fernandez', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-09-29 15:27:10', '2026-09-29 15:32:00', '2026-09-29 15:32:00', '2026-09-29 15:37:49', 1),
(7061, 1, '2026-09-29', 64, 'MO-E064', 'e3a24000d5420ed84eb08ccbaac97bd5', 5, 'Financial assistance', 'Rosario Cruz', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-09-29 15:28:26', '2026-09-29 15:32:49', '2026-09-29 15:32:49', '2026-09-29 15:37:24', 1),
(7062, 1, '2026-09-29', 65, 'MO-A065', '7310dd1a7421be48f6abb381a20b97bc', 1, 'Mayor\'s Clearance', 'Ricardo Gonzales', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-09-29 15:57:35', '2026-09-29 16:13:03', '2026-09-29 16:13:03', '2026-09-29 16:15:06', 1),
(7063, 1, '2026-09-29', 66, 'MO-A066', '69f646b9dc0d12193174ff27eb60b881', 1, 'Mayor\'s Clearance', 'Carmela Mendoza', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-09-29 16:01:35', '2026-09-29 16:03:28', '2026-09-29 16:03:28', '2026-09-29 16:06:37', 1),
(7064, 1, '2026-09-29', 67, 'MO-D067', '5cf903752dbc89815adfe1dd118ca5f9', 4, 'Courtesy call', 'Felipe Rivera', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-09-29 16:20:25', '2026-09-29 16:22:47', '2026-09-29 16:22:47', '2026-09-29 16:54:44', 1),
(7065, 1, '2026-09-29', 68, 'MO-E068', '4e54afb4a0fc8e6979be9db8a62bf2f4', 5, 'Financial assistance', 'Cynthia Villanueva', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-09-29 16:23:15', '2026-09-29 16:25:28', '2026-09-29 16:25:28', '2026-09-29 16:46:09', 1),
(7066, 1, '2026-09-29', 69, 'MO-A069', '611c05454f5884a077fe68ce1c3496ca', 1, 'Mayor\'s Clearance', 'Jose Lopez', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-09-29 16:44:32', '2026-09-29 16:48:31', '2026-09-29 16:48:31', '2026-09-29 16:53:42', 1),
(7067, 1, '2026-09-30', 1, 'MO-B001', '0c9dd238f647260d30ad5c67331ac423', 2, 'Business concerns', 'Liza Garcia', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-09-30 08:09:36', '2026-09-30 08:22:21', '2026-09-30 08:22:21', '2026-09-30 08:26:26', 1),
(7068, 1, '2026-09-30', 2, 'MO-E002', '5dd297d44ab8d3a8bf48a6408f39730d', 5, 'Financial assistance', 'Antonio Flores', NULL, NULL, NULL, 'staff', 'done', 54, 'Jean Frias', 0, '2026-09-30 08:12:46', '2026-09-30 08:16:15', '2026-09-30 08:16:15', '2026-09-30 08:23:18', 1),
(7069, 1, '2026-09-30', 3, 'MO-A003', '0ef3a02c71fb64bdeb7c0a223e106930', 1, 'Mayor\'s Clearance', 'Elena Bautista', NULL, NULL, NULL, 'self', 'skipped', 54, 'Jean Frias', 0, '2026-09-30 08:57:17', '2026-09-30 08:59:08', NULL, NULL, 1),
(7070, 1, '2026-09-30', 4, 'MO-A004', 'bde82ab6695d4605bff659f84dc86311', 1, 'Mayor\'s Clearance', 'Pedro Torres', NULL, NULL, NULL, 'staff', 'cancelled', NULL, NULL, 0, '2026-09-30 08:58:56', NULL, NULL, NULL, 1),
(7071, 1, '2026-09-30', 5, 'MO-A005', 'f75a83b84e82457134c004eef174b7cb', 1, 'Mayor\'s Clearance', 'Marites Dela Cruz', NULL, NULL, NULL, 'staff', 'done', 54, 'Jean Frias', 0, '2026-09-30 09:01:06', '2026-09-30 09:03:16', '2026-09-30 09:03:16', '2026-09-30 09:07:10', 1),
(7072, 1, '2026-09-30', 6, 'MO-A006', '3eca93b3c90f9485c1a693ff5f509e11', 1, 'Mayor\'s Clearance', 'Alfredo Pascual', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-09-30 09:27:33', '2026-09-30 09:32:58', '2026-09-30 09:32:58', '2026-09-30 09:36:46', 1),
(7073, 1, '2026-09-30', 7, 'MO-A007', '4cf6d10434ca2bcfdcbf499ad5c3bfa5', 1, 'Mayor\'s Clearance', 'Maria Reyes', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-09-30 09:28:19', '2026-09-30 09:31:15', '2026-09-30 09:31:15', '2026-09-30 09:34:07', 1),
(7074, 1, '2026-09-30', 8, 'MO-D008', '81a4c965781cdfe2373a097599014ad6', 4, 'Courtesy call', 'Eduardo Ramos', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-09-30 09:37:08', '2026-09-30 09:39:40', '2026-09-30 09:39:40', '2026-09-30 09:45:58', 1),
(7075, 1, '2026-09-30', 9, 'MO-E009', 'ff6bc365dcdfc96147c5ab112df76f44', 5, 'Financial assistance', 'Rosario Santos', NULL, NULL, NULL, 'staff', 'done', 54, 'Jean Frias', 0, '2026-09-30 09:37:43', '2026-09-30 09:39:16', '2026-09-30 09:39:16', '2026-09-30 09:47:18', 1),
(7076, 1, '2026-09-30', 10, 'MO-A010', '008dc52e1fed664d6a9bb42042952038', 1, 'Mayor\'s Clearance', 'Ricardo Castillo', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-09-30 09:39:26', '2026-09-30 09:43:29', '2026-09-30 09:43:29', '2026-09-30 09:50:08', 1),
(7077, 1, '2026-09-30', 11, 'MO-A011', 'd58f413c7c41cb268150b12842c59a33', 1, 'Mayor\'s Clearance', 'Carmela Cortez', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-09-30 09:44:05', '2026-09-30 09:47:18', '2026-09-30 09:47:18', '2026-09-30 09:51:08', 1),
(7078, 1, '2026-09-30', 12, 'MO-B012', 'd0c87b4eae23ef27dbced5beb1573497', 2, 'Business concerns', 'Felipe Domingo', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-09-30 09:45:16', '2026-09-30 09:54:39', '2026-09-30 09:54:39', '2026-09-30 10:15:51', 1),
(7079, 1, '2026-09-30', 13, 'MO-B013', 'a3454025060d48318f51ff88ba03ef43', 2, 'Business concerns', 'Cynthia Mercado', NULL, NULL, NULL, 'staff', 'done', 54, 'Jean Frias', 0, '2026-09-30 10:01:16', '2026-09-30 10:04:51', '2026-09-30 10:04:51', '2026-09-30 10:17:51', 1),
(7080, 1, '2026-09-30', 14, 'MO-C014', 'aae5cc61bc25e2c90fd00c7a6352f7ee', 3, 'Personal concerns', 'Jose Navarro', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-09-30 10:02:37', '2026-09-30 10:05:27', '2026-09-30 10:05:27', '2026-09-30 10:22:14', 1),
(7081, 1, '2026-09-30', 15, 'MO-A015', '1b0d8dde7054788129fa042a82d0957a', 1, 'Mayor\'s Clearance', 'Teresita Salazar', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-09-30 10:08:32', '2026-09-30 10:20:59', '2026-09-30 10:20:59', '2026-09-30 10:26:54', 1),
(7082, 1, '2026-09-30', 16, 'MO-C016', 'c45f253e33c2f7e5315b6bb5501fb76d', 3, 'Personal concerns', 'Danilo Aquino', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-09-30 10:09:03', '2026-09-30 10:10:50', '2026-09-30 10:10:50', '2026-09-30 10:22:50', 1),
(7083, 1, '2026-09-30', 17, 'MO-A017', 'ac6bbbcd8d28ac11e0e886183fc246f6', 1, 'Mayor\'s Clearance', 'Lourdes Fernandez', NULL, NULL, NULL, 'self', 'cancelled', NULL, NULL, 0, '2026-09-30 10:21:47', NULL, NULL, NULL, 1),
(7084, 1, '2026-09-30', 18, 'MO-A018', '9676c1cc63e28c7ff06c5eb4186ee051', 1, 'Mayor\'s Clearance', 'Ramon Cruz', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-09-30 10:21:57', '2026-09-30 10:32:39', '2026-09-30 10:32:39', '2026-09-30 10:38:21', 1),
(7085, 1, '2026-09-30', 19, 'MO-A019', '605cbd684c0e1c66bfdb77aec590a0e5', 1, 'Mayor\'s Clearance', 'Josefina Gonzales', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-09-30 10:25:46', '2026-09-30 10:38:53', '2026-09-30 10:38:53', '2026-09-30 10:49:16', 1),
(7086, 1, '2026-09-30', 20, 'MO-A020', 'ef34cbbca115d9999f6f92628d0fdab9', 1, 'Mayor\'s Clearance', 'Manuel Mendoza', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-09-30 10:28:16', '2026-09-30 11:00:56', '2026-09-30 11:00:56', '2026-09-30 11:02:26', 1),
(7087, 1, '2026-09-30', 21, 'MO-B021', '60a870d872b727cb684a6d48b77d01f5', 2, 'Business concerns', 'Ana Rivera', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-09-30 10:32:13', '2026-09-30 10:41:27', '2026-09-30 10:41:27', '2026-09-30 10:57:25', 1),
(7088, 1, '2026-09-30', 22, 'MO-A022', '434648e7a17eb760bd0c153b44199ca3', 1, 'Mayor\'s Clearance', 'Rogelio Villanueva', NULL, NULL, NULL, 'staff', 'done', 56, 'Angelu', 0, '2026-09-30 10:34:07', '2026-09-30 10:55:37', '2026-09-30 10:55:37', '2026-09-30 10:58:14', 1),
(7089, 1, '2026-09-30', 23, 'MO-E023', '819ef9a4147f5d9ba4cd1f0f8f8136d6', 5, 'Financial assistance', 'Grace Lopez', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-09-30 10:34:36', '2026-09-30 11:07:15', '2026-09-30 11:07:15', '2026-09-30 11:18:42', 1),
(7090, 1, '2026-09-30', 24, 'MO-A024', '031ac9aaef175064882bfe5596cc8e8d', 1, 'Mayor\'s Clearance', 'Gerardo Garcia', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-09-30 10:39:29', '2026-09-30 10:47:33', '2026-09-30 10:47:33', '2026-09-30 10:56:02', 1),
(7091, 1, '2026-09-30', 25, 'MO-B025', '440900a880d86209cefc4ea4353fd2b7', 2, 'Business concerns', 'Liza Flores', NULL, NULL, NULL, 'self', 'skipped', 56, 'Angelu', 0, '2026-09-30 10:50:12', '2026-09-30 10:51:50', NULL, NULL, 1),
(7092, 1, '2026-09-30', 26, 'MO-A026', 'f7573f5b813af05cacef8f1883f58f1f', 1, 'Mayor\'s Clearance', 'Antonio Bautista', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-09-30 10:53:58', '2026-09-30 11:03:16', '2026-09-30 11:03:16', '2026-09-30 11:10:34', 1),
(7093, 1, '2026-09-30', 27, 'MO-B027', '644cea1edfc0f01646f3c029592fdf83', 2, 'Business concerns', 'Elena Torres', NULL, NULL, NULL, 'staff', 'done', 56, 'Angelu', 0, '2026-09-30 11:02:22', '2026-09-30 11:06:56', '2026-09-30 11:06:56', '2026-09-30 11:14:05', 1),
(7094, 1, '2026-09-30', 28, 'MO-C028', '6fadbc7653ea550cc3f6d4d7eceefdd9', 3, 'Personal concerns', 'Pedro Dela Cruz', NULL, NULL, NULL, 'self', 'skipped', 55, 'Sir Henjie', 0, '2026-09-30 11:09:28', '2026-09-30 11:14:36', NULL, NULL, 1),
(7095, 1, '2026-09-30', 29, 'MO-D029', '4fb7865551bb32d3295134a2900e5baf', 4, 'Courtesy call', 'Marites Pascual', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-09-30 11:10:39', '2026-09-30 11:15:51', '2026-09-30 11:15:51', '2026-09-30 11:30:27', 1),
(7096, 1, '2026-09-30', 30, 'MO-A030', 'a60ffad26b5a8a52311060779966141d', 1, 'Mayor\'s Clearance', 'Alfredo Reyes', NULL, NULL, NULL, 'staff', 'done', 54, 'Jean Frias', 0, '2026-09-30 11:22:56', '2026-09-30 11:35:30', '2026-09-30 11:35:30', '2026-09-30 11:37:46', 1),
(7097, 1, '2026-09-30', 31, 'MO-E031', 'aef1c4045f494a2f4a45766a9adad3e8', 5, 'Financial assistance', 'Maria Ramos', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-09-30 11:24:43', '2026-09-30 11:29:20', '2026-09-30 11:29:20', '2026-09-30 11:38:51', 1),
(7098, 1, '2026-09-30', 32, 'MO-A032', '1bf8810904e0b86560b19b233e664459', 1, 'Mayor\'s Clearance', 'Eduardo Santos', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-09-30 11:25:58', '2026-09-30 11:37:45', '2026-09-30 11:37:45', '2026-09-30 11:40:29', 1),
(7099, 1, '2026-09-30', 33, 'MO-A033', '82543aa8ecb51138cf486d3dadc5021f', 1, 'Mayor\'s Clearance', 'Rosario Castillo', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-09-30 11:30:51', '2026-09-30 11:43:36', '2026-09-30 11:43:36', '2026-09-30 11:47:11', 1),
(7100, 1, '2026-09-30', 34, 'MO-A034', '691cfeb88846beb759a5007d206d626b', 1, 'Mayor\'s Clearance', 'Ricardo Cortez', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-09-30 11:36:33', '2026-09-30 11:45:04', '2026-09-30 11:45:04', '2026-09-30 11:48:23', 1),
(7101, 1, '2026-09-30', 35, 'MO-E035', 'a9138209cbace31b84d07097871bb47a', 5, 'Financial assistance', 'Carmela Domingo', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-09-30 11:43:58', '2026-09-30 11:51:38', '2026-09-30 11:51:38', '2026-09-30 11:59:29', 1),
(7102, 1, '2026-09-30', 36, 'MO-A036', '5991911348beea3d1cc39f967138ccee', 1, 'Mayor\'s Clearance', 'Felipe Mercado', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-09-30 11:45:27', '2026-09-30 11:56:03', '2026-09-30 11:56:03', '2026-09-30 11:58:41', 1),
(7103, 1, '2026-09-30', 37, 'MO-C037', '6b8e346574760c21e33d1bbbd816bdfb', 3, 'Personal concerns', 'Cynthia Navarro', NULL, NULL, NULL, 'staff', 'skipped', 54, 'Jean Frias', 0, '2026-09-30 11:56:07', '2026-09-30 12:06:49', NULL, NULL, 1),
(7104, 1, '2026-09-30', 38, 'MO-C038', '903d3dd28547d12071fd5ea2bd41fb1c', 3, 'Personal concerns', 'Jose Salazar', NULL, NULL, NULL, 'self', 'skipped', 56, 'Angelu', 0, '2026-09-30 12:06:02', '2026-09-30 12:11:42', NULL, NULL, 1),
(7105, 1, '2026-09-30', 39, 'MO-E039', 'b75bf1576258cc54ac40d450b397ea4e', 5, 'Financial assistance', 'Teresita Aquino', NULL, NULL, NULL, 'self', 'skipped', 55, 'Sir Henjie', 0, '2026-09-30 12:15:22', '2026-09-30 12:24:01', NULL, NULL, 1),
(7106, 1, '2026-09-30', 40, 'MO-B040', '562ed860510a2b244e98e48459cf65a5', 2, 'Business concerns', 'Danilo Fernandez', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-09-30 12:15:37', '2026-09-30 12:18:02', '2026-09-30 12:18:02', '2026-09-30 12:22:15', 1),
(7107, 1, '2026-09-30', 41, 'MO-A041', 'f123ddb9b5eb786b412606a5b51efbe0', 1, 'Mayor\'s Clearance', 'Lourdes Cruz', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-09-30 12:17:25', '2026-09-30 12:19:45', '2026-09-30 12:19:45', '2026-09-30 12:22:12', 1),
(7108, 1, '2026-09-30', 42, 'MO-B042', '26f314a5f40bf0dc2547b5b95dadfdc4', 2, 'Business concerns', 'Ramon Gonzales', NULL, NULL, NULL, 'staff', 'done', 56, 'Angelu', 0, '2026-09-30 12:25:04', '2026-09-30 12:26:34', '2026-09-30 12:26:34', '2026-09-30 12:36:49', 1),
(7109, 1, '2026-09-30', 43, 'MO-D043', '5b7db888e57f8f7783c5f8d834a42d3d', 4, 'Courtesy call', 'Josefina Mendoza', NULL, NULL, NULL, 'staff', 'done', 54, 'Jean Frias', 0, '2026-09-30 12:37:59', '2026-09-30 12:43:47', '2026-09-30 12:43:47', '2026-09-30 12:55:26', 1),
(7110, 1, '2026-09-30', 44, 'MO-A044', '8ce6a7932123c76ce56dcd670c72c87e', 1, 'Mayor\'s Clearance', 'Manuel Rivera', NULL, NULL, NULL, 'self', 'skipped', 56, 'Angelu', 0, '2026-09-30 12:39:52', '2026-09-30 12:44:40', NULL, NULL, 1),
(7111, 1, '2026-09-30', 45, 'MO-B045', '74267222698fb5772990007a83a5e075', 2, 'Business concerns', 'Ana Villanueva', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-09-30 12:40:11', '2026-09-30 12:46:35', '2026-09-30 12:46:35', '2026-09-30 12:58:54', 1),
(7112, 1, '2026-09-30', 46, 'MO-E046', '5707a0505e75e36c76a534819f56e785', 5, 'Financial assistance', 'Rogelio Lopez', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-09-30 13:01:03', '2026-09-30 13:10:08', '2026-09-30 13:10:08', '2026-09-30 13:16:55', 1),
(7113, 1, '2026-09-30', 47, 'MO-A047', 'e922cbf2a22a4af856b8efe524779bb4', 1, 'Mayor\'s Clearance', 'Grace Garcia', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-09-30 13:02:49', '2026-09-30 13:06:24', '2026-09-30 13:06:24', '2026-09-30 13:14:22', 1),
(7114, 1, '2026-09-30', 48, 'MO-F048', '41a3a686826b5dfaa5a17d50fec85e16', 6, 'Other', 'Gerardo Flores', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-09-30 13:04:12', '2026-09-30 13:11:54', '2026-09-30 13:11:54', '2026-09-30 13:16:28', 1),
(7115, 1, '2026-09-30', 49, 'MO-A049', '752453fd349d2878f094ee02e6c43133', 1, 'Mayor\'s Clearance', 'Liza Bautista', NULL, NULL, NULL, 'self', 'cancelled', NULL, NULL, 0, '2026-09-30 13:10:40', NULL, NULL, NULL, 1),
(7116, 1, '2026-09-30', 50, 'MO-C050', 'aa8217b3b04375c486dbe024a285cced', 3, 'Personal concerns', 'Antonio Torres', NULL, NULL, NULL, 'staff', 'done', 56, 'Angelu', 0, '2026-09-30 13:15:45', '2026-09-30 13:21:32', '2026-09-30 13:21:32', '2026-09-30 13:29:54', 1);
INSERT INTO `queue_tickets` (`id`, `queue_id`, `queue_day`, `number`, `code`, `access_key`, `service_id`, `service_name`, `full_name`, `mobile`, `phone_key`, `user_id`, `source`, `status`, `staff_id`, `staff_name`, `is_priority`, `created_at`, `called_at`, `started_at`, `finished_at`, `is_demo`) VALUES
(7117, 1, '2026-09-30', 51, 'MO-F051', '47dff2473b72db42a3ad28322699ac06', 6, 'Other', 'Elena Dela Cruz', NULL, NULL, NULL, 'staff', 'done', 55, 'Sir Henjie', 0, '2026-09-30 13:19:30', '2026-09-30 13:26:54', '2026-09-30 13:26:54', '2026-09-30 13:34:04', 1),
(7118, 1, '2026-09-30', 52, 'MO-F052', 'b96a6933708281ed2a1b2363b8a006b5', 6, 'Other', 'Pedro Pascual', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-09-30 13:23:58', '2026-09-30 13:41:20', '2026-09-30 13:41:20', '2026-09-30 13:47:14', 1),
(7119, 1, '2026-09-30', 53, 'MO-C053', '04ef47d67153257b2f961d4d42aa1c5b', 3, 'Personal concerns', 'Marites Reyes', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-09-30 13:28:25', '2026-09-30 13:41:51', '2026-09-30 13:41:51', '2026-09-30 13:47:02', 1),
(7120, 1, '2026-09-30', 54, 'MO-A054', '97dadc67623dde084d2aae1f6e111611', 1, 'Mayor\'s Clearance', 'Alfredo Ramos', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-09-30 13:30:52', '2026-09-30 13:35:30', '2026-09-30 13:35:30', '2026-09-30 13:38:43', 1),
(7121, 1, '2026-09-30', 55, 'MO-D055', 'b6e3b84416a4cb5f79631cd7aac5ae9d', 4, 'Courtesy call', 'Maria Santos', NULL, NULL, NULL, 'staff', 'skipped', 54, 'Jean Frias', 0, '2026-09-30 13:33:05', '2026-09-30 13:36:09', NULL, NULL, 1),
(7122, 1, '2026-09-30', 56, 'MO-E056', '9253ac18606ae4ab0b46a741ee59ef87', 5, 'Financial assistance', 'Eduardo Castillo', NULL, NULL, NULL, 'staff', 'done', 54, 'Jean Frias', 0, '2026-09-30 13:38:35', '2026-09-30 13:43:36', '2026-09-30 13:43:36', '2026-09-30 13:47:59', 1),
(7123, 1, '2026-09-30', 57, 'MO-B057', 'fe0a770ea693ab337c5aae17118be8e5', 2, 'Business concerns', 'Rosario Cortez', NULL, NULL, NULL, 'staff', 'done', 56, 'Angelu', 0, '2026-09-30 13:40:19', '2026-09-30 13:40:49', '2026-09-30 13:40:49', '2026-09-30 13:46:42', 1),
(7124, 1, '2026-09-30', 58, 'MO-B058', '5256df726cfe536d39e8b41ee87a1377', 2, 'Business concerns', 'Ricardo Domingo', NULL, NULL, NULL, 'staff', 'skipped', 55, 'Sir Henjie', 0, '2026-09-30 13:42:34', '2026-09-30 13:57:54', NULL, NULL, 1),
(7125, 1, '2026-09-30', 59, 'MO-C059', 'd6024cb1f9c1f4851c8a868c1c15da6f', 3, 'Personal concerns', 'Carmela Mercado', NULL, NULL, NULL, 'self', 'skipped', 54, 'Jean Frias', 0, '2026-09-30 13:44:57', '2026-09-30 13:46:39', NULL, NULL, 1),
(7126, 1, '2026-09-30', 60, 'MO-B060', 'fc2d7d1cbe1da3b11c286a3f8b9f88d1', 2, 'Business concerns', 'Felipe Navarro', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-09-30 13:48:29', '2026-09-30 13:51:10', '2026-09-30 13:51:10', '2026-09-30 13:57:02', 1),
(7127, 1, '2026-09-30', 61, 'MO-E061', '64fb7e8e92aafdf0610acc9c5cb83572', 5, 'Financial assistance', 'Cynthia Salazar', NULL, NULL, NULL, 'staff', 'done', 55, 'Sir Henjie', 0, '2026-09-30 13:49:58', '2026-09-30 13:50:57', '2026-09-30 13:50:57', '2026-09-30 14:02:36', 1),
(7128, 1, '2026-09-30', 62, 'MO-A062', 'abef5cbb3594e1af024cceacf676a251', 1, 'Mayor\'s Clearance', 'Jose Aquino', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-09-30 13:58:38', '2026-09-30 14:03:47', '2026-09-30 14:03:47', '2026-09-30 14:05:17', 1),
(7129, 1, '2026-09-30', 63, 'MO-B063', '4410f9d0cfbb2e610ca736bfea0e7cb4', 2, 'Business concerns', 'Teresita Fernandez', NULL, NULL, NULL, 'staff', 'done', 55, 'Sir Henjie', 0, '2026-09-30 14:13:32', '2026-09-30 14:18:30', '2026-09-30 14:18:30', '2026-09-30 14:45:42', 1),
(7130, 1, '2026-09-30', 64, 'MO-B064', '010aaba4ab4be7269a5a31ee102d6858', 2, 'Business concerns', 'Danilo Cruz', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-09-30 14:24:22', '2026-09-30 14:27:56', '2026-09-30 14:27:56', '2026-09-30 14:33:33', 1),
(7131, 1, '2026-09-30', 65, 'MO-A065', '01ae3b12dd57c089f93232878e7cde42', 1, 'Mayor\'s Clearance', 'Lourdes Gonzales', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-09-30 14:27:58', '2026-09-30 14:30:44', '2026-09-30 14:30:44', '2026-09-30 14:48:34', 1),
(7132, 1, '2026-09-30', 66, 'MO-B066', '2d912612faa86b59fcda589e7a6e5e34', 2, 'Business concerns', 'Ramon Mendoza', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-09-30 14:30:36', '2026-09-30 14:33:31', '2026-09-30 14:33:31', '2026-09-30 14:39:50', 1),
(7133, 1, '2026-09-30', 67, 'MO-B067', '4bd0c0382ef5bd9320b62990a970f619', 2, 'Business concerns', 'Josefina Rivera', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-09-30 14:31:06', '2026-09-30 14:38:42', '2026-09-30 14:38:42', '2026-09-30 15:02:17', 1),
(7134, 1, '2026-09-30', 68, 'MO-A068', '31b219d47d67b0baa70252dc7aadc66a', 1, 'Mayor\'s Clearance', 'Manuel Villanueva', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-09-30 14:39:40', '2026-09-30 14:43:40', '2026-09-30 14:43:40', '2026-09-30 14:48:58', 1),
(7135, 1, '2026-09-30', 69, 'MO-F069', 'b627318fa98bf51f18752544030b6e6f', 6, 'Other', 'Ana Lopez', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-09-30 14:47:16', '2026-09-30 15:01:02', '2026-09-30 15:01:02', '2026-09-30 15:06:26', 1),
(7136, 1, '2026-09-30', 70, 'MO-C070', 'e9ecfcd2f3c79050b421a85cd902164b', 3, 'Personal concerns', 'Rogelio Garcia', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-09-30 14:50:04', '2026-09-30 14:55:32', '2026-09-30 14:55:32', '2026-09-30 15:07:25', 1),
(7137, 1, '2026-09-30', 71, 'MO-C071', '3a394b2039acb495ecb7293e3c5d6114', 3, 'Personal concerns', 'Grace Flores', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-09-30 14:52:03', '2026-09-30 14:58:18', '2026-09-30 14:58:18', '2026-09-30 15:06:31', 1),
(7138, 1, '2026-09-30', 72, 'MO-D072', '1001990b9192a7f1a022668fd8991eed', 4, 'Courtesy call', 'Gerardo Bautista', NULL, NULL, NULL, 'staff', 'done', 55, 'Sir Henjie', 0, '2026-09-30 15:01:43', '2026-09-30 15:02:46', '2026-09-30 15:02:46', '2026-09-30 15:22:02', 1),
(7139, 1, '2026-09-30', 73, 'MO-A073', '1a1ae689af1a5efd57196935bfb743ce', 1, 'Mayor\'s Clearance', 'Liza Torres', NULL, NULL, NULL, 'self', 'skipped', 54, 'Jean Frias', 0, '2026-09-30 15:09:53', '2026-09-30 15:11:39', NULL, NULL, 1),
(7140, 1, '2026-09-30', 74, 'MO-D074', '2b85ad1ecf80aedf57905e89cc57c5f8', 4, 'Courtesy call', 'Antonio Dela Cruz', NULL, NULL, NULL, 'staff', 'done', 56, 'Angelu', 0, '2026-09-30 15:18:25', '2026-09-30 15:23:23', '2026-09-30 15:23:23', '2026-09-30 15:44:00', 1),
(7141, 1, '2026-09-30', 75, 'MO-B075', 'b194ef40c6942dc56df3a1faff1653aa', 2, 'Business concerns', 'Elena Pascual', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-09-30 15:25:32', '2026-09-30 15:28:31', '2026-09-30 15:28:31', '2026-09-30 15:40:23', 1),
(7142, 1, '2026-09-30', 76, 'MO-A076', 'eb814fdb5c6acaa4e6bd31e6df8ade24', 1, 'Mayor\'s Clearance', 'Pedro Reyes', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-09-30 15:37:37', '2026-09-30 15:38:42', '2026-09-30 15:38:42', '2026-09-30 15:50:05', 1),
(7143, 1, '2026-09-30', 77, 'MO-A077', '33f2fc3f153d59c438483b00ae57e0d0', 1, 'Mayor\'s Clearance', 'Marites Ramos', NULL, NULL, NULL, 'staff', 'done', 55, 'Sir Henjie', 0, '2026-09-30 15:41:37', '2026-09-30 15:43:46', '2026-09-30 15:43:46', '2026-09-30 15:49:14', 1),
(7144, 1, '2026-09-30', 78, 'MO-A078', '8213716d1bf7cd1355a0c92431856701', 1, 'Mayor\'s Clearance', 'Alfredo Santos', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-09-30 15:47:20', '2026-09-30 15:53:51', '2026-09-30 15:53:51', '2026-09-30 16:01:00', 1),
(7145, 1, '2026-09-30', 79, 'MO-C079', '07d8c1ed91774c752f2e7916b73d81c2', 3, 'Personal concerns', 'Maria Castillo', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-09-30 15:49:26', '2026-09-30 15:50:35', '2026-09-30 15:50:35', '2026-09-30 15:56:58', 1),
(7146, 1, '2026-09-30', 80, 'MO-C080', 'a00adc140b3c531c1482450d6295ba6d', 3, 'Personal concerns', 'Eduardo Cortez', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-09-30 15:51:36', '2026-09-30 15:52:30', '2026-09-30 15:52:30', '2026-09-30 16:11:05', 1),
(7147, 1, '2026-09-30', 81, 'MO-B081', 'bf6ec3923936a27d3f1e2d8bd19465a9', 2, 'Business concerns', 'Rosario Domingo', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-09-30 16:13:54', '2026-09-30 16:14:24', '2026-09-30 16:14:24', '2026-09-30 16:26:08', 1),
(7148, 1, '2026-10-01', 1, 'MO-A001', '40d0d845beb0815ac674e9e2ad13cf32', 1, 'Mayor\'s Clearance', 'Jose Cruz', NULL, NULL, NULL, 'staff', 'done', 56, 'Angelu', 0, '2026-10-01 08:15:13', '2026-10-01 08:18:57', '2026-10-01 08:18:57', '2026-10-01 08:21:20', 1),
(7149, 1, '2026-10-01', 2, 'MO-A002', 'c6fa8fdcc8a1596dcd6e1df46d66cb9e', 1, 'Mayor\'s Clearance', 'Teresita Gonzales', NULL, NULL, NULL, 'staff', 'done', 54, 'Jean Frias', 0, '2026-10-01 08:28:28', '2026-10-01 08:31:20', '2026-10-01 08:31:20', '2026-10-01 08:33:04', 1),
(7150, 1, '2026-10-01', 3, 'MO-D003', 'd515b1efb3e61e73f78eb604a9508549', 4, 'Courtesy call', 'Danilo Mendoza', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-01 08:31:01', '2026-10-01 08:43:26', '2026-10-01 08:43:26', '2026-10-01 08:53:18', 1),
(7151, 1, '2026-10-01', 4, 'MO-E004', '3688bd591d83f3b0a1768b2743d378b5', 5, 'Financial assistance', 'Lourdes Rivera', NULL, NULL, NULL, 'staff', 'done', 55, 'Sir Henjie', 0, '2026-10-01 08:37:40', '2026-10-01 08:41:47', '2026-10-01 08:41:47', '2026-10-01 08:43:44', 1),
(7152, 1, '2026-10-01', 5, 'MO-A005', '9fc1551a4da595737db158dbc4f06260', 1, 'Mayor\'s Clearance', 'Ramon Villanueva', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-01 08:41:02', '2026-10-01 08:45:45', '2026-10-01 08:45:45', '2026-10-01 08:50:42', 1),
(7153, 1, '2026-10-01', 6, 'MO-A006', '3469816c86b2be27eba6b52604df8cda', 1, 'Mayor\'s Clearance', 'Josefina Lopez', NULL, NULL, NULL, 'staff', 'done', 56, 'Angelu', 0, '2026-10-01 08:55:57', '2026-10-01 09:01:26', '2026-10-01 09:01:26', '2026-10-01 09:04:37', 1),
(7154, 1, '2026-10-01', 7, 'MO-F007', '0db51252601b930425dd313bdce28040', 6, 'Other', 'Manuel Garcia', NULL, NULL, NULL, 'staff', 'done', 54, 'Jean Frias', 0, '2026-10-01 09:20:07', '2026-10-01 09:40:36', '2026-10-01 09:40:36', '2026-10-01 09:55:51', 1),
(7155, 1, '2026-10-01', 8, 'MO-A008', '64fdf572d191e45c6ba81af63125e7c1', 1, 'Mayor\'s Clearance', 'Ana Flores', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-01 09:34:46', '2026-10-01 09:41:45', '2026-10-01 09:41:45', '2026-10-01 09:45:13', 1),
(7156, 1, '2026-10-01', 9, 'MO-E009', 'e13310994f90e4734cc8f88268d3e087', 5, 'Financial assistance', 'Rogelio Bautista', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-01 09:38:30', '2026-10-01 09:44:12', '2026-10-01 09:44:12', '2026-10-01 09:52:03', 1),
(7157, 1, '2026-10-01', 10, 'MO-E010', '67df3a04689cabc6ddb4500c97b18f8c', 5, 'Financial assistance', 'Grace Torres', NULL, NULL, NULL, 'staff', 'done', 55, 'Sir Henjie', 0, '2026-10-01 09:38:58', '2026-10-01 09:42:54', '2026-10-01 09:42:54', '2026-10-01 09:50:08', 1),
(7158, 1, '2026-10-01', 11, 'MO-F011', '5531224e85ef11fbe76bf9f954d058db', 6, 'Other', 'Gerardo Dela Cruz', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-01 09:39:12', '2026-10-01 09:43:45', '2026-10-01 09:43:45', '2026-10-01 09:49:16', 1),
(7159, 1, '2026-10-01', 12, 'MO-B012', '6fb0dd7698abc16f3aec6f5178ae8eac', 2, 'Business concerns', 'Liza Pascual', NULL, NULL, NULL, 'staff', 'done', 55, 'Sir Henjie', 0, '2026-10-01 09:48:25', '2026-10-01 09:52:28', '2026-10-01 09:52:28', '2026-10-01 10:03:15', 1),
(7160, 1, '2026-10-01', 13, 'MO-F013', '9c74dd59cdfa5723c5c50a99a0c3a033', 6, 'Other', 'Antonio Reyes', NULL, NULL, NULL, 'self', 'cancelled', NULL, NULL, 0, '2026-10-01 09:50:51', NULL, NULL, NULL, 1),
(7161, 1, '2026-10-01', 14, 'MO-B014', 'cdf431be4035ff78c5172b9f278b8293', 2, 'Business concerns', 'Elena Ramos', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-01 09:55:29', '2026-10-01 10:09:15', '2026-10-01 10:09:15', '2026-10-01 10:20:14', 1),
(7162, 1, '2026-10-01', 15, 'MO-E015', '055a0b9c0b76951418ffd21ae2f5fb3c', 5, 'Financial assistance', 'Pedro Santos', NULL, NULL, NULL, 'staff', 'done', 55, 'Sir Henjie', 0, '2026-10-01 10:07:44', '2026-10-01 10:16:10', '2026-10-01 10:16:10', '2026-10-01 10:32:35', 1),
(7163, 1, '2026-10-01', 16, 'MO-A016', 'b0916bdc5d4cff03e7b095a0818063ae', 1, 'Mayor\'s Clearance', 'Marites Castillo', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-01 10:14:34', '2026-10-01 10:20:14', '2026-10-01 10:20:14', '2026-10-01 10:29:06', 1),
(7164, 1, '2026-10-01', 17, 'MO-C017', 'b11f38e6bc68649f52bebd26370c269a', 3, 'Personal concerns', 'Alfredo Cortez', NULL, NULL, NULL, 'staff', 'cancelled', NULL, NULL, 0, '2026-10-01 10:24:37', NULL, NULL, NULL, 1),
(7165, 1, '2026-10-01', 18, 'MO-E018', '742da498c12a57cce128816d5ba03571', 5, 'Financial assistance', 'Maria Domingo', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-01 10:34:26', '2026-10-01 10:39:38', '2026-10-01 10:39:38', '2026-10-01 10:48:10', 1),
(7166, 1, '2026-10-01', 19, 'MO-B019', '826c6c1667dc440edb6a9a003f4b7f72', 2, 'Business concerns', 'Eduardo Mercado', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-01 10:35:28', '2026-10-01 10:49:12', '2026-10-01 10:49:12', '2026-10-01 10:59:56', 1),
(7167, 1, '2026-10-01', 20, 'MO-A020', '545a89f38daecf739b6216744ee60bd8', 1, 'Mayor\'s Clearance', 'Rosario Navarro', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-01 10:42:46', '2026-10-01 10:48:56', '2026-10-01 10:48:56', '2026-10-01 11:08:00', 1),
(7168, 1, '2026-10-01', 21, 'MO-A021', '95e6d6d013d40324df7742e253c0bfaf', 1, 'Mayor\'s Clearance', 'Ricardo Salazar', NULL, NULL, NULL, 'staff', 'done', 54, 'Jean Frias', 0, '2026-10-01 10:44:39', '2026-10-01 10:49:09', '2026-10-01 10:49:09', '2026-10-01 10:54:41', 1),
(7169, 1, '2026-10-01', 22, 'MO-C022', 'c34ccb81775ef5a8310bb9f4c4d7ee60', 3, 'Personal concerns', 'Carmela Aquino', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-01 10:49:51', '2026-10-01 11:04:21', '2026-10-01 11:04:21', '2026-10-01 11:05:59', 1),
(7170, 1, '2026-10-01', 23, 'MO-E023', 'aba8538e76429cb6c85b40075556c0db', 5, 'Financial assistance', 'Felipe Fernandez', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-01 10:50:27', '2026-10-01 10:59:21', '2026-10-01 10:59:21', '2026-10-01 11:01:54', 1),
(7171, 1, '2026-10-01', 24, 'MO-E024', '258cc28e69101a4a8d4ede4118bc9d39', 5, 'Financial assistance', 'Cynthia Cruz', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-01 10:51:33', '2026-10-01 10:55:30', '2026-10-01 10:55:30', '2026-10-01 11:04:46', 1),
(7172, 1, '2026-10-01', 25, 'MO-B025', '5638312242451f52efb57e0c9cd74368', 2, 'Business concerns', 'Jose Gonzales', NULL, NULL, NULL, 'staff', 'cancelled', NULL, NULL, 0, '2026-10-01 10:53:31', NULL, NULL, NULL, 1),
(7173, 1, '2026-10-01', 26, 'MO-F026', 'ffa38acc5e3cb27d23214efec98187d8', 6, 'Other', 'Teresita Mendoza', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-01 10:54:31', '2026-10-01 11:00:16', '2026-10-01 11:00:16', '2026-10-01 11:03:46', 1),
(7174, 1, '2026-10-01', 27, 'MO-A027', '02ab9ac780ad455e1908a0ca412e9da5', 1, 'Mayor\'s Clearance', 'Danilo Rivera', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-01 10:57:40', '2026-10-01 11:12:44', '2026-10-01 11:12:44', '2026-10-01 11:16:00', 1),
(7175, 1, '2026-10-01', 28, 'MO-E028', '8a1784b08435f904c6978729f3b2d07f', 5, 'Financial assistance', 'Lourdes Villanueva', NULL, NULL, NULL, 'staff', 'done', 56, 'Angelu', 0, '2026-10-01 11:06:44', '2026-10-01 11:08:02', '2026-10-01 11:08:02', '2026-10-01 11:19:37', 1),
(7176, 1, '2026-10-01', 29, 'MO-D029', '69ef129381b6ed3a9cdc4e4e35ff0d2c', 4, 'Courtesy call', 'Ramon Lopez', NULL, NULL, NULL, 'staff', 'done', 54, 'Jean Frias', 0, '2026-10-01 11:07:35', '2026-10-01 11:16:15', '2026-10-01 11:16:15', '2026-10-01 11:30:47', 1),
(7177, 1, '2026-10-01', 30, 'MO-E030', '5e9828b671f312b843fab736a7a90e6e', 5, 'Financial assistance', 'Josefina Garcia', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-01 11:08:22', '2026-10-01 11:29:14', '2026-10-01 11:29:14', '2026-10-01 11:38:59', 1),
(7178, 1, '2026-10-01', 31, 'MO-A031', '4a9b0d2555f9c7a8cc88ec8dff7ab87d', 1, 'Mayor\'s Clearance', 'Manuel Flores', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-01 11:13:51', '2026-10-01 11:21:43', '2026-10-01 11:21:43', '2026-10-01 11:26:00', 1),
(7179, 1, '2026-10-01', 32, 'MO-A032', '3a2403374183a0fec0d21b3291646e4a', 1, 'Mayor\'s Clearance', 'Ana Bautista', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-01 11:14:42', '2026-10-01 11:19:32', '2026-10-01 11:19:32', '2026-10-01 11:27:30', 1),
(7180, 1, '2026-10-01', 33, 'MO-B033', '8256cc71d5c7318e1fb2941076788ec3', 2, 'Business concerns', 'Rogelio Torres', NULL, NULL, NULL, 'staff', 'done', 54, 'Jean Frias', 0, '2026-10-01 11:20:06', '2026-10-01 11:21:52', '2026-10-01 11:21:52', '2026-10-01 11:37:44', 1),
(7181, 1, '2026-10-01', 34, 'MO-A034', 'e95eae5e02c27d75c013945c9e3723ee', 1, 'Mayor\'s Clearance', 'Grace Dela Cruz', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-01 11:29:26', '2026-10-01 11:33:47', '2026-10-01 11:33:47', '2026-10-01 11:38:48', 1),
(7182, 1, '2026-10-01', 35, 'MO-A035', 'ad580ea4223615adaf5815635482195d', 1, 'Mayor\'s Clearance', 'Gerardo Pascual', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-01 11:31:23', '2026-10-01 11:34:28', '2026-10-01 11:34:28', '2026-10-01 11:36:15', 1),
(7183, 1, '2026-10-01', 36, 'MO-E036', '4ca0f275f76a46753314c58c34dc76f9', 5, 'Financial assistance', 'Liza Reyes', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-01 11:48:44', '2026-10-01 11:51:54', '2026-10-01 11:51:54', '2026-10-01 11:58:55', 1),
(7184, 1, '2026-10-01', 37, 'MO-B037', 'f31f599da3ee3ba0890d83fa94ed87af', 2, 'Business concerns', 'Antonio Ramos', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-01 12:06:43', '2026-10-01 12:13:22', '2026-10-01 12:13:22', '2026-10-01 12:28:35', 1),
(7185, 1, '2026-10-01', 38, 'MO-F038', 'ab8a16c2010bc4794fd90fbdf66cea76', 6, 'Other', 'Elena Santos', NULL, NULL, NULL, 'self', 'skipped', 54, 'Jean Frias', 0, '2026-10-01 12:18:33', '2026-10-01 12:22:49', NULL, NULL, 1),
(7186, 1, '2026-10-01', 39, 'MO-E039', '502295996847115f1b7aad05096ececd', 5, 'Financial assistance', 'Pedro Castillo', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-01 12:52:28', '2026-10-01 12:57:01', '2026-10-01 12:57:01', '2026-10-01 13:01:07', 1),
(7187, 1, '2026-10-01', 40, 'MO-B040', 'b407f9fc8a5f2ad7fe8bbc6eb0e4cc14', 2, 'Business concerns', 'Marites Cortez', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-01 12:54:33', '2026-10-01 12:59:07', '2026-10-01 12:59:07', '2026-10-01 13:03:45', 1),
(7188, 1, '2026-10-01', 41, 'MO-F041', 'ba76d56c00e161a9bef70fb8160d56b7', 6, 'Other', 'Alfredo Domingo', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-01 13:01:10', '2026-10-01 13:13:48', '2026-10-01 13:13:48', '2026-10-01 13:21:10', 1),
(7189, 1, '2026-10-01', 42, 'MO-F042', 'dc7525c1a5c887a603877f28212945fa', 6, 'Other', 'Maria Mercado', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-01 13:02:36', '2026-10-01 13:16:21', '2026-10-01 13:16:21', '2026-10-01 13:20:21', 1),
(7190, 1, '2026-10-01', 43, 'MO-A043', '8464353076d19112fa419196a9df0101', 1, 'Mayor\'s Clearance', 'Eduardo Navarro', NULL, NULL, NULL, 'staff', 'done', 55, 'Sir Henjie', 0, '2026-10-01 13:03:51', '2026-10-01 13:09:42', '2026-10-01 13:09:42', '2026-10-01 13:16:31', 1),
(7191, 1, '2026-10-01', 44, 'MO-A044', 'c6c778bf634bb3afaa1673fbdbb65980', 1, 'Mayor\'s Clearance', 'Rosario Salazar', NULL, NULL, NULL, 'self', 'cancelled', NULL, NULL, 0, '2026-10-01 13:07:11', NULL, NULL, NULL, 1),
(7192, 1, '2026-10-01', 45, 'MO-F045', 'ece608a51fa31d1deac9c06a3f46dad3', 6, 'Other', 'Ricardo Aquino', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-01 13:12:33', '2026-10-01 13:16:23', '2026-10-01 13:16:23', '2026-10-01 13:22:11', 1),
(7193, 1, '2026-10-01', 46, 'MO-F046', 'f9dec34089d80f78fa2251556535d637', 6, 'Other', 'Carmela Fernandez', NULL, NULL, NULL, 'staff', 'done', 56, 'Angelu', 0, '2026-10-01 13:16:29', '2026-10-01 13:24:28', '2026-10-01 13:24:28', '2026-10-01 13:25:58', 1),
(7194, 1, '2026-10-01', 47, 'MO-E047', '621c647932af7793f37ef73d4020ab46', 5, 'Financial assistance', 'Felipe Cruz', NULL, NULL, NULL, 'self', 'cancelled', NULL, NULL, 0, '2026-10-01 13:17:00', NULL, NULL, NULL, 1),
(7195, 1, '2026-10-01', 48, 'MO-B048', '297f6417ebb6e224d6df1481257f5942', 2, 'Business concerns', 'Cynthia Gonzales', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-01 13:18:51', '2026-10-01 13:27:31', '2026-10-01 13:27:31', '2026-10-01 13:30:46', 1),
(7196, 1, '2026-10-01', 49, 'MO-F049', '39e827d240662354eb246ca7d5081ba0', 6, 'Other', 'Jose Mendoza', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-01 13:19:50', '2026-10-01 13:23:01', '2026-10-01 13:23:01', '2026-10-01 13:26:29', 1),
(7197, 1, '2026-10-01', 50, 'MO-F050', 'ffddeebc9de009c122c7fd95ae975424', 6, 'Other', 'Teresita Rivera', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-01 13:21:20', '2026-10-01 13:34:22', '2026-10-01 13:34:22', '2026-10-01 13:40:09', 1),
(7198, 1, '2026-10-01', 51, 'MO-B051', 'bd25022d8484dc0f6b2aa18089e8e1e7', 2, 'Business concerns', 'Danilo Villanueva', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-01 13:42:46', '2026-10-01 13:46:15', '2026-10-01 13:46:15', '2026-10-01 13:53:52', 1),
(7199, 1, '2026-10-01', 52, 'MO-F052', '1da92c82d647ce2634a19c810106151e', 6, 'Other', 'Lourdes Lopez', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-01 13:51:32', '2026-10-01 14:00:06', '2026-10-01 14:00:06', '2026-10-01 14:07:45', 1),
(7200, 1, '2026-10-01', 53, 'MO-B053', 'd19295c116bfcb055f4e3e3b76bf950b', 2, 'Business concerns', 'Ramon Garcia', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-01 13:53:49', '2026-10-01 14:10:12', '2026-10-01 14:10:12', '2026-10-01 14:26:01', 1),
(7201, 1, '2026-10-01', 54, 'MO-E054', 'd1392917bc6548a9cabf18898f7f2499', 5, 'Financial assistance', 'Josefina Flores', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-01 13:58:55', '2026-10-01 14:08:33', '2026-10-01 14:08:33', '2026-10-01 14:11:15', 1),
(7202, 1, '2026-10-01', 55, 'MO-B055', '09863bcdd4456aababf151dd974972d1', 2, 'Business concerns', 'Manuel Bautista', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-01 14:06:01', '2026-10-01 14:08:29', '2026-10-01 14:08:29', '2026-10-01 14:15:24', 1),
(7203, 1, '2026-10-01', 56, 'MO-E056', '4bb96fe536d3d670f5cfab71bf397e0d', 5, 'Financial assistance', 'Ana Torres', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-01 14:24:04', '2026-10-01 14:26:56', '2026-10-01 14:26:56', '2026-10-01 14:34:24', 1),
(7204, 1, '2026-10-01', 57, 'MO-A057', 'c6a36fc334a1e445e2a04ddc88480086', 1, 'Mayor\'s Clearance', 'Rogelio Dela Cruz', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-01 14:27:01', '2026-10-01 14:33:11', '2026-10-01 14:33:11', '2026-10-01 14:36:40', 1),
(7205, 1, '2026-10-01', 58, 'MO-C058', 'b7e51e7edbac39d774ce4c839f782eee', 3, 'Personal concerns', 'Grace Pascual', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-01 14:36:34', '2026-10-01 14:42:05', '2026-10-01 14:42:05', '2026-10-01 14:48:20', 1),
(7206, 1, '2026-10-01', 59, 'MO-C059', '2169fa42564e272ed569a2150a95c8d8', 3, 'Personal concerns', 'Gerardo Reyes', NULL, NULL, NULL, 'self', 'skipped', 54, 'Jean Frias', 0, '2026-10-01 15:00:52', '2026-10-01 15:03:32', NULL, NULL, 1),
(7207, 1, '2026-10-01', 60, 'MO-A060', 'aab25408a82b3277795291f6c604eecc', 1, 'Mayor\'s Clearance', 'Liza Ramos', NULL, NULL, NULL, 'self', 'cancelled', NULL, NULL, 0, '2026-10-01 15:01:45', NULL, NULL, NULL, 1),
(7208, 1, '2026-10-01', 61, 'MO-D061', '72b892f1038029e495c9177a6684df93', 4, 'Courtesy call', 'Antonio Santos', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-01 15:04:52', '2026-10-01 15:14:27', '2026-10-01 15:14:27', '2026-10-01 15:21:20', 1),
(7209, 1, '2026-10-01', 62, 'MO-C062', 'e8f4d37c62f3f7925330687b69bdc619', 3, 'Personal concerns', 'Elena Castillo', NULL, NULL, NULL, 'self', 'skipped', 56, 'Angelu', 0, '2026-10-01 15:20:41', '2026-10-01 15:23:08', NULL, NULL, 1),
(7210, 1, '2026-10-01', 63, 'MO-D063', 'a7170df0355840378a7ae47d4173c427', 4, 'Courtesy call', 'Pedro Cortez', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-01 15:40:19', '2026-10-01 15:44:06', '2026-10-01 15:44:06', '2026-10-01 15:55:30', 1),
(7211, 1, '2026-10-01', 64, 'MO-C064', 'f5d1d46fd065025b3b0cea008a42e68b', 3, 'Personal concerns', 'Marites Domingo', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-01 16:24:55', '2026-10-01 16:26:20', '2026-10-01 16:26:20', '2026-10-01 16:31:24', 1),
(7212, 1, '2026-10-01', 65, 'MO-A065', '601c3fae9334211fdca64024a1f94f32', 1, 'Mayor\'s Clearance', 'Alfredo Mercado', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-01 16:41:07', '2026-10-01 16:43:47', '2026-10-01 16:43:47', '2026-10-01 16:56:45', 1),
(7213, 1, '2026-10-01', 66, 'MO-F066', 'cb09e601e31fa8e634e551d4e74c586f', 6, 'Other', 'Maria Navarro', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-01 16:41:42', '2026-10-01 16:44:59', '2026-10-01 16:44:59', '2026-10-01 16:51:50', 1),
(7214, 1, '2026-10-01', 67, 'MO-A067', 'f52b00aa870820a139525c8f6067720c', 1, 'Mayor\'s Clearance', 'Eduardo Salazar', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-01 16:59:55', '2026-10-01 17:03:12', '2026-10-01 17:03:12', '2026-10-01 17:07:45', 1),
(7215, 1, '2026-10-02', 1, 'MO-B001', '7925d1d6f5f72950f61e2bfd48b11195', 2, 'Business concerns', 'Ana Cruz', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-02 08:02:06', '2026-10-02 08:06:58', '2026-10-02 08:06:58', '2026-10-02 08:16:23', 1),
(7216, 1, '2026-10-02', 2, 'MO-E002', '2f3a525b087767fdb0d1e67d1fde4fcc', 5, 'Financial assistance', 'Rogelio Gonzales', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-02 08:05:34', '2026-10-02 08:07:37', '2026-10-02 08:07:37', '2026-10-02 08:16:50', 1),
(7217, 1, '2026-10-02', 3, 'MO-E003', 'f39cc8922d900135a8db0074ee44b136', 5, 'Financial assistance', 'Grace Mendoza', NULL, NULL, NULL, 'staff', 'done', 56, 'Angelu', 0, '2026-10-02 08:11:51', '2026-10-02 08:17:14', '2026-10-02 08:17:14', '2026-10-02 08:20:07', 1),
(7218, 1, '2026-10-02', 4, 'MO-B004', '257087b8b7f5b75072a2134a3d91535a', 2, 'Business concerns', 'Gerardo Rivera', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-02 08:44:22', '2026-10-02 08:46:09', '2026-10-02 08:46:09', '2026-10-02 08:50:29', 1),
(7219, 1, '2026-10-02', 5, 'MO-C005', 'fef822b1549403662a7bfcee43ce050b', 3, 'Personal concerns', 'Liza Villanueva', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-02 08:47:06', '2026-10-02 08:49:24', '2026-10-02 08:49:24', '2026-10-02 08:53:23', 1),
(7220, 1, '2026-10-02', 6, 'MO-A006', '5273fa8f6212a387717798afe285c066', 1, 'Mayor\'s Clearance', 'Antonio Lopez', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-02 08:47:49', '2026-10-02 08:56:07', '2026-10-02 08:56:07', '2026-10-02 08:58:20', 1),
(7221, 1, '2026-10-02', 7, 'MO-A007', '1b94e337e8c7af5f28c22ddfd8a19e22', 1, 'Mayor\'s Clearance', 'Elena Garcia', NULL, NULL, NULL, 'staff', 'done', 55, 'Sir Henjie', 0, '2026-10-02 09:32:09', '2026-10-02 09:38:12', '2026-10-02 09:38:12', '2026-10-02 09:44:27', 1),
(7222, 1, '2026-10-02', 8, 'MO-C008', '0f4320d148fce158830c4c4351111937', 3, 'Personal concerns', 'Pedro Flores', NULL, NULL, NULL, 'staff', 'done', 55, 'Sir Henjie', 0, '2026-10-02 09:33:32', '2026-10-02 09:37:22', '2026-10-02 09:37:22', '2026-10-02 09:42:52', 1),
(7223, 1, '2026-10-02', 9, 'MO-B009', '93e29d921c7bbb0175e6156177799790', 2, 'Business concerns', 'Marites Bautista', NULL, NULL, NULL, 'self', 'skipped', 56, 'Angelu', 0, '2026-10-02 09:33:56', '2026-10-02 09:43:09', NULL, NULL, 1),
(7224, 1, '2026-10-02', 10, 'MO-A010', '75a4c1a8a58a320d318e14ff4ae5d31d', 1, 'Mayor\'s Clearance', 'Alfredo Torres', NULL, NULL, NULL, 'staff', 'done', 56, 'Angelu', 0, '2026-10-02 09:35:02', '2026-10-02 09:45:09', '2026-10-02 09:45:09', '2026-10-02 09:50:59', 1),
(7225, 1, '2026-10-02', 11, 'MO-A011', '228de0c513da66b5d0680c8a193e1ba9', 1, 'Mayor\'s Clearance', 'Maria Dela Cruz', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-02 09:41:29', '2026-10-02 09:54:18', '2026-10-02 09:54:18', '2026-10-02 10:02:07', 1),
(7226, 1, '2026-10-02', 12, 'MO-A012', '3c1a5fde055515ec120aa4b6736c3935', 1, 'Mayor\'s Clearance', 'Eduardo Pascual', NULL, NULL, NULL, 'staff', 'done', 56, 'Angelu', 0, '2026-10-02 09:50:05', '2026-10-02 09:52:28', '2026-10-02 09:52:28', '2026-10-02 09:55:12', 1),
(7227, 1, '2026-10-02', 13, 'MO-E013', 'f850321e806becf508270e59d2848077', 5, 'Financial assistance', 'Rosario Reyes', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-02 09:57:25', '2026-10-02 10:04:41', '2026-10-02 10:04:41', '2026-10-02 10:15:03', 1),
(7228, 1, '2026-10-02', 14, 'MO-F014', 'abc10be05d31e51a9da6de3abdc42b9d', 6, 'Other', 'Ricardo Ramos', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-02 10:03:30', '2026-10-02 10:18:55', '2026-10-02 10:18:55', '2026-10-02 10:22:57', 1),
(7229, 1, '2026-10-02', 15, 'MO-E015', '1aa95f8faff7574f9c3196090c021146', 5, 'Financial assistance', 'Carmela Santos', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-02 10:04:45', '2026-10-02 10:08:02', '2026-10-02 10:08:02', '2026-10-02 10:13:32', 1),
(7230, 1, '2026-10-02', 16, 'MO-B016', 'c9e123986c2d4a98feeed16e1e306bd1', 2, 'Business concerns', 'Felipe Castillo', NULL, NULL, NULL, 'self', 'skipped', 56, 'Angelu', 0, '2026-10-02 10:07:07', '2026-10-02 10:18:23', NULL, NULL, 1),
(7231, 1, '2026-10-02', 17, 'MO-A017', 'b7b837b0a9ab89e6ffaaf961558c97e7', 1, 'Mayor\'s Clearance', 'Cynthia Cortez', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-02 10:08:25', '2026-10-02 10:11:52', '2026-10-02 10:11:52', '2026-10-02 10:15:49', 1),
(7232, 1, '2026-10-02', 18, 'MO-E018', '6cbeed8744727bb2bf6bebc4c573a887', 5, 'Financial assistance', 'Jose Domingo', NULL, NULL, NULL, 'staff', 'done', 56, 'Angelu', 0, '2026-10-02 10:15:45', '2026-10-02 10:16:22', '2026-10-02 10:16:22', '2026-10-02 10:21:24', 1),
(7233, 1, '2026-10-02', 19, 'MO-A019', '398c2667761b9db7f7d77e45ffee11c0', 1, 'Mayor\'s Clearance', 'Teresita Mercado', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-02 10:27:26', '2026-10-02 10:34:14', '2026-10-02 10:34:14', '2026-10-02 10:49:31', 1),
(7234, 1, '2026-10-02', 20, 'MO-D020', 'd527b2774dccd822cb5878a981006bf9', 4, 'Courtesy call', 'Danilo Navarro', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-02 10:30:45', '2026-10-02 10:45:56', '2026-10-02 10:45:56', '2026-10-02 11:11:39', 1),
(7235, 1, '2026-10-02', 21, 'MO-E021', '505cbdbbf7caa3f70a1b3cc0c1a178b1', 5, 'Financial assistance', 'Lourdes Salazar', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-02 10:32:03', '2026-10-02 10:37:32', '2026-10-02 10:37:32', '2026-10-02 10:47:37', 1),
(7236, 1, '2026-10-02', 22, 'MO-A022', '3ad00a322c85d334cba7ecb1cdb268a9', 1, 'Mayor\'s Clearance', 'Ramon Aquino', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-02 10:35:50', '2026-10-02 10:39:17', '2026-10-02 10:39:17', '2026-10-02 10:45:56', 1),
(7237, 1, '2026-10-02', 23, 'MO-E023', '0ded263001681dccbf708e04b98e86f0', 5, 'Financial assistance', 'Josefina Fernandez', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-02 10:40:00', '2026-10-02 10:40:30', '2026-10-02 10:40:30', '2026-10-02 10:42:36', 1),
(7238, 1, '2026-10-02', 24, 'MO-B024', 'b724161d3dc7c0ba8a59a5f968351635', 2, 'Business concerns', 'Manuel Cruz', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-02 10:41:56', '2026-10-02 10:44:37', '2026-10-02 10:44:37', '2026-10-02 10:49:44', 1),
(7239, 1, '2026-10-02', 25, 'MO-A025', '80cb424ac9754c7506409d9f6a91536e', 1, 'Mayor\'s Clearance', 'Ana Gonzales', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-02 10:44:57', '2026-10-02 11:15:02', '2026-10-02 11:15:02', '2026-10-02 11:19:51', 1),
(7240, 1, '2026-10-02', 26, 'MO-A026', '4bae998bccfefdbdd3d1367fc1e1fb08', 1, 'Mayor\'s Clearance', 'Rogelio Mendoza', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-02 10:55:29', '2026-10-02 11:05:27', '2026-10-02 11:05:27', '2026-10-02 11:17:56', 1),
(7241, 1, '2026-10-02', 27, 'MO-A027', '1d73c20b8c90e749bec0b862307e2645', 1, 'Mayor\'s Clearance', 'Grace Rivera', NULL, NULL, NULL, 'staff', 'done', 56, 'Angelu', 0, '2026-10-02 10:55:33', '2026-10-02 11:03:20', '2026-10-02 11:03:20', '2026-10-02 11:05:19', 1),
(7242, 1, '2026-10-02', 28, 'MO-E028', '58408f6a68e0e679071a4833d2b65779', 5, 'Financial assistance', 'Gerardo Villanueva', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-02 10:57:46', '2026-10-02 11:04:52', '2026-10-02 11:04:52', '2026-10-02 11:14:59', 1),
(7243, 1, '2026-10-02', 29, 'MO-A029', 'c3160ac466cb5c5aeb8a60c576fe690a', 1, 'Mayor\'s Clearance', 'Liza Lopez', NULL, NULL, NULL, 'self', 'skipped', 56, 'Angelu', 0, '2026-10-02 11:04:03', '2026-10-02 11:10:42', NULL, NULL, 1),
(7244, 1, '2026-10-02', 30, 'MO-C030', '5df5a1906cd5912d7151858dc3b71c8a', 3, 'Personal concerns', 'Antonio Garcia', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-02 11:05:09', '2026-10-02 11:08:31', '2026-10-02 11:08:31', '2026-10-02 11:13:32', 1),
(7245, 1, '2026-10-02', 31, 'MO-F031', '96dfe88914d862fcb68af104974ec8fb', 6, 'Other', 'Elena Flores', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-02 11:17:25', '2026-10-02 11:18:58', '2026-10-02 11:18:58', '2026-10-02 11:26:51', 1),
(7246, 1, '2026-10-02', 32, 'MO-C032', '453b7b473cde4c0f4461676cf819cdf5', 3, 'Personal concerns', 'Pedro Bautista', NULL, NULL, NULL, 'staff', 'skipped', 54, 'Jean Frias', 0, '2026-10-02 11:28:17', '2026-10-02 11:30:20', NULL, NULL, 1),
(7247, 1, '2026-10-02', 33, 'MO-E033', '0ce7dd71ee4583a24d7cfb3d64204d3f', 5, 'Financial assistance', 'Marites Torres', NULL, NULL, NULL, 'self', 'skipped', 54, 'Jean Frias', 0, '2026-10-02 11:32:58', '2026-10-02 11:38:58', NULL, NULL, 1),
(7248, 1, '2026-10-02', 34, 'MO-A034', '444a1c42485c9e93f52abd238079431e', 1, 'Mayor\'s Clearance', 'Alfredo Dela Cruz', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-02 11:40:07', '2026-10-02 11:41:39', '2026-10-02 11:41:39', '2026-10-02 11:46:14', 1),
(7249, 1, '2026-10-02', 35, 'MO-D035', '00e2ecb4b31e88be893151fe7e3c2679', 4, 'Courtesy call', 'Maria Pascual', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-02 12:13:59', '2026-10-02 12:15:30', '2026-10-02 12:15:30', '2026-10-02 12:30:57', 1),
(7250, 1, '2026-10-02', 36, 'MO-E036', '9543b01aa4d4b8da824278807f1559d3', 5, 'Financial assistance', 'Eduardo Reyes', NULL, NULL, NULL, 'staff', 'done', 56, 'Angelu', 0, '2026-10-02 12:49:19', '2026-10-02 12:50:40', '2026-10-02 12:50:40', '2026-10-02 13:06:29', 1),
(7251, 1, '2026-10-02', 37, 'MO-B037', 'a4df4ed2b86dac06e6fff5b0cbf1e6f6', 2, 'Business concerns', 'Rosario Ramos', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-02 12:57:46', '2026-10-02 13:03:00', '2026-10-02 13:03:00', '2026-10-02 13:36:31', 1),
(7252, 1, '2026-10-02', 38, 'MO-C038', 'dbad9b9a64bd8e2e12399496e15b6724', 3, 'Personal concerns', 'Ricardo Santos', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-02 13:07:56', '2026-10-02 13:22:24', '2026-10-02 13:22:24', '2026-10-02 13:45:54', 1),
(7253, 1, '2026-10-02', 39, 'MO-F039', '81c62c68b25c970fae9fed04bc4d8b85', 6, 'Other', 'Carmela Castillo', NULL, NULL, NULL, 'self', 'cancelled', NULL, NULL, 0, '2026-10-02 13:20:09', NULL, NULL, NULL, 1),
(7254, 1, '2026-10-02', 40, 'MO-A040', 'c766b78739f70f35696daa315dc85a15', 1, 'Mayor\'s Clearance', 'Felipe Cortez', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-02 13:22:01', '2026-10-02 13:23:05', '2026-10-02 13:23:05', '2026-10-02 13:31:09', 1),
(7255, 1, '2026-10-02', 41, 'MO-F041', 'e348c37ff88523ab547619df956a10b2', 6, 'Other', 'Cynthia Domingo', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-02 13:22:38', '2026-10-02 13:28:10', '2026-10-02 13:28:10', '2026-10-02 13:33:15', 1),
(7256, 1, '2026-10-02', 42, 'MO-A042', '025c1f6d512f873a5b1111aace5f1b96', 1, 'Mayor\'s Clearance', 'Jose Mercado', NULL, NULL, NULL, 'self', 'skipped', 55, 'Sir Henjie', 0, '2026-10-02 13:36:09', '2026-10-02 13:54:02', NULL, NULL, 1),
(7257, 1, '2026-10-02', 43, 'MO-B043', 'a322c980934322980b7275ae51269133', 2, 'Business concerns', 'Teresita Navarro', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-02 13:39:33', '2026-10-02 13:47:54', '2026-10-02 13:47:54', '2026-10-02 13:56:08', 1),
(7258, 1, '2026-10-02', 44, 'MO-C044', 'bd33837f631e60bb45769e2cc68bf2de', 3, 'Personal concerns', 'Danilo Salazar', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-02 13:49:09', '2026-10-02 13:52:08', '2026-10-02 13:52:08', '2026-10-02 14:04:38', 1),
(7259, 1, '2026-10-02', 45, 'MO-D045', '64d026d36d9a23a3c9f98a7fd6f435e7', 4, 'Courtesy call', 'Lourdes Aquino', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-02 13:56:47', '2026-10-02 14:01:47', '2026-10-02 14:01:47', '2026-10-02 14:21:18', 1),
(7260, 1, '2026-10-02', 46, 'MO-C046', 'ff1f91c930caeb3333d064b817098436', 3, 'Personal concerns', 'Ramon Fernandez', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-02 13:57:34', '2026-10-02 14:05:49', '2026-10-02 14:05:49', '2026-10-02 14:08:11', 1),
(7261, 1, '2026-10-02', 47, 'MO-A047', 'a092fb3f1e35de2a72a2c19c7440be19', 1, 'Mayor\'s Clearance', 'Josefina Cruz', NULL, NULL, NULL, 'self', 'cancelled', NULL, NULL, 0, '2026-10-02 14:00:06', NULL, NULL, NULL, 1),
(7262, 1, '2026-10-02', 48, 'MO-D048', 'd3035c3e48252bf8f1784f7036a5ddaf', 4, 'Courtesy call', 'Manuel Gonzales', NULL, NULL, NULL, 'self', 'skipped', 56, 'Angelu', 0, '2026-10-02 14:03:41', '2026-10-02 14:05:18', NULL, NULL, 1),
(7263, 1, '2026-10-02', 49, 'MO-A049', '55d3d9664e0671d51627f0012d00e52c', 1, 'Mayor\'s Clearance', 'Ana Mendoza', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-02 14:04:04', '2026-10-02 14:05:29', '2026-10-02 14:05:29', '2026-10-02 14:08:05', 1),
(7264, 1, '2026-10-02', 50, 'MO-B050', 'd160eead4fc16f4f878fcfb883705ffd', 2, 'Business concerns', 'Rogelio Rivera', NULL, NULL, NULL, 'staff', 'done', 55, 'Sir Henjie', 0, '2026-10-02 14:11:03', '2026-10-02 14:14:14', '2026-10-02 14:14:14', '2026-10-02 14:27:31', 1),
(7265, 1, '2026-10-02', 51, 'MO-E051', '36d01c89283e00765338c7f2043ef8a2', 5, 'Financial assistance', 'Grace Villanueva', NULL, NULL, NULL, 'self', 'skipped', 54, 'Jean Frias', 0, '2026-10-02 14:14:44', '2026-10-02 14:17:22', NULL, NULL, 1),
(7266, 1, '2026-10-02', 52, 'MO-A052', '5329eb497f48714cb18d0d3d5f73a780', 1, 'Mayor\'s Clearance', 'Gerardo Lopez', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-02 14:20:06', '2026-10-02 14:36:50', '2026-10-02 14:36:50', '2026-10-02 14:43:33', 1),
(7267, 1, '2026-10-02', 53, 'MO-A053', '1e1e77023c130e87b11717279572e9f2', 1, 'Mayor\'s Clearance', 'Liza Garcia', NULL, NULL, NULL, 'staff', 'done', 56, 'Angelu', 0, '2026-10-02 14:23:23', '2026-10-02 14:29:02', '2026-10-02 14:29:02', '2026-10-02 14:30:54', 1),
(7268, 1, '2026-10-02', 54, 'MO-B054', 'a17e787b60779087789feeab5af7b309', 2, 'Business concerns', 'Antonio Flores', NULL, NULL, NULL, 'self', 'skipped', 56, 'Angelu', 0, '2026-10-02 14:33:02', '2026-10-02 14:37:51', NULL, NULL, 1),
(7269, 1, '2026-10-02', 55, 'MO-E055', 'b76e7d19b865e892466e1da760e594eb', 5, 'Financial assistance', 'Elena Bautista', NULL, NULL, NULL, 'staff', 'done', 56, 'Angelu', 0, '2026-10-02 14:33:04', '2026-10-02 14:39:22', '2026-10-02 14:39:22', '2026-10-02 14:43:36', 1),
(7270, 1, '2026-10-02', 56, 'MO-D056', '845b5528f776516fda51401a42ab2c92', 4, 'Courtesy call', 'Pedro Torres', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-02 14:42:59', '2026-10-02 14:45:38', '2026-10-02 14:45:38', '2026-10-02 14:56:11', 1),
(7271, 1, '2026-10-02', 57, 'MO-E057', '9b675064714acc00c2a8c0f999a577b7', 5, 'Financial assistance', 'Marites Dela Cruz', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-02 14:52:14', '2026-10-02 15:03:13', '2026-10-02 15:03:13', '2026-10-02 15:09:06', 1),
(7272, 1, '2026-10-02', 58, 'MO-B058', '473ef7589cee092db267edbbaabe5b40', 2, 'Business concerns', 'Alfredo Pascual', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-02 14:57:39', '2026-10-02 14:59:54', '2026-10-02 14:59:54', '2026-10-02 15:08:56', 1),
(7273, 1, '2026-10-02', 59, 'MO-B059', 'bce810410699788379bbff444a73780b', 2, 'Business concerns', 'Maria Reyes', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-02 15:12:26', '2026-10-02 15:13:26', '2026-10-02 15:13:26', '2026-10-02 15:32:49', 1),
(7274, 1, '2026-10-02', 60, 'MO-B060', 'f209a501f827de15db594baca5e14944', 2, 'Business concerns', 'Eduardo Ramos', NULL, NULL, NULL, 'staff', 'skipped', 54, 'Jean Frias', 0, '2026-10-02 15:19:15', '2026-10-02 15:21:26', NULL, NULL, 1),
(7275, 1, '2026-10-02', 61, 'MO-A061', '2e821fb471eb811c5d66b7928f2d7ea0', 1, 'Mayor\'s Clearance', 'Rosario Santos', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-02 15:38:37', '2026-10-02 15:41:56', '2026-10-02 15:41:56', '2026-10-02 15:45:09', 1),
(7276, 1, '2026-10-02', 62, 'MO-E062', '556fd09f5b48f9db1b6d4d9d71c52559', 5, 'Financial assistance', 'Ricardo Castillo', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-02 15:39:19', '2026-10-02 15:45:21', '2026-10-02 15:45:21', '2026-10-02 15:49:36', 1),
(7277, 1, '2026-10-02', 63, 'MO-E063', 'af3747817f7c57c4bfa9b19e09dc7169', 5, 'Financial assistance', 'Carmela Cortez', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-02 15:49:04', '2026-10-02 15:58:17', '2026-10-02 15:58:17', '2026-10-02 16:06:15', 1),
(7278, 1, '2026-10-02', 64, 'MO-A064', '13be2508475999daed4988f8fe214ba4', 1, 'Mayor\'s Clearance', 'Felipe Domingo', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-02 16:18:23', '2026-10-02 16:18:53', '2026-10-02 16:18:53', '2026-10-02 16:26:35', 1),
(7279, 1, '2026-10-02', 65, 'MO-B065', '17a2e2fafe92ff85681622b41c42932a', 2, 'Business concerns', 'Cynthia Mercado', NULL, NULL, NULL, 'self', 'skipped', 54, 'Jean Frias', 0, '2026-10-02 16:25:10', '2026-10-02 16:27:08', NULL, NULL, 1),
(7280, 1, '2026-10-02', 66, 'MO-A066', 'a143f8c988b8ab2d8f6e5446b425e7aa', 1, 'Mayor\'s Clearance', 'Jose Navarro', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-02 16:56:01', '2026-10-02 16:58:12', '2026-10-02 16:58:12', '2026-10-02 17:10:00', 1),
(7281, 1, '2026-10-05', 1, 'MO-A001', '6da36677c509f54649ea2c6205e2a38e', 1, 'Mayor\'s Clearance', 'Ramon Cruz', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-05 08:13:34', '2026-10-05 08:15:31', '2026-10-05 08:15:31', '2026-10-05 08:19:34', 1),
(7282, 1, '2026-10-05', 2, 'MO-B002', 'df2d9c32c9846cc3f51e817c940b0215', 2, 'Business concerns', 'Josefina Gonzales', NULL, NULL, NULL, 'self', 'skipped', 56, 'Angelu', 0, '2026-10-05 08:23:49', '2026-10-05 08:27:44', NULL, NULL, 1),
(7283, 1, '2026-10-05', 3, 'MO-B003', '1ee718018af985d5af9d1975a952588b', 2, 'Business concerns', 'Manuel Mendoza', NULL, NULL, NULL, 'staff', 'done', 56, 'Angelu', 0, '2026-10-05 08:25:26', '2026-10-05 08:34:08', '2026-10-05 08:34:08', '2026-10-05 08:57:36', 1),
(7284, 1, '2026-10-05', 4, 'MO-E004', 'b92bfc2651242f8fa02e1f43de31259e', 5, 'Financial assistance', 'Ana Rivera', NULL, NULL, NULL, 'self', 'cancelled', NULL, NULL, 0, '2026-10-05 08:26:12', NULL, NULL, NULL, 1),
(7285, 1, '2026-10-05', 5, 'MO-E005', '0c3f24475861e1d1d3c5d1f957e816e9', 5, 'Financial assistance', 'Rogelio Villanueva', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-05 08:34:20', '2026-10-05 08:34:50', '2026-10-05 08:34:50', '2026-10-05 08:45:27', 1),
(7286, 1, '2026-10-05', 6, 'MO-B006', '5a7046348a44600d66db646270580fdd', 2, 'Business concerns', 'Grace Lopez', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-05 08:40:25', '2026-10-05 08:42:29', '2026-10-05 08:42:29', '2026-10-05 08:55:51', 1),
(7287, 1, '2026-10-05', 7, 'MO-E007', '2f7323e5b8361b9c7363d4f3f6dbe868', 5, 'Financial assistance', 'Gerardo Garcia', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-05 08:40:36', '2026-10-05 08:47:16', '2026-10-05 08:47:16', '2026-10-05 08:52:13', 1),
(7288, 1, '2026-10-05', 8, 'MO-E008', '21bf71886d67571e91655b7fbde653f5', 5, 'Financial assistance', 'Liza Flores', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-05 08:44:15', '2026-10-05 08:54:12', '2026-10-05 08:54:12', '2026-10-05 09:04:36', 1),
(7289, 1, '2026-10-05', 9, 'MO-A009', '5520311bdc03c5e824faebd1928b8fc0', 1, 'Mayor\'s Clearance', 'Antonio Bautista', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-05 08:45:53', '2026-10-05 08:51:52', '2026-10-05 08:51:52', '2026-10-05 08:55:40', 1),
(7290, 1, '2026-10-05', 10, 'MO-E010', 'd6fc0796299421c2292bc20383e16072', 5, 'Financial assistance', 'Elena Torres', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-05 08:51:04', '2026-10-05 09:00:13', '2026-10-05 09:00:13', '2026-10-05 09:21:30', 1),
(7291, 1, '2026-10-05', 11, 'MO-C011', 'c4aac685dc038b5a0fc941612031a2e5', 3, 'Personal concerns', 'Pedro Dela Cruz', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-05 08:59:39', '2026-10-05 09:02:07', '2026-10-05 09:02:07', '2026-10-05 09:07:32', 1),
(7292, 1, '2026-10-05', 12, 'MO-A012', 'c88534f186b12d843af84ff784d06c06', 1, 'Mayor\'s Clearance', 'Marites Pascual', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-05 09:03:51', '2026-10-05 09:04:59', '2026-10-05 09:04:59', '2026-10-05 09:08:11', 1),
(7293, 1, '2026-10-05', 13, 'MO-A013', '3130467433131ec708e576f340ce8019', 1, 'Mayor\'s Clearance', 'Alfredo Reyes', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-05 09:05:10', '2026-10-05 09:19:47', '2026-10-05 09:19:47', '2026-10-05 09:26:37', 1),
(7294, 1, '2026-10-05', 14, 'MO-B014', 'c87c7047d3018003d6443fc5f769fb14', 2, 'Business concerns', 'Maria Ramos', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-05 09:10:18', '2026-10-05 09:13:52', '2026-10-05 09:13:52', '2026-10-05 09:21:53', 1),
(7295, 1, '2026-10-05', 15, 'MO-E015', '3d5d7fc58bd58012ff661a2d8830de1b', 5, 'Financial assistance', 'Eduardo Santos', NULL, NULL, NULL, 'self', 'cancelled', NULL, NULL, 0, '2026-10-05 09:21:28', NULL, NULL, NULL, 1),
(7296, 1, '2026-10-05', 16, 'MO-E016', 'e24525b46f9fdf5db475849355d54e1a', 5, 'Financial assistance', 'Rosario Castillo', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-05 09:24:09', '2026-10-05 09:26:41', '2026-10-05 09:26:41', '2026-10-05 09:32:41', 1),
(7297, 1, '2026-10-05', 17, 'MO-A017', '6c3f179fb2a6442e254618c95e0f8dec', 1, 'Mayor\'s Clearance', 'Ricardo Cortez', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-05 09:28:20', '2026-10-05 09:32:07', '2026-10-05 09:32:07', '2026-10-05 09:35:32', 1),
(7298, 1, '2026-10-05', 18, 'MO-E018', '5fbc4315615f6c1ee9e20202bb8b7591', 5, 'Financial assistance', 'Carmela Domingo', NULL, NULL, NULL, 'self', 'skipped', 56, 'Angelu', 0, '2026-10-05 09:38:51', '2026-10-05 09:44:21', NULL, NULL, 1),
(7299, 1, '2026-10-05', 19, 'MO-C019', '165408e32fab7de1b128a2b72e12e95b', 3, 'Personal concerns', 'Felipe Mercado', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-05 09:43:12', '2026-10-05 09:50:18', '2026-10-05 09:50:18', '2026-10-05 09:53:40', 1),
(7300, 1, '2026-10-05', 20, 'MO-F020', 'b5637aabf4003b1d5eb5ef8624d47c9c', 6, 'Other', 'Cynthia Navarro', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-05 09:44:41', '2026-10-05 09:47:36', '2026-10-05 09:47:36', '2026-10-05 09:59:46', 1),
(7301, 1, '2026-10-05', 21, 'MO-B021', 'fbe84c5a382b29f6b1ea753fb8b4bdc7', 2, 'Business concerns', 'Jose Salazar', NULL, NULL, NULL, 'staff', 'done', 54, 'Jean Frias', 0, '2026-10-05 09:49:04', '2026-10-05 10:02:09', '2026-10-05 10:02:09', '2026-10-05 10:14:20', 1),
(7302, 1, '2026-10-05', 22, 'MO-C022', '68657697e0d85fb229225d98e2ffcceb', 3, 'Personal concerns', 'Teresita Aquino', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-05 09:49:20', '2026-10-05 10:13:38', '2026-10-05 10:13:38', '2026-10-05 10:20:17', 1),
(7303, 1, '2026-10-05', 23, 'MO-F023', '8eb85e3faab443d20b6628f8f3b7eaf2', 6, 'Other', 'Danilo Fernandez', NULL, NULL, NULL, 'self', 'cancelled', NULL, NULL, 0, '2026-10-05 09:50:14', NULL, NULL, NULL, 1),
(7304, 1, '2026-10-05', 24, 'MO-E024', '4abeb633069a0811400127237fb8d3bd', 5, 'Financial assistance', 'Lourdes Cruz', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-05 09:52:06', '2026-10-05 10:14:13', '2026-10-05 10:14:13', '2026-10-05 10:23:43', 1),
(7305, 1, '2026-10-05', 25, 'MO-A025', '93cac6867cc4596272c3117a73d6e808', 1, 'Mayor\'s Clearance', 'Ramon Gonzales', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-05 09:53:35', '2026-10-05 09:56:16', '2026-10-05 09:56:16', '2026-10-05 10:01:29', 1),
(7306, 1, '2026-10-05', 26, 'MO-A026', 'faf86e807cf85bd92b887e50921caf28', 1, 'Mayor\'s Clearance', 'Josefina Mendoza', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-05 09:55:02', '2026-10-05 10:16:08', '2026-10-05 10:16:08', '2026-10-05 10:25:03', 1),
(7307, 1, '2026-10-05', 27, 'MO-B027', '489c08548db18ff833672f6cdeb18229', 2, 'Business concerns', 'Manuel Rivera', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-05 09:57:02', '2026-10-05 10:01:32', '2026-10-05 10:01:32', '2026-10-05 10:12:28', 1),
(7308, 1, '2026-10-05', 28, 'MO-E028', '4c94ed947b458da148c062e4bf63b127', 5, 'Financial assistance', 'Ana Villanueva', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-05 10:12:06', '2026-10-05 10:25:09', '2026-10-05 10:25:09', '2026-10-05 10:27:56', 1);
INSERT INTO `queue_tickets` (`id`, `queue_id`, `queue_day`, `number`, `code`, `access_key`, `service_id`, `service_name`, `full_name`, `mobile`, `phone_key`, `user_id`, `source`, `status`, `staff_id`, `staff_name`, `is_priority`, `created_at`, `called_at`, `started_at`, `finished_at`, `is_demo`) VALUES
(7309, 1, '2026-10-05', 29, 'MO-B029', '13907c0df62742d3c10eec03a47f7a7f', 2, 'Business concerns', 'Rogelio Lopez', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-05 10:19:06', '2026-10-05 10:37:36', '2026-10-05 10:37:36', '2026-10-05 10:51:59', 1),
(7310, 1, '2026-10-05', 30, 'MO-E030', '388d86564a7cf1aeacbe574f8a60abfe', 5, 'Financial assistance', 'Grace Garcia', NULL, NULL, NULL, 'staff', 'skipped', 55, 'Sir Henjie', 0, '2026-10-05 10:24:59', '2026-10-05 10:29:12', NULL, NULL, 1),
(7311, 1, '2026-10-05', 31, 'MO-A031', '49ffaed3d4b832f30dc0062cb3eb0291', 1, 'Mayor\'s Clearance', 'Gerardo Flores', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-05 10:28:15', '2026-10-05 10:31:55', '2026-10-05 10:31:55', '2026-10-05 10:36:50', 1),
(7312, 1, '2026-10-05', 32, 'MO-E032', '816e2c02d8d18f0c543705eb2c148e0f', 5, 'Financial assistance', 'Liza Bautista', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-05 10:30:27', '2026-10-05 10:33:28', '2026-10-05 10:33:28', '2026-10-05 10:45:01', 1),
(7313, 1, '2026-10-05', 33, 'MO-A033', '311d4847ba7c4fb91f388a1279b7e91f', 1, 'Mayor\'s Clearance', 'Antonio Torres', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-05 10:35:32', '2026-10-05 10:43:36', '2026-10-05 10:43:36', '2026-10-05 10:45:09', 1),
(7314, 1, '2026-10-05', 34, 'MO-B034', 'e7f71e80ad651957ee492a6d50d092e6', 2, 'Business concerns', 'Elena Dela Cruz', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-05 10:35:45', '2026-10-05 10:48:04', '2026-10-05 10:48:04', '2026-10-05 10:54:51', 1),
(7315, 1, '2026-10-05', 35, 'MO-C035', '85aeb08d847e9fa729c023ea7977e358', 3, 'Personal concerns', 'Pedro Pascual', NULL, NULL, NULL, 'staff', 'done', 54, 'Jean Frias', 0, '2026-10-05 10:35:47', '2026-10-05 10:40:45', '2026-10-05 10:40:45', '2026-10-05 10:48:27', 1),
(7316, 1, '2026-10-05', 36, 'MO-F036', 'efcd1c42198b121404d2046aeb88050b', 6, 'Other', 'Marites Reyes', NULL, NULL, NULL, 'staff', 'done', 54, 'Jean Frias', 0, '2026-10-05 10:35:57', '2026-10-05 10:48:17', '2026-10-05 10:48:17', '2026-10-05 10:50:46', 1),
(7317, 1, '2026-10-05', 37, 'MO-E037', '209d4843e6dd3e8861e325886d596ba5', 5, 'Financial assistance', 'Alfredo Ramos', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-05 10:40:30', '2026-10-05 10:43:49', '2026-10-05 10:43:49', '2026-10-05 10:49:10', 1),
(7318, 1, '2026-10-05', 38, 'MO-A038', 'dc7e4104a263cb604ca1cc139b205334', 1, 'Mayor\'s Clearance', 'Maria Santos', NULL, NULL, NULL, 'staff', 'done', 55, 'Sir Henjie', 0, '2026-10-05 10:40:42', '2026-10-05 10:55:21', '2026-10-05 10:55:21', '2026-10-05 11:15:27', 1),
(7319, 1, '2026-10-05', 39, 'MO-C039', '288fe8143d0ee58c90249aa7ece5db85', 3, 'Personal concerns', 'Eduardo Castillo', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-05 10:46:26', '2026-10-05 10:48:28', '2026-10-05 10:48:28', '2026-10-05 11:00:05', 1),
(7320, 1, '2026-10-05', 40, 'MO-D040', '3849c8aba6c53a4356e411c48eaf0f89', 4, 'Courtesy call', 'Rosario Cortez', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-05 10:50:58', '2026-10-05 10:57:49', '2026-10-05 10:57:49', '2026-10-05 11:16:03', 1),
(7321, 1, '2026-10-05', 41, 'MO-B041', 'b2f8738d3ac1217c53abf2570e41803f', 2, 'Business concerns', 'Ricardo Domingo', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-05 10:57:22', '2026-10-05 11:01:19', '2026-10-05 11:01:19', '2026-10-05 11:07:58', 1),
(7322, 1, '2026-10-05', 42, 'MO-C042', 'eff1c4d2ab3efb021aed99691de48437', 3, 'Personal concerns', 'Carmela Mercado', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-05 10:57:37', '2026-10-05 11:01:22', '2026-10-05 11:01:22', '2026-10-05 11:25:14', 1),
(7323, 1, '2026-10-05', 43, 'MO-C043', '822c5b7fecab950ddbbb99d77ae0d909', 3, 'Personal concerns', 'Felipe Navarro', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-05 10:58:33', '2026-10-05 11:05:41', '2026-10-05 11:05:41', '2026-10-05 11:09:10', 1),
(7324, 1, '2026-10-05', 44, 'MO-E044', 'b4710e7054171ca5606ac6c19465c670', 5, 'Financial assistance', 'Cynthia Salazar', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-05 10:59:41', '2026-10-05 11:09:50', '2026-10-05 11:09:50', '2026-10-05 11:22:22', 1),
(7325, 1, '2026-10-05', 45, 'MO-D045', '08b399d844c566d38aba024deb20784d', 4, 'Courtesy call', 'Jose Aquino', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-05 11:19:07', '2026-10-05 11:27:06', '2026-10-05 11:27:06', '2026-10-05 11:48:39', 1),
(7326, 1, '2026-10-05', 46, 'MO-A046', '932a550d754a7cc60cc51b39e8c4c839', 1, 'Mayor\'s Clearance', 'Teresita Fernandez', NULL, NULL, NULL, 'staff', 'done', 55, 'Sir Henjie', 0, '2026-10-05 11:24:16', '2026-10-05 11:31:07', '2026-10-05 11:31:07', '2026-10-05 11:37:35', 1),
(7327, 1, '2026-10-05', 47, 'MO-E047', 'd43487a9a3188cf1d61e3bf6952146f6', 5, 'Financial assistance', 'Danilo Cruz', NULL, NULL, NULL, 'staff', 'done', 56, 'Angelu', 0, '2026-10-05 11:43:01', '2026-10-05 11:48:35', '2026-10-05 11:48:35', '2026-10-05 11:59:19', 1),
(7328, 1, '2026-10-05', 48, 'MO-C048', 'de3280e1645b739a7926faacfbdf77ac', 3, 'Personal concerns', 'Lourdes Gonzales', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-05 11:47:23', '2026-10-05 11:55:52', '2026-10-05 11:55:52', '2026-10-05 12:17:06', 1),
(7329, 1, '2026-10-05', 49, 'MO-E049', '130aeb994d33561230e03970d982131c', 5, 'Financial assistance', 'Ramon Mendoza', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-05 11:50:11', '2026-10-05 11:57:41', '2026-10-05 11:57:41', '2026-10-05 12:03:45', 1),
(7330, 1, '2026-10-05', 50, 'MO-B050', '067f22e12bc14f53a134169805fc8519', 2, 'Business concerns', 'Josefina Rivera', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-05 11:52:57', '2026-10-05 11:54:19', '2026-10-05 11:54:19', '2026-10-05 11:56:12', 1),
(7331, 1, '2026-10-05', 51, 'MO-E051', '1e3fce391380555a7ddd86f1220edeaf', 5, 'Financial assistance', 'Manuel Villanueva', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-05 11:54:22', '2026-10-05 11:58:05', '2026-10-05 11:58:05', '2026-10-05 12:02:32', 1),
(7332, 1, '2026-10-05', 52, 'MO-D052', 'e63e1ebd2fefd37086d0adbeb9235754', 4, 'Courtesy call', 'Ana Lopez', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-05 11:58:12', '2026-10-05 12:10:11', '2026-10-05 12:10:11', '2026-10-05 12:20:16', 1),
(7333, 1, '2026-10-05', 53, 'MO-A053', 'c0b996eff38dea52aea98ba1de035704', 1, 'Mayor\'s Clearance', 'Rogelio Garcia', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-05 11:59:23', '2026-10-05 12:04:22', '2026-10-05 12:04:22', '2026-10-05 12:18:41', 1),
(7334, 1, '2026-10-05', 54, 'MO-E054', 'f192d2338c803ff2cbebe44d0376b08f', 5, 'Financial assistance', 'Grace Flores', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-05 12:16:05', '2026-10-05 12:19:07', '2026-10-05 12:19:07', '2026-10-05 12:27:58', 1),
(7335, 1, '2026-10-05', 55, 'MO-A055', 'c681dfa0b1a69d314425ab78850a9a36', 1, 'Mayor\'s Clearance', 'Gerardo Bautista', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-05 12:17:04', '2026-10-05 12:21:10', '2026-10-05 12:21:10', '2026-10-05 12:32:05', 1),
(7336, 1, '2026-10-05', 56, 'MO-E056', '0ee2321968d716e55b6b036af9d05cc4', 5, 'Financial assistance', 'Liza Torres', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-05 12:35:30', '2026-10-05 12:37:53', '2026-10-05 12:37:53', '2026-10-05 12:55:05', 1),
(7337, 1, '2026-10-05', 57, 'MO-A057', '96c6fa4926837748deebca4dc081df2f', 1, 'Mayor\'s Clearance', 'Antonio Dela Cruz', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-05 12:37:47', '2026-10-05 12:38:17', '2026-10-05 12:38:17', '2026-10-05 12:42:14', 1),
(7338, 1, '2026-10-05', 58, 'MO-B058', '23dcdb8d1c71a2b920aa3cfc6848a40f', 2, 'Business concerns', 'Elena Pascual', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-05 12:44:31', '2026-10-05 12:53:47', '2026-10-05 12:53:47', '2026-10-05 13:09:05', 1),
(7339, 1, '2026-10-05', 59, 'MO-A059', 'be6f9f163772461918250ddb6c75e0a2', 1, 'Mayor\'s Clearance', 'Pedro Reyes', NULL, NULL, NULL, 'staff', 'done', 55, 'Sir Henjie', 0, '2026-10-05 12:57:40', '2026-10-05 12:59:48', '2026-10-05 12:59:48', '2026-10-05 13:07:34', 1),
(7340, 1, '2026-10-05', 60, 'MO-D060', '87862752f716c99ef696f31b59e6003b', 4, 'Courtesy call', 'Marites Ramos', NULL, NULL, NULL, 'staff', 'done', 54, 'Jean Frias', 0, '2026-10-05 12:58:15', '2026-10-05 13:01:15', '2026-10-05 13:01:15', '2026-10-05 13:17:26', 1),
(7341, 1, '2026-10-05', 61, 'MO-E061', '595dd82a5fb168a2f4fb890dfc9a7005', 5, 'Financial assistance', 'Alfredo Santos', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-05 13:01:32', '2026-10-05 13:15:03', '2026-10-05 13:15:03', '2026-10-05 13:20:10', 1),
(7342, 1, '2026-10-05', 62, 'MO-A062', '9d4175823a6d450dd9dd31a4309442b9', 1, 'Mayor\'s Clearance', 'Maria Castillo', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-05 13:09:22', '2026-10-05 13:14:59', '2026-10-05 13:14:59', '2026-10-05 13:19:32', 1),
(7343, 1, '2026-10-05', 63, 'MO-E063', '21fbb1b00912d5f62d362ce101004dd6', 5, 'Financial assistance', 'Eduardo Cortez', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-05 13:14:44', '2026-10-05 13:17:15', '2026-10-05 13:17:15', '2026-10-05 13:24:30', 1),
(7344, 1, '2026-10-05', 64, 'MO-B064', 'ac65271145537a91e1938f232c033f8b', 2, 'Business concerns', 'Rosario Domingo', NULL, NULL, NULL, 'staff', 'done', 55, 'Sir Henjie', 0, '2026-10-05 13:19:26', '2026-10-05 13:25:35', '2026-10-05 13:25:35', '2026-10-05 13:34:39', 1),
(7345, 1, '2026-10-05', 65, 'MO-B065', '3e0e312289a7e71355a3a5086fc04546', 2, 'Business concerns', 'Ricardo Mercado', NULL, NULL, NULL, 'self', 'skipped', 54, 'Jean Frias', 0, '2026-10-05 13:22:53', '2026-10-05 13:24:32', NULL, NULL, 1),
(7346, 1, '2026-10-05', 66, 'MO-A066', '486206ec4a32b8f831bf85ba41f00750', 1, 'Mayor\'s Clearance', 'Carmela Navarro', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-05 13:28:10', '2026-10-05 13:29:32', '2026-10-05 13:29:32', '2026-10-05 13:33:33', 1),
(7347, 1, '2026-10-05', 67, 'MO-C067', '6dfc24b1eb8f1b4d6ec8277d49a4e7ee', 3, 'Personal concerns', 'Felipe Salazar', NULL, NULL, NULL, 'self', 'skipped', 56, 'Angelu', 0, '2026-10-05 13:29:43', '2026-10-05 13:34:08', NULL, NULL, 1),
(7348, 1, '2026-10-05', 68, 'MO-A068', 'a16bbd96b58602fe1cdc79560b243b93', 1, 'Mayor\'s Clearance', 'Cynthia Aquino', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-05 13:34:41', '2026-10-05 13:44:08', '2026-10-05 13:44:08', '2026-10-05 13:51:22', 1),
(7349, 1, '2026-10-05', 69, 'MO-B069', 'dc756cedcb772b9531ac89ed831b57d8', 2, 'Business concerns', 'Jose Fernandez', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-05 13:35:48', '2026-10-05 13:38:48', '2026-10-05 13:38:48', '2026-10-05 14:03:21', 1),
(7350, 1, '2026-10-05', 70, 'MO-B070', 'f82a29199da94bbd1795c1bda3b7e367', 2, 'Business concerns', 'Teresita Cruz', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-05 13:55:03', '2026-10-05 13:55:34', '2026-10-05 13:55:34', '2026-10-05 14:08:08', 1),
(7351, 1, '2026-10-05', 71, 'MO-B071', 'a9c801135504e2d3c309be70eb736c7d', 2, 'Business concerns', 'Danilo Gonzales', NULL, NULL, NULL, 'self', 'skipped', 55, 'Sir Henjie', 0, '2026-10-05 13:56:00', '2026-10-05 14:00:22', NULL, NULL, 1),
(7352, 1, '2026-10-05', 72, 'MO-A072', '08e0af62b2465d1adc323b8a72d76856', 1, 'Mayor\'s Clearance', 'Lourdes Mendoza', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-05 14:04:58', '2026-10-05 14:09:07', '2026-10-05 14:09:07', '2026-10-05 14:13:03', 1),
(7353, 1, '2026-10-05', 73, 'MO-B073', '4d62810a9b1edbabbbd203fcdb29f664', 2, 'Business concerns', 'Ramon Rivera', NULL, NULL, NULL, 'staff', 'done', 56, 'Angelu', 0, '2026-10-05 14:08:14', '2026-10-05 14:08:45', '2026-10-05 14:08:45', '2026-10-05 14:14:45', 1),
(7354, 1, '2026-10-05', 74, 'MO-C074', 'bb042846d6efa76bda640e77e42b509f', 3, 'Personal concerns', 'Josefina Villanueva', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-05 14:12:11', '2026-10-05 14:16:28', '2026-10-05 14:16:28', '2026-10-05 14:26:10', 1),
(7355, 1, '2026-10-05', 75, 'MO-F075', '803ae2fb13069103391d5239adf33d1d', 6, 'Other', 'Manuel Lopez', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-05 14:12:43', '2026-10-05 14:15:20', '2026-10-05 14:15:20', '2026-10-05 14:18:08', 1),
(7356, 1, '2026-10-05', 76, 'MO-E076', '3fe0adf8ab73b75f6a0d545fea6c37c6', 5, 'Financial assistance', 'Ana Garcia', NULL, NULL, NULL, 'staff', 'done', 56, 'Angelu', 0, '2026-10-05 14:13:37', '2026-10-05 14:16:38', '2026-10-05 14:16:38', '2026-10-05 14:18:08', 1),
(7357, 1, '2026-10-05', 77, 'MO-A077', 'e3dfd72fa5f0e96127ffd959760a29b1', 1, 'Mayor\'s Clearance', 'Rogelio Flores', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-05 14:16:36', '2026-10-05 14:30:28', '2026-10-05 14:30:28', '2026-10-05 14:38:00', 1),
(7358, 1, '2026-10-05', 78, 'MO-A078', '061a17677a5251fb24e14b61cbeda8b8', 1, 'Mayor\'s Clearance', 'Grace Bautista', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-05 14:27:30', '2026-10-05 14:32:06', '2026-10-05 14:32:06', '2026-10-05 14:51:03', 1),
(7359, 1, '2026-10-05', 79, 'MO-B079', '1f95523c1a45346d470627fdd9f1863a', 2, 'Business concerns', 'Gerardo Torres', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-05 14:37:03', '2026-10-05 14:39:09', '2026-10-05 14:39:09', '2026-10-05 14:42:22', 1),
(7360, 1, '2026-10-05', 80, 'MO-B080', 'e09871b1e7b3c48cd4786915ef073ca0', 2, 'Business concerns', 'Liza Dela Cruz', NULL, NULL, NULL, 'self', 'skipped', 54, 'Jean Frias', 0, '2026-10-05 14:47:36', '2026-10-05 14:51:11', NULL, NULL, 1),
(7361, 1, '2026-10-05', 81, 'MO-A081', '7077ad086e28fd0a6cfbd2f0d1fe218e', 1, 'Mayor\'s Clearance', 'Antonio Pascual', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-05 14:58:43', '2026-10-05 15:05:44', '2026-10-05 15:05:44', '2026-10-05 15:19:17', 1),
(7362, 1, '2026-10-05', 82, 'MO-C082', '4788c4aa95c80e1290645ecf9422d058', 3, 'Personal concerns', 'Elena Reyes', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-05 15:18:01', '2026-10-05 15:21:35', '2026-10-05 15:21:35', '2026-10-05 15:30:56', 1),
(7363, 1, '2026-10-05', 83, 'MO-A083', '895f22a7a4584790603de5586048c0fb', 1, 'Mayor\'s Clearance', 'Pedro Ramos', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-05 15:23:46', '2026-10-05 15:27:28', '2026-10-05 15:27:28', '2026-10-05 15:30:16', 1),
(7364, 1, '2026-10-05', 84, 'MO-A084', '91d5a76628b4a0f40201c661c078076b', 1, 'Mayor\'s Clearance', 'Marites Santos', NULL, NULL, NULL, 'staff', 'done', 55, 'Sir Henjie', 0, '2026-10-05 15:23:55', '2026-10-05 15:27:59', '2026-10-05 15:27:59', '2026-10-05 15:33:23', 1),
(7365, 1, '2026-10-05', 85, 'MO-A085', 'cbe9f73a3c1fe986c1584c989fec2c43', 1, 'Mayor\'s Clearance', 'Alfredo Castillo', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-05 15:29:14', '2026-10-05 15:29:44', '2026-10-05 15:29:44', '2026-10-05 15:35:59', 1),
(7366, 1, '2026-10-05', 86, 'MO-E086', 'f0e5e31d920148c7583d4e99a74e3ef5', 5, 'Financial assistance', 'Maria Cortez', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-05 15:33:52', '2026-10-05 15:37:12', '2026-10-05 15:37:12', '2026-10-05 15:39:56', 1),
(7367, 1, '2026-10-05', 87, 'MO-E087', 'd275b2f60d42545f8ddad5d80a312772', 5, 'Financial assistance', 'Eduardo Domingo', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-05 15:35:50', '2026-10-05 15:40:27', '2026-10-05 15:40:27', '2026-10-05 15:47:48', 1),
(7368, 1, '2026-10-05', 88, 'MO-A088', '68d9d1fbeeedb41492f47eade00863c7', 1, 'Mayor\'s Clearance', 'Rosario Mercado', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-05 15:36:35', '2026-10-05 15:39:19', '2026-10-05 15:39:19', '2026-10-05 15:51:39', 1),
(7369, 1, '2026-10-05', 89, 'MO-C089', '1bc943ca87809a7d78e785e8be88f1b0', 3, 'Personal concerns', 'Ricardo Navarro', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-05 15:46:13', '2026-10-05 15:48:36', '2026-10-05 15:48:36', '2026-10-05 16:01:35', 1),
(7370, 1, '2026-10-05', 90, 'MO-B090', '6b2c75d67d8553fa9622c53c6ba962d2', 2, 'Business concerns', 'Carmela Salazar', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-05 15:51:16', '2026-10-05 15:52:19', '2026-10-05 15:52:19', '2026-10-05 15:58:46', 1),
(7371, 1, '2026-10-05', 91, 'MO-A091', '239dc1bd2817285c9636a8122d63f9d5', 1, 'Mayor\'s Clearance', 'Felipe Aquino', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-05 15:56:29', '2026-10-05 16:02:16', '2026-10-05 16:02:16', '2026-10-05 16:08:09', 1),
(7372, 1, '2026-10-05', 92, 'MO-A092', '75eb36c3a9047773edef390eaffd12fe', 1, 'Mayor\'s Clearance', 'Cynthia Fernandez', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-05 16:04:44', '2026-10-05 16:05:24', '2026-10-05 16:05:24', '2026-10-05 16:09:53', 1),
(7373, 1, '2026-10-05', 93, 'MO-B093', 'febde400876b6a36fb652543e87bd554', 2, 'Business concerns', 'Jose Cruz', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-05 16:50:47', '2026-10-05 16:51:17', '2026-10-05 16:51:17', '2026-10-05 16:57:03', 1),
(7374, 1, '2026-10-06', 1, 'MO-A001', '70d6eb3920c2f9a25b387fd5d427c7f5', 1, 'Mayor\'s Clearance', 'Liza Cruz', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-06 08:10:41', '2026-10-06 08:14:37', '2026-10-06 08:14:37', '2026-10-06 08:22:42', 1),
(7375, 1, '2026-10-06', 2, 'MO-E002', '5cf05794913e3e2c580618950bbaa2a1', 5, 'Financial assistance', 'Antonio Gonzales', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-06 08:18:18', '2026-10-06 08:19:23', '2026-10-06 08:19:23', '2026-10-06 08:30:57', 1),
(7376, 1, '2026-10-06', 3, 'MO-C003', '4324f4a65d9c39ddd3e6661eafe68c6f', 3, 'Personal concerns', 'Elena Mendoza', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-06 08:19:56', '2026-10-06 08:22:07', '2026-10-06 08:22:07', '2026-10-06 08:46:16', 1),
(7377, 1, '2026-10-06', 4, 'MO-D004', '09f8bf18c3528184552f38ccc29a7b57', 4, 'Courtesy call', 'Pedro Rivera', NULL, NULL, NULL, 'staff', 'done', 54, 'Jean Frias', 0, '2026-10-06 08:21:29', '2026-10-06 08:22:35', '2026-10-06 08:22:35', '2026-10-06 08:28:35', 1),
(7378, 1, '2026-10-06', 5, 'MO-A005', '94f253d673c95ef24d490ea5f598aee3', 1, 'Mayor\'s Clearance', 'Marites Villanueva', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-06 08:26:34', '2026-10-06 08:30:53', '2026-10-06 08:30:53', '2026-10-06 08:39:05', 1),
(7379, 1, '2026-10-06', 6, 'MO-A006', 'a6daf5088f9803ea0a21bad1b27b0ded', 1, 'Mayor\'s Clearance', 'Alfredo Lopez', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-06 08:28:52', '2026-10-06 08:34:21', '2026-10-06 08:34:21', '2026-10-06 08:46:48', 1),
(7380, 1, '2026-10-06', 7, 'MO-B007', '6223aa52709141aee4ffd013da6ab14c', 2, 'Business concerns', 'Maria Garcia', NULL, NULL, NULL, 'self', 'skipped', 56, 'Angelu', 0, '2026-10-06 08:34:36', '2026-10-06 08:40:31', NULL, NULL, 1),
(7381, 1, '2026-10-06', 8, 'MO-E008', '2ab2acc53a38328ccfbea61fc0d0b194', 5, 'Financial assistance', 'Eduardo Flores', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-06 08:42:20', '2026-10-06 08:48:22', '2026-10-06 08:48:22', '2026-10-06 08:52:34', 1),
(7382, 1, '2026-10-06', 9, 'MO-E009', '58d10675aa53ca888bbc52570e15732b', 5, 'Financial assistance', 'Rosario Bautista', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-06 08:58:26', '2026-10-06 09:08:44', '2026-10-06 09:08:44', '2026-10-06 09:17:52', 1),
(7383, 1, '2026-10-06', 10, 'MO-B010', '7e02aa53eb4c2b09a8d2a483668873ec', 2, 'Business concerns', 'Ricardo Torres', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-06 09:03:20', '2026-10-06 09:08:33', '2026-10-06 09:08:33', '2026-10-06 09:18:22', 1),
(7384, 1, '2026-10-06', 11, 'MO-C011', '3ff06c48be02403c98390f03cb76bdc7', 3, 'Personal concerns', 'Carmela Dela Cruz', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-06 09:04:31', '2026-10-06 09:19:19', '2026-10-06 09:19:19', '2026-10-06 09:36:12', 1),
(7385, 1, '2026-10-06', 12, 'MO-E012', '3dc5e8d7a3eab0d1c10e8ed85d327e37', 5, 'Financial assistance', 'Felipe Pascual', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-06 09:04:36', '2026-10-06 09:10:45', '2026-10-06 09:10:45', '2026-10-06 09:17:15', 1),
(7386, 1, '2026-10-06', 13, 'MO-C013', 'c76070198ad759056d16b76162ac7170', 3, 'Personal concerns', 'Cynthia Reyes', NULL, NULL, NULL, 'staff', 'done', 54, 'Jean Frias', 0, '2026-10-06 09:13:31', '2026-10-06 09:25:02', '2026-10-06 09:25:02', '2026-10-06 09:33:07', 1),
(7387, 1, '2026-10-06', 14, 'MO-E014', 'c8a2daf704518c8e33941facbf57817b', 5, 'Financial assistance', 'Jose Ramos', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-06 09:14:21', '2026-10-06 09:22:21', '2026-10-06 09:22:21', '2026-10-06 09:34:03', 1),
(7388, 1, '2026-10-06', 15, 'MO-D015', '0e0eebb7e40668b42a25e571af696265', 4, 'Courtesy call', 'Teresita Santos', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-06 09:16:06', '2026-10-06 09:33:42', '2026-10-06 09:33:42', '2026-10-06 09:47:09', 1),
(7389, 1, '2026-10-06', 16, 'MO-C016', '5570c808add79615411ea0cb36a48d7c', 3, 'Personal concerns', 'Danilo Castillo', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-06 09:19:27', '2026-10-06 09:25:57', '2026-10-06 09:25:57', '2026-10-06 09:36:23', 1),
(7390, 1, '2026-10-06', 17, 'MO-E017', 'e7aec8f3c155b204c95a955f2165db83', 5, 'Financial assistance', 'Lourdes Cortez', NULL, NULL, NULL, 'staff', 'skipped', 54, 'Jean Frias', 0, '2026-10-06 09:31:58', '2026-10-06 09:47:27', NULL, NULL, 1),
(7391, 1, '2026-10-06', 18, 'MO-E018', 'c0cc9ffd3eafd650f4347834ad7d8edd', 5, 'Financial assistance', 'Ramon Domingo', NULL, NULL, NULL, 'staff', 'done', 56, 'Angelu', 0, '2026-10-06 09:35:30', '2026-10-06 09:52:54', '2026-10-06 09:52:54', '2026-10-06 09:57:09', 1),
(7392, 1, '2026-10-06', 19, 'MO-A019', 'a54b3eb15a44a2da5456a3307bf2f6ca', 1, 'Mayor\'s Clearance', 'Josefina Mercado', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-06 09:39:46', '2026-10-06 09:48:15', '2026-10-06 09:48:15', '2026-10-06 09:52:43', 1),
(7393, 1, '2026-10-06', 20, 'MO-C020', '1e8992ca298178bde32aa69a81c5fe59', 3, 'Personal concerns', 'Manuel Navarro', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-06 09:44:18', '2026-10-06 09:47:35', '2026-10-06 09:47:35', '2026-10-06 09:51:54', 1),
(7394, 1, '2026-10-06', 21, 'MO-B021', 'cd96a2184b8586732a159d01f494c0c3', 2, 'Business concerns', 'Ana Salazar', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-06 09:56:51', '2026-10-06 10:15:09', '2026-10-06 10:15:09', '2026-10-06 10:26:09', 1),
(7395, 1, '2026-10-06', 22, 'MO-C022', 'ddb9b3e433d1a28ee0291b805b5b3252', 3, 'Personal concerns', 'Rogelio Aquino', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-06 09:58:31', '2026-10-06 10:02:39', '2026-10-06 10:02:39', '2026-10-06 10:19:08', 1),
(7396, 1, '2026-10-06', 23, 'MO-C023', 'b09ec5353f7b8b6cb59c45ebe8b36702', 3, 'Personal concerns', 'Grace Fernandez', NULL, NULL, NULL, 'staff', 'done', 54, 'Jean Frias', 0, '2026-10-06 10:02:29', '2026-10-06 10:21:29', '2026-10-06 10:21:29', '2026-10-06 10:38:52', 1),
(7397, 1, '2026-10-06', 24, 'MO-A024', '89f7d962bab7159d8bf3bc17ea1eea4d', 1, 'Mayor\'s Clearance', 'Gerardo Cruz', NULL, NULL, NULL, 'staff', 'skipped', 56, 'Angelu', 0, '2026-10-06 10:11:57', '2026-10-06 10:41:56', NULL, NULL, 1),
(7398, 1, '2026-10-06', 25, 'MO-A025', '3b8da7abf2a933c46257285317ff827f', 1, 'Mayor\'s Clearance', 'Liza Gonzales', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-06 10:12:09', '2026-10-06 10:15:47', '2026-10-06 10:15:47', '2026-10-06 10:19:00', 1),
(7399, 1, '2026-10-06', 26, 'MO-C026', '6c91618afd30cde1bf9068bc75b02486', 3, 'Personal concerns', 'Antonio Mendoza', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-06 10:23:28', '2026-10-06 10:29:51', '2026-10-06 10:29:51', '2026-10-06 10:36:44', 1),
(7400, 1, '2026-10-06', 27, 'MO-B027', '9fb011283adfc96bc11a1b0625cfddee', 2, 'Business concerns', 'Elena Rivera', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-06 10:34:34', '2026-10-06 10:41:42', '2026-10-06 10:41:42', '2026-10-06 10:54:16', 1),
(7401, 1, '2026-10-06', 28, 'MO-B028', '4e6913040416bdeba4a6749e136b2023', 2, 'Business concerns', 'Pedro Villanueva', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-06 10:34:51', '2026-10-06 10:40:10', '2026-10-06 10:40:10', '2026-10-06 10:56:01', 1),
(7402, 1, '2026-10-06', 29, 'MO-D029', 'fdd22f5eab362d64d1a75984e9a46a32', 4, 'Courtesy call', 'Marites Lopez', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-06 10:50:55', '2026-10-06 10:59:36', '2026-10-06 10:59:36', '2026-10-06 11:36:58', 1),
(7403, 1, '2026-10-06', 30, 'MO-A030', '5ebc8fcae78e465670749d885d24d2fe', 1, 'Mayor\'s Clearance', 'Alfredo Garcia', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-06 10:56:09', '2026-10-06 10:58:26', '2026-10-06 10:58:26', '2026-10-06 11:03:04', 1),
(7404, 1, '2026-10-06', 31, 'MO-D031', 'b707a83bb7c601ec69d4cd2ed68387c7', 4, 'Courtesy call', 'Maria Flores', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-06 10:56:45', '2026-10-06 10:59:10', '2026-10-06 10:59:10', '2026-10-06 11:09:56', 1),
(7405, 1, '2026-10-06', 32, 'MO-B032', '1eab9296727d16253293af96cbf65069', 2, 'Business concerns', 'Eduardo Bautista', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-06 10:57:36', '2026-10-06 11:06:45', '2026-10-06 11:06:45', '2026-10-06 11:10:46', 1),
(7406, 1, '2026-10-06', 33, 'MO-A033', 'bb73764146982e05c545ef4b546dc32e', 1, 'Mayor\'s Clearance', 'Rosario Torres', NULL, NULL, NULL, 'staff', 'done', 55, 'Sir Henjie', 0, '2026-10-06 11:00:04', '2026-10-06 11:06:15', '2026-10-06 11:06:15', '2026-10-06 11:07:45', 1),
(7407, 1, '2026-10-06', 34, 'MO-B034', '04688a9757b10dd5008d94472a565fc9', 2, 'Business concerns', 'Ricardo Dela Cruz', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-06 11:09:58', '2026-10-06 11:13:09', '2026-10-06 11:13:09', '2026-10-06 11:19:42', 1),
(7408, 1, '2026-10-06', 35, 'MO-C035', 'a423d5ef48f7976fdbdf2248108c1a24', 3, 'Personal concerns', 'Carmela Pascual', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-06 11:10:44', '2026-10-06 11:14:27', '2026-10-06 11:14:27', '2026-10-06 11:31:46', 1),
(7409, 1, '2026-10-06', 36, 'MO-A036', 'c20b4cb507559abc2f37fc8fa805a090', 1, 'Mayor\'s Clearance', 'Felipe Reyes', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-06 11:25:05', '2026-10-06 11:34:28', '2026-10-06 11:34:28', '2026-10-06 11:44:35', 1),
(7410, 1, '2026-10-06', 37, 'MO-B037', 'dc3da0a1c2ee4dcc99bfe37b9272d7aa', 2, 'Business concerns', 'Cynthia Ramos', NULL, NULL, NULL, 'staff', 'done', 56, 'Angelu', 0, '2026-10-06 11:34:42', '2026-10-06 11:40:47', '2026-10-06 11:40:47', '2026-10-06 11:54:34', 1),
(7411, 1, '2026-10-06', 38, 'MO-A038', '99c284b03f58c6bf4d54bc2b34f0a12d', 1, 'Mayor\'s Clearance', 'Jose Santos', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-06 11:39:38', '2026-10-06 11:40:23', '2026-10-06 11:40:23', '2026-10-06 11:47:58', 1),
(7412, 1, '2026-10-06', 39, 'MO-A039', '61e930466285f9f4782e8717a1c9a5f8', 1, 'Mayor\'s Clearance', 'Teresita Castillo', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-06 11:47:52', '2026-10-06 11:51:58', '2026-10-06 11:51:58', '2026-10-06 12:00:30', 1),
(7413, 1, '2026-10-06', 40, 'MO-F040', 'f376e8352fdd1be9afa8ca374b04cf76', 6, 'Other', 'Danilo Cortez', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-06 11:51:22', '2026-10-06 11:54:28', '2026-10-06 11:54:28', '2026-10-06 11:55:59', 1),
(7414, 1, '2026-10-06', 41, 'MO-A041', 'b1fd1b23278561441d9929f4f2e7c957', 1, 'Mayor\'s Clearance', 'Lourdes Domingo', NULL, NULL, NULL, 'staff', 'skipped', 54, 'Jean Frias', 0, '2026-10-06 11:52:26', '2026-10-06 11:57:14', NULL, NULL, 1),
(7415, 1, '2026-10-06', 42, 'MO-A042', '94e658478a743638a4282d0d68d2be53', 1, 'Mayor\'s Clearance', 'Ramon Mercado', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-06 11:56:41', '2026-10-06 12:03:50', '2026-10-06 12:03:50', '2026-10-06 12:10:55', 1),
(7416, 1, '2026-10-06', 43, 'MO-A043', '652a0d9670c7b9c73bfecbc4da529727', 1, 'Mayor\'s Clearance', 'Josefina Navarro', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-06 11:58:07', '2026-10-06 12:13:37', '2026-10-06 12:13:37', '2026-10-06 12:19:06', 1),
(7417, 1, '2026-10-06', 44, 'MO-E044', 'eb87ee38c49d4274c90ec284f63e1360', 5, 'Financial assistance', 'Manuel Salazar', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-06 11:58:52', '2026-10-06 12:02:03', '2026-10-06 12:02:03', '2026-10-06 12:22:28', 1),
(7418, 1, '2026-10-06', 45, 'MO-E045', 'f0409b3b5a671f4944a5a69dec7c484e', 5, 'Financial assistance', 'Ana Aquino', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-06 12:07:53', '2026-10-06 12:09:36', '2026-10-06 12:09:36', '2026-10-06 12:14:24', 1),
(7419, 1, '2026-10-06', 46, 'MO-B046', 'd2581c2523634fceb2f674c1b96c87ed', 2, 'Business concerns', 'Rogelio Fernandez', NULL, NULL, NULL, 'staff', 'done', 55, 'Sir Henjie', 0, '2026-10-06 12:19:45', '2026-10-06 12:23:05', '2026-10-06 12:23:05', '2026-10-06 12:37:43', 1),
(7420, 1, '2026-10-06', 47, 'MO-C047', 'f48611ce3f1227ff78c7e62fe2b9741b', 3, 'Personal concerns', 'Grace Cruz', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-06 12:54:44', '2026-10-06 12:55:34', '2026-10-06 12:55:34', '2026-10-06 13:02:25', 1),
(7421, 1, '2026-10-06', 48, 'MO-A048', 'a53c2575f12cf1b5246dcee54cc667c8', 1, 'Mayor\'s Clearance', 'Gerardo Gonzales', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-06 12:57:03', '2026-10-06 13:01:13', '2026-10-06 13:01:13', '2026-10-06 13:06:36', 1),
(7422, 1, '2026-10-06', 49, 'MO-E049', '24a76f9cb952dcdb536a4822d9f7c274', 5, 'Financial assistance', 'Liza Mendoza', NULL, NULL, NULL, 'staff', 'done', 54, 'Jean Frias', 0, '2026-10-06 13:02:31', '2026-10-06 13:07:51', '2026-10-06 13:07:51', '2026-10-06 13:10:20', 1),
(7423, 1, '2026-10-06', 50, 'MO-E050', '87635d9fb710ae38a14f78d59cb6b78f', 5, 'Financial assistance', 'Antonio Rivera', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-06 13:07:21', '2026-10-06 13:10:08', '2026-10-06 13:10:08', '2026-10-06 13:24:23', 1),
(7424, 1, '2026-10-06', 51, 'MO-E051', 'd027c0aa8436dc43785b52c9797a61a0', 5, 'Financial assistance', 'Elena Villanueva', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-06 13:14:06', '2026-10-06 13:22:42', '2026-10-06 13:22:42', '2026-10-06 13:27:38', 1),
(7425, 1, '2026-10-06', 52, 'MO-C052', 'd68c49c565965a3efd5374700a18b6d3', 3, 'Personal concerns', 'Pedro Lopez', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-06 13:16:10', '2026-10-06 13:20:12', '2026-10-06 13:20:12', '2026-10-06 13:29:09', 1),
(7426, 1, '2026-10-06', 53, 'MO-B053', '18d1abc2aab1aa91672e14d93f8d147e', 2, 'Business concerns', 'Marites Garcia', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-06 13:16:40', '2026-10-06 13:19:21', '2026-10-06 13:19:21', '2026-10-06 13:37:28', 1),
(7427, 1, '2026-10-06', 54, 'MO-F054', '6bd8769d1ab07db72e9e566b097322ad', 6, 'Other', 'Alfredo Flores', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-06 13:25:05', '2026-10-06 13:28:36', '2026-10-06 13:28:36', '2026-10-06 13:37:29', 1),
(7428, 1, '2026-10-06', 55, 'MO-F055', 'f697fc94ba58552fc739d44a2fbb37cc', 6, 'Other', 'Maria Bautista', NULL, NULL, NULL, 'staff', 'done', 54, 'Jean Frias', 0, '2026-10-06 13:29:26', '2026-10-06 13:36:08', '2026-10-06 13:36:08', '2026-10-06 13:45:41', 1),
(7429, 1, '2026-10-06', 56, 'MO-B056', '3d112322aab0b4c5962b05dd9e9fca5c', 2, 'Business concerns', 'Eduardo Torres', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-06 13:35:24', '2026-10-06 13:37:32', '2026-10-06 13:37:32', '2026-10-06 13:46:20', 1),
(7430, 1, '2026-10-06', 57, 'MO-B057', 'f5cdea1bce6eb4d2c1eb5a96786a9a12', 2, 'Business concerns', 'Rosario Dela Cruz', NULL, NULL, NULL, 'staff', 'done', 56, 'Angelu', 0, '2026-10-06 13:50:05', '2026-10-06 14:00:27', '2026-10-06 14:00:27', '2026-10-06 14:30:15', 1),
(7431, 1, '2026-10-06', 58, 'MO-C058', '4515c0ec89b8435f7c466f5d0b3fcfe1', 3, 'Personal concerns', 'Ricardo Pascual', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-06 13:52:32', '2026-10-06 13:58:39', '2026-10-06 13:58:39', '2026-10-06 14:05:48', 1),
(7432, 1, '2026-10-06', 59, 'MO-A059', '401d7941566595bf3e6731a289f54c22', 1, 'Mayor\'s Clearance', 'Carmela Reyes', NULL, NULL, NULL, 'self', 'skipped', 54, 'Jean Frias', 0, '2026-10-06 14:14:52', '2026-10-06 14:20:50', NULL, NULL, 1),
(7433, 1, '2026-10-06', 60, 'MO-E060', '8688d871eb7b126d422320bb9d7550ef', 5, 'Financial assistance', 'Felipe Ramos', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-06 14:16:34', '2026-10-06 14:21:09', '2026-10-06 14:21:09', '2026-10-06 14:41:30', 1),
(7434, 1, '2026-10-06', 61, 'MO-F061', 'd0efcd88e7330bfbb9ec5d53d40faedd', 6, 'Other', 'Cynthia Santos', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-06 14:22:35', '2026-10-06 14:23:49', '2026-10-06 14:23:49', '2026-10-06 14:27:36', 1),
(7435, 1, '2026-10-06', 62, 'MO-F062', 'f3001a9a563d7c02ce771a269038d2d6', 6, 'Other', 'Jose Castillo', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-06 14:23:54', '2026-10-06 14:25:18', '2026-10-06 14:25:18', '2026-10-06 14:33:46', 1),
(7436, 1, '2026-10-06', 63, 'MO-B063', '83333970395a7d62182404bc57f0d104', 2, 'Business concerns', 'Teresita Cortez', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-06 14:28:48', '2026-10-06 14:35:37', '2026-10-06 14:35:37', '2026-10-06 14:38:46', 1),
(7437, 1, '2026-10-06', 64, 'MO-A064', 'fbfa9389edc92ba960403aa12379202d', 1, 'Mayor\'s Clearance', 'Danilo Domingo', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-06 14:29:42', '2026-10-06 14:32:34', '2026-10-06 14:32:34', '2026-10-06 14:40:07', 1),
(7438, 1, '2026-10-06', 65, 'MO-B065', 'bc09689e302c4d93a83425eb91de9504', 2, 'Business concerns', 'Lourdes Mercado', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-06 14:35:31', '2026-10-06 14:43:41', '2026-10-06 14:43:41', '2026-10-06 14:51:57', 1),
(7439, 1, '2026-10-06', 66, 'MO-A066', '2d24faa0a030db125c1ca3138ae9ad12', 1, 'Mayor\'s Clearance', 'Ramon Navarro', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-06 14:36:12', '2026-10-06 14:44:02', '2026-10-06 14:44:02', '2026-10-06 14:50:25', 1),
(7440, 1, '2026-10-06', 67, 'MO-A067', '91705e0c32f16e6d75950a8a6523539c', 1, 'Mayor\'s Clearance', 'Josefina Salazar', NULL, NULL, NULL, 'staff', 'skipped', 56, 'Angelu', 0, '2026-10-06 14:46:55', '2026-10-06 14:54:42', NULL, NULL, 1),
(7441, 1, '2026-10-06', 68, 'MO-A068', 'ecabb42ecaf9fcac632decaf4b53429f', 1, 'Mayor\'s Clearance', 'Manuel Aquino', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-06 14:49:04', '2026-10-06 14:53:24', '2026-10-06 14:53:24', '2026-10-06 15:07:32', 1),
(7442, 1, '2026-10-06', 69, 'MO-A069', 'a37adf2efa96f66727cde1d16642fc58', 1, 'Mayor\'s Clearance', 'Ana Fernandez', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-06 14:50:19', '2026-10-06 14:58:12', '2026-10-06 14:58:12', '2026-10-06 15:00:57', 1),
(7443, 1, '2026-10-06', 70, 'MO-A070', '320320118e429d3cd87eda2369c5a688', 1, 'Mayor\'s Clearance', 'Rogelio Cruz', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-06 15:23:07', '2026-10-06 15:26:34', '2026-10-06 15:26:34', '2026-10-06 15:31:27', 1),
(7444, 1, '2026-10-06', 71, 'MO-B071', '1cc349f3876c16c631aa5b950d256bd7', 2, 'Business concerns', 'Grace Gonzales', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-06 15:59:57', '2026-10-06 16:03:59', '2026-10-06 16:03:59', '2026-10-06 16:10:59', 1),
(7445, 1, '2026-10-06', 72, 'MO-E072', '142a860109271ddec347c33e9a2af27e', 5, 'Financial assistance', 'Gerardo Mendoza', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-06 16:19:22', '2026-10-06 16:23:31', '2026-10-06 16:23:31', '2026-10-06 16:28:00', 1),
(7446, 1, '2026-10-06', 73, 'MO-B073', '9eeb9db0809b1d37bd46a785aab6c5d6', 2, 'Business concerns', 'Liza Rivera', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-06 16:23:26', '2026-10-06 16:27:14', '2026-10-06 16:27:14', '2026-10-06 16:41:33', 1),
(7447, 1, '2026-10-06', 74, 'MO-A074', 'ab139de71574aad5d38f4e9bff42c4b3', 1, 'Mayor\'s Clearance', 'Antonio Villanueva', NULL, NULL, NULL, 'staff', 'done', 54, 'Jean Frias', 0, '2026-10-06 16:43:57', '2026-10-06 16:51:27', '2026-10-06 16:51:27', '2026-10-06 16:59:39', 1),
(7448, 1, '2026-10-07', 1, 'MO-A001', '829c9f36ae7033f97779d865513b1df7', 1, 'Mayor\'s Clearance', 'Eduardo Cruz', NULL, NULL, NULL, 'staff', 'done', 54, 'Jean Frias', 0, '2026-10-07 08:01:56', '2026-10-07 08:05:09', '2026-10-07 08:05:09', '2026-10-07 08:11:36', 1),
(7449, 1, '2026-10-07', 2, 'MO-A002', '4a938ec27d25e4a71a6c5a88570b07d0', 1, 'Mayor\'s Clearance', 'Rosario Gonzales', NULL, NULL, NULL, 'staff', 'done', 56, 'Angelu', 0, '2026-10-07 08:22:05', '2026-10-07 08:26:24', '2026-10-07 08:26:24', '2026-10-07 08:33:55', 1),
(7450, 1, '2026-10-07', 3, 'MO-A003', 'f5bcb34fe9b764db5343fe3934306e6d', 1, 'Mayor\'s Clearance', 'Ricardo Mendoza', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-07 08:37:35', '2026-10-07 08:39:05', '2026-10-07 08:39:05', '2026-10-07 08:42:09', 1),
(7451, 1, '2026-10-07', 4, 'MO-A004', 'cb19c6cf0ffc5ef9adb96f001fe22311', 1, 'Mayor\'s Clearance', 'Carmela Rivera', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-07 08:55:15', '2026-10-07 08:56:59', '2026-10-07 08:56:59', '2026-10-07 08:59:43', 1),
(7452, 1, '2026-10-07', 5, 'MO-B005', '343123b77a6928cbe0f2a8a62ab96123', 2, 'Business concerns', 'Felipe Villanueva', NULL, NULL, NULL, 'staff', 'done', 56, 'Angelu', 0, '2026-10-07 09:02:00', '2026-10-07 09:17:43', '2026-10-07 09:17:43', '2026-10-07 09:26:55', 1),
(7453, 1, '2026-10-07', 6, 'MO-A006', 'de806f206ba1cea7e470409cf5716c5d', 1, 'Mayor\'s Clearance', 'Cynthia Lopez', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-07 09:03:31', '2026-10-07 09:16:39', '2026-10-07 09:16:39', '2026-10-07 09:19:10', 1),
(7454, 1, '2026-10-07', 7, 'MO-B007', '352f7ec3a102ca914eebc3d8eb14a3d8', 2, 'Business concerns', 'Jose Garcia', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-07 09:16:58', '2026-10-07 09:27:42', '2026-10-07 09:27:42', '2026-10-07 09:35:31', 1),
(7455, 1, '2026-10-07', 8, 'MO-A008', '460b90f4dd5f19ce5d81f088924c71cf', 1, 'Mayor\'s Clearance', 'Teresita Flores', NULL, NULL, NULL, 'staff', 'done', 56, 'Angelu', 0, '2026-10-07 09:21:04', '2026-10-07 09:35:21', '2026-10-07 09:35:21', '2026-10-07 09:38:50', 1),
(7456, 1, '2026-10-07', 9, 'MO-D009', 'db6cbf064c4983412829c103bdeb28af', 4, 'Courtesy call', 'Danilo Bautista', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-07 09:22:07', '2026-10-07 09:25:19', '2026-10-07 09:25:19', '2026-10-07 09:28:38', 1),
(7457, 1, '2026-10-07', 10, 'MO-C010', '49be48699b2dc5c1415de9b19ecbe7dc', 3, 'Personal concerns', 'Lourdes Torres', NULL, NULL, NULL, 'staff', 'done', 56, 'Angelu', 0, '2026-10-07 09:24:45', '2026-10-07 09:27:24', '2026-10-07 09:27:24', '2026-10-07 09:45:15', 1),
(7458, 1, '2026-10-07', 11, 'MO-A011', '40d93aa8a6772534ddff6bf19e7fd1dd', 1, 'Mayor\'s Clearance', 'Ramon Dela Cruz', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-07 09:24:48', '2026-10-07 09:36:52', '2026-10-07 09:36:52', '2026-10-07 09:42:30', 1),
(7459, 1, '2026-10-07', 12, 'MO-A012', '629dc9f33c0068811f34a9c406c135c1', 1, 'Mayor\'s Clearance', 'Josefina Pascual', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-07 09:36:26', '2026-10-07 09:51:04', '2026-10-07 09:51:04', '2026-10-07 09:57:23', 1),
(7460, 1, '2026-10-07', 13, 'MO-A013', 'dc2bfe8eb0ece0df2e82cd18516f2771', 1, 'Mayor\'s Clearance', 'Manuel Reyes', NULL, NULL, NULL, 'staff', 'done', 56, 'Angelu', 0, '2026-10-07 09:47:05', '2026-10-07 09:51:58', '2026-10-07 09:51:58', '2026-10-07 09:58:56', 1),
(7461, 1, '2026-10-07', 14, 'MO-C014', '47e00b3f245d9ccf25b43047a6fecebd', 3, 'Personal concerns', 'Ana Ramos', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-07 09:49:32', '2026-10-07 10:00:54', '2026-10-07 10:00:54', '2026-10-07 10:16:54', 1),
(7462, 1, '2026-10-07', 15, 'MO-A015', '89ef8507913291cf22f89a1e96dbf8c5', 1, 'Mayor\'s Clearance', 'Rogelio Santos', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-07 09:56:01', '2026-10-07 10:05:32', '2026-10-07 10:05:32', '2026-10-07 10:13:41', 1),
(7463, 1, '2026-10-07', 16, 'MO-F016', '9421f9b943ca5b623d46630f7c9b9a17', 6, 'Other', 'Grace Castillo', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-07 09:58:18', '2026-10-07 10:08:32', '2026-10-07 10:08:32', '2026-10-07 10:13:15', 1),
(7464, 1, '2026-10-07', 17, 'MO-C017', '915ea265e246d353b6594047c061278f', 3, 'Personal concerns', 'Gerardo Cortez', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-07 10:00:17', '2026-10-07 10:04:52', '2026-10-07 10:04:52', '2026-10-07 10:10:16', 1),
(7465, 1, '2026-10-07', 18, 'MO-C018', 'fa8dd2770f8f4751847882e846b943f9', 3, 'Personal concerns', 'Liza Domingo', NULL, NULL, NULL, 'staff', 'done', 54, 'Jean Frias', 0, '2026-10-07 10:09:28', '2026-10-07 10:19:05', '2026-10-07 10:19:05', '2026-10-07 10:36:58', 1),
(7466, 1, '2026-10-07', 19, 'MO-E019', 'c70438056854b9098872f6b7bde5f4ff', 5, 'Financial assistance', 'Antonio Mercado', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-07 10:15:32', '2026-10-07 10:17:02', '2026-10-07 10:17:02', '2026-10-07 10:34:57', 1),
(7467, 1, '2026-10-07', 20, 'MO-C020', '1e8d3758d69f62a20ef5c6701ae6f64b', 3, 'Personal concerns', 'Elena Navarro', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-07 10:19:08', '2026-10-07 10:34:02', '2026-10-07 10:34:02', '2026-10-07 11:05:49', 1),
(7468, 1, '2026-10-07', 21, 'MO-C021', 'f9eb40da10bd3a0a59a0e7ca64245fff', 3, 'Personal concerns', 'Pedro Salazar', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-07 10:24:12', '2026-10-07 10:32:56', '2026-10-07 10:32:56', '2026-10-07 10:45:11', 1),
(7469, 1, '2026-10-07', 22, 'MO-A022', '1a6722ca529e3bd1df5c16acc7895408', 1, 'Mayor\'s Clearance', 'Marites Aquino', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-07 10:25:53', '2026-10-07 10:34:45', '2026-10-07 10:34:45', '2026-10-07 10:37:42', 1),
(7470, 1, '2026-10-07', 23, 'MO-A023', '2fd29c6dccee7d6126d073c33fcd03dc', 1, 'Mayor\'s Clearance', 'Alfredo Fernandez', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-07 10:27:28', '2026-10-07 10:38:32', '2026-10-07 10:38:32', '2026-10-07 10:40:07', 1),
(7471, 1, '2026-10-07', 24, 'MO-E024', '3801aab2cee189afba51f829cf00b397', 5, 'Financial assistance', 'Maria Cruz', NULL, NULL, NULL, 'self', 'skipped', 56, 'Angelu', 0, '2026-10-07 10:30:39', '2026-10-07 10:38:41', NULL, NULL, 1),
(7472, 1, '2026-10-07', 25, 'MO-B025', '307bbbacfca39f8c9b170b63a5301f6a', 2, 'Business concerns', 'Eduardo Gonzales', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-07 10:32:07', '2026-10-07 10:41:41', '2026-10-07 10:41:41', '2026-10-07 10:50:07', 1),
(7473, 1, '2026-10-07', 26, 'MO-C026', '1a40c544afe51784ab1916b7e17f75e1', 3, 'Personal concerns', 'Rosario Mendoza', NULL, NULL, NULL, 'staff', 'done', 54, 'Jean Frias', 0, '2026-10-07 10:33:28', '2026-10-07 10:43:43', '2026-10-07 10:43:43', '2026-10-07 10:48:10', 1),
(7474, 1, '2026-10-07', 27, 'MO-A027', '23d89e2d975a8e5567024686c6d1ccdf', 1, 'Mayor\'s Clearance', 'Ricardo Rivera', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-07 10:34:54', '2026-10-07 10:41:52', '2026-10-07 10:41:52', '2026-10-07 10:46:12', 1),
(7475, 1, '2026-10-07', 28, 'MO-A028', '2394bb204febc922dc9f892d75050b60', 1, 'Mayor\'s Clearance', 'Carmela Villanueva', NULL, NULL, NULL, 'staff', 'cancelled', NULL, NULL, 0, '2026-10-07 10:44:43', NULL, NULL, NULL, 1),
(7476, 1, '2026-10-07', 29, 'MO-C029', 'e084349d9175df0f5d9f0f25b5017889', 3, 'Personal concerns', 'Felipe Lopez', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-07 10:46:07', '2026-10-07 10:59:30', '2026-10-07 10:59:30', '2026-10-07 11:07:11', 1),
(7477, 1, '2026-10-07', 30, 'MO-F030', '07e82da628bd54d0a1ba63ffd5717650', 6, 'Other', 'Cynthia Garcia', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-07 10:47:17', '2026-10-07 10:52:22', '2026-10-07 10:52:22', '2026-10-07 10:55:33', 1),
(7478, 1, '2026-10-07', 31, 'MO-E031', 'd7e49ef2c3a32799ac65893c50b64c5a', 5, 'Financial assistance', 'Jose Flores', NULL, NULL, NULL, 'staff', 'done', 55, 'Sir Henjie', 0, '2026-10-07 10:50:11', '2026-10-07 10:52:37', '2026-10-07 10:52:37', '2026-10-07 10:56:55', 1),
(7479, 1, '2026-10-07', 32, 'MO-A032', 'b694b34dccec5429d94af7bc88bee825', 1, 'Mayor\'s Clearance', 'Teresita Bautista', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-07 11:00:10', '2026-10-07 11:03:58', '2026-10-07 11:03:58', '2026-10-07 11:05:28', 1),
(7480, 1, '2026-10-07', 33, 'MO-A033', 'f41c0019aa3ed59caf1788f5f5e86f80', 1, 'Mayor\'s Clearance', 'Danilo Torres', NULL, NULL, NULL, 'self', 'cancelled', NULL, NULL, 0, '2026-10-07 11:18:17', NULL, NULL, NULL, 1),
(7481, 1, '2026-10-07', 34, 'MO-B034', 'f63a5d27bf25d7a04b9e035fc73714ee', 2, 'Business concerns', 'Lourdes Dela Cruz', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-07 11:23:41', '2026-10-07 11:26:51', '2026-10-07 11:26:51', '2026-10-07 11:43:12', 1),
(7482, 1, '2026-10-07', 35, 'MO-A035', 'b2d3720a99598febc0d651481b53fdcb', 1, 'Mayor\'s Clearance', 'Ramon Pascual', NULL, NULL, NULL, 'staff', 'done', 56, 'Angelu', 0, '2026-10-07 11:23:53', '2026-10-07 11:26:03', '2026-10-07 11:26:03', '2026-10-07 11:39:14', 1),
(7483, 1, '2026-10-07', 36, 'MO-F036', 'd836d8d04ae1f82419807fcb97728528', 6, 'Other', 'Josefina Reyes', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-07 11:26:32', '2026-10-07 11:27:06', '2026-10-07 11:27:06', '2026-10-07 11:35:14', 1),
(7484, 1, '2026-10-07', 37, 'MO-E037', '69613145ff7fd55b24fa96ccbbbd2e61', 5, 'Financial assistance', 'Manuel Ramos', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-07 11:37:30', '2026-10-07 11:43:05', '2026-10-07 11:43:05', '2026-10-07 11:52:59', 1),
(7485, 1, '2026-10-07', 38, 'MO-A038', '0cc475ef31cecf24e846e5a2032991ab', 1, 'Mayor\'s Clearance', 'Ana Santos', NULL, NULL, NULL, 'self', 'skipped', 55, 'Sir Henjie', 0, '2026-10-07 11:53:14', '2026-10-07 11:55:12', NULL, NULL, 1),
(7486, 1, '2026-10-07', 39, 'MO-E039', '3f195162b34db1f59461ea917f5689e3', 5, 'Financial assistance', 'Rogelio Castillo', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-07 11:59:32', '2026-10-07 12:04:16', '2026-10-07 12:04:16', '2026-10-07 12:17:05', 1),
(7487, 1, '2026-10-07', 40, 'MO-D040', '2a92b164e66d70225c9a09b6cac6c15f', 4, 'Courtesy call', 'Grace Cortez', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-07 12:00:38', '2026-10-07 12:03:21', '2026-10-07 12:03:21', '2026-10-07 12:12:20', 1),
(7488, 1, '2026-10-07', 41, 'MO-E041', '3748e02410aabea90813a223324d292d', 5, 'Financial assistance', 'Gerardo Domingo', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-07 12:07:27', '2026-10-07 12:11:54', '2026-10-07 12:11:54', '2026-10-07 12:22:59', 1),
(7489, 1, '2026-10-07', 42, 'MO-B042', '96a7c10c5b23d4fd473b33cf5df54d2c', 2, 'Business concerns', 'Liza Mercado', NULL, NULL, NULL, 'staff', 'cancelled', NULL, NULL, 0, '2026-10-07 12:10:09', NULL, NULL, NULL, 1),
(7490, 1, '2026-10-07', 43, 'MO-B043', '068f4cb491e97ef9862ed0cacc6943f1', 2, 'Business concerns', 'Antonio Navarro', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-07 12:13:00', '2026-10-07 12:16:18', '2026-10-07 12:16:18', '2026-10-07 12:18:05', 1),
(7491, 1, '2026-10-07', 44, 'MO-A044', 'bb7a96d5b963209a7b9817d4c1cddf11', 1, 'Mayor\'s Clearance', 'Elena Salazar', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-07 12:20:35', '2026-10-07 12:25:07', '2026-10-07 12:25:07', '2026-10-07 12:28:04', 1),
(7492, 1, '2026-10-07', 45, 'MO-A045', '3f3bc697180f5d4ff96d43bb14d0d69a', 1, 'Mayor\'s Clearance', 'Pedro Aquino', NULL, NULL, NULL, 'staff', 'done', 54, 'Jean Frias', 0, '2026-10-07 12:22:19', '2026-10-07 12:26:59', '2026-10-07 12:26:59', '2026-10-07 12:31:17', 1),
(7493, 1, '2026-10-07', 46, 'MO-F046', '9c57588d6244cf0cbf8579e6f6f70210', 6, 'Other', 'Marites Fernandez', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-07 12:23:00', '2026-10-07 12:28:52', '2026-10-07 12:28:52', '2026-10-07 12:33:32', 1),
(7494, 1, '2026-10-07', 47, 'MO-F047', '6cdb6a23e3f521f0639feeb9dbc521fb', 6, 'Other', 'Alfredo Cruz', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-07 12:43:28', '2026-10-07 12:45:03', '2026-10-07 12:45:03', '2026-10-07 12:50:39', 1),
(7495, 1, '2026-10-07', 48, 'MO-F048', '3770913ad17e4bc7533fa55b131df56f', 6, 'Other', 'Maria Gonzales', NULL, NULL, NULL, 'self', 'cancelled', NULL, NULL, 0, '2026-10-07 13:12:45', NULL, NULL, NULL, 1),
(7496, 1, '2026-10-07', 49, 'MO-A049', 'e7248c2cd1f52f53555a3ee8863b8385', 1, 'Mayor\'s Clearance', 'Eduardo Mendoza', NULL, NULL, NULL, 'staff', 'done', 55, 'Sir Henjie', 0, '2026-10-07 13:13:42', '2026-10-07 13:20:23', '2026-10-07 13:20:23', '2026-10-07 13:24:22', 1),
(7497, 1, '2026-10-07', 50, 'MO-F050', '202a583bf1249162da41896d63b47354', 6, 'Other', 'Rosario Rivera', NULL, NULL, NULL, 'staff', 'done', 56, 'Angelu', 0, '2026-10-07 13:14:25', '2026-10-07 13:29:31', '2026-10-07 13:29:31', '2026-10-07 13:35:56', 1),
(7498, 1, '2026-10-07', 51, 'MO-F051', 'f340e6e5face1b03b4f6b7f9d8d2d337', 6, 'Other', 'Ricardo Villanueva', NULL, NULL, NULL, 'self', 'skipped', 56, 'Angelu', 0, '2026-10-07 13:15:32', '2026-10-07 13:29:00', NULL, NULL, 1);
INSERT INTO `queue_tickets` (`id`, `queue_id`, `queue_day`, `number`, `code`, `access_key`, `service_id`, `service_name`, `full_name`, `mobile`, `phone_key`, `user_id`, `source`, `status`, `staff_id`, `staff_name`, `is_priority`, `created_at`, `called_at`, `started_at`, `finished_at`, `is_demo`) VALUES
(7499, 1, '2026-10-07', 52, 'MO-A052', '437e574bf256b3c3566be18dd10c890c', 1, 'Mayor\'s Clearance', 'Carmela Lopez', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-07 13:19:16', '2026-10-07 13:28:14', '2026-10-07 13:28:14', '2026-10-07 13:32:02', 1),
(7500, 1, '2026-10-07', 53, 'MO-A053', '68e0af65c5fb74a39c518ec5c2a9d194', 1, 'Mayor\'s Clearance', 'Felipe Garcia', NULL, NULL, NULL, 'staff', 'done', 54, 'Jean Frias', 0, '2026-10-07 13:29:26', '2026-10-07 13:31:09', '2026-10-07 13:31:09', '2026-10-07 13:36:13', 1),
(7501, 1, '2026-10-07', 54, 'MO-A054', 'b80f9fc9674946af8854db46ec0b3caa', 1, 'Mayor\'s Clearance', 'Cynthia Flores', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-07 13:32:51', '2026-10-07 13:45:33', '2026-10-07 13:45:33', '2026-10-07 13:48:52', 1),
(7502, 1, '2026-10-07', 55, 'MO-E055', 'a83f5c3d460ec5568fbcbb77227c9de5', 5, 'Financial assistance', 'Jose Bautista', NULL, NULL, NULL, 'self', 'cancelled', NULL, NULL, 0, '2026-10-07 13:35:20', NULL, NULL, NULL, 1),
(7503, 1, '2026-10-07', 56, 'MO-F056', '47fc4a9178d274df3c529445a8541d92', 6, 'Other', 'Teresita Torres', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-07 13:36:44', '2026-10-07 13:47:25', '2026-10-07 13:47:25', '2026-10-07 13:57:05', 1),
(7504, 1, '2026-10-07', 57, 'MO-C057', '50792854b250c9a2c13fe799744aeeb4', 3, 'Personal concerns', 'Danilo Dela Cruz', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-07 13:37:17', '2026-10-07 13:39:47', '2026-10-07 13:39:47', '2026-10-07 14:02:45', 1),
(7505, 1, '2026-10-07', 58, 'MO-A058', '48b2de585182f230699e179f8d7d5389', 1, 'Mayor\'s Clearance', 'Lourdes Pascual', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-07 13:37:59', '2026-10-07 13:48:03', '2026-10-07 13:48:03', '2026-10-07 13:51:45', 1),
(7506, 1, '2026-10-07', 59, 'MO-A059', '108f27e8f99cea8c7a4a6fedacf83418', 1, 'Mayor\'s Clearance', 'Ramon Reyes', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-07 13:42:12', '2026-10-07 13:44:32', '2026-10-07 13:44:32', '2026-10-07 13:47:55', 1),
(7507, 1, '2026-10-07', 60, 'MO-A060', '39dc0288d4bddd9cc1ac94f0a756aa28', 1, 'Mayor\'s Clearance', 'Josefina Ramos', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-07 13:42:21', '2026-10-07 13:46:03', '2026-10-07 13:46:03', '2026-10-07 13:54:06', 1),
(7508, 1, '2026-10-07', 61, 'MO-F061', 'f95e05640e2a00fe2a6b20cfc07943b1', 6, 'Other', 'Manuel Santos', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-07 13:44:12', '2026-10-07 13:50:20', '2026-10-07 13:50:20', '2026-10-07 13:54:22', 1),
(7509, 1, '2026-10-07', 62, 'MO-B062', 'b74b6cbd9c9d941fe37f3c61747e94ab', 2, 'Business concerns', 'Ana Castillo', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-07 13:46:19', '2026-10-07 13:55:00', '2026-10-07 13:55:00', '2026-10-07 14:11:46', 1),
(7510, 1, '2026-10-07', 63, 'MO-A063', '37d4a8c1cf95f7d705b84dc68b461535', 1, 'Mayor\'s Clearance', 'Rogelio Cortez', NULL, NULL, NULL, 'staff', 'done', 54, 'Jean Frias', 0, '2026-10-07 13:55:43', '2026-10-07 13:57:59', '2026-10-07 13:57:59', '2026-10-07 14:00:36', 1),
(7511, 1, '2026-10-07', 64, 'MO-E064', '5cfc7b0b10c5b4ccd79bcf9486602e01', 5, 'Financial assistance', 'Grace Domingo', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-07 13:58:54', '2026-10-07 14:00:53', '2026-10-07 14:00:53', '2026-10-07 14:08:33', 1),
(7512, 1, '2026-10-07', 65, 'MO-F065', '845b95c43539a4c1954fad6c9dc16454', 6, 'Other', 'Gerardo Mercado', NULL, NULL, NULL, 'staff', 'done', 56, 'Angelu', 0, '2026-10-07 14:00:21', '2026-10-07 14:02:59', '2026-10-07 14:02:59', '2026-10-07 14:08:18', 1),
(7513, 1, '2026-10-07', 66, 'MO-B066', 'bf030d369e46678e5a8488866fd295b2', 2, 'Business concerns', 'Liza Navarro', NULL, NULL, NULL, 'self', 'cancelled', NULL, NULL, 0, '2026-10-07 14:03:30', NULL, NULL, NULL, 1),
(7514, 1, '2026-10-07', 67, 'MO-A067', 'e6f90caf9a93d77cbec7e5dbad192d6d', 1, 'Mayor\'s Clearance', 'Antonio Salazar', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-07 14:13:28', '2026-10-07 14:27:23', '2026-10-07 14:27:23', '2026-10-07 14:36:43', 1),
(7515, 1, '2026-10-07', 68, 'MO-E068', '44e04fe5d1d2b023b17a0743d8157b58', 5, 'Financial assistance', 'Elena Aquino', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-07 14:16:05', '2026-10-07 14:18:27', '2026-10-07 14:18:27', '2026-10-07 14:32:45', 1),
(7516, 1, '2026-10-07', 69, 'MO-E069', '7692467b17276c58f74b09910d763a52', 5, 'Financial assistance', 'Pedro Fernandez', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-07 14:20:24', '2026-10-07 14:29:29', '2026-10-07 14:29:29', '2026-10-07 14:34:37', 1),
(7517, 1, '2026-10-07', 70, 'MO-F070', 'da7dd6297e53fc3a6b23560c8e2f8717', 6, 'Other', 'Marites Cruz', NULL, NULL, NULL, 'self', 'skipped', 56, 'Angelu', 0, '2026-10-07 14:27:04', '2026-10-07 14:28:30', NULL, NULL, 1),
(7518, 1, '2026-10-07', 71, 'MO-A071', '0254eb1705a82d4e61b8b71651f7b386', 1, 'Mayor\'s Clearance', 'Alfredo Gonzales', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-07 14:31:00', '2026-10-07 14:35:02', '2026-10-07 14:35:02', '2026-10-07 14:40:14', 1),
(7519, 1, '2026-10-07', 72, 'MO-A072', 'c075cc732d8039f5eb82fcd177683d0e', 1, 'Mayor\'s Clearance', 'Maria Mendoza', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-07 14:49:10', '2026-10-07 14:49:40', '2026-10-07 14:49:40', '2026-10-07 14:54:21', 1),
(7520, 1, '2026-10-07', 73, 'MO-D073', '337a5f608aabc1f701344d6fde7a79c7', 4, 'Courtesy call', 'Eduardo Rivera', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-07 14:51:02', '2026-10-07 15:00:02', '2026-10-07 15:00:02', '2026-10-07 15:21:36', 1),
(7521, 1, '2026-10-07', 74, 'MO-A074', 'c6bd8e5df9217a539a00e337a68dc111', 1, 'Mayor\'s Clearance', 'Rosario Villanueva', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-07 14:53:05', '2026-10-07 15:01:19', '2026-10-07 15:01:19', '2026-10-07 15:07:04', 1),
(7522, 1, '2026-10-07', 75, 'MO-B075', '253ca2564137d69d1f0f107765bb525f', 2, 'Business concerns', 'Ricardo Lopez', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-07 14:59:37', '2026-10-07 15:07:58', '2026-10-07 15:07:58', '2026-10-07 15:25:01', 1),
(7523, 1, '2026-10-07', 76, 'MO-F076', 'b919f04693d557a14479f8c1ca01d8d9', 6, 'Other', 'Carmela Garcia', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-07 15:03:33', '2026-10-07 15:06:47', '2026-10-07 15:06:47', '2026-10-07 15:17:09', 1),
(7524, 1, '2026-10-07', 77, 'MO-C077', '5890c6f69443ef278b8c5e9691c5142d', 3, 'Personal concerns', 'Felipe Flores', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-07 15:32:39', '2026-10-07 15:35:56', '2026-10-07 15:35:56', '2026-10-07 15:40:18', 1),
(7525, 1, '2026-10-07', 78, 'MO-E078', 'bfab12b3075509b6c92adabcea64b9cb', 5, 'Financial assistance', 'Cynthia Bautista', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-07 15:42:08', '2026-10-07 15:45:15', '2026-10-07 15:45:15', '2026-10-07 15:48:31', 1),
(7526, 1, '2026-10-07', 79, 'MO-C079', '257f2dbe9d8131c8fca0f051e1b2a788', 3, 'Personal concerns', 'Jose Torres', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-07 15:56:14', '2026-10-07 15:59:33', '2026-10-07 15:59:33', '2026-10-07 16:08:13', 1),
(7527, 1, '2026-10-07', 80, 'MO-F080', '896c8c808c5e84b7f462fef2bba9053f', 6, 'Other', 'Teresita Dela Cruz', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-07 16:13:40', '2026-10-07 16:14:11', '2026-10-07 16:14:11', '2026-10-07 16:19:23', 1),
(7528, 1, '2026-10-07', 81, 'MO-B081', '3605baf19db1ef1957e9524b7be0e895', 2, 'Business concerns', 'Danilo Pascual', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-07 16:18:35', '2026-10-07 16:22:15', '2026-10-07 16:22:15', '2026-10-07 16:42:43', 1),
(7529, 1, '2026-10-08', 1, 'MO-C001', '82f909268aa93e1c8fec05c452167be5', 3, 'Personal concerns', 'Teresita Cruz', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-08 08:03:30', '2026-10-08 08:09:20', '2026-10-08 08:09:20', '2026-10-08 08:24:20', 1),
(7530, 1, '2026-10-08', 2, 'MO-A002', '09b5b806ee2551cf56c8c53770475a3c', 1, 'Mayor\'s Clearance', 'Danilo Gonzales', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-08 08:26:15', '2026-10-08 08:29:28', '2026-10-08 08:29:28', '2026-10-08 08:36:31', 1),
(7531, 1, '2026-10-08', 3, 'MO-F003', 'acf2e542304319c07221d37c6ebc7aa9', 6, 'Other', 'Lourdes Mendoza', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-08 09:06:31', '2026-10-08 09:12:17', '2026-10-08 09:12:17', '2026-10-08 09:21:48', 1),
(7532, 1, '2026-10-08', 4, 'MO-A004', '4ca8d02dcb5803a534d85b133de32537', 1, 'Mayor\'s Clearance', 'Ramon Rivera', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-08 09:16:11', '2026-10-08 09:22:17', '2026-10-08 09:22:17', '2026-10-08 09:25:48', 1),
(7533, 1, '2026-10-08', 5, 'MO-A005', 'd3368cb39abab909500fbf04b2c32420', 1, 'Mayor\'s Clearance', 'Josefina Villanueva', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-08 09:18:59', '2026-10-08 09:24:26', '2026-10-08 09:24:26', '2026-10-08 09:29:47', 1),
(7534, 1, '2026-10-08', 6, 'MO-E006', 'a9c78682737b65d550de5fd6af9b0e00', 5, 'Financial assistance', 'Manuel Lopez', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-08 09:27:25', '2026-10-08 09:32:34', '2026-10-08 09:32:34', '2026-10-08 09:37:45', 1),
(7535, 1, '2026-10-08', 7, 'MO-C007', 'b2f200ca1c7cfb8b2fefe1653aa29a83', 3, 'Personal concerns', 'Ana Garcia', NULL, NULL, NULL, 'staff', 'done', 56, 'Angelu', 0, '2026-10-08 09:32:59', '2026-10-08 09:37:42', '2026-10-08 09:37:42', '2026-10-08 09:45:25', 1),
(7536, 1, '2026-10-08', 8, 'MO-A008', '615a08bb69aa43164ce59731d89b600e', 1, 'Mayor\'s Clearance', 'Rogelio Flores', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-08 09:49:51', '2026-10-08 09:55:22', '2026-10-08 09:55:22', '2026-10-08 09:57:03', 1),
(7537, 1, '2026-10-08', 9, 'MO-E009', '4df49eccbf0ebfd22ecca5ebd9b2fa24', 5, 'Financial assistance', 'Grace Bautista', NULL, NULL, NULL, 'staff', 'done', 55, 'Sir Henjie', 0, '2026-10-08 09:53:18', '2026-10-08 09:56:47', '2026-10-08 09:56:47', '2026-10-08 10:06:41', 1),
(7538, 1, '2026-10-08', 10, 'MO-B010', 'eefdb81d2541d89d51cffa16aa8e66a8', 2, 'Business concerns', 'Gerardo Torres', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-08 09:55:01', '2026-10-08 10:02:30', '2026-10-08 10:02:30', '2026-10-08 10:04:46', 1),
(7539, 1, '2026-10-08', 11, 'MO-A011', '2a612555555a9770e627db32295aee34', 1, 'Mayor\'s Clearance', 'Liza Dela Cruz', NULL, NULL, NULL, 'self', 'skipped', 56, 'Angelu', 0, '2026-10-08 09:59:45', '2026-10-08 10:02:18', NULL, NULL, 1),
(7540, 1, '2026-10-08', 12, 'MO-F012', 'e6719e7dcb55e3311234a668666c36b1', 6, 'Other', 'Antonio Pascual', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-08 10:11:05', '2026-10-08 10:19:59', '2026-10-08 10:19:59', '2026-10-08 10:30:12', 1),
(7541, 1, '2026-10-08', 13, 'MO-C013', 'd3ab4bac56438fc2c3d7f679bda5c35d', 3, 'Personal concerns', 'Elena Reyes', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-08 10:20:56', '2026-10-08 10:33:12', '2026-10-08 10:33:12', '2026-10-08 10:38:49', 1),
(7542, 1, '2026-10-08', 14, 'MO-A014', '454639321b0ef330d8dfde103e92b962', 1, 'Mayor\'s Clearance', 'Pedro Ramos', NULL, NULL, NULL, 'staff', 'done', 54, 'Jean Frias', 0, '2026-10-08 10:29:07', '2026-10-08 10:42:41', '2026-10-08 10:42:41', '2026-10-08 10:46:22', 1),
(7543, 1, '2026-10-08', 15, 'MO-C015', '50ce9273530e0bb3c2c8e11247a63ec9', 3, 'Personal concerns', 'Marites Santos', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-08 10:34:39', '2026-10-08 10:40:53', '2026-10-08 10:40:53', '2026-10-08 10:57:35', 1),
(7544, 1, '2026-10-08', 16, 'MO-B016', 'fa948b9f9622461eccf65d2246b60208', 2, 'Business concerns', 'Alfredo Castillo', NULL, NULL, NULL, 'staff', 'done', 55, 'Sir Henjie', 0, '2026-10-08 10:36:27', '2026-10-08 10:39:49', '2026-10-08 10:39:49', '2026-10-08 10:51:38', 1),
(7545, 1, '2026-10-08', 17, 'MO-C017', 'c4935954c43c9d41d43b0476b684a4eb', 3, 'Personal concerns', 'Maria Cortez', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-08 10:56:05', '2026-10-08 11:02:57', '2026-10-08 11:02:57', '2026-10-08 11:13:37', 1),
(7546, 1, '2026-10-08', 18, 'MO-A018', '1f357de948bbbd66d29111603fad982e', 1, 'Mayor\'s Clearance', 'Eduardo Domingo', NULL, NULL, NULL, 'staff', 'done', 55, 'Sir Henjie', 0, '2026-10-08 10:56:12', '2026-10-08 11:03:58', '2026-10-08 11:03:58', '2026-10-08 11:08:00', 1),
(7547, 1, '2026-10-08', 19, 'MO-A019', '1be85372a66e7b5a0c97b67b66d930f3', 1, 'Mayor\'s Clearance', 'Rosario Mercado', NULL, NULL, NULL, 'staff', 'done', 54, 'Jean Frias', 0, '2026-10-08 10:56:47', '2026-10-08 11:00:53', '2026-10-08 11:00:53', '2026-10-08 11:07:25', 1),
(7548, 1, '2026-10-08', 20, 'MO-B020', '37b6bc366e3532b910590bfed7eb22b8', 2, 'Business concerns', 'Ricardo Navarro', NULL, NULL, NULL, 'staff', 'done', 54, 'Jean Frias', 0, '2026-10-08 10:57:12', '2026-10-08 10:59:53', '2026-10-08 10:59:53', '2026-10-08 11:07:26', 1),
(7549, 1, '2026-10-08', 21, 'MO-D021', '5815b184c9ef059f4db0ed5a6b5c8859', 4, 'Courtesy call', 'Carmela Salazar', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-08 11:01:51', '2026-10-08 11:44:52', '2026-10-08 11:44:52', '2026-10-08 12:02:06', 1),
(7550, 1, '2026-10-08', 22, 'MO-A022', 'c02ba780b24e1cf9d79044b357a0288a', 1, 'Mayor\'s Clearance', 'Felipe Aquino', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-08 11:04:55', '2026-10-08 11:06:29', '2026-10-08 11:06:29', '2026-10-08 11:10:16', 1),
(7551, 1, '2026-10-08', 23, 'MO-C023', '339cab9e42fbd6822c4d56b8bf86f64f', 3, 'Personal concerns', 'Cynthia Fernandez', NULL, NULL, NULL, 'self', 'skipped', 55, 'Sir Henjie', 0, '2026-10-08 11:11:00', '2026-10-08 11:21:30', NULL, NULL, 1),
(7552, 1, '2026-10-08', 24, 'MO-F024', '5e46baa3f32cfed4ebffdc03dde50b40', 6, 'Other', 'Jose Cruz', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-08 11:15:12', '2026-10-08 11:18:33', '2026-10-08 11:18:33', '2026-10-08 11:23:08', 1),
(7553, 1, '2026-10-08', 25, 'MO-A025', 'c68a8802d23aa0c510d83826e0e293c2', 1, 'Mayor\'s Clearance', 'Teresita Gonzales', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-08 11:18:24', '2026-10-08 11:28:46', '2026-10-08 11:28:46', '2026-10-08 11:30:16', 1),
(7554, 1, '2026-10-08', 26, 'MO-C026', 'c0dcdc9422c13bac735f64ba709519c1', 3, 'Personal concerns', 'Danilo Mendoza', NULL, NULL, NULL, 'staff', 'done', 55, 'Sir Henjie', 0, '2026-10-08 11:18:30', '2026-10-08 11:22:30', '2026-10-08 11:22:30', '2026-10-08 11:42:08', 1),
(7555, 1, '2026-10-08', 27, 'MO-E027', '37b4b76510e02be4a0ce4326d01de8c2', 5, 'Financial assistance', 'Lourdes Rivera', NULL, NULL, NULL, 'self', 'skipped', 54, 'Jean Frias', 0, '2026-10-08 11:22:27', '2026-10-08 11:33:14', NULL, NULL, 1),
(7556, 1, '2026-10-08', 28, 'MO-E028', '93b9ebb4ae23b0e205896d7e5f98e321', 5, 'Financial assistance', 'Ramon Villanueva', NULL, NULL, NULL, 'staff', 'done', 55, 'Sir Henjie', 0, '2026-10-08 11:29:17', '2026-10-08 11:29:47', '2026-10-08 11:29:47', '2026-10-08 11:37:20', 1),
(7557, 1, '2026-10-08', 29, 'MO-F029', '5f3f230ce0d48703ce93f90330e2b921', 6, 'Other', 'Josefina Lopez', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-08 11:37:01', '2026-10-08 11:42:16', '2026-10-08 11:42:16', '2026-10-08 11:46:51', 1),
(7558, 1, '2026-10-08', 30, 'MO-C030', '9edb456f0256ddc0c0594e1c920b6681', 3, 'Personal concerns', 'Manuel Garcia', NULL, NULL, NULL, 'self', 'skipped', 55, 'Sir Henjie', 0, '2026-10-08 11:42:16', '2026-10-08 12:06:15', NULL, NULL, 1),
(7559, 1, '2026-10-08', 31, 'MO-E031', 'f85831c075ced61c4692f13bcd28e1ef', 5, 'Financial assistance', 'Ana Flores', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-08 11:45:21', '2026-10-08 11:50:41', '2026-10-08 11:50:41', '2026-10-08 12:00:15', 1),
(7560, 1, '2026-10-08', 32, 'MO-A032', '4e6b7bf479cc6a4986d9923292ab6e98', 1, 'Mayor\'s Clearance', 'Rogelio Bautista', NULL, NULL, NULL, 'staff', 'done', 55, 'Sir Henjie', 0, '2026-10-08 11:48:48', '2026-10-08 11:56:00', '2026-10-08 11:56:00', '2026-10-08 11:59:38', 1),
(7561, 1, '2026-10-08', 33, 'MO-E033', '82ae965781143b9bcf97f1471c29ad49', 5, 'Financial assistance', 'Grace Torres', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-08 11:49:22', '2026-10-08 12:07:24', '2026-10-08 12:07:24', '2026-10-08 12:24:20', 1),
(7562, 1, '2026-10-08', 34, 'MO-A034', '9cab6f338cb94425b2cbf75faa1415d0', 1, 'Mayor\'s Clearance', 'Gerardo Dela Cruz', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-08 11:53:29', '2026-10-08 11:59:58', '2026-10-08 11:59:58', '2026-10-08 12:09:50', 1),
(7563, 1, '2026-10-08', 35, 'MO-E035', '49cd2117361f0893cf99b7ed260a3696', 5, 'Financial assistance', 'Liza Pascual', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-08 11:56:07', '2026-10-08 11:59:32', '2026-10-08 11:59:32', '2026-10-08 12:06:04', 1),
(7564, 1, '2026-10-08', 36, 'MO-F036', 'bd534946b8136dba57e7ebc0bb4217dd', 6, 'Other', 'Antonio Reyes', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-08 11:57:46', '2026-10-08 12:03:56', '2026-10-08 12:03:56', '2026-10-08 12:09:37', 1),
(7565, 1, '2026-10-08', 37, 'MO-E037', 'd78b75c313594eb0ba51ac645627c9c9', 5, 'Financial assistance', 'Elena Ramos', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-08 12:19:42', '2026-10-08 12:23:37', '2026-10-08 12:23:37', '2026-10-08 12:31:50', 1),
(7566, 1, '2026-10-08', 38, 'MO-C038', 'ddaa430e11b77c8a2f17af0acaa5f45c', 3, 'Personal concerns', 'Pedro Santos', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-08 12:51:40', '2026-10-08 12:54:43', '2026-10-08 12:54:43', '2026-10-08 12:58:52', 1),
(7567, 1, '2026-10-08', 39, 'MO-B039', '7d65bb94bdb2c075d0669e87203966e7', 2, 'Business concerns', 'Marites Castillo', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-08 13:06:27', '2026-10-08 13:10:50', '2026-10-08 13:10:50', '2026-10-08 13:31:21', 1),
(7568, 1, '2026-10-08', 40, 'MO-F040', '609d88d34a6820258a7e4e0bb78b14c0', 6, 'Other', 'Alfredo Cortez', NULL, NULL, NULL, 'staff', 'done', 56, 'Angelu', 0, '2026-10-08 13:15:08', '2026-10-08 13:35:44', '2026-10-08 13:35:44', '2026-10-08 13:40:34', 1),
(7569, 1, '2026-10-08', 41, 'MO-A041', '1dad693ef61d7e0c625f1f30a85e2ce0', 1, 'Mayor\'s Clearance', 'Maria Domingo', NULL, NULL, NULL, 'staff', 'done', 54, 'Jean Frias', 0, '2026-10-08 13:25:14', '2026-10-08 13:28:35', '2026-10-08 13:28:35', '2026-10-08 13:31:40', 1),
(7570, 1, '2026-10-08', 42, 'MO-C042', 'd0ec86a720b2513a0cfc7d8b2d1cfa98', 3, 'Personal concerns', 'Eduardo Mercado', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-08 13:30:59', '2026-10-08 13:40:23', '2026-10-08 13:40:23', '2026-10-08 13:43:03', 1),
(7571, 1, '2026-10-08', 43, 'MO-A043', '5bbca341fa7b60b1db763b25ea6b4d2e', 1, 'Mayor\'s Clearance', 'Rosario Navarro', NULL, NULL, NULL, 'self', 'skipped', 55, 'Sir Henjie', 0, '2026-10-08 13:32:57', '2026-10-08 13:40:09', NULL, NULL, 1),
(7572, 1, '2026-10-08', 44, 'MO-A044', 'cbbfa89f375d9e50c18491a6c3c4489f', 1, 'Mayor\'s Clearance', 'Ricardo Salazar', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-08 13:34:36', '2026-10-08 13:41:53', '2026-10-08 13:41:53', '2026-10-08 13:45:33', 1),
(7573, 1, '2026-10-08', 45, 'MO-B045', '8a0f3996e9aace936b6782ef5262605c', 2, 'Business concerns', 'Carmela Aquino', NULL, NULL, NULL, 'staff', 'done', 55, 'Sir Henjie', 0, '2026-10-08 13:41:05', '2026-10-08 13:51:51', '2026-10-08 13:51:51', '2026-10-08 14:04:20', 1),
(7574, 1, '2026-10-08', 46, 'MO-C046', '6c2a4e8c0f3919f904456320cdd31bc4', 3, 'Personal concerns', 'Felipe Fernandez', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-08 13:54:30', '2026-10-08 14:07:24', '2026-10-08 14:07:24', '2026-10-08 14:25:23', 1),
(7575, 1, '2026-10-08', 47, 'MO-E047', '02e7a7dd50477a2929c24514cc951652', 5, 'Financial assistance', 'Cynthia Cruz', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-08 13:57:11', '2026-10-08 13:59:32', '2026-10-08 13:59:32', '2026-10-08 14:09:59', 1),
(7576, 1, '2026-10-08', 48, 'MO-A048', 'e2ede31844f0d16bd4ddcc5267c3416f', 1, 'Mayor\'s Clearance', 'Jose Gonzales', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-08 14:00:50', '2026-10-08 14:10:46', '2026-10-08 14:10:46', '2026-10-08 14:17:05', 1),
(7577, 1, '2026-10-08', 49, 'MO-B049', 'a9192137014459ad0fe1bcc7a0bb0185', 2, 'Business concerns', 'Teresita Mendoza', NULL, NULL, NULL, 'staff', 'done', 54, 'Jean Frias', 0, '2026-10-08 14:15:09', '2026-10-08 14:19:42', '2026-10-08 14:19:42', '2026-10-08 14:22:59', 1),
(7578, 1, '2026-10-08', 50, 'MO-A050', '5d557552dcb3b35aace6e76d6c65f648', 1, 'Mayor\'s Clearance', 'Danilo Rivera', NULL, NULL, NULL, 'self', 'skipped', 56, 'Angelu', 0, '2026-10-08 14:40:27', '2026-10-08 14:56:26', NULL, NULL, 1),
(7579, 1, '2026-10-08', 51, 'MO-C051', 'eae73f3c9b96d41ea42def36def9bef5', 3, 'Personal concerns', 'Lourdes Villanueva', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-08 14:42:26', '2026-10-08 14:58:35', '2026-10-08 14:58:35', '2026-10-08 15:13:16', 1),
(7580, 1, '2026-10-08', 52, 'MO-A052', '4ceed7d5a66f4d9ede0ebecc9f613cf7', 1, 'Mayor\'s Clearance', 'Ramon Lopez', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-08 14:43:30', '2026-10-08 14:45:48', '2026-10-08 14:45:48', '2026-10-08 14:51:19', 1),
(7581, 1, '2026-10-08', 53, 'MO-B053', '66ee24164ee74252c90a96522a4ae202', 2, 'Business concerns', 'Josefina Garcia', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-08 14:52:38', '2026-10-08 14:54:47', '2026-10-08 14:54:47', '2026-10-08 15:35:11', 1),
(7582, 1, '2026-10-08', 54, 'MO-B054', 'f19fbaed10285c030238a6919193be12', 2, 'Business concerns', 'Manuel Flores', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-08 14:53:54', '2026-10-08 15:01:56', '2026-10-08 15:01:56', '2026-10-08 15:10:30', 1),
(7583, 1, '2026-10-08', 55, 'MO-C055', 'e081114fd5a4a2906b9f8f63c517547d', 3, 'Personal concerns', 'Ana Bautista', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-08 14:56:01', '2026-10-08 14:57:12', '2026-10-08 14:57:12', '2026-10-08 15:02:02', 1),
(7584, 1, '2026-10-08', 56, 'MO-B056', '8b10af4b554fe9a24312af553eaee8e4', 2, 'Business concerns', 'Rogelio Torres', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-08 14:59:22', '2026-10-08 15:13:28', '2026-10-08 15:13:28', '2026-10-08 15:24:43', 1),
(7585, 1, '2026-10-08', 57, 'MO-E057', '65c6d54b6af52dfa57ca57275574ef9d', 5, 'Financial assistance', 'Grace Dela Cruz', NULL, NULL, NULL, 'staff', 'done', 55, 'Sir Henjie', 0, '2026-10-08 15:13:34', '2026-10-08 15:15:20', '2026-10-08 15:15:20', '2026-10-08 15:21:15', 1),
(7586, 1, '2026-10-08', 58, 'MO-A058', '1e0a95cdecb37f987723e678864f2dca', 1, 'Mayor\'s Clearance', 'Gerardo Pascual', NULL, NULL, NULL, 'staff', 'done', 55, 'Sir Henjie', 0, '2026-10-08 15:17:37', '2026-10-08 15:20:20', '2026-10-08 15:20:20', '2026-10-08 15:24:42', 1),
(7587, 1, '2026-10-08', 59, 'MO-E059', '1b57e1f78628b357a4c0ea7b52de5183', 5, 'Financial assistance', 'Liza Reyes', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-08 15:24:45', '2026-10-08 15:29:10', '2026-10-08 15:29:10', '2026-10-08 15:40:36', 1),
(7588, 1, '2026-10-08', 60, 'MO-A060', 'a03d108b577dd5982dd24209306b8184', 1, 'Mayor\'s Clearance', 'Antonio Ramos', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-08 15:34:09', '2026-10-08 15:36:15', '2026-10-08 15:36:15', '2026-10-08 15:45:09', 1),
(7589, 1, '2026-10-08', 61, 'MO-A061', '6909a7e0f1b09c50863bf1c8da660bd9', 1, 'Mayor\'s Clearance', 'Elena Santos', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-08 15:52:27', '2026-10-08 16:00:27', '2026-10-08 16:00:27', '2026-10-08 16:02:20', 1),
(7590, 1, '2026-10-08', 62, 'MO-A062', 'e8404ab02883cf40d1fde1a2e8d81302', 1, 'Mayor\'s Clearance', 'Pedro Castillo', NULL, NULL, NULL, 'staff', 'cancelled', NULL, NULL, 0, '2026-10-08 16:37:36', NULL, NULL, NULL, 1),
(7591, 1, '2026-10-09', 1, 'MO-C001', '1cfe45ebd5c033d5e3e9bf3863e5e6ab', 3, 'Personal concerns', 'Rogelio Cruz', NULL, NULL, NULL, 'self', 'cancelled', NULL, NULL, 0, '2026-10-09 08:00:55', NULL, NULL, NULL, 1),
(7592, 1, '2026-10-09', 2, 'MO-E002', '291728581b5729e9a53a9bdc3f6881f2', 5, 'Financial assistance', 'Grace Gonzales', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-09 08:03:28', '2026-10-09 08:12:09', '2026-10-09 08:12:09', '2026-10-09 08:24:05', 1),
(7593, 1, '2026-10-09', 3, 'MO-B003', 'a40e38a55c823af0c6d04d3acdf7fcf6', 2, 'Business concerns', 'Gerardo Mendoza', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-09 08:22:05', '2026-10-09 08:25:41', '2026-10-09 08:25:41', '2026-10-09 08:35:22', 1),
(7594, 1, '2026-10-09', 4, 'MO-F004', '98e4db8b9315a2663855a5eb3de61974', 6, 'Other', 'Liza Rivera', NULL, NULL, NULL, 'staff', 'done', 56, 'Angelu', 0, '2026-10-09 08:26:34', '2026-10-09 08:34:17', '2026-10-09 08:34:17', '2026-10-09 08:38:57', 1),
(7595, 1, '2026-10-09', 5, 'MO-E005', '802aedfead5dd521820c6e44521ef098', 5, 'Financial assistance', 'Antonio Villanueva', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-09 08:28:20', '2026-10-09 08:34:51', '2026-10-09 08:34:51', '2026-10-09 08:50:03', 1),
(7596, 1, '2026-10-09', 6, 'MO-C006', '3abd06153e9a8c2955c8854d9f9d6cf2', 3, 'Personal concerns', 'Elena Lopez', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-09 08:48:24', '2026-10-09 08:55:47', '2026-10-09 08:55:47', '2026-10-09 09:13:04', 1),
(7597, 1, '2026-10-09', 7, 'MO-A007', '6af5aebbdf915b4bb9af7327cb49e093', 1, 'Mayor\'s Clearance', 'Pedro Garcia', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-09 08:58:18', '2026-10-09 09:04:20', '2026-10-09 09:04:20', '2026-10-09 09:10:43', 1),
(7598, 1, '2026-10-09', 8, 'MO-E008', '603d1cb68577e91bc297e18d453f41bf', 5, 'Financial assistance', 'Marites Flores', NULL, NULL, NULL, 'staff', 'done', 55, 'Sir Henjie', 0, '2026-10-09 09:12:51', '2026-10-09 09:28:13', '2026-10-09 09:28:13', '2026-10-09 09:38:48', 1),
(7599, 1, '2026-10-09', 9, 'MO-A009', '144b7345a947ca28f0ea3400ef313f06', 1, 'Mayor\'s Clearance', 'Alfredo Bautista', NULL, NULL, NULL, 'staff', 'done', 56, 'Angelu', 0, '2026-10-09 09:20:56', '2026-10-09 09:31:13', '2026-10-09 09:31:13', '2026-10-09 09:36:07', 1),
(7600, 1, '2026-10-09', 10, 'MO-A010', '7f0a81eff9821a797ae1a87c9f692066', 1, 'Mayor\'s Clearance', 'Maria Torres', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-09 09:22:12', '2026-10-09 09:27:21', '2026-10-09 09:27:21', '2026-10-09 09:28:51', 1),
(7601, 1, '2026-10-09', 11, 'MO-F011', '28b9b8eee019f461e4bc987feb2ff651', 6, 'Other', 'Eduardo Dela Cruz', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-09 09:26:51', '2026-10-09 09:29:44', '2026-10-09 09:29:44', '2026-10-09 09:32:35', 1),
(7602, 1, '2026-10-09', 12, 'MO-A012', '3d1c1dee46764e9ba64532122e845031', 1, 'Mayor\'s Clearance', 'Rosario Pascual', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-09 09:32:38', '2026-10-09 09:40:41', '2026-10-09 09:40:41', '2026-10-09 09:51:27', 1),
(7603, 1, '2026-10-09', 13, 'MO-A013', 'c67ad81c26b02342fddd2a4537cf6830', 1, 'Mayor\'s Clearance', 'Ricardo Reyes', NULL, NULL, NULL, 'staff', 'done', 55, 'Sir Henjie', 0, '2026-10-09 09:36:36', '2026-10-09 09:37:44', '2026-10-09 09:37:44', '2026-10-09 09:39:14', 1),
(7604, 1, '2026-10-09', 14, 'MO-B014', 'cd45649d79285e6efe44d4257b888dd6', 2, 'Business concerns', 'Carmela Ramos', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-09 09:40:27', '2026-10-09 09:52:59', '2026-10-09 09:52:59', '2026-10-09 09:59:28', 1),
(7605, 1, '2026-10-09', 15, 'MO-E015', '4525964f2936d2ce4f0a6d9e0235e1e7', 5, 'Financial assistance', 'Felipe Santos', NULL, NULL, NULL, 'staff', 'done', 56, 'Angelu', 0, '2026-10-09 09:44:22', '2026-10-09 09:51:37', '2026-10-09 09:51:37', '2026-10-09 09:57:36', 1),
(7606, 1, '2026-10-09', 16, 'MO-D016', '77a8d47b5bae48af2e6f1c1a4cb76b49', 4, 'Courtesy call', 'Cynthia Castillo', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-09 09:59:58', '2026-10-09 10:17:41', '2026-10-09 10:17:41', '2026-10-09 11:02:41', 1),
(7607, 1, '2026-10-09', 17, 'MO-C017', '48c1e56ec2ebe5e9803ef8727ec203ee', 3, 'Personal concerns', 'Jose Cortez', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-09 10:00:00', '2026-10-09 10:15:59', '2026-10-09 10:15:59', '2026-10-09 10:20:10', 1),
(7608, 1, '2026-10-09', 18, 'MO-B018', '380b5e5cfd8208c7f46ea0452f348afd', 2, 'Business concerns', 'Teresita Domingo', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-09 10:02:35', '2026-10-09 10:08:35', '2026-10-09 10:08:35', '2026-10-09 10:17:42', 1),
(7609, 1, '2026-10-09', 19, 'MO-F019', 'd558968bebd9c484909f450cbf74c975', 6, 'Other', 'Danilo Mercado', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-09 10:07:52', '2026-10-09 10:13:15', '2026-10-09 10:13:15', '2026-10-09 10:21:32', 1),
(7610, 1, '2026-10-09', 20, 'MO-A020', 'dc5c782a7a2fdc945b033ba8f476edfd', 1, 'Mayor\'s Clearance', 'Lourdes Navarro', NULL, NULL, NULL, 'staff', 'done', 54, 'Jean Frias', 0, '2026-10-09 10:09:54', '2026-10-09 10:19:24', '2026-10-09 10:19:24', '2026-10-09 10:23:55', 1),
(7611, 1, '2026-10-09', 21, 'MO-E021', '76a55c5c287e644fa9e5fb19b58fbe12', 5, 'Financial assistance', 'Ramon Salazar', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-09 10:10:57', '2026-10-09 10:21:19', '2026-10-09 10:21:19', '2026-10-09 10:24:59', 1),
(7612, 1, '2026-10-09', 22, 'MO-D022', 'bebf26f0fd9b0c011e8f5aa4c8826cde', 4, 'Courtesy call', 'Josefina Aquino', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-09 10:16:27', '2026-10-09 10:19:46', '2026-10-09 10:19:46', '2026-10-09 10:41:11', 1),
(7613, 1, '2026-10-09', 23, 'MO-E023', '532cafc796557ad2113212c3ea3a2c5c', 5, 'Financial assistance', 'Manuel Fernandez', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-09 10:17:17', '2026-10-09 10:26:45', '2026-10-09 10:26:45', '2026-10-09 10:35:53', 1),
(7614, 1, '2026-10-09', 24, 'MO-A024', '82dc492a81ccd4758181c1f3f11d8bf8', 1, 'Mayor\'s Clearance', 'Ana Cruz', NULL, NULL, NULL, 'staff', 'done', 54, 'Jean Frias', 0, '2026-10-09 10:17:30', '2026-10-09 10:21:11', '2026-10-09 10:21:11', '2026-10-09 10:28:35', 1),
(7615, 1, '2026-10-09', 25, 'MO-E025', '070fd66872703347ab5ceb88b6c1d98b', 5, 'Financial assistance', 'Rogelio Gonzales', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-09 10:19:01', '2026-10-09 10:26:59', '2026-10-09 10:26:59', '2026-10-09 10:33:22', 1),
(7616, 1, '2026-10-09', 26, 'MO-C026', 'aea3d1a905dc50b42f205c7d6d13c43f', 3, 'Personal concerns', 'Grace Mendoza', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-09 10:23:53', '2026-10-09 10:26:55', '2026-10-09 10:26:55', '2026-10-09 10:31:21', 1),
(7617, 1, '2026-10-09', 27, 'MO-E027', '521e976a7830c96c02650ac7289ced07', 5, 'Financial assistance', 'Gerardo Rivera', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-09 10:37:38', '2026-10-09 10:47:33', '2026-10-09 10:47:33', '2026-10-09 10:49:52', 1),
(7618, 1, '2026-10-09', 28, 'MO-A028', '5d733eea67276fe29f2272b8e6cd5de8', 1, 'Mayor\'s Clearance', 'Liza Villanueva', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-09 10:37:40', '2026-10-09 10:43:21', '2026-10-09 10:43:21', '2026-10-09 10:50:39', 1),
(7619, 1, '2026-10-09', 29, 'MO-C029', 'ee5a1aae58fbdac213c7013c82c09db5', 3, 'Personal concerns', 'Antonio Lopez', NULL, NULL, NULL, 'staff', 'done', 56, 'Angelu', 0, '2026-10-09 10:52:01', '2026-10-09 11:04:08', '2026-10-09 11:04:08', '2026-10-09 11:13:49', 1),
(7620, 1, '2026-10-09', 30, 'MO-A030', '0a1d2bc1ecfbec17b323e085e547a785', 1, 'Mayor\'s Clearance', 'Elena Garcia', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-09 10:52:13', '2026-10-09 10:53:30', '2026-10-09 10:53:30', '2026-10-09 11:00:16', 1),
(7621, 1, '2026-10-09', 31, 'MO-B031', 'b56fb4e32d35a6bd86c319cc13cd7501', 2, 'Business concerns', 'Pedro Flores', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-09 10:53:24', '2026-10-09 10:56:10', '2026-10-09 10:56:10', '2026-10-09 11:13:13', 1),
(7622, 1, '2026-10-09', 32, 'MO-C032', 'c353c50b6f99469918d90e764bfb1f6e', 3, 'Personal concerns', 'Marites Bautista', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-09 10:54:33', '2026-10-09 11:07:56', '2026-10-09 11:07:56', '2026-10-09 11:19:23', 1),
(7623, 1, '2026-10-09', 33, 'MO-F033', '7f43ceba4bf3b0f99ddc429d11cf14e1', 6, 'Other', 'Alfredo Torres', NULL, NULL, NULL, 'self', 'skipped', 56, 'Angelu', 0, '2026-10-09 10:56:38', '2026-10-09 10:58:36', NULL, NULL, 1),
(7624, 1, '2026-10-09', 34, 'MO-C034', 'd6c628a5e2fbdb9e1768ba43bf2cea2c', 3, 'Personal concerns', 'Maria Dela Cruz', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-09 11:06:40', '2026-10-09 11:09:37', '2026-10-09 11:09:37', '2026-10-09 11:22:48', 1),
(7625, 1, '2026-10-09', 35, 'MO-F035', 'dbd37c61fe95308da959c13f614a345c', 6, 'Other', 'Eduardo Pascual', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-09 11:07:04', '2026-10-09 11:11:52', '2026-10-09 11:11:52', '2026-10-09 11:17:10', 1),
(7626, 1, '2026-10-09', 36, 'MO-A036', '661c6a91f74c954487cc76d5f7cbc96b', 1, 'Mayor\'s Clearance', 'Rosario Reyes', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-09 11:08:12', '2026-10-09 11:12:45', '2026-10-09 11:12:45', '2026-10-09 11:24:19', 1),
(7627, 1, '2026-10-09', 37, 'MO-D037', '9fa9be2a7f3ae4213558fe2e59f8f089', 4, 'Courtesy call', 'Ricardo Ramos', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-09 11:08:30', '2026-10-09 11:12:44', '2026-10-09 11:12:44', '2026-10-09 11:44:14', 1),
(7628, 1, '2026-10-09', 38, 'MO-E038', 'fb0ddaf04b4eb4466c2ce29004927fab', 5, 'Financial assistance', 'Carmela Santos', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-09 11:27:51', '2026-10-09 11:33:53', '2026-10-09 11:33:53', '2026-10-09 11:41:36', 1),
(7629, 1, '2026-10-09', 39, 'MO-C039', '4225eb1f654f9be749d6cae9b412ab33', 3, 'Personal concerns', 'Felipe Castillo', NULL, NULL, NULL, 'staff', 'done', 54, 'Jean Frias', 0, '2026-10-09 11:29:15', '2026-10-09 11:33:58', '2026-10-09 11:33:58', '2026-10-09 11:40:00', 1),
(7630, 1, '2026-10-09', 40, 'MO-A040', '6eccbdf4f40c837a02d60194fc2dad59', 1, 'Mayor\'s Clearance', 'Cynthia Cortez', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-09 11:54:11', '2026-10-09 11:57:08', '2026-10-09 11:57:08', '2026-10-09 12:00:21', 1),
(7631, 1, '2026-10-09', 41, 'MO-D041', '5715cb50a6c0fb5d153cdccea181b0df', 4, 'Courtesy call', 'Jose Domingo', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-09 12:13:51', '2026-10-09 12:17:04', '2026-10-09 12:17:04', '2026-10-09 12:57:37', 1),
(7632, 1, '2026-10-09', 42, 'MO-C042', '70cb5ce176d4eb6fc67ef8a5db0b555f', 3, 'Personal concerns', 'Teresita Mercado', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-09 12:21:00', '2026-10-09 12:25:03', '2026-10-09 12:25:03', '2026-10-09 12:41:12', 1),
(7633, 1, '2026-10-09', 43, 'MO-A043', '2bfb2b93f4d9909efefab49193718ebc', 1, 'Mayor\'s Clearance', 'Danilo Navarro', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-09 13:03:06', '2026-10-09 13:06:59', '2026-10-09 13:06:59', '2026-10-09 13:09:25', 1),
(7634, 1, '2026-10-09', 44, 'MO-A044', '51b5c4da6e5d5f7fa24a5cddde85b598', 1, 'Mayor\'s Clearance', 'Lourdes Salazar', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-09 13:11:49', '2026-10-09 13:25:10', '2026-10-09 13:25:10', '2026-10-09 13:27:41', 1),
(7635, 1, '2026-10-09', 45, 'MO-A045', 'a6babb42bac5809bcb772a4d906a2b5b', 1, 'Mayor\'s Clearance', 'Ramon Aquino', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-09 13:17:45', '2026-10-09 13:24:02', '2026-10-09 13:24:02', '2026-10-09 13:29:07', 1),
(7636, 1, '2026-10-09', 46, 'MO-D046', 'cf249ac5771f25f6962f4241d60b8df8', 4, 'Courtesy call', 'Josefina Fernandez', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-09 13:35:57', '2026-10-09 13:42:32', '2026-10-09 13:42:32', '2026-10-09 13:56:04', 1),
(7637, 1, '2026-10-09', 47, 'MO-B047', '639f7e0c051bb3190d65b0ef9769cfc6', 2, 'Business concerns', 'Manuel Cruz', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-09 13:48:11', '2026-10-09 13:59:36', '2026-10-09 13:59:36', '2026-10-09 14:01:37', 1),
(7638, 1, '2026-10-09', 48, 'MO-E048', '180478fa9027665f22f61ce49d3374cd', 5, 'Financial assistance', 'Ana Gonzales', NULL, NULL, NULL, 'staff', 'done', 56, 'Angelu', 0, '2026-10-09 14:02:46', '2026-10-09 14:09:20', '2026-10-09 14:09:20', '2026-10-09 14:13:45', 1),
(7639, 1, '2026-10-09', 49, 'MO-A049', '88b70ad289b95a6239f9c362905f3421', 1, 'Mayor\'s Clearance', 'Rogelio Mendoza', NULL, NULL, NULL, 'staff', 'done', 55, 'Sir Henjie', 0, '2026-10-09 14:02:55', '2026-10-09 14:11:51', '2026-10-09 14:11:51', '2026-10-09 14:13:27', 1),
(7640, 1, '2026-10-09', 50, 'MO-A050', '4d7f3e576ca442d268fbb1a6952a0096', 1, 'Mayor\'s Clearance', 'Grace Rivera', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-09 14:17:07', '2026-10-09 14:23:58', '2026-10-09 14:23:58', '2026-10-09 14:33:25', 1),
(7641, 1, '2026-10-09', 51, 'MO-F051', '14b60dc367ad4bb9831927543d364376', 6, 'Other', 'Gerardo Villanueva', NULL, NULL, NULL, 'staff', 'done', 55, 'Sir Henjie', 0, '2026-10-09 14:32:08', '2026-10-09 14:42:57', '2026-10-09 14:42:57', '2026-10-09 14:50:02', 1),
(7642, 1, '2026-10-09', 52, 'MO-A052', '9c2d0c2ad2a537b18d12838b51e057e9', 1, 'Mayor\'s Clearance', 'Liza Lopez', NULL, NULL, NULL, 'staff', 'done', 55, 'Sir Henjie', 0, '2026-10-09 14:42:39', '2026-10-09 14:46:49', '2026-10-09 14:46:49', '2026-10-09 14:49:42', 1),
(7643, 1, '2026-10-09', 53, 'MO-E053', 'b3197db261693d0f44181ad85569f076', 5, 'Financial assistance', 'Antonio Garcia', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-09 14:55:14', '2026-10-09 14:57:52', '2026-10-09 14:57:52', '2026-10-09 15:07:55', 1),
(7644, 1, '2026-10-09', 54, 'MO-A054', '14e8a71540d9e87d80adddba8e15a500', 1, 'Mayor\'s Clearance', 'Elena Flores', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-09 14:59:04', '2026-10-09 15:18:12', '2026-10-09 15:18:12', '2026-10-09 15:29:19', 1),
(7645, 1, '2026-10-09', 55, 'MO-A055', '7ec7420be04759c9767a31e5f2246567', 1, 'Mayor\'s Clearance', 'Pedro Bautista', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-09 15:32:53', '2026-10-09 15:36:30', '2026-10-09 15:36:30', '2026-10-09 15:41:40', 1),
(7646, 1, '2026-10-09', 56, 'MO-A056', '42b640fa70e2f27a163b65402d0dc7a2', 1, 'Mayor\'s Clearance', 'Marites Torres', NULL, NULL, NULL, 'staff', 'done', 54, 'Jean Frias', 0, '2026-10-09 15:52:17', '2026-10-09 15:57:33', '2026-10-09 15:57:33', '2026-10-09 16:05:19', 1),
(7647, 1, '2026-10-09', 57, 'MO-E057', '66526cf0754236a5a8ee02ffeb0e4d5d', 5, 'Financial assistance', 'Alfredo Dela Cruz', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-09 15:55:58', '2026-10-09 15:57:10', '2026-10-09 15:57:10', '2026-10-09 16:04:55', 1),
(7648, 1, '2026-10-09', 58, 'MO-F058', '8e93973538ba768d4c3c0d379308d182', 6, 'Other', 'Maria Pascual', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-09 16:06:31', '2026-10-09 16:09:44', '2026-10-09 16:09:44', '2026-10-09 16:19:30', 1),
(7649, 1, '2026-10-09', 59, 'MO-F059', '373cc344c406b28042e582fc3dbcff29', 6, 'Other', 'Eduardo Reyes', NULL, NULL, NULL, 'self', 'skipped', 54, 'Jean Frias', 0, '2026-10-09 16:36:16', '2026-10-09 16:38:35', NULL, NULL, 1),
(7650, 1, '2026-10-09', 60, 'MO-E060', '6d65bb95aeea57deb10a49e60660b299', 5, 'Financial assistance', 'Rosario Ramos', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-09 16:57:19', '2026-10-09 16:58:04', '2026-10-09 16:58:04', '2026-10-09 17:07:03', 1),
(7651, 1, '2026-10-12', 1, 'MO-D001', '25b393331c25bea137df9d8bcb25daf7', 4, 'Courtesy call', 'Josefina Cruz', NULL, NULL, NULL, 'staff', 'skipped', 54, 'Jean Frias', 0, '2026-10-12 08:05:46', '2026-10-12 08:07:25', NULL, NULL, 1),
(7652, 1, '2026-10-12', 2, 'MO-A002', '336d2d0bc5857fd6531bede42b6c3453', 1, 'Mayor\'s Clearance', 'Manuel Gonzales', NULL, NULL, NULL, 'staff', 'done', 54, 'Jean Frias', 0, '2026-10-12 08:24:47', '2026-10-12 08:26:39', '2026-10-12 08:26:39', '2026-10-12 08:30:57', 1),
(7653, 1, '2026-10-12', 3, 'MO-A003', '50f68225c7274b053ded946f40a7c3d4', 1, 'Mayor\'s Clearance', 'Ana Mendoza', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-12 09:02:26', '2026-10-12 09:09:58', '2026-10-12 09:09:58', '2026-10-12 09:18:41', 1),
(7654, 1, '2026-10-12', 4, 'MO-E004', '2f499c92ef445d0205ec933579ba0f9d', 5, 'Financial assistance', 'Rogelio Rivera', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-12 09:08:18', '2026-10-12 09:12:34', '2026-10-12 09:12:34', '2026-10-12 09:19:15', 1),
(7655, 1, '2026-10-12', 5, 'MO-B005', '5dd9c55d114a72bd97a380aff4f6c308', 2, 'Business concerns', 'Grace Villanueva', NULL, NULL, NULL, 'staff', 'done', 55, 'Sir Henjie', 0, '2026-10-12 09:08:41', '2026-10-12 09:23:35', '2026-10-12 09:23:35', '2026-10-12 09:40:10', 1),
(7656, 1, '2026-10-12', 6, 'MO-A006', '5353ffd8be464060c0d64443be7ff3b4', 1, 'Mayor\'s Clearance', 'Gerardo Lopez', NULL, NULL, NULL, 'staff', 'skipped', 54, 'Jean Frias', 0, '2026-10-12 09:11:44', '2026-10-12 09:31:18', NULL, NULL, 1),
(7657, 1, '2026-10-12', 7, 'MO-E007', '2f78adbcdc82d8dd61ebc98ff78c758b', 5, 'Financial assistance', 'Liza Garcia', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-12 09:15:41', '2026-10-12 09:27:08', '2026-10-12 09:27:08', '2026-10-12 09:34:46', 1),
(7658, 1, '2026-10-12', 8, 'MO-A008', '653cbbdc9843c0b63d51bc9be909d58c', 1, 'Mayor\'s Clearance', 'Antonio Flores', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-12 09:22:42', '2026-10-12 09:25:37', '2026-10-12 09:25:37', '2026-10-12 09:37:11', 1),
(7659, 1, '2026-10-12', 9, 'MO-B009', 'd55bc7646aa9548537425d614668a00c', 2, 'Business concerns', 'Elena Bautista', NULL, NULL, NULL, 'self', 'skipped', 56, 'Angelu', 0, '2026-10-12 09:28:29', '2026-10-12 09:29:03', NULL, NULL, 1),
(7660, 1, '2026-10-12', 10, 'MO-A010', 'f546c92ac828c84f36ff73f8db97b6d7', 1, 'Mayor\'s Clearance', 'Pedro Torres', NULL, NULL, NULL, 'staff', 'done', 55, 'Sir Henjie', 0, '2026-10-12 09:33:07', '2026-10-12 09:37:37', '2026-10-12 09:37:37', '2026-10-12 09:43:20', 1),
(7661, 1, '2026-10-12', 11, 'MO-F011', '6d6279a3153ada06bf8e9929a64048d4', 6, 'Other', 'Marites Dela Cruz', NULL, NULL, NULL, 'staff', 'skipped', 55, 'Sir Henjie', 0, '2026-10-12 09:35:03', '2026-10-12 09:38:25', NULL, NULL, 1),
(7662, 1, '2026-10-12', 12, 'MO-C012', '12957f568b181fb6beed174e3de5197f', 3, 'Personal concerns', 'Alfredo Pascual', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-12 09:43:08', '2026-10-12 09:44:26', '2026-10-12 09:44:26', '2026-10-12 09:56:20', 1),
(7663, 1, '2026-10-12', 13, 'MO-D013', '90e0877d073fcaeac4d9a21d2a688326', 4, 'Courtesy call', 'Maria Reyes', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-12 09:45:46', '2026-10-12 09:54:56', '2026-10-12 09:54:56', '2026-10-12 10:07:58', 1),
(7664, 1, '2026-10-12', 14, 'MO-F014', '9a4f3a704cf810126ec4980e05bed69f', 6, 'Other', 'Eduardo Ramos', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-12 09:56:02', '2026-10-12 10:12:25', '2026-10-12 10:12:25', '2026-10-12 10:20:34', 1),
(7665, 1, '2026-10-12', 15, 'MO-A015', '5f0516ecb598feb451bf7038c8837fde', 1, 'Mayor\'s Clearance', 'Rosario Santos', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-12 09:56:59', '2026-10-12 09:57:45', '2026-10-12 09:57:45', '2026-10-12 10:07:49', 1),
(7666, 1, '2026-10-12', 16, 'MO-D016', '5e0144da0a6008429594e2a7d4e2900f', 4, 'Courtesy call', 'Ricardo Castillo', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-12 10:08:01', '2026-10-12 10:12:06', '2026-10-12 10:12:06', '2026-10-12 10:33:02', 1),
(7667, 1, '2026-10-12', 17, 'MO-A017', '575dc46a93ac61bdb23baa7fdc0c1c43', 1, 'Mayor\'s Clearance', 'Carmela Cortez', NULL, NULL, NULL, 'staff', 'done', 55, 'Sir Henjie', 0, '2026-10-12 10:14:06', '2026-10-12 10:15:13', '2026-10-12 10:15:13', '2026-10-12 10:17:39', 1),
(7668, 1, '2026-10-12', 18, 'MO-F018', 'cd7f3deba800f4ac225954ccfad622a1', 6, 'Other', 'Felipe Domingo', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-12 10:14:32', '2026-10-12 10:25:39', '2026-10-12 10:25:39', '2026-10-12 10:38:22', 1),
(7669, 1, '2026-10-12', 19, 'MO-A019', '29715bc7fe62ab49955a19751ff1b46b', 1, 'Mayor\'s Clearance', 'Cynthia Mercado', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-12 10:15:40', '2026-10-12 10:21:36', '2026-10-12 10:21:36', '2026-10-12 10:31:30', 1),
(7670, 1, '2026-10-12', 20, 'MO-F020', 'e36c64707916fd49752109ee10f1f0d7', 6, 'Other', 'Jose Navarro', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-12 10:15:48', '2026-10-12 10:16:58', '2026-10-12 10:16:58', '2026-10-12 10:25:20', 1),
(7671, 1, '2026-10-12', 21, 'MO-B021', '7bb4876f5d7858f0fec72b5182b0df65', 2, 'Business concerns', 'Teresita Salazar', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-12 10:17:40', '2026-10-12 10:31:49', '2026-10-12 10:31:49', '2026-10-12 10:38:09', 1),
(7672, 1, '2026-10-12', 22, 'MO-B022', 'e8d51aa7e17d1d6e80555345fd98cd27', 2, 'Business concerns', 'Danilo Aquino', NULL, NULL, NULL, 'self', 'cancelled', NULL, NULL, 0, '2026-10-12 10:17:47', NULL, NULL, NULL, 1),
(7673, 1, '2026-10-12', 23, 'MO-E023', '8aae5114517a54bd90a60eb299bb3be3', 5, 'Financial assistance', 'Lourdes Fernandez', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-12 10:18:24', '2026-10-12 10:22:38', '2026-10-12 10:22:38', '2026-10-12 10:24:24', 1),
(7674, 1, '2026-10-12', 24, 'MO-B024', 'f89d1d52fb3670f88a220b6ad47d29e4', 2, 'Business concerns', 'Ramon Cruz', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-12 10:23:31', '2026-10-12 10:25:34', '2026-10-12 10:25:34', '2026-10-12 10:39:57', 1),
(7675, 1, '2026-10-12', 25, 'MO-B025', '3f6c2caaf0afa4ce3756c3f430e7cde4', 2, 'Business concerns', 'Josefina Gonzales', NULL, NULL, NULL, 'self', 'skipped', 56, 'Angelu', 0, '2026-10-12 10:37:30', '2026-10-12 10:46:25', NULL, NULL, 1),
(7676, 1, '2026-10-12', 26, 'MO-A026', '42fd2e2c062222b99a284bc512594cd6', 1, 'Mayor\'s Clearance', 'Manuel Mendoza', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-12 10:41:53', '2026-10-12 10:49:55', '2026-10-12 10:49:55', '2026-10-12 10:57:36', 1),
(7677, 1, '2026-10-12', 27, 'MO-A027', 'd59e96e9c49e5e9dd30ee69f386d02aa', 1, 'Mayor\'s Clearance', 'Ana Rivera', NULL, NULL, NULL, 'staff', 'done', 55, 'Sir Henjie', 0, '2026-10-12 10:54:50', '2026-10-12 10:56:42', '2026-10-12 10:56:42', '2026-10-12 10:58:12', 1),
(7678, 1, '2026-10-12', 28, 'MO-A028', '54f175dc0cf88aa63f89b3d727486698', 1, 'Mayor\'s Clearance', 'Rogelio Villanueva', NULL, NULL, NULL, 'staff', 'done', 55, 'Sir Henjie', 0, '2026-10-12 10:55:24', '2026-10-12 11:04:54', '2026-10-12 11:04:54', '2026-10-12 11:09:54', 1),
(7679, 1, '2026-10-12', 29, 'MO-A029', '56a12fb6dbf255fdd47f2e08dd8594f7', 1, 'Mayor\'s Clearance', 'Grace Lopez', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-12 10:58:02', '2026-10-12 11:10:49', '2026-10-12 11:10:49', '2026-10-12 11:15:41', 1),
(7680, 1, '2026-10-12', 30, 'MO-F030', '848a3fdacb3749cfc191ad0716c59839', 6, 'Other', 'Gerardo Garcia', NULL, NULL, NULL, 'self', 'skipped', 54, 'Jean Frias', 0, '2026-10-12 10:59:17', '2026-10-12 11:18:45', NULL, NULL, 1),
(7681, 1, '2026-10-12', 31, 'MO-A031', '029f6c00e086e32d538544f7b9ffe057', 1, 'Mayor\'s Clearance', 'Liza Flores', NULL, NULL, NULL, 'self', 'skipped', 55, 'Sir Henjie', 0, '2026-10-12 11:00:52', '2026-10-12 11:02:10', NULL, NULL, 1),
(7682, 1, '2026-10-12', 32, 'MO-A032', '8d7853b099b6953f5c1b45a11d780cb4', 1, 'Mayor\'s Clearance', 'Antonio Bautista', NULL, NULL, NULL, 'self', 'skipped', 55, 'Sir Henjie', 0, '2026-10-12 11:02:48', '2026-10-12 11:04:19', NULL, NULL, 1),
(7683, 1, '2026-10-12', 33, 'MO-C033', 'be38c45e6562cc2365a77b68d50e283b', 3, 'Personal concerns', 'Elena Torres', NULL, NULL, NULL, 'staff', 'done', 55, 'Sir Henjie', 0, '2026-10-12 11:04:34', '2026-10-12 11:10:05', '2026-10-12 11:10:05', '2026-10-12 11:20:22', 1),
(7684, 1, '2026-10-12', 34, 'MO-E034', '81dbdd84d14bbed0ff8b2cfe25616d27', 5, 'Financial assistance', 'Pedro Dela Cruz', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-12 11:07:10', '2026-10-12 11:09:54', '2026-10-12 11:09:54', '2026-10-12 11:20:25', 1),
(7685, 1, '2026-10-12', 35, 'MO-A035', '860b6afdda10ecada4316823ed1be370', 1, 'Mayor\'s Clearance', 'Marites Pascual', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-12 11:07:37', '2026-10-12 11:13:07', '2026-10-12 11:13:07', '2026-10-12 11:21:07', 1),
(7686, 1, '2026-10-12', 36, 'MO-E036', '15e853a1c4545cabbba4ab880c9d9125', 5, 'Financial assistance', 'Alfredo Reyes', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-12 11:11:27', '2026-10-12 11:13:12', '2026-10-12 11:13:12', '2026-10-12 11:19:07', 1),
(7687, 1, '2026-10-12', 37, 'MO-C037', '606615ecc9e95e321bad29802ceb49c0', 3, 'Personal concerns', 'Maria Ramos', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-12 11:21:04', '2026-10-12 11:24:44', '2026-10-12 11:24:44', '2026-10-12 11:29:34', 1),
(7688, 1, '2026-10-12', 38, 'MO-C038', 'ea470f110ae9141c8c7826709582ad15', 3, 'Personal concerns', 'Eduardo Santos', NULL, NULL, NULL, 'staff', 'cancelled', NULL, NULL, 0, '2026-10-12 11:23:31', NULL, NULL, NULL, 1),
(7689, 1, '2026-10-12', 39, 'MO-D039', '3f4ddcb8523747f59716c32d734d747a', 4, 'Courtesy call', 'Rosario Castillo', NULL, NULL, NULL, 'staff', 'done', 56, 'Angelu', 0, '2026-10-12 11:27:56', '2026-10-12 11:33:05', '2026-10-12 11:33:05', '2026-10-12 11:45:21', 1);
INSERT INTO `queue_tickets` (`id`, `queue_id`, `queue_day`, `number`, `code`, `access_key`, `service_id`, `service_name`, `full_name`, `mobile`, `phone_key`, `user_id`, `source`, `status`, `staff_id`, `staff_name`, `is_priority`, `created_at`, `called_at`, `started_at`, `finished_at`, `is_demo`) VALUES
(7690, 1, '2026-10-12', 40, 'MO-A040', 'ffe55baaf91281f86fc3c0e6fcee8cf3', 1, 'Mayor\'s Clearance', 'Ricardo Cortez', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-12 11:29:46', '2026-10-12 11:37:48', '2026-10-12 11:37:48', '2026-10-12 11:44:05', 1),
(7691, 1, '2026-10-12', 41, 'MO-C041', 'e25a759d053d8a6d3f644ad3771bc02a', 3, 'Personal concerns', 'Carmela Domingo', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-12 11:32:23', '2026-10-12 11:36:49', '2026-10-12 11:36:49', '2026-10-12 11:56:23', 1),
(7692, 1, '2026-10-12', 42, 'MO-E042', 'e219c5f8f69e3b717926232bfcfdb3d1', 5, 'Financial assistance', 'Felipe Mercado', NULL, NULL, NULL, 'self', 'cancelled', NULL, NULL, 0, '2026-10-12 11:38:39', NULL, NULL, NULL, 1),
(7693, 1, '2026-10-12', 43, 'MO-A043', 'e51689f87ba6adc0b5910fad38995eb6', 1, 'Mayor\'s Clearance', 'Cynthia Navarro', NULL, NULL, NULL, 'staff', 'done', 56, 'Angelu', 0, '2026-10-12 11:51:55', '2026-10-12 11:56:57', '2026-10-12 11:56:57', '2026-10-12 11:59:15', 1),
(7694, 1, '2026-10-12', 44, 'MO-A044', '84058122915bbeabdeb9ebda07606314', 1, 'Mayor\'s Clearance', 'Jose Salazar', NULL, NULL, NULL, 'staff', 'done', 56, 'Angelu', 0, '2026-10-12 11:53:53', '2026-10-12 11:56:33', '2026-10-12 11:56:33', '2026-10-12 12:02:42', 1),
(7695, 1, '2026-10-12', 45, 'MO-F045', 'a836a16ff603c8b012bfa2081cf4fde0', 6, 'Other', 'Teresita Aquino', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-12 12:22:11', '2026-10-12 12:25:13', '2026-10-12 12:25:13', '2026-10-12 12:31:31', 1),
(7696, 1, '2026-10-12', 46, 'MO-B046', '0fc1cc7c57b55805c7a1138f2b32cd2a', 2, 'Business concerns', 'Danilo Fernandez', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-12 12:36:14', '2026-10-12 12:40:39', '2026-10-12 12:40:39', '2026-10-12 12:52:04', 1),
(7697, 1, '2026-10-12', 47, 'MO-E047', 'e616f3fe06483e370edde5bc1e54f037', 5, 'Financial assistance', 'Lourdes Cruz', NULL, NULL, NULL, 'staff', 'done', 54, 'Jean Frias', 0, '2026-10-12 12:43:03', '2026-10-12 12:51:59', '2026-10-12 12:51:59', '2026-10-12 12:58:46', 1),
(7698, 1, '2026-10-12', 48, 'MO-A048', '6acd8e5cf8823db8fc4992513bb3fba6', 1, 'Mayor\'s Clearance', 'Ramon Gonzales', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-12 12:48:58', '2026-10-12 12:50:59', '2026-10-12 12:50:59', '2026-10-12 12:55:56', 1),
(7699, 1, '2026-10-12', 49, 'MO-D049', '86a2b613a492374ef6056cd067d3ecab', 4, 'Courtesy call', 'Josefina Mendoza', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-12 13:04:10', '2026-10-12 13:10:51', '2026-10-12 13:10:51', '2026-10-12 13:23:43', 1),
(7700, 1, '2026-10-12', 50, 'MO-B050', 'd011c3e6f793336e2bc4bee039a6d735', 2, 'Business concerns', 'Manuel Rivera', NULL, NULL, NULL, 'self', 'cancelled', NULL, NULL, 0, '2026-10-12 13:08:05', NULL, NULL, NULL, 1),
(7701, 1, '2026-10-12', 51, 'MO-B051', '2b416691ea95fb48b5fc5bdead2a7a1c', 2, 'Business concerns', 'Ana Villanueva', NULL, NULL, NULL, 'self', 'cancelled', NULL, NULL, 0, '2026-10-12 13:11:07', NULL, NULL, NULL, 1),
(7702, 1, '2026-10-12', 52, 'MO-A052', '4ba432947b0bc7f0c9907b63881e6ba8', 1, 'Mayor\'s Clearance', 'Rogelio Lopez', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-12 13:13:35', '2026-10-12 13:19:22', '2026-10-12 13:19:22', '2026-10-12 13:22:31', 1),
(7703, 1, '2026-10-12', 53, 'MO-E053', '3668ddcea09ee45e47466da48bdc7960', 5, 'Financial assistance', 'Grace Garcia', NULL, NULL, NULL, 'staff', 'skipped', 56, 'Angelu', 0, '2026-10-12 13:15:26', '2026-10-12 13:22:00', NULL, NULL, 1),
(7704, 1, '2026-10-12', 54, 'MO-F054', 'b49ee22ee80cfe85b29b86d4f9a646b9', 6, 'Other', 'Gerardo Flores', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-12 13:19:27', '2026-10-12 13:23:02', '2026-10-12 13:23:02', '2026-10-12 13:29:47', 1),
(7705, 1, '2026-10-12', 55, 'MO-F055', 'b40ca512b1c5778bf5167e4bbe60e38b', 6, 'Other', 'Liza Bautista', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-12 13:31:22', '2026-10-12 13:35:40', '2026-10-12 13:35:40', '2026-10-12 13:40:22', 1),
(7706, 1, '2026-10-12', 56, 'MO-F056', '0e208438b8d60e835ef5ae418fc51294', 6, 'Other', 'Antonio Torres', NULL, NULL, NULL, 'staff', 'done', 56, 'Angelu', 0, '2026-10-12 13:32:38', '2026-10-12 13:36:12', '2026-10-12 13:36:12', '2026-10-12 13:46:22', 1),
(7707, 1, '2026-10-12', 57, 'MO-A057', 'f199ae976964ed5ad1deb04c979024bb', 1, 'Mayor\'s Clearance', 'Elena Dela Cruz', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-12 13:35:18', '2026-10-12 13:45:03', '2026-10-12 13:45:03', '2026-10-12 13:48:43', 1),
(7708, 1, '2026-10-12', 58, 'MO-E058', '0c13ec08ea13088818b84c3edc93f25d', 5, 'Financial assistance', 'Pedro Pascual', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-12 13:45:57', '2026-10-12 13:48:29', '2026-10-12 13:48:29', '2026-10-12 14:01:17', 1),
(7709, 1, '2026-10-12', 59, 'MO-E059', '89a6adc69b21ca5a2c100a973270b2b4', 5, 'Financial assistance', 'Marites Reyes', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-12 13:46:01', '2026-10-12 13:54:16', '2026-10-12 13:54:16', '2026-10-12 14:07:17', 1),
(7710, 1, '2026-10-12', 60, 'MO-A060', 'cd2930bad533b39c8006319f1b6ec5e1', 1, 'Mayor\'s Clearance', 'Alfredo Ramos', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-12 13:49:09', '2026-10-12 13:54:16', '2026-10-12 13:54:16', '2026-10-12 13:57:24', 1),
(7711, 1, '2026-10-12', 61, 'MO-A061', '0a56793b04acdcdb1d29abc2745d5a20', 1, 'Mayor\'s Clearance', 'Maria Santos', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-12 13:57:15', '2026-10-12 14:07:06', '2026-10-12 14:07:06', '2026-10-12 14:09:29', 1),
(7712, 1, '2026-10-12', 62, 'MO-A062', '9863c07b6f323eafb4ead0f48e26b11f', 1, 'Mayor\'s Clearance', 'Eduardo Castillo', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-12 14:08:20', '2026-10-12 14:09:32', '2026-10-12 14:09:32', '2026-10-12 14:11:23', 1),
(7713, 1, '2026-10-12', 63, 'MO-E063', '9db947fb4e1cc60c0f6f1761a2198d19', 5, 'Financial assistance', 'Rosario Cortez', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-12 14:12:12', '2026-10-12 14:17:20', '2026-10-12 14:17:20', '2026-10-12 14:26:23', 1),
(7714, 1, '2026-10-12', 64, 'MO-C064', 'b0099834799a3334361482c30e44c7c8', 3, 'Personal concerns', 'Ricardo Domingo', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-12 14:23:12', '2026-10-12 14:27:00', '2026-10-12 14:27:00', '2026-10-12 14:32:35', 1),
(7715, 1, '2026-10-12', 65, 'MO-F065', 'd6f32751d470d0e58b67a3be329538c2', 6, 'Other', 'Carmela Mercado', NULL, NULL, NULL, 'self', 'cancelled', NULL, NULL, 0, '2026-10-12 14:36:39', NULL, NULL, NULL, 1),
(7716, 1, '2026-10-12', 66, 'MO-E066', '9ee1ca7b7a6e1bdc87dfd685c93f228e', 5, 'Financial assistance', 'Felipe Navarro', NULL, NULL, NULL, 'staff', 'done', 56, 'Angelu', 0, '2026-10-12 14:38:05', '2026-10-12 15:02:28', '2026-10-12 15:02:28', '2026-10-12 15:10:45', 1),
(7717, 1, '2026-10-12', 67, 'MO-E067', 'a01c44ae711345b613c30ec5148ee742', 5, 'Financial assistance', 'Cynthia Salazar', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-12 14:54:03', '2026-10-12 15:09:41', '2026-10-12 15:09:41', '2026-10-12 15:20:33', 1),
(7718, 1, '2026-10-12', 68, 'MO-A068', '26127673adff657ffbc75d3c3a097ae0', 1, 'Mayor\'s Clearance', 'Jose Aquino', NULL, NULL, NULL, 'staff', 'skipped', 55, 'Sir Henjie', 0, '2026-10-12 14:59:21', '2026-10-12 15:05:14', NULL, NULL, 1),
(7719, 1, '2026-10-12', 69, 'MO-A069', 'e4930ffb127eda154735d41801dab059', 1, 'Mayor\'s Clearance', 'Teresita Fernandez', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-12 15:05:13', '2026-10-12 15:07:49', '2026-10-12 15:07:49', '2026-10-12 15:17:46', 1),
(7720, 1, '2026-10-12', 70, 'MO-A070', '609d27aff9669df6a06a24ec8852d280', 1, 'Mayor\'s Clearance', 'Danilo Cruz', NULL, NULL, NULL, 'staff', 'done', 54, 'Jean Frias', 0, '2026-10-12 15:05:33', '2026-10-12 15:06:40', '2026-10-12 15:06:40', '2026-10-12 15:16:05', 1),
(7721, 1, '2026-10-12', 71, 'MO-B071', '089ad660d225e46d09b3fe8f846da306', 2, 'Business concerns', 'Lourdes Gonzales', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-12 15:24:53', '2026-10-12 15:29:06', '2026-10-12 15:29:06', '2026-10-12 15:41:48', 1),
(7722, 1, '2026-10-12', 72, 'MO-B072', 'ec972335eb78efc498424d817e78e5e2', 2, 'Business concerns', 'Ramon Mendoza', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-12 15:25:53', '2026-10-12 15:30:15', '2026-10-12 15:30:15', '2026-10-12 15:40:05', 1),
(7723, 1, '2026-10-12', 73, 'MO-D073', '75b1d607b36b19e93cee3d88ae7c64ac', 4, 'Courtesy call', 'Josefina Rivera', NULL, NULL, NULL, 'staff', 'done', 55, 'Sir Henjie', 0, '2026-10-12 15:30:39', '2026-10-12 15:36:01', '2026-10-12 15:36:01', '2026-10-12 15:56:49', 1),
(7724, 1, '2026-10-12', 74, 'MO-D074', '652155c025243bbc034d6e7c3f3d0703', 4, 'Courtesy call', 'Manuel Villanueva', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-12 15:34:13', '2026-10-12 15:35:16', '2026-10-12 15:35:16', '2026-10-12 15:48:32', 1),
(7725, 1, '2026-10-12', 75, 'MO-E075', '9a448d81af2d1c4c0d7e53cf3ee420cb', 5, 'Financial assistance', 'Ana Lopez', NULL, NULL, NULL, 'self', 'skipped', 54, 'Jean Frias', 0, '2026-10-12 15:37:56', '2026-10-12 15:39:10', NULL, NULL, 1),
(7726, 1, '2026-10-12', 76, 'MO-B076', '2034f18394fbd2a52362a76a72893a43', 2, 'Business concerns', 'Rogelio Garcia', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-12 15:38:08', '2026-10-12 15:43:36', '2026-10-12 15:43:36', '2026-10-12 15:56:39', 1),
(7727, 1, '2026-10-12', 77, 'MO-A077', 'db807ae603f2f15080e89c02595ca335', 1, 'Mayor\'s Clearance', 'Grace Flores', NULL, NULL, NULL, 'self', 'skipped', 56, 'Angelu', 0, '2026-10-12 15:49:40', '2026-10-12 15:52:39', NULL, NULL, 1),
(7728, 1, '2026-10-12', 78, 'MO-B078', 'de4b448791e4065a438b8dfa9383f02a', 2, 'Business concerns', 'Gerardo Bautista', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-12 15:54:39', '2026-10-12 15:56:27', '2026-10-12 15:56:27', '2026-10-12 16:06:41', 1),
(7729, 1, '2026-10-12', 79, 'MO-A079', 'f68aed76362584471aa93ed4aabf9930', 1, 'Mayor\'s Clearance', 'Liza Torres', NULL, NULL, NULL, 'self', 'skipped', 54, 'Jean Frias', 0, '2026-10-12 15:54:39', '2026-10-12 16:05:31', NULL, NULL, 1),
(7730, 1, '2026-10-12', 80, 'MO-A080', 'c096366f5a0598dd61536d81ea94b85f', 1, 'Mayor\'s Clearance', 'Antonio Dela Cruz', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-12 15:59:53', '2026-10-12 16:03:12', '2026-10-12 16:03:12', '2026-10-12 16:05:05', 1),
(7731, 1, '2026-10-12', 81, 'MO-D081', '3b590a1d75ac964c219a5c2ab5abdbd8', 4, 'Courtesy call', 'Elena Pascual', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-12 16:20:44', '2026-10-12 16:22:21', '2026-10-12 16:22:21', '2026-10-12 16:44:12', 1),
(7732, 1, '2026-10-12', 82, 'MO-E082', 'f4ad675238260e177b76126a574f60ce', 5, 'Financial assistance', 'Pedro Reyes', NULL, NULL, NULL, 'self', 'skipped', 56, 'Angelu', 0, '2026-10-12 16:29:06', '2026-10-12 16:33:11', NULL, NULL, 1),
(7733, 1, '2026-10-13', 1, 'MO-A001', '038528914386c260c72bc04b37448634', 1, 'Mayor\'s Clearance', 'Antonio Cruz', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-13 08:09:46', '2026-10-13 08:18:20', '2026-10-13 08:18:20', '2026-10-13 08:26:31', 1),
(7734, 1, '2026-10-13', 2, 'MO-E002', '3bb104015b1670c03bf6b096c7a1d8f7', 5, 'Financial assistance', 'Elena Gonzales', NULL, NULL, NULL, 'self', 'skipped', 54, 'Jean Frias', 0, '2026-10-13 08:12:56', '2026-10-13 08:17:53', NULL, NULL, 1),
(7735, 1, '2026-10-13', 3, 'MO-D003', '6b63deaac1d9d016a830635b4c3e212d', 4, 'Courtesy call', 'Pedro Mendoza', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-13 08:26:10', '2026-10-13 08:27:38', '2026-10-13 08:27:38', '2026-10-13 08:47:24', 1),
(7736, 1, '2026-10-13', 4, 'MO-A004', '9bea51498a48d1e0f4cd397ba36d54e2', 1, 'Mayor\'s Clearance', 'Marites Rivera', NULL, NULL, NULL, 'staff', 'done', 54, 'Jean Frias', 0, '2026-10-13 08:35:31', '2026-10-13 08:40:12', '2026-10-13 08:40:12', '2026-10-13 08:43:33', 1),
(7737, 1, '2026-10-13', 5, 'MO-F005', '6ecb4b3cbe58dca9c03ac33d6acd6555', 6, 'Other', 'Alfredo Villanueva', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-13 08:36:08', '2026-10-13 08:38:25', '2026-10-13 08:38:25', '2026-10-13 08:47:03', 1),
(7738, 1, '2026-10-13', 6, 'MO-A006', 'dcd38743a0cbc8bfd24d571e5133a945', 1, 'Mayor\'s Clearance', 'Maria Lopez', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-13 08:42:55', '2026-10-13 08:47:25', '2026-10-13 08:47:25', '2026-10-13 08:54:26', 1),
(7739, 1, '2026-10-13', 7, 'MO-F007', 'fc33e866180e332be3caafa84dcb5c8d', 6, 'Other', 'Eduardo Garcia', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-13 08:59:54', '2026-10-13 09:02:31', '2026-10-13 09:02:31', '2026-10-13 09:21:36', 1),
(7740, 1, '2026-10-13', 8, 'MO-B008', 'bf9929afc0922d7efd5178ca0b1d583d', 2, 'Business concerns', 'Rosario Flores', NULL, NULL, NULL, 'self', 'cancelled', NULL, NULL, 0, '2026-10-13 09:01:42', NULL, NULL, NULL, 1),
(7741, 1, '2026-10-13', 9, 'MO-A009', 'd4b94721cc7ec203c6bbaa4f63542b7b', 1, 'Mayor\'s Clearance', 'Ricardo Bautista', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-13 09:15:31', '2026-10-13 09:19:12', '2026-10-13 09:19:12', '2026-10-13 09:27:51', 1),
(7742, 1, '2026-10-13', 10, 'MO-F010', 'f3ab5662c90b9b1590ce09a3453fc531', 6, 'Other', 'Carmela Torres', NULL, NULL, NULL, 'staff', 'done', 56, 'Angelu', 0, '2026-10-13 09:17:02', '2026-10-13 09:32:03', '2026-10-13 09:32:03', '2026-10-13 09:37:26', 1),
(7743, 1, '2026-10-13', 11, 'MO-A011', '17b1588c3b73a079206899596d2d452b', 1, 'Mayor\'s Clearance', 'Felipe Dela Cruz', NULL, NULL, NULL, 'staff', 'done', 55, 'Sir Henjie', 0, '2026-10-13 09:31:28', '2026-10-13 09:39:30', '2026-10-13 09:39:30', '2026-10-13 09:56:56', 1),
(7744, 1, '2026-10-13', 12, 'MO-D012', '893f734f7ffd2f620357e0c5c21d5fde', 4, 'Courtesy call', 'Cynthia Pascual', NULL, NULL, NULL, 'self', 'skipped', 56, 'Angelu', 0, '2026-10-13 09:45:24', '2026-10-13 09:49:37', NULL, NULL, 1),
(7745, 1, '2026-10-13', 13, 'MO-A013', '5d448920bea3ded0480a0c7e14789e75', 1, 'Mayor\'s Clearance', 'Jose Reyes', NULL, NULL, NULL, 'self', 'skipped', 56, 'Angelu', 0, '2026-10-13 09:46:42', '2026-10-13 09:47:23', NULL, NULL, 1),
(7746, 1, '2026-10-13', 14, 'MO-A014', '56c76091244d53376c4c318ebedcd7d9', 1, 'Mayor\'s Clearance', 'Teresita Ramos', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-13 09:52:44', '2026-10-13 09:57:28', '2026-10-13 09:57:28', '2026-10-13 10:00:18', 1),
(7747, 1, '2026-10-13', 15, 'MO-C015', 'df4065696b1482f49f35860c27b362af', 3, 'Personal concerns', 'Danilo Santos', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-13 09:53:37', '2026-10-13 10:16:32', '2026-10-13 10:16:32', '2026-10-13 10:28:35', 1),
(7748, 1, '2026-10-13', 16, 'MO-E016', '6dd1250e38e336f346308cb2883f577a', 5, 'Financial assistance', 'Lourdes Castillo', NULL, NULL, NULL, 'staff', 'done', 55, 'Sir Henjie', 0, '2026-10-13 09:53:49', '2026-10-13 10:06:43', '2026-10-13 10:06:43', '2026-10-13 10:16:35', 1),
(7749, 1, '2026-10-13', 17, 'MO-B017', '87a4b12aa711e285a9293a604f8859a5', 2, 'Business concerns', 'Ramon Cortez', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-13 09:56:46', '2026-10-13 10:05:04', '2026-10-13 10:05:04', '2026-10-13 10:10:37', 1),
(7750, 1, '2026-10-13', 18, 'MO-B018', 'a054f9dbd74d6a7fc2a3cc5388146d39', 2, 'Business concerns', 'Josefina Domingo', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-13 09:57:38', '2026-10-13 10:03:10', '2026-10-13 10:03:10', '2026-10-13 10:09:45', 1),
(7751, 1, '2026-10-13', 19, 'MO-C019', '0faf962132da27d50abc3796f48fca11', 3, 'Personal concerns', 'Manuel Mercado', NULL, NULL, NULL, 'staff', 'done', 55, 'Sir Henjie', 0, '2026-10-13 10:01:44', '2026-10-13 10:07:02', '2026-10-13 10:07:02', '2026-10-13 10:13:36', 1),
(7752, 1, '2026-10-13', 20, 'MO-F020', '4543905ee624c9e83a28b826f78b3109', 6, 'Other', 'Ana Navarro', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-13 10:03:43', '2026-10-13 10:11:26', '2026-10-13 10:11:26', '2026-10-13 10:15:13', 1),
(7753, 1, '2026-10-13', 21, 'MO-B021', '4c64661fe09783927a91335639fcff4d', 2, 'Business concerns', 'Rogelio Salazar', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-13 10:08:32', '2026-10-13 10:15:05', '2026-10-13 10:15:05', '2026-10-13 10:25:23', 1),
(7754, 1, '2026-10-13', 22, 'MO-A022', '27e24d9fcc41901d2a5325a2a6c2e6e2', 1, 'Mayor\'s Clearance', 'Grace Aquino', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-13 10:09:36', '2026-10-13 10:22:49', '2026-10-13 10:22:49', '2026-10-13 10:33:44', 1),
(7755, 1, '2026-10-13', 23, 'MO-A023', '93e493f03a53c9beb9dd815f4caef7bb', 1, 'Mayor\'s Clearance', 'Gerardo Fernandez', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-13 10:18:59', '2026-10-13 10:25:21', '2026-10-13 10:25:21', '2026-10-13 10:29:36', 1),
(7756, 1, '2026-10-13', 24, 'MO-C024', '2c7c392577430d2ab8c790d788c000a0', 3, 'Personal concerns', 'Liza Cruz', NULL, NULL, NULL, 'staff', 'done', 54, 'Jean Frias', 0, '2026-10-13 10:19:16', '2026-10-13 10:30:09', '2026-10-13 10:30:09', '2026-10-13 10:40:53', 1),
(7757, 1, '2026-10-13', 25, 'MO-D025', '8c1f59d7ee508ca344f3c7ce230f0e89', 4, 'Courtesy call', 'Antonio Gonzales', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-13 10:23:20', '2026-10-13 10:27:16', '2026-10-13 10:27:16', '2026-10-13 10:40:11', 1),
(7758, 1, '2026-10-13', 26, 'MO-C026', '16d81e0b16a2c848c80d8ea0896d8b96', 3, 'Personal concerns', 'Elena Mendoza', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-13 10:25:22', '2026-10-13 10:41:08', '2026-10-13 10:41:08', '2026-10-13 10:53:12', 1),
(7759, 1, '2026-10-13', 27, 'MO-E027', '38cfabcec810416c91fe33af632a75f6', 5, 'Financial assistance', 'Pedro Rivera', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-13 10:26:04', '2026-10-13 10:32:56', '2026-10-13 10:32:56', '2026-10-13 10:37:12', 1),
(7760, 1, '2026-10-13', 28, 'MO-A028', '67fdcac6d9eb30e105d4df4800dc97ba', 1, 'Mayor\'s Clearance', 'Marites Villanueva', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-13 10:27:50', '2026-10-13 10:35:43', '2026-10-13 10:35:43', '2026-10-13 10:37:13', 1),
(7761, 1, '2026-10-13', 29, 'MO-F029', '3c26eb5d2f8bfc719316717c15b5dd9b', 6, 'Other', 'Alfredo Lopez', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-13 10:30:12', '2026-10-13 10:40:15', '2026-10-13 10:40:15', '2026-10-13 10:46:20', 1),
(7762, 1, '2026-10-13', 30, 'MO-B030', '82e281cee149dcd668e4e9f61eeb9221', 2, 'Business concerns', 'Maria Garcia', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-13 10:37:15', '2026-10-13 10:59:05', '2026-10-13 10:59:05', '2026-10-13 11:03:53', 1),
(7763, 1, '2026-10-13', 31, 'MO-C031', '453e21cc5cd8e987f53c82a91acd869a', 3, 'Personal concerns', 'Eduardo Flores', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-13 10:41:39', '2026-10-13 11:00:58', '2026-10-13 11:00:58', '2026-10-13 11:17:37', 1),
(7764, 1, '2026-10-13', 32, 'MO-D032', '11dbea4a94c72815fe2332ca3a1e8a1d', 4, 'Courtesy call', 'Rosario Bautista', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-13 10:48:12', '2026-10-13 11:01:13', '2026-10-13 11:01:13', '2026-10-13 11:16:31', 1),
(7765, 1, '2026-10-13', 33, 'MO-A033', 'eaeb59bd2f9167a547dd6c95169383da', 1, 'Mayor\'s Clearance', 'Ricardo Torres', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-13 10:51:41', '2026-10-13 11:01:56', '2026-10-13 11:01:56', '2026-10-13 11:08:19', 1),
(7766, 1, '2026-10-13', 34, 'MO-F034', '6990453d3ef3cd8205fa86743a74bc60', 6, 'Other', 'Carmela Dela Cruz', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-13 10:54:32', '2026-10-13 10:55:29', '2026-10-13 10:55:29', '2026-10-13 11:01:52', 1),
(7767, 1, '2026-10-13', 35, 'MO-C035', 'ef82ee220af5145dd35e6663f32641b3', 3, 'Personal concerns', 'Felipe Pascual', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-13 11:01:44', '2026-10-13 11:15:20', '2026-10-13 11:15:20', '2026-10-13 11:25:34', 1),
(7768, 1, '2026-10-13', 36, 'MO-B036', '9769cfd3f7a68741f11c544af2324c5f', 2, 'Business concerns', 'Cynthia Reyes', NULL, NULL, NULL, 'staff', 'done', 54, 'Jean Frias', 0, '2026-10-13 11:03:37', '2026-10-13 11:12:54', '2026-10-13 11:12:54', '2026-10-13 11:28:23', 1),
(7769, 1, '2026-10-13', 37, 'MO-C037', 'b5a3e82bf75ba192cb7061ffbbb76cf7', 3, 'Personal concerns', 'Jose Ramos', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-13 11:04:42', '2026-10-13 11:09:28', '2026-10-13 11:09:28', '2026-10-13 11:20:49', 1),
(7770, 1, '2026-10-13', 38, 'MO-E038', 'cb7982ee98ad3a6cc4afcef0dd73c8e7', 5, 'Financial assistance', 'Teresita Santos', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-13 11:56:07', '2026-10-13 11:58:49', '2026-10-13 11:58:49', '2026-10-13 12:05:53', 1),
(7771, 1, '2026-10-13', 39, 'MO-F039', 'b7f4b178334ef0caeff6ca99bc7eea05', 6, 'Other', 'Danilo Castillo', NULL, NULL, NULL, 'self', 'skipped', 55, 'Sir Henjie', 0, '2026-10-13 12:03:25', '2026-10-13 12:05:25', NULL, NULL, 1),
(7772, 1, '2026-10-13', 40, 'MO-A040', '5b632d2b3f7fab2df590523fde0bbef0', 1, 'Mayor\'s Clearance', 'Lourdes Cortez', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-13 12:23:27', '2026-10-13 12:26:50', '2026-10-13 12:26:50', '2026-10-13 12:29:42', 1),
(7773, 1, '2026-10-13', 41, 'MO-F041', 'c3111db51926f44bf65f6e2b772a8d3b', 6, 'Other', 'Ramon Domingo', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-13 12:24:39', '2026-10-13 12:28:58', '2026-10-13 12:28:58', '2026-10-13 12:35:21', 1),
(7774, 1, '2026-10-13', 42, 'MO-C042', 'f643302712eaff716548ca5d58ad43e4', 3, 'Personal concerns', 'Josefina Mercado', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-13 13:10:17', '2026-10-13 13:28:14', '2026-10-13 13:28:14', '2026-10-13 13:44:23', 1),
(7775, 1, '2026-10-13', 43, 'MO-A043', 'e0ebb4aacb668b093160f1ef64406067', 1, 'Mayor\'s Clearance', 'Manuel Navarro', NULL, NULL, NULL, 'staff', 'done', 54, 'Jean Frias', 0, '2026-10-13 13:15:09', '2026-10-13 13:26:05', '2026-10-13 13:26:05', '2026-10-13 13:35:37', 1),
(7776, 1, '2026-10-13', 44, 'MO-F044', 'a052749b9e933a63fe055106772e1dc8', 6, 'Other', 'Ana Salazar', NULL, NULL, NULL, 'staff', 'done', 56, 'Angelu', 0, '2026-10-13 13:26:03', '2026-10-13 13:36:53', '2026-10-13 13:36:53', '2026-10-13 13:42:02', 1),
(7777, 1, '2026-10-13', 45, 'MO-A045', '1feee7b0ff975130a4db4db7e98015e8', 1, 'Mayor\'s Clearance', 'Rogelio Aquino', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-13 13:27:38', '2026-10-13 13:43:08', '2026-10-13 13:43:08', '2026-10-13 13:51:20', 1),
(7778, 1, '2026-10-13', 46, 'MO-B046', '7dbb933d361453e5a488b6751223726d', 2, 'Business concerns', 'Grace Fernandez', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-13 13:57:44', '2026-10-13 14:03:23', '2026-10-13 14:03:23', '2026-10-13 14:10:54', 1),
(7779, 1, '2026-10-13', 47, 'MO-E047', 'daf8007288775eabecc9203f2578c090', 5, 'Financial assistance', 'Gerardo Cruz', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-13 14:07:07', '2026-10-13 14:08:59', '2026-10-13 14:08:59', '2026-10-13 14:17:18', 1),
(7780, 1, '2026-10-13', 48, 'MO-A048', '3d87709a064294bde02caf47efbb08fd', 1, 'Mayor\'s Clearance', 'Liza Gonzales', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-13 14:19:28', '2026-10-13 14:26:04', '2026-10-13 14:26:04', '2026-10-13 14:27:34', 1),
(7781, 1, '2026-10-13', 49, 'MO-A049', '94d2de1523d4199aa3f64b87dab82d31', 1, 'Mayor\'s Clearance', 'Antonio Mendoza', NULL, NULL, NULL, 'self', 'skipped', 56, 'Angelu', 0, '2026-10-13 14:29:48', '2026-10-13 14:32:35', NULL, NULL, 1),
(7782, 1, '2026-10-13', 50, 'MO-D050', '5b46402219178d0467369242b039ca55', 4, 'Courtesy call', 'Elena Rivera', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-13 14:47:57', '2026-10-13 14:51:53', '2026-10-13 14:51:53', '2026-10-13 14:57:39', 1),
(7783, 1, '2026-10-13', 51, 'MO-B051', 'd316c0e6c593ab81e7900ea7f36c83a0', 2, 'Business concerns', 'Pedro Villanueva', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-13 14:48:20', '2026-10-13 14:52:23', '2026-10-13 14:52:23', '2026-10-13 14:58:51', 1),
(7784, 1, '2026-10-13', 52, 'MO-A052', '5f55f75d026d6ed83f849f6220dd630b', 1, 'Mayor\'s Clearance', 'Marites Lopez', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-13 14:54:43', '2026-10-13 14:58:43', '2026-10-13 14:58:43', '2026-10-13 15:00:15', 1),
(7785, 1, '2026-10-13', 53, 'MO-A053', '2eb895b0819deb5637f39837e08b024e', 1, 'Mayor\'s Clearance', 'Alfredo Garcia', NULL, NULL, NULL, 'staff', 'done', 55, 'Sir Henjie', 0, '2026-10-13 15:07:34', '2026-10-13 15:09:57', '2026-10-13 15:09:57', '2026-10-13 15:19:10', 1),
(7786, 1, '2026-10-13', 54, 'MO-D054', '77f1839e89d0ba090bed820db4dec7db', 4, 'Courtesy call', 'Maria Flores', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-13 15:33:49', '2026-10-13 15:41:19', '2026-10-13 15:41:19', '2026-10-13 15:55:44', 1),
(7787, 1, '2026-10-13', 55, 'MO-E055', '805fc18d8178328101b38f80fd19acc2', 5, 'Financial assistance', 'Eduardo Bautista', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-13 15:41:11', '2026-10-13 15:58:16', '2026-10-13 15:58:16', '2026-10-13 16:06:59', 1),
(7788, 1, '2026-10-13', 56, 'MO-F056', '6bde53cee1ff77abd43a137865f4118d', 6, 'Other', 'Rosario Torres', NULL, NULL, NULL, 'staff', 'done', 54, 'Jean Frias', 0, '2026-10-13 15:46:20', '2026-10-13 15:48:09', '2026-10-13 15:48:09', '2026-10-13 15:57:58', 1),
(7789, 1, '2026-10-13', 57, 'MO-B057', 'ff97f9bb602dfa95389ef55ed547e68f', 2, 'Business concerns', 'Ricardo Dela Cruz', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-13 16:20:08', '2026-10-13 16:23:00', '2026-10-13 16:23:00', '2026-10-13 16:32:59', 1),
(7790, 1, '2026-10-13', 58, 'MO-B058', '7222b9594c06bd5e0d436ad18aaffa1f', 2, 'Business concerns', 'Carmela Pascual', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-13 16:42:23', '2026-10-13 16:44:43', '2026-10-13 16:44:43', '2026-10-13 16:53:25', 1),
(7791, 1, '2026-10-13', 59, 'MO-E059', 'e6430930b3384382481d900fa6af5195', 5, 'Financial assistance', 'Felipe Reyes', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-13 16:50:43', '2026-10-13 16:54:15', '2026-10-13 16:54:15', '2026-10-13 16:58:16', 1),
(7792, 1, '2026-10-13', 60, 'MO-F060', 'ee096fe3e35f247c91fa27dae8834e8b', 6, 'Other', 'Cynthia Ramos', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-13 16:52:08', '2026-10-13 16:54:33', '2026-10-13 16:54:33', '2026-10-13 16:57:45', 1),
(7793, 1, '2026-10-14', 1, 'MO-B001', '435ac4547711da949369a94e0a558c5a', 2, 'Business concerns', 'Rosario Cruz', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-14 08:05:59', '2026-10-14 08:07:13', '2026-10-14 08:07:13', '2026-10-14 08:16:30', 1),
(7794, 1, '2026-10-14', 2, 'MO-A002', 'a40e53721afbf0766ae0c14aede841b6', 1, 'Mayor\'s Clearance', 'Ricardo Gonzales', NULL, NULL, NULL, 'self', 'skipped', 55, 'Sir Henjie', 0, '2026-10-14 08:12:42', '2026-10-14 08:18:39', NULL, NULL, 1),
(7795, 1, '2026-10-14', 3, 'MO-A003', 'a600488eaa652fcf686db90219ff3ee7', 1, 'Mayor\'s Clearance', 'Carmela Mendoza', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-14 08:23:14', '2026-10-14 08:25:21', '2026-10-14 08:25:21', '2026-10-14 08:33:58', 1),
(7796, 1, '2026-10-14', 4, 'MO-F004', '9fab8cd0155baa4e077980225d86db8f', 6, 'Other', 'Felipe Rivera', NULL, NULL, NULL, 'staff', 'done', 56, 'Angelu', 0, '2026-10-14 08:40:42', '2026-10-14 08:44:29', '2026-10-14 08:44:29', '2026-10-14 08:50:15', 1),
(7797, 1, '2026-10-14', 5, 'MO-A005', 'eff85d7513c5e2df63a06e124da02467', 1, 'Mayor\'s Clearance', 'Cynthia Villanueva', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-14 08:45:24', '2026-10-14 08:55:35', '2026-10-14 08:55:35', '2026-10-14 08:58:18', 1),
(7798, 1, '2026-10-14', 6, 'MO-E006', '5f735f4ea46b0ced7d7f1f477fe589ef', 5, 'Financial assistance', 'Jose Lopez', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-14 08:49:05', '2026-10-14 08:50:04', '2026-10-14 08:50:04', '2026-10-14 08:53:34', 1),
(7799, 1, '2026-10-14', 7, 'MO-C007', 'e957a55a53bef34e62577dd0f8bea527', 3, 'Personal concerns', 'Teresita Garcia', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-14 08:53:38', '2026-10-14 08:59:16', '2026-10-14 08:59:16', '2026-10-14 09:07:54', 1),
(7800, 1, '2026-10-14', 8, 'MO-E008', 'a50369c928371bb7d061d564caa3f56b', 5, 'Financial assistance', 'Danilo Flores', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-14 09:01:30', '2026-10-14 09:05:05', '2026-10-14 09:05:05', '2026-10-14 09:12:44', 1),
(7801, 1, '2026-10-14', 9, 'MO-C009', '13f1ee98f82b94f414cba0b161ec36fb', 3, 'Personal concerns', 'Lourdes Bautista', NULL, NULL, NULL, 'staff', 'skipped', 54, 'Jean Frias', 0, '2026-10-14 09:16:17', '2026-10-14 09:31:02', NULL, NULL, 1),
(7802, 1, '2026-10-14', 10, 'MO-B010', '07d07f8b3f7d92f2e632f81eacfe841d', 2, 'Business concerns', 'Ramon Torres', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-14 09:17:18', '2026-10-14 09:27:56', '2026-10-14 09:27:56', '2026-10-14 09:46:57', 1),
(7803, 1, '2026-10-14', 11, 'MO-B011', 'c61d5dd126a6d0020ea50c943c259ce5', 2, 'Business concerns', 'Josefina Dela Cruz', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-14 09:18:17', '2026-10-14 09:31:09', '2026-10-14 09:31:09', '2026-10-14 09:46:56', 1),
(7804, 1, '2026-10-14', 12, 'MO-E012', '3bf1d7656057c76db0a98c7502a5426f', 5, 'Financial assistance', 'Manuel Pascual', NULL, NULL, NULL, 'self', 'cancelled', NULL, NULL, 0, '2026-10-14 09:19:26', NULL, NULL, NULL, 1),
(7805, 1, '2026-10-14', 13, 'MO-F013', '075dbc996bbcd729f8c98374a04b08b1', 6, 'Other', 'Ana Reyes', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-14 09:26:30', '2026-10-14 09:28:58', '2026-10-14 09:28:58', '2026-10-14 09:31:50', 1),
(7806, 1, '2026-10-14', 14, 'MO-B014', '34a24a95599830be5eee77388c99bbc2', 2, 'Business concerns', 'Rogelio Ramos', NULL, NULL, NULL, 'self', 'cancelled', NULL, NULL, 0, '2026-10-14 09:28:10', NULL, NULL, NULL, 1),
(7807, 1, '2026-10-14', 15, 'MO-E015', '0c3c0d6e534090eb9a3d024de18dab93', 5, 'Financial assistance', 'Grace Santos', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-14 09:29:27', '2026-10-14 09:33:27', '2026-10-14 09:33:27', '2026-10-14 09:43:05', 1),
(7808, 1, '2026-10-14', 16, 'MO-B016', 'f831da258dab00b934789a28310fe0e6', 2, 'Business concerns', 'Gerardo Castillo', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-14 09:35:20', '2026-10-14 09:39:59', '2026-10-14 09:39:59', '2026-10-14 10:03:25', 1),
(7809, 1, '2026-10-14', 17, 'MO-A017', 'f2a087565e1c0ca480226d134c5ddba2', 1, 'Mayor\'s Clearance', 'Liza Cortez', NULL, NULL, NULL, 'self', 'cancelled', NULL, NULL, 0, '2026-10-14 10:04:50', NULL, NULL, NULL, 1),
(7810, 1, '2026-10-14', 18, 'MO-A018', 'e638aa8c09463791ab50056cdc5e6ba5', 1, 'Mayor\'s Clearance', 'Antonio Domingo', NULL, NULL, NULL, 'self', 'cancelled', NULL, NULL, 0, '2026-10-14 10:07:13', NULL, NULL, NULL, 1),
(7811, 1, '2026-10-14', 19, 'MO-A019', '7bf3ad3d4cce94650a292c63abc047f7', 1, 'Mayor\'s Clearance', 'Elena Mercado', NULL, NULL, NULL, 'self', 'skipped', 55, 'Sir Henjie', 0, '2026-10-14 10:09:16', '2026-10-14 10:19:58', NULL, NULL, 1),
(7812, 1, '2026-10-14', 20, 'MO-C020', 'd21a1366c4ffd2d43a4552c581d3b103', 3, 'Personal concerns', 'Pedro Navarro', NULL, NULL, NULL, 'staff', 'done', 54, 'Jean Frias', 0, '2026-10-14 10:12:32', '2026-10-14 10:19:35', '2026-10-14 10:19:35', '2026-10-14 10:26:45', 1),
(7813, 1, '2026-10-14', 21, 'MO-A021', 'df776519ce3f064828e9167d633532c3', 1, 'Mayor\'s Clearance', 'Marites Salazar', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-14 10:20:25', '2026-10-14 10:24:17', '2026-10-14 10:24:17', '2026-10-14 10:37:21', 1),
(7814, 1, '2026-10-14', 22, 'MO-A022', '0489853a3b5dc3be9d4edf66355859ac', 1, 'Mayor\'s Clearance', 'Alfredo Aquino', NULL, NULL, NULL, 'staff', 'cancelled', NULL, NULL, 0, '2026-10-14 10:24:43', NULL, NULL, NULL, 1),
(7815, 1, '2026-10-14', 23, 'MO-B023', '86ed3810e72c77a5ef4455fbd3174eb5', 2, 'Business concerns', 'Maria Fernandez', NULL, NULL, NULL, 'staff', 'done', 55, 'Sir Henjie', 0, '2026-10-14 10:26:18', '2026-10-14 10:45:45', '2026-10-14 10:45:45', '2026-10-14 10:48:31', 1),
(7816, 1, '2026-10-14', 24, 'MO-C024', 'baed5076f041a075f6fe1861a66b726b', 3, 'Personal concerns', 'Eduardo Cruz', NULL, NULL, NULL, 'staff', 'done', 56, 'Angelu', 0, '2026-10-14 10:35:07', '2026-10-14 10:43:31', '2026-10-14 10:43:31', '2026-10-14 10:49:34', 1),
(7817, 1, '2026-10-14', 25, 'MO-E025', 'e4520aae988369675c0edc102044544d', 5, 'Financial assistance', 'Rosario Gonzales', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-14 10:35:31', '2026-10-14 10:42:44', '2026-10-14 10:42:44', '2026-10-14 10:49:58', 1),
(7818, 1, '2026-10-14', 26, 'MO-A026', '64665b0f29508e0305295155adfb278e', 1, 'Mayor\'s Clearance', 'Ricardo Mendoza', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-14 10:35:42', '2026-10-14 10:53:36', '2026-10-14 10:53:36', '2026-10-14 11:17:39', 1),
(7819, 1, '2026-10-14', 27, 'MO-A027', '79dc8a3c0798b97703ce2522858b05ef', 1, 'Mayor\'s Clearance', 'Carmela Rivera', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-14 10:43:19', '2026-10-14 10:53:44', '2026-10-14 10:53:44', '2026-10-14 10:59:36', 1),
(7820, 1, '2026-10-14', 28, 'MO-F028', 'baab69cde063bd524d36dc47eebf2f46', 6, 'Other', 'Felipe Villanueva', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-14 10:43:25', '2026-10-14 10:48:57', '2026-10-14 10:48:57', '2026-10-14 10:58:09', 1),
(7821, 1, '2026-10-14', 29, 'MO-D029', 'd9a2a1f2aeaa3c1ec8e636bd22fa7802', 4, 'Courtesy call', 'Cynthia Lopez', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-14 10:53:08', '2026-10-14 11:04:31', '2026-10-14 11:04:31', '2026-10-14 11:26:31', 1),
(7822, 1, '2026-10-14', 30, 'MO-F030', 'c7448820ddb29f2f5ff05a66c7f0bd33', 6, 'Other', 'Jose Garcia', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-14 11:04:38', '2026-10-14 11:08:16', '2026-10-14 11:08:16', '2026-10-14 11:23:39', 1),
(7823, 1, '2026-10-14', 31, 'MO-F031', 'f9b37e957dac83eef05ee0fbcb271170', 6, 'Other', 'Teresita Flores', NULL, NULL, NULL, 'staff', 'done', 55, 'Sir Henjie', 0, '2026-10-14 11:07:46', '2026-10-14 11:10:47', '2026-10-14 11:10:47', '2026-10-14 11:17:30', 1),
(7824, 1, '2026-10-14', 32, 'MO-C032', 'fb97288ed682734c304fb9de23f6aa77', 3, 'Personal concerns', 'Danilo Bautista', NULL, NULL, NULL, 'staff', 'done', 55, 'Sir Henjie', 0, '2026-10-14 11:14:04', '2026-10-14 11:15:14', '2026-10-14 11:15:14', '2026-10-14 11:23:39', 1),
(7825, 1, '2026-10-14', 33, 'MO-B033', 'b0a5f2ab6cd8f6970675f68089e2085a', 2, 'Business concerns', 'Lourdes Torres', NULL, NULL, NULL, 'staff', 'done', 54, 'Jean Frias', 0, '2026-10-14 11:23:28', '2026-10-14 11:33:40', '2026-10-14 11:33:40', '2026-10-14 11:37:37', 1),
(7826, 1, '2026-10-14', 34, 'MO-B034', '4e851751d0b0942b3e6d80da38ca4f49', 2, 'Business concerns', 'Ramon Dela Cruz', NULL, NULL, NULL, 'self', 'skipped', 54, 'Jean Frias', 0, '2026-10-14 11:26:58', '2026-10-14 11:34:45', NULL, NULL, 1),
(7827, 1, '2026-10-14', 35, 'MO-C035', '0f4a7c09c3907f83ce06038bcd5b9fbe', 3, 'Personal concerns', 'Josefina Pascual', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-14 11:36:40', '2026-10-14 11:42:30', '2026-10-14 11:42:30', '2026-10-14 11:52:16', 1),
(7828, 1, '2026-10-14', 36, 'MO-D036', '4e8bdacdb4a0eea22eb65b632dfee0be', 4, 'Courtesy call', 'Manuel Reyes', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-14 11:41:59', '2026-10-14 11:45:22', '2026-10-14 11:45:22', '2026-10-14 12:08:00', 1),
(7829, 1, '2026-10-14', 37, 'MO-C037', '253dd31beb2716d9fa47c7460a5af940', 3, 'Personal concerns', 'Ana Ramos', NULL, NULL, NULL, 'self', 'cancelled', NULL, NULL, 0, '2026-10-14 11:48:24', NULL, NULL, NULL, 1),
(7830, 1, '2026-10-14', 38, 'MO-A038', '8beb82d11958699db99b35482497c710', 1, 'Mayor\'s Clearance', 'Rogelio Santos', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-14 12:10:27', '2026-10-14 12:17:46', '2026-10-14 12:17:46', '2026-10-14 12:25:06', 1),
(7831, 1, '2026-10-14', 39, 'MO-C039', '703bd5b9d0c9530ec3db7c3b2386ac88', 3, 'Personal concerns', 'Grace Castillo', NULL, NULL, NULL, 'staff', 'done', 55, 'Sir Henjie', 0, '2026-10-14 12:14:45', '2026-10-14 12:17:27', '2026-10-14 12:17:27', '2026-10-14 12:40:28', 1),
(7832, 1, '2026-10-14', 40, 'MO-A040', '30f8d315eda896b7a24705074896a5c3', 1, 'Mayor\'s Clearance', 'Gerardo Cortez', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-14 12:21:42', '2026-10-14 12:26:14', '2026-10-14 12:26:14', '2026-10-14 12:43:49', 1),
(7833, 1, '2026-10-14', 41, 'MO-C041', '2bd26ba1691a3dbece15504fe074b159', 3, 'Personal concerns', 'Liza Domingo', NULL, NULL, NULL, 'staff', 'done', 55, 'Sir Henjie', 0, '2026-10-14 12:24:12', '2026-10-14 12:25:17', '2026-10-14 12:25:17', '2026-10-14 12:39:13', 1),
(7834, 1, '2026-10-14', 42, 'MO-C042', '963c2e1c2bddc37d4d33cb8ef1e275dd', 3, 'Personal concerns', 'Antonio Mercado', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-14 12:40:49', '2026-10-14 12:46:15', '2026-10-14 12:46:15', '2026-10-14 12:49:30', 1),
(7835, 1, '2026-10-14', 43, 'MO-C043', '186913bdb55867584bb721d0ed46a40f', 3, 'Personal concerns', 'Elena Navarro', NULL, NULL, NULL, 'staff', 'done', 56, 'Angelu', 0, '2026-10-14 12:50:26', '2026-10-14 12:51:37', '2026-10-14 12:51:37', '2026-10-14 13:00:01', 1),
(7836, 1, '2026-10-14', 44, 'MO-A044', '58490dd85b1ff1e20372476d573df27e', 1, 'Mayor\'s Clearance', 'Pedro Salazar', NULL, NULL, NULL, 'staff', 'done', 55, 'Sir Henjie', 0, '2026-10-14 12:51:39', '2026-10-14 12:52:55', '2026-10-14 12:52:55', '2026-10-14 13:01:18', 1),
(7837, 1, '2026-10-14', 45, 'MO-C045', '702949dc743dc127a0bda4ee2569bcbc', 3, 'Personal concerns', 'Marites Aquino', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-14 13:13:36', '2026-10-14 13:27:00', '2026-10-14 13:27:00', '2026-10-14 13:38:43', 1),
(7838, 1, '2026-10-14', 46, 'MO-E046', 'b525a56d9a6fd0de52ed0438fe7a004f', 5, 'Financial assistance', 'Alfredo Fernandez', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-14 13:19:17', '2026-10-14 13:23:26', '2026-10-14 13:23:26', '2026-10-14 13:33:07', 1),
(7839, 1, '2026-10-14', 47, 'MO-E047', '3089bb8d6a1394bf41dcba2ba2dbfc31', 5, 'Financial assistance', 'Maria Cruz', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-14 13:33:17', '2026-10-14 13:37:37', '2026-10-14 13:37:37', '2026-10-14 13:55:58', 1),
(7840, 1, '2026-10-14', 48, 'MO-F048', 'ebf737f4d7af378f8f1957560998eb3b', 6, 'Other', 'Eduardo Gonzales', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-14 13:45:58', '2026-10-14 13:48:44', '2026-10-14 13:48:44', '2026-10-14 13:54:31', 1),
(7841, 1, '2026-10-14', 49, 'MO-A049', 'aa0c3c40d982d7ab1dbf99b3311f3758', 1, 'Mayor\'s Clearance', 'Rosario Mendoza', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-14 13:49:13', '2026-10-14 13:50:54', '2026-10-14 13:50:54', '2026-10-14 13:53:20', 1),
(7842, 1, '2026-10-14', 50, 'MO-A050', 'ecba1b9b7f292ec69e7685b1154d4cc3', 1, 'Mayor\'s Clearance', 'Ricardo Rivera', NULL, NULL, NULL, 'staff', 'done', 54, 'Jean Frias', 0, '2026-10-14 13:54:47', '2026-10-14 13:59:27', '2026-10-14 13:59:27', '2026-10-14 14:11:41', 1),
(7843, 1, '2026-10-14', 51, 'MO-C051', '1e49df7efb76e08db3dfef2163e23919', 3, 'Personal concerns', 'Carmela Villanueva', NULL, NULL, NULL, 'staff', 'done', 56, 'Angelu', 0, '2026-10-14 13:59:08', '2026-10-14 14:04:24', '2026-10-14 14:04:24', '2026-10-14 14:27:43', 1),
(7844, 1, '2026-10-14', 52, 'MO-E052', '33966e6840e179dfe6be6257e2dd0ba8', 5, 'Financial assistance', 'Felipe Lopez', NULL, NULL, NULL, 'self', 'cancelled', NULL, NULL, 0, '2026-10-14 14:03:13', NULL, NULL, NULL, 1),
(7845, 1, '2026-10-14', 53, 'MO-A053', '9f07783608b03b118238b0cf2b920543', 1, 'Mayor\'s Clearance', 'Cynthia Garcia', NULL, NULL, NULL, 'staff', 'done', 56, 'Angelu', 0, '2026-10-14 14:05:44', '2026-10-14 14:11:49', '2026-10-14 14:11:49', '2026-10-14 14:19:00', 1),
(7846, 1, '2026-10-14', 54, 'MO-E054', '3c223db06b3385f6b712b867ca0a3eb5', 5, 'Financial assistance', 'Jose Flores', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-14 14:10:19', '2026-10-14 14:16:18', '2026-10-14 14:16:18', '2026-10-14 14:21:07', 1),
(7847, 1, '2026-10-14', 55, 'MO-A055', 'c8b04db910d2d87590717f296a10373d', 1, 'Mayor\'s Clearance', 'Teresita Bautista', NULL, NULL, NULL, 'self', 'cancelled', NULL, NULL, 0, '2026-10-14 14:13:16', NULL, NULL, NULL, 1),
(7848, 1, '2026-10-14', 56, 'MO-F056', '514eb2c76da41d876e88fcd1006bce6b', 6, 'Other', 'Danilo Torres', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-14 14:26:28', '2026-10-14 14:33:32', '2026-10-14 14:33:32', '2026-10-14 14:35:27', 1),
(7849, 1, '2026-10-14', 57, 'MO-E057', 'd9f34affc6db693eb4eec8ee690cf739', 5, 'Financial assistance', 'Lourdes Dela Cruz', NULL, NULL, NULL, 'staff', 'done', 55, 'Sir Henjie', 0, '2026-10-14 14:26:30', '2026-10-14 14:37:32', '2026-10-14 14:37:32', '2026-10-14 14:46:34', 1),
(7850, 1, '2026-10-14', 58, 'MO-A058', 'c8a1b583953521f133135411d4730ca5', 1, 'Mayor\'s Clearance', 'Ramon Pascual', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-14 14:35:15', '2026-10-14 14:37:34', '2026-10-14 14:37:34', '2026-10-14 14:45:31', 1),
(7851, 1, '2026-10-14', 59, 'MO-C059', 'ae37dcede5441b112bdf490354d73d27', 3, 'Personal concerns', 'Josefina Reyes', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-14 14:39:36', '2026-10-14 14:42:05', '2026-10-14 14:42:05', '2026-10-14 14:52:38', 1),
(7852, 1, '2026-10-14', 60, 'MO-E060', '5307d3daf4e75715ef36d99c07d13c10', 5, 'Financial assistance', 'Manuel Ramos', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-14 14:51:32', '2026-10-14 14:56:44', '2026-10-14 14:56:44', '2026-10-14 15:02:45', 1),
(7853, 1, '2026-10-14', 61, 'MO-E061', '4f7949328fc1ebcf8dc98f8ef587dfff', 5, 'Financial assistance', 'Ana Santos', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-14 14:56:56', '2026-10-14 15:01:16', '2026-10-14 15:01:16', '2026-10-14 15:05:42', 1),
(7854, 1, '2026-10-14', 62, 'MO-B062', '663849d018d04faf6af47df379fe75ec', 2, 'Business concerns', 'Rogelio Castillo', NULL, NULL, NULL, 'staff', 'done', 54, 'Jean Frias', 0, '2026-10-14 15:05:15', '2026-10-14 15:08:31', '2026-10-14 15:08:31', '2026-10-14 15:23:00', 1),
(7855, 1, '2026-10-14', 63, 'MO-C063', '80284d3ee21c02b86ad922da3e58ce14', 3, 'Personal concerns', 'Grace Cortez', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-14 15:24:04', '2026-10-14 15:25:13', '2026-10-14 15:25:13', '2026-10-14 15:36:30', 1),
(7856, 1, '2026-10-14', 64, 'MO-F064', '38abf430565f74fb7fb123076d05ccc2', 6, 'Other', 'Gerardo Domingo', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-14 15:43:16', '2026-10-14 15:44:54', '2026-10-14 15:44:54', '2026-10-14 15:46:24', 1),
(7857, 1, '2026-10-14', 65, 'MO-A065', '92396accc0c3c065b99d242054ef4b15', 1, 'Mayor\'s Clearance', 'Liza Mercado', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-14 15:43:21', '2026-10-14 15:48:19', '2026-10-14 15:48:19', '2026-10-14 15:49:49', 1),
(7858, 1, '2026-10-14', 66, 'MO-D066', '57ebaefdc9bd624ff435ea6486d67b71', 4, 'Courtesy call', 'Antonio Navarro', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-14 15:54:12', '2026-10-14 15:56:36', '2026-10-14 15:56:36', '2026-10-14 16:04:19', 1),
(7859, 1, '2026-10-14', 67, 'MO-C067', 'f93e0feac349c9da58509591fd538d23', 3, 'Personal concerns', 'Elena Salazar', NULL, NULL, NULL, 'staff', 'done', 56, 'Angelu', 0, '2026-10-14 15:55:53', '2026-10-14 15:58:50', '2026-10-14 15:58:50', '2026-10-14 16:08:54', 1),
(7860, 1, '2026-10-14', 68, 'MO-E068', 'b04a5c27659ab7fe541c64f84a3d6928', 5, 'Financial assistance', 'Pedro Aquino', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-14 15:56:53', '2026-10-14 16:04:09', '2026-10-14 16:04:09', '2026-10-14 16:07:30', 1),
(7861, 1, '2026-10-14', 69, 'MO-D069', '036a288d5290871c5050646dc1a11eaf', 4, 'Courtesy call', 'Marites Fernandez', NULL, NULL, NULL, 'self', 'skipped', 56, 'Angelu', 0, '2026-10-14 16:05:59', '2026-10-14 16:09:11', NULL, NULL, 1),
(7862, 1, '2026-10-14', 70, 'MO-E070', '8d8324c9c7fff82a06e5ccee811dc33a', 5, 'Financial assistance', 'Alfredo Cruz', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-14 16:20:58', '2026-10-14 16:23:17', '2026-10-14 16:23:17', '2026-10-14 16:41:54', 1),
(7863, 1, '2026-10-14', 71, 'MO-A071', '6b8ebe7987653afd7599a9dd08c64a22', 1, 'Mayor\'s Clearance', 'Maria Gonzales', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-14 16:37:53', '2026-10-14 16:40:00', '2026-10-14 16:40:00', '2026-10-14 16:43:06', 1),
(7864, 1, '2026-10-14', 72, 'MO-B072', '5869cc8b5eb726f40083b1161c1b4a07', 2, 'Business concerns', 'Eduardo Mendoza', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-14 16:47:36', '2026-10-14 16:48:06', '2026-10-14 16:48:06', '2026-10-14 17:06:55', 1),
(7865, 1, '2026-10-15', 1, 'MO-A001', '293a9ef5f5fc5298e2efe984ea6ae39a', 1, 'Mayor\'s Clearance', 'Danilo Cruz', NULL, NULL, NULL, 'staff', 'done', 54, 'Jean Frias', 0, '2026-10-15 08:02:27', '2026-10-15 08:10:25', '2026-10-15 08:10:25', '2026-10-15 08:13:47', 1),
(7866, 1, '2026-10-15', 2, 'MO-A002', '36f435cadf73403f500f271961e565d1', 1, 'Mayor\'s Clearance', 'Lourdes Gonzales', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-15 08:05:50', '2026-10-15 08:09:41', '2026-10-15 08:09:41', '2026-10-15 08:15:45', 1),
(7867, 1, '2026-10-15', 3, 'MO-A003', '64efa0520f3e1eb2087c867a46d56c77', 1, 'Mayor\'s Clearance', 'Ramon Mendoza', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-15 08:16:50', '2026-10-15 08:21:37', '2026-10-15 08:21:37', '2026-10-15 08:32:06', 1),
(7868, 1, '2026-10-15', 4, 'MO-F004', '66feb0571b6a09d329f87b64a86aa21a', 6, 'Other', 'Josefina Rivera', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-15 08:35:05', '2026-10-15 08:36:54', '2026-10-15 08:36:54', '2026-10-15 08:43:05', 1),
(7869, 1, '2026-10-15', 5, 'MO-B005', '49ff09a4abc145b30e59978d59cf1f14', 2, 'Business concerns', 'Manuel Villanueva', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-15 09:05:26', '2026-10-15 09:22:04', '2026-10-15 09:22:04', '2026-10-15 09:34:44', 1),
(7870, 1, '2026-10-15', 6, 'MO-B006', '65082ce6f5689c37558fbecab9072fbd', 2, 'Business concerns', 'Ana Lopez', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-15 09:25:32', '2026-10-15 09:27:20', '2026-10-15 09:27:20', '2026-10-15 09:35:48', 1),
(7871, 1, '2026-10-15', 7, 'MO-E007', '9002b69188a8fbf31ecc7e043df5035d', 5, 'Financial assistance', 'Rogelio Garcia', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-15 09:26:11', '2026-10-15 09:32:44', '2026-10-15 09:32:44', '2026-10-15 09:37:56', 1),
(7872, 1, '2026-10-15', 8, 'MO-A008', '358695d34fc06b1aaf27185eb03d1677', 1, 'Mayor\'s Clearance', 'Grace Flores', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-15 09:27:40', '2026-10-15 09:38:29', '2026-10-15 09:38:29', '2026-10-15 09:41:10', 1),
(7873, 1, '2026-10-15', 9, 'MO-A009', '9c526b4877ebcc44fadee81d6eda1b47', 1, 'Mayor\'s Clearance', 'Gerardo Bautista', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-15 09:35:20', '2026-10-15 09:50:02', '2026-10-15 09:50:02', '2026-10-15 10:02:55', 1),
(7874, 1, '2026-10-15', 10, 'MO-F010', '3e8d684638ff31b9b7a319a246a8d654', 6, 'Other', 'Liza Torres', NULL, NULL, NULL, 'staff', 'skipped', 55, 'Sir Henjie', 0, '2026-10-15 09:40:54', '2026-10-15 09:42:25', NULL, NULL, 1),
(7875, 1, '2026-10-15', 11, 'MO-F011', 'b095931df6adaaacd850500d5564b911', 6, 'Other', 'Antonio Dela Cruz', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-15 09:44:51', '2026-10-15 09:53:47', '2026-10-15 09:53:47', '2026-10-15 10:01:58', 1),
(7876, 1, '2026-10-15', 12, 'MO-A012', '5875e75ec28dae63a731aaffb4697415', 1, 'Mayor\'s Clearance', 'Elena Pascual', NULL, NULL, NULL, 'staff', 'done', 55, 'Sir Henjie', 0, '2026-10-15 09:46:33', '2026-10-15 09:58:11', '2026-10-15 09:58:11', '2026-10-15 10:05:38', 1),
(7877, 1, '2026-10-15', 13, 'MO-E013', '841e7d2fe7d5e020937b33d476b26fb1', 5, 'Financial assistance', 'Pedro Reyes', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-15 10:03:04', '2026-10-15 10:09:25', '2026-10-15 10:09:25', '2026-10-15 10:16:57', 1),
(7878, 1, '2026-10-15', 14, 'MO-E014', '61cacd93d74c72f3982f9fc634f656c9', 5, 'Financial assistance', 'Marites Ramos', NULL, NULL, NULL, 'staff', 'done', 56, 'Angelu', 0, '2026-10-15 10:04:56', '2026-10-15 10:14:22', '2026-10-15 10:14:22', '2026-10-15 10:20:50', 1),
(7879, 1, '2026-10-15', 15, 'MO-D015', 'b445db522b51dd014fd1f1c6161eebf0', 4, 'Courtesy call', 'Alfredo Santos', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-15 10:08:30', '2026-10-15 10:24:12', '2026-10-15 10:24:12', '2026-10-15 10:45:55', 1),
(7880, 1, '2026-10-15', 16, 'MO-D016', '6a863e70120d12cb78a91509bfc82ea6', 4, 'Courtesy call', 'Maria Castillo', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-15 10:12:09', '2026-10-15 10:33:44', '2026-10-15 10:33:44', '2026-10-15 10:57:12', 1),
(7881, 1, '2026-10-15', 17, 'MO-A017', '98da5431e0a64477944f548b95da4890', 1, 'Mayor\'s Clearance', 'Eduardo Cortez', NULL, NULL, NULL, 'self', 'skipped', 55, 'Sir Henjie', 0, '2026-10-15 10:13:09', '2026-10-15 10:19:40', NULL, NULL, 1),
(7882, 1, '2026-10-15', 18, 'MO-B018', 'c76e6b912615188b89e484be2e2b458b', 2, 'Business concerns', 'Rosario Domingo', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-15 10:13:49', '2026-10-15 10:36:41', '2026-10-15 10:36:41', '2026-10-15 10:45:03', 1);
INSERT INTO `queue_tickets` (`id`, `queue_id`, `queue_day`, `number`, `code`, `access_key`, `service_id`, `service_name`, `full_name`, `mobile`, `phone_key`, `user_id`, `source`, `status`, `staff_id`, `staff_name`, `is_priority`, `created_at`, `called_at`, `started_at`, `finished_at`, `is_demo`) VALUES
(7883, 1, '2026-10-15', 19, 'MO-E019', '61668bfe4cdb969ef4594d12cd734dd3', 5, 'Financial assistance', 'Ricardo Mercado', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-15 10:22:46', '2026-10-15 10:35:21', '2026-10-15 10:35:21', '2026-10-15 10:37:50', 1),
(7884, 1, '2026-10-15', 20, 'MO-D020', 'a7bd05bf532ee75808655cac1cbd5428', 4, 'Courtesy call', 'Carmela Navarro', NULL, NULL, NULL, 'staff', 'done', 55, 'Sir Henjie', 0, '2026-10-15 10:24:00', '2026-10-15 10:33:29', '2026-10-15 10:33:29', '2026-10-15 10:51:48', 1),
(7885, 1, '2026-10-15', 21, 'MO-B021', '3b993ac8c002317ef173bd61057b707e', 2, 'Business concerns', 'Felipe Salazar', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-15 10:24:57', '2026-10-15 10:35:07', '2026-10-15 10:35:07', '2026-10-15 10:43:10', 1),
(7886, 1, '2026-10-15', 22, 'MO-E022', '3c99fa0625263c6ca1104a25c081b143', 5, 'Financial assistance', 'Cynthia Aquino', NULL, NULL, NULL, 'self', 'cancelled', NULL, NULL, 0, '2026-10-15 10:27:00', NULL, NULL, NULL, 1),
(7887, 1, '2026-10-15', 23, 'MO-E023', '7829147efecf820ac167ad5c67291ac5', 5, 'Financial assistance', 'Jose Fernandez', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-15 10:31:45', '2026-10-15 10:40:58', '2026-10-15 10:40:58', '2026-10-15 10:51:26', 1),
(7888, 1, '2026-10-15', 24, 'MO-F024', '7a568ec64ae7bffe605d095643a5a04b', 6, 'Other', 'Teresita Cruz', NULL, NULL, NULL, 'staff', 'done', 56, 'Angelu', 0, '2026-10-15 10:32:30', '2026-10-15 10:41:24', '2026-10-15 10:41:24', '2026-10-15 10:42:54', 1),
(7889, 1, '2026-10-15', 25, 'MO-E025', '69864158818e4115dd6044e8c90ff872', 5, 'Financial assistance', 'Danilo Gonzales', NULL, NULL, NULL, 'staff', 'skipped', 54, 'Jean Frias', 0, '2026-10-15 10:34:13', '2026-10-15 10:45:38', NULL, NULL, 1),
(7890, 1, '2026-10-15', 26, 'MO-F026', '08337e52eb4755a12add65eca6c6cba4', 6, 'Other', 'Lourdes Mendoza', NULL, NULL, NULL, 'staff', 'done', 55, 'Sir Henjie', 0, '2026-10-15 10:42:38', '2026-10-15 11:05:41', '2026-10-15 11:05:41', '2026-10-15 11:11:55', 1),
(7891, 1, '2026-10-15', 27, 'MO-E027', 'e4b08383212ec1cac8155779dc2033f4', 5, 'Financial assistance', 'Ramon Rivera', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-15 10:48:18', '2026-10-15 10:55:06', '2026-10-15 10:55:06', '2026-10-15 11:08:28', 1),
(7892, 1, '2026-10-15', 28, 'MO-F028', 'd5ca3a48432cbf0b55868e2515e3ab8c', 6, 'Other', 'Josefina Villanueva', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-15 10:49:06', '2026-10-15 10:51:50', '2026-10-15 10:51:50', '2026-10-15 11:02:47', 1),
(7893, 1, '2026-10-15', 29, 'MO-E029', '316920289bf6ffd404113628db079ce9', 5, 'Financial assistance', 'Manuel Lopez', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-15 10:50:51', '2026-10-15 10:52:36', '2026-10-15 10:52:36', '2026-10-15 10:59:43', 1),
(7894, 1, '2026-10-15', 30, 'MO-E030', '28b6c6714d81e4d8a3978e2e20ede3ef', 5, 'Financial assistance', 'Ana Garcia', NULL, NULL, NULL, 'staff', 'skipped', 55, 'Sir Henjie', 0, '2026-10-15 10:58:21', '2026-10-15 11:05:57', NULL, NULL, 1),
(7895, 1, '2026-10-15', 31, 'MO-D031', '5929372851cd55498ed8798b8acda282', 4, 'Courtesy call', 'Rogelio Flores', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-15 10:59:45', '2026-10-15 11:08:26', '2026-10-15 11:08:26', '2026-10-15 11:14:51', 1),
(7896, 1, '2026-10-15', 32, 'MO-C032', '4db4b37a195875d53f3b9d9087e5c68d', 3, 'Personal concerns', 'Grace Bautista', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-15 11:00:23', '2026-10-15 11:04:40', '2026-10-15 11:04:40', '2026-10-15 11:12:43', 1),
(7897, 1, '2026-10-15', 33, 'MO-C033', '2d56a8090980515dc66664315bbc5206', 3, 'Personal concerns', 'Gerardo Torres', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-15 11:03:26', '2026-10-15 11:11:04', '2026-10-15 11:11:04', '2026-10-15 11:20:29', 1),
(7898, 1, '2026-10-15', 34, 'MO-A034', '5b79264b9ba8fef1b1ff0dec5e2a4013', 1, 'Mayor\'s Clearance', 'Liza Dela Cruz', NULL, NULL, NULL, 'self', 'skipped', 55, 'Sir Henjie', 0, '2026-10-15 11:06:49', '2026-10-15 11:16:29', NULL, NULL, 1),
(7899, 1, '2026-10-15', 35, 'MO-D035', '75c4062be106752594c8fe0ddc6508d2', 4, 'Courtesy call', 'Antonio Pascual', NULL, NULL, NULL, 'staff', 'cancelled', NULL, NULL, 0, '2026-10-15 11:19:33', NULL, NULL, NULL, 1),
(7900, 1, '2026-10-15', 36, 'MO-B036', '888cf45ebde916fb09fb7f597f6e1d72', 2, 'Business concerns', 'Elena Reyes', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-15 11:39:31', '2026-10-15 11:41:54', '2026-10-15 11:41:54', '2026-10-15 11:53:32', 1),
(7901, 1, '2026-10-15', 37, 'MO-C037', 'a282be17f54bb421d941a11f29a361bb', 3, 'Personal concerns', 'Pedro Ramos', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-15 11:40:38', '2026-10-15 11:44:55', '2026-10-15 11:44:55', '2026-10-15 11:50:42', 1),
(7902, 1, '2026-10-15', 38, 'MO-B038', '3bde4320a77bf1f8805de5f2163411fb', 2, 'Business concerns', 'Marites Santos', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-15 11:58:52', '2026-10-15 12:08:06', '2026-10-15 12:08:06', '2026-10-15 12:11:53', 1),
(7903, 1, '2026-10-15', 39, 'MO-C039', 'dad41f123056cb4d6e7cbf600c24ea9d', 3, 'Personal concerns', 'Alfredo Castillo', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-15 12:04:48', '2026-10-15 12:08:03', '2026-10-15 12:08:03', '2026-10-15 12:17:20', 1),
(7904, 1, '2026-10-15', 40, 'MO-A040', '4cfe05f3924674a9cdaa4b7bc79623a9', 1, 'Mayor\'s Clearance', 'Maria Cortez', NULL, NULL, NULL, 'self', 'skipped', 55, 'Sir Henjie', 0, '2026-10-15 12:09:35', '2026-10-15 12:15:45', NULL, NULL, 1),
(7905, 1, '2026-10-15', 41, 'MO-A041', 'e79defd4ca4e91383aa992ac5e3d5cd3', 1, 'Mayor\'s Clearance', 'Eduardo Domingo', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-15 12:19:10', '2026-10-15 12:24:52', '2026-10-15 12:24:52', '2026-10-15 12:27:36', 1),
(7906, 1, '2026-10-15', 42, 'MO-B042', '354251be8c097f04caa4d86671ba00e0', 2, 'Business concerns', 'Rosario Mercado', NULL, NULL, NULL, 'self', 'skipped', 54, 'Jean Frias', 0, '2026-10-15 12:21:10', '2026-10-15 12:25:55', NULL, NULL, 1),
(7907, 1, '2026-10-15', 43, 'MO-F043', '5307b2dd5f63dea0149025ef1f6161e8', 6, 'Other', 'Ricardo Navarro', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-15 12:27:04', '2026-10-15 12:29:55', '2026-10-15 12:29:55', '2026-10-15 12:45:53', 1),
(7908, 1, '2026-10-15', 44, 'MO-A044', 'bb13b8fcaa2840349fdb28c684742265', 1, 'Mayor\'s Clearance', 'Carmela Salazar', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-15 12:33:29', '2026-10-15 12:33:59', '2026-10-15 12:33:59', '2026-10-15 12:40:40', 1),
(7909, 1, '2026-10-15', 45, 'MO-A045', '64a5afc26b41ca6c8a56a4a505ba52bf', 1, 'Mayor\'s Clearance', 'Felipe Aquino', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-15 13:11:01', '2026-10-15 13:25:06', '2026-10-15 13:25:06', '2026-10-15 13:34:46', 1),
(7910, 1, '2026-10-15', 46, 'MO-E046', 'd4dc969167384e28499e62e389cd4391', 5, 'Financial assistance', 'Cynthia Fernandez', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-15 13:23:55', '2026-10-15 13:38:24', '2026-10-15 13:38:24', '2026-10-15 13:40:25', 1),
(7911, 1, '2026-10-15', 47, 'MO-A047', '0226a8110fa7bfd40c18a5af1f397d37', 1, 'Mayor\'s Clearance', 'Jose Cruz', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-15 13:42:03', '2026-10-15 13:48:24', '2026-10-15 13:48:24', '2026-10-15 13:55:59', 1),
(7912, 1, '2026-10-15', 48, 'MO-D048', 'e5bd69257a8791a86d3102fe70126204', 4, 'Courtesy call', 'Teresita Gonzales', NULL, NULL, NULL, 'staff', 'done', 55, 'Sir Henjie', 0, '2026-10-15 13:46:51', '2026-10-15 13:56:15', '2026-10-15 13:56:15', '2026-10-15 14:14:35', 1),
(7913, 1, '2026-10-15', 49, 'MO-D049', 'eae871947816ae18c80bc6ad68bdd0b8', 4, 'Courtesy call', 'Danilo Mendoza', NULL, NULL, NULL, 'staff', 'done', 55, 'Sir Henjie', 0, '2026-10-15 13:49:50', '2026-10-15 13:51:13', '2026-10-15 13:51:13', '2026-10-15 13:58:07', 1),
(7914, 1, '2026-10-15', 50, 'MO-C050', 'b5093ad017694f4ed1506ad3a8281634', 3, 'Personal concerns', 'Lourdes Rivera', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-15 13:49:51', '2026-10-15 14:02:29', '2026-10-15 14:02:29', '2026-10-15 14:11:04', 1),
(7915, 1, '2026-10-15', 51, 'MO-E051', '78defbd22d4d4de672343211be464a6f', 5, 'Financial assistance', 'Ramon Villanueva', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-15 13:50:36', '2026-10-15 13:58:49', '2026-10-15 13:58:49', '2026-10-15 14:04:08', 1),
(7916, 1, '2026-10-15', 52, 'MO-A052', 'a07e46c52ea076010c956612de796292', 1, 'Mayor\'s Clearance', 'Josefina Lopez', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-15 13:50:56', '2026-10-15 14:00:02', '2026-10-15 14:00:02', '2026-10-15 14:03:54', 1),
(7917, 1, '2026-10-15', 53, 'MO-A053', '3e6a2b58cdd574fea6cb34ac4618ad39', 1, 'Mayor\'s Clearance', 'Manuel Garcia', NULL, NULL, NULL, 'staff', 'done', 56, 'Angelu', 0, '2026-10-15 13:51:44', '2026-10-15 14:08:28', '2026-10-15 14:08:28', '2026-10-15 14:13:25', 1),
(7918, 1, '2026-10-15', 54, 'MO-F054', 'ed0ff2f551fc4f99cc628ece40042a4e', 6, 'Other', 'Ana Flores', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-15 14:00:27', '2026-10-15 14:07:16', '2026-10-15 14:07:16', '2026-10-15 14:18:38', 1),
(7919, 1, '2026-10-15', 55, 'MO-E055', '0defd90d08238bc78fdedd1dbc4e3231', 5, 'Financial assistance', 'Rogelio Bautista', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-15 14:23:38', '2026-10-15 14:29:36', '2026-10-15 14:29:36', '2026-10-15 14:31:34', 1),
(7920, 1, '2026-10-15', 56, 'MO-E056', '5b93737c788f734252d12f4b28b6a2d4', 5, 'Financial assistance', 'Grace Torres', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-15 14:33:37', '2026-10-15 14:35:13', '2026-10-15 14:35:13', '2026-10-15 14:44:27', 1),
(7921, 1, '2026-10-15', 57, 'MO-E057', '2cace9765bcbf23393fbfc3d0bb4908a', 5, 'Financial assistance', 'Gerardo Dela Cruz', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-15 14:38:34', '2026-10-15 14:46:55', '2026-10-15 14:46:55', '2026-10-15 14:50:36', 1),
(7922, 1, '2026-10-15', 58, 'MO-F058', '5d9e8b56806627dbdeb8252e3c05f946', 6, 'Other', 'Liza Pascual', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-15 14:58:36', '2026-10-15 15:00:19', '2026-10-15 15:00:19', '2026-10-15 15:08:04', 1),
(7923, 1, '2026-10-15', 59, 'MO-E059', '755de2151c8aea0e40ba1319a4b9a308', 5, 'Financial assistance', 'Antonio Reyes', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-15 15:08:59', '2026-10-15 15:15:32', '2026-10-15 15:15:32', '2026-10-15 15:18:02', 1),
(7924, 1, '2026-10-15', 60, 'MO-E060', '5a38517bc948c83f1d47bfb3e9defd9f', 5, 'Financial assistance', 'Elena Ramos', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-15 15:17:32', '2026-10-15 15:20:59', '2026-10-15 15:20:59', '2026-10-15 15:24:37', 1),
(7925, 1, '2026-10-15', 61, 'MO-F061', 'b97378c30a2835e0118b493425e00c34', 6, 'Other', 'Pedro Santos', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-15 15:26:40', '2026-10-15 15:27:58', '2026-10-15 15:27:58', '2026-10-15 15:37:31', 1),
(7926, 1, '2026-10-15', 62, 'MO-E062', '75c591f4b0575263f082e4e9c15aa11d', 5, 'Financial assistance', 'Marites Castillo', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-15 15:48:44', '2026-10-15 15:51:36', '2026-10-15 15:51:36', '2026-10-15 15:59:43', 1),
(7927, 1, '2026-10-15', 63, 'MO-A063', '1bbea9c6b12e60676d70f8eab7ba691b', 1, 'Mayor\'s Clearance', 'Alfredo Cortez', NULL, NULL, NULL, 'staff', 'done', 55, 'Sir Henjie', 0, '2026-10-15 15:53:18', '2026-10-15 15:55:16', '2026-10-15 15:55:16', '2026-10-15 16:05:17', 1),
(7928, 1, '2026-10-15', 64, 'MO-B064', 'fc08b038d6500b37a0bf06d4248a10a3', 2, 'Business concerns', 'Maria Domingo', NULL, NULL, NULL, 'staff', 'done', 54, 'Jean Frias', 0, '2026-10-15 16:26:51', '2026-10-15 16:27:21', '2026-10-15 16:27:21', '2026-10-15 16:38:29', 1),
(7929, 1, '2026-10-16', 1, 'MO-A001', '53f2c32bf1f8e6415a972a91268d7a18', 1, 'Mayor\'s Clearance', 'Grace Cruz', NULL, NULL, NULL, 'staff', 'done', 56, 'Angelu', 0, '2026-10-16 08:04:44', '2026-10-16 08:07:20', '2026-10-16 08:07:20', '2026-10-16 08:17:17', 1),
(7930, 1, '2026-10-16', 2, 'MO-B002', '163ff1f66c3dc04e56accf430f1ac9e3', 2, 'Business concerns', 'Gerardo Gonzales', NULL, NULL, NULL, 'staff', 'done', 55, 'Sir Henjie', 0, '2026-10-16 08:09:48', '2026-10-16 08:11:10', '2026-10-16 08:11:10', '2026-10-16 08:17:27', 1),
(7931, 1, '2026-10-16', 3, 'MO-B003', '729e41930e59e44c31ecb95b82db0f1a', 2, 'Business concerns', 'Liza Mendoza', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-16 08:24:13', '2026-10-16 08:27:12', '2026-10-16 08:27:12', '2026-10-16 08:38:57', 1),
(7932, 1, '2026-10-16', 4, 'MO-E004', '3402f00169de011c7adef1649ea23aa3', 5, 'Financial assistance', 'Antonio Rivera', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-16 08:26:40', '2026-10-16 08:29:07', '2026-10-16 08:29:07', '2026-10-16 08:32:36', 1),
(7933, 1, '2026-10-16', 5, 'MO-B005', 'c49a74054b08c4b92dcfb2c2bd16cf98', 2, 'Business concerns', 'Elena Villanueva', NULL, NULL, NULL, 'self', 'cancelled', NULL, NULL, 0, '2026-10-16 08:28:41', NULL, NULL, NULL, 1),
(7934, 1, '2026-10-16', 6, 'MO-C006', '22bcef0c1e33326edae9bd5ca24954b9', 3, 'Personal concerns', 'Pedro Lopez', NULL, NULL, NULL, 'staff', 'done', 55, 'Sir Henjie', 0, '2026-10-16 08:37:09', '2026-10-16 08:38:06', '2026-10-16 08:38:06', '2026-10-16 08:48:33', 1),
(7935, 1, '2026-10-16', 7, 'MO-E007', 'f620b1c8585cec5352a5b1fe165f7228', 5, 'Financial assistance', 'Marites Garcia', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-16 09:01:30', '2026-10-16 09:05:18', '2026-10-16 09:05:18', '2026-10-16 09:13:09', 1),
(7936, 1, '2026-10-16', 8, 'MO-E008', '2228f3e4e31b96ef7ed850f1539e0d6f', 5, 'Financial assistance', 'Alfredo Flores', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-16 09:09:59', '2026-10-16 09:19:08', '2026-10-16 09:19:08', '2026-10-16 09:30:03', 1),
(7937, 1, '2026-10-16', 9, 'MO-D009', 'bda895f839221ce0f1c2fc0b1916709a', 4, 'Courtesy call', 'Maria Bautista', NULL, NULL, NULL, 'staff', 'done', 55, 'Sir Henjie', 0, '2026-10-16 09:12:06', '2026-10-16 09:14:50', '2026-10-16 09:14:50', '2026-10-16 09:30:05', 1),
(7938, 1, '2026-10-16', 10, 'MO-A010', '3d11424fa07daaa5ab7777615805e9f2', 1, 'Mayor\'s Clearance', 'Eduardo Torres', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-16 09:14:22', '2026-10-16 09:19:55', '2026-10-16 09:19:55', '2026-10-16 09:21:25', 1),
(7939, 1, '2026-10-16', 11, 'MO-B011', 'dc59c80fa3c5b1345c4bb290c4d9e245', 2, 'Business concerns', 'Rosario Dela Cruz', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-16 09:15:31', '2026-10-16 09:25:11', '2026-10-16 09:25:11', '2026-10-16 09:34:02', 1),
(7940, 1, '2026-10-16', 12, 'MO-A012', '0e8897aa6b23da56eb28284eeba8891b', 1, 'Mayor\'s Clearance', 'Ricardo Pascual', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-16 09:15:42', '2026-10-16 09:45:09', '2026-10-16 09:45:09', '2026-10-16 09:50:13', 1),
(7941, 1, '2026-10-16', 13, 'MO-A013', '7865ca16da9584818b1195b605f64d09', 1, 'Mayor\'s Clearance', 'Carmela Reyes', NULL, NULL, NULL, 'self', 'cancelled', NULL, NULL, 0, '2026-10-16 09:21:15', NULL, NULL, NULL, 1),
(7942, 1, '2026-10-16', 14, 'MO-B014', '848fba6e30040aeccf8165b57d07ce92', 2, 'Business concerns', 'Felipe Ramos', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-16 09:21:42', '2026-10-16 09:34:05', '2026-10-16 09:34:05', '2026-10-16 09:47:05', 1),
(7943, 1, '2026-10-16', 15, 'MO-C015', '3103126adf90b16e9f546c20faee0444', 3, 'Personal concerns', 'Cynthia Santos', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-16 09:29:24', '2026-10-16 09:39:23', '2026-10-16 09:39:23', '2026-10-16 09:46:05', 1),
(7944, 1, '2026-10-16', 16, 'MO-D016', 'eb04dd2a9c09ed15366f36462455d93a', 4, 'Courtesy call', 'Jose Castillo', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-16 09:54:14', '2026-10-16 09:55:42', '2026-10-16 09:55:42', '2026-10-16 10:02:10', 1),
(7945, 1, '2026-10-16', 17, 'MO-B017', 'a51f7cf476fa6d78ce3f9d6971e0648c', 2, 'Business concerns', 'Teresita Cortez', NULL, NULL, NULL, 'staff', 'done', 55, 'Sir Henjie', 0, '2026-10-16 09:55:33', '2026-10-16 10:02:40', '2026-10-16 10:02:40', '2026-10-16 10:20:23', 1),
(7946, 1, '2026-10-16', 18, 'MO-A018', 'e281b48fcde4c7edac2990da76a63818', 1, 'Mayor\'s Clearance', 'Danilo Domingo', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-16 09:57:23', '2026-10-16 10:04:01', '2026-10-16 10:04:01', '2026-10-16 10:15:25', 1),
(7947, 1, '2026-10-16', 19, 'MO-A019', 'af0c2f90bb5251a07d4b6b2061c7b798', 1, 'Mayor\'s Clearance', 'Lourdes Mercado', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-16 10:01:07', '2026-10-16 10:03:56', '2026-10-16 10:03:56', '2026-10-16 10:10:49', 1),
(7948, 1, '2026-10-16', 20, 'MO-A020', '3b8c3538d358a7e1679a958422e24bbc', 1, 'Mayor\'s Clearance', 'Ramon Navarro', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-16 10:06:32', '2026-10-16 10:07:32', '2026-10-16 10:07:32', '2026-10-16 10:09:20', 1),
(7949, 1, '2026-10-16', 21, 'MO-A021', '4ba0c958af7553e9940b6952437d1e78', 1, 'Mayor\'s Clearance', 'Josefina Salazar', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-16 10:33:48', '2026-10-16 10:47:01', '2026-10-16 10:47:01', '2026-10-16 10:51:41', 1),
(7950, 1, '2026-10-16', 22, 'MO-D022', 'a2dc3e77c6c0e9790341404477d8fae4', 4, 'Courtesy call', 'Manuel Aquino', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-16 10:48:42', '2026-10-16 10:50:05', '2026-10-16 10:50:05', '2026-10-16 11:08:38', 1),
(7951, 1, '2026-10-16', 23, 'MO-A023', '3de2d74549171535c65a1b2e4e49da6d', 1, 'Mayor\'s Clearance', 'Ana Fernandez', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-16 11:04:18', '2026-10-16 11:10:38', '2026-10-16 11:10:38', '2026-10-16 11:17:20', 1),
(7952, 1, '2026-10-16', 24, 'MO-A024', '7941c8fc407c3b2a57002fd3d19de05c', 1, 'Mayor\'s Clearance', 'Rogelio Cruz', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-16 11:08:24', '2026-10-16 11:11:32', '2026-10-16 11:11:32', '2026-10-16 11:17:41', 1),
(7953, 1, '2026-10-16', 25, 'MO-B025', 'fb9a11696bbc641b3a3b3c30ff952b85', 2, 'Business concerns', 'Grace Gonzales', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-16 11:11:51', '2026-10-16 11:17:19', '2026-10-16 11:17:19', '2026-10-16 11:28:27', 1),
(7954, 1, '2026-10-16', 26, 'MO-E026', '7435b063adae80a1e8f94bfa599fbda9', 5, 'Financial assistance', 'Gerardo Mendoza', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-16 11:12:13', '2026-10-16 11:15:51', '2026-10-16 11:15:51', '2026-10-16 11:27:19', 1),
(7955, 1, '2026-10-16', 27, 'MO-B027', 'bbbeafaf68218dfb38cf2d1efc7a0ed2', 2, 'Business concerns', 'Liza Rivera', NULL, NULL, NULL, 'staff', 'done', 55, 'Sir Henjie', 0, '2026-10-16 11:41:35', '2026-10-16 11:45:05', '2026-10-16 11:45:05', '2026-10-16 11:49:22', 1),
(7956, 1, '2026-10-16', 28, 'MO-A028', '118fd7fb10297c0ca83783cb36ea1c50', 1, 'Mayor\'s Clearance', 'Antonio Villanueva', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-16 11:55:26', '2026-10-16 12:06:01', '2026-10-16 12:06:01', '2026-10-16 12:07:33', 1),
(7957, 1, '2026-10-16', 29, 'MO-A029', '9f7a8d2e5c06e1e6e71209d1847f2dd3', 1, 'Mayor\'s Clearance', 'Elena Lopez', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-16 12:05:08', '2026-10-16 12:09:07', '2026-10-16 12:09:07', '2026-10-16 12:13:26', 1),
(7958, 1, '2026-10-16', 30, 'MO-A030', '47234421a6de45e3eab887294bc4a842', 1, 'Mayor\'s Clearance', 'Pedro Garcia', NULL, NULL, NULL, 'self', 'skipped', 54, 'Jean Frias', 0, '2026-10-16 12:14:44', '2026-10-16 12:19:03', NULL, NULL, 1),
(7959, 1, '2026-10-16', 31, 'MO-A031', 'f87db4e7a5455bbc236ba004df23608e', 1, 'Mayor\'s Clearance', 'Marites Flores', NULL, NULL, NULL, 'self', 'cancelled', NULL, NULL, 0, '2026-10-16 12:25:13', NULL, NULL, NULL, 1),
(7960, 1, '2026-10-16', 32, 'MO-A032', '60435a954404e319a69b6127b7538410', 1, 'Mayor\'s Clearance', 'Alfredo Bautista', NULL, NULL, NULL, 'self', 'skipped', 55, 'Sir Henjie', 0, '2026-10-16 12:33:30', '2026-10-16 12:37:45', NULL, NULL, 1),
(7961, 1, '2026-10-16', 33, 'MO-B033', '77c9688aa97117d3d724a8f1e26b66de', 2, 'Business concerns', 'Maria Torres', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-16 12:48:11', '2026-10-16 12:49:46', '2026-10-16 12:49:46', '2026-10-16 13:00:22', 1),
(7962, 1, '2026-10-16', 34, 'MO-E034', '63a58458bec9aa9cab86b6e94f922281', 5, 'Financial assistance', 'Eduardo Dela Cruz', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-16 12:51:13', '2026-10-16 12:55:57', '2026-10-16 12:55:57', '2026-10-16 13:17:25', 1),
(7963, 1, '2026-10-16', 35, 'MO-F035', 'd46dc03ee3d463d3ba1cb6ae0267b233', 6, 'Other', 'Rosario Pascual', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-16 13:00:37', '2026-10-16 13:07:46', '2026-10-16 13:07:46', '2026-10-16 13:20:19', 1),
(7964, 1, '2026-10-16', 36, 'MO-A036', 'ba0e1225c389216394be185873341afc', 1, 'Mayor\'s Clearance', 'Ricardo Reyes', NULL, NULL, NULL, 'staff', 'done', 56, 'Angelu', 0, '2026-10-16 13:03:00', '2026-10-16 13:06:10', '2026-10-16 13:06:10', '2026-10-16 13:10:51', 1),
(7965, 1, '2026-10-16', 37, 'MO-A037', '36efd661f29de564b00f4f60a97f7b2b', 1, 'Mayor\'s Clearance', 'Carmela Ramos', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-16 13:05:25', '2026-10-16 13:05:55', '2026-10-16 13:05:55', '2026-10-16 13:12:15', 1),
(7966, 1, '2026-10-16', 38, 'MO-C038', '6770abd52f11907b05443732ffee14df', 3, 'Personal concerns', 'Felipe Santos', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-16 13:06:26', '2026-10-16 13:08:15', '2026-10-16 13:08:15', '2026-10-16 13:12:58', 1),
(7967, 1, '2026-10-16', 39, 'MO-E039', '638a41030486e47a9cf9b46ea65c883f', 5, 'Financial assistance', 'Cynthia Castillo', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-16 13:08:06', '2026-10-16 13:19:23', '2026-10-16 13:19:23', '2026-10-16 13:28:48', 1),
(7968, 1, '2026-10-16', 40, 'MO-E040', '1e51cff07971d6f3725c97b5cb9c330d', 5, 'Financial assistance', 'Jose Cortez', NULL, NULL, NULL, 'staff', 'skipped', 54, 'Jean Frias', 0, '2026-10-16 13:08:45', '2026-10-16 13:25:41', NULL, NULL, 1),
(7969, 1, '2026-10-16', 41, 'MO-C041', '61b6e0be3ce4dc4238cb269e3997a6d1', 3, 'Personal concerns', 'Teresita Domingo', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-16 13:12:39', '2026-10-16 13:22:03', '2026-10-16 13:22:03', '2026-10-16 13:27:56', 1),
(7970, 1, '2026-10-16', 42, 'MO-E042', '507d33a4065598c17725133d55386a1c', 5, 'Financial assistance', 'Danilo Mercado', NULL, NULL, NULL, 'staff', 'done', 55, 'Sir Henjie', 0, '2026-10-16 13:13:50', '2026-10-16 13:17:26', '2026-10-16 13:17:26', '2026-10-16 13:26:07', 1),
(7971, 1, '2026-10-16', 43, 'MO-A043', '21c58b66aab997b5254038e0dca3f0d5', 1, 'Mayor\'s Clearance', 'Lourdes Navarro', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-16 13:22:25', '2026-10-16 13:24:14', '2026-10-16 13:24:14', '2026-10-16 13:27:49', 1),
(7972, 1, '2026-10-16', 44, 'MO-A044', '41dcb36f64d5ea7d691953c19509eb16', 1, 'Mayor\'s Clearance', 'Ramon Salazar', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-16 13:31:24', '2026-10-16 13:36:53', '2026-10-16 13:36:53', '2026-10-16 13:40:20', 1),
(7973, 1, '2026-10-16', 45, 'MO-C045', 'da8e234c2f1f4a4c8634e189b8efbc3e', 3, 'Personal concerns', 'Josefina Aquino', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-16 13:39:07', '2026-10-16 13:49:24', '2026-10-16 13:49:24', '2026-10-16 13:51:47', 1),
(7974, 1, '2026-10-16', 46, 'MO-F046', '01b53d2b9a56b4d04e89eb194b40465b', 6, 'Other', 'Manuel Fernandez', NULL, NULL, NULL, 'staff', 'done', 54, 'Jean Frias', 0, '2026-10-16 13:41:12', '2026-10-16 13:47:42', '2026-10-16 13:47:42', '2026-10-16 13:51:12', 1),
(7975, 1, '2026-10-16', 47, 'MO-C047', '908099e9b0fb4c507b0a1e6a59b05828', 3, 'Personal concerns', 'Ana Cruz', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-16 13:45:13', '2026-10-16 13:50:55', '2026-10-16 13:50:55', '2026-10-16 13:59:20', 1),
(7976, 1, '2026-10-16', 48, 'MO-C048', '9e3bfce2535d5d0ba16623eef66698a8', 3, 'Personal concerns', 'Rogelio Gonzales', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-16 13:49:55', '2026-10-16 13:56:03', '2026-10-16 13:56:03', '2026-10-16 14:03:30', 1),
(7977, 1, '2026-10-16', 49, 'MO-A049', '556babac25523aefe72ebb6289d00a36', 1, 'Mayor\'s Clearance', 'Grace Mendoza', NULL, NULL, NULL, 'staff', 'done', 55, 'Sir Henjie', 0, '2026-10-16 14:07:14', '2026-10-16 14:13:20', '2026-10-16 14:13:20', '2026-10-16 14:16:32', 1),
(7978, 1, '2026-10-16', 50, 'MO-E050', '5269e5189655362654ad35b3c109cb16', 5, 'Financial assistance', 'Gerardo Rivera', NULL, NULL, NULL, 'staff', 'done', 54, 'Jean Frias', 0, '2026-10-16 14:14:27', '2026-10-16 14:24:55', '2026-10-16 14:24:55', '2026-10-16 14:41:06', 1),
(7979, 1, '2026-10-16', 51, 'MO-D051', '855a369119b68f190294f91a8aecfdde', 4, 'Courtesy call', 'Liza Villanueva', NULL, NULL, NULL, 'staff', 'done', 54, 'Jean Frias', 0, '2026-10-16 14:15:45', '2026-10-16 14:18:59', '2026-10-16 14:18:59', '2026-10-16 14:29:58', 1),
(7980, 1, '2026-10-16', 52, 'MO-A052', 'c6045a625399d71829e706d56897b3ed', 1, 'Mayor\'s Clearance', 'Antonio Lopez', NULL, NULL, NULL, 'staff', 'cancelled', NULL, NULL, 0, '2026-10-16 14:17:11', NULL, NULL, NULL, 1),
(7981, 1, '2026-10-16', 53, 'MO-C053', '96262e9d3b84a479d574f8cfc78b44ac', 3, 'Personal concerns', 'Elena Garcia', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-16 14:18:38', '2026-10-16 14:20:45', '2026-10-16 14:20:45', '2026-10-16 14:31:41', 1),
(7982, 1, '2026-10-16', 54, 'MO-C054', '1f9542dc7f41ac764f77bd44181b2c4d', 3, 'Personal concerns', 'Pedro Flores', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-16 14:24:30', '2026-10-16 14:36:08', '2026-10-16 14:36:08', '2026-10-16 14:44:40', 1),
(7983, 1, '2026-10-16', 55, 'MO-C055', 'e09f7b7434042560906f57cd9b67d379', 3, 'Personal concerns', 'Marites Bautista', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-16 14:48:09', '2026-10-16 14:55:15', '2026-10-16 14:55:15', '2026-10-16 15:01:44', 1),
(7984, 1, '2026-10-16', 56, 'MO-C056', '45ad2981be45277d84adbe887677aed0', 3, 'Personal concerns', 'Alfredo Torres', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-16 15:02:30', '2026-10-16 15:04:11', '2026-10-16 15:04:11', '2026-10-16 15:07:23', 1),
(7985, 1, '2026-10-16', 57, 'MO-F057', '104469480a6b3f7d3c97b53c7bc44279', 6, 'Other', 'Maria Dela Cruz', NULL, NULL, NULL, 'staff', 'done', 54, 'Jean Frias', 0, '2026-10-16 15:14:26', '2026-10-16 15:17:20', '2026-10-16 15:17:20', '2026-10-16 15:22:31', 1),
(7986, 1, '2026-10-16', 58, 'MO-A058', 'c015e94949837785981b37518c6283ef', 1, 'Mayor\'s Clearance', 'Eduardo Pascual', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-16 15:18:28', '2026-10-16 15:19:36', '2026-10-16 15:19:36', '2026-10-16 15:22:48', 1),
(7987, 1, '2026-10-16', 59, 'MO-D059', '3b96077c531a2b7dd1a253f6b26375fb', 4, 'Courtesy call', 'Rosario Reyes', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-16 15:29:57', '2026-10-16 15:32:36', '2026-10-16 15:32:36', '2026-10-16 15:50:23', 1),
(7988, 1, '2026-10-16', 60, 'MO-C060', '9025714c804d694f7d1a4df7ecda4896', 3, 'Personal concerns', 'Ricardo Ramos', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-16 15:31:58', '2026-10-16 15:36:06', '2026-10-16 15:36:06', '2026-10-16 15:37:36', 1),
(7989, 1, '2026-10-16', 61, 'MO-C061', '3e3ae73e67ec0ed0efefa9ed6a6679ee', 3, 'Personal concerns', 'Carmela Santos', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-16 15:34:51', '2026-10-16 15:36:14', '2026-10-16 15:36:14', '2026-10-16 15:49:49', 1),
(7990, 1, '2026-10-16', 62, 'MO-F062', 'ae3f25344cf4d9bf22a9c67ded59e4ea', 6, 'Other', 'Felipe Castillo', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-16 15:48:31', '2026-10-16 15:51:28', '2026-10-16 15:51:28', '2026-10-16 16:01:26', 1),
(7991, 1, '2026-10-16', 63, 'MO-A063', '161262d1c74da02f474652c3db254647', 1, 'Mayor\'s Clearance', 'Cynthia Cortez', NULL, NULL, NULL, 'staff', 'done', 54, 'Jean Frias', 0, '2026-10-16 16:03:12', '2026-10-16 16:04:45', '2026-10-16 16:04:45', '2026-10-16 16:10:15', 1),
(7992, 1, '2026-10-19', 1, 'MO-E001', '6635a460c4c149182d70f21eaea4b057', 5, 'Financial assistance', 'Manuel Cruz', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-19 08:15:51', '2026-10-19 08:22:12', '2026-10-19 08:22:12', '2026-10-19 08:27:06', 1),
(7993, 1, '2026-10-19', 2, 'MO-D002', '18423c42b1b971c2122827a7dbea3dea', 4, 'Courtesy call', 'Ana Gonzales', NULL, NULL, NULL, 'staff', 'skipped', 56, 'Angelu', 0, '2026-10-19 08:19:50', '2026-10-19 08:22:09', NULL, NULL, 1),
(7994, 1, '2026-10-19', 3, 'MO-A003', '72efe1757479d73bfbf6ebcbaba97950', 1, 'Mayor\'s Clearance', 'Rogelio Mendoza', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-19 08:26:44', '2026-10-19 08:28:11', '2026-10-19 08:28:11', '2026-10-19 08:31:41', 1),
(7995, 1, '2026-10-19', 4, 'MO-E004', '8d4e464e4449405832e0456d21ef4f57', 5, 'Financial assistance', 'Grace Rivera', NULL, NULL, NULL, 'staff', 'done', 55, 'Sir Henjie', 0, '2026-10-19 08:30:53', '2026-10-19 08:32:40', '2026-10-19 08:32:40', '2026-10-19 08:39:25', 1),
(7996, 1, '2026-10-19', 5, 'MO-A005', '9fc8936e7baea4ea7f4dd91fd7213e24', 1, 'Mayor\'s Clearance', 'Gerardo Villanueva', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-19 08:34:04', '2026-10-19 08:35:09', '2026-10-19 08:35:09', '2026-10-19 08:38:33', 1),
(7997, 1, '2026-10-19', 6, 'MO-A006', '4f310257e9f4980498823f6bc8dd2a72', 1, 'Mayor\'s Clearance', 'Liza Lopez', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-19 08:37:05', '2026-10-19 08:39:39', '2026-10-19 08:39:39', '2026-10-19 08:44:10', 1),
(7998, 1, '2026-10-19', 7, 'MO-A007', '932fb2624288462616b4b9a766aab9e8', 1, 'Mayor\'s Clearance', 'Antonio Garcia', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-19 08:38:02', '2026-10-19 08:42:11', '2026-10-19 08:42:11', '2026-10-19 08:50:08', 1),
(7999, 1, '2026-10-19', 8, 'MO-A008', 'e1c7de3bfaa97f8dbfd1d6f8684cf876', 1, 'Mayor\'s Clearance', 'Elena Flores', NULL, NULL, NULL, 'staff', 'done', 55, 'Sir Henjie', 0, '2026-10-19 08:56:44', '2026-10-19 08:57:49', '2026-10-19 08:57:49', '2026-10-19 09:02:02', 1),
(8000, 1, '2026-10-19', 9, 'MO-A009', '8fd64e74d94fb666edb57f91d02f8210', 1, 'Mayor\'s Clearance', 'Pedro Bautista', NULL, NULL, NULL, 'staff', 'done', 54, 'Jean Frias', 0, '2026-10-19 09:04:28', '2026-10-19 09:12:27', '2026-10-19 09:12:27', '2026-10-19 09:23:24', 1),
(8001, 1, '2026-10-19', 10, 'MO-B010', 'ce8dd4c59833e187944614034da93b1e', 2, 'Business concerns', 'Marites Torres', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-19 09:06:19', '2026-10-19 09:18:16', '2026-10-19 09:18:16', '2026-10-19 09:25:43', 1),
(8002, 1, '2026-10-19', 11, 'MO-B011', '27ac112234c9d42991870060e79fafb3', 2, 'Business concerns', 'Alfredo Dela Cruz', NULL, NULL, NULL, 'staff', 'done', 54, 'Jean Frias', 0, '2026-10-19 09:18:19', '2026-10-19 09:22:48', '2026-10-19 09:22:48', '2026-10-19 09:27:39', 1),
(8003, 1, '2026-10-19', 12, 'MO-A012', '3c9da1b2b784435df44dca530505fe20', 1, 'Mayor\'s Clearance', 'Maria Pascual', NULL, NULL, NULL, 'self', 'cancelled', NULL, NULL, 0, '2026-10-19 09:21:18', NULL, NULL, NULL, 1),
(8004, 1, '2026-10-19', 13, 'MO-A013', 'bc47e32d7f1b3f70343b8a1e3ab940f0', 1, 'Mayor\'s Clearance', 'Eduardo Reyes', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-19 09:24:27', '2026-10-19 09:30:18', '2026-10-19 09:30:18', '2026-10-19 09:35:16', 1),
(8005, 1, '2026-10-19', 14, 'MO-D014', '0f7c0b2bcc88e3b53ae4760f8d321aa7', 4, 'Courtesy call', 'Rosario Ramos', NULL, NULL, NULL, 'staff', 'done', 54, 'Jean Frias', 0, '2026-10-19 09:25:13', '2026-10-19 09:28:37', '2026-10-19 09:28:37', '2026-10-19 09:34:07', 1),
(8006, 1, '2026-10-19', 15, 'MO-B015', '19d0985b52e44902fe2b579c324302de', 2, 'Business concerns', 'Ricardo Santos', NULL, NULL, NULL, 'self', 'skipped', 54, 'Jean Frias', 0, '2026-10-19 09:30:58', '2026-10-19 09:35:49', NULL, NULL, 1),
(8007, 1, '2026-10-19', 16, 'MO-C016', 'c37a74e6d4a622fe0987194bb3743bd2', 3, 'Personal concerns', 'Carmela Castillo', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-19 09:31:53', '2026-10-19 09:39:27', '2026-10-19 09:39:27', '2026-10-19 09:44:15', 1),
(8008, 1, '2026-10-19', 17, 'MO-E017', '73e7860abb6ddde033d2157cf7945116', 5, 'Financial assistance', 'Felipe Cortez', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-19 09:55:35', '2026-10-19 10:05:04', '2026-10-19 10:05:04', '2026-10-19 10:14:52', 1),
(8009, 1, '2026-10-19', 18, 'MO-B018', '7f4a07136cb637a04b2c8b38eb3c4c98', 2, 'Business concerns', 'Cynthia Domingo', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-19 09:58:00', '2026-10-19 10:17:57', '2026-10-19 10:17:57', '2026-10-19 10:48:21', 1),
(8010, 1, '2026-10-19', 19, 'MO-D019', '166c867f40211f8489ebc8b2c9e2c29b', 4, 'Courtesy call', 'Jose Mercado', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-19 09:58:36', '2026-10-19 10:03:23', '2026-10-19 10:03:23', '2026-10-19 10:27:31', 1),
(8011, 1, '2026-10-19', 20, 'MO-A020', '6890bad4df9901af8375fc2372ba92f8', 1, 'Mayor\'s Clearance', 'Teresita Navarro', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-19 10:02:37', '2026-10-19 10:05:51', '2026-10-19 10:05:51', '2026-10-19 10:10:35', 1),
(8012, 1, '2026-10-19', 21, 'MO-B021', 'd9523e394950c7ae57ff9845173d09b9', 2, 'Business concerns', 'Danilo Salazar', NULL, NULL, NULL, 'staff', 'done', 54, 'Jean Frias', 0, '2026-10-19 10:05:57', '2026-10-19 10:09:23', '2026-10-19 10:09:23', '2026-10-19 10:18:45', 1),
(8013, 1, '2026-10-19', 22, 'MO-B022', 'd817ef20d9102e62e0877ffc0956a5b3', 2, 'Business concerns', 'Lourdes Aquino', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-19 10:08:18', '2026-10-19 10:14:10', '2026-10-19 10:14:10', '2026-10-19 10:28:37', 1),
(8014, 1, '2026-10-19', 23, 'MO-A023', '1938a7d6783e671149edef34f0b3522c', 1, 'Mayor\'s Clearance', 'Ramon Fernandez', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-19 10:12:41', '2026-10-19 10:14:31', '2026-10-19 10:14:31', '2026-10-19 10:18:07', 1),
(8015, 1, '2026-10-19', 24, 'MO-E024', 'b3208c0065004c812b75fc60c0b8f645', 5, 'Financial assistance', 'Josefina Cruz', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-19 10:21:00', '2026-10-19 10:24:53', '2026-10-19 10:24:53', '2026-10-19 10:32:52', 1),
(8016, 1, '2026-10-19', 25, 'MO-C025', 'c93bc6f2a39b85077b6935cb6aa23199', 3, 'Personal concerns', 'Manuel Gonzales', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-19 10:25:21', '2026-10-19 10:46:20', '2026-10-19 10:46:20', '2026-10-19 10:56:27', 1),
(8017, 1, '2026-10-19', 26, 'MO-E026', '00ea50a7b1dfdaff3ec96f4d4eabed53', 5, 'Financial assistance', 'Ana Mendoza', NULL, NULL, NULL, 'staff', 'skipped', 56, 'Angelu', 0, '2026-10-19 10:32:04', '2026-10-19 10:34:30', NULL, NULL, 1),
(8018, 1, '2026-10-19', 27, 'MO-C027', '6d3755a1c83b2dceab7684f13b2c13cd', 3, 'Personal concerns', 'Rogelio Rivera', NULL, NULL, NULL, 'staff', 'done', 54, 'Jean Frias', 0, '2026-10-19 10:35:10', '2026-10-19 10:46:20', '2026-10-19 10:46:20', '2026-10-19 11:01:50', 1),
(8019, 1, '2026-10-19', 28, 'MO-D028', '68ee84c7748297386645f0fb8525a14d', 4, 'Courtesy call', 'Grace Villanueva', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-19 10:37:10', '2026-10-19 10:47:11', '2026-10-19 10:47:11', '2026-10-19 10:53:19', 1),
(8020, 1, '2026-10-19', 29, 'MO-A029', '4618948e28c8455786ac96c2d0b5da8a', 1, 'Mayor\'s Clearance', 'Gerardo Lopez', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-19 10:42:59', '2026-10-19 10:50:48', '2026-10-19 10:50:48', '2026-10-19 11:01:23', 1),
(8021, 1, '2026-10-19', 30, 'MO-C030', '27533ee036991c8f5449527a3d0b63d6', 3, 'Personal concerns', 'Liza Garcia', NULL, NULL, NULL, 'staff', 'done', 56, 'Angelu', 0, '2026-10-19 10:43:16', '2026-10-19 10:50:00', '2026-10-19 10:50:00', '2026-10-19 10:59:13', 1),
(8022, 1, '2026-10-19', 31, 'MO-B031', '2100a3202a33a02cda04a8a65e5bc0a2', 2, 'Business concerns', 'Antonio Flores', NULL, NULL, NULL, 'self', 'cancelled', NULL, NULL, 0, '2026-10-19 10:46:48', NULL, NULL, NULL, 1),
(8023, 1, '2026-10-19', 32, 'MO-D032', 'edb6220c007d3ea349a0cc7b01e96a98', 4, 'Courtesy call', 'Elena Bautista', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-19 10:48:09', '2026-10-19 10:51:30', '2026-10-19 10:51:30', '2026-10-19 11:11:35', 1),
(8024, 1, '2026-10-19', 33, 'MO-A033', '5b955e2e93c82c9ee521cb89c6817e3d', 1, 'Mayor\'s Clearance', 'Pedro Torres', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-19 10:49:49', '2026-10-19 11:04:36', '2026-10-19 11:04:36', '2026-10-19 11:08:39', 1),
(8025, 1, '2026-10-19', 34, 'MO-B034', '1274b55f9ecfe49ababe2b4a134a1e11', 2, 'Business concerns', 'Marites Dela Cruz', NULL, NULL, NULL, 'staff', 'done', 54, 'Jean Frias', 0, '2026-10-19 10:50:31', '2026-10-19 10:58:41', '2026-10-19 10:58:41', '2026-10-19 11:04:20', 1),
(8026, 1, '2026-10-19', 35, 'MO-F035', '2ae795954af82a81937f7aa08a330274', 6, 'Other', 'Alfredo Pascual', NULL, NULL, NULL, 'self', 'skipped', 56, 'Angelu', 0, '2026-10-19 10:55:37', '2026-10-19 11:15:15', NULL, NULL, 1),
(8027, 1, '2026-10-19', 36, 'MO-A036', 'a22e7ae905f31a40cdcb303edefd1812', 1, 'Mayor\'s Clearance', 'Maria Reyes', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-19 10:58:04', '2026-10-19 11:01:47', '2026-10-19 11:01:47', '2026-10-19 11:08:18', 1),
(8028, 1, '2026-10-19', 37, 'MO-B037', 'effec6e87449c955dea34115b0495372', 2, 'Business concerns', 'Eduardo Ramos', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-19 10:59:15', '2026-10-19 11:07:34', '2026-10-19 11:07:34', '2026-10-19 11:17:11', 1),
(8029, 1, '2026-10-19', 38, 'MO-F038', 'd9a67e7eacbb5159bb8ff1b894df3ad1', 6, 'Other', 'Rosario Santos', NULL, NULL, NULL, 'self', 'skipped', 54, 'Jean Frias', 0, '2026-10-19 11:01:20', '2026-10-19 11:14:57', NULL, NULL, 1),
(8030, 1, '2026-10-19', 39, 'MO-C039', '2aebdcf474088b7352dd02a593ea292d', 3, 'Personal concerns', 'Ricardo Castillo', NULL, NULL, NULL, 'staff', 'done', 55, 'Sir Henjie', 0, '2026-10-19 11:07:28', '2026-10-19 11:13:10', '2026-10-19 11:13:10', '2026-10-19 11:15:59', 1),
(8031, 1, '2026-10-19', 40, 'MO-C040', '7e2a8e06871d148ee6674a3118a0194d', 3, 'Personal concerns', 'Carmela Cortez', NULL, NULL, NULL, 'staff', 'done', 54, 'Jean Frias', 0, '2026-10-19 11:21:32', '2026-10-19 11:31:29', '2026-10-19 11:31:29', '2026-10-19 11:46:53', 1),
(8032, 1, '2026-10-19', 41, 'MO-A041', '63ea7764eec477ff32b7c945c9747bd4', 1, 'Mayor\'s Clearance', 'Felipe Domingo', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-19 11:37:00', '2026-10-19 11:40:43', '2026-10-19 11:40:43', '2026-10-19 11:47:22', 1),
(8033, 1, '2026-10-19', 42, 'MO-A042', '2e0bcbaa411f5efef1f31ef7e77361f8', 1, 'Mayor\'s Clearance', 'Cynthia Mercado', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-19 11:39:59', '2026-10-19 11:44:24', '2026-10-19 11:44:24', '2026-10-19 11:51:46', 1),
(8034, 1, '2026-10-19', 43, 'MO-C043', '673fd3f5d34cc0264e40b13630c413bc', 3, 'Personal concerns', 'Jose Navarro', NULL, NULL, NULL, 'staff', 'done', 56, 'Angelu', 0, '2026-10-19 11:51:27', '2026-10-19 12:00:44', '2026-10-19 12:00:44', '2026-10-19 12:07:39', 1),
(8035, 1, '2026-10-19', 44, 'MO-E044', 'cfe8038e37443a0f06d44f7aed3e6b0e', 5, 'Financial assistance', 'Teresita Salazar', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-19 11:58:26', '2026-10-19 12:04:06', '2026-10-19 12:04:06', '2026-10-19 12:10:10', 1),
(8036, 1, '2026-10-19', 45, 'MO-B045', 'e350e13d3facdcf6debf8a6a1751889e', 2, 'Business concerns', 'Danilo Aquino', NULL, NULL, NULL, 'staff', 'done', 55, 'Sir Henjie', 0, '2026-10-19 11:58:36', '2026-10-19 12:17:15', '2026-10-19 12:17:15', '2026-10-19 12:23:35', 1),
(8037, 1, '2026-10-19', 46, 'MO-E046', 'd51c430bd67eed76d1e09b49c26d2a6d', 5, 'Financial assistance', 'Lourdes Fernandez', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-19 12:07:22', '2026-10-19 12:11:37', '2026-10-19 12:11:37', '2026-10-19 12:13:18', 1),
(8038, 1, '2026-10-19', 47, 'MO-A047', 'c17311e836470cfe83a7847afdb4274e', 1, 'Mayor\'s Clearance', 'Ramon Cruz', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-19 12:16:30', '2026-10-19 12:17:14', '2026-10-19 12:17:14', '2026-10-19 12:23:13', 1),
(8039, 1, '2026-10-19', 48, 'MO-D048', 'e4085d247f81dc3e55ea537b8b1ae5b2', 4, 'Courtesy call', 'Josefina Gonzales', NULL, NULL, NULL, 'staff', 'done', 56, 'Angelu', 0, '2026-10-19 12:46:06', '2026-10-19 12:56:44', '2026-10-19 12:56:44', '2026-10-19 13:02:26', 1),
(8040, 1, '2026-10-19', 49, 'MO-B049', '2f5a91ea7457196c068568a7f0922dea', 2, 'Business concerns', 'Manuel Mendoza', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-19 12:52:28', '2026-10-19 12:57:27', '2026-10-19 12:57:27', '2026-10-19 13:03:04', 1),
(8041, 1, '2026-10-19', 50, 'MO-A050', 'afb19f4f84425e5998daab5c80d1d625', 1, 'Mayor\'s Clearance', 'Ana Rivera', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-19 13:10:13', '2026-10-19 13:31:14', '2026-10-19 13:31:14', '2026-10-19 13:35:23', 1),
(8042, 1, '2026-10-19', 51, 'MO-B051', 'cb776cfb902e953a784d6c357280bfc2', 2, 'Business concerns', 'Rogelio Villanueva', NULL, NULL, NULL, 'staff', 'done', 54, 'Jean Frias', 0, '2026-10-19 13:19:40', '2026-10-19 13:26:05', '2026-10-19 13:26:05', '2026-10-19 13:39:09', 1),
(8043, 1, '2026-10-19', 52, 'MO-A052', '32e82fd38d4694dcdc7dc22e455dd227', 1, 'Mayor\'s Clearance', 'Grace Lopez', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-19 13:34:02', '2026-10-19 13:46:54', '2026-10-19 13:46:54', '2026-10-19 13:51:37', 1),
(8044, 1, '2026-10-19', 53, 'MO-B053', '5974dc786ceaf6ec287e0f14c9e10674', 2, 'Business concerns', 'Gerardo Garcia', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-19 13:38:05', '2026-10-19 13:44:49', '2026-10-19 13:44:49', '2026-10-19 13:59:15', 1),
(8045, 1, '2026-10-19', 54, 'MO-E054', '838e896e09375b430b4282d3e24d9e72', 5, 'Financial assistance', 'Liza Flores', NULL, NULL, NULL, 'staff', 'done', 55, 'Sir Henjie', 0, '2026-10-19 13:43:47', '2026-10-19 13:50:50', '2026-10-19 13:50:50', '2026-10-19 13:58:16', 1),
(8046, 1, '2026-10-19', 55, 'MO-E055', 'cf5f74223e8b88749bdf1b1e2638273d', 5, 'Financial assistance', 'Antonio Bautista', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-19 13:43:54', '2026-10-19 13:54:42', '2026-10-19 13:54:42', '2026-10-19 13:59:02', 1),
(8047, 1, '2026-10-19', 56, 'MO-A056', 'a81d02a6020559e0e436fcc4da7c3376', 1, 'Mayor\'s Clearance', 'Elena Torres', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-19 13:45:11', '2026-10-19 13:57:29', '2026-10-19 13:57:29', '2026-10-19 14:06:43', 1),
(8048, 1, '2026-10-19', 57, 'MO-A057', '3f016e291118292114cf04247bbff619', 1, 'Mayor\'s Clearance', 'Pedro Dela Cruz', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-19 13:49:15', '2026-10-19 14:01:52', '2026-10-19 14:01:52', '2026-10-19 14:14:05', 1),
(8049, 1, '2026-10-19', 58, 'MO-C058', '421931e87bbe08b15427017403a7c945', 3, 'Personal concerns', 'Marites Pascual', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-19 13:49:54', '2026-10-19 14:01:27', '2026-10-19 14:01:27', '2026-10-19 14:14:21', 1),
(8050, 1, '2026-10-19', 59, 'MO-E059', 'd7e95040dc4570ab5026f340f8ac733f', 5, 'Financial assistance', 'Alfredo Reyes', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-19 13:51:20', '2026-10-19 13:54:13', '2026-10-19 13:54:13', '2026-10-19 13:57:57', 1),
(8051, 1, '2026-10-19', 60, 'MO-A060', 'aabd1b43ad85f429eacabcec7d728749', 1, 'Mayor\'s Clearance', 'Maria Ramos', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-19 13:55:48', '2026-10-19 14:11:10', '2026-10-19 14:11:10', '2026-10-19 14:16:29', 1),
(8052, 1, '2026-10-19', 61, 'MO-C061', '915c92993abc435caa788764143223e4', 3, 'Personal concerns', 'Eduardo Santos', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-19 14:00:13', '2026-10-19 14:00:43', '2026-10-19 14:00:43', '2026-10-19 14:11:06', 1),
(8053, 1, '2026-10-19', 62, 'MO-B062', '6716ba65368d5520762e05cdb5759410', 2, 'Business concerns', 'Rosario Castillo', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-19 14:06:49', '2026-10-19 14:17:53', '2026-10-19 14:17:53', '2026-10-19 14:27:39', 1),
(8054, 1, '2026-10-19', 63, 'MO-C063', 'be6d1699067bd16d0dbd8fd1e2c6cf56', 3, 'Personal concerns', 'Ricardo Cortez', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-19 14:13:17', '2026-10-19 14:20:03', '2026-10-19 14:20:03', '2026-10-19 14:38:27', 1),
(8055, 1, '2026-10-19', 64, 'MO-E064', 'b406e0da3b8873bd76713487f0fac99c', 5, 'Financial assistance', 'Carmela Domingo', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-19 14:22:36', '2026-10-19 14:27:43', '2026-10-19 14:27:43', '2026-10-19 14:39:01', 1),
(8056, 1, '2026-10-19', 65, 'MO-E065', '29c1b691b3271be0eebb070c4d18d4d4', 5, 'Financial assistance', 'Felipe Mercado', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-19 14:27:28', '2026-10-19 14:31:32', '2026-10-19 14:31:32', '2026-10-19 14:42:40', 1),
(8057, 1, '2026-10-19', 66, 'MO-A066', 'f55e96def9b0f0523a9fac85653d84f2', 1, 'Mayor\'s Clearance', 'Cynthia Navarro', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-19 14:33:09', '2026-10-19 14:35:20', '2026-10-19 14:35:20', '2026-10-19 14:41:31', 1),
(8058, 1, '2026-10-19', 67, 'MO-B067', 'd1a6a5299f9fa143ffb03d94433b7dbf', 2, 'Business concerns', 'Jose Salazar', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-19 14:38:04', '2026-10-19 14:47:15', '2026-10-19 14:47:15', '2026-10-19 14:56:53', 1),
(8059, 1, '2026-10-19', 68, 'MO-F068', 'cc1017ededb88b5d1d9d1395125610bb', 6, 'Other', 'Teresita Aquino', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-19 14:42:47', '2026-10-19 14:48:56', '2026-10-19 14:48:56', '2026-10-19 14:56:01', 1),
(8060, 1, '2026-10-19', 69, 'MO-A069', 'e79fe632a9efd2e38886e7e0df72bd61', 1, 'Mayor\'s Clearance', 'Danilo Fernandez', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-19 14:46:14', '2026-10-19 14:51:16', '2026-10-19 14:51:16', '2026-10-19 14:54:02', 1),
(8061, 1, '2026-10-19', 70, 'MO-F070', '23821573cbdab4723392b7c5ead29957', 6, 'Other', 'Lourdes Cruz', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-19 14:52:50', '2026-10-19 14:56:27', '2026-10-19 14:56:27', '2026-10-19 15:01:31', 1),
(8062, 1, '2026-10-19', 71, 'MO-F071', 'c00f30d724e1c7b92cdeae93b8ee6aeb', 6, 'Other', 'Ramon Gonzales', NULL, NULL, NULL, 'staff', 'done', 54, 'Jean Frias', 0, '2026-10-19 14:54:40', '2026-10-19 15:04:11', '2026-10-19 15:04:11', '2026-10-19 15:09:16', 1),
(8063, 1, '2026-10-19', 72, 'MO-F072', '90787eec817b585f334944157378647e', 6, 'Other', 'Josefina Mendoza', NULL, NULL, NULL, 'self', 'skipped', 56, 'Angelu', 0, '2026-10-19 15:06:47', '2026-10-19 15:12:47', NULL, NULL, 1),
(8064, 1, '2026-10-19', 73, 'MO-C073', 'ba667b1cb727698cfef5dec2a3d54cb0', 3, 'Personal concerns', 'Manuel Rivera', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-19 15:08:48', '2026-10-19 15:10:24', '2026-10-19 15:10:24', '2026-10-19 15:20:40', 1),
(8065, 1, '2026-10-19', 74, 'MO-E074', '9d89c52a9a570c6e55b4f8b730a880f3', 5, 'Financial assistance', 'Ana Villanueva', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-19 15:32:38', '2026-10-19 15:38:35', '2026-10-19 15:38:35', '2026-10-19 15:42:31', 1),
(8066, 1, '2026-10-19', 75, 'MO-E075', '1a72186084b00d13f1cb9deb25b58d0a', 5, 'Financial assistance', 'Rogelio Lopez', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-19 15:36:43', '2026-10-19 15:38:31', '2026-10-19 15:38:31', '2026-10-19 15:44:24', 1),
(8067, 1, '2026-10-19', 76, 'MO-D076', '49ea5363859760f6cbb6f469591c2735', 4, 'Courtesy call', 'Grace Garcia', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-19 15:46:04', '2026-10-19 15:53:37', '2026-10-19 15:53:37', '2026-10-19 15:58:22', 1),
(8068, 1, '2026-10-19', 77, 'MO-E077', '74b248732a456ebecc760b7433b2cb95', 5, 'Financial assistance', 'Gerardo Flores', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-19 15:48:25', '2026-10-19 15:49:25', '2026-10-19 15:49:25', '2026-10-19 15:51:39', 1),
(8069, 1, '2026-10-19', 78, 'MO-E078', '0e639f9a697e6bd6fcfd1daad6e906b9', 5, 'Financial assistance', 'Liza Bautista', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-19 15:49:21', '2026-10-19 15:50:37', '2026-10-19 15:50:37', '2026-10-19 15:57:09', 1),
(8070, 1, '2026-10-19', 79, 'MO-A079', 'ba6a28d962ead596fe7da286cd3fcce1', 1, 'Mayor\'s Clearance', 'Antonio Torres', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-19 15:50:52', '2026-10-19 15:55:19', '2026-10-19 15:55:19', '2026-10-19 15:58:15', 1),
(8071, 1, '2026-10-19', 80, 'MO-F080', '229a6f7ed2e1b687ea3457bf28a4ee81', 6, 'Other', 'Elena Dela Cruz', NULL, NULL, NULL, 'staff', 'done', 56, 'Angelu', 0, '2026-10-19 16:15:20', '2026-10-19 16:15:59', '2026-10-19 16:15:59', '2026-10-19 16:17:29', 1),
(8072, 1, '2026-10-19', 81, 'MO-E081', '2da280261d53905d0c75ded4874e3156', 5, 'Financial assistance', 'Pedro Pascual', NULL, NULL, NULL, 'staff', 'done', 55, 'Sir Henjie', 0, '2026-10-19 16:33:29', '2026-10-19 16:37:19', '2026-10-19 16:37:19', '2026-10-19 16:41:27', 1),
(8073, 1, '2026-10-20', 1, 'MO-A001', '0f0bd29b120058b07cb9c8669ab2bd84', 1, 'Mayor\'s Clearance', 'Elena Cruz', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-20 08:06:10', '2026-10-20 08:10:34', '2026-10-20 08:10:34', '2026-10-20 08:16:42', 1);
INSERT INTO `queue_tickets` (`id`, `queue_id`, `queue_day`, `number`, `code`, `access_key`, `service_id`, `service_name`, `full_name`, `mobile`, `phone_key`, `user_id`, `source`, `status`, `staff_id`, `staff_name`, `is_priority`, `created_at`, `called_at`, `started_at`, `finished_at`, `is_demo`) VALUES
(8074, 1, '2026-10-20', 2, 'MO-B002', '45c33303e36d57475ec1a3593a9a237f', 2, 'Business concerns', 'Pedro Gonzales', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-20 08:08:29', '2026-10-20 08:22:10', '2026-10-20 08:22:10', '2026-10-20 08:30:23', 1),
(8075, 1, '2026-10-20', 3, 'MO-F003', 'b7a9a75460e7fb131549cb938ab9f7e1', 6, 'Other', 'Marites Mendoza', NULL, NULL, NULL, 'staff', 'done', 54, 'Jean Frias', 0, '2026-10-20 08:11:03', '2026-10-20 08:12:03', '2026-10-20 08:12:03', '2026-10-20 08:18:57', 1),
(8076, 1, '2026-10-20', 4, 'MO-B004', '1804b2935bee816cd37172faa1ff5ca3', 2, 'Business concerns', 'Alfredo Rivera', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-20 08:21:16', '2026-10-20 08:24:14', '2026-10-20 08:24:14', '2026-10-20 08:29:53', 1),
(8077, 1, '2026-10-20', 5, 'MO-B005', '028ce379e2955ae76b6dcac6af08db23', 2, 'Business concerns', 'Maria Villanueva', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-20 09:03:09', '2026-10-20 09:10:24', '2026-10-20 09:10:24', '2026-10-20 09:30:04', 1),
(8078, 1, '2026-10-20', 6, 'MO-C006', '879a81cd3e13f45c972b3f06472d6589', 3, 'Personal concerns', 'Eduardo Lopez', NULL, NULL, NULL, 'self', 'cancelled', NULL, NULL, 0, '2026-10-20 09:14:35', NULL, NULL, NULL, 1),
(8079, 1, '2026-10-20', 7, 'MO-E007', 'b06157c4a7ee0870ccc4de5def5ef3f3', 5, 'Financial assistance', 'Rosario Garcia', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-20 09:14:40', '2026-10-20 09:24:21', '2026-10-20 09:24:21', '2026-10-20 09:37:41', 1),
(8080, 1, '2026-10-20', 8, 'MO-A008', '3f693a8eb00ba2067c9e2d48a3fe5ba9', 1, 'Mayor\'s Clearance', 'Ricardo Flores', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-20 09:15:39', '2026-10-20 09:29:36', '2026-10-20 09:29:36', '2026-10-20 09:32:54', 1),
(8081, 1, '2026-10-20', 9, 'MO-A009', '225baea4dc4b28ee943cf820d93326d1', 1, 'Mayor\'s Clearance', 'Carmela Bautista', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-20 09:20:46', '2026-10-20 09:40:45', '2026-10-20 09:40:45', '2026-10-20 09:44:28', 1),
(8082, 1, '2026-10-20', 10, 'MO-A010', 'b948cbfa3637fa99219a7a637cd9f240', 1, 'Mayor\'s Clearance', 'Felipe Torres', NULL, NULL, NULL, 'self', 'skipped', 56, 'Angelu', 0, '2026-10-20 09:25:35', '2026-10-20 09:26:40', NULL, NULL, 1),
(8083, 1, '2026-10-20', 11, 'MO-C011', 'dca738da76453d848cb5137a87599cfd', 3, 'Personal concerns', 'Cynthia Dela Cruz', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-20 09:25:42', '2026-10-20 09:37:12', '2026-10-20 09:37:12', '2026-10-20 09:40:40', 1),
(8084, 1, '2026-10-20', 12, 'MO-F012', '92bbfe4f6b1832fcf2ee669d8a081a5e', 6, 'Other', 'Jose Pascual', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-20 09:41:31', '2026-10-20 10:05:56', '2026-10-20 10:05:56', '2026-10-20 10:21:16', 1),
(8085, 1, '2026-10-20', 13, 'MO-A013', 'd0aae87f001c23ef08f2a6cb868712b0', 1, 'Mayor\'s Clearance', 'Teresita Reyes', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-20 09:44:34', '2026-10-20 09:50:46', '2026-10-20 09:50:46', '2026-10-20 09:53:28', 1),
(8086, 1, '2026-10-20', 14, 'MO-F014', '88af7ec2388141f933c9d29f73fb23b6', 6, 'Other', 'Danilo Ramos', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-20 09:48:32', '2026-10-20 09:54:01', '2026-10-20 09:54:01', '2026-10-20 10:04:16', 1),
(8087, 1, '2026-10-20', 15, 'MO-B015', '028d3178b04d27b4782d3899652fc8d1', 2, 'Business concerns', 'Lourdes Santos', NULL, NULL, NULL, 'self', 'skipped', 55, 'Sir Henjie', 0, '2026-10-20 10:06:54', '2026-10-20 10:10:15', NULL, NULL, 1),
(8088, 1, '2026-10-20', 16, 'MO-E016', '77279869efc3fac7fbf6538429b0de32', 5, 'Financial assistance', 'Ramon Castillo', NULL, NULL, NULL, 'staff', 'done', 54, 'Jean Frias', 0, '2026-10-20 10:07:58', '2026-10-20 10:18:13', '2026-10-20 10:18:13', '2026-10-20 10:21:19', 1),
(8089, 1, '2026-10-20', 17, 'MO-B017', '18e66fb1ad378af6290062fece812e84', 2, 'Business concerns', 'Josefina Cortez', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-20 10:09:53', '2026-10-20 10:14:04', '2026-10-20 10:14:04', '2026-10-20 10:24:57', 1),
(8090, 1, '2026-10-20', 18, 'MO-B018', '05e44a874ea81c21cd5748770639b709', 2, 'Business concerns', 'Manuel Domingo', NULL, NULL, NULL, 'staff', 'cancelled', NULL, NULL, 0, '2026-10-20 10:15:38', NULL, NULL, NULL, 1),
(8091, 1, '2026-10-20', 19, 'MO-E019', '38612a279fa3783fb8013fe8b93a7cdc', 5, 'Financial assistance', 'Ana Mercado', NULL, NULL, NULL, 'self', 'cancelled', NULL, NULL, 0, '2026-10-20 10:17:04', NULL, NULL, NULL, 1),
(8092, 1, '2026-10-20', 20, 'MO-A020', '63e2ae206e4ea67b9e6d73e0c46891d4', 1, 'Mayor\'s Clearance', 'Rogelio Navarro', NULL, NULL, NULL, 'staff', 'done', 56, 'Angelu', 0, '2026-10-20 10:19:10', '2026-10-20 10:25:34', '2026-10-20 10:25:34', '2026-10-20 10:27:40', 1),
(8093, 1, '2026-10-20', 21, 'MO-F021', 'f5ad467f34ee20702ac6697edfc4226e', 6, 'Other', 'Grace Salazar', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-20 10:24:52', '2026-10-20 10:34:04', '2026-10-20 10:34:04', '2026-10-20 10:39:29', 1),
(8094, 1, '2026-10-20', 22, 'MO-C022', '3f1c91310742833f645e687009cc1361', 3, 'Personal concerns', 'Gerardo Aquino', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-20 10:28:10', '2026-10-20 10:34:25', '2026-10-20 10:34:25', '2026-10-20 10:50:24', 1),
(8095, 1, '2026-10-20', 23, 'MO-E023', 'bb8c06e8152c305b43a5fd4323f954f3', 5, 'Financial assistance', 'Liza Fernandez', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-20 10:29:39', '2026-10-20 10:39:10', '2026-10-20 10:39:10', '2026-10-20 10:44:05', 1),
(8096, 1, '2026-10-20', 24, 'MO-A024', '6ea784e6109d7d2b8edf1c64555cc097', 1, 'Mayor\'s Clearance', 'Antonio Cruz', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-20 10:37:49', '2026-10-20 10:38:32', '2026-10-20 10:38:32', '2026-10-20 10:44:10', 1),
(8097, 1, '2026-10-20', 25, 'MO-E025', '6c3139ba6d960986aca54d8d7f1044fd', 5, 'Financial assistance', 'Elena Gonzales', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-20 10:41:47', '2026-10-20 10:48:21', '2026-10-20 10:48:21', '2026-10-20 10:55:39', 1),
(8098, 1, '2026-10-20', 26, 'MO-A026', 'd97d298583c258acd29826b6a933951f', 1, 'Mayor\'s Clearance', 'Pedro Mendoza', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-20 10:42:13', '2026-10-20 10:51:50', '2026-10-20 10:51:50', '2026-10-20 10:57:08', 1),
(8099, 1, '2026-10-20', 27, 'MO-B027', 'fa580d50e21ad6b942e42e10fcc56b2e', 2, 'Business concerns', 'Marites Rivera', NULL, NULL, NULL, 'staff', 'done', 54, 'Jean Frias', 0, '2026-10-20 10:50:09', '2026-10-20 10:56:16', '2026-10-20 10:56:16', '2026-10-20 11:06:26', 1),
(8100, 1, '2026-10-20', 28, 'MO-B028', '34a949317087818d598fa54330abd882', 2, 'Business concerns', 'Alfredo Villanueva', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-20 11:06:41', '2026-10-20 11:14:28', '2026-10-20 11:14:28', '2026-10-20 11:51:01', 1),
(8101, 1, '2026-10-20', 29, 'MO-D029', '488ee0e946e0030b41ccaec0efd74621', 4, 'Courtesy call', 'Maria Lopez', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-20 11:08:09', '2026-10-20 11:23:12', '2026-10-20 11:23:12', '2026-10-20 11:37:30', 1),
(8102, 1, '2026-10-20', 30, 'MO-B030', 'c65108d5d7eca60b929605c0ea6a4bc2', 2, 'Business concerns', 'Eduardo Garcia', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-20 11:11:52', '2026-10-20 11:12:31', '2026-10-20 11:12:31', '2026-10-20 11:21:15', 1),
(8103, 1, '2026-10-20', 31, 'MO-E031', '6627f8e98ca9d18f333bfe87c6c97fd5', 5, 'Financial assistance', 'Rosario Flores', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-20 11:17:01', '2026-10-20 11:32:59', '2026-10-20 11:32:59', '2026-10-20 11:38:46', 1),
(8104, 1, '2026-10-20', 32, 'MO-A032', '3aefd7d8cfc8e1fb5d81122266012e13', 1, 'Mayor\'s Clearance', 'Ricardo Bautista', NULL, NULL, NULL, 'self', 'skipped', 54, 'Jean Frias', 0, '2026-10-20 11:22:38', '2026-10-20 11:27:52', NULL, NULL, 1),
(8105, 1, '2026-10-20', 33, 'MO-E033', 'd81240e3dd035bf9dbea11d230b2dbcf', 5, 'Financial assistance', 'Carmela Torres', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-20 11:29:08', '2026-10-20 11:47:05', '2026-10-20 11:47:05', '2026-10-20 11:50:00', 1),
(8106, 1, '2026-10-20', 34, 'MO-B034', '5a4d102795f8fc1ba161dff9f3006e9e', 2, 'Business concerns', 'Felipe Dela Cruz', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-20 11:34:00', '2026-10-20 11:38:41', '2026-10-20 11:38:41', '2026-10-20 11:57:17', 1),
(8107, 1, '2026-10-20', 35, 'MO-C035', 'a29cf053627994b44ff6995c172ba4ef', 3, 'Personal concerns', 'Cynthia Pascual', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-20 11:38:58', '2026-10-20 11:42:15', '2026-10-20 11:42:15', '2026-10-20 11:52:30', 1),
(8108, 1, '2026-10-20', 36, 'MO-D036', 'f850811bd2272bcf8d00c0f96b1e9e18', 4, 'Courtesy call', 'Jose Reyes', NULL, NULL, NULL, 'self', 'skipped', 54, 'Jean Frias', 0, '2026-10-20 11:44:18', '2026-10-20 11:49:02', NULL, NULL, 1),
(8109, 1, '2026-10-20', 37, 'MO-A037', 'b202c496c1073df447cb7262d80fb1d4', 1, 'Mayor\'s Clearance', 'Teresita Ramos', NULL, NULL, NULL, 'staff', 'done', 54, 'Jean Frias', 0, '2026-10-20 11:56:29', '2026-10-20 12:03:24', '2026-10-20 12:03:24', '2026-10-20 12:08:28', 1),
(8110, 1, '2026-10-20', 38, 'MO-A038', '5893935aff54402edd27bb8a370ec787', 1, 'Mayor\'s Clearance', 'Danilo Santos', NULL, NULL, NULL, 'staff', 'done', 55, 'Sir Henjie', 0, '2026-10-20 12:03:15', '2026-10-20 12:07:32', '2026-10-20 12:07:32', '2026-10-20 12:25:37', 1),
(8111, 1, '2026-10-20', 39, 'MO-A039', '87def83e6279ed4031555f3821d0cfa1', 1, 'Mayor\'s Clearance', 'Lourdes Castillo', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-20 12:06:10', '2026-10-20 12:09:14', '2026-10-20 12:09:14', '2026-10-20 12:12:05', 1),
(8112, 1, '2026-10-20', 40, 'MO-B040', 'cb3729821f75bf16282142051ff69966', 2, 'Business concerns', 'Ramon Cortez', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-20 12:06:33', '2026-10-20 12:08:27', '2026-10-20 12:08:27', '2026-10-20 12:14:55', 1),
(8113, 1, '2026-10-20', 41, 'MO-F041', 'd2f6f14a14829fa0af847272feb15784', 6, 'Other', 'Josefina Domingo', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-20 12:12:36', '2026-10-20 12:16:16', '2026-10-20 12:16:16', '2026-10-20 12:22:31', 1),
(8114, 1, '2026-10-20', 42, 'MO-A042', '659b95eb2e301a6dd0b2a8b3fc4ee40a', 1, 'Mayor\'s Clearance', 'Manuel Mercado', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-20 12:12:40', '2026-10-20 12:16:24', '2026-10-20 12:16:24', '2026-10-20 12:19:07', 1),
(8115, 1, '2026-10-20', 43, 'MO-C043', '9ba0fb62435f5a3fdb6900c668584967', 3, 'Personal concerns', 'Ana Navarro', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-20 12:21:48', '2026-10-20 12:24:40', '2026-10-20 12:24:40', '2026-10-20 12:30:35', 1),
(8116, 1, '2026-10-20', 44, 'MO-A044', 'e0ecfa7bd732215fa965916df999bc2f', 1, 'Mayor\'s Clearance', 'Rogelio Salazar', NULL, NULL, NULL, 'staff', 'done', 54, 'Jean Frias', 0, '2026-10-20 12:58:18', '2026-10-20 13:01:40', '2026-10-20 13:01:40', '2026-10-20 13:03:33', 1),
(8117, 1, '2026-10-20', 45, 'MO-B045', 'b3b3f8fb2ced89d8c170da76aea52247', 2, 'Business concerns', 'Grace Aquino', NULL, NULL, NULL, 'staff', 'done', 55, 'Sir Henjie', 0, '2026-10-20 13:17:30', '2026-10-20 13:23:26', '2026-10-20 13:23:26', '2026-10-20 13:32:36', 1),
(8118, 1, '2026-10-20', 46, 'MO-A046', '6a13726bb616fbe2e0862a4b8eeb24f4', 1, 'Mayor\'s Clearance', 'Gerardo Fernandez', NULL, NULL, NULL, 'staff', 'cancelled', NULL, NULL, 0, '2026-10-20 13:28:34', NULL, NULL, NULL, 1),
(8119, 1, '2026-10-20', 47, 'MO-A047', '27f755171d9c3ed31146f4269587d74a', 1, 'Mayor\'s Clearance', 'Liza Cruz', NULL, NULL, NULL, 'staff', 'skipped', 55, 'Sir Henjie', 0, '2026-10-20 13:39:08', '2026-10-20 13:40:19', NULL, NULL, 1),
(8120, 1, '2026-10-20', 48, 'MO-A048', '43eb7f3317030bca276030ec9138e1fc', 1, 'Mayor\'s Clearance', 'Antonio Gonzales', NULL, NULL, NULL, 'staff', 'done', 54, 'Jean Frias', 0, '2026-10-20 13:40:24', '2026-10-20 13:43:41', '2026-10-20 13:43:41', '2026-10-20 13:51:11', 1),
(8121, 1, '2026-10-20', 49, 'MO-A049', '519d2e4bf080aafcee4993a4f4d39e82', 1, 'Mayor\'s Clearance', 'Elena Mendoza', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-20 13:44:33', '2026-10-20 13:53:45', '2026-10-20 13:53:45', '2026-10-20 13:56:36', 1),
(8122, 1, '2026-10-20', 50, 'MO-C050', 'ff4169de68accd8b2cd796f656a5bcea', 3, 'Personal concerns', 'Pedro Rivera', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-20 13:47:40', '2026-10-20 13:58:46', '2026-10-20 13:58:46', '2026-10-20 14:07:53', 1),
(8123, 1, '2026-10-20', 51, 'MO-C051', '8e6e8c3ef79787e068a62bbe012ecd2b', 3, 'Personal concerns', 'Marites Villanueva', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-20 13:49:15', '2026-10-20 13:56:29', '2026-10-20 13:56:29', '2026-10-20 14:00:43', 1),
(8124, 1, '2026-10-20', 52, 'MO-C052', 'c7bebe48d81b51729fba23e3f8be7322', 3, 'Personal concerns', 'Alfredo Lopez', NULL, NULL, NULL, 'staff', 'done', 56, 'Angelu', 0, '2026-10-20 14:04:52', '2026-10-20 14:14:42', '2026-10-20 14:14:42', '2026-10-20 14:20:45', 1),
(8125, 1, '2026-10-20', 53, 'MO-D053', 'e89c136d5ba033698651fdd9267675d9', 4, 'Courtesy call', 'Maria Garcia', NULL, NULL, NULL, 'self', 'skipped', 56, 'Angelu', 0, '2026-10-20 14:14:04', '2026-10-20 14:21:52', NULL, NULL, 1),
(8126, 1, '2026-10-20', 54, 'MO-F054', 'a101b12713a58bf764f014cbc7eaeaef', 6, 'Other', 'Eduardo Flores', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-20 14:22:01', '2026-10-20 14:23:28', '2026-10-20 14:23:28', '2026-10-20 14:26:38', 1),
(8127, 1, '2026-10-20', 55, 'MO-D055', '16f167149e9ce82c2930f42378d83496', 4, 'Courtesy call', 'Rosario Bautista', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-20 14:42:38', '2026-10-20 14:47:37', '2026-10-20 14:47:37', '2026-10-20 15:25:41', 1),
(8128, 1, '2026-10-20', 56, 'MO-A056', '09491876068bb18a2fbb16bb846c6a92', 1, 'Mayor\'s Clearance', 'Ricardo Torres', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-20 14:48:32', '2026-10-20 14:55:59', '2026-10-20 14:55:59', '2026-10-20 14:59:33', 1),
(8129, 1, '2026-10-20', 57, 'MO-D057', '6183a44c7a3b53585a1c0166c3fe9944', 4, 'Courtesy call', 'Carmela Dela Cruz', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-20 15:17:40', '2026-10-20 15:21:37', '2026-10-20 15:21:37', '2026-10-20 15:33:17', 1),
(8130, 1, '2026-10-20', 58, 'MO-B058', 'ecee236193f5bc754de2367a8c47dae7', 2, 'Business concerns', 'Felipe Pascual', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-20 15:21:44', '2026-10-20 15:25:41', '2026-10-20 15:25:41', '2026-10-20 15:41:26', 1),
(8131, 1, '2026-10-20', 59, 'MO-F059', '1638c9e48d5556c06439cfbdfd0e5179', 6, 'Other', 'Cynthia Reyes', NULL, NULL, NULL, 'staff', 'done', 54, 'Jean Frias', 0, '2026-10-20 15:25:30', '2026-10-20 15:27:14', '2026-10-20 15:27:14', '2026-10-20 15:32:09', 1),
(8132, 1, '2026-10-20', 60, 'MO-C060', '9771f720052664ef965f683086c71eb3', 3, 'Personal concerns', 'Jose Ramos', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-20 15:25:46', '2026-10-20 15:35:46', '2026-10-20 15:35:46', '2026-10-20 15:39:09', 1),
(8133, 1, '2026-10-20', 61, 'MO-A061', '0c8a79687a4e391e536c369cbcef80e7', 1, 'Mayor\'s Clearance', 'Teresita Santos', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-20 15:29:23', '2026-10-20 15:34:10', '2026-10-20 15:34:10', '2026-10-20 15:35:56', 1),
(8134, 1, '2026-10-20', 62, 'MO-A062', '4e0f4e63d2f1d2fed4e7d5ed952540f7', 1, 'Mayor\'s Clearance', 'Danilo Castillo', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-20 15:45:44', '2026-10-20 15:47:28', '2026-10-20 15:47:28', '2026-10-20 15:55:02', 1),
(8135, 1, '2026-10-20', 63, 'MO-F063', 'da894c3027afb7dba797efe465d9d258', 6, 'Other', 'Lourdes Cortez', NULL, NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-20 15:48:38', '2026-10-20 15:59:35', '2026-10-20 15:59:35', '2026-10-20 16:02:22', 1),
(8136, 1, '2026-10-20', 64, 'MO-D064', '6b50a8e22774ef29106dbf7ce746e63d', 4, 'Courtesy call', 'Ramon Domingo', NULL, NULL, NULL, 'staff', 'done', 56, 'Angelu', 0, '2026-10-20 15:51:49', '2026-10-20 15:55:57', '2026-10-20 15:55:57', '2026-10-20 16:08:51', 1),
(8137, 1, '2026-10-20', 65, 'MO-E065', 'b6c5d0ca0271b3e9daca5fd3c1d4a26a', 5, 'Financial assistance', 'Josefina Mercado', NULL, NULL, NULL, 'staff', 'done', 54, 'Jean Frias', 0, '2026-10-20 16:03:37', '2026-10-20 16:07:59', '2026-10-20 16:07:59', '2026-10-20 16:10:42', 1),
(8138, 1, '2026-10-20', 66, 'MO-B066', '9ca91e1751f4b1b9b9e5c363d641ecb4', 2, 'Business concerns', 'Manuel Navarro', NULL, NULL, NULL, 'staff', 'skipped', 54, 'Jean Frias', 0, '2026-10-20 16:06:28', '2026-10-20 16:06:58', NULL, NULL, 1),
(8139, 1, '2026-10-20', 67, 'MO-C067', 'e87b1ad16c381a7294d794d67cb1125d', 3, 'Personal concerns', 'Ana Salazar', NULL, NULL, NULL, 'staff', 'done', 55, 'Sir Henjie', 0, '2026-10-20 16:42:14', '2026-10-20 16:45:19', '2026-10-20 16:45:19', '2026-10-20 16:57:29', 1),
(8140, 1, '2026-10-20', 68, 'MO-C068', '22421bfd0f45fa3cb6ef81fc9dd519ef', 3, 'Personal concerns', 'Rogelio Aquino', NULL, NULL, NULL, 'self', 'done', 55, 'Sir Henjie', 0, '2026-10-20 16:50:12', '2026-10-20 16:52:04', '2026-10-20 16:52:04', '2026-10-20 16:55:32', 1),
(8141, 1, '2026-10-21', 1, 'MO-A001', '933de17e2be2db68b264fbbbf5c1b746', 1, 'Mayor\'s Clearance', 'Nikki San Pedro', '09151713532', NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-21 20:27:03', '2026-10-21 20:28:31', '2026-10-21 20:28:43', '2026-10-21 20:32:03', 0),
(8142, 1, '2026-10-21', 2, 'MO-D002', '6554ed919a599554317fd9a14276932f', 4, 'Courtesy call', 'kaksklala', '09643136664', NULL, NULL, 'self', 'done', 56, 'Angelu', 0, '2026-10-21 20:29:31', '2026-10-21 20:31:15', '2026-10-21 20:31:41', '2026-10-21 21:04:43', 0),
(8143, 1, '2026-10-21', 3, 'MO-B003', '5a229575df6ea6f9684ac22672cc6e0c', 2, 'Business concerns', 'Nikki san pedro', '09452213467', NULL, NULL, 'self', 'done', 54, 'Jean Frias', 0, '2026-10-21 20:32:33', '2026-10-21 20:32:40', '2026-10-21 21:05:14', '2026-10-21 21:05:23', 0),
(9398, 1, '2026-10-21', 4, 'MO-E004', '3b6d19b2fb7687aa3c8213ddd5911abb', 5, 'Financial assistance', 'Nikki san pedro', NULL, NULL, NULL, 'self', 'skipped', 54, 'Jean Frias', 0, '2026-10-21 21:05:42', '2026-10-21 21:06:16', NULL, '2026-10-21 21:06:20', 0),
(9414, 1, '2026-10-21', 5, 'MO-D005', '756dcbcb29b5b69d1b513dba2009f730', 4, 'Courtesy call', 'Nikki', NULL, NULL, NULL, 'self', 'done', 54, 'Jean Frias', 1, '2026-10-21 21:41:01', '2026-10-21 21:41:09', '2026-10-21 21:57:10', '2026-10-21 21:57:14', 0),
(9635, 1, '2026-10-21', 6, 'MO-E006', '44f5fcd29b079703594ebf7b4ffa5a91', 5, 'Financial assistance', 'kaka jdks', NULL, NULL, NULL, 'self', 'skipped', 56, 'Angelu', 1, '2026-10-21 21:55:12', '2026-10-21 21:55:26', NULL, '2026-10-21 21:55:48', 0),
(9636, 1, '2026-10-21', 7, 'MO-C007', 'f2dae661bbbca54c66b2d554a1c2bc0a', 3, 'Personal concerns', 'jsjaka', NULL, NULL, NULL, 'self', 'cancelled', NULL, NULL, 0, '2026-10-21 21:56:13', NULL, NULL, '2026-10-21 21:56:32', 0),
(9637, 1, '2026-10-21', 8, 'MO-B008', '550b136e1309a60d41a78a8226901eb6', 2, 'Business concerns', 'lkAHJBDWEa', NULL, NULL, NULL, 'staff', 'called', 54, 'Jean Frias', 0, '2026-10-21 21:56:56', '2026-10-21 21:57:15', NULL, NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `rate_limits`
--

CREATE TABLE `rate_limits` (
  `bucket` varchar(120) NOT NULL,
  `hits` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `window_start` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `rate_limits`
--

INSERT INTO `rate_limits` (`bucket`, `hits`, `window_start`) VALUES
('queue-ip:192.168.100.5', 2, '2026-10-21 21:55:12');

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `setting_key` varchar(50) NOT NULL,
  `setting_value` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`setting_key`, `setting_value`) VALUES
('close_time', '17:00'),
('holidays_seeded', '1'),
('lunch_enabled', '0'),
('lunch_end', '13:00'),
('lunch_start', '12:00'),
('news_options_seeded', '1'),
('open_time', '08:00'),
('queue_last_cleanup', '2026-10-21'),
('work_days', '1,2,3,4,5');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `full_name` varchar(150) NOT NULL,
  `first_name` varchar(60) DEFAULT NULL,
  `middle_name` varchar(60) DEFAULT NULL,
  `last_name` varchar(60) DEFAULT NULL,
  `suffix` varchar(10) DEFAULT NULL,
  `mobile` varchar(20) DEFAULT NULL,
  `organization` varchar(100) DEFAULT NULL,
  `position_title` varchar(100) DEFAULT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `privacy_consent_at` datetime DEFAULT NULL,
  `role` enum('admin','sysadmin','office','user') NOT NULL DEFAULT 'user',
  `department_id` int(10) UNSIGNED DEFAULT NULL,
  `status` enum('active','suspended') NOT NULL DEFAULT 'active',
  `suspended_at` datetime DEFAULT NULL,
  `suspended_reason` varchar(255) DEFAULT NULL,
  `must_change_password` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `office_department_id` int(10) UNSIGNED GENERATED ALWAYS AS (if(`role` = 'office',`department_id`,NULL)) STORED,
  `is_demo` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `full_name`, `first_name`, `middle_name`, `last_name`, `suffix`, `mobile`, `organization`, `position_title`, `email`, `password`, `privacy_consent_at`, `role`, `department_id`, `status`, `suspended_at`, `suspended_reason`, `must_change_password`, `created_at`, `is_demo`) VALUES
(1, 'Municipal Administrator', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'admin@norzagaray.gov.ph', '$2y$10$O4OhYIqM1atikiHf4J4yxeeJg0O4DnyrzGcKATe3tA8b1qHqN7K/6', NULL, 'admin', 29, 'active', NULL, NULL, 0, '2026-08-14 12:28:34', 0),
(2, 'Juan Dela Cruz', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'nikkisanpedro314@gmail.com', '$2y$10$Ok1.2eMQqL8t5VcCSHq1t.YaW6oy9TOtD7WV2vgBmPOwW.O4zz3JO', NULL, 'user', NULL, 'active', NULL, NULL, 0, '2026-08-14 12:28:34', 0),
(5, 'Nikki San Pedro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'nikkisanpedro@hotmail.com', '$2y$10$D8zjgm8aEqW7B2eeSeXZEO18tZFGpKjSZqUeRISIBLJKrFM3OcTga', NULL, 'user', NULL, 'active', NULL, NULL, 0, '2026-09-11 07:25:40', 0),
(10, 'Manuel R. Marcial', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'acct@placeholder.norzagaray.local', '$2y$10$/qVbxAuGPDiIUKXIp9rOmOk4pjlpNi7d5d.EfOTbViHA.rsUhNfoa', NULL, 'office', 12, 'active', NULL, NULL, 0, '2026-10-17 12:48:00', 0),
(11, 'Agriculture Office', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'agric@placeholder.norzagaray.local', '$2y$10$/qVbxAuGPDiIUKXIp9rOmOk4pjlpNi7d5d.EfOTbViHA.rsUhNfoa', NULL, 'office', 16, 'active', NULL, NULL, 0, '2026-10-17 12:48:00', 0),
(12, 'Portia H. German', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'assessor@placeholder.norzagaray.local', '$2y$10$/qVbxAuGPDiIUKXIp9rOmOk4pjlpNi7d5d.EfOTbViHA.rsUhNfoa', NULL, 'office', 11, 'active', NULL, NULL, 0, '2026-10-17 12:48:00', 0),
(13, 'Fernando DG. Cruz', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'budget@placeholder.norzagaray.local', '$2y$10$/qVbxAuGPDiIUKXIp9rOmOk4pjlpNi7d5d.EfOTbViHA.rsUhNfoa', NULL, 'office', 13, 'active', NULL, NULL, 0, '2026-10-17 12:48:00', 0),
(14, 'Jemelee John De Silva', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'bplo@placeholder.norzagaray.local', '$2y$10$/qVbxAuGPDiIUKXIp9rOmOk4pjlpNi7d5d.EfOTbViHA.rsUhNfoa', NULL, 'office', 9, 'active', NULL, NULL, 0, '2026-10-17 12:48:00', 0),
(15, 'Engineering Office', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'engr@placeholder.norzagaray.local', '$2y$10$/qVbxAuGPDiIUKXIp9rOmOk4pjlpNi7d5d.EfOTbViHA.rsUhNfoa', NULL, 'office', 21, 'active', NULL, NULL, 0, '2026-10-17 12:48:00', 0),
(16, 'Marilyn C. Ocampo', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'gso@placeholder.norzagaray.local', '$2y$10$/qVbxAuGPDiIUKXIp9rOmOk4pjlpNi7d5d.EfOTbViHA.rsUhNfoa', NULL, 'office', 8, 'active', NULL, NULL, 0, '2026-10-17 12:48:00', 0),
(17, 'Filipina DG. Pascual', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'lcr@placeholder.norzagaray.local', '$2y$10$/qVbxAuGPDiIUKXIp9rOmOk4pjlpNi7d5d.EfOTbViHA.rsUhNfoa', NULL, 'office', 3, 'active', NULL, NULL, 0, '2026-10-17 12:48:00', 0),
(18, 'Wilmie Kate Z. Logo', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'lydo@placeholder.norzagaray.local', '$2y$10$/qVbxAuGPDiIUKXIp9rOmOk4pjlpNi7d5d.EfOTbViHA.rsUhNfoa', NULL, 'office', 20, 'active', NULL, NULL, 0, '2026-10-17 12:48:00', 0),
(19, 'Hon. Maria Elena L. Germar', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'mo@placeholder.norzagaray.local', '$2y$10$/qVbxAuGPDiIUKXIp9rOmOk4pjlpNi7d5d.EfOTbViHA.rsUhNfoa', NULL, 'office', 1, 'active', NULL, NULL, 0, '2026-10-17 12:48:00', 0),
(20, 'Jerry R. Sumbillo', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'mdrrmo@placeholder.norzagaray.local', '$2y$10$/qVbxAuGPDiIUKXIp9rOmOk4pjlpNi7d5d.EfOTbViHA.rsUhNfoa', NULL, 'office', 25, 'active', NULL, NULL, 0, '2026-10-17 12:48:00', 0),
(21, 'Mark Anthony Francisco', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'menro@placeholder.norzagaray.local', '$2y$10$/qVbxAuGPDiIUKXIp9rOmOk4pjlpNi7d5d.EfOTbViHA.rsUhNfoa', NULL, 'office', 22, 'active', NULL, NULL, 0, '2026-10-17 12:48:00', 0),
(22, 'Dr. George Peppard C. Paitim', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'mho@placeholder.norzagaray.local', '$2y$10$/qVbxAuGPDiIUKXIp9rOmOk4pjlpNi7d5d.EfOTbViHA.rsUhNfoa', NULL, 'office', 15, 'active', NULL, NULL, 0, '2026-10-17 12:48:00', 0),
(23, 'Fernando DG. Cruz', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'mhrmo@placeholder.norzagaray.local', '$2y$10$/qVbxAuGPDiIUKXIp9rOmOk4pjlpNi7d5d.EfOTbViHA.rsUhNfoa', NULL, 'office', 4, 'active', NULL, NULL, 0, '2026-10-17 12:48:00', 0),
(24, 'Mark Anthony Francisco', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'mpdo@placeholder.norzagaray.local', '$2y$10$/qVbxAuGPDiIUKXIp9rOmOk4pjlpNi7d5d.EfOTbViHA.rsUhNfoa', NULL, 'office', 5, 'active', NULL, NULL, 0, '2026-10-17 12:48:00', 0),
(25, 'Annalyn Joy C. San Pedro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'mswdo@placeholder.norzagaray.local', '$2y$10$/qVbxAuGPDiIUKXIp9rOmOk4pjlpNi7d5d.EfOTbViHA.rsUhNfoa', NULL, 'office', 14, 'active', NULL, NULL, 0, '2026-10-17 12:48:00', 0),
(26, 'Dr. Maria Liberty DG. Pascual', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'nc@placeholder.norzagaray.local', '$2y$10$/qVbxAuGPDiIUKXIp9rOmOk4pjlpNi7d5d.EfOTbViHA.rsUhNfoa', NULL, 'office', 56, 'active', NULL, NULL, 0, '2026-10-17 12:48:00', 0),
(27, 'Dr. Joselito C. Santiago', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'nmh@placeholder.norzagaray.local', '$2y$10$/qVbxAuGPDiIUKXIp9rOmOk4pjlpNi7d5d.EfOTbViHA.rsUhNfoa', NULL, 'office', 54, 'active', NULL, NULL, 0, '2026-10-17 12:48:01', 0),
(28, 'Hon. Maria Elena L. Germar', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'ntmpso@placeholder.norzagaray.local', '$2y$10$/qVbxAuGPDiIUKXIp9rOmOk4pjlpNi7d5d.EfOTbViHA.rsUhNfoa', NULL, 'office', 26, 'active', NULL, NULL, 0, '2026-10-17 12:48:01', 0),
(29, 'Christilina B. Torres', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'nutr@placeholder.norzagaray.local', '$2y$10$/qVbxAuGPDiIUKXIp9rOmOk4pjlpNi7d5d.EfOTbViHA.rsUhNfoa', NULL, 'office', 48, 'active', NULL, NULL, 0, '2026-10-17 12:48:01', 0),
(30, 'Hon. Maria Elena L. Germar', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'osca@placeholder.norzagaray.local', '$2y$10$/qVbxAuGPDiIUKXIp9rOmOk4pjlpNi7d5d.EfOTbViHA.rsUhNfoa', NULL, 'office', 17, 'active', NULL, NULL, 0, '2026-10-17 12:48:01', 0),
(31, 'Hon. Maria Elena L. Germar', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pdao@placeholder.norzagaray.local', '$2y$10$/qVbxAuGPDiIUKXIp9rOmOk4pjlpNi7d5d.EfOTbViHA.rsUhNfoa', NULL, 'office', 18, 'active', NULL, NULL, 0, '2026-10-17 12:48:01', 0),
(32, 'Hon. Maria Elena L. Germar', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pop@placeholder.norzagaray.local', '$2y$10$/qVbxAuGPDiIUKXIp9rOmOk4pjlpNi7d5d.EfOTbViHA.rsUhNfoa', NULL, 'office', 47, 'active', NULL, NULL, 0, '2026-10-17 12:48:01', 0),
(33, 'Atty. Rochelle Marie G. Torres', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'peso@placeholder.norzagaray.local', '$2y$10$/qVbxAuGPDiIUKXIp9rOmOk4pjlpNi7d5d.EfOTbViHA.rsUhNfoa', NULL, 'office', 19, 'active', NULL, NULL, 0, '2026-10-17 12:48:01', 0),
(34, 'Hon. Maria Elena L. Germar', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pio@placeholder.norzagaray.local', '$2y$10$/qVbxAuGPDiIUKXIp9rOmOk4pjlpNi7d5d.EfOTbViHA.rsUhNfoa', NULL, 'office', 6, 'active', NULL, NULL, 0, '2026-10-17 12:48:01', 0),
(35, 'Hon. Giulius Ceazar S. Iapino', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'sb@placeholder.norzagaray.local', '$2y$10$/qVbxAuGPDiIUKXIp9rOmOk4pjlpNi7d5d.EfOTbViHA.rsUhNfoa', NULL, 'office', 2, 'active', NULL, NULL, 0, '2026-10-17 12:48:01', 0),
(36, 'Hon. Maria Elena L. Germar', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'tour@placeholder.norzagaray.local', '$2y$10$sqhW0CjNUSkcx8VhAws3ZOiEF2Aq9n1lXX0RtPwN.lFGIL/hXyQC2', NULL, 'office', 23, 'active', NULL, NULL, 0, '2026-10-17 12:48:01', 0),
(37, 'Shena Liz P. Alonzo', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'treas@placeholder.norzagaray.local', '$2y$10$/qVbxAuGPDiIUKXIp9rOmOk4pjlpNi7d5d.EfOTbViHA.rsUhNfoa', NULL, 'office', 10, 'active', NULL, NULL, 0, '2026-10-17 12:48:01', 0),
(39, 'System Admin', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'sysadmin@norzagaray.gov.ph', '$2y$10$sw6.nGTiT5sQfpn8gk9q3uLV/NGE573HdkV8c7Oatx7hWYB69FjsO', NULL, 'sysadmin', NULL, 'active', NULL, NULL, 0, '2026-10-17 12:59:40', 0),
(64, 'Juan Dela Cruz', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'juan.delacruz@example.com', '$2y$10$Ok1.2eMQqL8t5VcCSHq1t.YaW6oy9TOtD7WV2vgBmPOwW.O4zz3JO', NULL, 'user', NULL, 'active', NULL, NULL, 0, '2026-10-20 15:35:15', 0),
(138, 'Maria Santos', 'Maria', NULL, 'Santos', NULL, '09170023757', NULL, NULL, 'maria.santos@example.com', '$2y$10$V3nNY/jq7iVUXrujp5Qo1OZbIaKN/AHmFRnp1ekHM9ceSizGpgepq', '2026-10-21 14:39:38', 'user', NULL, 'active', NULL, NULL, 0, '2026-10-21 06:39:38', 1),
(139, 'Jose Reyes', 'Jose', NULL, 'Reyes', NULL, '09170031676', NULL, NULL, 'jose.reyes@example.com', '$2y$10$HLk6qIqjXRmYG7YUFqfyce.zNZ1b3TpFlqvakLmYGM.hJ7tBeLwzK', '2026-10-21 14:39:38', 'user', NULL, 'active', NULL, NULL, 0, '2026-10-21 06:39:38', 1),
(140, 'Ana Dela Cruz', 'Ana', NULL, 'Dela Cruz', NULL, '09170039595', NULL, NULL, 'ana.delacruz@example.com', '$2y$10$QNYkEKQyk.ySXA4VMFS.feFVK4.bFRUTqSvw5MU14/HZqEkFcv0F.', '2026-10-21 14:39:38', 'user', NULL, 'active', NULL, NULL, 0, '2026-10-21 06:39:38', 1),
(141, 'Pedro Bautista', 'Pedro', NULL, 'Bautista', NULL, '09170047514', NULL, NULL, 'pedro.bautista@example.com', '$2y$10$mzRjVt90XeCVKiAJLPth/erECknxf5yNf4lJp1Ef72ji4/aM.HoyO', '2026-10-21 14:39:38', 'user', NULL, 'active', NULL, NULL, 0, '2026-10-21 06:39:38', 1),
(142, 'Carmela Garcia', 'Carmela', NULL, 'Garcia', NULL, '09170055433', NULL, NULL, 'carmela.garcia@example.com', '$2y$10$yXd1A2DYiiaiAbT2kQJf8uFI06Ymn4xY1jfRt8yrLG9HHij5Niym.', '2026-10-21 14:39:38', 'user', NULL, 'active', NULL, NULL, 0, '2026-10-21 06:39:38', 1),
(143, 'Ramon Villanueva', 'Ramon', NULL, 'Villanueva', NULL, '09170063352', NULL, NULL, 'ramon.villanueva@example.com', '$2y$10$pNb/Q2IGg7kke2b/W0/puOghuqP92vREZJzLOszti9W1HH9/12LOW', '2026-10-21 14:39:38', 'user', NULL, 'active', NULL, NULL, 0, '2026-10-21 06:39:38', 1),
(144, 'Liza Mendoza', 'Liza', NULL, 'Mendoza', NULL, '09170071271', NULL, NULL, 'liza.mendoza@example.com', '$2y$10$4CfMGVqohKtM0vAhKe3o/uKoBThzuWMGghaSC1aD/piUeWNM03ma6', '2026-10-21 14:39:38', 'user', NULL, 'active', NULL, NULL, 0, '2026-10-21 06:39:38', 1),
(145, 'Eduardo Cruz', 'Eduardo', NULL, 'Cruz', NULL, '09170079190', NULL, NULL, 'eduardo.cruz@example.com', '$2y$10$YdVFKjpj4Gv9BlvVA.owO.zqnUjDQhP11gKNcwms83JUM0qG.IkVK', '2026-10-21 14:39:38', 'user', NULL, 'active', NULL, NULL, 0, '2026-10-21 06:39:38', 1),
(146, 'Teresita Aquino', 'Teresita', NULL, 'Aquino', NULL, '09170087109', NULL, NULL, 'teresita.aquino@example.com', '$2y$10$8nC5vCrBp.MnaOICCxoDLeWG5ncZD8ROgu3X8fIGMJX1KZ8RNDKx2', '2026-10-21 14:39:39', 'user', NULL, 'active', NULL, NULL, 0, '2026-10-21 06:39:39', 1),
(147, 'Rogelio Navarro', 'Rogelio', NULL, 'Navarro', NULL, '09170095028', NULL, NULL, 'rogelio.navarro@example.com', '$2y$10$8LZVJGz/HymL0Fy8yNlQ4uyjlGUgZ1./b9VJ9Whe1FCczk/lyWUDO', '2026-10-21 14:39:39', 'user', NULL, 'active', NULL, NULL, 0, '2026-10-21 06:39:39', 1),
(148, 'Marites Domingo', 'Marites', NULL, 'Domingo', NULL, '09170102947', NULL, NULL, 'marites.domingo@example.com', '$2y$10$GZ4DbpdfpwGHnRPU7.4TWe2yizL.PQCfr.GOLL2BR.7ZzQ7fBVRHC', '2026-10-21 14:39:39', 'user', NULL, 'active', NULL, NULL, 0, '2026-10-21 06:39:39', 1),
(149, 'Felipe Castillo', 'Felipe', NULL, 'Castillo', NULL, '09170110866', NULL, NULL, 'felipe.castillo@example.com', '$2y$10$CRCde.wcLR7mgOSu6HdHE.JnOwsrmmGtSH3OYvpLxTTQq/08FS15m', '2026-10-21 14:39:39', 'user', NULL, 'active', NULL, NULL, 0, '2026-10-21 06:39:39', 1),
(150, 'Josefina Ramos', 'Josefina', NULL, 'Ramos', NULL, '09170118785', NULL, NULL, 'josefina.ramos@example.com', '$2y$10$c/84jX4jEbcUTl7q15PbZOXnMNUW0WAyOXhiPk.nefdIAHVfqvI5q', '2026-10-21 14:39:39', 'user', NULL, 'active', NULL, NULL, 0, '2026-10-21 06:39:39', 1),
(151, 'Antonio Pascual', 'Antonio', NULL, 'Pascual', NULL, '09170126704', NULL, NULL, 'antonio.pascual@example.com', '$2y$10$HCPsE7naDEs1oeb/b6MrkOThTe6VJOrQcozve/iCy11R.ONRizMve', '2026-10-21 14:39:39', 'user', NULL, 'active', NULL, NULL, 0, '2026-10-21 06:39:39', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `department_id` (`department_id`),
  ADD KEY `reviewed_by` (`reviewed_by`),
  ADD KEY `scheduled_by` (`scheduled_by`);

--
-- Indexes for table `audit_log`
--
ALTER TABLE `audit_log`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `citizens_charter_services`
--
ALTER TABLE `citizens_charter_services`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Indexes for table `email_verifications`
--
ALTER TABLE `email_verifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `email_verifications_email` (`email`,`id`),
  ADD KEY `email_verifications_ip` (`ip_address`,`created_at`);

--
-- Indexes for table `holidays`
--
ALTER TABLE `holidays`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mail_log`
--
ALTER TABLE `mail_log`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `meetings`
--
ALTER TABLE `meetings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `requester_user_id` (`requester_user_id`),
  ADD KEY `requester_department_id` (`requester_department_id`),
  ADD KEY `reviewed_by` (`reviewed_by`);

--
-- Indexes for table `meeting_invitees`
--
ALTER TABLE `meeting_invitees`
  ADD PRIMARY KEY (`meeting_id`,`department_id`),
  ADD KEY `department_id` (`department_id`);

--
-- Indexes for table `news_categories`
--
ALTER TABLE `news_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `news_posts`
--
ALTER TABLE `news_posts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `news_posts_department` (`department_id`);

--
-- Indexes for table `news_submissions`
--
ALTER TABLE `news_submissions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `news_submissions_status` (`status`,`updated_at`),
  ADD KEY `post_id` (`post_id`),
  ADD KEY `department_id` (`department_id`),
  ADD KEY `author_user_id` (`author_user_id`);

--
-- Indexes for table `news_submission_events`
--
ALTER TABLE `news_submission_events`
  ADD PRIMARY KEY (`id`),
  ADD KEY `news_events_submission` (`submission_id`,`id`);

--
-- Indexes for table `news_types`
--
ALTER TABLE `news_types`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notifications_user_unread` (`user_id`,`is_read`,`id`);

--
-- Indexes for table `queues`
--
ALTER TABLE `queues`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_one_general` (`department_id`,`general_flag`);

--
-- Indexes for table `queue_counters`
--
ALTER TABLE `queue_counters`
  ADD PRIMARY KEY (`queue_id`,`queue_day`);

--
-- Indexes for table `queue_services`
--
ALTER TABLE `queue_services`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_service_name` (`department_id`,`name`),
  ADD UNIQUE KEY `uq_service_letter` (`department_id`,`letter`),
  ADD KEY `staff_id` (`staff_id`);

--
-- Indexes for table `queue_staff`
--
ALTER TABLE `queue_staff`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_staff_name` (`department_id`,`name`);

--
-- Indexes for table `queue_tickets`
--
ALTER TABLE `queue_tickets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_ticket_number` (`queue_id`,`queue_day`,`number`),
  ADD UNIQUE KEY `uq_ticket_key` (`access_key`),
  ADD UNIQUE KEY `uq_event_phone` (`queue_id`,`phone_key`),
  ADD KEY `idx_line` (`queue_id`,`queue_day`,`status`,`number`),
  ADD KEY `service_id` (`service_id`),
  ADD KEY `staff_id` (`staff_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `rate_limits`
--
ALTER TABLE `rate_limits`
  ADD PRIMARY KEY (`bucket`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`setting_key`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `one_office_account_per_department` (`office_department_id`),
  ADD KEY `users_ibfk_1` (`department_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1070;

--
-- AUTO_INCREMENT for table `audit_log`
--
ALTER TABLE `audit_log`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT for table `citizens_charter_services`
--
ALTER TABLE `citizens_charter_services`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=173;

--
-- AUTO_INCREMENT for table `email_verifications`
--
ALTER TABLE `email_verifications`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `holidays`
--
ALTER TABLE `holidays`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `mail_log`
--
ALTER TABLE `mail_log`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=237;

--
-- AUTO_INCREMENT for table `meetings`
--
ALTER TABLE `meetings`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=143;

--
-- AUTO_INCREMENT for table `news_categories`
--
ALTER TABLE `news_categories`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `news_posts`
--
ALTER TABLE `news_posts`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=187;

--
-- AUTO_INCREMENT for table `news_submissions`
--
ALTER TABLE `news_submissions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=205;

--
-- AUTO_INCREMENT for table `news_submission_events`
--
ALTER TABLE `news_submission_events`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=740;

--
-- AUTO_INCREMENT for table `news_types`
--
ALTER TABLE `news_types`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=213;

--
-- AUTO_INCREMENT for table `queues`
--
ALTER TABLE `queues`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT for table `queue_services`
--
ALTER TABLE `queue_services`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;

--
-- AUTO_INCREMENT for table `queue_staff`
--
ALTER TABLE `queue_staff`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=118;

--
-- AUTO_INCREMENT for table `queue_tickets`
--
ALTER TABLE `queue_tickets`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9638;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=157;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `appointments`
--
ALTER TABLE `appointments`
  ADD CONSTRAINT `appointments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `appointments_ibfk_2` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`),
  ADD CONSTRAINT `appointments_ibfk_3` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `appointments_ibfk_4` FOREIGN KEY (`scheduled_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `meetings`
--
ALTER TABLE `meetings`
  ADD CONSTRAINT `meetings_ibfk_1` FOREIGN KEY (`requester_user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `meetings_ibfk_2` FOREIGN KEY (`requester_department_id`) REFERENCES `departments` (`id`),
  ADD CONSTRAINT `meetings_ibfk_3` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `meeting_invitees`
--
ALTER TABLE `meeting_invitees`
  ADD CONSTRAINT `meeting_invitees_ibfk_1` FOREIGN KEY (`meeting_id`) REFERENCES `meetings` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `meeting_invitees_ibfk_2` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`);

--
-- Constraints for table `news_posts`
--
ALTER TABLE `news_posts`
  ADD CONSTRAINT `news_posts_department` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `news_submissions`
--
ALTER TABLE `news_submissions`
  ADD CONSTRAINT `news_submissions_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `news_posts` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `news_submissions_ibfk_2` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`),
  ADD CONSTRAINT `news_submissions_ibfk_3` FOREIGN KEY (`author_user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `news_submission_events`
--
ALTER TABLE `news_submission_events`
  ADD CONSTRAINT `news_submission_events_ibfk_1` FOREIGN KEY (`submission_id`) REFERENCES `news_submissions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `queues`
--
ALTER TABLE `queues`
  ADD CONSTRAINT `queues_ibfk_1` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `queue_counters`
--
ALTER TABLE `queue_counters`
  ADD CONSTRAINT `queue_counters_ibfk_1` FOREIGN KEY (`queue_id`) REFERENCES `queues` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `queue_services`
--
ALTER TABLE `queue_services`
  ADD CONSTRAINT `queue_services_ibfk_1` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `queue_services_ibfk_2` FOREIGN KEY (`staff_id`) REFERENCES `queue_staff` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `queue_staff`
--
ALTER TABLE `queue_staff`
  ADD CONSTRAINT `queue_staff_ibfk_1` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `queue_tickets`
--
ALTER TABLE `queue_tickets`
  ADD CONSTRAINT `queue_tickets_ibfk_1` FOREIGN KEY (`queue_id`) REFERENCES `queues` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `queue_tickets_ibfk_2` FOREIGN KEY (`service_id`) REFERENCES `queue_services` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `queue_tickets_ibfk_3` FOREIGN KEY (`staff_id`) REFERENCES `queue_staff` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `queue_tickets_ibfk_4` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
