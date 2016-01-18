-- ----8----8----8----8----8----8----8----8----8----8----8----8----8----8----
-- Author: aotake
-- Date : 2005/02/25
-- E-mail: aotake@bmath.org
-- Description:
-- 	This sql script support to convert simpleblog data into
--	popnupblog. I tried this script under following versions:
--		* simpleblog Version 0.21
--		* PopnupBlog 1.56 
-- Usage:
--	Access like following url
--		http://__PREFIX__url/modules/popnupblog/admin/s2p.php
--	
--                                        I'm sorry for my poor english :D
--
-- ----8----8----8----8----8----8----8----8----8----8----8----8----8----8----
--
-- copy simpleblog_info data into popnupblog_info table
--
insert into __PREFIX__popnupblog_info
  (uid,title,blog_permission,last_update)
  select uid, title, blog_permission, last_update
  from __PREFIX__simpleblog_info order by uid;
--
-- copy simpleblog data into popnupblog table
--
insert into __PREFIX__popnupblog
  (uid,blogid,blog_date,title,post_text,last_update)
  select uid, uid as blogid,blog_date,title,post_text,last_update
  from __PREFIX__simpleblog;
--
-- copy simpleblog comment data into popnupblog_comment table
--	NOTE: use above popnupblog_info to get blogid
--
insert into __PREFIX__popnupblog_comment 
  select * from __PREFIX__simpleblog_comment;
--
-- if nessasary,
-- copy simpleblog application data into popnupblog_application table
--
insert into __PREFIX__popnupblog_application
  select * from __PREFIX__simpleblog_application;
--
-- copy simpleblog trackback data into popnupblog_trackback table
--	NOTE: use above popnupblog_info to get blogid
--
insert into __PREFIX__popnupblog_trackback
  select * from __PREFIX__simpleblog_trackback;

