<?php
// $Id: create.php,v 3.0 2005/12/01 13:16:08 yoshis Exp $
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
include '../../../include/cp_header.php';
if(
	(!defined('XOOPS_ROOT_PATH')) || 
	(!is_object($xoopsUser)) || 
	(!$xoopsUser->isAdmin()) ){
	exit();
}
include_once '../conf.php';
require_once '../class/popnupblog.php';
/*********************************************************/
/* Users Functions                                       */
/*********************************************************/
include_once XOOPS_ROOT_PATH."/class/xoopslists.php";
include_once XOOPS_ROOT_PATH."/class/xoopsformloader.php";
include XOOPS_ROOT_PATH.'/class/pagenav.php';
xoops_cp_header();
include_once './adminmenu.php';
echo "&nbsp;<br />";
$groupnames = $member_handler->getGroupList(); 
$categories = array_merge(" ",category::get_categories());
global $xoopsDB, $xoopsConfig, $xoopsModule;
$userstart = isset($_GET['userstart']) ? intval($_GET['userstart']) : 0;
$member_handler =& xoops_gethandler('member');
$usercount = $member_handler->getUserCount();
$nav = new XoopsPageNav($usercount, 200, $userstart, "userstart", "fct=users");
$editform = new XoopsThemeForm(_AM_POPNUPBLOG_CREATE, "create", "index.php", 'POST');
$user_select = new XoopsFormSelect('', "uid");
$icq_text = new XoopsFormText("", "user_icq", 30, 100);
$icq_match = new XoopsFormSelectMatchOption("", "user_icq_match");
$blogtitle = new XoopsFormText(_AM_POPNUPBLOG_BLOG_TITLE, "title", 30, 100, "");
$category_select = new XoopsFormSelect(_AM_CATEGORY, 'cat_id', $categories);
$category_select->addOptionArray($categories);
$blogdesc = new XoopsFormTextArea(_AM_BLOGDESCRIPTION, "desc", "");
$criteria = new CriteriaCompo();
$criteria->setSort('uname');
$criteria->setOrder('ASC');
$criteria->setLimit(200);
$criteria->setStart($userstart);
$user_select->addOptionArray($member_handler->getUserList($criteria));
$user_select_tray = new XoopsFormElementTray(_AM_POPNUPBLOG_UNAME, "<br />");
$user_select_tray->addElement($user_select);
$user_select_nav = new XoopsFormLabel('', $nav->renderNav(4));
$user_select_tray->addElement($user_select_nav);
$submit_button = new XoopsFormButton("", "create", _SUBMIT, "submit");
$fct_hidden = new XoopsFormHidden("fct", "users");
$editform->addElement($user_select_tray);
$editform->addElement($op_select);
$editform->addElement($category_select);
$editform->addElement($blogtitle);
$editform->addElement($blogdesc);
$editform->addElement($submit_button);
$editform->addElement($fct_hidden);
$editform->display();
xoops_cp_footer();
exit();    
