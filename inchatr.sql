/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50720
Source Host           : localhost:3306
Source Database       : inchatr

Target Server Type    : MYSQL
Target Server Version : 50720
File Encoding         : 65001

Date: 2019-06-15 18:30:10
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for add_ons
-- ----------------------------
DROP TABLE IF EXISTS `add_ons`;
CREATE TABLE `add_ons` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `add_on_name` varchar(255) NOT NULL,
  `unique_name` varchar(255) NOT NULL,
  `version` varchar(255) NOT NULL,
  `installed_at` datetime NOT NULL,
  `update_at` datetime NOT NULL,
  `purchase_code` varchar(100) NOT NULL,
  `module_folder_name` varchar(255) NOT NULL,
  `project_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_name` (`unique_name`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of add_ons
-- ----------------------------
INSERT INTO `add_ons` VALUES ('1', 'Facebook Poster', 'ultrapost', '1.0', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '', 'ultrapost', '19');
INSERT INTO `add_ons` VALUES ('2', 'Bot Inboxer', 'messenger_bot', '3.3.8', '2019-06-11 14:07:07', '0000-00-00 00:00:00', '8f3a1eaf-47a0-4a4c-b609-58678bc469b1', 'messenger_bot', '3');
INSERT INTO `add_ons` VALUES ('3', 'Drip Messaging', 'drip_messaging', '2.1.1', '2019-06-12 06:24:29', '0000-00-00 00:00:00', '5552fe0c-4d62-4254-a93e-ec43754395b0', 'drip_messaging', '18');

-- ----------------------------
-- Table structure for ad_config
-- ----------------------------
DROP TABLE IF EXISTS `ad_config`;
CREATE TABLE `ad_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `section1_html` longtext,
  `section1_html_mobile` longtext,
  `section2_html` longtext,
  `section3_html` longtext,
  `section4_html` longtext,
  `status` enum('0','1') NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of ad_config
-- ----------------------------

-- ----------------------------
-- Table structure for announcement
-- ----------------------------
DROP TABLE IF EXISTS `announcement`;
CREATE TABLE `announcement` (
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

-- ----------------------------
-- Records of announcement
-- ----------------------------

-- ----------------------------
-- Table structure for announcement_seen
-- ----------------------------
DROP TABLE IF EXISTS `announcement_seen`;
CREATE TABLE `announcement_seen` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `announcement_id` int(11) NOT NULL,
  `seen_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `announcement_id` (`announcement_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of announcement_seen
-- ----------------------------

-- ----------------------------
-- Table structure for autoposting
-- ----------------------------
DROP TABLE IF EXISTS `autoposting`;
CREATE TABLE `autoposting` (
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

-- ----------------------------
-- Records of autoposting
-- ----------------------------

-- ----------------------------
-- Table structure for auto_comment_reply_info
-- ----------------------------
DROP TABLE IF EXISTS `auto_comment_reply_info`;
CREATE TABLE `auto_comment_reply_info` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of auto_comment_reply_info
-- ----------------------------

-- ----------------------------
-- Table structure for auto_comment_reply_tb
-- ----------------------------
DROP TABLE IF EXISTS `auto_comment_reply_tb`;
CREATE TABLE `auto_comment_reply_tb` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `template_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `auto_reply_comment_text` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of auto_comment_reply_tb
-- ----------------------------

-- ----------------------------
-- Table structure for ci_sessions
-- ----------------------------
DROP TABLE IF EXISTS `ci_sessions`;
CREATE TABLE `ci_sessions` (
  `id` varchar(128) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `timestamp` int(10) unsigned NOT NULL DEFAULT '0',
  `data` blob NOT NULL,
  KEY `ci_sessions_timestamp` (`timestamp`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of ci_sessions
-- ----------------------------

-- ----------------------------
-- Table structure for email
-- ----------------------------
DROP TABLE IF EXISTS `email`;
CREATE TABLE `email` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `domain_id` int(11) NOT NULL,
  `url_id` int(11) NOT NULL,
  `search_engine_url_id` int(11) NOT NULL,
  `found_email` varchar(250) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of email
-- ----------------------------

-- ----------------------------
-- Table structure for email_config
-- ----------------------------
DROP TABLE IF EXISTS `email_config`;
CREATE TABLE `email_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `email_address` varchar(100) NOT NULL,
  `smtp_host` varchar(100) NOT NULL,
  `smtp_port` varchar(100) NOT NULL,
  `smtp_user` varchar(100) NOT NULL,
  `smtp_type` enum('Default','tls','ssl') NOT NULL DEFAULT 'Default',
  `smtp_password` varchar(100) NOT NULL,
  `status` enum('0','1') NOT NULL,
  `deleted` enum('0','1') NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of email_config
-- ----------------------------

-- ----------------------------
-- Table structure for email_template_management
-- ----------------------------
DROP TABLE IF EXISTS `email_template_management`;
CREATE TABLE `email_template_management` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `template_type` varchar(255) NOT NULL,
  `subject` text NOT NULL,
  `message` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of email_template_management
-- ----------------------------
INSERT INTO `email_template_management` VALUES ('1', 'signup_activation', '', '');
INSERT INTO `email_template_management` VALUES ('2', 'reset_password', '', '');
INSERT INTO `email_template_management` VALUES ('3', 'change_password', '', '');
INSERT INTO `email_template_management` VALUES ('4', 'membership_expiration_10_days_before', '', '');
INSERT INTO `email_template_management` VALUES ('5', 'membership_expiration_1_day_before', '', '');
INSERT INTO `email_template_management` VALUES ('6', 'membership_expiration_1_day_after', '', '');
INSERT INTO `email_template_management` VALUES ('7', 'send_messenger_notification', '', '');
INSERT INTO `email_template_management` VALUES ('8', 'paypal_payment', '', '');
INSERT INTO `email_template_management` VALUES ('9', 'paypal_new_payment_made', '', '');
INSERT INTO `email_template_management` VALUES ('10', 'stripe_payment', '', '');
INSERT INTO `email_template_management` VALUES ('11', 'stripe_new_payment_made', '', '');

-- ----------------------------
-- Table structure for facebook_config
-- ----------------------------
DROP TABLE IF EXISTS `facebook_config`;
CREATE TABLE `facebook_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `app_name` varchar(100) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `api_id` varchar(250) DEFAULT NULL,
  `api_secret` varchar(250) DEFAULT NULL,
  `user_access_token` varchar(500) DEFAULT NULL,
  `status` enum('0','1') NOT NULL DEFAULT '1',
  `deleted` enum('0','1') NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of facebook_config
-- ----------------------------

-- ----------------------------
-- Table structure for facebook_ex_autoreply
-- ----------------------------
DROP TABLE IF EXISTS `facebook_ex_autoreply`;
CREATE TABLE `facebook_ex_autoreply` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of facebook_ex_autoreply
-- ----------------------------

-- ----------------------------
-- Table structure for facebook_ex_conversation_campaign
-- ----------------------------
DROP TABLE IF EXISTS `facebook_ex_conversation_campaign`;
CREATE TABLE `facebook_ex_conversation_campaign` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of facebook_ex_conversation_campaign
-- ----------------------------

-- ----------------------------
-- Table structure for facebook_ex_conversation_campaign_send
-- ----------------------------
DROP TABLE IF EXISTS `facebook_ex_conversation_campaign_send`;
CREATE TABLE `facebook_ex_conversation_campaign_send` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `campaign_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `page_id` int(11) NOT NULL,
  `page_name` varchar(255) NOT NULL,
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

-- ----------------------------
-- Records of facebook_ex_conversation_campaign_send
-- ----------------------------

-- ----------------------------
-- Table structure for facebook_page_insight_page_list
-- ----------------------------
DROP TABLE IF EXISTS `facebook_page_insight_page_list`;
CREATE TABLE `facebook_page_insight_page_list` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `page_id` varchar(250) DEFAULT NULL,
  `page_name` text,
  `page_email` varchar(250) DEFAULT NULL,
  `page_cover` longtext,
  `page_profile` longtext,
  `page_access_token` longtext,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of facebook_page_insight_page_list
-- ----------------------------

-- ----------------------------
-- Table structure for facebook_rx_auto_post
-- ----------------------------
DROP TABLE IF EXISTS `facebook_rx_auto_post`;
CREATE TABLE `facebook_rx_auto_post` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of facebook_rx_auto_post
-- ----------------------------

-- ----------------------------
-- Table structure for facebook_rx_config
-- ----------------------------
DROP TABLE IF EXISTS `facebook_rx_config`;
CREATE TABLE `facebook_rx_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `app_name` varchar(100) DEFAULT NULL,
  `api_id` varchar(250) DEFAULT NULL,
  `api_secret` varchar(250) DEFAULT NULL,
  `numeric_id` varchar(250) NOT NULL,
  `user_access_token` varchar(500) DEFAULT NULL,
  `status` enum('0','1') NOT NULL DEFAULT '1',
  `deleted` enum('0','1') NOT NULL DEFAULT '0',
  `user_id` int(11) NOT NULL,
  `use_by` enum('only_me','everyone') NOT NULL DEFAULT 'only_me',
  `developer_access` enum('0','1') NOT NULL DEFAULT '0',
  `facebook_id` varchar(50) NOT NULL,
  `secret_code` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of facebook_rx_config
-- ----------------------------

-- ----------------------------
-- Table structure for facebook_rx_conversion_contact_group
-- ----------------------------
DROP TABLE IF EXISTS `facebook_rx_conversion_contact_group`;
CREATE TABLE `facebook_rx_conversion_contact_group` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `group_name` varchar(255) NOT NULL,
  `user_id` int(11) NOT NULL,
  `deleted` enum('0','1') DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of facebook_rx_conversion_contact_group
-- ----------------------------

-- ----------------------------
-- Table structure for facebook_rx_conversion_user_list
-- ----------------------------
DROP TABLE IF EXISTS `facebook_rx_conversion_user_list`;
CREATE TABLE `facebook_rx_conversion_user_list` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of facebook_rx_conversion_user_list
-- ----------------------------

-- ----------------------------
-- Table structure for facebook_rx_cta_post
-- ----------------------------
DROP TABLE IF EXISTS `facebook_rx_cta_post`;
CREATE TABLE `facebook_rx_cta_post` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of facebook_rx_cta_post
-- ----------------------------

-- ----------------------------
-- Table structure for facebook_rx_fb_group_info
-- ----------------------------
DROP TABLE IF EXISTS `facebook_rx_fb_group_info`;
CREATE TABLE `facebook_rx_fb_group_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `facebook_rx_fb_user_info_id` int(11) NOT NULL,
  `group_id` varchar(200) NOT NULL,
  `group_cover` text,
  `group_profile` text,
  `group_name` varchar(200) DEFAULT NULL,
  `group_access_token` text NOT NULL,
  `add_date` date NOT NULL,
  `deleted` enum('0','1') NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of facebook_rx_fb_group_info
-- ----------------------------

-- ----------------------------
-- Table structure for facebook_rx_fb_page_info
-- ----------------------------
DROP TABLE IF EXISTS `facebook_rx_fb_page_info`;
CREATE TABLE `facebook_rx_fb_page_info` (
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
  `webhook_enabled` enum('0','1') NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `page_id` (`page_id`),
  KEY `user_id` (`user_id`,`page_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of facebook_rx_fb_page_info
-- ----------------------------

-- ----------------------------
-- Table structure for facebook_rx_fb_user_info
-- ----------------------------
DROP TABLE IF EXISTS `facebook_rx_fb_user_info`;
CREATE TABLE `facebook_rx_fb_user_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `facebook_rx_config_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `access_token` text NOT NULL,
  `name` varchar(200) DEFAULT NULL,
  `email` varchar(200) DEFAULT NULL,
  `fb_id` varchar(200) NOT NULL,
  `add_date` date NOT NULL,
  `deleted` enum('0','1') NOT NULL,
  `need_to_delete` enum('0','1') NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of facebook_rx_fb_user_info
-- ----------------------------

-- ----------------------------
-- Table structure for facebook_rx_offer_campaign
-- ----------------------------
DROP TABLE IF EXISTS `facebook_rx_offer_campaign`;
CREATE TABLE `facebook_rx_offer_campaign` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of facebook_rx_offer_campaign
-- ----------------------------

-- ----------------------------
-- Table structure for facebook_rx_offer_campaign_view
-- ----------------------------
DROP TABLE IF EXISTS `facebook_rx_offer_campaign_view`;
CREATE TABLE `facebook_rx_offer_campaign_view` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of facebook_rx_offer_campaign_view
-- ----------------------------

-- ----------------------------
-- Table structure for facebook_rx_offer_currency
-- ----------------------------
DROP TABLE IF EXISTS `facebook_rx_offer_currency`;
CREATE TABLE `facebook_rx_offer_currency` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `currency` varchar(50) NOT NULL,
  `currency_sign` varchar(20) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `deleted` enum('0','1') NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=163 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of facebook_rx_offer_currency
-- ----------------------------
INSERT INTO `facebook_rx_offer_currency` VALUES ('1', 'AED', '', 'United Arab Emirates Dirham', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('2', 'AFN', '', 'Afghanistan Afghani', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('3', 'ALL', '', 'Albania Lek', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('4', 'AMD', '', 'Armenia Dram', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('5', 'ANG', '', 'Netherlands Antilles Guilder', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('6', 'AOA', '', 'Angola Kwanza', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('7', 'ARS', '', 'Argentina Peso', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('8', 'AUD', '', 'Australia Dollar', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('9', 'AWG', '', 'Aruba Guilder', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('10', 'AZN', '', 'Azerbaijan New Manat', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('11', 'BAM', '', 'Bosnia and Herzegovina Convertible Marka', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('12', 'BBD', '', 'Barbados Dollar', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('13', 'BDT', '', 'Bangladesh Taka', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('14', 'BGN', '', 'Bulgaria Lev', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('15', 'BHD', '', 'Bahrain Dinar', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('16', 'BIF', '', 'Burundi Franc', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('17', 'BMD', '', 'Bermuda Dollar', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('18', 'BND', '', 'Brunei Darussalam Dollar', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('19', 'BOB', '', 'Bolivia Bol?viano', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('20', 'BRL', '', 'Brazil Real', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('21', 'BSD', '', 'Bahamas Dollar', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('22', 'BTN', '', 'Bhutan Ngultrum', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('23', 'BWP', '', 'Botswana Pula', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('24', 'BYN', '', 'Belarus Ruble', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('25', 'BZD', '', 'Belize Dollar', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('26', 'CAD', '', 'Canada Dollar', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('27', 'CDF', '', 'Congo/Kinshasa Franc', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('28', 'CHF', '', 'Switzerland Franc', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('29', 'CLP', '', 'Chile Peso', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('30', 'CNY', '', 'China Yuan Renminbi', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('31', 'COP', '', 'Colombia Peso', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('32', 'CRC', '', 'Costa Rica Colon', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('33', 'CUC', '', 'Cuba Convertible Peso', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('34', 'CUP', '', 'Cuba Peso', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('35', 'CVE', '', 'Cape Verde Escudo', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('36', 'CZK', '', 'Czech Republic Koruna', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('37', 'DJF', '', 'Djibouti Franc', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('38', 'DKK', '', 'Denmark Krone', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('39', 'DOP', '', 'Dominican Republic Peso', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('40', 'DZD', '', 'Algeria Dinar', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('41', 'EGP', '', 'Egypt Pound', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('42', 'ERN', '', 'Eritrea Nakfa', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('43', 'ETB', '', 'Ethiopia Birr', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('44', 'EUR', '', 'Euro Member Countries', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('45', 'FJD', '', 'Fiji Dollar', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('46', 'FKP', '', 'Falkland Islands (Malvinas) Pound', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('47', 'GBP', '', 'United Kingdom Pound', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('48', 'GEL', '', 'Georgia Lari', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('49', 'GGP', '', 'Guernsey Pound', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('50', 'GHS', '', 'Ghana Cedi', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('51', 'GIP', '', 'Gibraltar Pound', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('52', 'GMD', '', 'Gambia Dalasi', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('53', 'GNF', '', 'Guinea Franc', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('54', 'GTQ', '', 'Guatemala Quetzal', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('55', 'GYD', '', 'Guyana Dollar', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('56', 'HKD', '', 'Hong Kong Dollar', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('57', 'HNL', '', 'Honduras Lempira', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('58', 'HRK', '', 'Croatia Kuna', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('59', 'HTG', '', 'Haiti Gourde', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('60', 'HUF', '', 'Hungary Forint', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('61', 'IDR', '', 'Indonesia Rupiah', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('62', 'ILS', '', 'Israel Shekel', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('63', 'IMP', '', 'Isle of Man Pound', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('64', 'INR', '', 'India Rupee', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('65', 'IQD', '', 'Iraq Dinar', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('66', 'IRR', '', 'Iran Rial', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('67', 'ISK', '', 'Iceland Krona', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('68', 'JEP', '', 'Jersey Pound', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('69', 'JMD', '', 'Jamaica Dollar', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('70', 'JOD', '', 'Jordan Dinar', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('71', 'JPY', '', 'Japan Yen', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('72', 'KES', '', 'Kenya Shilling', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('73', 'KGS', '', 'Kyrgyzstan Som', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('74', 'KHR', '', 'Cambodia Riel', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('75', 'KMF', '', 'Comoros Franc', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('76', 'KPW', '', 'Korea (North) Won', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('77', 'KRW', '', 'Korea (South) Won', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('78', 'KWD', '', 'Kuwait Dinar', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('79', 'KYD', '', 'Cayman Islands Dollar', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('80', 'KZT', '', 'Kazakhstan Tenge', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('81', 'LAK', '', 'Laos Kip', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('82', 'LBP', '', 'Lebanon Pound', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('83', 'LKR', '', 'Sri Lanka Rupee', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('84', 'LRD', '', 'Liberia Dollar', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('85', 'LSL', '', 'Lesotho Loti', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('86', 'LYD', '', 'Libya Dinar', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('87', 'MAD', '', 'Morocco Dirham', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('88', 'MDL', '', 'Moldova Leu', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('89', 'MGA', '', 'Madagascar Ariary', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('90', 'MKD', '', 'Macedonia Denar', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('91', 'MMK', '', 'Myanmar (Burma) Kyat', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('92', 'MNT', '', 'Mongolia Tughrik', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('93', 'MOP', '', 'Macau Pataca', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('94', 'MRO', '', 'Mauritania Ouguiya', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('95', 'MUR', '', 'Mauritius Rupee', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('96', 'MVR', '', 'Maldives (Maldive Islands) Rufiyaa', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('97', 'MWK', '', 'Malawi Kwacha', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('98', 'MXN', '', 'Mexico Peso', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('99', 'MYR', '', 'Malaysia Ringgit', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('100', 'MZN', '', 'Mozambique Metical', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('101', 'NAD', '', 'Namibia Dollar', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('102', 'NGN', '', 'Nigeria Naira', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('103', 'NIO', '', 'Nicaragua Cordoba', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('104', 'NOK', '', 'Norway Krone', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('105', 'NPR', '', 'Nepal Rupee', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('106', 'NZD', '', 'New Zealand Dollar', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('107', 'OMR', '', 'Oman Rial', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('108', 'PAB', '', 'Panama Balboa', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('109', 'PEN', '', 'Peru Sol', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('110', 'PGK', '', 'Papua New Guinea Kina', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('111', 'PHP', '', 'Philippines Peso', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('112', 'PKR', '', 'Pakistan Rupee', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('113', 'PLN', '', 'Poland Zloty', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('114', 'PYG', '', 'Paraguay Guarani', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('115', 'QAR', '', 'Qatar Riyal', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('116', 'RON', '', 'Romania New Leu', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('117', 'RSD', '', 'Serbia Dinar', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('118', 'RUB', '', 'Russia Ruble', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('119', 'RWF', '', 'Rwanda Franc', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('120', 'SAR', '', 'Saudi Arabia Riyal', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('121', 'SBD', '', 'Solomon Islands Dollar', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('122', 'SCR', '', 'Seychelles Rupee', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('123', 'SDG', '', 'Sudan Pound', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('124', 'SEK', '', 'Sweden Krona', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('125', 'SGD', '', 'Singapore Dollar', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('126', 'SHP', '', 'Saint Helena Pound', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('127', 'SLL', '', 'Sierra Leone Leone', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('128', 'SOS', '', 'Somalia Shilling', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('129', 'SPL*', '', 'Seborga Luigino', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('130', 'SRD', '', 'Suriname Dollar', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('131', 'STD', '', 'S?o Tom? and Pr?ncipe Dobra', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('132', 'SVC', '', 'El Salvador Colon', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('133', 'SYP', '', 'Syria Pound', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('134', 'SZL', '', 'Swaziland Lilangeni', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('135', 'THB', '', 'Thailand Baht', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('136', 'TJS', '', 'Tajikistan Somoni', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('137', 'TMT', '', 'Turkmenistan Manat', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('138', 'TND', '', 'Tunisia Dinar', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('139', 'TOP', '', 'Tonga Pa\'anga', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('140', 'TRY', '', 'Turkey Lira', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('141', 'TTD', '', 'Trinidad and Tobago Dollar', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('142', 'TVD', '', 'Tuvalu Dollar', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('143', 'TWD', '', 'Taiwan New Dollar', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('144', 'TZS', '', 'Tanzania Shilling', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('145', 'UAH', '', 'Ukraine Hryvnia', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('146', 'UGX', '', 'Uganda Shilling', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('147', 'USD', '', 'United States Dollar', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('148', 'UYU', '', 'Uruguay Peso', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('149', 'UZS', '', 'Uzbekistan Som', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('150', 'VEF', '', 'Venezuela Bolivar', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('151', 'VND', '', 'Viet Nam Dong', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('152', 'VUV', '', 'Vanuatu Vatu', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('153', 'WST', '', 'Samoa Tala', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('154', 'XAF', '', 'Communaut? Financi?re Africaine (BEAC) CFA Franc BEAC', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('155', 'XCD', '', 'East Caribbean Dollar', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('156', 'XDR', '', 'International Monetary Fund (IMF) Special Drawing Rights', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('157', 'XOF', '', 'Communaut? Financi?re Africaine (BCEAO) Franc', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('158', 'XPF', '', 'Comptoirs Fran?ais du Pacifique (CFP) Franc', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('159', 'YER', '', 'Yemen Rial', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('160', 'ZAR', '', 'South Africa Rand', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('161', 'ZMW', '', 'Zambia Kwacha', '0');
INSERT INTO `facebook_rx_offer_currency` VALUES ('162', 'ZWD', '', 'Zimbabwe Dollar', '0');

-- ----------------------------
-- Table structure for facebook_rx_offer_upload
-- ----------------------------
DROP TABLE IF EXISTS `facebook_rx_offer_upload`;
CREATE TABLE `facebook_rx_offer_upload` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `upload_time` datetime NOT NULL,
  `fb_photo_video_id` varchar(100) NOT NULL,
  `upload_type` enum('image','video') NOT NULL DEFAULT 'image',
  `file_location` text NOT NULL,
  `thumbnail_location` text NOT NULL,
  `posting_status` enum('0','1','2') NOT NULL DEFAULT '0',
  `error_message` tinytext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of facebook_rx_offer_upload
-- ----------------------------

-- ----------------------------
-- Table structure for facebook_rx_slider_post
-- ----------------------------
DROP TABLE IF EXISTS `facebook_rx_slider_post`;
CREATE TABLE `facebook_rx_slider_post` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of facebook_rx_slider_post
-- ----------------------------

-- ----------------------------
-- Table structure for fb_chat_plugin
-- ----------------------------
DROP TABLE IF EXISTS `fb_chat_plugin`;
CREATE TABLE `fb_chat_plugin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` varchar(200) DEFAULT NULL,
  `domain_name` varchar(250) DEFAULT NULL,
  `message_header` varchar(255) NOT NULL,
  `fb_page_url` text,
  `js_code` text NOT NULL,
  `domain_code` varchar(200) DEFAULT NULL,
  `add_date` date NOT NULL,
  `deleted` enum('0','1') NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of fb_chat_plugin
-- ----------------------------

-- ----------------------------
-- Table structure for fb_msg_manager
-- ----------------------------
DROP TABLE IF EXISTS `fb_msg_manager`;
CREATE TABLE `fb_msg_manager` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `facebook_rx_fb_user_info_id` int(11) NOT NULL,
  `page_table_id` int(12) NOT NULL,
  `from_user` varchar(255) DEFAULT NULL,
  `from_user_id` varchar(255) DEFAULT NULL,
  `last_snippet` longtext NOT NULL,
  `message_count` varchar(255) DEFAULT NULL,
  `thread_id` varchar(255) NOT NULL,
  `inbox_link` text NOT NULL,
  `unread_count` varchar(255) DEFAULT NULL,
  `sync_time` datetime NOT NULL,
  `last_update_time` varchar(100) NOT NULL COMMENT 'this time in +00 UTC format, We need to convert it to the user time zone',
  PRIMARY KEY (`id`),
  UNIQUE KEY `thread_id` (`thread_id`,`user_id`,`facebook_rx_fb_user_info_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of fb_msg_manager
-- ----------------------------

-- ----------------------------
-- Table structure for fb_msg_manager_notification_settings
-- ----------------------------
DROP TABLE IF EXISTS `fb_msg_manager_notification_settings`;
CREATE TABLE `fb_msg_manager_notification_settings` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of fb_msg_manager_notification_settings
-- ----------------------------

-- ----------------------------
-- Table structure for fb_simple_support_desk
-- ----------------------------
DROP TABLE IF EXISTS `fb_simple_support_desk`;
CREATE TABLE `fb_simple_support_desk` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `ticket_title` text NOT NULL,
  `ticket_text` longtext NOT NULL,
  `ticket_status` enum('2','1') NOT NULL DEFAULT '1',
  `support_category` int(11) NOT NULL,
  `ticket_open_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `support_category` (`support_category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of fb_simple_support_desk
-- ----------------------------

-- ----------------------------
-- Table structure for fb_support_category
-- ----------------------------
DROP TABLE IF EXISTS `fb_support_category`;
CREATE TABLE `fb_support_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_name` varchar(255) NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of fb_support_category
-- ----------------------------
INSERT INTO `fb_support_category` VALUES ('1', 'Billing', '1');
INSERT INTO `fb_support_category` VALUES ('2', 'Technical', '1');
INSERT INTO `fb_support_category` VALUES ('3', 'Query', '1');

-- ----------------------------
-- Table structure for fb_support_desk_reply
-- ----------------------------
DROP TABLE IF EXISTS `fb_support_desk_reply`;
CREATE TABLE `fb_support_desk_reply` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ticket_reply_text` longtext NOT NULL,
  `ticket_reply_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `reply_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of fb_support_desk_reply
-- ----------------------------

-- ----------------------------
-- Table structure for forget_password
-- ----------------------------
DROP TABLE IF EXISTS `forget_password`;
CREATE TABLE `forget_password` (
  `id` int(12) NOT NULL AUTO_INCREMENT,
  `confirmation_code` varchar(15) NOT NULL,
  `email` varchar(100) NOT NULL,
  `success` int(11) NOT NULL DEFAULT '0',
  `expiration` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of forget_password
-- ----------------------------

-- ----------------------------
-- Table structure for login_config
-- ----------------------------
DROP TABLE IF EXISTS `login_config`;
CREATE TABLE `login_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `app_name` varchar(100) DEFAULT NULL,
  `api_id` varchar(250) DEFAULT NULL,
  `api_secret` varchar(250) DEFAULT NULL,
  `user_access_token` text NOT NULL,
  `google_client_id` text,
  `google_client_secret` varchar(250) DEFAULT NULL,
  `status` enum('0','1') NOT NULL DEFAULT '1',
  `deleted` enum('0','1') NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of login_config
-- ----------------------------

-- ----------------------------
-- Table structure for menu
-- ----------------------------
DROP TABLE IF EXISTS `menu`;
CREATE TABLE `menu` (
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
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of menu
-- ----------------------------
INSERT INTO `menu` VALUES ('1', 'dashboard', 'ti-dashboard', 'facebook_ex_dashboard/index', '1', '', '0', '0', '0', '0', '0');
INSERT INTO `menu` VALUES ('2', 'App Settings', 'fa fa-cog', 'facebook_rx_config/index', '2', '65', '0', '0', '1', '0', '0');
INSERT INTO `menu` VALUES ('3', 'Billing', 'fa fa-paypal', 'payment/member_payment_history', '3', '', '0', '0', '1', '0', '0');
INSERT INTO `menu` VALUES ('4', 'usage log', 'ti-view-list-alt', 'payment/usage_history', '4', '', '0', '0', '1', '0', '0');
INSERT INTO `menu` VALUES ('5', 'Administration', 'ti-user', '#', '5', '', '1', '1', '0', '0', '0');
INSERT INTO `menu` VALUES ('6', 'Add Account', 'ti-cloud-down', 'facebook_rx_account_import/index', '6', '65', '0', '0', '0', '0', '0');
INSERT INTO `menu` VALUES ('7', 'Audience', 'fa fa-group', '#', '7', '76', '1', '0', '0', '0', '0');
INSERT INTO `menu` VALUES ('10', 'Bulk Broadcast', 'ti-comments', '#', '8', '76', '1', '0', '0', '0', '0');
INSERT INTO `menu` VALUES ('14', 'Lead Growth', 'ti-bolt', '#', '9', '77,80,81,69,28', '1', '0', '0', '0', '0');
INSERT INTO `menu` VALUES ('15', 'Post Alerts', 'ti-comment', '#', '10', '82,83', '1', '0', '0', '0', '0');
INSERT INTO `menu` VALUES ('16', 'Cron job', 'ti-time', 'native_api/index', '12', '', '0', '1', '0', '0', '0');
INSERT INTO `menu` VALUES ('18', 'add-ons', 'ti-plug', 'addons/lists', '11', '', '0', '1', '0', '0', '0');
INSERT INTO `menu` VALUES ('19', 'check update', 'ti-angle-double-up', 'update_system/index', '13', '', '0', '1', '0', '0', '0');
INSERT INTO `menu` VALUES ('20', 'Facebook Poster', 'ti-share', '', '9', '220,222,223', '1', '0', '0', '0', '0');
INSERT INTO `menu` VALUES ('21', 'Comment Bot', 'ti-comment-alt', '', '8', '251', '1', '0', '0', '0', '0');
INSERT INTO `menu` VALUES ('22', 'Auto Post', 'ti-rss-alt', 'autoposting/settings', '9', '256', '0', '0', '0', '0', '0');
INSERT INTO `menu` VALUES ('23', 'Support Desk', 'ti-ticket', 'simplesupport/support_list', '14', '', '0', '0', '1', '0', '0');
INSERT INTO `menu` VALUES ('24', 'Support Desk', 'ti-ticket', '', '14', '', '1', '1', '0', '0', '0');
INSERT INTO `menu` VALUES ('25', ' Multi-Language', 'ti-world', 'multi_language/index', '13', '', '0', '1', '0', '0', '0');
INSERT INTO `menu` VALUES ('26', 'Schedule', 'ti-calendar', 'calendar/index', '13', '', '0', '0', '0', '0', '0');
INSERT INTO `menu` VALUES ('27', 'Bot Setup', 'ti-reddit', '#', '10', '200', '1', '0', '0', '2', '0');
INSERT INTO `menu` VALUES ('28', 'Automation', 'fa fa-tint', '#', '10', '218', '1', '0', '0', '3', '0');

-- ----------------------------
-- Table structure for menu_child_1
-- ----------------------------
DROP TABLE IF EXISTS `menu_child_1`;
CREATE TABLE `menu_child_1` (
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
  `is_external` enum('0','1') NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=45 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of menu_child_1
-- ----------------------------
INSERT INTO `menu_child_1` VALUES ('1', 'User Management', 'admin/user_management', '1', 'ti-user', '', '5', '0', '1', '0', '0');
INSERT INTO `menu_child_1` VALUES ('2', 'Send Notification', 'admin/notify_members', '2', 'ti-bell', '', '5', '0', '1', '0', '0');
INSERT INTO `menu_child_1` VALUES ('3', 'Settings', '#', '3', 'ti-settings', '', '5', '1', '1', '0', '0');
INSERT INTO `menu_child_1` VALUES ('4', 'Payment', '#', '4', 'ti-money', '', '5', '1', '1', '0', '0');
INSERT INTO `menu_child_1` VALUES ('5', 'Auto Reply', 'facebook_ex_autoreply/index', '1', 'fa fa-reply-all', '80', '14', '0', '0', '0', '0');
INSERT INTO `menu_child_1` VALUES ('6', 'auto reply report', 'facebook_ex_autoreply/all_auto_reply_report', '2', 'ti-list-ol', '80', '14', '0', '0', '0', '0');
INSERT INTO `menu_child_1` VALUES ('7', 'Website Widget', 'facebook_ex_message_button/index', '3', 'ti-comment', '77', '14', '0', '0', '0', '0');
INSERT INTO `menu_child_1` VALUES ('8', 'Ad Template', 'facebook_ex_json_messanger/index', '4', 'ti-split-v-alt', '81', '14', '0', '0', '0', '0');
INSERT INTO `menu_child_1` VALUES ('11', 'settings', 'fb_msg_manager/index', '1', 'ti-settings', '', '15', '0', '0', '0', '0');
INSERT INTO `menu_child_1` VALUES ('12', 'Bot Report', 'fb_msg_manager/message_dashboard', '2', 'ti-dashboard', '82', '15', '0', '0', '0', '0');
INSERT INTO `menu_child_1` VALUES ('14', 'Import', 'facebook_ex_import_lead/index', '1', 'ti-cloud-down', '76', '7', '0', '0', '0', '0');
INSERT INTO `menu_child_1` VALUES ('15', 'Groups', 'facebook_ex_import_lead/contact_group', '2', 'ti-layout-column2', '76', '7', '0', '0', '0', '0');
INSERT INTO `menu_child_1` VALUES ('16', 'All audience', 'facebook_ex_import_lead/contact_list', '3', 'ti-user', '76', '7', '0', '0', '0', '0');
INSERT INTO `menu_child_1` VALUES ('17', 'Multi-Page Broadcast', 'facebook_ex_campaign/create_multipage_campaign', '1', 'ti-layers', '76', '10', '0', '0', '0', '0');
INSERT INTO `menu_child_1` VALUES ('18', 'Multi-Group Broadcast', 'facebook_ex_campaign/create_multigroup_campaign', '2', 'fa fa-object-ungroup', '76', '10', '0', '0', '0', '0');
INSERT INTO `menu_child_1` VALUES ('19', 'Custom Broadcast', 'facebook_ex_campaign/custom_campaign', '3', 'ti-control-shuffle', '76', '10', '0', '0', '0', '0');
INSERT INTO `menu_child_1` VALUES ('20', 'Broadcast Report', 'facebook_ex_campaign/campaign_report', '4', 'ti-list', '76', '10', '0', '0', '0', '0');
INSERT INTO `menu_child_1` VALUES ('21', 'Announcement', 'announcement/full_list', '5', 'ti-announcement', '', '5', '0', '1', '0', '0');
INSERT INTO `menu_child_1` VALUES ('22', 'Create Post', 'ultrapost/text_image_link_video', '1', 'ti-menu-alt', '223', '20', '0', '0', '0', '0');
INSERT INTO `menu_child_1` VALUES ('23', 'Create CTA', 'ultrapost/cta_post', '3', 'ti-arrow-right', '220', '20', '0', '0', '0', '0');
INSERT INTO `menu_child_1` VALUES ('24', 'Multi-Slide Post', 'ultrapost/carousel_slider_post', '4', 'ti-video-camera', '222', '20', '0', '0', '0', '0');
INSERT INTO `menu_child_1` VALUES ('25', 'Auto-reply Template Manager', 'ultrapost/template_manager', '5', 'ti-layout-grid2', '', '14', '0', '0', '0', '0');
INSERT INTO `menu_child_1` VALUES ('27', 'Setup', 'facebook_ex_auto_comment/index', '5', 'ti-comment', '251', '21', '0', '0', '0', '0');
INSERT INTO `menu_child_1` VALUES ('28', 'Template', 'facebook_ex_auto_comment/comment_template_manager', '6', 'ti-layout-grid2', '251', '21', '0', '0', '0', '0');
INSERT INTO `menu_child_1` VALUES ('29', 'Auto Comment Report', 'facebook_ex_auto_comment/all_auto_reply_report', '7', 'ti-view-list-alt', '251', '21', '0', '0', '0', '0');
INSERT INTO `menu_child_1` VALUES ('30', 'Support Category', 'simplesupport/support_category', '1', 'ti-menu-alt', '204', '24', '0', '1', '0', '0');
INSERT INTO `menu_child_1` VALUES ('31', 'All Ticket', 'simplesupport/all_ticket', '1', 'ti-ticket', '204', '24', '0', '1', '0', '0');
INSERT INTO `menu_child_1` VALUES ('32', 'User Activity Log', 'admin/activity_log', '1', 'ti-layers-alt', '', '5', '0', '1', '0', '0');
INSERT INTO `menu_child_1` VALUES ('33', 'Page Messaging', 'facebook_rx_account_import/pages_messaging', '3', 'ti-email', '', '15', '0', '1', '0', '0');
INSERT INTO `menu_child_1` VALUES ('34', 'Dashboard', 'messenger_bot/index', '1', 'ti-dashboard', '200', '27', '0', '0', '0', '0');
INSERT INTO `menu_child_1` VALUES ('35', 'General Settings', 'messenger_bot/configuration', '2', 'ti-settings', '200', '27', '0', '0', '0', '0');
INSERT INTO `menu_child_1` VALUES ('36', 'Facebook API Settings', 'messenger_bot/facebook_config', '3', 'ti-facebook', '200', '27', '0', '0', '0', '0');
INSERT INTO `menu_child_1` VALUES ('37', 'Add Account', 'messenger_bot/account_import', '4', 'ti-cloud-down', '200', '27', '0', '0', '0', '0');
INSERT INTO `menu_child_1` VALUES ('38', 'Active Domains', 'messenger_bot/domain_whitelist', '5', 'ti-check-box', '200', '27', '0', '0', '0', '0');
INSERT INTO `menu_child_1` VALUES ('39', 'Settings', 'messenger_bot/bot_list', '6', 'ti-panel', '200', '27', '0', '0', '0', '0');
INSERT INTO `menu_child_1` VALUES ('40', 'Post-back Manager', 'messenger_bot/template_manager', '7', 'ti-layout-grid2', '200', '27', '0', '0', '0', '0');
INSERT INTO `menu_child_1` VALUES ('41', 'Cron Job', 'messenger_bot/cron_job', '8', 'ti-time', '200', '27', '0', '0', '0', '0');
INSERT INTO `menu_child_1` VALUES ('42', 'Automation Setup', 'drip_messaging/eligible_pages', '1', 'ti-settings', '218', '28', '0', '0', '0', '0');
INSERT INTO `menu_child_1` VALUES ('43', 'Message Sent Log', 'drip_messaging/messaging_report', '2', 'ti-menu-alt', '218', '28', '0', '0', '0', '0');
INSERT INTO `menu_child_1` VALUES ('44', 'Cron Job', 'drip_messaging/cron_job', '3', 'ti-time', '218', '28', '0', '1', '0', '0');

-- ----------------------------
-- Table structure for menu_child_2
-- ----------------------------
DROP TABLE IF EXISTS `menu_child_2`;
CREATE TABLE `menu_child_2` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  `serial` int(11) NOT NULL,
  `icon` varchar(255) NOT NULL,
  `module_access` varchar(255) NOT NULL,
  `parent_child` int(11) NOT NULL,
  `only_admin` enum('1','0') NOT NULL DEFAULT '1',
  `only_member` enum('1','0') NOT NULL DEFAULT '0',
  `is_external` enum('0','1') NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of menu_child_2
-- ----------------------------
INSERT INTO `menu_child_2` VALUES ('1', 'Email Settings', 'admin_config_email/index', '2', 'ti-email', '', '3', '1', '0', '0');
INSERT INTO `menu_child_2` VALUES ('2', 'General Settings', 'admin_config/configuration', '1', 'ti-settings', '', '3', '1', '0', '0');
INSERT INTO `menu_child_2` VALUES ('3', 'Analytics Settings', 'admin_config/analytics_config', '3', 'ti-pie-chart', '', '3', '1', '0', '0');
INSERT INTO `menu_child_2` VALUES ('5', 'advertisement settings', 'admin_config_ad/ad_config', '5', 'ti-announcement', '', '3', '1', '0', '0');
INSERT INTO `menu_child_2` VALUES ('6', 'social login settings', 'admin_config_login/login_config', '6', 'fa fa-sign-in', '', '3', '1', '0', '0');
INSERT INTO `menu_child_2` VALUES ('7', 'Facebook API Settings', 'facebook_rx_config/index', '7', 'ti-facebook', '', '3', '1', '0', '0');
INSERT INTO `menu_child_2` VALUES ('8', 'Dashboard', 'payment/payment_dashboard_admin', '1', 'ti-dashboard', '', '4', '1', '0', '0');
INSERT INTO `menu_child_2` VALUES ('9', 'Package Settings', 'payment/package_settings', '2', 'ti-package', '', '4', '1', '0', '0');
INSERT INTO `menu_child_2` VALUES ('10', 'Payment Settings', 'payment/payment_setting_admin', '3', 'ti-settings', '', '4', '1', '0', '0');
INSERT INTO `menu_child_2` VALUES ('11', 'Payment History', 'payment/admin_payment_history', '4', 'fa fa-history', '', '4', '1', '0', '0');
INSERT INTO `menu_child_2` VALUES ('12', 'Frontend Settings', 'admin_config/frontend_configuration', '1', 'fa fa-newspaper-o', '', '3', '1', '0', '0');
INSERT INTO `menu_child_2` VALUES ('13', 'Email Template Settings', 'admin_config/email_template_settings', '2', 'ti-id-badge', '', '3', '1', '0', '0');

-- ----------------------------
-- Table structure for messenger_bot
-- ----------------------------
DROP TABLE IF EXISTS `messenger_bot`;
CREATE TABLE `messenger_bot` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `page_id` int(11) NOT NULL,
  `fb_page_id` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `template_type` enum('text','image','audio','video','file','quick reply','text with buttons','generic template','carousel','list','media') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'text',
  `bot_type` enum('generic','keyword') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'generic',
  `keyword_type` enum('reply','post-back','no match','get-started','email-quick-reply','phone-quick-reply','location-quick-reply') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'reply',
  `keywords` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `buttons` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `images` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `audio` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `video` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1',
  `bot_name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `postback_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_replied_at` datetime NOT NULL,
  `is_template` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL,
  `broadcaster_labels` tinytext COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'comma separated',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`,`page_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of messenger_bot
-- ----------------------------

-- ----------------------------
-- Table structure for messenger_bot_config
-- ----------------------------
DROP TABLE IF EXISTS `messenger_bot_config`;
CREATE TABLE `messenger_bot_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `app_name` varchar(100) DEFAULT NULL,
  `api_id` varchar(250) DEFAULT NULL,
  `api_secret` varchar(250) DEFAULT NULL,
  `numeric_id` varchar(250) NOT NULL,
  `user_access_token` varchar(500) DEFAULT NULL,
  `status` enum('0','1') NOT NULL DEFAULT '1',
  `deleted` enum('0','1') NOT NULL DEFAULT '0',
  `user_id` int(11) NOT NULL,
  `use_by` enum('only_me','everyone') NOT NULL DEFAULT 'only_me',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of messenger_bot_config
-- ----------------------------

-- ----------------------------
-- Table structure for messenger_bot_domain_whitelist
-- ----------------------------
DROP TABLE IF EXISTS `messenger_bot_domain_whitelist`;
CREATE TABLE `messenger_bot_domain_whitelist` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `messenger_bot_user_info_id` int(11) NOT NULL,
  `page_id` int(11) NOT NULL,
  `domain` tinytext NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`,`page_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of messenger_bot_domain_whitelist
-- ----------------------------

-- ----------------------------
-- Table structure for messenger_bot_drip_campaign
-- ----------------------------
DROP TABLE IF EXISTS `messenger_bot_drip_campaign`;
CREATE TABLE `messenger_bot_drip_campaign` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `campaign_name` varchar(250) NOT NULL,
  `page_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `message_content` longtext NOT NULL,
  `created_at` datetime NOT NULL,
  `last_sent_at` datetime NOT NULL,
  `drip_type` enum('default','messenger_bot_engagement_checkbox','messenger_bot_engagement_send_to_msg','messenger_bot_engagement_mme','messenger_bot_engagement_messenger_codes','messenger_bot_engagement_2way_chat_plugin','custom') NOT NULL DEFAULT 'default',
  `engagement_table_id` int(11) NOT NULL,
  `between_start` varchar(50) NOT NULL DEFAULT '00:00',
  `between_end` varchar(50) NOT NULL DEFAULT '23:59',
  `timezone` varchar(250) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `page_id` (`page_id`,`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of messenger_bot_drip_campaign
-- ----------------------------

-- ----------------------------
-- Table structure for messenger_bot_drip_report
-- ----------------------------
DROP TABLE IF EXISTS `messenger_bot_drip_report`;
CREATE TABLE `messenger_bot_drip_report` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `messenger_bot_drip_campaign_id` int(11) NOT NULL,
  `messenger_bot_subscriber_id` int(11) NOT NULL,
  `page_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `subscribe_id` varchar(250) NOT NULL,
  `first_name` varchar(250) NOT NULL,
  `last_name` varchar(250) NOT NULL,
  `last_completed_day` int(11) NOT NULL,
  `is_sent` enum('0','1') NOT NULL DEFAULT '1',
  `is_opened` enum('0','1') NOT NULL DEFAULT '0',
  `is_delivered` enum('0','1') NOT NULL DEFAULT '0',
  `sent_at` datetime NOT NULL,
  `delivered_at` datetime NOT NULL,
  `opened_at` datetime NOT NULL,
  `sent_response` tinytext NOT NULL,
  `delivered_response` tinytext NOT NULL,
  `last_updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `messenger_bot_drip_campaign_id` (`messenger_bot_drip_campaign_id`),
  KEY `page_id` (`page_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of messenger_bot_drip_report
-- ----------------------------

-- ----------------------------
-- Table structure for messenger_bot_page_info
-- ----------------------------
DROP TABLE IF EXISTS `messenger_bot_page_info`;
CREATE TABLE `messenger_bot_page_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `messenger_bot_user_info_id` int(11) NOT NULL,
  `page_id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `page_cover` mediumtext COLLATE utf8mb4_unicode_ci,
  `page_profile` mediumtext COLLATE utf8mb4_unicode_ci,
  `page_name` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `page_access_token` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `page_email` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `add_date` date NOT NULL,
  `deleted` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `bot_enabled` enum('0','1','2') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `started_button_enabled` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `welcome_message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `chat_human_email` varchar(250) COLLATE utf8mb4_unicode_ci NOT NULL,
  `persistent_enabled` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `enable_mark_seen` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `enbale_type_on` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `review_status` enum('NOT SUBMITTED','PENDING','REJECTED','APPROVED','LIMITED') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'NOT SUBMITTED',
  `review_status_last_checked` datetime NOT NULL,
  `reply_delay_time` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `page_id` (`page_id`),
  KEY `user_id` (`user_id`,`page_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of messenger_bot_page_info
-- ----------------------------

-- ----------------------------
-- Table structure for messenger_bot_persistent_menu
-- ----------------------------
DROP TABLE IF EXISTS `messenger_bot_persistent_menu`;
CREATE TABLE `messenger_bot_persistent_menu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `page_id` varchar(100) NOT NULL,
  `locale` varchar(20) NOT NULL DEFAULT 'default',
  `item_json` longtext NOT NULL,
  `composer_input_disabled` enum('0','1') NOT NULL DEFAULT '0',
  `poskback_id_json` text NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `page_id` (`page_id`,`locale`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of messenger_bot_persistent_menu
-- ----------------------------

-- ----------------------------
-- Table structure for messenger_bot_postback
-- ----------------------------
DROP TABLE IF EXISTS `messenger_bot_postback`;
CREATE TABLE `messenger_bot_postback` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `postback_id` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `page_id` int(11) NOT NULL,
  `use_status` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `status` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1',
  `messenger_bot_table_id` int(11) NOT NULL,
  `bot_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_template` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL,
  `template_jsoncode` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `template_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `template_for` enum('reply_message','unsubscribe','resubscribe','email-quick-reply','phone-quick-reply','location-quick-reply','chat-with-human','chat-with-bot') COLLATE utf8mb4_unicode_ci NOT NULL,
  `template_id` int(11) NOT NULL,
  `inherit_from_template` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL,
  `broadcaster_labels` tinytext COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'comma separated',
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`,`postback_id`,`page_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of messenger_bot_postback
-- ----------------------------

-- ----------------------------
-- Table structure for messenger_bot_quick_reply_email
-- ----------------------------
DROP TABLE IF EXISTS `messenger_bot_quick_reply_email`;
CREATE TABLE `messenger_bot_quick_reply_email` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fb_page_id` varchar(50) NOT NULL,
  `user_id` int(11) NOT NULL,
  `fb_user_id` varchar(50) NOT NULL,
  `fb_user_first_name` varchar(100) NOT NULL,
  `fb_user_last_name` varchar(100) NOT NULL,
  `profile_pic` text NOT NULL,
  `email` varchar(200) NOT NULL,
  `entry_time` datetime NOT NULL,
  `last_update_time` datetime NOT NULL,
  `phone_number` varchar(20) NOT NULL,
  `phone_number_entry_time` datetime NOT NULL,
  `phone_number_last_update` datetime NOT NULL,
  `user_location` varchar(30) NOT NULL,
  `location_map_url` text NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `fb_page_id` (`fb_page_id`,`fb_user_id`,`user_id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of messenger_bot_quick_reply_email
-- ----------------------------

-- ----------------------------
-- Table structure for messenger_bot_reply_error_log
-- ----------------------------
DROP TABLE IF EXISTS `messenger_bot_reply_error_log`;
CREATE TABLE `messenger_bot_reply_error_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `page_id` int(11) NOT NULL,
  `fb_page_id` varchar(200) NOT NULL,
  `user_id` int(11) NOT NULL,
  `error_message` varchar(250) NOT NULL,
  `bot_settings_id` int(11) NOT NULL,
  `error_time` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of messenger_bot_reply_error_log
-- ----------------------------

-- ----------------------------
-- Table structure for messenger_bot_subscriber
-- ----------------------------
DROP TABLE IF EXISTS `messenger_bot_subscriber`;
CREATE TABLE `messenger_bot_subscriber` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `page_id` varchar(200) NOT NULL,
  `subscribe_id` varchar(255) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `profile_pic` varchar(255) NOT NULL,
  `gender` varchar(255) NOT NULL,
  `locale` varchar(255) NOT NULL,
  `timezone` varchar(255) NOT NULL,
  `refferer_id` varchar(100) NOT NULL COMMENT 'get started refference number from ref parameter of chat plugin',
  `refferer_source` varchar(50) NOT NULL COMMENT 'CUSTOMER_CHAT_PLUGIN or SHORTLINK or Direct',
  `refferer_uri` tinytext NOT NULL COMMENT 'CUSTOMER_CHAT_PLUGIN URL',
  `subscribed_at` datetime NOT NULL,
  `last_name_update_time` datetime NOT NULL,
  `is_image_download` enum('0','1') NOT NULL DEFAULT '0',
  `image_path` varchar(250) NOT NULL,
  `status` enum('0','1') NOT NULL DEFAULT '1',
  `messenger_bot_drip_campaign_id` int(11) NOT NULL,
  `drip_type` enum('default','messenger_bot_engagement_checkbox','messenger_bot_engagement_send_to_msg','messenger_bot_engagement_mme','messenger_bot_engagement_messenger_codes','messenger_bot_engagement_2way_chat_plugin','custom') NOT NULL DEFAULT 'default',
  `messenger_bot_drip_last_completed_day` int(11) NOT NULL,
  `messenger_bot_drip_is_toatally_complete` enum('0','1') NOT NULL DEFAULT '0',
  `messenger_bot_drip_last_sent_at` datetime NOT NULL,
  `messenger_bot_drip_initial_date` datetime NOT NULL,
  `last_processing_started_at` datetime NOT NULL,
  `messenger_bot_drip_processing_status` enum('0','1') NOT NULL DEFAULT '0',
  `is_imported` enum('0','1') NOT NULL DEFAULT '0',
  `is_updated_name` enum('0','1') NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`,`page_id`,`subscribe_id`) USING BTREE,
  KEY `messenger_bot_drip_campaign_id` (`messenger_bot_drip_campaign_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of messenger_bot_subscriber
-- ----------------------------

-- ----------------------------
-- Table structure for messenger_bot_user_info
-- ----------------------------
DROP TABLE IF EXISTS `messenger_bot_user_info`;
CREATE TABLE `messenger_bot_user_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `messenger_bot_config_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `access_token` text NOT NULL,
  `name` varchar(200) DEFAULT NULL,
  `email` varchar(200) DEFAULT NULL,
  `fb_id` varchar(200) NOT NULL,
  `add_date` date NOT NULL,
  `deleted` enum('0','1') NOT NULL,
  `need_to_delete` enum('0','1') NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of messenger_bot_user_info
-- ----------------------------

-- ----------------------------
-- Table structure for modules
-- ----------------------------
DROP TABLE IF EXISTS `modules`;
CREATE TABLE `modules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `module_name` varchar(250) DEFAULT NULL,
  `add_ons_id` int(11) NOT NULL,
  `extra_text` varchar(50) NOT NULL DEFAULT 'month',
  `limit_enabled` enum('0','1') NOT NULL DEFAULT '1',
  `deleted` enum('0','1') NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=257 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of modules
-- ----------------------------
INSERT INTO `modules` VALUES ('65', 'Facebook Account Import', '0', '', '1', '0');
INSERT INTO `modules` VALUES ('76', 'Bulk Message Campaign', '0', 'month', '1', '0');
INSERT INTO `modules` VALUES ('77', 'Lead Generator : Message Send Button', '0', '', '0', '0');
INSERT INTO `modules` VALUES ('78', 'Background Lead Scan', '0', '', '0', '0');
INSERT INTO `modules` VALUES ('79', 'Bulk Message Send Limit', '0', 'month', '1', '0');
INSERT INTO `modules` VALUES ('80', 'Lead Generator : Auto Reply Enabled Post', '0', '', '1', '0');
INSERT INTO `modules` VALUES ('81', 'Lead Generator : Messenger Ad JSON Script', '0', '', '1', '0');
INSERT INTO `modules` VALUES ('82', 'Page Inbox Manager', '0', '', '0', '0');
INSERT INTO `modules` VALUES ('197', 'Messenger Bot - Persistent Menu', '0', '', '1', '0');
INSERT INTO `modules` VALUES ('198', 'Messenger Bot - Persistent Menu Copyright', '0', '', '0', '0');
INSERT INTO `modules` VALUES ('199', 'Messenger Bot - Account Import', '0', '', '1', '0');
INSERT INTO `modules` VALUES ('200', 'Messenger Bot Enabled Page', '2', '', '1', '0');
INSERT INTO `modules` VALUES ('218', 'Messenger Bot - Drip Messaging : Message Send Limit', '3', '', '1', '0');
INSERT INTO `modules` VALUES ('219', 'Messenger Bot - Drip Messaging : Eligible Pages', '0', '', '1', '0');
INSERT INTO `modules` VALUES ('220', 'CTA Post', '0', 'month', '1', '0');
INSERT INTO `modules` VALUES ('222', 'Carousel/Slider Post', '0', 'month', '1', '0');
INSERT INTO `modules` VALUES ('223', 'Text/Image/Link/Video Post', '0', 'month', '1', '0');
INSERT INTO `modules` VALUES ('251', 'Auto Comment Campaign', '0', '', '1', '0');
INSERT INTO `modules` VALUES ('256', 'Auto Posting (RSS)', '0', '', '1', '0');

-- ----------------------------
-- Table structure for native_api
-- ----------------------------
DROP TABLE IF EXISTS `native_api`;
CREATE TABLE `native_api` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `api_key` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of native_api
-- ----------------------------

-- ----------------------------
-- Table structure for package
-- ----------------------------
DROP TABLE IF EXISTS `package`;
CREATE TABLE `package` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `package_name` varchar(250) NOT NULL,
  `module_ids` varchar(250) CHARACTER SET latin1 NOT NULL,
  `monthly_limit` text,
  `bulk_limit` text,
  `price` varchar(20) CHARACTER SET latin1 NOT NULL DEFAULT '0',
  `validity` int(11) NOT NULL,
  `is_default` enum('0','1') NOT NULL,
  `deleted` enum('0','1') CHARACTER SET latin1 NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of package
-- ----------------------------
INSERT INTO `package` VALUES ('1', 'Trial', '65,69,76,77,78,79,80,81,82,83,12,84,28,13,1,2', '{\"65\":0,\"69\":0,\"76\":0,\"77\":0,\"78\":0,\"79\":\"0\",\"80\":0,\"81\":0,\"82\":0,\"83\":0,\"12\":0,\"84\":0,\"28\":0,\"13\":0,\"1\":0,\"2\":0}', '{\"65\":0,\"69\":0,\"76\":0,\"77\":0,\"78\":0,\"79\":\"0\",\"80\":0,\"81\":0,\"82\":0,\"83\":0,\"12\":0,\"84\":0,\"28\":0,\"13\":0,\"1\":0,\"2\":0}', 'Trial', '7', '1', '0');

-- ----------------------------
-- Table structure for page_messaging_information
-- ----------------------------
DROP TABLE IF EXISTS `page_messaging_information`;
CREATE TABLE `page_messaging_information` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `page_id` int(11) NOT NULL,
  `fb_page_id` varchar(200) NOT NULL,
  `keywords` varchar(100) NOT NULL,
  `reply_message` text NOT NULL,
  `message` longtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of page_messaging_information
-- ----------------------------

-- ----------------------------
-- Table structure for payment_config
-- ----------------------------
DROP TABLE IF EXISTS `payment_config`;
CREATE TABLE `payment_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `paypal_email` varchar(250) NOT NULL,
  `stripe_secret_key` varchar(150) NOT NULL,
  `stripe_publishable_key` varchar(150) NOT NULL,
  `currency` enum('USD','AUD','BRL','CAD','CZK','DKK','EUR','HKD','HUF','ILS','JPY','MYR','MXN','TWD','NZD','NOK','PHP','PLN','GBP','RUB','SGD','SEK','CHF','VND') NOT NULL,
  `deleted` enum('0','1') NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of payment_config
-- ----------------------------
INSERT INTO `payment_config` VALUES ('1', 'Paypalemail@example.com', '', '', 'USD', '0');

-- ----------------------------
-- Table structure for paypal_error_log
-- ----------------------------
DROP TABLE IF EXISTS `paypal_error_log`;
CREATE TABLE `paypal_error_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `call_time` datetime DEFAULT NULL,
  `ipn_value` text,
  `error_log` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of paypal_error_log
-- ----------------------------

-- ----------------------------
-- Table structure for transaction_history
-- ----------------------------
DROP TABLE IF EXISTS `transaction_history`;
CREATE TABLE `transaction_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `verify_status` varchar(200) NOT NULL,
  `first_name` varchar(250) NOT NULL,
  `last_name` varchar(250) NOT NULL,
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
  `package_id` int(11) NOT NULL,
  `stripe_card_source` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of transaction_history
-- ----------------------------

-- ----------------------------
-- Table structure for ultrapost_auto_reply
-- ----------------------------
DROP TABLE IF EXISTS `ultrapost_auto_reply`;
CREATE TABLE `ultrapost_auto_reply` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ultrapost_campaign_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `reply_type` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `auto_like_comment` enum('no','yes') COLLATE utf8mb4_unicode_ci NOT NULL,
  `multiple_reply` enum('no','yes') COLLATE utf8mb4_unicode_ci NOT NULL,
  `comment_reply_enabled` enum('no','yes') COLLATE utf8mb4_unicode_ci NOT NULL,
  `nofilter_word_found_text` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `auto_reply_text` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `hide_comment_after_comment_reply` enum('no','yes') COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_delete_offensive` enum('hide','delete') COLLATE utf8mb4_unicode_ci NOT NULL,
  `offensive_words` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `private_message_offensive_words` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of ultrapost_auto_reply
-- ----------------------------

-- ----------------------------
-- Table structure for update_list
-- ----------------------------
DROP TABLE IF EXISTS `update_list`;
CREATE TABLE `update_list` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `files` text NOT NULL,
  `sql_query` text NOT NULL,
  `update_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of update_list
-- ----------------------------

-- ----------------------------
-- Table structure for usage_log
-- ----------------------------
DROP TABLE IF EXISTS `usage_log`;
CREATE TABLE `usage_log` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `module_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `usage_month` int(11) NOT NULL,
  `usage_year` year(4) NOT NULL,
  `usage_count` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `module_id` (`module_id`,`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of usage_log
-- ----------------------------

-- ----------------------------
-- Table structure for users
-- ----------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(99) NOT NULL,
  `email` varchar(99) NOT NULL,
  `mobile` varchar(100) NOT NULL,
  `password` varchar(99) NOT NULL,
  `address` text NOT NULL,
  `user_type` enum('Member','Admin') NOT NULL,
  `status` enum('1','0') NOT NULL,
  `add_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `purchase_date` datetime NOT NULL,
  `last_login_at` datetime NOT NULL,
  `activation_code` varchar(20) DEFAULT NULL,
  `expired_date` datetime NOT NULL,
  `package_id` int(11) NOT NULL,
  `deleted` enum('0','1') NOT NULL,
  `brand_logo` text,
  `brand_url` text,
  `vat_no` varchar(100) DEFAULT NULL,
  `currency` enum('USD','AUD','CAD','EUR','ILS','NZD','RUB','SGD','SEK','BRL') NOT NULL DEFAULT 'USD',
  `company_email` varchar(200) DEFAULT NULL,
  `paypal_email` varchar(100) NOT NULL,
  `last_login_ip` varchar(25) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of users
-- ----------------------------
INSERT INTO `users` VALUES ('1', 'Inchatr', 'tonienickkie@gmail.com', '+2347030195298', '$2y$2y$10$yEiE6xTCfddR2ckDVbnJiO8zT/JCgtCEiX0sKJ7mmdpEZazPHQHrS', 'California', 'Admin', '1', '2016-01-01 04:00:00', '0000-00-00 00:00:00', '2019-06-12 06:23:55', null, '0000-00-00 00:00:00', '0', '0', null, null, null, 'USD', null, '', '103.121.60.46');
INSERT INTO `users` VALUES ('2', 'Leonid', 'leonid.danielyan.96@gmail.com', '', 'e10adc3949ba59abbe56e057f20f883e', '', 'Admin', '1', '2019-06-13 16:09:15', '0000-00-00 00:00:00', '2019-06-15 06:12:58', '141763', '2019-06-20 00:00:00', '1', '0', null, null, null, 'USD', null, '', '127.0.0.1');
INSERT INTO `users` VALUES ('3', 'leo', 'leo@gmail.com', '', 'e10adc3949ba59abbe56e057f20f883e', '', 'Member', '1', '2019-06-15 16:52:19', '0000-00-00 00:00:00', '2019-06-15 13:52:41', '844691', '2019-06-22 00:00:00', '1', '0', null, null, null, 'USD', null, '', '127.0.0.1');

-- ----------------------------
-- Table structure for user_login_info
-- ----------------------------
DROP TABLE IF EXISTS `user_login_info`;
CREATE TABLE `user_login_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(12) NOT NULL,
  `user_name` varchar(100) NOT NULL,
  `user_email` varchar(150) NOT NULL,
  `login_time` datetime NOT NULL,
  `login_ip` varchar(25) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of user_login_info
-- ----------------------------
INSERT INTO `user_login_info` VALUES ('1', '1', 'Inchatr', 'tonienickkie@gmail.com', '2019-06-11 06:50:13', '129.56.79.24');
INSERT INTO `user_login_info` VALUES ('2', '1', 'Inchatr', 'tonienickkie@gmail.com', '2019-06-11 12:53:45', '103.121.60.46');
INSERT INTO `user_login_info` VALUES ('3', '1', 'Inchatr', 'tonienickkie@gmail.com', '2019-06-11 23:06:46', '129.56.87.65');
INSERT INTO `user_login_info` VALUES ('4', '1', 'Inchatr', 'tonienickkie@gmail.com', '2019-06-12 06:23:55', '103.121.60.46');
INSERT INTO `user_login_info` VALUES ('5', '2', 'Leonid', 'leonid.danielyan.96@gmail.com', '2019-06-13 13:10:38', '127.0.0.1');
INSERT INTO `user_login_info` VALUES ('6', '2', 'Leonid', 'leonid.danielyan.96@gmail.com', '2019-06-15 06:12:58', '127.0.0.1');
INSERT INTO `user_login_info` VALUES ('7', '3', 'leo', 'leo@gmail.com', '2019-06-15 13:52:41', '127.0.0.1');

-- ----------------------------
-- Table structure for version
-- ----------------------------
DROP TABLE IF EXISTS `version`;
CREATE TABLE `version` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `version` varchar(255) NOT NULL,
  `current` enum('1','0') NOT NULL DEFAULT '1',
  `date` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `version` (`version`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of version
-- ----------------------------
INSERT INTO `version` VALUES ('1', '7.3.5', '1', '2019-06-11 05:50:06');

-- ----------------------------
-- View structure for view_usage_log
-- ----------------------------
DROP VIEW IF EXISTS `view_usage_log`;
CREATE ALGORITHM=UNDEFINED DEFINER=`inchatr_main`@`localhost` SQL SECURITY DEFINER VIEW `view_usage_log` AS select `usage_log`.`id` AS `id`,`usage_log`.`module_id` AS `module_id`,`usage_log`.`user_id` AS `user_id`,`usage_log`.`usage_month` AS `usage_month`,`usage_log`.`usage_year` AS `usage_year`,`usage_log`.`usage_count` AS `usage_count` from `usage_log` where ((`usage_log`.`usage_month` = month(curdate())) and (`usage_log`.`usage_year` = year(curdate()))) ;
