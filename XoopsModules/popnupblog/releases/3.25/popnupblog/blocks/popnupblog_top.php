<?php
// $Id: popnupblog_top.php,v 1.1.1.1 2005/08/28 02:13:08 yoshis Exp $
if(!defined('XOOPS_ROOT_PATH')){
	exit();
}
include_once XOOPS_ROOT_PATH.'/modules/popnupblog/class/PopnupBlogUtils.php';
include_once XOOPS_ROOT_PATH.'/modules/popnupblog/pop.ini.php';

function b_popnupblog_wait_appl($options){
	global $xoopsUser;
	$result = array();
	PopnupBlogUtils::assign_message($result);
	if($xoopsUser && ($xoopsUser->isAdmin())){
		$result['popnupblog_applicationNum'] = PopnupBlogUtils::getApplicationNum();	
	}
	return $result;
}

function b_popnupblog_show($options) {
	global $xoopsUser;
	$result = array();
	PopnupBlogUtils::assign_message($result);
	$showNums = isset($options[1]) ? $options[1] : 10;
	$showDays = isset($options[2]) ? $options[2] : 90;
	$result['popnupblog'] = PopnupBlogUtils::get_blog_list(0,0,0,$showNums,$showDays);
	$result['show_rss'] = ($options[0] == 1) ? 1 : 0;
	/*
	$result['blogTitle'] = _MB_POPNUPBLOG_BLOG_TITLE;
	$result['unameTitle'] = _MB_POPNUPBLOG_BLOGGER_NAME;
	$result['lastUpdateTitle'] = _MB_POPNUPBLOG_UPDATE_DATE;
	*/
	return $result;
}

function b_popnupblog_edit($options){
	$checked = array();
	$checked[0] = ($options[0] == 1) ? ' selected' : '';
	$checked[1] = ($checked[0] == '') ? ' selected' : '';
	$form = '';
	$form .= _MB_POPNUPBLOG_SHOW_RSS_LINK." :";
	$form .= "<select name='options[0]'>\n";
	$form .= "<option value='1'".$checked[0].">"._MB_POPNUPBLOG_YES."</option>\n";
	$form .= "<option value='0'".$checked[1].">"._MB_POPNUPBLOG_NO."</option>\n";
	$form .= "</select>\n";
	if (!isset($options[1])) $options[1] = POPNUPBLOG_BLOCK_LIST_NUM;
	if (!isset($options[2])) $options[2] = POPNUPBLOG_BLOCK_LASTDAYS;
    $form .= "<br>" . _MB_POPNUPBLOG_SHOW_LASTCOUNT . "&nbsp;<input type='text' name='options[]' value='" . $options[1] . "' />";
    $form .= "<br>" . _MB_POPNUPBLOG_SHOW_LASTDAYS . "&nbsp;<input type='text' name='options[]' value='" . $options[2] . "' />";
	return $form;
}
?>
