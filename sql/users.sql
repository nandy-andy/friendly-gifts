--
-- Table structure for table `xg_users`
--

CREATE TABLE IF NOT EXISTS `xg_users` (
  `id` int(3) NOT NULL AUTO_INCREMENT,
  `login` varchar(24) NOT NULL,
  `password` varchar(32) NOT NULL,
  `name` varchar(64) NOT NULL,
  `drawn` int(3) DEFAULT NULL,
  `drawn_by` int(3) DEFAULT NULL,
  `drawn_on` timestamp DEFAULT '0000-00-00 00:00:00',
  `drawn_by_on` timestamp DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE drawn_idx (`drawn`),
  UNIQUE drawn_by_idx (`drawn_by`),
  FOREIGN KEY (`drawn`) REFERENCES xg_users(id) ON DELETE SET NULL ON UPDATE SET NULL,
  FOREIGN KEY (`drawn_by`) REFERENCES xg_users(id) ON DELETE SET NULL ON UPDATE SET NULL
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
