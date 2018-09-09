-- Adminer 4.3.1 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

-- CREATE DATABASE `zitkino` /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_czech_ci */;
-- USE `zitkino`;

DROP TABLE IF EXISTS `cinemas`;
CREATE TABLE `cinemas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(20) COLLATE utf8_bin NOT NULL,
  `name` varchar(255) COLLATE utf8_bin NOT NULL,
  `type` int(11) DEFAULT NULL,
  `address` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `city` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT 'Brno',
  `phone` varchar(100) COLLATE utf8_bin DEFAULT NULL,
  `email` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `url` varchar(1000) COLLATE utf8_bin DEFAULT NULL,
  `gmaps` varchar(1000) COLLATE utf8_bin DEFAULT NULL,
  `programme` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `facebook` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `googlePlus` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `instagram` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `twitter` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `active_since` date DEFAULT NULL,
  `active_until` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  UNIQUE KEY `code` (`code`),
  KEY `type` (`type`),
  CONSTRAINT `cinemas_ibfk_1` FOREIGN KEY (`type`) REFERENCES `cinemas_types` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;


DROP TABLE IF EXISTS `cinemas_types`;
CREATE TABLE `cinemas_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `name` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


DROP TABLE IF EXISTS `languages`;
CREATE TABLE `languages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(10) COLLATE utf8_czech_ci NOT NULL,
  `czech` varchar(10) COLLATE utf8_czech_ci DEFAULT NULL,
  `english` varchar(10) COLLATE utf8_czech_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


DROP TABLE IF EXISTS `movies`;
CREATE TABLE `movies` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `length` int(11) DEFAULT NULL,
  `csfd` varchar(255) COLLATE utf8_czech_ci DEFAULT NULL,
  `imdb` varchar(255) COLLATE utf8_czech_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


DROP TABLE IF EXISTS `screenings`;
CREATE TABLE `screenings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `movie` int(11) NOT NULL,
  `cinema` int(11) NOT NULL,
  `type` int(11) DEFAULT NULL,
  `dubbing` int(11) DEFAULT NULL,
  `subtitles` int(11) DEFAULT NULL,
  `price` int(11) DEFAULT NULL,
  `link` varchar(1000) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `movie` (`movie`),
  KEY `cinema` (`cinema`),
  KEY `type` (`type`),
  KEY `dubbing` (`dubbing`),
  KEY `subtitles` (`subtitles`),
  CONSTRAINT `screenings_ibfk_1` FOREIGN KEY (`movie`) REFERENCES `movies` (`id`),
  CONSTRAINT `screenings_ibfk_2` FOREIGN KEY (`cinema`) REFERENCES `cinemas` (`id`),
  CONSTRAINT `screenings_ibfk_3` FOREIGN KEY (`type`) REFERENCES `screenings_types` (`id`),
  CONSTRAINT `screenings_ibfk_4` FOREIGN KEY (`dubbing`) REFERENCES `languages` (`id`),
  CONSTRAINT `screenings_ibfk_5` FOREIGN KEY (`subtitles`) REFERENCES `languages` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;


DROP TABLE IF EXISTS `screenings_types`;
CREATE TABLE `screenings_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(10) COLLATE utf8_czech_ci NOT NULL,
  `name` varchar(255) COLLATE utf8_czech_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


DROP TABLE IF EXISTS `showtimes`;
CREATE TABLE `showtimes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `screening` int(11) NOT NULL,
  `datetime` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `screening` (`screening`),
  CONSTRAINT `showtimes_ibfk_1` FOREIGN KEY (`screening`) REFERENCES `screenings` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


-- 2018-09-05 23:16:56
