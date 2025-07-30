-- Adminer 4.8.1 MySQL 5.5.5-10.4.34-MariaDB-log dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

SET NAMES utf8mb4;

DROP TABLE IF EXISTS `zk_cinemas`;
CREATE TABLE `zk_cinemas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(20) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `city` varchar(255) NOT NULL DEFAULT 'Brno',
  `phone` varchar(100) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `url` varchar(1000) DEFAULT NULL,
  `gmaps` varchar(1000) DEFAULT NULL,
  `programme` varchar(255) DEFAULT NULL,
  `facebook` varchar(255) DEFAULT NULL,
  `googlePlus` varchar(255) DEFAULT NULL,
  `instagram` varchar(255) DEFAULT NULL,
  `twitter` varchar(255) DEFAULT NULL,
  `active_since` date DEFAULT NULL,
  `active_until` date DEFAULT NULL,
  `parsable` tinyint(1) NOT NULL DEFAULT 0,
  `parsing` varchar(255) DEFAULT NULL,
  `parsed` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  UNIQUE KEY `code` (`code`),
  KEY `type` (`type`),
  CONSTRAINT `FK_1A6B67D08CDE5729` FOREIGN KEY (`type`) REFERENCES `zk_cinemas_types` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_czech_ci;


DROP TABLE IF EXISTS `zk_cinemas_types`;
CREATE TABLE `zk_cinemas_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(191) NOT NULL,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_czech_ci;


DROP TABLE IF EXISTS `zk_languages`;
CREATE TABLE `zk_languages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(10) NOT NULL,
  `czech` varchar(10) DEFAULT NULL,
  `english` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_czech_ci;


DROP TABLE IF EXISTS `zk_movies`;
CREATE TABLE `zk_movies` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `length` int(11) DEFAULT NULL,
  `csfd` varchar(255) DEFAULT NULL,
  `imdb` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=4271 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_czech_ci;


DROP TABLE IF EXISTS `zk_places`;
CREATE TABLE `zk_places` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cinema` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `link` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cinema` (`cinema`),
  CONSTRAINT `zk_places_ibfk_1` FOREIGN KEY (`cinema`) REFERENCES `zk_cinemas` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=346 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_czech_ci;


DROP TABLE IF EXISTS `zk_screenings`;
CREATE TABLE `zk_screenings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `movie` int(11) NOT NULL,
  `cinema` int(11) NOT NULL,
  `type` int(11) DEFAULT 2,
  `place` int(11) DEFAULT NULL,
  `dubbing` varchar(255) DEFAULT NULL,
  `subtitles` varchar(255) DEFAULT NULL,
  `price` int(11) DEFAULT NULL,
  `link` varchar(1000) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_EBDE1F11741D53CD` (`place`),
  KEY `movie` (`movie`),
  KEY `cinema` (`cinema`),
  KEY `type` (`type`),
  CONSTRAINT `FK_EBDE1F111D5EF26F` FOREIGN KEY (`movie`) REFERENCES `zk_movies` (`id`),
  CONSTRAINT `FK_EBDE1F11741D53CD` FOREIGN KEY (`place`) REFERENCES `zk_places` (`id`),
  CONSTRAINT `FK_EBDE1F118CDE5729` FOREIGN KEY (`type`) REFERENCES `zk_screenings_types` (`id`),
  CONSTRAINT `FK_EBDE1F11D48304B4` FOREIGN KEY (`cinema`) REFERENCES `zk_cinemas` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4514189 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_czech_ci;


DROP TABLE IF EXISTS `zk_screenings_types`;
CREATE TABLE `zk_screenings_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(191) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=85 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_czech_ci;


DROP TABLE IF EXISTS `zk_showtimes`;
CREATE TABLE `zk_showtimes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `screening` int(11) NOT NULL,
  `datetime` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `screening` (`screening`),
  CONSTRAINT `zk_showtimes_ibfk_1` FOREIGN KEY (`screening`) REFERENCES `zk_screenings` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5632354 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- 2025-07-30 07:37:03
