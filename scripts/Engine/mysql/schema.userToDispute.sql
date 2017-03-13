--
-- Table structure for table `userToDispute`
--

CREATE TABLE IF NOT EXISTS `userToDispute` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `disputeID` int(11) DEFAULT NULL,
  `userID` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
