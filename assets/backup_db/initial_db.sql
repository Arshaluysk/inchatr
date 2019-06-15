-- phpMyAdmin SQL Dump
-- version 3.2.4
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Apr 03, 2016 at 03:06 PM
-- Server version: 5.1.41
-- PHP Version: 5.3.1

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";



--
-- Table structure for table `ci_sessions`
--

CREATE TABLE IF NOT EXISTS `ci_sessions` (
    `id` varchar(128) NOT NULL,
    `ip_address` varchar(45) NOT NULL,
    `timestamp` int(10) unsigned DEFAULT 0 NOT NULL,
    `data` blob NOT NULL,
    KEY `ci_sessions_timestamp` (`timestamp`)
);


--
-- Table structure for table `email_config`
--

CREATE TABLE IF NOT EXISTS `email_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `email_address` varchar(100) NOT NULL,
  `smtp_host` varchar(100) NOT NULL,
  `smtp_port` varchar(100) NOT NULL,
  `smtp_user` varchar(100) NOT NULL,
  `smtp_password` varchar(100) NOT NULL,
  `status` enum('0','1') NOT NULL,
  `deleted` enum('0','1') NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `email_config`
--




--
-- Table structure for table `forget_password`
--

CREATE TABLE IF NOT EXISTS `forget_password` (
  `id` int(12) NOT NULL AUTO_INCREMENT,
  `confirmation_code` varchar(15) CHARACTER SET utf8 NOT NULL,
  `email` varchar(100) CHARACTER SET utf8 NOT NULL,
  `success` int(11) NOT NULL DEFAULT '0',
  `expiration` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `forget_password`
--



--
-- Table structure for table `native_api`
--

CREATE TABLE IF NOT EXISTS `native_api` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `api_key` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `native_api`
--

--
-- Table structure for table `payment_config`
--

CREATE TABLE IF NOT EXISTS `payment_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `paypal_email` varchar(250) NOT NULL,
  `currency` enum('USD','AUD','CAD','EUR','ILS','NZD','RUB','SGD','SEK') NOT NULL,
  `deleted` enum('0','1') NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

INSERT INTO `payment_config` (`id`, `paypal_email`, `currency`, `deleted`) VALUES
(1, 'Paypalemail@example.com', 'USD', '0');
--
-- Dumping data for table `payment_config`
--


--
-- Table structure for table `transaction_history`
--

CREATE TABLE IF NOT EXISTS `transaction_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `verify_status` varchar(200) NOT NULL,
  `first_name` varchar(250) CHARACTER SET utf8 NOT NULL,
  `last_name` varchar(250) CHARACTER SET utf8 NOT NULL,
  `paypal_email` varchar(200) NOT NULL,
  `receiver_email` varchar(200) NOT NULL,
  `country` varchar(100) NOT NULL,
  `payment_date` varchar(250) NOT NULL,
  `payment_type` varchar(100) NOT NULL,
  `transaction_id` varchar(150) NOT NULL,
  `paid_amount` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `cycle_start_date` date NOT NULL,
  `cycle_expired_date` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `transaction_history`
--
ALTER TABLE `transaction_history` ADD `package_id` INT NOT NULL;
ALTER TABLE `transaction_history` ADD `stripe_card_source` TEXT NOT NULL; 

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`name` varchar(99) CHARACTER SET utf8 NOT NULL,
`email` varchar(99) NOT NULL,
`mobile` varchar(100) NOT NULL,
`password` varchar(99) NOT NULL,
`address` text CHARACTER SET utf8 NOT NULL,
`user_type` enum('Member','Admin') NOT NULL,
`status` enum('1','0') NOT NULL,
`add_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
`purchase_date` datetime NOT NULL,
`last_login_at` datetime NOT NULL,
`activation_code` varchar(20) DEFAULT NULL,
`expired_date` datetime NOT NULL,
`package_id` int(11) NOT NULL,
`deleted` enum('0','1') NOT NULL,
`brand_logo` text CHARACTER SET utf8,
`brand_url` text CHARACTER SET utf8,
`vat_no` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
`currency` enum('USD','AUD','CAD','EUR','ILS','NZD','RUB','SGD','SEK','BRL') NOT NULL DEFAULT 'USD',
`company_email` varchar(200) DEFAULT NULL,
`paypal_email` varchar(100) NOT NULL,
`last_login_ip` varchar(25) NOT NULL,
PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `mobile`, `password`, `address`, `user_type`, `status`, `add_date`, `activation_code`, `expired_date`, `deleted`) VALUES
(1, 'Xerone IT', 'admin@gmail.com', '+88 01729 853 6452', '259534db5d66c3effb7aa2dbbee67ab0', 'Rajshahi', 'Admin', '1', '2016-01-01 00:00:00', NULL, '0000-00-00 00:00:00', '0');

-- --------------------------------------------------------




  
CREATE TABLE IF NOT EXISTS `modules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `module_name` varchar(250) CHARACTER SET utf8 DEFAULT NULL,
  `deleted` enum('0','1') NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=84;

INSERT INTO `modules` (`id`, `module_name`, `deleted`) VALUES (28, 'FB Chat Plugin', '0');
INSERT INTO `modules` (`id`, `module_name`, `deleted`) VALUES (65, 'Account Import', '0');
INSERT INTO `modules` (`id`, `module_name`, `deleted`) VALUES (69, 'CTA Poster', '0');
INSERT INTO `modules` (`id`, `module_name`, `deleted`) VALUES (76, 'Campaign', '0');
INSERT INTO `modules` (`id`, `module_name`, `deleted`) VALUES (77, 'Messge Send Button', '0');
INSERT INTO `modules` (`id`, `module_name`, `deleted`) VALUES (78, 'Daily Auto Lead Scan', '0');
INSERT INTO `modules` (`id`, `module_name`, `deleted`) VALUES (79, 'Send Message', '0');
INSERT INTO `modules` (`id`, `module_name`, `deleted`) VALUES (80, 'Auto Private Reply', '0');
INSERT INTO `modules` (`id`, `module_name`, `deleted`) VALUES (81, 'Messenger Ad JSON Script', '0');
INSERT INTO `modules` (`id`, `module_name`, `deleted`) VALUES (82, 'Page Inbox Manager', '0');
INSERT INTO `modules` (`id`, `module_name`, `deleted`) VALUES (83, 'Page Notification Manager', '0');

-- module insert will goes here by mostofa

CREATE TABLE `package` (
`id` INT NOT NULL AUTO_INCREMENT ,
`package_name` VARCHAR( 250 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
`module_ids` VARCHAR( 250 ) NOT NULL ,
`price` FLOAT NOT NULL ,
`deleted` INT NOT NULL ,
PRIMARY KEY ( `id` )
) ENGINE = InnoDB ;

ALTER TABLE `package` CHANGE `deleted` `deleted` ENUM( '0', '1' ) NOT NULL;
ALTER TABLE `package` ADD `validity` INT NOT NULL AFTER `price`;
ALTER TABLE `package` ADD `is_default` ENUM( '0', '1' ) NOT NULL AFTER `validity`;
ALTER TABLE `package` CHANGE `is_default` `is_default` ENUM( '0', '1' ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `package` CHANGE `price` `price` VARCHAR( 20 ) NOT NULL DEFAULT '0';
INSERT INTO `package` (`id`, `package_name`, `module_ids`, `price`, `validity`, `is_default`, `deleted`) VALUES (1, 'Trial', '1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20', 'Trial', '7', '1', '0');


ALTER TABLE `package` ENGINE = InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;



ALTER TABLE `package` ADD `monthly_limit` TEXT NULL AFTER `module_ids` ,
ADD `bulk_limit` TEXT NULL AFTER `monthly_limit`;
UPDATE `package` SET `package_name` = 'Trial',
`module_ids` = '65,69,76,77,78,79,80,81,82,83,12,84,28,13,1,2',
`monthly_limit` = '{"65":0,"69":0,"76":0,"77":0,"78":0,"79":"0","80":0,"81":0,"82":0,"83":0,"12":0,"84":0,"28":0,"13":0,"1":0,"2":0}',
`bulk_limit` = '{"65":0,"69":0,"76":0,"77":0,"78":0,"79":"0","80":0,"81":0,"82":0,"83":0,"12":0,"84":0,"28":0,"13":0,"1":0,"2":0}',
`price` = 'Trial' WHERE `package`.`id` =1;


CREATE TABLE `usage_log` (
`id` BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`module_id` INT NOT NULL ,
`user_id` INT NOT NULL ,
`usage_month` INT NOT NULL ,
`usage_year` YEAR NOT NULL ,
`usage_count` INT NOT NULL
) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci;



CREATE TABLE `login_config` (
`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`api_id` VARCHAR( 250 ) NULL ,
`api_secret` VARCHAR( 250 ) NULL ,
`google_client_id` VARCHAR( 250 ) NULL ,
`google_client_secret` VARCHAR( 250 ) NULL ,
`status` ENUM( '0', '1' ) NOT NULL DEFAULT '1',
`deleted` ENUM( '0', '1' ) NOT NULL DEFAULT '0'
) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci;
ALTER TABLE `login_config` ADD `app_name` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL AFTER `id` ;
ALTER TABLE `login_config` CHANGE `google_client_id` `google_client_id` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;

ALTER TABLE `payment_config` CHANGE `currency` `currency` ENUM( 'USD', 'AUD', 'CAD', 'EUR', 'ILS', 'NZD', 'RUB', 'SGD', 'SEK', 'BRL' ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;



ALTER TABLE `payment_config` ADD `stripe_secret_key` VARCHAR( 150 ) NOT NULL AFTER `paypal_email`;

ALTER TABLE `payment_config` ADD `stripe_publishable_key` VARCHAR( 150 ) NOT NULL AFTER `stripe_secret_key`;





CREATE TABLE `facebook_config` (
  `id` int(11) NOT NULL,
  `app_name` varchar(100) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `api_id` varchar(250) DEFAULT NULL,
  `api_secret` varchar(250) DEFAULT NULL,
  `user_access_token` varchar(500) DEFAULT NULL,
  `status` enum('0','1') NOT NULL DEFAULT '1',
  `deleted` enum('0','1') NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



ALTER TABLE `facebook_config`
  ADD PRIMARY KEY (`id`);



ALTER TABLE `facebook_config`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;



ALTER TABLE `login_config` ADD `user_access_token` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `api_secret`;



CREATE
 ALGORITHM = UNDEFINED
 VIEW `view_usage_log`
 (id,module_id,user_id,usage_month,usage_year,usage_count)
 AS select * from usage_log where `usage_month`=MONTH(curdate()) and `usage_year`= YEAR(curdate()) ;



CREATE TABLE IF NOT EXISTS `facebook_page_insight_page_list` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `page_id` varchar(250) DEFAULT NULL,
  `page_name` text,
  `page_email` varchar(250) DEFAULT NULL,
  `page_cover` longtext,
  `page_profile` longtext,
  `page_access_token` longtext,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;




CREATE TABLE IF NOT EXISTS `email` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `domain_id` int(11) NOT NULL,
  `url_id` int(11) NOT NULL,
  `search_engine_url_id` int(11) NOT NULL,
  `found_email` varchar(250) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;



ALTER TABLE `payment_config` CHANGE `currency` `currency` ENUM('USD','AUD','CAD','EUR','ILS','NZD','RUB','SGD','SEK','BRL','VND') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;



CREATE TABLE IF NOT EXISTS `facebook_rx_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `app_name` varchar(100) DEFAULT NULL,
  `api_id` varchar(250) DEFAULT NULL,
  `api_secret` varchar(250) DEFAULT NULL,
  `numeric_id` text NOT NULL,
  `user_access_token` varchar(500) DEFAULT NULL,
  `status` enum('0','1') NOT NULL DEFAULT '1',
  `deleted` enum('0','1') NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `facebook_rx_conversion_user_list` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `page_table_id` int(11) NOT NULL COMMENT 'page table auto id',
  `page_id` varchar(200) NOT NULL,
  `contact_group_id` varchar(255) NOT NULL,
  `client_username` varchar(200) NOT NULL,
  `client_thread_id` varchar(200) NOT NULL,
  `client_id` varchar(200) NOT NULL,
  `permission` enum('0','1') NOT NULL,
  `subscribed_at` datetime NOT NULL,
  `unsubscribed_at` datetime NOT NULL,
  `link` text NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `Client_unique` (`page_table_id`,`client_thread_id`,`client_id`),
  KEY `userid and permission` (`user_id`,`permission`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `facebook_rx_fb_group_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `facebook_rx_fb_user_info_id` int(11) NOT NULL,
  `group_id` varchar(200) NOT NULL,
  `group_cover` text,
  `group_profile` text,
  `group_name` varchar(200) DEFAULT NULL,
  `group_access_token` text NOT NULL,
  `add_date` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;



CREATE TABLE IF NOT EXISTS `facebook_rx_fb_page_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `facebook_rx_fb_user_info_id` int(11) NOT NULL,
  `page_id` varchar(200) NOT NULL,
  `page_cover` text,
  `page_profile` text,
  `page_name` varchar(200) DEFAULT NULL,
  `username` varchar(255) NOT NULL,
  `page_access_token` text NOT NULL,
  `page_email` varchar(200) DEFAULT NULL,
  `add_date` date NOT NULL,
  `deleted` enum('0','1') NOT NULL DEFAULT '0',
  `auto_sync_lead` enum('0','1','2','3') NOT NULL DEFAULT '0' COMMENT '0=disabled,1=enabled,2=processing,3=completed',
  `last_lead_sync` datetime NOT NULL,
  `next_scan_url` text NOT NULL,
  `current_lead_count` int(11) NOT NULL,
  `current_subscribed_lead_count` int(11) NOT NULL,
  `current_unsubscribed_lead_count` int(11) NOT NULL,
  `msg_manager` enum('0','1') NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `page_id` (`page_id`),
  KEY `user_id` (`user_id`,`page_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;



CREATE TABLE IF NOT EXISTS `facebook_rx_fb_user_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `facebook_rx_config_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `access_token` text NOT NULL,
  `name` varchar(200) DEFAULT NULL,
  `email` varchar(200) DEFAULT NULL,
  `fb_id` varchar(200) NOT NULL,
  `add_date` date NOT NULL,
  `deleted` enum('0','1') NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;



ALTER TABLE `facebook_rx_config` CHANGE `numeric_id` `numeric_id` VARCHAR(250) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;

ALTER TABLE `facebook_rx_fb_group_info` ADD `deleted` ENUM('0','1') NOT NULL DEFAULT '0' AFTER `add_date`;


ALTER TABLE `facebook_rx_config` ADD `user_id` INT NOT NULL AFTER `deleted`;
ALTER TABLE `facebook_rx_config` ADD `use_by` ENUM('only_me','everyone') NOT NULL DEFAULT 'only_me' AFTER `user_id`;



-- added by mostofa 26/02/2017

CREATE TABLE IF NOT EXISTS `ad_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `section1_html` longtext,
  `section1_html_mobile` longtext,
  `section2_html` longtext,
  `section3_html` longtext,
  `section4_html` longtext,
  `status` enum('0','1') NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `facebook_ex_autoreply` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `facebook_rx_fb_user_info_id` int(11) NOT NULL,
  `auto_reply_campaign_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `page_info_table_id` int(11) NOT NULL,
  `page_name` mediumtext COLLATE utf8mb4_unicode_ci,
  `post_id` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `post_created_at` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `post_description` longtext COLLATE utf8mb4_unicode_ci,
  `post_thumb` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `reply_type` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `auto_like_comment` enum('no','yes') COLLATE utf8mb4_unicode_ci NOT NULL,
  `multiple_reply` enum('no','yes') COLLATE utf8mb4_unicode_ci NOT NULL,
  `comment_reply_enabled` enum('no','yes') COLLATE utf8mb4_unicode_ci NOT NULL,
  `nofilter_word_found_text` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `auto_reply_text` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `auto_private_reply_status` enum('0','1','2') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `auto_private_reply_count` int(11) NOT NULL,
  `auto_private_reply_done_ids` longtext COLLATE utf8mb4_unicode_ci,
  `auto_reply_done_info` longtext COLLATE utf8mb4_unicode_ci,
  `last_updated_at` datetime NOT NULL,
  `last_reply_time` datetime NOT NULL,
  `error_message` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `hide_comment_after_comment_reply` enum('no','yes') COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_delete_offensive` enum('hide','delete') COLLATE utf8mb4_unicode_ci NOT NULL,
  `offensive_words` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `private_message_offensive_words` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `hidden_comment_count` int(11) NOT NULL,
  `deleted_comment_count` int(11) NOT NULL,
  `auto_comment_reply_count` int(11) NOT NULL,
  `template_manager_table_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`,`page_info_table_id`,`post_id`),
  KEY `dashboard` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `facebook_ex_conversation_campaign` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(20) NOT NULL,
  `group_ids` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'comma seperated group ids if group wise',
  `page_ids` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'comma separated page auto ids',
  `fb_page_ids` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'comma separated fb page ids',
  `page_ids_names` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'associative array',
  `do_not_send_to` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `campaign_name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `campaign_type` enum('page-wise','lead-wise','group-wise') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'page-wise',
  `campaign_message` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attached_url` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attached_video` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `schedule_time` datetime NOT NULL,
  `time_zone` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `posting_status` enum('0','1','2','3') COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_try_again` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1',
  `last_try_error_count` int(11) NOT NULL,
  `is_spam_caught` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `error_message` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_thread` int(11) NOT NULL,
  `successfully_sent` int(11) NOT NULL,
  `added_at` datetime NOT NULL,
  `completed_at` datetime NOT NULL,
  `report` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `unsubscribe_button` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `delay_time` int(11) NOT NULL DEFAULT '0' COMMENT '0 means random',
  PRIMARY KEY (`id`),
  KEY `status` (`posting_status`),
  KEY `dashboard` (`user_id`),
  KEY `dashboard2` (`user_id`,`completed_at`),
  KEY `dashboard3` (`user_id`,`posting_status`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE `fb_chat_plugin` (
  `id` int(11) NOT NULL,
  `user_id` varchar(200) DEFAULT NULL,
  `domain_name` varchar(250) DEFAULT NULL,
  `fb_page_url` text,
  `add_date` date NOT NULL,
  `deleted` enum('0','1') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `fb_chat_plugin`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `fb_chat_plugin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `fb_chat_plugin` ADD `js_code` TEXT NOT NULL AFTER `fb_page_url`;
ALTER TABLE `fb_chat_plugin` ADD `domain_code` VARCHAR(200) NULL AFTER `js_code`;
ALTER TABLE `fb_chat_plugin` ADD `message_header` VARCHAR( 255 ) NOT NULL AFTER `domain_name` ;


CREATE TABLE IF NOT EXISTS `fb_msg_manager_notification_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `facebook_rx_fb_user_info_id` int(11) NOT NULL,
  `email_address` varchar(255) DEFAULT NULL,
  `time_zone` varchar(255) NOT NULL,
  `time_interval` varchar(100) DEFAULT NULL,
  `is_enabled` enum('yes','no') NOT NULL,
  `has_business_account` enum('yes','no') NOT NULL DEFAULT 'no',
  `last_email_time` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `fb_msg_manager` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `facebook_rx_fb_user_info_id` int(11) NOT NULL,
  `page_table_id` int(12) NOT NULL,
  `from_user` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `from_user_id` varchar(255) DEFAULT NULL,
  `last_snippet` longtext CHARACTER SET utf8 NOT NULL,
  `message_count` varchar(255) DEFAULT NULL,
  `thread_id` varchar(255) NOT NULL,
  `inbox_link` text NOT NULL,
  `unread_count` varchar(255) DEFAULT NULL,
  `sync_time` datetime NOT NULL,
  `last_update_time` varchar(100) NOT NULL COMMENT 'this time in +00 UTC format, We need to convert it to the user time zone',
  PRIMARY KEY (`id`),
  UNIQUE KEY `thread_id` (`thread_id`,`user_id`,`facebook_rx_fb_user_info_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `facebook_rx_conversion_contact_group` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `group_name` varchar(255) NOT NULL,
  `user_id` int(11) NOT NULL,
  `deleted` enum('0','1') DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `payment_config` CHANGE `currency` `currency` ENUM('USD','AUD','BRL','CAD','CZK','DKK','EUR','HKD','HUF','ILS','JPY','MYR','MXN','TWD','NZD','NOK','PHP','PLN','GBP','RUB','SGD','SEK','CHF','VND') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;


CREATE TABLE IF NOT EXISTS `facebook_ex_conversation_campaign_send` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `campaign_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `page_id` int(11) NOT NULL,
  `client_thread_id` varchar(255) NOT NULL,
  `client_username` varchar(255) NOT NULL,
  `client_id` varchar(255) NOT NULL,
  `message_sent_id` varchar(255) NOT NULL,
  `sent_time` datetime NOT NULL,
  `lead_id` int(11) NOT NULL,
  `processed` enum('0','1') NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `campaign_id` (`campaign_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
ALTER TABLE `facebook_ex_conversation_campaign_send` ADD `page_name` VARCHAR(255) NOT NULL AFTER `page_id`;


DROP TABLE IF EXISTS `menu`;
CREATE TABLE IF NOT EXISTS `menu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `icon` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  `serial` int(11) NOT NULL,
  `module_access` varchar(255) NOT NULL,
  `have_child` enum('1','0') NOT NULL DEFAULT '0',
  `only_admin` enum('1','0') NOT NULL DEFAULT '1',
  `only_member` enum('1','0') NOT NULL DEFAULT '0',
  `add_ons_id` int(11) NOT NULL,
  `is_external` enum('0','1') NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8;


INSERT INTO `menu` (`id`, `name`, `icon`, `url`, `serial`, `module_access`, `have_child`, `only_admin`, `only_member`, `add_ons_id`, `is_external`) VALUES
(1, 'dashboard', 'fa fa-dashboard', 'facebook_ex_dashboard/index', 1, '', '0', '0', '0', 0, '0'),
(2, 'App Settings', 'fa fa-cog', 'facebook_rx_config/index', 2, '65', '0', '0', '1', 0, '0'),
(3, 'Payment', 'fa fa-paypal', 'payment/member_payment_history', 3, '', '0', '0', '1', 0, '0'),
(4, 'usage log', 'fa fa-list-ol', 'payment/usage_history', 4, '', '0', '0', '1', 0, '0'),
(5, 'Administration', 'fa fa-user-plus', '#', 5, '', '1', '1', '0', 0, '0'),
(6, 'import account', 'fa fa-cloud-download', 'facebook_rx_account_import/index', 6, '65', '0', '0', '0', 0, '0'),
(7, 'facebook lead', 'fa fa-group', '#', 7, '76', '1', '0', '0', 0, '0'),
(10, 'bulk message campaign', 'fa fa-envelope', '#', 8, '76', '1', '0', '0', 0, '0'),
(14, 'lead generator', 'fa fa-flash', '#', 9, '77,80,81,69,28', '1', '0', '0', 0, '0'),
(15, 'page inbox & notification', 'fa fa-commenting', '#', 10, '82,83', '1', '0', '0', 0, '0'),
(16, 'Cron job', 'fa fa-clock-o', 'native_api/index', 12, '', '0', '1', '0', 0, '0'),
(18, 'add-ons', 'fa fa-plug', 'addons/lists', 11, '', '0', '1', '0', 0, '0'),
(19, 'check update', 'fa fa-angle-double-up', 'update_system/index', 13, '', '0', '1', '0', 0, '0');

DROP TABLE IF EXISTS `menu_child_1`;
CREATE TABLE IF NOT EXISTS `menu_child_1` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  `serial` int(11) NOT NULL,
  `icon` varchar(255) NOT NULL,
  `module_access` varchar(255) NOT NULL,
  `parent_id` int(11) NOT NULL,
  `have_child` enum('1','0') NOT NULL DEFAULT '0',
  `only_admin` enum('1','0') NOT NULL DEFAULT '1',
  `only_member` enum('1','0') NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8;


INSERT INTO `menu_child_1` (`id`, `name`, `url`, `serial`, `icon`, `module_access`, `parent_id`, `have_child`, `only_admin`, `only_member`) VALUES
(1, 'User Management', 'admin/user_management', 1, 'fa fa-user', '', 5, '0', '1', '0'),
(2, 'Send Notification', 'admin/notify_members', 2, 'fa fa-bell-o', '', 5, '0', '1', '0'),
(3, 'Settings', '#', 3, 'fa fa-cog', '', 5, '1', '1', '0'),
(4, 'Payment', '#', 4, 'fa fa-paypal', '', 5, '1', '1', '0'),
(5, 'enable auto reply', 'facebook_ex_autoreply/index', 1, 'fa fa-reply-all', '80', 14, '0', '1', '0'),
(6, 'auto reply report', 'facebook_ex_autoreply/all_auto_reply_report', 2, 'fa fa-list-ol', '80', 14, '0', '1', '0'),
(7, '''send message'' button', 'facebook_ex_message_button/index', 3, 'fa fa-comment', '77', 14, '0', '1', '0'),
(8, 'messanger ad json script', 'facebook_ex_json_messanger/index', 4, 'fa fa-code', '81', 14, '0', '1', '0'),
(9, 'call to action poster', 'facebook_rx_cta_poster/cta_post_list/1', 5, 'fa fa-hand-o-up', '69', 14, '0', '1', '0'),
(10, 'facebook chat plugin', 'fb_chat_plugin_custom/index', 6, 'fa fa-wechat', '28', 14, '0', '1', '0'),
(11, 'settings', 'fb_msg_manager/index', 1, 'fa fa-cog', '', 15, '0', '0', '0'),
(12, 'message dashboard', 'fb_msg_manager/message_dashboard', 2, 'fa fa-dashboard', '82', 15, '0', '1', '0'),
(13, 'notification dashboard', 'fb_msg_manager_notification/index', 3, 'fa fa-dashboard', '83', 15, '0', '1', '0'),
(14, 'import lead', 'facebook_ex_import_lead/index', 1, 'fa fa-download', '76', 7, '0', '0', '0'),
(15, 'lead group', 'facebook_ex_import_lead/contact_group', 2, 'fa  fa-columns', '76', 7, '0', '0', '0'),
(16, 'lead list', 'facebook_ex_import_lead/contact_list', 3, 'fa fa-user', '76', 7, '0', '0', '0'),
(17, 'Multi-page Campaign', 'facebook_ex_campaign/create_multipage_campaign', 1, 'fa fa-clone', '76', 10, '0', '0', '0'),
(18, 'Multi-group Campaign', 'facebook_ex_campaign/create_multigroup_campaign', 2, 'fa fa-object-ungroup', '76', 10, '0', '0', '0'),
(19, 'Custom Campaign', 'facebook_ex_campaign/custom_campaign', 3, 'fa fa-random', '76', 10, '0', '0', '0'),
(20, 'Campaign Report', 'facebook_ex_campaign/campaign_report', 4, 'fa fa-th-list', '76', 10, '0', '0', '0');
ALTER TABLE `menu_child_1` ADD `is_external` ENUM('0','1') NOT NULL DEFAULT '0' AFTER `only_member`;

DROP TABLE IF EXISTS `menu_child_2`;
CREATE TABLE IF NOT EXISTS `menu_child_2` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  `serial` int(11) NOT NULL,
  `icon` varchar(255) NOT NULL,
  `module_access` varchar(255) NOT NULL,
  `parent_child` int(11) NOT NULL,
  `only_admin` enum('1','0') NOT NULL DEFAULT '1',
  `only_member` enum('1','0') NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;

INSERT INTO `menu_child_2` (`id`, `name`, `url`, `serial`, `icon`, `module_access`, `parent_child`, `only_admin`, `only_member`) VALUES
(1, 'Email Settings', 'admin_config_email/index', 2, 'fa fa-envelope', '', 3, '1', '0'),
(2, 'General Settings', 'admin_config/configuration', 1, 'fa fa-cog', '', 3, '1', '0'),
(3, 'Analytics Settings', 'admin_config/analytics_config', 3, 'fa fa-pie-chart', '', 3, '1', '0'),
(4, 'Purchase Code Settings', 'admin_config/purchase_code_configuration', 4, 'fa fa-code', '', 3, '1', '0'),
(5, 'advertisement settings', 'admin_config_ad/ad_config', 5, 'fa fa-bullhorn', '', 3, '1', '0'),
(6, 'social login settings', 'admin_config_login/login_config', 6, 'fa fa-sign-in', '', 3, '1', '0'),
(7, 'Facebook API Settings', 'facebook_rx_config/index', 7, 'fa fa-facebook-official', '', 3, '1', '0'),
(8, 'Dashboard', 'payment/payment_dashboard_admin', 1, 'fa fa-dashboard', '', 4, '1', '0'),
(9, 'Package Settings', 'payment/package_settings', 2, 'fa fa-cube', '', 4, '1', '0'),
(10, 'Payment Settings', 'payment/payment_setting_admin', 3, 'fa fa-cog', '', 4, '1', '0'),
(11, 'Payment History', 'payment/admin_payment_history', 4, 'fa fa-history', '', 4, '1', '0');
ALTER TABLE `menu_child_2` ADD `is_external` ENUM('0','1') NOT NULL DEFAULT '0' AFTER `only_member`;

CREATE TABLE IF NOT EXISTS `add_ons` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `add_on_name` varchar(255) NOT NULL,
  `unique_name` varchar(255) NOT NULL,
  `installed_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_name` (`unique_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
ALTER TABLE `add_ons` ADD `version` FLOAT NOT NULL AFTER `unique_name`;
ALTER TABLE `add_ons` ADD `purchase_code` VARCHAR(100) NOT NULL AFTER `installed_at`;
ALTER TABLE `add_ons` ADD `module_folder_name` VARCHAR(255) NOT NULL AFTER `purchase_code`;
ALTER TABLE `add_ons` ADD `project_id` INT NOT NULL AFTER `module_folder_name`;
ALTER TABLE `add_ons` ADD `update_at` DATETIME NOT NULL AFTER `installed_at`;
ALTER TABLE `add_ons` CHANGE `version` `version` VARCHAR(255) NOT NULL;

ALTER TABLE `modules` ADD `add_ons_id` INT NOT NULL AFTER `module_name`;
INSERT INTO `modules` (`id`, `module_name`, `add_ons_id`, `deleted`) VALUES (74, 'CTA Poster Auto Like/Share/Comment', '0', '0');


CREATE TABLE IF NOT EXISTS `version` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `version` varchar(255) NOT NULL,
  `current` enum('1','0') NOT NULL DEFAULT '1',
  `date` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `version` (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


ALTER TABLE `facebook_rx_fb_user_info` ADD `need_to_delete` ENUM('0','1') NOT NULL AFTER `deleted`;


ALTER TABLE `modules` ADD `extra_text` VARCHAR(50) NOT NULL DEFAULT 'month' AFTER `add_ons_id`, ADD `limit_enabled` ENUM('0','1') NOT NULL DEFAULT '1' AFTER `extra_text`;
ALTER TABLE `usage_log` ADD INDEX( `module_id`, `user_id`);
UPDATE `modules` SET `extra_text` = '' WHERE `modules`.`id` = 199;
UPDATE `modules` SET `extra_text` = '' WHERE `modules`.`id` = 200;
UPDATE `modules` SET `extra_text` = '' WHERE `modules`.`id` = 203;
UPDATE `modules` SET `extra_text` = '' WHERE `modules`.`id` = 28;
UPDATE `modules` SET `extra_text` = '' WHERE `modules`.`id` = 65;
UPDATE `modules` SET `extra_text` = '' WHERE `modules`.`id` = 74;
UPDATE `modules` SET `extra_text` = '' WHERE `modules`.`id` = 77;
UPDATE `modules` SET `extra_text` = '' WHERE `modules`.`id` = 78;
UPDATE `modules` SET `extra_text` = '' WHERE `modules`.`id` = 82;
UPDATE `modules` SET `extra_text` = '' WHERE `modules`.`id` = 83;
UPDATE `modules` SET `extra_text` = '' WHERE `modules`.`id` = 84;
UPDATE `modules` SET `limit_enabled` = '0' WHERE `modules`.`id` = 74;
UPDATE `modules` SET `limit_enabled` = '0' WHERE `modules`.`id` = 77;
UPDATE `modules` SET `limit_enabled` = '0' WHERE `modules`.`id` = 78;
UPDATE `modules` SET `limit_enabled` = '0' WHERE `modules`.`id` = 82;
UPDATE `modules` SET `limit_enabled` = '0' WHERE `modules`.`id` = 83;
UPDATE `modules` SET `limit_enabled` = '0' WHERE `modules`.`id` = 84;
UPDATE `modules` SET `module_name` = 'Message Send Button' WHERE `modules`.`id` = 77;
update menu_child_1 set only_admin='0' where module_access in ('28','69','77','80','81','82','83');

CREATE TABLE IF NOT EXISTS `update_list` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `files` text NOT NULL,
  `sql_query` text NOT NULL,
  `update_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


CREATE TABLE IF NOT EXISTS `user_login_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(12) NOT NULL,
  `user_name` varchar(100) NOT NULL,
  `user_email` varchar(150) NOT NULL,
  `login_time` datetime NOT NULL,
  `login_ip` varchar(25) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `announcement` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `created_at` datetime NOT NULL,
  `user_id` int(11) NOT NULL,
  `status` enum('published','draft') NOT NULL DEFAULT 'draft',
  PRIMARY KEY (`id`),
  KEY `created_at` (`created_at`),
  KEY `title` (`title`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `announcement_seen` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `announcement_id` int(11) NOT NULL,
  `seen_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `announcement_id` (`announcement_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `menu_child_1` (`id`, `name`, `url`, `serial`, `icon`, `module_access`, `parent_id`, `have_child`, `only_admin`, `only_member`, `is_external`) VALUES (NULL, 'Announcement', 'announcement/full_list', '5', 'fa fa-bullhorn', '', '5', '0', '1', '0', '0');

ALTER TABLE `email_config` ADD `smtp_type` ENUM('Default','tls','ssl') NOT NULL DEFAULT 'Default' AFTER `smtp_user`;


INSERT INTO `menu` (`id`, `name`, `icon`, `url`, `serial`, `module_access`, `have_child`, `only_admin`, `only_member`, `add_ons_id`, `is_external`) VALUES (NULL, 'Facebook Poster', 'fa fa-share-square', '', '10', '', '1', '0', '0', '', '0');



INSERT INTO `menu_child_1` (`id`, `name`, `url`, `serial`, `icon`, `module_access`, `parent_id`, `have_child`, `only_admin`, `only_member`, `is_external`) 
VALUES 
(NULL, 'Text/Image/Link/Video Post', 'ultrapost/text_image_link_video', '1', 'fa fa-list', '223', '29', '0', '0', '0', '0'), 
(NULL, 'CTA Post', 'ultrapost/cta_post', '3', 'fa fa-arrow-right', '220', '29', '0', '0', '0', '0'), 
(NULL, 'Carousel/Slider Post', 'ultrapost/carousel_slider_post', '4', 'fa fa-video-camera', '222', '29', '0', '0', '0', '0'), 
(NULL, 'Auto-reply Template Manager', 'ultrapost/template_manager', '5', 'fa fa-th-large', '', '29', '0', '0', '0', '0'), 
(NULL, 'Cron Job', 'ultrapost/cron_job_list', '6', 'fa fa-clock-o', '', '29', '0', '1', '0', '0');

UPDATE menu_child_1 SET parent_id= (select id from menu WHERE name='Facebook Poster') WHERE url='ultrapost/text_image_link_video' OR url='ultrapost/cta_post' OR url='ultrapost/carousel_slider_post' OR url='ultrapost/template_manager' OR url='ultrapost/cron_job_list';

DROP TABLE IF EXISTS `facebook_rx_auto_post`;
CREATE TABLE IF NOT EXISTS `facebook_rx_auto_post` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `facebook_rx_fb_user_info_id` int(11) NOT NULL,
  `post_type` enum('text_submit','link_submit','image_submit','video_submit') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'text_submit',
  `campaign_name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `page_group_user_id` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `page_or_group_or_user` enum('page','group','user') COLLATE utf8mb4_unicode_ci NOT NULL,
  `page_or_group_or_user_name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `link` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `link_preview_image` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `link_caption` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `link_description` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `image_url` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `video_title` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `video_url` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `video_thumb_url` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `auto_share_post` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `auto_share_this_post_by_pages` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `auto_share_to_profile` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `auto_like_post` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `auto_private_reply` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `auto_private_reply_text` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `auto_private_reply_status` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0' COMMENT 'taken by cronjob or not',
  `auto_private_reply_count` int(11) NOT NULL,
  `auto_private_reply_done_ids` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `auto_comment` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `auto_comment_text` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `posting_status` enum('0','1','2') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0' COMMENT 'pending,processing,completed',
  `post_id` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'fb post id',
  `post_url` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_updated_at` datetime NOT NULL,
  `schedule_time` datetime NOT NULL,
  `time_zone` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `post_auto_comment_cron_jon_status` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0' COMMENT 'post''s auto comment is done by cron job',
  `post_auto_like_cron_jon_status` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0' COMMENT 'post''s auto like is done by cron job',
  `post_auto_share_cron_jon_status` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0' COMMENT 'post''s auto share is done by cron job',
  `error_mesage` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_child` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `parent_campaign_id` int(11) NOT NULL,
  `page_ids` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `group_ids` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `ultrapost_auto_reply_table_id` int(11) NOT NULL,
  `is_autopost` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `repeat_times` int(11) NOT NULL,
  `time_interval` int(11) NOT NULL,
  `full_complete` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL,
  `schedule_type` enum('now','later') COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`,`facebook_rx_fb_user_info_id`),
  KEY `posting_status` (`posting_status`),
  KEY `dashboard` (`user_id`,`last_updated_at`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



DROP TABLE IF EXISTS `facebook_rx_cta_post`;
CREATE TABLE IF NOT EXISTS `facebook_rx_cta_post` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `facebook_rx_fb_user_info_id` int(11) NOT NULL,
  `campaign_name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `page_group_user_id` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'auto_like_post_comment',
  `page_or_group_or_user` enum('page') COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'cta post is only available for page',
  `cta_type` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cta_value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `link` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `link_preview_image` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `link_caption` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `link_description` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `auto_share_post` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `auto_share_this_post_by_pages` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `auto_share_to_profile` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `auto_like_post` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `auto_private_reply` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `auto_private_reply_text` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `auto_private_reply_status` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0' COMMENT 'taken by cronjob or not',
  `auto_private_reply_count` int(11) NOT NULL,
  `auto_private_reply_done_ids` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `auto_comment` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `auto_comment_text` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `posting_status` enum('0','1','2') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0' COMMENT 'pending,processing,completed',
  `post_id` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'fb post id',
  `post_url` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_updated_at` datetime NOT NULL,
  `schedule_time` datetime NOT NULL,
  `time_zone` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `post_auto_comment_cron_jon_status` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0' COMMENT 'post''s auto comment is done by cron job',
  `post_auto_like_cron_jon_status` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0' COMMENT 'post''s auto like is done by cron job',
  `post_auto_share_cron_jon_status` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0' COMMENT 'post''s auto share is done by cron job',
  `error_mesage` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_child` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `parent_campaign_id` int(11) NOT NULL,
  `page_ids` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `ultrapost_auto_reply_table_id` int(11) NOT NULL,
  `repeat_times` int(11) NOT NULL,
  `time_interval` int(11) NOT NULL,
  `schedule_type` enum('now','later') COLLATE utf8mb4_unicode_ci NOT NULL,
  `full_complete` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`,`facebook_rx_fb_user_info_id`),
  KEY `posting_status` (`posting_status`),
  KEY `dashboard` (`user_id`,`last_updated_at`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



DROP TABLE IF EXISTS `facebook_rx_offer_campaign`;
CREATE TABLE IF NOT EXISTS `facebook_rx_offer_campaign` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `facebook_rx_fb_user_info_id` int(11) NOT NULL,
  `fb_offer_id` varchar(200) NOT NULL,
  `campaign_view_id` int(11) NOT NULL COMMENT 'facebook_rx_offer_campaign_view.id , there is one to many relation between facebook_rx_offer_campaign and facebook_rx_offer_campaign_view, this field only contains id of facebook_rx_offer_campaign_view table''s very first relation. So joining facebook_rx_offer_campaign and facebook_rx_offer_campaign_view in left to right manner using facebook_rx_offer_campaign.campaign_view_id = facebook_rx_offer_campaign.id will not return exact data. But joining facebook_rx_offer_campaign_view.campaign_id = facebook_rx_offer_campaign.id is okay, no problem there.',
  `post_type` enum('image_submit','carousel_submit','video_submit') NOT NULL DEFAULT 'image_submit',
  `campaign_name` varchar(200) NOT NULL,
  `page_group_user_id` varchar(200) NOT NULL,
  `page_or_group_or_user` enum('page','group','user') NOT NULL,
  `page_or_group_or_user_name` varchar(200) NOT NULL,
  `message` text NOT NULL,
  `link` text NOT NULL,
  `offer_details` text NOT NULL,
  `type_offer` enum('percentage_off','free_stuff','cash_discount','bogo') NOT NULL DEFAULT 'percentage_off',
  `max_save_count` varchar(20) NOT NULL,
  `discount_title` varchar(255) NOT NULL,
  `discount_value` double NOT NULL DEFAULT '0',
  `currency` varchar(100) NOT NULL,
  `expiration_time` datetime NOT NULL,
  `expiry_time_zone` varchar(100) NOT NULL,
  `location_type` enum('online','offline','both') NOT NULL DEFAULT 'online',
  `online_coupon_code` varchar(200) NOT NULL,
  `store_coupon_code` varchar(200) NOT NULL,
  `barcode_type` enum('CODE128','CODE128B','EAN','PDF417','QR','UPC_A','UPC_E','DATAMATRIX','CODE93') NOT NULL DEFAULT 'CODE128',
  `barcode_value` text NOT NULL,
  `terms_condition` longtext NOT NULL,
  `fb_photo_ids` text NOT NULL COMMENT 'comma separated',
  `upload_ids` varchar(200) NOT NULL COMMENT 'facebook_rx_offer_upload.id',
  `fb_video_id` varchar(255) NOT NULL,
  `ultrapost_auto_reply_table_id` int(11) NOT NULL,
  `posting_status` enum('0','1','2') NOT NULL DEFAULT '0' COMMENT 'pending,processing,completed',
  `last_updated_at` datetime NOT NULL,
  `schedule_time` datetime NOT NULL,
  `schedule_type` enum('now','later') NOT NULL DEFAULT 'now',
  `time_zone` varchar(100) NOT NULL,
  `error_message` tinytext NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`,`facebook_rx_fb_user_info_id`),
  KEY `posting_status` (`posting_status`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;



DROP TABLE IF EXISTS `facebook_rx_offer_campaign_view`;
CREATE TABLE IF NOT EXISTS `facebook_rx_offer_campaign_view` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `offer_name` varchar(255) NOT NULL,
  `facebook_rx_fb_user_info_id` int(11) NOT NULL,
  `campaign_id` int(11) NOT NULL COMMENT 'facebook_rx_offer_campaign.id, many to one relation',
  `message` text NOT NULL,
  `fb_offer_id` varchar(200) NOT NULL,
  `post_id` varchar(255) NOT NULL COMMENT 'pageID_postID',
  `native_offer_view_id` varchar(255) NOT NULL,
  `post_url` text NOT NULL,
  `post_type` enum('image_submit','carousel_submit','video_submit') NOT NULL DEFAULT 'image_submit',
  `page_group_user_id` varchar(200) NOT NULL,
  `page_or_group_or_user` enum('page','group','user') NOT NULL,
  `page_or_group_or_user_name` varchar(200) NOT NULL,
  `auto_share_post` enum('0','1') NOT NULL DEFAULT '0',
  `auto_share_this_post_by_pages` text NOT NULL,
  `auto_share_this_post_by_pages_ids` text NOT NULL,
  `auto_share_this_post_by_groups` text NOT NULL,
  `auto_share_this_post_by_groups_ids` text NOT NULL,
  `auto_share_to_profile` enum('0','1') NOT NULL DEFAULT '0',
  `auto_share_to_profile_id` tinytext NOT NULL,
  `auto_like_post` enum('0','1') NOT NULL DEFAULT '0',
  `auto_comment` enum('0','1') DEFAULT '0',
  `auto_comment_text` tinytext NOT NULL,
  `posting_status` enum('0','1','2') NOT NULL DEFAULT '0' COMMENT 'pending,processing,completed',
  `last_updated_at` datetime NOT NULL,
  `schedule_time` datetime NOT NULL,
  `schedule_type` enum('now','later') NOT NULL DEFAULT 'now',
  `time_zone` varchar(100) NOT NULL,
  `post_auto_like_cron_jon_status` enum('0','1','2') NOT NULL DEFAULT '0' COMMENT 'post''s auto like is done by cron job',
  `post_auto_share_cron_jon_status` enum('0','1','2') NOT NULL DEFAULT '0' COMMENT 'post''s auto share is done by cron job',
  `post_auto_share_group_cron_jon_status` enum('0','1','2') NOT NULL DEFAULT '0',
  `post_auto_comment_cron_jon_status` enum('0','1','2') NOT NULL DEFAULT '0',
  `error_message` tinytext NOT NULL,
  `is_repost` enum('0','1') DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`,`facebook_rx_fb_user_info_id`),
  KEY `posting_status` (`posting_status`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;



DROP TABLE IF EXISTS `facebook_rx_offer_currency`;
CREATE TABLE IF NOT EXISTS `facebook_rx_offer_currency` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `currency` varchar(50) NOT NULL,
  `currency_sign` varchar(20) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `deleted` enum('0','1') NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=163 DEFAULT CHARSET=utf8;

INSERT INTO `facebook_rx_offer_currency` (`id`, `currency`, `currency_sign`, `full_name`, `deleted`) VALUES
(1, 'AED', '', 'United Arab Emirates Dirham', '0'),
(2, 'AFN', '', 'Afghanistan Afghani', '0'),
(3, 'ALL', '', 'Albania Lek', '0'),
(4, 'AMD', '', 'Armenia Dram', '0'),
(5, 'ANG', '', 'Netherlands Antilles Guilder', '0'),
(6, 'AOA', '', 'Angola Kwanza', '0'),
(7, 'ARS', '', 'Argentina Peso', '0'),
(8, 'AUD', '', 'Australia Dollar', '0'),
(9, 'AWG', '', 'Aruba Guilder', '0'),
(10, 'AZN', '', 'Azerbaijan New Manat', '0'),
(11, 'BAM', '', 'Bosnia and Herzegovina Convertible Marka', '0'),
(12, 'BBD', '', 'Barbados Dollar', '0'),
(13, 'BDT', '', 'Bangladesh Taka', '0'),
(14, 'BGN', '', 'Bulgaria Lev', '0'),
(15, 'BHD', '', 'Bahrain Dinar', '0'),
(16, 'BIF', '', 'Burundi Franc', '0'),
(17, 'BMD', '', 'Bermuda Dollar', '0'),
(18, 'BND', '', 'Brunei Darussalam Dollar', '0'),
(19, 'BOB', '', 'Bolivia Bol?viano', '0'),
(20, 'BRL', '', 'Brazil Real', '0'),
(21, 'BSD', '', 'Bahamas Dollar', '0'),
(22, 'BTN', '', 'Bhutan Ngultrum', '0'),
(23, 'BWP', '', 'Botswana Pula', '0'),
(24, 'BYN', '', 'Belarus Ruble', '0'),
(25, 'BZD', '', 'Belize Dollar', '0'),
(26, 'CAD', '', 'Canada Dollar', '0'),
(27, 'CDF', '', 'Congo/Kinshasa Franc', '0'),
(28, 'CHF', '', 'Switzerland Franc', '0'),
(29, 'CLP', '', 'Chile Peso', '0'),
(30, 'CNY', '', 'China Yuan Renminbi', '0'),
(31, 'COP', '', 'Colombia Peso', '0'),
(32, 'CRC', '', 'Costa Rica Colon', '0'),
(33, 'CUC', '', 'Cuba Convertible Peso', '0'),
(34, 'CUP', '', 'Cuba Peso', '0'),
(35, 'CVE', '', 'Cape Verde Escudo', '0'),
(36, 'CZK', '', 'Czech Republic Koruna', '0'),
(37, 'DJF', '', 'Djibouti Franc', '0'),
(38, 'DKK', '', 'Denmark Krone', '0'),
(39, 'DOP', '', 'Dominican Republic Peso', '0'),
(40, 'DZD', '', 'Algeria Dinar', '0'),
(41, 'EGP', '', 'Egypt Pound', '0'),
(42, 'ERN', '', 'Eritrea Nakfa', '0'),
(43, 'ETB', '', 'Ethiopia Birr', '0'),
(44, 'EUR', '', 'Euro Member Countries', '0'),
(45, 'FJD', '', 'Fiji Dollar', '0'),
(46, 'FKP', '', 'Falkland Islands (Malvinas) Pound', '0'),
(47, 'GBP', '', 'United Kingdom Pound', '0'),
(48, 'GEL', '', 'Georgia Lari', '0'),
(49, 'GGP', '', 'Guernsey Pound', '0'),
(50, 'GHS', '', 'Ghana Cedi', '0'),
(51, 'GIP', '', 'Gibraltar Pound', '0'),
(52, 'GMD', '', 'Gambia Dalasi', '0'),
(53, 'GNF', '', 'Guinea Franc', '0'),
(54, 'GTQ', '', 'Guatemala Quetzal', '0'),
(55, 'GYD', '', 'Guyana Dollar', '0'),
(56, 'HKD', '', 'Hong Kong Dollar', '0'),
(57, 'HNL', '', 'Honduras Lempira', '0'),
(58, 'HRK', '', 'Croatia Kuna', '0'),
(59, 'HTG', '', 'Haiti Gourde', '0'),
(60, 'HUF', '', 'Hungary Forint', '0'),
(61, 'IDR', '', 'Indonesia Rupiah', '0'),
(62, 'ILS', '', 'Israel Shekel', '0'),
(63, 'IMP', '', 'Isle of Man Pound', '0'),
(64, 'INR', '', 'India Rupee', '0'),
(65, 'IQD', '', 'Iraq Dinar', '0'),
(66, 'IRR', '', 'Iran Rial', '0'),
(67, 'ISK', '', 'Iceland Krona', '0'),
(68, 'JEP', '', 'Jersey Pound', '0'),
(69, 'JMD', '', 'Jamaica Dollar', '0'),
(70, 'JOD', '', 'Jordan Dinar', '0'),
(71, 'JPY', '', 'Japan Yen', '0'),
(72, 'KES', '', 'Kenya Shilling', '0'),
(73, 'KGS', '', 'Kyrgyzstan Som', '0'),
(74, 'KHR', '', 'Cambodia Riel', '0'),
(75, 'KMF', '', 'Comoros Franc', '0'),
(76, 'KPW', '', 'Korea (North) Won', '0'),
(77, 'KRW', '', 'Korea (South) Won', '0'),
(78, 'KWD', '', 'Kuwait Dinar', '0'),
(79, 'KYD', '', 'Cayman Islands Dollar', '0'),
(80, 'KZT', '', 'Kazakhstan Tenge', '0'),
(81, 'LAK', '', 'Laos Kip', '0'),
(82, 'LBP', '', 'Lebanon Pound', '0'),
(83, 'LKR', '', 'Sri Lanka Rupee', '0'),
(84, 'LRD', '', 'Liberia Dollar', '0'),
(85, 'LSL', '', 'Lesotho Loti', '0'),
(86, 'LYD', '', 'Libya Dinar', '0'),
(87, 'MAD', '', 'Morocco Dirham', '0'),
(88, 'MDL', '', 'Moldova Leu', '0'),
(89, 'MGA', '', 'Madagascar Ariary', '0'),
(90, 'MKD', '', 'Macedonia Denar', '0'),
(91, 'MMK', '', 'Myanmar (Burma) Kyat', '0'),
(92, 'MNT', '', 'Mongolia Tughrik', '0'),
(93, 'MOP', '', 'Macau Pataca', '0'),
(94, 'MRO', '', 'Mauritania Ouguiya', '0'),
(95, 'MUR', '', 'Mauritius Rupee', '0'),
(96, 'MVR', '', 'Maldives (Maldive Islands) Rufiyaa', '0'),
(97, 'MWK', '', 'Malawi Kwacha', '0'),
(98, 'MXN', '', 'Mexico Peso', '0'),
(99, 'MYR', '', 'Malaysia Ringgit', '0'),
(100, 'MZN', '', 'Mozambique Metical', '0'),
(101, 'NAD', '', 'Namibia Dollar', '0'),
(102, 'NGN', '', 'Nigeria Naira', '0'),
(103, 'NIO', '', 'Nicaragua Cordoba', '0'),
(104, 'NOK', '', 'Norway Krone', '0'),
(105, 'NPR', '', 'Nepal Rupee', '0'),
(106, 'NZD', '', 'New Zealand Dollar', '0'),
(107, 'OMR', '', 'Oman Rial', '0'),
(108, 'PAB', '', 'Panama Balboa', '0'),
(109, 'PEN', '', 'Peru Sol', '0'),
(110, 'PGK', '', 'Papua New Guinea Kina', '0'),
(111, 'PHP', '', 'Philippines Peso', '0'),
(112, 'PKR', '', 'Pakistan Rupee', '0'),
(113, 'PLN', '', 'Poland Zloty', '0'),
(114, 'PYG', '', 'Paraguay Guarani', '0'),
(115, 'QAR', '', 'Qatar Riyal', '0'),
(116, 'RON', '', 'Romania New Leu', '0'),
(117, 'RSD', '', 'Serbia Dinar', '0'),
(118, 'RUB', '', 'Russia Ruble', '0'),
(119, 'RWF', '', 'Rwanda Franc', '0'),
(120, 'SAR', '', 'Saudi Arabia Riyal', '0'),
(121, 'SBD', '', 'Solomon Islands Dollar', '0'),
(122, 'SCR', '', 'Seychelles Rupee', '0'),
(123, 'SDG', '', 'Sudan Pound', '0'),
(124, 'SEK', '', 'Sweden Krona', '0'),
(125, 'SGD', '', 'Singapore Dollar', '0'),
(126, 'SHP', '', 'Saint Helena Pound', '0'),
(127, 'SLL', '', 'Sierra Leone Leone', '0'),
(128, 'SOS', '', 'Somalia Shilling', '0'),
(129, 'SPL*', '', 'Seborga Luigino', '0'),
(130, 'SRD', '', 'Suriname Dollar', '0'),
(131, 'STD', '', 'S?o Tom? and Pr?ncipe Dobra', '0'),
(132, 'SVC', '', 'El Salvador Colon', '0'),
(133, 'SYP', '', 'Syria Pound', '0'),
(134, 'SZL', '', 'Swaziland Lilangeni', '0'),
(135, 'THB', '', 'Thailand Baht', '0'),
(136, 'TJS', '', 'Tajikistan Somoni', '0'),
(137, 'TMT', '', 'Turkmenistan Manat', '0'),
(138, 'TND', '', 'Tunisia Dinar', '0'),
(139, 'TOP', '', 'Tonga Pa\'anga', '0'),
(140, 'TRY', '', 'Turkey Lira', '0'),
(141, 'TTD', '', 'Trinidad and Tobago Dollar', '0'),
(142, 'TVD', '', 'Tuvalu Dollar', '0'),
(143, 'TWD', '', 'Taiwan New Dollar', '0'),
(144, 'TZS', '', 'Tanzania Shilling', '0'),
(145, 'UAH', '', 'Ukraine Hryvnia', '0'),
(146, 'UGX', '', 'Uganda Shilling', '0'),
(147, 'USD', '', 'United States Dollar', '0'),
(148, 'UYU', '', 'Uruguay Peso', '0'),
(149, 'UZS', '', 'Uzbekistan Som', '0'),
(150, 'VEF', '', 'Venezuela Bolivar', '0'),
(151, 'VND', '', 'Viet Nam Dong', '0'),
(152, 'VUV', '', 'Vanuatu Vatu', '0'),
(153, 'WST', '', 'Samoa Tala', '0'),
(154, 'XAF', '', 'Communaut? Financi?re Africaine (BEAC) CFA Franc BEAC', '0'),
(155, 'XCD', '', 'East Caribbean Dollar', '0'),
(156, 'XDR', '', 'International Monetary Fund (IMF) Special Drawing Rights', '0'),
(157, 'XOF', '', 'Communaut? Financi?re Africaine (BCEAO) Franc', '0'),
(158, 'XPF', '', 'Comptoirs Fran?ais du Pacifique (CFP) Franc', '0'),
(159, 'YER', '', 'Yemen Rial', '0'),
(160, 'ZAR', '', 'South Africa Rand', '0'),
(161, 'ZMW', '', 'Zambia Kwacha', '0'),
(162, 'ZWD', '', 'Zimbabwe Dollar', '0');



DROP TABLE IF EXISTS `facebook_rx_offer_upload`;
CREATE TABLE IF NOT EXISTS `facebook_rx_offer_upload` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `upload_time` datetime NOT NULL,
  `fb_photo_video_id` varchar(100) NOT NULL,
  `upload_type` enum('image','video') NOT NULL DEFAULT 'image',
  `file_location` text NOT NULL,
  `thumbnail_location` text NOT NULL,
  `posting_status` enum('0','1','2') NOT NULL DEFAULT '0',
  `error_message` tinytext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;



DROP TABLE IF EXISTS `facebook_rx_slider_post`;
CREATE TABLE IF NOT EXISTS `facebook_rx_slider_post` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `facebook_rx_fb_user_info_id` int(11) NOT NULL,
  `post_type` enum('slider_post','carousel_post') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'slider_post',
  `message` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `carousel_content` longtext COLLATE utf8mb4_unicode_ci,
  `carousel_link` mediumtext COLLATE utf8mb4_unicode_ci,
  `slider_images` longtext COLLATE utf8mb4_unicode_ci,
  `slider_image_duration` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `slider_transition_duration` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `campaign_name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `page_group_user_id` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `page_or_group_or_user` enum('page','group','user') COLLATE utf8mb4_unicode_ci NOT NULL,
  `page_or_group_or_user_name` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `auto_share_post` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `auto_share_this_post_by_pages` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `auto_share_to_profile` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `auto_like_post` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `auto_private_reply` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `auto_private_reply_text` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `auto_private_reply_status` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0' COMMENT 'taken by cronjob or not',
  `auto_private_reply_count` int(11) NOT NULL,
  `auto_private_reply_done_ids` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `auto_comment` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL,
  `auto_comment_text` mediumtext COLLATE utf8mb4_unicode_ci,
  `posting_status` enum('0','1','2') COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'pending,processing,completed',
  `post_id` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `post_url` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_updated_at` datetime NOT NULL,
  `schedule_time` datetime NOT NULL,
  `time_zone` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `post_auto_comment_cron_jon_status` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `post_auto_like_cron_jon_status` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `post_auto_share_cron_jon_status` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `error_mesage` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_child` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `parent_campaign_id` int(11) NOT NULL,
  `page_ids` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `ultrapost_auto_reply_table_id` int(11) NOT NULL,
  `repeat_times` int(11) NOT NULL,
  `time_interval` int(11) NOT NULL,
  `schedule_type` enum('now','later') COLLATE utf8mb4_unicode_ci NOT NULL,
  `full_complete` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`,`facebook_rx_fb_user_info_id`),
  KEY `posting_status` (`posting_status`),
  KEY `dashboard` (`user_id`,`last_updated_at`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



DROP TABLE IF EXISTS `ultrapost_auto_reply`;
CREATE TABLE IF NOT EXISTS `ultrapost_auto_reply` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ultrapost_campaign_name` varchar(255) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `reply_type` varchar(200) NOT NULL,
  `auto_like_comment` enum('no','yes') NOT NULL,
  `multiple_reply` enum('no','yes') NOT NULL,
  `comment_reply_enabled` enum('no','yes') NOT NULL,
  `nofilter_word_found_text` longtext NOT NULL,
  `auto_reply_text` longtext NOT NULL,
  `hide_comment_after_comment_reply` enum('no','yes') NOT NULL,
  `is_delete_offensive` enum('hide','delete') NOT NULL,
  `offensive_words` longtext NOT NULL,
  `private_message_offensive_words` longtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


INSERT INTO `modules` (`id`, `module_name`, `add_ons_id`, `extra_text`, `limit_enabled`, `deleted`) VALUES (220, 'CTA Post', '0', 'month', '1', '0');
INSERT INTO `modules` (`id`, `module_name`, `add_ons_id`, `extra_text`, `limit_enabled`, `deleted`) VALUES (222, 'Carousel/Slider Post', '0', 'month', '1', '0');
INSERT INTO `modules` (`id`, `module_name`, `add_ons_id`, `extra_text`, `limit_enabled`, `deleted`) VALUES (223, 'Text/Image/Link/Video Post', '0', 'month', '1', '0');

INSERT INTO `add_ons` (`id`, `add_on_name`, `unique_name`, `version`, `installed_at`, `update_at`, `purchase_code`, `module_folder_name`, `project_id`) VALUES (NULL, 'Facebook Poster', 'ultrapost', '1.0', '', '', '', 'ultrapost', '19');

UPDATE `menu` SET `module_access` = '220,222,223' WHERE `menu`.`name` = 'Facebook Poster';



ALTER TABLE `facebook_rx_config` ADD `developer_access` ENUM('0','1') NOT NULL DEFAULT '0' AFTER `use_by`, ADD `facebook_id` VARCHAR(50) NOT NULL AFTER `developer_access`, ADD `secret_code` VARCHAR(50) NOT NULL AFTER `facebook_id`;

DELETE FROM `modules` WHERE `id`=28;
DELETE FROM `modules` WHERE `id`=69;
DELETE FROM `modules` WHERE `id`=74;
DELETE FROM `modules` WHERE `id`=83;
DELETE FROM `menu_child_1` WHERE `module_access`='28';
DELETE FROM `menu_child_1` WHERE `module_access`='69';
DELETE FROM `menu_child_1` WHERE `module_access`='83';
DELETE FROM `menu_child_1` WHERE `url`='ultrapost/cron_job_list';
UPDATE `menu` SET `serial`=9 WHERE `module_access`='220,222,223';
UPDATE `menu` SET `icon`='fa fa-robot' WHERE `module_access`='200';
UPDATE `menu` SET `icon`='fa fa-newspaper' WHERE `module_access`='204';
UPDATE `menu` SET `icon`='fa fa-comment-alt' WHERE `module_access`='82,83';
UPDATE `menu` SET `icon`='fa fa-mail-bulk' WHERE `module_access`='76' AND `serial`='8';
UPDATE `menu_child_1` SET `icon`='fa fa-sliders-h' WHERE `url`='messenger_bot/bot_list';
UPDATE `menu_child_1` SET `icon`='fa fa-check-circle' WHERE `url`='messenger_bot/domain_whitelist';


CREATE TABLE IF NOT EXISTS `auto_comment_reply_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `auto_comment_template_id` int(11) NOT NULL,
  `time_zone` varchar(255) NOT NULL,
  `schedule_time` datetime NOT NULL,
  `campaign_name` varchar(255) NOT NULL,
  `post_id` varchar(200) NOT NULL,
  `page_info_table_id` int(11) NOT NULL,
  `page_name` mediumtext NOT NULL,
  `post_created_at` varchar(255) NOT NULL,
  `last_reply_time` datetime NOT NULL,
  `last_updated_at` datetime NOT NULL,
  `auto_comment_count` int(11) NOT NULL,
  `periodic_time` varchar(255) NOT NULL,
  `schedule_type` varchar(255) NOT NULL,
  `auto_comment_type` varchar(255) NOT NULL,
  `campaign_start_time` datetime NOT NULL,
  `campaign_end_time` datetime NOT NULL,
  `comment_start_time` time NOT NULL,
  `comment_end_time` time NOT NULL,
  `auto_private_reply_status` enum('0','1','2') NOT NULL DEFAULT '0',
  `auto_reply_done_info` longtext NOT NULL,
  `periodic_serial_reply_count` int(11) NOT NULL,
  `error_message` mediumtext NOT NULL,
  `post_description` longtext NOT NULL,
  `post_thumb` text NOT NULL,
  `deleted` enum('0','1') NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `auto_comment_reply_tb` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `template_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `auto_reply_comment_text` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
UPDATE `modules` SET `extra_text` = '',`module_name`='Auto Private Reply Campaign' WHERE `modules`.`id` = 80;
INSERT INTO `modules` (`id`, `module_name`, `add_ons_id`, `extra_text`, `limit_enabled`, `deleted`) VALUES (251, 'Auto Comment Campaign', '0', '', '1', '0');
INSERT INTO `menu` (`id`, `name`, `icon`, `url`, `serial`, `module_access`, `have_child`, `only_admin`, `only_member`, `add_ons_id`, `is_external`) VALUES (NULL, 'Auto Comment', 'fa fa-comment-dots', '', '8', '251', '1', '0', '0', '', '0');
INSERT INTO `menu_child_1` (`id`, `name`, `url`, `serial`, `icon`, `module_access`, `parent_id`, `have_child`, `only_admin`, `only_member`, `is_external`) VALUES (NULL, 'Enable Auto Comment', 'facebook_ex_auto_comment/index', '5', 'fa fa-comment', '251', '', '0', '0', '0', '0');
INSERT INTO `menu_child_1` (`id`, `name`, `url`, `serial`, `icon`, `module_access`, `parent_id`, `have_child`, `only_admin`, `only_member`, `is_external`) VALUES (NULL, 'Auto Comment template', 'facebook_ex_auto_comment/comment_template_manager', '6', 'fa fa-th-large', '251', '', '0', '0', '0', '0');
INSERT INTO `menu_child_1` (`id`, `name`, `url`, `serial`, `icon`, `module_access`, `parent_id`, `have_child`, `only_admin`, `only_member`, `is_external`) VALUES (NULL, 'Auto Comment Report', 'facebook_ex_auto_comment/all_auto_reply_report', '7', 'fa fa-list-ol', '251', '', '0', '0', '0', '0');
UPDATE menu_child_1 SET parent_id=(select id from menu WHERE module_access='251') WHERE module_access='251';


ALTER TABLE ultrapost_auto_reply CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
INSERT INTO `menu_child_2` (`id`, `name`, `url`, `serial`, `icon`, `module_access`, `parent_child`, `only_admin`, `only_member`, `is_external`) VALUES (NULL, 'Frontend Settings', 'admin_config/frontend_configuration', '1', 'fa fa-newspaper-o', '', '3', '1', '0', '0');
DELETE FROM `menu_child_2` WHERE url='admin_config/purchase_code_configuration';

INSERT INTO `modules` (`id`, `module_name`, `add_ons_id`, `extra_text`, `limit_enabled`, `deleted`) VALUES(256, 'Auto Posting (RSS)', 0, '', '1', '0');
INSERT INTO `menu` (`name`, `icon`, `url`, `serial`, `module_access`, `have_child`, `only_admin`, `only_member`, `add_ons_id`, `is_external`) VALUES('Auto Posting', 'fa fa-rss', 'autoposting/settings', 9, '256', '0', '0', '0', 0, '0');
CREATE TABLE IF NOT EXISTS `autoposting` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `feed_name` varchar(255) NOT NULL,
  `feed_type` enum('rss','youtube','twitter') NOT NULL DEFAULT 'rss',
  `feed_url` tinytext NOT NULL,
  `youtube_channel_id` varchar(255) NOT NULL,
  `page_ids` tinytext NOT NULL COMMENT 'auto ids',
  `page_names` text NOT NULL COMMENT 'page names',
  `facebook_rx_fb_user_info_ids` text NOT NULL COMMENT 'page id => fb rx user id json',
  `posting_start_time` varchar(50) NOT NULL,
  `posting_end_time` varchar(50) NOT NULL,
  `posting_timezone` varchar(250) NOT NULL,
  `page_id` int(11) NOT NULL COMMENT 'broadcast',
  `fb_page_id` varchar(200) NOT NULL COMMENT 'broadcast',
  `page_name` varchar(255) NOT NULL COMMENT 'broadcast',
  `label_ids` text NOT NULL COMMENT 'broadcast',
  `excluded_label_ids` text NOT NULL COMMENT 'broadcast',
  `broadcast_start_time` varchar(50) NOT NULL,
  `broadcast_end_time` varchar(50) NOT NULL,
  `broadcast_timezone` varchar(250) NOT NULL,
  `broadcast_notification_type` varchar(100) NOT NULL DEFAULT 'REGULAR',
  `broadcast_display_unsubscribe` enum('0','1') NOT NULL DEFAULT '0',
  `last_pub_date` datetime NOT NULL,
  `last_pub_title` tinytext NOT NULL,
  `last_pub_url` tinytext NOT NULL,
  `status` enum('0','1','2') NOT NULL DEFAULT '1' COMMENT 'pending, processing, abandoned',
  `last_updated_at` datetime NOT NULL,
  `cron_status` enum('0','1') NOT NULL DEFAULT '0',
  `error_message` longtext NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `status` (`status`,`cron_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


INSERT INTO `menu` (`name`, `icon`, `url`, `serial`, `module_access`, `have_child`, `only_admin`, `only_member`, `add_ons_id`, `is_external`) VALUES ('Support Desk', 'fa fa-ticket', 'simplesupport/support_list', '14', '', '0', '0', '1', '0', '0');
INSERT INTO `menu` (`name`, `icon`, `url`, `serial`, `module_access`, `have_child`, `only_admin`, `only_member`, `add_ons_id`, `is_external`) VALUES ('Support Desk', 'fa fa-ticket', '', '14', '', '1', '1', '0', '0', '0');
INSERT INTO `menu_child_1` (`name`, `url`, `serial`, `icon`, `module_access`, `parent_id`, `have_child`, `only_admin`, `only_member`, `is_external`) VALUES ('Support Category', 'simplesupport/support_category', '1', 'fa fa-list', '204', '100', '0', '1', '0', '0');
INSERT INTO `menu_child_1` (`name`, `url`, `serial`, `icon`, `module_access`, `parent_id`, `have_child`, `only_admin`, `only_member`, `is_external`) VALUES ('All Ticket', 'simplesupport/all_ticket', '1', 'fa fa-ticket', '204', '100', '0', '1', '0', '0');
UPDATE menu_child_1 SET parent_id= (select id from menu WHERE serial='14' AND have_child='1') WHERE url='simplesupport/support_category' OR url='simplesupport/all_ticket';
UPDATE menu_child_1 SET parent_id=(select id from menu WHERE module_access='77,80,81,69,28') WHERE url='ultrapost/template_manager';

CREATE TABLE IF NOT EXISTS `fb_simple_support_desk` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `ticket_title` varchar(255) CHARACTER SET utf8 NOT NULL,
  `ticket_text` longtext CHARACTER SET utf8 NOT NULL,
  `ticket_status` enum('2','1') CHARACTER SET utf8 NOT NULL DEFAULT '1',
  `support_category` int(11) NOT NULL,
  `ticket_open_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `fb_support_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_name` varchar(255) CHARACTER SET utf8 NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `fb_support_desk_reply` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ticket_reply_text` longtext CHARACTER SET utf8 NOT NULL,
  `ticket_reply_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `reply_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
ALTER TABLE `fb_support_desk_reply` ADD INDEX(`user_id`);
ALTER TABLE `fb_simple_support_desk` ADD INDEX(`user_id`);
ALTER TABLE `fb_simple_support_desk` ADD INDEX(`support_category`);
INSERT INTO `fb_support_category` ( `category_name`) VALUES ( 'Billing');
INSERT INTO `fb_support_category` ( `category_name`) VALUES ( 'Technical');
INSERT INTO `fb_support_category` ( `category_name`) VALUES ( 'Query');
UPDATE `fb_support_category` SET `user_id`=(SELECT id FROM users WHERE user_type='Admin'  LIMIT 1);


INSERT INTO `menu_child_1` (`id`, `name`, `url`, `serial`, `icon`, `module_access`, `parent_id`, `have_child`, `only_admin`, `only_member`, `is_external`) VALUES (NULL, 'User Activity Log', 'admin/activity_log', '1', 'fa fa-history', '', '5', '0', '1', '0', '0');
INSERT INTO `menu` (`id`, `name`, `icon`, `url`, `serial`, `module_access`, `have_child`, `only_admin`, `only_member`, `add_ons_id`, `is_external`) VALUES (NULL, ' Multi-Language', 'fa fa-language', 'multi_language/index', '13', '', '0', '1', '0', '', '0');
INSERT INTO `menu` (`id`, `name`, `icon`, `url`, `serial`, `module_access`, `have_child`, `only_admin`, `only_member`, `add_ons_id`, `is_external`) VALUES (NULL, 'Activity Calendar', 'fa fa-calendar', 'calendar/index', '13', '', '0', '0', '0', '0', '0');
UPDATE `modules` SET `module_name` = 'Facebook Account Import' WHERE `modules`.`id` = 65;
UPDATE `modules` SET `module_name` = 'Bulk Message Campaign' WHERE `modules`.`id` = 76;
UPDATE `modules` SET `module_name` = 'Lead Generator : Message Send Button' WHERE `modules`.`id` = 77;
UPDATE `modules` SET `module_name` = 'Background Lead Scan' WHERE `modules`.`id` = 78;
UPDATE `modules` SET `module_name` = 'Bulk Message Send Limit' WHERE `modules`.`id` = 79;
UPDATE `modules` SET `module_name` = 'Lead Generator : Auto Reply Enabled Post' WHERE `modules`.`id` = 80;
UPDATE `modules` SET `module_name` = 'Lead Generator : Messenger Ad JSON Script',`extra_text` = ''  WHERE `modules`.`id` = 81;


ALTER TABLE `fb_simple_support_desk` CHANGE `ticket_text` `ticket_text` LONGTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `fb_support_desk_reply` CHANGE `ticket_reply_text` `ticket_reply_text` LONGTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `fb_simple_support_desk` CHANGE `ticket_title` `ticket_title` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;


CREATE TABLE `paypal_error_log` ( `id` INT(11) NOT NULL AUTO_INCREMENT , `call_time` DATETIME NULL , `ipn_value` TEXT NULL , `error_log` VARCHAR(255) NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci;

CREATE TABLE IF NOT EXISTS `email_template_management` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `template_type` varchar(255) NOT NULL,
  `subject` text NOT NULL,
  `message` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


INSERT INTO `email_template_management` (`id`, `template_type`, `subject`, `message`) VALUES
(1, 'signup_activation', '', ''),
(2, 'reset_password', '', ''),
(3, 'change_password', '', ''),
(4, 'membership_expiration_10_days_before', '', ''),
(5, 'membership_expiration_1_day_before', '', ''),
(6, 'membership_expiration_1_day_after', '', ''),
(7, 'send_messenger_notification', '', ''),
(8, 'paypal_payment', '', ''),
(9, 'paypal_new_payment_made', '', ''),
(10, 'stripe_payment', '', ''),
(11, 'stripe_new_payment_made', '', '');


INSERT INTO `menu_child_2` (`id`, `name`, `url`, `serial`, `icon`, `module_access`, `parent_child`, `only_admin`, `only_member`, `is_external`) VALUES (NULL, 'Email Template Settings', 'admin_config/email_template_settings', '2', 'fa fa-id-card', '', '3', '1', '0', '0');

INSERT INTO `menu_child_1` (`id`, `name`, `url`, `serial`, `icon`, `module_access`, `parent_id`, `have_child`, `only_admin`, `only_member`, `is_external`) VALUES (NULL, 'Page Messaging', 'facebook_rx_account_import/pages_messaging', '3', 'fa fa-envelope', '', '0', '0', '1', '0', '0');

UPDATE menu_child_1 SET parent_id=(select id from menu WHERE module_access='82,83') WHERE url='facebook_rx_account_import/pages_messaging';

ALTER TABLE `facebook_rx_fb_page_info` ADD `webhook_enabled` ENUM('0','1') NOT NULL DEFAULT '0' AFTER `msg_manager`;

CREATE TABLE `page_messaging_information` ( `id` INT(11) NOT NULL AUTO_INCREMENT , `user_id` INT(11) NOT NULL , `page_id` INT(11) NOT NULL , `fb_page_id` VARCHAR(200) NOT NULL , `keywords` VARCHAR(100) NOT NULL , `reply_message` TEXT NOT NULL , `message` LONGTEXT NOT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci;