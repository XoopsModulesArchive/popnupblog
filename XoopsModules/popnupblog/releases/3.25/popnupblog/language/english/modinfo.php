<?php
// $Id: modinfo.php,v 1.1.1.1 2005/08/28 02:13:09 yoshis Exp $ 

define('_MI_POPNUPBLOG_APPL_DESC', '');
define('_MI_POPNUPBLOG_1_LINE', 'Recently updated blog');
define('_MI_POPNUPBLOG_CONF_DESC', 'Description');
define('_MI_POPNUPBLOG_TRACKBACK', 'TrackBack');
define('_MI_POPNUPBLOG_REWRITE_TITLE', 'Use Apache rewrite engine');
define('_MI_POPNUPBLOG_NAME', 'PopnupBLOG');
define('_MI_POPNUPBLOG_DESC', 'Popnup Blog');
define('_MI_POPNUPBLOG_UNUSE_UPDATE_PING', 'Unuse update ping');
define('_MI_POPNUPBLOG_UNUSE_TRACKBACK', 'Unuse trackback');
define('_MI_POPNUPBLOG_APPL_WAITING_TITLE', 'PopnupBlog New Application');
define('_MI_POPNUPBLOG_NAME_BIG_BLOCK', 'PopnupBlog');
define('_MI_POPNUPBLOG_USE_REWRITE', 'Use rewrite');
define('_MI_POPNUPBLOG_UPDATE_PING', 'Use update ping');
define('_MI_POPNUPBLOG_1_LINE_DESC', '1 line view block');
define('_MI_POPNUPBLOG_REWRITE_DESC', 'User can use blog url from/modules/popnupblog/view/index.php?uid=1 to /modules/popnupblog/view/1.html (only expert)');
define('_MI_POPNUPBLOG_APPL_WAITING', 'New Application');
define('_MI_POPNUPBLOG_UPDATE_PING_DESC', 'Use update ping');
define('_MI_POPNUPBLOG_WRITE', 'Write Blog');
define('_MI_POPNUPBLOG_PREFERENCE', 'Preference');
define('_MI_POPNUPBLOG_APPLY', 'New Blog');
define('_MI_POPNUPBLOG_TRACKBACK_DESC', 'Enable TrackBack Feature');
define('_MI_POPNUPBLOG_UNUSE_REWRITE', 'Unuse rewrite');
define('_MI_POPNUPBLOG_APPL_DENY', 'unpermit');
define('_MI_POPNUPBLOG_CONFIG_RSS_DEF', 'Popnup blog user can write');
define('_MI_POPNUPBLOG_USE_TRACKBACK', 'Use trackback');
define('_MI_POPNUPBLOG_APPL_ALLOW', 'permit');
define('_MI_POPNUPBLOG_APPL_OK', 'Allow to application for user');
define('_MI_POPNUPBLOG_USE_UPDATE_PING', 'Use update ping');
define('_MI_POPNUPBLOG_CONFIG_RSS_DESC', 'Description for this blog on rss feed');
// Add 2006.02.02 by yoshis
define('_MI_POPNUPBLOG_FILECHRSET', 'Character-code for attach file');
define('_MI_POPNUPBLOG_FILECHRSET_DESC', 'Set the character-code for save to server. (ASCII,UTF-8,EUC etc)');
// Add 2004.10.27 by yoshis
define('_MI_POPNUPBLOG_MAILSERVER', 'Mail Server');
define('_MI_POPNUPBLOG_MAILSERVER_DESC', 'Input pop3 mail server for recive blog.');
define('_MI_POPNUPBLOG_MAILUSER', 'Mail User');
define('_MI_POPNUPBLOG_MAILUSER_DESC', 'Input mail user name for recieve blog.');
define('_MI_POPNUPBLOG_MAILPWD', 'Mail Password');
define('_MI_POPNUPBLOG_MAILPWD_DESC', 'Input mail password for recieve blog.');
define('_MI_POPNUPBLOG_MAILADDR', 'Mail Address');
define('_MI_POPNUPBLOG_MAILADDR_DESC', 'Input mail address for recieve blog.');
// Add 2005.01.22 by yoshis
define("_MI_POPNUPBLOG_GUESTBLOGID","Allow to Blog ID from anonymous mail");
define("_MI_POPNUPBLOG_ACTVTYPE","Select activation type of newly registered blogs");
define("_MI_POPNUPBLOG_AUTOACTV","Activate automatically");
define("_MI_POPNUPBLOG_ADMINACTV","Activation by administrators");
define("_MI_POPNUPBLOG_NEWUNOTIFY","Notify by mail when a new blog is registered?");
define("_MI_POPNUPBLOG_SHOWNAME","Replace user name with real name");
// Add 2006.03.21 by yoshis
define("_MI_POPNUPBLOG_GROUPSETBYUSER","Group permission by user (post,view,comment,vote)");

// For Notify
define ('_MI_POPNUPBLOG_BLOG_NOTIFY', 'Forum');
define ('_MI_POPNUPBLOG_BLOG_NOTIFYDSC', 'Notification options that apply to the current blog.');

define ('_MI_POPNUPBLOG_GLOBAL_NOTIFY', 'Global');
define ('_MI_POPNUPBLOG_GLOBAL_NOTIFYDSC', 'Global blog notification options.');

define ('_MI_POPNUPBLOG_BLOG_NEWPOST_NOTIFY', 'New Post');
define ('_MI_POPNUPBLOG_BLOG_NEWPOST_NOTIFYCAP', 'Notify me of any new posts in the current blog.');
define ('_MI_POPNUPBLOG_BLOG_NEWPOST_NOTIFYDSC', 'Receive notification when any new message is posted in the current blog.');
define ('_MI_POPNUPBLOG_BLOG_NEWPOST_NOTIFYSBJ', '[{X_SITENAME}] {X_MODULE} auto-notify : New post in blog');

define ('_MI_POPNUPBLOG_BLOG_NEWFULLPOST_NOTIFY', 'New Post (Full Text)');
define ('_MI_POPNUPBLOG_BLOG_NEWFULLPOST_NOTIFYCAP', 'Notify me of any new posts (include full text in message).');
define ('_MI_POPNUPBLOG_BLOG_NEWFULLPOST_NOTIFYDSC', 'Receive full text notification when any new message is posted.');
define ('_MI_POPNUPBLOG_BLOG_NEWFULLPOST_NOTIFYSBJ', '[{X_SITENAME}] {X_MODULE} auto-notify : New post (full text)');

define ('_MI_POPNUPBLOG_BLOG_NEWCOMMENT_NOTIFY', 'New comment (Full Text)');
define ('_MI_POPNUPBLOG_BLOG_NEWCOMMENT_NOTIFYCAP', 'Notify me of any new comments (include full text in message).');
define ('_MI_POPNUPBLOG_BLOG_NEWCOMMENT_NOTIFYDSC', 'Receive full text notification when any new message is commented.');
define ('_MI_POPNUPBLOG_BLOG_NEWCOMMENT_NOTIFYSBJ', '[{X_SITENAME}] {X_MODULE} auto-notify : New comment (full text)');

define ('_MI_POPNUPBLOG_GLOBAL_NEWBLOG_NOTIFY', 'New Blog');
define ('_MI_POPNUPBLOG_GLOBAL_NEWBLOG_NOTIFYCAP', 'Notify me when a new blog is created.');
define ('_MI_POPNUPBLOG_GLOBAL_NEWBLOG_NOTIFYDSC', 'Receive notification when a new blog is created.');
define ('_MI_POPNUPBLOG_GLOBAL_NEWBLOG_NOTIFYSBJ', '[{X_SITENAME}] {X_MODULE} auto-notify : New blog');

define ('_MI_POPNUPBLOG_GLOBAL_NEWPOST_NOTIFY', 'New Post');
define ('_MI_POPNUPBLOG_GLOBAL_NEWPOST_NOTIFYCAP', 'Notify me of any new posts.');
define ('_MI_POPNUPBLOG_GLOBAL_NEWPOST_NOTIFYDSC', 'Receive notification when any new message is posted.');
define ('_MI_POPNUPBLOG_GLOBAL_NEWPOST_NOTIFYSBJ', '[{X_SITENAME}] {X_MODULE} auto-notify : New post');

define ('_MI_POPNUPBLOG_GLOBAL_NEWFULLPOST_NOTIFY', 'New Post (Full Text)');
define ('_MI_POPNUPBLOG_GLOBAL_NEWFULLPOST_NOTIFYCAP', 'Notify me of any new posts (include full text in message).');
define ('_MI_POPNUPBLOG_GLOBAL_NEWFULLPOST_NOTIFYDSC', 'Receive full text notification when any new message is posted.');
define ('_MI_POPNUPBLOG_GLOBAL_NEWFULLPOST_NOTIFYSBJ', '[{X_SITENAME}] {X_MODULE} auto-notify : New post (full text)');

define ('_MI_POPNUPBLOG_GLOBAL_NEWCOMMENT_NOTIFY', 'New comment (Full Text)');
define ('_MI_POPNUPBLOG_GLOBAL_NEWCOMMENT_NOTIFYCAP', 'Notify me of any new comments (include full text in message).');
define ('_MI_POPNUPBLOG_GLOBAL_NEWCOMMENT_NOTIFYDSC', 'Receive full text notification when any new message is commented.');
define ('_MI_POPNUPBLOG_GLOBAL_NEWCOMMENT_NOTIFYSBJ', '[{X_SITENAME}] {X_MODULE} auto-notify : New comment (full text)');

?>
