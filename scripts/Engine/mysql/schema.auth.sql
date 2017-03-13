--
-- Table structure for table `auth`
--

CREATE TABLE IF NOT EXISTS `auth` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(30) DEFAULT NULL,
  `role` varchar(30) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `realname` varchar(50) DEFAULT NULL,
  `termsOfUse` tinyint(1) DEFAULT NULL,
  `password` mediumblob,
  `password_salt` mediumblob,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
