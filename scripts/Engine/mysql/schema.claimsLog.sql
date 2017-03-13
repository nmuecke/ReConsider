-- phpMyAdmin SQL Dump
-- version 3.3.9.2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jun 15, 2011 at 06:55 PM
-- Server version: 5.1.56
-- PHP Version: 5.3.6

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `ReConsider_Dev`
--

-- --------------------------------------------------------

--
-- Table structure for table `ClaimsLog`
--

CREATE TABLE IF NOT EXISTS `ClaimsLog` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userID` int(11) DEFAULT NULL,
  `nodeID` int(11) DEFAULT NULL,
  `claimID` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

