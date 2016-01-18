-- ----8----8----8----8----8----8----8----8----8----8----8----8----8----8----
-- Author: yoshis
-- Date : 2006/05/24
-- E-mail: webmaster@bluemooninc.biz
-- Description:
-- 	This sql script support to convert popnupblog v2.3 to v2.4
--	I tried this script under following versions:
--		* PopnupBlog 2.34
-- Usage:
--	Access like following url
--		http://__PREFIX__url/modules/popnupblog/admin/sqlupdate.php
-- ----8----8----8----8----8----8----8----8----8----8----8----8----8----8----
--
-- Add postid to popnupblog_info table and set postid from popnupblog table.
--
ALTER TABLE __PREFIX__popnupblog_application CHANGE `groupid` `group_post` VARCHAR( 255 ) NULL DEFAULT NULL;
ALTER TABLE __PREFIX__popnupblog_info CHANGE `groupid` `group_post` VARCHAR( 255 ) NULL DEFAULT NULL;
--
-- End of file
--
