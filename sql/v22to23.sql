-- ----8----8----8----8----8----8----8----8----8----8----8----8----8----8----
-- Author: yoshis
-- Date : 2005/09/03
-- E-mail: webmaster@bluemooninc.biz
-- Description:
-- 	This sql script support to convert popnupblog v2.1 to v2.2
--	I tried this script under following versions:
--		* PopnupBlog 2.1 redwood-803
--		* PopnupBlog 2.2 RC1
-- Usage:
--	Access like following url
--		http://__PREFIX__url/modules/popnupblog/admin/sqlupdate.php
-- ----8----8----8----8----8----8----8----8----8----8----8----8----8----8----
--
-- Add group_read,group_comment,group_vote to popnupblog info table
--
ALTER TABLE __PREFIX__popnupblog_application ADD group_read varchar(255) default NULL AFTER permission;
ALTER TABLE __PREFIX__popnupblog_application ADD group_comment varchar(255) default NULL AFTER group_read;
ALTER TABLE __PREFIX__popnupblog_application ADD group_vote varchar(255) default NULL AFTER group_comment;
--
-- Add postid to popnupblog_info table and set postid from popnupblog table.
--
ALTER TABLE __PREFIX__popnupblog_info ADD group_read varchar(255) default NULL AFTER blog_permission;
ALTER TABLE __PREFIX__popnupblog_info ADD group_comment varchar(255) default NULL AFTER group_read;
ALTER TABLE __PREFIX__popnupblog_info ADD group_vote varchar(255) default NULL AFTER group_comment;
--
-- End of file
--
