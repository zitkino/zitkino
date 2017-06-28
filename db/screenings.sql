
-- --------------------------------------------------------

--
-- Struktura tabulky `screenings`
--

DROP TABLE IF EXISTS `screenings`;
CREATE TABLE IF NOT EXISTS `screenings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `movie` int(11) DEFAULT NULL,
  `cinema` int(11) DEFAULT NULL,
  `type` enum('3D') COLLATE utf8_bin DEFAULT NULL,
  `language` varchar(100) COLLATE utf8_bin DEFAULT NULL,
  `subtitles` varchar(100) COLLATE utf8_bin DEFAULT NULL,
  `date` date DEFAULT NULL,
  `time` time DEFAULT NULL,
  `price` int(11) DEFAULT NULL,
  `link` varchar(1000) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
