-- phpMyAdmin SQL Dump
-- version 5.0.0
-- https://www.phpmyadmin.net/
--
-- Host: mysql
-- Generation Time: Sep 16, 2020 at 02:52 PM
-- Server version: 10.4.14-MariaDB-1:10.4.14+maria~focal
-- PHP Version: 7.4.9

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
  `access_code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `classroom_student`
--

CREATE TABLE `classroom_student` (
  `classroom_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `classroom_teacher`
--

CREATE TABLE `classroom_teacher` (
  `classroom_id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
('DoctrineMigrations\\Version20200913094533', '2020-09-13 11:46:23', 225),
('DoctrineMigrations\\Version20200913095556', '2020-09-13 11:56:02', 14),
('DoctrineMigrations\\Version20200913100733', '2020-09-13 12:07:41', 13),
('DoctrineMigrations\\Version20200913104201', '2020-09-13 12:42:07', 13),
('DoctrineMigrations\\Version20200913112855', '2020-09-13 13:29:02', 12),
('DoctrineMigrations\\Version20200913114432', '2020-09-13 13:44:36', 12),
('DoctrineMigrations\\Version20200913114457', '2020-09-13 13:45:00', 12),
('DoctrineMigrations\\Version20200914200830', '2020-09-14 22:10:22', 143),
('DoctrineMigrations\\Version20200916090810', '2020-09-16 09:08:42', 1484);

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
(3, 6, 'respuesta evidente', 1),
(4, 6, 'respuesta sin sentido', 0),
(5, 6, 'respuesta posible pero \"no\"', 0),
(6, 7, 'facil', 1),
(7, 7, 'dificil', 0),
(8, 8, 'rreponse 1', 1),
(9, 8, 'reponse 2', 0),
(10, 8, 'reponse 3', 1),
(11, 9, 'repons 1', 0),
(12, 9, 'reponse 2', 1);

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
(6, 13, 'Pregunta dificilisima', '5.00'),
(7, 13, 'test question 2', '3.00'),
(8, 14, 'Question 1', '9.00'),
(9, 14, 'question 2', '6.00');

-- --------------------------------------------------------

--
-- Table structure for table `questionnaire`
--

CREATE TABLE `questionnaire` (
  `id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `difficulty` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `questionnaire`
--

INSERT INTO `questionnaire` (`id`, `teacher_id`, `title`, `difficulty`) VALUES
(1, 16, 'test questionnaire', 'difficile'),
(2, 16, 'test questionnaire 2', 'facile'),
(3, 16, 'test questionnaire 3', 'moyen'),
(13, 16, 'test questionnaire de la hostia 2046', 'difficile'),
(14, 16, 'questionnnaire test', 'moyen');

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
  `subject` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hooby` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `username`, `roles`, `password`, `surname`, `name`, `email`, `photo_name`, `is_verified`, `entry_date`, `type`, `subject`, `hooby`) VALUES
(14, 'admin', '[\"ROLE_ADMIN\"]', '$argon2id$v=19$m=65536,t=4,p=1$0jsuEN8/xlghcdD9tbFG9Q$xsBFVqKhI/giBH3rqsMJYqkrYyfyD5vWItbUVXReNYU', NULL, NULL, 'admin@test.mail', NULL, 0, '2020-09-16', 'user', NULL, NULL),
(15, 'student', '[]', '$argon2id$v=19$m=65536,t=4,p=1$lpjZe8b+fW3f1wWMJY5VSw$/pkoSm+PH6d/jG19hFQvH/x7dXdY2MqoJ7XMfqcc/G4', NULL, NULL, 'student@test.mail', NULL, 0, '2020-09-16', 'student', NULL, NULL),
(16, 'teacher', '[]', '$argon2id$v=19$m=65536,t=4,p=1$L+ewk49T0+6KfkVeL4fQRw$tHKuI4m9U/tcAJzeBS3QGSoVFTmKFj1Eb2bACey7hIM', NULL, NULL, 'teacher@test.mail', NULL, 0, '2020-09-16', 'teacher', NULL, NULL);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `pass`
--
ALTER TABLE `pass`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `proposition`
--
ALTER TABLE `proposition`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `question`
--
ALTER TABLE `question`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `questionnaire`
--
ALTER TABLE `questionnaire`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

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

