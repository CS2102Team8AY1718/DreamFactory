-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Apr 10, 2018 at 03:16 PM
-- Server version: 5.7.21-log
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

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`category`) VALUES
('Business'),
('Ecommerce'),
('Education'),
('Entertainment'),
('Financials'),
('Food'),
('Gaming'),
('Media'),
('Social Network'),
('Technology'),
('Telecommunication'),
('Travel'),
('Utilities');

-- --------------------------------------------------------

--
-- Table structure for table `fundings`
--

DROP TABLE IF EXISTS `fundings`;
CREATE TABLE IF NOT EXISTS `fundings` (
  `user_id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `amount` int(11) UNSIGNED NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`,`project_id`),
  KEY `user_id` (`user_id`),
  KEY `project_id` (`project_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `fundings`
--

INSERT INTO `fundings` (`user_id`, `project_id`, `amount`, `timestamp`) VALUES
(1, 1, 2000, '2018-04-09 11:57:28'),
(1, 12, 34000, '2018-04-10 07:09:26'),
(5, 1, 12000, '2018-04-09 13:01:53'),
(6, 3, 25000, '2018-04-09 12:48:08'),
(7, 2, 9000, '2018-04-09 13:02:41'),
(7, 4, 4000, '2018-04-09 12:46:42'),
(8, 4, 34000, '2018-04-09 13:03:51'),
(9, 7, 7000, '2018-04-09 12:45:15'),
(9, 10, 5000, '2018-04-09 13:05:41'),
(10, 2, 2000, '2018-04-09 13:02:31'),
(11, 6, 7500, '2018-04-09 12:47:34'),
(14, 10, 230, '2018-04-09 13:04:15'),
(18, 10, 10000, '2018-04-09 13:04:55'),
(20, 2, 4500, '2018-04-09 13:04:28');

-- --------------------------------------------------------

--
-- Table structure for table `keywords`
--

DROP TABLE IF EXISTS `keywords`;
CREATE TABLE IF NOT EXISTS `keywords` (
  `keyword` varchar(32) NOT NULL,
  PRIMARY KEY (`keyword`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `keywords`
--

INSERT INTO `keywords` (`keyword`) VALUES
('app'),
('coding'),
('computer'),
('dating'),
('delivery'),
('expert'),
('final'),
('fire'),
('food'),
('funding'),
('game'),
('getjar'),
('knowledge'),
('loop'),
('money'),
('music'),
('project'),
('score'),
('sky'),
('song'),
('travel');

-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

DROP TABLE IF EXISTS `projects`;
CREATE TABLE IF NOT EXISTS `projects` (
  `project_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `title` varchar(256) NOT NULL,
  `image_url` varchar(256) DEFAULT NULL,
  `description` varchar(1024) NOT NULL,
  `end_datetime` datetime NOT NULL,
  `category` varchar(32) NOT NULL,
  `funding_goal` int(11) NOT NULL,
  PRIMARY KEY (`project_id`),
  UNIQUE KEY `title` (`title`),
  KEY `category` (`category`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `projects`
--

INSERT INTO `projects` (`project_id`, `user_id`, `title`, `image_url`, `description`, `end_datetime`, `category`, `funding_goal`) VALUES
(1, 3, 'Moolah', 'https://www.shareicon.net/data/128x128/2016/06/30/788748_notes_512x512.png', 'Financing company', '2018-02-28 00:00:00', 'Financials', 12000),
(2, 5, 'Skyrocket', 'https://i.pinimg.com/564x/6d/a8/13/6da813bbec754b95ba5bb72b1a61eca9.jpg', 'Educating business', '2018-03-10 00:00:00', 'Education', 10000),
(3, 4, 'Gimmefood', 'https://www.shareicon.net/download/2016/09/02/824429_fork_512x512.png', 'Food Delivery Company', '2018-12-31 00:00:00', 'Food', 340000),
(4, 6, 'Helloworld', 'http://icons.iconarchive.com/icons/dtafalonso/modern-xp/256/ModernXP-73-Globe-icon.png', 'Coding class start up', '2018-02-28 00:00:00', 'Education', 30000),
(5, 7, 'Mixcloud', 'http://icons.iconarchive.com/icons/dtafalonso/yosemite-flat/512/Music-icon.png', 'Music record start up', '2018-04-03 00:00:00', 'Entertainment', 70000),
(6, 8, 'Dating Life', 'https://ih1.redbubble.net/image.30156406.3433/sticker,375x360-bg,ffffff.png', 'Dating enhancement application the start up is focusing on', '2018-12-28 00:00:00', 'Social Network', 50000),
(7, 9, 'Foodies Deliver', 'https://www.shareicon.net/download/2016/11/25/856584_food_512x512.png', 'Start up doing food delivery services', '2018-12-19 00:00:00', 'Food', 60000),
(8, 1, 'Crossknowledge', 'https://png.icons8.com/ios/1600/knowledge-sharing.png', 'Education start up', '2018-06-28 00:00:00', 'Education', 37000),
(9, 10, 'Funding Circle', 'https://labs.robinhood.org/wp-content/uploads/2015/07/FUNDING.png?x94397', 'VC company for smaller entreupreneurs', '2018-12-17 00:00:00', 'Financials', 57000),
(10, 11, 'Songkick', 'https://png.pngtree.com/element_origin_min_pic/16/08/30/2057c5817bcf859.jpg', 'Music industry entertainment', '2018-01-03 00:00:00', 'Entertainment', 12000),
(11, 16, 'Songtify', 'https://upload.wikimedia.org/wikipedia/en/thumb/a/af/Song_logo.svg/1200px-Song_logo.svg.png', 'Music streaming services company', '2018-12-27 00:00:00', 'Media', 80000),
(12, 19, 'Experteer', 'https://www.shareicon.net/download/2016/07/03/790220_school_512x512.png', 'Education that offers any course.', '2019-04-26 00:00:00', 'Education', 124000),
(13, 5, 'GetJar', 'https://png.icons8.com/color/1600/cash-in-hand.png', 'Saving investments for you', '2019-04-20 00:00:00', 'Financials', 65000),
(14, 8, 'Game3D', 'https://png.pngtree.com/element_pic/16/11/22/56551424a96d8b34d760f5c4fc338e07.jpg', 'Gaming VR company', '2020-04-12 00:00:00', 'Gaming', 140000),
(15, 6, 'Scoreloop', 'https://cdn4.iconfinder.com/data/icons/flat-icon-set/2133/flat_icons-graficheria.it-13.png', 'Score in our games', '2018-09-08 00:00:00', 'Gaming', 48000),
(16, 7, 'Appsfire', 'http://www.clker.com/cliparts/1/0/9/e/1487324295866254597fire-vector-icon-png-27.hi.png', 'Social media', '2018-10-25 00:00:00', 'Social Network', 9300),
(17, 21, 'Skyscanner', 'https://cdn.iconscout.com/public/images/icon/free/png-512/aeroplane-airplane-plane-air-transportation-vehicle-pessanger-people-emoj-symbol-3306ff886517b0e9-512x512.png', 'Travelling company', '2018-08-18 00:00:00', 'Travel', 9000);

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

--
-- Dumping data for table `project_keywords`
--

INSERT INTO `project_keywords` (`project_id`, `keyword`) VALUES
(1, 'money'),
(2, 'money'),
(3, 'delivery'),
(3, 'food'),
(4, 'coding'),
(4, 'computer'),
(5, 'coding'),
(5, 'computer'),
(6, 'dating'),
(7, 'delivery'),
(7, 'food'),
(8, 'knowledge'),
(9, 'funding'),
(10, 'music'),
(10, 'song'),
(11, 'song'),
(12, 'expert'),
(13, 'getjar'),
(14, 'game'),
(15, 'loop'),
(15, 'score'),
(16, 'app'),
(16, 'fire'),
(17, 'sky'),
(17, 'travel');

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
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `email`, `password_hash`, `full_name`, `privilege`, `session_id`, `session_exp`) VALUES
(1, 'afi@cat.com', '35932720f27c684829c6b308235f47fb3394456793fce2a61deb418058b8cf7c', 'afi', 0, NULL, NULL),
(2, 'admin@admin.com', 'password', 'admin', 1, NULL, NULL),
(3, 'email0@email.com', 'password', 'USER0', 0, NULL, NULL),
(4, 'email@email.com', '5e884898da28047151d0e56f8dc6292773603d0d6aabbdd62a11ef721d1542d8', 'USER1', 0, NULL, NULL),
(5, 'email1@email.com', '5e884898da28047151d0e56f8dc6292773603d0d6aabbdd62a11ef721d1542d8', 'USER2', 0, NULL, NULL),
(6, 'email2@email.com', '5e884898da28047151d0e56f8dc6292773603d0d6aabbdd62a11ef721d1542d8', 'USER3', 0, NULL, NULL),
(7, 'email3@email.com', '5e884898da28047151d0e56f8dc6292773603d0d6aabbdd62a11ef721d1542d8', 'USER4', 0, NULL, NULL),
(8, 'email4@email.com', '5e884898da28047151d0e56f8dc6292773603d0d6aabbdd62a11ef721d1542d8', 'USER5', 0, NULL, NULL),
(9, 'email5@email.com', '5e884898da28047151d0e56f8dc6292773603d0d6aabbdd62a11ef721d1542d8', 'USER6', 0, NULL, NULL),
(10, 'email6@email.com', '5e884898da28047151d0e56f8dc6292773603d0d6aabbdd62a11ef721d1542d8', 'USER7', 0, NULL, NULL),
(11, 'email7@email.com', '5e884898da28047151d0e56f8dc6292773603d0d6aabbdd62a11ef721d1542d8', 'USER8', 0, NULL, NULL),
(12, 'user@email.com', '5e884898da28047151d0e56f8dc6292773603d0d6aabbdd62a11ef721d1542d8', 'Amy', 0, NULL, NULL),
(13, 'user1@email.com', '5e884898da28047151d0e56f8dc6292773603d0d6aabbdd62a11ef721d1542d8', 'Betty', 0, NULL, NULL),
(14, 'candice@email.com', '5e884898da28047151d0e56f8dc6292773603d0d6aabbdd62a11ef721d1542d8', 'Candice', 0, NULL, NULL),
(15, 'danny@email.com', '5e884898da28047151d0e56f8dc6292773603d0d6aabbdd62a11ef721d1542d8', 'Danny', 0, NULL, NULL),
(16, 'elizabeth@email.com', '5e884898da28047151d0e56f8dc6292773603d0d6aabbdd62a11ef721d1542d8', 'Elizabeth', 0, NULL, NULL),
(17, 'faith@email.com', '5e884898da28047151d0e56f8dc6292773603d0d6aabbdd62a11ef721d1542d8', 'Faith', 0, NULL, NULL),
(18, 'gary@email.com', '5e884898da28047151d0e56f8dc6292773603d0d6aabbdd62a11ef721d1542d8', 'Gary', 0, NULL, NULL),
(19, 'harry@email.com', '5e884898da28047151d0e56f8dc6292773603d0d6aabbdd62a11ef721d1542d8', 'Harry', 0, NULL, NULL),
(20, 'ivy@email.com', '5e884898da28047151d0e56f8dc6292773603d0d6aabbdd62a11ef721d1542d8', 'Ivy', 0, NULL, NULL),
(21, 'june@email.com', '5e884898da28047151d0e56f8dc6292773603d0d6aabbdd62a11ef721d1542d8', 'June', 0, NULL, NULL),
(22, 'kane@email.com', '5e884898da28047151d0e56f8dc6292773603d0d6aabbdd62a11ef721d1542d8', 'Kane', 0, NULL, NULL),
(23, 'zachary@email.com', '5e884898da28047151d0e56f8dc6292773603d0d6aabbdd62a11ef721d1542d8', 'Zachary', 0, NULL, NULL),
(24, 'user100@email.com', '5e884898da28047151d0e56f8dc6292773603d0d6aabbdd62a11ef721d1542d8', 'user100', 0, NULL, NULL);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `fundings`
--
ALTER TABLE `fundings`
  ADD CONSTRAINT `fk_fundings_projects_project_id` FOREIGN KEY (`project_id`) REFERENCES `projects` (`project_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_fundings_users_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `projects`
--
ALTER TABLE `projects`
  ADD CONSTRAINT `fk_projects_categories_category` FOREIGN KEY (`category`) REFERENCES `categories` (`category`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_projects_users_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

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
