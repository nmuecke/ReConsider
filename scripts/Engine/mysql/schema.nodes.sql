-- phpMyAdmin SQL Dump
-- version 3.3.9.2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jun 14, 2011 at 06:39 PM
-- Server version: 5.1.56
-- PHP Version: 5.3.6

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `ReConsider_Dev`
--

-- --------------------------------------------------------

--
-- Table structure for table `nodes`
--

CREATE TABLE IF NOT EXISTS `nodes` (
  `1d` int(11) NOT NULL AUTO_INCREMENT,
  `disputeType` varchar(30) DEFAULT NULL,
  `nodeID` varchar(50) DEFAULT NULL,
  `parentNodeID` varchar(50) DEFAULT NULL,
  `counterNodeID` varchar(50) DEFAULT NULL,
  `title` varchar(80) DEFAULT NULL,
  `prefix` mediumblob,
  `suffix` mediumblob,
  `relevance` mediumblob,
  `moreInfo` mediumblob,
  `action` varchar(50) DEFAULT NULL,
  `nodeType` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`1d`),
  UNIQUE KEY `nodeID` (`nodeID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
