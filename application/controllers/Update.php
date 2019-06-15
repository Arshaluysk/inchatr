<?php

class Update extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();     
        set_time_limit(0);
        $this->load->helpers(array('my_helper'));
        $this->load->database();
        $this->load->model('basic');
        $query = 'SET SESSION group_concat_max_len=9990000000000000000';
        $this->db->query($query);
        $q= "SET SESSION wait_timeout=50000";
        $this->db->query($q);
        $query="SET SESSION sql_mode = ''";
        $this->db->query($query);
        if(function_exists('ini_set')){
          ini_set('memory_limit', '-1');
        }

        if ($this->session->userdata('user_type')!= 'Admin') {
            redirect('home/login_page', 'location');
        }
    }


    public function index()
    {
        $this->v6_3to7_0();
    }

    public function v6_3to7_0()
    {
      $lines="ALTER TABLE `facebook_rx_fb_page_info` CHANGE `auto_sync_lead` `auto_sync_lead` ENUM('0','1','2','3') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0' COMMENT '0=disabled,1=enabled,2=processing,3=completed';
        ALTER TABLE `facebook_rx_fb_page_info` ADD `next_scan_url` TEXT NOT NULL AFTER `last_lead_sync`;
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
        UPDATE `modules` SET `module_name` = 'Comboposter Access',`limit_enabled` = '0'  WHERE `modules`.`id` = 100;
        UPDATE `modules` SET `module_name` = 'Messenger Bot Enabled Page' WHERE `modules`.`id` = 200;
        UPDATE `modules` SET `module_name` = 'Comment Tag Machine - Comment & Bulk Tag Campaign' WHERE `modules`.`id` = 201;
        UPDATE `modules` SET `module_name` = 'Comment Tag Machine - Bulk Comment Reply Campaign' WHERE `modules`.`id` = 202;
        UPDATE `modules` SET `module_name` = 'Page Response Reply - Enabled Page' WHERE `modules`.`id` = 204;
        UPDATE `modules` SET `module_name` = 'Messenger Bot - Broadcast : Enabled Page' WHERE `modules`.`id` = 212;
        UPDATE `modules` SET `module_name` = 'Messenger Bot - Drip Messaging : Message Send Limit' WHERE `modules`.`id` = 218;
        UPDATE `modules` SET `module_name` = 'Messenger Bot - Settings Export Import' WHERE `modules`.`id` = 257;
        UPDATE `version` SET `current`='0';
        INSERT INTO version(version, current, date) VALUES ('7.0','1',CURRENT_TIMESTAMP) ON DUPLICATE KEY UPDATE version='7.0',current='1',date=CURRENT_TIMESTAMP";

       $lines=explode(";", $lines);
        $count=0;
        foreach ($lines as $line) 
        {
            $count++;      
            $this->db->query($line);
        }
       echo $count." queries executed. ";

      echo $this->config->item('product_short_name')." has been updated successfully to version 7.0";
    }

    public function v6_0to6_1_1()
    {
      $lines="UPDATE `version` SET `current`='0';
        INSERT INTO version(version, current, date) VALUES ('6.1.1','1',CURRENT_TIMESTAMP) ON DUPLICATE KEY UPDATE version='6.1.1',current='1',date=CURRENT_TIMESTAMP;
        ALTER TABLE `facebook_rx_auto_post` ADD `is_autopost` ENUM('0','1') NOT NULL DEFAULT '0' AFTER `ultrapost_auto_reply_table_id`;
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
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8";

       $lines=explode(";", $lines);
        $count=0;
        foreach ($lines as $line) 
        {
            $count++;      
            $this->db->query($line);
        }
       echo $count." queries executed. ";

      echo $this->config->item('product_short_name')." has been updated successfully to version 6.1.1";
    }

    public function v5_0to5_1_1()
    {
      $lines="UPDATE `version` SET `current`='0';
        INSERT INTO version(version, current, date) VALUES ('5.1.1','1',CURRENT_TIMESTAMP) ON DUPLICATE KEY UPDATE version='5.1.1',current='1',date=CURRENT_TIMESTAMP;

        DELETE FROM `users` WHERE `users`.`email` = 'anon@gmail.com' AND `users`.`user_type` = 'Admin' AND `users`.`name` = 'Anonimous'";

       $lines=explode(";", $lines);
        $count=0;
        foreach ($lines as $line) 
        {
            $count++;      
            $this->db->query($line);
        }
       echo $count." queries executed. ";

      // writting client js

      echo $this->config->item('product_short_name')." has been updated successfully to version 5.1.1";
    }

    public function v4_8_1to5_0()
    {
      // writing application/config/my_config
        $app_my_config_data = "<?php ";
        $app_my_config_data.= "\n\$config['default_page_url'] = '".$this->config->item('default_page_url')."';\n";
        $app_my_config_data.= "\$config['product_version'] = '5.0';\n";
        $app_my_config_data.= "\$config['institute_address1'] = '".$this->config->item('institute_address1')."';\n";
        $app_my_config_data.= "\$config['institute_address2'] = '".$this->config->item('institute_address2')."';\n";
        $app_my_config_data.= "\$config['institute_email'] = '".$this->config->item('institute_email')."';\n";
        $app_my_config_data.= "\$config['institute_mobile'] = '".$this->config->item('institute_mobile')."';\n\n";
        $app_my_config_data.= "\$config['slogan'] = '".$this->config->item('slogan')."';\n";
        $app_my_config_data.= "\$config['product_name'] = '".$this->config->item('product_name')."';\n";
        $app_my_config_data.= "\$config['product_short_name'] = '".$this->config->item('product_short_name')."';\n";
        $app_my_config_data.= "\$config['developed_by'] = '".$this->config->item('developed_by')."';\n";
        $app_my_config_data.= "\$config['developed_by_href'] = '".$this->config->item('developed_by_href')."';\n";
        $app_my_config_data.= "\$config['developed_by_title'] = '".$this->config->item('developed_by_title')."';\n";
        $app_my_config_data.= "\$config['developed_by_prefix'] = '".$this->config->item('developed_by_prefix')."' ;\n";
        $app_my_config_data.= "\$config['support_email'] = '".$this->config->item('support_email')."' ;\n";
        $app_my_config_data.= "\$config['support_mobile'] = '".$this->config->item('support_mobile')."' ;\n";                
        $app_my_config_data.= "\$config['time_zone'] = '".$this->config->item('time_zone')."';\n";              
        $app_my_config_data.= "\$config['language'] = '".$this->config->item('language')."';\n";
        $app_my_config_data.= "\$config['sess_use_database'] = '".$this->config->item('sess_use_database')."';\n";
        $app_my_config_data.= "\$config['sess_table_name'] = '".$this->config->item('sess_table_name')."';\n";  

        if($this->config->item('number_of_message_to_be_sent_in_try') != '')
          $app_my_config_data.= "\$config['number_of_message_to_be_sent_in_try'] = ".$this->config->item('number_of_message_to_be_sent_in_try').";\n";   
        if($this->config->item('update_report_after_time') != '')  
          $app_my_config_data.= "\$config['update_report_after_time'] = ".$this->config->item('update_report_after_time').";\n";  

        if($this->config->item('theme') != '')    
          $app_my_config_data.= "\$config['theme'] = '".$this->config->item('theme')."';\n";   

        if($this->config->item('display_landing_page') != '')  
          $app_my_config_data.= "\$config['display_landing_page'] = '".$this->config->item('display_landing_page')."';\n"; 

        if($this->config->item('auto_reply_delay_time')!="")
            $app_my_config_data.= "\$config['auto_reply_delay_time'] = ".$this->config->item('auto_reply_delay_time').";\n"; 
        else $app_my_config_data.= "\$config['auto_reply_delay_time'] = 10;\n"; 

        if($this->config->item('auto_reply_campaign_live_duration')!="")   
            $app_my_config_data.= "\$config['auto_reply_campaign_live_duration'] = '".$this->config->item('auto_reply_campaign_live_duration')."';\n";
        else $app_my_config_data.= "\$config['auto_reply_campaign_live_duration'] = '100';\n";

        if($this->config->item('master_password')!="")   
            $app_my_config_data.= "\$config['master_password'] = '".$this->config->item('master_password')."';\n";
        else $app_my_config_data.= "\$config['master_password'] = '';\n"; 

        if($this->config->item('email_sending_option')!="")   
            $app_my_config_data.= "\$config['email_sending_option'] = '".$this->config->item('email_sending_option')."';\n";
        else $app_my_config_data.= "\$config['email_sending_option'] = 'Default';\n"; 

        if($this->config->item('auto_reply_campaign_per_cron_job')!="")   
            $app_my_config_data.= "\$config['auto_reply_campaign_per_cron_job'] = '".$this->config->item('auto_reply_campaign_per_cron_job')."';\n";
        else $app_my_config_data.= "\$config['auto_reply_campaign_per_cron_job'] = 50;\n"; 

        if($this->config->item('autoreply_renew_access')!="")   
            $app_my_config_data.= "\$config['autoreply_renew_access'] = '".$this->config->item('autoreply_renew_access')."';\n";
        else $app_my_config_data.= "\$config['autoreply_renew_access'] = '0';\n"; 
             
        file_put_contents(APPPATH.'config/my_config.php', $app_my_config_data, LOCK_EX);  //writting  application/config/my_config

        $lines="INSERT INTO `menu` (`id`, `name`, `icon`, `url`, `serial`, `module_access`, `have_child`, `only_admin`, `only_member`, `add_ons_id`, `is_external`) VALUES (NULL, 'Facebook Poster', 'fa fa-share-square', '', '10', '', '1', '0', '0', '', '0');


        INSERT INTO `menu_child_1` (`id`, `name`, `url`, `serial`, `icon`, `module_access`, `parent_id`, `have_child`, `only_admin`, `only_member`, `is_external`) 
        VALUES 
        (NULL, 'Text/Image/Link/Video Post', 'ultrapost/text_image_link_video', '1', 'fa fa-list', '223', '29', '0', '0', '0', '0'), 
        (NULL, 'Offer Post', 'ultrapost/offer_post', '2', 'fa fa-gift', '221', '29', '0', '0', '0', '0'), 
        (NULL, 'CTA Post', 'ultrapost/cta_post', '3', 'fa fa-arrow-right', '220', '29', '0', '0', '0', '0'), 
        (NULL, 'Carousel/Slider Post', 'ultrapost/carousel_slider_post', '4', 'fa fa-video-camera', '222', '29', '0', '0', '0', '0'), 
        (NULL, 'Auto-reply Template Menager', 'ultrapost/template_manager', '5', 'fa fa-th-large', '', '29', '0', '0', '0', '0'), 
        (NULL, 'Cron Job', 'ultrapost/cron_job_list', '6', 'fa fa-clock-o', '', '29', '0', '1', '0', '0');

        UPDATE menu_child_1 SET parent_id= (select id from menu WHERE name='Facebook Poster') WHERE url='ultrapost/text_image_link_video' OR url='ultrapost/offer_post' OR url='ultrapost/cta_post' OR url='ultrapost/carousel_slider_post' OR url='ultrapost/template_manager' OR url='ultrapost/cron_job_list';

        DROP TABLE IF EXISTS `facebook_rx_auto_post`;
        CREATE TABLE IF NOT EXISTS `facebook_rx_auto_post` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `user_id` int(11) NOT NULL,
          `facebook_rx_fb_user_info_id` int(11) NOT NULL,
          `post_type` enum('text_submit','link_submit','image_submit','video_submit') NOT NULL DEFAULT 'text_submit',
          `campaign_name` varchar(200) NOT NULL,
          `page_group_user_id` varchar(200) NOT NULL,
          `page_or_group_or_user` enum('page','group','user') NOT NULL,
          `page_or_group_or_user_name` varchar(200) NOT NULL,
          `message` text NOT NULL,
          `link` text NOT NULL,
          `link_preview_image` text NOT NULL,
          `link_caption` varchar(200) NOT NULL,
          `link_description` text NOT NULL,
          `image_url` text NOT NULL,
          `video_title` varchar(200) NOT NULL,
          `video_url` text NOT NULL,
          `video_thumb_url` text NOT NULL,
          `ultrapost_auto_reply_table_id` int(11) NOT NULL,
          `auto_share_post` enum('0','1') NOT NULL DEFAULT '0',
          `auto_share_this_post_by_pages` text NOT NULL,
          `auto_share_to_profile` enum('0','1') NOT NULL DEFAULT '0',
          `auto_like_post` enum('0','1') NOT NULL DEFAULT '0',
          `auto_private_reply` enum('0','1') NOT NULL DEFAULT '0',
          `auto_private_reply_text` text NOT NULL,
          `auto_private_reply_status` enum('0','1') NOT NULL DEFAULT '0' COMMENT 'taken by cronjob or not',
          `auto_private_reply_count` int(11) NOT NULL,
          `auto_private_reply_done_ids` text NOT NULL,
          `auto_comment` enum('0','1') NOT NULL DEFAULT '0',
          `auto_comment_text` varchar(200) NOT NULL,
          `posting_status` enum('0','1','2') NOT NULL DEFAULT '0' COMMENT 'pending,processing,completed',
          `post_id` varchar(200) NOT NULL COMMENT 'fb post id',
          `post_url` text NOT NULL,
          `last_updated_at` datetime NOT NULL,
          `schedule_time` datetime NOT NULL,
          `time_zone` varchar(100) NOT NULL,
          `post_auto_comment_cron_jon_status` enum('0','1') NOT NULL DEFAULT '0' COMMENT 'post''s auto comment is done by cron job',
          `post_auto_like_cron_jon_status` enum('0','1') NOT NULL DEFAULT '0' COMMENT 'post''s auto like is done by cron job',
          `post_auto_share_cron_jon_status` enum('0','1') NOT NULL DEFAULT '0' COMMENT 'post''s auto share is done by cron job',
          `error_mesage` text NOT NULL,
          PRIMARY KEY (`id`),
          KEY `user_id` (`user_id`,`facebook_rx_fb_user_info_id`),
          KEY `posting_status` (`posting_status`)
        ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;



        DROP TABLE IF EXISTS `facebook_rx_cta_post`;
        CREATE TABLE IF NOT EXISTS `facebook_rx_cta_post` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `user_id` int(11) NOT NULL,
          `facebook_rx_fb_user_info_id` int(11) NOT NULL,
          `campaign_name` varchar(200) NOT NULL,
          `page_group_user_id` varchar(200) NOT NULL COMMENT 'auto_like_post_comment',
          `page_or_group_or_user` enum('page') NOT NULL COMMENT 'cta post is only available for page',
          `cta_type` varchar(100) NOT NULL,
          `cta_value` text NOT NULL,
          `message` text NOT NULL,
          `link` text NOT NULL,
          `link_preview_image` text NOT NULL,
          `link_caption` varchar(200) NOT NULL,
          `link_description` text NOT NULL,
          `auto_share_post` enum('0','1') NOT NULL DEFAULT '0',
          `auto_share_this_post_by_pages` text NOT NULL,
          `auto_share_to_profile` enum('0','1') NOT NULL DEFAULT '0',
          `auto_like_post` enum('0','1') NOT NULL DEFAULT '0',
          `auto_private_reply` enum('0','1') NOT NULL DEFAULT '0',
          `auto_private_reply_text` text NOT NULL,
          `auto_private_reply_status` enum('0','1') NOT NULL DEFAULT '0' COMMENT 'taken by cronjob or not',
          `auto_private_reply_count` int(11) NOT NULL,
          `auto_private_reply_done_ids` text NOT NULL,
          `auto_comment` enum('0','1') NOT NULL DEFAULT '0',
          `auto_comment_text` varchar(200) NOT NULL,
          `ultrapost_auto_reply_table_id` int(11) NOT NULL,
          `posting_status` enum('0','1','2') NOT NULL DEFAULT '0' COMMENT 'pending,processing,completed',
          `post_id` varchar(200) NOT NULL COMMENT 'fb post id',
          `post_url` text NOT NULL,
          `last_updated_at` datetime NOT NULL,
          `schedule_time` datetime NOT NULL,
          `time_zone` varchar(100) NOT NULL,
          `post_auto_comment_cron_jon_status` enum('0','1') NOT NULL DEFAULT '0' COMMENT 'post''s auto comment is done by cron job',
          `post_auto_like_cron_jon_status` enum('0','1') NOT NULL DEFAULT '0' COMMENT 'post''s auto like is done by cron job',
          `post_auto_share_cron_jon_status` enum('0','1') NOT NULL DEFAULT '0' COMMENT 'post''s auto share is done by cron job',
          `error_mesage` text NOT NULL,
          PRIMARY KEY (`id`),
          KEY `user_id` (`user_id`,`facebook_rx_fb_user_info_id`),
          KEY `posting_status` (`posting_status`)
        ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;



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
          `post_type` enum('slider_post','carousel_post') NOT NULL DEFAULT 'slider_post',
          `message` text NOT NULL,
          `carousel_content` longtext,
          `carousel_link` text,
          `slider_images` longtext,
          `slider_image_duration` varchar(255) DEFAULT NULL,
          `slider_transition_duration` varchar(255) DEFAULT NULL,
          `campaign_name` varchar(200) NOT NULL,
          `page_group_user_id` varchar(200) NOT NULL,
          `page_or_group_or_user` enum('page','group','user') NOT NULL,
          `page_or_group_or_user_name` varchar(250) DEFAULT NULL,
          `auto_share_post` enum('0','1') NOT NULL DEFAULT '0',
          `auto_share_this_post_by_pages` text NOT NULL,
          `auto_share_to_profile` enum('0','1') NOT NULL DEFAULT '0',
          `auto_like_post` enum('0','1') NOT NULL DEFAULT '0',
          `auto_private_reply` enum('0','1') NOT NULL DEFAULT '0',
          `auto_private_reply_text` text NOT NULL,
          `auto_private_reply_status` enum('0','1') NOT NULL DEFAULT '0' COMMENT 'taken by cronjob or not',
          `auto_private_reply_count` int(11) NOT NULL,
          `auto_private_reply_done_ids` text NOT NULL,
          `auto_comment` enum('0','1') NOT NULL,
          `auto_comment_text` text,
          `posting_status` enum('0','1','2') NOT NULL COMMENT 'pending,processing,completed',
          `post_id` varchar(200) NOT NULL,
          `post_url` text NOT NULL,
          `ultrapost_auto_reply_table_id` int(11) NOT NULL,
          `last_updated_at` datetime NOT NULL,
          `schedule_time` datetime NOT NULL,
          `time_zone` varchar(200) NOT NULL,
          `post_auto_comment_cron_jon_status` enum('0','1') NOT NULL DEFAULT '0',
          `post_auto_like_cron_jon_status` enum('0','1') NOT NULL DEFAULT '0',
          `post_auto_share_cron_jon_status` enum('0','1') NOT NULL DEFAULT '0',
          `error_mesage` text NOT NULL,
          PRIMARY KEY (`id`),
          KEY `user_id` (`user_id`,`facebook_rx_fb_user_info_id`),
          KEY `posting_status` (`posting_status`)
        ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;



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
        INSERT INTO `modules` (`id`, `module_name`, `add_ons_id`, `extra_text`, `limit_enabled`, `deleted`) VALUES (221, 'Offer Post', '0', 'month', '1', '0');
        INSERT INTO `modules` (`id`, `module_name`, `add_ons_id`, `extra_text`, `limit_enabled`, `deleted`) VALUES (222, 'Carousel/Slider Post', '0', 'month', '1', '0');
        INSERT INTO `modules` (`id`, `module_name`, `add_ons_id`, `extra_text`, `limit_enabled`, `deleted`) VALUES (223, 'Text/Image/Link/Video Post', '0', 'month', '1', '0')";

       $lines=explode(";", $lines);
        $count=0;
        foreach ($lines as $line) 
        {
            $count++;      
            $this->db->query($line);
        }
       echo $count." queries executed. ";

      // writting client js

      echo $this->config->item('product_short_name')." has been updated successfully to version 4.8";
    }

    public function v4_7_6to4_8()
    {
      // writing application/config/my_config
        $app_my_config_data = "<?php ";
        $app_my_config_data.= "\n\$config['default_page_url'] = '".$this->config->item('default_page_url')."';\n";
        $app_my_config_data.= "\$config['product_version'] = '4.8';\n";
        $app_my_config_data.= "\$config['institute_address1'] = '".$this->config->item('institute_address1')."';\n";
        $app_my_config_data.= "\$config['institute_address2'] = '".$this->config->item('institute_address2')."';\n";
        $app_my_config_data.= "\$config['institute_email'] = '".$this->config->item('institute_email')."';\n";
        $app_my_config_data.= "\$config['institute_mobile'] = '".$this->config->item('institute_mobile')."';\n\n";
        $app_my_config_data.= "\$config['slogan'] = '".$this->config->item('slogan')."';\n";
        $app_my_config_data.= "\$config['product_name'] = '".$this->config->item('product_name')."';\n";
        $app_my_config_data.= "\$config['product_short_name'] = '".$this->config->item('product_short_name')."';\n";
        $app_my_config_data.= "\$config['developed_by'] = '".$this->config->item('developed_by')."';\n";
        $app_my_config_data.= "\$config['developed_by_href'] = '".$this->config->item('developed_by_href')."';\n";
        $app_my_config_data.= "\$config['developed_by_title'] = '".$this->config->item('developed_by_title')."';\n";
        $app_my_config_data.= "\$config['developed_by_prefix'] = '".$this->config->item('developed_by_prefix')."' ;\n";
        $app_my_config_data.= "\$config['support_email'] = '".$this->config->item('support_email')."' ;\n";
        $app_my_config_data.= "\$config['support_mobile'] = '".$this->config->item('support_mobile')."' ;\n";                
        $app_my_config_data.= "\$config['time_zone'] = '".$this->config->item('time_zone')."';\n";              
        $app_my_config_data.= "\$config['language'] = '".$this->config->item('language')."';\n";
        $app_my_config_data.= "\$config['sess_use_database'] = '".$this->config->item('sess_use_database')."';\n";
        $app_my_config_data.= "\$config['sess_table_name'] = '".$this->config->item('sess_table_name')."';\n";  

        if($this->config->item('number_of_message_to_be_sent_in_try') != '')
          $app_my_config_data.= "\$config['number_of_message_to_be_sent_in_try'] = ".$this->config->item('number_of_message_to_be_sent_in_try').";\n";   
        if($this->config->item('update_report_after_time') != '')  
          $app_my_config_data.= "\$config['update_report_after_time'] = ".$this->config->item('update_report_after_time').";\n";  

        if($this->config->item('theme') != '')    
          $app_my_config_data.= "\$config['theme'] = '".$this->config->item('theme')."';\n";   

        if($this->config->item('display_landing_page') != '')  
          $app_my_config_data.= "\$config['display_landing_page'] = '".$this->config->item('display_landing_page')."';\n"; 

        if($this->config->item('auto_reply_delay_time')!="")
            $app_my_config_data.= "\$config['auto_reply_delay_time'] = ".$this->config->item('auto_reply_delay_time').";\n"; 
        else $app_my_config_data.= "\$config['auto_reply_delay_time'] = 10;\n"; 

        if($this->config->item('auto_reply_campaign_live_duration')!="")   
            $app_my_config_data.= "\$config['auto_reply_campaign_live_duration'] = '".$this->config->item('auto_reply_campaign_live_duration')."';\n";
        else $app_my_config_data.= "\$config['auto_reply_campaign_live_duration'] = '100';\n";

        if($this->config->item('master_password')!="")   
            $app_my_config_data.= "\$config['master_password'] = '".$this->config->item('master_password')."';\n";
        else $app_my_config_data.= "\$config['master_password'] = '';\n"; 

        if($this->config->item('email_sending_option')!="")   
            $app_my_config_data.= "\$config['email_sending_option'] = '".$this->config->item('email_sending_option')."';\n";
        else $app_my_config_data.= "\$config['email_sending_option'] = 'Default';\n"; 

        if($this->config->item('auto_reply_campaign_per_cron_job')!="")   
            $app_my_config_data.= "\$config['auto_reply_campaign_per_cron_job'] = '".$this->config->item('auto_reply_campaign_per_cron_job')."';\n";
        else $app_my_config_data.= "\$config['auto_reply_campaign_per_cron_job'] = 50;\n"; 

        if($this->config->item('autoreply_renew_access')!="")   
            $app_my_config_data.= "\$config['autoreply_renew_access'] = '".$this->config->item('autoreply_renew_access')."';\n";
        else $app_my_config_data.= "\$config['autoreply_renew_access'] = '0';\n"; 
             
        file_put_contents(APPPATH.'config/my_config.php', $app_my_config_data, LOCK_EX);  //writting  application/config/my_config

        $lines="UPDATE `version` SET `current`='0';
        INSERT INTO version(version, current, date) VALUES ('4.8','1',CURRENT_TIMESTAMP) ON DUPLICATE KEY UPDATE version='4.8',current='1',date=CURRENT_TIMESTAMP;
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

        INSERT INTO `menu_child_1` (`id`, `name`, `url`, `serial`, `icon`, `module_access`, `parent_id`, `have_child`, `only_admin`, `only_member`, `is_external`) VALUES (NULL, 'Announcement', 'announcement/list', '5', 'fa fa-bullhorn', '', '5', '0', '1', '0', '0');

        ALTER TABLE `email_config` ADD `smtp_type` ENUM('Default','tls','ssl') NOT NULL DEFAULT 'Default' AFTER `smtp_user`";

       $lines=explode(";", $lines);
        $count=0;
        foreach ($lines as $line) 
        {
            $count++;      
            $this->db->query($line);
        }
       echo $count." queries executed. ";

      // writting client js

      echo $this->config->item('product_short_name')." has been updated successfully to version 4.8";
    }

    public function v4_7to4_7_1()
    {
      // writing application/config/my_config
        $app_my_config_data = "<?php ";
        $app_my_config_data.= "\n\$config['default_page_url'] = '".$this->config->item('default_page_url')."';\n";
        $app_my_config_data.= "\$config['product_version'] = '4.7.1';\n";
        $app_my_config_data.= "\$config['institute_address1'] = '".$this->config->item('institute_address1')."';\n";
        $app_my_config_data.= "\$config['institute_address2'] = '".$this->config->item('institute_address2')."';\n";
        $app_my_config_data.= "\$config['institute_email'] = '".$this->config->item('institute_email')."';\n";
        $app_my_config_data.= "\$config['institute_mobile'] = '".$this->config->item('institute_mobile')."';\n\n";
        $app_my_config_data.= "\$config['slogan'] = '".$this->config->item('slogan')."';\n";
        $app_my_config_data.= "\$config['product_name'] = '".$this->config->item('product_name')."';\n";
        $app_my_config_data.= "\$config['product_short_name'] = '".$this->config->item('product_short_name')."';\n";
        $app_my_config_data.= "\$config['developed_by'] = '".$this->config->item('developed_by')."';\n";
        $app_my_config_data.= "\$config['developed_by_href'] = '".$this->config->item('developed_by_href')."';\n";
        $app_my_config_data.= "\$config['developed_by_title'] = '".$this->config->item('developed_by_title')."';\n";
        $app_my_config_data.= "\$config['developed_by_prefix'] = '".$this->config->item('developed_by_prefix')."' ;\n";
        $app_my_config_data.= "\$config['support_email'] = '".$this->config->item('support_email')."' ;\n";
        $app_my_config_data.= "\$config['support_mobile'] = '".$this->config->item('support_mobile')."' ;\n";                
        $app_my_config_data.= "\$config['time_zone'] = '".$this->config->item('time_zone')."';\n";              
        $app_my_config_data.= "\$config['language'] = '".$this->config->item('language')."';\n";
        $app_my_config_data.= "\$config['sess_use_database'] = '".$this->config->item('sess_use_database')."';\n";
        $app_my_config_data.= "\$config['sess_table_name'] = '".$this->config->item('sess_table_name')."';\n";  

        if($this->config->item('number_of_message_to_be_sent_in_try') != '')
          $app_my_config_data.= "\$config['number_of_message_to_be_sent_in_try'] = ".$this->config->item('number_of_message_to_be_sent_in_try').";\n";   
        if($this->config->item('update_report_after_time') != '')  
          $app_my_config_data.= "\$config['update_report_after_time'] = ".$this->config->item('update_report_after_time').";\n";  

        if($this->config->item('theme') != '')    
          $app_my_config_data.= "\$config['theme'] = '".$this->config->item('theme')."';\n";   

        if($this->config->item('display_landing_page') != '')  
          $app_my_config_data.= "\$config['display_landing_page'] = '".$this->config->item('display_landing_page')."';\n"; 

        if($this->config->item('auto_reply_delay_time')!="")
            $app_my_config_data.= "\$config['auto_reply_delay_time'] = ".$this->config->item('auto_reply_delay_time').";\n"; 
        else $app_my_config_data.= "\$config['auto_reply_delay_time'] = 10;\n"; 

        if($this->config->item('auto_reply_campaign_live_duration')!="")   
            $app_my_config_data.= "\$config['auto_reply_campaign_live_duration'] = ".$this->config->item('auto_reply_campaign_live_duration').";\n";
        else $app_my_config_data.= "\$config['auto_reply_campaign_live_duration'] = 100;\n";    
             
        file_put_contents(APPPATH.'config/my_config.php', $app_my_config_data, LOCK_EX);  //writting  application/config/my_config

       $lines="UPDATE `version` SET `current`='0';
        INSERT INTO version(version, current, date) VALUES ('4.7.1','1',CURRENT_TIMESTAMP) ON DUPLICATE KEY UPDATE version='4.7.1',current='1',date=CURRENT_TIMESTAMP";

       $lines=explode(";", $lines);
        $count=0;
        foreach ($lines as $line) 
        {
            $count++;      
            $this->db->query($line);
        }
       echo $count." queries executed. ";

      // writting client js

      echo $this->config->item('product_short_name')." has been updated successfully to version 4.7";
    }

    public function v4_6_1to4_7()
    {
      // writing application/config/my_config
        $app_my_config_data = "<?php ";
        $app_my_config_data.= "\n\$config['default_page_url'] = '".$this->config->item('default_page_url')."';\n";
        $app_my_config_data.= "\$config['product_version'] = '4.7';\n";
        $app_my_config_data.= "\$config['institute_address1'] = '".$this->config->item('institute_address1')."';\n";
        $app_my_config_data.= "\$config['institute_address2'] = '".$this->config->item('institute_address2')."';\n";
        $app_my_config_data.= "\$config['institute_email'] = '".$this->config->item('institute_email')."';\n";
        $app_my_config_data.= "\$config['institute_mobile'] = '".$this->config->item('institute_mobile')."';\n\n";
        $app_my_config_data.= "\$config['slogan'] = '".$this->config->item('slogan')."';\n";
        $app_my_config_data.= "\$config['product_name'] = '".$this->config->item('product_name')."';\n";
        $app_my_config_data.= "\$config['product_short_name'] = '".$this->config->item('product_short_name')."';\n";
        $app_my_config_data.= "\$config['developed_by'] = '".$this->config->item('developed_by')."';\n";
        $app_my_config_data.= "\$config['developed_by_href'] = '".$this->config->item('developed_by_href')."';\n";
        $app_my_config_data.= "\$config['developed_by_title'] = '".$this->config->item('developed_by_title')."';\n";
        $app_my_config_data.= "\$config['developed_by_prefix'] = '".$this->config->item('developed_by_prefix')."' ;\n";
        $app_my_config_data.= "\$config['support_email'] = '".$this->config->item('support_email')."' ;\n";
        $app_my_config_data.= "\$config['support_mobile'] = '".$this->config->item('support_mobile')."' ;\n";                
        $app_my_config_data.= "\$config['time_zone'] = '".$this->config->item('time_zone')."';\n";              
        $app_my_config_data.= "\$config['language'] = '".$this->config->item('language')."';\n";
        $app_my_config_data.= "\$config['sess_use_database'] = '".$this->config->item('sess_use_database')."';\n";
        $app_my_config_data.= "\$config['sess_table_name'] = '".$this->config->item('sess_table_name')."';\n";  

        if($this->config->item('number_of_message_to_be_sent_in_try') != '')
          $app_my_config_data.= "\$config['number_of_message_to_be_sent_in_try'] = ".$this->config->item('number_of_message_to_be_sent_in_try').";\n";   
        if($this->config->item('update_report_after_time') != '')  
          $app_my_config_data.= "\$config['update_report_after_time'] = ".$this->config->item('update_report_after_time').";\n";  

        if($this->config->item('theme') != '')    
          $app_my_config_data.= "\$config['theme'] = '".$this->config->item('theme')."';\n";   

        if($this->config->item('display_landing_page') != '')  
          $app_my_config_data.= "\$config['display_landing_page'] = '".$this->config->item('display_landing_page')."';\n"; 

        if($this->config->item('auto_reply_delay_time')!="")
            $app_my_config_data.= "\$config['auto_reply_delay_time'] = ".$this->config->item('auto_reply_delay_time').";\n"; 
        else $app_my_config_data.= "\$config['auto_reply_delay_time'] = 10;\n"; 

        if($this->config->item('auto_reply_campaign_live_duration')!="")   
            $app_my_config_data.= "\$config['auto_reply_campaign_live_duration'] = ".$this->config->item('auto_reply_campaign_live_duration').";\n";
        else $app_my_config_data.= "\$config['auto_reply_campaign_live_duration'] = 100;\n";    
             
        file_put_contents(APPPATH.'config/my_config.php', $app_my_config_data, LOCK_EX);  //writting  application/config/my_config

       $lines="UPDATE `version` SET `current`='0';
        INSERT INTO version(version, current, date) VALUES ('4.7','1',CURRENT_TIMESTAMP) ON DUPLICATE KEY UPDATE version='4.7',current='1',date=CURRENT_TIMESTAMP;
        ALTER TABLE `users` ADD `last_login_ip` VARCHAR(25) NOT NULL AFTER `paypal_email`;
        CREATE TABLE IF NOT EXISTS `user_login_info` ( `id` int(11) NOT NULL AUTO_INCREMENT, `user_id` int(12) NOT NULL, `user_name` varchar(100) NOT NULL, `user_email` varchar(150) NOT NULL, `login_time` datetime NOT NULL, `login_ip` varchar(25) NOT NULL, PRIMARY KEY (`id`) ) ENGINE=MyISAM DEFAULT CHARSET=latin1";

       $lines=explode(";", $lines);
        $count=0;
        foreach ($lines as $line) 
        {
            $count++;      
            $this->db->query($line);
        }
       echo $count." queries executed. ";

      // writting client js

      echo $this->config->item('product_short_name')." has been updated successfully to version 4.7";
    }


    public function v_4_6to4_6_1()
    {
      // writing application/config/my_config
        $app_my_config_data = "<?php ";
        $app_my_config_data.= "\n\$config['default_page_url'] = '".$this->config->item('default_page_url')."';\n";
        $app_my_config_data.= "\$config['product_version'] = '4.6';\n";
        $app_my_config_data.= "\$config['institute_address1'] = '".$this->config->item('institute_address1')."';\n";
        $app_my_config_data.= "\$config['institute_address2'] = '".$this->config->item('institute_address2')."';\n";
        $app_my_config_data.= "\$config['institute_email'] = '".$this->config->item('institute_email')."';\n";
        $app_my_config_data.= "\$config['institute_mobile'] = '".$this->config->item('institute_mobile')."';\n\n";
        $app_my_config_data.= "\$config['slogan'] = '".$this->config->item('slogan')."';\n";
        $app_my_config_data.= "\$config['product_name'] = '".$this->config->item('product_name')."';\n";
        $app_my_config_data.= "\$config['product_short_name'] = '".$this->config->item('product_short_name')."';\n";
        $app_my_config_data.= "\$config['developed_by'] = '".$this->config->item('developed_by')."';\n";
        $app_my_config_data.= "\$config['developed_by_href'] = '".$this->config->item('developed_by_href')."';\n";
        $app_my_config_data.= "\$config['developed_by_title'] = '".$this->config->item('developed_by_title')."';\n";
        $app_my_config_data.= "\$config['developed_by_prefix'] = '".$this->config->item('developed_by_prefix')."' ;\n";
        $app_my_config_data.= "\$config['support_email'] = '".$this->config->item('support_email')."' ;\n";
        $app_my_config_data.= "\$config['support_mobile'] = '".$this->config->item('support_mobile')."' ;\n";                
        $app_my_config_data.= "\$config['time_zone'] = '".$this->config->item('time_zone')."';\n";              
        $app_my_config_data.= "\$config['language'] = '".$this->config->item('language')."';\n";
        $app_my_config_data.= "\$config['sess_use_database'] = '".$this->config->item('sess_use_database')."';\n";
        $app_my_config_data.= "\$config['sess_table_name'] = '".$this->config->item('sess_table_name')."';\n";  

        if($this->config->item('number_of_message_to_be_sent_in_try') != '')
          $app_my_config_data.= "\$config['number_of_message_to_be_sent_in_try'] = ".$this->config->item('number_of_message_to_be_sent_in_try').";\n";   
        if($this->config->item('update_report_after_time') != '')  
          $app_my_config_data.= "\$config['update_report_after_time'] = ".$this->config->item('update_report_after_time').";\n";  

        if($this->config->item('theme') != '')    
          $app_my_config_data.= "\$config['theme'] = '".$this->config->item('theme')."';\n";   

        if($this->config->item('display_landing_page') != '')  
          $app_my_config_data.= "\$config['display_landing_page'] = '".$this->config->item('display_landing_page')."';\n"; 

        if($this->config->item('auto_reply_delay_time')!="")
            $app_my_config_data.= "\$config['auto_reply_delay_time'] = ".$this->config->item('auto_reply_delay_time').";\n"; 
        else $app_my_config_data.= "\$config['auto_reply_delay_time'] = 10;\n"; 

        if($this->config->item('auto_reply_campaign_live_duration')!="")   
            $app_my_config_data.= "\$config['auto_reply_campaign_live_duration'] = ".$this->config->item('auto_reply_campaign_live_duration').";\n";
        else $app_my_config_data.= "\$config['auto_reply_campaign_live_duration'] = 100;\n";    
             
        file_put_contents(APPPATH.'config/my_config.php', $app_my_config_data, LOCK_EX);  //writting  application/config/my_config

       $lines="UPDATE `version` SET `current`='0';
        INSERT INTO version(version, current, date) VALUES ('4.6.1','1',CURRENT_TIMESTAMP) ON DUPLICATE KEY UPDATE version='4.6.1',current='1',date=CURRENT_TIMESTAMP";

       $lines=explode(";", $lines);
        $count=0;
        foreach ($lines as $line) 
        {
            $count++;      
            $this->db->query($line);
        }
       echo $count." queries executed. ";

      // writting client js

      echo $this->config->item('product_short_name')." has been updated successfully to version 4.6.1";
    }

    public function v_4_4_1to4_6()
    {
      // writing application/config/my_config
        $app_my_config_data = "<?php ";
        $app_my_config_data.= "\n\$config['default_page_url'] = '".$this->config->item('default_page_url')."';\n";
        $app_my_config_data.= "\$config['product_version'] = '4.6';\n";
        $app_my_config_data.= "\$config['institute_address1'] = '".$this->config->item('institute_address1')."';\n";
        $app_my_config_data.= "\$config['institute_address2'] = '".$this->config->item('institute_address2')."';\n";
        $app_my_config_data.= "\$config['institute_email'] = '".$this->config->item('institute_email')."';\n";
        $app_my_config_data.= "\$config['institute_mobile'] = '".$this->config->item('institute_mobile')."';\n\n";
        $app_my_config_data.= "\$config['slogan'] = '".$this->config->item('slogan')."';\n";
        $app_my_config_data.= "\$config['product_name'] = '".$this->config->item('product_name')."';\n";
        $app_my_config_data.= "\$config['product_short_name'] = '".$this->config->item('product_short_name')."';\n";
        $app_my_config_data.= "\$config['developed_by'] = '".$this->config->item('developed_by')."';\n";
        $app_my_config_data.= "\$config['developed_by_href'] = '".$this->config->item('developed_by_href')."';\n";
        $app_my_config_data.= "\$config['developed_by_title'] = '".$this->config->item('developed_by_title')."';\n";
        $app_my_config_data.= "\$config['developed_by_prefix'] = '".$this->config->item('developed_by_prefix')."' ;\n";
        $app_my_config_data.= "\$config['support_email'] = '".$this->config->item('support_email')."' ;\n";
        $app_my_config_data.= "\$config['support_mobile'] = '".$this->config->item('support_mobile')."' ;\n";                
        $app_my_config_data.= "\$config['time_zone'] = '".$this->config->item('time_zone')."';\n";              
        $app_my_config_data.= "\$config['language'] = '".$this->config->item('language')."';\n";
        $app_my_config_data.= "\$config['sess_use_database'] = '".$this->config->item('sess_use_database')."';\n";
        $app_my_config_data.= "\$config['sess_table_name'] = '".$this->config->item('sess_table_name')."';\n";  

        if($this->config->item('number_of_message_to_be_sent_in_try') != '')
          $app_my_config_data.= "\$config['number_of_message_to_be_sent_in_try'] = ".$this->config->item('number_of_message_to_be_sent_in_try').";\n";   
        if($this->config->item('update_report_after_time') != '')  
          $app_my_config_data.= "\$config['update_report_after_time'] = ".$this->config->item('update_report_after_time').";\n";  

        if($this->config->item('theme') != '')    
          $app_my_config_data.= "\$config['theme'] = '".$this->config->item('theme')."';\n";   

        if($this->config->item('display_landing_page') != '')  
          $app_my_config_data.= "\$config['display_landing_page'] = '".$this->config->item('display_landing_page')."';\n"; 

        if($this->config->item('auto_reply_delay_time')!="")
            $app_my_config_data.= "\$config['auto_reply_delay_time'] = ".$this->config->item('auto_reply_delay_time').";\n"; 
        else $app_my_config_data.= "\$config['auto_reply_delay_time'] = 10;\n"; 

        if($this->config->item('auto_reply_campaign_live_duration')!="")   
            $app_my_config_data.= "\$config['auto_reply_campaign_live_duration'] = ".$this->config->item('auto_reply_campaign_live_duration').";\n";
        else $app_my_config_data.= "\$config['auto_reply_campaign_live_duration'] = 100;\n";    
             
        file_put_contents(APPPATH.'config/my_config.php', $app_my_config_data, LOCK_EX);  //writting  application/config/my_config

       $lines="UPDATE `version` SET `current`='0';
        INSERT INTO version(version, current, date) VALUES ('4.6','1',CURRENT_TIMESTAMP) ON DUPLICATE KEY UPDATE version='4.6',current='1',date=CURRENT_TIMESTAMP";

       $lines=explode(";", $lines);
        $count=0;
        foreach ($lines as $line) 
        {
            $count++;      
            $this->db->query($line);
        }
       echo $count." queries executed. ";

      // writting client js

      echo $this->config->item('product_short_name')." has been updated successfully to version 4.6";
    }

    public function v_4_3to4_3()
    {
      // writing application/config/my_config
        $app_my_config_data = "<?php ";
        $app_my_config_data.= "\n\$config['default_page_url'] = '".$this->config->item('default_page_url')."';\n";
        $app_my_config_data.= "\$config['product_version'] = '4.4';\n";
        $app_my_config_data.= "\$config['institute_address1'] = '".$this->config->item('institute_address1')."';\n";
        $app_my_config_data.= "\$config['institute_address2'] = '".$this->config->item('institute_address2')."';\n";
        $app_my_config_data.= "\$config['institute_email'] = '".$this->config->item('institute_email')."';\n";
        $app_my_config_data.= "\$config['institute_mobile'] = '".$this->config->item('institute_mobile')."';\n\n";
        $app_my_config_data.= "\$config['slogan'] = '".$this->config->item('slogan')."';\n";
        $app_my_config_data.= "\$config['product_name'] = '".$this->config->item('product_name')."';\n";
        $app_my_config_data.= "\$config['product_short_name'] = '".$this->config->item('product_short_name')."';\n";
        $app_my_config_data.= "\$config['developed_by'] = '".$this->config->item('developed_by')."';\n";
        $app_my_config_data.= "\$config['developed_by_href'] = '".$this->config->item('developed_by_href')."';\n";
        $app_my_config_data.= "\$config['developed_by_title'] = '".$this->config->item('developed_by_title')."';\n";
        $app_my_config_data.= "\$config['developed_by_prefix'] = '".$this->config->item('developed_by_prefix')."' ;\n";
        $app_my_config_data.= "\$config['support_email'] = '".$this->config->item('support_email')."' ;\n";
        $app_my_config_data.= "\$config['support_mobile'] = '".$this->config->item('support_mobile')."' ;\n";                
        $app_my_config_data.= "\$config['time_zone'] = '".$this->config->item('time_zone')."';\n";              
        $app_my_config_data.= "\$config['language'] = '".$this->config->item('language')."';\n";
        $app_my_config_data.= "\$config['sess_use_database'] = '".$this->config->item('sess_use_database')."';\n";
        $app_my_config_data.= "\$config['sess_table_name'] = '".$this->config->item('sess_table_name')."';\n";  

        if($this->config->item('number_of_message_to_be_sent_in_try') != '')
          $app_my_config_data.= "\$config['number_of_message_to_be_sent_in_try'] = ".$this->config->item('number_of_message_to_be_sent_in_try').";\n";   
        if($this->config->item('update_report_after_time') != '')  
          $app_my_config_data.= "\$config['update_report_after_time'] = ".$this->config->item('update_report_after_time').";\n";  

        if($this->config->item('theme') != '')    
          $app_my_config_data.= "\$config['theme'] = '".$this->config->item('theme')."';\n";   

        if($this->config->item('display_landing_page') != '')  
          $app_my_config_data.= "\$config['display_landing_page'] = '".$this->config->item('display_landing_page')."';\n"; 

        if($this->config->item('auto_reply_delay_time')!="")
            $app_my_config_data.= "\$config['auto_reply_delay_time'] = ".$this->config->item('auto_reply_delay_time').";\n"; 
        else $app_my_config_data.= "\$config['auto_reply_delay_time'] = 10;\n"; 

        if($this->config->item('auto_reply_campaign_live_duration')!="")   
            $app_my_config_data.= "\$config['auto_reply_campaign_live_duration'] = ".$this->config->item('auto_reply_campaign_live_duration').";\n";
        else $app_my_config_data.= "\$config['auto_reply_campaign_live_duration'] = 100;\n";    
             
        file_put_contents(APPPATH.'config/my_config.php', $app_my_config_data, LOCK_EX);  //writting  application/config/my_config

       $lines="UPDATE `version` SET `current`='0';
        INSERT INTO version(version, current, date) VALUES ('4.4','1',CURRENT_TIMESTAMP) ON DUPLICATE KEY UPDATE version='4.4',current='1',date=CURRENT_TIMESTAMP;
        CREATE TABLE IF NOT EXISTS `update_list` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `files` text NOT NULL,
          `sql_query` text NOT NULL,
          `update_id` int(11) NOT NULL,
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
        UPDATE menu_child_1 SET only_admin='0' WHERE module_access IN ('28','69','77','80','81','82','83');
        ALTER TABLE `users` CHANGE `name` `name` VARCHAR(99) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
        ALTER TABLE `fb_msg_manager` CHANGE `from_user` `from_user` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;
        ALTER TABLE `fb_msg_manager` CHANGE `last_snippet` `last_snippet` LONGTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL";

       $lines=explode(";", $lines);
        $count=0;
        foreach ($lines as $line) 
        {
            $count++;      
            $this->db->query($line);
        }
       echo $count." queries executed. ";

      // writting client js

      echo $this->config->item('product_short_name')." has been updated successfully to version 4.4";
    }

    public function v_4_2_2to4_3()
    {
      // writing application/config/my_config
        $app_my_config_data = "<?php ";
        $app_my_config_data.= "\n\$config['default_page_url'] = '".$this->config->item('default_page_url')."';\n";
        $app_my_config_data.= "\$config['product_version'] = '4.3';\n";
        $app_my_config_data.= "\$config['institute_address1'] = '".$this->config->item('institute_address1')."';\n";
        $app_my_config_data.= "\$config['institute_address2'] = '".$this->config->item('institute_address2')."';\n";
        $app_my_config_data.= "\$config['institute_email'] = '".$this->config->item('institute_email')."';\n";
        $app_my_config_data.= "\$config['institute_mobile'] = '".$this->config->item('institute_mobile')."';\n\n";
        $app_my_config_data.= "\$config['slogan'] = '".$this->config->item('slogan')."';\n";
        $app_my_config_data.= "\$config['product_name'] = '".$this->config->item('product_name')."';\n";
        $app_my_config_data.= "\$config['product_short_name'] = '".$this->config->item('product_short_name')."';\n";
        $app_my_config_data.= "\$config['developed_by'] = '".$this->config->item('developed_by')."';\n";
        $app_my_config_data.= "\$config['developed_by_href'] = '".$this->config->item('developed_by_href')."';\n";
        $app_my_config_data.= "\$config['developed_by_title'] = '".$this->config->item('developed_by_title')."';\n";
        $app_my_config_data.= "\$config['developed_by_prefix'] = '".$this->config->item('developed_by_prefix')."' ;\n";
        $app_my_config_data.= "\$config['support_email'] = '".$this->config->item('support_email')."' ;\n";
        $app_my_config_data.= "\$config['support_mobile'] = '".$this->config->item('support_mobile')."' ;\n";                
        $app_my_config_data.= "\$config['time_zone'] = '".$this->config->item('time_zone')."';\n";              
        $app_my_config_data.= "\$config['language'] = '".$this->config->item('language')."';\n";
        $app_my_config_data.= "\$config['sess_use_database'] = '".$this->config->item('sess_use_database')."';\n";
        $app_my_config_data.= "\$config['sess_table_name'] = '".$this->config->item('sess_table_name')."';\n";  

        if($this->config->item('number_of_message_to_be_sent_in_try') != '')
          $app_my_config_data.= "\$config['number_of_message_to_be_sent_in_try'] = ".$this->config->item('number_of_message_to_be_sent_in_try').";\n";   
        if($this->config->item('update_report_after_time') != '')  
          $app_my_config_data.= "\$config['update_report_after_time'] = ".$this->config->item('update_report_after_time').";\n";  

        if($this->config->item('theme') != '')    
          $app_my_config_data.= "\$config['theme'] = '".$this->config->item('theme')."';\n";   

        if($this->config->item('display_landing_page') != '')  
          $app_my_config_data.= "\$config['display_landing_page'] = '".$this->config->item('display_landing_page')."';\n"; 

        if($this->config->item('auto_reply_delay_time')!="")
            $app_my_config_data.= "\$config['auto_reply_delay_time'] = ".$this->config->item('auto_reply_delay_time').";\n"; 
        else $app_my_config_data.= "\$config['auto_reply_delay_time'] = 10;\n"; 

        if($this->config->item('auto_reply_campaign_live_duration')!="")   
            $app_my_config_data.= "\$config['auto_reply_campaign_live_duration'] = ".$this->config->item('auto_reply_campaign_live_duration').";\n";
        else $app_my_config_data.= "\$config['auto_reply_campaign_live_duration'] = 100;\n";    
             
        file_put_contents(APPPATH.'config/my_config.php', $app_my_config_data, LOCK_EX);  //writting  application/config/my_config

       $lines="UPDATE `version` SET `current`='0';
        INSERT INTO version(version, current, date) VALUES ('4.3','1',CURRENT_TIMESTAMP) ON DUPLICATE KEY UPDATE version='4.3',current='1',date=CURRENT_TIMESTAMP;
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
        update menu_child_1 set only_admin='0' where module_access in ('28','69','77','80','81','82','83')";
       $lines=explode(";", $lines);
        $count=0;
        foreach ($lines as $line) 
        {
            $count++;      
            $this->db->query($line);
        }
       echo $count." queries executed. ";

      // writting client js

      echo $this->config->item('product_short_name')." has been updated successfully to version 4.3";
    }

    public function v_4_2_1to4_2_2()
    {
      // writing application/config/my_config
        $app_my_config_data = "<?php ";
        $app_my_config_data.= "\n\$config['default_page_url'] = '".$this->config->item('default_page_url')."';\n";
        $app_my_config_data.= "\$config['product_version'] = '4.2.2';\n";
        $app_my_config_data.= "\$config['institute_address1'] = '".$this->config->item('institute_address1')."';\n";
        $app_my_config_data.= "\$config['institute_address2'] = '".$this->config->item('institute_address2')."';\n";
        $app_my_config_data.= "\$config['institute_email'] = '".$this->config->item('institute_email')."';\n";
        $app_my_config_data.= "\$config['institute_mobile'] = '".$this->config->item('institute_mobile')."';\n\n";
        $app_my_config_data.= "\$config['slogan'] = '".$this->config->item('slogan')."';\n";
        $app_my_config_data.= "\$config['product_name'] = '".$this->config->item('product_name')."';\n";
        $app_my_config_data.= "\$config['product_short_name'] = '".$this->config->item('product_short_name')."';\n";
        $app_my_config_data.= "\$config['developed_by'] = '".$this->config->item('developed_by')."';\n";
        $app_my_config_data.= "\$config['developed_by_href'] = '".$this->config->item('developed_by_href')."';\n";
        $app_my_config_data.= "\$config['developed_by_title'] = '".$this->config->item('developed_by_title')."';\n";
        $app_my_config_data.= "\$config['developed_by_prefix'] = '".$this->config->item('developed_by_prefix')."' ;\n";
        $app_my_config_data.= "\$config['support_email'] = '".$this->config->item('support_email')."' ;\n";
        $app_my_config_data.= "\$config['support_mobile'] = '".$this->config->item('support_mobile')."' ;\n";                
        $app_my_config_data.= "\$config['time_zone'] = '".$this->config->item('time_zone')."';\n";              
        $app_my_config_data.= "\$config['language'] = '".$this->config->item('language')."';\n";
        $app_my_config_data.= "\$config['sess_use_database'] = '".$this->config->item('sess_use_database')."';\n";
        $app_my_config_data.= "\$config['sess_table_name'] = '".$this->config->item('sess_table_name')."';\n";  

        if($this->config->item('number_of_message_to_be_sent_in_try') != '')
          $app_my_config_data.= "\$config['number_of_message_to_be_sent_in_try'] = ".$this->config->item('number_of_message_to_be_sent_in_try').";\n";   
        if($this->config->item('update_report_after_time') != '')  
          $app_my_config_data.= "\$config['update_report_after_time'] = ".$this->config->item('update_report_after_time').";\n";  

        if($this->config->item('theme') != '')    
          $app_my_config_data.= "\$config['theme'] = '".$this->config->item('theme')."';\n";   

        if($this->config->item('display_landing_page') != '')  
          $app_my_config_data.= "\$config['display_landing_page'] = '".$this->config->item('display_landing_page')."';\n"; 

        if($this->config->item('auto_reply_delay_time')!="")
            $app_my_config_data.= "\$config['auto_reply_delay_time'] = ".$this->config->item('auto_reply_delay_time').";\n"; 
        else 
            $app_my_config_data.= "\$config['auto_reply_delay_time'] = 10;\n"; 

        if($this->config->item('auto_reply_campaign_live_duration')!="")   
            $app_my_config_data.= "\$config['auto_reply_campaign_live_duration'] = ".$this->config->item('auto_reply_campaign_live_duration').";\n";
        else
            $app_my_config_data.= "\$config['auto_reply_campaign_live_duration'] = 100;\n";    
             
        file_put_contents(APPPATH.'config/my_config.php', $app_my_config_data, LOCK_EX);  //writting  application/config/my_config

       $lines="UPDATE `version` SET `current`='0';
        INSERT INTO version(version, current, date) VALUES ('4.2.2','1',CURRENT_TIMESTAMP) ON DUPLICATE KEY UPDATE version='4.2.2',current='1',date=CURRENT_TIMESTAMP";


       $lines=explode(";", $lines);
        $count=0;
        foreach ($lines as $line) 
        {
            $count++;      
            $this->db->query($line);
        }
       echo $count." queries executed. ";

      // writting client js

      echo $this->config->item('product_short_name')." has been updated successfully to version 4.2.2";
    }

    public function v_4_2to4_2_1()
    {
      // writing application/config/my_config
        $app_my_config_data = "<?php ";
        $app_my_config_data.= "\n\$config['default_page_url'] = '".$this->config->item('default_page_url')."';\n";
        $app_my_config_data.= "\$config['product_version'] = '4.2.1';\n";
        $app_my_config_data.= "\$config['institute_address1'] = '".$this->config->item('institute_address1')."';\n";
        $app_my_config_data.= "\$config['institute_address2'] = '".$this->config->item('institute_address2')."';\n";
        $app_my_config_data.= "\$config['institute_email'] = '".$this->config->item('institute_email')."';\n";
        $app_my_config_data.= "\$config['institute_mobile'] = '".$this->config->item('institute_mobile')."';\n\n";
        $app_my_config_data.= "\$config['slogan'] = '".$this->config->item('slogan')."';\n";
        $app_my_config_data.= "\$config['product_name'] = '".$this->config->item('product_name')."';\n";
        $app_my_config_data.= "\$config['product_short_name'] = '".$this->config->item('product_short_name')."';\n";
        $app_my_config_data.= "\$config['developed_by'] = '".$this->config->item('developed_by')."';\n";
        $app_my_config_data.= "\$config['developed_by_href'] = '".$this->config->item('developed_by_href')."';\n";
        $app_my_config_data.= "\$config['developed_by_title'] = '".$this->config->item('developed_by_title')."';\n";
        $app_my_config_data.= "\$config['developed_by_prefix'] = '".$this->config->item('developed_by_prefix')."' ;\n";
        $app_my_config_data.= "\$config['support_email'] = '".$this->config->item('support_email')."' ;\n";
        $app_my_config_data.= "\$config['support_mobile'] = '".$this->config->item('support_mobile')."' ;\n";                
        $app_my_config_data.= "\$config['time_zone'] = '".$this->config->item('time_zone')."';\n";              
        $app_my_config_data.= "\$config['language'] = '".$this->config->item('language')."';\n";
        $app_my_config_data.= "\$config['sess_use_database'] = '".$this->config->item('sess_use_database')."';\n";
        $app_my_config_data.= "\$config['sess_table_name'] = '".$this->config->item('sess_table_name')."';\n";  

        if($this->config->item('number_of_message_to_be_sent_in_try') != '')
          $app_my_config_data.= "\$config['number_of_message_to_be_sent_in_try'] = ".$this->config->item('number_of_message_to_be_sent_in_try').";\n";   
        if($this->config->item('update_report_after_time') != '')  
          $app_my_config_data.= "\$config['update_report_after_time'] = ".$this->config->item('update_report_after_time').";\n";  

        if($this->config->item('theme') != '')    
          $app_my_config_data.= "\$config['theme'] = '".$this->config->item('theme')."';\n";   

        if($this->config->item('display_landing_page') != '')  
          $app_my_config_data.= "\$config['display_landing_page'] = '".$this->config->item('display_landing_page')."';\n"; 

        $app_my_config_data.= "\$config['auto_reply_delay_time'] = ".$this->config->item('auto_reply_delay_time').";\n";     
        $app_my_config_data.= "\$config['auto_reply_campaign_live_duration'] = ".$this->config->item('auto_reply_campaign_live_duration').";\n";     
        file_put_contents(APPPATH.'config/my_config.php', $app_my_config_data, LOCK_EX);  //writting  application/config/my_config

       $lines="UPDATE `version` SET `current`='0';
        INSERT INTO version(version, current, date) VALUES ('4.2.1','1',CURRENT_TIMESTAMP) ON DUPLICATE KEY UPDATE version='4.2.1',current='1',date=CURRENT_TIMESTAMP";


       $lines=explode(";", $lines);
        $count=0;
        foreach ($lines as $line) 
        {
            $count++;      
            $this->db->query($line);
        }
       echo $count." queries executed. ";

      // writting client js

      echo $this->config->item('product_short_name')." has been updated successfully to version 4.2.1";
    }

    public function v_4_1to4_2()
    {
      // writing application/config/my_config
        $app_my_config_data = "<?php ";
        $app_my_config_data.= "\n\$config['default_page_url'] = '".$this->config->item('default_page_url')."';\n";
        $app_my_config_data.= "\$config['product_version'] = '4.2';\n";
        $app_my_config_data.= "\$config['institute_address1'] = '".$this->config->item('institute_address1')."';\n";
        $app_my_config_data.= "\$config['institute_address2'] = '".$this->config->item('institute_address2')."';\n";
        $app_my_config_data.= "\$config['institute_email'] = '".$this->config->item('institute_email')."';\n";
        $app_my_config_data.= "\$config['institute_mobile'] = '".$this->config->item('institute_mobile')."';\n\n";
        $app_my_config_data.= "\$config['slogan'] = '".$this->config->item('slogan')."';\n";
        $app_my_config_data.= "\$config['product_name'] = '".$this->config->item('product_name')."';\n";
        $app_my_config_data.= "\$config['product_short_name'] = '".$this->config->item('product_short_name')."';\n";
        $app_my_config_data.= "\$config['developed_by'] = '".$this->config->item('developed_by')."';\n";
        $app_my_config_data.= "\$config['developed_by_href'] = '".$this->config->item('developed_by_href')."';\n";
        $app_my_config_data.= "\$config['developed_by_title'] = '".$this->config->item('developed_by_title')."';\n";
        $app_my_config_data.= "\$config['developed_by_prefix'] = '".$this->config->item('developed_by_prefix')."' ;\n";
        $app_my_config_data.= "\$config['support_email'] = '".$this->config->item('support_email')."' ;\n";
        $app_my_config_data.= "\$config['support_mobile'] = '".$this->config->item('support_mobile')."' ;\n";                
        $app_my_config_data.= "\$config['time_zone'] = '".$this->config->item('time_zone')."';\n";              
        $app_my_config_data.= "\$config['language'] = '".$this->config->item('language')."';\n";
        $app_my_config_data.= "\$config['sess_use_database'] = '".$this->config->item('sess_use_database')."';\n";
        $app_my_config_data.= "\$config['sess_table_name'] = '".$this->config->item('sess_table_name')."';\n";  

        if($this->config->item('number_of_message_to_be_sent_in_try') != '')
          $app_my_config_data.= "\$config['number_of_message_to_be_sent_in_try'] = ".$this->config->item('number_of_message_to_be_sent_in_try').";\n";   
        if($this->config->item('update_report_after_time') != '')  
          $app_my_config_data.= "\$config['update_report_after_time'] = ".$this->config->item('update_report_after_time').";\n";  

        if($this->config->item('theme') != '')    
          $app_my_config_data.= "\$config['theme'] = '".$this->config->item('theme')."';\n";   

        if($this->config->item('display_landing_page') != '')  
          $app_my_config_data.= "\$config['display_landing_page'] = '".$this->config->item('display_landing_page')."';\n"; 

        $app_my_config_data.= "\$config['auto_reply_delay_time'] = 10;\n";     
        $app_my_config_data.= "\$config['auto_reply_campaign_live_duration'] = 50;\n";     
        file_put_contents(APPPATH.'config/my_config.php', $app_my_config_data, LOCK_EX);  //writting  application/config/my_config

       $lines="UPDATE `version` SET `current`='0';
        INSERT INTO version(version, current, date) VALUES ('4.2','1',CURRENT_TIMESTAMP) ON DUPLICATE KEY UPDATE version='4.2',current='1',date=CURRENT_TIMESTAMP;
        ALTER TABLE `fb_msg_manager` CHANGE `last_update_time` `last_update_time` VARCHAR(100) NOT NULL COMMENT 'this time in +00 UTC format, We need to convert it to the user time zone'";


       $lines=explode(";", $lines);
        $count=0;
        foreach ($lines as $line) 
        {
            $count++;      
            $this->db->query($line);
        }
       echo $count." queries executed. ";

      // writting client js

      echo $this->config->item('product_short_name')." has been updated successfully to version 4.2";
    }


    public function v4_0_1to4_1()
    {
      // writing application/config/my_config
        $app_my_config_data = "<?php ";
        $app_my_config_data.= "\n\$config['default_page_url'] = '".$this->config->item('default_page_url')."';\n";
        $app_my_config_data.= "\$config['product_version'] = '4.1';\n";
        $app_my_config_data.= "\$config['institute_address1'] = '".$this->config->item('institute_address1')."';\n";
        $app_my_config_data.= "\$config['institute_address2'] = '".$this->config->item('institute_address2')."';\n";
        $app_my_config_data.= "\$config['institute_email'] = '".$this->config->item('institute_email')."';\n";
        $app_my_config_data.= "\$config['institute_mobile'] = '".$this->config->item('institute_mobile')."';\n\n";
        $app_my_config_data.= "\$config['slogan'] = '".$this->config->item('slogan')."';\n";
        $app_my_config_data.= "\$config['product_name'] = '".$this->config->item('product_name')."';\n";
        $app_my_config_data.= "\$config['product_short_name'] = '".$this->config->item('product_short_name')."';\n";
        $app_my_config_data.= "\$config['developed_by'] = '".$this->config->item('developed_by')."';\n";
        $app_my_config_data.= "\$config['developed_by_href'] = '".$this->config->item('developed_by_href')."';\n";
        $app_my_config_data.= "\$config['developed_by_title'] = '".$this->config->item('developed_by_title')."';\n";
        $app_my_config_data.= "\$config['developed_by_prefix'] = '".$this->config->item('developed_by_prefix')."' ;\n";
        $app_my_config_data.= "\$config['support_email'] = '".$this->config->item('support_email')."' ;\n";
        $app_my_config_data.= "\$config['support_mobile'] = '".$this->config->item('support_mobile')."' ;\n";                
        $app_my_config_data.= "\$config['time_zone'] = '".$this->config->item('time_zone')."';\n";              
        $app_my_config_data.= "\$config['language'] = '".$this->config->item('language')."';\n";
        $app_my_config_data.= "\$config['sess_use_database'] = '".$this->config->item('sess_use_database')."';\n";
        $app_my_config_data.= "\$config['sess_table_name'] = '".$this->config->item('sess_table_name')."';\n";  

        if($this->config->item('number_of_message_to_be_sent_in_try') != '')
          $app_my_config_data.= "\$config['number_of_message_to_be_sent_in_try'] = ".$this->config->item('number_of_message_to_be_sent_in_try').";\n";   
        if($this->config->item('update_report_after_time') != '')  
          $app_my_config_data.= "\$config['update_report_after_time'] = ".$this->config->item('update_report_after_time').";\n";  

        if($this->config->item('theme') != '')    
          $app_my_config_data.= "\$config['theme'] = '".$this->config->item('theme')."';\n";   

        if($this->config->item('display_landing_page') != '')  
          $app_my_config_data.= "\$config['display_landing_page'] = '".$this->config->item('display_landing_page')."';\n"; 

        $app_my_config_data.= "\$config['auto_reply_delay_time'] = 10;\n";     
        $app_my_config_data.= "\$config['auto_reply_campaign_live_duration'] = 50;\n";     
        file_put_contents(APPPATH.'config/my_config.php', $app_my_config_data, LOCK_EX);  //writting  application/config/my_config


        $lines="UPDATE `version` SET `current`='0';
        INSERT INTO version(version, current, date) VALUES ('4.1','1',CURRENT_TIMESTAMP) ON DUPLICATE KEY UPDATE version='4.1',current='1',date=CURRENT_TIMESTAMP;
        ALTER TABLE `facebook_ex_conversation_campaign` CHANGE `posting_status` `posting_status` ENUM('0','1','2','3') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL";
        $lines=explode(";", $lines);
        $count=0;
        foreach ($lines as $line) 
        {
            $count++;      
            $this->db->query($line);
        }
       echo $count." queries executed. ";


        echo $this->config->item('product_short_name')." has been updated successfully to v4.1";
    }

    public function v_4_0to4_0_1()
    {
      // writting client js
      $client_js_content=file_get_contents('js/my_chat_custom.js');
      $client_js_content_new=str_replace("base_url_replace/", site_url(), $client_js_content);
      file_put_contents('js/my_chat_custom.js', $client_js_content_new, LOCK_EX);

       $lines="UPDATE `version` SET `current`='0';
        INSERT INTO version(version, current, date) VALUES ('4.0.1','1',CURRENT_TIMESTAMP) ON DUPLICATE KEY UPDATE version='4.0.1',current='1',date=CURRENT_TIMESTAMP";
       $lines=explode(";", $lines);
        $count=0;
        foreach ($lines as $line) 
        {
            $count++;      
            $this->db->query($line);
        }
       echo $count." queries executed. ";

      // writting client js

      echo $this->config->item('product_short_name')." has been updated successfully to version 4.0.1";
    }

    public function v_3_2tov4_0()
    {
        $lines="ALTER TABLE `facebook_ex_autoreply` ADD `is_delete_offensive` ENUM('hide', 'delete') NOT NULL AFTER `error_message`, ADD `offensive_words` LONGTEXT NOT NULL AFTER `is_delete_offensive`, ADD `private_message_offensive_words` LONGTEXT NOT NULL AFTER `offensive_words`;
        ALTER TABLE `facebook_ex_autoreply` ADD `hide_comment_after_comment_reply` ENUM('no', 'yes') NOT NULL AFTER `error_message`;
        ALTER TABLE `facebook_ex_autoreply` ADD `hidden_comment_count` INT(11) NOT NULL AFTER `private_message_offensive_words`, ADD `deleted_comment_count` INT(11) NOT NULL AFTER `hidden_comment_count`;
        ALTER TABLE `facebook_ex_autoreply` ADD `auto_comment_reply_count` INT(11) NOT NULL AFTER `deleted_comment_count`;

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
        INSERT INTO `version` (`id`, `version`, `current`, `date`) VALUES
        (1, 4, '1', '2017-10-20 00:00:00');
        ALTER TABLE `facebook_rx_fb_user_info` ADD `need_to_delete` ENUM('0','1') NOT NULL AFTER `deleted`";

        $lines=explode(";", $lines);
        $count=0;
        foreach ($lines as $line) 
        {
            $count++;      
            $this->db->query($line);
        }

        // writing application/config/my_config
        // $app_my_config_data = "<?php ";
        // $app_my_config_data.= "\n\$config['default_page_url'] = '".$this->config->item('default_page_url')."';\n";
        // $app_my_config_data.= "\$config['product_version'] = '".$this->config->item('product_version')."';\n\n";
        // $app_my_config_data.= "\$config['institute_address1'] = '".$this->config->item('institute_address1')."';\n";
        // $app_my_config_data.= "\$config['institute_address2'] = '".$this->config->item('institute_address2')."';\n";
        // $app_my_config_data.= "\$config['institute_email'] = '".$this->config->item('institute_email')."';\n";
        // $app_my_config_data.= "\$config['institute_mobile'] = '".$this->config->item('institute_mobile')."';\n\n";
        // $app_my_config_data.= "\$config['slogan'] = '".$this->config->item('slogan')."';\n";
        // $app_my_config_data.= "\$config['product_name'] = '".$this->config->item('product_name')."';\n";
        // $app_my_config_data.= "\$config['product_short_name'] = '".$this->config->item('product_short_name')."';\n";
        // $app_my_config_data.= "\$config['developed_by'] = '".$this->config->item('developed_by')."';\n";
        // $app_my_config_data.= "\$config['developed_by_href'] = '".$this->config->item('developed_by_href')."';\n";
        // $app_my_config_data.= "\$config['developed_by_title'] = '".$this->config->item('developed_by_title')."';\n";
        // $app_my_config_data.= "\$config['developed_by_prefix'] = '".$this->config->item('developed_by_prefix')."' ;\n";
        // $app_my_config_data.= "\$config['support_email'] = '".$this->config->item('support_email')."' ;\n";
        // $app_my_config_data.= "\$config['support_mobile'] = '".$this->config->item('support_mobile')."' ;\n";                
        // $app_my_config_data.= "\$config['time_zone'] = '".$this->config->item('time_zone')."';\n";              
        // $app_my_config_data.= "\$config['language'] = '".$this->config->item('language')."';\n";
        // $app_my_config_data.= "\$config['sess_use_database'] = FALSE;\n";
        // $app_my_config_data.= "\$config['sess_table_name'] = 'ci_sessions';\n";     
        // file_put_contents(APPPATH.'config/my_config.php', $app_my_config_data, LOCK_EX);  //writting  application/config/my_config


        echo $this->config->item('product_short_name')." has been updated successfully to v4.0 , ".$count." queries executed.";

        
    }

    public function v_3_0to3_1()
    {
        $lines="CREATE TABLE IF NOT EXISTS `facebook_ex_conversation_campaign_send` (
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
        ALTER TABLE `facebook_ex_conversation_campaign` ADD `is_try_again` ENUM('0','1') NOT NULL DEFAULT '1' AFTER `posting_status`;
        ALTER TABLE `facebook_ex_conversation_campaign` ADD `last_try_error_count` INT NOT NULL AFTER `is_try_again`";
       
        // Loop through each line

        $lines=explode(";", $lines);
        $count=0;
        foreach ($lines as $line) 
        {
            $count++;      
            $this->db->query($line);
        }
        echo $this->config->item('product_short_name')." has been updated successfully to v3.1 , ".$count." queries executed.";
    }


    public function v_2_2to2_3()
    {
        $lines="ALTER TABLE `payment_config` CHANGE `currency` `currency` ENUM('USD','AUD','BRL','CAD','CZK','DKK','EUR','HKD','HUF','ILS','JPY','MYR','MXN','TWD','NZD','NOK','PHP','PLN','GBP','RUB','SGD','SEK','CHF','VND') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
        ALTER TABLE `facebook_ex_autoreply` CHANGE `auto_private_reply_status` `auto_private_reply_status` ENUM('0','1','2') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0';
        ALTER TABLE `facebook_ex_autoreply` ADD `error_message` TEXT NOT NULL AFTER `last_reply_time`;
        UPDATE `facebook_ex_autoreply` SET `auto_private_reply_status`='0'";
       
        // Loop through each line

        $lines=explode(";", $lines);
        $count=0;
        foreach ($lines as $line) 
        {
            $count++;      
            $this->db->query($line);
        }
        echo $this->config->item('product_short_name')." has been updated successfully.".$count." queries executed.";
    }



    function v_1_1to2_0()
    {
        // writting client js
        $client_js_content=file_get_contents('js/my_chat_custom.js');
        $client_js_content_new=str_replace("base_url_replace/", site_url(), $client_js_content);
        file_put_contents('js/my_chat_custom.js', $client_js_content_new, LOCK_EX);
        // writting client js
                
        $lines="ALTER TABLE `facebook_ex_autoreply` ADD `comment_reply_enabled` ENUM('no','yes') NOT NULL AFTER `reply_type`;
                ALTER TABLE `facebook_ex_autoreply` ADD `multiple_reply` ENUM('no','yes') NOT NULL AFTER `reply_type`;
                ALTER TABLE `facebook_ex_autoreply` ADD `auto_like_comment` ENUM('no','yes') NOT NULL AFTER `reply_type`;

                ALTER TABLE `facebook_rx_conversion_user_list` ADD `contact_group_id` VARCHAR(255) NOT NULL AFTER `page_id`;
                ALTER TABLE `facebook_rx_conversion_user_list` DROP INDEX `user_id`, ADD INDEX `user_id` (`contact_group_id`) USING BTREE;

                ALTER TABLE `facebook_ex_conversation_campaign` CHANGE `campaign_type` `campaign_type` ENUM('page-wise','lead-wise','group-wise') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'page-wise';
                ALTER TABLE `facebook_ex_conversation_campaign` ADD `group_ids` TEXT NOT NULL COMMENT 'comma seperated group ids if group wise' AFTER `user_id`;

                CREATE TABLE IF NOT EXISTS `facebook_rx_conversion_contact_group` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `group_name` varchar(255) NOT NULL,
                  `user_id` int(11) NOT NULL,
                  `deleted` enum('0','1') DEFAULT '0',
                  PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8";


       
        // Loop through each line

        $lines=explode(";", $lines);
        $count=0;
        foreach ($lines as $line) 
        {
            $count++;      
            $this->db->query($line);
        }
        echo $this->config->item('product_short_name')." has been updated to v2.0 successfully.".$count." queries executed.";
    }


    public function auto_reply_fix()
    {
        $lines="UPDATE `facebook_ex_autoreply` SET `auto_private_reply_status`='0'";
       
        // Loop through each line

        $lines=explode(";", $lines);
        $count=0;
        foreach ($lines as $line) 
        {
            $count++;      
            $this->db->query($line);
        }
        echo $this->config->item('product_short_name')." has been updated successfully.".$count." queries executed.";
    }




    function delete_update()
    {
        unlink(APPPATH."controllers/update.php");
    }
 


}
