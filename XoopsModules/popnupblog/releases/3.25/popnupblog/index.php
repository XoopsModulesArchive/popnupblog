<?php
// $Id: index.php,v 2.42 2006/05/22 14:59:56 yoshis Exp $
//  ------------------------------------------------------------------------ //
//                  Copyright (c) Yoshi.Sakai @ Bluemoon inc.                //
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

require('header.php');
require_once XOOPS_ROOT_PATH.'/modules/popnupblog/class/PopnupBlogUtils.php';

$params = PopnupBlogUtils::getDateFromHttpParams();
$start = PopnupBlogUtils::getStartFromHttpParams();
$view = $BlogCNF['default_view'];
$select_uid = isset($_GET['uid']) ? intval($_GET['uid']) : 0;
//print_r($params);
if($params['trackback']) {
	$blog = new PopnupBlog($params['blogid'],$params['postid']);
	$tb = $blog->useTrackBack();
	if($tb == false){
		redirect_header(XOOPS_URL.'/',1,_MD_POPNUPBLOG_INTERNALERROR);
		exit();
	}
	$result = $blog->getBlogData($params['postid'],$params['year'],$params['month'],$params['date']);
	$xoopsOption['template_main'] = 'popnupblog_trackback.html';
	$xoopsTpl->assign('url', "index.php?postid=".$params['postid']);
	$xoopsTpl->assign('mt_tb_url', PopnupBlogUtils::makeTrackBackURL($params['postid']));
	$xoopsTpl->assign('xoops_module_header', '<link rel="alternate" type="application/rss+xml" title="RSS" href="'.PopnupBlogUtils::createRssURL($params['blogid']).'">');
} elseif($params) {
	$xoopsTpl->assign('popimg',PopnupBlogUtils::mail_popimg());		// get email
	$blog = new PopnupBlog($params['blogid'],$params['postid']);
	// init
	if (!$params['blogid']) $params['blogid'] = $blog->blogid;
	$popnupblog_editable = false;
	if ($xoopsUser){
		if($blog->canWrite($params['blogid']) || $blog->blogUid==$xoopsUser->uid() || $xoopsUser->isAdmin()){
			$popnupblog_editable = true;
		}
	}
	$xoopsTpl->assign('popnupblog_editable', $popnupblog_editable);
	$xoopsTpl->assign('popnupblog_commentable', false);
	$xoopsTpl->assign('params', $params['params']);
	$result = array();
	$result_max_num = (
		PopnupBlogUtils::isCompleteDate($params) && array_key_exists('month', $params) ) ? 31 : POPNUPBLOG_VIEW_LIST_NUM;
	$vote = isset($params['vote']) ? $params['vote'] : 0;
	$result = $blog->getBlogData($params['postid'],$params['year'],$params['month'],$params['date'],$params['command'],$start,$vote);
	if(!empty($result['blog']))
		$xoopsTpl->assign('popnupblog_blogdata', $result['blog']);
	if (!empty($xoopsUser))
		$xoopsTpl->assign('popnupblog_admin', $xoopsUser->isAdmin());
	$xoopsTpl->assign('popnupblog', $result);
	$xoopsTpl->assign('blog_title', $blog->getTitle());
	$xoopsTpl->assign('blog_desc',str_replace('{X_SITEURL}',XOOPS_URL,$blog->getBlogdesc()));
	$xoopsTpl->assign('blog_uid', $blog->blogUid);
	$xoopsTpl->assign('blog_uname', $blog->getTargetUname());
	$xoopsTpl->assign('popnupblog_targetUid', isset($params['uid']) ? $params['uid'] : 0);
	$xoopsTpl->assign('popnupblog_targetBid', $blog->blogid);
	$xoopsTpl->assign('popnupblog_index', $blog->getBlogIndex());
	if(!empty($result['jump_url']))
		$xoopsTpl->assign('jump_url', $result['jump_url']);
	if(!empty($result['today']))
		$xoopsTpl->assign('popnupblog_today', $result['today']);
	if($blog->canRead()){
		$xoopsTpl->assign('popnupblog_user_rss', PopnupBlogUtils::createRssURL($params['blogid']));
	}
	$show_name = PopnupBlogUtils::getXoopsModuleConfig('show_name');
	if ($xoopsUser){
		if ( $show_name==1 && (trim(users::realname($xoopsUser->uid()))!='') )
			$uname = users::realname($xoopsUser->uid());
		else
			$uname = $xoopsUser->uname();
	}
	if($blog->canComment($params['blogid'])){
		$xoopsTpl->assign('popnupblog_commentable', true);
		if($xoopsUser){
			$xoopsTpl->assign('popnupblog_uid', $xoopsUser->uid());
			$xoopsTpl->assign('popnupblog_uname', $uname);
		}
	}
	if($blog->canVote($params['blogid'])){
		$xoopsTpl->assign('popnupblog_votable', true);
		if($xoopsUser){
			$xoopsTpl->assign('popnupblog_uid', $xoopsUser->uid());
			$xoopsTpl->assign('popnupblog_uname', $uname);
		}
	}
	$blog->recieve_trackback_ping($params);
	if($params['postid']){
		$xoopsTpl->assign('trackbacks', $blog->getTrackBack($params['postid']));
	}
	if (isset($_GET['view'])) $view = intval($_GET['view']);
	$xoopsOption['template_main'] = 'popnupblog_view.html';
	$mh = '';
	$mh .= '<link rel="alternate" type="application/rss+xml" title="RSS" href="'.PopnupBlogUtils::createRssURL($params['blogid']).'">'."\n";
	$mh .= '<link rel="start" href="'.PopnupBlogUtils::createUrl($params['blogid']).'" title="Home" />'."\n";
	$xoopsTpl->assign('xoops_module_header', $mh);
	// $xoopsTpl->assign('xoops_module_header', '<link rel="alternate" type="application/rss+xml" title="RSS" href="'.PopnupBlogUtils::createRssURL($params['blogid']).'">');
	// <link rel="start" href="http://el30.sub.jp/" title="Home" />
	// $xoopsTpl->assign('popnupblog_home_url', PopnupBlogUtils::createUrl($params['blogid']));
}else{
//	$popimg = PopnupBlogUtils::mail_popimg();
//	print($popimg);
	$xoopsTpl->assign('popimg',PopnupBlogUtils::mail_popimg());		// get email
	$cat_id=0;
	if (isset($_GET['cat_id'])) $cat_id = intval($_GET['cat_id']);
	if (isset($_POST['cat_id'])) $cat_id = intval($_POST['cat_id']);
	$xoopsTpl->assign('popnupblog', PopnupBlogUtils::get_blog_list($start,$cat_id,$select_uid));
	if (isset($_GET['view'])) $view = intval($_GET['view']);
	if (isset($_POST['view'])) $view = intval($_POST['view']);
	$xoopsTpl->assign('view',$view);
	$rssUrl = XOOPS_URL.'/modules/popnupblog/rss.php';
	$xoopsTpl->assign('popnupblog_rss_url', $rssUrl);
	$categories = category::get_categories();
	$xoopsTpl->assign('categories', PopnupBlogUtils::mkselect('cat_id',$categories,$cat_id));
	$jump_url = PopnupBlogUtils::mk_list_url($cat_id,$view);
	$xoopsTpl->assign('jump_url', $jump_url);
	$xoopsOption['template_main'] = 'popnupblog_list.html';
	$xoopsTpl->assign('xoops_module_header', '<link rel="alternate" type="application/rss+xml" title="RSS" href="'.$rssUrl.'">');
}
//
// For MarkItUp, LightBox plugins
//
$xoops_module_header = 
  '<link rel="stylesheet" type="text/css" media="screen,tv,print" href="style.css" />
  <!-- jQuery -->
  <script type="text/javascript" src="js/jquery-1.2.6.min.js"></script>
  <!-- markItUp! -->
  <script type="text/javascript" src="js/markitup/jquery.markitup.pack.js"></script>
  <!-- markItUp! toolbar settings -->
  <script type="text/javascript" src="js/markitup/sets/bbcode/set.js"></script>
  <!-- markItUp! skin -->
  <link rel="stylesheet" type="text/css" href="js/markitup/skins/simple/style.css" />
  <!--  markItUp! toolbar skin -->
  <link rel="stylesheet" type="text/css" href="js/markitup/sets/bbcode/style.css" />
  <!-- For JQuery lightbox plugin -->
  <link href="js/jquery.lightbox-0.5.css" type="text/css" rel="stylesheet" />
  <script type="text/javascript" src="js/jquery.lightbox-0.5.min.js"></script>
  <script type="text/javascript">  
  $(function() {
    $(\'a[@href$=".jpeg"], a[@href$=".jpg"], a[@href$=".gif"], a[@href$=".png"], a[@href$=".JPEG"], a[@href$=".JPG"], a[@href$=".GIF"], a[@href$=".PNG"]\').lightBox();
  });
  </script>
';
//
$xoopsTpl->assign( 'xoops_module_header', $xoops_module_header);

require('footer.php');

?>
