-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Počítač: 127.0.0.1
-- Vytvořeno: Pon 15. kvě 2017, 11:31
-- Verze serveru: 10.1.16-MariaDB
-- Verze PHP: 7.0.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Databáze: `zitkino`
--

-- --------------------------------------------------------

--
-- Struktura tabulky `cinemas`
--

CREATE TABLE IF NOT EXISTS `cinemas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8_bin DEFAULT NULL,
  `shortName` varchar(20) COLLATE utf8_bin DEFAULT NULL,
  `type` enum('classic','summer') COLLATE utf8_bin NOT NULL DEFAULT 'classic',
  `address` varchar(100) COLLATE utf8_bin DEFAULT NULL,
  `city` varchar(100) COLLATE utf8_bin NOT NULL DEFAULT 'Brno',
  `gmaps` varchar(1000) COLLATE utf8_bin DEFAULT NULL,
  `url` varchar(100) COLLATE utf8_bin DEFAULT NULL,
  `programme` varchar(100) COLLATE utf8_bin DEFAULT NULL,
  `facebook` varchar(100) COLLATE utf8_bin DEFAULT NULL,
  `twitter` varchar(100) COLLATE utf8_bin DEFAULT NULL,
  `google+` varchar(100) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
