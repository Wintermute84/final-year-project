-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 15, 2025 at 06:44 AM
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
-- Database: `exam`
--

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `cid` int(11) NOT NULL,
  `ccode` varchar(250) NOT NULL,
  `cname` varchar(250) NOT NULL,
  `is_elective` int(11) NOT NULL,
  `sem` int(11) NOT NULL,
  `branch` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`cid`, `ccode`, `cname`, `is_elective`, `sem`, `branch`) VALUES
(1, 'MAT101', 'Linear Algebra and Calculus', 0, 1, 'CE'),
(2, 'MAT101', 'Linear Algebra and Calculus', 0, 1, 'CSE'),
(3, 'MAT101', 'Linear Algebra and Calculus', 0, 1, 'CSE AI'),
(4, 'MAT101', 'Linear Algebra and Calculus', 0, 1, 'AIDS'),
(5, 'MAT101', 'Linear Algebra and Calculus', 0, 1, 'ECE'),
(6, 'MAT101', 'Linear Algebra and Calculus', 0, 1, 'EEE'),
(7, 'MAT101', 'Linear Algebra and Calculus', 0, 1, 'ME'),
(8, 'EST130', 'Basics of Electrical and Electronics Engineering', 0, 1, 'CE'),
(9, 'EST130', 'Basics of Electrical and Electronics Engineering', 0, 1, 'CSE'),
(10, 'EST120', 'Basics of Civil and Mechanical Engineering', 0, 1, 'CSE AI'),
(11, 'EST120', 'Basics of Civil and Mechanical Engineering', 0, 1, 'AIDS'),
(12, 'EST120', 'Basics of Civil and Mechanical Engineering', 0, 1, 'ECE'),
(13, 'EST120', 'Basics of Civil and Mechanical Engineering', 0, 1, 'EEE'),
(14, 'EST130', 'Basics of Electrical and Electronics Engineering', 0, 1, 'ME'),
(15, 'PHT110', 'Engineering Physics', 0, 1, 'CE'),
(16, 'PHT100', 'Engineering Physics', 0, 1, 'CSE'),
(17, 'CYT100', 'Engineering Chemistry', 0, 1, 'CSE AI'),
(18, 'CYT100', 'Engineering Chemistry', 0, 1, 'AIDS'),
(19, 'CYT100', 'Engineering Chemistry', 0, 1, 'ECE'),
(20, 'CYT100', 'Engineering Chemistry', 0, 1, 'EEE'),
(21, 'PHT110', 'Engineering Physics', 0, 1, 'ME'),
(22, 'EST100', 'Engineering Mechanics', 0, 1, 'CE'),
(23, 'EST100', 'Engineering Mechanics', 0, 1, 'CSE'),
(24, 'EST110', 'Engineering Graphics', 0, 1, 'CSE AI'),
(25, 'EST110', 'Engineering Graphics', 0, 1, 'AIDS'),
(26, 'EST110', 'Engineering Graphics', 0, 1, 'ECE'),
(27, 'EST110', 'Engineering Graphics', 0, 1, 'EEE'),
(28, 'EST100', 'Engineering Mechanics', 0, 1, 'ME'),
(118, 'CET201', 'Mechanics of Solids', 0, 3, 'CE'),
(119, 'CST201', 'Data Structures', 0, 3, 'CSE'),
(120, 'CST201', 'Data Structures', 0, 3, 'CSE AI'),
(121, 'CST201', 'Data Structures', 0, 3, 'AIDS'),
(122, 'CST201', 'Data Structures', 0, 3, 'CY'),
(123, 'ECT205', 'Network Theory', 0, 3, 'ECE'),
(124, 'EET201', 'Circuits and Networks', 0, 3, 'EEE'),
(125, 'MET203', 'Mechanics of Fluids', 0, 3, 'ME'),
(126, 'MCN201', 'Sustainable Engineering', 0, 3, 'CE'),
(127, 'MCN201', 'Sustainable Engineering', 0, 3, 'CSE'),
(128, 'MCN201', 'Sustainable Engineering', 0, 3, 'CSE AI'),
(129, 'MCN201', 'Sustainable Engineering', 0, 3, 'AIDS'),
(130, 'MCN201', 'Sustainable Engineering', 0, 3, 'CY'),
(131, 'MCN201', 'Sustainable Engineering', 0, 3, 'ECE'),
(132, 'MCN201', 'Sustainable Engineering', 0, 3, 'EEE'),
(133, 'MCN201', 'Sustainable Engineering', 0, 3, 'ME'),
(134, 'MAT201', 'Partial Differential Equations and Complex Analysis', 0, 3, 'CE'),
(135, 'MAT203', 'Discrete Mathematical Structures', 0, 3, 'CSE'),
(136, 'MAT203', 'Discrete Mathematical Structures', 0, 3, 'CSE AI'),
(137, 'MAT203', 'Discrete Mathematical Structures', 0, 3, 'AIDS'),
(138, 'MAT203', 'Discrete Mathematical Structures', 0, 3, 'CY'),
(139, 'MAT201', 'Partial Differential Equations and Complex Analysis', 0, 3, 'ECE'),
(140, 'MAT201', 'Partial Differential Equations and Complex Analysis', 0, 3, 'EEE'),
(141, 'MAT201', 'Partial Differential Equations and Complex Analysis', 0, 3, 'ME'),
(142, 'CET205', 'Surveying and Geomatics', 0, 3, 'CE'),
(143, 'CST203', 'Logic System Design', 0, 3, 'CSE'),
(144, 'CST203', 'Logic System Design', 0, 3, 'CSE AI'),
(145, 'CST203', 'Logic System Design', 0, 3, 'AIDS'),
(146, 'CST203', 'Logic System Design', 0, 3, 'CY'),
(147, 'ECT203', 'Logic Circuit Design', 0, 3, 'ECE'),
(148, 'EET203', 'Measurements and Instrumentation', 0, 3, 'EEE'),
(149, 'MET201', 'Mechanics of Solids', 0, 3, 'ME'),
(150, 'CET203', 'Fluid Mechanics and Hydraulics', 0, 3, 'CE'),
(151, 'CST205', 'Object Oriented Programming using Java', 0, 3, 'CSE'),
(152, 'CST205', 'Object Oriented Programming using Java', 0, 3, 'CSE AI'),
(153, 'CST205', 'Object Oriented Programming using Java', 0, 3, 'AIDS'),
(154, 'CST205', 'Object Oriented Programming using Java', 0, 3, 'CY'),
(155, 'ECT201', 'Solid State Devices', 0, 3, 'ECE'),
(156, 'EET205', 'Analog Electronics', 0, 3, 'EEE'),
(157, 'MET205', 'Metallurgy & Material Science', 0, 3, 'ME'),
(158, 'EST200', 'Design and Engineering', 0, 3, 'CE'),
(159, 'HUT200', 'Professional Ethics', 0, 3, 'CSE'),
(160, 'EST200', 'Design and Engineering', 0, 3, 'CSE AI'),
(161, 'EST200', 'Design and Engineering', 0, 3, 'AIDS'),
(162, 'HUT200', 'Professional Ethics', 0, 3, 'CY'),
(163, 'EST200', 'Design and Engineering', 0, 3, 'ECE'),
(164, 'EST200', 'Design and Engineering', 0, 3, 'EEE'),
(165, 'HUT200', 'Professional Ethics', 0, 3, 'ME'),
(166, 'MET401', 'Design of Machine Elements', 0, 7, 'ME'),
(167, 'CET401', 'Design of teel tructures', 0, 7, 'CE'),
(168, 'ECT413', 'Optical Fibre Communication', 0, 7, 'ECE'),
(169, 'ECT463', 'Machine Learning', 0, 7, 'ECE'),
(170, 'EET401', 'Advanced Control ystems', 0, 7, 'EEE'),
(171, 'CT463', 'Web Programming', 0, 7, 'CE A'),
(172, 'CT473', 'Natural Language Processing', 0, 7, 'CE A'),
(173, 'CT433', 'ecurity in Computing', 0, 7, 'CE A'),
(174, 'CT413', 'Machine Learning', 0, 7, 'CE A'),
(175, 'CT463', 'Web Programming', 0, 7, 'CE AI'),
(176, 'CT473', 'Natural Language Processing', 0, 7, 'CE AI'),
(177, 'CT433', 'ecurity in Computing', 0, 7, 'CE AI'),
(178, 'CT423', 'Cloud Computing', 0, 7, 'CE AI'),
(179, 'CT463', 'Web Programming', 0, 7, 'AID'),
(180, 'CT473', 'Natural Language Processing', 0, 7, 'AID'),
(181, 'CT423', 'Cloud Computing', 0, 7, 'AID'),
(182, 'CT463', 'Web Programming', 0, 7, 'CY'),
(183, 'CT473', 'Natural Language Processing', 0, 7, 'CY'),
(184, 'CCT433', 'Cloud Computing and ecurity', 0, 7, 'CY'),
(185, 'CT413', 'Machine Learning', 0, 7, 'CY'),
(186, 'CET415', 'Environmental Impact Assessment', 0, 7, 'ME'),
(187, 'CT435', 'Computer Graphics', 0, 7, 'ME'),
(188, 'CT445', 'Python for Engineers', 0, 7, 'ME'),
(189, 'ECT445', 'IoT and Applications', 0, 7, 'ME'),
(190, 'EET435', 'Renewable Energy ystems', 0, 7, 'ME'),
(191, 'EET455', 'Energy Management', 0, 7, 'ME'),
(192, 'MET425', 'Quantitative Techniques for Engineers', 0, 7, 'CE'),
(193, 'EET455', 'Energy Management', 0, 7, 'CE'),
(194, 'EET435', 'Renewable Energy ystems', 0, 7, 'CE'),
(195, 'ECT445', 'IoT and Applications', 0, 7, 'CE'),
(196, 'MET445', 'Renewable Energy Engineering', 0, 7, 'CE'),
(197, 'MET455', 'Quality Engineering and Management', 0, 7, 'CE'),
(198, 'EET435', 'Renewable Energy ystems', 0, 7, 'ECE'),
(199, 'CT435', 'Computer Graphics', 0, 7, 'ECE'),
(200, 'CET415', 'Environmental Impact Assessment', 0, 7, 'ECE'),
(201, 'EET455', 'Energy Management', 0, 7, 'ECE'),
(202, 'MET425', 'Quantitative Techniques for Engineers', 0, 7, 'ECE'),
(203, 'MET445', 'Renewable Energy Engineering', 0, 7, 'ECE'),
(204, 'MET455', 'Quality Engineering and Management', 0, 7, 'ECE'),
(205, 'MET425', 'Quantitative Techniques for Engineers', 0, 7, 'EEE'),
(206, 'CET415', 'Environmental Impact Assessment', 0, 7, 'EEE'),
(207, 'ECT445', 'IoT and Applications', 0, 7, 'EEE'),
(208, 'CT435', 'Computer Graphics', 0, 7, 'EEE'),
(209, 'MET445', 'Renewable Energy Engineering', 0, 7, 'EEE'),
(210, 'MET455', 'Quality Engineering and Management', 0, 7, 'EEE'),
(211, 'CET415', 'Environmental Impact Assessment', 0, 7, 'CE A'),
(212, 'ECT445', 'IoT and Applications', 0, 7, 'CE A'),
(213, 'EET435', 'Renewable Energy ystems', 0, 7, 'CE A'),
(214, 'EET455', 'Energy Management', 0, 7, 'CE A'),
(215, 'MET425', 'Quantitative Techniques for Engineers', 0, 7, 'CE A'),
(216, 'MET445', 'Renewable Energy Engineering', 0, 7, 'CE A'),
(217, 'MET455', 'Quality Engineering and Management', 0, 7, 'CE A'),
(218, 'EET435', 'Renewable Energy ystems', 0, 7, 'CE AI'),
(219, 'EET455', 'Energy Management', 0, 7, 'CE AI'),
(220, 'MET425', 'Quantitative Techniques for Engineers', 0, 7, 'CE AI'),
(221, 'MET455', 'Quality Engineering and Management', 0, 7, 'CE AI'),
(222, 'CET415', 'Environmental Impact Assessment', 0, 7, 'CE AI'),
(223, 'MET445', 'Renewable Energy Engineering', 0, 7, 'CE AI'),
(224, 'EET435', 'Renewable Energy ystems', 0, 7, 'AID'),
(225, 'EET455', 'Energy Management', 0, 7, 'AID'),
(226, 'MET425', 'Quantitative Techniques for Engineers', 0, 7, 'AID'),
(227, 'MET455', 'Quality Engineering and Management', 0, 7, 'AID'),
(228, 'CET415', 'Environmental Impact Assessment', 0, 7, 'AID'),
(229, 'ECT455', 'ECT455', 0, 7, 'AID'),
(230, 'MET445', 'Renewable Energy Engineering', 0, 7, 'AID'),
(231, 'EET435', 'Renewable Energy ystems', 0, 7, 'CY'),
(232, 'EET455', 'Energy Management', 0, 7, 'CY'),
(233, 'MET425', 'Quantitative Techniques for Engineers', 0, 7, 'CY'),
(234, 'MET455', 'Quality Engineering and Management', 0, 7, 'CY'),
(235, 'CET415', 'Environmental Impact Assessment', 0, 7, 'CY'),
(236, 'ECT445', 'IoT and Applications', 0, 7, 'CY'),
(237, 'MET445', 'Renewable Energy Engineering', 0, 7, 'CY'),
(238, 'MET413', 'Advanced Methods in Nondestructive Testing', 0, 7, 'ME'),
(239, 'CET423', 'Ground Improvement Techniques', 0, 7, 'CE'),
(240, 'CET453', 'Construction Planning and Management', 0, 7, 'CE'),
(241, 'ECT401', 'Microwave and Antennas', 0, 7, 'ECE'),
(242, 'EET463', 'Illumination Technology', 0, 7, 'EEE'),
(243, 'CT401', 'Artificial Intelligence', 0, 7, 'CE A'),
(244, 'AIT401', 'Foundations of Deep Learning', 0, 7, 'CE AI'),
(245, 'AIT401', 'Foundations of Deep Learning', 0, 7, 'AID'),
(246, 'CCT401', 'Ethical Hacking', 0, 7, 'CY'),
(247, 'MCN401', 'Industrial afety Engineering', 0, 7, 'ME'),
(248, 'MCN401', 'Industrial afety Engineering', 0, 7, 'CE'),
(249, 'MCN401', 'Industrial afety Engineering', 0, 7, 'ECE'),
(250, 'MCN401', 'Industrial afety Engineering', 0, 7, 'EEE'),
(251, 'MCN401', 'Industrial afety Engineering', 0, 7, 'CE A'),
(252, 'MCN401', 'Industrial afety Engineering', 0, 7, 'CE AI'),
(253, 'MCN401', 'Industrial afety Engineering', 0, 7, 'AID'),
(254, 'MCN401', 'Industrial afety Engineering', 0, 7, 'CY'),
(255, 'CET301', 'Structural Analysis I', 0, 5, 'CE'),
(256, 'CST301', 'Formal Languages and Automata Theory', 0, 5, 'CSE'),
(257, 'CST301', 'Formal Languages and Automata Theory', 0, 5, 'CY'),
(258, 'ADT301', 'Foundations of Data Science', 0, 5, 'CSE AI'),
(259, 'ADT301', 'Foundations of Data Science', 0, 5, 'AIDS'),
(260, 'ECT305', 'Analog and Digital Communication', 0, 5, 'ECE'),
(261, 'ET305', 'Signals and Systems', 0, 5, 'EEE'),
(262, 'MET303', 'MET303', 0, 5, 'ME'),
(263, 'CET307', 'Hydrology and Water Resources Engineering', 0, 5, 'CE'),
(264, 'CST307', 'Microprocessors and Microcontrollers', 0, 5, 'CSE'),
(265, 'CCT305', 'Systems and Network Security', 0, 5, 'CY'),
(266, 'AIT307', 'Introduction to Artificial Intelligence', 0, 5, 'CSE AI'),
(267, 'AIT307', 'Introduction to Artificial Intelligence', 0, 5, 'AIDS'),
(268, 'HUT300', 'Industrial Economics and Foreign Trade', 0, 5, 'ECE'),
(269, 'HUT300', 'Industrial Economics and Foreign Trade', 0, 5, 'EEE'),
(270, 'HUT300', 'Industrial Economics and Foreign Trade', 0, 5, 'ME'),
(271, 'CET303', 'Design of Concrete Structures', 0, 5, 'CE'),
(272, 'CST305', 'System Software', 0, 5, 'CSE'),
(273, 'CCT307', 'Applied Cryptography', 0, 5, 'CY'),
(274, 'AMT305', 'Introduction to Machine Learning', 0, 5, 'CSE AI'),
(275, 'AMT305', 'Introduction to Machine Learning', 0, 5, 'AIDS'),
(276, 'ECT307', 'Control Systems', 0, 5, 'ECE'),
(277, 'EET307', 'Synchronous and Induction Machines', 0, 5, 'EEE'),
(278, 'MET305', 'Industrial and Systems Engineering', 0, 5, 'ME'),
(279, 'MCN301', 'Disaster Management', 0, 5, 'CE'),
(280, 'MCN301', 'Disaster Management', 0, 5, 'CSE'),
(281, 'MCN301', 'Disaster Management', 0, 5, 'CY'),
(282, 'MCN301', 'Disaster Management', 0, 5, 'CSE AI'),
(283, 'MCN301', 'Disaster Management', 0, 5, 'AIDS'),
(284, 'MCN301', 'Disaster Management', 0, 5, 'ECE'),
(285, 'MCN301', 'Disaster Management', 0, 5, 'EEE'),
(286, 'MCN301', 'Disaster Management', 0, 5, 'ME'),
(287, 'CET305', 'Geotechnical Engineering II', 0, 5, 'CE'),
(288, 'CST303', 'Computer Networks', 0, 5, 'CSE'),
(289, 'CST303', 'Computer Networks', 0, 5, 'CY'),
(290, 'CST303', 'Computer Networks', 0, 5, 'CSE AI'),
(291, 'CST303', 'Computer Networks', 0, 5, 'AIDS'),
(292, 'ECT303', 'Digital Signal Processing', 0, 5, 'ECE'),
(293, 'EET301', 'Power Systems I', 0, 5, 'EEE'),
(294, 'MET301', 'Mechanics of Machinery', 0, 5, 'ME'),
(295, 'CET309', 'Construction Technology and Management', 0, 5, 'CE'),
(296, 'CST309', 'Management of Software Systems', 0, 5, 'CSE'),
(297, 'CST309', 'Management of Software Systems', 0, 5, 'CY'),
(298, 'CST309', 'Management of Software Systems', 0, 5, 'CSE AI'),
(299, 'CST309', 'Management of Software Systems', 0, 5, 'AIDS'),
(300, 'ECT301', 'Linear Integrated Circuits', 0, 5, 'ECE'),
(301, 'EET303', 'Microprocessors and Microcontrollers', 0, 5, 'EEE'),
(302, 'MET307', 'Machine Tools and Metrology', 0, 5, 'ME');

-- --------------------------------------------------------

--
-- Table structure for table `exam_definition`
--

CREATE TABLE `exam_definition` (
  `eid` int(11) NOT NULL,
  `ename` varchar(500) NOT NULL,
  `sdate` date NOT NULL,
  `edate` date NOT NULL,
  `etype` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `exam_definition`
--

INSERT INTO `exam_definition` (`eid`, `ename`, `sdate`, `edate`, `etype`) VALUES
(9, 'First Internal Examination, 2025', '2025-12-08', '2025-12-30', 1);

-- --------------------------------------------------------

--
-- Table structure for table `exam_time_table`
--

CREATE TABLE `exam_time_table` (
  `ttid` int(11) NOT NULL,
  `eid` int(11) NOT NULL,
  `edate` varchar(255) NOT NULL,
  `session` varchar(10) NOT NULL,
  `ccode` varchar(11) NOT NULL,
  `branch` varchar(255) NOT NULL,
  `sem` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `exam_time_table`
--

INSERT INTO `exam_time_table` (`ttid`, `eid`, `edate`, `session`, `ccode`, `branch`, `sem`) VALUES
(268, 9, '16-08-2025', 'FN', 'MET401', 'ME', 7),
(269, 9, '16-08-2025', 'FN', 'CET401', 'CE', 7),
(270, 9, '16-08-2025', 'FN', 'ECT413', 'ECE', 7),
(271, 9, '16-08-2025', 'FN', 'ECT463', 'ECE', 7),
(272, 9, '16-08-2025', 'FN', 'EET401', 'EEE', 7),
(273, 9, '16-08-2025', 'FN', 'CST463', 'CSE A', 7),
(274, 9, '16-08-2025', 'FN', 'CST473', 'CSE A', 7),
(275, 9, '16-08-2025', 'FN', 'CST433', 'CSE A', 7),
(276, 9, '16-08-2025', 'FN', 'CST413', 'CSE A', 7),
(277, 9, '16-08-2025', 'FN', 'CST463', 'CSE AI', 7),
(278, 9, '16-08-2025', 'FN', 'CST473', 'CSE AI', 7),
(279, 9, '16-08-2025', 'FN', 'CST433', 'CSE AI', 7),
(280, 9, '16-08-2025', 'FN', 'CST423', 'CSE AI', 7),
(281, 9, '16-08-2025', 'FN', 'CST463', 'AIDS', 7),
(282, 9, '16-08-2025', 'FN', 'CST473', 'AIDS', 7),
(283, 9, '16-08-2025', 'FN', 'CST423', 'AIDS', 7),
(284, 9, '16-08-2025', 'FN', 'CST463', 'CY', 7),
(285, 9, '16-08-2025', 'FN', 'CST473', 'CY', 7),
(286, 9, '16-08-2025', 'FN', 'CCT433', 'CY', 7),
(287, 9, '16-08-2025', 'FN', 'CST413', 'CY', 7),
(288, 9, '16-08-2025', 'AN', 'CET415', 'ME', 7),
(289, 9, '16-08-2025', 'AN', 'CST435', 'ME', 7),
(290, 9, '16-08-2025', 'AN', 'CST445', 'ME', 7),
(291, 9, '16-08-2025', 'AN', 'ECT445', 'ME', 7),
(292, 9, '16-08-2025', 'AN', 'EET435', 'ME', 7),
(293, 9, '16-08-2025', 'AN', 'EET455', 'ME', 7),
(294, 9, '16-08-2025', 'AN', 'MET425', 'CE', 7),
(295, 9, '16-08-2025', 'AN', 'EET455', 'CE', 7),
(296, 9, '16-08-2025', 'AN', 'EET435', 'CE', 7),
(297, 9, '16-08-2025', 'AN', 'ECT445', 'CE', 7),
(298, 9, '16-08-2025', 'AN', 'MET445', 'CE', 7),
(299, 9, '16-08-2025', 'AN', 'MET455', 'CE', 7),
(300, 9, '16-08-2025', 'AN', 'EET435', 'ECE', 7),
(301, 9, '16-08-2025', 'AN', 'CST435', 'ECE', 7),
(302, 9, '16-08-2025', 'AN', 'CET415', 'ECE', 7),
(303, 9, '16-08-2025', 'AN', 'EET455', 'ECE', 7),
(304, 9, '16-08-2025', 'AN', 'MET425', 'ECE', 7),
(305, 9, '16-08-2025', 'AN', 'MET445', 'ECE', 7),
(306, 9, '16-08-2025', 'AN', 'MET455', 'ECE', 7),
(307, 9, '16-08-2025', 'AN', 'MET425', 'EEE', 7),
(308, 9, '16-08-2025', 'AN', 'CET415', 'EEE', 7),
(309, 9, '16-08-2025', 'AN', 'ECT445', 'EEE', 7),
(310, 9, '16-08-2025', 'AN', 'CST435', 'EEE', 7),
(311, 9, '16-08-2025', 'AN', 'MET445', 'EEE', 7),
(312, 9, '16-08-2025', 'AN', 'MET455', 'EEE', 7),
(313, 9, '16-08-2025', 'AN', 'CET415', 'CSE A', 7),
(314, 9, '16-08-2025', 'AN', 'ECT445', 'CSE A', 7),
(315, 9, '16-08-2025', 'AN', 'EET435', 'CSE A', 7),
(316, 9, '16-08-2025', 'AN', 'EET455', 'CSE A', 7),
(317, 9, '16-08-2025', 'AN', 'MET425', 'CSE A', 7),
(318, 9, '16-08-2025', 'AN', 'MET445', 'CSE A', 7),
(319, 9, '16-08-2025', 'AN', 'MET455', 'CSE A', 7),
(320, 9, '16-08-2025', 'AN', 'EET435', 'CSE AI', 7),
(321, 9, '16-08-2025', 'AN', 'EET455', 'CSE AI', 7),
(322, 9, '16-08-2025', 'AN', 'MET425', 'CSE AI', 7),
(323, 9, '16-08-2025', 'AN', 'MET455', 'CSE AI', 7),
(324, 9, '16-08-2025', 'AN', 'CET415', 'CSE AI', 7),
(325, 9, '16-08-2025', 'AN', 'MET445', 'CSE AI', 7),
(326, 9, '16-08-2025', 'AN', 'EET435', 'AIDS', 7),
(327, 9, '16-08-2025', 'AN', 'EET455', 'AIDS', 7),
(328, 9, '16-08-2025', 'AN', 'MET425', 'AIDS', 7),
(329, 9, '16-08-2025', 'AN', 'MET455', 'AIDS', 7),
(330, 9, '16-08-2025', 'AN', 'CET415', 'AIDS', 7),
(331, 9, '16-08-2025', 'AN', 'ECT455', 'AIDS', 7),
(332, 9, '16-08-2025', 'AN', 'MET445', 'AIDS', 7),
(333, 9, '16-08-2025', 'AN', 'EET435', 'CY', 7),
(334, 9, '16-08-2025', 'AN', 'EET455', 'CY', 7),
(335, 9, '16-08-2025', 'AN', 'MET425', 'CY', 7),
(336, 9, '16-08-2025', 'AN', 'MET455', 'CY', 7),
(337, 9, '16-08-2025', 'AN', 'CET415', 'CY', 7),
(338, 9, '16-08-2025', 'AN', 'ECT445', 'CY', 7),
(339, 9, '16-08-2025', 'AN', 'MET445', 'CY', 7),
(340, 9, '18-08-2025', 'FN', 'MET413', 'ME', 7),
(341, 9, '18-08-2025', 'FN', 'CET423', 'CE', 7),
(342, 9, '18-08-2025', 'FN', 'CET453', 'CE', 7),
(343, 9, '18-08-2025', 'FN', 'ECT401', 'ECE', 7),
(344, 9, '18-08-2025', 'FN', 'EET463', 'EEE', 7),
(345, 9, '18-08-2025', 'FN', 'CST401', 'CSE A', 7),
(346, 9, '18-08-2025', 'FN', 'AIT401', 'CSE AI', 7),
(347, 9, '18-08-2025', 'FN', 'AIT401', 'AIDS', 7),
(348, 9, '18-08-2025', 'FN', 'CCT401', 'CY', 7),
(349, 9, '18-08-2025', 'AN', 'MCN401', 'ME', 7),
(350, 9, '18-08-2025', 'AN', 'MCN401', 'CE', 7),
(351, 9, '18-08-2025', 'AN', 'MCN401', 'ECE', 7),
(352, 9, '18-08-2025', 'AN', 'MCN401', 'EEE', 7),
(353, 9, '18-08-2025', 'AN', 'MCN401', 'CSE A', 7),
(354, 9, '18-08-2025', 'AN', 'MCN401', 'CSE AI', 7),
(355, 9, '18-08-2025', 'AN', 'MCN401', 'AIDS', 7),
(356, 9, '18-08-2025', 'AN', 'MCN401', 'CY', 7);

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

CREATE TABLE `rooms` (
  `Rid` int(11) NOT NULL,
  `Block` varchar(255) NOT NULL,
  `Room_no` varchar(255) NOT NULL,
  `Capacity` int(11) NOT NULL,
  `Type` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rooms`
--

INSERT INTO `rooms` (`Rid`, `Block`, `Room_no`, `Capacity`, `Type`) VALUES
(1, 'M George Block', 'M101', 64, 'Normal'),
(2, 'M George Block', 'M103', 44, 'Normal'),
(3, 'M George Block', 'M104', 60, 'Normal'),
(4, 'M George Block', 'M105', 60, 'Normal'),
(5, 'M George Block', 'M201', 70, 'Normal'),
(6, 'M George Block', 'M202', 66, 'Normal'),
(7, 'M George Block', 'M204', 72, 'Normal'),
(8, 'M George Block', 'M205', 42, 'Normal'),
(9, 'M George Block', 'M206', 60, 'Drawing'),
(10, 'M George Block', 'M208', 60, 'Drawing'),
(11, 'M George Block', 'M222', 74, 'Normal'),
(12, 'M George Block', 'M321', 65, 'Normal'),
(13, 'Ramanujan Block', '209', 60, 'Normal'),
(14, 'Ramanujan Block', '211', 60, 'Normal'),
(15, 'Ramanujan Block', '212', 60, 'Normal'),
(16, 'Ramanujan Block', '502', 70, 'Normal'),
(17, 'Ramanujan Block', '506', 70, 'Normal'),
(18, 'Ramanujan Block', '508', 70, 'Normal'),
(19, 'Ramanujan Block', '512', 70, 'Normal'),
(20, 'Ramanujan Block', '601', 76, 'Normal'),
(21, 'Ramanujan Block', '603', 76, 'Normal'),
(22, 'Ramanujan Block', '604', 70, 'Normal'),
(23, 'Ramanujan Block', '606', 40, 'Normal'),
(24, 'Ramanujan Block', '607', 72, 'Normal'),
(25, 'Ramanujan Block', '610', 70, 'Normal'),
(26, 'Ramanujan Block', '612', 76, 'Normal'),
(27, 'Ramanujan Block', '613', 76, 'Normal'),
(28, 'Ramanujan Block', '704', 76, 'Normal'),
(29, 'Ramanujan Block', '705', 72, 'Normal'),
(30, 'Ramanujan Block', '706', 62, 'Normal');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `student_id` int(11) NOT NULL,
  `reg_no` varchar(50) NOT NULL,
  `rollno` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `branch` varchar(255) NOT NULL,
  `semester` int(11) NOT NULL,
  `elective_1` varchar(50) NOT NULL,
  `elective_2` varchar(50) NOT NULL,
  `elective_3` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`student_id`, `reg_no`, `rollno`, `name`, `branch`, `semester`, `elective_1`, `elective_2`, `elective_3`) VALUES
(1, 'MUT25CSEA001', 1, 'DIVYA', 'CSE A', 1, 'CST451', 'CST436', 'CST441'),
(2, 'MUT25CSEB002', 2, 'DEEPA', 'CSE B', 1, 'CST451', 'CST436', 'CST441'),
(3, 'MUT25AIDS003', 3, 'RAHUL', 'AIDS', 1, 'CST451', 'CST436', 'CST441'),
(4, 'MUT25CSEAI004', 4, 'VISHNU', 'CSE-AI', 1, 'CST451', 'CST436', 'CST441'),
(5, 'MUT25CY005', 5, 'VISHNU', 'CY', 1, 'CST451', 'CST436', 'CST441'),
(6, 'MUT25ECE006', 6, 'SUNIL', 'ECE', 1, 'CST451', 'CST436', 'CST441'),
(7, 'MUT25EEE007', 7, 'SANJAY', 'EEE', 1, 'CST451', 'CST436', 'CST441'),
(8, 'MUT25ME008', 8, 'AMAL', 'ME', 1, 'CST451', 'CST436', 'CST441'),
(9, 'MUT25CE009', 9, 'HEMA', 'CE', 1, 'CST451', 'CST436', 'CST441'),
(10, 'MUT25CSEA010', 10, 'NITHIN', 'CSE A', 3, 'CST451', 'CST436', 'CST441'),
(11, 'MUT25CSEB011', 11, 'MANOJ', 'CSE B', 3, 'CST451', 'CST436', 'CST441'),
(12, 'MUT25AIDS012', 12, 'VISHNU', 'AIDS', 3, 'CST451', 'CST436', 'CST441'),
(13, 'MUT25CSEAI013', 13, 'MOHAN', 'CSE-AI', 3, 'CST451', 'CST436', 'CST441'),
(14, 'MUT25CY014', 14, 'PRIYA', 'CY', 3, 'CST451', 'CST436', 'CST441'),
(15, 'MUT25ECE015', 15, 'AISHA', 'ECE', 3, 'CST451', 'CST436', 'CST441'),
(16, 'MUT25EEE016', 16, 'DEEPA', 'EEE', 3, 'CST451', 'CST436', 'CST441'),
(17, 'MUT25ME017', 17, 'MEERA', 'ME', 3, 'CST451', 'CST436', 'CST441'),
(18, 'MUT25CE018', 18, 'REKHA', 'CE', 3, 'CST451', 'CST436', 'CST441'),
(19, 'MUT25CSEA019', 19, 'ROHIT', 'CSE A', 5, 'CST451', 'CST436', 'CST441'),
(20, 'MUT25CSEB020', 20, 'DEEPA', 'CSE B', 5, 'CST451', 'CST436', 'CST441'),
(21, 'MUT25AIDS021', 21, 'RAHUL', 'AIDS', 5, 'CST451', 'CST436', 'CST441'),
(22, 'MUT25CSEAI022', 22, 'NANDINI', 'CSE-AI', 5, 'CST451', 'CST436', 'CST441'),
(23, 'MUT25CY023', 23, 'SNEHA', 'CY', 5, 'CST451', 'CST436', 'CST441'),
(24, 'MUT25ECE024', 24, 'AJAY', 'ECE', 5, 'CST451', 'CST436', 'CST441'),
(25, 'MUT25EEE025', 25, 'NEETHU', 'EEE', 5, 'CST451', 'CST436', 'CST441'),
(26, 'MUT25ME026', 26, 'DIVYA', 'ME', 5, 'CST451', 'CST436', 'CST441'),
(27, 'MUT25CE027', 27, 'ARJUN', 'CE', 5, 'CST451', 'CST436', 'CST441'),
(28, 'MUT25CSEA028', 28, 'DEEPA', 'CSE A', 7, 'CST451', 'CST436', 'CST441'),
(29, 'MUT25CSEB029', 29, 'MANOJ', 'CSE B', 7, 'CST451', 'CST436', 'CST441'),
(30, 'MUT25AIDS030', 30, 'RAJ', 'AIDS', 7, 'CST451', 'CST436', 'CST441'),
(31, 'MUT25CSEAI031', 31, 'SUNIL', 'CSE-AI', 7, 'CST451', 'CST436', 'CST441'),
(32, 'MUT25CY032', 32, 'HEMA', 'CY', 7, 'CST451', 'CST436', 'CST441'),
(33, 'MUT25ECE033', 33, 'DEEPA', 'ECE', 7, 'CST451', 'CST436', 'CST441'),
(34, 'MUT25EEE034', 34, 'MEERA', 'EEE', 7, 'CST451', 'CST436', 'CST441'),
(35, 'MUT25ME035', 35, 'ARUN', 'ME', 7, 'CST451', 'CST436', 'CST441'),
(36, 'MUT25CE036', 36, 'MANOJ', 'CE', 7, 'CST451', 'CST436', 'CST441'),
(37, 'MUT25CSEA037', 37, 'AJAY', 'CSE A', 1, 'CST451', 'CST436', 'CST441'),
(38, 'MUT25CSEB038', 38, 'AISHA', 'CSE B', 1, 'CST451', 'CST436', 'CST441'),
(39, 'MUT25AIDS039', 39, 'KIRAN', 'AIDS', 1, 'CST451', 'CST436', 'CST441'),
(40, 'MUT25CSEAI040', 40, 'HEMA', 'CSE-AI', 1, 'CST451', 'CST436', 'CST441'),
(41, 'MUT25CY041', 41, 'AMAL', 'CY', 1, 'CST451', 'CST436', 'CST441'),
(42, 'MUT25ECE042', 42, 'HEMA', 'ECE', 1, 'CST451', 'CST436', 'CST441'),
(43, 'MUT25EEE043', 43, 'ARJUN', 'EEE', 1, 'CST451', 'CST436', 'CST441'),
(44, 'MUT25ME044', 44, 'SUNIL', 'ME', 1, 'CST451', 'CST436', 'CST441'),
(45, 'MUT25CE045', 45, 'ANU', 'CE', 1, 'CST451', 'CST436', 'CST441'),
(46, 'MUT25CSEA046', 46, 'ARUN', 'CSE A', 3, 'CST451', 'CST436', 'CST441'),
(47, 'MUT25CSEB047', 47, 'PRIYA', 'CSE B', 3, 'CST451', 'CST436', 'CST441'),
(48, 'MUT25AIDS048', 48, 'AJAY', 'AIDS', 3, 'CST451', 'CST436', 'CST441'),
(49, 'MUT25CSEAI049', 49, 'KIRAN', 'CSE-AI', 3, 'CST451', 'CST436', 'CST441'),
(50, 'MUT25CY050', 50, 'AJAY', 'CY', 3, 'CST451', 'CST436', 'CST441'),
(51, 'MUT25ECE051', 51, 'AJAY', 'ECE', 3, 'CST451', 'CST436', 'CST441'),
(52, 'MUT25EEE052', 52, 'ANU', 'EEE', 3, 'CST451', 'CST436', 'CST441'),
(53, 'MUT25ME053', 53, 'RESHMA', 'ME', 3, 'CST451', 'CST436', 'CST441'),
(54, 'MUT25CE054', 54, 'AJAY', 'CE', 3, 'CST451', 'CST436', 'CST441'),
(55, 'MUT25CSEA055', 55, 'RAHUL', 'CSE A', 5, 'CST451', 'CST436', 'CST441'),
(56, 'MUT25CSEB056', 56, 'KAVYA', 'CSE B', 5, 'CST451', 'CST436', 'CST441'),
(57, 'MUT25AIDS057', 57, 'NEETHU', 'AIDS', 5, 'CST451', 'CST436', 'CST441'),
(58, 'MUT25CSEAI058', 58, 'PRIYA', 'CSE-AI', 5, 'CST451', 'CST436', 'CST441'),
(59, 'MUT25CY059', 59, 'DIVYA', 'CY', 5, 'CST451', 'CST436', 'CST441'),
(60, 'MUT25ECE060', 60, 'VISHNU', 'ECE', 5, 'CST451', 'CST436', 'CST441'),
(61, 'MUT25EEE061', 61, 'DEEPA', 'EEE', 5, 'CST451', 'CST436', 'CST441'),
(62, 'MUT25ME062', 62, 'ANU', 'ME', 5, 'CST451', 'CST436', 'CST441'),
(63, 'MUT25CE063', 63, 'SUNIL', 'CE', 5, 'CST451', 'CST436', 'CST441'),
(64, 'MUT25CSEA064', 64, 'NEETHU', 'CSE A', 7, 'CST451', 'CST436', 'CST441'),
(65, 'MUT25CSEB065', 65, 'AISHA', 'CSE B', 7, 'CST451', 'CST436', 'CST441'),
(66, 'MUT25AIDS066', 66, 'AMAL', 'AIDS', 7, 'CST451', 'CST436', 'CST441'),
(67, 'MUT25CSEAI067', 67, 'RAHUL', 'CSE-AI', 7, 'CST451', 'CST436', 'CST441'),
(68, 'MUT25CY068', 68, 'AMAL', 'CY', 7, 'CST451', 'CST436', 'CST441'),
(69, 'MUT25ECE069', 69, 'SUNIL', 'ECE', 7, 'CST451', 'CST436', 'CST441'),
(70, 'MUT25EEE070', 70, 'NEETHU', 'EEE', 7, 'CST451', 'CST436', 'CST441'),
(71, 'MUT25ME071', 71, 'HEMA', 'ME', 7, 'CST451', 'CST436', 'CST441'),
(72, 'MUT25CE072', 72, 'HEMA', 'CE', 7, 'CST451', 'CST436', 'CST441'),
(73, 'MUT25CSEA073', 73, 'RAJ', 'CSE A', 1, 'CST451', 'CST436', 'CST441'),
(74, 'MUT25CSEB074', 74, 'VISHNU', 'CSE B', 1, 'CST451', 'CST436', 'CST441'),
(75, 'MUT25AIDS075', 75, 'AMAL', 'AIDS', 1, 'CST451', 'CST436', 'CST441'),
(76, 'MUT25CSEAI076', 76, 'AMAL', 'CSE-AI', 1, 'CST451', 'CST436', 'CST441'),
(77, 'MUT25CY077', 77, 'POOJA', 'CY', 1, 'CST451', 'CST436', 'CST441'),
(78, 'MUT25ECE078', 78, 'PRIYA', 'ECE', 1, 'CST451', 'CST436', 'CST441'),
(79, 'MUT25EEE079', 79, 'VISHNU', 'EEE', 1, 'CST451', 'CST436', 'CST441'),
(80, 'MUT25ME080', 80, 'REKHA', 'ME', 1, 'CST451', 'CST436', 'CST441'),
(81, 'MUT25CE081', 81, 'RAJ', 'CE', 1, 'CST451', 'CST436', 'CST441'),
(82, 'MUT25CSEA082', 82, 'SUNIL', 'CSE A', 3, 'CST451', 'CST436', 'CST441'),
(83, 'MUT25CSEB083', 83, 'VINOD', 'CSE B', 3, 'CST451', 'CST436', 'CST441'),
(84, 'MUT25AIDS084', 84, 'POOJA', 'AIDS', 3, 'CST451', 'CST436', 'CST441'),
(85, 'MUT25CSEAI085', 85, 'VISHNU', 'CSE-AI', 3, 'CST451', 'CST436', 'CST441'),
(86, 'MUT25CY086', 86, 'MEERA', 'CY', 3, 'CST451', 'CST436', 'CST441'),
(87, 'MUT25ECE087', 87, 'VINOD', 'ECE', 3, 'CST451', 'CST436', 'CST441'),
(88, 'MUT25EEE088', 88, 'ANU', 'EEE', 3, 'CST451', 'CST436', 'CST441'),
(89, 'MUT25ME089', 89, 'NANDINI', 'ME', 3, 'CST451', 'CST436', 'CST441'),
(90, 'MUT25CE090', 90, 'DEEPA', 'CE', 3, 'CST451', 'CST436', 'CST441'),
(91, 'MUT25CSEA091', 91, 'NANDINI', 'CSE A', 5, 'CST451', 'CST436', 'CST441'),
(92, 'MUT25CSEB092', 92, 'ANJANA', 'CSE B', 5, 'CST451', 'CST436', 'CST441'),
(93, 'MUT25AIDS093', 93, 'REKHA', 'AIDS', 5, 'CST451', 'CST436', 'CST441'),
(94, 'MUT25CSEAI094', 94, 'SANJAY', 'CSE-AI', 5, 'CST451', 'CST436', 'CST441'),
(95, 'MUT25CY095', 95, 'PRIYA', 'CY', 5, 'CST451', 'CST436', 'CST441'),
(96, 'MUT25ECE096', 96, 'DEEPA', 'ECE', 5, 'CST451', 'CST436', 'CST441'),
(97, 'MUT25EEE097', 97, 'HEMA', 'EEE', 5, 'CST451', 'CST436', 'CST441'),
(98, 'MUT25ME098', 98, 'MANOJ', 'ME', 5, 'CST451', 'CST436', 'CST441'),
(99, 'MUT25CE099', 99, 'ANJANA', 'CE', 5, 'CST451', 'CST436', 'CST441'),
(100, 'MUT25CSEA100', 100, 'MANOJ', 'CSE A', 7, 'CST451', 'CST436', 'CST441'),
(101, 'MUT25CSEB101', 101, 'ROHIT', 'CSE B', 7, 'CST451', 'CST436', 'CST441'),
(102, 'MUT25AIDS102', 102, 'HEMA', 'AIDS', 7, 'CST451', 'CST436', 'CST441'),
(103, 'MUT25CSEAI103', 103, 'MEERA', 'CSE-AI', 7, 'CST451', 'CST436', 'CST441'),
(104, 'MUT25CY104', 104, 'RESHMA', 'CY', 7, 'CST451', 'CST436', 'CST441'),
(105, 'MUT25ECE105', 105, 'PRIYA', 'ECE', 7, 'CST451', 'CST436', 'CST441'),
(106, 'MUT25EEE106', 106, 'ANJANA', 'EEE', 7, 'CST451', 'CST436', 'CST441'),
(107, 'MUT25ME107', 107, 'VISHNU', 'ME', 7, 'CST451', 'CST436', 'CST441'),
(108, 'MUT25CE108', 108, 'SANJAY', 'CE', 7, 'CST451', 'CST436', 'CST441'),
(109, 'MUT25CSEA109', 109, 'PRIYA', 'CSE A', 1, 'CST451', 'CST436', 'CST441'),
(110, 'MUT25CSEB110', 110, 'AMAL', 'CSE B', 1, 'CST451', 'CST436', 'CST441'),
(111, 'MUT25AIDS111', 111, 'SANJAY', 'AIDS', 1, 'CST451', 'CST436', 'CST441'),
(112, 'MUT25CSEAI112', 112, 'RAJ', 'CSE-AI', 1, 'CST451', 'CST436', 'CST441'),
(113, 'MUT25CY113', 113, 'MOHAN', 'CY', 1, 'CST451', 'CST436', 'CST441'),
(114, 'MUT25ECE114', 114, 'KIRAN', 'ECE', 1, 'CST451', 'CST436', 'CST441'),
(115, 'MUT25EEE115', 115, 'POOJA', 'EEE', 1, 'CST451', 'CST436', 'CST441'),
(116, 'MUT25ME116', 116, 'AISHA', 'ME', 1, 'CST451', 'CST436', 'CST441'),
(117, 'MUT25CE117', 117, 'AMAL', 'CE', 1, 'CST451', 'CST436', 'CST441'),
(118, 'MUT25CSEA118', 118, 'ANU', 'CSE A', 3, 'CST451', 'CST436', 'CST441'),
(119, 'MUT25CSEB119', 119, 'ROHIT', 'CSE B', 3, 'CST451', 'CST436', 'CST441'),
(120, 'MUT25AIDS120', 120, 'ROHIT', 'AIDS', 3, 'CST451', 'CST436', 'CST441'),
(121, 'MUT25CSEAI121', 121, 'SUNIL', 'CSE-AI', 3, 'CST451', 'CST436', 'CST441'),
(122, 'MUT25CY122', 122, 'VISHNU', 'CY', 3, 'CST451', 'CST436', 'CST441'),
(123, 'MUT25ECE123', 123, 'PRIYA', 'ECE', 3, 'CST451', 'CST436', 'CST441'),
(124, 'MUT25EEE124', 124, 'ROHIT', 'EEE', 3, 'CST451', 'CST436', 'CST441'),
(125, 'MUT25ME125', 125, 'AISHA', 'ME', 3, 'CST451', 'CST436', 'CST441'),
(126, 'MUT25CE126', 126, 'SUNIL', 'CE', 3, 'CST451', 'CST436', 'CST441'),
(127, 'MUT25CSEA127', 127, 'VINOD', 'CSE A', 5, 'CST451', 'CST436', 'CST441'),
(128, 'MUT25CSEB128', 128, 'ARUN', 'CSE B', 5, 'CST451', 'CST436', 'CST441'),
(129, 'MUT25AIDS129', 129, 'NANDINI', 'AIDS', 5, 'CST451', 'CST436', 'CST441'),
(130, 'MUT25CSEAI130', 130, 'AJAY', 'CSE-AI', 5, 'CST451', 'CST436', 'CST441'),
(131, 'MUT25CY131', 131, 'ANU', 'CY', 5, 'CST451', 'CST436', 'CST441'),
(132, 'MUT25ECE132', 132, 'DEEPA', 'ECE', 5, 'CST451', 'CST436', 'CST441'),
(133, 'MUT25EEE133', 133, 'POOJA', 'EEE', 5, 'CST451', 'CST436', 'CST441'),
(134, 'MUT25ME134', 134, 'RAJ', 'ME', 5, 'CST451', 'CST436', 'CST441'),
(135, 'MUT25CE135', 135, 'AISHA', 'CE', 5, 'CST451', 'CST436', 'CST441'),
(136, 'MUT25CSEA136', 136, 'REKHA', 'CSE A', 7, 'CST451', 'CST436', 'CST441'),
(137, 'MUT25CSEB137', 137, 'DEEPA', 'CSE B', 7, 'CST451', 'CST436', 'CST441'),
(138, 'MUT25AIDS138', 138, 'AISHA', 'AIDS', 7, 'CST451', 'CST436', 'CST441'),
(139, 'MUT25CSEAI139', 139, 'VISHNU', 'CSE-AI', 7, 'CST451', 'CST436', 'CST441'),
(140, 'MUT25CY140', 140, 'NANDINI', 'CY', 7, 'CST451', 'CST436', 'CST441'),
(141, 'MUT25ECE141', 141, 'ARUN', 'ECE', 7, 'CST451', 'CST436', 'CST441'),
(142, 'MUT25EEE142', 142, 'RAHUL', 'EEE', 7, 'CST451', 'CST436', 'CST441'),
(143, 'MUT25ME143', 143, 'MOHAN', 'ME', 7, 'CST451', 'CST436', 'CST441'),
(144, 'MUT25CE144', 144, 'ARJUN', 'CE', 7, 'CST451', 'CST436', 'CST441'),
(145, 'MUT25CSEA145', 145, 'KAVYA', 'CSE A', 1, 'CST451', 'CST436', 'CST441'),
(146, 'MUT25CSEB146', 146, 'RESHMA', 'CSE B', 1, 'CST451', 'CST436', 'CST441'),
(147, 'MUT25AIDS147', 147, 'SANJAY', 'AIDS', 1, 'CST451', 'CST436', 'CST441'),
(148, 'MUT25CSEAI148', 148, 'AMAL', 'CSE-AI', 1, 'CST451', 'CST436', 'CST441'),
(149, 'MUT25CY149', 149, 'SANJAY', 'CY', 1, 'CST451', 'CST436', 'CST441'),
(150, 'MUT25ECE150', 150, 'PRIYA', 'ECE', 1, 'CST451', 'CST436', 'CST441'),
(151, 'MUT25EEE151', 151, 'RESHMA', 'EEE', 1, 'CST451', 'CST436', 'CST441'),
(152, 'MUT25ME152', 152, 'REKHA', 'ME', 1, 'CST451', 'CST436', 'CST441'),
(153, 'MUT25CE153', 153, 'REKHA', 'CE', 1, 'CST451', 'CST436', 'CST441'),
(154, 'MUT25CSEA154', 154, 'MOHAN', 'CSE A', 3, 'CST451', 'CST436', 'CST441'),
(155, 'MUT25CSEB155', 155, 'NITHIN', 'CSE B', 3, 'CST451', 'CST436', 'CST441'),
(156, 'MUT25AIDS156', 156, 'AJAY', 'AIDS', 3, 'CST451', 'CST436', 'CST441'),
(157, 'MUT25CSEAI157', 157, 'SUNIL', 'CSE-AI', 3, 'CST451', 'CST436', 'CST441'),
(158, 'MUT25CY158', 158, 'ARJUN', 'CY', 3, 'CST451', 'CST436', 'CST441'),
(159, 'MUT25ECE159', 159, 'MEERA', 'ECE', 3, 'CST451', 'CST436', 'CST441'),
(160, 'MUT25EEE160', 160, 'KIRAN', 'EEE', 3, 'CST451', 'CST436', 'CST441'),
(161, 'MUT25ME161', 161, 'ANU', 'ME', 3, 'CST451', 'CST436', 'CST441'),
(162, 'MUT25CE162', 162, 'ROHIT', 'CE', 3, 'CST451', 'CST436', 'CST441'),
(163, 'MUT25CSEA163', 163, 'RESHMA', 'CSE A', 5, 'CST451', 'CST436', 'CST441'),
(164, 'MUT25CSEB164', 164, 'RESHMA', 'CSE B', 5, 'CST451', 'CST436', 'CST441'),
(165, 'MUT25AIDS165', 165, 'ANU', 'AIDS', 5, 'CST451', 'CST436', 'CST441'),
(166, 'MUT25CSEAI166', 166, 'RAJ', 'CSE-AI', 5, 'CST451', 'CST436', 'CST441'),
(167, 'MUT25CY167', 167, 'ARUN', 'CY', 5, 'CST451', 'CST436', 'CST441'),
(168, 'MUT25ECE168', 168, 'DEEPA', 'ECE', 5, 'CST451', 'CST436', 'CST441'),
(169, 'MUT25EEE169', 169, 'DEEPA', 'EEE', 5, 'CST451', 'CST436', 'CST441'),
(170, 'MUT25ME170', 170, 'MANOJ', 'ME', 5, 'CST451', 'CST436', 'CST441'),
(171, 'MUT25CE171', 171, 'KIRAN', 'CE', 5, 'CST451', 'CST436', 'CST441'),
(172, 'MUT25CSEA172', 172, 'RESHMA', 'CSE A', 7, 'CST451', 'CST436', 'CST441'),
(173, 'MUT25CSEB173', 173, 'VISHNU', 'CSE B', 7, 'CST451', 'CST436', 'CST441'),
(174, 'MUT25AIDS174', 174, 'REKHA', 'AIDS', 7, 'CST451', 'CST436', 'CST441'),
(175, 'MUT25CSEAI175', 175, 'KIRAN', 'CSE-AI', 7, 'CST451', 'CST436', 'CST441'),
(176, 'MUT25CY176', 176, 'POOJA', 'CY', 7, 'CST451', 'CST436', 'CST441'),
(177, 'MUT25ECE177', 177, 'VINOD', 'ECE', 7, 'CST451', 'CST436', 'CST441'),
(178, 'MUT25EEE178', 178, 'POOJA', 'EEE', 7, 'CST451', 'CST436', 'CST441'),
(179, 'MUT25ME179', 179, 'PRIYA', 'ME', 7, 'CST451', 'CST436', 'CST441'),
(180, 'MUT25CE180', 180, 'HEMA', 'CE', 7, 'CST451', 'CST436', 'CST441'),
(181, 'MUT25CSEA181', 181, 'RESHMA', 'CSE A', 1, 'CST451', 'CST436', 'CST441'),
(182, 'MUT25CSEB182', 182, 'DIVYA', 'CSE B', 1, 'CST451', 'CST436', 'CST441'),
(183, 'MUT25AIDS183', 183, 'KAVYA', 'AIDS', 1, 'CST451', 'CST436', 'CST441'),
(184, 'MUT25CSEAI184', 184, 'NANDINI', 'CSE-AI', 1, 'CST451', 'CST436', 'CST441'),
(185, 'MUT25CY185', 185, 'SNEHA', 'CY', 1, 'CST451', 'CST436', 'CST441'),
(186, 'MUT25ECE186', 186, 'MANOJ', 'ECE', 1, 'CST451', 'CST436', 'CST441'),
(187, 'MUT25EEE187', 187, 'DIVYA', 'EEE', 1, 'CST451', 'CST436', 'CST441'),
(188, 'MUT25ME188', 188, 'POOJA', 'ME', 1, 'CST451', 'CST436', 'CST441'),
(189, 'MUT25CE189', 189, 'ARUN', 'CE', 1, 'CST451', 'CST436', 'CST441'),
(190, 'MUT25CSEA190', 190, 'RAJ', 'CSE A', 3, 'CST451', 'CST436', 'CST441'),
(191, 'MUT25CSEB191', 191, 'HEMA', 'CSE B', 3, 'CST451', 'CST436', 'CST441'),
(192, 'MUT25AIDS192', 192, 'DIVYA', 'AIDS', 3, 'CST451', 'CST436', 'CST441'),
(193, 'MUT25CSEAI193', 193, 'KAVYA', 'CSE-AI', 3, 'CST451', 'CST436', 'CST441'),
(194, 'MUT25CY194', 194, 'RAJ', 'CY', 3, 'CST451', 'CST436', 'CST441'),
(195, 'MUT25ECE195', 195, 'ARUN', 'ECE', 3, 'CST451', 'CST436', 'CST441'),
(196, 'MUT25EEE196', 196, 'SNEHA', 'EEE', 3, 'CST451', 'CST436', 'CST441'),
(197, 'MUT25ME197', 197, 'ANJANA', 'ME', 3, 'CST451', 'CST436', 'CST441'),
(198, 'MUT25CE198', 198, 'MANOJ', 'CE', 3, 'CST451', 'CST436', 'CST441'),
(199, 'MUT25CSEA199', 199, 'KIRAN', 'CSE A', 5, 'CST451', 'CST436', 'CST441'),
(200, 'MUT25CSEB200', 200, 'AMAL', 'CSE B', 5, 'CST451', 'CST436', 'CST441');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `uid` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`uid`, `username`, `password`) VALUES
(1, 'admin', 'admin@mits');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`cid`);

--
-- Indexes for table `exam_definition`
--
ALTER TABLE `exam_definition`
  ADD PRIMARY KEY (`eid`);

--
-- Indexes for table `exam_time_table`
--
ALTER TABLE `exam_time_table`
  ADD PRIMARY KEY (`ttid`);

--
-- Indexes for table `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`Rid`),
  ADD UNIQUE KEY `Room_no` (`Room_no`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`student_id`),
  ADD UNIQUE KEY `reg_no` (`reg_no`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`uid`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `cid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=303;

--
-- AUTO_INCREMENT for table `exam_definition`
--
ALTER TABLE `exam_definition`
  MODIFY `eid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `exam_time_table`
--
ALTER TABLE `exam_time_table`
  MODIFY `ttid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=357;

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `Rid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `student_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=201;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `uid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
