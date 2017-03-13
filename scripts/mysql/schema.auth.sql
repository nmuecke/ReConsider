
SQL Dump
-- version 3.3.9.2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Sep 26, 2011 at 11:53 PM
-- Server version: 5.1.58
-- PHP Version: 5.3.6

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Table structure for table `auth`
--
DROP TABLE IF EXISTS `auth`;
CREATE TABLE IF NOT EXISTS `auth` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(30) DEFAULT NULL,
  `role` varchar(30) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `emailIsValid` tinyint(1) DEFAULT NULL,
  `emailValidationCode` tinyblob,
  `realname` varchar(50) DEFAULT NULL,
  `password` mediumblob,
  `password_salt` mediumblob,
  `recoveryPassword` mediumblob,
  `recoveryPassword_salt` mediumblob,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=0;
CREATE INDEX "id" ON "auth" ("id");
