<?php
// $Id: rss.php,v 3.16 2007/11/09 16:36:10 yoshis Exp $

include '../../mainfile.php';
if(
	!defined('XOOPS_ROOT_PATH') ||
	!defined('XOOPS_CACHE_PATH') ||
	!is_file(XOOPS_ROOT_PATH.'/class/template.php') ||
	!is_file(XOOPS_ROOT_PATH.'/modules/popnupblog/class/popnupblog.php') 
){
	exit();
}
include_once XOOPS_ROOT_PATH.'/class/template.php';
require_once './include/sanitize.php';
include_once './class/popnupblog.php';
include_once "./class/mbstrings.php";

if(function_exists('mb_http_output')) { 
	mb_http_output('pass');
} 

header ('Content-Type: text/xml; charset=utf-8');

$params = PopnupBlogUtils::getDateFromHttpParams();
$generator =  $xoopsModule->getVar( 'name' ) . "&nbsp;v" . sprintf( "%2.2f" ,  $xoopsModule->getVar('version') / 100.0 ) ;
function to_urf8($text){
	// xoops_convert_encoding($text);
	return PopnupBlogUtils::convert_encoding($text, _CHARSET, 'utf-8');
}
if($params['blogid'] && $params['blogid'] > 0){
	$blog = new PopnupBlog($params['blogid']);
	$blogList = array();
	if($blog->canRead()){
		$blogList = $blog->getBlogData();
	}
	$tpl = new XoopsTpl();
	if (is_array($blogList)) {
		$tpl->assign('channel_title', to_urf8(htmlspecialchars($blog->getTitle(), ENT_QUOTES)));
		$tpl->assign('channel_link', PopnupBlogUtils::createUrl($blog->blogid));
		$tpl->assign('channel_desc', to_urf8(htmlspecialchars($blog->blog_desc, ENT_QUOTES)));
		$tpl->assign('channel_lastbuild', formatTimestamp(time(), 'rss'));
		$tpl->assign('channel_webmaster', to_urf8(htmlspecialchars($blog->targetUser->uname(), ENT_QUOTES)));
		$tpl->assign('channel_editor', to_urf8(htmlspecialchars($blog->targetUser->email(), ENT_QUOTES)));
		$tpl->assign('channel_category', to_urf8(htmlspecialchars(category::get_categoryname($blog->cat_id), ENT_QUOTES)));
		$tpl->assign('channel_generator', $generator);
		$tpl->assign('channel_language', _LANGCODE);
		$tpl->assign('image_url', XOOPS_URL.'/images/logo.gif');
		foreach ($blogList['blog'] as $b) {
			if ($GLOBALS['BlogCNF']['text_limit']>0)
				$post_text = mbstrings::_strcut($b['post_text'],0,$GLOBALS['BlogCNF']['text_limit']);
			$tpl->append('items', array(
				'title' => to_urf8(htmlspecialchars($b['title'], ENT_QUOTES)), 
				// 'link' => XOOPS_URL.'/modules/popnupblog/view.php?uid='.$b['uid'], 
				'link' => $b['url'], 
				'desc' => to_urf8(htmlspecialchars($post_text, ENT_QUOTES)), 
				'date' => htmlspecialchars($b['last_update4rss'], ENT_QUOTES),
				'uname' => to_urf8(htmlspecialchars($b['uname'], ENT_QUOTES))
			));
		}
	}
	$tpl->display('db:popnupblog_blogrss.html');
}else{
	$tpl = new XoopsTpl();
	$tpl->xoops_setTemplateDir(XOOPS_ROOT_PATH.'/cache');
	$tpl->xoops_setCaching(2);
	$tpl->xoops_setCacheTime(3600);
	if (!$tpl->is_cached('db:popnupblog_rss.html')) {
		$blogList = PopnupBlogUtils::get_blog_list();
		if (is_array($blogList)) {
			$tpl->assign('channel_title', to_urf8(htmlspecialchars($xoopsConfig['sitename'], ENT_QUOTES)));
			$tpl->assign('channel_link', XOOPS_URL.'/modules/popnupblog/');
			$tpl->assign('channel_desc', to_urf8(htmlspecialchars($xoopsModuleConfig['blog_description'], ENT_QUOTES)));
			$tpl->assign('channel_lastbuild', formatTimestamp(time(), 'rss'));
			$tpl->assign('channel_webmaster', $xoopsConfig['adminmail']);
			$tpl->assign('channel_editor', $xoopsConfig['adminmail']);
			$tpl->assign('channel_category', 'News');
			$tpl->assign('channel_generator', $generator);	//XOOPS_VERSION
			$tpl->assign('channel_language', _LANGCODE);
			$tpl->assign('image_url', XOOPS_URL.'/images/logo.gif');
			foreach ($blogList as $blog) {
				$tpl->append('items', array(
					'title' => to_urf8(htmlspecialchars($blog['title'], ENT_QUOTES)), 
					// 'link' => XOOPS_URL.'/modules/popnupblog/view.php?uid='.$blog['uid'], 
					'link' => $blog['url'], 
					'desc' => to_urf8(htmlspecialchars($blog['desc'], ENT_QUOTES)), 
					'date' => htmlspecialchars($blog['last_update4rss'], ENT_QUOTES),
					'uname' => to_urf8(htmlspecialchars($blog['uname'], ENT_QUOTES))
				));
			}
		}
	}
	$tpl->display('db:popnupblog_rss.html');
}
?>
