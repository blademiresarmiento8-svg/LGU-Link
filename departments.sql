-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 16, 2026 at 03:50 PM
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
-- Database: `govflow_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `department_id` int(10) UNSIGNED NOT NULL,
  `department_name` varchar(50) NOT NULL,
  `responsibility_center` varchar(20) DEFAULT NULL,
  `ps_code` varchar(20) DEFAULT NULL,
  `mooe_code` varchar(20) DEFAULT NULL,
  `co_code` varchar(20) DEFAULT NULL,
  `department_head` varchar(255) DEFAULT NULL,
  `designation` varchar(100) DEFAULT NULL,
  `charging_department_id` int(10) UNSIGNED DEFAULT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`department_id`, `department_name`, `responsibility_center`, `ps_code`, `mooe_code`, `co_code`, `department_head`, `designation`, `charging_department_id`, `description`) VALUES
(0, 'Municipal Government of Norzagaray, Bulacan', NULL, NULL, NULL, NULL, 'Maria Elena L. Germar', 'Municipal Mayor', NULL, NULL),
(1, 'Administrator\'s Office', NULL, '1031-100', '1031-200', '1031-300', 'Atty. Jhorolar N. Andres', 'Municipal Administrator', NULL, NULL),
(2, 'Mayor\'s Office', '1011-200', '1011-100', '1011-200', '1011-300', 'Atty. Jhorolar N. Andres', 'Municipal Administrator', NULL, NULL),
(3, 'Budget Office', '1002', '1071-100', '1071-200', '1071-300', 'Fernando DG. Cruz', 'Budget Officer', NULL, NULL),
(4, 'BAC Office', '1014', NULL, NULL, NULL, 'Atty. Jhorolar N. Andres', 'Municipal Budget Officer', 2, NULL),
(5, 'Accounting Office', '1025', '1081-100', '1081-200', '1081-300', 'Manuel R. Marcial', 'Municipal Accountant', NULL, NULL),
(6, 'General Service Office', '1036', '1061-100', '1061-200', '1061-300', 'Marilyn C. Ocampo', 'Senior Administrative Assistant III', NULL, NULL),
(7, 'Treasurer\'s Office', '1047', '1091-100', '1091-200', '1091-300', 'Shena Liz P. Alonzo', 'Municipal Treasurer', NULL, NULL),
(8, 'BPLO', '1058', '1031-101', '1031-201', '1031-301', 'Jemelee John De Silva', 'Licensing Officer', NULL, NULL),
(9, 'HRMO', '1069', '1032-100', '1032-200', '1032-300', 'Fernando DG. Cruz', 'OIC - HRMO', NULL, NULL),
(10, 'MENRO', '1070', '8731-100', '8731-200', '8731-300', 'Mark Anthony Francisco', 'OIC - MENRO', NULL, NULL),
(11, 'Registrar\'s Office', '1081', '1051-100', '1051-200', '1051-300', 'Filipina DG. Pascual', 'OIC - Municipal Registrar', NULL, NULL),
(12, 'MPDO', '1092', '1041-100', '1041-200', '1041-300', 'Mark Anthony Francisco', 'Municipal Planning and Development Officer', NULL, NULL),
(13, 'LYDO', '1103', NULL, NULL, NULL, 'Wilmie Kate Z. Logo', 'Local Youth Development Officer', 2, NULL),
(14, 'Tourism Office', NULL, '8717-100', '8717-200', '8717-300', 'Hon. Maria Elena L. Germar', 'Municipal Mayor', NULL, NULL),
(15, 'Public Information Office', NULL, NULL, NULL, NULL, 'Hon. Maria Elena L. Germar', 'Municipal Mayor', 2, NULL),
(16, 'Assessor\'s Office', NULL, '1101-100', '1101-200', '1101-300', 'Portia H. German', 'Municipal Assessor', NULL, NULL),
(17, 'MDRRMO', NULL, '1011-101', '1011-201', '1011-301', 'Jerry R. Sumbillo', 'OIC-MDRRMO', NULL, NULL),
(18, 'NTMPSO', NULL, NULL, NULL, NULL, 'Hon. Maria Elena L. Germar', 'Municipal Mayor', 2, NULL),
(19, 'Sangguniang Bayan', NULL, '1021-100', '1021-200', '1021-300', 'Hon. Giulius Ceazar S. Iapino', 'Municipal Vice Mayor', NULL, NULL),
(20, 'OSCA', NULL, NULL, NULL, NULL, 'Hon. Maria Elena L. Germar', 'Municipal Mayor', 2, NULL),
(21, 'PDAO', NULL, NULL, NULL, NULL, 'Hon. Maria Elena L. Germar', 'Municipal Mayor', 2, NULL),
(22, 'Population Office', NULL, NULL, NULL, NULL, 'Hon. Maria Elena L. Germar', 'Municipal Mayor', 2, NULL),
(23, 'Nutrition Office', NULL, '1031-102', '1031-202', '1031-302', 'Christilina B. Torres', NULL, NULL, NULL),
(24, 'MSWDO', NULL, '7611-100', '7611-200', '7611-300', 'Annalyn Joy C. San Pedro', 'OIC - MSWDO', NULL, NULL),
(25, 'PESO', NULL, '5992-100', '5992-200', '5992-300', 'Atty. Rochelle Marie G. Torres', 'OIC - PESO Manager', NULL, NULL),
(26, 'Engineering Office', NULL, '8751-100', '8751-200', '8751-300', NULL, NULL, NULL, NULL),
(27, 'Agriculture Office', NULL, '8711-100', '8711-200', '8711-300', NULL, NULL, NULL, NULL),
(28, 'Norzagaray Municipal Hospital', NULL, '4421-100', '4421-200', '4421-300', 'Dr. Joselito C. Santiago', NULL, NULL, NULL),
(29, 'Norzagaray College', NULL, '3323-100', '3323-200', '3323-300', 'Dr. Maria Liberty DG. Pascual', 'College President', NULL, NULL),
(30, 'Municipal Health Office', NULL, '4411-100', '4411-200', '4411-300', 'Dr. George Peppard C. Paitim', 'Municipal Health Officer', NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`department_id`),
  ADD KEY `fk_departments_departments` (`charging_department_id`);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `departments`
--
ALTER TABLE `departments`
  ADD CONSTRAINT `fk_departments_departments` FOREIGN KEY (`charging_department_id`) REFERENCES `departments` (`department_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
