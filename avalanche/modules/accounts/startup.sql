CREATE TABLE `avalanche_loggedinusers` (
  `id` mediumint(9) NOT NULL auto_increment,
  `ip` text NOT NULL,
  `user_id` mediumint(9) NOT NULL default '0',
  `last_active` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

CREATE TABLE `avalanche_preferences` (
  `id` bigint(20) NOT NULL auto_increment,
  `user_id` bigint(20) NOT NULL default '0',
  `start_page` text NOT NULL,
  `preferred_contact` int(11) NOT NULL default '3',
  PRIMARY KEY  (`id`),
  KEY `user_id` (`user_id`),
  KEY `user_id_2` (`user_id`)
) TYPE=MyISAM AUTO_INCREMENT=2 ;

CREATE TABLE `avalanche_notifier_notifications` (
  `id` bigint(20) NOT NULL auto_increment,
  `user_id` bigint(20) NOT NULL default '0',
  `contact_by` smallint(6) NOT NULL default '0',
  `item` tinyint(4) NOT NULL default '0',
  `action` tinyint(4) NOT NULL default '0',
  `calendars` text NOT NULL,
  `all_calendars` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=27 ;

CREATE TABLE `avalanche_modules` (
  `id` mediumint(9) NOT NULL auto_increment,
  `folder` text NOT NULL,
  `version` text NOT NULL,
  `active` tinyint(4) NOT NULL default '0',
  `expiresOn` datetime NOT NULL default '0000-00-00 00:00:00',
  `trialTime` mediumint(9) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=12 ;

INSERT INTO `avalanche_modules` VALUES (1, 'strongcal', '1.0.0', 1, '0000-00-00 00:00:00', 0);
INSERT INTO `avalanche_modules` VALUES (2, 'os', '1.0.0', 1, '0000-00-00 00:00:00', 0);
INSERT INTO `avalanche_modules` VALUES (3, 'bootstrap', '1.0.0', 1, '0000-00-00 00:00:00', 0);
INSERT INTO `avalanche_modules` VALUES (4, 'fileloader', '1.0.0', 1, '0000-00-00 00:00:00', 0);
INSERT INTO `avalanche_modules` VALUES (5, 'taskman', '1.0.0', 1, '0000-00-00 00:00:00', 0);
INSERT INTO `avalanche_modules` VALUES (6, 'notifier', '1.0.0', 1, '0000-00-00 00:00:00', 0);
INSERT INTO `avalanche_modules` VALUES (7, 'reminder', '1.0.0', 1, '0000-00-00 00:00:00', 0);

CREATE TABLE `avalanche_skins` (
  `id` mediumint(9) NOT NULL auto_increment,
  `folder` text NOT NULL,
  `version` text NOT NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=20 ;

INSERT INTO `avalanche_skins` VALUES (1, 'control', '1.0.0');
INSERT INTO `avalanche_skins` VALUES (2, 'installer', '1.0.0');
INSERT INTO `avalanche_skins` VALUES (3, 'default', '1.0.0');
INSERT INTO `avalanche_skins` VALUES (4, 'buffer', '1.0.0');

CREATE TABLE `avalanche_strongcal_calendars` (
  `id` mediumint(9) NOT NULL auto_increment,
  `name` text NOT NULL,
  `description` text NOT NULL,
  `color` text NOT NULL,
  `author` mediumint(9) NOT NULL default '0',
  `public` tinyint(4) NOT NULL default '0',
  `added_on` DATETIME NOT NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

CREATE TABLE `avalanche_strongcal_linked_calendars` (
  `id` mediumint(9) NOT NULL auto_increment,
  `ext_id` mediumint(9) NOT NULL default '0',
  `url` mediumtext NOT NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=2 ;

CREATE TABLE `avalanche_strongcal_permissions` (
  `id` mediumint(9) NOT NULL auto_increment,
  `usergroup` mediumint(9) NOT NULL default '0',
  `change_permissions` tinyint(4) NOT NULL default '0',
  `add_calendar` tinyint(4) NOT NULL default '0',
  `delete_calendar` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=15 ;

INSERT INTO `avalanche_strongcal_permissions` VALUES (1, 1, 1, -1, 1);
INSERT INTO `avalanche_strongcal_permissions` VALUES (2, 2, 0, 0, 0);
INSERT INTO `avalanche_strongcal_permissions` VALUES (3, 3, 1, -1, 1);
INSERT INTO `avalanche_strongcal_permissions` VALUES (4, 4, 0, 0, 0);

CREATE TABLE `avalanche_strongcal_preferences` (
  `id` bigint(20) NOT NULL auto_increment,
  `user_id` bigint(20) NOT NULL default '0',
  `highlight` text NOT NULL,
  `timezone` text NOT NULL,
  `selected_calendars` tinytext NOT NULL,
  `day_start` time NOT NULL default '06:00:00',
  `day_end` time NOT NULL default '22:00:00',
  PRIMARY KEY  (`id`),
  KEY `user_id` (`user_id`),
  KEY `user_id_2` (`user_id`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;
INSERT INTO `avalanche_strongcal_preferences` ( `id` , `user_id` , `highlight` , `timezone` , `selected_calendars` , `day_start` , `day_end` ) VALUES ('1', '-1', 'month', '-6', '', '06:00:00', '22:00:00');

CREATE TABLE `avalanche_strongcal_varlist` (
  `id` mediumint(9) NOT NULL auto_increment,
  `var` text NOT NULL,
  `val` text NOT NULL,
  `dflt` text NOT NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=7 ;

INSERT INTO `avalanche_strongcal_varlist` VALUES (1, 'welcome_title', 'Welcome to Aurora', 'Welcome to Aurora');
INSERT INTO `avalanche_strongcal_varlist` VALUES (2, 'welcome_body', 'It looks like this is your first time using Aurora. We''re glad you''re here. You can browse around the public calendars, or <a href=''../login.php?show_pref_link=1'' target=''main_frame''>log in</a> to manage your personal calendars.\r\n<br><br>\r\nIf you would like some help getting started, we''ve set up a small <a href=''tour.php''>tour</a> of Aurora to help you get aquainted.', 'It looks like this is your first time using Aurora. We''re glad you''re here. You can browse around the public calendars, or <a href=''../login.php?show_pref_link=1'' target=''main_frame''>log in</a> to manage your personal calendars.\r\n<br><br>\r\nIf you would like some help getting started, we''ve set up a small <a href=''tour.php''>tour</a> of Aurora to help you get aquainted.');
INSERT INTO `avalanche_strongcal_varlist` VALUES (3, 'logo', 'none.gif', 'aurora.gif');
INSERT INTO `avalanche_strongcal_varlist` VALUES (4, 'ip_filter', '', '');
INSERT INTO `avalanche_strongcal_varlist` VALUES (5, 'ip_ban', '', '');

CREATE TABLE `avalanche_taskman_categories` (
  `id` mediumint(9) NOT NULL auto_increment,
  `cal_id` mediumint(9) NOT NULL default '0',
  `name` mediumtext NOT NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=27 ;

CREATE TABLE `avalanche_taskman_catlink` (
  `id` mediumint(9) NOT NULL auto_increment,
  `task_id` mediumint(9) NOT NULL default '0',
  `category_id` mediumint(9) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=10 ;


CREATE TABLE `avalanche_taskman_status_history` (
  `id` mediumint(9) NOT NULL auto_increment,
  `task_id` mediumint(9) NOT NULL default '0',
  `user_id` mediumint(9) NOT NULL default '0',
  `status` tinyint(4) NOT NULL default '0',
  `stamp` datetime NOT NULL default '0000-00-00 00:00:00',
  `to_user_id` mediumint(9) NOT NULL default '0',
  `comment` mediumtext NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `task_id` (`task_id`,`user_id`)
) TYPE=MyISAM AUTO_INCREMENT=578 ;

CREATE TABLE `avalanche_taskman_tasks` (
  `id` mediumint(9) NOT NULL auto_increment,
  `created_on` datetime NOT NULL default '0000-00-00 00:00:00',
  `author` mediumint(9) NOT NULL default '0',
  `cal_id` mediumint(9) NOT NULL default '0',
  `completed` datetime NOT NULL default '0000-00-00 00:00:00',
  `description` mediumtext NOT NULL,
  `due` datetime NOT NULL default '0000-00-00 00:00:00',
  `priority` smallint(6) NOT NULL default '0',
  `summary` tinytext NOT NULL,
  `status` tinyint(4) NOT NULL default '0',
  `delegated_to` mediumint(9) NOT NULL default '0',
  `assigned_to` mediumint(9) NOT NULL default '0',
  `modified_by` mediumint(9) NOT NULL default '0',
  `cancelled` datetime NOT NULL default '0000-00-00 00:00:00',
  `modified_on` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`),
  KEY `author` (`author`),
  KEY `cal_id` (`cal_id`),
  KEY `author_2` (`author`)
) TYPE=MyISAM AUTO_INCREMENT=237 ;
        

CREATE TABLE `avalanche_user_link` (
  `id` mediumint(9) NOT NULL auto_increment,
  `user_id` mediumint(9) NOT NULL default '0',
  `group_id` mediumint(9) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=150 ;

INSERT INTO `avalanche_user_link` VALUES (1, 1, 1);
INSERT INTO `avalanche_user_link` VALUES (2, 2, 2);
INSERT INTO `avalanche_user_link` VALUES (3, 1, 3);
INSERT INTO `avalanche_user_link` VALUES (4, 1, 2);
INSERT INTO `avalanche_user_link` VALUES (5, 2, 4);

CREATE TABLE `avalanche_usergroups` (
  `id` mediumint(9) NOT NULL auto_increment,
  `author` mediumint(9) NOT NULL default '0',
  `type` text NOT NULL,
  `name` text NOT NULL,
  `description` text NOT NULL,
  `keywords` text NOT NULL,
  `install_mod` tinyint(4) NOT NULL default '0',
  `uninstall_mod` tinyint(4) NOT NULL default '0',
  `install_skin` tinyint(4) NOT NULL default '0',
  `uninstall_skin` tinyint(4) NOT NULL default '0',
  `add_user` tinyint(4) NOT NULL default '0',
  `del_user` tinyint(4) NOT NULL default '0',
  `rename_user` tinyint(4) NOT NULL default '0',
  `add_usergroup` tinyint(4) NOT NULL default '0',
  `del_usergroup` tinyint(4) NOT NULL default '0',
  `rename_usergroup` tinyint(4) NOT NULL default '0',
  `change_default_skin` tinyint(4) NOT NULL default '0',
  `change_permissions` tinyint(4) NOT NULL default '0',
  `link_user` tinyint(4) NOT NULL default '0',
  `unlink_user` tinyint(4) NOT NULL default '0',
  `change_default_usergroup` tinyint(4) NOT NULL default '0',
  `view_cp` tinyint(4) NOT NULL default '0',
  `change_password` tinyint(4) NOT NULL default '0',
  `view_password` tinyint(4) NOT NULL default '0',
  `change_name` tinyint(4) NOT NULL default '0',
  `view_name` tinyint(4) NOT NULL default '0',
  `disable_user` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `author` (`author`)
) TYPE=MyISAM AUTO_INCREMENT=14 ;

INSERT INTO `avalanche_usergroups` VALUES (1, -1, 'SYSTEM', 'admin', '', '', 1, 1, 1, 1,  1, 1, 1, 1, 1, 1, 1, 1, 1, 1,  1, 1, 1, 1, 1, 1, 1);
INSERT INTO `avalanche_usergroups` VALUES (2, -1, 'SYSTEM', 'guest', '', '', 0, 0, 0, 0,  0, 0, 0, 0, 0, 0, 0, 0, 0, 0,  0, 0, 0, 0, 0, 1, 0);
INSERT INTO `avalanche_usergroups` VALUES (3, -1, 'PUBLIC', 'All Users', '', '', 0, 0, 0, 0,  0, 0, 0, 0, 0, 0, 0, 0, 0, 0,  0, 0, 0, 0, 0, 0, 0);
INSERT INTO `avalanche_usergroups` VALUES (4, -1, 'PUBLIC', 'Guests', '', '', 0, 0, 0, 0,  0, 0, 0, 0, 0, 0, 0, 0, 0, 0,  0, 0, 0, 0, 0, 1, 0);

CREATE TABLE `avalanche_users` (
  `id` mediumint(9) NOT NULL auto_increment,
  `title` text NOT NULL,
  `first` text NOT NULL,
  `middle` text NOT NULL,
  `last` text NOT NULL,
  `username` text NOT NULL,
  `password` text NOT NULL,
  `last_ip` tinytext NOT NULL,
  `last_login` datetime NOT NULL default '0000-00-00 00:00:00',
  `last_logout` datetime NOT NULL default '0000-00-00 00:00:00',
  `email` mediumtext NOT NULL,
  `sms` mediumtext NOT NULL,
  `disabled` tinyint(4) NOT NULL default '0',
  `avatar` blob NOT NULL,
  `need_new_pass` tinyint(4) NOT NULL default '0',
  `bio` text NOT NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=22 ;        

INSERT INTO `avalanche_users` VALUES (1, '', '', '', '', '%user%', '%pass%', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '%email%', '', 0,'',0,'');
INSERT INTO `avalanche_users` VALUES (2, '', '', '', '', 'guest', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '', '', 0,'',0,'');

CREATE TABLE `avalanche_varlist` (
  `id` mediumint(9) NOT NULL auto_increment,
  `var` text NOT NULL,
  `val` text NOT NULL,
  `dflt` text NOT NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=8 ;

INSERT INTO `avalanche_varlist` VALUES (1, 'SKIN', 'installer', 'installer');
INSERT INTO `avalanche_varlist` VALUES (2, 'USERGROUP', '2', '2');
INSERT INTO `avalanche_varlist` VALUES (3, 'USER', '2', '2');
INSERT INTO `avalanche_varlist` VALUES (4, 'ACTIVITY', '', '');
INSERT INTO `avalanche_varlist` VALUES (5, 'ACTIVE_OFFSET', '', '');
INSERT INTO `avalanche_varlist` VALUES (6, 'ALLUSERS', '3', '3');
INSERT INTO `avalanche_varlist` VALUES (7, 'ORGANIZATION', '%title%', 'Inversion Designs');
INSERT INTO `avalanche_varlist` VALUES (8, 'GUESTGROUP', '4', '4');





CREATE TABLE `avalanche_reminder_outbox` (
  `id` mediumint(9) NOT NULL auto_increment,
  `reminder_id` mediumint(9) NOT NULL default '0',
  `user_id` mediumint(9) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=17 ;


CREATE TABLE `avalanche_reminder_relation_event` (
  `id` mediumint(9) NOT NULL auto_increment,
  `reminder_id` mediumint(9) NOT NULL default '0',
  `cal_id` mediumint(9) NOT NULL default '0',
  `event_id` mediumint(9) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=13 ;


CREATE TABLE `avalanche_reminder_relation_task` (
  `id` mediumint(9) NOT NULL auto_increment,
  `reminder_id` mediumint(9) NOT NULL default '0',
  `task_id` mediumint(9) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;


CREATE TABLE `avalanche_reminder_reminders` (
  `id` mediumint(9) NOT NULL auto_increment,
  `author` mediumint(9) NOT NULL default '0',
  `type` tinyint(4) NOT NULL default '0',
  `subject` text NOT NULL,
  `body` text NOT NULL,
  `year` mediumint(9) NOT NULL default '0',
  `month` tinyint(4) NOT NULL default '0',
  `day` tinyint(4) NOT NULL default '0',
  `hour` tinyint(4) NOT NULL default '0',
  `minute` tinyint(4) NOT NULL default '0',
  `second` tinyint(4) NOT NULL default '0',
  `send_on` datetime NOT NULL default '0000-00-00 00:00:00',
  `sent_on` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=18 ;

CREATE TABLE `avalanche_strongcal_attendees` (
  `id` bigint(20) NOT NULL auto_increment,
  `cal_id` mediumint(9) NOT NULL default '0',
  `event_id` mediumint(9) NOT NULL default '0',
  `user_id` mediumint(9) NOT NULL default '0',
  `confirm` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=1;


