<?php
// $Id: index.php,v 1.1.2 2006/04/13 11:16:30 yoshis Exp $
// ------------------------------------------------------------------------ //
// XOOPS - PHP Content Management System  				                    //
// Copyright (c) 2000 XOOPS.org                         					//
// <http://www.xoops.org/>                             						//
// ------------------------------------------------------------------------ //
// This program is free software; you can redistribute it and/or modify     //
// it under the terms of the GNU General Public License as published by     //
// the Free Software Foundation; either version 2 of the License, or        //
// (at your option) any later version.                                      //
// 																			//
// You may not change or alter any portion of this comment or credits       //
// of supporting developers from this source code or any supporting         //
// source code which is considered copyrighted (c) material of the          //
// original comment or credit authors.                                      //
// 																			//
// This program is distributed in the hope that it will be useful,          //
// but WITHOUT ANY WARRANTY; without even the implied warranty of           //
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            //
// GNU General Public License for more details.                             //
// 																			//
// You should have received a copy of the GNU General Public License        //
// along with this program; if not, write to the Free Software              //
// Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 USA //
// ------------------------------------------------------------------------ //
include_once '../../../include/cp_header.php';
if(
	(!defined('XOOPS_ROOT_PATH')) || 
	(!is_object($xoopsUser)) || 
	(!$xoopsUser->isAdmin()) ){
	exit();
}
include_once '../conf.php';

//$dateformat=getmoduleoption('dateformat');
$myts =& MyTextSanitizer::getInstance();
$topicscount=0;
/**
* Get all submitted stories awaiting approval
**/
function getAllSubmitted($status=0){
	$db =& Database::getInstance();
	$myts =& MyTextSanitizer::getInstance();
	$ret = array();
	$sql = "SELECT s.postid, s.uid, s.title, i.title, s.blog_date FROM ".$db->prefix("popnupblog")." s, ".$db->prefix("popnupblog_info")." i ";
	$sql .= " WHERE status=".$status." AND (s.blogid=i.blogid) ORDER BY blog_date DESC";
	$result = $db->query($sql);
	while ( list($pid,$uid,$ptitle,$btitle,$pdate) = $db->fetchRow($result) ) {
		$r = array();
		$r['pid'] = $pid;
		$r['uid'] = $uid;
		$r['ptitle'] = $myts->htmlSpecialChars($ptitle);
		$r['btitle'] = $myts->htmlSpecialChars($btitle);
		$r['uname'] = users::uname($uid);
		$r['pdate'] = $pdate;
		$ret[]=$r;
	}
	return $ret;
}
/**
* Get all submitted stories awaiting approval
**/
function getAllComments($status=0){
	$db =& Database::getInstance();
	$myts =& MyTextSanitizer::getInstance();
	$ret = array();
	$sql = "SELECT s.postid, s.title, c.comment_id, c.comment_uid, c.post_text,c.create_date FROM ".$db->prefix("popnupblog")." s, ".$db->prefix("popnupblog_comment")." c ";
	$sql .= " WHERE c.status=".$status." AND (s.postid=c.postid) ORDER BY create_date DESC";
	$result = $db->query($sql);
	while ( list($pid,$ptitle,$cid,$uid,$post_text,$cdate) = $db->fetchRow($result) ) {
		$r = array();
		$r['pid'] = $pid;
		$r['ptitle'] = $myts->htmlSpecialChars($ptitle);
		$r['cid'] = $cid;
		$r['uid'] = $uid;
		$r['uname'] = users::uname($uid);
		$r['ptext'] = mbstrings::_strcut($post_text, 0, 20);
		$r['cdate'] = $cdate;
		$ret[]=$r;
	}
	return $ret;
}

/**
 * Show new submissions
 *
 * This list can be view in the module's admin when you click on the tab named "Post/Edit News"
 * Submissions are news that was submit by users but who are not approved, so you need to edit
 * them to approve them.
 * Actually you can see the the story's title, the topic, the posted date, the author and a
 * link to delete the story. If you click on the story's title, you will be able to edit the news.
 * The table contains the last x new submissions.
 * The system's block called "Waiting Contents" is listing the number of those news.
 */
function newSubmissions($comment_id=0)
{
    global $dateformat;
    $start = isset($_GET['startnew']) ? intval($_GET['startnew']) : 0;

    $storyarray = getAllSubmitted(0);
    $commentarray = getAllcomments(0);
    if ( count($storyarray)> 0) {
		echo "<H4>"._AM_NEWSUB."</H4>";
        echo "<div style='text-align: center;'><table width='100%' cellspacing='1' cellpadding='3' border='0' class='outer'><tr class='bg3'><td align='center'>" 
        	. _AM_TITLE . "</td><td align='center'>" . _AM_CATEGORY . "</td><td align='center'>" . _AM_POSTED . "</td><td align='center'>" . _AM_POSTER 
        	. "</td><td align='center'>" . _AM_ACTION . "</td></tr>\n";
        $class='';
        foreach( $storyarray as $newstory ){
            $class = ($class == 'even') ? 'odd' : 'even';
            echo "<tr class='".$class."'><td align='left'>\n";
            echo "<a href='".XOOPS_URL."/modules/popnupblog/edit.php?postid=" . $newstory['pid'] . "'>" 
                	. $newstory['ptitle'] . "</a>\n";
            echo "</td><td>" . $newstory['btitle'] . "</td><td align='center' class='nw'>" . $newstory['pdate']
            	. "</td><td align='center'><a href='" . XOOPS_URL . "/userinfo.php?uid=" . $newstory['uid'] . "'>" . $newstory['uname'] 
            	. "</a></td><td align='right'><a href='".XOOPS_URL."/modules/popnupblog/edit.php?postid=" . $newstory['pid'] . "'>" 
            	. _AM_EDIT . "</a></td></tr>\n";
        }
        echo "</table></div>";
    }
    if ( count($commentarray)> 0) {
		echo "<H4>"._AM_NEWCOMMENT."</H4>";
        echo "<div style='text-align: center;'><table width='100%' cellspacing='1' cellpadding='3' border='0' class='outer'><tr class='bg3'><td align='center'>"
        	. _AM_COMMENT . "</td><td align='center'>" 
        	. _AM_TITLE . "</td><td align='center'>" . _AM_POSTED . "</td><td align='center'>" . _AM_POSTER 
        	. "</td><td align='center'>" . _AM_ACTION . "</td></tr>\n";
        $class='';
        $delcomment=null;
        foreach( $commentarray as $newcomment ){
        	if ($comment_id==$newcomment['cid']) $delcomment=$newcomment;
            $class = ($class == 'even') ? 'odd' : 'even';
            echo "<tr class='".$class."'><td align='left'>\n";
            echo "<a href='".XOOPS_URL."/modules/popnupblog/commentedit.php?comment_id=" . $newcomment['cid'] . "'>" 
            	. $newcomment['ptext'] . "</a></td>\n";
            echo "<td><a href='".XOOPS_URL."/modules/popnupblog/index.php?postid=" . $newcomment['pid'] . "'>" 
                	. $newcomment['ptitle'] . "</a>\n";
            echo "</td><td align='center' class='nw'>" . $newcomment['cdate']
            	. "</td><td align='center'><a href='" . XOOPS_URL . "/userinfo.php?uid=" . $newcomment['uid'] . "'>" . $newcomment['uname'] 
            	. "</a></td><td align='right'>"
            	. "<a href='".$_SERVER['PHP_SELF']."?mode=delete&comment_id=" . $newcomment['cid'] . "'>" . _DELETE . "</a>&nbsp;&nbsp;"
            	. "<a href='".XOOPS_URL."/modules/popnupblog/commentedit.php?comment_id=" . $newcomment['cid'] . "'>" . _AM_EDIT . "</a></td></tr>\n";
        }
        echo "</table></div>";
        if ($delcomment){
            echo "<BR /><DIV align='center'>".$delcomment['ptext'] ."&nbsp;:&nbsp;". $delcomment['ptitle'] ."&nbsp;:&nbsp;". $delcomment['cdate'] ."&nbsp;:&nbsp;". $delcomment['uname']
            	. "&nbsp;&nbsp;<a href='".$_SERVER['PHP_SELF']."?mode=delete_comment&comment_id=" . $delcomment['cid'] . "'>[<font color='red'>" . _AM_DELETECOMMENT . "</font>]</a></DIV>";
        }
    }
}
xoops_cp_header();
include_once './adminmenu.php';
echo "<h4>" . _AM_WAITING . "</h4>";
include_once XOOPS_ROOT_PATH . "/class/module.textsanitizer.php";
$mode = isset($_GET['mode']) ? ($_GET['mode']) : "";
$comment_id = isset($_GET['comment_id']) ? ($_GET['comment_id']) : 0;
if ($mode=="delete_comment") {
	$comment_id = intval($comment_id);
	pb_comment::deleteComment($comment_id);
} 
newSubmissions($comment_id);
?>
