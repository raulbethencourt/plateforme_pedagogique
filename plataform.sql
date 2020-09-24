-- phpMyAdmin SQL Dump
-- version 5.0.0
-- https://www.phpmyadmin.net/
--
-- Host: mysql
-- Generation Time: Sep 24, 2020 at 02:56 PM
-- Server version: 10.4.14-MariaDB-1:10.4.14+maria~focal
-- PHP Version: 7.4.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `plataform`
--

-- --------------------------------------------------------

--
-- Table structure for table `classroom`
--

CREATE TABLE `classroom` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `discipline` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `classroom`
--

INSERT INTO `classroom` (`id`, `name`, `discipline`) VALUES
(1, 'clase de programacion web', 'informatica');

-- --------------------------------------------------------

--
-- Table structure for table `classroom_student`
--

CREATE TABLE `classroom_student` (
  `classroom_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `classroom_student`
--

INSERT INTO `classroom_student` (`classroom_id`, `student_id`) VALUES
(1, 5);

-- --------------------------------------------------------

--
-- Table structure for table `classroom_teacher`
--

CREATE TABLE `classroom_teacher` (
  `classroom_id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `classroom_teacher`
--

INSERT INTO `classroom_teacher` (`classroom_id`, `teacher_id`) VALUES
(1, 4);

-- --------------------------------------------------------

--
-- Table structure for table `doctrine_migration_versions`
--

CREATE TABLE `doctrine_migration_versions` (
  `version` varchar(191) COLLATE utf8_unicode_ci NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `doctrine_migration_versions`
--

INSERT INTO `doctrine_migration_versions` (`version`, `executed_at`, `execution_time`) VALUES
('DoctrineMigrations\\Version20200924072201', '2020-09-24 07:22:17', 1381);

-- --------------------------------------------------------

--
-- Table structure for table `pass`
--

CREATE TABLE `pass` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `questionnaire_id` int(11) NOT NULL,
  `points` int(11) NOT NULL,
  `date_realisation` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pass`
--

INSERT INTO `pass` (`id`, `student_id`, `questionnaire_id`, `points`, `date_realisation`) VALUES
(1, 5, 1, 6, '2020-09-24');

-- --------------------------------------------------------

--
-- Table structure for table `proposition`
--

CREATE TABLE `proposition` (
  `id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `text` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `correct` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `proposition`
--

INSERT INTO `proposition` (`id`, `question_id`, `text`, `correct`) VALUES
(1, 1, 'Java', 0),
(2, 1, 'PHP', 1),
(3, 1, 'JS', 0),
(4, 2, '2008', 0),
(5, 2, '2005', 1),
(6, 2, '2010', 0),
(7, 2, '2020', 0);

-- --------------------------------------------------------

--
-- Table structure for table `question`
--

CREATE TABLE `question` (
  `id` int(11) NOT NULL,
  `questionnaire_id` int(11) NOT NULL,
  `wording` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `score` decimal(5,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `question`
--

INSERT INTO `question` (`id`, `questionnaire_id`, `wording`, `score`) VALUES
(1, 1, 'En quel langage est développé le framework Symfony ?', '4.00'),
(2, 1, 'En quelle année a été crée Symfony ?', '2.00');

-- --------------------------------------------------------

--
-- Table structure for table `questionnaire`
--

CREATE TABLE `questionnaire` (
  `id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `difficulty` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_creation` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `questionnaire`
--

INSERT INTO `questionnaire` (`id`, `teacher_id`, `title`, `difficulty`, `date_creation`) VALUES
(1, 4, 'Symfony', 'facile', '2020-09-24');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `username` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `roles` longtext COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:json)',
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `surname` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `photo_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_verified` tinyint(1) NOT NULL,
  `entry_date` date NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `hobby` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subject` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `username`, `roles`, `password`, `surname`, `name`, `email`, `photo_name`, `is_verified`, `entry_date`, `type`, `hobby`, `subject`) VALUES
(1, 'admin', '[\"ROLE_ADMIN\"]', '$argon2id$v=19$m=65536,t=4,p=1$0jvgyua5sWauRQCr9ZCHzQ$Tt33xJVowAsud0iNjgPwOtNS1H/6rZwkaUbYbaGN7LY', NULL, NULL, 'admin@test.mail', NULL, 0, '2020-09-24', 'user', NULL, NULL),
(4, 'Micka', '[\"ROLE_TEACHER\"]', '$argon2id$v=19$m=65536,t=4,p=1$5qi9RevAFvhhBzbUwVpduQ$Z61gl0QFaqueRalpQIy9C737uokjjL4dWTQpnixDVXU', 'Mickael', 'MURMANN', 'mickael.murmann@gmail.com', NULL, 0, '2020-09-24', 'teacher', NULL, NULL),
(5, 'Raul', '[\"ROLE_STUDENT\"]', '$argon2id$v=19$m=65536,t=4,p=1$yi2S6lXMmUa9VzuKnCnCcA$D9smaZ8ezYIkhhvSjmLV8PASC1GXrki3duiNbh9KjrU', 'Raul', 'BETHENCOURT', 'raul@exemple.com', NULL, 0, '2020-09-24', 'student', NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `classroom`
--
ALTER TABLE `classroom`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `classroom_student`
--
ALTER TABLE `classroom_student`
  ADD PRIMARY KEY (`classroom_id`,`student_id`),
  ADD KEY `IDX_3DD26E1B6278D5A8` (`classroom_id`),
  ADD KEY `IDX_3DD26E1BCB944F1A` (`student_id`);

--
-- Indexes for table `classroom_teacher`
--
ALTER TABLE `classroom_teacher`
  ADD PRIMARY KEY (`classroom_id`,`teacher_id`),
  ADD KEY `IDX_3A0767FD6278D5A8` (`classroom_id`),
  ADD KEY `IDX_3A0767FD41807E1D` (`teacher_id`);

--
-- Indexes for table `doctrine_migration_versions`
--
ALTER TABLE `doctrine_migration_versions`
  ADD PRIMARY KEY (`version`);

--
-- Indexes for table `pass`
--
ALTER TABLE `pass`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_CE70D424CB944F1A` (`student_id`),
  ADD KEY `IDX_CE70D424CE07E8FF` (`questionnaire_id`);

--
-- Indexes for table `proposition`
--
ALTER TABLE `proposition`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_C7CDC3531E27F6BF` (`question_id`);

--
-- Indexes for table `question`
--
ALTER TABLE `question`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_B6F7494ECE07E8FF` (`questionnaire_id`);

--
-- Indexes for table `questionnaire`
--
ALTER TABLE `questionnaire`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_7A64DAF41807E1D` (`teacher_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_8D93D649F85E0677` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `classroom`
--
ALTER TABLE `classroom`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `pass`
--
ALTER TABLE `pass`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `proposition`
--
ALTER TABLE `proposition`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `question`
--
ALTER TABLE `question`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `questionnaire`
--
ALTER TABLE `questionnaire`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `classroom_student`
--
ALTER TABLE `classroom_student`
  ADD CONSTRAINT `FK_3DD26E1B6278D5A8` FOREIGN KEY (`classroom_id`) REFERENCES `classroom` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_3DD26E1BCB944F1A` FOREIGN KEY (`student_id`) REFERENCES `user` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `classroom_teacher`
--
ALTER TABLE `classroom_teacher`
  ADD CONSTRAINT `FK_3A0767FD41807E1D` FOREIGN KEY (`teacher_id`) REFERENCES `user` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_3A0767FD6278D5A8` FOREIGN KEY (`classroom_id`) REFERENCES `classroom` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `pass`
--
ALTER TABLE `pass`
  ADD CONSTRAINT `FK_CE70D424CB944F1A` FOREIGN KEY (`student_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `FK_CE70D424CE07E8FF` FOREIGN KEY (`questionnaire_id`) REFERENCES `questionnaire` (`id`);

--
-- Constraints for table `proposition`
--
ALTER TABLE `proposition`
  ADD CONSTRAINT `FK_C7CDC3531E27F6BF` FOREIGN KEY (`question_id`) REFERENCES `question` (`id`);

--
-- Constraints for table `question`
--
ALTER TABLE `question`
  ADD CONSTRAINT `FK_B6F7494ECE07E8FF` FOREIGN KEY (`questionnaire_id`) REFERENCES `questionnaire` (`id`);

--
-- Constraints for table `questionnaire`
--
ALTER TABLE `questionnaire`
  ADD CONSTRAINT `FK_7A64DAF41807E1D` FOREIGN KEY (`teacher_id`) REFERENCES `user` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

