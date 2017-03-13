-- phpMyAdmin SQL Dump
-- version 3.3.9.2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jul 07, 2011 at 04:27 PM
-- Server version: 5.1.56
-- PHP Version: 5.3.6

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `ReConsider_Dev`
--

-- --------------------------------------------------------

--
-- Table structure for table `claimState`
--

CREATE TABLE IF NOT EXISTS `claimState` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `disputeID` int(11) DEFAULT NULL,
  `userID` int(11) DEFAULT NULL,
  `nodeID` int(11) DEFAULT NULL,
  `claimID` int(11) DEFAULT NULL,
  `status` varchar(10) DEFAULT NULL,
  `inferedClaimID` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=11 ;

