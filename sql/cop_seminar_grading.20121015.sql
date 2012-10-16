-- phpMyAdmin SQL Dump
-- version 3.3.10.4
-- http://www.phpmyadmin.net
--
-- Host: db.cop.toddholmberg.net
-- Generation Time: Oct 15, 2012 at 09:14 PM
-- Server version: 5.1.53
-- PHP Version: 5.3.13

SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT=0;
START TRANSACTION;


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `cop_seminar_grading`
--

-- --------------------------------------------------------

--
-- Table structure for table `academic_year`
--

CREATE TABLE IF NOT EXISTS `academic_year` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `year` int(4) NOT NULL,
  `current` tinyint(1) NOT NULL COMMENT 'Current academic year',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

--
-- Dumping data for table `academic_year`
--

INSERT INTO `academic_year` (`id`, `year`, `current`) VALUES
(1, 2012, 1);

-- --------------------------------------------------------

--
-- Table structure for table `academic_year__p_year__section`
--

CREATE TABLE IF NOT EXISTS `academic_year__p_year__section` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `academic_year_id` int(11) NOT NULL,
  `p_year_id` int(11) NOT NULL,
  `section_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `academic_year_id` (`academic_year_id`,`p_year_id`,`section_id`),
  KEY `p_year_id` (`p_year_id`),
  KEY `section_id` (`section_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;

--
-- Dumping data for table `academic_year__p_year__section`
--

INSERT INTO `academic_year__p_year__section` (`id`, `academic_year_id`, `p_year_id`, `section_id`) VALUES
(1, 1, 3, 1),
(2, 1, 3, 2),
(3, 1, 3, 3),
(4, 1, 3, 4),
(5, 1, 3, 5),
(6, 1, 4, 1),
(7, 1, 4, 2),
(8, 1, 4, 3),
(9, 1, 4, 4),
(10, 1, 4, 5);

-- --------------------------------------------------------

--
-- Table structure for table `p_year`
--

CREATE TABLE IF NOT EXISTS `p_year` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `p` enum('1','2','3','4') NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;

--
-- Dumping data for table `p_year`
--

INSERT INTO `p_year` (`id`, `p`) VALUES
(1, '1'),
(2, '2'),
(3, '3'),
(4, '4');

-- --------------------------------------------------------

--
-- Table structure for table `role`
--

CREATE TABLE IF NOT EXISTS `role` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`title`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=4 ;

--
-- Dumping data for table `role`
--

INSERT INTO `role` (`id`, `title`) VALUES
(1, 'Admin'),
(3, 'Faculty'),
(2, 'Student');

-- --------------------------------------------------------

--
-- Table structure for table `score`
--

CREATE TABLE IF NOT EXISTS `score` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `seminar_id` int(11) NOT NULL,
  `grader_user_id` int(11) NOT NULL,
  `prep` float NOT NULL,
  `prof` float NOT NULL,
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `seminar_id` (`seminar_id`,`grader_user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;


-- --------------------------------------------------------

--
-- Table structure for table `section`
--

CREATE TABLE IF NOT EXISTS `section` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `number` enum('1','2','3','4','5') COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=6 ;

--
-- Dumping data for table `section`
--

INSERT INTO `section` (`id`, `number`) VALUES
(1, '1'),
(2, '2'),
(3, '3'),
(4, '4'),
(5, '5');

-- --------------------------------------------------------

--
-- Table structure for table `seminar`
--

CREATE TABLE IF NOT EXISTS `seminar` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `presenter_user_section_id` int(11) NOT NULL,
  `prep` int(11) NOT NULL DEFAULT '0',
  `prof` int(11) NOT NULL DEFAULT '0',
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `presenter_user_section_id` (`presenter_user_section_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Seminar data. Maps to section defined in academic_year__p_ye' ;


-- --------------------------------------------------------

--
-- Table structure for table `survey`
--

CREATE TABLE IF NOT EXISTS `survey` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `seminar_id` int(11) NOT NULL,
  `reviewer_user_section_id` int(11) NOT NULL,
  `survey_date` datetime NOT NULL,
  `qualtrics_id` varchar(30) NOT NULL COMMENT 'Qualtrics ResponseID',
  `grade_sum` float NOT NULL,
  `grade_weighted_average` float NOT NULL,
  `grade_standard_deviation` float NOT NULL,
  `ps_pace` tinyint(1) NOT NULL,
  `ps_eyecontact` tinyint(1) NOT NULL,
  `ps_professionalism` tinyint(1) NOT NULL,
  `ps_materials` tinyint(1) NOT NULL,
  `ps_comments` text NOT NULL,
  `im_handouts` tinyint(1) NOT NULL,
  `im_grammar` tinyint(1) NOT NULL,
  `im_charts` tinyint(1) NOT NULL,
  `im_cites` tinyint(1) NOT NULL,
  `im_comments` text NOT NULL,
  `op_introduction` tinyint(1) NOT NULL,
  `op_purpose` tinyint(1) NOT NULL,
  `op_objectives` tinyint(1) NOT NULL,
  `op_background` tinyint(1) NOT NULL,
  `op_organization` tinyint(1) NOT NULL,
  `op_comments` text NOT NULL,
  `cd_objectives` tinyint(1) NOT NULL,
  `cd_outcome` tinyint(1) NOT NULL,
  `cd_analysis` tinyint(1) NOT NULL,
  `cd_samplesize` tinyint(1) NOT NULL,
  `cd_withdrawals` tinyint(1) NOT NULL,
  `cd_details` tinyint(1) NOT NULL,
  `cd_comments` text NOT NULL,
  `cc_data` tinyint(1) NOT NULL,
  `cc_importance` tinyint(1) NOT NULL,
  `cc_recommendations` tinyint(1) NOT NULL,
  `cc_role` tinyint(1) NOT NULL,
  `cc_comments` tinyint(1) NOT NULL,
  `qa_answers` tinyint(1) NOT NULL,
  `qa_interaction` tinyint(1) NOT NULL,
  `qa_comments` text NOT NULL,
  `ok_demonstrated` tinyint(1) NOT NULL,
  `ok_difference` tinyint(1) NOT NULL,
  `ok_deep` tinyint(1) NOT NULL,
  `ok_discussion` tinyint(1) NOT NULL,
  `ok_think` tinyint(1) NOT NULL,
  `ok_comments` text NOT NULL,
  `comments_like` text NOT NULL,
  `comments_improve` text NOT NULL,
  `comments_overall` text NOT NULL,
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `seminar_id` (`seminar_id`),
  KEY `reviewer_user_section_id` (`reviewer_user_section_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;


-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `unid` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `archive` tinyint(1) NOT NULL DEFAULT '0',
  `first_name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `last_name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_date` datetime DEFAULT NULL,
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unid` (`unid`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

--
-- Insert admin user
--
INSERT INTO `user` (`unid`, `archive`, `first_name`, `last_name`, `email`, `created_date`) VALUES
('u0615627', 0, 'Gisel', 'Gomez', 'gisel.gomez@pharm.utah.edu', '2012-10-16 08:00:00');

-- --------------------------------------------------------

--
-- Stand-in structure for view `userAuthData`
--
CREATE TABLE IF NOT EXISTS `userAuthData` (
`unid` varchar(20)
,`archive` tinyint(1)
,`first_name` varchar(100)
,`last_name` varchar(100)
,`email` varchar(100)
,`role_id` int(11)
,`role_title` varchar(20)
);
-- --------------------------------------------------------

--
-- Table structure for table `user__role`
--

CREATE TABLE IF NOT EXISTS `user__role` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL DEFAULT '2',
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id_role_id` (`user_id`,`role_id`),
  KEY `role_id` (`role_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

--
-- Insert admin user for table `user__role`
--

INSERT INTO `user__role` (`user_id`, `role_id`) VALUES
(1, 1);


-- --------------------------------------------------------

--
-- Table structure for table `user__section`
--

CREATE TABLE IF NOT EXISTS `user__section` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `section_id` int(11) NOT NULL COMMENT 'id from academic_year__p_year__section',
  `is_grader` tinyint(1) NOT NULL DEFAULT '0',
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id_section_id` (`user_id`,`section_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

-- --------------------------------------------------------

--
-- Structure for view `userAuthData`
--
DROP TABLE IF EXISTS `userAuthData`;

CREATE ALGORITHM=UNDEFINED DEFINER=`cop_admin`@`173.236.128.0/255.255.128.0` SQL SECURITY DEFINER VIEW `userAuthData` AS select `user`.`unid` AS `unid`,`user`.`archive` AS `archive`,`user`.`first_name` AS `first_name`,`user`.`last_name` AS `last_name`,`user`.`email` AS `email`,`role`.`id` AS `role_id`,`role`.`title` AS `role_title` from ((`user` left join `user__role` on((`user`.`id` = `user__role`.`user_id`))) left join `role` on((`role`.`id` = `user__role`.`role_id`)));

--
-- Constraints for dumped tables
--

--
-- Constraints for table `academic_year__p_year__section`
--
ALTER TABLE `academic_year__p_year__section`
  ADD CONSTRAINT `academic_year__p_year__section_ibfk_4` FOREIGN KEY (`section_id`) REFERENCES `section` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `academic_year__p_year__section_ibfk_3` FOREIGN KEY (`p_year_id`) REFERENCES `p_year` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `seminar`
--
ALTER TABLE `seminar`
  ADD CONSTRAINT `seminar_ibfk_1` FOREIGN KEY (`presenter_user_section_id`) REFERENCES `user__section` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `user__role`
--
ALTER TABLE `user__role`
  ADD CONSTRAINT `user__role_ibfk_7` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `user__role_ibfk_8` FOREIGN KEY (`role_id`) REFERENCES `role` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `user__section`
--
ALTER TABLE `user__section`
  ADD CONSTRAINT `user__section_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
SET FOREIGN_KEY_CHECKS=1;
COMMIT;
