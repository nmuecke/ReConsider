-- phpMyAdmin SQL Dump
-- version 3.3.9.2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Sep 27, 2011 at 12:00 AM
-- Server version: 5.1.58
-- PHP Version: 5.3.6

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `ReConsider_Dev`
--

-- --------------------------------------------------------

--
-- Table structure for table `mailQueue`
--

DROP TABLE IF EXISTS `mailQueue`;
CREATE TABLE IF NOT EXISTS `mailQueue` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mailType` tinytext,
  `disputeID` int(11) DEFAULT NULL,
  `userID` int(11) DEFAULT NULL,
  `addedAt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=0 ;
