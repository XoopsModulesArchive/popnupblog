-- ----8----8----8----8----8----8----8----8----8----8----8----8----8----8----
-- Author: aotake
-- Date : 2005/02/17
-- E-mail: aotake@bmath.org
-- Description:
-- 	This sql script support to convert simpleblog data into
--	popnupblog. I tried this script under following versions:
--		* simpleblog Version 0.21
--		* PopnupBlog 2.04c
-- Usage:
--	Access like following url
--		http://xoops_url/modules/popnupblog/admin/s2p.php
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
--	NOTE: use above popnupblog_info to get blogid
--
insert into __PREFIX__popnupblog
  (uid,blogid,blog_date,title,post_text,last_update)
  select s.uid, i.blogid,s.blog_date,s.title,s.post_text,s.last_update
  from __PREFIX__simpleblog s
  left join __PREFIX__popnupblog_info i on i.uid = s.uid;
--
-- copy simpleblog comment data into popnupblog_comment table
--	NOTE: use above popnupblog_info to get blogid
--
insert into __PREFIX__popnupblog_comment
  (blogid,blog_date,comment_id,comment_uid,comment_name,post_text,create_date)
  select i.blogid,s.blog_date,s.comment_id,s.comment_uid,s.comment_name,
         s.post_text,s.create_date
  from __PREFIX__simpleblog_comment s
  left join __PREFIX__popnupblog_info i on i.uid = s.uid;
--
-- if nessasary,
-- copy simpleblog application data into popnupblog_application table
--
insert into __PREFIX__popnupblog_application
  (uid,title,permission,create_date)
  select * from __PREFIX__simpleblog_application;
--
-- copy simpleblog trackback data into popnupblog_trackback table
--	NOTE: use above popnupblog_info to get blogid
--
insert into __PREFIX__popnupblog_trackback
  (blogid,t_date,count,title,url)
  select i.blogid,s.t_date,s.count,s.title,s.url
  from __PREFIX__simpleblog_trackback s
  left join __PREFIX__popnupblog_info i on i.uid = s.uid;

