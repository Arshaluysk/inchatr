-- phpMyAdmin SQL Dump
-- version 3.2.4
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jul 25, 2016 at 01:10 PM
-- Server version: 5.1.41
-- PHP Version: 5.3.1

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `test_member`
--

-- --------------------------------------------------------

--
-- Table structure for table `wpiw_77_sm_membership_details`
--

CREATE TABLE IF NOT EXISTS `wpiw_77_sm_membership_details` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `membership_level_name` varchar(250) NOT NULL,
  `redirect_page` varchar(255) DEFAULT NULL,
  `membership_level_key` varchar(60) DEFAULT NULL,
  `clickbank_product_id` varchar(60) DEFAULT NULL,
  `jvzoo_product_id` varchar(60) DEFAULT NULL,
  `WSOPRO_prod_code` varchar(60) DEFAULT NULL,
  `spbas_product_id` varchar(60) DEFAULT NULL,
  `spbas_tier_id` varchar(60) DEFAULT NULL,
  `subscribe_aweber` bigint(20) DEFAULT NULL,
  `email_title` text,
  `email_body` text,
  `integrate_to_spbas` bigint(20) DEFAULT NULL,
  `integrate_to_clickbank` bigint(20) DEFAULT NULL,
  `integrate_to_jvzoo` bigint(20) DEFAULT NULL,
  `integrate_to_WSOPRO` bigint(20) DEFAULT NULL,
  `webinar_enable` bigint(20) DEFAULT NULL,
  `webinar_serverno` bigint(20) DEFAULT NULL,
  `webinarkey1` varchar(255) DEFAULT NULL,
  `webinarkey2` varchar(255) DEFAULT NULL,
  `webinarkey3` varchar(255) DEFAULT NULL,
  `webinarkey4` varchar(255) DEFAULT NULL,
  `webinarkey5` varchar(255) DEFAULT NULL,
  `webinarkey6` varchar(255) DEFAULT NULL,
  `arweb_code` text,
  `arname_fields` text,
  `aremail_fields` text,
  `arpost_url` text,
  `arhidden_fields` text,
  `created_by` bigint(20) DEFAULT NULL,
  `created_date` datetime DEFAULT NULL,
  `pp_coupon_codes` text,
  `other_info` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11 ;

--
-- Dumping data for table `wpiw_77_sm_membership_details`
--

INSERT INTO `wpiw_77_sm_membership_details` (`id`, `membership_level_name`, `redirect_page`, `membership_level_key`, `clickbank_product_id`, `jvzoo_product_id`, `WSOPRO_prod_code`, `spbas_product_id`, `spbas_tier_id`, `subscribe_aweber`, `email_title`, `email_body`, `integrate_to_spbas`, `integrate_to_clickbank`, `integrate_to_jvzoo`, `integrate_to_WSOPRO`, `webinar_enable`, `webinar_serverno`, `webinarkey1`, `webinarkey2`, `webinarkey3`, `webinarkey4`, `webinarkey5`, `webinarkey6`, `arweb_code`, `arname_fields`, `aremail_fields`, `arpost_url`, `arhidden_fields`, `created_by`, `created_date`, `pp_coupon_codes`, `other_info`) VALUES
(1, 'Video Composer Monthly', 'http://videocomposerpro.com/login/', 'wso_57427d5d6e2f9', '', '220278', '', '', '', 2, 'Here is Your [memberlevel] Login Information.', 'Dear [firstname], \r\n  \r\n You have successfully registered as one of our [memberlevel] members. \r\n  \r\n Please keep this information safe as it contains your username and password.   \r\n  \r\n Your Membership Info:  \r\n  \r\n U: [username]  \r\n  \r\n P: [password]  \r\n  \r\n Login URL: [loginurl]  \r\n  \r\n Regards, \r\n \r\n \r\n \r\n \r\n \r\n \r\n \r\n \r\n \r\n', NULL, NULL, 1, NULL, NULL, 0, '', '', '', '', '', '', '', '', '', '', '', 1, '2016-05-23 03:47:41', NULL, '{"awlistname":"4303943","remove_level":null,"add_level":null,"after_login_memlevel":"http:\\/\\/videocomposerpro.com\\/video-composer-pro\\/","mclistid":null,"mclistidgroup":"","sllistid":null,"aclistid":null,"actcamtags":"","actcamfrmid":"","integrate_to_zaxaa":null,"zaxaa_product_id":"","integrate_to_digiresults":"","digiresults_product_id":"","integrate_ExtWebsite":null,"DomApiIds":"a:1:{i:0;a:3:{s:6:\\"domain\\";s:0:\\"\\";s:6:\\"apikey\\";s:0:\\"\\";s:8:\\"prodcode\\";s:0:\\"\\";}}","no_multi_emails":null,"arfname_fields":"","arlname_fields":"","integrate_to_dealguardian":null,"dealguardian_product_id":"","email_title2":"Your [memberlevel] is activated!","email_body2":"Dear [firstname],  \\r\\n  \\r\\n Your [memberlevel] membership is successfully activated.  \\r\\n  \\r\\n  You can use the present login info to access the membership.  \\r\\n  \\r\\n Regards, \\r\\n \\r\\n \\r\\n \\r\\n \\r\\n \\r\\n \\r\\n \\r\\n \\r\\n \\r\\n","futureonly":null,"enable_wjam":null,"wjam_webicode":"a:0:{}","blockwelcomeemails":null,"integrate_to_Thrive":null,"Thrive_product_id":"","Thrive_prod_opts":"main","paid_memlevel":null}'),
(2, 'Video Composer Pro  Annual', 'http://videocomposerpro.com/login/', 'wso_5743671134bbe', '', '220280', '', '', '', 2, 'Here is Your [memberlevel] Login Information.', 'Dear [firstname], \r\n  \r\n You have successfully registered as one of our [memberlevel] members. \r\n  \r\n Please keep this information safe as it contains your username and password.   \r\n  \r\n Your Membership Info:  \r\n  \r\n U: [username]  \r\n  \r\n P: [password]  \r\n  \r\n Login URL: [loginurl]  \r\n  \r\n Regards, \r\n \r\n \r\n \r\n \r\n', NULL, NULL, 1, NULL, NULL, 0, '', '', '', '', '', '', '', '', '', '', '', 1, '2016-05-23 03:47:41', NULL, '{"awlistname":"4303943","remove_level":null,"add_level":null,"after_login_memlevel":"http:\\/\\/videocomposerpro.com\\/video-composer-pro\\/","mclistid":null,"mclistidgroup":"","sllistid":null,"aclistid":null,"actcamtags":"","actcamfrmid":"","integrate_to_zaxaa":null,"zaxaa_product_id":"","integrate_to_digiresults":"","digiresults_product_id":"","integrate_ExtWebsite":null,"DomApiIds":"a:1:{i:0;a:3:{s:6:\\"domain\\";s:0:\\"\\";s:6:\\"apikey\\";s:0:\\"\\";s:8:\\"prodcode\\";s:0:\\"\\";}}","no_multi_emails":null,"arfname_fields":"","arlname_fields":"","integrate_to_dealguardian":null,"dealguardian_product_id":"","email_title2":"Your [memberlevel] is activated!","email_body2":"Dear [firstname],  \\r\\n  \\r\\n Your [memberlevel] membership is successfully activated.  \\r\\n  \\r\\n  You can use the present login info to access the membership.  \\r\\n  \\r\\n Regards, \\r\\n \\r\\n \\r\\n \\r\\n \\r\\n","futureonly":null,"enable_wjam":null,"wjam_webicode":"a:0:{}","blockwelcomeemails":null,"integrate_to_Thrive":null,"Thrive_product_id":"","Thrive_prod_opts":"main"}'),
(3, 'Video Composer Pro Monthly Promo 1', 'http://videocomposerpro.com/login/', 'wso_574ca22fcc2b0', '', '221492', '', '', '', 2, 'Here is Your [memberlevel] Login Information.', 'Dear [firstname], \r\n  \r\n You have successfully registered as one of our [memberlevel] members. \r\n  \r\n Please keep this information safe as it contains your username and password.   \r\n  \r\n Your Membership Info:  \r\n  \r\n U: [username]  \r\n  \r\n P: [password]  \r\n  \r\n Login URL: [loginurl]  \r\n  \r\n Regards, \r\n \r\n \r\n \r\n \r\n \r\n', NULL, NULL, 1, NULL, NULL, 0, '', '', '', '', '', '', '', '', '', '', '', 1, '2016-05-23 03:47:41', NULL, '{"awlistname":"4303943","remove_level":null,"add_level":null,"after_login_memlevel":"http:\\/\\/videocomposerpro.com\\/video-composer-pro\\/","mclistid":null,"mclistidgroup":"","sllistid":null,"aclistid":null,"actcamtags":"","actcamfrmid":"","integrate_to_zaxaa":null,"zaxaa_product_id":"","integrate_to_digiresults":"","digiresults_product_id":"","integrate_ExtWebsite":null,"DomApiIds":"a:1:{i:0;a:3:{s:6:\\"domain\\";s:0:\\"\\";s:6:\\"apikey\\";s:0:\\"\\";s:8:\\"prodcode\\";s:0:\\"\\";}}","no_multi_emails":null,"arfname_fields":"","arlname_fields":"","integrate_to_dealguardian":null,"dealguardian_product_id":"","email_title2":"Your [memberlevel] is activated!","email_body2":"Dear [firstname],  \\r\\n  \\r\\n Your [memberlevel] membership is successfully activated.  \\r\\n  \\r\\n  You can use the present login info to access the membership.  \\r\\n  \\r\\n Regards, \\r\\n \\r\\n \\r\\n \\r\\n \\r\\n \\r\\n","futureonly":null,"enable_wjam":null,"wjam_webicode":"a:0:{}","blockwelcomeemails":null,"integrate_to_Thrive":null,"Thrive_product_id":"","Thrive_prod_opts":"main"}'),
(5, 'Synd XPoser Pro', 'http://videocomposerpro.com/login/', 'wso_5755d0af52cb0', '', '224116', '', '', '', 2, 'Here is Your [memberlevel] Login Information.', 'Dear [firstname], \r\n  \r\n You have successfully registered as one of our [memberlevel] members. \r\n  \r\n Please keep this information safe as it contains your username and password.   \r\n  \r\n Your Membership Info:  \r\n  \r\n U: [username]  \r\n  \r\n P: [password]  \r\n  \r\n Login URL: [loginurl]  \r\n  \r\n Regards, \r\n \r\n \r\n \r\n \r\n \r\n \r\n \r\n \r\n \r\n \r\n \r\n \r\n \r\n', NULL, NULL, 1, NULL, NULL, 0, '', '', '', '', '', '', '', '', '', '', '', 1, '2016-05-23 03:47:41', NULL, '{"awlistname":"4303943","remove_level":null,"add_level":"1|","after_login_memlevel":"http:\\/\\/videocomposerpro.com\\/synd-xposer-pro\\/","mclistid":null,"mclistidgroup":"","sllistid":null,"aclistid":null,"actcamtags":"","actcamfrmid":"","integrate_to_zaxaa":null,"zaxaa_product_id":"","integrate_to_digiresults":"","digiresults_product_id":"","integrate_ExtWebsite":null,"DomApiIds":"a:1:{i:0;a:3:{s:6:\\"domain\\";s:0:\\"\\";s:6:\\"apikey\\";s:0:\\"\\";s:8:\\"prodcode\\";s:0:\\"\\";}}","no_multi_emails":null,"arfname_fields":"","arlname_fields":"","integrate_to_dealguardian":null,"dealguardian_product_id":"","email_title2":"Your [memberlevel] is activated!","email_body2":"Dear [firstname],  \\r\\n  \\r\\n Your [memberlevel] membership is successfully activated.  \\r\\n  \\r\\n  You can use the present login info to access the membership.  \\r\\n  \\r\\n Regards, \\r\\n \\r\\n \\r\\n \\r\\n \\r\\n \\r\\n \\r\\n \\r\\n \\r\\n \\r\\n \\r\\n \\r\\n \\r\\n \\r\\n","futureonly":null,"enable_wjam":null,"wjam_webicode":"a:0:{}","blockwelcomeemails":null,"integrate_to_Thrive":null,"Thrive_product_id":"","Thrive_prod_opts":"main","paid_memlevel":null}'),
(6, 'Mastermind Profits', 'http://videocomposerpro.com/login/', 'wso_575c6bc36a8f0', '', '222414', '', '', '', 2, 'Here is Your [memberlevel] Login Information.', 'Dear [firstname], \r\n  \r\n You have successfully registered as one of our [memberlevel] members. \r\n  \r\n Please keep this information safe as it contains your username and password.   \r\n  \r\n Your Membership Info:  \r\n  \r\n U: [username]  \r\n  \r\n P: [password]  \r\n  \r\n Login URL: [loginurl]  \r\n  \r\n Regards, \r\n \r\n \r\n \r\n \r\n \r\n \r\n \r\n \r\n', NULL, NULL, 1, NULL, NULL, 0, '', '', '', '', '', '', '', '', '', '', '', 1, '2016-05-23 03:47:41', NULL, '{"awlistname":"4303943","remove_level":null,"add_level":null,"after_login_memlevel":"http:\\/\\/videocomposerpro.com\\/1-unstoppable-automation\\/","mclistid":null,"mclistidgroup":"","sllistid":null,"aclistid":null,"actcamtags":"","actcamfrmid":"","integrate_to_zaxaa":null,"zaxaa_product_id":"","integrate_to_digiresults":"","digiresults_product_id":"","integrate_ExtWebsite":null,"DomApiIds":"a:1:{i:0;a:3:{s:6:\\"domain\\";s:0:\\"\\";s:6:\\"apikey\\";s:0:\\"\\";s:8:\\"prodcode\\";s:0:\\"\\";}}","no_multi_emails":null,"arfname_fields":"","arlname_fields":"","integrate_to_dealguardian":null,"dealguardian_product_id":"","email_title2":"Your [memberlevel] is activated!","email_body2":"Dear [firstname],  \\r\\n  \\r\\n Your [memberlevel] membership is successfully activated.  \\r\\n  \\r\\n  You can use the present login info to access the membership.  \\r\\n  \\r\\n Regards, \\r\\n \\r\\n \\r\\n \\r\\n \\r\\n \\r\\n \\r\\n \\r\\n \\r\\n","futureonly":null,"enable_wjam":null,"wjam_webicode":"a:0:{}","blockwelcomeemails":null,"integrate_to_Thrive":null,"Thrive_product_id":"","Thrive_prod_opts":"main"}'),
(7, 'Mastermind Profits 2 Pay', 'http://videocomposerpro.com/login/', 'wso_575c6ddf45e58', '', '222992', '', '', '', 2, 'Here is Your [memberlevel] Login Information.', 'Dear [firstname], \r\n  \r\n You have successfully registered as one of our [memberlevel] members. \r\n  \r\n Please keep this information safe as it contains your username and password.   \r\n  \r\n Your Membership Info:  \r\n  \r\n U: [username]  \r\n  \r\n P: [password]  \r\n  \r\n Login URL: [loginurl]  \r\n  \r\n Regards, \r\n \r\n \r\n \r\n \r\n \r\n \r\n \r\n \r\n \r\n', NULL, NULL, 1, NULL, NULL, 0, '', '', '', '', '', '', '', '', '', '', '', 1, '2016-05-23 03:47:41', NULL, '{"awlistname":"4303943","remove_level":null,"add_level":null,"after_login_memlevel":"http:\\/\\/videocomposerpro.com\\/1-unstoppable-automation\\/","mclistid":null,"mclistidgroup":"","sllistid":null,"aclistid":null,"actcamtags":"","actcamfrmid":"","integrate_to_zaxaa":null,"zaxaa_product_id":"","integrate_to_digiresults":"","digiresults_product_id":"","integrate_ExtWebsite":null,"DomApiIds":"a:1:{i:0;a:3:{s:6:\\"domain\\";s:0:\\"\\";s:6:\\"apikey\\";s:0:\\"\\";s:8:\\"prodcode\\";s:0:\\"\\";}}","no_multi_emails":null,"arfname_fields":"","arlname_fields":"","integrate_to_dealguardian":null,"dealguardian_product_id":"","email_title2":"Your [memberlevel] is activated!","email_body2":"Dear [firstname],  \\r\\n  \\r\\n Your [memberlevel] membership is successfully activated.  \\r\\n  \\r\\n  You can use the present login info to access the membership.  \\r\\n  \\r\\n Regards, \\r\\n \\r\\n \\r\\n \\r\\n \\r\\n \\r\\n \\r\\n \\r\\n \\r\\n \\r\\n","futureonly":null,"enable_wjam":null,"wjam_webicode":"a:0:{}","blockwelcomeemails":null,"integrate_to_Thrive":null,"Thrive_product_id":"","Thrive_prod_opts":"main"}'),
(8, 'Mastermind Profits 3 Pay', 'http://videocomposerpro.com/login/', 'wso_575c6f0a1e810', '', '223104', '', '', '', 2, 'Here is Your [memberlevel] Login Information.', 'Dear [firstname], \r\n  \r\n You have successfully registered as one of our [memberlevel] members. \r\n  \r\n Please keep this information safe as it contains your username and password.   \r\n  \r\n Your Membership Info:  \r\n  \r\n U: [username]  \r\n  \r\n P: [password]  \r\n  \r\n Login URL: [loginurl]  \r\n  \r\n Regards, \r\n \r\n \r\n \r\n \r\n \r\n \r\n \r\n \r\n \r\n \r\n', NULL, NULL, 1, NULL, NULL, 0, '', '', '', '', '', '', '', '', '', '', '', 1, '2016-05-23 03:47:41', NULL, '{"awlistname":"4303943","remove_level":null,"add_level":null,"after_login_memlevel":"http:\\/\\/videocomposerpro.com\\/1-unstoppable-automation\\/","mclistid":null,"mclistidgroup":"","sllistid":null,"aclistid":null,"actcamtags":"","actcamfrmid":"","integrate_to_zaxaa":null,"zaxaa_product_id":"","integrate_to_digiresults":"","digiresults_product_id":"","integrate_ExtWebsite":null,"DomApiIds":"a:1:{i:0;a:3:{s:6:\\"domain\\";s:0:\\"\\";s:6:\\"apikey\\";s:0:\\"\\";s:8:\\"prodcode\\";s:0:\\"\\";}}","no_multi_emails":null,"arfname_fields":"","arlname_fields":"","integrate_to_dealguardian":null,"dealguardian_product_id":"","email_title2":"Your [memberlevel] is activated!","email_body2":"Dear [firstname],  \\r\\n  \\r\\n Your [memberlevel] membership is successfully activated.  \\r\\n  \\r\\n  You can use the present login info to access the membership.  \\r\\n  \\r\\n Regards, \\r\\n \\r\\n \\r\\n \\r\\n \\r\\n \\r\\n \\r\\n \\r\\n \\r\\n \\r\\n \\r\\n","futureonly":null,"enable_wjam":null,"wjam_webicode":"a:0:{}","blockwelcomeemails":null,"integrate_to_Thrive":null,"Thrive_product_id":"","Thrive_prod_opts":"main"}'),
(10, 'Video Composer Pro Webby', 'http://videocomposerpro.com/login/', 'wso_5783b5686fbb5', '', '224116', '', '', '', 2, 'Here is Your [memberlevel] Login Information.', 'Dear [firstname], \r\n  \r\n You have successfully registered as one of our [memberlevel] members. \r\n  \r\n Please keep this information safe as it contains your username and password.   \r\n  \r\n Your Membership Info:  \r\n  \r\n U: [username]  \r\n  \r\n P: [password]  \r\n  \r\n Login URL: [loginurl]  \r\n  \r\n Regards, \r\n \r\n \r\n \r\n \r\n \r\n \r\n \r\n \r\n \r\n \r\n \r\n', NULL, NULL, 1, NULL, NULL, 0, '', '', '', '', '', '', '', '', '', '', '', 1, '2016-05-23 03:47:41', NULL, '{"awlistname":"4303943","remove_level":null,"add_level":"5|","after_login_memlevel":"http:\\/\\/videocomposerpro.com\\/video-composer-pro\\/","mclistid":null,"mclistidgroup":"","sllistid":null,"aclistid":null,"actcamtags":"","actcamfrmid":"","integrate_to_zaxaa":null,"zaxaa_product_id":"","integrate_to_digiresults":"","digiresults_product_id":"","integrate_ExtWebsite":null,"DomApiIds":"a:1:{i:0;a:3:{s:6:\\"domain\\";s:0:\\"\\";s:6:\\"apikey\\";s:0:\\"\\";s:8:\\"prodcode\\";s:0:\\"\\";}}","no_multi_emails":null,"arfname_fields":"","arlname_fields":"","integrate_to_dealguardian":null,"dealguardian_product_id":"","email_title2":"Your [memberlevel] is activated!","email_body2":"Dear [firstname],  \\r\\n  \\r\\n Your [memberlevel] membership is successfully activated.  \\r\\n  \\r\\n  You can use the present login info to access the membership.  \\r\\n  \\r\\n Regards, \\r\\n \\r\\n \\r\\n \\r\\n \\r\\n \\r\\n \\r\\n \\r\\n \\r\\n \\r\\n \\r\\n \\r\\n","futureonly":null,"enable_wjam":null,"wjam_webicode":"a:0:{}","blockwelcomeemails":null,"integrate_to_Thrive":null,"Thrive_product_id":"","Thrive_prod_opts":"main"}');

-- --------------------------------------------------------

--
-- Table structure for table `wpiw_77_sm_member_assoc`
--

CREATE TABLE IF NOT EXISTS `wpiw_77_sm_member_assoc` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) DEFAULT NULL,
  `wp_membership_id` int(11) DEFAULT NULL,
  `is_active` int(1) DEFAULT '1',
  `profile_id` varchar(255) DEFAULT NULL,
  `created_by` bigint(20) DEFAULT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=154 ;

--
-- Dumping data for table `wpiw_77_sm_member_assoc`
--

INSERT INTO `wpiw_77_sm_member_assoc` (`id`, `user_id`, `wp_membership_id`, `is_active`, `profile_id`, `created_by`, `created_date`) VALUES
(1, 1, 1, 1, NULL, 1, '2016-05-23 04:12:20'),
(2, 2, 1, 1, NULL, 1, '2016-05-23 16:48:47'),
(3, 1, 2, 1, NULL, 1, '2016-05-23 20:31:05'),
(4, 3, 1, 1, NULL, 1, '2016-05-24 01:55:54'),
(5, 4, 1, 1, NULL, 1, '2016-05-24 04:23:45'),
(6, 5, 1, 1, NULL, 1, '2016-05-24 19:13:34'),
(7, 6, 1, 0, NULL, 1, '2016-07-16 20:07:03'),
(8, 1, 3, 1, NULL, 1, '2016-05-30 20:30:59'),
(9, 7, 2, 1, NULL, 1, '2016-06-03 16:57:05'),
(10, 8, 1, 1, NULL, 1, '2016-06-03 17:17:01'),
(11, 9, 1, 1, NULL, 1, '2016-06-03 17:49:44'),
(12, 10, 1, 0, NULL, 1, '2016-07-14 16:07:01'),
(13, 11, 1, 1, NULL, 1, '2016-06-03 18:49:24'),
(14, 12, 1, 1, NULL, 1, '2016-06-03 20:01:16'),
(15, 13, 1, 0, NULL, 1, '2016-07-03 11:28:03'),
(16, 14, 1, 0, NULL, 1, '2016-07-03 04:57:43'),
(17, 15, 1, 0, NULL, 1, '2016-07-06 13:47:04'),
(18, 16, 1, 1, NULL, 1, '2016-06-03 21:53:21'),
(19, 17, 1, 1, NULL, 1, '2016-06-03 22:38:27'),
(20, 18, 1, 1, NULL, 1, '2016-06-03 22:41:46'),
(21, 19, 1, 1, NULL, 1, '2016-06-03 22:45:13'),
(22, 20, 1, 0, NULL, 1, '2016-07-03 04:59:14'),
(23, 21, 1, 1, NULL, 1, '2016-06-04 13:48:56'),
(24, 22, 1, 1, NULL, 1, '2016-06-04 13:52:51'),
(25, 23, 1, 0, NULL, 1, '2016-07-04 04:57:52'),
(26, 24, 2, 1, '57D62891H35082601', 0, '2016-06-04 18:51:27'),
(27, 25, 1, 0, '2WS55574FY8376625', 0, '2016-07-04 04:50:01'),
(28, 26, 1, 1, '3FH98345D0681173S', 0, '2016-06-04 21:00:32'),
(29, 27, 1, 0, '0UJ84120H63261334', 0, '2016-07-09 16:36:45'),
(30, 28, 1, 1, '95U21007A4326351A', 0, '2016-06-05 09:35:24'),
(31, 29, 1, 1, '5BD91942FX2250321', 0, '2016-06-05 12:44:39'),
(32, 30, 1, 1, '267984025X626045V', 0, '2016-06-05 15:38:53'),
(33, 31, 2, 1, '5H956924P0402661A', 0, '2016-06-05 17:20:08'),
(34, 32, 1, 0, '9V802535CN482994D', 0, '2016-07-05 14:26:39'),
(35, 33, 1, 1, NULL, 1, '2016-06-06 04:45:29'),
(36, 34, 1, 0, '1HH99607C38943824', 0, '2016-07-14 04:48:35'),
(37, 35, 1, 1, '6PR64904FP9339919', 0, '2016-06-06 14:51:41'),
(38, 33, 2, 0, NULL, 1, '2016-06-06 19:42:10'),
(39, 33, 3, 0, NULL, 1, '2016-06-06 19:42:10'),
(40, 33, 5, 1, NULL, 1, '2016-06-06 19:42:10'),
(41, 1, 5, 1, NULL, 1, '2016-06-06 19:42:57'),
(42, 2, 2, 0, NULL, 1, '2016-06-06 19:43:05'),
(43, 2, 3, 0, NULL, 1, '2016-06-06 19:43:05'),
(44, 2, 5, 1, NULL, 1, '2016-06-06 19:43:05'),
(45, 3, 2, 0, NULL, 1, '2016-06-06 19:43:14'),
(46, 3, 3, 0, NULL, 1, '2016-06-06 19:43:14'),
(47, 3, 5, 1, NULL, 1, '2016-06-06 19:43:14'),
(48, 4, 2, 0, NULL, 1, '2016-06-06 19:43:22'),
(49, 4, 3, 0, NULL, 1, '2016-06-06 19:43:22'),
(50, 4, 5, 1, NULL, 1, '2016-06-06 19:43:22'),
(51, 5, 2, 0, NULL, 1, '2016-06-06 19:43:29'),
(52, 5, 3, 0, NULL, 1, '2016-06-06 19:43:29'),
(53, 5, 5, 1, NULL, 1, '2016-06-06 19:43:29'),
(54, 6, 2, 0, NULL, 1, '2016-06-06 19:43:36'),
(55, 6, 3, 0, NULL, 1, '2016-06-06 19:43:36'),
(56, 6, 5, 0, NULL, 1, '2016-07-16 20:07:03'),
(57, 36, 1, 1, NULL, 1, '2016-06-08 19:57:18'),
(58, 37, 1, 1, '87J164254R243850F', 0, '2016-06-09 07:22:41'),
(59, 38, 1, 0, '9WE17674JC151745X', 0, '2016-07-09 16:34:56'),
(60, 39, 1, 0, '670832027Y726784X', 0, '2016-07-10 05:18:25'),
(61, 2, 6, 1, NULL, 1, '2016-06-11 20:09:53'),
(62, 2, 7, 1, NULL, 1, '2016-06-11 20:10:12'),
(63, 2, 8, 1, NULL, 1, '2016-06-11 20:10:40'),
(64, 1, 6, 1, NULL, 1, '2016-06-11 20:10:43'),
(65, 1, 7, 1, NULL, 1, '2016-06-11 20:10:58'),
(66, 1, 8, 1, NULL, 1, '2016-06-11 20:11:02'),
(67, 40, 1, 1, '6AM33190923894923', 0, '2016-06-12 01:53:34'),
(68, 6, 6, 0, NULL, 1, '2016-07-13 15:47:14'),
(69, 6, 7, 0, NULL, 1, '2016-07-13 15:47:14'),
(70, 6, 8, 0, NULL, 1, '2016-07-13 15:47:14'),
(71, 41, 1, 1, '2AF75102RR916864X', 0, '2016-06-15 10:47:42'),
(72, 42, 7, 1, '7C539200RH990950T', 0, '2016-06-18 16:50:40'),
(73, 43, 1, 1, '97V15101F9838331T', 0, '2016-06-20 05:31:27'),
(74, 44, 1, 1, '95X48389ML4962116', 0, '2016-06-23 09:34:25'),
(75, 45, 1, 0, NULL, 1, '2016-07-03 15:27:19'),
(76, 46, 3, 1, '4CN50830UY886122B', 0, '2016-07-02 18:36:40'),
(77, 47, 3, 1, '12F03392FN191853S', 0, '2016-07-03 03:45:13'),
(78, 48, 1, 1, '5T6193142X6676645', 0, '2016-07-06 02:20:23'),
(79, 1, 10, 1, NULL, 1, '2016-07-12 17:36:56'),
(80, 49, 1, 1, '2A613057T1711904F', 0, '2016-07-12 19:44:37'),
(81, 49, 5, 1, '', NULL, '2016-07-12 19:44:40'),
(82, 50, 5, 1, '3RK85681J8746481R', 0, '2016-07-13 00:05:47'),
(83, 51, 5, 1, '7CE58940F5204170N', 0, '2016-07-13 00:06:35'),
(84, 52, 5, 1, '42J25712P99548113', 0, '2016-07-13 00:07:55'),
(85, 53, 1, 1, '1LG18775BM134441E', 0, '2016-07-13 00:12:10'),
(86, 53, 5, 1, '', NULL, '2016-07-13 00:12:13'),
(87, 54, 5, 1, '0B930500L6969052R', 0, '2016-07-14 04:40:52'),
(88, 55, 5, 1, '725352032W530463C', 0, '2016-07-13 00:13:49'),
(89, 56, 5, 1, '2VW730496B4887529', 0, '2016-07-13 00:20:21'),
(90, 57, 5, 1, '31S43834YT3845458', 0, '2016-07-13 00:33:49'),
(91, 34, 5, 1, '35D214387H7325306', 0, '2016-07-13 00:34:54'),
(92, 58, 5, 1, '9FB86075BX821002K', 0, '2016-07-13 04:21:35'),
(93, 59, 5, 1, '1PL70920E7579372E', 0, '2016-07-13 06:10:22'),
(94, 60, NULL, 1, '6R091811C2923902J', 0, '2016-07-13 06:45:23'),
(95, 61, 5, 1, '7R258618CV176691T', 0, '2016-07-13 08:43:40'),
(96, 62, 5, 1, '3885059366487043G', 0, '2016-07-13 10:52:09'),
(97, 63, NULL, 1, '1U665673LG8944811', 0, '2016-07-13 11:33:28'),
(98, 64, 5, 1, '1XJ51722PJ5149924', 0, '2016-07-13 11:36:02'),
(99, 65, 5, 1, '20K205573T846753S', 0, '2016-07-17 02:58:45'),
(100, 66, 5, 1, '25P66640081821907', 0, '2016-07-13 16:09:16'),
(101, 67, 5, 1, '0FL46141MC293152N', 0, '2016-07-13 23:37:39'),
(102, 68, 5, 1, '0GE47196GC705004K', 0, '2016-07-14 01:58:57'),
(103, 69, 1, 1, '2WV32467KW314634V', 0, '2016-07-14 02:42:02'),
(104, 69, 5, 1, '', NULL, '2016-07-14 02:42:04'),
(105, 70, 5, 1, '6R8927689P3559007', 0, '2016-07-14 06:47:34'),
(106, 71, 5, 1, '9FY48339NK155142M', 0, '2016-07-14 07:16:36'),
(107, 10, 5, 1, '5JU219052H452102J', 0, '2016-07-14 16:04:37'),
(108, 72, 3, 1, '1S864596HE689933G', 0, '2016-07-14 20:02:23'),
(109, 51, 1, 1, NULL, 1, '2016-07-15 17:29:02'),
(110, 73, 5, 1, '5X61005620902260K', 0, '2016-07-14 22:09:06'),
(111, 68, 2, 0, NULL, 1, '2016-07-14 22:55:17'),
(112, 68, 1, 1, NULL, 1, '2016-07-14 22:55:15'),
(113, 58, 1, 1, NULL, 1, '2016-07-15 03:14:51'),
(114, 74, 5, 1, '4H168250RX854051R', 0, '2016-07-15 04:19:41'),
(115, 75, 5, 1, '8WR904627D510714E', 0, '2016-07-15 10:21:38'),
(116, 76, NULL, 1, '3PM63803C79867340', 0, '2016-07-15 13:15:42'),
(117, 77, 5, 1, '6GD24221E6101011D', 0, '2016-07-15 14:28:07'),
(118, 78, 5, 1, '00713319D4271030U', 0, '2016-07-15 17:47:17'),
(119, 77, 1, 1, NULL, 1, '2016-07-15 17:23:45'),
(120, 75, 1, 1, NULL, 1, '2016-07-15 17:26:23'),
(121, 74, 1, 1, NULL, 1, '2016-07-15 17:26:33'),
(122, 73, 1, 1, NULL, 1, '2016-07-15 17:26:42'),
(123, 71, 1, 1, NULL, 1, '2016-07-15 17:26:53'),
(124, 70, 1, 1, NULL, 1, '2016-07-15 17:27:03'),
(125, 67, 1, 1, NULL, 1, '2016-07-15 17:27:14'),
(126, 66, 1, 1, NULL, 1, '2016-07-15 17:27:23'),
(127, 65, 1, 1, NULL, 1, '2016-07-15 17:27:31'),
(128, 64, 1, 1, NULL, 1, '2016-07-15 17:27:39'),
(129, 62, 1, 1, NULL, 1, '2016-07-15 17:27:46'),
(130, 61, 1, 1, NULL, 1, '2016-07-15 17:27:55'),
(131, 59, 1, 1, NULL, 1, '2016-07-15 17:28:04'),
(132, 57, 1, 1, NULL, 1, '2016-07-15 17:28:16'),
(133, 56, 1, 1, NULL, 1, '2016-07-15 17:28:25'),
(134, 55, 1, 1, NULL, 1, '2016-07-15 17:28:35'),
(135, 54, 1, 1, NULL, 1, '2016-07-15 17:28:43'),
(136, 52, 1, 1, NULL, 1, '2016-07-15 17:28:53'),
(137, 50, 1, 1, NULL, 1, '2016-07-15 17:29:12'),
(138, 78, 1, 1, NULL, 1, '2016-07-15 17:47:15'),
(139, 79, NULL, 1, '70D697525E3792842', 0, '2016-07-15 18:12:58'),
(140, 79, 1, 1, NULL, 1, '2016-07-15 21:15:59'),
(141, 79, 5, 1, NULL, 1, '2016-07-15 21:16:01'),
(142, 76, 1, 1, NULL, 1, '2016-07-15 21:17:29'),
(143, 76, 5, 1, NULL, 1, '2016-07-15 21:17:31'),
(144, 80, 5, 1, '2AP56394859446133', 0, '2016-07-16 01:28:14'),
(145, 80, 1, 1, '', NULL, '2016-07-16 01:28:17'),
(146, 81, 5, 1, '25E13413L9343612V', 0, '2016-07-16 02:17:31'),
(147, 81, 1, 1, '', NULL, '2016-07-16 02:17:34'),
(148, 65, NULL, 1, '7CA951647L2503645', 0, '2016-07-16 05:20:20'),
(149, 82, 5, 1, '55696963CS417262X', 0, '2016-07-16 09:25:44'),
(150, 82, 1, 1, '', NULL, '2016-07-16 09:25:47'),
(151, 83, NULL, 1, '9JJ45057Y0582815K', 0, '2016-07-17 14:06:21'),
(152, 83, 1, 1, NULL, 1, '2016-07-18 17:58:41'),
(153, 83, 5, 1, NULL, 1, '2016-07-18 17:58:46');

-- --------------------------------------------------------

--
-- Table structure for table `wpiw_users`
--

CREATE TABLE IF NOT EXISTS `wpiw_users` (
  `ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_login` varchar(60) NOT NULL DEFAULT '',
  `user_pass` varchar(255) NOT NULL DEFAULT '',
  `user_nicename` varchar(50) NOT NULL DEFAULT '',
  `user_email` varchar(100) NOT NULL DEFAULT '',
  `user_url` varchar(100) NOT NULL DEFAULT '',
  `user_registered` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `user_activation_key` varchar(255) NOT NULL DEFAULT '',
  `user_status` int(11) NOT NULL DEFAULT '0',
  `display_name` varchar(250) NOT NULL DEFAULT '',
  PRIMARY KEY (`ID`),
  KEY `user_login_key` (`user_login`),
  KEY `user_nicename` (`user_nicename`),
  KEY `user_email` (`user_email`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=84 ;

--
-- Dumping data for table `wpiw_users`
--

INSERT INTO `wpiw_users` (`ID`, `user_login`, `user_pass`, `user_nicename`, `user_email`, `user_url`, `user_registered`, `user_activation_key`, `user_status`, `display_name`) VALUES
(1, 'VCadmin', '$P$BxltCBtY8CDeyfjbSRtJYmpZ2vHlxJ.', 'vcadmin', 'trukn4fun@yahoo.com', 'http://trukn4fun@yahoo.com', '2016-03-12 14:12:30', '', 0, 'Griff'),
(2, 'vickcarty2014@gmail.com', '$P$BVIazIcOhwqNWI91CVx.PuyUqhiFAN/', 'vickcarty2014gmail-com', 'vickcarty2014@gmail.com', '', '2016-05-23 16:49:11', '', 0, 'vickcarty2014@gmail.com'),
(3, 'drenfro56@gmail.com', '$P$BHyxB0jLJIxQzte.c7y0WflGRufzsR/', 'drenfro56gmail-com', 'drenfro56@gmail.com', '', '2016-05-24 01:56:18', '', 0, 'drenfro56@gmail.com'),
(4, 'trukn4fun@gmail.com', '$P$BQyIzt9wAIqM94vHs9hf829L798if4/', 'trukn4fungmail-com', 'trukn4fun@gmail.com', '', '2016-05-24 04:24:09', '', 0, 'trukn4fun@gmail.com'),
(5, 'luann.plan4it@gmail.com', '$P$BAqEDKkGSKPFnHVmxVkU1utTWVMXfV.', 'luann-plan4itgmail-com', 'luann.plan4it@gmail.com', '', '2016-05-24 19:13:58', '', 0, 'luann.plan4it@gmail.com'),
(6, 'deep@invanto.com', '$P$BHWsQQ2JcJkwGZwVlrUWnJehL8n1MP/', 'deepinvanto-com', 'deep@invanto.com', '', '2016-05-27 16:22:50', '', 0, 'deep@invanto.com'),
(7, 'mikemoffitt@me.com', '$P$BB5ICG0IipxNEyxM.WUdfs0pzhwZmX0', 'mikemoffittme-com', 'mikemoffitt@me.com', '', '2016-06-03 16:57:29', '', 0, 'mikemoffitt@me.com'),
(8, 'philskin@gmail.com', '$P$BiYRHoz3.gU0CGJnzQrdJUQ/VhZzym/', 'philskingmail-com', 'philskin@gmail.com', '', '2016-06-03 17:17:25', '', 0, 'philskin@gmail.com'),
(9, 'dseaman@bigboyslifestyle.com', '$P$Bp3jvF7YOsWjhDawdcPHm.HX2Y0sYs.', 'dseamanbigboyslifestyle-com', 'dseaman@bigboyslifestyle.com', '', '2016-06-03 17:50:08', '', 0, 'dseaman@bigboyslifestyle.com'),
(10, 'dan.faschingbauer@gmail.com', '$P$ByYaH.brGov7Sp84FX/4y.q.GITh6i.', 'dan-faschingbauergmail-com', 'dan.faschingbauer@gmail.com', '', '2016-06-03 18:44:38', '', 0, 'dan.faschingbauer@gmail.com'),
(11, 'keithbenjamin62@gmail.com', '$P$BJdmM.qMYhYwvRE8llLyJvTJRB9yxV0', 'keithbenjamin62gmail-com', 'keithbenjamin62@gmail.com', '', '2016-06-03 18:49:48', '', 0, 'keithbenjamin62@gmail.com'),
(12, 'gallen2000@cstel.net', '$P$BIS.2dnbHSxw5oL53QQ9VuuDcDpZD31', 'gallen2000cstel-net', 'gallen2000@cstel.net', '', '2016-06-03 20:01:40', '', 0, 'gallen2000@cstel.net'),
(13, 'traffic@ultralightnews.com', '$P$B.Ojkv9PpLw4vWr69JT/1CRQs/6Djg.', 'trafficultralightnews-com', 'traffic@ultralightnews.com', '', '2016-06-03 21:08:13', '', 0, 'traffic@ultralightnews.com'),
(14, 'robertsheppard777@yahoo.com', '$P$BIJ7.lBx2GVoAsFpWq1fohjoOwsOi8.', 'robertsheppard777yahoo-com', 'robertsheppard777@yahoo.com', '', '2016-06-03 21:11:20', '', 0, 'robertsheppard777@yahoo.com'),
(15, 'viralprotege@gmail.com', '$P$BYr5hbYMgP/XTBaz84/Xyuv8l3D2pt1', 'viralprotegegmail-com', 'viralprotege@gmail.com', '', '2016-06-03 21:45:58', '', 0, 'viralprotege@gmail.com'),
(16, 'gregemoten@gmail.com', '$P$BUEFisf6D7eFsMmf6NsiiTPS5iExJr1', 'gregemotengmail-com', 'gregemoten@gmail.com', '', '2016-06-03 21:53:45', '', 0, 'gregemoten@gmail.com'),
(17, 'rebecca.happy1@gmail.com', '$P$BzHeA2k8SzatQCpH0wN18uhna3Aqs80', 'rebecca-happy1gmail-com', 'rebecca.happy1@gmail.com', '', '2016-06-03 22:38:51', '', 0, 'Rebecca'),
(18, 'textwizardmobile@gmail.com', '$P$BcWnO9cL/mK9MHNSWn7NgI20xeYrxd0', 'textwizardmobilegmail-com', 'textwizardmobile@gmail.com', '', '2016-06-03 22:42:10', '', 0, 'textwizardmobile@gmail.com'),
(19, '20.50jirah@gmail.com', '$P$BFHtIHca3CMwkjkOaKu0nIAMe4oBCI1', '20-50jirahgmail-com', '20.50jirah@gmail.com', '', '2016-06-03 22:45:37', '', 0, '20.50jirah@gmail.com'),
(20, 'eltonbrown@bellsouth.net', '$P$BQZdK7XCMCmb71FcqmXC.jFMsmCdTF0', 'eltonbrownbellsouth-net', 'eltonbrown@bellsouth.net', '', '2016-06-04 00:08:32', '', 0, 'eltonbrown@bellsouth.net'),
(21, 'winthefcg@gmail.com', '$P$BsTYR0mzOgSTD5ZzH0W1sQWYA0fHfG.', 'winthefcggmail-com', 'winthefcg@gmail.com', '', '2016-06-04 13:49:20', '', 0, 'winthefcg@gmail.com'),
(22, 'getbrainfuel@gmail.com', '$P$BR6LCZkp3tPPDXaUZ03sv5RoL2qe7B0', 'getbrainfuelgmail-com', 'getbrainfuel@gmail.com', '', '2016-06-04 13:53:15', '', 0, 'getbrainfuel@gmail.com'),
(23, 'quartz46@videotron.ca', '$P$BOcWVEzjMwAPKE904THEdWpJLquHnC/', 'quartz46videotron-ca', 'quartz46@videotron.ca', '', '2016-06-04 15:08:27', '', 0, 'quartz46@videotron.ca'),
(24, 'goran.olson@gmail.com', '$P$BCy93i5us/WmfC/PG.FMrsrRK68KLY/', 'goran-olsongmail-com', 'goran.olson@gmail.com', '', '2016-06-04 18:51:51', '', 0, 'goran.olson@gmail.com'),
(25, 'bargainsparadise@aol.com', '$P$BV4kUlKnxR71aTEWb/zlYdisuTeGDT1', 'bargainsparadiseaol-com', 'bargainsparadise@aol.com', '', '2016-06-04 19:12:21', '', 0, 'bargainsparadise@aol.com'),
(26, 'chincoteaguebill@gmail.com', '$P$BFOE09y15d8.SQgX0LocB66P5gfbGj0', 'chincoteaguebillgmail-com', 'chincoteaguebill@gmail.com', '', '2016-06-04 21:00:56', '', 0, 'chincoteaguebill@gmail.com'),
(27, 'joashindustries@gmail.com', '$P$BSkaAzSlLsgkR/tz/k8JrsZDgkK9rR1', 'joashindustriesgmail-com', 'joashindustries@gmail.com', '', '2016-06-04 21:53:20', '', 0, 'joashindustries@gmail.com'),
(28, 'partech@hotmail.co.uk', '$P$Bok6DnoZd6VLVswYkt5YBreGun2pLq.', 'partechhotmail-co-uk', 'partech@hotmail.co.uk', '', '2016-06-05 09:35:48', '', 0, 'partech@hotmail.co.uk'),
(29, 'jeff@brainercises.com', '$P$Bbv5Y8cWH0OGq2ZD6FnnGPo/I8sFzE.', 'jeffbrainercises-com', 'jeff@brainercises.com', '', '2016-06-05 12:45:03', '', 0, 'jeff@brainercises.com'),
(30, 'supportdesk@postkasse.com', '$P$BlBEm6gKydmDmiYFQNekUJ41chBnQA0', 'supportdeskpostkasse-com', 'supportdesk@postkasse.com', '', '2016-06-05 15:39:17', '', 0, 'supportdesk@postkasse.com'),
(31, 'instant-access@sbcglobal.net', '$P$BsQc5cf5IVDljKY2O73QEdARdgt3dY1', 'instant-accesssbcglobal-net', 'instant-access@sbcglobal.net', '', '2016-06-05 17:20:32', '', 0, 'instant-access@sbcglobal.net'),
(32, 'info@trimwellfinancial.com', '$P$BQud4ZJLSDtVCBPkPM0bEQTQhwaaDP/', 'infotrimwellfinancial-com', 'info@trimwellfinancial.com', '', '2016-06-05 18:39:45', '', 0, 'info@trimwellfinancial.com'),
(33, 'toddwx@gmail.com', '$P$BqNkN5U2P2dki9YNKejspBNnIC9Pfw1', 'toddwxgmail-com', 'toddwx@gmail.com', '', '2016-06-06 04:45:53', '', 0, 'toddwx@gmail.com'),
(34, 'parkerae@gmail.com', '$P$Bg45zbMpsIv96rFf5Lr2Uh4gsFOtgP.', 'parkeraegmail-com', 'parkerae@gmail.com', '', '2016-06-06 05:23:44', '', 0, 'parkerae@gmail.com'),
(35, 'philgy1@aol.com', '$P$BHSTo654WegI/9Q4YSIuBg/tzugqCn1', 'philgy1aol-com', 'philgy1@aol.com', '', '2016-06-06 14:52:05', '', 0, 'philgy1@aol.com'),
(36, 'jw@jw33.com', '$P$Bt/uhktZI6d3ijJcBLr595934eMUHv0', 'jwjw33-com', 'jw@jw33.com', '', '2016-06-08 19:57:42', '', 0, 'jw@jw33.com'),
(37, 'williemem@aol.com', '$P$BblzShcD6fy7uQKf2mSqfaKrf8hwAI.', 'williememaol-com', 'williemem@aol.com', '', '2016-06-09 07:23:05', '', 0, 'williemem@aol.com'),
(38, 'davidhubbardsw@yahoo.com', '$P$Bp4W2cxDNaXISs2jm013v6ADBZC9hE1', 'davidhubbardswyahoo-com', 'davidhubbardsw@yahoo.com', '', '2016-06-09 14:17:03', '', 0, 'davidhubbardsw@yahoo.com'),
(39, 'steen@sativa.dk', '$P$BeDX/kgMiuxiqlP4KLRs.P3AmvqZfr/', 'steensativa-dk', 'steen@sativa.dk', '', '2016-06-10 10:55:51', '', 0, 'steen@sativa.dk'),
(40, 'wtccmarketing@gmail.com', '$P$BXYvN.EGO8P0cbpYZ9tRtpe7qvjXzH/', 'wtccmarketinggmail-com', 'wtccmarketing@gmail.com', '', '2016-06-12 01:53:58', '', 0, 'wtccmarketing@gmail.com'),
(41, 'chef.andyn@yahoo.com', '$P$BKWhLZEMdC1GKl5YMwv3eUWnwu33fj0', 'chef-andynyahoo-com', 'chef.andyn@yahoo.com', '', '2016-06-15 10:48:06', '', 0, 'chef.andyn@yahoo.com'),
(42, 'ashwin1@btinternet.com', '$P$BLDkooRsbEpoIIdH4RawuAmE5RHf0q.', 'ashwin1btinternet-com', 'ashwin1@btinternet.com', '', '2016-06-18 16:51:04', '', 0, 'ashwin1@btinternet.com'),
(43, 's.meehan@live.com.au', '$P$Br1SBtMvZp5d7wNg5bEHdIuNB/tsN31', 's-meehanlive-com-au', 's.meehan@live.com.au', '', '2016-06-20 05:31:51', '1466422638:$P$B/zk6p9/eN.kqIid4Q2TvdIZxoilMz0', 0, 's.meehan@live.com.au'),
(44, 'hawk8d@gmail.com', '$P$B/QW0KOgeTtFzrGDXI.yR5ODAji34z0', 'hawk8dgmail-com', 'hawk8d@gmail.com', '', '2016-06-23 09:34:49', '', 0, 'hawk8d@gmail.com'),
(45, 'picturepros@verizon.net', '$P$B043G73GJhkcdBkA8GzDXSZE5/NXFq.', 'pictureprosverizon-net', 'picturepros@verizon.net', '', '2016-06-30 22:32:29', '', 0, 'picturepros@verizon.net'),
(46, 'manny@tsimedbill.com', '$P$BwMUNH7gAy0XLjyKRAKklNevZCIeGz0', 'mannytsimedbill-com', 'manny@tsimedbill.com', '', '2016-07-02 18:37:04', '', 0, 'manny@tsimedbill.com'),
(47, 'ronmathwell@gmail.com', '$P$Bxo.mhE0cSgcGvqV70ZSePDsWfAUN/.', 'ronmathwellgmail-com', 'ronmathwell@gmail.com', '', '2016-07-03 03:45:37', '', 0, 'ronmathwell@gmail.com'),
(48, 'drq@drqdc.com', '$P$Ba8LLIDps1c/HV5mCFt0uM9495fZ7C/', 'drqdrqdc-com', 'drq@drqdc.com', '', '2016-07-06 02:20:47', '', 0, 'drq@drqdc.com'),
(49, 'mikeuribe06@gmail.com', '$P$BUGBkWyWTRn5Rxu8HeOEdVqNq8iYDE1', 'mikeuribe06gmail-com', 'mikeuribe06@gmail.com', '', '2016-07-12 19:45:01', '', 0, 'mikeuribe06@gmail.com'),
(50, 'jondinga@gmail.com', '$P$BJv5/7RO4b/RIitJ/3VzImnwLvsMQY1', 'jondingagmail-com', 'jondinga@gmail.com', '', '2016-07-13 00:06:10', '', 0, 'jondinga@gmail.com'),
(51, 'dave@owenclan.com', '$P$BSQ8cPGP9.l28q3tR5wdtvdCUqIuRS.', 'daveowenclan-com', 'Dave@owenclan.net', '', '2016-07-13 00:06:59', '', 0, 'Dave Owen'),
(52, 'bf4004@gmail.com', '$P$BTS0NVRWlGGpG0JLyGAZ8HrpGMiZb3.', 'bf4004gmail-com', 'bf4004@gmail.com', '', '2016-07-13 00:08:19', '', 0, 'bf4004@gmail.com'),
(53, 'renata5717@yahoo.com', '$P$Bv1peFNcBK4ugGQzYDkH2caQqKWd/K.', 'renata5717yahoo-com', 'renata5717@yahoo.com', '', '2016-07-13 00:12:34', '', 0, 'renata5717@yahoo.com'),
(54, 'caldwell.rick@gmail.com', '$P$BtDalA32f/ywT6d/W0c77WrJ9u72V2.', 'caldwell-rickgmail-com', 'caldwell.rick@gmail.com', '', '2016-07-13 00:13:23', '', 0, 'caldwell.rick@gmail.com'),
(55, 'hbepurchases@gmail.com', '$P$B0puxAhL0baFmwtDWOVrhOs8t6hx8i.', 'hbepurchasesgmail-com', 'hbepurchases@gmail.com', '', '2016-07-13 00:14:13', '', 0, 'hbepurchases@gmail.com'),
(56, 'wes@connectionsperformance.com', '$P$BRDTlAQ0RnaOzSsm3MiyCMHZ1iLyfH1', 'wesconnectionsperformance-com', 'wes@connectionsperformance.com', '', '2016-07-13 00:20:45', '', 0, 'wes@connectionsperformance.com'),
(57, 'bill@billdowning.net', '$P$BNtGuDrmepwOkaoGoRJWcPM7HjK5K20', 'billbilldowning-net', 'bill@billdowning.net', '', '2016-07-13 00:34:13', '', 0, 'bill@billdowning.net'),
(58, 'sdyer58@gmail.com', '$P$BQD9zD9kbvE5n59ItdfMcrp39ODEMj.', 'sdyer58gmail-com', 'sdyer58@gmail.com', '', '2016-07-13 04:21:59', '', 0, 'sdyer58@gmail.com'),
(59, 'info@nostringsmarketing.com', '$P$ByAwv5Lgb/bIABbR7EIHR1Q2.12QO40', 'infonostringsmarketing-com', 'info@nostringsmarketing.com', '', '2016-07-13 06:10:46', '', 0, 'info@nostringsmarketing.com'),
(60, 'rknapp1@rochester.rr.com', '$P$BILD0pairbk14K90SqK6IMzxgjli.h1', 'rknapp1rochester-rr-com', 'rknapp1@rochester.rr.com', '', '2016-07-13 06:45:47', '', 0, 'rknapp1@rochester.rr.com'),
(61, 'wenkmagic@yahoo.com', '$P$BNL9j0v4bIiz9LhoO.Mot1MYsg2Nhj1', 'wenkmagicyahoo-com', 'wenkmagic@yahoo.com', '', '2016-07-13 08:44:04', '', 0, 'wenkmagic@yahoo.com'),
(62, 'longqualitylife@yahoo.com', '$P$BTw2iHH29IwYCiIUtk52u2zY44tki20', 'longqualitylifeyahoo-com', 'longqualitylife@yahoo.com', '', '2016-07-13 10:52:33', '', 0, 'longqualitylife@yahoo.com'),
(63, 'deejayhart.nyc@gmail.com', '$P$B/f03q3p.XeT0WS9Hag2r3OjlCx53L/', 'deejayhart-nycgmail-com', 'deejayhart.nyc@gmail.com', '', '2016-07-13 11:33:51', '', 0, 'deejayhart.nyc@gmail.com'),
(64, 'azmindfull@gmail.com', '$P$B2A1eP/WDGPj.tgWHQv6BA76cfPS6y0', 'azmindfullgmail-com', 'azmindfull@gmail.com', '', '2016-07-13 11:36:26', '', 0, 'azmindfull@gmail.com'),
(65, 'joe-jaikishun@hotmail.com', '$P$Bcd4oyc3m0F6chLAE2WV7X7K8bBIkT.', 'joe-jaikishunhotmail-com', 'joe-jaikishun@hotmail.com', '', '2016-07-13 14:17:21', '1468725168:$P$B/1QqMARi50xTvseE2q6mhQ29/r7BT1', 0, 'joe-jaikishun@hotmail.com'),
(66, 'dianajohnson909@googlemail.com', '$P$B7njIsUaRnur731ApQ3M4SgpCwlYjF0', 'dianajohnson909googlemail-com', 'dianajohnson909@googlemail.com', '', '2016-07-13 16:09:40', '', 0, 'dianajohnson909@googlemail.com'),
(67, 'ricklapp8@gmail.com', '$P$Bz24QwniiGZqPpOYxAPIu5/IEeD.S6.', 'ricklapp8gmail-com', 'ricklapp8@gmail.com', '', '2016-07-13 23:38:03', '', 0, 'ricklapp8@gmail.com'),
(68, 'fred@fredgillen.com', '$P$BlQz2hQRifZnFrUSTO5GE0bvqOxCYL/', 'fredfredgillen-com', 'fred@fredgillen.com', '', '2016-07-14 01:59:21', '', 0, 'fred@fredgillen.com'),
(69, 'jimd0320@gmail.com', '$P$BeQiOlnEXAZvV.g/VMq2baD9/Hv0es1', 'jimd0320gmail-com', 'jimd0320@gmail.com', '', '2016-07-14 02:42:26', '', 0, 'jimd0320@gmail.com'),
(70, 'guskamassah@gmail.com', '$P$BQLzx3NAAalxM8lvJdzIGeKmhN2jwi1', 'guskamassahgmail-com', 'guskamassah@gmail.com', '', '2016-07-14 06:47:58', '', 0, 'guskamassah@gmail.com'),
(71, 'akstrategies@yahoo.com', '$P$BBp9PlXjmd/85crMfczXTqFCqD3D1t/', 'akstrategiesyahoo-com', 'akstrategies@yahoo.com', '', '2016-07-14 07:17:00', '', 0, 'akstrategies@yahoo.com'),
(72, 'rondispatch@aim.com', '$P$B9RfUZMJKNSUnZ3Twle5vtHCaHMz6K0', 'rondispatchaim-com', 'rondispatch@aim.com', '', '2016-07-14 20:02:47', '', 0, 'rondispatch@aim.com'),
(73, 'getclientsvideo@gmail.com', '$P$BE5J741uSrMIv/HEE6k/pU1aCiuKJE/', 'getclientsvideogmail-com', 'getclientsvideo@gmail.com', 'http://www.getclients.co/', '2016-07-14 22:09:30', '', 0, 'Scott'),
(74, 'multifamily2012@gmail.com', '$P$Bq8haafdN7Jo19PfSU/nKRDKs7Go820', 'multifamily2012gmail-com', 'multifamily2012@gmail.com', '', '2016-07-15 04:20:05', '', 0, 'multifamily2012@gmail.com'),
(75, 'ggeorgiev47@gmail.com', '$P$BAu1NvW0SL0qtskFRUOiwLp92zcbUC1', 'ggeorgiev47gmail-com', 'ggeorgiev47@gmail.com', '', '2016-07-15 10:22:02', '', 0, 'ggeorgiev47@gmail.com'),
(76, 'eaglespt@gmail.com', '$P$Be7AciAamSl8KN/Nfuzx5H/1cxdlvU0', 'eaglesptgmail-com', 'eaglespt@gmail.com', '', '2016-07-15 13:16:06', '', 0, 'eaglespt@gmail.com'),
(77, 'info@seearchpointmarketing.com', '$P$Bz8KQLwBonyHbNLBpWA/Zq.NvAJQda0', 'infoseearchpointmarketing-com', 'info@searchpointmarketing.com', '', '2016-07-15 14:28:31', '1468798692:$P$BkEqtNJ7IF3ew5x95kcw.h1QwsbC8//', 0, 'John Thoma'),
(78, 'eransch@bluewin.ch', '$P$BgnyGf2aZI8BdF7piuQ5Uov4VBk4yp/', 'eranschbluewin-ch', 'eransch@bluewin.ch', '', '2016-07-15 15:24:58', '', 0, 'eransch@bluewin.ch'),
(79, 'macristdds@yahoo.com', '$P$BWLyulSFbK/tTZ0733EImDKHd6012i.', 'macristddsyahoo-com', 'macristdds@yahoo.com', '', '2016-07-15 18:13:22', '', 0, 'macristdds@yahoo.com'),
(80, 'jeffhamot@gmail.com', '$P$BuWcb7zBBA.AFRkiOpzjWpKO4rzUcH1', 'jeffhamotgmail-com', 'jeffhamot@gmail.com', '', '2016-07-16 01:28:38', '', 0, 'jeffhamot@gmail.com'),
(81, 'stjohn4500@gmail.com', '$P$B5Gkg87exkiFjWUOdvcDXO7g.iYma6/', 'stjohn4500gmail-com', 'stjohn4500@gmail.com', '', '2016-07-16 02:17:55', '', 0, 'stjohn4500@gmail.com'),
(82, 'barkingdogsmarketing@gmail.com', '$P$B3aYedvClPIHRG1lwo3wD3DpOvlHix1', 'barkingdogsmarketinggmail-com', 'barkingdogsmarketing@gmail.com', '', '2016-07-16 09:26:08', '', 0, 'barkingdogsmarketing@gmail.com'),
(83, 'joanbeaulieu@gmail.com', '$P$B2k.zmrfXSd16MCuYqQXnKxcN1y.Mk0', 'joanbeaulieugmail-com', 'joanbeaulieu@gmail.com', '', '2016-07-17 14:06:45', '', 0, 'joanbeaulieu@gmail.com');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
