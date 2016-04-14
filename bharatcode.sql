-- phpMyAdmin SQL Dump
-- version 3.4.5
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Apr 14, 2016 at 12:45 PM
-- Server version: 5.5.16
-- PHP Version: 5.3.8

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `bharatcode`
--

-- --------------------------------------------------------

--
-- Table structure for table `bh_user_master`
--

CREATE TABLE IF NOT EXISTS `bh_user_master` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `firstname` varchar(40) NOT NULL,
  `lastname` varchar(40) NOT NULL,
  `email` varchar(40) NOT NULL,
  `password` text NOT NULL,
  `register_date` datetime NOT NULL,
  `register_ipaddress` varchar(40) NOT NULL,
  `user_type` int(1) NOT NULL COMMENT '0:user,1: admin',
  `active_status` int(1) NOT NULL COMMENT '0:inactive, 1:active',
  `verify_code` varchar(100) NOT NULL,
  `verify_status` int(1) NOT NULL COMMENT '0:Pending, 1:Verified',
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
