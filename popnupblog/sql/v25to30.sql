-- ----8----8----8----8----8----8----8----8----8----8----8----8----8----8----
-- Author: yoshis
-- Date : 2006/12/11
-- E-mail: webmaster@bluemooninc.biz
-- Description:
-- 	This sql script support to convert popnupblog v2.5x to v3.0
--	I tried this script under following versions:
--		* PopnupBlog 3.0
-- Usage:
--	Access like following url
--		http://__PREFIX__url/modules/popnupblog/admin/sqlupdate.php
-- ----8----8----8----8----8----8----8----8----8----8----8----8----8----8----
--
-- Add postid to popnupblog_info table and set postid from popnupblog table.
--
ALTER TABLE __PREFIX__popnupblog_info CHANGE `last_update` `last_update` DATETIME NULL DEFAULT NULL;
ALTER TABLE __PREFIX__popnupblog_info CHANGE `blog_permission` `ml_function` smallint(1) unsigned NOT NULL default '0';
ALTER TABLE __PREFIX__popnupblog_info ADD pop_server varchar(30) default NULL;
ALTER TABLE __PREFIX__popnupblog_info ADD pop_user varchar(30) default NULL;
ALTER TABLE __PREFIX__popnupblog_info ADD pop_password varchar(20) default NULL;
ALTER TABLE __PREFIX__popnupblog_info ADD pop_address varchar(60) default NULL;
ALTER TABLE __PREFIX__popnupblog_info ADD default_status tinyint(1) NOT NULL default '1';
ALTER TABLE __PREFIX__popnupblog ADD `blog_count` INT(5) UNSIGNED DEFAULT '0' NOT NULL AFTER `blogid`;
ALTER TABLE __PREFIX__popnupblog ADD INDEX `blog_count` ( `blogid` , `blog_count` );  
ALTER TABLE __PREFIX__popnupblog ADD notifypub tinyint(1) NOT NULL default '0';
ALTER TABLE __PREFIX__popnupblog ADD status tinyint(1) NOT NULL default '1';
ALTER TABLE __PREFIX__popnupblog CHANGE `last_update` `last_update` DATETIME NULL DEFAULT NULL;
ALTER TABLE __PREFIX__popnupblog_comment ADD notifypub tinyint(1) NOT NULL default '0';
ALTER TABLE __PREFIX__popnupblog_comment ADD status tinyint(1) NOT NULL default '1';
ALTER TABLE __PREFIX__popnupblog_comment DROP blog_date;
ALTER TABLE __PREFIX__popnupblog_trackback ADD tbid INT(5) unsigned NOT NULL auto_increment FIRST,
	ADD excerpt text AFTER url,
	ADD PRIMARY KEY (tbid);
--
-- End of file
--
