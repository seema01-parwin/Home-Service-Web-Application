-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 24, 2025 at 01:05 PM
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
-- Database: `home_service_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `admin_id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `profile_picture` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`admin_id`, `username`, `password`, `profile_picture`) VALUES
(1, 'admin', '$2y$10$oEze7jNsFGBod76KX3VfLO07tL.3Bp6Wsu.4kfhLr6JMNy.1WUiOy', 'admin_1750742746.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `booking_id` int(11) NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `worker_id` int(11) DEFAULT NULL,
  `payment_method` enum('Cash on Service','Card') NOT NULL,
  `service_id` int(11) NOT NULL,
  `date` date DEFAULT NULL,
  `time` time DEFAULT NULL,
  `booking_datetime` datetime NOT NULL,
  `status` enum('Pending','Confirmed','Completed','Cancelled') NOT NULL DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `booking_status` varchar(20) DEFAULT 'Pending' CHECK (`booking_status` in ('Pending','Confirmed','Completed','Cancelled')),
  `service_address` varchar(255) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `postal_code` varchar(20) DEFAULT NULL,
  `customer_note` text DEFAULT NULL,
  `total_amount` decimal(10,2) DEFAULT 0.00,
  `payment_status` enum('Pending','Unpaid','Paid') DEFAULT 'Pending',
  `updated_at` datetime DEFAULT NULL,
  `customer_visible` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`booking_id`, `customer_id`, `worker_id`, `payment_method`, `service_id`, `date`, `time`, `booking_datetime`, `status`, `created_at`, `booking_status`, `service_address`, `city`, `postal_code`, `customer_note`, `total_amount`, `payment_status`, `updated_at`, `customer_visible`) VALUES
(6, 1, 1, '', 1, '2025-06-21', '15:47:00', '2025-06-21 15:47:00', 'Pending', '2025-06-21 10:48:02', 'Completed', 'Yalipadunugama, Mahawala Road, Ratnapura.', 'Ratnapura', '70000', 'None', 2000.00, 'Paid', '2025-06-21 05:12:43', 1),
(7, 3, 2, 'Cash on Service', 3, '2025-06-24', '21:30:00', '2025-06-24 21:30:00', 'Pending', '2025-06-24 06:40:10', 'Pending', 'Yalipadunugama, Mahawala Road, Ratnapura.', 'Ratnapura', '70000', 'None', 0.00, 'Pending', NULL, 1),
(8, 1, 1, 'Cash on Service', 1, '2025-06-24', '09:51:00', '2025-06-24 09:51:00', 'Pending', '2025-06-24 06:52:00', 'Pending', 'Yalipadunugama, Mahawala Road, Ratnapura.', 'Ratnapura', '70000', 'None', 0.00, 'Pending', NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `contacts`
--

CREATE TABLE `contacts` (
  `contact_id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `address` varchar(255) NOT NULL,
  `working_hours` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contacts`
--

INSERT INTO `contacts` (`contact_id`, `email`, `phone`, `address`, `working_hours`) VALUES
(1, 'spi@homeservices.com', '+947-123-4567', 'New Town, Ratnapura, Sri Lanka.', 'Mon - Fri: 9:00 AM - 6:00 PM');

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

CREATE TABLE `contact_messages` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `customer_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `password` varchar(255) NOT NULL,
  `address` text NOT NULL,
  `profile_picture` varchar(255) DEFAULT 'default.png',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('active','inactive') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`customer_id`, `name`, `email`, `phone`, `password`, `address`, `profile_picture`, `created_at`, `status`) VALUES
(1, 'Seema Parwin', 'sheema2374@gmail.com', '0752272861', '$2y$10$R4Hn4AB9rbzwSAs4thtIluvJazHjtaLCwDAy4Is3kQyfrvb2jA9u6', 'Yalipadunugama, Mahawala Road, Ratnapura.', 'profile_6855b4b50eb377.70599129.jpg', '2025-06-21 02:17:39', 'active'),
(2, 'Fathima Himasha', 'hima12@gmail.com', '0767644312', '$2y$10$Uv5sz.w0g2nyrY3AZYfhpObFaYYqHmV92tA7Lw7sj/PkTE/qs.bIe', 'Kahawatta, Ratnapura.', 'profile_685a4433dce726.14075786.jpg', '2025-06-24 06:00:47', 'active'),
(3, 'Fathima Shihara', 'shihara12@gmail.com', '0765322453', '$2y$10$GI2y8PdWuVJhJ8VadqcHz.uim3MHdoO4VDYVxgBHPzL6V5V40/Ht2', 'Mahawala Road, Ratnapura.', 'profile_685a44fc4306a9.27030802.jpeg', '2025-06-24 06:18:58', 'active'),
(4, 'Fathima Shamra', 'shamra12@gmail.com', '0765434567', '$2y$10$INtmzBYRb36E6E0yo1z4hO01pJGfqhNQjt6VioD1ueBUlf.TPx6jq', 'Rakwana, Ratnapura.', 'profile_685a445d3fbfc5.14836197.jpg', '2025-06-24 06:20:01', 'active'),
(5, 'Joshua Dilukshan', 'dilukshan12@gmail.com', '0765645321', '$2y$10$UK9U7/YTcAzkH4AQNIAEAOK/YOiwgu/2FQXMvYfksOMFG3UP6Jo.2', 'Balangoda, Ratnapura.', 'profile_685a4480627e25.60123486.jpg', '2025-06-24 06:21:07', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `faqs`
--

CREATE TABLE `faqs` (
  `id` int(11) NOT NULL,
  `question` text NOT NULL,
  `answer` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `faqs`
--

INSERT INTO `faqs` (`id`, `question`, `answer`) VALUES
(1, 'How do I book a home service?', 'Go to your dashboard, click on “Book a Service”, choose a category, date and time.'),
(2, 'Can I cancel a booking?', 'Yes, you can cancel it from your dashboard before the service time.'),
(3, 'What if the worker does not show up?', 'You can report the issue via Contact Support, and we’ll investigate it.'),
(4, 'How do I rate a service?', 'After the service is completed, go to Booking History and click “Rate Now”.'),
(5, 'How to contact customer support?', 'Use the Contact Us form or email support@example.com for help.');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `sender_type` enum('admin','worker','customer') NOT NULL,
  `sender_id` int(11) NOT NULL,
  `recipient_type` enum('customer','worker','admin','all_customers','all_workers','all_admins') NOT NULL,
  `recipient_id` int(11) DEFAULT NULL,
  `message` text NOT NULL,
  `emoji` varchar(10) DEFAULT NULL,
  `sent_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `sender_type`, `sender_id`, `recipient_type`, `recipient_id`, `message`, `emoji`, `sent_at`) VALUES
(1, 'admin', 0, 'worker', 1, 'Hello..! Mufeedh', '😍', '2025-06-21 18:54:22'),
(3, 'worker', 1, 'admin', NULL, 'Hello..! Admin..', '❤️', '2025-06-21 19:32:09'),
(4, 'customer', 1, 'admin', NULL, 'Hello Admin', '❤️', '2025-06-22 09:04:05');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `payment_id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `worker_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` enum('Cash','Card','Online','Bank Transfer') DEFAULT 'Cash',
  `payment_status` enum('Pending','Paid','Unpaid') DEFAULT 'Pending',
  `payment_date` datetime DEFAULT current_timestamp(),
  `remarks` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`payment_id`, `booking_id`, `customer_id`, `worker_id`, `amount`, `payment_method`, `payment_status`, `payment_date`, `remarks`) VALUES
(1, 6, 1, 1, 2000.00, 'Cash', 'Paid', '2025-06-22 07:31:28', 'Invoice sent by admin');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `review_id` int(11) NOT NULL,
  `worker_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `rating` decimal(2,1) NOT NULL CHECK (`rating` >= 0 and `rating` <= 5),
  `review_text` text DEFAULT NULL,
  `review_date` datetime DEFAULT current_timestamp(),
  `reply` text DEFAULT NULL,
  `customer_reply` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`review_id`, `worker_id`, `customer_id`, `rating`, `review_text`, `review_date`, `reply`, `customer_reply`) VALUES
(0, 1, 1, 4.0, 'Very Good Work... Satisfied with the service..🤗❤️', '2025-06-21 07:16:37', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `review_replies`
--

CREATE TABLE `review_replies` (
  `reply_id` int(11) NOT NULL,
  `review_id` int(11) NOT NULL,
  `worker_id` int(11) NOT NULL,
  `reply_text` text NOT NULL,
  `reply_date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `review_replies`
--

INSERT INTO `review_replies` (`reply_id`, `review_id`, `worker_id`, `reply_text`, `reply_date`) VALUES
(2, 0, 1, 'Thank you very much...👍', '2025-06-21 07:17:10');

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE `services` (
  `service_id` int(11) NOT NULL,
  `service_name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `service_rate` varchar(100) DEFAULT NULL,
  `service_duration` varchar(100) DEFAULT NULL,
  `service_rules` text DEFAULT NULL,
  `icon_class` varchar(255) DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`service_id`, `service_name`, `description`, `price`, `service_rate`, `service_duration`, `service_rules`, `icon_class`, `active`) VALUES
(1, 'Electrician', 'Handle wiring, lighting, and electrical repairs.', 50.00, 'Per Visit', '2 Hours', 'Ensure access to the main electrical panel.', 'fa-solid fa-bolt', 1),
(2, 'Plumber', 'Fix leaks, install or repair plumbing systems.', 40.00, 'Per Visit', '1.5 Hours', 'Turn off water supply before service.', 'fa-solid fa-wrench', 1),
(3, 'Carpenter', 'Woodwork repairs, fittings, and installations.', 45.00, 'Per Hour', '2 Hours', 'Ensure access to woodwork areas.', 'fa-solid fa-hammer', 1),
(4, 'Painter', 'Interior and exterior painting services.', 35.00, 'Per Hour', '3 Hours', 'Cover furniture before painting.', 'fa-solid fa-paint-roller', 1),
(5, 'Cleaner', 'Home and office cleaning services.', 30.00, 'Per Hour', '2 Hours', 'Remove fragile items beforehand.', 'fa-solid fa-broom', 1),
(6, 'AC Technician', 'Repair and maintenance of air conditioners.', 60.00, 'Per Visit', '1 Hour', 'Turn off AC units before inspection.', 'fa-solid fa-snowflake', 1),
(7, 'Pest Control Technician', 'Pest inspection and extermination services.', 70.00, 'Per Visit', '2 Hours', 'Evacuate children and pets during service.', 'fa-solid fa-bug', 1),
(8, 'Mover', 'Packing and relocation services.', 80.00, 'Per Job', '4 Hours', 'Label all boxes clearly.', 'fa-solid fa-truck-moving', 1),
(9, 'Gardener', 'Garden maintenance, planting, and trimming.', 25.00, 'Per Hour', '2 Hours', 'Clear garden access paths.', 'fa-solid fa-seedling', 1),
(10, 'Security Technician', 'Installation and repair of CCTV and alarm systems.', 65.00, 'Per Visit', '2 Hours', 'Provide network and power access.', 'fa-solid fa-shield-halved', 1);

-- --------------------------------------------------------

--
-- Table structure for table `workers`
--

CREATE TABLE `workers` (
  `worker_id` int(11) NOT NULL,
  `fullname` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `address` varchar(255) NOT NULL,
  `skill` varchar(50) NOT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `status` enum('Pending','Approved','Rejected') DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `availability` varchar(100) DEFAULT NULL,
  `working_hours` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `workers`
--

INSERT INTO `workers` (`worker_id`, `fullname`, `email`, `phone`, `address`, `skill`, `profile_picture`, `password`, `status`, `created_at`, `availability`, `working_hours`) VALUES
(1, 'Mohamed Mufeedh', 'mhdmf12@gmil.com', '0755633537', 'Yalipadunugama, Mahawala Road, Ratnapura.', 'Electrician', 'Image/1750482819_Wk_4.jpg', '$2y$10$C1BnfnlAftKTJuA.tSXPcOzgW4L5aDmfaV1G3WiiKEoMHzO2.BGl2', 'Approved', '2025-06-21 05:11:32', 'Mon - Sat', '09:00 a.m to 05:00 p.m'),
(2, 'Madura Suresh', 'madura90@gmail.com', '0765657123', 'Mahawala Road, Ratnapura.', 'Carpenter', 'Image/1750746665_Wk_2.jpg', '$2y$10$M3DGVi9vunAEPALQstGBjuBUQGIP7MOM079cCA8aQsf3g7VRMFAY2', 'Approved', '2025-06-24 06:27:27', NULL, NULL),
(3, 'Mohamed Shezan', 'shezan12@gmail.com', '0767654321', 'Eheliyagoda, Ratnapura.', 'Plumber', 'Image/1750746695_wk_1.jpg', '$2y$10$m1VR34h402kqjl88tLhhveimdHtCPEiz9/4.4ONNoKcpsXxVCcxZ.', 'Approved', '2025-06-24 06:28:43', NULL, NULL),
(4, 'Abdul Rahman', 'abdul12@gmail.com', '0765645321', 'Kuruwita, Ratnapura', 'AC Technician', 'Image/1750746735_Wk_3.jpg', '$2y$10$T6G6vZGKQqMjnueLHHzo7eR2L9aZcPFRbWVrMVch36qruJvX94bTa', 'Approved', '2025-06-24 06:29:48', NULL, NULL),
(5, 'Mohamed Aarif', 'aarif12@gmail.com', '0742375431', 'Awissawella, Ratnapura.', 'Security Technician', NULL, '$2y$10$rZSSTJvubjWEQU9UfANY1.STNucGB1crruN324yu6GmTMeP6EMwCS', 'Pending', '2025-06-24 06:34:11', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `worker_proofs`
--

CREATE TABLE `worker_proofs` (
  `id` int(11) NOT NULL,
  `worker_id` int(11) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `uploaded_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `worker_proofs`
--

INSERT INTO `worker_proofs` (`id`, `worker_id`, `image_path`, `uploaded_at`) VALUES
(1, 1, 'uploads/proofs/1750508829_affordable.jpg', '2025-06-21 05:27:09'),
(2, 1, 'uploads/proofs/1750729807_bg_4.jpg', '2025-06-23 18:50:07');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`booking_id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `worker_id` (`worker_id`),
  ADD KEY `service_id` (`service_id`);

--
-- Indexes for table `contacts`
--
ALTER TABLE `contacts`
  ADD PRIMARY KEY (`contact_id`);

--
-- Indexes for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`customer_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `faqs`
--
ALTER TABLE `faqs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `fk_payments_booking` (`booking_id`),
  ADD KEY `fk_payments_customer` (`customer_id`),
  ADD KEY `fk_payments_worker` (`worker_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`review_id`),
  ADD KEY `worker_id` (`worker_id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Indexes for table `review_replies`
--
ALTER TABLE `review_replies`
  ADD PRIMARY KEY (`reply_id`),
  ADD KEY `review_id` (`review_id`),
  ADD KEY `worker_id` (`worker_id`);

--
-- Indexes for table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`service_id`);

--
-- Indexes for table `workers`
--
ALTER TABLE `workers`
  ADD PRIMARY KEY (`worker_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `worker_proofs`
--
ALTER TABLE `worker_proofs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_worker` (`worker_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `booking_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `contacts`
--
ALTER TABLE `contacts`
  MODIFY `contact_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `customer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `faqs`
--
ALTER TABLE `faqs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `review_replies`
--
ALTER TABLE `review_replies`
  MODIFY `reply_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
  MODIFY `service_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `workers`
--
ALTER TABLE `workers`
  MODIFY `worker_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `worker_proofs`
--
ALTER TABLE `worker_proofs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `fk_booking_customer` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_booking_service` FOREIGN KEY (`service_id`) REFERENCES `services` (`service_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_booking_worker` FOREIGN KEY (`worker_id`) REFERENCES `workers` (`worker_id`) ON DELETE SET NULL;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `fk_payments_booking` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`booking_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_payments_customer` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_payments_worker` FOREIGN KEY (`worker_id`) REFERENCES `workers` (`worker_id`) ON DELETE CASCADE;

--
-- Constraints for table `review_replies`
--
ALTER TABLE `review_replies`
  ADD CONSTRAINT `review_replies_ibfk_1` FOREIGN KEY (`review_id`) REFERENCES `reviews` (`review_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `review_replies_ibfk_2` FOREIGN KEY (`worker_id`) REFERENCES `workers` (`worker_id`) ON DELETE CASCADE;

--
-- Constraints for table `worker_proofs`
--
ALTER TABLE `worker_proofs`
  ADD CONSTRAINT `fk_worker` FOREIGN KEY (`worker_id`) REFERENCES `workers` (`worker_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
