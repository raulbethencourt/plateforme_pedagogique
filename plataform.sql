-- phpMyAdmin SQL Dump
-- version 5.0.0
-- https://www.phpmyadmin.net/
--
-- Host: mysql
-- Generation Time: Sep 20, 2020 at 09:11 PM
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

--
-- Dumping data for table `classroom`
--

INSERT INTO `classroom` (`id`, `name`, `access_code`) VALUES
(19, 'primera clase 2.0', 'arte/escultura'),
(20, 'dl7', 'programacion web');

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
('DoctrineMigrations\\Version20200916090810', '2020-09-16 09:08:42', 1484),
('DoctrineMigrations\\Version20200918211023', '2020-09-18 23:10:38', 84),
('DoctrineMigrations\\Version20200918212454', '2020-09-18 23:25:03', 19);

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
(31, 17, 'test 2', 0),
(32, 18, 'test 2', 0),
(33, 19, 'fsgf', 0),
(34, 20, 'propsiton 2', 0),
(35, 20, 'propsition 3 la buena', 1),
(36, 21, 'respuesta 1', 0),
(37, 21, 'respuesta 2 buena', 1),
(38, 22, 'respuesta buena', 1),
(39, 22, 'mala respuesta', 0),
(40, 23, 'respuesta 1', 1),
(41, 23, 'reponse 3', 0),
(42, 25, 'respuesta 1', 1),
(43, 26, 'respu', 0),
(44, 27, 'respuesta 1', 1),
(45, 28, 'respuesta 1', 0),
(46, 28, 'respuesta 2 buena editada', 1),
(47, 28, 'repuesta 3', 0),
(52, 31, 'respuesta 1', 1),
(53, 31, 'respuesta sin sentido', 0),
(54, 32, 'respuesta 1', 0),
(55, 32, 'respuesta 2 buena', 1),
(56, 32, 'respuesta', 1),
(57, 33, 'respuesta 1', 1),
(58, 33, 'respuesta 2 mala', 0);

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
(17, 23, 'pregunta 2 de test', '6.00'),
(18, 23, 'pregunta 2 de test', '6.00'),
(19, 23, 'test question 2', '7.00'),
(20, 23, 'pregunta ultima que quiero ver que he anadido', '10.00'),
(21, 24, 'pregunta1', '7.00'),
(22, 24, 'pregunta 2', '8.00'),
(23, 24, 'pregunta de prueba nueva de prueba', '6.00'),
(24, 24, 'pregunta 4', '6.00'),
(25, 26, 'pregunta1', '5.00'),
(26, 27, 'pregunta1', '5.00'),
(27, 27, 'pregunta1', '5.00'),
(28, 27, 'pregunta 2', '9.00'),
(31, 29, 'question 1', '6.00'),
(32, 29, 'test question 2', '9.00'),
(33, 30, 'pregunta 1 de test', '5.00');

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
(23, 16, 'test questionnaire que te cagas de duro', 'moyen'),
(24, 16, 'nueva prueba', 'facile'),
(25, 16, 'test questionnaire de la hostia 2.0', 'difficile'),
(26, 16, 'test questionnaire que te cagas de duro 2.0', 'difficile'),
(27, 16, 'test questionnaire que te cagas de duro 2.0.1', 'facile'),
(29, 16, 'test questionnaire edité', 'difficile'),
(30, 17, 'primero de raul', 'facile');

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
(16, 'teacher', '[]', '$argon2id$v=19$m=65536,t=4,p=1$L+ewk49T0+6KfkVeL4fQRw$tHKuI4m9U/tcAJzeBS3QGSoVFTmKFj1Eb2bACey7hIM', NULL, NULL, 'teacher@test.mail', NULL, 0, '2020-09-16', 'teacher', NULL, NULL),
(17, 'raulbethencourt', '[]', '$argon2id$v=19$m=65536,t=4,p=1$AM6s366x/VnpCmps8j08+Q$iV/AsI3TcoxRhBGxMTCoC1P6gxCiXyoS7iYbD9hrfw0', 'Beta', 'Raul', 'raul@test.mail', NULL, 0, '2020-09-20', 'teacher', NULL, NULL);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `pass`
--
ALTER TABLE `pass`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `proposition`
--
ALTER TABLE `proposition`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- AUTO_INCREMENT for table `question`
--
ALTER TABLE `question`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `questionnaire`
--
ALTER TABLE `questionnaire`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

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

