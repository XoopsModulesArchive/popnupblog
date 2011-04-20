<?php
// $Id: search.inc.php,v 1.4 2007/10/03 12:20:08 yoshis Exp $
if(!defined('XOOPS_ROOT_PATH')){
	exit();
}
global $xoopsConfig;
include_once XOOPS_ROOT_PATH.'/modules/popnupblog/class/popnupblog.php';
include_once XOOPS_ROOT_PATH.'/modules/popnupblog/class/PopnupBlogUtils.php';

function popnupblog_search($queryarray, $andor, $limit, $offset, $userid = -1){
	global $xoopsDB, $xoopsUser;
	$sql = 'select b.blogid, b.title, b.postid, UNIX_TIMESTAMP(b.blog_date) ';
	$sql .= 'FROM '.$xoopsDB->prefix("popnupblog_info")." info LEFT JOIN ".$xoopsDB->prefix("popnupblog")." b ON info.blogid=b.blogid"
		. " LEFT JOIN " . $xoopsDB->prefix("popnupblog_comment")." c ON c.postid=b.postid";
	
	$i = 0;
	if($userid > 0){
		$sql .= " WHERE b.uid = ".$userid;
	}else{
		$sql .= ' WHERE ';
		$wstr = "";
		foreach ( $queryarray as $ql ) {
			$wstr .= strlen($wstr) ? " $andor " : "";
			$wstr .= ' (b.post_text like '.'\'%'.str_replace('\\"', '"', addslashes($ql))
				.'%\' or c.post_text like '.'\'%'.str_replace('\\"', '"', addslashes($ql))
				.'%\' or b.title like '.'\'%'.str_replace('\\"', '"', addslashes($ql)).'%\') ' ;
			$i++;
		}
		$sql .= $wstr;
	}
	$sql .= " ORDER by b.blog_date desc ";
	$sqlLimit = $limit+$offset;
	$sql = $sql." limit ".$sqlLimit;
	$result = $xoopsDB->query($sql);
	$i = 0;
	$counter = 0;
	$ret = array();
	while( list($blogid, $title, $postid, $blog_date) = $xoopsDB->fetchRow($result) ){
		if($counter >= $offset){
			$ret[$i]['link'] = "index.php?postid=".$postid;
			$ret[$i]['title'] = (empty($title) || (strlen($title) == 0)) ? "&lt;empty title&gt;" : $title;
			$ret[$i]['time'] = $blog_date;
			$ret[$i]['blogid'] = $blogid;
			$i++;
		}
		$counter++;
	}
	return $ret;
}
?>