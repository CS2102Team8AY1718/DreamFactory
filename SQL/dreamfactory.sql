-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Feb 08, 2018 at 01:20 PM
-- Server version: 5.7.19
-- PHP Version: 5.6.31

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `dreamfactory`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
CREATE TABLE IF NOT EXISTS `categories` (
  `category` varchar(32) NOT NULL,
  PRIMARY KEY (`category`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `fundings`
--

DROP TABLE IF EXISTS `fundings`;
CREATE TABLE IF NOT EXISTS `fundings` (
  `funder_id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `amount` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`funder_id`,`project_id`),
  KEY `funder_id` (`funder_id`),
  KEY `project_id` (`project_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Triggers `fundings`
--
DROP TRIGGER IF EXISTS `trigger_before_delete_fundings`;
DELIMITER $$
CREATE TRIGGER `trigger_before_delete_fundings` BEFORE DELETE ON `fundings` FOR EACH ROW BEGIN
	UPDATE projects SET funding_amount = funding_amount - OLD.amount WHERE project_id = OLD.project_id;
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `trigger_before_insert_fundings`;
DELIMITER $$
CREATE TRIGGER `trigger_before_insert_fundings` BEFORE INSERT ON `fundings` FOR EACH ROW BEGIN
	UPDATE projects SET funding_amount = funding_amount + NEW.amount WHERE project_id = NEW.project_id;
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `trigger_before_update_fundings`;
DELIMITER $$
CREATE TRIGGER `trigger_before_update_fundings` BEFORE UPDATE ON `fundings` FOR EACH ROW BEGIN
	UPDATE projects SET funding_amount = funding_amount - OLD.amount + NEW.amount WHERE project_id = NEW.project_id;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `keywords`
--

DROP TABLE IF EXISTS `keywords`;
CREATE TABLE IF NOT EXISTS `keywords` (
  `keyword` varchar(32) NOT NULL,
  PRIMARY KEY (`keyword`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

DROP TABLE IF EXISTS `projects`;
CREATE TABLE IF NOT EXISTS `projects` (
  `project_id` int(11) NOT NULL AUTO_INCREMENT,
  `creator_id` int(11) NOT NULL,
  `title` varchar(256) NOT NULL,
  `image_url` varchar(256) DEFAULT NULL,
  `description` varchar(1024) NOT NULL,
  `start_datetime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `duration` int(11) NOT NULL,
  `category` varchar(32) NOT NULL,
  `funding_goal` int(11) NOT NULL,
  `funding_amount` int(11) NOT NULL,
  `is_funded` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`project_id`),
  UNIQUE KEY `title` (`title`),
  KEY `category` (`category`),
  KEY `creator_id` (`creator_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Triggers `projects`
--
DROP TRIGGER IF EXISTS `trigger_is_funded`;
DELIMITER $$
CREATE TRIGGER `trigger_is_funded` BEFORE UPDATE ON `projects` FOR EACH ROW BEGIN
	SET NEW.is_funded = NEW.funding_amount >= NEW.funding_goal;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `project_keywords`
--

DROP TABLE IF EXISTS `project_keywords`;
CREATE TABLE IF NOT EXISTS `project_keywords` (
  `project_id` int(11) NOT NULL,
  `keyword` varchar(32) NOT NULL,
  PRIMARY KEY (`project_id`,`keyword`),
  KEY `project_id` (`project_id`),
  KEY `keyword` (`keyword`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `password_hash` varchar(64) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `privilege` tinyint(1) NOT NULL DEFAULT '0',
  `session_id` varchar(32) DEFAULT NULL,
  `session_exp` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `fundings`
--
ALTER TABLE `fundings`
  ADD CONSTRAINT `fk_fundings_projects_project_id` FOREIGN KEY (`project_id`) REFERENCES `projects` (`project_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_fundings_users_funder_id` FOREIGN KEY (`funder_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `projects`
--
ALTER TABLE `projects`
  ADD CONSTRAINT `fk_projects_categories_category` FOREIGN KEY (`category`) REFERENCES `categories` (`category`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_projects_users_creator_id` FOREIGN KEY (`creator_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `project_keywords`
--
ALTER TABLE `project_keywords`
  ADD CONSTRAINT `fk_project_keywords_keywords_keyword` FOREIGN KEY (`keyword`) REFERENCES `keywords` (`keyword`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_project_keywords_projects_project_id` FOREIGN KEY (`project_id`) REFERENCES `projects` (`project_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
