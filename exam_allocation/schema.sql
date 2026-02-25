-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               8.4.3 - MySQL Community Server - GPL
-- Server OS:                    Win64
-- HeidiSQL Version:             12.8.0.6908
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Dumping database structure for exam
CREATE DATABASE IF NOT EXISTS `exam` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `exam`;

-- Dumping structure for table exam.appearing_list
CREATE TABLE IF NOT EXISTS `appearing_list` (
  `alid` int NOT NULL AUTO_INCREMENT,
  `eid` int NOT NULL,
  `student` varchar(50) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `branch` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `ccode` varchar(50) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `edate` varchar(50) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `session` varchar(50) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`alid`),
  KEY `FK_appearing_list_exam_definition` (`eid`),
  CONSTRAINT `FK_appearing_list_exam_definition` FOREIGN KEY (`eid`) REFERENCES `exam_definition` (`eid`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7599 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table exam.courses
CREATE TABLE IF NOT EXISTS `courses` (
  `cid` int NOT NULL AUTO_INCREMENT,
  `ccode` varchar(50) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '0',
  `cname` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '0',
  `is_elective` int NOT NULL DEFAULT (0),
  `sem` int NOT NULL DEFAULT (0),
  `branch` varchar(50) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '0',
  PRIMARY KEY (`cid`)
) ENGINE=InnoDB AUTO_INCREMENT=811 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table exam.exam_definition
CREATE TABLE IF NOT EXISTS `exam_definition` (
  `eid` int NOT NULL AUTO_INCREMENT,
  `ename` varchar(50) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '0',
  `sdate` varchar(50) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '0000-00-00',
  `edate` varchar(50) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '0000-00-00',
  `etype` int NOT NULL DEFAULT (0),
  PRIMARY KEY (`eid`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table exam.exam_time_table
CREATE TABLE IF NOT EXISTS `exam_time_table` (
  `ttid` int NOT NULL AUTO_INCREMENT,
  `eid` int NOT NULL DEFAULT '0',
  `edate` varchar(50) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '0',
  `session` varchar(50) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '0',
  `ccode` varchar(50) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '0',
  `branch` varchar(50) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '0',
  `sem` varchar(50) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '0',
  PRIMARY KEY (`ttid`)
) ENGINE=InnoDB AUTO_INCREMENT=7959 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table exam.rooms
CREATE TABLE IF NOT EXISTS `rooms` (
  `Rid` int NOT NULL AUTO_INCREMENT,
  `Block` varchar(255) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '0',
  `Room_no` varchar(255) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '0',
  `Capacity` int NOT NULL DEFAULT (0),
  `Type` varchar(50) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '0',
  PRIMARY KEY (`Rid`),
  UNIQUE KEY `Index 2` (`Room_no`)
) ENGINE=InnoDB AUTO_INCREMENT=138 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table exam.seating_allocation_data
CREATE TABLE IF NOT EXISTS `seating_allocation_data` (
  `seatingId` int NOT NULL AUTO_INCREMENT,
  `aid` int NOT NULL,
  `reg_no` varchar(50) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `room` varchar(50) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '0',
  `edate` varchar(50) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '0',
  `session` varchar(50) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '0',
  `electiveCourseId` varchar(50) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '0',
  `seat` varchar(50) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '0',
  PRIMARY KEY (`seatingId`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=59987 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table exam.seating_allocation_definition
CREATE TABLE IF NOT EXISTS `seating_allocation_definition` (
  `aid` int NOT NULL AUTO_INCREMENT,
  `eid` int NOT NULL DEFAULT '0',
  `created_at` varchar(50) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '0',
  PRIMARY KEY (`aid`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table exam.students
CREATE TABLE IF NOT EXISTS `students` (
  `student_id` int NOT NULL AUTO_INCREMENT,
  `reg_no` varchar(50) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '0',
  `rollno` int NOT NULL DEFAULT (0),
  `name` varchar(50) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '0',
  `branch` varchar(50) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '0',
  `semester` int NOT NULL DEFAULT (0),
  `elective_1` varchar(50) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '0',
  `elective_2` varchar(50) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '0',
  `elective_3` varchar(50) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '0',
  `minor` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`student_id`),
  UNIQUE KEY `Index 2` (`reg_no`)
) ENGINE=InnoDB AUTO_INCREMENT=4149 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table exam.users
CREATE TABLE IF NOT EXISTS `users` (
  `uid` int NOT NULL AUTO_INCREMENT,
  `username` varchar(255) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '0',
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '0',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
