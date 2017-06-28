
-- --------------------------------------------------------

--
-- Struktura tabulky `cinemas`
--

DROP TABLE IF EXISTS `cinemas`;
CREATE TABLE IF NOT EXISTS `cinemas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8_bin NOT NULL,
  `short_name` varchar(20) COLLATE utf8_bin NOT NULL,
  `type` enum('classic','multiplex','summer') COLLATE utf8_bin NOT NULL DEFAULT 'classic',
  `address` varchar(100) COLLATE utf8_bin DEFAULT NULL,
  `city` varchar(100) COLLATE utf8_bin NOT NULL DEFAULT 'Brno',
  `phone` varchar(100) COLLATE utf8_bin DEFAULT NULL,
  `email` varchar(100) COLLATE utf8_bin DEFAULT NULL,
  `url` varchar(1000) COLLATE utf8_bin DEFAULT NULL,
  `gmaps` varchar(1000) COLLATE utf8_bin DEFAULT NULL,
  `programme` varchar(100) COLLATE utf8_bin DEFAULT NULL,
  `facebook` varchar(100) COLLATE utf8_bin DEFAULT NULL,
  `google+` varchar(100) COLLATE utf8_bin DEFAULT NULL,
  `instagram` varchar(100) COLLATE utf8_bin DEFAULT NULL,
  `twitter` varchar(100) COLLATE utf8_bin DEFAULT NULL,
  `active_since` date DEFAULT NULL,
  `active_until` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
