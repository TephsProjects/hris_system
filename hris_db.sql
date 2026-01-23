-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 23, 2026 at 08:44 AM
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
-- Database: `hris_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `benefits`
--

CREATE TABLE `benefits` (
  `benefit_id` int(11) NOT NULL,
  `emp_id` int(11) NOT NULL,
  `health_plan` varchar(255) DEFAULT NULL,
  `retirement_plan` varchar(255) DEFAULT NULL,
  `insurance_type` varchar(255) DEFAULT NULL,
  `dependents` int(11) DEFAULT 0,
  `date_assigned` date DEFAULT curdate()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `branches`
--

CREATE TABLE `branches` (
  `branch_id` int(11) NOT NULL,
  `branch_name` varchar(255) NOT NULL,
  `address` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `branches`
--

INSERT INTO `branches` (`branch_id`, `branch_name`, `address`, `created_at`, `updated_at`) VALUES
(1, 'Office', 'Bayani Road', '2025-12-02 03:08:28', '2025-12-02 03:08:28'),
(2, 'BGC', 'adasdasd', '2025-12-02 05:39:03', '2025-12-02 05:39:03');

-- --------------------------------------------------------

--
-- Table structure for table `candidates`
--

CREATE TABLE `candidates` (
  `candidate_id` int(11) NOT NULL,
  `job_id` int(11) DEFAULT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `resume` varchar(255) DEFAULT NULL,
  `status` enum('Applied','Screened','Interview','Hired','Rejected') DEFAULT 'Applied',
  `applied_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `candidates`
--

INSERT INTO `candidates` (`candidate_id`, `job_id`, `full_name`, `email`, `phone`, `resume`, `status`, `applied_at`) VALUES
(1, 1, 'John Doe', 'john_doe@gmail.com', '0900000000', NULL, 'Hired', '2026-01-06 03:28:48');

-- --------------------------------------------------------

--
-- Table structure for table `company_info`
--

CREATE TABLE `company_info` (
  `id` int(11) NOT NULL,
  `company_name` varchar(255) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `postal_code` varchar(20) DEFAULT NULL,
  `contact_number` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `website` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `company_info`
--

INSERT INTO `company_info` (`id`, `company_name`, `address`, `postal_code`, `contact_number`, `email`, `website`, `created_at`, `updated_at`) VALUES
(1, 'Your Company Name', 'Company Address', '0000', '09123456789', 'info@company.com', 'https://company.com', '2026-01-13 07:02:51', '2026-01-15 01:46:00');

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `department_id` int(11) NOT NULL,
  `department_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`department_id`, `department_name`) VALUES
(5, 'Billing'),
(2, 'Finance'),
(4, 'Human Resources'),
(1, 'IT'),
(3, 'Payroll');

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `emp_id` int(11) NOT NULL,
  `employee_no` varchar(20) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `middle_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(100) NOT NULL,
  `name_suffix` varchar(20) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `age` int(3) DEFAULT NULL,
  `civil_status` varchar(50) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `place_of_birth` varchar(100) DEFAULT NULL,
  `department` varchar(100) DEFAULT NULL,
  `branch_id` int(11) DEFAULT NULL,
  `department_id` int(11) DEFAULT NULL,
  `position_id` int(11) DEFAULT NULL,
  `tax_status` varchar(100) DEFAULT NULL,
  `tin_no` varchar(20) DEFAULT NULL,
  `sss_no` varchar(20) DEFAULT NULL,
  `philhealth_no` varchar(20) DEFAULT NULL,
  `nationality` varchar(50) DEFAULT NULL,
  `religion` varchar(50) DEFAULT NULL,
  `spouse_name` varchar(100) DEFAULT NULL,
  `blood_type` varchar(5) DEFAULT NULL,
  `medical_notes` text DEFAULT NULL,
  `gender` varchar(10) DEFAULT NULL,
  `mobile_number` varchar(20) DEFAULT NULL,
  `secondary_mobile` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `personal_email` varchar(150) DEFAULT NULL,
  `contact_person` varchar(255) DEFAULT NULL,
  `emergency_contact` varchar(50) DEFAULT NULL,
  `emergency_contact_relationship` varchar(100) DEFAULT NULL,
  `emergency_contact_address` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `contract_file` varchar(255) DEFAULT NULL,
  `contract_end_date` date DEFAULT NULL,
  `employment_status` varchar(50) DEFAULT NULL,
  `employment_type` varchar(50) DEFAULT NULL,
  `position` varchar(100) DEFAULT NULL,
  `date_hired` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `philhealth_file` varchar(255) DEFAULT NULL,
  `sss_file` varchar(255) DEFAULT NULL,
  `pagibig_file` varchar(255) DEFAULT NULL,
  `nbi_file` varchar(255) DEFAULT NULL,
  `father_name` varchar(255) DEFAULT NULL,
  `father_occupation` varchar(255) DEFAULT NULL,
  `mother_name` varchar(255) DEFAULT NULL,
  `mother_occupation` varchar(255) DEFAULT NULL,
  `guardian_name` varchar(255) DEFAULT NULL,
  `guardian_contact` varchar(50) DEFAULT NULL,
  `work_schedule` varchar(50) DEFAULT NULL,
  `regularization_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`emp_id`, `employee_no`, `first_name`, `middle_name`, `last_name`, `name_suffix`, `address`, `age`, `civil_status`, `date_of_birth`, `place_of_birth`, `department`, `branch_id`, `department_id`, `position_id`, `tax_status`, `tin_no`, `sss_no`, `philhealth_no`, `nationality`, `religion`, `spouse_name`, `blood_type`, `medical_notes`, `gender`, `mobile_number`, `secondary_mobile`, `email`, `personal_email`, `contact_person`, `emergency_contact`, `emergency_contact_relationship`, `emergency_contact_address`, `image`, `contract_file`, `contract_end_date`, `employment_status`, `employment_type`, `position`, `date_hired`, `created_at`, `philhealth_file`, `sss_file`, `pagibig_file`, `nbi_file`, `father_name`, `father_occupation`, `mother_name`, `mother_occupation`, `guardian_name`, `guardian_contact`, `work_schedule`, `regularization_date`) VALUES
(1, '000000', 'Stephen', '', 'Baay', '', '20 A Salong St. Western Bicutan Taguig City', 24, 'Single', '2001-09-05', 'Taguig City', 'IT', 1, 1, 1, '', '', '', '', ' Filipino', '', '', '', '', 'Male', '09322343454', '', 'steph@example.com', '', '', '', '', '', '1764310395_bald_man.jpg', NULL, '0000-00-00', 'Active', 'Full-time', 'Programmer', '2025-11-28', '2025-11-28 06:11:36', NULL, NULL, NULL, NULL, '', '', '', '', '', '', '', NULL),
(16, '1', 'Lucas', NULL, 'Wills', NULL, '37 Bayani Road', 35, 'Married', '0000-00-00', NULL, 'Billing', NULL, 5, 5, '11132123', '', '', '007000003949', 'Filipino', NULL, NULL, NULL, NULL, 'Male', '', NULL, 'wills.lucas@gmail.com', NULL, '', '', NULL, NULL, NULL, NULL, '0000-00-00', 'Active', 'Full-time', 'Admin Assistant', '2025-12-25', '2026-01-06 06:47:59', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(17, '2', 'Robert', NULL, 'White', NULL, '20 Random Str', 25, 'Single', '2000-10-06', NULL, 'Payroll', 1, 4, 6, '', '', '', '007000003948', 'Filipino', NULL, NULL, NULL, NULL, 'Male', '', NULL, 'white.rober@gmail.com', NULL, '', '', NULL, NULL, NULL, NULL, '0000-00-00', 'Active', 'Full-time', 'Payroll Manager', '2025-06-13', '2026-01-06 06:47:59', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(18, '3', 'Cornelius', NULL, 'Magecus', NULL, 'Saturn', 50, 'Married', '0000-00-00', NULL, 'Mage ', NULL, 2, 4, '', '', '', '007000003947', 'Filipino', NULL, NULL, NULL, NULL, 'Male', '', NULL, 'cornelius90@gmail.com', NULL, '', '', NULL, NULL, NULL, NULL, '0000-00-00', NULL, NULL, 'Magician', '2010-10-25', '2026-01-06 06:47:59', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(19, '4', 'Hadie', NULL, 'Micks', NULL, '47 Phase 53 Taguig City', 27, 'Single', '1999-12-08', NULL, 'Finance', NULL, NULL, NULL, '', NULL, NULL, NULL, 'Filipino', NULL, NULL, NULL, NULL, 'Female', NULL, NULL, 'cheese.micks@gmail.com', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Manager', '0000-00-00', '2026-01-06 06:47:59', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(20, '5', 'Danilo', NULL, 'Robles', NULL, 'Secret', 34, 'Married', '0000-00-00', NULL, 'IT', 1, NULL, NULL, '', NULL, NULL, NULL, 'Filipino', NULL, NULL, NULL, NULL, 'Male', NULL, NULL, 'robles_danilo11@gmail.com', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 'Probationary', 'Full-time', 'Project Manager', '2015-05-09', '2026-01-06 06:47:59', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(27, '6', 'Karrie', NULL, 'Malupit', NULL, 'Lopez Street', 23, 'Single', '0000-00-00', NULL, 'Finance', NULL, NULL, NULL, '', NULL, NULL, NULL, 'Filipino', NULL, NULL, NULL, NULL, 'Female', NULL, NULL, 'karrie_ml@gmail.com', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Assistant', '2026-04-01', '2026-01-06 06:50:46', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(28, '7', 'Bruce', NULL, 'Willis', NULL, 'New York', 45, 'Married', '0000-00-00', NULL, 'Purchasing ', NULL, NULL, NULL, '', NULL, NULL, NULL, 'Filipino', NULL, NULL, NULL, NULL, 'Male', NULL, NULL, 'brc.wls@gmail.com', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Purchase Officer', '2005-07-14', '2026-01-06 06:50:46', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(37, '8', 'Derby', NULL, 'Skates', NULL, 'Signal Village Tagui', 30, 'Married', '0000-00-00', NULL, 'Finance', NULL, NULL, NULL, '', NULL, NULL, NULL, 'Filipino', NULL, NULL, NULL, NULL, 'Female', NULL, NULL, 'skates@gmail.com', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Assistant', '2015-10-28', '2026-01-06 06:58:02', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(38, '9', 'Rudy', NULL, 'Baldwin', NULL, 'Kalahkang Maynila', 0, 'Single', '1991-06-16', NULL, NULL, 2, 1, 1, 'Resident Citizen', '123123123', '1231231231', '123123123123', 'Filipino', NULL, NULL, NULL, NULL, 'Female', '09854246544', NULL, 'rudy_baldwin@gmail.com', NULL, 'Mommy Baldwin', '096513136435', NULL, NULL, '1768271794_MV5BYzFlY2UyOGQtNDI1Mi00NzBkLWI4ZDUtNTI5ZDVlOGQ5ZGY4XkEyXkFqcGc@._V1_QL75_UY207_CR72,0,140,207_.jpg', NULL, '0000-00-00', 'Active', 'Full-time', NULL, '2026-01-13', '2026-01-13 02:36:34', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `job_openings`
--

CREATE TABLE `job_openings` (
  `job_id` int(11) NOT NULL,
  `title` varchar(100) DEFAULT NULL,
  `department` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `requirements` text DEFAULT NULL,
  `status` enum('Open','Closed') DEFAULT 'Open',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `job_openings`
--

INSERT INTO `job_openings` (`job_id`, `title`, `department`, `description`, `requirements`, `status`, `created_at`) VALUES
(1, 'Software Developer', 'IT', 'Hiring for this role', 'At least 50 years of experience in related field.', 'Open', '2026-01-06 03:28:15');

-- --------------------------------------------------------

--
-- Table structure for table `leaves`
--

CREATE TABLE `leaves` (
  `leave_id` int(11) NOT NULL,
  `emp_id` int(11) NOT NULL,
  `leave_type` varchar(50) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `reason` text DEFAULT NULL,
  `status` enum('Pending','Approved','Denied') DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `leaves`
--

INSERT INTO `leaves` (`leave_id`, `emp_id`, `leave_type`, `start_date`, `end_date`, `reason`, `status`, `created_at`) VALUES
(1, 1, 'Vacation Leave', '2026-01-14', '2026-01-16', '', 'Approved', '2026-01-14 00:34:20');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `emp_id` int(11) DEFAULT NULL,
  `type` varchar(50) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `read_status` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `onboarding_tasks`
--

CREATE TABLE `onboarding_tasks` (
  `task_id` int(11) NOT NULL,
  `emp_id` int(11) DEFAULT NULL,
  `task_name` varchar(255) DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `completed_at` date DEFAULT NULL,
  `status` enum('Pending','Completed') DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `onboarding_tasks`
--

INSERT INTO `onboarding_tasks` (`task_id`, `emp_id`, `task_name`, `due_date`, `completed_at`, `status`) VALUES
(1, NULL, 'Submit Government IDs', '2025-12-01', NULL, 'Pending'),
(2, NULL, 'Attend Company Orientation', '2025-12-03', NULL, 'Pending'),
(3, NULL, 'Setup Company Email', '2025-11-30', NULL, 'Pending');

-- --------------------------------------------------------

--
-- Table structure for table `payroll`
--

CREATE TABLE `payroll` (
  `payroll_id` int(11) NOT NULL,
  `emp_id` int(11) NOT NULL,
  `payroll_month` varchar(20) NOT NULL,
  `basic_salary` decimal(10,2) NOT NULL,
  `allowances` decimal(10,2) DEFAULT 0.00,
  `deductions` decimal(10,2) DEFAULT 0.00,
  `net_salary` decimal(10,2) GENERATED ALWAYS AS (`basic_salary` + `allowances` - `deductions`) STORED,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `payment_type` enum('monthly','biweekly') NOT NULL DEFAULT 'monthly'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `performance`
--

CREATE TABLE `performance` (
  `perf_id` int(11) NOT NULL,
  `emp_id` int(11) NOT NULL,
  `evaluation_period` varchar(50) DEFAULT NULL,
  `evaluator` varchar(100) DEFAULT NULL,
  `rating` decimal(3,2) DEFAULT NULL,
  `comments` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `performance`
--

INSERT INTO `performance` (`perf_id`, `emp_id`, `evaluation_period`, `evaluator`, `rating`, `comments`, `created_at`) VALUES
(1, 1, 'Q1 2026', 'Ako', 5.00, '', '2026-01-05 06:27:50');

-- --------------------------------------------------------

--
-- Table structure for table `positions`
--

CREATE TABLE `positions` (
  `position_id` int(11) NOT NULL,
  `position_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `positions`
--

INSERT INTO `positions` (`position_id`, `position_name`) VALUES
(4, 'Admin Assistant'),
(5, 'Billing Consultant'),
(3, 'HR Assistant'),
(6, 'HR Consultant'),
(2, 'HR Manager'),
(1, 'IT Programmer'),
(7, 'Software Developer');

-- --------------------------------------------------------

--
-- Table structure for table `salary_history`
--

CREATE TABLE `salary_history` (
  `history_id` int(11) NOT NULL,
  `emp_id` int(11) NOT NULL,
  `previous_salary` decimal(10,2) DEFAULT NULL,
  `new_salary` decimal(10,2) DEFAULT NULL,
  `change_reason` varchar(255) DEFAULT NULL,
  `effective_date` date DEFAULT curdate()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `default_work_hours` varchar(50) DEFAULT '8:00-17:00',
  `regularization_days` int(11) DEFAULT 90,
  `date_format` varchar(20) DEFAULT 'Y-m-d',
  `notify_new_employee` tinyint(1) DEFAULT 1,
  `notify_leave_request` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `default_work_hours`, `regularization_days`, `date_format`, `notify_new_employee`, `notify_leave_request`, `created_at`, `updated_at`) VALUES
(1, '8:00-15:00', 90, 'Y-m-d', 1, 1, '2026-01-13 07:22:41', '2026-01-13 07:45:00');

-- --------------------------------------------------------

--
-- Table structure for table `trainings`
--

CREATE TABLE `trainings` (
  `training_id` int(11) NOT NULL,
  `emp_id` int(11) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `progress` int(11) DEFAULT 0,
  `due_date` date DEFAULT NULL,
  `completion_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `trainings`
--

INSERT INTO `trainings` (`training_id`, `emp_id`, `title`, `progress`, `due_date`, `completion_date`) VALUES
(1, NULL, 'Workplace Safety Orientation', 100, NULL, '2026-01-08'),
(2, NULL, 'Data Privacy and Security Training', 50, NULL, NULL),
(6, 28, 'Health and Safety', 0, '2026-01-16', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) UNSIGNED NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `role` varchar(50) NOT NULL DEFAULT 'HR Staff',
  `employer_ss_number` varchar(20) DEFAULT NULL,
  `employer_type` enum('regular','household') DEFAULT NULL,
  `e_signature` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `full_name`, `created_at`, `role`, `employer_ss_number`, `employer_type`, `e_signature`) VALUES
(1, 'admin', '$2y$10$1/zeyFzWzmELhAea1GJikO1Mev4MJL2r/NbFb6cHNzPaLsGKziC96', 'Administrator', '2025-11-28 06:01:47', 'HR Staff', NULL, NULL, NULL),
(4, 'hr_staff', '$2y$10$1M.k69e73g3VpUjnjBnTUO5Xfeppw4SpKReJKSngDF2J2/876l9bi', 'steph', '2026-01-08 06:54:38', 'Admin', '21-00542-021', 'regular', 'esign_695f54aed1f0f9.65288352.png');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `benefits`
--
ALTER TABLE `benefits`
  ADD PRIMARY KEY (`benefit_id`),
  ADD KEY `emp_id` (`emp_id`);

--
-- Indexes for table `branches`
--
ALTER TABLE `branches`
  ADD PRIMARY KEY (`branch_id`);

--
-- Indexes for table `candidates`
--
ALTER TABLE `candidates`
  ADD PRIMARY KEY (`candidate_id`),
  ADD KEY `job_id` (`job_id`);

--
-- Indexes for table `company_info`
--
ALTER TABLE `company_info`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`department_id`),
  ADD UNIQUE KEY `department_name` (`department_name`);

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`emp_id`),
  ADD UNIQUE KEY `employee_no` (`employee_no`);

--
-- Indexes for table `job_openings`
--
ALTER TABLE `job_openings`
  ADD PRIMARY KEY (`job_id`);

--
-- Indexes for table `leaves`
--
ALTER TABLE `leaves`
  ADD PRIMARY KEY (`leave_id`),
  ADD KEY `emp_id` (`emp_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `onboarding_tasks`
--
ALTER TABLE `onboarding_tasks`
  ADD PRIMARY KEY (`task_id`),
  ADD KEY `emp_id` (`emp_id`);

--
-- Indexes for table `payroll`
--
ALTER TABLE `payroll`
  ADD PRIMARY KEY (`payroll_id`),
  ADD KEY `emp_id` (`emp_id`);

--
-- Indexes for table `performance`
--
ALTER TABLE `performance`
  ADD PRIMARY KEY (`perf_id`),
  ADD KEY `emp_id` (`emp_id`);

--
-- Indexes for table `positions`
--
ALTER TABLE `positions`
  ADD PRIMARY KEY (`position_id`),
  ADD UNIQUE KEY `position_name` (`position_name`);

--
-- Indexes for table `salary_history`
--
ALTER TABLE `salary_history`
  ADD PRIMARY KEY (`history_id`),
  ADD KEY `emp_id` (`emp_id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `trainings`
--
ALTER TABLE `trainings`
  ADD PRIMARY KEY (`training_id`),
  ADD KEY `emp_id` (`emp_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `benefits`
--
ALTER TABLE `benefits`
  MODIFY `benefit_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `branches`
--
ALTER TABLE `branches`
  MODIFY `branch_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `candidates`
--
ALTER TABLE `candidates`
  MODIFY `candidate_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `company_info`
--
ALTER TABLE `company_info`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `department_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `emp_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `job_openings`
--
ALTER TABLE `job_openings`
  MODIFY `job_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `leaves`
--
ALTER TABLE `leaves`
  MODIFY `leave_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `onboarding_tasks`
--
ALTER TABLE `onboarding_tasks`
  MODIFY `task_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `payroll`
--
ALTER TABLE `payroll`
  MODIFY `payroll_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `performance`
--
ALTER TABLE `performance`
  MODIFY `perf_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `positions`
--
ALTER TABLE `positions`
  MODIFY `position_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `salary_history`
--
ALTER TABLE `salary_history`
  MODIFY `history_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `trainings`
--
ALTER TABLE `trainings`
  MODIFY `training_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `benefits`
--
ALTER TABLE `benefits`
  ADD CONSTRAINT `benefits_ibfk_1` FOREIGN KEY (`emp_id`) REFERENCES `employees` (`emp_id`);

--
-- Constraints for table `candidates`
--
ALTER TABLE `candidates`
  ADD CONSTRAINT `candidates_ibfk_1` FOREIGN KEY (`job_id`) REFERENCES `job_openings` (`job_id`);

--
-- Constraints for table `leaves`
--
ALTER TABLE `leaves`
  ADD CONSTRAINT `leaves_ibfk_1` FOREIGN KEY (`emp_id`) REFERENCES `employees` (`emp_id`);

--
-- Constraints for table `onboarding_tasks`
--
ALTER TABLE `onboarding_tasks`
  ADD CONSTRAINT `onboarding_tasks_ibfk_1` FOREIGN KEY (`emp_id`) REFERENCES `employees` (`emp_id`);

--
-- Constraints for table `payroll`
--
ALTER TABLE `payroll`
  ADD CONSTRAINT `payroll_ibfk_1` FOREIGN KEY (`emp_id`) REFERENCES `employees` (`emp_id`);

--
-- Constraints for table `performance`
--
ALTER TABLE `performance`
  ADD CONSTRAINT `performance_ibfk_1` FOREIGN KEY (`emp_id`) REFERENCES `employees` (`emp_id`);

--
-- Constraints for table `salary_history`
--
ALTER TABLE `salary_history`
  ADD CONSTRAINT `salary_history_ibfk_1` FOREIGN KEY (`emp_id`) REFERENCES `employees` (`emp_id`);

--
-- Constraints for table `trainings`
--
ALTER TABLE `trainings`
  ADD CONSTRAINT `trainings_ibfk_1` FOREIGN KEY (`emp_id`) REFERENCES `employees` (`emp_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
