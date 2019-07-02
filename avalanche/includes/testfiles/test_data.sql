# phpMyAdmin SQL Dump
# version 2.5.2-rc2
# http://www.phpmyadmin.net
#
# Host: 66.135.32.134:3306
# Generation Time: Jun 09, 2004 at 08:11 PM
# Server version: 4.0.13
# PHP Version: 5.0.0RC2
#
# Database : `inversion_data`
#

# --------------------------------------------------------

#
# Table structure for table `awulf_calendar_test_loggedinusers`
#
# Creation: Jun 03, 2004 at 09:19 PM
# Last update: Jun 09, 2004 at 08:08 PM
#

DROP TABLE IF EXISTS `awulf_calendar_test_loggedinusers`;
CREATE TABLE `awulf_calendar_test_loggedinusers` (
  `id` mediumint(9) NOT NULL auto_increment,
  `ip` text NOT NULL,
  `user_id` mediumint(9) NOT NULL default '0',
  `last_active` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=46230 ;

#
# Dumping data for table `awulf_calendar_test_loggedinusers`
#


# --------------------------------------------------------

#
# Table structure for table `awulf_calendar_test_modules`
#
# Creation: Jun 03, 2004 at 09:19 PM
# Last update: Jun 03, 2004 at 09:19 PM
#

DROP TABLE IF EXISTS `awulf_calendar_test_modules`;
CREATE TABLE `awulf_calendar_test_modules` (
  `id` mediumint(9) NOT NULL auto_increment,
  `folder` text NOT NULL,
  `version` text NOT NULL,
  `active` tinyint(4) NOT NULL default '0',
  `expiresOn` datetime NOT NULL default '0000-00-00 00:00:00',
  `trialTime` mediumint(9) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=9 ;

#
# Dumping data for table `awulf_calendar_test_modules`
#

INSERT INTO `awulf_calendar_test_modules` VALUES (1, 'moduleManager', '1.0.0', 1, '0000-00-00 00:00:00', 0);
INSERT INTO `awulf_calendar_test_modules` VALUES (2, 'strongcal', '1.0.0', 1, '0000-00-00 00:00:00', 0);
INSERT INTO `awulf_calendar_test_modules` VALUES (3, 'groupManager', '1.0.0', 1, '2004-01-05 05:07:40', 0);
INSERT INTO `awulf_calendar_test_modules` VALUES (4, 'google', '1.0.0', 1, '2004-01-07 09:01:34', 0);
INSERT INTO `awulf_calendar_test_modules` VALUES (5, 'paypal', '1.0.0', 1, '2004-01-13 21:37:13', 0);
INSERT INTO `awulf_calendar_test_modules` VALUES (6, 'menuMaker', '1.0.0', 1, '0000-00-00 00:00:00', 0);
INSERT INTO `awulf_calendar_test_modules` VALUES (10, 'os', '1.0.0', 1, '0000-00-00 00:00:00', 0);
INSERT INTO `awulf_calendar_test_modules` VALUES (8, 'bootstrap', '1.0.0', 1, '0000-00-00 00:00:00', 0);
INSERT INTO `awulf_calendar_test_modules` VALUES (9, 'fileloader', '1.0.0', 1, '0000-00-00 00:00:00', 0);
INSERT INTO `awulf_calendar_test_modules` VALUES (7, 'taskman', '1.0.0', 1, '0000-00-00 00:00:00', 0);

# --------------------------------------------------------

#
# Table structure for table `awulf_calendar_test_skins`
#
# Creation: Jun 03, 2004 at 09:19 PM
# Last update: Jun 03, 2004 at 09:19 PM
#

DROP TABLE IF EXISTS `awulf_calendar_test_skins`;
CREATE TABLE `awulf_calendar_test_skins` (
  `id` mediumint(9) NOT NULL auto_increment,
  `folder` text NOT NULL,
  `version` text NOT NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=18 ;

#
# Dumping data for table `awulf_calendar_test_skins`
#

INSERT INTO `awulf_calendar_test_skins` VALUES (7, 'control', '1.0.0');
INSERT INTO `awulf_calendar_test_skins` VALUES (8, 'installer', '1.0.0');
INSERT INTO `awulf_calendar_test_skins` VALUES (9, 'default', '1.0.0');
INSERT INTO `awulf_calendar_test_skins` VALUES (10, 'buffer', '1.0.0');
INSERT INTO `awulf_calendar_test_skins` VALUES (11, 'glass', '1.0.0');
INSERT INTO `awulf_calendar_test_skins` VALUES (12, 'jetset', '1.0.0');
INSERT INTO `awulf_calendar_test_skins` VALUES (13, 'jetsetred', '1.0.0');
INSERT INTO `awulf_calendar_test_skins` VALUES (14, 'jetsetblue', '1.0.0');
INSERT INTO `awulf_calendar_test_skins` VALUES (15, 'yaxay', '1.0');
INSERT INTO `awulf_calendar_test_skins` VALUES (16, 'baseboard', '1.0');
INSERT INTO `awulf_calendar_test_skins` VALUES (17, 'inversion', '1.0.0');

# --------------------------------------------------------

#
# Table structure for table `awulf_calendar_test_strongcal_cal_38`
#
# Creation: Jun 09, 2004 at 08:05 PM
# Last update: Jun 09, 2004 at 08:05 PM
#

DROP TABLE IF EXISTS `awulf_calendar_test_strongcal_cal_38`;
CREATE TABLE `awulf_calendar_test_strongcal_cal_38` (
  `id` mediumint(9) NOT NULL auto_increment,
  `author` tinytext NOT NULL,
  `recur_id` mediumint(9) NOT NULL default '0',
  `added_on` datetime NOT NULL default '0000-00-00 00:00:00',
  `start_date` date NOT NULL default '0000-00-00',
  `end_date` date NOT NULL default '0000-00-00',
  `title` text NOT NULL,
  `start_time` time NOT NULL default '00:00:00',
  `end_time` time NOT NULL default '00:00:00',
  `description` text NOT NULL,
  `priority` text NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `start_time` (`start_time`),
  KEY `end_time` (`end_time`),
  KEY `start_date` (`start_date`),
  KEY `end_date` (`end_date`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

#
# Dumping data for table `awulf_calendar_test_strongcal_cal_38`
#


# --------------------------------------------------------

#
# Table structure for table `awulf_calendar_test_strongcal_cal_38_comments`
#
# Creation: Jun 03, 2004 at 09:19 PM
# Last update: Jun 03, 2004 at 09:19 PM
#

DROP TABLE IF EXISTS `awulf_calendar_test_strongcal_cal_38_comments`;
CREATE TABLE `awulf_calendar_test_strongcal_cal_38_comments` (
  `id` mediumint(9) NOT NULL auto_increment,
  `event_id` mediumint(9) NOT NULL default '0',
  `author` mediumint(9) NOT NULL default '0',
  `post_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `title` tinytext NOT NULL,
  `body` mediumtext NOT NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

#
# Dumping data for table `awulf_calendar_test_strongcal_cal_38_comments`
#


# --------------------------------------------------------

#
# Table structure for table `awulf_calendar_test_strongcal_cal_38_fields`
#
# Creation: Jun 09, 2004 at 08:05 PM
# Last update: Jun 09, 2004 at 08:05 PM
#

DROP TABLE IF EXISTS `awulf_calendar_test_strongcal_cal_38_fields`;
CREATE TABLE `awulf_calendar_test_strongcal_cal_38_fields` (
  `id` mediumint(9) NOT NULL auto_increment,
  `prompt` text NOT NULL,
  `field` text NOT NULL,
  `type` text NOT NULL,
  `value` text NOT NULL,
  `size` mediumint(9) NOT NULL default '0',
  `style` mediumint(9) NOT NULL default '0',
  `valid` tinyint(4) NOT NULL default '0',
  `form_order` tinyint(4) NOT NULL default '0',
  `user` tinyint(4) NOT NULL default '0',
  `usergroup` tinyint(4) NOT NULL default '0',
  `removeable` tinyint(4) NOT NULL default '1',
  `ics` mediumint(9) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=13 ;

#
# Dumping data for table `awulf_calendar_test_strongcal_cal_38_fields`
#

INSERT INTO `awulf_calendar_test_strongcal_cal_38_fields` VALUES (1, 'Start Date:', 'start_date', 'date', '2003-10-31', 0, 0, 0, 1, 0, 0, 0, 0);
INSERT INTO `awulf_calendar_test_strongcal_cal_38_fields` VALUES (2, 'Start Time:', 'start_time', 'time', '13:00:00', 15, 0, 0, 2, 0, 0, 0, 0);
INSERT INTO `awulf_calendar_test_strongcal_cal_38_fields` VALUES (3, 'End Date:', 'end_date', 'date', '2003-10-31', 0, 0, 0, 3, 0, 0, 0, 0);
INSERT INTO `awulf_calendar_test_strongcal_cal_38_fields` VALUES (4, 'End Time:', 'end_time', 'time', '14:00:00', 15, 0, 0, 4, 0, 0, 0, 0);
INSERT INTO `awulf_calendar_test_strongcal_cal_38_fields` VALUES (5, 'Title:', 'title', 'text', '', 10, 0, 0, 5, 0, 0, 0, 0);
INSERT INTO `awulf_calendar_test_strongcal_cal_38_fields` VALUES (6, 'Description:', 'description', 'largetext', '', 0, 0, 0, 6, 0, 0, 0, 0);
INSERT INTO `awulf_calendar_test_strongcal_cal_38_fields` VALUES (7, 'Priority:', 'priority', 'select', 'High\nHigh\n\nNormal\nNormal\n1\nLow\nLow\n', 0, 0, 0, 6, 0, 0, 0, 0);

# --------------------------------------------------------

#
# Table structure for table `awulf_calendar_test_strongcal_cal_38_recur`
#
# Creation: Jun 09, 2004 at 08:05 PM
# Last update: Jun 09, 2004 at 08:05 PM
#

DROP TABLE IF EXISTS `awulf_calendar_test_strongcal_cal_38_recur`;
CREATE TABLE `awulf_calendar_test_strongcal_cal_38_recur` (
  `id` mediumint(9) NOT NULL auto_increment,
  `start_time` time NOT NULL default '00:00:00',
  `end_time` time NOT NULL default '00:00:00',
  `start_date` date NOT NULL default '0000-00-00',
  `end_type` mediumint(9) NOT NULL default '0',
  `end_after` mediumint(9) NOT NULL default '0',
  `end_date` date NOT NULL default '0000-00-00',
  `recur_type` mediumtext NOT NULL,
  `day_count` mediumint(9) NOT NULL default '0',
  `week_count` mediumint(9) NOT NULL default '0',
  `week_days` text NOT NULL,
  `month_type` tinyint(4) NOT NULL default '0',
  `month_day` mediumint(9) NOT NULL default '0',
  `month_week` mediumint(9) NOT NULL default '0',
  `month_weekday` mediumint(9) NOT NULL default '0',
  `month_months` mediumint(9) NOT NULL default '0',
  `year_type` tinyint(4) NOT NULL default '0',
  `year_m` mediumint(9) NOT NULL default '0',
  `year_day` mediumint(9) NOT NULL default '0',
  `year_week` mediumint(9) NOT NULL default '0',
  `year_weekday` mediumint(9) NOT NULL default '0',
  `last_entry_date` date NOT NULL default '0000-00-00',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

#
# Dumping data for table `awulf_calendar_test_strongcal_cal_38_recur`
#


# --------------------------------------------------------

#
# Table structure for table `awulf_calendar_test_strongcal_cal_38_varlist`
#
# Creation: Jun 03, 2004 at 09:19 PM
# Last update: Jun 03, 2004 at 09:19 PM
#

DROP TABLE IF EXISTS `awulf_calendar_test_strongcal_cal_38_varlist`;
CREATE TABLE `awulf_calendar_test_strongcal_cal_38_varlist` (
  `id` mediumint(9) NOT NULL auto_increment,
  `var` text NOT NULL,
  `val` text NOT NULL,
  `dflt` text NOT NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

#
# Dumping data for table `awulf_calendar_test_strongcal_cal_38_varlist`
#


# --------------------------------------------------------

#
# Table structure for table `awulf_calendar_test_strongcal_calendars`
#
# Creation: Jun 03, 2004 at 09:19 PM
# Last update: Jun 09, 2004 at 08:10 PM
#

DROP TABLE IF EXISTS `awulf_calendar_test_strongcal_calendars`;
CREATE TABLE `awulf_calendar_test_strongcal_calendars` (
  `id` mediumint(9) NOT NULL auto_increment,
  `name` text NOT NULL,
  `color` text NOT NULL,
  `author` mediumint(9) NOT NULL default '0',
  `public` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=75 ;

#
# Dumping data for table `awulf_calendar_test_strongcal_calendars`
#

INSERT INTO `awulf_calendar_test_strongcal_calendars` VALUES (38, 'my new calendar', '#E49D32', 10, 0);

# --------------------------------------------------------

#
# Table structure for table `awulf_calendar_test_strongcal_linked_calendars`
#
# Creation: Jun 03, 2004 at 09:19 PM
# Last update: Jun 03, 2004 at 09:19 PM
#

DROP TABLE IF EXISTS `awulf_calendar_test_strongcal_linked_calendars`;
CREATE TABLE `awulf_calendar_test_strongcal_linked_calendars` (
  `id` mediumint(9) NOT NULL auto_increment,
  `ext_id` mediumint(9) NOT NULL default '0',
  `url` mediumtext NOT NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=2 ;

#
# Dumping data for table `awulf_calendar_test_strongcal_linked_calendars`
#


# --------------------------------------------------------

#
# Table structure for table `awulf_calendar_test_strongcal_permissions`
#
# Creation: Jun 09, 2004 at 08:10 PM
# Last update: Jun 09, 2004 at 08:10 PM
#

DROP TABLE IF EXISTS `awulf_calendar_test_strongcal_permissions`;
CREATE TABLE `awulf_calendar_test_strongcal_permissions` (
  `id` mediumint(9) NOT NULL auto_increment,
  `usergroup` mediumint(9) NOT NULL default '0',
  `change_permissions` tinyint(4) NOT NULL default '0',
  `add_calendar` tinyint(4) NOT NULL default '0',
  `delete_calendar` tinyint(4) NOT NULL default '0',
  `cal_38_entry` tinytext NOT NULL,
  `cal_38_field` tinytext NOT NULL,
  `cal_38_validation` tinytext NOT NULL,
  `cal_38_name` tinytext NOT NULL,
  `cal_38_comments` tinytext NOT NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=8 ;

#
# Dumping data for table `awulf_calendar_test_strongcal_permissions`
#

INSERT INTO `awulf_calendar_test_strongcal_permissions` VALUES (1, 1, 1, -1, 1, '', '', '', 'rw', '');
INSERT INTO `awulf_calendar_test_strongcal_permissions` VALUES (2, 2, 0, 0, 0, '', '', '', '', '');
INSERT INTO `awulf_calendar_test_strongcal_permissions` VALUES (3, 3, 1, -1, 1, '', '', '', '', '');
INSERT INTO `awulf_calendar_test_strongcal_permissions` VALUES (4, 5, 0, 0, 0, '', '', '', '', '');
INSERT INTO `awulf_calendar_test_strongcal_permissions` VALUES (5, 6, 0, 0, 0, '', '', '', '', '');
INSERT INTO `awulf_calendar_test_strongcal_permissions` VALUES (6, 7, 0, 0, 0, '', '', '', '', '');
INSERT INTO `awulf_calendar_test_strongcal_permissions` VALUES (7, 8, 0, 0, 0, '', '', '', '', '');

# --------------------------------------------------------

#
# Table structure for table `awulf_calendar_test_strongcal_preferences`
#
# Creation: Jun 09, 2004 at 08:05 PM
# Last update: Jun 09, 2004 at 08:05 PM
#

DROP TABLE IF EXISTS `awulf_calendar_test_strongcal_preferences`;
CREATE TABLE `awulf_calendar_test_strongcal_preferences` (
  `id` bigint(20) NOT NULL auto_increment,
  `user_id` bigint(20) NOT NULL default '0',
  `tooltip_off` text NOT NULL,
  `highlight_day` text NOT NULL,
  `highlight` text NOT NULL,
  `day_inc` text NOT NULL,
  `double_click` text NOT NULL,
  `show_to_validate` text NOT NULL,
  `show_validated` text NOT NULL,
  `download_type` text NOT NULL,
  `daylight_savings` text NOT NULL,
  `skin` text NOT NULL,
  `start_week_on_day` text NOT NULL,
  `timezone` text NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `user_id` (`user_id`),
  KEY `user_id_2` (`user_id`)
) TYPE=MyISAM AUTO_INCREMENT=5 ;

#
# Dumping data for table `awulf_calendar_test_strongcal_preferences`
#

INSERT INTO `awulf_calendar_test_strongcal_preferences` VALUES (4, 10, '', '', 'day', '', '', '', '', '', '', '', '', '');
INSERT INTO `awulf_calendar_test_strongcal_preferences` VALUES (3, 1, '', '', 'day', '', '', '', '', '', '', '', '', '');

# --------------------------------------------------------

#
# Table structure for table `awulf_calendar_test_strongcal_varlist`
#
# Creation: Jun 03, 2004 at 09:19 PM
# Last update: Jun 03, 2004 at 09:19 PM
#

DROP TABLE IF EXISTS `awulf_calendar_test_strongcal_varlist`;
CREATE TABLE `awulf_calendar_test_strongcal_varlist` (
  `id` mediumint(9) NOT NULL auto_increment,
  `var` text NOT NULL,
  `val` text NOT NULL,
  `dflt` text NOT NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=7 ;

#
# Dumping data for table `awulf_calendar_test_strongcal_varlist`
#

INSERT INTO `awulf_calendar_test_strongcal_varlist` VALUES (1, 'welcome_title', 'Welcome to Aurora', 'Welcome to Aurora');
INSERT INTO `awulf_calendar_test_strongcal_varlist` VALUES (2, 'welcome_body', 'It looks like this is your first time using Aurora. We\'re glad you\'re here. You can browse around the public calendars, or <a href=\'../login.php?show_pref_link=1\' target=\'main_frame\'>log in</a> to manage your personal calendars.\r\n<br><br>\r\nIf you would like some help getting started, we\'ve set up a small <a href=\'tour.php\'>tour</a> of Aurora to help you get aquainted.', 'It looks like this is your first time using Aurora. We\'re glad you\'re here. You can browse around the public calendars, or <a href=\'../login.php?show_pref_link=1\' target=\'main_frame\'>log in</a> to manage your personal calendars.\r\n<br><br>\r\nIf you would like some help getting started, we\'ve set up a small <a href=\'tour.php\'>tour</a> of Aurora to help you get aquainted.');
INSERT INTO `awulf_calendar_test_strongcal_varlist` VALUES (3, 'logo', 'aurora.gif', 'aurora.gif');
INSERT INTO `awulf_calendar_test_strongcal_varlist` VALUES (4, 'ip_filter', '', '');
INSERT INTO `awulf_calendar_test_strongcal_varlist` VALUES (5, 'ip_ban', '', '');

# --------------------------------------------------------

#
# Table structure for table `awulf_calendar_test_user_link`
#
# Creation: Jun 03, 2004 at 09:19 PM
# Last update: Jun 03, 2004 at 09:19 PM
#

DROP TABLE IF EXISTS `awulf_calendar_test_user_link`;
CREATE TABLE `awulf_calendar_test_user_link` (
  `id` mediumint(9) NOT NULL auto_increment,
  `user_id` mediumint(9) NOT NULL default '0',
  `group_id` mediumint(9) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=13 ;

#
# Dumping data for table `awulf_calendar_test_user_link`
#

INSERT INTO `awulf_calendar_test_user_link` VALUES (1, 1, 1);
INSERT INTO `awulf_calendar_test_user_link` VALUES (2, 2, 2);
INSERT INTO `awulf_calendar_test_user_link` VALUES (12, 10, 1);

# --------------------------------------------------------

#
# Table structure for table `awulf_calendar_test_usergroups`
#
# Creation: Jun 03, 2004 at 09:19 PM
# Last update: Jun 03, 2004 at 09:19 PM
#

DROP TABLE IF EXISTS `awulf_calendar_test_usergroups`;
CREATE TABLE `awulf_calendar_test_usergroups` (
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
) TYPE=MyISAM AUTO_INCREMENT=9 ;

#
# Dumping data for table `awulf_calendar_test_usergroups`
#

INSERT INTO `awulf_calendar_test_usergroups` VALUES (1, -1, '0', 'admin', '', '', 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, 0, 0);
INSERT INTO `awulf_calendar_test_usergroups` VALUES (2, -1, '0', 'guest', '', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);

# --------------------------------------------------------

#
# Table structure for table `awulf_calendar_test_users`
#
# Creation: Jun 03, 2004 at 09:19 PM
# Last update: Jun 09, 2004 at 08:08 PM
#

DROP TABLE IF EXISTS `awulf_calendar_test_users`;
CREATE TABLE `awulf_calendar_test_users` (
  `id` mediumint(9) NOT NULL auto_increment,
  `title` text NOT NULL,
  `first` text NOT NULL,
  `middle` text NOT NULL,
  `last` text NOT NULL,
  `username` text NOT NULL,
  `password` text NOT NULL,
  `last_ip` tinytext NOT NULL,
  `last_login` datetime NOT NULL default '0000-00-00 00:00:00',
  `email` mediumtext NOT NULL,
  `disabled` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=11 ;

#
# Dumping data for table `awulf_calendar_test_users`
#

INSERT INTO `awulf_calendar_test_users` VALUES (1, '', '', '', '', 'awulf', 'samplepassword', '127.0.0.1', '1969-12-31 18:00:00', 'awulf@inversiondesigns.com', 0);
INSERT INTO `awulf_calendar_test_users` VALUES (2, '', '', '', '', 'guest', '', '', '0000-00-00 00:00:00', '', 0);
INSERT INTO `awulf_calendar_test_users` VALUES (10, '', '', '', '', 'phpunit', 'samplepassword', '70.241.126.50', '2004-06-10 01:05:39', '', 0);

# --------------------------------------------------------

#
# Table structure for table `awulf_calendar_test_varlist`
#
# Creation: Jun 03, 2004 at 09:19 PM
# Last update: Jun 03, 2004 at 09:19 PM
#

DROP TABLE IF EXISTS `awulf_calendar_test_varlist`;
CREATE TABLE `awulf_calendar_test_varlist` (
  `id` mediumint(9) NOT NULL auto_increment,
  `var` text NOT NULL,
  `val` text NOT NULL,
  `dflt` text NOT NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=6 ;

#
# Dumping data for table `awulf_calendar_test_varlist`
#

INSERT INTO `awulf_calendar_test_varlist` VALUES (1, 'SKIN', 'installer', 'installer');
INSERT INTO `awulf_calendar_test_varlist` VALUES (2, 'USERGROUP', '2', '2');
INSERT INTO `awulf_calendar_test_varlist` VALUES (3, 'USER', '2', '2');
INSERT INTO `awulf_calendar_test_varlist` VALUES (4, 'ACTIVITY', '', '');
INSERT INTO `awulf_calendar_test_varlist` VALUES (5, 'ACTIVE_OFFSET', '', '');
