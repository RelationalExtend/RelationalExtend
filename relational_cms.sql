-- phpMyAdmin SQL Dump
-- version 3.4.2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Feb 15, 2013 at 04:30 PM
-- Server version: 5.5.25
-- PHP Version: 5.4.4

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `relational_cms`
--

-- --------------------------------------------------------

--
-- Table structure for table `extensions`
--

CREATE TABLE IF NOT EXISTS `extensions` (
  `extension_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `extension_name` varchar(100) NOT NULL,
  `extension_version` float NOT NULL DEFAULT '1',
  `extension_slug` varchar(100) NOT NULL,
  `extension_folder` varchar(100) NOT NULL,
  `extension_active` int(11) NOT NULL DEFAULT '1',
  `extension_description` varchar(500) NOT NULL,
  `extension_install_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`extension_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `extension_objects`
--

CREATE TABLE IF NOT EXISTS `extension_objects` (
  `object_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `object_name` varchar(100) NOT NULL,
  `object_slug` varchar(100) NOT NULL,
  `extension_id` bigint(20) NOT NULL,
  `object_type` varchar(20) NOT NULL,
  PRIMARY KEY (`object_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `extension_object_meta`
--

CREATE TABLE IF NOT EXISTS `extension_object_meta` (
  `object_meta_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `object_meta_name` varchar(100) NOT NULL,
  `object_meta_slug` varchar(100) NOT NULL,
  `object_meta_type` varchar(20) NOT NULL,
  `object_meta_size` int(11) NOT NULL DEFAULT '0',
  `object_meta_control` varchar(20) NOT NULL,
  `object_meta_values` text NOT NULL,
  `object_id` bigint(20) NOT NULL,
  PRIMARY KEY (`object_meta_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `extension_settings`
--

CREATE TABLE IF NOT EXISTS `extension_settings` (
  `extension_setting_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `extension_setting_name` varchar(100) NOT NULL,
  `extension_setting_slug` varchar(100) NOT NULL,
  `extension_setting_value` varchar(500) NOT NULL,
  `extension_setting_extension_id` bigint(20) NOT NULL,
  PRIMARY KEY (`extension_setting_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE IF NOT EXISTS `settings` (
  `setting_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `setting_name` varchar(100) NOT NULL,
  `setting_slug` varchar(100) NOT NULL,
  `setting_value` varchar(500) NOT NULL,
  PRIMARY KEY (`setting_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `themes`
--

CREATE TABLE IF NOT EXISTS `themes` (
  `theme_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `theme_name` varchar(100) NOT NULL,
  `theme_version` float NOT NULL DEFAULT '1',
  `theme_slug` varchar(100) NOT NULL,
  `theme_folder` varchar(100) NOT NULL,
  `theme_core` int(11) NOT NULL DEFAULT '1',
  `theme_description` varchar(500) NOT NULL,
  `theme_install_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `theme_active` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`theme_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `theme_javascript`
--

CREATE TABLE IF NOT EXISTS `theme_javascript` (
  `theme_js_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `theme_js_name` varchar(100) NOT NULL,
  `theme_js_slug` varchar(100) NOT NULL,
  `theme_js_content` longtext NOT NULL,
  `theme_js_theme_id` bigint(20) NOT NULL,
  PRIMARY KEY (`theme_js_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `theme_layouts`
--

CREATE TABLE IF NOT EXISTS `theme_layouts` (
  `theme_layout_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `theme_layout_name` varchar(100) NOT NULL,
  `theme_layout_slug` varchar(100) NOT NULL,
  `theme_layout_content` longtext NOT NULL,
  `theme_layout_active` int(11) NOT NULL DEFAULT '1',
  `theme_layout_default` int(11) NOT NULL DEFAULT '0',
  `theme_layout_last_edit_time` bigint(20) NOT NULL,
  `theme_layout_theme_id` bigint(20) NOT NULL,
  PRIMARY KEY (`theme_layout_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `theme_partials`
--

CREATE TABLE IF NOT EXISTS `theme_partials` (
  `theme_partial_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `theme_partial_name` varchar(100) NOT NULL,
  `theme_partial_slug` varchar(100) NOT NULL,
  `theme_partial_content` longtext NOT NULL,
  `theme_partial_active` int(11) NOT NULL DEFAULT '1',
  `theme_partial_last_edit_time` bigint(20) NOT NULL,
  `theme_partial_theme_id` bigint(20) NOT NULL,
  PRIMARY KEY (`theme_partial_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `theme_styles`
--

CREATE TABLE IF NOT EXISTS `theme_styles` (
  `theme_css_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `theme_css_name` varchar(100) NOT NULL,
  `theme_css_slug` varchar(100) NOT NULL,
  `theme_css_content` longtext NOT NULL,
  `theme_css_theme_id` bigint(20) NOT NULL,
  PRIMARY KEY (`theme_css_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;



------------------------------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `group` int(11) NOT NULL DEFAULT '1',
  `email` varchar(255) NOT NULL,
  `last_login` varchar(25) NOT NULL,
  `login_hash` varchar(255) NOT NULL,
  `profile_fields` text NOT NULL,
  `created_at` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`,`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;