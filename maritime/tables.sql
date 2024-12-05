-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               8.0.30 - MySQL Community Server - GPL
-- Server OS:                    Win64
-- HeidiSQL Version:             12.1.0.6537
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Dumping database structure for maritime
CREATE DATABASE IF NOT EXISTS `maritime` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `maritime`;

-- Dumping structure for table maritime.admin
CREATE TABLE IF NOT EXISTS `admin` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table maritime.admin: ~0 rows (approximately)
INSERT INTO `admin` (`id`, `username`, `email`, `password`) VALUES
	(1, 'admin', 'adminofmaritime@gmail.com', 'adminmaritime1');

-- Dumping structure for table maritime.broadcasts
CREATE TABLE IF NOT EXISTS `broadcasts` (
  `id` int NOT NULL AUTO_INCREMENT,
  `message` text NOT NULL,
  `type` enum('emergency','safety','weather','navigation','general') NOT NULL,
  `priority` enum('high','medium','low') NOT NULL,
  `target_audience` enum('all_vessels','specific_vessels','authorities','emergency_teams') NOT NULL,
  `sent_by` int DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `sent_by` (`sent_by`),
  CONSTRAINT `broadcasts_ibfk_1` FOREIGN KEY (`sent_by`) REFERENCES `admin` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table maritime.broadcasts: ~0 rows (approximately)

-- Dumping structure for table maritime.broadcast_recipients
CREATE TABLE IF NOT EXISTS `broadcast_recipients` (
  `id` int NOT NULL AUTO_INCREMENT,
  `broadcast_id` int DEFAULT NULL,
  `recipient_id` int DEFAULT NULL,
  `recipient_type` enum('vessel','user','team') NOT NULL,
  `read_status` tinyint(1) DEFAULT '0',
  `read_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `broadcast_id` (`broadcast_id`),
  CONSTRAINT `broadcast_recipients_ibfk_1` FOREIGN KEY (`broadcast_id`) REFERENCES `broadcasts` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table maritime.broadcast_recipients: ~0 rows (approximately)

-- Dumping structure for table maritime.incidents
CREATE TABLE IF NOT EXISTS `incidents` (
  `incident_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `vessel_id` int DEFAULT NULL,
  `incident_type` varchar(100) DEFAULT NULL,
  `severity_level` enum('low','medium','high','critical') DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `description` text,
  `date_time` datetime DEFAULT NULL,
  `status` enum('reported','investigating','resolved','closed') DEFAULT NULL,
  `attachments` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`incident_id`),
  KEY `user_id` (`user_id`),
  KEY `vessel_id` (`vessel_id`),
  CONSTRAINT `incidents_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  CONSTRAINT `incidents_ibfk_2` FOREIGN KEY (`vessel_id`) REFERENCES `vessels` (`vessel_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table maritime.incidents: ~0 rows (approximately)
INSERT INTO `incidents` (`incident_id`, `user_id`, `vessel_id`, `incident_type`, `severity_level`, `location`, `description`, `date_time`, `status`, `attachments`, `created_at`, `updated_at`) VALUES
	(1, 1, 2, 'fire', 'medium', 'salt bae lake ', 'fire broke out ', '2024-11-21 20:43:00', 'resolved', '["../uploads/incidents/1732286642_IMG_20210130_194938.jpg"]', '2024-11-22 14:44:02', '2024-11-22 14:49:17');

-- Dumping structure for table maritime.incident_assignments
CREATE TABLE IF NOT EXISTS `incident_assignments` (
  `assignment_id` int NOT NULL AUTO_INCREMENT,
  `incident_id` int DEFAULT NULL,
  `team_id` int DEFAULT NULL,
  `assigned_at` datetime DEFAULT NULL,
  `status` enum('assigned','responding','completed') DEFAULT 'assigned',
  `notes` text,
  PRIMARY KEY (`assignment_id`),
  KEY `incident_id` (`incident_id`),
  KEY `team_id` (`team_id`),
  CONSTRAINT `incident_assignments_ibfk_1` FOREIGN KEY (`incident_id`) REFERENCES `incidents` (`incident_id`),
  CONSTRAINT `incident_assignments_ibfk_2` FOREIGN KEY (`team_id`) REFERENCES `response_teams` (`team_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table maritime.incident_assignments: ~0 rows (approximately)
INSERT INTO `incident_assignments` (`assignment_id`, `incident_id`, `team_id`, `assigned_at`, `status`, `notes`) VALUES
	(1, 1, 1, '2024-11-22 15:44:32', 'assigned', NULL);

-- Dumping structure for table maritime.response_teams
CREATE TABLE IF NOT EXISTS `response_teams` (
  `team_id` int NOT NULL AUTO_INCREMENT,
  `team_name` varchar(100) NOT NULL,
  `team_type` enum('fire_service','maritime_police','first_responders','environmental','medical') NOT NULL,
  `contact_info` varchar(255) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`team_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table maritime.response_teams: ~5 rows (approximately)
INSERT INTO `response_teams` (`team_id`, `team_name`, `team_type`, `contact_info`, `status`, `created_at`) VALUES
	(1, 'Fire Service Team', 'fire_service', NULL, 'active', '2024-11-22 14:41:09'),
	(2, 'Maritime Police Unit', 'maritime_police', NULL, 'active', '2024-11-22 14:41:09'),
	(3, 'First Responders Team', 'first_responders', NULL, 'active', '2024-11-22 14:41:09'),
	(4, 'Environmental Response Team', 'environmental', NULL, 'active', '2024-11-22 14:41:09'),
	(5, 'Medical Emergency Team', 'medical', NULL, 'active', '2024-11-22 14:41:09');

-- Dumping structure for table maritime.users
CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int NOT NULL AUTO_INCREMENT,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `gender` enum('male','female','other') NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `status` enum('active','disabled') NOT NULL DEFAULT 'active',
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table maritime.users: ~1 rows (approximately)
INSERT INTO `users` (`user_id`, `full_name`, `email`, `username`, `password_hash`, `gender`, `created_at`, `updated_at`, `status`) VALUES
	(1, 'Jay Jay', 'jay@gmail.com', 'jaymarine', '$2y$10$L71UfQeSdyxJTJI/Cw6ou.k/xftRMHJrO7KVt8YOgFJcq147ruztG', 'male', '2024-11-14 12:45:24', '2024-11-22 15:19:08', 'active'),
	(2, 'Seritime John', 'Seritime@gmail.com', 'seritime', '$2y$10$DeH0YVyssVxvKoJoSb9Vw.h3drflg4tLXm4JFrA/CNG0XzhJKcUYu', 'male', '2024-12-05 06:53:46', '2024-12-05 06:53:46', 'active');

-- Dumping structure for table maritime.vessels
CREATE TABLE IF NOT EXISTS `vessels` (
  `vessel_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `vessel_name` varchar(100) NOT NULL,
  `imo_number` varchar(20) NOT NULL,
  `vessel_type` varchar(50) NOT NULL,
  `flag_state` varchar(50) NOT NULL,
  `gross_tonnage` decimal(10,2) DEFAULT NULL,
  `year_built` int DEFAULT NULL,
  `classification_society` varchar(100) DEFAULT NULL,
  `registration_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `last_inspection_date` datetime DEFAULT NULL,
  `status` enum('active','in_port','maintenance','inactive','suspended') DEFAULT 'active',
  `location` enum('at_sea','in_port') DEFAULT 'in_port',
  `departing_from` varchar(100) DEFAULT NULL,
  `arriving_at` varchar(100) DEFAULT NULL,
  `estimated_arrival` datetime DEFAULT NULL,
  `departure_time` datetime DEFAULT NULL,
  `approval_status` enum('pending','approved','rejected') DEFAULT 'pending',
  `approval_date` datetime DEFAULT NULL,
  `approval_notes` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`vessel_id`),
  UNIQUE KEY `imo_number` (`imo_number`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `vessels_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table maritime.vessels: ~2 rows (approximately)
INSERT INTO `vessels` (`vessel_id`, `user_id`, `vessel_name`, `imo_number`, `vessel_type`, `flag_state`, `gross_tonnage`, `year_built`, `classification_society`, `registration_date`, `last_inspection_date`, `status`, `location`, `departing_from`, `arriving_at`, `estimated_arrival`, `departure_time`, `approval_status`, `approval_date`, `approval_notes`) VALUES
	(1, 1, 'Maskorov Voyager', '3853458qwe', 'Passenger', 'Singapore', 85000.00, 1995, 'American Bureau of Shipping', '2024-11-17 19:45:23', '2024-11-22 15:27:31', 'active', 'at_sea', 'Russia', 'Taiwan', '2024-11-30 12:26:00', '2024-11-18 12:26:00', 'approved', '2024-11-22 14:53:56', 'alright good to go'),
	(2, 1, 'Sardine Hunter', '000034', 'Fishing', 'Phillipines', 5000.00, 2013, 'Chinese Ships ', '2024-11-18 11:46:41', '2024-11-22 16:09:53', 'active', 'in_port', 'not set', 'not set', '2024-11-18 12:27:00', '2024-11-18 12:27:00', 'approved', '2024-11-22 15:08:09', 'so far the crew are respecting themselves well'),
	(3, 2, 'Dong Feng ', '30993018', 'Tanker', 'China', 20000.00, 2000, 'Chinese Ships ', '2024-12-05 07:14:46', NULL, 'in_port', 'in_port', 'China', 'Taiwan', '2024-12-25 08:07:00', '2024-12-16 08:07:00', 'pending', NULL, NULL);

-- Dumping structure for table maritime.vessel_compliance
CREATE TABLE IF NOT EXISTS `vessel_compliance` (
  `compliance_id` int NOT NULL AUTO_INCREMENT,
  `vessel_id` int DEFAULT NULL,
  `user_id` int DEFAULT NULL,
  `safety_equipment_check` tinyint(1) DEFAULT '0',
  `navigation_systems_check` tinyint(1) DEFAULT '0',
  `crew_certification_check` tinyint(1) DEFAULT '0',
  `environmental_compliance` tinyint(1) DEFAULT '0',
  `hull_integrity_check` tinyint(1) DEFAULT '0',
  `firefighting_equipment` tinyint(1) DEFAULT '0',
  `medical_supplies_check` tinyint(1) DEFAULT '0',
  `radio_equipment_check` tinyint(1) DEFAULT '0',
  `waste_management_check` tinyint(1) DEFAULT '0',
  `security_systems_check` tinyint(1) DEFAULT '0',
  `last_checked` datetime DEFAULT NULL,
  `next_check_due` datetime DEFAULT NULL,
  `compliance_status` enum('compliant','warning','non_compliant') DEFAULT 'non_compliant',
  `warning_message` text,
  `admin_notes` text,
  `override_by` int DEFAULT NULL,
  `override_date` datetime DEFAULT NULL,
  `deadline_date` date DEFAULT NULL,
  PRIMARY KEY (`compliance_id`),
  KEY `vessel_id` (`vessel_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `vessel_compliance_ibfk_1` FOREIGN KEY (`vessel_id`) REFERENCES `vessels` (`vessel_id`),
  CONSTRAINT `vessel_compliance_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table maritime.vessel_compliance: ~1 rows (approximately)
INSERT INTO `vessel_compliance` (`compliance_id`, `vessel_id`, `user_id`, `safety_equipment_check`, `navigation_systems_check`, `crew_certification_check`, `environmental_compliance`, `hull_integrity_check`, `firefighting_equipment`, `medical_supplies_check`, `radio_equipment_check`, `waste_management_check`, `security_systems_check`, `last_checked`, `next_check_due`, `compliance_status`, `warning_message`, `admin_notes`, `override_by`, `override_date`, `deadline_date`) VALUES
	(1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, '2024-11-22 16:20:24', '2025-02-22 16:20:24', 'warning', NULL, 'vdfbvdfbvfs', NULL, '2024-11-22 16:28:06', NULL),
	(2, 2, 1, 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, '2024-11-22 16:24:47', '2025-02-22 16:24:47', 'non_compliant', NULL, NULL, NULL, NULL, NULL);

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
