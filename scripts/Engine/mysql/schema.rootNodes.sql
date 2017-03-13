-- phpMyAdmin SQL Dump
-- version 3.3.9.2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jun 15, 2011 at 02:00 PM
-- Server version: 5.1.56
-- PHP Version: 5.3.6

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `ReConsider_Dev`
--

-- --------------------------------------------------------

--
-- Table structure for table `rootNodes`
--

CREATE TABLE IF NOT EXISTS `rootNodes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nodeID` varchar(50) NOT NULL,
  `disputeType` varchar(50) NOT NULL,
  `desctiption` varchar(200) NOT NULL,
  `label` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nodeID` (`nodeID`),
  UNIQUE KEY `disputeType` (`disputeType`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

