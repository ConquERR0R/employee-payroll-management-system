-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 22, 2026 at 01:41 AM
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
-- Database: `employee_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `work_date` date NOT NULL,
  `time_in` time NOT NULL,
  `time_out` time NOT NULL,
  `regular_hours` decimal(5,2) NOT NULL DEFAULT 8.00,
  `ot_hours` decimal(5,2) NOT NULL DEFAULT 0.00,
  `status` varchar(30) NOT NULL DEFAULT 'Regular',
  `rdot_hours` decimal(5,2) NOT NULL DEFAULT 0.00,
  `double_pay_hours` decimal(10,2) NOT NULL DEFAULT 0.00,
  `premium_30_hours` decimal(10,2) NOT NULL DEFAULT 0.00,
  `paid_leave_hours` decimal(5,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`id`, `employee_id`, `work_date`, `time_in`, `time_out`, `regular_hours`, `ot_hours`, `status`, `rdot_hours`, `double_pay_hours`, `premium_30_hours`, `paid_leave_hours`) VALUES
(1, 1, '2026-07-03', '00:00:00', '00:00:00', 8.00, 0.00, 'Regular', 0.00, 0.00, 0.00, 0.00),
(2, 1, '2026-07-02', '00:00:00', '00:00:00', 8.00, 0.00, 'Regular', 0.00, 0.00, 0.00, 0.00),
(4, 1, '2026-07-04', '00:00:00', '00:00:00', 8.00, 0.00, 'Regular', 0.00, 0.00, 0.00, 0.00),
(5, 1, '2026-07-05', '00:00:00', '00:00:00', 8.00, 0.00, 'Regular', 0.00, 0.00, 0.00, 0.00),
(6, 1, '2026-07-06', '00:00:00', '00:00:00', 8.00, 0.00, 'Regular', 0.00, 0.00, 0.00, 0.00),
(7, 1, '2026-07-07', '00:00:00', '00:00:00', 0.00, 0.00, 'RDOT', 8.00, 0.00, 0.00, 0.00),
(8, 1, '2026-07-08', '00:00:00', '00:00:00', 0.00, 0.00, 'RDOT', 8.00, 0.00, 0.00, 0.00),
(9, 1, '2026-07-09', '00:00:00', '00:00:00', 8.00, 0.00, 'Regular', 0.00, 0.00, 0.00, 0.00),
(10, 1, '2026-07-10', '00:00:00', '00:00:00', 8.00, 0.00, 'Regular', 0.00, 0.00, 0.00, 0.00),
(11, 1, '2026-07-11', '00:00:00', '00:00:00', 8.00, 0.00, 'Regular', 0.00, 0.00, 0.00, 0.00),
(22, 1, '2026-07-01', '00:00:00', '00:00:00', 8.00, 0.00, 'Regular', 0.00, 0.00, 0.00, 0.00),
(33, 3, '2026-07-01', '00:00:00', '00:00:00', 8.00, 0.00, 'Regular', 0.00, 0.00, 0.00, 0.00),
(34, 3, '2026-07-02', '00:00:00', '00:00:00', 8.00, 0.00, 'Regular', 0.00, 0.00, 0.00, 0.00),
(35, 3, '2026-07-03', '00:00:00', '00:00:00', 8.00, 0.00, 'Regular', 0.00, 0.00, 0.00, 0.00),
(36, 3, '2026-07-04', '00:00:00', '00:00:00', 0.00, 0.00, 'Day Off', 0.00, 0.00, 0.00, 0.00),
(37, 3, '2026-07-05', '00:00:00', '00:00:00', 0.00, 0.00, 'Day Off', 0.00, 0.00, 0.00, 0.00),
(38, 3, '2026-07-06', '00:00:00', '00:00:00', 8.00, 0.00, 'Regular', 0.00, 0.00, 0.00, 0.00),
(39, 3, '2026-07-07', '00:00:00', '00:00:00', 8.00, 0.00, 'Regular', 0.00, 0.00, 0.00, 0.00),
(40, 3, '2026-07-08', '00:00:00', '00:00:00', 8.00, 0.00, 'Regular', 0.00, 0.00, 0.00, 0.00),
(41, 3, '2026-07-09', '00:00:00', '00:00:00', 8.00, 0.00, 'Regular', 0.00, 0.00, 0.00, 0.00),
(42, 3, '2026-07-10', '00:00:00', '00:00:00', 8.00, 0.00, 'Regular', 0.00, 0.00, 0.00, 0.00),
(43, 3, '2026-07-11', '00:00:00', '00:00:00', 0.00, 0.00, 'Day Off', 0.00, 0.00, 0.00, 0.00),
(44, 3, '2026-07-12', '00:00:00', '00:00:00', 0.00, 0.00, 'Day Off', 0.00, 0.00, 0.00, 0.00),
(45, 3, '2026-07-13', '00:00:00', '00:00:00', 8.00, 0.00, 'Regular', 0.00, 0.00, 0.00, 0.00),
(46, 3, '2026-07-14', '00:00:00', '00:00:00', 8.00, 0.00, 'Regular', 0.00, 0.00, 0.00, 0.00),
(47, 3, '2026-07-15', '00:00:00', '00:00:00', 8.00, 0.00, 'Regular', 0.00, 0.00, 0.00, 0.00),
(48, 2, '2026-07-11', '00:00:00', '00:00:00', 8.00, 0.00, 'Regular', 0.00, 0.00, 0.00, 0.00),
(49, 2, '2026-07-12', '00:00:00', '00:00:00', 0.00, 0.00, 'Day Off', 0.00, 0.00, 0.00, 0.00),
(50, 2, '2026-07-13', '00:00:00', '00:00:00', 8.00, 3.00, 'Regular', 0.00, 0.00, 0.00, 0.00),
(51, 2, '2026-07-14', '00:00:00', '00:00:00', 8.00, 2.00, 'Regular', 0.00, 0.00, 0.00, 0.00),
(52, 2, '2026-07-15', '00:00:00', '00:00:00', 8.00, 3.00, 'Regular', 0.00, 0.00, 0.00, 0.00),
(53, 2, '2026-07-16', '00:00:00', '00:00:00', 8.00, 2.00, 'Regular', 0.00, 0.00, 0.00, 0.00),
(54, 2, '2026-07-17', '00:00:00', '00:00:00', 8.00, 0.00, 'Regular', 0.00, 0.00, 0.00, 0.00),
(55, 2, '2026-07-18', '00:00:00', '00:00:00', 8.00, 0.00, 'Regular', 0.00, 0.00, 0.00, 0.00),
(56, 2, '2026-07-19', '00:00:00', '00:00:00', 0.00, 0.00, 'Day Off', 0.00, 0.00, 0.00, 0.00),
(57, 2, '2026-07-20', '00:00:00', '00:00:00', 8.00, 0.00, 'Regular', 0.00, 0.00, 0.00, 0.00),
(58, 2, '2026-07-21', '00:00:00', '00:00:00', 8.00, 0.00, 'Regular', 0.00, 0.00, 0.00, 0.00),
(59, 2, '2026-07-22', '00:00:00', '00:00:00', 8.00, 0.00, 'Regular', 0.00, 0.00, 0.00, 0.00),
(60, 2, '2026-07-23', '00:00:00', '00:00:00', 8.00, 0.00, 'Regular', 0.00, 0.00, 0.00, 0.00),
(61, 2, '2026-07-24', '00:00:00', '00:00:00', 8.00, 0.00, 'Regular', 0.00, 0.00, 0.00, 0.00),
(62, 2, '2026-07-25', '00:00:00', '00:00:00', 8.00, 0.00, 'Regular', 0.00, 0.00, 0.00, 0.00),
(108, 4, '2026-07-11', '00:00:00', '00:00:00', 8.00, 0.00, 'Regular', 0.00, 0.00, 0.00, 0.00),
(109, 4, '2026-07-12', '00:00:00', '00:00:00', 0.00, 0.00, 'Day Off', 0.00, 0.00, 0.00, 0.00),
(110, 4, '2026-07-13', '00:00:00', '00:00:00', 0.00, 0.00, '30 Percent', 0.00, 0.00, 8.00, 0.00),
(111, 4, '2026-07-14', '00:00:00', '00:00:00', 8.00, 3.00, 'Regular', 0.00, 0.00, 0.00, 0.00),
(112, 4, '2026-07-15', '00:00:00', '00:00:00', 8.00, 0.00, 'Regular', 0.00, 0.00, 0.00, 0.00),
(113, 4, '2026-07-16', '00:00:00', '00:00:00', 8.00, 0.00, 'Regular', 0.00, 0.00, 0.00, 0.00),
(114, 4, '2026-07-17', '00:00:00', '00:00:00', 8.00, 3.00, 'Regular', 0.00, 0.00, 0.00, 0.00),
(115, 4, '2026-07-18', '00:00:00', '00:00:00', 0.00, 0.00, 'RDOT', 8.00, 0.00, 0.00, 0.00),
(116, 4, '2026-07-19', '00:00:00', '00:00:00', 0.00, 0.00, 'Double Pay', 0.00, 8.00, 0.00, 0.00),
(117, 4, '2026-07-20', '00:00:00', '00:00:00', 0.00, 0.00, '30 Percent', 0.00, 0.00, 8.00, 0.00),
(118, 4, '2026-07-21', '00:00:00', '00:00:00', 8.00, 0.00, 'Regular', 0.00, 0.00, 0.00, 0.00),
(119, 4, '2026-07-22', '00:00:00', '00:00:00', 8.00, 0.00, 'Regular', 0.00, 0.00, 0.00, 0.00),
(120, 4, '2026-07-23', '00:00:00', '00:00:00', 8.00, 0.00, 'Regular', 0.00, 0.00, 0.00, 0.00),
(121, 4, '2026-07-24', '00:00:00', '00:00:00', 8.00, 1.00, 'Regular', 0.00, 0.00, 0.00, 0.00),
(122, 4, '2026-07-25', '00:00:00', '00:00:00', 8.00, 5.00, 'Regular', 0.00, 0.00, 0.00, 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `position` varchar(100) NOT NULL,
  `hourly_rate` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`id`, `name`, `email`, `position`, `hourly_rate`) VALUES
(1, 'victor ronnuel david', 'vdbeat10@gmail.com', 'manager', 104.00),
(2, 'Neuton John Paz', 'nj@gmail.com', 'employee', 64.75),
(3, 'Chyra Mae Torrejos', 'chyramaetorrejos@gmail.com', 'teacher', 162.50),
(4, 'Neuton Bayot', 'bayot@gmail.com', 'Team Leader', 84.00);

-- --------------------------------------------------------

--
-- Table structure for table `payroll`
--

CREATE TABLE `payroll` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `cutoff_start` date NOT NULL,
  `cutoff_end` date NOT NULL,
  `total_hours` decimal(10,2) NOT NULL,
  `hourly_rate` decimal(10,2) NOT NULL,
  `gross_pay` decimal(10,2) NOT NULL,
  `sss` decimal(10,2) DEFAULT 0.00,
  `philhealth` decimal(10,2) DEFAULT 0.00,
  `pagibig` decimal(10,2) DEFAULT 0.00,
  `cash_advance` decimal(10,2) DEFAULT 0.00,
  `other_deduction` decimal(10,2) DEFAULT 0.00,
  `total_deductions` decimal(10,2) NOT NULL,
  `net_pay` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payroll`
--

INSERT INTO `payroll` (`id`, `employee_id`, `cutoff_start`, `cutoff_end`, `total_hours`, `hourly_rate`, `gross_pay`, `sss`, `philhealth`, `pagibig`, `cash_advance`, `other_deduction`, `total_deductions`, `net_pay`, `created_at`) VALUES
(1, 2, '2026-07-01', '2026-07-09', 0.00, 1078.00, 0.00, 200.00, 300.00, 200.00, 0.00, 0.00, 700.00, -700.00, '2026-07-21 14:11:33'),
(2, 1, '2026-07-01', '2026-07-09', 0.00, 104.00, 0.00, 300.00, 300.00, 300.00, 0.00, 0.00, 900.00, -900.00, '2026-07-21 14:16:00'),
(3, 1, '2026-07-01', '2026-07-09', 0.00, 104.00, 0.00, 300.00, 300.00, 300.00, 0.00, 0.00, 900.00, 0.00, '2026-07-21 14:20:20'),
(4, 1, '2026-07-01', '2026-07-10', 0.00, 104.00, 0.00, 200.00, 200.00, 200.00, 0.00, 0.00, 600.00, 0.00, '2026-07-21 14:21:08');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_employee_date` (`employee_id`,`work_date`);

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payroll`
--
ALTER TABLE `payroll`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_id` (`employee_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=153;

--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `payroll`
--
ALTER TABLE `payroll`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attendance`
--
ALTER TABLE `attendance`
  ADD CONSTRAINT `attendance_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payroll`
--
ALTER TABLE `payroll`
  ADD CONSTRAINT `payroll_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
