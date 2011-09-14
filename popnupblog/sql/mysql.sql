-- $Id: mysql.sql,v 2.60 2006/11/06 19:46:11 yoshis Exp $

-- blog information table
-- uid :
--    xoops uid
-- last_update:
--    update time
-- Auther : Yoshi.Sakai @ bluemooninc.biz
--    post blog from email. Work under pop.php.
CREATE TABLE popnupblog_info (
	blogid int(5) unsigned NOT NULL auto_increment,
	uid int(5) unsigned NOT NULL default '0',
	cat_id smallint(5) unsigned NOT NULL default '0',
	title varchar(200) binary,
	blog_desc text,
	group_post varchar(255) default NULL,
	group_read varchar(255) default NULL,
	group_comment varchar(255) default NULL,
	group_vote varchar(255) default NULL,
	last_update DATETIME NOT NULL ,
	email varchar(60) default NULL,
	plugin varchar(30) default NULL,
	ml_function smallint(1) unsigned NOT NULL default '0',
	pop_server varchar(30) default NULL,
	pop_user varchar(30) default NULL,
	pop_password varchar(20) default NULL,
	pop_address varchar(60) default NULL,
	default_status tinyint(1) NOT NULL default '1',
	PRIMARY KEY (blogid)
) ENGINE=MyISAM;

-- blog data table
-- uid :
--     xoops uid
-- blog_date :
--     date of blog
-- title :
--     title of blog
-- post_text :
--     blog data
-- alter table xoops_popnupblog add last_update timestamp not null;
CREATE TABLE popnupblog (
	postid int(5) UNSIGNED NOT NULL AUTO_INCREMENT,
	uid int(5) unsigned NOT NULL default '0',
	blogid int(5) unsigned NOT NULL default '0',
	blog_count INT(5) UNSIGNED DEFAULT '0' NOT NULL,
	blog_date DATETIME not null,
	title varchar(200),
	post_text text,
	last_update DATETIME NOT NULL,
	votes_yes int(5) unsigned NOT NULL default '0',
	votes_no int(5) unsigned NOT NULL default '0',
	notifypub tinyint(1) NOT NULL default '0',
	status tinyint(1) NOT NULL default '0',
	PRIMARY KEY  (postid),
	KEY blog_count ( `blogid` , `blog_count` )
) ENGINE=MyISAM;


-- blog comment table
-- uid :
--     xoops uid
-- blog_date :
--     date of blog
-- comment_id :
--     sequential comment id. 
-- comment_uid :
--     uid of comment user. set value to 0 if guest user
-- comment_name :
--     guest user name. 
-- post_text :
--     comment data
CREATE TABLE popnupblog_comment (
	blogid int(5) unsigned NOT NULL default '0',
	postid INT(5) UNSIGNED DEFAULT '0' NOT NULL,
	comment_id int(8) unsigned NOT NULL auto_increment,
	comment_uid int(5) unsigned NOT NULL default '0',
	comment_name varchar(200),
	post_text text,
	create_date DATETIME not null,
	vote tinyint(1) NOT NULL default '0',
	notifypub tinyint(1) NOT NULL default '0',
	status tinyint(1) NOT NULL default '0',
	KEY (postid),
	PRIMARY KEY (comment_id)
) ENGINE=MyISAM;

CREATE TABLE popnupblog_application (
	uid int(5) unsigned NOT NULL,
	group_post varchar(255) default NULL,
	cat_id smallint(5) unsigned NOT NULL default '0',
	title varchar(200) binary,
	blog_desc text,
	permission smallint(1) unsigned NOT NULL default '0',
	group_read varchar(255) default NULL,
	group_comment varchar(255) default NULL,
	group_vote varchar(255) default NULL,
	create_date timestamp(14) NOT NULL ,
	email varchar(60) ,
	emailalias varchar(60) ,
	PRIMARY KEY (uid)
) TYPE = MyISAM;

-- tbid, excerpt added by kazy 2006.11.18
CREATE TABLE popnupblog_trackback (
	tbid INT(5) unsigned NOT NULL auto_increment,
	blogid int(5) unsigned NOT NULL,
	postid INT(5) UNSIGNED DEFAULT '0' NOT NULL,
	t_date DATETIME not null,
	count int(8) unsigned,
	title varchar(250),
	url text,
	excerpt text,
	KEY(postid),
	PRIMARY KEY (tbid)
) TYPE = MyISAM;

-- moblog email alias table added by hoshiyan@hoshiba-farm.com 2004.8.3
-- uid :
--    xoops uid
-- public:
--    0: closed it for other member 
--    1:
-- email:
--    another email address for moblog posting. Work under pop.php.
CREATE TABLE popnupblog_emailalias (
	blogid int(5) unsigned NOT NULL default '0',
	public tinyint(1) NOT NULL default '1',
	email varchar(60) NOT NULL,
	uid int(5) unsigned NOT NULL default '0',
	PRIMARY KEY (blogid,public,email)
) ENGINE=MyISAM;

CREATE TABLE popnupblog_categories (
	cat_id smallint(3) unsigned NOT NULL auto_increment,
	cat_title varchar(100) NOT NULL default '',
	cat_order varchar(10) default NULL,
	PRIMARY KEY  (cat_id)
) ENGINE=MyISAM;

