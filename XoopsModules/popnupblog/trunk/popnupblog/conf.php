<?php
// $Id: conf.php,v 3.00 2006/12/25 10:55:19 yoshis Exp $
//  ------------------------------------------------------------------------ //
//                Copyright (c) 2005 Yoshi.Sakai @ Bluemoon inc.             //
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
global $xoopsConfig,$xoopsModule;

if(
	!defined('XOOPS_ROOT_PATH') ||
	!defined('XOOPS_CACHE_PATH') ||
	!is_dir(XOOPS_CACHE_PATH)
){
	exit();
}
include_once XOOPS_ROOT_PATH.'/language/'.$xoopsConfig['language'].'/calendar.php';

// You shouldn't have to change any of these
popnupblog_init();
function popnupblog_init(){
	global $xoopsDB;
	define('POPNUPBLOG_VERSION', 'V3 Denali');
	define('POPNUPBLOG_DIR_NAME', 'popnupblog');
	define('POPNUPBLOG_DIR', XOOPS_URL.'/modules/'.POPNUPBLOG_DIR_NAME.'/');
	define('POPNUPBLOG_BLOCK_LIST_NUM',10);
	define('POPNUPBLOG_BLOCK_LASTDAYS',150);
	define('POPNUPBLOG_VIEW_LIST_NUM',10);
	define('POPNUPBLOG_MAIN_LIST_NUM',50);
	define('POPNUPBLOG_DEBUG_OUT', 1);	// Modified by hoshiyan@hoshiba-farm.com 2004.8.4
	define('PBTBL_BLOG', $xoopsDB->prefix('popnupblog'));
	define('PBTBL_INFO', $xoopsDB->prefix('popnupblog_info'));
	define('PBTBL_COMMENT', $xoopsDB->prefix('popnupblog_comment'));
	define('PBTBL_APPL', $xoopsDB->prefix('popnupblog_application'));
	define('PBTBL_TRACKBACK', $xoopsDB->prefix('popnupblog_trackback'));
	define('PBTBL_EMAILALIAS', $xoopsDB->prefix('popnupblog_emailalias'));
	define('PBTBL_CATEGORIES', $xoopsDB->prefix('popnupblog_categories'));
	// option for cgi user (modifed by Yoshi.S)
	define('POPNUPBLOG_TRACKBACK_URI_SEP', '/');
	define('POPNUPBLOG_REQUEST_URI_SEP', '?param=');
	//
	// After v1.46 yoshis 2004.9.9
	//
	global $weekday, $d_month;
	// the weekdays and the months.. using XOOPS_ROOT_PATH/languages/calender.php
	$weekday[0]=_CAL_SUNDAY;
	$weekday[1]=_CAL_MONDAY;
	$weekday[2]=_CAL_TUESDAY;
	$weekday[3]=_CAL_WEDNESDAY;
	$weekday[4]=_CAL_THURSDAY;
	$weekday[5]=_CAL_FRIDAY;
	$weekday[6]=_CAL_SATURDAY;
	// the months.
	$d_month['01']=_CAL_JANUARY;
	$d_month['02']=_CAL_FEBRUARY;
	$d_month['03']=_CAL_MARCH;
	$d_month['04']=_CAL_APRIL;
	$d_month['05']=_CAL_MAY;
	$d_month['06']=_CAL_JUNE;
	$d_month['07']=_CAL_JULY;
	$d_month['08']=_CAL_AUGUST;
	$d_month['09']=_CAL_SEPTEMBER;
	$d_month['10']=_CAL_OCTOBER;
	$d_month['11']=_CAL_NOVEMBER;
	$d_month['12']=_CAL_DECEMBER;
    if (!defined('STATUS_CANREAD_MODERATOR')) {
        define('STATUS_CANREAD_MODERATOR',  0x01);
        define('STATUS_CANREAD_MEMBER',  0x02);
        define('STATUS_CANREAD_GUEST',  0x03);
        define('STATUS_CANCOMMENT_MODERATOR',  0x04);
        define('STATUS_CANCOMMENT_MEMBER',  0x08);
        define('STATUS_CANCOMMENT_GUEST',  0x0c);
        define('STATUS_CANVOTE_MODERATOR',  0x10);
        define('STATUS_CANVOTE_MEMBER',  0x20);
        define('STATUS_CANVOTE_GUEST',  0x30);
    }
}
?>
