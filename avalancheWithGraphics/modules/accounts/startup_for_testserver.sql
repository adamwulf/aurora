
DROP TABLE IF EXISTS `avalanche_strongcal_cal_38`;
CREATE TABLE `avalanche_strongcal_cal_38` (
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
) TYPE=MyISAM AUTO_INCREMENT=12473 ;

DROP TABLE IF EXISTS `avalanche_strongcal_cal_38_comments`;
CREATE TABLE `avalanche_strongcal_cal_38_comments` (
  `id` mediumint(9) NOT NULL auto_increment,
  `event_id` mediumint(9) NOT NULL default '0',
  `author` mediumint(9) NOT NULL default '0',
  `post_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `title` tinytext NOT NULL,
  `body` mediumtext NOT NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;


DROP TABLE IF EXISTS `avalanche_strongcal_cal_38_fields`;
CREATE TABLE `avalanche_strongcal_cal_38_fields` (
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
) TYPE=MyISAM AUTO_INCREMENT=6369 ;


INSERT INTO `avalanche_strongcal_cal_38_fields` VALUES (1, 'Start Date:', 'start_date', 'date', '2003-10-31', 0, 0, 0, 1, 0, 0, 0, 0);
INSERT INTO `avalanche_strongcal_cal_38_fields` VALUES (2, 'Start Time:', 'start_time', 'time', '13:00:00', 15, 0, 0, 2, 0, 0, 0, 0);
INSERT INTO `avalanche_strongcal_cal_38_fields` VALUES (3, 'End Date:', 'end_date', 'date', '2003-10-31', 0, 0, 0, 3, 0, 0, 0, 0);
INSERT INTO `avalanche_strongcal_cal_38_fields` VALUES (4, 'End Time:', 'end_time', 'time', '14:00:00', 15, 0, 0, 4, 0, 0, 0, 0);
INSERT INTO `avalanche_strongcal_cal_38_fields` VALUES (5, 'Title:', 'title', 'text', '', 10, 0, 0, 5, 0, 0, 0, 0);
INSERT INTO `avalanche_strongcal_cal_38_fields` VALUES (6, 'Description:', 'description', 'largetext', '', 0, 0, 0, 6, 0, 0, 0, 0);
INSERT INTO `avalanche_strongcal_cal_38_fields` VALUES (7, 'Priority:', 'priority', 'select', 'High\nHigh\n\nNormal\nNormal\n1\nLow\nLow\n', 0, 0, 0, 6, 0, 0, 0, 0);


DROP TABLE IF EXISTS `avalanche_strongcal_cal_38_recur`;
CREATE TABLE `avalanche_strongcal_cal_38_recur` (
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
) TYPE=MyISAM AUTO_INCREMENT=1907 ;


INSERT INTO `avalanche_strongcal_cal_38_recur` VALUES (1856, '00:00:00', '00:00:00', '2004-11-14', 7, 0, '2007-03-16', '4', 0, 0, '0', 0, 0, 0, 0, 0, 11, 3, 0, 2, 3, '0000-00-00');
INSERT INTO `avalanche_strongcal_cal_38_recur` VALUES (1857, '00:00:00', '00:00:00', '2004-12-24', 6, 2, '0000-00-00', '1', 1, 0, '', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '0000-00-00');
INSERT INTO `avalanche_strongcal_cal_38_recur` VALUES (1858, '00:00:00', '00:00:00', '2004-12-24', 6, 2, '0000-00-00', '1', 1, 0, '', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '0000-00-00');
INSERT INTO `avalanche_strongcal_cal_38_recur` VALUES (1859, '00:00:00', '00:00:00', '2004-05-24', 7, 0, '2004-05-30', '2', 0, 2, '124', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '0000-00-00');
INSERT INTO `avalanche_strongcal_cal_38_recur` VALUES (1860, '00:00:00', '00:00:00', '2004-11-14', 7, 0, '2007-03-16', '4', 0, 0, '0', 0, 0, 0, 0, 0, 11, 3, 0, 2, 3, '0000-00-00');
INSERT INTO `avalanche_strongcal_cal_38_recur` VALUES (1861, '00:00:00', '00:00:00', '2004-12-14', 7, 0, '2005-01-12', '2', 0, 2, '124', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '0000-00-00');
INSERT INTO `avalanche_strongcal_cal_38_recur` VALUES (1862, '00:00:00', '00:00:00', '2004-11-14', 7, 0, '2007-03-16', '4', 0, 0, '0', 0, 0, 0, 0, 0, 11, 3, 0, 2, 3, '0000-00-00');
INSERT INTO `avalanche_strongcal_cal_38_recur` VALUES (1863, '00:00:00', '00:00:00', '0000-00-00', 0, 0, '0000-00-00', '', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '0000-00-00');
INSERT INTO `avalanche_strongcal_cal_38_recur` VALUES (1864, '00:00:00', '00:00:00', '2004-04-03', 6, 3, '0000-00-00', '', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '0000-00-00');
INSERT INTO `avalanche_strongcal_cal_38_recur` VALUES (1865, '00:00:00', '00:00:00', '2004-12-14', 7, 0, '2005-01-12', '1', 2, 0, '', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '0000-00-00');
INSERT INTO `avalanche_strongcal_cal_38_recur` VALUES (1866, '00:00:00', '00:00:00', '2004-05-24', 7, 0, '2004-05-30', '1', 2, 0, '', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '0000-00-00');
INSERT INTO `avalanche_strongcal_cal_38_recur` VALUES (1867, '00:00:00', '00:00:00', '2004-12-14', 7, 0, '2005-01-12', '2', 0, 2, '124', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '0000-00-00');
INSERT INTO `avalanche_strongcal_cal_38_recur` VALUES (1868, '00:00:00', '00:00:00', '2004-05-24', 7, 0, '2004-05-30', '2', 0, 2, '124', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '0000-00-00');
INSERT INTO `avalanche_strongcal_cal_38_recur` VALUES (1869, '00:00:00', '00:00:00', '2004-11-14', 7, 0, '2005-03-12', '3', 0, 0, '0', 8, 10, 0, 0, 2, 0, 0, 0, 0, 0, '0000-00-00');
INSERT INTO `avalanche_strongcal_cal_38_recur` VALUES (1870, '00:00:00', '00:00:00', '2004-11-14', 7, 0, '2005-03-12', '3', 0, 0, '0', 9, 0, 2, 1, 1, 0, 0, 0, 0, 0, '0000-00-00');
INSERT INTO `avalanche_strongcal_cal_38_recur` VALUES (1871, '00:00:00', '00:00:00', '2004-11-14', 7, 0, '2007-03-12', '4', 0, 0, '0', 0, 0, 0, 0, 0, 10, 3, 5, 0, 0, '0000-00-00');
INSERT INTO `avalanche_strongcal_cal_38_recur` VALUES (1872, '00:00:00', '00:00:00', '2004-11-14', 7, 0, '2007-03-16', '4', 0, 0, '0', 0, 0, 0, 0, 0, 11, 3, 0, 2, 3, '0000-00-00');
INSERT INTO `avalanche_strongcal_cal_38_recur` VALUES (1873, '00:00:00', '00:00:00', '2004-11-14', 7, 0, '2007-03-16', '4', 0, 0, '0', 0, 0, 0, 0, 0, 11, 3, 0, 2, 3, '0000-00-00');
INSERT INTO `avalanche_strongcal_cal_38_recur` VALUES (1874, '00:00:00', '00:00:00', '2004-12-24', 6, 2, '0000-00-00', '1', 1, 0, '', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '0000-00-00');
INSERT INTO `avalanche_strongcal_cal_38_recur` VALUES (1875, '00:00:00', '00:00:00', '2004-12-24', 6, 2, '0000-00-00', '1', 1, 0, '', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '0000-00-00');
INSERT INTO `avalanche_strongcal_cal_38_recur` VALUES (1876, '00:00:00', '00:00:00', '2004-05-24', 7, 0, '2004-05-30', '2', 0, 2, '124', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '0000-00-00');
INSERT INTO `avalanche_strongcal_cal_38_recur` VALUES (1877, '00:00:00', '00:00:00', '2004-11-14', 7, 0, '2007-03-16', '4', 0, 0, '0', 0, 0, 0, 0, 0, 11, 3, 0, 2, 3, '0000-00-00');
INSERT INTO `avalanche_strongcal_cal_38_recur` VALUES (1878, '00:00:00', '00:00:00', '2004-12-14', 7, 0, '2005-01-12', '2', 0, 2, '124', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '0000-00-00');
INSERT INTO `avalanche_strongcal_cal_38_recur` VALUES (1879, '00:00:00', '00:00:00', '2004-11-14', 7, 0, '2007-03-16', '4', 0, 0, '0', 0, 0, 0, 0, 0, 11, 3, 0, 2, 3, '0000-00-00');
INSERT INTO `avalanche_strongcal_cal_38_recur` VALUES (1880, '00:00:00', '00:00:00', '0000-00-00', 0, 0, '0000-00-00', '', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '0000-00-00');
INSERT INTO `avalanche_strongcal_cal_38_recur` VALUES (1881, '00:00:00', '00:00:00', '2004-04-03', 6, 3, '0000-00-00', '', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '0000-00-00');
INSERT INTO `avalanche_strongcal_cal_38_recur` VALUES (1882, '00:00:00', '00:00:00', '2004-12-14', 7, 0, '2005-01-12', '1', 2, 0, '', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '0000-00-00');
INSERT INTO `avalanche_strongcal_cal_38_recur` VALUES (1883, '00:00:00', '00:00:00', '2004-05-24', 7, 0, '2004-05-30', '1', 2, 0, '', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '0000-00-00');
INSERT INTO `avalanche_strongcal_cal_38_recur` VALUES (1884, '00:00:00', '00:00:00', '2004-12-14', 7, 0, '2005-01-12', '2', 0, 2, '124', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '0000-00-00');
INSERT INTO `avalanche_strongcal_cal_38_recur` VALUES (1885, '00:00:00', '00:00:00', '2004-05-24', 7, 0, '2004-05-30', '2', 0, 2, '124', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '0000-00-00');
INSERT INTO `avalanche_strongcal_cal_38_recur` VALUES (1886, '00:00:00', '00:00:00', '2004-11-14', 7, 0, '2005-03-12', '3', 0, 0, '0', 8, 10, 0, 0, 2, 0, 0, 0, 0, 0, '0000-00-00');
INSERT INTO `avalanche_strongcal_cal_38_recur` VALUES (1887, '00:00:00', '00:00:00', '2004-11-14', 7, 0, '2005-03-12', '3', 0, 0, '0', 9, 0, 2, 1, 1, 0, 0, 0, 0, 0, '0000-00-00');
INSERT INTO `avalanche_strongcal_cal_38_recur` VALUES (1888, '00:00:00', '00:00:00', '2004-11-14', 7, 0, '2007-03-12', '4', 0, 0, '0', 0, 0, 0, 0, 0, 10, 3, 5, 0, 0, '0000-00-00');
INSERT INTO `avalanche_strongcal_cal_38_recur` VALUES (1889, '00:00:00', '00:00:00', '2004-11-14', 7, 0, '2007-03-16', '4', 0, 0, '0', 0, 0, 0, 0, 0, 11, 3, 0, 2, 3, '0000-00-00');
INSERT INTO `avalanche_strongcal_cal_38_recur` VALUES (1890, '00:00:00', '00:00:00', '2004-11-14', 7, 0, '2007-03-16', '4', 0, 0, '0', 0, 0, 0, 0, 0, 11, 3, 0, 2, 3, '0000-00-00');
INSERT INTO `avalanche_strongcal_cal_38_recur` VALUES (1891, '00:00:00', '00:00:00', '2004-12-24', 6, 2, '0000-00-00', '1', 1, 0, '', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '0000-00-00');
INSERT INTO `avalanche_strongcal_cal_38_recur` VALUES (1892, '00:00:00', '00:00:00', '2004-12-24', 6, 2, '0000-00-00', '1', 1, 0, '', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '0000-00-00');
INSERT INTO `avalanche_strongcal_cal_38_recur` VALUES (1893, '00:00:00', '00:00:00', '2004-05-24', 7, 0, '2004-05-30', '2', 0, 2, '124', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '0000-00-00');
INSERT INTO `avalanche_strongcal_cal_38_recur` VALUES (1894, '00:00:00', '00:00:00', '2004-11-14', 7, 0, '2007-03-16', '4', 0, 0, '0', 0, 0, 0, 0, 0, 11, 3, 0, 2, 3, '0000-00-00');
INSERT INTO `avalanche_strongcal_cal_38_recur` VALUES (1895, '00:00:00', '00:00:00', '2004-12-14', 7, 0, '2005-01-12', '2', 0, 2, '124', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '0000-00-00');
INSERT INTO `avalanche_strongcal_cal_38_recur` VALUES (1896, '00:00:00', '00:00:00', '2004-11-14', 7, 0, '2007-03-16', '4', 0, 0, '0', 0, 0, 0, 0, 0, 11, 3, 0, 2, 3, '0000-00-00');
INSERT INTO `avalanche_strongcal_cal_38_recur` VALUES (1897, '00:00:00', '00:00:00', '0000-00-00', 0, 0, '0000-00-00', '', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '0000-00-00');
INSERT INTO `avalanche_strongcal_cal_38_recur` VALUES (1898, '00:00:00', '00:00:00', '2004-04-03', 6, 3, '0000-00-00', '', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '0000-00-00');
INSERT INTO `avalanche_strongcal_cal_38_recur` VALUES (1899, '00:00:00', '00:00:00', '2004-12-14', 7, 0, '2005-01-12', '1', 2, 0, '', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '0000-00-00');
INSERT INTO `avalanche_strongcal_cal_38_recur` VALUES (1900, '00:00:00', '00:00:00', '2004-05-24', 7, 0, '2004-05-30', '1', 2, 0, '', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '0000-00-00');
INSERT INTO `avalanche_strongcal_cal_38_recur` VALUES (1901, '00:00:00', '00:00:00', '2004-12-14', 7, 0, '2005-01-12', '2', 0, 2, '124', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '0000-00-00');
INSERT INTO `avalanche_strongcal_cal_38_recur` VALUES (1902, '00:00:00', '00:00:00', '2004-05-24', 7, 0, '2004-05-30', '2', 0, 2, '124', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '0000-00-00');
INSERT INTO `avalanche_strongcal_cal_38_recur` VALUES (1903, '00:00:00', '00:00:00', '2004-11-14', 7, 0, '2005-03-12', '3', 0, 0, '0', 8, 10, 0, 0, 2, 0, 0, 0, 0, 0, '0000-00-00');
INSERT INTO `avalanche_strongcal_cal_38_recur` VALUES (1904, '00:00:00', '00:00:00', '2004-11-14', 7, 0, '2005-03-12', '3', 0, 0, '0', 9, 0, 2, 1, 1, 0, 0, 0, 0, 0, '0000-00-00');
INSERT INTO `avalanche_strongcal_cal_38_recur` VALUES (1905, '00:00:00', '00:00:00', '2004-11-14', 7, 0, '2007-03-12', '4', 0, 0, '0', 0, 0, 0, 0, 0, 10, 3, 5, 0, 0, '0000-00-00');
INSERT INTO `avalanche_strongcal_cal_38_recur` VALUES (1906, '00:00:00', '00:00:00', '2004-11-14', 7, 0, '2007-03-16', '4', 0, 0, '0', 0, 0, 0, 0, 0, 11, 3, 0, 2, 3, '0000-00-00');


DROP TABLE IF EXISTS `avalanche_strongcal_cal_38_varlist`;
CREATE TABLE `avalanche_strongcal_cal_38_varlist` (
  `id` mediumint(9) NOT NULL auto_increment,
  `var` text NOT NULL,
  `val` text NOT NULL,
  `dflt` text NOT NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;


DROP TABLE IF EXISTS `avalanche_strongcal_calendars`;
CREATE TABLE `avalanche_strongcal_calendars` (
  `id` mediumint(9) NOT NULL auto_increment,
  `name` text NOT NULL,
  `description` text NOT NULL,
  `color` text NOT NULL,
  `author` mediumint(9) NOT NULL default '0',
  `public` tinyint(4) NOT NULL default '0',
  `added_on` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=1156 ;

INSERT INTO `avalanche_strongcal_calendars` VALUES (38, 'Test Calendar', '', '#FFFFFF', 3, 0, '0000-00-00 00:00:00');


DROP TABLE IF EXISTS `avalanche_strongcal_permissions`;
CREATE TABLE `avalanche_strongcal_permissions` (
  `id` mediumint(9) NOT NULL auto_increment,
  `usergroup` mediumint(9) NOT NULL default '0',
  `change_permissions` tinyint(4) NOT NULL default '0',
  `add_calendar` tinyint(4) NOT NULL default '0',
  `delete_calendar` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=32 ;


INSERT INTO `avalanche_strongcal_permissions` VALUES (1, 1, 1, -1, 1);
INSERT INTO `avalanche_strongcal_permissions` VALUES (2, 2, 0, 0, 0);
INSERT INTO `avalanche_strongcal_permissions` VALUES (3, 3, 1, -1, 1);


DROP TABLE IF EXISTS `avalanche_strongcal_preferences`;
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
) TYPE=MyISAM AUTO_INCREMENT=4 ;


INSERT INTO `avalanche_strongcal_preferences` VALUES (1, -1, 'overview', '-6', '', '06:00:00', '22:00:00');
INSERT INTO `avalanche_strongcal_preferences` VALUES (2, 1, 'overview', '', '', '06:00:00', '22:00:00');
INSERT INTO `avalanche_strongcal_preferences` VALUES (3, 3, 'overview', '-6', '', '06:00:00', '22:00:00');


DROP TABLE IF EXISTS `avalanche_user_link`;
CREATE TABLE `avalanche_user_link` (
  `id` mediumint(9) NOT NULL auto_increment,
  `user_id` mediumint(9) NOT NULL default '0',
  `group_id` mediumint(9) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=1111 ;


INSERT INTO `avalanche_user_link` VALUES (1053, 3, 1);
INSERT INTO `avalanche_user_link` VALUES (2, 2, 2);
INSERT INTO `avalanche_user_link` VALUES (1080, 1, 1);


DROP TABLE IF EXISTS `avalanche_usergroups`;
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
) TYPE=MyISAM AUTO_INCREMENT=22 ;


INSERT INTO `avalanche_usergroups` VALUES (1, -1, 'SYSTEM', 'Administration', '', '', 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1);
INSERT INTO `avalanche_usergroups` VALUES (2, -1, 'SYSTEM', 'Guests', '', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0);
INSERT INTO `avalanche_usergroups` VALUES (3, -1, 'PUBLIC', 'All Users', '', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
INSERT INTO `avalanche_usergroups` VALUES (4, -1, 'PUBLIC', 'Guests', '', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0);


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
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=22 ;

INSERT INTO `avalanche_users` VALUES (1, '', '', '', '', 'awulf', 'wewcdh', '127.0.0.1', '1969-12-31 18:00:00', '0000-00-00 00:00:00', 'awulf@inversiondesigns.com', '', 0,'',0);
INSERT INTO `avalanche_users` VALUES (2, '', '', '', '', 'guest', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '', '', 0,'',0);
INSERT INTO `avalanche_users` VALUES (3, '', '', '', '', 'phpunit', 'samplepassword', '67.10.15.207', '2005-01-07 05:55:56', '2005-01-07 05:55:56', '', '', 0,'',0);


CREATE TABLE `avalanche_accounts` (
  `id` mediumint(9) NOT NULL auto_increment,
  `name` text NOT NULL,
  `email` text NOT NULL,
  `added_on` datetime NOT NULL default '0000-00-00 00:00:00',
  `domain` text NOT NULL,
  `disabled` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=23 ;

