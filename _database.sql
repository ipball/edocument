-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Apr 03, 2025 at 07:06 AM
-- Server version: 11.1.2-MariaDB-log
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `edocument`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `category_name` varchar(200) NOT NULL COMMENT 'ชื่อหมวดหมู่เอกสาร',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `category_name`, `created_at`, `updated_at`) VALUES
(1, 'เอกสารทางธุรกิจ', '2025-02-13 21:44:57', '2025-04-02 23:38:44'),
(2, 'เอกสารทางกฎหมาย', '2025-02-13 22:34:29', '2025-04-02 23:38:51'),
(3, 'เอกสารภายในองค์กร', NULL, '2025-04-02 23:38:58'),
(4, 'เอกสารทางการศึกษา', '2025-04-02 23:39:06', NULL),
(5, 'เอกสารทางราชการ', '2025-04-02 23:39:14', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `documents`
--

CREATE TABLE `documents` (
  `id` int(11) NOT NULL,
  `document_code` varchar(100) DEFAULT NULL COMMENT 'รหัสเอกสาร',
  `topic` varchar(255) NOT NULL COMMENT 'ชื่อเอกสาร',
  `register_date` date DEFAULT NULL COMMENT 'วันที่ลงทะเบียนเอกสาร',
  `document_status` enum('draft','pending','approved','active','cancelled','expired','archived','exported') NOT NULL COMMENT 'ร่าง (Draft) – อยู่ระหว่างการจัดทำหรือแก้ไข\r\nรออนุมัติ (Pending) – รอการตรวจสอบจากผู้มีอำนาจ\r\nอนุมัติแล้ว (Approved) – ผ่านการตรวจสอบและได้รับอนุมัติ\r\nใช้งานอยู่ (Active) – มีผลบังคับใช้อย่างเป็นทางการ\r\nยกเลิก (Cancelled) – ไม่มีผลบังคับใช้อีกต่อไป\r\nหมดอายุ (Expired) – หมดอายุหรือสิ้นสุดระยะเวลาการใช้งาน\r\nจัดเก็บ (Archived) – ถูกเก็บไว้เพื่อการอ้างอิง\r\nส่งออก (Exported) – ถูกส่งออกไปยังระบบอื่น',
  `reference` varchar(255) DEFAULT NULL COMMENT 'อ้างอิงเอกสาร',
  `store_location` varchar(255) DEFAULT NULL COMMENT 'สถานที่จัดเก็บเอกสาร',
  `file_name` varchar(255) NOT NULL COMMENT 'อัพโหลดไฟล์เอกสาร',
  `description` text DEFAULT NULL COMMENT 'รายละเอียด',
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `category_id` int(11) NOT NULL DEFAULT 0 COMMENT 'หมวดหมู่เอกสาร'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `documents`
--

INSERT INTO `documents` (`id`, `document_code`, `topic`, `register_date`, `document_status`, `reference`, `store_location`, `file_name`, `description`, `created_by`, `updated_by`, `created_at`, `updated_at`, `category_id`) VALUES
(2, 'DOC-2025-00001', 'ขออนุมัติจัดจ้างติดตั้งระบบสอบเทียบ ผู้ชนะการเสนอราคา : บริษัท โพรเซส ออโตเมชั่น แอนด์ คอนโทรล จำกัด', '2025-04-01', 'active', 'DWM6609Y680004', 'ST1', 'doc_202504030635342061.pdf', 'เพื่อให้การดำเนินงานเป็นไปอย่างมีประสิทธิภาพ และสอดคล้องกับมาตรฐานที่กำหนด การสอบเทียบเครื่องมือและอุปกรณ์จึงเป็นสิ่งสำคัญในการรักษาคุณภาพและความแม่นยำ Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. \r\n\r\nจากการตรวจสอบและประเมินความจำเป็นในการดำเนินงาน พบว่ามีความต้องการในการติดตั้งระบบสอบเทียบเพื่อรองรับกระบวนการที่ได้มาตรฐานและเพิ่มประสิทธิภาพการทำงาน Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip', 1, NULL, '2025-04-03 06:35:34', NULL, 5),
(3, 'DOC-2025-00002', 'ขออนุมัติจัดจ้างทำระบบ Temperature online calibraton ผู้ชนะการเสนอราคา : บริษัท โมโมอินฟินิเทค จำกัด', '2025-04-06', 'pending', '68029412941', 'ST1', 'doc_202504030637432082.pdf', 'เพื่อให้การดำเนินงานเป็นไปอย่างมีประสิทธิภาพและสอดคล้องกับมาตรฐานที่กำหนด การสอบเทียบเครื่องมือและอุปกรณ์จึงมีความจำเป็นในการรักษาคุณภาพและความแม่นยำในการทำงาน Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.\r\nจากการประเมินความจำเป็นในการดำเนินงาน พบว่ามีความต้องการในการติดตั้งระบบสอบเทียบเพื่อรองรับกระบวนการที่ได้มาตรฐานและเพิ่มประสิทธิภาพการทำงาน Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.\r\nดังนั้น จึงขออนุมัติการจัดจ้างเพื่อติดตั้งระบบสอบเทียบ เพื่อให้สามารถดำเนินการได้อย่างถูกต้องและคุ้มค่า Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.\r\nขอขอบคุณสำหรับการพิจารณาและอนุมัติในครั้งนี้ เพื่อให้กระบวนการดำเนินงานเป็นไปอย่างราบรื่นและสอดคล้องกับข้อกำหนดมาตรฐาน Curabitur pretium tincidunt lacus. Nulla gravida orci a odio. Nullam varius, turpis et commodo pharetra, est eros bibendum elit.', 1, NULL, '2025-04-03 06:37:43', NULL, 5),
(4, 'DOC-2025-00003', 'ขออนุมัติจัดซื้อวัสดุสิ้นเปลือง Helium Purifier ผู้ชนะการเสนอราคา : บริษัท ซี.อี. อินสทรูเม้นท์ (ประเทศไทย) จำกัด', '2025-04-03', 'pending', 'ST670102ด030069	', 'ST1', 'doc_202504030639489441.pdf', 'เพื่อให้กระบวนการทำงานเป็นไปตามมาตรฐานที่กำหนด การสอบเทียบอุปกรณ์และเครื่องมือต่างๆ มีความจำเป็นในการรักษาความแม่นยำและประสิทธิภาพในการดำเนินงาน Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.\r\nตามการตรวจสอบและวิเคราะห์ข้อมูลเบื้องต้น พบว่ามีความจำเป็นต้องดำเนินการติดตั้งระบบสอบเทียบเพื่อรองรับการใช้งานที่มีคุณภาพและลดข้อผิดพลาดในการวัดค่าทางเทคนิค Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.\r\nดังนั้น ขออนุมัติการจัดจ้างผู้เชี่ยวชาญในการติดตั้งระบบสอบเทียบ เพื่อให้สามารถดำเนินงานได้อย่างถูกต้อง แม่นยำ และเป็นไปตามข้อกำหนดมาตรฐานที่เกี่ยวข้อง Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.\r\nขอความอนุเคราะห์พิจารณาและอนุมัติการดำเนินการตามข้อเสนอนี้ เพื่อให้สามารถพัฒนากระบวนการทำงานให้ดียิ่งขึ้น และตอบสนองต่อข้อกำหนดด้านคุณภาพ Curabitur pretium tincidunt lacus. Nulla gravida orci a odio. Nullam varius, turpis et commodo pharetra, est eros bibendum elit.', 1, 1, '2025-04-03 06:39:48', '2025-04-03 06:47:26', 5);

-- --------------------------------------------------------

--
-- Table structure for table `running_numbers`
--

CREATE TABLE `running_numbers` (
  `id` int(11) NOT NULL,
  `prefix` varchar(10) NOT NULL,
  `current_number` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `running_numbers`
--

INSERT INTO `running_numbers` (`id`, `prefix`, `current_number`, `created_at`, `updated_at`) VALUES
(3, 'DOC', 3, '2025-04-02 23:33:52', '2025-04-02 23:39:48');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(20) NOT NULL COMMENT 'username',
  `password` varchar(255) NOT NULL COMMENT 'password',
  `email` varchar(255) NOT NULL COMMENT 'อีเมล์',
  `fullname` varchar(255) NOT NULL COMMENT 'ชื่อ',
  `profile_image` varchar(255) DEFAULT NULL COMMENT 'รูปภาพ',
  `login_time` datetime DEFAULT NULL COMMENT 'เวลาเข้าสู่ระบบ',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`, `fullname`, `profile_image`, `login_time`, `created_at`, `updated_at`) VALUES
(1, 'todo', '$2y$10$ZblmKTgtPeWlM74XzI6pEua3ymtW03q.ODRfXkYGhsGxsxVARG0Ni', 'todo@itoffside.com', 'เอกพงษ์ ใจจิตดี', 'user_202504022332073684.jpg', NULL, '2025-02-14 00:04:35', '2025-04-02 23:32:07'),
(2, 'tawatsak', '$2y$10$i0a31.51gytrLPJnnAZ18.aVWH4l2NVULx2uXST/py/OqImU59hSC', 'tawatsak@itoffside.com', 'Tawatsak Jaidee', '', NULL, NULL, '2025-02-14 07:34:27');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `documents`
--
ALTER TABLE `documents`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `running_numbers`
--
ALTER TABLE `running_numbers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `documents`
--
ALTER TABLE `documents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `running_numbers`
--
ALTER TABLE `running_numbers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
