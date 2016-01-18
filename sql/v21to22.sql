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
-- Add postid to popnupblog table and set to primary key.
--
ALTER TABLE __PREFIX__popnupblog DROP Primary key;
ALTER TABLE __PREFIX__popnupblog ADD postid INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST ;
--
-- Add postid to popnupblog_comment table and set postid from popnupblog table.
--
ALTER TABLE __PREFIX__popnupblog_comment ADD postid INT UNSIGNED DEFAULT '0' NOT NULL AFTER blogid;
UPDATE __PREFIX__popnupblog INNER JOIN __PREFIX__popnupblog_comment ON (__PREFIX__popnupblog.blogid = __PREFIX__popnupblog_comment.blogid) AND (__PREFIX__popnupblog.blog_date = __PREFIX__popnupblog_comment.blog_date) SET __PREFIX__popnupblog_comment.postid = __PREFIX__popnupblog.postid;
--
-- Add postid to popnupblog_trackback table and set postid from popnupblog table.
--
ALTER TABLE __PREFIX__popnupblog_trackback ADD postid INT UNSIGNED DEFAULT '0' NOT NULL AFTER blogid;
UPDATE __PREFIX__popnupblog INNER JOIN __PREFIX__popnupblog_trackback ON (__PREFIX__popnupblog.blogid = __PREFIX__popnupblog_trackback.blogid) AND (__PREFIX__popnupblog.blog_date = __PREFIX__popnupblog_trackback.t_date) SET __PREFIX__popnupblog_trackback.postid = __PREFIX__popnupblog.postid;
--
-- End of file
--
