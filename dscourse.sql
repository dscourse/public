-- phpMyAdmin SQL Dump
-- version 3.4.10.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Dec 13, 2013 at 12:34 PM
-- Server version: 5.1.60
-- PHP Version: 5.3.8

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `dscourse`
--
CREATE DATABASE `dscourse` DEFAULT CHARACTER SET utf8 COLLATE utf8_bin;
USE `dscourse`;

-- --------------------------------------------------------

--
-- Table structure for table `courseDiscussions`
--

CREATE TABLE IF NOT EXISTS `courseDiscussions` (
  `courseDiscussionID` int(20) NOT NULL AUTO_INCREMENT,
  `courseID` int(12) NOT NULL,
  `discussionID` int(12) NOT NULL,
  `courseDiscussionTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`courseDiscussionID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=75 ;

-- --------------------------------------------------------

--
-- Table structure for table `courseRoles`
--

CREATE TABLE IF NOT EXISTS `courseRoles` (
  `courseRoleID` int(12) NOT NULL AUTO_INCREMENT,
  `courseID` int(12) NOT NULL,
  `userID` int(12) NOT NULL,
  `userRole` varchar(30) NOT NULL,
  `courseRoleTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`courseRoleID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1089 ;

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE IF NOT EXISTS `courses` (
  `courseID` int(11) NOT NULL AUTO_INCREMENT,
  `courseName` varchar(255) NOT NULL,
  `courseHash` text,
  `courseStatus` varchar(255) NOT NULL DEFAULT 'active',
  `courseStartDate` date NOT NULL,
  `courseEndDate` date NOT NULL,
  `courseDescription` text NOT NULL,
  `courseImage` varchar(255) NOT NULL,
  `courseURL` varchar(255) NOT NULL,
  `courseView` varchar(30) NOT NULL,
  `courseParticipate` varchar(30) NOT NULL,
  `courseTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`courseID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=57 ;

-- --------------------------------------------------------

--
-- Table structure for table `discussionPosts`
--

CREATE TABLE IF NOT EXISTS `discussionPosts` (
  `discussionPostID` int(20) NOT NULL AUTO_INCREMENT,
  `discussionID` int(12) NOT NULL,
  `postID` int(20) NOT NULL,
  `discussionPostTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `postStatus` varchar(100) NOT NULL DEFAULT 'active',
  PRIMARY KEY (`discussionPostID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=607 ;

-- --------------------------------------------------------

--
-- Table structure for table `discussions`
--

CREATE TABLE IF NOT EXISTS `discussions` (
  `dID` int(11) NOT NULL AUTO_INCREMENT,
  `dTitle` text NOT NULL,
  `dPrompt` text NOT NULL,
  `dStartDate` datetime NOT NULL,
  `dOpenDate` datetime NOT NULL,
  `dEndDate` datetime NOT NULL,
  `dPosts` text NOT NULL,
  `dRules` text NOT NULL,
  `dChangeDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`dID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=63 ;

-- --------------------------------------------------------

--
-- Table structure for table `logs`
--

CREATE TABLE IF NOT EXISTS `logs` (
  `logID` int(100) NOT NULL AUTO_INCREMENT,
  `logSessionID` varchar(50) NOT NULL,
  `logUserID` int(10) NOT NULL,
  `logPageType` varchar(50) NOT NULL,
  `logPageID` int(10) NOT NULL,
  `logAction` varchar(200) NOT NULL,
  `logActionID` int(10) NOT NULL,
  `logMessage` text NOT NULL,
  `logTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `logUserAgent` varchar(255) NOT NULL,
  PRIMARY KEY (`logID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=10021 ;

-- --------------------------------------------------------

--
-- Table structure for table `networkCourses`
--

CREATE TABLE IF NOT EXISTS `networkCourses` (
  `networkCourseID` int(12) NOT NULL AUTO_INCREMENT,
  `networkID` int(12) NOT NULL,
  `courseID` int(12) NOT NULL,
  `networkCourseTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`networkCourseID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `networkUsers`
--

CREATE TABLE IF NOT EXISTS `networkUsers` (
  `networkUserID` int(12) NOT NULL AUTO_INCREMENT,
  `networkID` int(12) NOT NULL,
  `userID` int(12) NOT NULL,
  `networkUserRole` varchar(30) NOT NULL,
  `networkUserTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`networkUserID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `networks`
--

CREATE TABLE IF NOT EXISTS `networks` (
  `networkID` int(12) NOT NULL AUTO_INCREMENT,
  `networkName` varchar(255) NOT NULL,
  `networkDesc` text NOT NULL,
  `networkCode` int(10) NOT NULL,
  `networkStatus` varchar(20) NOT NULL DEFAULT 'private',
  `networkTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`networkID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `options`
--

CREATE TABLE IF NOT EXISTS `options` (
  `optionsID` int(30) NOT NULL AUTO_INCREMENT,
  `optionsType` text NOT NULL,
  `optionsTypeID` int(30) NOT NULL,
  `optionsName` varchar(200) NOT NULL,
  `optionsValue` text NOT NULL,
  `optionsChangeTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `optionAttr` text NOT NULL,
  PRIMARY KEY (`optionsID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=384 ;

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE IF NOT EXISTS `posts` (
  `postID` int(12) NOT NULL AUTO_INCREMENT,
  `postFromId` int(10) NOT NULL,
  `postAuthorId` int(10) NOT NULL,
  `postMessage` text NOT NULL,
  `postTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `postToWhom` text NOT NULL,
  `postMentions` text NOT NULL,
  `postSelection` text NOT NULL,
  `postContext` text NOT NULL,
  `postType` varchar(255) NOT NULL,
  `postTags` varchar(255) NOT NULL,
  `postSubject` varchar(255) NOT NULL,
  `postMedia` text NOT NULL,
  `postMediaType` varchar(20) NOT NULL,
  PRIMARY KEY (`postID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=603 ;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `UserID` int(25) NOT NULL AUTO_INCREMENT,
  `username` varchar(65) NOT NULL,
  `password` varchar(32) NOT NULL,
  `firstName` varchar(50) NOT NULL,
  `lastName` varchar(50) NOT NULL,
  `sysRole` varchar(20) NOT NULL,
  `userFacebook` varchar(50) NOT NULL,
  `userTwitter` varchar(50) NOT NULL,
  `userPhone` varchar(15) NOT NULL,
  `userWebsite` varchar(255) NOT NULL,
  `userPictureURL` varchar(255) NOT NULL,
  `userStatus` varchar(10) NOT NULL,
  `userProfile` text NOT NULL,
  `userAbout` text NOT NULL,
  `userRecovery` varchar(255) NOT NULL,
  `userRecoveryTime` int(11) NOT NULL,
  PRIMARY KEY (`UserID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=142 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
