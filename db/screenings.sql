
DROP TABLE IF EXISTS `screenings`;
CREATE TABLE IF NOT EXISTS `screenings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `movie` int(11) DEFAULT NULL,
  `cinema` int(11) DEFAULT NULL,
  `type` enum('3D') COLLATE utf8_bin DEFAULT NULL,
  `language` int(11) DEFAULT NULL,
  `subtitles` int(11) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `time` time DEFAULT NULL,
  `price` int(11) DEFAULT NULL,
  `link` varchar(1000) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `movie` (`movie`),
  KEY `cinema` (`cinema`),
  KEY `language` (`language`),
  KEY `subtitles` (`subtitles`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
