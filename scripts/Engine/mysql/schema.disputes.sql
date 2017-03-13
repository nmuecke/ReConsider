--
-- Table structure for table `disputes`
--

CREATE TABLE IF NOT EXISTS `disputes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `disputeType` varchar(50) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `outcomeID` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
