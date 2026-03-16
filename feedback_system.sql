-- InfinityFree Compatible SQL Export
SET FOREIGN_KEY_CHECKS = 0;

-- Table structure for students
DROP TABLE IF EXISTS `students`;
CREATE TABLE `students` (
  `id` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `full_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `reg_number` varchar(100) NOT NULL,
  `str_year` varchar(50) NOT NULL DEFAULT '1',
  `password` varchar(255) NOT NULL,
  `role` varchar(50) DEFAULT 'Student',
  `reg_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `reg_number` (`reg_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table structure for teachers
DROP TABLE IF EXISTS `teachers`;
CREATE TABLE `teachers` (
  `id` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `reg_number` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(50) DEFAULT 'Teacher',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  UNIQUE KEY `reg_number` (`reg_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seeding teachers
INSERT INTO `teachers` (`name`, `reg_number`, `password`, `role`) VALUES 
('Ann mary', 'MCA 10001', 'Ann123', 'Teacher'),
('Anit james', 'MCA 10002', 'Anit123', 'Teacher'),
('Nimmy Francis', 'MCA 10003', 'Nimmy123', 'Teacher'),
('Ajith G S', 'MCA 10004', 'Ajith123', 'Teacher'),
('Amal K Jose', 'MCA 10005', 'Amal123', 'Teacher'),
('Lisha Varghese', 'MCA 10006', 'Lisha123', 'Teacher');

-- Table structure for feedbacks
DROP TABLE IF EXISTS `feedbacks`;
CREATE TABLE `feedbacks` (
  `id` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `student_id` int(6) unsigned DEFAULT NULL,
  `teacher_id` int(6) unsigned DEFAULT NULL,
  `subject` varchar(255) NOT NULL,
  `q1` int(2) DEFAULT NULL,
  `q2` int(2) DEFAULT NULL,
  `q3` int(2) DEFAULT NULL,
  `q4` int(2) DEFAULT NULL,
  `q5` int(2) DEFAULT NULL,
  `q6` int(2) DEFAULT NULL,
  `q7` int(2) DEFAULT NULL,
  `q8` int(2) DEFAULT NULL,
  `q9` int(2) DEFAULT NULL,
  `suggestion` text DEFAULT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `student_id` (`student_id`),
  KEY `teacher_id` (`teacher_id`),
  CONSTRAINT `feedbacks_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  CONSTRAINT `feedbacks_ibfk_2` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

SET FOREIGN_KEY_CHECKS = 1;
