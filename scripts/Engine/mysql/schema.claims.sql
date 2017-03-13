-- phpMyAdmin SQL Dump
-- version 3.3.9.2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jun 15, 2011 at 02:12 PM
-- Server version: 5.1.56
-- PHP Version: 5.3.6

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `ReConsider_Dev`
--

-- --------------------------------------------------------

--
-- Table structure for table `claims`
--

CREATE TABLE IF NOT EXISTS `claims` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `claimID` tinyint(4) NOT NULL DEFAULT '0',
  `nodeID` varchar(50) NOT NULL DEFAULT '0',
  `counterClaimID` mediumtext,
  `value` mediumtext,
  `threshold` tinyint(4) DEFAULT NULL,
  `weight` varchar(50) DEFAULT NULL,
  `action` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=458 ;
