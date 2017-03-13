--
-- Table structure for table `outcomes`
--

CREATE TABLE IF NOT EXISTS `outcomes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `disputeID` int(11) DEFAULT NULL,
  `summary` mediumblob,
  `result` longblob,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
