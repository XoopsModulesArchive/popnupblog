<?php
// $Id$
//  ------------------------------------------------------------------------ //
//                               Popnup Blog                                 //
//                    Copyright (c) 2005 - 2009 bluemoon inc.                //
//                       <http://www.bluemooninc.biz/>                       //
// ------------------------------------------------------------------------- //
//  This program is free software; you can redistribute it and/or modify     //
//  it under the terms of the GNU General Public License as published by     //
//  the Free Software Foundation; either version 2 of the License, or        //
//  (at your option) any later version.                                      //
//                                                                           //
//  You may not change or alter any portion of this comment or credits       //
//  of supporting developers from this source code or any supporting         //
//  source code which is considered copyrighted (c) material of the          //
//  original comment or credit authors.                                      //
//                                                                           //
//  This program is distributed in the hope that it will be useful,          //
//  but WITHOUT ANY WARRANTY; without even the implied warranty of           //
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            //
//  GNU General Public License for more details.                             //
//                                                                           //
//  You should have received a copy of the GNU General Public License        //
//  along with this program; if not, write to the Free Software              //
//  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 USA //
//  ------------------------------------------------------------------------ //

$modversion['name'] = _MI_POPNUPBLOG_NAME;
$modversion['version'] = "3.25";
$modversion['description'] = _MI_POPNUPBLOG_DESC;
$modversion['credits'] = "Bluemoon inc.";
$modversion['author'] = "Yoshi Sakai";
$modversion['help'] = "help.html";
$modversion['license'] = "GPL see LICENSE";
$modversion['official'] = 0;
$modversion['image'] = "popnupblog.gif";
$modversion['dirname'] = "popnupblog";

$modversion['sqlfile']['mysql'] = "sql/mysql.sql";
$modversion['tables'][0] = "popnupblog_info";
$modversion['tables'][1] = "popnupblog";
$modversion['tables'][2] = "popnupblog_comment";
$modversion['tables'][3] = "popnupblog_application";
$modversion['tables'][4] = "popnupblog_trackback";
$modversion['tables'][5] = "popnupblog_emailalias";
$modversion['tables'][6] = "popnupblog_categories";

//XOOPS Buit-in comment
//$modversion['hasComments'] = 1;
//$modversion['comments']['itemName'] = 'param';
//$modversion['comments']['pageName'] = 'index.php';

//Admin things
$modversion['hasAdmin'] = 1;
$modversion['adminindex'] = "admin/index.php";
$modversion['adminmenu'] = "admin/menu.php";

// Menu
$modversion['hasMain'] = 1;
$modversion['sub'][1]['name'] = _MI_POPNUPBLOG_WRITE;
$modversion['sub'][1]['url'] = "edit.php?today=on";
$modversion['sub'][2]['name'] = _MI_POPNUPBLOG_PREFERENCE;
$modversion['sub'][2]['url'] = "edit.php?today=preference";
$modversion['sub'][3]['name'] = _MI_POPNUPBLOG_APPLY;
$modversion['sub'][3]['url'] = "edit.php?today=apply";

// search
$modversion['hasSearch'] = 1;
$modversion['search']['file'] = "search.inc.php";
$modversion['search']['func'] = "popnupblog_search";

// config
// 2004.10.27 Add by Y.Sakai
$modversion['config'][] = array(
	'name'        => 'FILE_CHARSET',
	'title'       => '_MI_POPNUPBLOG_FILECHRSET',
	'description' => '_MI_POPNUPBLOG_FILECHRSET_DESC',
	'formtype'    => 'textbox',
	'valuetype'   => 'text',
	'default'     => 'ASCII'
);
$modversion['config'][] = array(
	'name'        => 'MAILSERVER',
	'title'       => '_MI_POPNUPBLOG_MAILSERVER',
	'description' => '_MI_POPNUPBLOG_MAILSERVER_DESC',
	'formtype'    => 'textbox',
	'valuetype'   => 'text',
	'default'     => null
);
$modversion['config'][] = array(
	'name'        => 'MAILUSER',
	'title'       => '_MI_POPNUPBLOG_MAILUSER',
	'description' => '_MI_POPNUPBLOG_MAILUSER_DESC',
	'formtype'    => 'textbox',
	'valuetype'   => 'text',
	'default'     => null
);
$modversion['config'][] = array(
	'name'        => 'MAILPWD',
	'title'       => '_MI_POPNUPBLOG_MAILPWD',
	'description' => '_MI_POPNUPBLOG_MAILPWD_DESC',
	'formtype'    => 'textbox',
	'valuetype'   => 'text',
	'default'     => null
);
$modversion['config'][] = array(
	'name'        => 'MAILADDR',
	'title'       => '_MI_POPNUPBLOG_MAILADDR',
	'description' => '_MI_POPNUPBLOG_MAILADDR_DESC',
	'formtype'    => 'textbox',
	'valuetype'   => 'text',
	'default'     => null
);
$modversion['config'][] = array(
	'name'        => 'guestpost_blogid',
	'title'       => '_MI_POPNUPBLOG_GUESTBLOGID',
	'description' => '',
	'formtype'    => 'textbox',
	'valuetype'   => 'int',
	'default'     => null
);
// 2004.10.27 End of Add
$modversion['config'][] = array(
	'name'        => 'blog_description',
	'title'       => '_MI_POPNUPBLOG_CONF_DESC',
	'description' => '_MI_POPNUPBLOG_CONFIG_RSS_DESC',
	'formtype'    => 'textbox',
	'valuetype'   => 'text',
	'default'     => _MI_POPNUPBLOG_CONFIG_RSS_DEF
);
$modversion['config'][] = array(
	'name'        => 'show_name',
	'title'       => '_MI_POPNUPBLOG_SHOWNAME',
	'description' => '',
	'formtype'    => 'yesno',
	'valuetype'   => 'int',
	'default'     => 0
);
$modversion['config'][] = array(
	'name'        => 'POPNUPBLOG_APPL',
	'title'       => '_MI_POPNUPBLOG_APPL_OK',
	'description' => '_MI_POPNUPBLOG_APPL_DESC',
	'formtype'    => 'select',
	'valuetype'   => 'int',
	'default'     => 0,
	'options'     => array(_MI_POPNUPBLOG_APPL_ALLOW => 0, _MI_POPNUPBLOG_APPL_DENY => 1)
);
$modversion['config'][] = array(	// 2006.03.21 Add by Yoshi
	'name'        => 'GroupSetByUser',
	'title'       => '_MI_POPNUPBLOG_GROUPSETBYUSER',
	'description' => '_MI_POPNUPBLOG_GROUPSETBYUSER_DESC',
	'formtype'    => 'yesno',
	'valuetype'   => 'int',
	'default'     => 1
);
$modversion['config'][] = array(	// 2005.01.19 Add by Yoshi
	'name'        => 'new_user_notify',
	'title'       => '_MI_POPNUPBLOG_NEWUNOTIFY',
	'description' => '',
	'formtype'    => 'yesno',
	'valuetype'   => 'int',
	'default'     => 1
);
$modversion['config'][] = array(
	'name'        => 'activation_type',
	'title'       => '_MI_POPNUPBLOG_ACTVTYPE',
	'description' => '',
	'formtype'    => 'select',
	'valuetype'   => 'int',
	'default'     => 1,
	'options'     => array(_MI_POPNUPBLOG_AUTOACTV => 0, _MI_POPNUPBLOG_ADMINACTV => 1)
	
);
// 2005.01.19 End of Add
$modversion['config'][] = array(
	'name'        => 'POPNUPBLOG_REWRITE',
	'title'       => '_MI_POPNUPBLOG_REWRITE_TITLE',
	'description' => '_MI_POPNUPBLOG_REWRITE_DESC',
	'formtype'    => 'select',
	'valuetype'   => 'int',
	'default'     => 0,
	'options'     => array(_MI_POPNUPBLOG_UNUSE_REWRITE => 0, _MI_POPNUPBLOG_USE_REWRITE => 1)
);

$modversion['config'][] = array(
	'name'        => 'POPNUPBLOG_TRACKBACK',
	'title'       => '_MI_POPNUPBLOG_TRACKBACK',
	'description' => '_MI_POPNUPBLOG_TRACKBACK_DESC',
	'formtype'    => 'select',
	'valuetype'   => 'int',
	'default'     => 0,
	'options'     => array(_MI_POPNUPBLOG_UNUSE_TRACKBACK => 0, _MI_POPNUPBLOG_USE_TRACKBACK => 1)
);

$modversion['config'][] = array(
	'name'        => 'POPNUPBLOG_UPDATE_PING',
	'title'       => '_MI_POPNUPBLOG_UPDATE_PING',
	'description' => '_MI_POPNUPBLOG_UPDATE_PING_DESC',
	'formtype'    => 'select',
	'valuetype'   => 'int',
	'default'     => 0,
	'options'     => array(_MI_POPNUPBLOG_UNUSE_UPDATE_PING => 0, _MI_POPNUPBLOG_USE_UPDATE_PING => 1)
);



// Blocks
$modversion['blocks'][1]['file'] = "popnupblog_top.php";
$modversion['blocks'][1]['name'] = _MI_POPNUPBLOG_NAME;
$modversion['blocks'][1]['description'] = _MI_POPNUPBLOG_DESC;
$modversion['blocks'][1]['show_func'] = "b_popnupblog_show";
$modversion['blocks'][1]['edit_func'] = "b_popnupblog_edit";
$modversion['blocks'][1]['options'] = "1";
$modversion['blocks'][1]['template'] = 'popnupblog_block.html';

$modversion['blocks'][2]['file'] = "popnupblog_top.php";
$modversion['blocks'][2]['name'] = _MI_POPNUPBLOG_1_LINE;
$modversion['blocks'][2]['description'] = _MI_POPNUPBLOG_1_LINE_DESC;
$modversion['blocks'][2]['show_func'] = "b_popnupblog_show";
$modversion['blocks'][2]['edit_func'] = "b_popnupblog_edit";
$modversion['blocks'][2]['options'] = "1";
$modversion['blocks'][2]['template'] = 'popnupblog_block_1.html';

$modversion['blocks'][3]['file'] = "popnupblog_top.php";
$modversion['blocks'][3]['name'] = _MI_POPNUPBLOG_APPL_WAITING_TITLE;
$modversion['blocks'][3]['description'] = _MI_POPNUPBLOG_APPL_WAITING_TITLE;
$modversion['blocks'][3]['show_func'] = "b_popnupblog_wait_appl";
$modversion['blocks'][3]['template'] = 'popnupblog_block_wait.html';


$modversion['templates'][] = array(
	'file'        => 'popnupblog_list.html',
	'description' => 'Blog List'
);
$modversion['templates'][] = array(
	'file'        => 'popnupblog_info.html',
	'description' => 'Blog Infomation'
);
$modversion['templates'][] = array(
	'file'        => 'popnupblog_view.html',
	'description' => 'ViewBlog'
);
$modversion['templates'][] = array(
	'file'        => 'popnupblog_submit.html',
	'description' => 'Submit Blog'
);
$modversion['templates'][] = array(
	'file'        => 'popnupblog_ml.html',
	'description' => 'ML Reception'
);

$modversion['templates'][] = array(
	'file'        => 'popnupblog_rss.html',
	'description' => 'PopnupBlog RSS'
);
$modversion['templates'][] = array(
	'file'        => 'popnupblog_blogrss.html',
	'description' => 'PopnupBlog blog RSS'
);


$modversion['templates'][] = array(
	'file'        => 'popnupblog_application.html',
	'description' => 'Application for PopnupBlog'
);

$modversion['templates'][] = array(
	'file'        => 'popnupblog_trackback.html',
	'description' => 'TrackBack for PopnupBlog'
);

$modversion['hasNotification'] = 1;
$modversion['notification']['lookup_file'] = 'include/notification.inc.php';
$modversion['notification']['lookup_func'] = 'popnupblog_notify_iteminfo';

$modversion['notification']['category'][1]['name'] = 'global';
$modversion['notification']['category'][1]['title'] = _MI_POPNUPBLOG_GLOBAL_NOTIFY;
$modversion['notification']['category'][1]['description'] = _MI_POPNUPBLOG_GLOBAL_NOTIFYDSC;
$modversion['notification']['category'][1]['subscribe_from'] = array('index.php');

$modversion['notification']['category'][2]['name'] = 'blog';
$modversion['notification']['category'][2]['title'] = _MI_POPNUPBLOG_BLOG_NOTIFY;
$modversion['notification']['category'][2]['description'] = _MI_POPNUPBLOG_BLOG_NOTIFYDSC;
$modversion['notification']['category'][2]['subscribe_from'] = array('index.php');
$modversion['notification']['category'][2]['item_name'] = 'param';
$modversion['notification']['category'][2]['allow_bookmark'] = 1;

$modversion['notification']['event'][1]['name'] = 'new_post';
$modversion['notification']['event'][1]['category'] = 'blog';
$modversion['notification']['event'][1]['title'] = _MI_POPNUPBLOG_BLOG_NEWPOST_NOTIFY;
$modversion['notification']['event'][1]['caption'] = _MI_POPNUPBLOG_BLOG_NEWPOST_NOTIFYCAP;
$modversion['notification']['event'][1]['description'] = _MI_POPNUPBLOG_BLOG_NEWPOST_NOTIFYDSC;
$modversion['notification']['event'][1]['mail_template'] = 'blog_newpost_notify';
$modversion['notification']['event'][1]['mail_subject'] = _MI_POPNUPBLOG_BLOG_NEWPOST_NOTIFYSBJ;

$modversion['notification']['event'][2]['name'] = 'new_fullpost';
$modversion['notification']['event'][2]['category'] = 'blog';
$modversion['notification']['event'][2]['title'] = _MI_POPNUPBLOG_BLOG_NEWFULLPOST_NOTIFY;
$modversion['notification']['event'][2]['caption'] = _MI_POPNUPBLOG_BLOG_NEWFULLPOST_NOTIFYCAP;
$modversion['notification']['event'][2]['description'] = _MI_POPNUPBLOG_BLOG_NEWFULLPOST_NOTIFYDSC;
$modversion['notification']['event'][2]['mail_template'] = 'global_newfullpost_notify';
$modversion['notification']['event'][2]['mail_subject'] = _MI_POPNUPBLOG_BLOG_NEWFULLPOST_NOTIFYSBJ;

$modversion['notification']['event'][3]['name'] = 'new_comment';
$modversion['notification']['event'][3]['category'] = 'blog';
$modversion['notification']['event'][3]['title'] = _MI_POPNUPBLOG_BLOG_NEWCOMMENT_NOTIFY;
$modversion['notification']['event'][3]['caption'] = _MI_POPNUPBLOG_BLOG_NEWCOMMENT_NOTIFYCAP;
$modversion['notification']['event'][3]['description'] = _MI_POPNUPBLOG_BLOG_NEWCOMMENT_NOTIFYDSC;
$modversion['notification']['event'][3]['mail_template'] = 'global_newcomment_notify';
$modversion['notification']['event'][3]['mail_subject'] = _MI_POPNUPBLOG_BLOG_NEWCOMMENT_NOTIFYSBJ;

$modversion['notification']['event'][4]['name'] = 'new_blog';
$modversion['notification']['event'][4]['category'] = 'global';
$modversion['notification']['event'][4]['title'] = _MI_POPNUPBLOG_GLOBAL_NEWBLOG_NOTIFY;
$modversion['notification']['event'][4]['caption'] = _MI_POPNUPBLOG_GLOBAL_NEWBLOG_NOTIFYCAP;
$modversion['notification']['event'][4]['description'] = _MI_POPNUPBLOG_GLOBAL_NEWBLOG_NOTIFYDSC;
$modversion['notification']['event'][4]['mail_template'] = 'global_newblog_notify';
$modversion['notification']['event'][4]['mail_subject'] = _MI_POPNUPBLOG_GLOBAL_NEWBLOG_NOTIFYSBJ;

$modversion['notification']['event'][5]['name'] = 'new_post';
$modversion['notification']['event'][5]['category'] = 'global';
$modversion['notification']['event'][5]['title'] = _MI_POPNUPBLOG_GLOBAL_NEWPOST_NOTIFY;
$modversion['notification']['event'][5]['caption'] = _MI_POPNUPBLOG_GLOBAL_NEWPOST_NOTIFYCAP;
$modversion['notification']['event'][5]['description'] = _MI_POPNUPBLOG_GLOBAL_NEWPOST_NOTIFYDSC;
$modversion['notification']['event'][5]['mail_template'] = 'global_newpost_notify';
$modversion['notification']['event'][5]['mail_subject'] = _MI_POPNUPBLOG_GLOBAL_NEWPOST_NOTIFYSBJ;

$modversion['notification']['event'][6]['name'] = 'new_fullpost';
$modversion['notification']['event'][6]['category'] = 'global';
$modversion['notification']['event'][6]['admin_only'] = 1;
$modversion['notification']['event'][6]['title'] = _MI_POPNUPBLOG_GLOBAL_NEWFULLPOST_NOTIFY;
$modversion['notification']['event'][6]['caption'] = _MI_POPNUPBLOG_GLOBAL_NEWFULLPOST_NOTIFYCAP;
$modversion['notification']['event'][6]['description'] = _MI_POPNUPBLOG_GLOBAL_NEWFULLPOST_NOTIFYDSC;
$modversion['notification']['event'][6]['mail_template'] = 'global_newfullpost_notify';
$modversion['notification']['event'][6]['mail_subject'] = _MI_POPNUPBLOG_GLOBAL_NEWFULLPOST_NOTIFYSBJ;

$modversion['notification']['event'][7]['name'] = 'new_comment';
$modversion['notification']['event'][7]['category'] = 'global';
$modversion['notification']['event'][7]['admin_only'] = 1;
$modversion['notification']['event'][7]['title'] = _MI_POPNUPBLOG_GLOBAL_NEWCOMMENT_NOTIFY;
$modversion['notification']['event'][7]['caption'] = _MI_POPNUPBLOG_GLOBAL_NEWCOMMENT_NOTIFYCAP;
$modversion['notification']['event'][7]['description'] = _MI_POPNUPBLOG_GLOBAL_NEWCOMMENT_NOTIFYDSC;
$modversion['notification']['event'][7]['mail_template'] = 'global_newcomment_notify';
$modversion['notification']['event'][7]['mail_subject'] = _MI_POPNUPBLOG_GLOBAL_NEWCOMMENT_NOTIFYSBJ;
?>
