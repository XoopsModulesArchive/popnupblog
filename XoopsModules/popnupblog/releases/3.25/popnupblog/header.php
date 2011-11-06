<?php
// $Id$
require('../../mainfile.php');
if(
	!defined('XOOPS_ROOT_PATH') ||
	!is_file(XOOPS_ROOT_PATH.'/header.php') || 
	!is_file(XOOPS_ROOT_PATH.'/modules/popnupblog/class/popnupblog.php') ||
	!defined('XOOPS_CACHE_PATH') ||
	!is_dir(XOOPS_CACHE_PATH)
){

	exit();
}
require_once(XOOPS_ROOT_PATH.'/header.php');
require_once XOOPS_ROOT_PATH.'/modules/popnupblog/class/popnupblog.php';
if($xoopsTpl){
	PopnupBlogUtils::assign_message($xoopsTpl);
}
$xoopsTpl->assign('xoops_module_header', '<link rel="alternate" type="application/rss+xml" title="RSS" href="'.XOOPS_URL.'/modules/popnupblog/rss.php">');
?>